<?php
/**
 * class-affiliate-admin.php
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
 * Affiliate plugin administration.
 */
class Affiliate_Admin {

	/**
	 * Required capability to access the administrative sections.
	 * 
	 * @var string
	 */
	const ADMIN_CAPABILITY = 'manage_options';

	/**
	 * Administration setup.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_filter( 'plugin_action_links_'. plugin_basename( AFFILIATE_PLUGIN_FILE ), array( __CLASS__, 'admin_settings_link' ) );
	}

	/**
	 * Admin init, hooked on admin_init.
	 */
	public static function admin_init() {
		wp_register_style( 'affiliate_admin', AFFILIATE_PLUGIN_URL . 'css/admin.css', array(), AFFILIATE_PLUGIN_VERSION );
	}

	/**
	 * Loads styles for the admin section.
	 */
	public static function admin_print_styles() {
		wp_enqueue_style( 'affiliate_admin' );
	}

	/**
	 * Add a menu item to the Appearance menu.
	 */
	public static function admin_menu() {

		$pages = array();

		// Affiliate menu and main menu item
		$page = add_menu_page(
			__( 'Affiliate', AFFILIATE_PLUGIN_DOMAIN ),
			__( 'Affiliate', AFFILIATE_PLUGIN_DOMAIN ),
			self::ADMIN_CAPABILITY,
			'affiliate-admin',
			array( __CLASS__, 'affiliate_admin_section' ),
			AFFILIATE_PLUGIN_URL . '/images/affiliate.png'
		);
		$pages[] = $page;
		add_action( 'admin_print_styles-' . $page, array( __CLASS__, 'admin_print_styles' ) );

		// Keywords menu item
		$affiliate_keyword_cpt = get_post_type_object( 'affiliate_keyword' );
		add_submenu_page(
			'affiliate-admin',
			$affiliate_keyword_cpt->labels->name,
			$affiliate_keyword_cpt->labels->all_items,
			$affiliate_keyword_cpt->cap->edit_posts,
			"edit.php?post_type=affiliate_keyword"
		);

// 		remove_submenu_page( 'affiliate-admin', 'affiliate-admin' );

		// @todo Links menu item
// 		$affiliate_link_cpt = get_post_type_object( 'affiliate_link' );
// 		add_submenu_page(
// 			'affiliate-admin',
// 			$affiliate_link_cpt->labels->name,
// 			$affiliate_link_cpt->labels->all_items,
// 			$affiliate_link_cpt->cap->edit_posts,
// 			"edit.php?post_type=affiliate_link"
// 		);

		// Settings menu item
		$page = add_submenu_page(
			'affiliate-admin',
			__( 'Settings', AFFILIATE_PLUGIN_DOMAIN ),
			__( 'Settings', AFFILIATE_PLUGIN_DOMAIN ),
			self::ADMIN_CAPABILITY,
			'affiliate-settings',
			array( __CLASS__, 'affiliate_settings_section' )
		);
		add_action( 'admin_print_styles-' . $page, array( __CLASS__, 'admin_print_styles' ) );
	}

	/**
	 * Affiliate administration screen.
	 */
	public static function affiliate_admin_section() {

		if ( !current_user_can( self::ADMIN_CAPABILITY ) ) {
			wp_die( __( 'Access denied.', AFFILIATE_PLUGIN_DOMAIN ) );
		}

		$output = '';

		$output .= '<div class="affiliate-admin">';

		$output .= '<h1>';
		$output .= __( 'Affiliate', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</h1>';

		$output .= '<h2>';
		$output .= __( 'Keywords', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</h2>';

		$output .= '<p>';
		$output .= __( 'Keywords can be substituted with links automatically anywhere they appear in the content of the site.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';
		$output .= '<p>';
		$output .= sprintf(
			__( 'Keywords are substituted only on post types enabled in the %s.', AFFILIATE_PLUGIN_DOMAIN ),
			sprintf(
				'<a href="%s">%s</a>',
				esc_attr( get_admin_url( null, 'admin.php?page=affiliate-settings' ) ),
				esc_html( __( 'Settings', AFFILIATE_PLUGIN_DOMAIN ) )
			)
		);
		$output .= '</p>';

		$output .= '<p>';
		$output .= __( 'To create a keyword, go to <strong>Affiliate > Keywords</strong>. Click <strong>New Keyword</strong> and on the <em>Add New Keyword</em> screen, enter the desired keyword and target URL for the link that should substitute the keyword.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<p>';
		$output .= __( 'Keywords that are enabled will be susbtituted automatically.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<p>';
		$output .= __( 'Example:', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<p>';
		$output .= __( 'Assume you have created and enabled a keyword "Example" that should link to http://www.example.com.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<p>';
		$output .= __( 'The following content ...', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<blockquote>';
		$output .= __( 'This is an example of how keyword substitution works.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</blockquote>';

		$output .= '<p>';
		$output .= __( '... will have the word <em>example</em> replaced by a link:', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<blockquote>';
		$output .= __( 'This is an <a href="http://www.example.com">example</a> of how keyword substitution works.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</blockquote>';

		$output .= '</div>';
		echo $output;
	}

	/**
	 * Settings screen.
	 */
	public static function affiliate_settings_section() {

		if ( !current_user_can( self::ADMIN_CAPABILITY ) ) {
			wp_die( __( 'Access denied.', AFFILIATE_PLUGIN_DOMAIN ) );
		}

		// handle save
		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST['affiliate-settings'], 'save' ) ) {
				$post_types_option = Affiliate::get_post_types();
				$post_types = get_post_types( array( 'public' => true ) );
				$selected_post_types = is_array( $_POST['post-type'] ) ? $_POST['post-type'] : array();
				foreach( $post_types as $post_type ) {
					$post_types_option[$post_type] = in_array( $post_type, $selected_post_types );
				}
				Affiliate::set_post_types( $post_types_option );
			}
		}

		// build settings
		$output = '';
		$output .= '<div class="affiliate-settings">';
		$output .= '<h1>';
		$output .= __( 'Settings', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</h1>';
		$output .= '<form action="" name="options" method="post">';
		$output .= '<div>';
		$output .= '<h2>' . __( 'Post Types', AFFILIATE_PLUGIN_DOMAIN ) . '</h2>';
		$output .= '<p class="description">' .  __( 'Enable keyword substitution for these post types.', AFFILIATE_PLUGIN_DOMAIN ) . '</p>';
		$post_types_option = Affiliate::get_post_types();
		$post_types = get_post_types( array( 'public' => true ) );
		$output .= '<ul>';
		foreach( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$output .= '<li>';
			$output .= '<label>';
			$label = $post_type;
			$labels = isset( $post_type_object->labels ) ? $post_type_object->labels : null;
			if ( ( $labels !== null ) && isset( $labels->singular_name ) ) {
				$label = __( $labels->singular_name );
			}
			$checked = ( isset( $post_types_option[$post_type] ) && $post_types_option[$post_type] ) ? ' checked="checked" ' : '';
			$output .= '<input name="post-type[]" type="checkbox" value="' . esc_attr( $post_type ) . '" ' . $checked . '/>';
			$output .= $label;
			$output .= '</label>';
			$output .= '</li>';
		}
		$output .= '<ul>';
		$output .= '<p class="description">';
		$output .= __( 'Keywords are substituted with affiliate links on enabled post types only.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= wp_nonce_field( 'save', 'affiliate-settings', true, false );
		$output .= sprintf( '<input class="button button-primary" type="submit" name="submit" value="%s"/>',  esc_attr( __( 'Save', AFFILIATE_PLUGIN_DOMAIN ) ) );
		$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>';

		// render settings
		echo $output;
	}

	/**
	 * Adds plugin links.
	 *
	 * @param array $links
	 * @param array $links with additional links
	 */
	public static function admin_settings_link( $links ) {
		if ( current_user_can( self::ADMIN_CAPABILITY ) ) {
			$links = array(
				'<a href="' . get_admin_url( null, 'admin.php?page=affiliate-admin' ) . '">' . __( 'Affiliate', AFFILIATE_PLUGIN_DOMAIN ) . '</a>',
				'<a href="' . get_admin_url( null, 'admin.php?page=affiliate-settings' ) . '">' . __( 'Settings', AFFILIATE_PLUGIN_DOMAIN ) . '</a>'
			) + $links;
		}
		return $links;
	}
}
add_action( 'init', array( 'Affiliate_Admin', 'init' ) );
