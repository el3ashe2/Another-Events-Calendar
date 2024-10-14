<?php

/**
 * Categories.
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
 * AEC_Admin_Categories Class
 *
 * @since    1.0.0
 */
class AEC_Admin_Categories {	
	 
	 /**
	 * Register the custom taxonomy "aec_categories".
	 * 
	 * @since    1.0.0
	 */
	public function register_taxonomy() {
		
		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'another-events-calendar' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'another-events-calendar' ),
			'menu_name'                  => __( 'Event Categories', 'another-events-calendar' ),
			'all_items'                  => __( 'All Categories', 'another-events-calendar' ),
			'parent_item'                => __( 'Parent Category', 'another-events-calendar' ),
			'parent_item_colon'          => __( 'Parent Category:', 'another-events-calendar' ),
			'new_item_name'              => __( 'New Category Name', 'another-events-calendar' ),
			'add_new_item'               => __( 'Add New Category', 'another-events-calendar' ),
			'edit_item'                  => __( 'Edit Category', 'another-events-calendar' ),
			'update_item'                => __( 'Update Category', 'another-events-calendar' ),
			'view_item'                  => __( 'View Category', 'another-events-calendar' ),
			'separate_items_with_commas' => __( 'Separate Categories with commas', 'another-events-calendar' ),
			'add_or_remove_items'        => __( 'Add or remove Categories', 'another-events-calendar' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'another-events-calendar' ),
			'popular_items'              => NULL,
			'search_items'               => __( 'Search Categories', 'another-events-calendar' ),
			'not_found'                  => __( 'Not Found', 'another-events-calendar' ),
		);
		
		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,			
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'query_var'         => true
		);
		
		register_taxonomy( 'aec_categories', array( 'aec_events' ), $args );
		
	}	
 
}