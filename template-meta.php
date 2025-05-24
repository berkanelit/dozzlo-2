<?php
/**
 * Template for the post meta, which includes share links, tags and categories.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */
?>


	<div class="bar">
		<div class="bar-frame clearfix">
			<div class="share">
				 <?php the_tags('<div class="tags">
                                                    <i class="fa fa-tags"></i>
                                                    ',', ','
                    </div>'); ?>
                                           
			
		</div><!-- bar frame -->
	</div><!-- bar -->