<?php
/**
 * Template for the post navigation and archive pagination.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */
?>




<?php if( is_single() ) { ?>
    <div class="next-prev">
        <?php if ($prev_post = get_previous_post()) : ?>
            <div class="prev-post">
                <strong class="next-prev-title"><?php echo esc_html__('Previous Post', 'dazzlo'); ?></strong>

                <?php
                $prev_thumbnail = get_the_post_thumbnail($prev_post->ID, 'medium', array('class' => 'pagination-previous'));
                $prev_title = $prev_post->post_title;

                previous_post_link(
                    '<span class="thumbnail">%link</span>',
                    $prev_thumbnail . '<span class="title">' . esc_html($prev_title) . '</span>',
                    TRUE
                );
                ?>
            </div>
        <?php endif; ?>


        <?php if ($next_post = get_next_post()) : ?>
            <div class="next-post">
                <strong class="next-prev-title"><?php echo esc_html__('Next Post', 'dazzlo'); ?></strong>

                <?php
                $next_thumbnail = get_the_post_thumbnail($next_post->ID, 'medium', array('class' => 'pagination-next'));
                $next_title = $next_post->post_title;

                next_post_link(
                    '<span class="thumbnail">%link</span>',
                    $next_thumbnail . '<span class="title">' . esc_html($next_title) . '</span>',
                    TRUE
                );
                ?>
            </div>
        <?php endif; ?>
    </div><!-- next prev -->
<?php } ?>



<!-- post navigation -->
<?php if( $wp_query->max_num_pages > 1 ) : ?>
    <?php the_posts_pagination(array(
        'screen_reader_text' => __( 'Posts navigation','dazzlo' ),
        'mid_size' => 2
    )); ?>
<?php endif; ?>


