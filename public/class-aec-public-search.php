<?php

/**
 * Search.
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
 * AEC_Public_Search Class
 *
 * @since    1.0.0
 */
class  AEC_Public_Search {

	/**
	 * Get things started.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {
		
		add_shortcode( 'aec_search', array( $this, 'shortcode_aec_search' ) );

	}

	/**
	 * Process the shortcode [aec_search].
	 *
	 * @since    1.0.0
	 */
	function shortcode_aec_search( $atts , $post ) {   
	
		$search_query = isset( $_GET['aec'] ) ? sanitize_text_field( $_GET['aec'] ) : '';	
		if( ! $search_query ) return __( 'Sorry, no results matched your criteria.', 'another-events-calendar' );
		
		// Load dependencies
		wp_enqueue_style( AEC_PLUGIN_SLUG );
		
		// Vars
		$general_settings = get_option( 'aec_general_settings' );
		$events_settings = get_option( 'aec_events_settings' );
		
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
			's'              => $search_query,
		);
		
		$meta_queries = array();
		
		$tax_queries = array();
		
		if( !empty( $_GET['venue'] ) ) { 
			$meta_queries[] = array(
				'key'     => 'venue_id',
				'type'    => 'NUMERIC',
				'compare' => '=',
				'value'	  => (int) $_GET['venue'],
				
			);
		}
		
		if( !empty( $_GET['cat'] ) ) { 
			$tax_queries[] = array(
				'taxonomy' => 'aec_categories',
				'field'    => 'term_id',
				'terms'    => (int) $_GET['cat'],
				
			);
		}
		
		if( ! empty( $_GET['date'] ) )	{		
			$meta_queries[] = array(
				'key'    	=> 'start_date_time',
				'type'    	=> 'DATETIME',
				'compare' 	=> 'LIKE',
				'value'		=> sanitize_text_field( $_GET['date'] ),
			);
		}
		
		if( ! empty( $_GET['from'] ) && isset( $_GET['to'] ) )	{		
			$meta_queries[] = array(
				'key'    	=> 'start_date_time',
				'type'    	=> 'DATETIME',
				'compare' 	=> '>=',
				'value'		=> sanitize_text_field( $_GET['from'] ),
			);
			$meta_queries[] = array(
				'key'     	=> 'start_date_time',
				'type'    	=> 'DATETIME',
				'compare' 	=> '<=',
				'value'		=> sanitize_text_field( $_GET['to'] ),
			);	
		}
		
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
		
		$count_tax_queries = count( $tax_queries );
		if( $count_tax_queries ) {
			$args['tax_query'] = ( $count_tax_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $tax_queries ) : array( $tax_queries );
		}
		
		$aec_query = new WP_Query( $args );
		
		if( $aec_query->have_posts() ) {
		
			global $post;
			
			ob_start();
			include AEC_PLUGIN_DIR."public/partials/events/aec-public-events-$view-display.php";
			return ob_get_clean();
			
		} else {
		
			return __( 'Sorry, no results matched your criteria.', 'another-events-calendar' );
			
		}
			
	}
	
}
