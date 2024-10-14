<?php
 
/**
 * Settings.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/admin
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Admin_Settings Class 
 * 
 * @since    1.0.0
 */
class AEC_Admin_Settings {

	/**
	 * Add the settings submenu.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_menu() {
 
		add_submenu_page(
			'edit.php?post_type=aec_events', 
			__('Settings', 'another-events-calendar' ), 
			__('Settings', 'another-events-calendar' ), 
			'manage_options', 
			'aec_settings',
			array( $this, 'display_settings' )
		); 
 	
	}	
	
	/**
	 * Display settings.
	 *
	 * @since    1.0.0
	 */
	public function display_settings() {
	
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
				
		include AEC_PLUGIN_DIR.'admin/partials/settings/aec-admin-settings-display.php';
		
	}
	
	
	/**
	 * Register settings.
	 *
	 * @since    1.0.0
	 */
	function admin_init() {  
	
        $tabs = array( 'general', 'page', 'advanced' );
		
		foreach( $tabs as $tab ) {
			call_user_func( array( $this, 'register_'.$tab.'_settings' ), 'aec_'.$tab .'_settings_tab' );
		}
		
    }
	
	/**
	 * Regsiter general settings.
	 *
	 * @since    1.0.0
	 */
	function register_general_settings( $page_hook ) { 
 
		// Section : aec_general_settings_section
		add_settings_section(
			'aec_general_settings_section',
        	__( 'General Settings', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
		
		add_settings_field( 
			'aec_general_settings[bootstrap]',
    		__( 'Bootstrap', 'another-events-calendar' ), 
    		array( $this, 'callback_multicheck' ),
    		$page_hook,  
    		'aec_general_settings_section',
			array(
				'option_name' => 'aec_general_settings',
				'field_name'  => 'bootstrap',
				'options'     => array(
					'css'        => __( 'Load bootstrap css', 'another-events-calendar' ),
					'javascript' => __( 'Load bootstrap javascript libraries', 'another-events-calendar' )
				),
				'description' => __( 'This plugin uses bootstrap 3. Disable these options if your theme already include them.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_general_settings[has_tags]',
    		__( 'Enable Tags', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_general_settings_section',
			array(
				'option_name' => 'aec_general_settings',
				'field_name'  => 'has_tags',
				'field_label' => __( 'Check this to enable tags', 'another-events-calendar' )
				
			)
		);
		
		add_settings_field( 
			'aec_general_settings[has_venues]',
    		__( 'Enable Venues', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_general_settings_section',
			array(
				'option_name' => 'aec_general_settings',
				'field_name'  => 'has_venues',
				'field_label' => __( 'Check this to enable venues', 'another-events-calendar' )
			)	
		);
		
		add_settings_field( 
			'aec_general_settings[has_organizers]',
    		__( 'Enable Organizers', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_general_settings_section',
			array(
				'option_name' => 'aec_general_settings',
				'field_name'  => 'has_organizers',
				'field_label' => __( 'Check this to enable organizers', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_general_settings[show_comments]',
    		__( 'Show Comments', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_general_settings_section',
			array(
				'option_name' => 'aec_general_settings',
				'field_name'  => 'show_comments',
				'field_label' => __( 'Check this to enable comments on event pages', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_general_settings[show_past_events]',
    		__( 'Show Past Events', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_general_settings_section',
			array(
				'option_name' => 'aec_general_settings',
				'field_name'  => 'show_past_events',
				'field_label' => __( 'Check this to show past events', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_general_settings[default_location]',
    		__( 'Default Location', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_general_settings_section',
			array(
				'option_name' => 'aec_general_settings',
				'field_name'  => 'default_location',
				'options'     =>  aec_get_countries(),
				'description' => __( 'Select a country that must be pre-selected when adding a new venue.', 'another-events-calendar' )
			)
		);
		
		register_setting(
			$page_hook,
    		'aec_general_settings',
    		array( $this, 'sanitize_options' )
		);
		
		// Section : aec_calendar_settings_section
		add_settings_section(
			'aec_calendar_settings_section',
        	__( 'Calendar Page', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);

		add_settings_field( 
			'aec_calendar_settings[view_options]',
    		__( 'View Options', 'another-events-calendar' ),
    		array( $this, 'callback_multicheck' ),
    		$page_hook,
    		'aec_calendar_settings_section',
			array(
				'option_name' => 'aec_calendar_settings',
				'field_name'  => 'view_options',
				'options'     => array(
					'month' => __( 'Monthly view', 'another-events-calendar' ),
					'week'  => __( 'Weekly view', 'another-events-calendar' ),
					'day'   => __( 'Daily view', 'another-events-calendar' ),
				),
				'description' => __( 'You must select at least one view.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_calendar_settings[default_view]',
    		__( 'Default View', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_calendar_settings_section',
			array(
				'option_name' => 'aec_calendar_settings',
				'field_name'  => 'default_view',
				'options'     => array(
					'month' => __( 'Monthly view', 'another-events-calendar' ),
					'week'  => __( 'Weekly view', 'another-events-calendar' ),
					'day'   => __( 'Daily view', 'another-events-calendar' )					
				)
			)
		);
		
		add_settings_field( 
			'aec_calendar_settings[show_all_event_days]',
    		__( 'Show all days of multi-day events', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_calendar_settings_section',
			array(
				'option_name' => 'aec_calendar_settings',
				'field_name'  => 'show_all_event_days',
				'field_label' => __( 'When checked, events running on multiple days will appear on each of those dates in the calendar', 'another-events-calendar' ),
			)
		);
		
		register_setting(
			$page_hook,
    		'aec_calendar_settings',
    		array( $this, 'sanitize_options' )
		);

		
		// Section : aec_events_settings_section
		add_settings_section(
			'aec_events_settings_section',
        	__( 'Event Archive Pages', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
		
		add_settings_field( 
			'aec_events_settings[view_options]',
    		__( 'View Options', 'another-events-calendar' ),
    		array( $this, 'callback_multicheck' ),
    		$page_hook,
    		'aec_events_settings_section',
			array(
				'option_name' => 'aec_events_settings',
				'field_name'  => 'view_options',
				'options'     => array(
					'table'  => __( 'Table view', 'another-events-calendar' ),
					'grid'   => __( 'Grid view', 'another-events-calendar' ),
					'blog'   => __( 'Blog view', 'another-events-calendar' )					
				),
				'description' => __( 'You must select at least one view.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_events_settings[default_view]',
    		__( 'Default View', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_events_settings_section',
			array(
				'option_name' => 'aec_events_settings',
				'field_name'  => 'default_view',
				'options'     => array(
					'table'  => __( 'Table view', 'another-events-calendar' ),
					'grid'   => __( 'Grid view', 'another-events-calendar' ),
					'blog'   => __( 'Blog view', 'another-events-calendar' )
				)
			)
		);
		
		add_settings_field( 
			'aec_events_settings[orderby]',
    		__( 'Order Events By', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_events_settings_section',
			array(
				'option_name' => 'aec_events_settings',
				'field_name'  => 'orderby',
				'options'     => array(
					'title'            => __( 'Title', 'another-events-calendar' ),
					'date'       	   => __( 'Date posted', 'another-events-calendar' ),
					'event_start_date' => __( 'Event start date', 'another-events-calendar' )
				 )
			)	
		);
		
		add_settings_field( 
			'aec_events_settings[order]',
    		__( 'Sort Events By', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_events_settings_section',
			array(
				'option_name' => 'aec_events_settings',
				'field_name'  => 'order',
				'options'     => array(
					'asc'  => __( 'Ascending', 'another-events-calendar' ),
					'desc' => __( 'Descending', 'another-events-calendar' )
				)
			)
		);
		
		add_settings_field( 
			'aec_events_settings[no_of_cols]',
    		__( 'Number of columns ( grid view only )', 'another-events-calendar' ),
    		array( $this, 'callback_text' ),
    		$page_hook,
    		'aec_events_settings_section',
			array(
				'option_name' => 'aec_events_settings',
				'field_name'  => 'no_of_cols',
				'description' => __( 'Enter the number of columns in which the events should display in grid view.', 'another-events-calendar'  )
			)
		);
		
		add_settings_field( 
			'aec_events_settings[events_per_page]',
    		__( 'Number of events to show per page', 'another-events-calendar' ),
    		array( $this, 'callback_text' ),
    		$page_hook,
    		'aec_events_settings_section',
			array(
				'option_name' => 'aec_events_settings',
				'field_name'  => 'events_per_page',
				'description' => __( 'Enter the maximum number of events to show per page.', 'another-events-calendar' )
			)
		);
		
		register_setting(
			$page_hook,
    		'aec_events_settings',
    		array( $this, 'sanitize_options' )
		);

		// Section : aec_categories_settings_section
		add_settings_section(
			'aec_categories_settings_section',
        	__( 'Categories Page', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
				
		add_settings_field( 
			'aec_categories_settings[orderby]',
    		__( 'Order Categories By', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_categories_settings_section',
			array(
				'option_name' => 'aec_categories_settings',
				'field_name'  => 'orderby',
				'options'     => array(
					'id'    => __( 'Id', 'another-events-calendar' ),
					'count' => __( 'Count', 'another-events-calendar' ),
					'name'  => __( 'Name', 'another-events-calendar' ),
					'slug'  => __( 'Slug', 'another-events-calendar' )				
				)
			)
		);
		
		add_settings_field( 
			'aec_categories_settings[order]',
    		__( 'Sort Categories By', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_categories_settings_section',
			array(
				'option_name' => 'aec_categories_settings',
				'field_name'  => 'order',
				'options'     => array(
					'asc'  => __( 'Ascending', 'another-events-calendar' ),
					'desc' => __( 'Descending', 'another-events-calendar' )
				)
			)
		);
		
		add_settings_field( 
			'aec_categories_settings[show_events_count]',
    		__( 'Show Events count next to Category name', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_categories_settings_section',
			array(
				'option_name' => 'aec_categories_settings',
				'field_name'  => 'show_events_count',
				'field_label' => __( 'Check this to show events count next to the category name', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_categories_settings[hide_empty_categories]',
    		__( 'Hide Empty Categories?', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_categories_settings_section',
			array(
				'option_name' => 'aec_categories_settings',
				'field_name'  => 'hide_empty_categories',
				'field_label' => __( 'Check this to hide categories with no events', 'another-events-calendar' )
			)
		);
		
		register_setting(
			$page_hook,
    		'aec_categories_settings',
    		array( $this, 'sanitize_options' )
		);
		
    }
	
	/**
	 * Register page settings.
	 *
	 * @since    1.0.0
	 */
	function register_page_settings( $page_hook ) {
		
		// Section : aec_page_settings_section
		add_settings_section(
			'aec_page_settings_section',
        	__( 'Configure Pages', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
		
		add_settings_field( 
			'aec_page_settings[calendar]',
    		__( 'Calendar Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'calendar',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the page where your events are displayed in calendar. [aec_calendar] shortcode must be in this page.', 'another-events-calendar' )	
			)
		);
		
		add_settings_field( 
			'aec_page_settings[events]',
    		__( 'Events Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'events',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the page where all your events are displayed. [aec_events] shortcode must be in this page.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_page_settings[categories]',
    		__( 'Categories Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'categories',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the page where all your event categories are displayed. [aec_categories] shortcode must be in this page.', 'another-events-calendar' )
			)	
		);
		
		add_settings_field( 
			'aec_page_settings[category]',
    		__( 'Single Category Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'category',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the page where events from a single category are displayed. [aec_category] shortcode must be in this page.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_page_settings[tag]',
    		__( 'Single Tag Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'tag',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the page where events from a single tag are displayed. [aec_tag] shortcode must be in this page.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_page_settings[venue]',
    		__( 'Single Venue Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'venue',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the page where events from a single venue are displayed. [aec_venue] shortcode must be in this page.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_page_settings[organizer]',
    		__( 'Single Organizer Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'organizer',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the organizer profile page. [aec_organizer] shortcode must be in this page.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_page_settings[search]',
    		__( 'Search Results Page', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_page_settings_section',
			array(
				'option_name' => 'aec_page_settings',
				'field_name'  => 'search',
				'options'     => aec_get_pages(),
				'description' => __( 'This is the page where the event search results are displayed. [aec_search] shortcode must be in this page.', 'another-events-calendar' )
			)
		);
		
		register_setting(
			$page_hook,
    		'aec_page_settings',
    		array( $this, 'sanitize_options' )
		);
		
    }
	
	/**
	 * Register advanced settings.
	 *
	 * @since    1.0.0
	 */
	function register_advanced_settings( $page_hook ) {
		
		// Section : aec_permalink_settings_section
		add_settings_section(
			'aec_permalink_settings_section',
        	__( 'Permalink Settings', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
		
		add_settings_field( 
			'aec_permalink_settings[event_slug]',
    		__( 'Single event URL slug', 'another-events-calendar' ),
    		array( $this, 'callback_text' ),
    		$page_hook,
    		'aec_permalink_settings_section',
			array(
				'option_name' => 'aec_permalink_settings',
				'field_name'  => 'event_slug',
				'description' => __( "A typical single event page will include the alias 'aec_events' in it's URL. We know this is ugly and you can change this using this field.  The value entered in this field must be a string and should be unique. Avoid using generic terms like 'events', 'event', etc.. and this may lead conflict with other pages those use the same slug. So, care must be taken while editing this field.", 'another-events-calendar' ).'<br /><strong>'.__( 'Example URL', 'another-events-calendar' ).'</strong> : http://mysite.com/aec_events/single-post-name/'
			)
		);
		
		register_setting(
    		$page_hook,
    		'aec_permalink_settings',
    		array( $this, 'sanitize_options' )
		);
		
		// Section : aec_currency_settings_section
		add_settings_section(
			'aec_currency_settings_section',
        	__( 'Currency Settings', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
		
		add_settings_field( 
			'aec_currency_settings[currency]',
    		__( 'Currency', 'another-events-calendar' ),
    		array( $this, 'callback_text' ),
    		$page_hook,
    		'aec_currency_settings_section',
			array(
				'option_name' => 'aec_currency_settings',
				'field_name'  => 'currency',
				'description' => __( 'Enter the currency value to display with your event prices.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_currency_settings[position]',
    		__( 'Currency Position', 'another-events-calendar' ),
    		array( $this, 'callback_select' ),
    		$page_hook,
    		'aec_currency_settings_section',
			array(
				'option_name' => 'aec_currency_settings',
				'field_name'  => 'position',
				'options'     => array(
					'before' => __( 'Before - $10', 'another-events-calendar' ),
					'after'  => __( 'After - 10$', 'another-events-calendar' )
				),
				'description' => __( 'Choose the location of the currency sign.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_currency_settings[thousands_separator]',
    		__( 'Thousands Separator', 'another-events-calendar' ),
    		array( $this, 'callback_text' ),
    		$page_hook,
    		'aec_currency_settings_section',
			array(
				'option_name' => 'aec_currency_settings',
				'field_name'  => 'thousands_separator',
				'description' => __( 'The symbol (usually , or .) to separate thousands.', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_currency_settings[decimal_separator]',
    		__( 'Decimal Separator', 'another-events-calendar' ),
    		array( $this, 'callback_text' ),
    		$page_hook,
    		'aec_currency_settings_section',
			array(
				'option_name' => 'aec_currency_settings',
				'field_name'  => 'decimal_separator',
				'description' => __( 'The symbol (usually , or .) to separate decimal points.', 'another-events-calendar' )
			)
		);
		
		register_setting(
			$page_hook,
    		'aec_currency_settings',
    		array( $this, 'sanitize_options' )
		);
		
		// Section : aec_map_settings_section
		add_settings_section(
			'aec_map_settings_section',
        	__( 'Map Settings', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
		
		add_settings_field( 
			'aec_map_settings[enabled]',
    		__( 'Enable Google Maps', 'another-events-calendar' ),
    		array( $this, 'callback_checkbox' ),
    		$page_hook,
    		'aec_map_settings_section',
			array(
				'option_name' => 'aec_map_settings',
				'field_name'  => 'enabled',
				'field_label' => __( 'Check this to enable maps', 'another-events-calendar' )
			)
		);
		
		add_settings_field( 
			'aec_map_settings[zoom_level]',
    		__( 'Default Zoom Level', 'another-events-calendar' ),
    		array( $this, 'callback_text' ),
    		$page_hook,
    		'aec_map_settings_section',
			array(
				'option_name' => 'aec_map_settings',
				'field_name'  => 'zoom_level',
				'description' => __( '0 = zoomed out; 21 = zoomed in.', 'another-events-calendar' )
			)
		);
		
		register_setting(
			$page_hook,
    		'aec_map_settings',
    		array( $this, 'sanitize_options' )
		);
		
		// Section : aec_socialshare_settings_section
		add_settings_section(
			'aec_socialshare_settings_section',
        	__( 'Social-share Settings', 'another-events-calendar' ),
        	array( $this, 'section_callback' ),
        	$page_hook
    	);
		
		add_settings_field( 
			'aec_socialshare_settings[services]',
    		__( 'Enable Services', 'another-events-calendar' ),
    		array( $this, 'callback_multicheck' ),
    		$page_hook,
    		'aec_socialshare_settings_section',
			array(
				'option_name' => 'aec_socialshare_settings',
				'field_name'  => 'services',
				'options'     => array(
					'facebook' 	 => __( 'Facebook', 'another-events-calendar' ),
					'twitter'  	 => __( 'Twitter', 'another-events-calendar' ),
					'gplus'   	 => __( 'GPlus', 'another-events-calendar' ),
					'linkedin'   => __( 'Linkedin', 'another-events-calendar' ),		
					'pinterest'  => __( 'Pinterest', 'another-events-calendar' )							
				)
			)
		);
		
		add_settings_field( 
			'aec_socialshare_settings[pages]',
    		__( 'Show in pages', 'another-events-calendar' ),
    		array( $this, 'callback_multicheck' ),
    		$page_hook,
    		'aec_socialshare_settings_section',
			array(
				'option_name' => 'aec_socialshare_settings',
				'field_name'  => 'pages',
				'options'     => array(
					'categories'   	 => __( 'Categories', 'another-events-calendar' ),
					'event_archives' => __( 'Event archives', 'another-events-calendar' ),
					'event_detail'   => __( 'Event detail page', 'another-events-calendar' )
				
				)
			)
		);
		
		register_setting(
    		$page_hook,
    		'aec_socialshare_settings',
    		array( $this, 'sanitize_options' )
		);
		
    }
	
	/**
	 * Displays description of each sections.
	 *
	 * @since    1.0.0
	 *
	 * @params	 array    $args    settings section args.
	 */
	public function section_callback( $args ) {	
	
		switch( $args['id'] ) {
			case 'aec_page_settings_section' :
				_e( "During the plugin activation, Another Events Calendar will add some pages dynamically to your site those are neccessary for the functional flow of the plugin. You can change those pages under this section. Please don't change these settings unless neccessary. Mis-configuration of these settings may break the plugin form working correctly. So, care must be taken while editing the settings under this section.", 'another-events-calendar' );
				break;
		}	
		
    }
	
	/**
	 * Displays a text field with the field description for a settings field.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_text( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? esc_attr( $values[ $args['field_name'] ] ) : '';
	
		// Echo proper textarea
		echo '<input type="text" id="'.$id.'" name="'.$name.'" size="50" value="'.$value.'" />';
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a textarea with the field description for a settings field.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_textarea( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? esc_textarea( $values[ $args['field_name'] ] ) : '';
	
		// Echo proper textarea
		echo '<textarea id="'.$id.'" name="'.$name.'" rows="6" cols="60">'.$value.'</textarea>';
	
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a rich text textarea with the field description for a settings field.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_wysiwyg( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : '';
		
		// Echo wordpress editor
		wp_editor(
			$value,
			$id,
			array(
				'textarea_name' => $name,
				'media_buttons' => false,
				'quicktags'     => true,
				'editor_height' => 250
			)
	  	);
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<pre>'.$args['description'].'</pre>';
		}
		
	}
	
	/**
	 * Displays a checkbox with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_checkbox( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$checked = ( isset( $values[ $args['field_name'] ] ) && $values[ $args['field_name'] ] == 1 ) ? ' checked="checked"' : '';
		
		// Echo proper input type="checkbox"
		echo '<label for="'.$id.'">';
		echo '<input type="checkbox" id="'.$id.'" name="'.$name.'" value="1"'.$checked.'/>';
		echo $args['field_label'];
		echo '</label>';
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays multiple checkboxes with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_multicheck( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$values = isset( $values[ $args['field_name'] ] ) ? (array) $values[ $args['field_name'] ] : array();	

		// Echo proper input type="checkbox"
		foreach( $args['options'] as $value => $label ) {
			$checked = in_array( $value, $values ) ? ' checked="checked"' : '';
		
			echo '<p>';
			echo '<label for="'.$id.'_'.$value.'">';
			echo '<input type="checkbox" id="'.$id.'_'.$value.'" name="'.$name.'[]" value="'.$value.'"'.$checked.'/>';
			echo $label;
			echo '</label>';
			echo '</p>';
		}
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a radio button group with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_radio( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$checked = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : '';	

		// Echo proper input type="radio"
		foreach( $args['options'] as $key => $label ) {
			echo '<p>';
			echo "<label for='".$id."_".$key."'>";
			echo "<input type='radio' id='".$id."_".$key."' name='".$name."' value='".$key."'".checked( $checked, $key, false )."/>";
			echo $label;
			echo "</label>";
			echo "</p>";
		}
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a selectbox with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_select( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$selected = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : '';	
	
		// Echo proper selectbox
		echo '<select id="'.$id.'" name="'.$name.'">'; 
		foreach( $args['options'] as $value => $label ) { 
			echo '<option value="'.$value.'"'.selected( $selected, $value, false ).'>'.$label.'</option>';  
		} 
		echo '</select>';
		
		// Echo the field description from the $args array
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Sanitize settings.
	 *
	 * @since    1.0.0
	 */
	function sanitize_options( $input ) {

		$output = array();
	
		if( ! empty( $input ) ) {
		
			foreach( $input as $key => $value ) {

				switch( $key ) {
					// Sanitize text field
					case 'default_location':
					case 'event_slug' :
					case 'currency':
					case 'thousands_separator':
					case 'decimal_separator':
						$output[ $key ] = sanitize_text_field( $input[ $key ] );
						break;
					// Sanitize text field[integer]
					case 'no_of_cols' :
					case 'events_per_page':
					case 'zoom_level':
						$output[ $key ] = (int) $input[ $key ];
						break;
					// Sanitize checkbox
					case 'has_tags':
					case 'has_venues' :	
					case 'has_organizers':	
					case 'show_comments':
		            case 'show_past_events':
					case 'show_all_event_days':
					case 'show_events_count':
					case 'hide_empty_categories':
					case 'enabled':
						$output[ $key ] = (int) $input[ $key ];
						break;
					// Sanitize multi-checkbox
					case 'bootstrap' :
					case 'view_options':
					case 'services':
					case 'pages':
						$output[ $key ] = array_map( 'esc_attr', $input[ $key ] );
						break;
					// Sanitize select or radio field
					case 'default_view' :
					case 'orderby':
					case 'order':
					case 'calendar':
					case 'events':
					case 'categories':
					case 'category':
					case 'tag':
					case 'venue':
					case 'organizer':
					case 'search':					
					case 'position':
						$output[ $key ] = sanitize_key( $input[ $key ] );
						break;
					// Default sanitize method
					default :
						$output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );	
				}			
	
			}
		
		}
		
		return $output;
		
    }	
	
}