<?php

/**
 * Search Form.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/widgets/search
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WP_Widget Class
 *
 * @since    1.0.0
 */
class AEC_Widget_Search extends WP_Widget {

	/**
 	 * Get things started.
     *
     * @since    1.0.0
     */
	public function __construct() {

		$widget_ops = array( 
			'classname'   => 'aec-widget-search',
			'description' => __( 'Search events added through Another Events Calendar plugin.', 'another-events-calendar' ),
		);
		
		parent::__construct( 'aec-widget-search', __( 'Search Events', 'another-events-calendar' ) , $widget_ops );
		
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
	 * @since    1.0.0
	 *
	 * @param    array     $args
	 * @param    array     $instance
	 */
	public function widget( $args, $instance ) {
		
		echo $args['before_widget'];
		
		if( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		$display               = isset( $instance['display'] ) ? $instance['display'] : 'vertical';
		$search_by_category    = isset( $instance['search_by_category'] )    ? $instance['search_by_category']    : 0;
		$search_by_venue       = isset( $instance['search_by_venue'] )       ? $instance['search_by_venue']       : 0;
		$search_by_single_date = isset( $instance['search_by_single_date'] ) ? $instance['search_by_single_date'] : 0;	
		$search_by_date_range  = isset( $instance['search_by_date_range'] )  ? $instance['search_by_date_range']  : 0;			
		$search                = isset( $_GET['q'] ) ? sanitize_text_field( $_GET['q'] ) : '';		
		$widget_id             = $args['widget_id'].'-wrapper';
		
		// Get Search page id
		$page_settings = get_option( 'aec_page_settings' );
		$search_id     = $page_settings['search'];
		
		// Get Categories
		if( $search_by_category ) {
			$categories = get_terms( 'aec_categories' );
		}
		
		// Get Venues
		if( $search_by_venue ) {
			$venues = get_posts(
				array(
					'post_type'      => 'aec_venues',
					'post_status'    => 'publish',
					'posts_per_page' => '-1'
				)
			);
		}
		
		include AEC_PLUGIN_DIR.'widgets/search/views/widget.php';	
		
		echo $args['after_widget'];
		
	}

	/**
	 * Display the options form on admin.
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $instance    The widget options
	 */
	public function form( $instance ) {	
		
		$title                 = isset( $instance['title'] )   ? $instance['title']   : __( 'Search Events', 'another-events-calendar' );
		$display               = isset( $instance['display'] ) ? $instance['display'] : 'vertical';
		$search_by_category    = isset( $instance['search_by_category'] )    ? $instance['search_by_category']    : 0;
		$search_by_venue       = isset( $instance['search_by_venue'] )       ? $instance['search_by_venue']       : 0;
		$search_by_single_date = isset( $instance['search_by_single_date'] ) ? $instance['search_by_single_date'] : 0;	
		$search_by_date_range  = isset( $instance['search_by_date_range'] )  ? $instance['search_by_date_range']  : 0;	
		     
        include AEC_PLUGIN_DIR.'widgets/search/views/form.php';
		                    
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
		
		$instance['title']                 = isset( $new_instance['title'] )   ? strip_tags( $new_instance['title'] )   : '';
		$instance['display']               = isset( $new_instance['display'] ) ? strip_tags( $new_instance['display'] ) : 'vertical';
		$instance['search_by_category']    = isset( $new_instance['search_by_category'] )    ? 1 : 0;
		$instance['search_by_venue']       = isset( $new_instance['search_by_venue'] )       ? 1 : 0;
		$instance['search_by_single_date'] = isset( $new_instance['search_by_single_date'] ) ? 1 : 0;
		$instance['search_by_date_range']  = isset( $new_instance['search_by_date_range'] )  ? 1 : 0;
		
		return $instance;
		
	}
		
}

add_action( 'widgets_init', function() {
    register_widget( 'AEC_Widget_Search' );
} );
