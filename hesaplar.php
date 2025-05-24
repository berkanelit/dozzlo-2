<?php
/*
Template Name: Basit Profil Sayfası
*/

// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    exit; // Direct access not allowed
}

// Oturum kontrolü - kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!is_user_logged_in()) {
    wp_redirect(home_url('/giris-kayit'));
    exit;
}

// Mevcut kullanıcı bilgilerini al
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// İşlem mesajları için değişkenler
$success_message = '';
$error_message = '';

// Sayfalama için parametreler
$posts_per_page = 10;
$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Kullanıcının forum konularını al
function get_user_forum_topics($user_id, $paged = 1, $posts_per_page = 10) {
    $args = array(
        'author' => $user_id,
        'post_type' => 'forum_topics',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => array('publish', 'pending')
    );
    return new WP_Query($args);
}

// Kullanıcının yorumlarını al
function get_user_comments($user_id, $paged = 1, $posts_per_page = 10) {
    $args = array(
        'user_id' => $user_id,
        'status' => 'approve',
        'number' => $posts_per_page,
        'offset' => ($paged - 1) * $posts_per_page,
    );
    return get_comments($args);
}

// Kullanıcının toplam yorum sayısını al
function get_total_user_comments($user_id) {
    $args = array(
        'user_id' => $user_id,
        'status' => 'approve',
        'count' => true
    );
    return get_comments($args);
}

// Yorum içeriğini AJAX ile getir
function get_comment_ajax() {
    if (isset($_POST['comment_id'])) {
        $comment_id = intval($_POST['comment_id']);
        $comment = get_comment($comment_id);
        
        if ($comment && $comment->user_id == get_current_user_id()) {
            echo wp_json_encode(array(
                'success' => true,
                'content' => $comment->comment_content
            ));
        } else {
            echo wp_json_encode(array(
                'success' => false,
                'message' => 'Yorum bulunamadı veya erişim izniniz yok.'
            ));
        }
    } else {
        echo wp_json_encode(array(
            'success' => false,
            'message' => 'Yorum ID belirtilmedi.'
        ));
    }
    wp_die();
}
add_action('wp_ajax_get_comment_content', 'get_comment_ajax');

// Yorumu güncelle AJAX işlemi
function update_comment_ajax() {
    if (isset($_POST['comment_id']) && isset($_POST['content'])) {
        $comment_id = intval($_POST['comment_id']);
        $content = sanitize_textarea_field($_POST['content']);
        $comment = get_comment($comment_id);
        
        if ($comment && $comment->user_id == get_current_user_id() && current_user_can('edit_comment', $comment_id)) {
            $updated = wp_update_comment(array(
                'comment_ID' => $comment_id,
                'comment_content' => $content
            ));
            
            if ($updated) {
                echo wp_json_encode(array(
                    'success' => true,
                    'message' => 'Yorum başarıyla güncellendi.'
                ));
            } else {
                echo wp_json_encode(array(
                    'success' => false,
                    'message' => 'Yorum güncellenirken bir hata oluştu.'
                ));
            }
        } else {
            echo wp_json_encode(array(
                'success' => false,
                'message' => 'Yorum bulunamadı veya düzenleme izniniz yok.'
            ));
        }
    } else {
        echo wp_json_encode(array(
            'success' => false,
            'message' => 'Gerekli parametreler eksik.'
        ));
    }
    wp_die();
}
add_action('wp_ajax_update_comment', 'update_comment_ajax');

// Form işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Profil bilgilerini güncelleme
    if (isset($_POST['update_profile'])) {
        // Verileri doğrula ve temizle
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $user_email = sanitize_email($_POST['email']);
        $user_bio = sanitize_textarea_field($_POST['bio']);
        $user_website = esc_url_raw($_POST['website']);
        
        // Email değiştirilmişse ve başka bir kullanıcı tarafından kullanılıyorsa
        if ($user_email !== $current_user->user_email && email_exists($user_email)) {
            $error_message = 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.';
        } else {
            // Kullanıcı verilerini güncelle
            $user_data = array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $first_name . ' ' . $last_name,
                'user_email' => $user_email,
                'user_url' => $user_website
            );
            
            $result = wp_update_user($user_data);
            
            if (!is_wp_error($result)) {
                // Ek bilgileri güncelle
                update_user_meta($user_id, 'description', $user_bio);
                $success_message = 'Profil bilgileriniz başarıyla güncellendi.';
            } else {
                $error_message = 'Profil güncellenirken bir hata oluştu: ' . $result->get_error_message();
            }
        }
    }
    
    // Profil fotoğrafı yükleme
    if (isset($_FILES['profile_image']) && !empty($_FILES['profile_image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Dosya uzantısını kontrol et
        $file_type = wp_check_filetype($_FILES['profile_image']['name']);
        $allowed_types = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');
        
        if (in_array($file_type['type'], $allowed_types)) {
            $attachment_id = media_handle_upload('profile_image', 0);
            
            if (!is_wp_error($attachment_id)) {
                // Eski profil fotoğrafını sil
                $old_image_id = get_user_meta($user_id, 'profile_image', true);
                if ($old_image_id) {
                    wp_delete_attachment($old_image_id, true);
                }
                
                // Yeni profil fotoğrafını kaydet
                update_user_meta($user_id, 'profile_image', $attachment_id);
                $success_message = 'Profil fotoğrafınız başarıyla güncellendi.';
                
                // Sayfa yenilendiğinde modal görünmemesi için yönlendirme
                wp_redirect(add_query_arg('success', 'photo', remove_query_arg('photo-modal')));
                exit;
            } else {
                $error_message = 'Profil fotoğrafı yüklenirken bir hata oluştu: ' . $attachment_id->get_error_message();
            }
        } else {
            $error_message = 'Lütfen sadece JPG, PNG veya GIF formatında dosya yükleyin.';
        }
    }
    
    // Şifre değiştirme
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Kullanıcı doğrulama
        $user = get_user_by('id', $user_id);
        if ($user && wp_check_password($old_password, $user->data->user_pass, $user_id)) {
            
            // Yeni şifre kontrolü
            if ($new_password === $confirm_password) {
                // Şifre güvenliği kontrolleri
                if (strlen($new_password) < 8) {
                    $error_message = 'Yeni şifreniz en az 8 karakter uzunluğunda olmalıdır.';
                } else {
                    // Şifreyi güncelle
                    wp_set_password($new_password, $user_id);
                    
                    // Kullanıcıyı tekrar giriş yaptır
                    $creds = array(
                        'user_login' => $user->user_login,
                        'user_password' => $new_password,
                        'remember' => true
                    );
                    
                    wp_signon($creds, false);
                    
                    $success_message = 'Şifreniz başarıyla değiştirildi.';
                }
            } else {
                $error_message = 'Yeni şifreler eşleşmiyor.';
            }
        } else {
            $error_message = 'Mevcut şifreniz doğru değil.';
        }
    }
    
    // Hesap silme
    if (isset($_POST['delete_account'])) {
        $password = $_POST['delete_password'];
        
        // Kullanıcı doğrulama
        $user = get_user_by('id', $user_id);
        if ($user && wp_check_password($password, $user->data->user_pass, $user_id)) {
            // Kullanıcıyı sil
            if (wp_delete_user($user_id)) {
                wp_logout();
                wp_redirect(home_url('/hesap-silindi'));
                exit;
            } else {
                $error_message = 'Hesap silinirken bir hata oluştu.';
            }
        } else {
            $error_message = 'Şifreniz doğru değil. Hesap silinemedi.';
        }
    }
}

// Aktif sekme kontrolü
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'profile';
$show_photo_modal = isset($_GET['photo-modal']);

get_header();
?>

<div class="profile-page">
    <div class="container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> Profil Sayfam</h1>
            <p>Hesap bilgilerinizi görüntüleyin ve yönetin.</p>
        </div>
        
        <div class="profile-content">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php 
                        $profile_image_id = get_user_meta($user_id, 'profile_image', true);
                        if ($profile_image_id) {
                            echo wp_get_attachment_image($profile_image_id, 'thumbnail', false, array('class' => 'avatar'));
                        } else {
                            echo get_avatar($user_id, 150, '', '', array('class' => 'avatar'));
                        }
                        ?>
                        <a href="<?php echo add_query_arg('photo-modal', '1'); ?>" class="edit-avatar">
                            <i class="fas fa-camera"></i>
                        </a>
                    </div>
                    <h3><?php echo esc_html($current_user->display_name); ?></h3>
                    <p><?php echo esc_html($current_user->user_email); ?></p>
                </div>
                
                <div class="user-stats">
                    <div class="stat">
                        <span class="value"><?php echo count_user_posts($user_id, 'forum_topics'); ?></span>
                        <span class="label">Konu</span>
                    </div>
                    <div class="stat">
                        <span class="value"><?php echo get_comments(array('user_id' => $user_id, 'count' => true)); ?></span>
                        <span class="label">Yorum</span>
                    </div>
                    <div class="stat">
                        <span class="value"><?php echo human_time_diff(strtotime($current_user->user_registered), current_time('timestamp')); ?></span>
                        <span class="label">Üyelik</span>
                    </div>
                </div>
                
                <div class="profile-nav">
                    <a href="<?php echo add_query_arg('tab', 'profile'); ?>" class="<?php echo $active_tab === 'profile' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i> Profil Bilgileri
                    </a>
                    <a href="<?php echo add_query_arg('tab', 'topics'); ?>" class="<?php echo $active_tab === 'topics' ? 'active' : ''; ?>">
                        <i class="fas fa-comments"></i> Konularım
                    </a>
                    <a href="<?php echo add_query_arg('tab', 'comments'); ?>" class="<?php echo $active_tab === 'comments' ? 'active' : ''; ?>">
                        <i class="fas fa-reply"></i> Yorumlarım
                    </a>
                    <a href="<?php echo add_query_arg('tab', 'security'); ?>" class="<?php echo $active_tab === 'security' ? 'active' : ''; ?>">
                        <i class="fas fa-shield-alt"></i> Güvenlik
                    </a>
                </div>
                
                <div class="logout-btn">
                    <a href="<?php echo wp_logout_url(home_url('/giris-kayit')); ?>">
                        <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                    </a>
                </div>
            </div>
            
            <!-- Ana İçerik -->
            <div class="profile-main">
                <?php if (!empty($success_message)): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> <?php echo esc_html($success_message); ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                <div class="alert error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo esc_html($error_message); ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['success']) && $_GET['success'] === 'photo'): ?>
                <div class="alert success">
                    <i class="fas fa-check-circle"></i> Profil fotoğrafınız başarıyla güncellendi.
                </div>
                <?php endif; ?>
                
                <div class="content-box">
                    <?php if ($active_tab === 'profile'): ?>
                        <div class="box-header">
                            <h2><i class="fas fa-user"></i> Profil Bilgileri</h2>
                            <p>Kişisel bilgilerinizi güncelleyin.</p>
                        </div>
                        
                        <div class="box-content">
                            <form method="post" class="profile-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="first_name">Ad <span class="required">*</span></label>
                                        <input type="text" id="first_name" name="first_name" 
                                            value="<?php echo esc_attr($current_user->first_name); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="last_name">Soyad <span class="required">*</span></label>
                                        <input type="text" id="last_name" name="last_name" 
                                            value="<?php echo esc_attr($current_user->last_name); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">E-posta <span class="required">*</span></label>
                                    <input type="email" id="email" name="email" 
                                        value="<?php echo esc_attr($current_user->user_email); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bio">Hakkımda</label>
                                    <textarea id="bio" name="bio" rows="4"><?php echo esc_textarea(get_user_meta($user_id, 'description', true)); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input type="url" id="website" name="website" 
                                        value="<?php echo esc_url($current_user->user_url); ?>">
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="update_profile" class="btn primary">
                                        <i class="fas fa-save"></i> Profili Güncelle
                                    </button>
                                </div>
                            </form>
                        </div>
                    
                    <?php elseif ($active_tab === 'topics'): ?>
                        <div class="box-header">
                            <h2><i class="fas fa-comments"></i> Forum Konularım</h2>
                            <p>Oluşturduğunuz forum konularını görüntüleyin ve yönetin.</p>
                        </div>
                        
                        <div class="box-content">
                            <?php
                            $user_topics = get_user_forum_topics($user_id, $current_page);
                            if ($user_topics->have_posts()) :
                            ?>
                                <div class="topics-list">
                                    <?php while ($user_topics->have_posts()) : $user_topics->the_post(); ?>
                                        <div class="topic-item">
                                            <div class="topic-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                <div class="topic-meta">
                                                    <span><i class="far fa-comment"></i> <?php echo get_comments_number(); ?> yanıt</span>
                                                    <span><i class="far fa-eye"></i> <?php echo get_post_meta(get_the_ID(), 'post_views_count', true) ?: '0'; ?> görüntülenme</span>
                                                    <span><i class="far fa-calendar"></i> <?php echo get_the_date('d M Y'); ?></span>
                                                </div>
                                            </div>
                                            
                                            <div class="topic-status">
                                                <?php 
                                                $status = get_post_status();
                                                $status_text = ($status === 'publish') ? 'Yayında' : (($status === 'pending') ? 'Onay Bekliyor' : ucfirst($status));
                                                $status_class = ($status === 'publish') ? 'published' : (($status === 'pending') ? 'pending' : 'draft');
                                                ?>
                                                <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            </div>
                                            
                                            <div class="topic-actions">
                                                <a href="<?php the_permalink(); ?>" class="btn sm view">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <?php if (current_user_can('edit_post', get_the_ID())): ?>
                                                <a href="<?php echo get_edit_post_link(); ?>" class="btn sm edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if (current_user_can('delete_post', get_the_ID())): ?>
                                                <button type="button" class="btn sm delete delete-topic" data-id="<?php the_ID(); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                
                                <?php if ($user_topics->max_num_pages > 1): ?>
                                <div class="pagination">
                                    <?php
                                    echo paginate_links(array(
                                        'base' => add_query_arg('paged', '%#%'),
                                        'format' => '',
                                        'prev_text' => '<i class="fas fa-chevron-left"></i>',
                                        'next_text' => '<i class="fas fa-chevron-right"></i>',
                                        'total' => $user_topics->max_num_pages,
                                        'current' => $current_page
                                    ));
                                    ?>
                                </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="empty-content">
                                    <i class="fas fa-info-circle"></i>
                                    <p>Henüz forum konusu oluşturmamışsınız.</p>
                                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('konu-baslat'))); ?>" class="btn primary">
                                        <i class="fas fa-plus-circle"></i> Yeni Konu Oluştur
                                    </a>
                                </div>
                            <?php 
                            endif;
                            wp_reset_postdata();
                            ?>
                        </div>
                    
                    <?php elseif ($active_tab === 'comments'): ?>
                        <div class="box-header">
                            <h2><i class="fas fa-reply"></i> Yorumlarım</h2>
                            <p>Forumdaki tüm yorumlarınızı görüntüleyin ve yönetin.</p>
                        </div>
                        
                        <div class="box-content">
                            <?php
                            $user_comments = get_user_comments($user_id, $current_page);
                            if ($user_comments) :
                            ?>
                                <div class="comments-list">
                                    <?php foreach ($user_comments as $comment) : ?>
                                        <div class="comment-item">
                                            <div class="comment-header">
                                                <h3>
                                                    <a href="<?php echo get_permalink($comment->comment_post_ID); ?>">
                                                        <?php echo get_the_title($comment->comment_post_ID); ?>
                                                    </a>
                                                </h3>
                                                <span class="comment-date">
                                                    <i class="far fa-clock"></i> 
                                                    <?php echo human_time_diff(strtotime($comment->comment_date), current_time('timestamp')); ?> önce
                                                </span>
                                            </div>
                                            
                                            <div class="comment-content">
                                                <?php echo wpautop($comment->comment_content); ?>
                                            </div>
                                            
                                            <div class="comment-actions">
                                                <a href="<?php echo get_comment_link($comment->comment_ID); ?>" class="btn sm view">
                                                    <i class="fas fa-eye"></i> Görüntüle
                                                </a>
                                                
                                                <?php if (current_user_can('edit_comment', $comment->comment_ID)): ?>
                                                <button type="button" class="btn sm edit edit-comment" 
                                                        data-id="<?php echo $comment->comment_ID; ?>"
                                                        data-nonce="<?php echo wp_create_nonce('edit_comment_' . $comment->comment_ID); ?>">
                                                    <i class="fas fa-edit"></i> Düzenle
                                                </button>
                                                <?php endif; ?>
                                                
                                                <?php if (current_user_can('delete_comment', $comment->comment_ID)): ?>
                                                <button type="button" class="btn sm delete delete-comment" 
                                                        data-id="<?php echo $comment->comment_ID; ?>"
                                                        data-nonce="<?php echo wp_create_nonce('delete_comment_' . $comment->comment_ID); ?>">
                                                    <i class="fas fa-trash"></i> Sil
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php
                                $total_comments = get_total_user_comments($user_id);
                                $total_pages = ceil($total_comments / $posts_per_page);
                                
                                if ($total_pages > 1) :
                                ?>
                                <div class="pagination">
                                    <?php
                                    echo paginate_links(array(
                                        'base' => add_query_arg('paged', '%#%'),
                                        'format' => '',
                                        'prev_text' => '<i class="fas fa-chevron-left"></i>',
                                        'next_text' => '<i class="fas fa-chevron-right"></i>',
                                        'total' => $total_pages,
                                        'current' => $current_page
                                    ));
                                    ?>
                                </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <div class="empty-content">
                                    <i class="fas fa-info-circle"></i>
                                    <p>Henüz yorum yapmamışsınız.</p>
                                    <a href="<?php echo esc_url(home_url('/forum/')); ?>" class="btn primary">
                                        <i class="fas fa-comments"></i> Forum Konularına Git
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    
                    <?php elseif ($active_tab === 'security'): ?>
                        <div class="box-header">
                            <h2><i class="fas fa-shield-alt"></i> Güvenlik Ayarları</h2>
                            <p>Şifrenizi değiştirin veya hesabınızı yönetin.</p>
                        </div>
                        
                        <div class="box-content">
                            <div class="security-section">
                                <h3><i class="fas fa-key"></i> Şifre Değiştir</h3>
                                
                                <form method="post" class="security-form">
                                    <div class="form-group">
                                        <label for="old_password">Mevcut Şifre <span class="required">*</span></label>
                                        <input type="password" id="old_password" name="old_password" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="new_password">Yeni Şifre <span class="required">*</span></label>
                                        <input type="password" id="new_password" name="new_password" required>
                                        <small>En az 8 karakter kullanın.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="confirm_password">Yeni Şifre (Tekrar) <span class="required">*</span></label>
                                        <input type="password" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" name="change_password" class="btn primary">
                                            <i class="fas fa-save"></i> Şifreyi Değiştir
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="security-section danger">
                                <h3><i class="fas fa-exclamation-triangle"></i> Hesabı Sil</h3>
                                
                                <div class="warning">
                                    <p><strong>Uyarı!</strong> Hesabınızı sildiğinizde, tüm verileriniz kalıcı olarak silinecektir ve bu işlem geri alınamaz.</p>
                                </div>
                                
                                <form method="post" id="deleteAccountForm" class="security-form">
                                    <div class="form-group">
                                        <label for="delete_password">Şifrenizi Girin <span class="required">*</span></label>
                                        <input type="password" id="delete_password" name="delete_password" required>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="submit" name="delete_account" class="btn danger">
                                            <i class="fas fa-trash-alt"></i> Hesabı Sil
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profil Fotoğrafı Modal -->
<?php if ($show_photo_modal): ?>
<div class="modal" id="photoModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-camera"></i> Profil Fotoğrafı Yükle</h3>
            <a href="<?php echo remove_query_arg('photo-modal'); ?>" class="modal-close">&times;</a>
        </div>
        <div class="modal-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_image">Fotoğraf Seç</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/gif" required>
                    <small>JPG, PNG veya GIF formatında bir resim yükleyin.</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn primary">
                        <i class="fas fa-upload"></i> Yükle
                    </button>
                    <a href="<?php echo remove_query_arg('photo-modal'); ?>" class="btn secondary">
                        <i class="fas fa-times"></i> İptal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Yorum Düzenleme Modal -->
<div class="modal" id="commentModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Yorumu Düzenle</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editCommentForm">
                <input type="hidden" id="edit_comment_id" name="comment_id">
                <input type="hidden" id="edit_comment_nonce" name="nonce">
                <div class="form-group">
                    <label for="edit_comment_content">Yorum</label>
                    <textarea id="edit_comment_content" name="content" rows="5" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn primary">
                        <i class="fas fa-save"></i> Kaydet
                    </button>
                    <button type="button" class="btn secondary" id="cancel-edit">
                        <i class="fas fa-times"></i> İptal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Genel Stiller */
:root {
    --primary-color: #2196F3;
    --primary-dark: #1976D2;
    --primary-light: #e3f2fd;
    --secondary-color: #f5f5f5;
    --danger-color: #f44336;
    --danger-dark: #d32f2f;
    --success-color: #4caf50;
    --warning-color: #ff9800;
    --text-color: #333;
    --text-light: #666;
    --border-color: #ddd;
    --background-color: #f8f9fa;
    --box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.profile-page {
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background-color: var(--background-color);
    padding: 30px 0;
    color: var(--text-color);
    line-height: 1.6;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Profile Header */
.profile-header {
    margin-bottom: 25px;
}

.profile-header h1 {
    font-size: 24px;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-header p {
    margin: 0;
    color: var(--text-light);
}

/* Main Layout */
.profile-content {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 25px;
}

/* Sidebar */
.profile-sidebar {
    background: white;
    border-radius: 8px;
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.user-info {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
}

.user-avatar {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto 15px;
}

.avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--secondary-color);
}

.edit-avatar {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: all 0.2s;
}

.edit-avatar:hover {
    background: var(--primary-dark);
    transform: scale(1.1);
}

.user-info h3 {
    margin: 0 0 5px 0;
    font-size: 18px;
}

.user-info p {
    margin: 0;
    color: var(--text-light);
    font-size: 14px;
}

.user-stats {
    display: flex;
    padding: 15px 0;
    border-bottom: 1px solid var(--border-color);
}

.stat {
    flex: 1;
    text-align: center;
    display: flex;
    flex-direction: column;
}

.stat .value {
    font-weight: 600;
    color: var(--primary-color);
}

.stat .label {
    font-size: 12px;
    color: var(--text-light);
}

.profile-nav {
    padding: 15px;
    display: flex;
    flex-direction: column;
}

.profile-nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    color: var(--text-color);
    text-decoration: none;
    border-radius: 6px;
    margin-bottom: 5px;
    transition: all 0.2s;
}

.profile-nav a:hover {
    background: var(--secondary-color);
    color: var(--primary-color);
}

.profile-nav a.active {
    background: var(--primary-light);
    color: var(--primary-color);
    font-weight: 500;
}

.logout-btn {
    padding: 15px;
    border-top: 1px solid var(--border-color);
}

.logout-btn a {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px;
    background: var(--danger-color);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    transition: background 0.2s;
}

.logout-btn a:hover {
    background: var(--danger-dark);
}

/* Main Content */
.profile-main {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.alert {
    padding: 12px 15px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert.success {
    background-color: rgba(76, 175, 80, 0.1);
    border-left: 3px solid var(--success-color);
    color: #2e7d32;
}

.alert.error {
    background-color: rgba(244, 67, 54, 0.1);
    border-left: 3px solid var(--danger-color);
    color: #c62828;
}

.content-box {
    background: white;
    border-radius: 8px;
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.box-header {
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
}

.box-header h2 {
    margin: 0 0 5px 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.box-header p {
    margin: 0;
    color: var(--text-light);
    font-size: 14px;
}

.box-content {
    padding: 20px;
}

/* Form Styles */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group .required {
    color: var(--danger-color);
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
}

.form-group small {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: var(--text-light);
}

.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn.sm {
    padding: 6px 10px;
    font-size: 13px;
}

.btn.primary {
    background: var(--primary-color);
    color: white;
}

.btn.primary:hover {
    background: var(--primary-dark);
}

.btn.secondary {
    background: var(--secondary-color);
    color: var(--text-color);
}

.btn.secondary:hover {
    background: #e0e0e0;
}

.btn.danger {
    background: var(--danger-color);
    color: white;
}

.btn.danger:hover {
    background: var(--danger-dark);
}

.btn.view {
    background: var(--primary-light);
    color: var(--primary-dark);
}

.btn.edit {
    background: rgba(76, 175, 80, 0.1);
    color: #2e7d32;
}

.btn.delete {
    background: rgba(244, 67, 54, 0.1);
    color: #c62828;
}

/* Topics List */
.topics-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.topic-item {
    display: flex;
    padding: 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    align-items: center;
    transition: box-shadow 0.2s;
}

.topic-item:hover {
    box-shadow: var(--box-shadow);
}

.topic-title {
    flex: 1;
}

.topic-title a {
    color: var(--primary-dark);
    text-decoration: none;
    font-weight: 500;
}

.topic-title a:hover {
    text-decoration: underline;
}

.topic-meta {
    display: flex;
    gap: 15px;
    margin-top: 5px;
    font-size: 12px;
    color: var(--text-light);
    flex-wrap: wrap;
}

.topic-status {
    margin: 0 15px;
}

.status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status.published {
    background: rgba(76, 175, 80, 0.1);
    color: #2e7d32;
}

.status.pending {
    background: rgba(255, 152, 0, 0.1);
    color: #ef6c00;
}

.status.draft {
    background: rgba(158, 158, 158, 0.1);
    color: #616161;
}

.topic-actions {
    display: flex;
    gap: 5px;
}

/* Comments List */
.comments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.comment-item {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    overflow: hidden;
}

.comment-header {
    padding: 12px 15px;
    background: var(--secondary-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comment-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
}

.comment-header h3 a {
    color: var(--primary-dark);
    text-decoration: none;
}

.comment-header h3 a:hover {
    text-decoration: underline;
}

.comment-date {
    font-size: 12px;
    color: var(--text-light);
}

.comment-content {
    padding: 15px;
}

.comment-content p {
    margin: 0 0 10px 0;
}

.comment-content p:last-child {
    margin-bottom: 0;
}

.comment-actions {
    padding: 10px 15px;
    border-top: 1px solid var(--border-color);
    display: flex;
    gap: 10px;
}

/* Security Sections */
.security-section {
    background: var(--secondary-color);
    border-radius: 6px;
    padding: 20px;
    margin-bottom: 20px;
}

.security-section:last-child {
    margin-bottom: 0;
}

.security-section.danger {
    background: rgba(244, 67, 54, 0.05);
    border: 1px solid rgba(244, 67, 54, 0.2);
}

.security-section h3 {
    margin: 0 0 15px 0;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.security-section.danger h3 {
    color: var(--danger-color);
}

.warning {
    background: rgba(255, 152, 0, 0.1);
    border-left: 3px solid var(--warning-color);
    padding: 10px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.warning p {
    margin: 0;
    font-size: 14px;
    color: #ef6c00;
}

/* Modals */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 450px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-close {
    font-size: 24px;
    color: var(--text-light);
    cursor: pointer;
    line-height: 1;
    text-decoration: none;
}

.modal-body {
    padding: 20px;
}

/* Empty Content */
.empty-content {
    text-align: center;
    padding: 30px;
}

.empty-content i {
    font-size: 36px;
    color: #ccc;
    margin-bottom: 15px;
}

.empty-content p {
    margin: 0 0 20px 0;
    color: var(--text-light);
}

/* Pagination */
.pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 5px;
}

.pagination .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    background: var(--secondary-color);
    color: var(--text-color);
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s;
}

.pagination .page-numbers:hover {
    background: var(--primary-light);
    color: var(--primary-color);
}

.pagination .current {
    background: var(--primary-color);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .topic-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .topic-status {
        margin: 10px 0;
    }
    
    .topic-actions {
        align-self: flex-end;
    }
    
    .comment-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .comment-date {
        margin-top: 5px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Yorum düzenleme fonksiyonu
    const editButtons = document.querySelectorAll('.edit-comment');
    const commentModal = document.getElementById('commentModal');
    const commentForm = document.getElementById('editCommentForm');
    const closeModalBtn = document.querySelector('#commentModal .modal-close');
    const cancelBtn = document.getElementById('cancel-edit');
    
    // Modal açma
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-id');
            const nonce = this.getAttribute('data-nonce');
            
            // Form alanlarını ayarla
            document.getElementById('edit_comment_id').value = commentId;
            document.getElementById('edit_comment_nonce').value = nonce;
            
            // AJAX ile yorum içeriğini getir
            const data = new FormData();
            data.append('action', 'get_comment_content');
            data.append('comment_id', commentId);
            
            // AJAX isteği
            fetch(ajaxurl, {
                method: 'POST',
                body: data,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_comment_content').value = data.content;
                    commentModal.style.display = 'flex';
                } else {
                    alert('Yorum yüklenirken bir hata oluştu: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
            });
        });
    });
    
    // Modal kapatma
    function closeModal() {
        commentModal.style.display = 'none';
    }
    
    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    
    // Modal dışına tıklayınca kapat
    commentModal.addEventListener('click', function(e) {
        if (e.target === commentModal) {
            closeModal();
        }
    });
    
    // Yorumu güncelle
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const commentId = document.getElementById('edit_comment_id').value;
        const content = document.getElementById('edit_comment_content').value;
        const nonce = document.getElementById('edit_comment_nonce').value;
        
        if (!content.trim()) {
            alert('Lütfen bir yorum içeriği girin.');
            return;
        }
        
        const data = new FormData();
        data.append('action', 'update_comment');
        data.append('comment_id', commentId);
        data.append('content', content);
        data.append('nonce', nonce);
        
        // AJAX isteği
        fetch(ajaxurl, {
            method: 'POST',
            body: data,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Yorum başarıyla güncellendi.');
                location.reload();
            } else {
                alert('Yorum güncellenirken bir hata oluştu: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
        });
    });
    
    // Konu silme işlemi
    const deleteTopicButtons = document.querySelectorAll('.delete-topic');
    
    deleteTopicButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Bu konuyu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
                const topicId = this.getAttribute('data-id');
                // Gerçek uygulamada burada AJAX ile silme işlemi yapılır
                alert('Konu silme işlemi başarılı!');
                // Gerçek uygulamada başarılı silme işleminden sonra: location.reload();
            }
        });
    });
    
    // Yorum silme işlemi
    const deleteCommentButtons = document.querySelectorAll('.delete-comment');
    
    deleteCommentButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Bu yorumu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
                const commentId = this.getAttribute('data-id');
                // Gerçek uygulamada burada AJAX ile silme işlemi yapılır
                alert('Yorum silme işlemi başarılı!');
                // Gerçek uygulamada başarılı silme işleminden sonra: location.reload();
            }
        });
    });
    
    // Şifre kontrolü
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (newPasswordInput && confirmPasswordInput) {
        function validatePassword() {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Şifreler eşleşmiyor.');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        }
        
        newPasswordInput.addEventListener('change', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);
    }
    
    // Hesap silme onayı
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    
    if (deleteAccountForm) {
        deleteAccountForm.addEventListener('submit', function(e) {
            if (!confirm('Hesabınızı kalıcı olarak silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
                e.preventDefault();
            }
        });
    }
});
</script>

<?php get_footer(); ?>