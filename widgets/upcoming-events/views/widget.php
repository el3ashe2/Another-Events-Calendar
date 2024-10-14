<?php

/**
 * Markup the widget front-end.
 */
?>

<div class="aec aec-widget aec-upcoming-events" id="<?php echo $widget_id; ?>">
	<?php while( $aec_query->have_posts() ) : $aec_query->the_post(); ?>
    	<div class="row aec-no-margin">
        	<?php if( $show_image ) { ?>
            	<div class="col-md-4">
            		<?php if( has_post_thumbnail() ) : ?>
               			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
                	<?php else : ?>
                    	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        	<img src="<?php echo AEC_PLUGIN_URL; ?>public/images/placeholder-event.jpg" class="img-responsive" />
                        </a>
              		<?php endif; ?>
            	</div>
                
                <div class="col-md-8">
           	<?php } else { ?>
            	<div class="col-md-12">
            <?php } ?>     
                   
            	<h4 class="aec-no-margin aec-margin-bottom"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(); ?></a></h4>
                
                <?php if( $show_date ) : ?>
                    <p class="aec-no-margin">
                        <span class="glyphicon glyphicon-calendar"></span>
                        <?php echo aec_get_event_date( get_the_ID() ); ?>
                    </p>
               	<?php endif; ?>
                
				<?php $venue_id = get_post_meta( get_the_ID(), 'venue_id', true ); ?>
				<?php if( $show_venue && $venue_id > 0 ) : ?>
                	<p class="aec-no-margin text-muted">
                    	<span class="glyphicon glyphicon-map-marker"></span>
                       	<a href="<?php echo aec_venue_page_link( $venue_id );?>"><?php echo get_the_title( $venue_id ); ?> </a>
                    </p>
            	<?php endif; ?> 
            </div>
		</div>
        <hr />
	<?php endwhile; ?>
</div>