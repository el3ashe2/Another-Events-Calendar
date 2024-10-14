<?php

/**
 * Markup the event details metabox used in the "aec_events" custom post type form.
 */ 
?>

<table class="aec-input widefat">
	<tbody>
    	<tr>
        	<td class="label">
				<label for="aec-all-day-event"><?php _e( 'All Day Event', 'another-events-calendar' )?></label>
          	</td>
            <td colspan="3">
               	<input type="checkbox" name="all_day_event" id="aec-all-day-event" value="1"<?php checked( $all_day_event, 1 ); ?>/>
           	</td>
     	</tr>
		<tr>
       		<td class="label">
        		<label><?php _e( 'Start Date & Time', 'another-events-calendar' ); ?></label>
      		</td>
       		<td>
    			<input type="text" name="start_date" id="aec-start-date" class="aec-date" value="<?php echo $start_date; ?>" />
           	</td>
            <td>
    			<select name="start_hour" class="aec-event-time-fields" style="display: none;">
    			<?php
     				for( $i = 0; $i <= 23; $i++ ) {
						printf( '<option value="%d"%s>%02d</option>', $i, selected( (int) $start_hour, $i ), $i );
					}					
				?>
  				</select>
           	</td>
            <td>
  				<select name="start_min" class="aec-event-time-fields" style="display: none;">
    			<?php
     				for( $i = 0; $i <= 59; $i++ ) {
						printf( '<option value="%d"%s>%02d</option>', $i, selected( (int) $start_min, $i ), $i );
					}					
				?>
				</select>
          	</td>
       	</tr>
		<tr>
        	<td class="label">
            	<label><?php _e( 'End Date & Time', 'another-events-calendar' ); ?></label>
           	</td>
            <td>
				<input type="text" name="end_date" id="aec-end-date" class="aec-date" value="<?php echo $end_date; ?>" />
			</td>
            <td>
				<select name="end_hour" class="aec-event-time-fields" style="display: none;">
    			<?php
     				for( $i = 0; $i <= 23; $i++ ) {
						printf( '<option value="%d"%s>%02d</option>', $i, selected( (int) $end_hour, $i ), $i );
					}				
				?>
  				</select>
			</td>
            <td>
				<select name="end_min" class="aec-event-time-fields" style="display: none;">
    			<?php
     				for( $i = 0; $i <= 59; $i++ ) {
						printf( '<option value="%d"%s>%02d</option>', $i, selected( (int) $end_min, $i ), $i );
					}					
				?>
				</select>
           </td>
		</tr>
	</tbody>
</table>