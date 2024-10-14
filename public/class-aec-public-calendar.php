<?php   

/**
 * The public-facing functionality of the plugin.
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
 * AEC_Public_Calendar Class
 *
 * @since    1.0.0
 */
class  AEC_Public_Calendar {
	
	/**
	 * Get things started.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) { 
		
		add_shortcode( 'aec_calendar', array( $this, 'shortcode_aec_calendar' ) );

	}

	/**
	 * Process shortcode [aec_calendar].
	 *
	 * @since    1.0.0
	 *
	 */
	function shortcode_aec_calendar() {

		// Load dependencies
		wp_enqueue_style( AEC_PLUGIN_SLUG );  
		
		// Vars
		$calendar_settings = get_option( 'aec_calendar_settings' );
		
		$view_options = $calendar_settings['view_options'];
		$view_options[] = $calendar_settings['default_view'];		
		$calendar_settings['view_options'] = array_unique( $view_options );

		$view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : $calendar_settings['default_view'];
		
		switch( $view ) {
			case 'week' :
				return $this->weekly_view( $calendar_settings );
			case 'day' :
				return $this->daily_view( $calendar_settings ); 
			default :
				return $this->monthly_view( $calendar_settings ); 
		}
		
	}
	
	/**
	 * Monthly Calendar view
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $calendar_settings    Calendar Settings.
	 * @return   string                         HTML. Monthly Calendar.
	 */
	public function monthly_view( $calendar_settings ) {
		
		$general_settings = get_option( 'aec_general_settings' );
		$page_settings 	  = get_option( 'aec_page_settings' );
		$calendar_id      = $page_settings['calendar'];
				
		// Vars
		$today = date('Y-m-d');
		$month = isset( $_GET['mo'] ) ? (int) $_GET['mo'] : date('n');
		$year  = isset( $_GET['yr'] ) ? (int) $_GET['yr'] : date('Y');	
		$date  = $year.'-'.sprintf( '%02d', $month );	 
		$days_in_month = date( 't', mktime( 0, 0, 0, $month, 1, $year ));
		
		$can_query_events = 1;
		$events = array();
		
		if( empty( $general_settings['show_past_events'] ) ) {
			if( strtotime( $date.'-'.$days_in_month ) < strtotime( $today ) ) {
				$can_query_events = 0;
			}
		}
		
		if( $can_query_events ) {
		
			// Query
			$args = array( 
				'post_type'      => 'aec_events',
				'post_status'    => 'publish', 
				'posts_per_page' => -1
			);
		
			$meta_queries = array();
		
			if( ! empty( $calendar_settings['show_all_event_days'] ) ) {

				$meta_queries[] = array(
					'relation' => 'OR',
					array(
						'key'     => 'start_date_time',
						'value'	  => $date,
						'compare' => 'LIKE',
					),
					array(
						'key'     => 'end_date_time',
						'value'	  => $date,
						'compare' => 'LIKE',
					),
					array(
						'relation' => 'AND',
						array(
							'key'     => 'start_date_time',
							'value'	  => $date.'-01 00:00:00',
							'compare' => '<',
							'type'    => 'DATETIME'
						),
						array(
							'key'     => 'end_date_time',
							'value'	  => $date.'-'.$days_in_month.' 23:59:59',
							'compare' => '>',
							'type'    => 'DATETIME'
						)
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
			 
			} else {
		
				$meta_queries[] = array(
					'key'     => 'start_date_time',
					'value'	  => $date,
					'compare' => 'LIKE',
				);
			
				if( empty( $general_settings['show_past_events'] ) ) { 
					$meta_queries[] = array(
						'key'     => 'start_date_time',
						'value'	  => current_time('mysql'),
						'compare' => '>=',
						'type'    => 'DATETIME'
					);
				}
			
			}
		
			$count_meta_queries = count( $meta_queries );
			if( $count_meta_queries ) {
				$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
			}
		
			$items = get_posts( $args );

			foreach( $items as $item ) {
		
				$start_date_time = get_post_meta( $item->ID, 'start_date_time', true );
				$end_date_time   = get_post_meta( $item->ID, 'end_date_time', true );
			
				if( ! empty( $calendar_settings['show_all_event_days'] ) ) {
			
					$start_date = explode( ' ', trim( $start_date_time ) );
					$start_date = $start_date[0];
					if( empty( $general_settings['show_past_events'] ) ) { 
						if( strtotime( $start_date ) < strtotime( $today ) ) $start_date = $today;
					}
				
					$end_date = explode( ' ', trim( $end_date_time ) );
					$end_date = $end_date[0];
				
					$dates = aec_get_dates_from_range( $start_date, $end_date );
	
					foreach( $dates as $date ) {
						if( ! array_key_exists( $date, $events ) ) $events[ $date ] = array();
						$events[ $date ][] = $item;
					}
				
				} else { 
			
					// Find day using start_date_time
					$start_date = explode( ' ', trim( $start_date_time ) );
					$date = trim( $start_date[0] );
			
					if( ! array_key_exists( $date, $events ) ) $events[ $date ] = array();
					$events[ $date ][] = $item;
				
				}
			
			}
		
		}
		
		ob_start();
		include AEC_PLUGIN_DIR.'public/partials/calendar/aec-public-calendar-monthly-display.php';
		return ob_get_clean();
		
	}
	
	/**
	 * Weekly Calendar view
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $calendar_settings    Calendar Settings.
	 * @return   string                         HTML. Weekly Calendar.
	 */
	public function weekly_view( $calendar_settings ) {
		
		$general_settings = get_option( 'aec_general_settings' );
		
		// Vars
		$today = date( "Y-m-d" );
		
		if( ! isset( $_GET['date'] ) ) {
			$day = date('w');
			if( get_option( 'start_of_week' ) > 0 ) $day = $day - 1;
			$week_start = date( 'Y-m-d', strtotime( '-'.$day.' days' ) );
		} else {
			$week_start = $_GET['date'];
		}	
		
		$week_start_time  = strtotime( $week_start );
		$week_end         = date( 'Y-m-d', strtotime( "+6 days", $week_start_time ) );
		$week_end_time    = strtotime( $week_end );
		$week_days        = aec_get_dates_from_range( $week_start, $week_end );
		
		$can_query_events = 1;
		$events = array();
		
		if( empty( $general_settings['show_past_events'] ) ) {
			if( $week_end_time < strtotime( $today ) ) {
				$can_query_events = 0;
			}
		}
		
		if( $can_query_events ) {
			
			// Query
			$args = array(
				'post_type' 	 => 'aec_events',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby' 		 => 'start_date_time',
				'order'   		 => 'ASC'
			);
		
			$meta_queries = array();
		
			$date_start = $week_start.' 00:00:00';
			if( empty( $general_settings['show_past_events'] ) ) {
				$date_start = current_time('mysql');
			}
		
			if( ! empty( $calendar_settings['show_all_event_days'] ) ) {
		
				$meta_queries[] = array(
					'relation' => 'OR',
					array(
						'key'     => 'start_date_time',
						'value'	  => $date_start,
						'compare' => '>=',
						'type'    => 'DATETIME'
					),
					array(
						'key'     => 'end_date_time',
						'value'	  => $date_start,
						'compare' => '>=',
						'type'    => 'DATETIME'
					),
					array(
						'relation' => 'AND',
						array(
							'key'     => 'start_date_time',
							'value'	  => $date_start,
							'compare' => '<',
							'type'    => 'DATETIME'
						),
						array(
							'key'     => 'end_date_time',
							'value'	  => $week_end.' 23:59:59',
							'compare' => '>',
							'type'    => 'DATETIME'
						)
					)
				);
		
			} else {

				$meta_queries[] = array(
					'key'     => 'start_date_time',
					'value'	  => $date_start,
					'compare' => '>=',
					'type'    => 'DATETIME'
				);
		
			}
		
			$meta_queries[] = array(
				'key'     => 'start_date_time',
				'value'	  => $week_end.' 23:59:59',
				'compare' => '<=',
				'type'    => 'DATETIME'
			);
		
			$count_meta_queries = count( $meta_queries );
			if( $count_meta_queries ) {
				$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
			}
		
			$items = get_posts( $args );
		
			foreach( $items as $item ) {
		
				$start_date_time = get_post_meta( $item->ID, 'start_date_time', true );
				$end_date_time   = get_post_meta( $item->ID, 'end_date_time', true );
			
				if( ! empty( $calendar_settings['show_all_event_days'] ) ) {
				
					$start_date = explode( ' ', trim( $start_date_time ) );
					$start_date = $start_date[0];
					if( empty( $general_settings['show_past_events'] ) ) { 
						if( strtotime( $start_date ) < strtotime( $today ) ) $start_date = $today;
					}
				
					$end_date = explode( ' ', trim( $end_date_time ) );
					$end_date = $end_date[0];
				
					$dates = aec_get_dates_from_range( $start_date, $end_date );
	
					foreach( $dates as $date ) {					
						if( ! array_key_exists( $date , $events ) ) $events[ $date  ] = array();
						$events[ $date  ][] = $item;
					}
				
				} else {
			
					// Find day using start_date_time
					$start_date = explode( ' ', trim( $start_date_time ) );
					$date = $start_date[0];
			
					if( ! array_key_exists( $date, $events ) ) $events[ $date ] = array();
					$events[ $date ][] = $item;
			
				}
				
			}
			
		}
								
		ob_start();
		include AEC_PLUGIN_DIR.'public/partials/calendar/aec-public-calendar-weekly-display.php';
		return ob_get_clean();
	
	}
	
	/**
	 * Daily Calendar view
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $calendar_settings    Calendar Settings.
	 * @return   string                         HTML. Daily Calendar.
	 */
	public function daily_view( $calendar_settings ) {
		
		$general_settings = get_option( 'aec_general_settings' );
		
		$today = date('Y-m-d');
		$date  = isset( $_GET['date'] ) ? $_GET['date'] : date('Y-m-d');
		$can_query_events = 1;
		
		if( empty( $general_settings['show_past_events'] ) ) {
			if( strtotime( $date ) < strtotime( $today ) ) {
				$can_query_events = 0;
			}
		}
		
		if( $can_query_events ) {
		
			// Query
			$args = array(
				'post_type'      => 'aec_events',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby' 		 => 'start_date_time',
				'order'   		 => 'ASC'
			);
		
			$meta_queries = array();
		
			if( ! empty( $calendar_settings['show_all_event_days'] ) ) {
		
				$meta_queries[] = array(
					'relation' => 'OR',
					array(
						'key'     => 'start_date_time',
						'value'	  => $date,
						'compare' => 'LIKE'
					),
					array(
						'key'     => 'end_date_time',
						'value'	  => $date,
						'compare' => 'LIKE'
					),
					array(
						'relation' => 'AND',
						array(
							'key'     => 'start_date_time',
							'value'	  => $date.' 00:00:00',
							'compare' => '<',
							'type'    => 'DATETIME'
						),
						array(
							'key'     => 'end_date_time',
							'value'	  => $date.' 23:59:59',
							'compare' => '>',
							'type'    => 'DATETIME'
						)
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
		
			} else {
		
				$meta_queries[] = array(
					'key'     => 'start_date_time',
					'value'	  => $date,
					'compare' => 'LIKE'
				);
				
				if( empty( $general_settings['show_past_events'] ) ) {
					$meta_queries[] = array(
						'key'     => 'start_date_time',
						'value'	  => current_time('mysql'),
						'compare' => '>=',
						'type'    => 'DATETIME'
					);	
				}
		
			}
		
			$count_meta_queries = count( $meta_queries );
			if( $count_meta_queries ) {
				$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
			}

			$aec_query = new WP_Query( $args );
				
			global $post;
			
		}
			
		ob_start();
		include AEC_PLUGIN_DIR.'public/partials/calendar/aec-public-calendar-daily-display.php';
		return ob_get_clean();
			
	}
		
}
