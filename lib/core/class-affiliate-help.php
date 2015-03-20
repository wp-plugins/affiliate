<?php
/**
 * class-affiliate-help.php
 * 
 * Copyright (c) 2015 "kento" Karim Rahimpur www.itthinx.com
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
 * @author itthinx
 * @package affiliate
 * @since 1.3.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Help sections.
 */
class Affiliate_Help {

	/**
	 * Adds the filter for contextual help.
	 */
	public static function init() {
		add_filter( 'contextual_help', array( __CLASS__, 'contextual_help' ), 10, 3);
	}

	/**
	 * Adds contextual help on our screens.
	 * 
	 * @param string $contextual_help
	 * @param string $screen_id
	 * @param WP_Screen $screen
	 */
	public static function contextual_help( $contextual_help, $screen_id, $screen ) {
		switch( $screen_id ) {
			case 'toplevel_page_affiliate-admin' : // Affiliate
			case 'edit-affiliate_keyword' : // Keywords
			case 'affiliate_keyword' : // Edit Keyword
				$screen->add_help_tab(
					array(
						'id'      => 'affiliate',
						'title'   => __( 'Affiliate', AFFILIATE_PLUGIN_DOMAIN ),
						'content' => 
							'<p>' .
							__( 'Thanks for using the Affiliate toolbox for Affiliate Marketers.', AFFILIATE_PLUGIN_DOMAIN ) .
							'</p>' .
							'<p>' .
							__( 'Please read the <a class="button button-primary" href="http://docs.itthinx.com/document/affiliate/">Documentation</a> if you need help on its usage.', AFFILIATE_PLUGIN_DOMAIN ) .
							'</p>'
					)
				);
				break;
		}
	}
}
Affiliate_Help::init();
