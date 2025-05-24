<?php
/*
Template Name: İletişim Sayfası
*/

get_header();
?>

<style>
    .contact-container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 20px;
        font-family: 'Roboto', sans-serif;
    }

    .contact-wrapper {
        display: flex;
        gap: 30px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .contact-form {
        flex: 2;
        padding: 40px;
        background: #fff;
    }

    .contact-info {
        flex: 1;
        padding: 40px;
        background: #f8f9fa;
        position: relative;
        overflow: hidden;
    }

    .contact-info::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, #007bff22, #0056b322);
        z-index: 0;
    }

    .form-title {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
    }

    .input-group {
        margin-bottom: 20px;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e1e1e1;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    }

    textarea.form-input {
        min-height: 150px;
        resize: vertical;
    }

    .submit-btn {
        background: #007bff;
        color: #fff;
        padding: 12px 30px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        background: #0056b3;
        transform: translateY(-2px);
    }

    .info-title {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        position: relative;
        z-index: 1;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .info-item i {
        width: 40px;
        height: 40px;
        background: #007bff;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .info-text {
        color: #555;
        font-size: 16px;
    }

    .social-links {
        margin-top: 40px;
        position: relative;
        z-index: 1;
    }

    .social-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
    }

    .social-icons {
        display: flex;
        gap: 15px;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #007bff;
        font-size: 20px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .social-icon:hover {
        background: #007bff;
        color: #fff;
        transform: translateY(-3px);
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: none;
    }

    @media (max-width: 768px) {
        .contact-wrapper {
            flex-direction: column;
        }

        .contact-form,
        .contact-info {
            padding: 30px;
        }

        .form-title {
            font-size: 28px;
        }

        .info-title {
            font-size: 22px;
        }
    }
</style>

<div class="contact-container">
    <div class="contact-wrapper">
        <!-- Form Bölümü -->
        <div class="contact-form">
            <h2 class="form-title">Bize Ulaşın</h2>
            <?php if(isset($_GET['submitted']) && $_GET['submitted'] == 'true'): ?>
                <div class="success-message" style="display: block;">
                    Mesajınız başarıyla gönderildi!
                </div>
            <?php endif; ?>
            <form id="contact-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <div class="input-group">
                    <input type="text" name="name" class="form-input" placeholder="Adınız" required>
                </div>
                <div class="input-group">
                    <input type="text" name="surname" class="form-input" placeholder="Soyadınız" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" class="form-input" placeholder="E-posta Adresiniz" required>
                </div>
                <div class="input-group">
                    <textarea name="message" class="form-input" placeholder="Mesajınız" required></textarea>
                </div>
                <input type="hidden" name="action" value="submit_contact_form">
                <?php wp_nonce_field('contact_form_submit', 'contact_nonce'); ?>
                <button type="submit" class="submit-btn">Gönder</button>
            </form>
        </div>

        <!-- Bilgi Bölümü -->
        <div class="contact-info">
            <h3 class="info-title">İletişim Bilgileri</h3>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <span class="info-text">admin@metaprora.com</span>
            </div>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span class="info-text">Adres bilgileriniz</span>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <span class="info-text">Telefon numaranız</span>
            </div>

            <div class="social-links">
                <h4 class="social-title">Sosyal Medya</h4>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>