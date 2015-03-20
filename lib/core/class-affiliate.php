<?php
/**
 * class-affiliate.php
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
 * Affiliate plugin controller.
 */
class Affiliate {

	public static $admin_messages = array();

	/**
	 * Plugin setup procedure.
	 */
	public static function boot() {
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		load_plugin_textdomain( AFFILIATE_PLUGIN_DOMAIN, null, AFFILIATE_PLUGIN_NAME . '/lib/core/languages' );
		if ( function_exists( 'mb_strlen' ) ) {
			require_once AFFILIATE_CORE_LIB . '/class-affiliate-admin.php';
			require_once AFFILIATE_CORE_LIB . '/class-affiliate-content.php';
			require_once AFFILIATE_CORE_LIB . '/class-affiliate-keyword.php';
			require_once AFFILIATE_CORE_LIB . '/class-affiliate-help.php';
		} else {
			self::$admin_messages[] =
				'<div class="error">' .
				__( '<em>Affiliate</em> requires the Multibyte String <a href="http://www.php.net/manual/en/mbstring.installation.php">mbstring</a> PHP extension.', AFFILIATE_PLUGIN_DOMAIN ) .
				' ' .
				__( 'Please ask your website administrator to enable this extension.', AFFILIATE_PLUGIN_DOMAIN ) .
				'</div>';
		}
	}

	/**
	 * Renders accumulated admin notices.
	 */
	public static function admin_notices() {
		if ( !empty( self::$admin_messages ) ) {
			foreach ( self::$admin_messages as $msg ) {
				echo $msg;
			}
		}
	}
}
Affiliate::boot();
