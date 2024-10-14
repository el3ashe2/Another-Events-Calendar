<?php

/**
 * Events.
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
 * AEC_Admin_Events Class
 *
 * @since    1.0.0
 */
class AEC_Admin_Events {
		
	 /**
	 * Register the Custom Post Type for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() { 	
	
		$general_settings   = get_option( 'aec_general_settings' );
		$permalink_settings = get_option( 'aec_permalink_settings' );
		
		$supports = array( 'title', 'editor', 'author', 'thumbnail' );		
		if( ! empty( $general_settings['show_comments'] ) ) {
			$supports[] = 'comments';
		}

		$labels = array(
			'name'                => _x( 'Events', 'Post Type General Name', 'another-events-calendar' ),
			'singular_name'       => _x( 'Event', 'Post Type Singular Name', 'another-events-calendar' ),
			'menu_name'           => __( 'Another Events Calendar', 'another-events-calendar' ),
			'name_admin_bar'      => __( 'Event', 'another-events-calendar' ),
			'all_items'           => __( 'All Events', 'another-events-calendar' ),
			'add_new_item'        => __( 'Add New Event', 'another-events-calendar' ),
			'add_new'             => __( 'Add New', 'another-events-calendar' ),
			'new_item'            => __( 'New Event', 'another-events-calendar' ),
			'edit_item'           => __( 'Edit Event', 'another-events-calendar' ),
			'update_item'         => __( 'Update Event', 'another-events-calendar' ),
			'view_item'           => __( 'View Event', 'another-events-calendar' ),
			'search_items'        => __( 'Search Event', 'another-events-calendar' ),
			'not_found'           => __( 'Not found', 'another-events-calendar' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'another-events-calendar' ),
		);
		
    	$args = array(
        	'labels'             => $labels,
			'supports'           => $supports,
            'has_archive'        => false,
			'public'             => true,        	
	        'show_ui'            => current_user_can( 'administrator' ) ? true : false,
			'show_in_menu'       => true,
			'menu_position'      => 7,
    	    'menu_icon'          => 'dashicons-calendar-alt',
        	'publicly_queryable' => true,
	       	'capability_type'    => 'post'         	
    	);   
		
		if( ! empty( $permalink_settings['event_slug'] ) ) {
			$args['rewrite'] = array(
				'slug' => $permalink_settings['event_slug']
			);
		} 
		
		register_post_type( 'aec_events', $args );
	
	} 
	
	/**
 	 * Register the meta boxes.
 	 *
 	 * @since    1.0.0
 	 */
	public function register_meta_boxes() {
	
    	$general_settings = get_option( 'aec_general_settings' );
		
		add_meta_box( 'aec-event-details', __( 'Time & Date', 'another-events-calendar' ), array( $this, 'display_meta_box_event_details' ), 'aec_events', 'normal', 'high' );
		add_meta_box( 'aec-cost-details', __( 'Event Cost', 'another-events-calendar' ), array( $this, 'display_meta_box_cost_details' ), 'aec_events', 'normal', 'high' );

		if( ! empty( $general_settings['has_venues'] ) ) {
			add_meta_box( 'aec-venue-details', __( 'Select Venue', 'another-events-calendar' ), array( $this, 'display_meta_box_venue_details' ), 'aec_events', 'normal', 'high' ); 
		}
		
		if( ! empty( $general_settings['has_organizers'] ) ) {
		    add_meta_box( 'aec-organizer-details', __( 'Select Organizers', 'another-events-calendar' ), array( $this, 'display_meta_box_organizer_details' ), 'aec_events', 'normal', 'high' );  
		}	
				   
	}
	
	/**
 	 * Display the event details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
    public function display_meta_box_event_details( $post ) {
	
		$all_day_event   = get_post_meta( $post->ID, 'all_day_event', true );
		
		$start_date_time = get_post_meta( $post->ID, 'start_date_time', true );
		if( ! empty( $start_date_time ) ) {
			$start_date = date( 'Y-m-d', strtotime( $start_date_time ) );
			$start_hour = date( 'G', strtotime( $start_date_time ) );
			$start_min  = date( 'i', strtotime( $start_date_time ) );
		} else {
			$start_date = $start_hour = $start_min = ''; 
		}		
		
		$end_date_time = get_post_meta( $post->ID, 'end_date_time', true );
		if( ! empty( $end_date_time ) && '0000-00-00 00:00:00' != $end_date_time ) {
			$end_date = date( 'Y-m-d', strtotime( $end_date_time ) ); 
			$end_hour = date( 'G', strtotime( $end_date_time ) );
			$end_min  = date( 'i', strtotime( $end_date_time ) );
		} else {
			$end_date = $end_hour = $end_min = ''; 
		}		
		
		wp_nonce_field( 'aec_save_events', 'aec_events_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-events-display.php';
			   				
	}
	
	/**
 	 * Display the event cost details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
    public function display_meta_box_cost_details( $post ) {
	
		$cost = get_post_meta( $post->ID, 'cost', true );
		
		wp_nonce_field( 'aec_save_event_cost', 'aec_event_cost_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-event-cost-display.php';
			   				
	}
	
	/**
 	 * Display the venue details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
	public function display_meta_box_venue_details( $post ) {
		
		$venues = get_posts(
			array(
				'post_type'      => 'aec_venues',
				'posts_per_page' => '-1'
			)
		);
		
		$venue_id = get_post_meta( $post->ID, 'venue_id', true );
		
		$general_settings = get_option( 'aec_general_settings' );
		$map_settings     = get_option( 'aec_map_settings' );
		
		$countries        = aec_get_countries();
		$default_location = $general_settings['default_location'];
		
	    wp_nonce_field( 'aec_save_venue', 'aec_venue_nonce' );

		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-venues-display.php';
	
	}
		
	/**
 	 * Display the organizer details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
	public function display_meta_box_organizer_details( $post ) {

		$organizers_list = get_posts(
			array(
				'post_type'   => 'aec_organizers',
				'posts_per_page' => '-1'
			)
	    );
		
		$organizers = get_post_meta( $post->ID, 'organizers', true );
		$organizers = (array) $organizers;
		
		wp_nonce_field( 'aec_save_organizers', 'aec_organizers_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/events/aec-admin-organizers-display.php';
		
   }

	/**
 	 * Save the meta box data.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    int       $post_id    Post ID.
	 * @param    object    $post       Post Object.
	 * @return   int       $post_id    Post ID.
 	 */
	public function save_meta_boxes( $post_id, $post ) {
	 
	 	// Bail if we're doing an auto save.
    	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		// Check if the post type = "aec_events".
		if ( 'aec_events' != $post->post_type ) {
			return $post_id;
		}
	
		// Check if the current user has permission to edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) { 
			return $post_id;
		}	 
	 	
		// Save event details
		if ( isset( $_POST['aec_events_nonce'] ) && wp_verify_nonce( $_POST['aec_events_nonce'], 'aec_save_events' ) ) {
			
			$all_day_event = isset( $_POST['all_day_event'] ) ? 1 : 0; 
			update_post_meta( $post_id, 'all_day_event', $all_day_event );
		
			$start_date_time = sprintf( '%s %02d:%02d:00', sanitize_text_field( $_POST['start_date'] ), $_POST['start_hour'], $_POST['start_min'] );
			update_post_meta( $post_id, 'start_date_time', $start_date_time );
			
			if( ! empty( $_POST['end_date'] ) ) {
				$end_date_time = sprintf( '%s %02d:%02d:00', sanitize_text_field( $_POST['end_date'] ), $_POST['end_hour'], $_POST['end_min'] );
			} else { 
				$end_date_time = '0000-00-00 00:00:00';
			}			
			update_post_meta( $post_id, 'end_date_time', $end_date_time );
			
		}
		
		// Save event cost
		if ( isset( $_POST['aec_event_cost_nonce'] ) && wp_verify_nonce( $_POST['aec_event_cost_nonce'], 'aec_save_event_cost' ) ) {
			
			$cost = aec_sanitize_amount( $_POST['cost'] );
			update_post_meta( $post_id, 'cost', $cost );
			
		}

		// Save venue details
		if ( isset( $_POST['aec_venue_nonce'] ) && wp_verify_nonce( $_POST['aec_venue_nonce'], 'aec_save_venue' ) ) {
			
			$venue_id = (int) $_POST['venue_id'];
			
			if( -1 == $venue_id && ! empty( $_POST['venue_name'] ) ) {
			
				// Insert new venue
				$args = array(
					'post_type'   => 'aec_venues',
					'post_title'  => sanitize_text_field( $_POST['venue_name'] ),
					'post_status' => 'publish'
				);
								
				$venue_id = wp_insert_post( $args );					
						
				$address = ! empty( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
				update_post_meta( $venue_id, 'address', $address );
						
				$city = ! empty( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
				update_post_meta( $venue_id, 'city', $city );
							
				$state = ! empty( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
				update_post_meta( $venue_id, 'state', $state );
				
				$country = ! empty( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
				update_post_meta( $venue_id, 'country', $country );

				$pincode = ! empty( $_POST['pincode'] ) ? sanitize_text_field( $_POST['pincode'] ) : '';
				update_post_meta( $venue_id, 'pincode', $pincode );
							
				$phone = ! empty( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
				update_post_meta( $venue_id, 'phone', $phone );
						
				$website = ! empty( $_POST['website'] ) ? esc_url_raw( $_POST['website'] ) : '';
				update_post_meta( $venue_id, 'website', $website );
						
				$hide_map = ! empty( $_POST['hide_map'] ) ? 1 : 0;
				update_post_meta( $venue_id, 'hide_map', $hide_map );
				
				$latitude = isset( $_POST['latitude'] ) ? sanitize_text_field( $_POST['latitude'] ) : '';
				update_post_meta( $venue_id, 'latitude', $latitude );  
		
				$longitude = isset( $_POST['longitude'] ) ? sanitize_text_field( $_POST['longitude'] ) : '';
				update_post_meta( $venue_id, 'longitude', $longitude ); 
				
			}
			
			if( ! empty( $venue_id ) ) {
				update_post_meta( $post_id, 'venue_id', $venue_id );
			}		
			
		}
				
		// Save organizer details
		if ( isset( $_POST['aec_organizers_nonce'] ) && wp_verify_nonce( $_POST['aec_organizers_nonce'], 'aec_save_organizers' ) ) {
		
			$organizer_ids = array();
			
			if( ! empty( $_POST['organizers'] ) ) {
				$organizer_ids = array_map( 'trim', $_POST['organizers'] );
			}
	
			// Insert new organizers	
			if( ! empty( $_POST['organizer_name'] ) ) {	
				
				$organizers = $_POST['organizer_name'];
				$organizers = array_map( 'trim', $organizers );	
				$organizers = array_filter( $organizers );
					
				foreach( $organizers as $key => $organizer ) {
					$args = array(
						'post_type'   => 'aec_organizers',
						'post_title'  => sanitize_text_field( $organizer ),
						'post_status' => 'publish'
					);
										
					$organizer_id = wp_insert_post( $args );
					
					$phone = ! empty( $_POST['organizer_phone'][ $key ] ) ? sanitize_text_field( $_POST['organizer_phone'][ $key ] ) : '';
					update_post_meta( $organizer_id, 'phone', $phone );
	
					$email = ! empty( $_POST['organizer_email'][ $key ] ) ? sanitize_email( $_POST['organizer_email'][ $key ] ) : '';
					update_post_meta( $organizer_id, 'email', $email );
						
					$website = ! empty( $_POST['organizer_website'][ $key ] ) ? esc_url_raw( $_POST['organizer_website'][ $key ] ) : '';
					update_post_meta( $organizer_id, 'website', $website );
					
					$organizer_ids[] = (string) $organizer_id;
				}
				
			}

			if( ! empty( $organizer_ids ) ) {
				update_post_meta( $post_id, 'organizers', $organizer_ids );
			}
			
		}		
		
	}
	
	/**
	 * Adds the categories filter.
	 *
	 * @since    1.0.0
	 *
	 */
	public function restrict_manage_posts() {
	
		global $typenow;
		
		$post_type = 'aec_events'; 
		$taxonomy  = 'aec_categories'; 
		
		if( $typenow == $post_type ) {
		
			$selected      = isset( $_GET[ $taxonomy ] ) ? (int) $_GET[ $taxonomy ] : '';
			$info_taxonomy = get_taxonomy( $taxonomy );
			
			wp_dropdown_categories( array(
				'show_option_none' => __( "Show All {$info_taxonomy->label}" ),
				'taxonomy'         => $taxonomy,
				'name'             => $taxonomy,
				'orderby'          => 'name',
				'selected'         => $selected,
				'hierarchical'     => true,
				'show_count'       => true,
				'hide_empty'       => false,
			));
			
		}
		
	}
	
	/**
	 * Convert term id into slug.
	 *
	 * @since    1.0.0
	 */
	public function parse_query( $query ) {
	
		global $pagenow;
		
		$post_type = 'aec_events'; 
		$taxonomy  = 'aec_categories';		
		$q_vars    = &$query->query_vars;
		
		if( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == $post_type && isset( $q_vars[ $taxonomy ] ) && is_numeric( $q_vars[ $taxonomy ] ) ) {
			if( $q_vars[ $taxonomy ] > 0 ) {
				$term = get_term_by( 'id', $q_vars[ $taxonomy ], $taxonomy );
				$q_vars[ $taxonomy ] = $term->slug;
			} else {			
				unset( $q_vars[ $taxonomy ] );
			}
		}
		
	}
	
	/**
	 * Display column values.
	 *
	 * @since    1.0.0
	 *
     * @param    int       $post_id    Post ID.
	 * @param    string    $columns    Column name.  
	 */
	public function manage_posts_custom_column( $columns, $post_id ) {

		switch( $columns ) {
			case 'start_date':
				$start_date_time = get_post_meta( $post_id, 'start_date_time', true );
				$start_date = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $start_date_time ) );
				echo ! empty( $start_date ) ? $start_date : '--';
				break;
			case 'end_date':
				$end_date_time = get_post_meta( $post_id, 'end_date_time', true );
				echo ( '0000-00-00 00:00:00' != $end_date_time ) ? date_i18n( get_option('date_format').' '.get_option('time_format'), strtotime( $end_date_time ) ) : "--";
				break;
		}
		
	}
	
	/**
	 * Add custom columns.
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $columns    Array of column names.
	 * @return   array                Updated column names.
	 */
	function manage_posts_columns( $columns ) {
    
		$n_columns = array(); 
		
		unset( $columns['taxonomy-aec_categories'] );
		
	 	foreach( $columns as $key => $value ) {
	  		if( 'author' == $key ) {
				$n_columns['taxonomy-aec_categories'] = __( 'Categories', 'another-events-calendar' );
	   			$n_columns['start_date']              = __( 'Start Date','another-events-calendar' );
				$n_columns['end_date']                = __( 'End Date', 'another-events-calendar' );
	    	} 
			
			$n_columns[ $key ] = $value; 
		} 
		
		return $n_columns;
			
	}
	
}