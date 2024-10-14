<?php

/**
 * Markup the single category page header content.
 */
?>

<?php if( ! empty( $category->description ) ) : ?>
	<p><?php echo $category->description; ?></p>
<?php endif; ?>