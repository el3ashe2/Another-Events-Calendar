<?php

/**
 * Upcoming Events Widget.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/widgets/upcoming-events
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Widget_Upcoming_Events Class
 *
 * @since    1.0.0
 */
class AEC_Widget_Upcoming_Events extends WP_Widget {

	/**
 	 * Get things started.
     *
     * @since    1.0.0
     */
	public function __construct() {

		$widget_ops = array( 
			'classname'   => 'aec-widget-upcoming-events',
			'description' => __( 'Display upcoming events.', 'another-events-calendar' ),
		);
		
		parent::__construct( 'aec-widget-upcoming-events', __( 'Upcoming Events', 'another-events-calendar' ), $widget_ops );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );
		
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
	 * @param    array    $args
	 * @param    array    $instance
	 */
	public function widget( $args, $instance ) {

		$show_image	= isset( $instance['show_image'] ) ? $instance['show_image']  : 0;
		$show_date 	= isset( $instance['show_date'] )  ? $instance['show_date']   : 0;
		$show_venue = isset( $instance['show_venue'] ) ? $instance['show_venue']  : 0;
		$limit	    = ! empty( $instance['limit'] )    ? (int) $instance['limit'] : -1;	
		
		$widget_id = $args['widget_id'].'-wrapper';
		if( isset( $_POST['widget_id'] ) ) {
			$widget_id = $_POST['widget_id'];
		}
		
		// Vars
		$events_settings = get_option( 'aec_events_settings' );
		
		// Build query 
		$query = array(
			'post_type'      => 'aec_events', 
			'posts_per_page' => $limit,
			'meta_key'       => 'start_date_time',
			'orderby'        => 'meta_value',
			'order'  		 => 'asc',
			'post_status'	 => 'publish',
		);

		$meta_queries = array();
		
		$meta_queries[] = array(
			array(
				'key'     => 'start_date_time',
				'value'	  => current_time('mysql'),
				'compare' => '>=',
				'type'    => 'DATETIME'
			)
		);
				
		$count_meta_queries = count( $meta_queries );
		if( $count_meta_queries ) {
			$query['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
		}
		
		$aec_query = new WP_Query( $query );
		
		if( $aec_query->have_posts() ) {
			echo $args['before_widget'];
		
			if( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}
		
			include AEC_PLUGIN_DIR.'widgets/upcoming-events/views/widget.php';
		
			echo $args['after_widget'];
		}
		
	}

	/**
	 * Display the options form on admin.
	 *
	 * @param    array    $instance    The widget options
	 */
	public function form( $instance ) {
	
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Upcoming Events', 'another-events-calendar' );
		
		$show_image	= isset( $instance['show_image'] ) ? $instance['show_image'] : 0;
		$show_date  = isset( $instance['show_date'] )  ? $instance['show_date']  : 0;
		$show_venue = isset( $instance['show_venue'] ) ? $instance['show_venue'] : 0;	
		$limit      = isset( $instance['limit'] ) 	   ? $instance['limit']	     : '';	
		
		include AEC_PLUGIN_DIR.'widgets/upcoming-events/views/form.php';	  
		                    
	}     
 
	/**  
	 * Processing widget options on save    
	 * 
	 * @param    array    $new_instance    The new options
	 * @param    array    $old_instance    The previous options
	 */ 
	public function update( $new_instance, $old_instance ) {
	
		$instance = array();
		
		$instance['title'] 		= isset( $new_instance['title'] ) 	   ? strip_tags( $new_instance['title'] ) : '';
		$instance['show_image'] = isset( $new_instance['show_image'] ) ? 1 : 0;
		$instance['show_date']  = isset( $new_instance['show_date'] )  ? 1 : 0;
		$instance['show_venue'] = isset( $new_instance['show_venue'] ) ? 1 : 0;
		$instance['limit']      = isset( $new_instance['limit'] ) ? (int) $new_instance['limit'] : '';
		
		return $instance;
		
	}
	
}

add_action( 'widgets_init', function() {
    register_widget( 'AEC_Widget_Upcoming_Events' );
} );
