<?php

/**
 *  Markup the settings pages of the plugin.
 */ 
?>

<div class="wrap">
	
	<?php settings_errors(); ?>
    
    <h2 class="nav-tab-wrapper">
    	<a href="?post_type=aec_events&page=aec_settings" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'General', 'another-events-calendar' ); ?>
        </a>
    	<a href="?post_type=aec_events&page=aec_settings&tab=page" class="nav-tab <?php echo $active_tab == 'page' ? 'nav-tab-active' : ''; ?>">
        	<?php _e( 'Pages', 'another-events-calendar' ); ?>
        </a>
    	<a href="?post_type=aec_events&page=aec_settings&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">
        	<?php _e( 'Advanced', 'another-events-calendar' ); ?>
        </a>
	</h2>

	<form method="post" action="options.php"> 
    	<?php
			settings_fields( 'aec_'.$active_tab.'_settings_tab' );
			do_settings_sections( 'aec_'.$active_tab.'_settings_tab' );
					
			submit_button();
		?>
	</form>

</div>