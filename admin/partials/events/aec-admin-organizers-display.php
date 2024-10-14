<?php
	
/**
 * Markup the organizer details metabox used in the "aec_events" custom post type form.
 */
 
?>

<table class="aec-input widefat">
	<tbody>
    	<tr>
    		<td class="label">
				<label for="organizers"><?php _e( 'Select Organizers', 'another-events-calendar' )?></label>
           	</td>
        	<td>
            	<div class="aec-multi-checkbox">
                <?php
                    foreach( $organizers_list as $organizer ) {
                        $checked = in_array( $organizer->ID, $organizers ) ? ' checked' : ''; 
                        printf( '<label><input type="checkbox" name="organizers[]" value="%s"%s/>%s</label><br>', $organizer->ID, $checked, $organizer->post_title );
                    }	
                 ?>
               	</div>
           	</td>
		</tr>
  	</tbody>
</table>

<div id="aec-organizer-fields-container"></div>

<div class="aec-add-new-organizer-block">
	<a id="aec-add-new-organizer" class="button-secondary"><?php _e( 'Add another Organizer', 'another-events-calendar' ); ?></a>
</div>
        
<div id="aec-organizer-fields" style="display: none;">
   	<div class="aec-organizer-fields">
		<p>#1</p>
		<table class="aec-input widefat">
    		<tbody>
        		<tr>
            		<td class="label">
                		<label><?php _e( 'Organizer Name', 'another-events-calendar' ); ?></label>
                	</td>
                	<td>
                    	<input type="text" name="organizer_name[]" />
                	</td>
            	</tr>
            	<tr>
            		<td class="label">
                		<label><?php _e( 'Phone', 'another-events-calendar' ); ?></label>
                	</td>
                	<td>
                    	<input type="text" name="organizer_phone[]" />
                	</td>
            	</tr>
            	<tr>
                	<td class="label">
            	    	<label><?php _e( 'Email', 'another-events-calendar' ); ?></label>
                	</td>
                	<td>
                		<input type="text" name="organizer_email[]" />
                	</td>
            	</tr>
            	<tr>
            		<td class="label">
                   		<label><?php _e( 'Website', 'another-events-calendar' ); ?></label>
                	</td>
                	<td>
                   		<input type="text" name="organizer_website[]" />
                	</td>
            	</tr>
        	</tbody>
    	</table>
	</div>
</div>