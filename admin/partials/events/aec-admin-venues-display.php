<?php

/**
 * Markup the venue details metabox used in the "aec_events" custom post type form.
 */ 
?>

<table class="aec-input widefat">
	<tbody>
    	<tr>
        	<td class="label">
				<label for="aec-venues"><?php _e( 'Select Venue', 'another-events-calendar' ); ?></label>
           	</td>
            <td>
               	<select name="venue_id" id="aec-venues">
   					<option value="-1"><?php _e( 'Create New Venue', 'another-events-calendar' ); ?></option>
					<?php
                    	foreach( $venues as $venue ) {	
    						printf('<option value="%s"%s>%s</option>', $venue->ID, selected( $venue->ID, $venue_id ) , $venue->post_title );
						}
					?>
				</select>
          	</td>
       	</tr>
	</tbody>
</table>

<div id="aec-new-venue-fields">
	<table class="aec-input widefat">
		<tbody>
        	<tr>
                <td class="label">
                    <label for="aec-venue-name"><?php _e( 'Venue Name', 'another-events-calendar' ); ?></label>
                </td>
                <td>
	                <input type="text" name="venue_name" id="aec-venue-name" class="aec-map-field" />
                </td>
            </tr>
          	<tr>
                <td class="label">
                    <label for="aec-address"><?php _e( 'Address', 'another-events-calendar' ); ?></label>
                </td>
                <td>
	                <input type="text" name="address" id="aec-address" class="aec-map-field" />
                </td>
            </tr>
            <tr>
                <td class="label">     
                    <label for="aec-city"><?php _e( 'City', 'another-events-calendar' ); ?></label>
                </td>
                <td>
	                <input type="text" name="city" id="aec-city" class="aec-map-field" />
                </td>
            </tr>
            <tr>
                <td class="label">
                   <label for="aec-state"><?php _e( 'State', 'another-events-calendar' ); ?></label>
                </td>
                <td>
	                <input type="text" name="state" id="aec-state" class="aec-map-field" />
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label for="aec-country"><?php _e( 'Select Country', 'another-events-calendar' ); ?></label>
                </td>
                <td>
	                <select name="country" id="aec-country" class="aec-map-field">
    	                <option value="">-- <?php _e( 'Select Country', 'another-events-calendar' ); ?> --</option>
        	            <?php
                        	foreach( $countries as $key => $label ) {
                            	printf( '<option value="%s"%s>%s</option>', $key, selected( $default_location, $key, false ), $label );
                            }
                       	?>
                   	</select>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label for="aec-pincode"><?php _e( 'Postal Code', 'another-events-calendar' ); ?></label>
                </td>
                <td>
	                <input type="text" name="pincode" id="aec-pincode" class="aec-map-field" />
                </td>
            </tr>
            <tr>
                <td class="label">
                   <label for="aec-phone"><?php _e( 'Phone', 'another-events-calendar' ); ?></label>
                </td>
                <td>
	                <input type="text" name="phone" />
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label for="aec-website"><?php _e( 'Website', 'another-events-calendar' ); ?></label>
                </td>
                <td>
 		        	<input type="text" name="website" />
                </td>
            </tr>
            <?php if( ! empty( $map_settings['enabled'] ) ) : ?>
       			<tr>
        			<td class="label">
            			<label for="aec-hide-map"><?php _e( 'Hide Google Map', 'another-events-calendar' ); ?></label>
            		</td>
            		<td>
               			<input type="checkbox" name="hide_map" id="aec-hide-map" value="1"/>
         			</td>
      			</tr>
      		<?php endif; ?>
   		</tbody>
	</table>
    
    <?php if( ! empty( $map_settings['enabled'] ) ) : ?>
		<div class="aec-map" data-default_location="<?php echo $default_location; ?>">
	   		<div class="marker"></div>
		</div>
		<input type="hidden" name="latitude" id="aec-latitude" value="" />
		<input type="hidden" name="longitude" id="aec-longitude" value="" />
	<?php endif; ?>
</div>
