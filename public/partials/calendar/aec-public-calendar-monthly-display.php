<?php

/**
 * Markup the monthly calendar page.
 */
?>

<div class="aec aec-monthly-calendar">
	<!-- Header -->
	<form action="<?php echo get_permalink(); ?>" method="get">	
    	<?php if( ! get_option('permalink_structure') ) : ?>
       		<input type="hidden" name="page_id" value="<?php echo $calendar_id; ?>"/>
    	<?php endif; ?>
        
		<table class="table table-borderless">
      		<tr>
            	<td width="20%">
                	<div class="btn-group btn-group-sm">
                    	<?php 
							// Previous Month - nav
							$url = add_query_arg(
								array(
									'mo' => ( $month != 1 ? $month - 1 : 12 ) ,
									'yr' => ( $month != 1 ? $year : $year - 1 )
								)
							);
						
							printf( '<a href="%s" class="btn btn-default btn-sm"> <span class="glyphicon glyphicon-chevron-left"></span></a>', $url );
							
							// Next Month - nav
							$url = add_query_arg(
								array(
									'mo' => ( $month != 12 ? $month + 1 : 1 ) ,
									'yr' => ( $month != 12 ? $year : $year + 1 )
								)
							);
																  
							printf( '<a href="%s" class="btn btn-default btn-sm"> <span class="glyphicon glyphicon-chevron-right"></span></a>', $url );
						?>
                    </div>
                </td>
        		<td width="40%" class="text-center hidden-xs hidden-sm">              		
					<?php
						// Select month control
						$months = array(
							__( 'January', 'another-events-calendar' ),
							__( 'February', 'another-events-calendar' ),
							__( 'March', 'another-events-calendar' ),
							__( 'April', 'another-events-calendar' ),
							__( 'May', 'another-events-calendar' ),
							__( 'June', 'another-events-calendar' ),
							__( 'July', 'another-events-calendar' ),
							__( 'August', 'another-events-calendar' ),
							__( 'September', 'another-events-calendar' ),
							__( 'October', 'another-events-calendar' ),
							__( 'November', 'another-events-calendar' ),
							__( 'December', 'another-events-calendar' )
						);
							
						echo '<select name="mo">';
						foreach( $months as $key => $text ) {
							$i = ( $key + 1 );
							printf( '<option value="%d"%s>%s</option>', $i, selected( $month, $i ), $text );
						}
						echo '</select>&nbsp;';
						  
						// Select year control
						$year_range = 20;
						$from_year  = $year - floor( $year_range / 2 );
						$to_year    = $year + floor( $year_range / 2 );
						
						echo '<select name="yr">';
						for( $i = $from_year; $i <= $to_year; $i++ ) {
							printf( '<option value="%d"%s>%s</option>', $i, selected( $year, $i ), $i );
						}
						echo '</select>';
        			?>
                    <input type="submit" value="<?php _e( 'Go', 'another-events-calendar' ); ?>" /> 
          		</td>
          		<td width="40%" class="text-right">
              		<div class="btn-group btn-group-sm">
						<?php if( in_array( 'month', $calendar_settings['view_options'] ) ) : ?>
                        	<a class="btn btn-default" href="<?php echo add_query_arg( 'view', 'month', get_permalink() ); ?>" title="<?php _e( 'Month', 'another-events-calendar' ); ?>">
                            	<span class="glyphicon glyphicon-calendar"></span> 
                                <span class="hidden-xs hidden-sm"><?php _e( 'Month', 'another-events-calendar' ); ?></span>
                        	</a>
                    	<?php endif; ?>
                    
						<?php if( in_array( 'week', $calendar_settings['view_options'] ) ) : ?>
                        	<a class="btn btn-default" href="<?php echo add_query_arg( 'view', 'week', get_permalink() ); ?>" title="<?php _e( 'Week', 'another-events-calendar' ); ?>">
                            	<span class="glyphicon glyphicon-th-list"></span>
                                <span class="hidden-xs hidden-sm"><?php _e( 'Week', 'another-events-calendar' ); ?></span>
                        	</a>
                    	<?php endif; ?>
                    
                    	<?php if( in_array( 'day', $calendar_settings['view_options'] ) ) : ?>
                        	<a class="btn btn-default" href="<?php echo add_query_arg( 'view', 'day', get_permalink() ); ?>" title="<?php _e( 'Day', 'another-events-calendar' ); ?>">
                          	<span class="glyphicon glyphicon-bookmark"></span> 
                            <span class="hidden-xs hidden-sm"><?php _e( 'Day', 'another-events-calendar' ); ?></span>
                        	</a>
                    	<?php endif; ?>
               		</div>
            	</td>
        	</tr>
		</table>
	</form>	

	<!-- Calendar -->
	<table class="table table-striped table-bordered aec-calendar">
        <?php
			// Table headings
			$headings = array(
				__( 'MON', 'another-events-calendar' ), 
				__( 'TUE', 'another-events-calendar' ),
				__( 'WED', 'another-events-calendar' ),
				__( 'THU', 'another-events-calendar' ),
				__( 'FRI', 'another-events-calendar' ),
				__( 'SAT', 'another-events-calendar' )
			);
					  
			// Vars
			$running_day = date( 'w', mktime( 0, 0, 0, $month, 1, $year ) );
		
			if( get_option( 'start_of_week' ) > 0 )  {
				$running_day = $running_day - 1;
				array_push($headings, __( 'SUN', 'another-events-calendar' ) );
			} else {
				array_unshift($headings, __( 'SUN', 'another-events-calendar' ) );			
			}
		
			if( $running_day == -1 ) {
				$running_day = 6;
			}
		
			echo '<tr class="aec-header"><th class="center">'.implode( '</th><th class="center">', $headings ).'</th></tr>';
		
			$days_in_month = date( 't', mktime( 0, 0, 0, $month, 1, $year ));
			$days_in_this_week = 1;
			$day_counter = 0;
		
			echo '<tbody>';
	
			// Row for week one
			echo'<tr class="aec-calendar-row">';
	
			// Print "blank" days until the first of the current week
			for( $i = 0; $i < $running_day; $i++ ) {
				echo '<td class="aec-empty-cell"> </td>';
				$days_in_this_week++;
			}
	
			// Keep going with days....
			for( $list_day = 1; $list_day <= $days_in_month; $list_day++ ) {
				echo '<td>';
				
				// Add in the day number
				echo '<div class="aec-section-1">';	
				echo '<p class="text-muted text-right aec-date">'.$list_day.'</p>';
				echo '<p class="text-muted text-center aec-day">'.$headings[$running_day].'</p>';
				echo '<h2 class="text-center aec-date">'.$list_day.'</h2>';
				echo '</div>';
				
				// QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  if MATCHES FOUND, PRINT THEM !!
				echo '<div class="aec-section-2">';	
				$date = sprintf( '%d-%02d-%02d', $year, $month, $list_day );
				if( ! empty( $events[ $date ] ) ) {
					$meta = array();
					
					foreach( $events[ $date ] as $event  ) {
						$meta[] = '<a href="'.get_permalink( $event->ID ).'">'.$event->post_title.'</a>';
					}
					
					echo implode( '<hr />', $meta );
				} else {
					echo str_repeat( '<p>&nbsp;</p>', 2 );
				}
				echo '</div>';
				
				echo '</td>';
				
				if( $running_day == 6 ) {
					echo '</tr>';
					if( ( $day_counter + 1 ) != $days_in_month ) {
						echo '<tr class="aec-calendar-row">';
					}
					$running_day = -1;
					$days_in_this_week = 0;
				}
				
				$days_in_this_week++; 
				$running_day++; 
				$day_counter++;
			}
	
			// Finish the rest of the days in the week
			if( $days_in_this_week > 1 && $days_in_this_week < 8 ) {
				for( $i = 1; $i <= ( 8 - $days_in_this_week ); $i++ ) {
					echo '<td class="aec-empty-cell"> </td>';
				}
			}
			
			// Final row
			echo '</tr>';
	 	?>
	</table>		
</div>	

<?php the_aec_socialshare_buttons(); ?>  