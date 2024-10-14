<?php

/**
 * Mini Calendar.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/widgets/mini-calendar
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Widget_Mini_Calendar Class
 *
 * @since    1.0.0
 */
class AEC_Widget_Mini_Calendar extends WP_Widget {

	/**
 	 * Get things started.
     *
     * @since    1.0.0
     */
	public function __construct() {

		$widget_ops = array( 
			'classname'   => 'aec-widget-mini-calendar',
			'description' => __( 'Display mini calendar. Users can see what dates have and click on to see the events from that date. If the date only has an event, click on the date will bring users to event detail page.', 'another-events-calendar' ),
		);		
		
		parent::__construct( 'aec-widget-mini-calendar', __( 'Mini Calendar', 'another-events-calendar' ), $widget_ops );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );
		add_action( 'wp_ajax_aec_mini_calendar', array( $this, 'display_calendar' ) );
		add_action( 'wp_ajax_nopriv_aec_mini_calendar', array( $this, 'display_calendar' ) );
		
	}

	/**
 	 * Enqueue styles and scripts.
     *
     * @since    1.0.0
     */
	function enqueue_styles_scripts() {
	
		if( is_active_widget( false, false, $this->id_base, true ) ) {
		
			wp_enqueue_style( AEC_PLUGIN_SLUG );
			wp_enqueue_script( AEC_PLUGIN_SLUG );
			
		}
		
	}
	
	/**
	 * Display the content of the widget.
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $args
	 * @param    array    $instance
	 */
	public function widget( $args, $instance ) {
		
		echo $args['before_widget'];
		
		if( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		$widget_id = $args['widget_id'].'-wrapper';
		$this->display_calendar( $widget_id );
		
		echo $args['after_widget'];
		
	}

	/**
	 * Display the options form on admin.
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $instance    The widget options
	 *
	 */
	public function form( $instance ) {
	
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Mini Calendar', 'another-events-calendar' );
		
		include AEC_PLUGIN_DIR.'widgets/mini-calender/views/form.php';	  
		  
	}     
 
	/**  
	 * Process widget options on save  .  
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $new_instance    The new options
	 * @param    array    $old_instance    The previous options
	 */
	public function update( $new_instance, $old_instance ) {
	
		$instance = array();
		
		$instance['title'] = isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		
		return $instance;
		
	}
	
	/**  
	 * Display mini calendar.   
	 *
	 * @since    1.0.0
	 *
	 * @param    string    $widget_id    Widget ID.
	 */
	public function display_calendar( $widget_id = 0 ) {  

		$calendar_settings = get_option( 'aec_calendar_settings' );
		$general_settings = get_option( 'aec_general_settings' );
		$page_settings = get_option( 'aec_page_settings' );
		
		$ajax  = false;	
		if( isset( $_POST['mo'] ) && isset( $_POST['yr'] ) ) {
			$ajax = true;
		}
		
		$today = date('Y-m-d');
		$month = isset( $_POST['mo'] ) ? (int) $_POST['mo'] : date('m');
		$year  = isset( $_POST['yr'] ) ? (int) $_POST['yr'] : date('Y');
		$date  = sprintf( '%d-%02d', $year, $month );	
		$days_in_month = date( 't', mktime( 0, 0, 0, $month, 1, $year )); 	

		if( isset( $_POST['widget_id'] ) ) {
			$widget_id = $_POST['widget_id']; 
		}		

		$calendar_page_id = $page_settings['calendar'];
		
		$can_query_events = 1;
		$events = array();
		
		if( empty( $general_settings['show_past_events'] ) ) {
			if( strtotime( $date.'-'.$days_in_month ) < strtotime( $today ) ) {
				$can_query_events = 0;
			}
		}
		
		if( $can_query_events ) {
		
			// Get Events for the given date		
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
		
			// Arrange Events data by day		
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
		
		include AEC_PLUGIN_DIR.'widgets/mini-calender/views/widget.php';
		
		if( $ajax == true ) wp_die();	
	
	}	

}

add_action( 'widgets_init', function() {
    register_widget( 'AEC_Widget_Mini_Calendar' );
} );
