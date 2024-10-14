<?php

/**
 * Venues.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/public
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Public_Venues Class
 *
 * @since    1.0.0
 */
class  AEC_Public_Venues {

	/**
	 * Get things started.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {
		
		add_shortcode( 'aec_venue', array( $this, 'shortcode_aec_venue' ) );

	}

	/**
	 * Process the shortcode [aec_venue];
	 *
	 * @since    1.0.0
	 */
	function shortcode_aec_venue( $atts ) {
	
		$venue_slug = get_query_var( 'aec_venue' ) ? get_query_var( 'aec_venue' ) : '';
		$error = 0;
		
		if( $venue_slug ) {
			$venue = get_page_by_path( $venue_slug, OBJECT, 'aec_venues' );
			if( ! $venue ) $error = 1;
		} else {
			$error = 1;
		}
		
		if( $error ) return __( 'Sorry, no results matched your criteria.', 'another-events-calendar' );
		
		// Load dependencies
		wp_enqueue_style( AEC_PLUGIN_SLUG );
		
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-google-map' );
		wp_enqueue_script( AEC_PLUGIN_SLUG.'-bootstrap' );
		wp_enqueue_script( AEC_PLUGIN_SLUG );
		 
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$events_settings = get_option( 'aec_events_settings' );
		$map_settings = get_option( 'aec_map_settings' );
		
		$has_map   = empty( $map_settings['enabled'] ) ? false : true;
		$latitude  = get_post_meta( $venue->ID, 'latitude', true );
		$longitude = get_post_meta( $venue->ID, 'longitude', true );
		
		$view_options = isset( $events_settings['view_options'] ) ? $events_settings['view_options'] : array();
		$view_options[] = $events_settings['default_view'];
		$view_options = array_unique( $view_options );

		$view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : $events_settings['default_view'];
		
		$no_of_cols = empty( $events_settings['no_of_cols'] ) ? 1 : $events_settings['no_of_cols'] ;
		$span = round( 12 / $no_of_cols );
		$count = 0;

		// Build query 
		$paged = aec_get_page_number();

		$args = array(
			'post_type'      => 'aec_events', 
			'posts_per_page' => empty( $events_settings['events_per_page'] ) ? -1 : $events_settings['events_per_page'],
			'order'  		 => $events_settings['order'],
			'paged'          => $paged,
			'post_status'	 => 'publish',
		);
		
		switch( $events_settings['orderby'] ) {
			case 'date':
				$args['orderby'] = 'date';
				break;
			case 'title':
				$args['orderby'] = 'title';
				break;
			case 'event_start_date':
				$args['meta_key'] = 'start_date_time';
				$args['orderby'] = 'meta_value';
				break;
		}
		
				
		$meta_queries = array();
		
		$meta_queries[] = array( 
			array( 
				'key' => 'venue_id', 
				'value' => $venue->ID, 
				'compare' => '=' 
			) 
		);

		if( empty( $general_settings['show_past_events'] ) ) { 
			$meta_queries[] = array(
				'relation' => 'OR',
				array(
					'key'     => 'start_date_time',
					'value'	  => current_time('mysql'),
					'compare' => '>=',
					'type'    => 'DATETIME'
				),
				array(
					'key'     => 'end_date_time',
					'value'	  => current_time('mysql'),
					'compare' => '>=',
					'type'    => 'DATETIME'
				)
			);
		} 
		
		$count_meta_queries = count( $meta_queries );
		if( $count_meta_queries ) {
			$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
		}
		
		$aec_query = new WP_Query( $args );
		
		ob_start();
		include AEC_PLUGIN_DIR.'public/partials/venues/aec-public-venue-header-display.php';
		if( $aec_query->have_posts() ) {
			global $post;
			include AEC_PLUGIN_DIR."public/partials/events/aec-public-events-$view-display.php";
		} else {
			_e( 'No events found.', 'another-events-calendar' );
		}
		return ob_get_clean();
		
	}

}
