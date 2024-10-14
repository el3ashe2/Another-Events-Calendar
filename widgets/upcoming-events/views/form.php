<?php

/**
 * Markup the widget admin options.
 */ 
?>

<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'another-events-calendar' ); ?>:</label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
</p> 

<p>
	<input class="checkbox" type="checkbox" <?php checked( $show_image, 1 ); ?> id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="1" /> 
    <label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Show image', 'another-events-calendar' ); ?></label>
</p>

<p>
    <input class="checkbox" type="checkbox" <?php checked( $show_date, 1 ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" value="1" /> 
    <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Show date', 'another-events-calendar' ); ?></label>
</p>

<p>
  	<input class="checkbox" type="checkbox" <?php checked( $show_venue, 1 ); ?> id="<?php echo $this->get_field_id( 'show_venue' ); ?>" name="<?php echo $this->get_field_name( 'show_venue' ); ?>" value="1" /> 
   	<label for="<?php echo $this->get_field_id( 'show_venue' ); ?>"><?php _e( 'Show venue', 'another-events-calendar' ); ?></label>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit', 'another-events-calendar' ); ?>:</label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>">
</p> 

