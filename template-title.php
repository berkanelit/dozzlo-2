<?php
/**
 * Template for the post and page titles.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */
?>

<?php if ( !is_front_page() && !is_single() && !is_page() )  { ?>
	<div class="sub-title">
    <?php
        the_archive_title( '<h1>', '</h1>' );
    ?>
	</div>
<?php } ?>