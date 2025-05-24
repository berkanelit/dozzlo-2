<?php
/**
 * The template for displaying standard pages.
 *
 * This template displays all pages with proper structure and formatting.
 * 
 * @package dazzlo
 * @since dazzlo 1.0
 */

get_header(); ?>

<div id="content-wrap" class="clearfix">
    <div class="single-container"></div>
    
    <div id="content" class="<?php echo (get_theme_mod('dazzlo_general_sidebar_page') == true) ? 'fullwidth' : ''; ?>">
        <?php get_template_part('template-title'); ?>
        
        <div class="post-wrap">
            <?php if (have_posts()): 
                while (have_posts()): the_post(); ?>
                
                <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
                    <div class="box">
                        <?php
                        // Display gallery if post has gallery format
                        if (has_post_format('gallery', $post->ID) && function_exists('array_gallery')) {
                            array_gallery();
                        } 
                        // Display video if post has video meta
                        elseif (get_post_meta($post->ID, 'arrayvideo', true)) { ?>
                            <div class="post-media video-container">
                                <?php echo wp_kses_post(get_post_meta($post->ID, 'arrayvideo', true)); ?>
                            </div>
                        <?php 
                        // Display featured image if available
                        } elseif (has_post_thumbnail()) { ?>
                            <div class="post-media featured-image-container">
                                <?php the_post_thumbnail('dazzlo-large-image', array(
                                    'class' => 'featured-image',
                                    'title' => get_the_title(),
                                    'alt' => get_the_title(),
                                    'loading' => 'lazy'
                                )); ?>
                            </div>
                        <?php } ?>
                        
                        <div class="frame">
                            <header class="title-wrap">
                                <h1 class="page-title"><?php the_title(); ?></h1>
                            </header>
                            
                            <div class="post-content">
                                <?php 
                                // Page content
                                the_content();
                                
                                // Page pagination for paginated posts
                                wp_link_pages(array(
                                    'before' => '<div class="page-links">' . __('Pages:', 'dazzlo'),
                                    'after'  => '</div>',
                                )); 
                                ?>
                            </div>
                        </div>
                    </div>
                </article>
                
                <?php endwhile; ?>
        </div><!-- post-wrap -->
        
        <?php else: ?>
            <div class="no-results not-found">
                <div class="page-header">
                    <h1 class="page-title"><?php _e('Nothing Found', 'dazzlo'); ?></h1>
                </div>
                <div class="page-content">
                    <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'dazzlo'); ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) {
            comments_template();
        }
        ?>
    </div><!-- #content -->
    
    <?php
    // Load the sidebar if full width is not enabled
    if (!get_theme_mod('dazzlo_general_sidebar_page')) {
        get_sidebar();
    }
    ?>
</div><!-- #content-wrap -->


<?php get_footer(); ?>