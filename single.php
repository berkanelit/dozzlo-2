<?php
/**
 * Single Post Template
 * 
 * This template is used when a single post page is shown.
 * Optimized for wider content area and better mobile responsiveness
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */

get_header(); ?>

<div id="content-wrap" class="clearfix wider-content">
    <div id="content" tabindex="-1" class="<?php echo (get_theme_mod('dazzlo_general_sidebar_post') == true) ? 'fullwidth' : 'wider-main-content'; ?>">
        
        <?php get_template_part('template-title'); ?>

        <div class="post-wrap single-post-wrap">
            <?php if (have_posts()): 
                while (have_posts()): the_post(); ?>
                
                <article <?php post_class('post single-post'); ?> id="post-<?php the_ID(); ?>">
                    <div class="box">
                        <div class="frame">
                            <div class="top-part-wrap">
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

                                <div class="title-meta-wrap">
                                    <?php if (!is_page()): ?>
                                        <div class="bar-categories">
                                            <div class="post-date">
                                                <i class="fa fa-calendar"></i>
                                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_date()); ?></a>
                                            </div>

                                            <?php if (has_category()): ?>
                                                <div class="categories">
                                                    <?php dazzlo_getCategory(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <header class="title-wrap">
                                        <h1 class="entry-title"><?php the_title(); ?></h1>

                                        <?php if (!is_page()): ?>
                                            <div class="title-meta">
                                                <div class="author-info">
                                                    <?php echo get_avatar(get_the_author_meta('ID'), 
                                                        apply_filters('dazzlo_author_bio_avatar_size', 40), 
                                                        '', 
                                                        get_the_author_meta('display_name'), 
                                                        array('class' => 'author-avatar')); 
                                                    ?>
                                                    <span class="author-name"><?php the_author_posts_link(); ?></span>
                                                </div>

                                                <div class="comment-count">
                                                    <i class="fa fa-comments"></i>
                                                    <?php comments_popup_link(
                                                        __('0 Comments', 'dazzlo'), 
                                                        __('1 Comment', 'dazzlo'), 
                                                        __('% Comments', 'dazzlo'),
                                                        'comments-link',
                                                        __('Comments Closed', 'dazzlo')
                                                    ); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </header>
                                </div>
                            </div>

                            <div class="post-content">
                                <?php if (is_search() || is_archive()): ?>
                                    <div class="post-excerpt">
                                        <?php the_excerpt(); ?>
                                        <p><a href="<?php the_permalink(); ?>" class="read-more-button"><?php _e('Read More', 'dazzlo'); ?></a></p>
                                    </div>
                                <?php else: ?>
                                    <div class="post-full-content">
                                        <?php the_content(); ?>

                                        <?php if (is_single() || is_page()): ?>
                                            <div class="page-links">
                                                <?php wp_link_pages(array(
                                                    'before' => '<div class="page-links-title">' . __('Pages:', 'dazzlo') . '</div>',
                                                    'after' => '',
                                                    'link_before' => '<span class="page-number">',
                                                    'link_after' => '</span>',
                                                )); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Post Meta -->
                                <?php get_template_part('template-meta'); ?>
                            </div>
                        </div>
                    </div>
                </article>

                <?php endwhile; ?>
            
                <!-- Post Navigation -->
                <?php get_template_part('template-nav'); ?>

            <?php else: ?>
                <div class="no-results not-found">
                    <h2><?php _e('Nothing Found', 'dazzlo'); ?></h2>
                    <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for.', 'dazzlo'); ?></p>
                </div>
            <?php endif; ?>

            <!-- Author Box -->
            <?php 
            if (!get_theme_mod('dazzlo_general_author_post')) {
                do_action('dazzlo_authorbox');
            }
            ?>

            <!-- Comments -->
            <?php 
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>
        </div>
    </div>

    <!-- Sidebar -->
    <?php 
    if (!get_theme_mod('dazzlo_general_sidebar_post')) {
        get_sidebar();
    }
    ?>
</div>


<?php get_footer(); ?>