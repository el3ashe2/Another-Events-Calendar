<?php  

/**
 * Events.
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
 * AEC_Public_Events Class
 *
 * @since    1.0.0
 */
class  AEC_Public_Events {

	/**
	 * Get things started.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {
	
		global $post;
		
		add_shortcode( 'aec_events', array( $this, 'shortcode_aec_events' ) );

	}

	/**
	 * Process the shortcode [aec_events].
	 *
	 * @since    1.0.0
	 */
	public function shortcode_aec_events() {   

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
		
		if( $aec_query->have_posts() ) {
			
			ob_start();
			include AEC_PLUGIN_DIR."public/partials/events/aec-public-events-$view-display.php";
			return ob_get_clean();
			
		} else {
		
			return __( 'Sorry, no results matched your criteria.', 'another-events-calendar' );
			
		}
				
	}	
	
	/**
	 * Unset featured image.
	 *
	 * @since    1.0.0
	 */
	public function post_thumbnail_html( $html ) {
	
		if( is_singular('aec_events') && in_the_loop() ) {
			return '';
		}
	
		return $html;
	
	}
	
	/**
	 * Modify the content of single event page.
	 *
	 * @since    1.0.0
	 */
	public function the_content( $content ){
		
		if( is_singular('aec_events') && in_the_loop() ) {

			$post_id = get_the_ID();
			$map_settings = get_option( 'aec_map_settings' );
			
			// Vars
			$title = get_the_title();
			
			$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id  ), 'single-post-thumbnail' ); 
			$post_thumbnail_src = ! empty( $post_thumbnail ) ? $post_thumbnail[0] : '';
			
			$description = $content;
			
			$all_day_event   = get_post_meta( $post_id , 'all_day_event', true );
			$start_date_time = get_post_meta( $post_id , 'start_date_time', true );
			$end_date_time   = get_post_meta( $post_id , 'end_date_time', true );
			
			if( '0000-00-00 00:00:00' == $end_date_time ) {	
				$end_date_time = '';
			} else {
				// Find the time difference between start date and end date
				$datetime1 = new DateTime( $start_date_time );
				$datetime2 = new DateTime( $end_date_time );
				$interval = $datetime1->diff( $datetime2 );
				$day_diff = $interval->format('%a');
			
				if( $all_day_event ) {
					 $end_date_time = ( $day_diff > 0 ) ? date_i18n( get_option('date_format'), strtotime( $end_date_time ) ) : '';
				} else {
					 $end_date_time = ( $day_diff < 1 ) ? date_i18n( get_option('time_format'), strtotime( $end_date_time ) ) : date_i18n( get_option('date_format').' - '.get_option('time_format'), strtotime( $end_date_time ) );
				}
			}
			
			if( $all_day_event ) {
				 $start_date_time = date_i18n( get_option('date_format'), strtotime( $start_date_time ) );
			} else {
				 $start_date_time = date_i18n( get_option('date_format').' - '.get_option('time_format'), strtotime( $start_date_time ) );
			}
			
			$cost			 = get_post_meta( $post_id , 'cost', true );
			$categories      = get_the_terms( $post_id , 'aec_categories' );
			$tags            = get_the_terms( $post_id, 'aec_tags' );
			
			$venue_id  		 = get_post_meta( $post_id, 'venue_id', true );	
					
			$hide_map 		 = ! empty( $map_settings['enabled'] ) ? get_post_meta( $venue_id, 'hide_map', true ) : 1;
			$latitude		 = get_post_meta( $venue_id, 'latitude', true );
			$longitude 		 = get_post_meta( $venue_id, 'longitude', true );
			
			$has_map = 0;
			if( ! $hide_map && $latitude && $longitude ) {
				$has_map = 1;
			}
			
			// Organizers
			$organizers = array();
			
			$organizer_ids = get_post_meta( $post_id, 'organizers', true );
			if( ! empty( $organizer_ids ) ) {
				foreach( $organizer_ids as $organizer_id ) {			
					$organizers[] = array(
						'id'      => $organizer_id,
						'name'    => get_the_title( $organizer_id ),
						'phone'   => get_post_meta( $organizer_id, 'phone', true ),
						'email'   => get_post_meta( $organizer_id, 'email', true ),
						'website' => get_post_meta( $organizer_id, 'website', true ),
						
					);
				}
			};
			
			$organizers = (array) $organizers;

			ob_start();	
			include AEC_PLUGIN_DIR.'public/partials/events/aec-public-single-event-display.php';
			return ob_get_clean();
		
		}
		
		 return $content;
	
	}	

}