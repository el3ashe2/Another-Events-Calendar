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
	<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Display', 'another-events-calendar' ); ?>:</label> 
	<select class="widefat" id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
    	<option value="vertical"<?php selected( $display, 'vertical' ); ?>><?php _e( 'Vertical', 'another-events-calendar' ); ?></option>
        <option value="horizontal"<?php selected( $display, 'horizontal' ); ?>><?php _e( 'Horizontal', 'another-events-calendar' ); ?></option>
    </select>
</p>

<p>
    <input class="checkbox" type="checkbox" <?php checked( $search_by_category, 1 ); ?> id="<?php echo $this->get_field_id( 'search_by_category' ); ?>" name="<?php echo $this->get_field_name( 'search_by_category' ); ?>" value="1" /> 
    <label for="<?php echo $this->get_field_id( 'search_by_category' ); ?>"><?php _e( 'Search by Categories', 'another-events-calendar' ); ?></label>
</p>

<p>
    <input class="checkbox" type="checkbox" <?php checked( $search_by_venue, 1 ); ?> id="<?php echo $this->get_field_id( 'search_by_venue' ); ?>" name="<?php echo $this->get_field_name( 'search_by_venue' ); ?>" value="1" /> 
    <label for="<?php echo $this->get_field_id( 'search_by_venue' ); ?>"><?php _e( 'Search by Venues', 'another-events-calendar' ); ?></label>
</p>

<p>
  	<input class="checkbox" type="checkbox" <?php checked( $search_by_single_date, 1 ); ?> id="<?php echo $this->get_field_id( 'search_by_single_date' ); ?>" name="<?php echo $this->get_field_name( 'search_by_single_date' ); ?>" value="1" /> 
   	<label for="<?php echo $this->get_field_id( 'search_by_single_date' ); ?>"><?php _e( 'Search by Single Date', 'another-events-calendar' ); ?></label>
</p>

<p>
   	<input class="checkbox" type="checkbox" <?php checked( $search_by_date_range, 1 ); ?> id="<?php echo $this->get_field_id( 'search_by_date_range' ); ?>" name="<?php echo $this->get_field_name( 'search_by_date_range' ); ?>" value="1" /> 
   	<label for="<?php echo $this->get_field_id( 'search_by_date_range' ); ?>"><?php _e( 'Search by Date Range', 'another-events-calendar' ); ?></label>
</p>