<?php

/**
 * Markup the event cost details metabox used in the "aec_events" custom post type form.
 */ 
?>

<table class="aec-input widefat">
	<tbody>
    	<tr>
    		<td class="label">
               	<label><?php _e( 'Cost', 'another-events-calendar' ); ?>&nbsp;[<?php echo aec_get_currency(); ?>]</label>
           	</td>
            <td>
                <input type="text" name="cost" value="<?php echo aec_format_amount( $cost ); ?>" />
                <p><?php _e( 'Enter 0 for events that are free or leave blank to hide the field.', 'another-events-calendar' ); ?></p>
            </td>
        </tr>
   	</tbody>
</table>