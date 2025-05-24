<?php
/*
Template Name: Konu Başlat
*/

// Security check
if (!defined('ABSPATH')) {
    exit; // Direct access not allowed
}

/**
 * Handle topic submission
 * 
 * @return array|null Result of form submission
 */
function handle_topic_submission() {
    // Only process POST requests with valid nonce
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
        !isset($_POST['submit_topic']) || 
        !isset($_POST['topic_nonce']) || 
        !wp_verify_nonce($_POST['topic_nonce'], 'submit_topic_action')) {
        return null;
    }

    try {
        // Captcha validation
        $captcha_type = get_option('forum_captcha_type', 'math');
        
        if ($captcha_type === 'math') {
            // Math captcha validation
            $num1 = isset($_POST['num1']) ? intval($_POST['num1']) : 0;
            $num2 = isset($_POST['num2']) ? intval($_POST['num2']) : 0;
            $correct_answer = $num1 + $num2;
            $user_answer = isset($_POST['math_answer']) ? intval($_POST['math_answer']) : 0;
            
            if ($user_answer !== $correct_answer) {
                throw new Exception('Lütfen matematik sorusunu doğru cevaplayın.');
            }
        }

        // Get current user info if logged in
        $current_user = wp_get_current_user();
        $is_logged_in = is_user_logged_in();

        // Get and sanitize form data
        $topic_title = isset($_POST['topic_title']) ? sanitize_text_field($_POST['topic_title']) : '';
        $topic_content = isset($_POST['topic_content']) ? wp_kses_post($_POST['topic_content']) : '';
        $topic_category = isset($_POST['topic_category']) ? intval($_POST['topic_category']) : 0;
        $topic_tags = isset($_POST['topic_tags']) ? sanitize_text_field($_POST['topic_tags']) : '';
        
        // If user is logged in, use their info, otherwise get from form
        if ($is_logged_in) {
            $user_email = $current_user->user_email;
            $user_name = $current_user->display_name;
        } else {
            $user_email = isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '';
            $user_name = isset($_POST['user_name']) ? sanitize_text_field($_POST['user_name']) : '';
            
            // Required fields validation for guest users
            if (empty($user_email) || empty($user_name)) {
                throw new Exception('Lütfen ad, soyad ve e-posta bilgilerinizi girin.');
            }
            
            // Email validation
            if (!is_email($user_email)) {
                throw new Exception('Lütfen geçerli bir e-posta adresi girin.');
            }
        }

        // Required fields validation
        if (empty($topic_title) || empty($topic_content) || empty($topic_category)) {
            throw new Exception('Lütfen tüm zorunlu alanları doldurun.');
        }

        // Title length check
        if (mb_strlen($topic_title) < 5) {
            throw new Exception('Konu başlığı en az 5 karakter olmalıdır.');
        }

        // Content length check
        if (mb_strlen($topic_content) < 20) {
            throw new Exception('Konu içeriği en az 20 karakter olmalıdır.');
        }

        // Create SEO-friendly slug
        $topic_slug = sanitize_title($topic_title);

        // Prepare topic data
        $post_data = array(
            'post_title'   => $topic_title,
            'post_content' => $topic_content,
            'post_status'  => get_option('forum_auto_approve', 'no') === 'yes' ? 'publish' : 'pending',
            'post_type'    => 'forum_topics',
            'post_name'    => $topic_slug,
            'meta_input'   => array(
                'user_email'         => $user_email,
                'user_name'          => $user_name,
                'post_views_count'   => 0,
                'forum_topic_tags'   => $topic_tags,
                'submission_ip'      => $_SERVER['REMOTE_ADDR'],
                'submission_date'    => current_time('mysql'),
                'last_activity_date' => current_time('mysql'),
            ),
        );

        // Check if user is logged in, assign authorship
        if ($is_logged_in) {
            $post_data['post_author'] = $current_user->ID;
        }

        // Clear cache
        wp_cache_delete('latest_topics_cache');
        wp_cache_delete('popular_topics_cache');
        wp_cache_delete('latest_topics_widget');
        wp_cache_delete('popular_topics_widget');

        // Insert post into database
        $post_id = wp_insert_post($post_data, true);
        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }

        // Assign category
        wp_set_object_terms($post_id, $topic_category, 'topic_category');
        
        // Process tags if they exist
        if (!empty($topic_tags)) {
            $tags_array = array_map('trim', explode(',', $topic_tags));
            wp_set_object_terms($post_id, $tags_array, 'topic_tag');
        }

        // Store user data in cookie for future use (only for guest users)
        if (!$is_logged_in) {
            $cookie_expiration = time() + (30 * DAY_IN_SECONDS);
            setcookie('forum_user_name', $user_name, $cookie_expiration, COOKIEPATH, COOKIE_DOMAIN);
            setcookie('forum_user_email', $user_email, $cookie_expiration, COOKIEPATH, COOKIE_DOMAIN);
        }

        // Send notification to admin
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $admin_subject = sprintf('[%s] Yeni Forum Konusu: %s', $site_name, $topic_title);
        $admin_message = sprintf(
            "Merhaba,\n\nYeni bir forum konusu oluşturuldu.\n\nBaşlık: %s\nYazar: %s\nE-posta: %s\n\nKonu içeriğini incelemek için: %s\n\n",
            $topic_title,
            $user_name,
            $user_email,
            admin_url('post.php?post=' . $post_id . '&action=edit')
        );
        wp_mail($admin_email, $admin_subject, $admin_message);

        // Send confirmation to user
        if (get_option('forum_send_confirmation', 'yes') === 'yes') {
            $user_subject = sprintf('[%s] Konunuz alındı: %s', $site_name, $topic_title);
            $user_message = sprintf(
                "Sayın %s,\n\n'%s' başlıklı konunuz başarıyla alınmıştır.\n\n%s\n\nKonunuz onaylandıktan sonra yayınlanacaktır. Bu işlem genellikle 24 saat içinde tamamlanır.\n\nTeşekkürler,\n%s Ekibi",
                $user_name,
                $topic_title,
                (get_option('forum_auto_approve', 'no') === 'yes' ? 'Konunuzu görüntülemek için: ' . get_permalink($post_id) : 'Konunuz şu anda inceleniyor ve onaylandıktan sonra yayınlanacak.'),
                $site_name
            );
            wp_mail($user_email, $user_subject, $user_message);
        }

        $status_message = get_option('forum_auto_approve', 'no') === 'yes' 
            ? 'Konunuz başarıyla oluşturuldu ve yayınlandı.' 
            : 'Konunuz başarıyla gönderildi ve onay bekliyor.';

        return array(
            'status'  => 'success',
            'message' => $status_message,
            'post_id' => $post_id
        );
    } catch (Exception $e) {
        return array(
            'status'  => 'error',
            'message' => $e->getMessage()
        );
    }
}

// Process form submission
$form_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_topic'])) {
    $form_result = handle_topic_submission();
}

// Generate math captcha
$captcha_type = get_option('forum_captcha_type', 'math');
$num1 = wp_rand(1, 10);
$num2 = wp_rand(1, 10);
$correct_math_answer = $num1 + $num2;

// Get saved user data from cookies (for guest users)
$cookie_name = isset($_COOKIE['forum_user_name']) ? $_COOKIE['forum_user_name'] : '';
$cookie_email = isset($_COOKIE['forum_user_email']) ? $_COOKIE['forum_user_email'] : '';

// Check if user is logged in
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();

// Load header
get_header();
?>

<div class="new-topic-page">
    <!-- Page Header Banner -->
    <div class="page-banner">
        <div class="container">
            <h1><i class="fas fa-edit"></i> Yeni Konu Başlat</h1>
            <p>Topluluğumuzda yeni bir tartışma başlatın ve uzmanlardan yanıt alın</p>
            
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <ul>
                    <li><a href="<?php echo esc_url(home_url()); ?>">Ana Sayfa</a></li>
                    <li><a href="<?php echo esc_url(home_url('/forum/')); ?>">Forum</a></li>
                    <li>Yeni Konu</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="content-wrapper">
            <!-- Main Content - Topic Form -->
            <main class="main-content">
                <!-- Tips Section -->
                <div class="topic-tips">
                    <h3><i class="fas fa-lightbulb"></i> İyi Bir Konu Nasıl Oluşturulur?</h3>
                    <ul>
                        <li><strong>Açık ve net bir başlık seçin</strong> - Konunun özünü yansıtan kısa ve açıklayıcı bir başlık yazın.</li>
                        <li><strong>Detaylı açıklama ekleyin</strong> - Sorunuzu veya konunuzu mümkün olduğunca detaylı anlatın.</li>
                        <li><strong>Doğru kategori seçin</strong> - Konunuz için en uygun kategoriyi belirleyin.</li>
                        <li><strong>Saygılı olun</strong> - Topluluk kurallarına uygun bir dil kullanmaya özen gösterin.</li>
                    </ul>
                </div>

                <div class="topic-form-box">
                    <?php if ($form_result): ?>
                        <div class="alert alert-<?php echo esc_attr($form_result['status']); ?>">
                            <i class="fas fa-<?php echo $form_result['status'] === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
                            <?php echo esc_html($form_result['message']); ?>
                            
                            <?php if ($form_result['status'] === 'success' && isset($form_result['post_id']) && get_option('forum_auto_approve', 'no') === 'yes'): ?>
                                <p class="mt-10">
                                    <a href="<?php echo esc_url(get_permalink($form_result['post_id'])); ?>" class="button-link">
                                        <i class="fas fa-external-link-alt"></i> Konuyu Görüntüle
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="topic-form" id="new-topic-form">
                        <?php wp_nonce_field('submit_topic_action', 'topic_nonce'); ?>

                        <?php if (!$is_logged_in): // Kişisel bilgiler sadece giriş yapmayan kullanıcılara gösterilir ?>
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-user"></i> Kişisel Bilgiler</h3>
                            
                            <div class="form-row form-row-half">
                                <label for="user_name">
                                    Ad Soyad <span class="required">*</span>
                                </label>
                                <input type="text" id="user_name" name="user_name" required 
                                       value="<?php echo isset($_POST['user_name']) ? esc_attr($_POST['user_name']) : esc_attr($cookie_name); ?>"
                                       placeholder="Adınız ve soyadınız">
                                <span class="form-tip">Gerçek adınızı kullanın</span>
                            </div>

                            <div class="form-row form-row-half">
                                <label for="user_email">
                                    E-posta <span class="required">*</span>
                                </label>
                                <input type="email" id="user_email" name="user_email" required
                                       value="<?php echo isset($_POST['user_email']) ? esc_attr($_POST['user_email']) : esc_attr($cookie_email); ?>"
                                       placeholder="ornek@email.com">
                                <span class="form-tip">E-postanız gizli tutulacaktır</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-clipboard-list"></i> Konu Detayları</h3>
                            
                            <div class="form-row">
                                <label for="topic_category">
                                    Kategori <span class="required">*</span>
                                </label>
                                <select id="topic_category" name="topic_category" required>
                                    <option value="">Kategori Seçin</option>
                                    <?php
                                    $categories = get_terms(array(
                                        'taxonomy'   => 'topic_category',
                                        'hide_empty' => false,
                                        'orderby'    => 'name',
                                        'order'      => 'ASC'
                                    ));
                                    
                                    if (!is_wp_error($categories)) {
                                        foreach ($categories as $category) {
                                            $selected = isset($_POST['topic_category']) && $_POST['topic_category'] == $category->term_id ? 'selected' : '';
                                            $topic_count = $category->count > 0 ? ' (' . $category->count . ')' : '';
                                            echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' 
                                                 . esc_html($category->name) . $topic_count . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="form-tip">Konunuzla en çok ilgili kategoriyi seçin</span>
                            </div>

                            <div class="form-row">
                                <label for="topic_title">
                                    Konu Başlığı <span class="required">*</span>
                                </label>
                                <input type="text" id="topic_title" name="topic_title" required
                                       value="<?php echo isset($_POST['topic_title']) ? esc_attr($_POST['topic_title']) : ''; ?>"
                                       placeholder="Konunuzu en iyi şekilde özetleyen kısa bir başlık">
                                <div class="title-character-count">
                                    <span id="title-char-count">0</span> / 100 karakter
                                </div>
                                <span class="form-tip">Açıklayıcı ve kısa bir başlık kullanın</span>
                            </div>

                            <div class="form-row">
                                <label for="topic_content">
                                    Konu İçeriği <span class="required">*</span>
                                </label>
                                <div class="editor-toolbar">
                                    <button type="button" class="toolbar-button" data-format="bold"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="toolbar-button" data-format="italic"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="toolbar-button" data-format="link"><i class="fas fa-link"></i></button>
                                    <button type="button" class="toolbar-button" data-format="list-ul"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="toolbar-button" data-format="list-ol"><i class="fas fa-list-ol"></i></button>
                                </div>
                                <textarea id="topic_content" name="topic_content" rows="10" required
                                          placeholder="Konunuzu detaylı bir şekilde açıklayın..."><?php 
                                    echo isset($_POST['topic_content']) ? esc_textarea($_POST['topic_content']) : ''; 
                                ?></textarea>
                                <div class="content-character-count">
                                    <span id="content-char-count">0</span> / 5000 karakter
                                </div>
                                <span class="form-tip">Konunuzu mümkün olduğunca detaylı açıklayın</span>
                            </div>

                            <div class="form-row">
                                <label for="topic_tags">
                                    Etiketler
                                </label>
                                <input type="text" id="topic_tags" name="topic_tags"
                                       value="<?php echo isset($_POST['topic_tags']) ? esc_attr($_POST['topic_tags']) : ''; ?>"
                                       placeholder="örnek, yardım, soru">
                                <span class="form-tip">Etiketleri virgülle ayırın (isteğe bağlı)</span>
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="form-section">
                            <h3 class="section-title"><i class="fas fa-shield-alt"></i> Güvenlik</h3>
                            
                            <!-- Math Captcha -->
                            <div class="security-question">
                                <label>
                                    Güvenlik Sorusu <span class="required">*</span>
                                </label>
                                <div class="question-wrapper">
                                    <div class="math-question">
                                        <?php echo esc_html($num1); ?> + <?php echo esc_html($num2); ?> = ?
                                    </div>
                                    <input type="number" name="math_answer" required>
                                    <!-- Hidden fields to preserve math values -->
                                    <input type="hidden" name="num1" value="<?php echo esc_attr($num1); ?>">
                                    <input type="hidden" name="num2" value="<?php echo esc_attr($num2); ?>">
                                </div>
                                <span class="form-tip">Bu bot önleme önlemidir</span>
                            </div>

                            <!-- Terms Acceptance -->
                            <div class="form-row checkbox-row">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="terms_accepted" required>
                                    <span>Forum <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" target="_blank">kurallarını</a> okudum ve kabul ediyorum</span>
                                </label>
                            </div>
                        </div>

                        <?php if ($is_logged_in): // Giriş yapmış kullanıcılara gösterilecek bilgi ?>
                        <div class="user-info-box">
                            <p class="user-info-message">
                                <i class="fas fa-info-circle"></i>
                                <strong><?php echo esc_html($current_user->display_name); ?></strong> olarak konu oluşturuyorsunuz.
                                E-posta adresiniz: <strong><?php echo esc_html($current_user->user_email); ?></strong>
                            </p>
                        </div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <a href="<?php echo esc_url(home_url('/forum/')); ?>" class="button-secondary">
                                <i class="fas fa-arrow-left"></i> Vazgeç
                            </a>
                            <button type="submit" name="submit_topic" class="submit-btn">
                                <i class="fas fa-paper-plane"></i> Konuyu Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </main>

            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- User Activity Widget -->
                <?php if ($is_logged_in): ?>
                <div class="widget">
                    <h3 class="widget-title">
                        <i class="fas fa-user-circle"></i> Hoş Geldiniz
                    </h3>
                    <div class="user-profile-mini">
                        <?php echo get_avatar($current_user->ID, 64); ?>
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
                </div>
                <?php else: ?>
                <!-- Login CTA for Guest Users -->
                <div class="widget">
                    <h3 class="widget-title">
                        <i class="fas fa-sign-in-alt"></i> Üye Girişi
                    </h3>
                    <div class="widget-content">
                        <p>Giriş yaparak forumda daha fazla özellikten yararlanabilirsiniz.</p>
                        <div class="login-buttons">
                            <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="login-button">
                                <i class="fas fa-sign-in-alt"></i> Giriş Yap
                            </a>
                            <a href="<?php echo esc_url(wp_registration_url()); ?>" class="register-button">
                                <i class="fas fa-user-plus"></i> Üye Ol
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Forum Guidelines -->
                <div class="widget">
                    <h3 class="widget-title">
                        <i class="fas fa-info-circle"></i> Forum Kuralları
                    </h3>
                    <div class="widget-content">
                        <ul class="guidelines-list">
                            <li><i class="fas fa-check"></i> Nazik ve saygılı olun</li>
                            <li><i class="fas fa-check"></i> Konuyla ilgili sorular sorun</li>
                            <li><i class="fas fa-check"></i> Başlıkta konuyu özetleyin</li>
                            <li><i class="fas fa-check"></i> Konuyu doğru kategoriye ekleyin</li>
                            <li><i class="fas fa-times"></i> Kişisel bilgilerinizi paylaşmayın</li>
                            <li><i class="fas fa-times"></i> Küfür ve hakaret içeren paylaşım yapmayın</li>
                        </ul>
                    </div>
                </div>

                <!-- Popular Topics Widget -->
                <div class="widget">
                    <h3 class="widget-title">
                        <i class="fas fa-fire"></i> Popüler Konular
                    </h3>
                    <div class="widget-content">
                        <?php
                        $cache_key = 'popular_topics_widget';
                        $popular_topics = wp_cache_get($cache_key);
                        
                        if (false === $popular_topics) {
                            $popular_topics = new WP_Query(array(
                                'post_type'              => 'forum_topics',
                                'posts_per_page'         => 5,
                                'meta_key'               => 'post_views_count',
                                'orderby'                => 'meta_value_num',
                                'order'                  => 'DESC',
                                'post_status'            => 'publish',
                                'no_found_rows'          => true,
                                'update_post_meta_cache' => false,
                                'update_post_term_cache' => false,
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
                </div>

                <!-- Categories Widget -->
                <div class="widget">
                    <h3 class="widget-title">
                        <i class="fas fa-folder"></i> Kategoriler
                    </h3>
                    <div class="widget-content">
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
                                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-link">
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
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
/* General Styles */
.new-topic-page {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding-bottom: 40px;
    color: #333;
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
}

/* Page Banner Styles */
.new-topic-page .page-banner {
    background: linear-gradient(135deg, #3f51b5, #2196F3);
    padding: 40px 0;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: relative;
}

.new-topic-page .page-banner h1 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
}

.new-topic-page .page-banner p {
    margin: 0;
    font-size: 16px;
    opacity: 0.9;
}

/* Breadcrumb */
.new-topic-page .breadcrumb {
    margin-top: 15px;
    background: rgba(255,255,255,0.1);
    display: inline-block;
    padding: 8px 15px;
    border-radius: 20px;
}

.new-topic-page .breadcrumb ul {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.new-topic-page .breadcrumb li {
    display: flex;
    align-items: center;
}

.new-topic-page .breadcrumb li:not(:last-child):after {
    content: '›';
    margin-left: 8px;
    color: rgba(255,255,255,0.7);
}

.new-topic-page .breadcrumb a {
    color: white;
    text-decoration: none;
    transition: opacity 0.2s;
}

.new-topic-page .breadcrumb a:hover {
    opacity: 0.8;
}

/* Layout Styles */
.new-topic-page .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

.new-topic-page .content-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

/* Tips Section */
.new-topic-page .topic-tips {
    background: #fff;
    border-left: 4px solid #2196F3;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}

.new-topic-page .topic-tips h3 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 18px;
    color: #2196F3;
    display: flex;
    align-items: center;
    gap: 8px;
}

.new-topic-page .topic-tips ul {
    margin: 0;
    padding-left: 20px;
}

.new-topic-page .topic-tips li {
    margin-bottom: 8px;
    color: #555;
}

.new-topic-page .topic-tips li strong {
    color: #333;
}

/* Form Box Styles */
.new-topic-page .topic-form-box {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/* Form Sections */
.new-topic-page .form-section {
    border-bottom: 1px solid #eee;
    padding-bottom: 25px;
    margin-bottom: 25px;
}

.new-topic-page .form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.new-topic-page .section-title {
    font-size: 18px;
    margin: 0 0 20px 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Form Row Styles */
.new-topic-page .form-row {
    margin-bottom: 20px;
    position: relative;
}

.new-topic-page .form-row:last-child {
    margin-bottom: 0;
}

.new-topic-page .form-row-half {
    width: calc(50% - 10px);
    display: inline-block;
    vertical-align: top;
}

.new-topic-page .form-row-half:nth-child(odd) {
    margin-right: 20px;
}

.new-topic-page .form-row label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.new-topic-page .form-row .required {
    color: #f44336;
    margin-left: 4px;
}

/* Form Input Styles */
.new-topic-page .form-row input,
.new-topic-page .form-row textarea,
.new-topic-page .form-row select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    transition: all 0.3s ease;
    color: #333;
    background-color: #fff;
}

.new-topic-page .form-row input:focus,
.new-topic-page .form-row textarea:focus,
.new-topic-page .form-row select:focus {
    border-color: #2196F3;
    outline: none;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.new-topic-page .form-row input::placeholder,
.new-topic-page .form-row textarea::placeholder {
    color: #999;
}

/* Form Tip Style */
.new-topic-page .form-tip {
    font-size: 13px;
    color: #777;
    margin-top: 6px;
    display: block;
}

/* Textarea Editor Styles */
.new-topic-page .editor-toolbar {
    display: flex;
    gap: 5px;
    margin-bottom: 10px;
    background: #f7f7f7;
    padding: 8px;
    border-radius: 6px 6px 0 0;
    border: 1px solid #ddd;
    border-bottom: none;
}

.new-topic-page .toolbar-button {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.new-topic-page .toolbar-button:hover {
    background: #f1f1f1;
}

.new-topic-page .toolbar-button:active {
    background: #e9e9e9;
    transform: translateY(1px);
}

.new-topic-page .form-row textarea {
    min-height: 220px;
    resize: vertical;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}

/* Character Counter Styles */
.new-topic-page .title-character-count,
.new-topic-page .content-character-count {
    position: absolute;
    right: 10px;
    bottom: 10px;
    font-size: 12px;
    color: #777;
    background: rgba(255,255,255,0.8);
    padding: 2px 8px;
    border-radius: 10px;
}

.new-topic-page .title-character-count.exceeding,
.new-topic-page .content-character-count.exceeding {
    color: #f44336;
}

/* Security Question Styles */
.new-topic-page .security-question {
    background: #f5f7fa;
    padding: 15px;
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.new-topic-page .question-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 10px;
}

.new-topic-page .math-question {
    font-size: 18px;
    font-weight: 500;
    background: #e9f0f7;
    padding: 10px 20px;
    border-radius: 4px;
    min-width: 120px;
    text-align: center;
}

.new-topic-page .security-question input {
    width: 80px !important;
    text-align: center;
    font-size: 18px !important;
}

/* Checkbox Styles */
.new-topic-page .checkbox-row {
    margin-top: 20px;
}

.new-topic-page .checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.new-topic-page .checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.new-topic-page .checkbox-label a {
    color: #2196F3;
    text-decoration: none;
}

.new-topic-page .checkbox-label a:hover {
    text-decoration: underline;
}

/* User Info Box */
.new-topic-page .user-info-box {
    background: #e8f5e9;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 25px;
}

.new-topic-page .user-info-message {
    margin: 0;
    color: #2e7d32;
    display: flex;
    align-items: center;
    gap: 10px;
}

.new-topic-page .user-info-message i {
    font-size: 20px;
}

/* Submit Button Styles */
.new-topic-page .form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
}

.new-topic-page .button-secondary {
    background: #f5f5f5;
    color: #555;
    padding: 12px 20px;
    border-radius: 6px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: all 0.3s;
    border: 1px solid #ddd;
}

.new-topic-page .button-secondary:hover {
    background: #eee;
}

.new-topic-page .submit-btn {
    background: #2196F3;
    color: white;
    padding: 14px 28px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.new-topic-page .submit-btn:hover {
    background: #1976D2;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.new-topic-page .submit-btn:active {
    transform: translateY(0);
}

/* Alert Styles */
.new-topic-page .alert {
    padding: 18px;
    border-radius: 6px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.new-topic-page .alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border-left: 4px solid #4CAF50;
}

.new-topic-page .alert-error {
    background: #ffebee;
    color: #c62828;
    border-left: 4px solid #f44336;
}

.new-topic-page .alert i {
    font-size: 20px;
}

.new-topic-page .alert .button-link {
    margin-top: 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.7);
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    color: inherit;
    font-weight: 500;
    transition: all 0.2s;
}

.new-topic-page .alert .button-link:hover {
    background: rgba(255,255,255,0.9);
}

/* Sidebar Styles */
.new-topic-page .sidebar {
    position: sticky;
    top: 20px;
}

.new-topic-page .widget {
    background: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}

.new-topic-page .widget:last-child {
    margin-bottom: 0;
}

.new-topic-page .widget-title {
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
.new-topic-page .user-profile-mini {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.new-topic-page .user-profile-mini img {
    border-radius: 50%;
}

.new-topic-page .user-info {
    flex: 1;
}

.new-topic-page .user-name {
    font-weight: 600;
    margin: 0 0 5px 0;
    color: #333;
}

.new-topic-page .user-role {
    margin: 0;
    color: #777;
    font-size: 14px;
}

.new-topic-page .user-stats {
    display: flex;
    text-align: center;
    background: #f8f9fa;
    border-radius: 6px;
    overflow: hidden;
}

.new-topic-page .stat-item {
    flex: 1;
    padding: 15px 10px;
    display: flex;
    flex-direction: column;
}

.new-topic-page .stat-item:first-child {
    border-right: 1px solid #eee;
}

.new-topic-page .stat-value {
    font-size: 20px;
    font-weight: 600;
    color: #2196F3;
}

.new-topic-page .stat-label {
    font-size: 14px;
    color: #666;
}

/* Login CTA */
.new-topic-page .login-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 15px;
}

.new-topic-page .login-button,
.new-topic-page .register-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
}

.new-topic-page .login-button {
    background: #2196F3;
    color: white;
}

.new-topic-page .login-button:hover {
    background: #1976D2;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.new-topic-page .register-button {
    background: #f5f5f5;
    color: #555;
    border: 1px solid #ddd;
}

.new-topic-page .register-button:hover {
    background: #eee;
}

/* Guidelines List */
.new-topic-page .guidelines-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.new-topic-page .guidelines-list li {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.new-topic-page .guidelines-list li i {
    width: 20px;
    text-align: center;
}

.new-topic-page .guidelines-list li i.fa-check {
    color: #4CAF50;
}

.new-topic-page .guidelines-list li i.fa-times {
    color: #f44336;
}

/* Topic List */
.new-topic-page .topic-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.new-topic-page .topic-item {
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.new-topic-page .topic-item:last-child {
    border-bottom: none;
}

.new-topic-page .topic-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    text-decoration: none;
    margin-bottom: 5px;
    font-weight: 500;
    transition: color 0.2s;
}

.new-topic-page .topic-link:hover {
    color: #2196F3;
}

.new-topic-page .topic-meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #777;
    margin-left: 28px;
}

.new-topic-page .expert-icon {
    color: #4CAF50;
}

/* Category List */
.new-topic-page .category-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.new-topic-page .category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.new-topic-page .category-item:last-child {
    border-bottom: none;
}

.new-topic-page .category-link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    text-decoration: none;
    transition: color 0.2s;
}

.new-topic-page .category-link:hover {
    color: #2196F3;
}

.new-topic-page .category-count {
    background: #f1f1f1;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 12px;
    color: #666;
}

/* Responsive Design */
@media (max-width: 992px) {
    .new-topic-page .content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .new-topic-page .sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .new-topic-page .form-row-half {
        width: 100%;
        display: block;
    }
    
    .new-topic-page .form-row-half:nth-child(odd) {
        margin-right: 0;
    }
    
    .new-topic-page .question-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .new-topic-page .security-question input {
        width: 100% !important;
    }
    
    .new-topic-page .form-actions {
        flex-direction: column;
        gap: 15px;
    }
    
    .new-topic-page .button-secondary,
    .new-topic-page .submit-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .new-topic-page .page-banner {
        padding: 30px 0;
    }
    
    .new-topic-page .topic-form-box {
        padding: 20px;
    }
    
    .new-topic-page .widget {
        padding: 20px;
    }
    
    .new-topic-page .editor-toolbar {
        overflow-x: auto;
    }
}