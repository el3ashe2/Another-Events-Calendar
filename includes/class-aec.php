<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/includes
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC Class
 *
 * @since    1.0.0
 */
class AEC {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      AEC_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once AEC_PLUGIN_DIR . 'includes/class-aec-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once AEC_PLUGIN_DIR . 'includes/class-aec-i18n.php';
		
		/**
		 * Helper functions
		 */
		require_once AEC_PLUGIN_DIR . 'includes/helper-functions.php';

		/**
		 * The classes responsible for defining all actions that occur in the admin area.
		 */
		require_once AEC_PLUGIN_DIR . 'admin/class-aec-admin.php';
		require_once AEC_PLUGIN_DIR . 'admin/class-aec-admin-events.php';
		require_once AEC_PLUGIN_DIR . 'admin/class-aec-admin-categories.php';
		require_once AEC_PLUGIN_DIR . 'admin/class-aec-admin-tags.php';
		require_once AEC_PLUGIN_DIR . 'admin/class-aec-admin-venues.php';
		require_once AEC_PLUGIN_DIR . 'admin/class-aec-admin-organizers.php';
		require_once AEC_PLUGIN_DIR . 'admin/class-aec-admin-settings.php';
		
		/**
		 * The classes responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public.php';
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public-categories.php';
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public-calendar.php';
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public-events.php';
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public-search.php';
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public-tags.php';
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public-venues.php';
		require_once AEC_PLUGIN_DIR . 'public/class-aec-public-organizers.php';
 
		$this->loader = new AEC_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function set_locale() {

		$plugin_i18n = new AEC_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function define_admin_hooks() {

		// Hooks common for all admin pages
		$plugin_admin = new AEC_Admin();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'set_menu_order', 99 );
		
		// Hooks specific to events page
		$plugin_events = new AEC_Admin_Events();
		
		$this->loader->add_action( 'init', $plugin_events, 'register_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_events, 'register_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_events, 'save_meta_boxes', 10, 2 );
		$this->loader->add_action( 'restrict_manage_posts', $plugin_events, 'restrict_manage_posts' );
		$this->loader->add_action( 'manage_aec_events_posts_custom_column', $plugin_events, 'manage_posts_custom_column', 10, 2 );
		
		$this->loader->add_filter( 'parse_query', $plugin_events, 'parse_query' );		
		$this->loader->add_filter( 'manage_edit-aec_events_columns', $plugin_events, 'manage_posts_columns' );
		
		// Hooks specific to categories page
		$plugin_categories = new AEC_Admin_Categories();
		
		$this->loader->add_action( 'init', $plugin_categories, 'register_taxonomy' );
		
		// Hooks specific to tags page
		$plugin_tags = new AEC_Admin_Tags();
		
		$this->loader->add_action( 'init', $plugin_tags, 'register_taxonomy' );
		
		// Hooks specific to venues page
		$plugin_venues = new AEC_Admin_Venues();
		
		$this->loader->add_action( 'init', $plugin_venues, 'register_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_venues, 'register_meta_boxes' ); 
		$this->loader->add_action( 'save_post', $plugin_venues, 'save_meta_boxes', 10, 2 );
		$this->loader->add_action( 'manage_aec_venues_posts_custom_column', $plugin_venues, 'manage_posts_custom_column', 10, 2 );

		$this->loader->add_filter( 'enter_title_here', $plugin_venues, 'change_title_text' );
		$this->loader->add_filter( 'manage_edit-aec_venues_columns', $plugin_venues, 'manage_posts_columns' );
		
		// Hooks specific to organizers page
		$plugin_organizers = new AEC_Admin_Organizers();
		
		$this->loader->add_action( 'init', $plugin_organizers, 'register_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_organizers, 'register_meta_boxes' ); 
		$this->loader->add_action( 'save_post', $plugin_organizers, 'save_meta_boxes', 10, 2 );
		$this->loader->add_action( 'manage_aec_organizers_posts_custom_column', $plugin_organizers, 'manage_posts_custom_column', 10, 2 );
		
		$this->loader->add_filter( 'enter_title_here', $plugin_organizers, 'change_title_text' );
		$this->loader->add_filter( 'manage_edit-aec_organizers_columns', $plugin_organizers, 'manage_posts_columns' );

		// Hooks specific to settings page
		$plugin_settings = new AEC_Admin_Settings();		
		
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'add_settings_menu' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'admin_init' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function define_public_hooks() {

		// Hooks common for all public facing functionality of the plugin
		$plugin_public = new AEC_Public();

		$this->loader->add_action( 'init', $plugin_public, 'add_rewrites' );	
		$this->loader->add_action( 'wp_loaded', $plugin_public, 'maybe_flush_rules' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'og_metatags' );		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_filter( 'the_title', $plugin_public, 'the_title', 99 );
				
		// Hooks specific to categor(ies) page
		$plugin_categories = new AEC_Public_Categories();

		// Hooks specific calendar page
		$plugin_calendar = new AEC_Public_Calendar();
		
		// Hooks specific event(s) page
		$plugin_events = new AEC_Public_Events();
		
		$this->loader->add_filter( 'post_thumbnail_html', $plugin_events, 'post_thumbnail_html' );
		$this->loader->add_filter( 'the_content', $plugin_events, 'the_content' );
					
		// Hooks specific to search results page
		$plugin_search = new AEC_Public_Search();
				
		// Hooks specific single venue page
		$plugin_venues = new AEC_Public_Venues();		
		
		// Hooks specific organizer page
		$plugin_organizers = new AEC_Public_Organizers();
		
		// Hooks specific to tag(s) page
		$plugin_tags = new AEC_Public_Tags();		
		
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
	
		$this->loader->run();
		
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    AEC_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
	
		return $this->loader;
		
	}

}
