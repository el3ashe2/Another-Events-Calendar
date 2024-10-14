<?php

/**
 * Markup the single event page content.
 */ 
?>

<div class="aec aec-single-event">
	 <div class="row">
     	<!-- Left column -->
     	<div class="col-md-9">
        	<?php if( ! empty( $post_thumbnail_src ) ) : ?>
        		<img src="<?php echo $post_thumbnail_src; ?>" class="img-responsive" alt="<?php the_title_attribute(); ?>" />
            <?php endif; ?>
            <p><?php echo $description; ?></p>
        </div>
                
        <!-- Right column -->
        <div class="col-md-3">
        	<!-- Details meta box -->
        	<div class="panel panel-default">
            	<div class="panel-heading">
                	<?php _e( 'Details', 'another-events-calendar' ); ?>
               	</div>
                <div class="panel-body">
                	<p class="aec-no-margin"><strong><?php _e( 'Starts On', 'another-events-calendar' ); ?></strong></p>
                	<p><?php echo $start_date_time; ?></p>
                    
                    <?php if( ! empty( $end_date_time ) ) : ?>
                    	<p class="aec-no-margin"><strong><?php _e( 'Ends On', 'another-events-calendar' ); ?></strong></p>
                		<p><?php echo $end_date_time; ?></p>
                    <?php endif; ?>
                    
                    <?php if( $cost > 0 ) : ?>
						<p class="aec-no-margin"><strong><?php _e( 'Cost', 'another-events-calendar' ); ?></strong></p>
                    	<p><?php echo aec_currency_filter( aec_format_amount( $cost ) ); ?></p> 
					<?php endif; ?>
                
                    <?php if( $categories ) : ?>
                    	<p class="aec-no-margin"><strong><?php _e( 'Event Categories', 'another-events-calendar' ); ?></strong>
                        <?php
							$meta = array();
                        	foreach( $categories as $category ) {
                            	$meta[] = sprintf( '<a class="text-primary" href="%s">%s</a>', aec_category_page_link( $category ), $category->name );
							}
							echo '<p>'.implode( ', ', $meta ).'</p>';
                       	?>
                        </p>
                    <?php endif; ?>
                    
                  	<?php if( $tags ) :?>
                        <p class="aec-no-margin"><strong><?php _e( 'Event Tags', 'another-events-calendar' ); ?></strong>
                      	<?php
							$meta = array(); 
                          	foreach( $tags as $tag ){ 
                            	$meta[] = sprintf( '<a class="text-primary" href="%s">%s</a>', aec_tag_page_link( $tag ), $tag->name );
                          	}
							echo '<p>'.implode( ', ', $meta ).'</p>';
                       	?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Organizer meta box -->
          	<?php if( ! empty( $organizers ) ) : ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _e( 'Organizers', 'another-events-calendar' ); ?>
                    </div>
                    <div class="panel-body">
                    	<?php foreach( $organizers as $organizer ) : ?>
                        		<p class="aec-margin-bottom">
                                	<a class="text-primary" href="<?php echo aec_organizer_page_link( $organizer['id'] ); ?>">
                                		<strong><?php echo $organizer['name']; ?></strong>
                                	</a>
                            	</p>
                                
                                <?php
									$meta = array();
									if( $organizer['phone'] ) $meta[] =  sprintf( __( '<abbr title="Phone">P:</abbr>%s' ), $organizer['phone'] );
									if( $organizer['email'] ) $meta[] =  sprintf( '<a href="mailto:%s">%s</a>', $organizer['email'], $organizer['email'] );
									if( $organizer['website'] ) $meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', $organizer['website'], $organizer['website'] );
								
									if( count( $meta ) ) echo '<p>'.implode( '<br>', $meta ).'</p>';
								?>                            	
                    	<?php endforeach; ?> 
                    </div>
                </div>
          	<?php endif; ?>  
            
            <!-- Venue meta box -->
            <?php if( $venue_id > 0) : ?>
            	<div class="panel panel-default">
            		<div class="panel-heading">
                		<?php _e( 'Venue', 'another-events-calendar' ); ?>
               		</div>
                	<div class="panel-body">
                    	<?php the_aec_address( $venue_id ); ?>
                        <?php if( $has_map ) : ?>
                        	<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
                            <a href="" data-toggle="modal" data-target="#aec-map-modal"><?php _e( 'Show map', 'another-events-calendar' ); ?></a>
                            
                            <!-- Modal -->
                            <div class="modal" id="aec-map-modal">
    							<div class="modal-dialog">  
     								<div class="modal-content">
                        				<div class="modal-body">
                                        	<div class="embed-responsive embed-responsive-16by9">
                                    			<div class="aec-map embed-responsive-item">
                                            		<div class="marker" data-latitude="<?php echo $latitude; ?>" data-longitude="<?php echo $longitude; ?>">
                                                		<?php the_aec_address( $venue_id ); ?>
                                                	</div>
                                        		</div>
                                            </div>
                        				</div>
                    				</div>
               					</div>
         					</div>
                        <?php endif; ?>
                	</div>
           		</div>
            <?php endif; ?>
  			
    	</div>
	</div>     
</div>

<?php the_aec_socialshare_buttons(); ?> 