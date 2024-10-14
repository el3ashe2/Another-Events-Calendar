<?php

/**
 * Markup the single tag page header content.
 */
?>

<?php if( ! empty( $tag->description ) ) : ?>
	<p><?php echo $tag->description; ?></p>
<?php endif; ?>