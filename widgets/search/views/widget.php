<?php

/**
 * Markup the widget front-end.
 */ 
?>

<div class="aec aec-widget-search" id="<?php echo $widget_id; ?>">

	<form method="get" action="<?php echo aec_search_page_link(); ?>"<?php if( 'horizontal' == $display ) echo ' class="form-inline"'; ?>>
    	<?php if( ! get_option('permalink_structure') ) : ?>
       		<input type="hidden" name="page_id" value="<?php echo $search_id; ?>" />
    	<?php endif; ?>
    
		<div class="form-group">
        	<input type="text" name="aec" class="form-control" placeholder="<?php _e( 'Search by event title, desc...', 'another-events-calendar' ); ?>" value="<?php echo isset( $_GET['aec'] ) ? sanitize_text_field( $_GET['aec'] ) : ''; ?>" required /></p>
        </div>
        
        <?php if( $search_by_category ) : ?>
			<div class="form-group">
            	<select name="cat" class="form-control">
                	<option value="">-- <?php _e( 'Select Category', 'another-events-calendar' ); ?> --</option>
                	<?php
                   		foreach( $categories as $category ) {	
							$active = !empty( $_GET['cat'] ) ? $_GET['cat'] : '';
                        	printf('<option value="%s"%s>%s</option>', $category->term_id, selected( $category->term_id, $active ) , $category->name );
                    	}
               		?>
            	</select>
        	</div>
    	<?php endif; ?>
        
   	 	<?php if( $search_by_venue ) : ?>
			<div class="form-group">
            	<select name="venue" class="form-control">
                	<option value="">-- <?php _e( 'Select Venue', 'another-events-calendar' ); ?> --</option>
                	<?php
                   		foreach( $venues as $venue ) {	
							$active = !empty( $_GET['venue'] ) ? $_GET['venue'] : '';
                        	printf('<option value="%s"%s>%s</option>', $venue->ID, selected( $venue->ID, $active ) , $venue->post_title );
							
                    	}
               		?>
            	</select>
            </div>
    	<?php endif; ?>
    
   		<?php if( $search_by_single_date ) : ?>
        	<div class="form-group">
        		<input name="date" type="text" class="aec-widget-date-picker form-control" value="<?php echo isset( $_GET['date'] ) ? sanitize_text_field( $_GET['date'] ) : '';?>" placeholder="<?php _e( 'Search by date', 'another-events-calendar' ); ?>" />
            </div>
    	<?php endif; ?>
    
    	<?php if( $search_by_date_range ) : ?>
        	<div class="form-group row">
            	<div class="col-md-6">
        			<input type="text" name="from" class="aec-widget-date-picker form-control" value="<?php echo isset( $_GET['from'] ) ? sanitize_text_field( $_GET['from'] ) : '';?>" placeholder="<?php _e( 'From date', 'another-events-calendar' ); ?>" />
                </div>
        		<div class="col-md-6">
           	 		<input type="text" name="to" class="aec-widget-date-picker form-control" value="<?php echo isset( $_GET['to'] ) ? sanitize_text_field( $_GET['to'] ) : '';?>" placeholder="<?php _e( 'To date', 'another-events-calendar' ); ?>" />
                </div>
            </div>
    	<?php endif; ?>      
    	
        <input type="submit" class="btn btn-primary" data-id=<?php echo $widget_id ?> value="<?php _e( 'Search', 'another-events-calendar' ) ?>" />
        <?php if( isset( $_GET['aec'] ) ) : ?>
        	<input type="button" class="btn btn-default" onclick="location.href='<?php echo aec_search_page_link(); ?>'" value="<?php _e( 'Reset', 'another-events-calendar' ); ?>" />
        <?php endif; ?>     
	</form>
 
</div>
