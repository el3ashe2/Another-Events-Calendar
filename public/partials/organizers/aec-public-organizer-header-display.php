<?php

/**
 * Markup the header content of the single organizer page.
 */ 
?>

<div class="aec aec-single-organizer">
	<!-- Header -->
	<div class="row">
     	<?php if( ! empty( $thumbnail ) ) { ?>
        	<!-- Left column -->
           	<div class="col-md-4">
        		<?php echo $thumbnail; ?>
            </div>
            
             <!-- Right column -->
            <div class="col-md-8">
        <?php } else { ?>
        	<div class="col-md-12">
        <?php } 
			$meta = array();
				
			if( $phone ) $meta[] = sprintf( '<abbr title="%s">%s:</abbr>%s', __( 'Phone', 'another-events-calendar' ),  __( 'P', 'another-events-calendar' ), $phone );
			if( $website ) $meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', $website, $website );
			if( $email ) $meta[] = sprintf( '<a href="mailto:%s" target="_blank">%s</a>', $email, $email );
			if( $description ) $meta[] = $description;
				
			echo implode( '<br />', $meta );
		?>
      	</div>
  	</div>
    
	<div class="aec-well">
		<h3 class="text-center aec-no-margin"><?php _e( 'Events', 'another-events-calendar' ); ?></h3>
	</div>
</div>