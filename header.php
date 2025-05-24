<?php
/**
 * Displays the <head> section and everything before <div id="content-wrap">
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if (is_singular() && pings_open()): ?>
        <link rel="pingback" href="<?php echo esc_url(get_bloginfo('pingback_url')); ?>">
    <?php endif; ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php
    // wp_body_open hook from WordPress 5.2
    if (function_exists('wp_body_open')) {
        wp_body_open();
    } else {
        do_action('wp_body_open');
    }
    ?>
    
    <div id="wrapper" class="clearfix">
        <?php
        // Information Bar
        dazzlo_information_bar();
        
        // Header Layout
        dazzlo_header_layout();
        ?>
        
        <!-- Yoast SEO Breadcrumb -->
        <div class="breadcrumb-container">
            <?php
            if (function_exists('yoast_breadcrumb')) {
                yoast_breadcrumb('<p id="breadcrumbs" class="yoast-breadcrumbs">','</p>');
            }
            ?>
        </div>
        
        <div id="main" class="clearfix">

<?php
/**
 * Information Bar Function
 */
function dazzlo_information_bar() {
    if (get_theme_mod('dazzlo_information_bar_disable', 'disable') == 'disable') {
        return;
    }
    ?>
    <div class="information-bar">
        <div class="container">
            <?php if (get_theme_mod('dazzlo_information_link')): ?>
                <a href="<?php echo esc_url(get_theme_mod('dazzlo_information_link')); ?>">
                    <?php echo wp_kses_post(get_theme_mod('dazzlo_information_text', __('Subscribe to get new recipes', 'dazzlo'))); ?>
                </a>
            <?php else: ?>
                <?php echo wp_kses_post(get_theme_mod('dazzlo_information_text', __('Subscribe to get new recipes', 'dazzlo'))); ?>
            <?php endif; ?>
            <div class="close"><i class="fa fa-times"></i></div>
        </div>
    </div>
    <?php
}

/**
 * Header Layout Function
 */
function dazzlo_header_layout() {
    $header_layout = get_theme_mod('dazzlo_header_design_layout', 'header1');
    $header_class = ($header_layout == 'header2') ? 'header2' : 'header1';
    ?>
    <div class="dazzlo-top-bar <?php echo esc_attr($header_class); ?>">
        <!-- Header Logo Section -->
        <div class="header-inside clearfix">
            <div class="header-holder">
                <?php dazzlo_site_logo(); ?>
                
                <?php if (has_header_image()): ?>
                    <img src="<?php header_image(); ?>" class="header-image" alt="<?php esc_attr_e('Header image', 'dazzlo'); ?>">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Navigation Section -->
        <div class="menu-wrap">
            <?php if (has_nav_menu('main')): ?>
                <div class="top-bar">
                    <div class="menu-wrap-inner">
                        <a class="menu-toggle" href="#"><i class="fa fa-bars"></i></a>
                        <?php wp_nav_menu(array(
                            'theme_location' => 'main',
                            'menu_class' => 'main-nav clearfix',
                            'container' => false,
                        )); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Social Links and Search -->
            <div class="social-links">
                <div class="socials">
                    <?php dazzlo_search_icon(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Site Logo Function
 */
function dazzlo_site_logo() {
    $is_front = is_front_page() && is_home();
    $heading_tag = $is_front ? 'h1' : 'h2';
    ?>
    <div class="logo-default">
        <div class="logo-text">
            <?php if (get_theme_mod('dazzlo_light_logo') || get_theme_mod('dazzlo_dark_logo')): ?>
                <a class="lightlogo" href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url(get_theme_mod('dazzlo_light_logo')); ?>" alt="<?php esc_attr_e('Header image', 'dazzlo'); ?>">
                </a>
                <a class="darklogo" href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url(get_theme_mod('dazzlo_dark_logo')); ?>" alt="<?php esc_attr_e('Header image', 'dazzlo'); ?>">
                </a>
                
                <?php if (display_header_text()): ?>
                    <?php if ($is_front): ?>
                        <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                        <span class="site-description"><?php bloginfo('description'); ?></span>
                    <?php else: ?>
                        <span class="only-text">
                            <h2 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h2>
                            <span class="site-description"><?php bloginfo('description'); ?></span>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
                
            <?php else: ?>
                <?php if (display_header_text()): ?>
                    <?php if ($is_front): ?>
                        <span class="only-text">
                            <h1><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
                            <span><?php bloginfo('description'); ?></span>
                        </span>
                    <?php else: ?>
                        <h2><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h2>
                        <span><?php bloginfo('description'); ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Search Icon Function
 */
function dazzlo_search_icon() {
    if (get_theme_mod('dazzlo_general_search_icon')) {
        return;
    }
    ?>
    <button class="button ct_icon search" id="open-trigger">
        <i class="fa fa-search"></i>
    </button>
    
    <div class="modal micromodal-slide" id="modal-1" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <main class="modal__content" id="modal-1-content">
                    <div id="modal-1-content">
                        <?php get_search_form(); ?>
                    </div>
                </main>
            </div>
            <button class="button" id="close-trigger">
                <i class="fa fa-times"></i>
            </button>
        </div>
    </div>		
    <?php
}
?>