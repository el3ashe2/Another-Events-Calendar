<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://yendif.com
 * @since      1.0.0
 *
 * @package    another-events-calendar
 */

// If uninstall not called from WordPress, then exit.
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete all the Custom Post Types
$post_types = array( 'aec_events', 'aec_venues', 'aec_organizers' );

foreach( $post_types as $post_type ) {

	$items = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	
	if( $items ) {
		foreach( $items as $item ) {
			// Delete attachments (only if applicable)
			if( 'aec_events' == $post_type || 'aec_organizers' == $post_type ) {
				 wp_delete_attachment ( $item, true );
			}
			
			// Delete the actual post
			wp_delete_post( $item, true );
		}
	}
			
}

// Delete all the Terms & Taxonomies
$taxonomies = array( 'aec_categories', 'aec_tags' );

foreach( $taxonomies as $taxonomy ) {

	$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );
	
	// Delete Terms
	if( $terms ) {
		foreach( $terms as $term ) {
			$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
			$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
		}
	}
	
	// Delete Taxonomies
	$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );

}

// Delete the Plugin Pages
if( $created_pages = get_option( 'aec_page_settings' ) ) {

	foreach( $created_pages as $page => $id ) {

		if( $id > 0 ) {
			wp_delete_post( $id, true );
		}
	
	}

}

// Delete all the Plugin Options
$settings = array(
	'aec_general_settings',
	'aec_calendar_settings',
	'aec_events_settings',
	'aec_categories_settings',
	'aec_page_settings',
	'aec_permalink_settings',
	'aec_currency_settings',
	'aec_map_settings',
	'aec_socialshare_settings'
);

foreach( $settings as $settings ) {
	delete_option( $settings );
}

delete_option( 'aec_categories_children' );
delete_option( 'aec_tags_children' );
delete_option( 'aec_version' );