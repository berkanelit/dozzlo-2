<?php
/*
Template Name: Basit Giriş/Kayıt Sayfası
*/

// Zaten giriş yapmış kullanıcıyı profil sayfasına yönlendir
if (is_user_logged_in()) {
    wp_redirect(home_url('/profilim'));
    exit;
}

// Hata ve başarı mesajları için değişkenler
$login_error = '';
$register_error = '';
$register_success = '';

// Kayıt işlemi
if (isset($_POST['register'])) {
    $user_login = sanitize_user($_POST['reg_username']);
    $user_email = sanitize_email($_POST['reg_email']);
    $user_pass = $_POST['reg_password'];
    $user_pass_confirm = $_POST['reg_password_confirm'];
    $first_name = sanitize_text_field($_POST['reg_first_name']);
    $last_name = sanitize_text_field($_POST['reg_last_name']);

    // Hata kontrolü
    if (empty($user_login) || empty($user_email) || empty($user_pass) || empty($first_name) || empty($last_name)) {
        $register_error = "Lütfen tüm alanları doldurun.";
    } elseif (!is_email($user_email)) {
        $register_error = "Geçerli bir e-posta adresi giriniz.";
    } elseif ($user_pass !== $user_pass_confirm) {
        $register_error = "Şifreler eşleşmiyor.";
    } elseif (strlen($user_pass) < 6) {
        $register_error = "Şifre en az 6 karakter olmalıdır.";
    } elseif (username_exists($user_login)) {
        $register_error = "Bu kullanıcı adı zaten kullanılıyor.";
    } elseif (email_exists($user_email)) {
        $register_error = "Bu e-posta adresi zaten kayıtlı.";
    }

    // Hata yoksa kullanıcı oluştur
    if (empty($register_error)) {
        $user_id = wp_create_user($user_login, $user_pass, $user_email);
        
        if (!is_wp_error($user_id)) {
            // Kullanıcı bilgilerini güncelle
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $first_name . ' ' . $last_name
            ]);

            // Otomatik giriş yap
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            // Profil sayfasına yönlendir
            wp_redirect(home_url('/profilim'));
            exit;
        } else {
            $register_error = "Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.";
        }
    }
}

// Giriş işlemi
if (isset($_POST['login'])) {
    $username = sanitize_text_field($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    if (empty($username) || empty($password)) {
        $login_error = "Lütfen kullanıcı adı ve şifre girin.";
    } else {
        $creds = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember
        );

        $user = wp_signon($creds, false);

        if (!is_wp_error($user)) {
            wp_redirect(home_url('/profilim'));
            exit;
        } else {
            $login_error = "Hatalı kullanıcı adı veya şifre.";
        }
    }
}

// Aktif sekme (varsayılan olarak giriş)
$active_tab = isset($_GET['tab']) && $_GET['tab'] === 'register' ? 'register' : 'login';

get_header();
?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Hoş Geldiniz</h1>
        </div>

        <div class="auth-tabs">
            <a href="?tab=login" class="auth-tab <?php echo $active_tab === 'login' ? 'active' : ''; ?>">Giriş</a>
            <a href="?tab=register" class="auth-tab <?php echo $active_tab === 'register' ? 'active' : ''; ?>">Kayıt</a>
        </div>

        <div class="auth-content">
            <!-- Giriş Formu -->
            <form method="post" class="auth-form <?php echo $active_tab === 'login' ? 'active' : ''; ?>" id="login-form">
                <?php if (!empty($login_error)): ?>
                    <div class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Beni Hatırla</label>
                </div>

                <button type="submit" name="login" class="auth-button">Giriş Yap</button>

                <div class="form-footer">
                    <a href="<?php echo wp_lostpassword_url(); ?>">Şifremi Unuttum</a>
                </div>
            </form>

            <!-- Kayıt Formu -->
            <form method="post" class="auth-form <?php echo $active_tab === 'register' ? 'active' : ''; ?>" id="register-form">
                <?php if (!empty($register_error)): ?>
                    <div class="error-message"><?php echo $register_error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($register_success)): ?>
                    <div class="success-message"><?php echo $register_success; ?></div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="reg_first_name">Ad</label>
                        <input type="text" id="reg_first_name" name="reg_first_name" required>
                    </div>
                    <div class="form-group half">
                        <label for="reg_last_name">Soyad</label>
                        <input type="text" id="reg_last_name" name="reg_last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg_username">Kullanıcı Adı</label>
                    <input type="text" id="reg_username" name="reg_username" required>
                </div>

                <div class="form-group">
                    <label for="reg_email">E-posta</label>
                    <input type="email" id="reg_email" name="reg_email" required>
                </div>

                <div class="form-group">
                    <label for="reg_password">Şifre</label>
                    <input type="password" id="reg_password" name="reg_password" required minlength="6">
                    <small>En az 6 karakter</small>
                </div>

                <div class="form-group">
                    <label for="reg_password_confirm">Şifre (Tekrar)</label>
                    <input type="password" id="reg_password_confirm" name="reg_password_confirm" required>
                </div>

                <button type="submit" name="register" class="auth-button">Kayıt Ol</button>
            </form>
        </div>
    </div>
</div>

<style>
.auth-container {
    max-width: 500px;
    margin: 40px auto;
    padding: 20px;
}

.auth-box {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.auth-header {
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.auth-header h1 {
    margin: 0;
    font-size: 24px;
    color: #333;
}

.auth-tabs {
    display: flex;
    border-bottom: 1px solid #eee;
}

.auth-tab {
    flex: 1;
    text-align: center;
    padding: 12px;
    color: #555;
    text-decoration: none;
    font-weight: 500;
}

.auth-tab.active {
    color: #3498db;
    border-bottom: 2px solid #3498db;
}

.auth-content {
    padding: 20px;
}

.auth-form {
    display: none;
}

.auth-form.active {
    display: block;
}

.form-group {
    margin-bottom: 15px;
}

.form-row {
    display: flex;
    margin: 0 -10px;
}

.form-group.half {
    flex: 0 0 50%;
    padding: 0 10px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus {
    border-color: #3498db;
    outline: none;
}

.form-checkbox {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.form-checkbox input {
    margin-right: 8px;
}

.auth-button {
    width: 100%;
    padding: 10px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}

.auth-button:hover {
    background: #2980b9;
}

.form-footer {
    margin-top: 15px;
    text-align: center;
}

.form-footer a {
    color: #3498db;
    text-decoration: none;
}

.error-message {
    background: #ffebee;
    color: #c62828;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.success-message {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
}

small {
    color: #777;
    font-size: 12px;
}

@media (max-width: 576px) {
    .form-row {
        flex-direction: column;
    }
    
    .form-group.half {
        flex: 0 0 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Şifre doğrulama
    const password = document.getElementById('reg_password');
    const confirmPassword = document.getElementById('reg_password_confirm');
    
    if (password && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Şifreler eşleşmiyor');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });
        
        password.addEventListener('change', function() {
            if (confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Şifreler eşleşmiyor');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
        });
    }
});
</script>

<?php get_footer(); ?>