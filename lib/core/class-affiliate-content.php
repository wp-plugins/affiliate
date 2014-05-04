<?php
/**
 * class-affiliate-content.php
 * 
 * Copyright (c) 2014 "kento" Karim Rahimpur www.itthinx.com
 * 
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author Karim Rahimpur
 * @package affiliate
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Content transformer.
 */
class Affiliate_Content {

	/**
	 * Filter priority for the_content hook.
	 * @var int
	 */
	const THE_CONTENT_FILTER_PRIORITY = 99999;

	/**
	 * Setup.
	 */
	public static function init() {
		// @todo excerpt?
		add_filter( 'the_content', array(__CLASS__, 'the_content' ), self::THE_CONTENT_FILTER_PRIORITY );
	}

	/**
	 * Content filter, hooked on the_content, invokes the transformer.
	 * 
	 * @param string $content
	 * @return string filtered content
	 */
	public static function the_content( $content ) {
		if ( $post = get_post() ) {
			$content = apply_filters( 'affiliate_post_the_content', self::transform( $content, $post ), $post );
		}
		return $content;
	}

	/**
	 * Content transformer.
	 * 
	 * @param string $content
	 * @param WP_Post $post
	 * @return string transformed content
	 */
	public static function transform( &$content, &$post ) {

		$keywords = get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'affiliate_keyword',
			'meta_key'    => 'enabled',
			'meta_value'  => 'yes'
		) );
		foreach( $keywords as $keyword ) {
			$url = get_post_meta( $keyword->ID, 'url', true );
			if ( !empty( $url ) ) {
				$search = $keyword->post_title;
				$match_case = get_post_meta( $keyword->ID, 'match_case', true ) == 'yes';
				if ( $match_case ) {
					$case = '';
				} else {
					$case = 'i';
				}
				if ( function_exists( 'mb_ereg_replace' ) ) {
					$content = mb_ereg_replace(
						'\b' . $search . '\b',
						sprintf( '<a href="%s">\\0</a>', esc_attr( $url ) ),
						$content,
						"msr$case"
					);
				} else {
					$content = preg_replace(
						'/\b' . $search . '\b/' . $case,
						sprintf( '<a href="%s">$0</a>', esc_attr( $url ) ),
						$content
					);
				}
			}
		}
		return $content;
	}
}
add_action( 'init', array( 'Affiliate_Content', 'init' ) );
