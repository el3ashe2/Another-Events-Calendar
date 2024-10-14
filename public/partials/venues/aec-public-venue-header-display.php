<?php

/**
 * Markup the single venue header content.
 */ 
?>

<div class="aec aec-single-venue">
	<div class="row">
     	<!-- Left column -->
     	<div class="col-md-4">
        	<?php the_aec_address( $venue->ID ); ?>
        </div>
        
        <?php if( $has_map && $latitude && $longitude ) : ?>  
        	<!-- Right column -->
        	<div class="col-md-8">
        		<div class="embed-responsive embed-responsive-16by9">
        			<div class="aec-map embed-responsive-item">
        				<div class="marker" data-latitude="<?php echo $latitude; ?>" data-longitude="<?php echo $longitude; ?>"></div>
        			</div>
            	</div>
        	</div>
        <?php endif; ?>
  	</div>
    
    <div class="aec-well">
		<h3 class="text-center aec-no-margin"><?php _e( 'Events', 'another-events-calendar' ); ?></h3>
	</div>
</div>