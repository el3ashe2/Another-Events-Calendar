<?php

/**
 * Organizers.
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
 * AEC_Admin_Organizers Class
 *
 * @since    1.0.0
 */
class AEC_Admin_Organizers {
	
	/**
 	 * Register the custom post type "aec_organizers".
 	 *
 	 * @since    1.0.0
 	 */
	public function register_post_type() {
		
		$general_settings = get_option( 'aec_general_settings' );
		
		if( ! empty( $general_settings['has_organizers'] ) ) {
		
			$labels = array(
				'name'                => _x( 'Organizers', 'Post Type General Name', 'another-events-calendar' ),
				'singular_name'       => _x( 'Organizer', 'Post Type Singular Name', 'another-events-calendar' ),
				'menu_name'           => __( 'Organizers', 'another-events-calendar' ),
				'name_admin_bar'      => __( 'Organizer', 'another-events-calendar' ),
				'all_items'           => __( 'Organizers', 'another-events-calendar' ),
				'add_new_item'        => __( 'Add New Organizer', 'another-events-calendar' ),
				'add_new'             => __( 'Add New', 'another-events-calendar' ),
				'new_item'            => __( 'New Organizer', 'another-events-calendar' ),
				'edit_item'           => __( 'Edit Organizer', 'another-events-calendar' ),
				'update_item'         => __( 'Update Organizer', 'another-events-calendar' ),
				'view_item'           => __( 'View Organizer', 'another-events-calendar' ),
				'search_items'        => __( 'Search Organizer', 'another-events-calendar' ),
				'not_found'           => __( 'Not found', 'another-events-calendar' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'another-events-calendar' ),
			);
			
			$args = array(
        		'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
            	'has_archive'         => false,
				'public'              => true,        	
	        	'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=aec_events',
        		'publicly_queryable'  => true,
	       		'capability_type'     => 'post',
				'exclude_from_search' => true    	
    		); 
			
			register_post_type( 'aec_organizers', $args );
			
		}
		
 	}
	
	/**
 	 * Register meta boxes.
 	 *
 	 * @since    1.0.0
 	 */
	public function register_meta_boxes() {
	
    	add_meta_box( 'aec-organizer-details', __( 'Organizer Details', 'another-events-calendar' ), array( $this, 'display_meta_box' ), 'aec_organizers', 'normal', 'high' ); 
		   
	}
	
	/**
 	 * Display the organizer details meta box.
 	 *
 	 * @since    1.0.0
	 *
	 * @param    object    $post    Post Object.
 	 */
    public function display_meta_box( $post ) {
	
		$phone   = get_post_meta( $post->ID, 'phone', true );
		$website = get_post_meta( $post->ID, 'website', true );
		$email   = get_post_meta( $post->ID, 'email', true );
		 	
	    wp_nonce_field( 'aec_save_organizer', 'aec_organizer_nonce' );
		
		include AEC_PLUGIN_DIR.'admin/partials/organizers/aec-admin-organizers-display.php';
	   
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
		
		// Check if the post type = "aec_organizers".
		if ( 'aec_organizers' != $post->post_type ) {
			return $post_id;
		}
		
		// Check if the current user has permission to edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		
		// Check if our nonce is set.
		if ( ! isset( $_POST['aec_organizer_nonce'] ) ) {
			return $post_id;    
		}
	 
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['aec_organizer_nonce'], 'aec_save_organizer' ) ) {
			return $post_id;
		}
	 
		// OK to save meta data
		$phone = sanitize_text_field( $_POST['phone'] );
		update_post_meta( $post_id, 'phone', $phone );
		
		$website = esc_url_raw( $_POST['website'] );
		update_post_meta( $post_id, 'website', $website );
		
		$email = sanitize_email( $_POST['email'] );
		update_post_meta( $post_id, 'email', $email );
		
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
			case 'website':
				$website = get_post_meta( $post_id, 'website', true );
				echo ! empty( $website ) ? $website : '--';
				break;
			case 'email':
				$email = get_post_meta( $post_id, 'email', true );
				echo ! empty( $email ) ? $email : '--';
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
  
     	if ( 'aec_organizers' == $screen->post_type ) {
		
          $title = __( 'Enter organizer name', 'another-events-calendar' );
		  
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
	   			$n_columns['website'] = __( 'Website','another-events-calendar' );
				$n_columns['email']   = __( 'Email', 'another-events-calendar' );
	    	} 
			
			if( 'title' == $key ) {
				 $value = __( 'Name', 'another-events-calendar' );
			}
			
			$n_columns[ $key ] = $value; 
		} 
		
		return $n_columns;
			
	}

}