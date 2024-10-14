<?php

/**
 * Markup the venue details metabox used in the "aec_venues" custom post type form.
 */
?>

<table class="aec-input widefat">
	<tbody>
    	<tr>
        	<td class="label">
        	    <label for="aec-address"><?php _e( 'Address', 'another-events-calendar' ); ?></label>
            </td>
           	<td>
           		<input type="text" name="address" id="aec-address" class="aec-map-field" value="<?php echo esc_attr( $address ); ?>" />
           	</td>	
		</tr>
        <tr>
        	<td class="label">
            	<label for="aec-city"><?php _e( 'City', 'another-events-calendar' ); ?></label>
            </td>
            <td>
           		<input type="text" name="city" id="aec-city" class="aec-map-field" value="<?php echo esc_attr( $city ); ?>" />
            </td>
        </tr>
        <tr>
        	<td class="label">
            	<label for="aec-state"><?php _e( 'State', 'another-events-calendar' ); ?></label>
            </td>
            <td>
               	<input type="text" name="state" id="aec-state" class="aec-map-field" value="<?php echo esc_attr( $state ); ?>" />
            </td>
        </tr>
		<tr>
        	<td class="label">       
       		     <label for="aec-country"><?php _e( 'Select Country', 'another-events-calendar'); ?></label>
			</td>
            <td>
               	<select name="country" id="aec-country" class="aec-map-field">
               		<option value="">-- <?php _e( 'Select Country', 'another-events-calendar' ); ?> --</option>
               		<?php
                   		foreach( $countries as $key => $label ) {
                       		printf( '<option value="%s"%s>%s</option>', $key, selected( $country, $key, false ), $label );
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
               	<input type="text" name="pincode" id="aec-pincode" class="aec-map-field" value="<?php echo esc_attr( $pincode ); ?>" />
            </td>
       	</tr>
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
        <?php if( ! empty( $map_settings['enabled'] ) ) : ?>
       		<tr>
        		<td class="label">
            		<label for="aec-hide-map"><?php _e( 'Hide Google Map', 'another-events-calendar' ); ?></label>
            	</td>
            	<td>
               		<input type="checkbox" name="hide_map" id="aec-hide-map" value="1" <?php checked( 1, $hide_map ); ?>/>
         		</td>
      		</tr>
      	<?php endif; ?>
	</tbody>
</table>

<?php if( ! empty( $map_settings['enabled'] ) ) : ?>
 
	<div class="aec-map" data-default_location="<?php echo $default_location; ?>">
	   	<div class="marker"></div>
	</div>
            
	<input type="hidden" name="latitude" id="aec-latitude" value="<?php echo $latitude; ?>" />
	<input type="hidden" name="longitude" id="aec-longitude" value="<?php echo $longitude; ?>" />
    
<?php endif; ?>

	