<?php

/**
 * Venues.
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
 * AEC_Admin_Venues Class
 *
 * @since    1.0.0
 */
class AEC_Admin_Venues {
	
	/**
	 * Register the custom post type "aec_venues".
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() {
	
		$general_settings = get_option( 'aec_general_settings' );
		
		if( ! empty( $general_settings['has_venues'] ) ) {
		
			$labels = array(
				'name'                => _x( 'Venues', 'Post Type General Name', 'another-events-calendar' ),
				'singular_name'       => _x( 'Venue', 'Post Type Singular Name', 'another-events-calendar' ),
				'menu_name'           => __( 'Venues', 'another-events-calendar' ),
				'name_admin_bar'      => __( 'Venue', 'another-events-calendar' ),
				'all_items'           => __( 'Venues', 'another-events-calendar' ),
				'add_new_item'        => __( 'Add New Venue', 'another-events-calendar' ),
				'add_new'             => __( 'Add New', 'another-events-calendar' ),
				'new_item'            => __( 'New Venue', 'another-events-calendar' ),
				'edit_item'           => __( 'Edit Venue', 'another-events-calendar' ),
				'update_item'         => __( 'Update Venue', 'another-events-calendar' ),
				'view_item'           => __( 'View Venue', 'another-events-calendar' ),
				'search_items'        => __( 'Search Venue', 'another-events-calendar' ),
				'not_found'           => __( 'Not found', 'another-events-calendar' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'another-events-calendar' ),
			);
			
			$args = array(
        		'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'author' ),
            	'has_archive'         => false,
				'public'              => true,        	
	        	'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=aec_events',
        		'publicly_queryable'  => true,
	       		'capability_type'     => 'post',
				'exclude_from_search' => true    	
    		); 
			
			register_post_type( 'aec_venues', $args );
			
		}
			
	}
	
	/**
	 * Register meta boxes.
	 *
	 * @since    1.0.0
	 */
	public function register_meta_boxes() {
	
    	add_meta_box( 'aec-venue-details', __( 'Venue Details', 'another-events-calendar' ), array( $this, 'display_meta_box' ), 'aec_venues', 'normal', 'high' );   
		 
	}
	
	/**
	 * Display the venue details meta box.
	 *
	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
	 */
	public function display_meta_box( $post ) {
	
		$general_settings = get_option( 'aec_general_settings' );
		$map_settings     = get_option( 'aec_map_settings' );
		
		$countries        = aec_get_countries();
		$default_location = $general_settings['default_location'];
		$zoom_level       = $map_settings['zoom_level'];		
		
	    $address   = get_post_meta( $post->ID, 'address', true );
		$city      = get_post_meta( $post->ID, 'city', true );
		$state     = get_post_meta( $post->ID, 'state', true );
		$country   = get_post_meta( $post->ID, 'country', true );		
		$pincode   = get_post_meta( $post->ID, 'pincode', true );
		$phone     = get_post_meta( $post->ID, 'phone', true );
		$website   = get_post_meta( $post->ID, 'website', true );
		$hide_map  = get_post_meta( $post->ID, 'hide_map', true );
		$latitude  = get_post_meta( $post->ID, 'latitude', true );
		$longitude = get_post_meta( $post->ID, 'longitude', true );
		
		if( $country == '' ) $country = $default_location;
	 	
	    wp_nonce_field( 'aec_save_venues', 'aec_venues_nonce' );
		
		include AEC_PLUGIN_DIR . 'admin/partials/venues/aec-admin-venues-display.php';
		
	}

	/**
	* Saves the meta box data.
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
		
	 	// Check if the post type = "aec_venues".
		if ( 'aec_venues' != $post->post_type ) {
			return $post_id;
		}

		// Check if the current user has permission to edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['aec_venues_nonce'] ) ) {
			return $post_id;    
		}
	 
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['aec_venues_nonce'], 'aec_save_venues' ) ) {
			return $post_id;
		}		
	 
		// OK to save meta data
		$address = sanitize_text_field( $_POST['address'] );
		update_post_meta( $post_id, 'address', $address );
		
		$city = sanitize_text_field( $_POST['city'] );
		update_post_meta( $post_id, 'city', $city );
		
		$state = sanitize_text_field( $_POST['state'] );
		update_post_meta( $post_id, 'state', $state );
		
		$country = sanitize_text_field( $_POST['country'] );
		update_post_meta( $post_id, 'country', $country );

		$pincode = sanitize_text_field( $_POST['pincode'] );
		update_post_meta( $post_id, 'pincode', $pincode );
		
		$phone = sanitize_text_field( $_POST['phone'] );
		update_post_meta( $post_id, 'phone', $phone );
		
		$website = esc_url_raw( $_POST['website'] );
		update_post_meta( $post_id, 'website', $website );
		
		$hide_map = isset( $_POST['hide_map'] ) ? 1 : 0;
		update_post_meta( $post_id, 'hide_map', $hide_map );
		
		$latitude = isset( $_POST['latitude'] ) ? sanitize_text_field( $_POST['latitude'] ) : '';
		update_post_meta( $post_id, 'latitude', $latitude );  
		
		$longitude = isset( $_POST['longitude'] ) ? sanitize_text_field( $_POST['longitude'] ) : '';
		update_post_meta( $post_id, 'longitude', $longitude ); 
		
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
			case 'country':
				$country = get_post_meta( $post_id, 'country', true );
				echo ! empty( $country ) ? aec_country_name( $country ) : '--';
				break;
			case 'hide_map':
				$hide_map = get_post_meta( $post_id, 'hide_map', true );
				echo ! empty( $hide_map ) ? __( 'Yes', 'another-events-calendar' ) : __( 'No', 'another-events-calendar' );
				break;
		}
		
	}
	
	/**
	 * Change the default "Enter title here" placeholder text.
	 *
	 * @since    1.0.0
	 *
	 * @param    string    $title    Default 'Enter title here'.
	 * @return   string              Updated placeholder text.
	 */
	public function change_title_text( $title ) {
	
		$screen = get_current_screen();
  
     	if ( 'aec_venues' == $screen->post_type ) {
		
          $title = __( 'Enter venue name', 'another-events-calendar' );
		  
     	}
  
     	return $title;
	
	}
	
	/**
	 * Add custom columns.
	 *
	 * @since    1.0.0
	 *
	 * @param    array    $columns    Array of column names.
	 * @return   array                Updated column names.
	 */
	public function manage_posts_columns( $columns ) {
    
		$n_columns = array(); 
		
	 	foreach( $columns as $key => $value ) {
	  		if( 'author' == $key ) {
				$n_columns['country']  = __( 'Country', 'another-events-calendar' );
	   			$n_columns['hide_map'] = __( 'Hide Map','another-events-calendar' );
	    	} 
			
			if( 'title' == $key ) {
				 $value = __( 'Name', 'another-events-calendar' );
			}
			
			$n_columns[ $key ] = $value; 
		} 
		
		return $n_columns;
			
	}
	
}
