<?php
/**
 * class-affiliate-builder.php
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
 * @since 1.1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builder class, used to do content replacements.
 */
class Affiliate_Content_Builder {

	/**
	 * Content holder.
	 * @var string
	 */
	private $content = null;

	/**
	 * Holds keyword tuples.
	 * @var array
	 */
	private $keywords = null;

	/**
	 * The current keyword tuple for replacement.
	 * @var array
	 */
	private $current_keyword = null;

	/**
	 * Orginal content starts with <p> flag.
	 * 
	 * @var boolean
	 */
	private $p_starts = false;

	/**
	 * Original content ends with </p> flag.
	 * 
	 * @var boolean
	 */
	private $p_ends = false; 

	/**
	 * Constructor for specific content taking on replacements for
	 * published keywords.
	 * 
	 * @param string $content
	 */
	public function __construct( $content ) {
		$this->content = $content;
		if ( stripos( $content, '<p>' ) === 0 ) {
			$this->p_starts = true;
		}
		if ( strripos( $content, '</p>' ) === strlen( $content ) - 4 ) {
			$this->p_ends = true;
		}
		$this->keywords = array();
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
				$this->keywords[] = array(
					'url'    => $url,
					'search' => $search,
					'case'   => $case
				);
			}
		}
	}

	/**
	 * Transforms the content using keyword replacement.
	 * 
	 * @return string
	 */
	public function get_content() {

		$charset = get_bloginfo( 'charset' );
		$d = new DOMDocument( '1.0', $charset );

		// Important to have the right encoding. Either like below or using
		// sprintf( '<meta http-equiv="Content-Type", content="text/html; charset=%s">', $charset );
		$prefix = sprintf( '<?xml version="1.0" encoding="%s"><html><body><div>', $charset );
		$suffix = '</div></body></html>';

		if ( !empty( $this->keywords ) ) {
			foreach( $this->keywords as $keyword ) {

				// we need to reconstruct the document after each replacement round
				@$d->loadHTML( $prefix . $this->content . $suffix );
				if ( $keyword['case'] == 'i' ? stripos( $this->content, $keyword['search'] ) : strpos( $this->content, $keyword['search'] ) ) {
					$this->current_keyword = $keyword;
					$this->traverse( $d );
					$output = $d->saveHTML();
					$open = mb_stripos( $output, $prefix );
					$close = mb_stripos( $output, $suffix );
					$output = mb_substr( $output, $open + strlen( $prefix ), $close - $open - strlen( $prefix ) );
					$this->content = html_entity_decode( $output, ENT_QUOTES, $charset );
					if ( ( stripos( $this->content, '<p>' ) === 0 ) && !$this->p_starts ) {
						$this->content = mb_substr( $this->content, 3 );
					}
					if ( ( strripos( $this->content, '</p>' ) === strlen( $this->content ) - 4 ) && !$this->p_ends ) {
						$this->content = mb_substr( $this->content, 0, strlen( $this->content ) - 4 );
					}
				}
			}
		}
		return $this->content;
	}

	/**
	 * Node traversal and content replacement.
	 * 
	 * @param DOMNode $DOMNode
	 */
	private function traverse( $DOMNode ) {
		if( $DOMNode->hasChildNodes() ){
			foreach ( $DOMNode->childNodes as $DOMElement ) {
				$nodeName = $DOMNode->nodeName;
				$nodeType = $DOMNode->nodeType;
				if ( $nodeType == XML_ELEMENT_NODE && $nodeName == 'a' ) {
					// skip links so that we don't place a link inside a link
				} else {
					$this->traverse( $DOMElement );
				}
			}
		} else {
			$nodeName = $DOMNode->nodeName;
			$nodeType = $DOMNode->nodeType;
			if ( $nodeType == XML_TEXT_NODE ) {
				if ( !empty( $this->current_keyword ) ) {
					$DOMNode->nodeValue = mb_ereg_replace(
						'\b' . $this->current_keyword['search'] . '\b',
						sprintf( '<a href="%s">\\0</a>', esc_attr( $this->current_keyword['url'] ) ),
						$DOMNode->nodeValue,
						"msr" . $this->current_keyword['case']
					);
				}
			}
		}
	}

}
