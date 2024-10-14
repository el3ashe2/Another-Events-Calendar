<?php

/**
 * Markup the organizer details metabox used in the "aec_organizers" custom post type form.
 */ 
?>

<table class="aec-input widefat">
	<tbody>
    	<tr>
        	<td class="label">
				<label for="aec-phone"><?php _e( 'Phone', 'another-events-calendar' ); ?></label>
          	</td>
            <td>
               	<input type="text" name="phone" id="aec-phone" value="<?php echo esc_attr( $phone ); ?>" />
          	</td>
      	</tr>
		<tr>
        	<td class="label">
         		<label for="aec-website"><?php _e( 'Website', 'another-events-calendar' ); ?></label>
          	</td>
            <td>
               	<input type="text" name="website" id="aec-website" value="<?php echo esc_attr( $website ); ?>" />
         	</td>
     	</tr>
		<tr>
        	<td class="label">
            	<label for="aec-email"><?php _e( 'Email', 'another-events-calendar' ); ?></label>
          	</td>
            <td>
               	<input type="text" name="email" id="aec-email" value="<?php echo esc_attr( $email ); ?>" />
         	</td>
    	</tr>
 	</tbody>
</table>
    
    
   
    