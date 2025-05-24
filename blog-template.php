<?php
/**
 * Template Name: Blog Grid
 *
 * A template for displaying blog posts in a three-column grid layout with a popular posts slider.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */

// Register custom thumbnail size for blog grid (add to theme's functions.php if not already present)
add_action('after_setup_theme', function() {
    add_image_size('dazzlo-blog-thumb', 400, 300, true); // 4:3 aspect ratio, hard crop
});

get_header(); ?>

<div id="content-wrap" class="container">
    <div id="content" tabindex="0" class="post-list fullwidth">
        <!-- Blog Title -->
        <?php if (get_theme_mod('dazzlo_blog_title', 'Blog Posts')) : ?>
            <h2 class="section-title"><?php echo wp_kses_post(get_theme_mod('dazzlo_blog_title', 'Blog Posts')); ?></h2>
        <?php endif; ?>

        <!-- Blog Grid -->
        <div class="blog-grid-container">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $blog_posts = new WP_Query(array(
                'post_type' => 'post',
                'paged' => $paged,
                'posts_per_page' => 12 // Max 12 posts per page
            ));

            if ($blog_posts->have_posts()) :
            ?>
                <div class="grid-row">
                    <?php while ($blog_posts->have_posts()) : $blog_posts->the_post(); ?>
                        <div class="grid-item">
                            <article <?php post_class('post-box'); ?>>
                                <div class="post-inner">
                                    <!-- Post Media -->
                                    <div class="post-media">
                                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                            <div class="image-container">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <?php the_post_thumbnail('dazzlo-blog-thumb', array(
                                                        'class' => 'post-thumbnail',
                                                        'loading' => 'lazy',
                                                        'alt' => get_the_title()
                                                    )); ?>
                                                <?php else : ?>
                                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/images/boat-placeholder.jpg'); ?>" 
                                                         alt="<?php esc_attr_e('Boat Placeholder', 'dazzlo'); ?>" 
                                                         class="post-thumbnail" 
                                                         loading="lazy" />
                                                <?php endif; ?>
                                            </div>
                                            <div class="post-date" aria-label="<?php echo esc_attr(get_the_date()); ?>">
                                                <span class="day"><?php the_time('d'); ?></span>
                                                <span class="month"><?php the_time('M'); ?></span>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Post Content -->
                                    <div class="post-content">
                                        <div class="post-meta">
                                            <div class="post-categories">
                                                <?php the_category(', '); ?>
                                            </div>
                                            <div class="post-comments">
                                                <span aria-label="<?php esc_attr_e('Comments', 'dazzlo'); ?>">
                                                    <?php comments_popup_link('0', '1', '%', '', ''); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <h2 class="post-title">
                                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                                <?php the_title(); ?>
                                            </a>
                                        </h2>

                                        <div class="post-author">
                                            <span class="author-avatar"><?php echo get_avatar(get_the_author_meta('ID'), 28); ?></span>
                                            <span class="author-name"><?php the_author(); ?></span>
                                        </div>

                                        <div class="post-excerpt">
                                            <?php echo esc_html(dazzlo_custom_excerpt(25)); ?>
                                        </div>

                                        <div class="post-read-more">
                                            <a href="<?php the_permalink(); ?>" class="read-more-button">
                                                <?php esc_html_e('Read More', 'dazzlo'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrap">
                    <?php
                    echo paginate_links(array(
                        'total' => $blog_posts->max_num_pages,
                        'current' => max(1, $paged),
                        'prev_text' => esc_html__('Newer Posts', 'dazzlo'),
                        'next_text' => esc_html__('Older Posts', 'dazzlo'),
                    ));
                    ?>
                </div>

                <?php wp_reset_postdata(); ?>

            <?php else : ?>
                <div class="no-posts">
                    <p><?php esc_html_e('Sorry, no posts matched your criteria.', 'dazzlo'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Popular Blogs Slider -->
        <section class="popular-blogs">
            <h2 class="section-title"><?php esc_html_e('Popular Blogs', 'dazzlo'); ?></h2>
            <div class="popular-slider">
                <?php
                $popular_posts = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 6,
                    'meta_key' => 'post_views_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC'
                ));

                if ($popular_posts->have_posts()) :
                    while ($popular_posts->have_posts()) : $popular_posts->the_post();
                ?>
                        <div class="slider-item">
                            <article <?php post_class('popular-post'); ?>>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <div class="image-container">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('dazzlo-blog-thumb', array(
                                                'class' => 'popular-thumbnail',
                                                'loading' => 'lazy',
                                                'alt' => get_the_title()
                                            )); ?>
                                        <?php else : ?>
                                            <img src="<?php echo esc_url(get_template_directory_uri() . '/images/boat-placeholder.jpg'); ?>" 
                                                 alt="<?php esc_attr_e('Boat Placeholder', 'dazzlo'); ?>" 
                                                 class="popular-thumbnail" 
                                                 loading="lazy" />
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="popular-title"><?php the_title(); ?></h3>
                                </a>
                            </article>
                        </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
            <button class="slider-prev" aria-label="<?php esc_attr_e('Previous Slide', 'dazzlo'); ?>">❮</button>
            <button class="slider-next" aria-label="<?php esc_attr_e('Next Slide', 'dazzlo'); ?>">❯</button>
        </section>
    </div>
</div>

<?php
/**
 * Custom excerpt function with word limit
 *
 * @param int $limit Number of words to display
 * @return string Formatted excerpt
 */
function dazzlo_custom_excerpt($limit) {
    $excerpt = get_the_excerpt();
    $excerpt = wp_strip_all_tags($excerpt);
    $excerpt = preg_replace('/\[[^\]]+\]/', '', $excerpt);
    $words = explode(' ', $excerpt, $limit + 1);
    if (count($words) > $limit) {
        array_pop($words);
        $excerpt = implode(' ', $words) . '...';
    } else {
        $excerpt = implode(' ', $words);
    }
    return $excerpt;
}
?>

<style type="text/css">
/* General Styles */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

.fullwidth {
    width: 100%;
}

.section-title {
    text-align: center;
    font-size: 32px;
    font-weight: 700;
    color: #1a2b4a;
    margin: 40px 0 30px;
    position: relative;
}

.section-title:after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background: #0077b6;
    margin: 10px auto;
}

/* Blog Grid Styles */
.blog-grid-container {
    margin: 0 -15px;
}

.grid-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.grid-item {
    padding: 0 15px;
}

.post-box {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-box:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.post-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.post-media {
    position: relative;
    overflow: hidden;
}

.image-container {
    position: relative;
    width: 100%;
    padding-top: 75%; /* 4:3 aspect ratio (300/400 = 0.75) */
}

.post-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.post-box:hover .post-thumbnail {
    transform: scale(1.08);
}

.post-date {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #0077b6;
    color: #fff;
    padding: 8px 12px;
    border-radius: 6px;
    text-align: center;
}

.post-date .day {
    display: block;
    font-size: 18px;
    font-weight: 700;
}

.post-date .month {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
}

.post-content {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.post-meta {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.post-categories a {
    color: #0077b6;
    text-decoration: none;
    font-weight: 600;
}

.post-categories a:hover {
    text-decoration: underline;
}

.post-comments {
    color: #666;
}

.post-title {
    font-size: 20px;
    line-height: 1.4;
    margin: 0 0 12px;
}

.post-title a {
    color: #1a2b4a;
    text-decoration: none;
}

.post-title a:hover {
    color: #0077b6;
}

.post-author {
    display: flex;
    align-items: center;
    font-size: 14px;
    margin-bottom: 15px;
}

.author-avatar img {
    border-radius: 50%;
    width: 28px;
    height: 28px;
    margin-right: 8px;
}

.author-name {
    color: #1a2b4a;
    font-weight: 600;
}

.post-excerpt {
    color: #555;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
}

.post-read-more {
    margin-top: auto;
}

.read-more-button {
    display: inline-block;
    padding: 10px 20px;
    background: #0077b6;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.3s ease;
}

.read-more-button:hover {
    background: #005f8c;
}

/* Pagination */
.pagination-wrap {
    text-align: center;
    margin: 40px 0;
}

.pagination a, .pagination span {
    display: inline-block;
    padding: 10px 15px;
    margin: 0 5px;
    background: #f5f5f5;
    color: #1a2b4a;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.pagination a:hover {
    background: #0077b6;
    color: #fff;
}

.pagination .current {
    background: #0077b6;
    color: #fff;
}

/* Popular Blogs Slider */
.popular-blogs {
    margin: 60px 0;
}

.popular-slider {
    display: flex;
    overflow: hidden;
    position: relative;
}

.slider-item {
    flex: 0 0 33.333%;
    padding: 0 10px;
    box-sizing: border-box;
}

.popular-post {
    text-align: center;
}

.popular-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    border-radius: 8px;
}

.popular-title {
    font-size: 16px;
    margin: 10px 0 0;
    color: #1a2b4a;
    text-decoration: none;
}

.popular-title:hover {
    color: #0077b6;
}

.slider-prev, .slider-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 119, 182, 0.8);
    color: #fff;
    border: none;
    padding: 10px;
    cursor: pointer;
    border-radius: 50%;
    font-size: 18px;
}

.slider-prev {
    left: 10px;
}

.slider-next {
    right: 10px;
}

.slider-prev:hover, .slider-next:hover {
    background: #005f8c;
}

/* Responsive */
@media (max-width: 992px) {
    .grid-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .grid-row {
        grid-template-columns: 1fr;
    }

    .section-title {
        font-size: 28px;
    }

    .post-title {
        font-size: 18px;
    }

    .slider-item {
        flex: 0 0 50%;
    }
}

@media (max-width: 576px) {
    .slider-item {
        flex: 0 0 100%;
    }
}
</style>

<script>
// Simple slider functionality for popular blogs
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.popular-slider');
    const items = document.querySelectorAll('.slider-item');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    let currentIndex = 0;

    function updateSlider() {
        const itemWidth = items[0].offsetWidth;
        slider.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    }

    nextBtn.addEventListener('click', () => {
        if (currentIndex < items.length - 3) {
            currentIndex++;
            updateSlider();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });

    window.addEventListener('resize', updateSlider);
    updateSlider();
});
</script>

<?php get_footer(); ?>