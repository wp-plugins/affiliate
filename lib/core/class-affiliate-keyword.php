<?php
/**
 * class-affiliate-keyword.php
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
 * Keyword
 */
class Affiliate_Keyword {

	/**
	 * Setup.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ), 11 );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ), 10, 2 );
		add_filter( 'enter_title_here', array( __CLASS__, 'enter_title_here' ), 10, 2 );
		add_action( 'edit_form_after_title', array( __CLASS__, 'edit_form_after_title' ) );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'wp_insert_post_data' ), 9999, 2 );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );

		add_filter( 'post_row_actions', array( __CLASS__, 'post_row_actions' ) );
		add_filter( 'post_updated_messages', array( __CLASS__, 'post_updated_messages' ) );
	}

	/**
	 * Hooked on the init action, register the post type.
	 */
	public static function wp_init() {
		self::post_type();
	}

	/**
	 * Admin hooks.
	 */
	public static function admin_init() {
		add_filter( 'parent_file', array( __CLASS__, 'parent_file' ) );
	}

	/**
	 * Sets the parent menu when adding or editing a keyword. Keeps the
	 * Affiliate menu open and marks the Keywords menu item as active.
	 * 
	 * @param string $parent_file
	 * @return string
	 */
	public static function parent_file( $parent_file ) {
		global $submenu_file;
		switch ( $submenu_file ) {
			case 'post-new.php?post_type=affiliate_keyword' :
			case 'edit.php?post_type=affiliate_keyword' :
				$parent_file = 'affiliate-admin';
				// Keywords menu item marked active when adding a keyword
				$submenu_file = 'edit.php?post_type=affiliate_keyword';
				break;
		}
		return $parent_file;
	}

	/**
	 * Register the keyword post type.
	 */
	public static function post_type() {
		register_post_type(
			'affiliate_keyword',
			array(
				'labels' => array(
					'name'               => __( 'Keywords', AFFILIATE_PLUGIN_DOMAIN ),
					'singular_name'      => __( 'Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'all_items'          => __( 'Keywords', AFFILIATE_PLUGIN_DOMAIN ),
					'add_new'            => __( 'New Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'add_new_item'       => __( 'Add New Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'edit'               => __( 'Edit', AFFILIATE_PLUGIN_DOMAIN ),
					'edit_item'          => __( 'Edit Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'new_item'           => __( 'New Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'not_found'          => __( 'No Keywords found', AFFILIATE_PLUGIN_DOMAIN ),
					'not_found_in_trash' => __( 'No Keywords found in trash', AFFILIATE_PLUGIN_DOMAIN ),
					'parent'             => __( 'Parent Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'search_items'       => __( 'Search Keywords', AFFILIATE_PLUGIN_DOMAIN ),
					'view'               => __( 'View Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'view_item'          => __( 'View Keyword', AFFILIATE_PLUGIN_DOMAIN ),
					'menu_name'          => __( 'Keywords', AFFILIATE_PLUGIN_DOMAIN )
				),
				'capability_type'     => array( 'affiliate_keyword', 'affiliate_keywords' ),
				'description'         => __( 'Affiliate Keyword', AFFILIATE_PLUGIN_DOMAIN ),
				'exclude_from_search' => true,
				'has_archive'         => false,
				'hierarchical'        => false,
				'map_meta_cap'        => true,
// 				'menu_position'       => 10,
				'menu_icon'           => AFFILIATE_PLUGIN_URL . '/images/keyword.png',
				'public'              => false,
				'publicly_queryable'  => false,
				'query_var'           => true,
				'rewrite'             => false,
				'show_in_nav_menus'   => false,
				'show_ui'             => true,
				'supports'            => array( 'title' ),
// 				'taxonomies'          => array( 'foobar' ),
				'show_in_menu'        => false, // *
			)
		);
		
		// * 'show_in_menu' => 'affiliate-admin' would add as it as an item to the Affiliate menu
		//   but it would replace the main menu item and the admin_menu action would need a lower
		//   than standard priority (e.g. 9) to avoid that ... messy, hacky, so we
		//   rather add it there using add_submenu_page
		self::create_capabilities();
	}

	/**
	 * Creates capabilities to handle keywords.
	 * Administrators have all capabilities.
	 * Assures that these capabilities exist.
	 */
	public static function create_capabilities() {
		global $wp_roles;
		$admin = $wp_roles->get_role( 'administrator' );
		$caps = self::get_capabilities();
		foreach( $caps as $key => $capability ) {
			if ( !$admin->has_cap( $capability ) ) {
				$admin->add_cap( $capability );
			}
		}
	}

	/**
	 * Returns an array of relevant capabilities for the affiliate_keyword post type.
	 * @return array
	 */
	public static function get_capabilities() {
		return array(
			'edit_posts'             => 'edit_affiliate_keywords',
			'edit_others_posts'      => 'edit_others_affiliate_keywords',
			'publish_posts'          => 'publish_affiliate_keywords',
			'read_private_posts'     => 'read_private_affiliate_keywords',
			'delete_posts'           => 'delete_affiliate_keywords',
			'delete_private_posts'   => 'delete_private_affiliate_keywords',
			'delete_published_posts' => 'delete_published_affiliate_keywords',
			'delete_others_posts'    => 'delete_others_affiliate_keywords',
			'edit_private_posts'     => 'edit_private_affiliate_keywords',
			'edit_published_posts'   => 'edit_published_affiliate_keywords',
		);
	}

	/**
	 * Adds a meta box for the link.
	 */
	public static function add_meta_boxes( $post_type, $post ) {
		if ( $post_type == 'affiliate_keyword' ) {

			add_meta_box(
				'options',
				__( 'Options', AFFILIATE_PLUGIN_DOMAIN ),
				array( __CLASS__, 'options_meta_box' ),
				'affiliate_keyword',
				'normal',
				'high'
			);

			add_meta_box(
				'link',
				__( 'Link', AFFILIATE_PLUGIN_DOMAIN ),
				array( __CLASS__, 'link_meta_box' ),
				'affiliate_keyword',
				'normal',
				'high'
			);

		}
	}

	/**
	 * Replacement indicating keyword rather than title ...
	 * 
	 * @param string $title
	 * @param WP_Post $post
	 * @return string
	 */
	public static function enter_title_here( $title, $post ) {
		if ( $post->post_type == 'affiliate_keyword' ) {
			return __( 'Enter the keyword here', AFFILIATE_PLUGIN_DOMAIN );
		}
		return $title;
	}

	/**
	 * Render stuff after the title when editing a keyword.
	 * 
	 * @param WP_Post $post
	 */
	public static function edit_form_after_title( $post ) {
		if ( $post->post_type == 'affiliate_keyword' ) {
			echo '<div style="padding:2px 1em 1em 1em;" class="description">';
			echo __( 'The keyword can be any combination of words and whitespace.', AFFILIATE_PLUGIN_DOMAIN );
			echo '</div>'; 
			if ( !function_exists( 'mb_ereg_replace' ) ) {
				echo '<div style="padding:1em;" class="warning">';
				echo __( 'Your setup does not support multibyte strings. If you require replacements for a language that requires support for multibyte strings, please ask your web server administrator to <a href="http://www.php.net/manual/en/mbstring.installation.php">install mbstring</a>.', AFFILIATE_PLUGIN_DOMAIN );
				echo '</div>';
			}
		}
	}

	/**
	 * Renders the link meta box.
	 * 
	 * @param object $post affiliate_keyword
	 */
	public static function link_meta_box( $post ) {
		$output = '';

		$url = get_post_meta( $post->ID, 'url', true );

		$output .= '<div>';
		$output .= '<label>';
		$output .= __( 'URL', AFFILIATE_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= sprintf( '<input style="display:block; width:100%%" type="text" name="url" value="%s" />', esc_attr( $url ) );
		$output .= '</label>';
		$output .= '<p class="description">';
		$output .= __( 'The URL that should be used in the link that replaces the keyword found in content.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';
		$output .= '</div>';

		if ( !empty( $url ) ) {

			$validate_url = $url;
			if ( function_exists( 'mb_strlen' ) ) {
				if ( mb_strlen( $validate_url ) != strlen( $validate_url ) ) {
					if ( function_exists( 'idn_to_ascii' ) ) {
						$validate_url = idn_to_ascii( $validate_url );
					}
				}
			}

			$valid_url = true;

			if ( function_exists( 'filter_var' ) ) {
				$valid_url = filter_var( $validate_url, FILTER_VALIDATE_URL ) !== false;
			}

			if ( !$valid_url ) {
				$output .= '<p class="warning">';
				$output .= __( 'The URL provided does not appear to be valid, although in some cases the URL may still be valid, it is recommended to check that the URL you have indicated is correct.', AFFILIATE_PLUGIN_DOMAIN );
				$output .= '</p>';
			}

		}

		echo $output;
	}

	/**
	 * Renders the options meta box.
	 * 
	 * @param object $post affiliate_keyword
	 */
	public static function options_meta_box( $post ) {
		$output = '';

		$enabled = get_post_meta( $post->ID, 'enabled', true );
		if ( empty( $enabled ) ) {
			$enabled = 'yes';
		}

		$output .= '<div>';
		$output .= '<label>';
		$output .= sprintf( '<input type="checkbox" name="enabled" %s />', $enabled == 'yes' ? ' checked="checked" ' : '' );
		$output .= ' ';
		$output .= __( 'Enabled', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</label>';
		$output .= '<p class="description">';
		$output .= __( 'Enable keyword replacement in content?', AFFILIATE_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= __( 'If enabled and a URL is given, the keyword will be replaced by a link pointing to the URL.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';
		$output .= '</div>';

		$match_case = get_post_meta( $post->ID, 'match_case', true );
		if ( empty( $match_case ) ) {
			$match_case = 'no';
		}

		$output .= '<div>';
		$output .= '<label>';
		$output .= sprintf( '<input type="checkbox" name="match_case" %s />', $match_case == 'yes' ? ' checked="checked" ' : '' );
		$output .= ' ';
		$output .= __( 'Match Case', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</label>';
		$output .= '<p class="description">';
		$output .= __( 'Use case-sensitive replacement?', AFFILIATE_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= __( 'If enabled, replacement will be done only if the keyword is matched exactly in case. By default, case-insensitive comparison is used, so that a keyword like "Example" would be matched by "Example", "example", "exAMple" etc. If case-sensitive replacement is enabled, only occurrences of "Example" would be replaced.', AFFILIATE_PLUGIN_DOMAIN );
		$output .= '</p>';
		$output .= '</div>';

		echo $output;
	}

	/**
	 * Title filter to allow only words combined with whitespace as keyword.
	 * 
	 * @param array $data
	 * @param array $postarr
	 * @return array
	 */
	public static function wp_insert_post_data( $data, $postarr ) {
		if ( $data['post_type'] == 'affiliate_keyword' ) {
			if ( function_exists( 'mb_ereg_replace' ) ) {
				$data['post_title'] = mb_ereg_replace( '[^\w\s]+', '', $data['post_title'] );
			} else {
				$data['post_title'] = preg_replace( '/[^\w\s]+/', '', $data['post_title'] );
			}
		}
		return $data;
	}

	/**
	 * Save post meta.
	 * 
	 * @param int $post_id
	 * @param object $post
	 */
	public static function save_post( $post_id = null, $post = null ) {
		if ( ! ( ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) ) ) {
			if ( $post->post_type == 'affiliate_keyword' ) {
				if ( $post->post_status != 'auto-draft' ) {

					$enabled = !empty( $_POST['enabled'] );
					delete_post_meta( $post_id, 'enabled' );
					if ( $enabled ) {
						add_post_meta( $post_id, 'enabled', 'yes' );
					} else {
						add_post_meta( $post_id, 'enabled', 'no' );
					}

					$match_case = !empty( $_POST['match_case'] );
					delete_post_meta( $post_id, 'match_case' );
					if ( $match_case ) {
						add_post_meta( $post_id, 'match_case', 'yes' );
					} else {
						add_post_meta( $post_id, 'match_case', 'no' );
					}

					$url = isset( $_POST['url'] ) ? trim( $_POST['url'] ) : '';
					delete_post_meta( $post_id, 'url' );
					if ( !empty( $url ) ) {
						$components = parse_url( $url );
						if ( $components ) {
							add_post_meta( $post_id, 'url', $url );
						}
					}

				}
			}
		}
	}

	/**
	 * View action removed.
	 * 
	 * @param array $actions
	 * @return array
	 */
	public static function post_row_actions( $actions ) {
		$post_type = get_post_type();
		if ( $post_type == 'affiliate_keyword' ) {
			unset( $actions['view'] );
		}
		return $actions;
	}

	/**
	 * Messages overriden.
	 * 
	 * @param array $messages
	 * @return array
	 */
	public static function post_updated_messages( $messages ) {
		$post = get_post();
		$messages['affiliate_keyword'] = array(
			0 => '',
			1 => __( 'Keyword updated.', AFFILIATE_PLUGIN_DOMAIN ),
			2 => __( 'Custom field updated.', AFFILIATE_PLUGIN_DOMAIN ),
			3 => __( 'Custom field deleted.', AFFILIATE_PLUGIN_DOMAIN ),
			4 => __( 'Keyword updated.', AFFILIATE_PLUGIN_DOMAIN ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Keyword restored to revision from %s', AFFILIATE_PLUGIN_DOMAIN ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6 => __( 'Keyword published.', AFFILIATE_PLUGIN_DOMAIN ),
			7 => __( 'Keyword saved.', AFFILIATE_PLUGIN_DOMAIN ),
			8 => __( 'Keyword submitted.', AFFILIATE_PLUGIN_DOMAIN ),
			9 => sprintf( __( 'Keyword scheduled for: <strong>%1$s</strong>.', AFFILIATE_PLUGIN_DOMAIN ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Keyword draft updated.', AFFILIATE_PLUGIN_DOMAIN )
		);
		return $messages;
	}
}
Affiliate_Keyword::init();
