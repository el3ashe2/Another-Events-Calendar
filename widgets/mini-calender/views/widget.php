<?php

/**
 * Markup the widget front-end.
 */ 
?>

<div class="aec aec-mini-calendar" id="<?php echo $widget_id; ?>">
<?php
   	$calendar = '';

	$date = mktime( 0, 0, 0, $month, 1, $year );	
	
    $prev_month = $month - 1;
	$prev_year = $year;
	if( $month == 1 ) {
    	$prev_month = 12;
        $prev_year = $year - 1;
    }		
  
    $next_month = $month + 1;
    $next_year = $year;
    if( $month == 12 ) {
    	$next_month = 1;
        $next_year = $year + 1;
    }
	?>	
    
    <table class="table table-striped table-bordered">
    	<tr>
       		<td class="text-center">
           		<a class="aec-mini-calendar-nav btn btn-default btn-sm" data-month="<?php echo $prev_month; ?>" data-year="<?php echo $prev_year; ?>" data-id="<?php echo $widget_id; ?>"><span class="glyphicon glyphicon-arrow-left"></span>
               	</a>
			</td>
							
			<td class="aec-spinner-container text-center" colspan="5">
            	<strong><?php echo date("F Y", $date); ?></strong>
            </td>
			
       		<td class="text-center">
               	<a class="aec-mini-calendar-nav btn btn-default btn-sm" data-month="<?php echo $next_month; ?>" data-year="<?php echo $next_year; ?>" data-id="<?php echo $widget_id; ?>"><span class="glyphicon glyphicon-arrow-right"></span></a>
			</td>
		</tr>

		<?php
    		$headings = array(
				__( 'MO', 'another-events-calendar' ),
				__( 'TU', 'another-events-calendar' ),
				__( 'WE', 'another-events-calendar' ),
				__( 'TH', 'another-events-calendar' ),
				__( 'FR', 'another-events-calendar' ),
				__( 'SA', 'another-events-calendar' )
			);					 
					  
			// days and weeks vars now ...
			$running_day = date( 'w', $date );
				
			if( get_option( 'start_of_week' ) > 0 )  {
				$running_day = $running_day - 1;
				array_push( $headings, __( 'SU', 'another-events-calendar' ) );
			} else {
				array_unshift( $headings, __( 'SU','another-events-calendar' ) );			
			}
		
			if( $running_day == -1 ) {
				$running_day = 6;
			}
	
			$days_in_this_week = 1;
			$day_counter = 0;
		?>
		<tr class="aec-mini-calendar-header">
        	<th class="text-center"><?php echo implode( '</th><th class="text-center">',$headings ); ?></th>
       	</tr>
        
        <tr>
		<?php
        	for( $i = 1; $i <= $running_day; $i++ ) {
        		 echo '<td></td>';
				 $days_in_this_week++;
			}
       
	   		for( $day = 1; $day <= $days_in_month; $day++ ) {
				
				$date = sprintf( '%d-%02d-%02d', $year, $month, $day );
				if( ! empty( $events[ $date ] ) ) {
					echo '<td class="text-center bg-primary">';
					echo '<div>';
					if( count( $events[ $date ] ) > 1 ) {
						$target_url = add_query_arg(
							array(
								'view' => 'day',
								'date' => sprintf( '%d-%02d-%02d', $year, $month, $day )
							),
							get_permalink( $calendar_page_id )
						);
						printf( '<a href="%s">%d</a>', $target_url, $day );
					} else {
						$target_url = get_permalink( $events[ $date ][0]->ID );
						printf( '<a href="%s">%d</a>', $target_url, $day );
					}
					echo '</div>';
					echo '</td>';
				} else {
					echo '<td class="text-center">';
					echo '<div>';
					echo $day;
					echo '</div>';
					echo '</td>';
				}
				
				
				if( $running_day == 6 ) {
					echo '</tr>';
					if( ( $day_counter + 1 ) != $days_in_month ) {
						echo '<tr>';
					}
					$running_day = -1;
					$days_in_this_week = 0;
				}
				
				$days_in_this_week++; 
				$running_day++; 
				$day_counter++;
			}
        
			if( $days_in_this_week > 1 && $days_in_this_week < 8 ) {
				for( $i = 1; $i <= ( 8 - $days_in_this_week ); $i++ ) {
					echo '<td></td>';
				}
			}        
		?>
        </tr>
	</table>
</div>
    