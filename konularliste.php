<?php 
/* 
Template Name: Forum Ana Sayfa 
*/

// Security check
if (!defined('ABSPATH')) {
    exit; // Direct access not allowed
}

// Skip function declarations if they are already defined in functions.php
if (!function_exists('get_forum_statistics')) {
    /**
     * Get forum statistics
     * 
     * @return array Statistics data
     */
    function get_forum_statistics() {
        // Get statistics with caching
        $cache_key = 'forum_statistics';
        $forum_stats = wp_cache_get($cache_key);
        
        if (false === $forum_stats) {
            // Topics count
            $topic_count = wp_count_posts('forum_topics')->publish;
            
            // Comments count - improved query to get ALL comments
            global $wpdb;
            $comment_count_query = "
                SELECT COUNT(comment_ID) 
                FROM $wpdb->comments 
                WHERE comment_approved = '1' 
                AND (comment_type = '' OR comment_type = 'comment')
                AND comment_post_ID IN (
                    SELECT ID FROM $wpdb->posts WHERE post_type = 'forum_topics' AND post_status = 'publish'
                )
            ";
            $comment_count = $wpdb->get_var($comment_count_query);
            
            // If comments still missing, try alternative method to count all comments
            if ($comment_count < 40) { // If below expected count, try alternative
                $alt_count_query = "
                    SELECT COUNT(*) 
                    FROM $wpdb->comments c
                    JOIN $wpdb->posts p ON c.comment_post_ID = p.ID
                    WHERE p.post_type = 'forum_topics' 
                    AND p.post_status = 'publish'
                    AND c.comment_approved = '1'
                ";
                $alt_comment_count = $wpdb->get_var($alt_count_query);
                
                // Use the higher count of the two methods
                $comment_count = max($comment_count, $alt_comment_count);
            }
            
            // User count
            $user_count = count_users()['total_users'];
            
            // Expert answers count
            $expert_count_query = "
                SELECT COUNT(post_id) 
                FROM $wpdb->postmeta 
                WHERE meta_key = 'has_expert_answer' 
                AND meta_value = 'yes'
            ";
            $expert_answer_count = $wpdb->get_var($expert_count_query);
            
            // Store in array - Add expert answers to the comments count for total replies
            $forum_stats = array(
                'topics'         => $topic_count,
                'comments'       => $comment_count + $expert_answer_count, // Include expert answers in total comments
                'users'          => $user_count,
                'expert_answers' => $expert_answer_count
            );
            
            // Cache for 1 hour
            wp_cache_set($cache_key, $forum_stats, '', HOUR_IN_SECONDS);
        }
        
        return $forum_stats;
    }
}

if (!function_exists('format_number_display')) {
    /**
     * Format number for display with K/M suffix for large numbers
     * 
     * @param int $number The number to format
     * @return string Formatted number
     */
    function format_number_display($number) {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } else if ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return $number;
    }
}

// Get forum statistics
$forum_stats = get_forum_statistics();

// Get current page
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// Get topics per page setting
$topics_per_page = get_option('forum_topics_per_page', 15);

// Get filter parameters
$filter_category = isset($_GET['category']) ? absint($_GET['category']) : 0;
$filter_sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'newest';
$filter_solved = isset($_GET['solved']) ? ($_GET['solved'] === '1' ? true : false) : false;

// Build query args
$args = array(
    'post_type'      => 'forum_topics',
    'posts_per_page' => $topics_per_page,
    'paged'          => $paged,
    'post_status'    => 'publish'
);

// Apply category filter
if ($filter_category > 0) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'topic_category',
            'field'    => 'term_id',
            'terms'    => $filter_category
        )
    );
}

// Apply solved filter (expert answers)
if ($filter_solved) {
    $args['meta_query'] = array(
        array(
            'key'   => 'has_expert_answer',
            'value' => 'yes'
        )
    );
}

// Apply sort order
switch ($filter_sort) {
    case 'newest':
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
        break;
    case 'oldest':
        $args['orderby'] = 'date';
        $args['order'] = 'ASC';
        break;
    case 'popular':
        $args['meta_key'] = 'post_views_count';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
        break;
    case 'active':
        $args['orderby'] = 'comment_count';
        $args['order'] = 'DESC';
        break;
    case 'title':
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
        break;
}

// Get topics
$topics_query = new WP_Query($args);

// Load header
get_header();
?>

<div class="forum-page">
    <!-- Page Header Banner -->
    <div class="page-banner">
        <div class="container">
            <div class="banner-content">
                <h1 class="page-title"><i class="fas fa-comments"></i> Forum</h1>
                <p class="page-description">Topluluğumuzda sorular sorun, yanıtlar alın ve fikirlerinizi paylaşın.</p>
                
                <!-- Quick Action Buttons -->
                <div class="quick-actions">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('konu-baslat'))); ?>" class="action-button primary">
                        <i class="fas fa-plus-circle"></i> Yeni Konu
                    </a>
                    <a href="#topic-categories" class="action-button secondary">
                        <i class="fas fa-th-list"></i> Kategoriler
                    </a>
                    <a href="#popular-topics" class="action-button secondary">
                        <i class="fas fa-fire"></i> Popüler Konular
                    </a>
                </div>
            </div>
            
            <!-- Forum Statistics -->
            <div class="statistics-container">
                <div class="statistic-item">
                    <div class="statistic-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="statistic-data">
                        <div class="statistic-value"><?php echo format_number_display($forum_stats['topics']); ?></div>
                        <div class="statistic-label">Konu</div>
                    </div>
                </div>
                
                <div class="statistic-item">
                    <div class="statistic-icon"><i class="fas fa-comments"></i></div>
                    <div class="statistic-data">
                        <div class="statistic-value"><?php echo format_number_display($forum_stats['comments']); ?></div>
                        <div class="statistic-label">Yanıt</div>
                    </div>
                </div>
                
                <div class="statistic-item">
                    <div class="statistic-icon"><i class="fas fa-users"></i></div>
                    <div class="statistic-data">
                        <div class="statistic-value"><?php echo format_number_display($forum_stats['users']); ?></div>
                        <div class="statistic-label">Üye</div>
                    </div>
                </div>
                
                <div class="statistic-item">
                    <div class="statistic-icon"><i class="fas fa-user-md"></i></div>
                    <div class="statistic-data">
                        <div class="statistic-value"><?php echo format_number_display($forum_stats['expert_answers']); ?></div>
                        <div class="statistic-label">Uzman Yanıtı</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <!-- Forum Search -->
            <div class="forum-search">
                <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form">
                    <input type="hidden" name="post_type" value="forum_topics">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="s" id="live-search" placeholder="Forum konularında ara..." 
                               value="<?php echo get_search_query(); ?>"
                               autocomplete="off">
                        <button type="button" class="search-clear" style="display:none;"><i class="fas fa-times"></i></button>
                    </div>
                    <button type="submit" class="search-button">Ara</button>
                </form>
                <div id="search-results" class="search-results"></div>
            </div>
            
            <!-- Forum Filters -->
            <div class="forum-filters">
                <form method="get" id="filter-form" class="filter-form">
                    <div class="filter-item">
                        <label for="category-filter">Kategori</label>
                        <select id="category-filter" name="category" class="filter-select">
                            <option value="0">Tüm Kategoriler</option>
                            <?php
                            $categories = get_terms(array(
                                'taxonomy'   => 'topic_category',
                                'hide_empty' => false,
                                'orderby'    => 'name',
                                'order'      => 'ASC'
                            ));
                            
                            if (!is_wp_error($categories)) {
                                foreach ($categories as $category) {
                                    $selected = $filter_category == $category->term_id ? 'selected' : '';
                                    echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' 
                                        . esc_html($category->name) . ' (' . esc_html($category->count) . ')</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <label for="sort-filter">Sıralama</label>
                        <select id="sort-filter" name="sort" class="filter-select">
                            <option value="newest" <?php selected($filter_sort, 'newest'); ?>>En Yeni</option>
                            <option value="oldest" <?php selected($filter_sort, 'oldest'); ?>>En Eski</option>
                            <option value="popular" <?php selected($filter_sort, 'popular'); ?>>En Popüler</option>
                            <option value="active" <?php selected($filter_sort, 'active'); ?>>En Aktif</option>
                            <option value="title" <?php selected($filter_sort, 'title'); ?>>Başlığa Göre (A-Z)</option>
                        </select>
                    </div>
                    
                    <div class="filter-item filter-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" name="solved" value="1" <?php checked($filter_solved, true); ?>>
                            <span class="checkbox-text">Sadece Uzman Yanıtı Olanlar</span>
                        </label>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="filter-button">Uygula</button>
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="reset-button">Sıfırla</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="content-wrapper">
            <!-- Main Topics List -->
            <main class="main-content">
                <div class="main-content-header">
                    <h2 class="content-title">
                        <?php 
                        if ($filter_category > 0) {
                            $term = get_term($filter_category, 'topic_category');
                            echo esc_html($term->name) . ' Konuları';
                        } else if ($filter_solved) {
                            echo 'Uzman Yanıtı Olan Konular';
                        } else {
                            echo 'Tüm Konular';
                        }
                        ?>
                    </h2>
                    
                    <?php if ($topics_query->have_posts()): ?>
                    <div class="topic-count">
                        <?php 
                        $total_topics = $topics_query->found_posts;
                        $start_item = (($paged - 1) * $topics_per_page) + 1;
                        $end_item = min($start_item + $topics_per_page - 1, $total_topics);
                        
                        printf('%d-%d / %d konu gösteriliyor', $start_item, $end_item, $total_topics);
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Topic List Header (Desktop) -->
                <div class="topics-header desktop-only">
                    <div class="th-topic">Konu</div>
                    <div class="th-replies">Yanıtlar</div>
                    <div class="th-views">Görüntüleme</div>
                    <div class="th-activity">Son Aktivite</div>
                </div>
                
                <!-- Topics List -->
                <div class="topics-list">
                    <?php
                    if ($topics_query->have_posts()):
                        while ($topics_query->have_posts()): $topics_query->the_post();
                            // Get topic data
                            $post_id = get_the_ID();
                            $comment_count = get_comments_number();
                            $views = get_post_meta($post_id, 'post_views_count', true) ? get_post_meta($post_id, 'post_views_count', true) : '0';
                            $has_expert = get_post_meta($post_id, 'has_expert_answer', true) == 'yes';
                            $author_info = get_topic_author_info($post_id);
                            $last_reply = get_last_reply_info($post_id);
                            
                            // Get topic categories
                            $categories = get_the_terms($post_id, 'topic_category');
                            $category = is_array($categories) && !empty($categories) ? $categories[0] : null;
                            
                            // Get topic tags
                            $tags = get_post_meta($post_id, 'forum_topic_tags', true);
                            $tags_array = !empty($tags) ? array_map('trim', explode(',', $tags)) : array();
                    ?>
                            <div class="topic-item <?php echo $has_expert ? 'has-expert' : ''; ?>">
                                <!-- Topic Main Info -->
                                <div class="topic-info">
                                    <div class="topic-status <?php echo $has_expert ? 'expert' : ($comment_count > 0 ? 'has-replies' : 'no-replies'); ?>">
                                        <?php if ($has_expert): ?>
                                            <i class="fas fa-user-md" title="Uzman yanıtı var"></i>
                                        <?php else: ?>
                                            <i class="fas fa-<?php echo $comment_count > 0 ? 'comments' : 'comment'; ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="topic-main-content">
                                        <h3 class="topic-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            <?php if ($has_expert): ?>
                                                <span class="expert-answer-badge">
                                                    <i class="fas fa-user-md"></i> Uzman Yanıtı
                                                </span>
                                            <?php endif; ?>
                                        </h3>
                                        
                                        <div class="topic-excerpt desktop-only">
                                            <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                        </div>
                                        
                                        <div class="topic-meta">
                                            <div class="author-info">
                                                <img src="<?php echo esc_url($author_info['avatar']); ?>" 
                                                     alt="<?php echo esc_attr($author_info['name']); ?>" 
                                                     class="author-avatar">
                                                <a href="<?php echo esc_url($author_info['url']); ?>" class="author-name">
                                                    <?php echo esc_html($author_info['name']); ?>
                                                </a>
                                            </div>
                                            
                                            <span class="topic-date">
                                                <i class="far fa-clock"></i>
                                                <?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' önce'); ?>
                                            </span>
                                            
                                            <?php if ($category): ?>
                                                <a href="<?php echo esc_url(add_query_arg('category', $category->term_id, get_permalink())); ?>" class="topic-category">
                                                    <i class="fas fa-folder"></i>
                                                    <?php echo esc_html($category->name); ?>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($tags_array)): ?>
                                                <div class="topic-tags mobile-hidden">
                                                    <i class="fas fa-tags"></i>
                                                    <?php 
                                                    $tag_links = array();
                                                    foreach (array_slice($tags_array, 0, 2) as $tag) {
                                                        $tag_links[] = '<span class="topic-tag">' . esc_html($tag) . '</span>';
                                                    }
                                                    echo implode('', $tag_links);
                                                    
                                                    if (count($tags_array) > 2) {
                                                        echo '<span class="more-tags">+' . (count($tags_array) - 2) . '</span>';
                                                    }
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Topic Stats -->
                                <div class="topic-replies">
                                    <div class="topic-number"><?php echo esc_html($comment_count + ($has_expert ? 1 : 0)); ?></div>
                                    <div class="topic-label">yanıt</div>
                                </div>
                                
                                <div class="topic-views">
                                    <div class="topic-number"><?php echo esc_html($views); ?></div>
                                    <div class="topic-label">görüntüleme</div>
                                </div>
                                
                                <!-- Last Activity -->
                                <div class="last-reply">
                                    <?php if ($last_reply): ?>
                                        <div class="last-reply-info">
                                            <img src="<?php echo esc_url($last_reply['avatar']); ?>" 
                                                 alt="<?php echo esc_attr($last_reply['name']); ?>" 
                                                 class="reply-author-avatar">
                                            
                                            <div class="last-reply-meta">
                                                <div class="reply-author-name">
                                                    <?php echo esc_html($last_reply['name']); ?>
                                                    <?php if ($last_reply['type'] === 'expert' && !empty($last_reply['title'])): ?>
                                                        <span class="expert-title"><?php echo esc_html($last_reply['title']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="reply-date">
                                                    <i class="far fa-clock"></i>
                                                    <?php echo esc_html($last_reply['date_human']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="no-replies">Henüz yanıt yok</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                    <?php
                        endwhile;
                        
                        // Pagination
                        echo '<div class="pagination">';
                        echo paginate_links(array(
                            'base'         => add_query_arg('paged', '%#%'),
                            'format'       => '',
                            'current'      => max(1, $paged),
                            'total'        => $topics_query->max_num_pages,
                            'prev_text'    => '<i class="fas fa-chevron-left"></i>',
                            'next_text'    => '<i class="fas fa-chevron-right"></i>',
                            'type'         => 'list',
                            'end_size'     => 1,
                            'mid_size'     => 2
                        ));
                        echo '</div>';
                    else:
                    ?>
                        <div class="no-topics">
                            <div class="no-topics-icon"><i class="fas fa-search"></i></div>
                            <h3>Aradığınız kriterlere uygun konu bulunamadı</h3>
                            <p>Farklı arama kriterleri deneyebilir veya yeni bir konu başlatabilirsiniz.</p>
                            <div class="no-topics-buttons">
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="button-secondary">
                                    <i class="fas fa-sync-alt"></i> Tüm Konuları Göster
                                </a>
                                <a href="<?php echo esc_url(get_permalink(get_page_by_path('konu-baslat'))); ?>" class="button-primary">
                                    <i class="fas fa-plus-circle"></i> Yeni Konu Başlat
                                </a>
                            </div>
                        </div>
                    <?php
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
            </main>
            
            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- User Widget -->
                <?php if (is_user_logged_in()): ?>
                <div class="sidebar-widget">
                    <h3 class="widget-title"><i class="fas fa-user-circle"></i> Profil</h3>
                    <div class="user-profile-mini">
                        <?php 
                        $current_user = wp_get_current_user();
                        echo get_avatar($current_user->ID, 64);
                        ?>
                        <div class="user-info">
                            <p class="user-name"><?php echo esc_html($current_user->display_name); ?></p>
                            <p class="user-role"><?php 
                                $user_roles = $current_user->roles;
                                $role_name = !empty($user_roles) ? ucfirst($user_roles[0]) : 'Üye';
                                echo esc_html($role_name); 
                            ?></p>
                        </div>
                    </div>
                    <div class="user-stats">
                        <?php
                        $user_topics = count_user_posts($current_user->ID, 'forum_topics');
                        $user_comments = get_comments(array(
                            'user_id' => $current_user->ID,
                            'count' => true
                        ));
                        ?>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo esc_html($user_topics); ?></span>
                            <span class="stat-label">Konular</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo esc_html($user_comments); ?></span>
                            <span class="stat-label">Yanıtlar</span>
                        </div>
                    </div>
                    <div class="user-actions">
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('konu-baslat'))); ?>" class="action-button">
                            <i class="fas fa-plus-circle"></i> Yeni Konu
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <!-- Call to Action for Guest Users -->
                <div class="sidebar-widget cta-widget">
                    <h3 class="widget-title"><i class="fas fa-sign-in-alt"></i> Üye Girişi</h3>
                    <p>Forum'da soru sormak ve yanıtlamak için giriş yapın veya üye olun.</p>
                    <div class="cta-buttons">
                        <a href="<?php echo esc_url('https://metaprora.com/profilim/'); ?>" class="button-primary full-width">
                            <i class="fas fa-sign-in-alt"></i> Giriş Yap
                        </a>
                        <a href="<?php echo esc_url('https://metaprora.com/profilim/'); ?>" class="button-secondary full-width">
                            <i class="fas fa-user-plus"></i> Üye Ol
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Popular Topics Widget -->
                <div class="sidebar-widget" id="popular-topics">
                    <h3 class="widget-title"><i class="fas fa-fire"></i> Popüler Konular</h3>
                    <?php
                    $cache_key = 'popular_topics_widget';
                    $popular_topics = wp_cache_get($cache_key);
                    
                    if (false === $popular_topics) {
                        $popular_topics = new WP_Query(array(
                            'post_type'      => 'forum_topics',
                            'posts_per_page' => 5,
                            'meta_key'       => 'post_views_count',
                            'orderby'        => 'meta_value_num',
                            'order'          => 'DESC',
                            'post_status'    => 'publish',
                            'no_found_rows'  => true
                        ));
                        
                        wp_cache_set($cache_key, $popular_topics, '', HOUR_IN_SECONDS);
                    }
                    
                    if ($popular_topics->have_posts()): ?>
                        <ul class="topic-list">
                        <?php while ($popular_topics->have_posts()): $popular_topics->the_post();
                            $comment_count = get_comments_number();
                            $views = get_post_meta(get_the_ID(), 'post_views_count', true) ?: '0';
                            $has_expert = get_post_meta(get_the_ID(), 'has_expert_answer', true) === 'yes';
                        ?>
                            <li class="topic-item <?php echo $has_expert ? 'has-expert' : ''; ?>">
                                <a href="<?php the_permalink(); ?>" class="topic-link">
                                    <?php if ($has_expert): ?>
                                        <i class="fas fa-user-md expert-icon" title="Uzman yanıtı var"></i>
                                    <?php else: ?>
                                        <i class="fas fa-comments"></i>
                                    <?php endif; ?>
                                    <?php the_title(); ?>
                                </a>
                                <div class="topic-meta">
                                    <span><i class="far fa-comment"></i> <?php echo $comment_count; ?></span>
                                    <span><i class="far fa-eye"></i> <?php echo $views; ?></span>
                                </div>
                            </li>
                        <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-topics">Henüz konu bulunmuyor.</p>
                    <?php endif;
                    wp_reset_postdata();
                    ?>
                </div>
                
                <!-- Categories Widget -->
                <div class="sidebar-widget" id="topic-categories">
                    <h3 class="widget-title"><i class="fas fa-folder"></i> Kategoriler</h3>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy'   => 'topic_category',
                        'hide_empty' => false,
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                    ));
                    
                    if (!is_wp_error($categories) && !empty($categories)): ?>
                        <ul class="category-list">
                        <?php foreach ($categories as $category): ?>
                            <li class="category-item">
                                <a href="<?php echo esc_url(add_query_arg('category', $category->term_id, get_permalink())); ?>" class="category-link">
                                    <i class="fas fa-folder"></i>
                                    <?php echo esc_html($category->name); ?>
                                </a>
                                <span class="category-count"><?php echo esc_html($category->count); ?></span>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Henüz kategori bulunmuyor.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Recent Activity Widget -->
                <div class="sidebar-widget">
                    <h3 class="widget-title"><i class="fas fa-clock"></i> Son Aktivite</h3>
                    <?php
                    // Get recent comments
                    $recent_comments = get_comments(array(
                        'number'      => 5,
                        'status'      => 'approve',
                        'post_type'   => 'forum_topics',
                        'post_status' => 'publish'
                    ));
                    
                    if (!empty($recent_comments)): ?>
                        <ul class="activity-list">
                        <?php foreach ($recent_comments as $comment): 
                            $comment_author = $comment->comment_author;
                            $comment_post = get_post($comment->comment_post_ID);
                            if (!$comment_post) continue;
                        ?>
                            <li class="activity-item">
                                <div class="activity-avatar">
                                    <?php echo get_avatar($comment, 40); ?>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-header">
                                        <a href="<?php echo esc_url(get_permalink($comment->comment_post_ID)); ?>" class="activity-title">
                                            <?php echo esc_html(get_the_title($comment->comment_post_ID)); ?>
                                        </a>
                                    </div>
                                    <div class="activity-meta">
                                        <span class="activity-author"><?php echo esc_html($comment_author); ?></span>
                                        <span class="activity-time">
                                            <?php echo esc_html(human_time_diff(strtotime($comment->comment_date), current_time('timestamp')) . ' önce'); ?>
                                        </span>
                                    </div>
                                    <div class="activity-excerpt">
                                        <?php echo wp_trim_words(wp_strip_all_tags($comment->comment_content), 8, '...'); ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-activity">Henüz aktivite bulunmuyor.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Tags Widget -->
                <div class="sidebar-widget">
                    <h3 class="widget-title"><i class="fas fa-tags"></i> Popüler Etiketler</h3>
                    <?php
                    // Get popular tags
                    global $wpdb;
                    $tags_query = "
                        SELECT meta_value 
                        FROM $wpdb->postmeta 
                        WHERE meta_key = 'forum_topic_tags'
                        AND meta_value != '' 
                        LIMIT 40
                    ";
                    $tags_data = $wpdb->get_col($tags_query);
                    
                    $tags_count = array();
                    if (!empty($tags_data)) {
                        foreach ($tags_data as $tags_string) {
                            $tags = array_map('trim', explode(',', $tags_string));
                            foreach ($tags as $tag) {
                                if (!empty($tag)) {
                                    if (isset($tags_count[$tag])) {
                                        $tags_count[$tag]++;
                                    } else {
                                        $tags_count[$tag] = 1;
                                    }
                                }
                            }
                        }
                        
                        // Sort by count
                        arsort($tags_count);
                        $tags_count = array_slice($tags_count, 0, 20);
                    }
                    
                    if (!empty($tags_count)): ?>
                        <div class="tag-cloud">
                            <?php foreach ($tags_count as $tag => $count): ?>
                                <a href="<?php echo esc_url(add_query_arg(array('s' => urlencode($tag), 'post_type' => 'forum_topics'), home_url('/'))); ?>" class="tag-item">
                                    <?php echo esc_html($tag); ?>
                                    <span class="tag-count"><?php echo esc_html($count); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-tags">Henüz etiket bulunmuyor.</p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
jQuery(document).ready(function($) {
    // Live search functionality
    var searchTimeout;
    var searchInput = $('#live-search');
    var searchResults = $('#search-results');
    var searchClear = $('.search-clear');
    
    function performSearch() {
        var searchTerm = searchInput.val();
        
        // Toggle clear button
        if (searchTerm.length > 0) {
            searchClear.show();
        } else {
            searchClear.hide();
        }
        
        if (searchTerm.length >= 2) {
            searchResults.html('<div class="search-loading active"><i class="fas fa-spinner"></i> Aranıyor...</div>').addClass('active');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'live_search_topics',
                    search_term: searchTerm,
                    nonce: '<?php echo wp_create_nonce('live_search_nonce'); ?>'
                },
                success: function(response) {
                    searchResults.html(response);
                    
                    if ($.trim(response) !== '') {
                        searchResults.addClass('active');
                    } else {
                        searchResults.removeClass('active');
                    }
                },
                error: function() {
                    searchResults.html('<div class="search-loading error">Arama sırasında bir hata oluştu.</div>');
                }
            });
        } else {
            searchResults.removeClass('active').html('');
        }
    }
    
    // Search input event
    searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });
    
    // Clear search button
    searchClear.on('click', function() {
        searchInput.val('').focus();
        searchResults.removeClass('active').html('');
        searchClear.hide();
    });
    
    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.forum-search').length) {
            searchResults.removeClass('active');
        }
    });
    
    // Close search results on escape key
    $(document).on('keyup', function(e) {
        if (e.key === "Escape") {
            searchResults.removeClass('active');
        }
    });
    
    // Filter form auto-submit
    $('.filter-select').on('change', function() {
        // Only auto-submit if this is not the first page load
        if (window.location.href.indexOf('?') > -1) {
            $('#filter-form').submit();
        }
    });
    
    // Smooth scroll to anchors
    $('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 20
            }, 500);
        }
    });
    
    // Tooltip for expert answers
    $('.expert-icon').tooltip({
        title: 'Uzman yanıtı var',
        placement: 'top'
    });
    
    // Initialize tooltips if Bootstrap is available
    if (typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Mobile topic excerpt toggle
    $('.topic-title a').on('click', function(e) {
        // Only for mobile devices
        if (window.innerWidth <= 768) {
            var $item = $(this).closest('.topic-item');
            var $excerpt = $item.find('.topic-excerpt');
            
            if ($excerpt.hasClass('mobile-visible')) {
                // Continue with normal link behavior
                return true;
            }
            
            e.preventDefault();
            $excerpt.addClass('mobile-visible').slideDown(200);
            return false;
        }
    });
});
</script>
<style>
/* General Styles */
.forum-page {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
    background-color: #f8f9fa;
    min-height: 100vh;
    padding-bottom: 40px;
    color: #333;
    line-height: 1.6;
}

/* Container */
.forum-page .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Page Header Banner */
.forum-page .page-banner {
    background: linear-gradient(135deg, #3f51b5, #2196F3);
    padding: 40px 0;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    position: relative;
}

.forum-page .page-banner:before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-image: url('data:image/svg+xml;utf8,<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect width="8" height="8" fill="rgba(255,255,255,0.03)" /><rect x="8" y="8" width="8" height="8" fill="rgba(255,255,255,0.03)" /></svg>');
    opacity: 0.3;
}

.forum-page .banner-content {
    position: relative;
    z-index: 1;
}

.forum-page .page-title {
    margin: 0 0 10px 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.forum-page .page-description {
    margin: 0 0 25px 0;
    font-size: 16px;
    max-width: 600px;
    opacity: 0.95;
}

/* Quick Action Buttons */
.forum-page .quick-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}

.forum-page .action-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 30px;
    font-weight: 500;
    font-size: 15px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.forum-page .action-button.primary {
    background: #ff5722;
    color: white;
    box-shadow: 0 2px 6px rgba(255,87,34,0.3);
}

.forum-page .action-button.primary:hover {
    background: #e64a19;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255,87,34,0.4);
}

.forum-page .action-button.secondary {
    background: rgba(255,255,255,0.15);
    color: white;
    backdrop-filter: blur(5px);
}

.forum-page .action-button.secondary:hover {
    background: rgba(255,255,255,0.25);
}

/* Statistics Container */
.forum-page .statistics-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 10px;
    backdrop-filter: blur(5px);
}

.forum-page .statistic-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 5px 0;
    flex: 1;
    min-width: 120px;
}

.forum-page .statistic-icon {
    background: rgba(255,255,255,0.2);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.forum-page .statistic-value {
    font-size: 22px;
    font-weight: 700;
    line-height: 1.2;
}

.forum-page .statistic-label {
    font-size: 14px;
    opacity: 0.9;
}

/* Search and Filter Section */
.forum-page .search-filter-section {
    background: white;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    flex-wrap: wrap;
}

/* Forum Search */
.forum-page .forum-search {
    flex: 1;
    padding: 20px;
    min-width: 300px;
    position: relative;
}

.forum-page .search-form {
    display: flex;
    align-items: center;
    gap: 10px;
}

.forum-page .search-input-wrapper {
    position: relative;
    flex: 1;
}

.forum-page .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #757575;
}

.forum-page .search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #757575;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    font-size: 14px;
}

.forum-page .search-input-wrapper input {
    width: 100%;
    padding: 12px 40px;
    border: 1px solid #ddd;
    border-radius: 30px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.forum-page .search-input-wrapper input:focus {
    border-color: #2196F3;
    outline: none;
    box-shadow: 0 0 0 3px rgba(33,150,243,0.1);
}

.forum-page .search-button {
    background: #2196F3;
    color: white;
    border: none;
    border-radius: 30px;
    padding: 12px 25px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.forum-page .search-button:hover {
    background: #1976D2;
}

/* Search Results */
.forum-page .search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    z-index: 10;
    max-height: 400px;
    overflow-y: auto;
    display: none;
}

.forum-page .search-results.active {
    display: block;
}

/* Forum Filters */
.forum-page .forum-filters {
    flex: 1;
    padding: 20px;
    border-left: 1px solid #eee;
    min-width: 300px;
}

.forum-page .filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.forum-page .filter-item {
    flex: 1;
    min-width: 150px;
}

.forum-page .filter-item label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    color: #666;
}

.forum-page .filter-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background-color: #f9f9f9;
    transition: all 0.3s;
}

.forum-page .filter-select:focus {
    border-color: #2196F3;
    outline: none;
    box-shadow: 0 0 0 3px rgba(33,150,243,0.1);
}

.forum-page .filter-checkbox {
    display: flex;
    align-items: center;
}

.forum-page .checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
}

.forum-page .filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.forum-page .filter-button {
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.forum-page .filter-button:hover {
    background: #388E3C;
}

.forum-page .reset-button {
    color: #666;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
}

.forum-page .reset-button:hover {
    color: #2196F3;
}

/* Content Layout */
.forum-page .content-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

/* Main Content */
.forum-page .main-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
}

.forum-page .main-content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.forum-page .content-title {
    margin: 0;
    font-size: 20px;
    color: #333;
}

.forum-page .topic-count {
    font-size: 14px;
    color: #666;
    background: #f5f5f5;
    padding: 6px 12px;
    border-radius: 20px;
}

/* Topics Header */
.forum-page .topics-header {
    display: grid;
    grid-template-columns: 2fr 0.5fr 0.5fr 1fr;
    background: #f8f9fa;
    padding: 15px 20px;
    font-weight: 600;
    color: #555;
    border-bottom: 1px solid #eee;
}

.forum-page .topics-header > div {
    padding: 0 10px;
}

/* Topic List */
.forum-page .topics-list {
    padding: 5px 0;
}

.forum-page .topic-item {
    display: grid;
    grid-template-columns: 2fr 0.5fr 0.5fr 1fr;
    padding: 20px;
    border-bottom: 1px solid #eee;
    align-items: center;
    transition: background 0.2s ease;
}

.forum-page .topic-item:hover {
    background: #f9f9f9;
}

.forum-page .topic-item.has-expert {
    background-color: #f9fff9;
}

.forum-page .topic-item.has-expert:hover {
    background-color: #f0fff0;
}

/* Topic Info */
.forum-page .topic-info {
    display: flex;
    gap: 15px;
    align-items: flex-start;
    padding: 0 10px;
}

.forum-page .topic-status {
    min-width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 16px;
    margin-top: 3px;
}

.forum-page .topic-status.no-replies {
    background: #f5f5f5;
    color: #757575;
}

.forum-page .topic-status.has-replies {
    background: #e3f2fd;
    color: #1976D2;
}

.forum-page .topic-status.expert {
    background: #e8f5e9;
    color: #388E3C;
}

.forum-page .topic-main-content {
    flex: 1;
}

.forum-page .topic-title {
    margin: 0 0 8px 0;
    font-size: 17px;
    font-weight: 600;
    line-height: 1.3;
}

.forum-page .topic-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.2s;
}

.forum-page .topic-title a:hover {
    color: #2196F3;
}

.forum-page .expert-answer-badge {
    display: inline-flex;
    align-items: center;
    background: #e8f5e9;
    color: #388E3C;
    border-radius: 20px;
    padding: 2px 8px;
    font-size: 12px;
    margin-left: 8px;
    vertical-align: middle;
    font-weight: normal;
}

.forum-page .topic-excerpt {
    margin-bottom: 8px;
    color: #666;
    font-size: 14px;
}

/* Topic Meta */
.forum-page .topic-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 13px;
    color: #666;
    align-items: center;
}

.forum-page .author-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.forum-page .author-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
}

.forum-page .author-name {
    color: #2196F3;
    text-decoration: none;
    font-weight: 500;
}

.forum-page .topic-date,
.forum-page .topic-category {
    display: flex;
    align-items: center;
    gap: 5px;
}

.forum-page .topic-category {
    color: #555;
    text-decoration: none;
    transition: color 0.2s;
}

.forum-page .topic-category:hover {
    color: #2196F3;
}

.forum-page .topic-tags {
    display: flex;
    align-items: center;
    gap: 5px;
}

.forum-page .topic-tag {
    background: #eee;
    color: #555;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
}

.forum-page .more-tags {
    font-size: 12px;
    color: #999;
}

/* Topic Stats */
.forum-page .topic-replies,
.forum-page .topic-views {
    text-align: center;
    padding: 0 10px;
}

.forum-page .topic-number {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.forum-page .topic-label {
    font-size: 13px;
    color: #666;
}

/* Last Reply */
.forum-page .last-reply {
    padding: 0 10px;
}

.forum-page .last-reply-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.forum-page .reply-author-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.forum-page .reply-author-name {
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.forum-page .expert-title {
    display: block;
    font-size: 12px;
    color: #388E3C;
    font-weight: normal;
}

.forum-page .reply-date {
    font-size: 13px;
    color: #666;
}

.forum-page .no-replies {
    font-size: 14px;
    color: #999;
    font-style: italic;
}

/* No Topics Found */
.forum-page .no-topics {
    text-align: center;
    padding: 50px 20px;
}

.forum-page .no-topics-icon {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 20px;
}

.forum-page .no-topics h3 {
    margin: 0 0 10px 0;
    color: #555;
}

.forum-page .no-topics p {
    margin: 0 0 25px 0;
    color: #777;
}

.forum-page .no-topics-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.forum-page .button-secondary {
    background: #f5f5f5;
    color: #555;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 10px 20px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.forum-page .button-secondary:hover {
    background: #eee;
}

.forum-page .button-primary {
    background: #2196F3;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.forum-page .button-primary:hover {
    background: #1976D2;
}

/* Pagination */
.forum-page .pagination {
    padding: 20px;
    display: flex;
    justify-content: center;
}

.forum-page .pagination ul {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 5px;
}

.forum-page .pagination li {
    margin: 0;
}

.forum-page .pagination a,
.forum-page .pagination span {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s;
}

.forum-page .pagination a:hover {
    background: #f0f8ff;
    border-color: #2196F3;
}

.forum-page .pagination .current {
    background: #2196F3;
    color: white;
    border-color: #2196F3;
}

/* Sidebar */
.forum-page .sidebar-widget {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.forum-page .widget-title {
    font-size: 18px;
    margin-top: 0;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #333;
}

/* User Profile Mini */
.forum-page .user-profile-mini {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.forum-page .user-profile-mini img {
    border-radius: 50%;
}

.forum-page .user-info {
    flex: 1;
}

.forum-page .user-name {
    font-weight: 600;
    margin: 0 0 5px 0;
    color: #333;
}

.forum-page .user-role {
    margin: 0;
    color: #777;
    font-size: 14px;
}

.forum-page .user-stats {
    display: flex;
    text-align: center;
    background: #f8f9fa;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 15px;
}

.forum-page .stat-item {
    flex: 1;
    padding: 12px 10px;
    display: flex;
    flex-direction: column;
}

.forum-page .stat-item:first-child {
    border-right: 1px solid #eee;
}

.forum-page .stat-value {
    font-size: 20px;
    font-weight: 600;
    color: #2196F3;
}

.forum-page .stat-label {
    font-size: 14px;
    color: #666;
}

.forum-page .user-actions {
    display: flex;
    justify-content: center;
}

.forum-page .action-button {
    background: #2196F3;
    color: white;
    border-radius: 6px;
    padding: 10px 20px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
    font-weight: 500;
}

.forum-page .action-button:hover {
    background: #1976D2;
}

/* CTA Widget */
.forum-page .cta-widget {
    text-align: center;
    background: linear-gradient(135deg, #f5f7fa, #e9f0f6);
}

.forum-page .cta-widget .widget-title {
    justify-content: center;
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 15px;
}

.forum-page .cta-widget p {
    margin: 0 0 20px 0;
    color: #555;
    font-size: 15px;
}

.forum-page .cta-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.forum-page .full-width {
    width: 100%;
    justify-content: center;
}

/* Topic List in Widget */
.forum-page .topic-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.forum-page .topic-list .topic-item {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    display: block;
}

.forum-page .topic-list .topic-item:last-child {
    border-bottom: none;
}

.forum-page .topic-list .topic-link {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    color: #333;
    text-decoration: none;
    margin-bottom: 5px;
    font-weight: 500;
    transition: color 0.2s;
    line-height: 1.4;
}

.forum-page .topic-list .topic-link:hover {
    color: #2196F3;
}

.forum-page .topic-list .topic-link i {
    margin-top: 3px;
}

.forum-page .topic-list .expert-icon {
    color: #4CAF50;
}

.forum-page .topic-list .topic-meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #777;
    margin-left: 24px;
}

/* Category List */
.forum-page .category-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.forum-page .category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.forum-page .category-item:last-child {
    border-bottom: none;
}

.forum-page .category-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    text-decoration: none;
    transition: color 0.2s;
    font-weight: 500;
}

.forum-page .category-link:hover {
    color: #2196F3;
}

.forum-page .category-count {
    background: #f1f1f1;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    color: #666;
}

/* Activity List */
.forum-page .activity-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.forum-page .activity-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.forum-page .activity-item:last-child {
    border-bottom: none;
}

.forum-page .activity-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.forum-page .activity-content {
    flex: 1;
}

.forum-page .activity-title {
    color: #333;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s;
    display: block;
    margin-bottom: 5px;
    line-height: 1.4;
}

.forum-page .activity-title:hover {
    color: #2196F3;
}

.forum-page .activity-meta {
    display: flex;
    gap: 10px;
    font-size: 12px;
    color: #777;
    margin-bottom: 5px;
}

.forum-page .activity-author {
    font-weight: 500;
    color: #333;
}

.forum-page .activity-excerpt {
    font-size: 13px;
    color: #666;
    line-height: 1.4;
}

/* Tag Cloud */
.forum-page .tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.forum-page .tag-item {
    background: #f1f1f1;
    color: #555;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.forum-page .tag-item:hover {
    background: #e3f2fd;
    color: #1976D2;
}

.forum-page .tag-count {
    background: rgba(0,0,0,0.1);
    font-size: 11px;
    border-radius: 10px;
    padding: 2px 6px;
    min-width: 20px;
    text-align: center;
}

/* Search Results Styling */
.forum-page .search-result-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.forum-page .search-result-item:last-child {
    border-bottom: none;
}

.forum-page .search-result-header {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.forum-page .search-result-title {
    font-weight: 500;
    color: #333;
    text-decoration: none;
    transition: color 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.forum-page .search-result-title:hover {
    color: #2196F3;
}

.forum-page .search-result-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 13px;
    color: #777;
}

.forum-page .search-no-results {
    padding: 20px;
    text-align: center;
    color: #666;
}

.forum-page .search-loading {
    padding: 20px;
    text-align: center;
    color: #666;
}

.forum-page .search-loading.active i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Styles */
@media screen and (max-width: 992px) {
    .forum-page .content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .forum-page .search-filter-section {
        flex-direction: column;
    }
    
    .forum-page .forum-filters {
        border-left: none;
        border-top: 1px solid #eee;
    }
}

@media screen and (max-width: 768px) {
    .forum-page .statistics-container {
        flex-wrap: wrap;
    }
    
    .forum-page .statistic-item {
        flex: 1 1 40%;
    }
    
    .forum-page .desktop-only {
        display: none !important;
    }
    
    .forum-page .topic-item {
        display: block;
        position: relative;
    }
    
    .forum-page .topic-info {
        margin-bottom: 15px;
    }
    
    .forum-page .topic-replies,
    .forum-page .topic-views {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-right: 15px;
        margin-bottom: 10px;
    }
    
    .forum-page .topic-number,
    .forum-page .topic-label {
        font-size: 14px;
    }
    
    .forum-page .last-reply {
        padding-left: 50px;
    }
    
    .forum-page .filter-form {
        flex-direction: column;
    }
    
    .forum-page .filter-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .forum-page .filter-button,
    .forum-page .reset-button {
        width: 100%;
        text-align: center;
    }
}

@media screen and (max-width: 576px) {
    .forum-page .page-banner {
        padding: 30px 0;
    }
    
    .forum-page .page-title {
        font-size: 24px;
    }
    
    .forum-page .quick-actions {
        flex-direction: column;
    }
    
    .forum-page .action-button {
        width: 100%;
        justify-content: center;
    }
    
    .forum-page .search-form {
        flex-direction: column;
    }
    
    .forum-page .search-button {
        width: 100%;
        margin-top: 10px;
    }
    
    .forum-page .statistic-item {
        flex: 1 1 100%;
    }
    
    .forum-page .mobile-hidden {
        display: none !important;
    }
}
</style>
<?php get_footer(); ?>