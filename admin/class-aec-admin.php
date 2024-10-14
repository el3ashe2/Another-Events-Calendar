<?php

/**
 * The admin-specific functionality of the plugin.
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
 * AEC_Admin Class
 *
 * @since    1.0.0
 */
class AEC_Admin {

	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {  

		wp_enqueue_style( 'jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );		
		wp_enqueue_style( AEC_PLUGIN_SLUG, AEC_PLUGIN_URL.'admin/css/aec-admin.css', array(), AEC_PLUGIN_VERSION, 'all' );
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$map_settings = get_option( 'aec_map_settings' );
		
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-google-map', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false' );	
			
		wp_enqueue_script( AEC_PLUGIN_SLUG, AEC_PLUGIN_URL.'admin/js/aec-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), AEC_PLUGIN_VERSION, false );
		wp_localize_script( AEC_PLUGIN_SLUG, 'aec', array(
				'zoom_level' => ! empty( $map_settings['zoom_level'] ) ? (int) $map_settings['zoom_level'] : 5
			)
		);
		
	}
	
	 /**
	 * Set menu display order.
	 *
	 * @since    1.0.0
	 */
 	public function set_menu_order() {
	
		global $submenu;
		
  		$before = $after = array();
		
		$settings_slug = 'aec_tags';
		$tags_slug     = 'aec_settings';
		
  		foreach( $submenu['edit.php?post_type=aec_events'] as $item ) {
			if( strpos( $item[2], $tags_slug) !== false || $item[2] == $settings_slug ) {
      			$after[]  = $item;
    		} else {
      			$before[] = $item;
    		}
  		}
		
  		$submenu['edit.php?post_type=aec_events'] = array_values( array_merge( $before, $after ) );
		
	}	 
	
}