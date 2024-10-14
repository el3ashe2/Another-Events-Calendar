<?php

/**
 * Tags.
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
 * AEC_Admin_Tags Class
 *
 * @since    1.0.0
 */
class AEC_Admin_Tags {	
	
	/**
	 * Register the custom taxonomy "aec_tags".
	 *
	 * @since    1.0.0
	 */		
	public function register_taxonomy() {
		
		$general_settings = get_option( 'aec_general_settings' );
		
		if( ! empty( $general_settings['has_tags'] ) ) {
	
			$labels = array(
				'name'                       => _x( 'Tags', 'Taxonomy General Name', 'another-events-calendar' ),
				'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'another-events-calendar' ),
				'menu_name'                  => __( 'Event Tags', 'another-events-calendar' ),
				'all_items'                  => __( 'All Tags', 'another-events-calendar' ),
				'parent_item'                => __( 'Parent Tag', 'another-events-calendar' ),
				'parent_item_colon'          => __( 'Parent Tag:', 'another-events-calendar' ),
				'new_item_name'              => __( 'New Tag Name', 'another-events-calendar' ),
				'add_new_item'               => __( 'Add New Tag', 'another-events-calendar' ),
				'edit_item'                  => __( 'Edit Tag', 'another-events-calendar' ),
				'update_item'                => __( 'Update Tag', 'another-events-calendar' ),
				'view_item'                  => __( 'View Tag', 'another-events-calendar' ),
				'separate_items_with_commas' => __( 'Separate Tags with commas', 'another-events-calendar' ),
				'add_or_remove_items'        => __( 'Add or remove Tags', 'another-events-calendar' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'another-events-calendar' ),
				'popular_items'              => __( 'Popular Tags', 'another-events-calendar' ),
				'search_items'               => __( 'Search Tags', 'another-events-calendar' ),
				'not_found'                  => __( 'Not Found', 'another-events-calendar' ),
			);
		
			$args = array(
				'labels'            => $labels,
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,			
				'show_admin_column' => false,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'query_var'         => true
			);
		
			register_taxonomy( 'aec_tags', array( 'aec_events' ), $args );
		
		}
	
	}	
 
}