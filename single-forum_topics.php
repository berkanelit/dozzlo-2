<?php
/**
 * Template for displaying single forum topic posts
 * 
 * @package WordPress
 * @subpackage Forum_System
 */

// Security check
if (!defined('ABSPATH')) {
    exit; // Direct access not allowed
}

// Start the session for view counting
if (!session_id()) {
    session_start();
}

// Increment view count
function increment_topic_view_count() {
    $post_id = get_the_ID();
    $count = get_post_meta($post_id, 'post_views_count', true);
    
    // Don't count views from admin or post author
    if (current_user_can('manage_options') || get_current_user_id() == get_post_field('post_author', $post_id)) {
        return;
    }
    
    // Don't increment on refresh within a session
    if (!isset($_SESSION['viewed_topics']) || !in_array($post_id, $_SESSION['viewed_topics'])) {
        if (empty($count)) {
            update_post_meta($post_id, 'post_views_count', 1);
        } else {
            update_post_meta($post_id, 'post_views_count', intval($count) + 1);
        }
        
        // Track in session
        if (!isset($_SESSION['viewed_topics'])) {
            $_SESSION['viewed_topics'] = array();
        }
        $_SESSION['viewed_topics'][] = $post_id;
    }
}

// Increment view count
increment_topic_view_count();

// Get header
get_header();

?>

<div class="forum-topic-detail">
    <!-- Breadcrumb Navigation -->
    <div class="container">
        <div class="breadcrumb">
            <ul>
                <li><a href="<?php echo esc_url(home_url()); ?>">Ana Sayfa</a></li>
                <li><a href="<?php echo esc_url(home_url('/forum/')); ?>">Forum</a></li>
                <?php
                $categories = get_the_terms(get_the_ID(), 'topic_category');
                if ($categories && !is_wp_error($categories)) {
                    echo '<li><a href="' . esc_url(add_query_arg('category', $categories[0]->term_id, home_url('/forum/'))) . '">' 
                        . esc_html($categories[0]->name) . '</a></li>';
                }
                ?>
                <li><?php the_title(); ?></li>
            </ul>
        </div>

        <div class="content-wrapper">
            <main class="main-content">
                <?php
                // Main WordPress Loop
                if (have_posts()): while (have_posts()): the_post();
                    // Get topic meta data
                    $post_id = get_the_ID();
                    $views_count = get_post_meta($post_id, 'post_views_count', true) ?: '0';
                    $has_expert_answer = get_post_meta($post_id, 'has_expert_answer', true) == 'yes';
                    $expert_name = get_post_meta($post_id, 'expert_name', true);
                    $expert_title = get_post_meta($post_id, 'expert_title', true);
                    $expert_answer = get_post_meta($post_id, 'expert_answer', true);
                    $topic_tags = get_post_meta($post_id, 'forum_topic_tags', true);
                    $tags_array = !empty($topic_tags) ? array_map('trim', explode(',', $topic_tags)) : array();
                    
                    // Get author info
                    $author_info = get_topic_author_info($post_id);
                ?>
                    <!-- Topic Header Section -->
                    <div class="topic-header">
                        <h1 class="topic-title"><?php the_title(); ?></h1>
                        
                        <div class="topic-meta">
                            <div class="topic-author">
                                <a href="<?php echo esc_url($author_info['url']); ?>" class="author-avatar">
                                    <img src="<?php echo esc_url($author_info['avatar']); ?>" 
                                         alt="<?php echo esc_attr($author_info['name']); ?>"
                                         width="36" height="36">
                                </a>
                                <div class="author-details">
                                    <a href="<?php echo esc_url($author_info['url']); ?>" class="author-name">
                                        <?php echo esc_html($author_info['name']); ?>
                                    </a>
                                    <span class="topic-date">
                                        <i class="far fa-clock"></i>
                                        <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' önce'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="topic-stats">
                                <span class="topic-stat views">
                                    <i class="far fa-eye"></i> <?php echo number_format($views_count); ?> görüntülenme
                                </span>
                                <span class="topic-stat comments">
                                    <i class="far fa-comments"></i> <?php echo get_comments_number(); ?> yanıt
                                </span>
                                <?php if ($has_expert_answer): ?>
                                <span class="topic-stat expert">
                                    <i class="fas fa-user-md"></i> Uzman yanıtı
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($categories && !is_wp_error($categories)): ?>
                            <div class="topic-categories">
                                <i class="fas fa-folder"></i>
                                <?php foreach ($categories as $category): ?>
                                    <a href="<?php echo esc_url(add_query_arg('category', $category->term_id, home_url('/forum/'))); ?>" class="topic-category">
                                        <?php echo esc_html($category->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($tags_array)): ?>
                            <div class="topic-tags">
                                <i class="fas fa-tags"></i>
                                <?php foreach ($tags_array as $tag): ?>
                                    <a href="<?php echo esc_url(add_query_arg(array('s' => urlencode($tag), 'post_type' => 'forum_topics'), home_url('/'))); ?>" class="topic-tag">
                                        <?php echo esc_html($tag); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Topic Main Content -->
                    <div class="topic-main">
                        <div class="topic-content">
                            <?php if (empty(get_the_content())): ?>
                                <p class="empty-content">İçerik bulunmuyor.</p>
                            <?php else: ?>
                                <?php the_content(); ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="topic-footer">
                            <div class="topic-actions">
                                <button class="action-button reply-button" id="main-reply-button" data-scroll-to="#respond">
                                    <i class="fas fa-reply"></i> Yanıtla
                                </button>
                                
                                <button class="action-button share-button" id="main-share-button">
                                    <i class="fas fa-share-alt"></i> Paylaş
                                </button>
                                
                                <button class="action-button report-button" id="main-report-button">
                                    <i class="fas fa-flag"></i> Bildir
                                </button>
                            </div>
                            
                            <div class="topic-author-panel">
                                <div class="author-avatar">
                                    <img src="<?php echo esc_url($author_info['avatar']); ?>" 
                                         alt="<?php echo esc_attr($author_info['name']); ?>"
                                         width="60" height="60">
                                </div>
                                <div class="author-info">
                                    <div class="author-name">
                                        <?php echo esc_html($author_info['name']); ?>
                                        <span class="author-role"><?php echo ucfirst($author_info['role']); ?></span>
                                    </div>
                                    <?php if (!empty($author_info['registered'])): ?>
                                    <div class="author-meta">
                                        <span><i class="fas fa-user-clock"></i> <?php echo $author_info['registered']; ?> süredir üye</span>
                                        <span><i class="fas fa-file-alt"></i> <?php echo $author_info['post_count']; ?> konu</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expert Answer Section (if exists) -->
                    <?php if ($has_expert_answer && !empty($expert_answer)): ?>
                    <div class="expert-answer-section">
                        <div class="expert-answer-header">
                            <div class="expert-info">
                                <div class="expert-avatar">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="expert-details">
                                    <h3><?php echo esc_html($expert_name); ?></h3>
                                    <?php if (!empty($expert_title)): ?>
                                        <span class="expert-title"><?php echo esc_html($expert_title); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="expert-badge">
                                <i class="fas fa-check-circle"></i> Uzman Cevabı
                            </div>
                        </div>
                        <div class="expert-answer-content">
                            <?php echo wpautop(wp_kses_post($expert_answer)); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Comments Section -->
                    <div class="comments-section">
                        <div class="comments-header">
                            <h3 class="comments-title">
                                <i class="fas fa-comments"></i>
                                Yanıtlar (<?php echo get_comments_number(); ?>)
                            </h3>
                            
                            <?php if (get_comments_number() > 0): ?>
                            <div class="comments-sort">
                                <label for="comments-sort-select">Sıralama:</label>
                                <select id="comments-sort-select" class="comments-sort-select">
                                    <option value="oldest">En Eski</option>
                                    <option value="newest">En Yeni</option>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if (get_comments_number() > 0): ?>
                            <!-- Comments List -->
                            <div class="comments-list">
                                <?php
                                // Get comments for this post
                                $comments = get_comments(array(
                                    'post_id' => get_the_ID(),
                                    'status' => 'approve',
                                    'order' => 'ASC'
                                ));
                                ?>
                                <ul class="comment-list">
                                    <?php
                                    // Display comments using the custom walker
                                    wp_list_comments(array(
                                        'walker' => new Forum_Comment_Walker(),
                                        'style' => 'ul',
                                        'callback' => null,
                                        'max_depth' => 3,
                                        'avatar_size' => 50,
                                        'short_ping' => true,
                                        'reply_text' => '<i class="fas fa-reply"></i> Yanıtla'
                                    ), $comments);
                                    ?>
                                </ul>
                                
                                <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>
                                <div class="comments-pagination">
                                    <nav class="navigation comment-navigation">
                                        <div class="nav-links">
                                            <div class="nav-previous"><?php previous_comments_link('Önceki Yanıtlar'); ?></div>
                                            <div class="nav-next"><?php next_comments_link('Sonraki Yanıtlar'); ?></div>
                                        </div>
                                    </nav>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- No Comments Message -->
                            <div class="no-comments">
                                <p>Henüz yanıt bulunmuyor. İlk yanıtı siz yazın!</p>
                            </div>
                        <?php endif; ?>

                        <!-- Comment Form -->
                        <?php
                        comment_form(array(
                            'title_reply'          => '<i class="fas fa-reply"></i> Yanıt Yaz',
                            'title_reply_to'       => '<i class="fas fa-reply"></i> %s\'a yanıt yaz',
                            'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
                            'title_reply_after'    => '</h3>',
                            'class_form'           => 'comment-form',
                            'comment_notes_before' => '<p class="comment-notes">E-posta adresiniz yayınlanmayacak. Gerekli alanlar işaretlendi *</p>',
                            'comment_notes_after'  => '',
                            'label_submit'         => 'Yanıtı Gönder',
                            'submit_button'        => '<button type="submit" id="%2$s" class="submit-comment %3$s"><i class="fas fa-paper-plane"></i> %4$s</button>',
                            'comment_field'        => '<div class="comment-form-comment form-row"><textarea id="comment" name="comment" placeholder="Yanıtınızı buraya yazın..." aria-required="true" required="required"></textarea></div>'
                        ));
                        ?>
                    </div>

                <?php endwhile; endif; ?>
            </main>

            <!-- Sidebar -->
            <aside class="topic-sidebar">
                <!-- Action Buttons Widget -->
                <div class="sidebar-widget action-widget">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('konu-baslat'))); ?>" class="action-button primary-button">
                        <i class="fas fa-plus-circle"></i> Yeni Konu
                    </a>
                    <a href="<?php echo esc_url(home_url('/forum/')); ?>" class="action-button secondary-button">
                        <i class="fas fa-th-list"></i> Tüm Konular
                    </a>
                </div>
                
                <!-- Related Topics Widget -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">
                        <i class="fas fa-exchange-alt"></i> Benzer Konular
                    </h3>
                    <?php
                    // Get category IDs for related topics
                    $category_ids = array();
                    if ($categories && !is_wp_error($categories)) {
                        foreach ($categories as $category) {
                            $category_ids[] = $category->term_id;
                        }
                    }
                    
                    // Get related topics
                    $related_args = array(
                        'post_type'      => 'forum_topics',
                        'posts_per_page' => 5,
                        'post__not_in'   => array(get_the_ID()),
                        'orderby'        => 'rand',
                        'post_status'    => 'publish'
                    );
                    
                    // Add tax query if categories exist
                    if (!empty($category_ids)) {
                        $related_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'topic_category',
                                'field'    => 'term_id',
                                'terms'    => $category_ids
                            )
                        );
                    }
                    
                    $related_topics = new WP_Query($related_args);
                    
                    if ($related_topics->have_posts()): ?>
                        <ul class="topic-list">
                        <?php while ($related_topics->have_posts()): $related_topics->the_post();
                            $comment_count = get_comments_number();
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
                                    <span><i class="far fa-clock"></i> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' önce'; ?></span>
                                </div>
                            </li>
                        <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-topics">Benzer konu bulunamadı.</p>
                    <?php endif;
                    wp_reset_postdata();
                    ?>
                </div>
                
                <!-- Popular Topics Widget -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">
                        <i class="fas fa-fire"></i> Popüler Konular
                    </h3>
                    <?php
                    $popular_args = array(
                        'post_type'      => 'forum_topics',
                        'posts_per_page' => 5,
                        'post__not_in'   => array(get_the_ID()),
                        'meta_key'       => 'post_views_count',
                        'orderby'        => 'meta_value_num',
                        'order'          => 'DESC',
                        'post_status'    => 'publish'
                    );
                    
                    $popular_topics = new WP_Query($popular_args);
                    
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
                <div class="sidebar-widget">
                    <h3 class="widget-title">
                        <i class="fas fa-folder"></i> Kategoriler
                    </h3>
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
                                <a href="<?php echo esc_url(add_query_arg('category', $category->term_id, home_url('/forum/'))); ?>" class="category-link">
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
            </aside>
        </div>
    </div>
</div>

<!-- Share Dialog HTML -->
<div id="shareDialog" class="share-dialog">
    <div class="share-dialog-content">
        <div class="share-dialog-header">
            <h3 class="share-dialog-title">Konuyu Paylaş</h3>
            <button id="closeShareDialog" class="close-dialog">&times;</button>
        </div>
        
        <div class="share-options">
            <div class="share-option share-facebook" id="shareFacebook">
                <i class="fab fa-facebook-f"></i>
                <span>Facebook</span>
            </div>
            
            <div class="share-option share-twitter" id="shareTwitter">
                <i class="fab fa-twitter"></i>
                <span>Twitter</span>
            </div>
            
            <div class="share-option share-whatsapp" id="shareWhatsapp">
                <i class="fab fa-whatsapp"></i>
                <span>WhatsApp</span>
            </div>
            
            <div class="share-option share-telegram" id="shareTelegram">
                <i class="fab fa-telegram-plane"></i>
                <span>Telegram</span>
            </div>
        </div>
        
        <div class="share-link-section">
            <p>Veya bağlantıyı kopyalayın:</p>
            <div class="share-link-input">
                <input type="text" id="shareUrl" class="share-url" value="<?php echo esc_url(get_permalink()); ?>" readonly>
                <button id="copyLink" class="copy-link">Kopyala</button>
            </div>
        </div>
    </div>
</div>

<!-- Report Dialog HTML -->
<div id="reportDialog" class="report-dialog">
    <div class="report-dialog-content">
        <div class="report-dialog-header">
            <h3 class="report-dialog-title">Konuyu Bildir</h3>
            <button id="closeReportDialog" class="close-dialog">&times;</button>
        </div>
        
        <form id="reportForm" class="report-form">
            <div class="form-group">
                <label for="report-reason">Bildirim Nedeni</label>
                <select id="report-reason" required>
                    <option value="">Lütfen bir neden seçin</option>
                    <option value="inappropriate">Uygunsuz İçerik</option>
                    <option value="spam">Spam</option>
                    <option value="duplicate">Tekrarlanan Konu</option>
                    <option value="wrong-category">Yanlış Kategori</option>
                    <option value="other">Diğer</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="report-description">Açıklama</label>
                <textarea id="report-description" placeholder="Lütfen bildirimin detaylarını açıklayın..."></textarea>
            </div>
            
            <div class="report-actions">
                <button type="button" class="cancel-report" id="cancelReport">İptal</button>
                <button type="submit" class="submit-report">Bildir</button>
            </div>
        </form>
    </div>
</div>
<style>
    /* General Styles */
    .forum-topic-detail {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
        background-color: #f8f9fa;
        padding: 30px 0;
        min-height: 100vh;
        color: #333;
        line-height: 1.6;
    }
    
    /* Container */
    .forum-topic-detail .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    /* Breadcrumb Styles */
    .forum-topic-detail .breadcrumb {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .forum-topic-detail .breadcrumb ul {
        list-style: none;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin: 0;
        padding: 0;
        align-items: center;
    }
    
    .forum-topic-detail .breadcrumb li {
        display: flex;
        align-items: center;
    }
    
    .forum-topic-detail .breadcrumb li:not(:last-child):after {
        content: '›';
        margin-left: 10px;
        margin-right: 5px;
        color: #777;
        font-size: 18px;
    }
    
    .forum-topic-detail .breadcrumb a {
        color: #2196F3;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .forum-topic-detail .breadcrumb a:hover {
        color: #0D47A1;
    }
    
    .forum-topic-detail .breadcrumb li:last-child {
        color: #555;
        font-weight: 600;
    }
    
    /* Content Layout */
    .forum-topic-detail .content-wrapper {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 30px;
    }
    
    /* Main Content Area */
    .forum-topic-detail .main-content {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }
    
    /* Topic Header Styles */
    .forum-topic-detail .topic-header {
        background: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .forum-topic-detail .topic-title {
        font-size: 26px;
        margin: 0 0 20px 0;
        line-height: 1.3;
        color: #333;
    }
    
    .forum-topic-detail .topic-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: center;
    }
    
    /* Author Info */
    .forum-topic-detail .topic-author {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .forum-topic-detail .author-avatar img {
        border-radius: 50%;
        object-fit: cover;
    }
    
    .forum-topic-detail .author-details {
        display: flex;
        flex-direction: column;
    }
    
    .forum-topic-detail .author-name {
        font-weight: 600;
        color: #1976D2;
        text-decoration: none;
    }
    
    .forum-topic-detail .topic-date {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        color: #666;
    }
    
    /* Topic Stats */
    .forum-topic-detail .topic-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .forum-topic-detail .topic-stat {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        color: #555;
    }
    
    .forum-topic-detail .topic-stat.expert {
        color: #388E3C;
    }
    
    /* Topic Categories */
    .forum-topic-detail .topic-categories {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    
    .forum-topic-detail .topic-category {
        background: #eee;
        padding: 4px 12px;
        border-radius: 20px;
        color: #555;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .topic-category:hover {
        background: #e0e0e0;
        color: #333;
    }
    
    /* Topic Tags */
    .forum-topic-detail .topic-tags {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 14px;
    }
    
    .forum-topic-detail .topic-tag {
        background: #e3f2fd;
        color: #1976D2;
        padding: 4px 12px;
        border-radius: 20px;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .topic-tag:hover {
        background: #bbdefb;
    }
    
    /* Main Topic Content */
    .forum-topic-detail .topic-main {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .forum-topic-detail .topic-content {
        padding: 25px;
        color: #333;
        line-height: 1.6;
        font-size: 16px;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
    
    .forum-topic-detail .topic-content p {
        margin-top: 0;
        margin-bottom: 1.5em;
    }
    
    .forum-topic-detail .topic-content p:last-child {
        margin-bottom: 0;
    }
    
    .forum-topic-detail .topic-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
    }
    
    .forum-topic-detail .topic-content a {
        color: #1976D2;
        text-decoration: none;
    }
    
    .forum-topic-detail .topic-content a:hover {
        text-decoration: underline;
    }
    
    .forum-topic-detail .topic-content ul,
    .forum-topic-detail .topic-content ol {
        margin-bottom: 1.5em;
        padding-left: 20px;
    }
    
    .forum-topic-detail .topic-content blockquote {
        margin: 1.5em 0;
        padding: 10px 20px;
        border-left: 4px solid #bbdefb;
        background: #f5f9fc;
        color: #444;
    }
    
    .forum-topic-detail .empty-content {
        color: #999;
        font-style: italic;
    }
    
    /* Topic Footer */
    .forum-topic-detail .topic-footer {
        border-top: 1px solid #eee;
        padding: 20px 25px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 20px;
    }
    
    /* Topic Actions */
    .forum-topic-detail .topic-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .forum-topic-detail .action-button {
        background: #f5f5f5;
        border: none;
        border-radius: 6px;
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #555;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .action-button:hover {
        background: #e0e0e0;
    }
    
    .forum-topic-detail .reply-button {
        background: #e3f2fd;
        color: #1976D2;
    }
    
    .forum-topic-detail .reply-button:hover {
        background: #bbdefb;
    }
    
    /* Topic Author Panel */
    .forum-topic-detail .topic-author-panel {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .forum-topic-detail .author-info {
        display: flex;
        flex-direction: column;
    }
    
    .forum-topic-detail .author-name {
        font-weight: 600;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .forum-topic-detail .author-role {
        background: #f1f1f1;
        color: #666;
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: normal;
    }
    
    .forum-topic-detail .author-meta {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #666;
    }
    
    /* Expert Answer Section */
    .forum-topic-detail .expert-answer-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 2px solid #4CAF50;
        overflow: hidden;
    }
    
    .forum-topic-detail .expert-answer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #e8f5e9;
        padding: 18px 25px;
    }
    
    .forum-topic-detail .expert-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .forum-topic-detail .expert-avatar {
        width: 50px;
        height: 50px;
        background: #4CAF50;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }
    
    .forum-topic-detail .expert-details h3 {
        margin: 0 0 5px 0;
        font-size: 18px;
        color: #2e7d32;
    }
    
    .forum-topic-detail .expert-title {
        font-size: 14px;
        color: #388E3C;
    }
    
    .forum-topic-detail .expert-badge {
        background: #4CAF50;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        font-size: 14px;
    }
    
    .forum-topic-detail .expert-answer-content {
        padding: 25px;
        color: #333;
        font-size: 16px;
        line-height: 1.6;
    }
    
    /* Comments Section */
    .forum-topic-detail .comments-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .forum-topic-detail .comments-header {
        padding: 20px 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .forum-topic-detail .comments-title {
        margin: 0;
        font-size: 18px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .forum-topic-detail .comments-sort {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    
    .forum-topic-detail .comments-sort label {
        color: #666;
    }
    
    .forum-topic-detail .comments-sort-select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        background-color: #f9f9f9;
    }
    
    /* Comments List */
    .forum-topic-detail .comments-list {
        padding: 0;
    }
    
    .forum-topic-detail .comment-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .forum-topic-detail .comment {
        border-bottom: 1px solid #eee;
        padding: 20px 25px;
    }
    
    .forum-topic-detail .comment:last-child {
        border-bottom: none;
    }
    
    .forum-topic-detail .comment-reply {
        margin-left: 40px;
        border-top: 1px solid #f5f5f5;
        border-bottom: none;
        background: #fafafa;
        border-radius: 0 0 8px 8px;
    }
    
    .forum-topic-detail .comment-header {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
    }
    
    .forum-topic-detail .comment-author-avatar img {
        border-radius: 50%;
        width: 50px;
        height: 50px;
    }
    
    .forum-topic-detail .comment-author-info {
        display: flex;
        flex-direction: column;
    }
    
    .forum-topic-detail .comment-author-name {
        font-weight: 600;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .forum-topic-detail .author-badge {
        background: #e3f2fd;
        color: #1976D2;
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: normal;
    }
    
    .forum-topic-detail .comment-metadata {
        font-size: 13px;
        color: #777;
    }
    
    .forum-topic-detail .comment-awaiting {
        color: #ff9800;
        font-size: 13px;
        display: block;
        margin-top: 5px;
    }
    
    .forum-topic-detail .comment-content {
        padding-left: 65px;
        color: #333;
        font-size: 15px;
        line-height: 1.6;
        margin-bottom: 10px;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
    
    .forum-topic-detail .comment-content p {
        margin-top: 0;
        margin-bottom: 1em;
    }
    
    .forum-topic-detail .comment-content p:last-child {
        margin-bottom: 0;
    }
    
    .forum-topic-detail .comment-actions {
        padding-left: 65px;
        display: flex;
        gap: 15px;
    }
    
    .forum-topic-detail .reply-link {
        display: flex;
    }
    
    .forum-topic-detail .reply-link a {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s;
    }
    
    .forum-topic-detail .reply-link a:hover {
        color: #1976D2;
    }
    
    .forum-topic-detail .comment-permalink {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s;
    }
    
    .forum-topic-detail .comment-permalink:hover {
        color: #1976D2;
    }
    
    /* Comment Pagination */
    .forum-topic-detail .comments-pagination {
        padding: 20px 25px;
        border-top: 1px solid #eee;
    }
    
    .forum-topic-detail .comment-navigation {
        display: flex;
        justify-content: space-between;
    }
    
    .forum-topic-detail .nav-previous a,
    .forum-topic-detail .nav-next a {
        background: #f5f5f5;
        color: #555;
        padding: 8px 15px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .nav-previous a:hover,
    .forum-topic-detail .nav-next a:hover {
        background: #e0e0e0;
    }
    
    /* No Comments */
    .forum-topic-detail .no-comments {
        padding: 30px 25px;
        text-align: center;
        color: #666;
    }
    
    /* Comment Form */
    .forum-topic-detail .comment-respond {
        padding: 25px;
        border-top: 1px solid #eee;
    }
    
    .forum-topic-detail .comment-reply-title {
        margin: 0 0 20px 0;
        font-size: 18px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .forum-topic-detail .comment-notes {
        margin-bottom: 20px;
        font-size: 14px;
        color: #666;
    }
    
    .forum-topic-detail .comment-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .forum-topic-detail .form-row {
        display: flex;
        flex-direction: column;
    }
    
    .forum-topic-detail .form-row input,
    .forum-topic-detail .form-row textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .forum-topic-detail .form-row input:focus,
    .forum-topic-detail .form-row textarea:focus {
        border-color: #2196F3;
        outline: none;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }
    
    .forum-topic-detail .form-row textarea {
        min-height: 150px;
        resize: vertical;
    }
    
    .forum-topic-detail .form-row label {
        margin-bottom: 8px;
        font-size: 14px;
        color: #555;
    }
    
    .forum-topic-detail .comment-form-cookies-consent {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #666;
    }
    
    .forum-topic-detail .comment-form-cookies-consent input {
        width: auto;
    }
    
    .forum-topic-detail .submit-comment {
        background: #2196F3;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 12px 20px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        align-self: flex-start;
        transition: all 0.3s;
    }
    
    .forum-topic-detail .submit-comment:hover {
        background: #1976D2;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .forum-topic-detail .submit-comment:active {
        transform: translateY(0);
    }
    
    /* Cancel Reply */
    .forum-topic-detail #cancel-comment-reply-link {
        font-size: 14px;
        color: #666;
        margin-left: 10px;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .forum-topic-detail #cancel-comment-reply-link:hover {
        color: #f44336;
    }
    
    /* Sidebar Styles */
    .forum-topic-detail .topic-sidebar {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }
    
    .forum-topic-detail .sidebar-widget {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .forum-topic-detail .widget-title {
        font-size: 18px;
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #333;
    }
    
    /* Action Widget */
    .forum-topic-detail .action-widget {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 15px;
    }
    
    .forum-topic-detail .primary-button {
        background: #ff5722;
        color: white;
        text-decoration: none;
        padding: 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.3s;
        box-shadow: 0 2px 6px rgba(255,87,34,0.3);
    }
    
    .forum-topic-detail .primary-button:hover {
        background: #e64a19;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255,87,34,0.4);
    }
    
    .forum-topic-detail .secondary-button {
        background: #f5f5f5;
        color: #555;
        text-decoration: none;
        padding: 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .secondary-button:hover {
        background: #e0e0e0;
    }
    
    /* Topics List */
    .forum-topic-detail .topic-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .forum-topic-detail .topic-list .topic-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .forum-topic-detail .topic-list .topic-item:last-child {
        border-bottom: none;
    }
    
    .forum-topic-detail .topic-list .topic-link {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #333;
        text-decoration: none;
        margin-bottom: 5px;
        font-weight: 500;
        line-height: 1.4;
        transition: color 0.2s;
    }
    
    .forum-topic-detail .topic-list .topic-link:hover {
        color: #2196F3;
    }
    
    .forum-topic-detail .topic-list .topic-link i {
        margin-top: 3px;
    }
    
    .forum-topic-detail .topic-list .expert-icon {
        color: #4CAF50;
    }
    
    .forum-topic-detail .topic-list .topic-meta {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #777;
        margin-left: 24px;
    }
    
    .forum-topic-detail .no-topics {
        color: #888;
        font-style: italic;
        text-align: center;
    }
    
    /* Categories List */
    .forum-topic-detail .category-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .forum-topic-detail .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .forum-topic-detail .category-item:last-child {
        border-bottom: none;
    }
    
    .forum-topic-detail .category-link {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        text-decoration: none;
        transition: color 0.2s;
        font-weight: 500;
    }
    
    .forum-topic-detail .category-link:hover {
        color: #2196F3;
    }
    
    .forum-topic-detail .category-count {
        background: #f1f1f1;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        color: #666;
    }
    
    /* Share Dialog */
    .forum-topic-detail .share-dialog {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .forum-topic-detail .share-dialog-content {
        background: white;
        border-radius: 8px;
        padding: 25px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    
    .forum-topic-detail .share-dialog-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .forum-topic-detail .share-dialog-title {
        font-size: 20px;
        margin: 0;
        color: #333;
    }
    
    .forum-topic-detail .close-dialog {
        background: none;
        border: none;
        font-size: 20px;
        color: #777;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .forum-topic-detail .close-dialog:hover {
        color: #333;
    }
    
    .forum-topic-detail .share-options {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .forum-topic-detail .share-option {
        flex: 1;
        min-width: 100px;
        background: #f5f5f5;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .share-option:hover {
        background: #e0e0e0;
        transform: translateY(-2px);
    }
    
    .forum-topic-detail .share-option i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }
    
    .forum-topic-detail .share-facebook i {
        color: #1877F2;
    }
    
    .forum-topic-detail .share-twitter i {
        color: #1DA1F2;
    }
    
    .forum-topic-detail .share-whatsapp i {
        color: #25D366;
    }
    
    .forum-topic-detail .share-telegram i {
        color: #0088cc;
    }
    
    .forum-topic-detail .share-link-section {
        margin-top: 20px;
    }
    
    .forum-topic-detail .share-link-section p {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 14px;
        color: #555;
    }
    
    .forum-topic-detail .share-link-input {
        display: flex;
        gap: 10px;
    }
    
    .forum-topic-detail .share-url {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        background: #f9f9f9;
    }
    
    .forum-topic-detail .copy-link {
        background: #2196F3;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .copy-link:hover {
        background: #1976D2;
    }
    
    .forum-topic-detail .copy-link.copied {
        background: #4CAF50;
    }
    
    /* Report Dialog */
    .forum-topic-detail .report-dialog {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .forum-topic-detail .report-dialog-content {
        background: white;
        border-radius: 8px;
        padding: 25px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    
    .forum-topic-detail .report-dialog-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .forum-topic-detail .report-dialog-title {
        font-size: 20px;
        margin: 0;
        color: #333;
    }
    
    .forum-topic-detail .report-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .forum-topic-detail .report-form .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .forum-topic-detail .report-form label {
        font-weight: 500;
        color: #333;
    }
    
    .forum-topic-detail .report-form select,
    .forum-topic-detail .report-form textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .forum-topic-detail .report-form select:focus,
    .forum-topic-detail .report-form textarea:focus {
        border-color: #2196F3;
        outline: none;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }
    
    .forum-topic-detail .report-form textarea {
        min-height: 120px;
        resize: vertical;
    }
    
    .forum-topic-detail .report-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .forum-topic-detail .cancel-report {
        background: #f5f5f5;
        color: #555;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .cancel-report:hover {
        background: #e0e0e0;
    }
    
    .forum-topic-detail .submit-report {
        background: #f44336;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .forum-topic-detail .submit-report:hover {
        background: #d32f2f;
    }
    
    .forum-topic-detail .success-message {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #4CAF50;
        font-weight: 500;
        padding: 20px 0;
        font-size: 16px;
    }
    
    .forum-topic-detail .success-message i {
        font-size: 24px;
    }
    
    .forum-topic-detail .comment.highlighted {
        background-color: #fffde7;
        transition: background-color 1s;
    }
    
    /* Responsive Styles */
    @media screen and (max-width: 992px) {
        .forum-topic-detail .content-wrapper {
            grid-template-columns: 1fr;
        }
        
        .forum-topic-detail .topic-sidebar {
            order: 1;
            margin-top: 20px;
        }
        
        .forum-topic-detail .action-widget {
            flex-direction: row;
            flex-wrap: wrap;
        }
        
        .forum-topic-detail .primary-button,
        .forum-topic-detail .secondary-button {
            flex: 1;
            min-width: 200px;
        }
    }
    
    @media screen and (max-width: 768px) {
        .forum-topic-detail .topic-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .forum-topic-detail .topic-footer {
            flex-direction: column;
        }
        
        .forum-topic-detail .comment-header {
            flex-direction: column;
            gap: 5px;
        }
        
        .forum-topic-detail .comment-content,
        .forum-topic-detail .comment-actions {
            padding-left: 0;
        }
        
        .forum-topic-detail .expert-answer-header {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
    }
    
    @media screen and (max-width: 576px) {
        .forum-topic-detail .topic-title {
            font-size: 22px;
        }
        
        .forum-topic-detail .topic-actions {
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .forum-topic-detail .action-button {
            flex: 1;
            justify-content: center;
        }
        
        .forum-topic-detail .breadcrumb {
            overflow-x: auto;
            white-space: nowrap;
            padding: 12px 15px;
        }
        
        .forum-topic-detail .submit-comment {
            width: 100%;
            justify-content: center;
        }
        
        .forum-topic-detail .share-options {
            grid-template-columns: 1fr 1fr;
        }
        
        .forum-topic-detail .share-link-input {
            flex-direction: column;
            gap: 10px;
        }
        
        .forum-topic-detail .copy-link {
            width: 100%;
        }
    }
    </style>
<script>
jQuery(document).ready(function($) {
    // Comment sorting functionality
    $('#comments-sort-select').on('change', function() {
        var sortOrder = $(this).val();
        var $commentsList = $('.comment-list');
        var $comments = $commentsList.find('> li.comment');
        
        if (sortOrder === 'newest') {
            $comments.sort(function(a, b) {
                return new Date($(b).find('time').attr('datetime')) - new Date($(a).find('time').attr('datetime'));
            });
        } else {
            $comments.sort(function(a, b) {
                return new Date($(a).find('time').attr('datetime')) - new Date($(b).find('time').attr('datetime'));
            });
        }
        
        $commentsList.append($comments);
    });
    
    // Scroll to comment form when reply button is clicked
    $('#main-reply-button').on('click', function() {
        var targetId = $(this).data('scroll-to');
        $('html, body').animate({
            scrollTop: $(targetId).offset().top - 50
        }, 500);
        $('#comment').focus();
    });
    
    // Share functionality
    $('#main-share-button').on('click', function(e) {
        e.preventDefault();
        $('#shareDialog').fadeIn(300).css('display', 'flex');
        $('body').css('overflow', 'hidden');
    });
    
    $('#closeShareDialog').on('click', function() {
        $('#shareDialog').fadeOut(300);
        $('body').css('overflow', '');
    });
    
    // Report functionality
    $('#main-report-button').on('click', function(e) {
        e.preventDefault();
        $('#reportDialog').fadeIn(300).css('display', 'flex');
        $('body').css('overflow', 'hidden');
    });
    
    $('#closeReportDialog, #cancelReport').on('click', function() {
        $('#reportDialog').fadeOut(300);
        $('body').css('overflow', '');
    });
    
    // Close dialogs when clicking outside
    $(document).on('click', function(e) {
        if ($(e.target).hasClass('share-dialog')) {
            $('#shareDialog').fadeOut(300);
            $('body').css('overflow', '');
        }
        if ($(e.target).hasClass('report-dialog')) {
            $('#reportDialog').fadeOut(300);
            $('body').css('overflow', '');
        }
    });
    
    // Copy link functionality
    $('#copyLink').on('click', function() {
        var $shareUrl = $('#shareUrl');
        $shareUrl.select();
        
        try {
            // Modern clipboard API
            if (navigator.clipboard) {
                navigator.clipboard.writeText($shareUrl.val()).then(function() {
                    showNotification('Link kopyalandı!', 'success');
                }).catch(function() {
                    // Fallback to execCommand
                    document.execCommand('copy');
                    showCopySuccess();
                });
            } else {
                // Fallback for older browsers
                document.execCommand('copy');
                showCopySuccess();
            }
        } catch (err) {
            alert('Kopyalama başarısız oldu: ' + err);
        }
    });
    
    // Helper function for copy success
    function showCopySuccess() {
        var originalText = $('#copyLink').text();
        $('#copyLink').text('Kopyalandı!').addClass('copied');
        
        setTimeout(function() {
            $('#copyLink').text(originalText).removeClass('copied');
        }, 2000);
    }
    
    // Social sharing links
    $('#shareFacebook').on('click', function() {
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(location.href), 'facebook-share', 'width=580,height=296');
    });
    
    $('#shareTwitter').on('click', function() {
        window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(document.title) + '&url=' + encodeURIComponent(location.href), 'twitter-share', 'width=580,height=296');
    });
    
    $('#shareWhatsapp').on('click', function() {
        window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(document.title + ' ' + location.href));
    });
    
    $('#shareTelegram').on('click', function() {
        window.open('https://t.me/share/url?url=' + encodeURIComponent(location.href) + '&text=' + encodeURIComponent(document.title));
    });
    
    // Submit report form
    $('#reportForm').on('submit', function(e) {
        e.preventDefault();
        
        var reason = $('#report-reason').val();
        var description = $('#report-description').val();
        
        if (!reason) {
            alert('Lütfen bir bildirim nedeni seçin.');
            return;
        }
        
        // AJAX implementation for report submission
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'submit_forum_report',
                post_id: <?php echo get_the_ID(); ?>,
                reason: reason,
                description: description,
                security: '<?php echo wp_create_nonce('forum_report_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $(e.target).html('<div class="success-message"><i class="fas fa-check-circle"></i> Bildiriminiz alınmıştır. Teşekkür ederiz!</div>');
                    
                    setTimeout(function() {
                        $('#reportDialog').fadeOut(300);
                        $('body').css('overflow', '');
                        
                        // Reset form after closing
                        setTimeout(function() {
                            resetReportForm();
                        }, 500);
                    }, 2000);
                } else {
                    alert('Bildirim gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
                }
            },
            error: function() {
                alert('Bildirim gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
            }
        });
    });
    
    // Helper function to reset report form
    function resetReportForm() {
        $('#reportForm').html(`
            <div class="form-group">
                <label for="report-reason">Bildirim Nedeni</label>
                <select id="report-reason" required>
                    <option value="">Lütfen bir neden seçin</option>
                    <option value="inappropriate">Uygunsuz İçerik</option>
                    <option value="spam">Spam</option>
                    <option value="duplicate">Tekrarlanan Konu</option>
                    <option value="wrong-category">Yanlış Kategori</option>
                    <option value="other">Diğer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="report-description">Açıklama</label>
                <textarea id="report-description" placeholder="Lütfen bildirimin detaylarını açıklayın..."></textarea>
            </div>
            <div class="report-actions">
                <button type="button" class="cancel-report" id="cancelReport">İptal</button>
                <button type="submit" class="submit-report">Bildir</button>
            </div>
        `);
        
        // Re-attach cancel button event
        $('#cancelReport').on('click', function() {
            $('#reportDialog').fadeOut(300);
            $('body').css('overflow', '');
        });
    }
    
    // Notification toast system
    function showNotification(message, type) {
        // Remove any existing notifications
        $('.notification-toast').remove();
        
        // Get icon based on type
        var icon = '';
        switch(type) {
            case 'success':
                icon = '<i class="fas fa-check-circle"></i>';
                break;
            case 'error':
                icon = '<i class="fas fa-exclamation-circle"></i>';
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-triangle"></i>';
                break;
            default:
                icon = '<i class="fas fa-info-circle"></i>';
        }
        
        // Create notification element
        var $notification = $(`
            <div class="notification-toast notification-${type}">
                <div class="notification-icon">${icon}</div>
                <div class="notification-message">${message}</div>
                <button class="notification-close">&times;</button>
            </div>
        `);
        
        // Add to document
        $('body').append($notification);
        
        // Show with animation
        setTimeout(() => $notification.addClass("show"), 100);
        
        // Auto-hide after timeout
        var hideTimeout = setTimeout(function() {
            $notification.removeClass("show");
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 5000);
        
        // Close button handler
        $notification.find(".notification-close").on("click", function() {
            clearTimeout(hideTimeout);
            $notification.removeClass("show");
            setTimeout(function() {
                $notification.remove();
            }, 300);
        });
    }
    
    // Comment highlighting when linking to a specific comment
    if (window.location.hash && window.location.hash.indexOf('#comment-') === 0) {
        var $targetComment = $(window.location.hash);
        if ($targetComment.length) {
            $targetComment.addClass('highlighted');
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $targetComment.offset().top - 50
                }, 500);
            }, 500);
        }
    }
    
    // Load more comments functionality
    $('.load-more-comments').on('click', function() {
        var $button = $(this);
        var offset = parseInt($button.data('offset'));
        var postId = <?php echo get_the_ID(); ?>;
        
        $button.text('Yükleniyor...').prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'load_more_comments',
                post_id: postId,
                offset: offset,
                nonce: '<?php echo wp_create_nonce('comment_nonce'); ?>'
            },
            success: function(response) {
                if (response) {
                    $('.comment-list').append(response);
                    $button.data('offset', offset + 10);
                    $button.text('Daha Fazla Yorum Yükle').prop('disabled', false);
                } else {
                    $button.text('Tüm yorumlar yüklendi').prop('disabled', true);
                    setTimeout(function() {
                        $button.hide();
                    }, 2000);
                }
            },
            error: function() {
                $button.text('Yüklenirken hata oluştu').prop('disabled', false);
                setTimeout(function() {
                    $button.text('Daha Fazla Yorum Yükle');
                }, 2000);
            }
        });
    });
    
    // Ajax comment submission
    $('#commentform').on('submit', function(e) {
        // Only intercept if ajax commenting is enabled
        if (!window.ajaxCommenting) {
            return true; // Use default submission
        }
        
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalBtnText = $submitBtn.html();
        
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...').prop('disabled', true);
        
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize() + '&ajax_commenting=1',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showNotification('Yorumunuz başarıyla gönderildi!', 'success');
                    
                    // Clear the form
                    $('#comment').val('');
                    
                    // If comment moderation is enabled
                    if (response.moderation) {
                        showNotification('Yorumunuz onay bekliyor.', 'info');
                    } else {
                        // Add the new comment to the list
                        if (response.comment_html) {
                            if ($('.no-comments').length) {
                                $('.no-comments').remove();
                                $('.comments-section').append('<div class="comments-list"><ul class="comment-list"></ul></div>');
                            }
                            
                            $('.comment-list').append(response.comment_html);
                            
                            // Update comment count
                            var commentCount = parseInt($('.comments-title').text().match(/\d+/)[0]) + 1;
                            $('.comments-title').html('<i class="fas fa-comments"></i> Yanıtlar (' + commentCount + ')');
                            
                            // Scroll to the new comment
                            $('html, body').animate({
                                scrollTop: $(response.comment_id).offset().top - 50
                            }, 500);
                            
                            // Highlight the new comment
                            $(response.comment_id).addClass('highlighted');
                        }
                    }
                } else {
                    // Show error message
                    showNotification(response.message || 'Yorum gönderilirken bir hata oluştu.', 'error');
                }
            },
            error: function() {
                showNotification('Yorum gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.', 'error');
            },
            complete: function() {
                $submitBtn.html(originalBtnText).prop('disabled', false);
            }
        });
    });
    
    // Helper function for live timestamp updates
    function updateTimestamps() {
        $('.comment-metadata time, .topic-date').each(function() {
            var timestamp = $(this).attr('datetime');
            if (timestamp) {
                var date = new Date(timestamp);
                var now = new Date();
                var diff = Math.floor((now - date) / 1000); // difference in seconds
                
                var timeAgo = '';
                if (diff < 60) {
                    timeAgo = 'az önce';
                } else if (diff < 3600) {
                    var minutes = Math.floor(diff / 60);
                    timeAgo = minutes + ' dakika önce';
                } else if (diff < 86400) {
                    var hours = Math.floor(diff / 3600);
                    timeAgo = hours + ' saat önce';
                } else if (diff < 604800) {
                    var days = Math.floor(diff / 86400);
                    timeAgo = days + ' gün önce';
                } else if (diff < 2592000) {
                    var weeks = Math.floor(diff / 604800);
                    timeAgo = weeks + ' hafta önce';
                } else if (diff < 31536000) {
                    var months = Math.floor(diff / 2592000);
                    timeAgo = months + ' ay önce';
                } else {
                    var years = Math.floor(diff / 31536000);
                    timeAgo = years + ' yıl önce';
                }
                
                $(this).text(timeAgo);
            }
        });
    }
    
    // Update timestamps every minute
    setInterval(updateTimestamps, 60000);
    updateTimestamps(); // Initial update
    
    // AJAX live search for forum
    var searchTimer;
    $('#forum-search-input').on('keyup', function() {
        var searchTerm = $(this).val().trim();
        
        clearTimeout(searchTimer);
        
        if (searchTerm.length >= 2) {
            $('#forum-search-results').html('<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Aranıyor...</div>').show();
            
            searchTimer = setTimeout(function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'live_search_topics',
                        search_term: searchTerm,
                        security: '<?php echo wp_create_nonce('search_nonce'); ?>'
                    },
                    success: function(response) {
                        $('#forum-search-results').html(response);
                    },
                    error: function() {
                        $('#forum-search-results').html('<div class="search-error">Bir hata oluştu. Lütfen daha sonra tekrar deneyin.</div>');
                    }
                });
            }, 500);
        } else {
            $('#forum-search-results').hide();
        }
    });
    
    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.forum-search-container').length) {
            $('#forum-search-results').hide();
        }
    });
    
    // Prevent form submission on enter in search field
    $('#forum-search-form').on('submit', function(e) {
        var searchTerm = $('#forum-search-input').val().trim();
        if (searchTerm.length < 2) {
            e.preventDefault();
        }
    });
});
</script>
<!-- JavaScript for Share and Report dialogs is included in the footer -->
<?php get_footer(); ?>