<?php
/**
* The template for displaying Comments.
*
* The area of the page that contains both current comments
* and the comment form. The actual display of comments is
* handled by a callback to dazzlo_comments() which is
* located in the functions.php file.
*
* @package dazzlo
* @since dazzlo 1.0
*/

if ( post_password_required() ) { ?>
	<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'dazzlo'); ?></p>
<?php
	return;
}
?>

<div id="comments" class="comments">
	<div class="comments-wrap">
		<ol class="commentlist">
			<?php wp_list_comments( "callback=dazzlo_comments" ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" role="navigation">
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'dazzlo' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'dazzlo' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php comment_form(); ?>
	</div><!-- .comments-wrap -->
</div><!-- #comments -->