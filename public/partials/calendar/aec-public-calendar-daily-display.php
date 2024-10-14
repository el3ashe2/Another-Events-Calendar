<?php

/**
 * Markup the daily calendar page.
 */
?>

<div class="aec aec-daily-calendar">
	<!-- Header -->
	<table class="table table-borderless">
    	<tbody>
        	<tr>
            	<td width="20%">	
                	<div class="btn-group btn-group-sm">
                    	<?php 
							// Previous Day - nav
							$url = add_query_arg( 'date', date( 'Y-m-d', strtotime( '-1 day', strtotime( $date ) ) ) );
							printf( '<a href="%s" class="btn btn-default btn-sm"> <span class="glyphicon glyphicon-chevron-left"></span></a>', $url );
							
							// Next Day - nav
							$url = add_query_arg( 'date',  date( 'Y-m-d', strtotime( '+1 day', strtotime( $date ) ) ) );
							printf( '<a href="%s" class="btn btn-default btn-sm"> <span class="glyphicon glyphicon-chevron-right"></span></a>', $url );
						?>
                    </div>
             	</td>
                <td width="40%" class="text-center hidden-xs hidden-sm">
                	<?php echo date_i18n( get_option('date_format'), strtotime( $date ) ); ?>
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
        </tbody>
    </table>

	<!-- Loop -->
    <?php if( isset( $aec_query ) && $aec_query->have_posts() ) : ?>
    
		<?php while( $aec_query->have_posts() ) : $aec_query->the_post(); ?>
              
        	<div class="row aec-table-layout-row">
           		<div class="col-md-2">
                	<?php 
						$start_date_time = get_post_meta( get_the_ID(), 'start_date_time', true );
						$start_date = date( 'Y-m-d', strtotime( $start_date_time ) );
						$start_time = date_i18n( get_option('time_format'), strtotime( $start_date_time ) );
							
						if( $date == $start_date ) {
							$all_day = get_post_meta( get_the_ID(), 'all_day_event', true );
							if( $all_day ) {
								printf( '<p class="text-muted">%s</p>', __( 'All Day', 'another-events-calendar' ) );
							} else {
								printf( '<p><span class="glyphicon glyphicon-time"></span> %s</p>', $start_time );
							}
						} else {
							printf( '<p class="text-muted"><small>%s</small></p>', __( 'This is a multi-day event starts earlier to this date.', 'another-events-calendar' ) );
						}
					?>
           		</div>
                
        		<div class="col-md-2">
                	<?php if( has_post_thumbnail() ) : ?>
               			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                    <?php else : ?>
                    	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        	<img src="<?php echo AEC_PLUGIN_URL; ?>public/images/placeholder-event.jpg" class="img-responsive" />
                        </a>
                   	<?php endif; ?>
            	</div>
                
            	<div class="col-md-6">                	
					<h3 class="aec-no-margin"><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h3>
					<?php $venue_id = get_post_meta( get_the_ID(), 'venue_id', true ); ?>
                	<?php if( $venue_id > 0 ) : ?>
						<p class="aec-margin-top text-muted">
							<span class="glyphicon glyphicon-map-marker"></span>
							<a href="<?php echo aec_venue_page_link( $venue_id ); ?>"><?php echo get_the_title( $venue_id ); ?></a>
						</p>
					<?php endif; ?>
                	<?php echo wp_kses_post( wp_trim_words( get_the_content(), 20 ) ); ?>
        		</div>
                
            	<div class="col-md-2 text-right">
               		<?php 
						$cost = get_post_meta( get_the_ID(), 'cost', true );
						if( $cost > 0 ) printf( '<p>%s</p>', aec_currency_filter( aec_format_amount( $cost ) ) );
					?>
              		<p>
                    	<a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm"><?php _e( 'Read more', 'another-events-calendar' ); ?></a>
                    </p>
            	</div>
        	</div>
            
		<?php endwhile; ?>
    
    <?php else : ?>
    	<p class="text-muted text-center"><?php _e( "No events found.", "another-events-calendar" ); ?></p>
    <?php endif; ?>
  
</div>

<?php wp_reset_postdata(); ?>
<?php the_aec_socialshare_buttons(); ?>  