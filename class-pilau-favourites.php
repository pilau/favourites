<?php

/**
 * Pilau Favourites
 *
 * @package   Pilau_Favourites
 * @author    Steve Taylor <steve@sltaylor.co.uk>
 * @license   GPL-2.0+
 * @copyright 2014 Public Life
 */

/**
 * Plugin class.
 *
 * @package Pilau_Favourites
 * @author  Steve Taylor <steve@sltaylor.co.uk>
 */
class Pilau_Favourites {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1
	 *
	 * @var     string
	 */
	const VERSION = '0.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'pilau-favourites';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1
	 */
	private function __construct() {

		// Load plugin text domain
		//add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Admin init
		//add_action( 'admin_init', array( $this, 'admin_init' ) );

		// Process admin actions
		//add_action( 'admin_init', array( $this, 'admin_processing' ) );

		// Add any admin menus
		//add_action( 'admin_menu', array( $this, 'admin_menus' ) );

		// Add an action link pointing to the options page.
		// $plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'pilau-favourites.php' );
		// add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// Load admin style sheet and JavaScript.
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Check request for adding or removing a favourite
		add_action( 'init', array( $this, 'request_add_remove_favourite' ) );

		// Make sure user info is available in $current_user global
		get_currentuserinfo();

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide  ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}
				restore_current_blog();
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}
				restore_current_blog();
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    0.1
	 *
	 * @param	int	$blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) )
			return;

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    0.1
	 *
	 * @return	array|false	The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    0.1
	 */
	private static function single_activate() {


	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    0.1
	 */
	private static function single_deactivate() {

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Initialize admin
	 *
	 * @since     0.1
	 */
	public function admin_init() {

	}

	/**
	 * Process admin actions
	 *
	 * @since     0.1
	 */
	public function admin_processing() {


	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1
	 */
	public function enqueue_styles() {
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * Register the administration menus for this plugin
	 *
	 * @since    0.1
	 */
	public function admin_menus() {

		/* Options page
		$this->plugin_screen_hook_suffix = add_plugins_page(
			__( 'Pilau Favourites options', $this->plugin_slug ),
			__( 'Pilau Favourites', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
		*/

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1
	 */
	public function add_action_links( $links ) {

		/*
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'plugins.php?page=pilau-favourites' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
		*/

	}

	/**
	 * Build link for favouriting a post or page
	 *
	 * @since	0.1
	 *
	 * @param	int		$post_id	Defaults to current post
	 * @param	array	$classes
	 * @param	string	$title
	 * @return	string
	 */
	public function favourite_link( $post_id = null, $classes = array(), $title = null ) {
		global $post;
		static $user_favourites = null;
		$classes[] = 'pf-fav-link';

		// Initialize post ID
		if ( is_null( $post_id ) ) {
			$post_id = $post->ID;
		}

		// Initialize favourites (kept static to cache in case multiple links are built in one request)
		if ( is_null( $user_favourites ) ) {
			$user_favourites = $this->get_user_favourites();
		}

		// Initialize URL
		$url = add_query_arg( 'pf-id', $post_id, $this->current_url() );

		// Already favourited?
		if ( $this->favourited( $post_id ) ) {
			$url = add_query_arg( 'pf-action', 'remove', $url );
			$text = apply_filters( 'pf_remove_favourite_text', __( 'Remove from favourites', $this->plugin_slug ) );
		} else {
			$url = add_query_arg( 'pf-action', 'add', $url );
			$text = apply_filters( 'pf_add_favourite_text', __( 'Add to favourites', $this->plugin_slug ) );
		}

		// Title
		if ( $title ) {
			$title = ' title="' . esc_attr( $title ) . '"';
		}

		// Build link
		$link = '<a href="' . $url . '" class="' . implode( ' ', $classes ) . '"' . $title . '>' . $text . '</a>';

		return $link;
	}

	/**
	 * Check if a post is currently favourited
	 *
	 * @since	0.1
	 *
	 * @param	int		$post_id	Defaults to current post
	 * @param	int		$user_id	Defaults to current user
	 * @return	bool
	 */
	public function favourited( $post_id = null, $user_id = null ) {
		global $post, $current_user;
		static $user_favourites = null;

		// Initialize post ID
		if ( is_null( $post_id ) ) {
			$post_id = $post->ID;
		}

		// Initialize user ID
		if ( is_null( $user_id ) ) {
			$user_id = $current_user->ID;
		}

		// Initialize favourites (kept static to cache in case multiple links are built in one request)
		if ( is_null( $user_favourites ) ) {
			$user_favourites = $this->get_user_favourites( $user_id );
		}

		// Already favourited?
		return in_array( $post_id, $user_favourites );
	}

	/**
	 * Check request for adding or removing a favourite
	 *
	 * @since	0.1
	 */
	public function request_add_remove_favourite() {

		// Is a favourite specified?
		if ( isset( $_REQUEST['pf-id'] ) && ctype_digit( $_REQUEST['pf-id'] ) ) {
			$result = 'none';

			// Initialize action
			$action = isset( $_REQUEST['pf-action'] ) ? $_REQUEST['pf-action'] : 'add';

			switch ( $action ) {
				case 'add': {
					// Try to add
					$result = $this->add_favourite( $_REQUEST['pf-id'] );
					break;
				}
				case 'remove': {
					// Try to remove
					$result = $this->remove_favourite( $_REQUEST['pf-id'] );
					break;
				}
			}

			// Redirect with message
			wp_redirect( add_query_arg( 'msg', 'pf-' . $result, $this->current_url() ) );
			exit();

		}
	}

	/**
	 * Get a user's favourites
	 *
	 * @since		0.1
	 *
	 * @param		int			$user_id	Defaults to current user
	 * @return		array					An array of post IDs
	 */
	public function get_user_favourites( $user_id = null ) {
		global $current_user;

		// Initialize user ID
		if ( is_null( $user_id ) ) {
			$user_id = $current_user->ID;
		}

		// Get current favourites
		$favourites = maybe_unserialize( get_user_meta( $user_id, 'pf-favourites', true ) );
		if ( ! is_array( $favourites ) ) {
			$favourites = array();
		}

		return $favourites;
	}

	/**
	 * Add a favourite
	 *
	 * @since	0.1
	 *
	 * @param	int		$post_id
	 * @param	int		$user_id	Defaults to current user
	 * @return	string				'added' | 'already-favourited' | 'does-not-exist'
	 */
	public function add_favourite( $post_id, $user_id = null ) {
		global $current_user;
		$return = 'added';

		// See if post exists
		if ( $favourite = get_post( $post_id ) ) {

			// Initialize user ID
			if ( is_null( $user_id ) ) {
				$user_id = $current_user->ID;
			}

			// Get favourites
			$user_favourites = $this->get_user_favourites( $user_id );

			// Already favourited?
			if ( ! in_array( $post_id, $user_favourites ) ) {

				// Add and save
				$user_favourites[] = $post_id;
				update_user_meta( $user_id, 'pf-favourites', $user_favourites );

			} else {

				$return = 'already-favourited';

			}

		} else {

			$return = 'does-not-exist';

		}

		return $return;
	}

	/**
	 * Remove a favourite
	 *
	 * @since	0.1
	 *
	 * @param	int		$post_id
	 * @param	int		$user_id	Defaults to current user
	 * @return	string				'removed'
	 */
	public function remove_favourite( $post_id, $user_id = null ) {
		global $current_user;
		$return = 'removed';

		// Initialize user ID
		if ( is_null( $user_id ) ) {
			$user_id = $current_user->ID;
		}

		// Get favourites
		$user_favourites = $this->get_user_favourites( $user_id );

		// Find key in favourites
		$key = array_search( $post_id, $user_favourites );
		if ( $key !== false ) {

			// Remove and save
			unset( $user_favourites[ $key ] );
			update_user_meta( $user_id, 'pf-favourites', $user_favourites );

		}

		return $return;
	}

	/**
	 * Get current URL
	 *
	 * @since	0.1
	 *
	 * @param	bool		$keep_params
	 * @return	string
	 */
	public function current_url( $keep_params = false ) {
		$url = $_SERVER['REQUEST_URI'];

		// Strip query parameters?
		if ( ! $keep_params ) {
			$url = strtok( $url, '?' );
		}

		return $url;
	}

}
