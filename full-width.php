<?php
/**
 * Template Name: Full Width
 *
 * A clean, optimized template for displaying full-width pages without sidebars.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */

get_header(); ?>

<div id="content-wrap" class="full-width-wrap clearfix">
    <div id="content" class="full-width-content">
        
        <?php get_template_part('template-title'); ?>
        
        <div class="post-wrap full-width-post-wrap">
            <?php if (have_posts()) : 
                while (have_posts()) : the_post(); ?>
                
                <article <?php post_class('post full-width-post'); ?>>
                    <div class="box">
                        <?php
                        // Check for gallery post format
                        if (has_post_format('gallery', $post->ID) && function_exists('array_gallery')) {
                            array_gallery();
                        }
                        // Check for video
                        elseif (get_post_meta($post->ID, 'arrayvideo', true)) { ?>
                            <div class="post-media post-video">
                                <?php echo wp_kses_post(get_post_meta($post->ID, 'arrayvideo', true)); ?>
                            </div>
                        <?php
                        // Check for featured image
                        } elseif (has_post_thumbnail()) { ?>
                            <div class="post-media post-thumbnail">
                                <a class="featured-image" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <?php the_post_thumbnail('dazzlo-large-image', array('class' => 'img-responsive')); ?>
                                </a>
                            </div>
                        <?php } ?>
                        
                        <div class="frame frame-full">
                            <header class="entry-header">
                                <h1 class="entry-title">
                                    <?php the_title(); ?>
                                </h1>
                            </header>
                            
                            <div class="post-content">
                                <?php 
                                the_content();
                                
                                // For multi-page posts
                                wp_link_pages(array(
                                    'before' => '<div class="page-links">' . __('Pages:', 'dazzlo'),
                                    'after'  => '</div>',
                                )); 
                                ?>
                            </div>
                        </div>
                        
                        <?php get_template_part('template-meta'); ?>
                    </div>
                </article>
                
                <?php endwhile; ?>
                
                <?php 
                // Comments
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>
                
            <?php else : ?>
                
                <div class="no-results">
                    <h2><?php _e('No content found', 'dazzlo'); ?></h2>
                    <p><?php _e('The page you requested could not be found.', 'dazzlo'); ?></p>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</div>


<?php get_footer(); ?>