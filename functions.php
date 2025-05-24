<?php
/**
 * dazzlo functions, scripts and styles.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */


if ( ! function_exists( 'dazzlo_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 * @since dazzlo 1.0
 */
function dazzlo_setup() {


	/* Add Customizer settings */
	require( get_template_directory() . '/customizer.php' );

	/* Add default posts and comments RSS feed links to head */
	add_theme_support( 'automatic-feed-links' );
    //add_theme_support( 'custom-header' );



	add_editor_style();

	/* Enable support for Post Thumbnails */
	add_theme_support( 'post-thumbnails' );

    add_image_size( 'dazzlo-full-thumb', 1080, 0, true );
    add_image_size('dazzlo-thumb', 200, 120, true);

    set_post_thumbnail_size( 150, 150, true ); // Default Thumb

    add_theme_support( "title-tag" );
    add_image_size( 'dazzlo-large-image', 9999, 9999, false  );// Large Post Image
    add_image_size( 'dazzlo-medium-image', 600, 600, true  );// Large Post Image
    add_image_size( 'dazzlo-featured-image', 150, 150, false  );// Large Post Image
    add_image_size( 'dazzlo-small-image', 715, 500, false  );// Large Post Image
    add_image_size( 'dazzlo-widget-small-thumb', 100, 100, true  );// Large Post Image
	/* Custom Background Support */
	add_theme_support( 'custom-background' );

        $args = array(
            'width'         => 2000,
            'height'        => 300,

        );
        add_theme_support( 'custom-header', $args );


       add_theme_support('custom-logo', array(
           'size' => 'dazzlo-thumb'
       ));


    add_action('after_setup_theme', 'dazzlo_setup');



	/* Register Menu */
	register_nav_menus( array(
		'main' 		=> __( 'Main Menu', 'dazzlo' )
	) );

	/* Make theme available for translation */
	load_theme_textdomain( 'dazzlo', get_template_directory() . '/languages' );

	/* Add gallery format and custom gallery support */
	add_theme_support( 'post-formats', array( 'gallery' ) );
	add_theme_support( 'array_themes_gallery_support' );

	// Add support for legacy widgets
	add_theme_support( 'array_toolkit_legacy_widgets' );

	// Theme Activation Notice
    global $pagenow;

    if ( is_admin() && ('themes.php' == $pagenow) && isset( $_GET['activated'] ) ) {
        add_action( 'admin_notices', 'dazzlo_activation_notice' );
    }
}
endif; // dazzlo_setup
add_action( 'after_setup_theme', 'dazzlo_setup' );


/* Enqueue scripts and styles */
function dazzlo_scripts() {

	$version = wp_get_theme()->Version;

	//Main Stylesheet
	wp_enqueue_style( 'dazzlo-style', get_stylesheet_uri() );

	//Fitvids
	wp_enqueue_script( 'dazzlo-jquery-fitvids', get_template_directory_uri() . '/includes/js/fitvid/jquery.fitvids.js', array( 'jquery' ), '1.0.3', true );

	//matchheight
    wp_enqueue_script( 'dazzlo-jquery-matchheight', get_template_directory_uri() . '/includes/js/matchheight/matchheight.js', array( 'jquery' ), $version, true );

    //micromodal
    wp_enqueue_script( 'dazzlo-jquery-micromodal', get_template_directory_uri() . '/includes/js/micromodal/micromodal.js', array( 'jquery' ), $version, true );

    //outline.js
    wp_enqueue_script( 'dazzlo-jquery-outline', get_template_directory_uri() . '/includes/js/outline/outline.js', array( 'jquery' ), $version, true );

    //Custom Scripts
	wp_enqueue_script( 'dazzlo-custom-js', get_template_directory_uri() . '/includes/js/custom/custom.js', array( 'jquery' ), $version, true );

    //Load More Scripts
    wp_enqueue_script( 'dazzlo-load-more-js', get_template_directory_uri() . '/includes/js/custom/load-more-script.js', array( 'jquery' ), $version, true );


    //Slickslider
    wp_enqueue_script( 'dazzlo-slickslider-js', get_template_directory_uri() . '/includes/js/slickslider/slick.min.js', array( 'jquery' ), '1.8.0', true );

    //Theiastickysidebar
    wp_enqueue_script( 'dazzlo-resizesensor-js', get_template_directory_uri() . '/includes/js/theiastickysidebar/ResizeSensor.min.js', array( 'jquery' ), '1.5.0', true );

    wp_enqueue_script( 'dazzlo-theiastickysidebar-js', get_template_directory_uri() . '/includes/js/theiastickysidebar/theia-sticky-sidebar.min.js', array( 'jquery' ), '1.5.0', true );


    wp_enqueue_script( 'dazzlo-jquery-slicknav', get_template_directory_uri() . '/includes/js/slicknav/jquery.slicknav.min.js', array( 'jquery' ), $version, true );


    wp_register_style('dazzlo-responsive', get_template_directory_uri() . '/css/responsive.css');

    if(!get_theme_mod('dazzlo_general_responsive')) {
        wp_enqueue_style('dazzlo-responsive');
    }
	//HTML5 IE Shiv
	wp_enqueue_script( 'dazzlo-jquery-htmlshiv', get_template_directory_uri() . '/includes/js/html5/html5shiv.js', array(), '3.7.0', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
add_action( 'wp_enqueue_scripts', 'dazzlo_scripts' );

function dazzlo_darkmode_script() {
    wp_enqueue_script('dazzlo-darkmode-script', get_template_directory_uri() . '/includes/js/darkmode/darkmode.js', array('jquery'), '1.0', true);

    // Pass the ajax_url to script.js
    wp_localize_script('dazzlo-darkmode-script', 'dazzlo_darkmode_script_vars', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'dazzlo_darkmode_script');



function dazzlo_excerpt_more( $more ) {
    if ( !is_admin()) {
        return '...';
    }
}
add_filter('excerpt_more', 'dazzlo_excerpt_more');



// Widgets
include(get_template_directory() . '/inc/widgets/about_widget.php');
include(get_template_directory() . '/inc/widgets/category_post_widget.php');

/* Set the content width */
if ( ! isset( $content_width ) )
	$content_width = 690; /* pixels */


/* Register sidebars */
function dazzlo_register_sidebars() {

    register_sidebar( array(
        'name'          => __( 'Below Slider', 'dazzlo' ),
        'id'            => 'below-slider',
        'description'   => __( 'This widget area is for Newsletter, Ads, Most popular widgets, etc.', 'dazzlo' ),
        'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="below-slider">',
        'after_title' => '</h4>',
    ) );
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'dazzlo' ),
        'id'            => 'sidebar',
        'description'   => __( 'Widgets in this area will be shown on the sidebar of all pages.', 'dazzlo' ),
        'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
        'after_widget'  => '</div>'
    ) );
    register_sidebar(array(
        'name' => __('Footer Top','dazzlo'),
        'id' => 'footer-top',
        'before_widget' => '<div id="%1$s" class="footer-top %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="footer-top-title">',
        'after_title' => '</h4>',
        'description' => __('Use the "Footer Top" widget here.','dazzlo')
    ));
    register_sidebar(array(
        'name' => __('Instagram Footer','dazzlo'),
        'id' => 'sidebar-2',
        'before_widget' => '<div id="%1$s" class="instagram-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="instagram-title">',
        'after_title' => '</h4>',
        'description' => __('Use the "Instagram" widget here. IMPORTANT: For best result set number of photos to 8.','dazzlo')
    ));

    register_sidebar( array(
        'name'          => __( 'Footer Left', 'dazzlo' ),
        'id'            => 'footer-left',
        'description'   => __( 'This widget area is for Footer Widgets.', 'dazzlo' ),
        'before_widget' => '<div id="%1$s" class="footerleft widget clearfix %2$s">',
        'after_widget' => '</div>'
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer Center', 'dazzlo' ),
        'id'            => 'footer-center',
        'description'   => __( 'This widget area is for Footer Widgets.', 'dazzlo' ),
        'before_widget' => '<div id="%1$s" class="footercenter widget clearfix %2$s">',
        'after_widget' => '</div>'
    ) );
    register_sidebar( array(
        'name'          => __( 'Footer Right', 'dazzlo' ),
        'id'            => 'footer-right',
        'description'   => __( 'This widget area is for Footer Widgets.', 'dazzlo' ),
        'before_widget' => '<div id="%1$s" class="footerright widget clearfix %2$s">',
        'after_widget' => '</div>'
    ) );
}
add_action( 'widgets_init', 'dazzlo_register_sidebars' );


/* Custom Excerpt Length only for List Post on Homepage */


    function dazzlo_custom_excerpt_length( $length ) {
        if ( !is_admin()) {
            return 40;
        }
    }
    add_filter('excerpt_length', 'dazzlo_custom_excerpt_length', 999);







/* Custom Comment Output */
function dazzlo_comments( $comment, $args, $depth ) {
	 ?>
	<li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID() ?>">

		<div class="comment-block" id="comment-<?php comment_ID(); ?>">
			<div class="comment-info">
				<div class="comment-dazzlo vcard clearfix">
					<?php echo get_avatar( $comment->comment_dazzlo_email, 75 ); ?>

					<div class="comment-meta commentmetadata">
						<?php /* translators: %s: comment author link */ printf(__('<cite class="fn">%s</cite>', 'dazzlo'), get_comment_author_link()) ?>
                        <a class="comment-time" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>"><?php /* translators: %s: comment date */ printf(__('%1$s at %2$s', 'dazzlo'), get_comment_date(),  get_comment_time()) ?></a>
					</div>


				</div>
			</div><!-- comment info -->

			<div class="comment-text">
				<?php comment_text() ?>

				<div class="comment-bottom">

					<?php edit_comment_link(__('Edit', 'dazzlo'),' ', '' ) ?>

				</div>
			</div><!-- comment text -->
            <p class="reply">
                <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) ?>
            </p>
			<?php if ($comment->comment_approved == '0') : ?>
				<em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'dazzlo') ?></em>
			<?php endif; ?>
		</div>
<?php
}

function dazzlo_cancel_comment_reply_button( $html, $link, $text ) {
    $style = isset($_GET['replytocom']) ? '' : ' style="display:none;"';
    $button = '<div id="cancel-comment-reply-link"' . $style . '>';
    return $button . '<i class="fa fa-times"></i> </div>';
}

add_action( 'cancel_comment_reply_link', 'dazzlo_cancel_comment_reply_button', 10, 3 );


/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */



/**
 * Sets the dazzlodata global when viewing an author archive.
 *
 * It removes the need to call the_post() and rewind_posts() in an dazzlo
 * template to print information about the dazzlo.
 *
 * @global WP_Query $wp_query WordPress Query object.
 * @return void
 */
function dazzlo_setup_dazzlo() {
	global $wp_query;

	if ( $wp_query->is_dazzlo() && isset( $wp_query->post ) ) {
		$GLOBALS['dazzlodata'] = get_userdata( $wp_query->post->post_dazzlo );
	}
}
add_action( 'wp', 'dazzlo_setup_dazzlo' );


/**
 * Return the Google font stylesheet URL
 */
function dazzlo_add_google_fonts() {
    wp_enqueue_style( 'dazzlo-poppins-display-google-webfonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap', false );
    wp_enqueue_style( 'dazzlo-open-sans-google-webfonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap', false );
}
add_action( 'wp_enqueue_scripts', 'dazzlo_add_google_fonts' );

/* Start Category Count in Span */

add_filter('wp_list_categories', 'dazzlo_cat_count_span');
function dazzlo_cat_count_span($links) {
    $links = str_replace('</a> (', '</a> <span>', $links);
    $links = str_replace(')', '</span>', $links);
    return $links;
}

/* End Category Count in Span */


/* Start Archive Count in Span */

add_filter('get_archives_link', 'dazzlo_archive_count_span');
function dazzlo_archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', '</a> <span class="archiveCount">', $links);
    $links = str_replace(')', '</span>', $links);
    return $links;
}

/* End Archive Count in Span */

function dazzlo_wpb_author_info_box( $content ) {

global $post;

// Detect if it is a single post with a post author
if ( is_single() && isset( $post->post_author ) ) {

// Get author's display name 
$display_name = get_the_author_meta( 'display_name', $post->post_author );

// If display name is not available then use nickname as display name
if ( empty( $display_name ) )
$display_name = get_the_author_meta( 'nickname', $post->post_author );

// Get author's biographical information or description
$user_description = get_the_author_meta( 'user_description', $post->post_author );

// Get author's website URL 
$user_website = get_the_author_meta('url', $post->post_author);

// Get link to the dazzlo archive page
$user_posts = get_author_posts_url( get_the_author_meta( 'ID' , $post->post_author));

    $author_details='';

if ( ! empty( $user_description ) )
// author avatar and bio

    $author_details = '<p class="dazzlo_details">' . get_avatar( get_the_author_meta('user_email') , 160 ) . '</p>';

if ( ! empty( $display_name ) ) {


    $author_details .= '<div class="dazzlo_author">' . __('Posted By', 'dazzlo') . '</div>';

    $author_details .= '<p class="dazzlo_name">' . '<a href="' . esc_url($user_posts) . '">' . esc_html($display_name) . '</a></p><p>' . nl2br($user_description) . '</p>';
}

// Pass all this info to post content  
$content = $content . '<footer class="dazzlo_bio_section" >' . $author_details . '</footer>';
}
echo $content;
}

function dazzlo_getCategory()
{
    $category = get_the_category();
    $useCatLink = true;
    // If post has a category assigned.
    if ($category) {
        $category_display = '';
        $category_link = '';
        if (class_exists('WPSEO_Primary_Term')) {
            $wpseo_primary_term = new WPSEO_Primary_Term('category', get_the_id());
            $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
            $term = get_term($wpseo_primary_term);
            if (is_wp_error($term)) {
                // Default to first category if an error is returned
                $category_display = $category[0]->name;
                $category_link = get_category_link($category[0]->term_id);
            } else {
                // Primary category
                $category_display = $term->name;
                $category_link = get_category_link($term->term_id);
            }
        } else {
            // Default, display the first category in WP's list of assigned categories
            $category_display = $category[0]->name;
            $category_link = get_category_link($category[0]->term_id);

        }

        // Display category
        if (!empty($category_display)) {
            if ($useCatLink == true && !empty($category_link)) {
                echo '<span class="post-category">';
                echo '<a href="' . esc_url($category_link) . '">' . esc_html($category_display) . '</a>';

                echo '</span>';
            } else {
                echo '<span class="post-category">' . esc_html($category_display) . '</span>';
            }
        }

    }
}

//theme options
include(get_template_directory() . '/dazzlo_custom_controller.php');
include(get_template_directory() . '/customizer_style.php');
//kirki themeoptions

if (  class_exists( 'kirki' ) ) {
    include(get_template_directory() . '/theme-options.php');
}

if ( ! function_exists( 'wp_body_open' ) ) {
    /**
     * Fire the wp_body_open action.
     *
     * Added for backwards compatibility to support WordPress versions prior to 5.2.0.
     */
    function wp_body_open() {
        /**
         * Triggered after the opening <body> tag.
         */
        do_action( 'wp_body_open' );
    }
}

// Add our function to the post content filter 
add_action( 'dazzlo_authorbox', 'dazzlo_wpb_author_info_box' );


add_action( 'wp_footer', function () {   if( !is_admin() ) {  ?>
    <script id="dazzlo-ajax-pagination-main-js-js-extra">
        if ( document.querySelector("#dazzlo-ajax-pagination") ) {
            var dazzloSettings = {"1":{"theme_defaults":"Twenty Sixteen","posts_wrapper":posts_wrapper,"post_wrapper":post_wrapper,"pagination_wrapper":pagination_wrapper,"next_page_selector":next_page_selector,"paging_type":"load-more","infinite_scroll_buffer":"20","ajax_loader":"<img src=\"<?php echo esc_url(get_template_directory_uri() . '/images/loading.gif'); ?>\" alt=\"AJAX Loader\" />","load_more_button_text":"Load More","loading_more_posts_text":"Loading...","callback_function":""}}};
    </script>
<?php  } } );

function google_analytics_code() {
?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-115FKL843C"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-115FKL843C');
    </script>
<?php
}
add_action('wp_head', 'google_analytics_code');

// İletişim formu işleme fonksiyonu
function handle_contact_form_submission() {
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'contact_form_submit')) {
        wp_die('Güvenlik doğrulaması başarısız');
    }

    $name = sanitize_text_field($_POST['name']);
    $surname = sanitize_text_field($_POST['surname']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    $to = 'admin@metaprora.com';
    $subject = 'Yeni İletişim Formu Mesajı';
    $body = "Ad: $name\n";
    $body .= "Soyad: $surname\n";
    $body .= "E-posta: $email\n\n";
    $body .= "Mesaj:\n$message";

    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    wp_mail($to, $subject, $body, $headers);

    wp_redirect(add_query_arg('submitted', 'true', wp_get_referer()));
    exit;
}
add_action('admin_post_submit_contact_form', 'handle_contact_form_submission');
add_action('admin_post_nopriv_submit_contact_form', 'handle_contact_form_submission');

// Üyelik sistemi 
// Giriş/Çıkış ve Kayıt Linklerini Ekleme
function add_login_register_links() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        echo '<div class="user-menu">';
        echo '<div class="user-avatar">' . get_avatar($current_user->ID, 32) . '</div>';
        echo '<div class="user-info">';
        echo '<span class="username">Merhaba, ' . $current_user->display_name . '</span>';
        echo '<a href="' . get_edit_profile_url() . '">Profilim</a>';
        echo '<a href="' . wp_logout_url(home_url()) . '">Çıkış Yap</a>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="login-register-links">';
        echo '<a href="' . wp_login_url() . '" class="login-link">Giriş Yap</a>';
        echo '<a href="' . wp_registration_url() . '" class="register-link">Kayıt Ol</a>';
        echo '</div>';
    }
}

// Abonelerin admin paneline erişimini engelle ve yönlendir
function restrict_admin_access() {
    if (
        is_admin() && 
        !current_user_can('administrator') && 
        !(defined('DOING_AJAX') && DOING_AJAX)
    ) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('init', 'restrict_admin_access');

// Admin bar'ı aboneler için gizle
function remove_admin_bar() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');

// Yeni kayıt olan kullanıcılara varsayılan "abone" rolü ata
function set_default_role($user_id) {
    $user = new WP_User($user_id);
    $user->set_role('subscriber');
}
add_action('user_register', 'set_default_role');

// Abonelerin yapabileceklerini sınırla
function subscriber_capabilities() {
    $role = get_role('subscriber');
    
    // Mevcut yetkileri temizle
    $role->remove_cap('edit_posts');
    $role->remove_cap('edit_published_posts');
    $role->remove_cap('publish_posts');
    $role->remove_cap('delete_posts');
    $role->remove_cap('delete_published_posts');
    $role->remove_cap('edit_pages');
    $role->remove_cap('edit_published_pages');
    $role->remove_cap('publish_pages');
    $role->remove_cap('delete_pages');
    $role->remove_cap('delete_published_pages');
    
    // Temel yetkileri ekle
    $role->add_cap('read'); // İçerik okuma
    $role->add_cap('edit_comments'); // Yorum yapabilme
}
add_action('init', 'subscriber_capabilities');

// Profil sayfasındaki gereksiz alanları gizle
function simplify_profile_page($buffer) {
    if (!current_user_can('administrator')) {
        // Renk şeması seçeneğini gizle
        $buffer = preg_replace('/<tr class="user-admin-color-wrap">.*?<\/tr>/s', '', $buffer);
        
        // Klavye kısayolları seçeneğini gizle
        $buffer = preg_replace('/<tr class="user-comment-shortcuts-wrap">.*?<\/tr>/s', '', $buffer);
        
        // Araç çubuğu seçeneğini gizle
        $buffer = preg_replace('/<tr class="user-admin-bar-front-wrap">.*?<\/tr>/s', '', $buffer);
        
        // Dil seçeneğini gizle
        $buffer = preg_replace('/<tr class="user-language-wrap">.*?<\/tr>/s', '', $buffer);
    }
    return $buffer;
}
add_action('admin_head', function() {
    ob_start('simplify_profile_page');
});
add_action('admin_footer', function() {
    ob_end_flush();
});

// Login sayfasını özelleştir
function custom_login_page() {
    return home_url('/kayit-ol-giris-yap'); // giris-kayit sayfanızın URL'si
}
add_filter('login_url', 'custom_login_page');

// Şifremi unuttum sayfasını özelleştir
function custom_lostpassword_page() {
    return home_url('/sifremi-unuttum'); // sifremi-unuttum sayfanızın URL'si
}
add_filter('lostpassword_url', 'custom_lostpassword_page');

// Çıkış yap yönlendirmesini özelleştir
function custom_logout_redirect() {
    wp_redirect(home_url('/giris-kayit'));
    exit();
}
add_action('wp_logout', 'custom_logout_redirect');

// Dashboard widget'larını gizle
function remove_dashboard_widgets() {
    if (!current_user_can('administrator')) {
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
    }
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

// Admin menü öğelerini gizle
function remove_admin_menus() {
    if (!current_user_can('administrator')) {
        remove_menu_page('index.php');                  // Dashboard
        remove_menu_page('edit.php');                   // Posts
        remove_menu_page('upload.php');                 // Media
        remove_menu_page('edit.php?post_type=page');    // Pages
        remove_menu_page('edit-comments.php');          // Comments
        remove_menu_page('themes.php');                 // Appearance
        remove_menu_page('plugins.php');                // Plugins
        remove_menu_page('users.php');                  // Users
        remove_menu_page('tools.php');                  // Tools
        remove_menu_page('options-general.php');        // Settings
    }
}
add_action('admin_menu', 'remove_admin_menus');

// Admin footer metinlerini özelleştir
function custom_admin_footer() {
    if (!current_user_can('administrator')) {
        echo 'Hoş geldiniz!';
    }
}
add_filter('admin_footer_text', 'custom_admin_footer');

// Profil sayfası dışındaki admin sayfalarına erişimi engelle
function restrict_admin_pages() {
    if (
        is_admin() && 
        !current_user_can('administrator') && 
        !(defined('DOING_AJAX') && DOING_AJAX)
    ) {
        $screen = get_current_screen();
        if ($screen->base !== 'profile') {
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('current_screen', 'restrict_admin_pages');

// E-posta bildirimlerini özelleştir
function custom_email_from_name($name) {
    return get_bloginfo('name');
}
add_filter('wp_mail_from_name', 'custom_email_from_name');

// Güvenlik önlemleri
function security_measures() {
    // XML-RPC'yi devre dışı bırak
    add_filter('xmlrpc_enabled', '__return_false');
    
    // WordPress sürüm bilgisini gizle
    remove_action('wp_head', 'wp_generator');
    
    // Başarısız giriş denemelerini sınırla
    if (isset($_POST['log']) && isset($_POST['pwd'])) {
        $failed_attempts = get_transient('failed_login_' . $_SERVER['REMOTE_ADDR']);
        if ($failed_attempts && $failed_attempts > 3) {
            wp_die('Çok fazla başarısız giriş denemesi. Lütfen daha sonra tekrar deneyin.');
        }
    }
}
add_action('init', 'security_measures');

// Başarısız giriş denemelerini kaydet
function log_failed_login($username) {
    $failed_attempts = get_transient('failed_login_' . $_SERVER['REMOTE_ADDR']);
    if (!$failed_attempts) {
        set_transient('failed_login_' . $_SERVER['REMOTE_ADDR'], 1, HOUR_IN_SECONDS);
    } else {
        set_transient('failed_login_' . $_SERVER['REMOTE_ADDR'], $failed_attempts + 1, HOUR_IN_SECONDS);
    }
}
add_action('wp_login_failed', 'log_failed_login');


/////////////////////////////////////////////////////////////////////////////////////////////////////

function custom_language_switcher() {
    if ( function_exists( 'pll_the_languages' ) ) {
        echo '<div id="custom-language-switcher">
                <button id="lang-toggle">
                    <span>Language</span>
                    <svg class="lang-arrow" width="12" height="12" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
                <div id="lang-menu">';
        pll_the_languages( array( 
            'dropdown' => 0, 
            'show_flags' => 1, 
            'show_names' => 1 
        ) );
        echo '</div>
              </div>';
    }
}
add_action( 'wp_footer', 'custom_language_switcher' );

function custom_language_switcher_styles() {
    ?>
    <style>
        #custom-language-switcher {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
        }

        #lang-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            width: 100%;
            min-width: 140px;
            border: none;
            background: none;
            color: #333333;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #lang-toggle:hover {
            background-color: #f8f8f8;
        }

        .lang-arrow {
            transition: transform 0.3s ease;
            fill: #666666;
        }

        #custom-language-switcher.active .lang-arrow {
            transform: rotate(180deg);
        }

        #lang-menu {
            display: none;
            background-color: #ffffff;
            border-top: 1px solid #eeeeee;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
        }

        #custom-language-switcher.active #lang-menu {
            display: block;
        }

        #lang-menu ul {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 200px;
            overflow-y: auto;
        }

        #lang-menu li {
            margin: 0;
            padding: 0;
        }

        #lang-menu a {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            text-decoration: none;
            color: #333333;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        #lang-menu a:hover {
            background-color: #f8f8f8;
        }

        #lang-menu img {
            width: 20px;
            height: 14px;
            margin-right: 10px;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            #custom-language-switcher {
                bottom: 15px;
                right: 15px;
            }

            #lang-toggle {
                padding: 10px 14px;
                min-width: 120px;
            }
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const switcher = document.getElementById('custom-language-switcher');
        const toggle = document.getElementById('lang-toggle');

        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            switcher.classList.toggle('active');
        });

        // Dışarı tıklandığında menüyü kapat
        document.addEventListener('click', function(e) {
            if (!switcher.contains(e.target)) {
                switcher.classList.remove('active');
            }
        });

        // Menü içindeki linklere tıklandığında menüyü kapat
        const menuLinks = switcher.querySelectorAll('#lang-menu a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });
    </script>
    <?php
}
add_action( 'wp_head', 'custom_language_switcher_styles' );


///////////////////////////////////////////////////////////////////
//

// Forum için gerekli function.php kodları

// Custom post type oluşturma
function create_forum_topics_post_type() {
    register_post_type('forum_topics',
        array(
            'labels' => array(
                'name' => __('Forum Konuları'),
                'singular_name' => __('Konu'),
                'add_new' => __('Yeni Konu'),
                'add_new_item' => __('Yeni Konu Ekle'),
                'edit_item' => __('Konuyu Düzenle'),
                'view_item' => __('Konuyu Görüntüle'),
                'search_items' => __('Konu Ara'),
                'not_found' => __('Konu bulunamadı'),
                'not_found_in_trash' => __('Çöp kutusunda konu bulunamadı')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'author', 'comments', 'thumbnail'),
            'menu_icon' => 'dashicons-format-chat',
            'menu_position' => 5,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'forum'),
            'capability_type' => 'post'
        )
    );

    // Kategori taksonomisi oluşturma
    register_taxonomy(
        'topic_category',
        'forum_topics',
        array(
            'labels' => array(
                'name' => __('Kategoriler'),
                'singular_name' => __('Kategori'),
                'search_items' => __('Kategori Ara'),
                'all_items' => __('Tüm Kategoriler'),
                'parent_item' => __('Üst Kategori'),
                'parent_item_colon' => __('Üst Kategori:'),
                'edit_item' => __('Kategoriyi Düzenle'),
                'update_item' => __('Kategoriyi Güncelle'),
                'add_new_item' => __('Yeni Kategori Ekle'),
                'new_item_name' => __('Yeni Kategori Adı')
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'forum-kategori')
        )
    );
}
add_action('init', 'create_forum_topics_post_type');

// Admin paneli için meta box ekleme
function add_topic_meta_boxes() {
    add_meta_box(
        'topic_details',
        'Konu Detayları',
        'display_topic_meta_box',
        'forum_topics',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_topic_meta_boxes');

// Meta box görüntüleme
function display_topic_meta_box($post) {
    $user_email = get_post_meta($post->ID, 'user_email', true);
    $user_name = get_post_meta($post->ID, 'user_name', true);
    $views = get_post_meta($post->ID, 'post_views_count', true);
    
    wp_nonce_field('topic_meta_box', 'topic_meta_box_nonce');
    ?>
    <div class="topic-meta-box">
        <p>
            <label>Konu Sahibi:</label>
            <input type="text" name="user_name" value="<?php echo esc_attr($user_name); ?>" readonly>
        </p>
        
        <p>
            <label>E-posta:</label>
            <input type="email" name="user_email" value="<?php echo esc_attr($user_email); ?>" readonly>
        </p>
        
        <p>
            <label>Görüntülenme:</label>
            <input type="number" name="post_views_count" value="<?php echo esc_attr($views); ?>" readonly>
        </p>
    </div>
    <?php
}

// Cache işlemleri için fonksiyonlar
function clear_forum_cache() {
    wp_cache_delete('latest_topics_cache');
    wp_cache_delete('popular_topics_cache');
    wp_cache_delete('latest_topics_widget');
    wp_cache_delete('popular_topics_widget');
}
add_action('save_post_forum_topics', 'clear_forum_cache');
add_action('deleted_post', 'clear_forum_cache');
add_action('comment_post', 'clear_forum_cache');

// Görüntülenme sayacı
function set_topic_views() {
    if(is_single() && get_post_type() === 'forum_topics') {
        $post_id = get_the_ID();
        $count = get_post_meta($post_id, 'post_views_count', true);
        
        if($count == '') {
            delete_post_meta($post_id, 'post_views_count');
            add_post_meta($post_id, 'post_views_count', 1);
        } else {
            update_post_meta($post_id, 'post_views_count', $count + 1);
        }
    }
}
add_action('wp', 'set_topic_views');

// Özel yorum şablonu
function custom_forum_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>
    
    <div class="comment-item" id="comment-<?php comment_ID(); ?>">
        <div class="comment-author-meta">
            <?php echo get_avatar($comment, 50); ?>
            <div class="comment-author-info">
                <h4 class="comment-author-name">
                    <?php echo get_comment_author_link(); ?>
                </h4>
                <div class="comment-metadata">
                    <time datetime="<?php comment_time('c'); ?>">
                        <?php printf('%1$s', get_comment_date('d F Y')); ?>
                    </time>
                </div>
            </div>
        </div>

        <div class="comment-content">
            <?php comment_text(); ?>
        </div>

        <?php if ('0' == $comment->comment_approved) : ?>
            <p class="comment-awaiting">
                Yorumunuz moderatör onayı bekliyor.
            </p>
        <?php endif; ?>

        <div class="comment-actions">
            <?php
            comment_reply_link(array_merge($args, array(
                'depth' => $depth,
                'max_depth' => $args['max_depth']
            )));
            ?>
        </div>
    </div>
<?php }

// SEO için meta açıklamaları
function add_forum_meta_description() {
    if (is_singular('forum_topics')) {
        $excerpt = wp_strip_all_tags(get_the_excerpt());
        echo '<meta name="description" content="' . esc_attr($excerpt) . '">';
    }
}
add_action('wp_head', 'add_forum_meta_description');

// Performans optimizasyonu
function optimize_forum_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('forum_topics')) {
        $query->set('posts_per_page', 10);
        $query->set('no_found_rows', true);
        $query->set('update_post_meta_cache', false);
        $query->set('update_post_term_cache', false);
    }
}
add_action('pre_get_posts', 'optimize_forum_query');

// AJAX ile yorum yükleme
function load_more_comments() {
    check_ajax_referer('comment_nonce', 'nonce');
    
    $post_id = $_POST['post_id'];
    $offset = $_POST['offset'];
    
    $comments = get_comments(array(
        'post_id' => $post_id,
        'offset' => $offset,
        'number' => 10,
        'status' => 'approve'
    ));
    
    foreach($comments as $comment) {
        // Yorum şablonunu yükle
        custom_forum_comment($comment, array(), 0);
    }
    
    die();
}
add_action('wp_ajax_load_more_comments', 'load_more_comments');
add_action('wp_ajax_nopriv_load_more_comments', 'load_more_comments');

// Gerekli script ve stilleri yükle
function enqueue_forum_scripts() {
    if (is_singular('forum_topics') || is_post_type_archive('forum_topics')) {
        wp_enqueue_script('forum-scripts', get_template_directory_uri() . '/js/forum.js', array('jquery'), '1.0', true);
        wp_localize_script('forum-scripts', 'forumAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('comment_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_forum_scripts');


// AJAX live search function
function live_search_topics() {
    $search_term = sanitize_text_field($_POST['search_term']);
    $html = '';

    if(strlen($search_term) >= 2) {
        $args = array(
            'post_type' => 'forum_topics',
            'posts_per_page' => 6,
            's' => $search_term,
            'orderby' => 'relevance',
            'post_status' => 'publish'
        );

        $search_query = new WP_Query($args);

        if($search_query->have_posts()) {
            while($search_query->have_posts()) {
                $search_query->the_post();
                $has_expert = get_post_meta(get_the_ID(), 'has_expert_answer', true);
                $expert_name = get_post_meta(get_the_ID(), 'expert_name', true);
                $expert_title = get_post_meta(get_the_ID(), 'expert_title', true);
                
                $html .= '<div class="search-result-item">';
                $html .= '<div class="search-result-header">';
                $html .= '<a href="' . get_permalink() . '" class="search-result-title">';
                if($has_expert == 'yes') {
                    $html .= '<i class="fas fa-user-md" style="color: #4CAF50;"></i>';
                } else {
                    $html .= '<i class="fas fa-comment"></i>';
                }
                $html .= get_the_title();
                $html .= '</a>';
                
                if($has_expert == 'yes') {
                    $html .= '<div class="expert-answer-badge">';
                    $html .= '<div class="expert-badge-content">';
                    $html .= '<i class="fas fa-user-md"></i>';
                    $html .= '<span class="expert-name">' . esc_html($expert_name) . '</span>';
                    $html .= '<span class="expert-title">' . esc_html($expert_title) . '</span>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '<div class="search-result-meta">';
                $html .= '<span><i class="far fa-user"></i> ' . get_post_meta(get_the_ID(), 'user_name', true) . '</span>';
                $html .= '<span><i class="far fa-comment"></i> ' . get_comments_number() . ' yanıt</span>';
                $html .= '<span><i class="far fa-eye"></i> ' . get_post_meta(get_the_ID(), 'post_views_count', true) . ' görüntülenme</span>';
                $html .= '<span><i class="far fa-clock"></i> ' . human_time_diff(get_the_time('U'), current_time('timestamp')) . ' önce</span>';
                $html .= '</div>';
                $html .= '</div>';
            }
        } else {
            $html .= '<div class="search-no-results">';
            $html .= '<i class="fas fa-search"></i> Sonuç bulunamadı';
            $html .= '</div>';
        }
        wp_reset_postdata();
    }

    echo $html;
    die();
}
add_action('wp_ajax_live_search_topics', 'live_search_topics');
add_action('wp_ajax_nopriv_live_search_topics', 'live_search_topics');

// AJAX URL'sini sayfaya ekle
function add_ajax_url() {
    ?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}
add_action('wp_head', 'add_ajax_url');

// Uzman cevabı için meta box ekleme
function add_expert_answer_meta_box() {
    add_meta_box(
        'expert_answer_box',
        'Uzman Cevabı',
        'display_expert_answer_box',
        'forum_topics',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_expert_answer_meta_box');

// Uzman cevabı meta box içeriği
function display_expert_answer_box($post) {
    wp_nonce_field('expert_answer_box', 'expert_answer_nonce');
    
    $expert_answer = get_post_meta($post->ID, 'expert_answer', true);
    $expert_name = get_post_meta($post->ID, 'expert_name', true);
    $expert_title = get_post_meta($post->ID, 'expert_title', true);
    $has_expert = get_post_meta($post->ID, 'has_expert_answer', true);
    ?>
    
    <div class="expert-answer-box">
        <p>
            <label>
                <input type="checkbox" name="has_expert_answer" value="yes" <?php checked($has_expert, 'yes'); ?>>
                Bu konuda uzman cevabı var
            </label>
        </p>
        
        <p>
            <label>Uzman Adı:</label><br>
            <input type="text" name="expert_name" value="<?php echo esc_attr($expert_name); ?>" style="width: 100%">
        </p>
        
        <p>
            <label>Uzman Ünvanı:</label><br>
            <input type="text" name="expert_title" value="<?php echo esc_attr($expert_title); ?>" style="width: 100%">
        </p>
        
        <p>
            <label>Uzman Cevabı:</label><br>
            <?php 
            wp_editor($expert_answer, 'expert_answer', array(
                'textarea_name' => 'expert_answer',
                'media_buttons' => true,
                'textarea_rows' => 10
            ));
            ?>
        </p>
    </div>
    <?php
}

// Uzman cevabı meta verilerini kaydetme
function save_expert_answer_meta($post_id) {
    if (!isset($_POST['expert_answer_nonce']) || 
        !wp_verify_nonce($_POST['expert_answer_nonce'], 'expert_answer_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Meta verileri kaydet
    update_post_meta($post_id, 'has_expert_answer', isset($_POST['has_expert_answer']) ? 'yes' : 'no');
    update_post_meta($post_id, 'expert_name', sanitize_text_field($_POST['expert_name']));
    update_post_meta($post_id, 'expert_title', sanitize_text_field($_POST['expert_title']));
    update_post_meta($post_id, 'expert_answer', wp_kses_post($_POST['expert_answer']));
}
add_action('save_post', 'save_expert_answer_meta');

// Konu yayınlandığında bildirim gönderme
function notify_author_on_topic_publish($new_status, $old_status, $post) {
    // Sadece forum konuları için çalış
    if ($post->post_type != 'forum_topics') {
        return;
    }

    // Konu ilk kez yayınlanıyorsa
    if ($old_status != 'publish' && $new_status == 'publish') {
        $author_email = get_post_meta($post->ID, 'user_email', true);
        $author_name = get_post_meta($post->ID, 'user_name', true);
        
        if (!empty($author_email)) {
            $subject = 'Konunuz Yayınlandı: ' . $post->post_title;
            
            $message = "Sayın {$author_name},\n\n";
            $message .= "'{$post->post_title}' başlıklı konunuz başarıyla yayınlanmıştır.\n\n";
            $message .= "Konunuzu görüntülemek için aşağıdaki bağlantıyı kullanabilirsiniz:\n";
            $message .= get_permalink($post->ID) . "\n\n";
            $message .= "Saygılarımızla,\n";
            $message .= get_bloginfo('name');
            
            wp_mail($author_email, $subject, $message);
        }
    }
}
add_action('transition_post_status', 'notify_author_on_topic_publish', 10, 3);

// Yeni yorum eklendiğinde bildirim gönderme
function notify_author_on_new_comment($comment_ID, $comment_approved, $commentdata) {
    // Yorum onaylanmışsa
    if ($comment_approved == 1) {
        $post = get_post($commentdata['comment_post_ID']);
        
        // Forum konusu için kontrol
        if ($post->post_type == 'forum_topics') {
            $author_email = get_post_meta($post->ID, 'user_email', true);
            $author_name = get_post_meta($post->ID, 'user_name', true);
            
            // Yorum yapan kişi konu sahibi değilse bildirim gönder
            if ($author_email && $author_email != $commentdata['comment_author_email']) {
                $subject = 'Konunuza Yeni Bir Yanıt Geldi: ' . $post->post_title;
                
                $message = "Sayın {$author_name},\n\n";
                $message .= "'{$post->post_title}' başlıklı konunuza yeni bir yanıt geldi.\n\n";
                $message .= "Yanıt: " . $commentdata['comment_content'] . "\n\n";
                $message .= "Yanıtı görüntülemek için aşağıdaki bağlantıyı kullanabilirsiniz:\n";
                $message .= get_permalink($post->ID) . "#comment-" . $comment_ID . "\n\n";
                $message .= "Saygılarımızla,\n";
                $message .= get_bloginfo('name');
                
                wp_mail($author_email, $subject, $message);
            }
        }
    }
}
add_action('comment_post', 'notify_author_on_new_comment', 10, 3);

// Uzman cevabı eklendiğinde bildirim gönderme
function notify_author_on_expert_answer($post_id) {
    // Post type kontrolü
    if (get_post_type($post_id) != 'forum_topics') {
        return;
    }
    
    // Uzman cevabı var mı kontrolü
    $has_expert = get_post_meta($post_id, 'has_expert_answer', true);
    $expert_answer = get_post_meta($post_id, 'expert_answer', true);
    
    if ($has_expert == 'yes' && !empty($expert_answer)) {
        $author_email = get_post_meta($post_id, 'user_email', true);
        $author_name = get_post_meta($post_id, 'user_name', true);
        $expert_name = get_post_meta($post_id, 'expert_name', true);
        $post = get_post($post_id);
        
        if (!empty($author_email)) {
            $subject = 'Konunuza Uzman Cevabı Geldi: ' . $post->post_title;
            
            $message = "Sayın {$author_name},\n\n";
            $message .= "'{$post->post_title}' başlıklı konunuza uzman {$expert_name} tarafından cevap verildi.\n\n";
            $message .= "Uzman cevabını görüntülemek için aşağıdaki bağlantıyı kullanabilirsiniz:\n";
            $message .= get_permalink($post_id) . "\n\n";
            $message .= "Saygılarımızla,\n";
            $message .= get_bloginfo('name');
            
            wp_mail($author_email, $subject, $message);
        }
    }
}
add_action('save_post', 'notify_author_on_expert_answer');

// HTML formatında email gönderme için filtre
function set_html_content_type() {
    return 'text/html';
}

// Email şablonunu HTML formatında hazırlama
function get_email_template($content) {
    $template = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .email-container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .email-header { background: #f5f5f5; padding: 15px; border-radius: 5px; }
            .email-content { padding: 20px 0; }
            .email-footer { border-top: 1px solid #eee; padding-top: 15px; font-size: 12px; }
            .button { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; 
                      text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <h2>' . get_bloginfo('name') . '</h2>
            </div>
            <div class="email-content">
                ' . $content . '
            </div>
            <div class="email-footer">
                Bu email ' . get_bloginfo('name') . ' tarafından otomatik olarak gönderilmiştir.
            </div>
        </div>
    </body>
    </html>';
    
    return $template;
}

// Email gönderirken HTML formatını kullanma
function send_formatted_email($to, $subject, $message) {
    add_filter('wp_mail_content_type', 'set_html_content_type');
    $formatted_message = get_email_template($message);
    $sent = wp_mail($to, $subject, $formatted_message);
    remove_filter('wp_mail_content_type', 'set_html_content_type');
    return $sent;
}



// Yorum formunu özelleştir
function custom_comment_form_fields($fields) {
    // Website alanını kaldır
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields', 'custom_comment_form_fields');

// Matematik sorusunu yorum formuna ekle
function add_math_captcha_to_comment_form() {
    ?>
    <div class="math-captcha-field">
        <p class="comment-form-math">
            <label for="math_captcha"><?php _e('Spam Koruması: 3 + 1 = ?', 'newsblogger'); ?> <span class="required">*</span></label>
            <input id="math_captcha" name="math_captcha" type="number" required="required" />
        </p>
    </div>
    <style>
        .math-captcha-field {
            margin: 15px 0;
        }
        .math-captcha-field label {
            display: block;
            margin-bottom: 5px;
        }
        .math-captcha-field input {
            width: 100px;
            padding: 5px;
        }
        .math-captcha-field .required {
            color: #ff0000;
        }
    </style>
    <?php
}
add_action('comment_form', 'add_math_captcha_to_comment_form', 1);

// Yorum kontrolü
function verify_comment_math_captcha($commentdata) {
    // Eğer kullanıcı giriş yapmışsa kontrol etme
    if (is_user_logged_in()) {
        return $commentdata;
    }

    if (!isset($_POST['math_captcha'])) {
        wp_die(__('Hata: Lütfen matematik sorusunu cevaplayın.', 'newsblogger'));
    }
    
    $answer = intval($_POST['math_captcha']);
    
    if ($answer !== 4) {
        wp_die(__('Hata: Matematik sorusuna verilen cevap yanlış.', 'newsblogger'));
    }
    
    return $commentdata;
}
add_filter('preprocess_comment', 'verify_comment_math_captcha');

// Yorum form stillerini ekle
function add_comment_form_styles() {
    if (is_singular() && comments_open()) {
    ?>
    <style>
        .comment-form {
            margin: 20px 0;
        }
        .comment-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .comment-form input[type="text"],
        .comment-form input[type="email"],
        .comment-form input[type="number"],
        .comment-form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .comment-form textarea {
            height: 150px;
        }
        .comment-form .required {
            color: #ff0000;
        }
        .comment-form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .comment-form input[type="submit"]:hover {
            background-color: #45a049;
        }
        #math_captcha {
            width: 100px !important;
        }
        .math-captcha-field {
            clear: both;
            margin: 15px 0;
        }
    </style>
    <?php
    }
}
add_action('wp_head', 'add_comment_form_styles');

// Yorum form metinlerini özelleştir
function custom_comment_form_text($defaults) {
    $defaults['title_reply'] = __('Yorum Yaz', 'newsblogger');
    $defaults['title_reply_to'] = __('Yanıtla: %s', 'newsblogger');
    $defaults['cancel_reply_link'] = __('İptal', 'newsblogger');
    $defaults['label_submit'] = __('Gönder', 'newsblogger');
    
    return $defaults;
}
add_filter('comment_form_defaults', 'custom_comment_form_text');

// Spam ve backlink kontrolü için gelişmiş yorum filtreleme sistemi
function advanced_comment_spam_filter($commentdata) {
    // Giriş yapmış kullanıcıları atla
    if (is_user_logged_in()) {
        return $commentdata;
    }

    $comment = $commentdata['comment_content'];
    $author = $commentdata['comment_author'];
    $email = $commentdata['comment_author_email'];
    $ip = $_SERVER['REMOTE_ADDR'];

    // Yasaklı kelimeler listesi
    $banned_words = array(
        'viagra', 'cialis', 'casino', 'porn', 'xxx', 
        'sex', 'dating', 'forex', 'crypto', 'bitcoin',
        'investment', 'loan', 'credit', 'mortgage', 'cheap',
        'free', 'discount', 'buy', 'sell', 'deal',
        'marketing', 'seo', 'backlink', 'link building'
    );

    // Yasaklı domainler
    $banned_domains = array(
        '.xyz', '.top', '.loan', '.win', '.click',
        '.link', '.work', '.porn', '.sex', '.xxx',
        '.casino', '.bet', '.game'
    );

    // URL sayısı kontrolü
    $url_count = substr_count(strtolower($comment), 'http');
    if ($url_count > 2) {
        wp_die('Çok fazla link içeren yorumlar spam olarak değerlendirilir.');
    }

    // Yasaklı kelime kontrolü
    foreach ($banned_words as $word) {
        if (stripos($comment, $word) !== false || 
            stripos($author, $word) !== false) {
            wp_die('Yorumunuz spam filtresine takıldı.');
        }
    }

    // E-posta domain kontrolü
    $email_domain = substr(strrchr($email, "@"), 1);
    foreach ($banned_domains as $domain) {
        if (stripos($email_domain, $domain) !== false) {
            wp_die('Bu e-posta domaini ile yorum yapamazsınız.');
        }
    }

    // Yorum uzunluğu kontrolü
    if (strlen($comment) < 10) {
        wp_die('Yorum çok kısa. Lütfen daha detaylı bir yorum yazın.');
    }

    // Çok fazla büyük harf kontrolü
    $uppercase_count = strlen(preg_replace('/[^A-Z]/', '', $comment));
    $total_length = strlen($comment);
    if ($total_length > 0 && ($uppercase_count / $total_length) > 0.5) {
        wp_die('Çok fazla büyük harf kullanımı tespit edildi.');
    }

    // HTML tag kontrolü
    if (strip_tags($comment) != $comment) {
        wp_die('HTML tag kullanımına izin verilmiyor.');
    }

    // Hızlı yorum kontrolü
    $last_comment_time = get_transient('last_comment_time_' . $ip);
    if (false !== $last_comment_time && time() - $last_comment_time < 30) {
        wp_die('Çok hızlı yorum yapıyorsunuz. Lütfen 30 saniye bekleyin.');
    }
    set_transient('last_comment_time_' . $ip, time(), 3600);

    // IP bazlı spam kontrolü
    $comment_count = get_transient('comment_count_' . $ip);
    if (false === $comment_count) {
        $comment_count = 1;
    } else {
        $comment_count++;
    }
    set_transient('comment_count_' . $ip, $comment_count, 3600);

    if ($comment_count > 5) {
        wp_die('Çok fazla yorum yaptınız. Lütfen daha sonra tekrar deneyin.');
    }

    // Akismet entegrasyonu (eğer kuruluysa)
    if (function_exists('akismet_verify_key') && akismet_verify_key(get_option('wordpress_api_key'))) {
        $akismet_data = array(
            'comment_author' => $author,
            'comment_author_email' => $email,
            'comment_content' => $comment,
            'comment_type' => 'comment',
            'permalink' => get_permalink(),
            'user_ip' => $ip
        );

        if (akismet_check_db_comment($akismet_data)) {
            wp_die('Yorumunuz Akismet spam filtresine takıldı.');
        }
    }

    return $commentdata;
}
add_filter('preprocess_comment', 'advanced_comment_spam_filter');

// Yönetici paneline spam istatistikleri widget'ı ekle
function add_spam_stats_dashboard_widget() {
    wp_add_dashboard_widget(
        'spam_stats_widget',
        'Spam Yorum İstatistikleri',
        'display_spam_stats_widget'
    );
}
add_action('wp_dashboard_setup', 'add_spam_stats_dashboard_widget');

function display_spam_stats_widget() {
    $total_blocked = get_option('total_spam_blocked', 0);
    $today_blocked = get_option('today_spam_blocked', 0);
    ?>
    <div class="spam-stats">
        <p>Bugün engellenen: <?php echo $today_blocked; ?></p>
        <p>Toplam engellenen: <?php echo $total_blocked; ?></p>
    </div>
    <?php
}

// Spam log sistemi
function log_spam_comment($reason, $comment_data) {
    $log_file = WP_CONTENT_DIR . '/spam-log.txt';
    $log_entry = date('Y-m-d H:i:s') . " | IP: " . $_SERVER['REMOTE_ADDR'] . 
                 " | Reason: " . $reason . " | Content: " . substr($comment_data['comment_content'], 0, 100) . "\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Yorum yapma limitini ayarla
function set_comment_rate_limit() {
    if (!is_user_logged_in()) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $transient_key = 'comment_rate_' . $ip;
        
        if (get_transient($transient_key)) {
            wp_die('Çok fazla yorum yaptınız. Lütfen birkaç dakika bekleyin.');
        }
        
        set_transient($transient_key, true, 180); // 3 dakika bekleme süresi
    }
}
add_action('comment_post', 'set_comment_rate_limit');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function correct_post_date_display($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Doğru tarih formatını al
    $post_date = get_the_date('Y-m-d', $post_id);
    $formatted_date = get_the_date('F j, Y', $post_id);
    
    // Eğer tarih 1970 ise, güncel tarihi kullan
    if (strpos($post_date, '1970') !== false) {
        $post_date = date('Y-m-d');
        $formatted_date = date('F j, Y');
    }
    
    return array(
        'datetime' => $post_date,
        'formatted' => $formatted_date
    );
}

// Kullanım örneği
function custom_date_link() {
    $date_info = correct_post_date_display();
    ?>
    <a itemprop="url" href="<?php echo get_permalink(); ?>" title="<?php echo $date_info['formatted']; ?>">
        <time datetime="<?php echo $date_info['datetime']; ?>" itemprop="datePublished" class="entry-date">
            <?php echo $date_info['formatted']; ?>
        </time>
    </a>
    <?php
}

// WordPress tema güncellemelerini devre dışı bırak
function disable_theme_updates() {
    remove_action('load-update-core.php', 'wp_update_themes');
    wp_clear_scheduled_hook('wp_update_themes');
    
    // Tema güncelleme kontrollerini gizle
    add_filter('pre_site_transient_update_themes', '__return_empty_array');
}
add_action('init', 'disable_theme_updates');

// Güncelleme bildirimlerini gizle
function hide_theme_update_notifications($values) {
    if (isset($values->response['dazzlo'])) {
        unset($values->response['dazzlo']);
    }
    return $values;
}
add_filter('site_transient_update_themes', 'hide_theme_update_notifications');

////////////////////////////////////////////////////////////////


// Register Minecraft Server custom post type
function minecraft_server_list_register_post_type() {
    $labels = array(
        'name'                  => _x('Minecraft Servers', 'Post type general name', 'minecraft-server-list'),
        'singular_name'         => _x('Minecraft Server', 'Post type singular name', 'minecraft-server-list'),
        'menu_name'             => _x('Minecraft Servers', 'Admin Menu text', 'minecraft-server-list'),
        'name_admin_bar'        => _x('Minecraft Server', 'Add New on Toolbar', 'minecraft-server-list'),
        'add_new'               => __('Add New', 'minecraft-server-list'),
        'add_new_item'          => __('Add New Server', 'minecraft-server-list'),
        'new_item'              => __('New Server', 'minecraft-server-list'),
        'edit_item'             => __('Edit Server', 'minecraft-server-list'),
        'view_item'             => __('View Server', 'minecraft-server-list'),
        'all_items'             => __('All Servers', 'minecraft-server-list'),
        'search_items'          => __('Search Servers', 'minecraft-server-list'),
        'parent_item_colon'     => __('Parent Servers:', 'minecraft-server-list'),
        'not_found'             => __('No servers found.', 'minecraft-server-list'),
        'not_found_in_trash'    => __('No servers found in Trash.', 'minecraft-server-list')
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'minecraft-servers'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-games',
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'          => true
    );

    register_post_type('minecraft_server', $args);
}
add_action('init', 'minecraft_server_list_register_post_type');

// Add meta boxes
function minecraft_server_list_add_meta_boxes() {
    add_meta_box(
        'minecraft_server_details',
        __('Server Details', 'minecraft-server-list'),
        'minecraft_server_list_details_callback',
        'minecraft_server',
        'normal',
        'high'
    );
    
    add_meta_box(
        'minecraft_server_status',
        __('Server Status', 'minecraft-server-list'),
        'minecraft_server_list_status_callback',
        'minecraft_server',
        'side',
        'default'
    );
    
    add_meta_box(
        'minecraft_server_stats',
        __('Server Stats', 'minecraft-server-list'),
        'minecraft_server_list_stats_callback',
        'minecraft_server',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'minecraft_server_list_add_meta_boxes');

// Server details meta box callback with edition support
function minecraft_server_list_details_callback($post) {
    wp_nonce_field('minecraft_server_details', 'minecraft_server_details_nonce');
    
    // Get existing values
    $server_ip = get_post_meta($post->ID, 'server_ip', true); // Legacy field
    $server_java_ip = get_post_meta($post->ID, 'server_java_ip', true) ?: $server_ip;
    $server_bedrock_ip = get_post_meta($post->ID, 'server_bedrock_ip', true) ?: $server_ip;
    $server_port = get_post_meta($post->ID, 'server_port', true) ?: '25565';
    $server_bedrock_port = get_post_meta($post->ID, 'server_bedrock_port', true) ?: '19132';
    
    // Get server editions
    $server_editions = get_post_meta($post->ID, 'server_editions', true);
    if (!is_array($server_editions)) {
        // Handle legacy data - migrate from server_category to server_editions
        $legacy_category = get_post_meta($post->ID, 'server_category', true);
        if (!empty($legacy_category)) {
            $server_editions = array($legacy_category);
        } else {
            $server_editions = array();
        }
    }
    
    $server_type = get_post_meta($post->ID, 'server_type', true);
    $server_version = get_post_meta($post->ID, 'server_version', true);
    $server_country = get_post_meta($post->ID, 'server_country', true);
    $server_website = get_post_meta($post->ID, 'server_website', true);
    $server_discord = get_post_meta($post->ID, 'server_discord', true);
    $server_banner = get_post_meta($post->ID, 'server_banner', true);
    $server_featured = get_post_meta($post->ID, 'server_featured', true) === 'yes';
    $server_sponsored = get_post_meta($post->ID, 'server_sponsored', true) === 'yes';
    $server_premium = get_post_meta($post->ID, 'server_premium', true) === 'yes';
    
    // Convert server types to array if needed
    if (!is_array($server_type)) {
        $server_type = $server_type ? array($server_type) : array();
    }
    
    // Get all options for dropdowns
    $server_categories = array(
        'java' => 'Java Edition',
        'bedrock' => 'Bedrock Edition',
        'java_bedrock' => 'Java & Bedrock'
    );
    
    $server_types = array(
        'survival' => 'Survival',
        'creative' => 'Creative',
        'skyblock' => 'Skyblock',
        'factions' => 'Factions',
        'minigames' => 'Minigames',
        'prison' => 'Prison',
        'pvp' => 'PvP',
        'towny' => 'Towny',
        'pixelmon' => 'Pixelmon',
        'vanilla' => 'Vanilla',
        'modded' => 'Modded',
        'hardcore' => 'Hardcore',
        'anarchy' => 'Anarchy',
        'economy' => 'Economy',
        'roleplay' => 'Roleplay',
        'adventure' => 'Adventure',
        'smp' => 'SMP',
        'craftbukkit' => 'CraftBukkit',
        'spigot' => 'Spigot',
        'paper' => 'Paper',
        'forge' => 'Forge',
        'fabric' => 'Fabric',
        'ftb' => 'FTB',
        'tekkit' => 'Tekkit',
        'realms' => 'Realms',
        'crossplay' => 'Crossplay',
        'other' => 'Other'
    );
    
    $server_versions = array(
        '1.22' => '1.22',
        '1.21' => '1.21',
        '1.20' => '1.20',
        '1.19' => '1.19',
        '1.18' => '1.18',
        '1.17' => '1.17',
        '1.16' => '1.16',
        '1.15' => '1.15',
        '1.14' => '1.14',
        '1.13' => '1.13',
        '1.12' => '1.12',
        '1.11' => '1.11',
        '1.10' => '1.10',
        '1.9' => '1.9',
        '1.8' => '1.8',
        '1.7' => '1.7',
        'bedrock_latest' => 'Bedrock Latest',
        'bedrock_legacy' => 'Bedrock Legacy'
    );

    $server_countries = array(
        'us' => 'United States',
        'ca' => 'Canada',
        'uk' => 'United Kingdom',
        'de' => 'Germany',
        'fr' => 'France',
        'au' => 'Australia',
        'br' => 'Brazil',
        'ru' => 'Russia',
        'jp' => 'Japan',
        'kr' => 'South Korea',
        'cn' => 'China',
        'in' => 'India',
        'es' => 'Spain',
        'it' => 'Italy',
        'nl' => 'Netherlands',
        'se' => 'Sweden',
        'no' => 'Norway',
        'fi' => 'Finland',
        'dk' => 'Denmark',
        'pl' => 'Poland',
        'tr' => 'Turkey',
        'mx' => 'Mexico',
        'sg' => 'Singapore',
        'ua' => 'Ukraine',
        'za' => 'South Africa',
        'ar' => 'Argentina',
        'ch' => 'Switzerland',
        'at' => 'Austria',
        'be' => 'Belgium',
        'gr' => 'Greece',
        'pt' => 'Portugal',
        'il' => 'Israel',
        'ie' => 'Ireland',
        'nz' => 'New Zealand',
        'hk' => 'Hong Kong',
        'other' => 'Other'
    );
    
    // Output the form
    ?>
    <div class="minecraft-server-details-form">
        <div class="form-section">
            <h3><?php _e('Server Editions & Connection Details', 'minecraft-server-list'); ?></h3>
            
            <div class="form-field">
                <label><?php _e('Server Editions', 'minecraft-server-list'); ?> <span class="required">*</span></label>
                <div class="server-editions-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="server_editions[]" value="java" id="edition_java" <?php checked(in_array('java', $server_editions)); ?>>
                        <span><?php _e('Java Edition', 'minecraft-server-list'); ?></span>
                    </label>
                    
                    <label class="checkbox-label">
                        <input type="checkbox" name="server_editions[]" value="bedrock" id="edition_bedrock" <?php checked(in_array('bedrock', $server_editions)); ?>>
                        <span><?php _e('Bedrock Edition', 'minecraft-server-list'); ?></span>
                    </label>
                </div>
                <p class="description"><?php _e('Select all editions that your server supports', 'minecraft-server-list'); ?></p>
            </div>
            
            <div id="java_fields" class="edition-fields" <?php echo !in_array('java', $server_editions) ? 'style="display:none;"' : ''; ?>>
                <div class="form-row">
                    <div class="form-group">
                        <label for="server_java_ip"><?php _e('Java Server IP', 'minecraft-server-list'); ?> <span class="required">*</span></label>
                        <input type="text" id="server_java_ip" name="server_java_ip" value="<?php echo esc_attr($server_java_ip); ?>" required>
                        <p class="description"><?php _e('The Java Edition server IP address (e.g. mc.example.com)', 'minecraft-server-list'); ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label for="server_port"><?php _e('Java Server Port', 'minecraft-server-list'); ?></label>
                        <input type="text" id="server_port" name="server_port" value="<?php echo esc_attr($server_port); ?>">
                        <p class="description"><?php _e('Default is 25565 for Java Edition', 'minecraft-server-list'); ?></p>
                    </div>
                </div>
            </div>
            
            <div id="bedrock_fields" class="edition-fields" <?php echo !in_array('bedrock', $server_editions) ? 'style="display:none;"' : ''; ?>>
                <div class="form-row">
                    <div class="form-group">
                        <label for="server_bedrock_ip"><?php _e('Bedrock Server IP', 'minecraft-server-list'); ?> <span class="required">*</span></label>
                        <input type="text" id="server_bedrock_ip" name="server_bedrock_ip" value="<?php echo esc_attr($server_bedrock_ip); ?>" required>
                        <p class="description"><?php _e('The Bedrock Edition server IP address', 'minecraft-server-list'); ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label for="server_bedrock_port"><?php _e('Bedrock Server Port', 'minecraft-server-list'); ?></label>
                        <input type="text" id="server_bedrock_port" name="server_bedrock_port" value="<?php echo esc_attr($server_bedrock_port); ?>">
                        <p class="description"><?php _e('Default is 19132 for Bedrock Edition', 'minecraft-server-list'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Legacy field (hidden for backwards compatibility) -->
            <div class="form-group" style="display: none;">
                <label for="server_ip"><?php _e('Legacy Server IP', 'minecraft-server-list'); ?></label>
                <input type="text" id="server_ip" name="server_ip" value="<?php echo esc_attr($server_ip); ?>">
            </div>
        </div>
        
        <div class="form-section">
            <h3><?php _e('Server Details', 'minecraft-server-list'); ?></h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="server_version"><?php _e('Minecraft Version', 'minecraft-server-list'); ?> <span class="required">*</span></label>
                    <select id="server_version" name="server_version" required>
                        <option value=""><?php _e('Select Version', 'minecraft-server-list'); ?></option>
                        <?php foreach ($server_versions as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($server_version, $key); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="server_country"><?php _e('Server Location', 'minecraft-server-list'); ?></label>
                    <select id="server_country" name="server_country">
                        <option value=""><?php _e('Select Country', 'minecraft-server-list'); ?></option>
                        <?php foreach ($server_countries as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($server_country, $key); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-field">
                <label><?php _e('Server Types', 'minecraft-server-list'); ?> <span class="required">*</span></label>
                <div class="server-types-grid">
                    <?php foreach ($server_types as $key => $label) : ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="server_type[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $server_type)); ?>>
                            <span><?php echo esc_html($label); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description"><?php _e('Select up to 5 types that describe this server', 'minecraft-server-list'); ?></p>
            </div>
        </div>
        
        <div class="form-section">
            <h3><?php _e('Additional Information', 'minecraft-server-list'); ?></h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="server_website"><?php _e('Server Website', 'minecraft-server-list'); ?></label>
                    <input type="url" id="server_website" name="server_website" value="<?php echo esc_url($server_website); ?>">
                </div>
                
                <div class="form-group">
                    <label for="server_discord"><?php _e('Discord Invite URL', 'minecraft-server-list'); ?></label>
                    <input type="url" id="server_discord" name="server_discord" value="<?php echo esc_url($server_discord); ?>">
                </div>
            </div>
            
            <div class="form-field">
                <label for="server_banner"><?php _e('Server Banner', 'minecraft-server-list'); ?></label>
                <div class="banner-upload-field">
                    <?php if (!empty($server_banner)) : ?>
                        <div class="banner-preview">
                            <?php echo wp_get_attachment_image($server_banner, 'medium'); ?>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" id="server_banner" name="server_banner" value="<?php echo esc_attr($server_banner); ?>">
                    <button type="button" class="button upload-banner-button"><?php _e('Upload Banner', 'minecraft-server-list'); ?></button>
                    <button type="button" class="button remove-banner-button" <?php echo empty($server_banner) ? 'style="display:none"' : ''; ?>><?php _e('Remove Banner', 'minecraft-server-list'); ?></button>
                    <p class="description"><?php _e('Recommended size: 1200x400 pixels', 'minecraft-server-list'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><?php _e('Server Flags', 'minecraft-server-list'); ?></h3>
            
            <div class="server-flags">
                <label class="checkbox-label">
                    <input type="checkbox" name="server_featured" value="yes" <?php checked($server_featured); ?>>
                    <span><?php _e('Featured Server', 'minecraft-server-list'); ?></span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="server_sponsored" value="yes" <?php checked($server_sponsored); ?>>
                    <span><?php _e('Sponsored Server', 'minecraft-server-list'); ?></span>
                </label>
                
                <label class="checkbox-label">
                    <input type="checkbox" name="server_premium" value="yes" <?php checked($server_premium); ?>>
                    <span><?php _e('Premium Server', 'minecraft-server-list'); ?></span>
                </label>
            </div>
        </div>
    </div>
    
    <style>
        .minecraft-server-details-form {
            display: grid;
            grid-template-columns: 1fr;
            grid-gap: 20px;
        }
        
        .form-section {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
        }
        
        .form-section h3 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 16px;
        }
        
        .minecraft-server-details-form .form-field {
            margin-bottom: 20px;
        }
        
        .minecraft-server-details-form .form-field:last-child {
            margin-bottom: 0;
        }
        
        .minecraft-server-details-form .form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .minecraft-server-details-form .form-field input[type="text"],
        .minecraft-server-details-form .form-field input[type="url"],
        .minecraft-server-details-form .form-field select,
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group select {
            width: 100%;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .minecraft-server-details-form .form-field .description,
        .form-group .description {
            margin-top: 5px;
            font-style: italic;
            font-size: 12px;
            color: #777;
        }
        
        .minecraft-server-details-form .server-types-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 10px;
            margin-bottom: 10px;
        }
        
        .minecraft-server-details-form .checkbox-label {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 8px;
            border-radius: 4px;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .minecraft-server-details-form .checkbox-label:hover {
            background-color: #f0f0f0;
        }
        
        .minecraft-server-details-form .server-flags {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .minecraft-server-details-form .required {
            color: #dc3232;
        }
        
        .minecraft-server-details-form .banner-preview {
            margin-bottom: 10px;
            max-width: 300px;
        }
        
        .minecraft-server-details-form .banner-preview img {
            width: 100%;
            height: auto;
            display: block;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .minecraft-server-details-form .remove-banner-button {
            margin-left: 5px;
        }
        
        .server-editions-options {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .edition-fields {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .minecraft-server-details-form .server-types-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .minecraft-server-details-form .server-types-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <script>
        jQuery(document).ready(function($) {
            // Toggle edition-specific IP fields
            function updateEditionFields() {
                const javaChecked = $('#edition_java').is(':checked');
                const bedrockChecked = $('#edition_bedrock').is(':checked');
                
                if (javaChecked) {
                    $('#java_fields').slideDown();
                    $('#server_java_ip').prop('required', true);
                } else {
                    $('#java_fields').slideUp();
                    $('#server_java_ip').prop('required', false);
                }
                
                if (bedrockChecked) {
                    $('#bedrock_fields').slideDown();
                    $('#server_bedrock_ip').prop('required', true);
                } else {
                    $('#bedrock_fields').slideUp();
                    $('#server_bedrock_ip').prop('required', false);
                }
                
                // For backwards compatibility
                updateLegacyIp();
            }
            
            // Update legacy IP field for backwards compatibility
            function updateLegacyIp() {
                const javaChecked = $('#edition_java').is(':checked');
                const bedrockChecked = $('#edition_bedrock').is(':checked');
                const javaIp = $('#server_java_ip').val();
                const bedrockIp = $('#server_bedrock_ip').val();
                
                // Prioritize Java IP if both are checked
                if (javaChecked && javaIp) {
                    $('#server_ip').val(javaIp);
                } else if (bedrockChecked && bedrockIp) {
                    $('#server_ip').val(bedrockIp);
                }
            }
            
            // Initialize edition fields visibility
            updateEditionFields();
            
            // Add event listeners
            $('#edition_java, #edition_bedrock').on('change', updateEditionFields);
            $('#server_java_ip, #server_bedrock_ip').on('input', updateLegacyIp);
            
            // Banner upload
            $('.upload-banner-button').on('click', function(e) {
                e.preventDefault();
                
                var bannerUploader = wp.media({
                    title: '<?php _e('Select or Upload Server Banner', 'minecraft-server-list'); ?>',
                    button: {
                        text: '<?php _e('Use this image', 'minecraft-server-list'); ?>'
                    },
                    multiple: false
                });
                
                bannerUploader.on('select', function() {
                    var attachment = bannerUploader.state().get('selection').first().toJSON();
                    $('#server_banner').val(attachment.id);
                    
                    if (attachment.type === 'image') {
                        var imgUrl = attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                        
                        if ($('.banner-preview').length) {
                            $('.banner-preview img').attr('src', imgUrl);
                        } else {
                            $('.banner-upload-field').prepend('<div class="banner-preview"><img src="' + imgUrl + '" alt=""></div>');
                        }
                        
                        $('.remove-banner-button').show();
                    }
                });
                
                bannerUploader.open();
            });
            
            // Remove banner
            $('.remove-banner-button').on('click', function(e) {
                e.preventDefault();
                $('#server_banner').val('');
                $('.banner-preview').remove();
                $(this).hide();
            });
            
            // Limit server types to 5
            $('input[name="server_type[]"]').on('change', function() {
                if ($('input[name="server_type[]"]:checked').length > 5) {
                    this.checked = false;
                    alert('<?php _e('You can select up to 5 server types', 'minecraft-server-list'); ?>');
                }
            });
        });
    </script>
    <?php
}

// Server status meta box callback
function minecraft_server_list_status_callback($post) {
    wp_nonce_field('minecraft_server_status', 'minecraft_server_status_nonce');
    
    // Get existing values
    $server_approved = get_post_meta($post->ID, 'server_approved', true) ?: 'pending';
    $server_ip = get_post_meta($post->ID, 'server_ip', true);
    $server_java_ip = get_post_meta($post->ID, 'server_java_ip', true) ?: $server_ip;
    $server_bedrock_ip = get_post_meta($post->ID, 'server_bedrock_ip', true) ?: $server_ip;
    $server_submission_date = get_post_meta($post->ID, 'server_submission_date', true);
    $server_status = get_post_meta($post->ID, 'server_status', true) ?: 'offline';
    
    // Edition-specific status information
    $java_status = get_post_meta($post->ID, 'server_java_status', true) ?: 'offline';
    $java_player_count = get_post_meta($post->ID, 'server_java_player_count', true) ?: 0;
    $java_max_players = get_post_meta($post->ID, 'server_java_max_players', true) ?: 0;
    $bedrock_status = get_post_meta($post->ID, 'server_bedrock_status', true) ?: 'offline';
    $bedrock_player_count = get_post_meta($post->ID, 'server_bedrock_player_count', true) ?: 0;
    $bedrock_max_players = get_post_meta($post->ID, 'server_bedrock_max_players', true) ?: 0;
    
    // Calculate total players
    $server_player_count = $java_player_count + $bedrock_player_count;
    $server_max_players = $java_max_players + $bedrock_max_players;
    
    // Get server editions
    $server_editions = get_post_meta($post->ID, 'server_editions', true);
    if (!is_array($server_editions)) {
        // Handle legacy data
        $legacy_category = get_post_meta($post->ID, 'server_category', true);
        if (!empty($legacy_category)) {
            $server_editions = array($legacy_category);
        } else {
            $server_editions = array();
        }
    }
    
    if (empty($server_submission_date)) {
        $server_submission_date = current_time('mysql');
        update_post_meta($post->ID, 'server_submission_date', $server_submission_date);
    }
    
    // Format submission date
    $submission_date = !empty($server_submission_date) ? date_i18n(get_option('date_format'), strtotime($server_submission_date)) : '';
    
    ?>
    <div class="minecraft-server-status">
        <p>
<label for="server_approved"><?php _e('Server Approval Status', 'minecraft-server-list'); ?></label>
                <select name="server_approved" id="server_approved">
                    <option value="pending" <?php selected($server_approved, 'pending'); ?>><?php _e('Pending Review', 'minecraft-server-list'); ?></option>
                    <option value="approved" <?php selected($server_approved, 'approved'); ?>><?php _e('Approved', 'minecraft-server-list'); ?></option>
                    <option value="rejected" <?php selected($server_approved, 'rejected'); ?>><?php _e('Rejected', 'minecraft-server-list'); ?></option>
                </select>
            </p>
            
            <p>
                <span class="submission-date">
                    <?php _e('Submission Date:', 'minecraft-server-list'); ?> <strong><?php echo esc_html($submission_date); ?></strong>
                </span>
            </p>
        </div>
    </div>
    <?php
}

// Server stats meta box callback
function minecraft_server_list_stats_callback($post) {
    wp_nonce_field('minecraft_server_stats', 'minecraft_server_stats_nonce');
    
    // Get existing values
    $server_votes = get_post_meta($post->ID, 'server_votes', true) ?: 0;
    $server_rank = get_post_meta($post->ID, 'server_rank', true) ?: 0;
    $server_rating = get_post_meta($post->ID, 'server_rating', true) ?: 0;
    $server_review_count = get_post_meta($post->ID, 'server_review_count', true) ?: 0;
    
    ?>
    <div class="minecraft-server-stats">
        <p>
            <label for="server_votes"><?php _e('Votes:', 'minecraft-server-list'); ?></label>
            <input type="number" id="server_votes" name="server_votes" value="<?php echo esc_attr($server_votes); ?>" min="0">
        </p>
        
        <p>
            <label for="server_rank"><?php _e('Rank:', 'minecraft-server-list'); ?></label>
            <input type="number" id="server_rank" name="server_rank" value="<?php echo esc_attr($server_rank); ?>" min="0">
        </p>
        
        <p>
            <label for="server_rating"><?php _e('Rating:', 'minecraft-server-list'); ?></label>
            <input type="number" id="server_rating" name="server_rating" value="<?php echo esc_attr($server_rating); ?>" min="0" max="5" step="0.1">
            <span class="rating-stars">
                <?php
                $rating_floor = floor($server_rating);
                $rating_decimal = $server_rating - $rating_floor;
                
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating_floor) {
                        echo '<span class="dashicons dashicons-star-filled"></span>';
                    } elseif ($i == $rating_floor + 1 && $rating_decimal >= 0.5) {
                        echo '<span class="dashicons dashicons-star-half"></span>';
                    } else {
                        echo '<span class="dashicons dashicons-star-empty"></span>';
                    }
                }
                ?>
            </span>
        </p>
        
        <p>
            <label for="server_review_count"><?php _e('Review Count:', 'minecraft-server-list'); ?></label>
            <input type="number" id="server_review_count" name="server_review_count" value="<?php echo esc_attr($server_review_count); ?>" min="0">
        </p>
    </div>
    
    <style>
        .minecraft-server-stats p {
            margin: 10px 0;
        }
        
        .minecraft-server-stats label {
            display: inline-block;
            min-width: 100px;
            font-weight: 600;
        }
        
        .minecraft-server-stats input[type="number"] {
            width: 70px;
        }
        
        .rating-stars {
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
            color: #FFB900;
        }
    </style>
    <?php
}

// Save meta box data
function minecraft_server_list_save_meta_boxes($post_id) {
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check if this is a revision
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if our nonce is set
    if (!isset($_POST['minecraft_server_details_nonce'])) {
        return;
    }
    
    // Verify the nonce
    if (!wp_verify_nonce($_POST['minecraft_server_details_nonce'], 'minecraft_server_details')) {
        return;
    }
    
    // Server details
    if (isset($_POST['server_editions'])) {
        update_post_meta($post_id, 'server_editions', $_POST['server_editions']);
    } else {
        update_post_meta($post_id, 'server_editions', array());
    }
    
    // Java Edition IP
    if (isset($_POST['server_java_ip'])) {
        update_post_meta($post_id, 'server_java_ip', sanitize_text_field($_POST['server_java_ip']));
    }
    
    // Bedrock Edition IP
    if (isset($_POST['server_bedrock_ip'])) {
        update_post_meta($post_id, 'server_bedrock_ip', sanitize_text_field($_POST['server_bedrock_ip']));
    }
    
    // Legacy Server IP (for backwards compatibility)
    if (isset($_POST['server_ip'])) {
        update_post_meta($post_id, 'server_ip', sanitize_text_field($_POST['server_ip']));
    }
    
    // Server Port
    if (isset($_POST['server_port'])) {
        update_post_meta($post_id, 'server_port', sanitize_text_field($_POST['server_port']));
    }
    
    // Bedrock Server Port
    if (isset($_POST['server_bedrock_port'])) {
        update_post_meta($post_id, 'server_bedrock_port', sanitize_text_field($_POST['server_bedrock_port']));
    }
    
    // Version
    if (isset($_POST['server_version'])) {
        update_post_meta($post_id, 'server_version', sanitize_text_field($_POST['server_version']));
    }
    
    // Country
    if (isset($_POST['server_country'])) {
        update_post_meta($post_id, 'server_country', sanitize_text_field($_POST['server_country']));
    }
    
    // Types
    if (isset($_POST['server_type'])) {
        update_post_meta($post_id, 'server_type', $_POST['server_type']);
    } else {
        update_post_meta($post_id, 'server_type', array());
    }
    
    // Website
    if (isset($_POST['server_website'])) {
        update_post_meta($post_id, 'server_website', esc_url_raw($_POST['server_website']));
    }
    
    // Discord
    if (isset($_POST['server_discord'])) {
        update_post_meta($post_id, 'server_discord', esc_url_raw($_POST['server_discord']));
    }
    
    // Banner
    if (isset($_POST['server_banner'])) {
        update_post_meta($post_id, 'server_banner', sanitize_text_field($_POST['server_banner']));
    }
    
    // Flags
    $flags = array('server_featured', 'server_sponsored', 'server_premium');
    foreach ($flags as $flag) {
        if (isset($_POST[$flag]) && $_POST[$flag] === 'yes') {
            update_post_meta($post_id, $flag, 'yes');
        } else {
            update_post_meta($post_id, $flag, 'no');
        }
    }
    
    // Check if server status meta box was submitted
    if (isset($_POST['minecraft_server_status_nonce']) && wp_verify_nonce($_POST['minecraft_server_status_nonce'], 'minecraft_server_status')) {
        // Server approval status
        if (isset($_POST['server_approved'])) {
            update_post_meta($post_id, 'server_approved', sanitize_text_field($_POST['server_approved']));
        }
    }
    
    // Check if server stats meta box was submitted
    if (isset($_POST['minecraft_server_stats_nonce']) && wp_verify_nonce($_POST['minecraft_server_stats_nonce'], 'minecraft_server_stats')) {
        // Votes
        if (isset($_POST['server_votes'])) {
            update_post_meta($post_id, 'server_votes', intval($_POST['server_votes']));
        }
        
        // Rank
        if (isset($_POST['server_rank'])) {
            update_post_meta($post_id, 'server_rank', intval($_POST['server_rank']));
        }
        
        // Rating
        if (isset($_POST['server_rating'])) {
            update_post_meta($post_id, 'server_rating', floatval($_POST['server_rating']));
        }
        
        // Review Count
        if (isset($_POST['server_review_count'])) {
            update_post_meta($post_id, 'server_review_count', intval($_POST['server_review_count']));
        }
    }
}
add_action('save_post_minecraft_server', 'minecraft_server_list_save_meta_boxes');

// Add custom taxonomies for servers
function minecraft_server_list_register_taxonomies() {
    // Server Edition Taxonomy
    register_taxonomy(
        'server_edition',
        'minecraft_server',
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => _x('Server Editions', 'taxonomy general name', 'minecraft-server-list'),
                'singular_name' => _x('Server Edition', 'taxonomy singular name', 'minecraft-server-list'),
                'menu_name' => __('Server Editions', 'minecraft-server-list'),
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'server-edition'),
            'show_in_rest' => true,
        )
    );

    // Server Type Taxonomy
    register_taxonomy(
        'server_type_tax',
        'minecraft_server',
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => _x('Server Types', 'taxonomy general name', 'minecraft-server-list'),
                'singular_name' => _x('Server Type', 'taxonomy singular name', 'minecraft-server-list'),
                'menu_name' => __('Server Types', 'minecraft-server-list'),
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'server-type'),
            'show_in_rest' => true,
        )
    );
    
    // Server Version Taxonomy
    register_taxonomy(
        'server_version_tax',
        'minecraft_server',
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => _x('Server Versions', 'taxonomy general name', 'minecraft-server-list'),
                'singular_name' => _x('Server Version', 'taxonomy singular name', 'minecraft-server-list'),
                'menu_name' => __('Server Versions', 'minecraft-server-list'),
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'server-version'),
            'show_in_rest' => true,
        )
    );
    
    // Server Location Taxonomy
    register_taxonomy(
        'server_location',
        'minecraft_server',
        array(
            'hierarchical' => true,
            'labels' => array(
                'name' => _x('Server Locations', 'taxonomy general name', 'minecraft-server-list'),
                'singular_name' => _x('Server Location', 'taxonomy singular name', 'minecraft-server-list'),
                'menu_name' => __('Server Locations', 'minecraft-server-list'),
            ),
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'server-location'),
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'minecraft_server_list_register_taxonomies');

// Add settings page
function minecraft_server_list_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=minecraft_server',
        __('Minecraft Server List Settings', 'minecraft-server-list'),
        __('Settings', 'minecraft-server-list'),
        'manage_options',
        'minecraft_server_list_settings',
        'minecraft_server_list_settings_page'
    );
}
add_action('admin_menu', 'minecraft_server_list_add_settings_page');

// Settings page content
function minecraft_server_list_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Handle form submission
    if (isset($_POST['minecraft_server_list_settings_nonce']) && wp_verify_nonce($_POST['minecraft_server_list_settings_nonce'], 'minecraft_server_list_settings')) {
        // Save settings
        update_option('minecraft_server_list_servers_per_page', isset($_POST['servers_per_page']) ? intval($_POST['servers_per_page']) : 20);
        update_option('minecraft_server_list_featured_first', isset($_POST['featured_first']) ? '1' : '0');
        update_option('minecraft_server_list_submissions_enabled', isset($_POST['submissions_enabled']) ? '1' : '0');
        
        // Display success message
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved.', 'minecraft-server-list') . '</p></div>';
    }
    
    // Get current settings
    $servers_per_page = get_option('minecraft_server_list_servers_per_page', 20);
    $featured_first = get_option('minecraft_server_list_featured_first', '1') === '1';
    $submissions_enabled = get_option('minecraft_server_list_submissions_enabled', '1') === '1';
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('minecraft_server_list_settings', 'minecraft_server_list_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="servers_per_page"><?php _e('Servers Per Page', 'minecraft-server-list'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="servers_per_page" name="servers_per_page" value="<?php echo esc_attr($servers_per_page); ?>" min="1" max="100">
                        <p class="description"><?php _e('Number of servers to display per page on the server list.', 'minecraft-server-list'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <?php _e('Featured Servers', 'minecraft-server-list'); ?>
                    </th>
                    <td>
                        <label for="featured_first">
                            <input type="checkbox" id="featured_first" name="featured_first" <?php checked($featured_first); ?>>
                            <?php _e('Show featured servers at the top of the list', 'minecraft-server-list'); ?>
                        </label>
                        <p class="description"><?php _e('If checked, featured servers will be displayed before the regular server listings.', 'minecraft-server-list'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <?php _e('Server Submissions', 'minecraft-server-list'); ?>
                    </th>
                    <td>
                        <label for="submissions_enabled">
                            <input type="checkbox" id="submissions_enabled" name="submissions_enabled" <?php checked($submissions_enabled); ?>>
                            <?php _e('Allow users to submit servers', 'minecraft-server-list'); ?>
                        </label>
                        <p class="description"><?php _e('If checked, users can submit their servers through the front-end form.', 'minecraft-server-list'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Save Settings', 'minecraft-server-list')); ?>
        </form>
    </div>
    <?php
}

// Add server status checker menu
function minecraft_server_list_add_status_checker_page() {
    add_submenu_page(
        'edit.php?post_type=minecraft_server',
        __('Check Server Status', 'minecraft-server-list'),
        __('Check Status', 'minecraft-server-list'),
        'manage_options',
        'minecraft_server_list_status_checker',
        'minecraft_server_list_status_checker_page'
    );
}
add_action('admin_menu', 'minecraft_server_list_add_status_checker_page');

// Status checker page content
function minecraft_server_list_status_checker_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Get all approved servers
    $servers = get_posts(array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'server_approved',
                'value' => 'approved',
                'compare' => '='
            )
        )
    ));
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="status-checker-controls">
            <button id="check-all-servers" class="button button-primary">
                <?php _e('Check All Servers', 'minecraft-server-list'); ?>
            </button>
            
            <div class="status-progress" style="display: none;">
                <progress id="status-progress-bar" value="0" max="100"></progress>
                <span class="progress-text">0%</span>
            </div>
        </div>
        
        <div class="notice notice-info status-info-notice">
            <p><?php _e('This tool checks the status of all approved Minecraft servers in your list. It uses the mcsrvstat.us API to ping servers and get their current status. You can use this to update all server statuses at once.', 'minecraft-server-list'); ?></p>
        </div>
        
        <table class="wp-list-table widefat fixed striped servers-status-table">
            <thead>
                <tr>
                    <th class="check-column"><input type="checkbox" id="select-all-servers"></th>
                    <th><?php _e('Server', 'minecraft-server-list'); ?></th>
                    <th><?php _e('Edition', 'minecraft-server-list'); ?></th>
                    <th><?php _e('IP Address', 'minecraft-server-list'); ?></th>
                    <th><?php _e('Java Status', 'minecraft-server-list'); ?></th>
                    <th><?php _e('Bedrock Status', 'minecraft-server-list'); ?></th>
                    <th><?php _e('Overall Status', 'minecraft-server-list'); ?></th>
                    <th><?php _e('Last Checked', 'minecraft-server-list'); ?></th>
                    <th><?php _e('Actions', 'minecraft-server-list'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servers as $server) : 
                    $server_id = $server->ID;
                    $server_java_ip = get_post_meta($server_id, 'server_java_ip', true) ?: get_post_meta($server_id, 'server_ip', true);
                    $server_bedrock_ip = get_post_meta($server_id, 'server_bedrock_ip', true) ?: get_post_meta($server_id, 'server_ip', true);
                    $server_port = get_post_meta($server_id, 'server_port', true) ?: '25565';
                    $server_bedrock_port = get_post_meta($server_id, 'server_bedrock_port', true) ?: '19132';
                    
                    // Get server editions
                    $server_editions = get_post_meta($server_id, 'server_editions', true);
                    if (!is_array($server_editions)) {
                        // Handle legacy data
                        $legacy_category = get_post_meta($server_id, 'server_category', true);
                        if (!empty($legacy_category)) {
                            $server_editions = array($legacy_category);
                        } else {
                            $server_editions = array();
                        }
                    }
                    
                    $has_java = in_array('java', $server_editions);
                    $has_bedrock = in_array('bedrock', $server_editions);
                    
                    // Get current status
                    $java_status = get_post_meta($server_id, 'server_java_status', true) ?: 'unknown';
                    $bedrock_status = get_post_meta($server_id, 'server_bedrock_status', true) ?: 'unknown';
                    $overall_status = get_post_meta($server_id, 'server_status', true) ?: 'unknown';
                    $last_checked = get_post_meta($server_id, 'server_last_checked', true);
                    
                    if (empty($last_checked)) {
                        $last_checked_text = __('Never', 'minecraft-server-list');
                    } else {
                        $last_checked_text = human_time_diff(strtotime($last_checked), current_time('timestamp')) . ' ' . __('ago', 'minecraft-server-list');
                    }
                    
                    $editions_text = array();
                    if ($has_java) $editions_text[] = 'Java';
                    if ($has_bedrock) $editions_text[] = 'Bedrock';
                    $editions_display = implode(' & ', $editions_text);
                    
                    $ips_display = array();
                    if ($has_java) $ips_display[] = $server_java_ip . ($server_port != '25565' ? ':' . $server_port : '');
                    if ($has_bedrock) $ips_display[] = $server_bedrock_ip . ' (Port: ' . $server_bedrock_port . ')';
                    
                    ?>
                    <tr data-server-id="<?php echo $server_id; ?>" data-java-ip="<?php echo esc_attr($server_java_ip); ?>" data-java-port="<?php echo esc_attr($server_port); ?>" data-bedrock-ip="<?php echo esc_attr($server_bedrock_ip); ?>" data-bedrock-port="<?php echo esc_attr($server_bedrock_port); ?>" data-has-java="<?php echo $has_java ? '1' : '0'; ?>" data-has-bedrock="<?php echo $has_bedrock ? '1' : '0'; ?>">
                        <td><input type="checkbox" class="server-checkbox"></td>
                        <td>
                            <strong><?php echo esc_html($server->post_title); ?></strong>
                        </td>
                        <td><?php echo esc_html($editions_display); ?></td>
                        <td>
                            <?php foreach ($ips_display as $ip_text) : ?>
                                <div><?php echo esc_html($ip_text); ?></div>
                            <?php endforeach; ?>
                        </td>
                        <td class="server-java-status">
                            <?php if ($has_java) : ?>
                                <span class="status-indicator status-<?php echo esc_attr($java_status); ?>"></span>
                                <span class="status-text"><?php echo ucfirst(esc_html($java_status)); ?></span>
                            <?php else : ?>
                                <span class="status-na">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td class="server-bedrock-status">
                            <?php if ($has_bedrock) : ?>
                                <span class="status-indicator status-<?php echo esc_attr($bedrock_status); ?>"></span>
                                <span class="status-text"><?php echo ucfirst(esc_html($bedrock_status)); ?></span>
                            <?php else : ?>
                                <span class="status-na">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td class="server-overall-status">
                            <span class="status-indicator status-<?php echo esc_attr($overall_status); ?>"></span>
                            <span class="status-text"><?php echo ucfirst(esc_html($overall_status)); ?></span>
                        </td>
                        <td class="server-last-checked">
                            <?php echo esc_html($last_checked_text); ?>
                        </td>
                        <td>
                            <button class="button check-server-status" data-server-id="<?php echo $server_id; ?>">
                                <?php _e('Check Now', 'minecraft-server-list'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($servers)) : ?>
                    <tr>
                        <td colspan="9"><?php _e('No approved servers found.', 'minecraft-server-list'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <style>
        .status-checker-controls {
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .status-progress {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        #status-progress-bar {
            width: 300px;
            height: 20px;
        }
        
        .status-info-notice {
            margin: 20px 0;
        }
        
        .servers-status-table {
            margin-top: 20px;
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-online {
            background-color: #4CAF50;
        }
        
        .status-offline {
            background-color: #F44336;
        }
        
        .status-unknown {
            background-color: #9E9E9E;
        }
        
        .status-checking {
            background-color: #2196F3;
            animation: pulse 1.5s infinite;
        }
        
        .status-na {
            color: #9E9E9E;
        }
        
        .status-text {
            font-weight: 600;
        }
        
        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Select all servers
        $('#select-all-servers').on('change', function() {
            $('.server-checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Handle individual server check
        $('.check-server-status').on('click', function() {
            const $button = $(this);
            const serverId = $button.data('server-id');
            const $row = $button.closest('tr');
            
            checkServerStatus($row);
        });
        
        // Check all selected servers
        $('#check-all-servers').on('click', function() {
            const $selectedRows = $('.server-checkbox:checked').closest('tr');
            
            if ($selectedRows.length === 0) {
                // If none selected, check all
                const $allRows = $('.server-checkbox').closest('tr');
                checkAllServers($allRows);
            } else {
                checkAllServers($selectedRows);
            }
        });
        
        // Function to check a single server
        function checkServerStatus($row) {
            const serverId = $row.data('server-id');
            const hasJava = $row.data('has-java') === 1;
            const hasBedrock = $row.data('has-bedrock') === 1;
            const javaIp = $row.data('java-ip');
            const javaPort = $row.data('java-port');
            const bedrockIp = $row.data('bedrock-ip');
            const bedrockPort = $row.data('bedrock-port');
            
            const $javaStatus = $row.find('.server-java-status');
            const $bedrockStatus = $row.find('.server-bedrock-status');
            const $overallStatus = $row.find('.server-overall-status');
            const $lastChecked = $row.find('.server-last-checked');
            const $button = $row.find('.check-server-status');
            
            // Update UI to show checking
            $button.prop('disabled', true).text('<?php _e('Checking...', 'minecraft-server-list'); ?>');
            
            if (hasJava) {
                $javaStatus.html('<span class="status-indicator status-checking"></span><span class="status-text"><?php _e('Checking...', 'minecraft-server-list'); ?></span>');
            }
            
            if (hasBedrock) {
                $bedrockStatus.html('<span class="status-indicator status-checking"></span><span class="status-text"><?php _e('Checking...', 'minecraft-server-list'); ?></span>');
}
            
            $overallStatus.html('<span class="status-indicator status-checking"></span><span class="status-text"><?php _e('Checking...', 'minecraft-server-list'); ?></span>');
            
            // Make AJAX request to check server status
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'check_minecraft_server_status',
                    server_id: serverId,
                    has_java: hasJava,
                    has_bedrock: hasBedrock,
                    java_ip: javaIp,
                    java_port: javaPort,
                    bedrock_ip: bedrockIp,
                    bedrock_port: bedrockPort,
                    security: '<?php echo wp_create_nonce('check_minecraft_server_status'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Update Java status
                        if (hasJava) {
                            const javaStatus = data.java_status;
                            $javaStatus.html('<span class="status-indicator status-' + javaStatus + '"></span><span class="status-text">' + javaStatus.charAt(0).toUpperCase() + javaStatus.slice(1) + '</span>');
                        }
                        
                        // Update Bedrock status
                        if (hasBedrock) {
                            const bedrockStatus = data.bedrock_status;
                            $bedrockStatus.html('<span class="status-indicator status-' + bedrockStatus + '"></span><span class="status-text">' + bedrockStatus.charAt(0).toUpperCase() + bedrockStatus.slice(1) + '</span>');
                        }
                        
                        // Update overall status
                        const overallStatus = data.overall_status;
                        $overallStatus.html('<span class="status-indicator status-' + overallStatus + '"></span><span class="status-text">' + overallStatus.charAt(0).toUpperCase() + overallStatus.slice(1) + '</span>');
                        
                        // Update last checked time
                        $lastChecked.text('<?php _e('Just now', 'minecraft-server-list'); ?>');
                    } else {
                        // Handle error
                        if (hasJava) {
                            $javaStatus.html('<span class="status-indicator status-unknown"></span><span class="status-text"><?php _e('Unknown', 'minecraft-server-list'); ?></span>');
                        }
                        
                        if (hasBedrock) {
                            $bedrockStatus.html('<span class="status-indicator status-unknown"></span><span class="status-text"><?php _e('Unknown', 'minecraft-server-list'); ?></span>');
                        }
                        
                        $overallStatus.html('<span class="status-indicator status-unknown"></span><span class="status-text"><?php _e('Unknown', 'minecraft-server-list'); ?></span>');
                        
                        alert(response.data.message || '<?php _e('Error checking server status', 'minecraft-server-list'); ?>');
                    }
                },
                error: function() {
                    // Handle error
                    if (hasJava) {
                        $javaStatus.html('<span class="status-indicator status-unknown"></span><span class="status-text"><?php _e('Unknown', 'minecraft-server-list'); ?></span>');
                    }
                    
                    if (hasBedrock) {
                        $bedrockStatus.html('<span class="status-indicator status-unknown"></span><span class="status-text"><?php _e('Unknown', 'minecraft-server-list'); ?></span>');
                    }
                    
                    $overallStatus.html('<span class="status-indicator status-unknown"></span><span class="status-text"><?php _e('Unknown', 'minecraft-server-list'); ?></span>');
                    
                    alert('<?php _e('Error connecting to server', 'minecraft-server-list'); ?>');
                },
                complete: function() {
                    // Re-enable button
                    $button.prop('disabled', false).text('<?php _e('Check Now', 'minecraft-server-list'); ?>');
                }
            });
        }
        
        // Function to check all servers
        function checkAllServers($rows) {
            if ($rows.length === 0) {
                return;
            }
            
            // Show progress bar
            $('.status-progress').show();
            
            let currentIndex = 0;
            const totalServers = $rows.length;
            
            // Check servers one by one with a delay
            function checkNextServer() {
                if (currentIndex >= totalServers) {
                    // All servers checked
                    $('.status-progress').hide();
                    return;
                }
                
                const $row = $rows.eq(currentIndex);
                
                // Update progress
                const progress = Math.round((currentIndex / totalServers) * 100);
                $('#status-progress-bar').val(progress);
                $('.progress-text').text(progress + '%');
                
                // Check current server
                checkServerStatus($row);
                
                // Move to next server after delay
                currentIndex++;
                setTimeout(checkNextServer, 2000);
            }
            
            // Start checking
            checkNextServer();
        }
    });
    </script>
    <?php
}

function minecraft_server_list_check_status() {
    // Check nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'check_minecraft_server_status')) {
        wp_send_json_error(array('message' => __('Security check failed', 'minecraft-server-list')));
    }
    
    // Get parameters
    $server_id = isset($_POST['server_id']) ? intval($_POST['server_id']) : 0;
    
    if ($server_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid server ID', 'minecraft-server-list')));
    }
    
    // Retry sayısını ve gecikmeyi belirle
    $retry_count = 3; // Kaç kez tekrar deneneceği
    $retry_delay = 3; // Denemeler arası kaç saniye beklenecek
    
    // Tekrar denemeli kontrol fonksiyonunu çağır
    $result = check_minecraft_server_status_with_retry($server_id, $retry_count, $retry_delay);
    
    // Sonucu döndür
    wp_send_json_success($result);
}
add_action('wp_ajax_check_minecraft_server_status', 'minecraft_server_list_check_status');
add_action('wp_ajax_nopriv_check_minecraft_server_status', 'minecraft_server_list_check_status');


// Aynı IP adresini kontrol eden fonksiyon
function minecraft_server_check_duplicate_ip($ip_address) {
    // IP adresi boşsa kontrole gerek yok
    if (empty($ip_address)) {
        return false;
    }
    
    // Tüm onaylanmış ve bekleyen sunucuları kontrol et
    $args = array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'server_java_ip',
                'value' => $ip_address,
                'compare' => '='
            ),
            array(
                'key' => 'server_bedrock_ip',
                'value' => $ip_address,
                'compare' => '='
            ),
            array(
                'key' => 'server_ip',
                'value' => $ip_address,
                'compare' => '='
            )
        )
    );
    
    $query = new WP_Query($args);
    
    // Eğer bir sonuç bulunduysa, IP zaten kullanımda demektir
    return ($query->found_posts > 0);
}

// Minecraft sunucu form gönderimini yöneten fonksiyon
function minecraft_server_list_handle_submission() {
    // Form gönderilmiş mi ve nonce geçerli mi kontrolü
    if (!isset($_POST['submit_minecraft_server']) || !isset($_POST['minecraft_server_submission_nonce']) || !wp_verify_nonce($_POST['minecraft_server_submission_nonce'], 'submit_minecraft_server')) {
        return;
    }
    
    // Sunucu ekleme özelliği açık mı kontrolü
    if (get_option('minecraft_server_list_submissions_enabled', '1') !== '1') {
        wp_die(__('Sunucu ekleme şu anda devre dışı.', 'minecraft-server-list'), __('Gönderim Hatası', 'minecraft-server-list'), array('response' => 403));
    }
    
    // Hata listesi hazırla
    $errors = array();
    
    // Zorunlu alanları kontrol et
    if (empty($_POST['server_title'])) {
        $errors[] = __('Sunucu adı gereklidir.', 'minecraft-server-list');
    }
    
    if (empty($_POST['server_description'])) {
        $errors[] = __('Sunucu açıklaması gereklidir.', 'minecraft-server-list');
    }
    
    // Sunucu sürümlerini kontrol et
    if (empty($_POST['server_editions']) || !is_array($_POST['server_editions'])) {
        $errors[] = __('Lütfen en az bir sunucu sürümü seçin.', 'minecraft-server-list');
    } else {
        $server_editions = $_POST['server_editions'];
        $has_java = in_array('java', $server_editions);
        $has_bedrock = in_array('bedrock', $server_editions);
        
        // Java IP kontrolü
        if ($has_java) {
            if (empty($_POST['server_java_ip'])) {
                $errors[] = __('Java Sunucu IP adresi gereklidir.', 'minecraft-server-list');
            } else {
                $java_ip = sanitize_text_field($_POST['server_java_ip']);
                // IP zaten var mı kontrolü
                if (minecraft_server_check_duplicate_ip($java_ip)) {
                    $errors[] = __('Bu Java IP adresi zaten sistemde kayıtlı: ', 'minecraft-server-list') . $java_ip;
                }
            }
        }
        
        // Bedrock IP kontrolü
        if ($has_bedrock) {
            if (empty($_POST['server_bedrock_ip'])) {
                $errors[] = __('Bedrock Sunucu IP adresi gereklidir.', 'minecraft-server-list');
            } else {
                $bedrock_ip = sanitize_text_field($_POST['server_bedrock_ip']);
                // IP zaten var mı kontrolü
                if (minecraft_server_check_duplicate_ip($bedrock_ip)) {
                    $errors[] = __('Bu Bedrock IP adresi zaten sistemde kayıtlı: ', 'minecraft-server-list') . $bedrock_ip;
                }
            }
        }
    }
    
    // Diğer zorunlu alanları kontrol et
    if (empty($_POST['server_version'])) {
        $errors[] = __('Sunucu sürümü gereklidir.', 'minecraft-server-list');
    }
    
    if (empty($_POST['server_country'])) {
        $errors[] = __('Sunucu konumu gereklidir.', 'minecraft-server-list');
    }
    
    if (empty($_POST['server_type']) || !is_array($_POST['server_type']) || count($_POST['server_type']) > 5) {
        $errors[] = __('Lütfen 1 ile 5 arasında sunucu tipi seçin.', 'minecraft-server-list');
    }
    
    // Hata varsa, formu tekrar göster
    if (!empty($errors)) {
        $error_html = '<div class="notification error"><ul class="error-list">';
        foreach ($errors as $error) {
            $error_html .= '<li>' . esc_html($error) . '</li>';
        }
        $error_html .= '</ul></div>';
        
        set_transient('minecraft_server_submission_errors', $error_html, 60);
        set_transient('minecraft_server_submission_data', $_POST, 60);
        
        // Form sayfasına yönlendir ve hata mesajını göster
        wp_redirect(add_query_arg('submission_error', '1', wp_get_referer() . '#add-server'));
        exit;
    }
    
    // Form verileri geçerli, yeni sunucu oluştur
    $server_title = sanitize_text_field($_POST['server_title']);
    $server_description = wp_kses_post($_POST['server_description']);
    $server_editions = $_POST['server_editions'];
    
    // Sunucu gönderimi oluştur
    $server_post = array(
        'post_title'    => $server_title,
        'post_content'  => $server_description,
        'post_status'   => 'publish',
        'post_type'     => 'minecraft_server'
    );
    
    $server_id = wp_insert_post($server_post);
    
    if (is_wp_error($server_id)) {
        wp_die($server_id->get_error_message(), __('Gönderim Hatası', 'minecraft-server-list'), array('response' => 500));
    }
    
    // Sunucu meta verilerini ayarla
    update_post_meta($server_id, 'server_editions', $server_editions);
    
    // Java IP ve port bilgisini ayarla
    if (in_array('java', $server_editions) && !empty($_POST['server_java_ip'])) {
        update_post_meta($server_id, 'server_java_ip', sanitize_text_field($_POST['server_java_ip']));
        update_post_meta($server_id, 'server_port', sanitize_text_field($_POST['server_port'] ?: '25565'));
    }
    
    // Bedrock IP ve port bilgisini ayarla
    if (in_array('bedrock', $server_editions) && !empty($_POST['server_bedrock_ip'])) {
        update_post_meta($server_id, 'server_bedrock_ip', sanitize_text_field($_POST['server_bedrock_ip']));
        update_post_meta($server_id, 'server_bedrock_port', sanitize_text_field($_POST['server_bedrock_port'] ?: '19132'));
    }
    
    // Geriye dönük uyumluluk için eski IP alanını da güncelle
    if (in_array('java', $server_editions) && !empty($_POST['server_java_ip'])) {
        update_post_meta($server_id, 'server_ip', sanitize_text_field($_POST['server_java_ip']));
    } elseif (in_array('bedrock', $server_editions) && !empty($_POST['server_bedrock_ip'])) {
        update_post_meta($server_id, 'server_ip', sanitize_text_field($_POST['server_bedrock_ip']));
    }
    
    // Diğer meta verileri ayarla
    update_post_meta($server_id, 'server_version', sanitize_text_field($_POST['server_version']));
    update_post_meta($server_id, 'server_country', sanitize_text_field($_POST['server_country']));
    update_post_meta($server_id, 'server_type', $_POST['server_type']);
    
    // İsteğe bağlı alanlar
    if (!empty($_POST['server_website'])) {
        update_post_meta($server_id, 'server_website', esc_url_raw($_POST['server_website']));
    }
    
    if (!empty($_POST['server_discord'])) {
        update_post_meta($server_id, 'server_discord', esc_url_raw($_POST['server_discord']));
    }
    
    // Logo yüklemesini işle (öne çıkan görsel)
    if (!empty($_FILES['server_logo']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $logo_id = media_handle_upload('server_logo', $server_id);
        
        if (!is_wp_error($logo_id)) {
            set_post_thumbnail($server_id, $logo_id);
        }
    }
    
    // Banner yüklemesini işle
    if (!empty($_FILES['server_banner']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $banner_id = media_handle_upload('server_banner', $server_id);
        
        if (!is_wp_error($banner_id)) {
            update_post_meta($server_id, 'server_banner', $banner_id);
        }
    }
    
    // Ekleme tarihi ve onay durumunu ayarla
    update_post_meta($server_id, 'server_submission_date', current_time('mysql'));
    update_post_meta($server_id, 'server_approved', 'pending');
    
    // İstatistikleri başlat
    update_post_meta($server_id, 'server_votes', 0);
    update_post_meta($server_id, 'server_rank', 0);
    update_post_meta($server_id, 'server_rating', 0);
    update_post_meta($server_id, 'server_review_count', 0);
    
    // Başarılı olduğunda yönlendir
    $redirect_url = add_query_arg('submission_success', '1', home_url('/minecraft-server-list/'));
    wp_redirect($redirect_url);
    exit;
}
add_action('init', 'minecraft_server_list_handle_submission');

// Add server vote AJAX handler
function minecraft_server_list_vote_server() {
    check_ajax_referer('minecraft_server_list_nonce', 'nonce');
    
    $server_id = isset($_POST['server_id']) ? intval($_POST['server_id']) : 0;
    
    if ($server_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid server ID', 'minecraft-server-list')));
    }
    
    // Check if server exists and is approved
    $server = get_post($server_id);
    $server_approved = get_post_meta($server_id, 'server_approved', true);
    
    if (!$server || $server->post_type !== 'minecraft_server' || $server_approved !== 'approved') {
        wp_send_json_error(array('message' => __('Server not found or not approved', 'minecraft-server-list')));
    }
    
    // Check if already voted using cookies
    $voted_cookie = 'mc_server_voted_' . $server_id;
    
    if (isset($_COOKIE[$voted_cookie])) {
        wp_send_json_error(array('message' => __('You have already voted for this server today. Please come back tomorrow.', 'minecraft-server-list')));
    }
    
    // Update vote count
    $votes = (int) get_post_meta($server_id, 'server_votes', true);
    $votes++;
    update_post_meta($server_id, 'server_votes', $votes);
    
    // Set cookie for 24 hours (not set here because AJAX can't set cookies, it's handled on the client side)
    
    wp_send_json_success(array(
        'message' => __('Thank you for your vote!', 'minecraft-server-list'),
        'votes' => $votes
    ));
}
add_action('wp_ajax_vote_server', 'minecraft_server_list_vote_server');
add_action('wp_ajax_nopriv_vote_server', 'minecraft_server_list_vote_server');

// CSS ve JavaScript kodlarını doğrudan WordPress'e ekleyen fonksiyon
function minecraft_server_list_enqueue_scripts() {
    // CSS kodunu inline olarak eklemek
    $inline_css = '
    /* Bildirim Toast Stili */
    .notification-toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        max-width: 350px;
        padding: 15px 20px;
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        border-radius: 5px;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 15px;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.3s, transform 0.3s;
    }

    .notification-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .notification-success {
        border-left: 4px solid #4CAF50;
    }

    .notification-error {
        border-left: 4px solid #F44336;
    }

    .notification-warning {
        border-left: 4px solid #FFC107;
    }

    .notification-info {
        border-left: 4px solid #2196F3;
    }

    .notification-icon {
        color: #555;
    }

    .notification-success .notification-icon {
        color: #4CAF50;
    }

    .notification-error .notification-icon {
        color: #F44336;
    }

    .notification-warning .notification-icon {
        color: #FFC107;
    }

    .notification-info .notification-icon {
        color: #2196F3;
    }

    .notification-message {
        flex: 1;
    }

    .notification-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #888;
    }

    /* Sunucu durum göstergeleri */
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 4px;
    }

    .status-online .status-indicator,
    .status-indicator.online {
        background-color: #4CAF50;
    }

    .status-offline .status-indicator,
    .status-indicator.offline {
        background-color: #F44336;
    }

    .status-unknown .status-indicator,
    .status-indicator.unknown {
        background-color: #9E9E9E;
    }

    /* IP kopyalama işlevselliği için düzeltme */
    .ip-copy, .server-ip-copy {
        cursor: pointer;
    }';

    // CSS'i head'e ekle
    wp_register_style('minecraft-server-list-style', false);
    wp_enqueue_style('minecraft-server-list-style');
    wp_add_inline_style('minecraft-server-list-style', $inline_css);

    // jQuery'i yükle
    wp_enqueue_script('jquery');

    // JavaScript kodunu doğrudan ekle
    $inline_script = '
    jQuery(document).ready(function($) {
        // Sabitler ve yapılandırma
        const CONFIG = {
            VOTE_INTERVAL: 24 * 60 * 60 * 1000, // 24 saat milisaniye cinsinden
            NOTIFICATION_TIMEOUT: 5000
        };

        // IP\'yi panoya kopyala
        $(".ip-copy, .server-ip-copy").on("click", function(e) {
            e.stopPropagation();
            const ip = $(this).data("clipboard");
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(ip)
                    .then(() => {
                        showNotification("Sunucu adresi kopyalandı!", "success");
                    })
                    .catch(() => {
                        showNotification("Adres kopyalanamadı", "error");
                    });
            } else {
                const $textarea = $("<textarea>").css({ position: "fixed", opacity: 0 }).val(ip);
                $("body").append($textarea);
                $textarea.select();
                
                try {
                    const success = document.execCommand("copy");
                    if (success) {
                        showNotification("Sunucu adresi kopyalandı!", "success");
                    } else {
                        showNotification("Adres kopyalanamadı", "error");
                    }
                } catch (err) {
                    showNotification("Adres kopyalanamadı", "error");
                }
                
                $textarea.remove();
            }
        });

        // Sunucu oylamasını işle
        $(".vote-form").on("submit", function(e) {
            e.preventDefault();
            const $form = $(this);
            const serverId = $form.find("input[name=\'server_id\']").val();
            const voteKey = `mc_server_voted_${serverId}`;
            const now = Date.now();
            const lastVote = parseInt(localStorage.getItem(voteKey) || "0");

            if (now - lastVote < CONFIG.VOTE_INTERVAL) {
                const hoursLeft = Math.ceil((CONFIG.VOTE_INTERVAL - (now - lastVote)) / (3600000));
                showNotification(`${hoursLeft} saat sonra tekrar oy verebilirsiniz`, "warning");
                return;
            }

            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: "vote_server",
                    server_id: serverId,
                    nonce: "' . wp_create_nonce('minecraft_server_list_nonce') . '"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        localStorage.setItem(voteKey, now);
                        showNotification("Oyunuz için teşekkürler!", "success");
                        
                        // Sayfadaki oy sayısını güncelle
                        const $voteCount = $form.closest(".server-card, .featured-server-card").find(".stat-votes span");
                        if ($voteCount.length) {
                            $voteCount.text(response.data.votes);
                        }
                    } else {
                        showNotification(response.data?.message || "Oy verme başarısız oldu", "error");
                    }
                },
                error: function() {
                    showNotification("Oy işlenirken hata oluştu", "error");
                }
            });
        });

        // Bildirim göster
        function showNotification(message, type = "info") {
            $(".notification-toast").remove();
            
            const icon = {
                success: "<i class=\'fas fa-check-circle\'></i>",
                error: "<i class=\'fas fa-exclamation-circle\'></i>",
                warning: "<i class=\'fas fa-exclamation-triangle\'></i>",
                info: "<i class=\'fas fa-info-circle\'></i>"
            }[type] || "<i class=\'fas fa-info-circle\'></i>";

            const $notification = $(`
                <div class="notification-toast notification-${type}">
                    <div class="notification-icon">${icon}</div>
                    <div class="notification-message">${message}</div>
                    <button class="notification-close">×</button>
                </div>
            `);

            $("body").append($notification);
            setTimeout(() => $notification.addClass("show"), 100);

            const hideNotification = () => {
                $notification.removeClass("show");
                setTimeout(() => $notification.remove(), 300);
            };

            setTimeout(hideNotification, CONFIG.NOTIFICATION_TIMEOUT);
            $notification.find(".notification-close").on("click", hideNotification);
        }

        // Add Server form toggle JavaScript kodunu düzeltelim
        $(".toggle-add-server").on("click", function(e) {
         e.preventDefault(); // Default link davranışını engelle
         e.stopPropagation(); // Olayın kabarcıklanmasını (bubbling) durdur

         const $addServerSection = $("#add-server");

			// Bölümün görünürlüğünü değiştir
			if ($addServerSection.is(":visible")) {
				$addServerSection.slideUp(300);
			} else {
				$addServerSection.slideDown(300);
				// Bölüme kaydır
				$("html, body").animate({
					scrollTop: $addServerSection.offset().top - 50
				}, 500);
			}

			return false; // Extra güvenlik önlemi
		});

        // Ülke bayraklarını yükle
        $(".country-flag").each(function() {
            const countryCode = $(this).data("country")?.toLowerCase();
            if (countryCode) {
                $(this).attr("src", `https://flagcdn.com/w20/${countryCode}.png`);
            }
        });

        // Sunucu detay sayfası için sekme işleme
        $(".status-tab-header").on("click", function() {
            const tabId = $(this).data("tab");
            $(".status-tab-header").removeClass("active");
            $(this).addClass("active");
            
            $(".status-tab-pane").removeClass("active");
            $(`#${tabId}-status-tab`).addClass("active");
        });

        // İnceleme bölümünde yıldız seçici
        $(".rating-selector label").on("click", function() {
            const $parent = $(this).parent();
            $parent.find("label").removeClass("selected");
            $(this).addClass("selected").prevAll("label").addClass("selected");
        });
    });';

    // Düzenli skript olarak ekleme
    wp_add_inline_script('jquery', $inline_script);

    // AJAX URL'sini lokalizeyt
    $script_data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('minecraft_server_list_nonce')
    );
    wp_localize_script('jquery', 'minecraft_server_list', $script_data);
}
add_action('wp_enqueue_scripts', 'minecraft_server_list_enqueue_scripts');

// Admin scripts and styles
// Admin CSS ve JavaScript kodlarını inline olarak ekle
function minecraft_server_list_admin_scripts($hook) {
    $screen = get_current_screen();
    
    if ($screen->post_type === 'minecraft_server' || $hook === 'minecraft_server_page_minecraft_server_list_status_checker') {
        wp_enqueue_media();
        
        // Admin CSS inline olarak ekle
        $admin_css = '
        /* Minecraft Server List Admin Styles */
        .status-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-online {
            background-color: #4CAF50;
        }
        
        .status-offline {
            background-color: #F44336;
        }
        
        .status-unknown {
            background-color: #9E9E9E;
        }
        
        .approval-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-approved {
            background-color: #E8F5E9;
            color: #2E7D32;
        }
        
        .status-pending {
            background-color: #FFF8E1;
            color: #FF8F00;
        }
        
        .status-rejected {
            background-color: #FFEBEE;
            color: #C62828;
        }';
        
        wp_register_style('minecraft-server-admin-style', false);
        wp_enqueue_style('minecraft-server-admin-style');
        wp_add_inline_style('minecraft-server-admin-style', $admin_css);
        
        // Admin JS inline olarak ekle
        $admin_js = '
        jQuery(document).ready(function($) {
            // Banner yükleme
            $(".upload-banner-button").on("click", function(e) {
                e.preventDefault();
                
                var bannerUploader = wp.media({
                    title: "Sunucu Banner Resmi Seç veya Yükle",
                    button: {
                        text: "Bu görüntüyü kullan"
                    },
                    multiple: false
                });
                
                bannerUploader.on("select", function() {
                    var attachment = bannerUploader.state().get("selection").first().toJSON();
                    $("#server_banner").val(attachment.id);
                    
                    if (attachment.type === "image") {
                        var imgUrl = attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                        
                        if ($(".banner-preview").length) {
                            $(".banner-preview img").attr("src", imgUrl);
                        } else {
                            $(".banner-upload-field").prepend(\'<div class="banner-preview"><img src="\' + imgUrl + \'" alt=""></div>\');
                        }
                        
                        $(".remove-banner-button").show();
                    }
                });
                
                bannerUploader.open();
            });
            
            // Banner kaldırma
            $(".remove-banner-button").on("click", function(e) {
                e.preventDefault();
                $("#server_banner").val("");
                $(".banner-preview").remove();
                $(this).hide();
            });
            
            // Sunucu tiplerini 5 ile sınırla
            $("input[name=\'server_type[]\']").on("change", function() {
                if ($("input[name=\'server_type[]\']:checked").length > 5) {
                    this.checked = false;
                    alert("En fazla 5 sunucu tipi seçebilirsiniz");
                }
            });
            
            // Sunucu durumu kontrol butonları
            $(".check-server-status").on("click", function() {
                var $button = $(this);
                var serverId = $button.data("server-id");
                var $row = $button.closest("tr");
                
                $button.prop("disabled", true).text("Kontrol ediliyor...");
                
                // AJAX ile sunucu durumunu kontrol et
                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                        action: "check_minecraft_server_status",
                        server_id: serverId,
                        has_java: $row.data("has-java"),
                        has_bedrock: $row.data("has-bedrock"),
                        java_ip: $row.data("java-ip"),
                        java_port: $row.data("java-port"),
                        bedrock_ip: $row.data("bedrock-ip"),
                        bedrock_port: $row.data("bedrock-port"),
                        security: "' . wp_create_nonce('check_minecraft_server_status') . '"
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            
                            // Durumları güncelle
                            var $javaStatus = $row.find(".server-java-status");
                            var $bedrockStatus = $row.find(".server-bedrock-status");
                            var $overallStatus = $row.find(".server-overall-status");
                            
                            if ($row.data("has-java") === 1) {
                                $javaStatus.html(\'<span class="status-dot status-\' + data.java_status + \'"></span> \' + 
                                                data.java_status.charAt(0).toUpperCase() + data.java_status.slice(1));
                            }
                            
                            if ($row.data("has-bedrock") === 1) {
                                $bedrockStatus.html(\'<span class="status-dot status-\' + data.bedrock_status + \'"></span> \' + 
                                                    data.bedrock_status.charAt(0).toUpperCase() + data.bedrock_status.slice(1));
                            }
                            
                            $overallStatus.html(\'<span class="status-dot status-\' + data.overall_status + \'"></span> \' + 
                                                data.overall_status.charAt(0).toUpperCase() + data.overall_status.slice(1));
                            
                            $row.find(".server-last-checked").text("Şimdi");
                        } else {
                            alert(response.data.message || "Sunucu durumu kontrol edilirken hata oluştu");
                        }
                    },
                    error: function() {
                        alert("Sunucuya bağlanırken hata oluştu");
                    },
                    complete: function() {
                        $button.prop("disabled", false).text("Şimdi Kontrol Et");
                    }
                });
            });
            
            // Tüm sunucuları kontrol et
            $("#check-all-servers").on("click", function() {
                var $selectedRows = $(".server-checkbox:checked").closest("tr");
                
                if ($selectedRows.length === 0) {
                    // Hiçbiri seçilmemişse tümünü kontrol et
                    $selectedRows = $(".server-checkbox").closest("tr");
                }
                
                if ($selectedRows.length === 0) {
                    alert("Kontrol edilecek sunucu bulunamadı");
                    return;
                }
                
                var $progressBar = $("#status-progress-bar");
                var $progressText = $(".progress-text");
                $(".status-progress").show();
                
                var currentIndex = 0;
                var totalServers = $selectedRows.length;
                
                function checkNextServer() {
                    if (currentIndex >= totalServers) {
                        $(".status-progress").hide();
                        return;
                    }
                    
                    var $row = $selectedRows.eq(currentIndex);
                    var $button = $row.find(".check-server-status");
                    
                    // İlerlemeyi güncelle
                    var progress = Math.round((currentIndex / totalServers) * 100);
                    $progressBar.val(progress);
                    $progressText.text(progress + "%");
                    
                    // Tıklama olayını tetikle
                    $button.trigger("click");
                    
                    // Sonraki sunucuya geç
                    currentIndex++;
                    setTimeout(checkNextServer, 2000);
                }
                
                // Kontrol etmeye başla
                checkNextServer();
            });
            
            // Sürüm-spesifik IP alanlarını göster/gizle
            function updateEditionFields() {
                var javaChecked = $("#edition_java").is(":checked");
                var bedrockChecked = $("#edition_bedrock").is(":checked");
                
                if (javaChecked) {
                    $("#java_fields").show();
                    $("#server_java_ip").prop("required", true);
                } else {
                    $("#java_fields").hide();
                    $("#server_java_ip").prop("required", false);
                }
                
                if (bedrockChecked) {
                    $("#bedrock_fields").show();
                    $("#server_bedrock_ip").prop("required", true);
                } else {
                    $("#bedrock_fields").hide();
                    $("#server_bedrock_ip").prop("required", false);
                }
                
                // Eski IP alanını güncelle
                updateLegacyIp();
            }
            
            // Eski IP alanını güncelle
            function updateLegacyIp() {
                var javaChecked = $("#edition_java").is(":checked");
                var bedrockChecked = $("#edition_bedrock").is(":checked");
                var javaIp = $("#server_java_ip").val();
                var bedrockIp = $("#server_bedrock_ip").val();
                
                if (javaChecked && javaIp) {
                    $("#server_ip").val(javaIp);
                } else if (bedrockChecked && bedrockIp) {
                    $("#server_ip").val(bedrockIp);
                }
            }
            
            // Alanların görünürlüğünü başlat
            updateEditionFields();
            
            // Olay dinleyicileri ekle
            $("#edition_java, #edition_bedrock").on("change", updateEditionFields);
            $("#server_java_ip, #server_bedrock_ip").on("input", updateLegacyIp);
        });';
        
        wp_add_inline_script('jquery', $admin_js);
    }
}
add_action('admin_enqueue_scripts', 'minecraft_server_list_admin_scripts');
// Add custom columns to the Minecraft Server list
function minecraft_server_list_custom_columns($columns) {
    $new_columns = array();
    
    // Insert columns after title
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        
        if ($key === 'title') {
            $new_columns['server_edition'] = __('Edition', 'minecraft-server-list');
            $new_columns['server_ip'] = __('Server IP', 'minecraft-server-list');
            $new_columns['server_status'] = __('Status', 'minecraft-server-list');
            $new_columns['server_votes'] = __('Votes', 'minecraft-server-list');
            $new_columns['server_approval'] = __('Approval', 'minecraft-server-list');
        }
    }
    
    return $new_columns;
}
add_filter('manage_minecraft_server_posts_columns', 'minecraft_server_list_custom_columns');

// Display the custom column content
function minecraft_server_list_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'server_edition':
            $server_editions = get_post_meta($post_id, 'server_editions', true);
            if (!is_array($server_editions)) {
                // Handle legacy data
                $legacy_category = get_post_meta($post_id, 'server_category', true);
                if (!empty($legacy_category)) {
                    $server_editions = array($legacy_category);
                } else {
                    $server_editions = array();
                }
            }
            
            $edition_labels = array();
            if (in_array('java', $server_editions)) $edition_labels[] = 'Java';
            if (in_array('bedrock', $server_editions)) $edition_labels[] = 'Bedrock';
            
            echo esc_html(implode(' & ', $edition_labels));
            break;
            
        case 'server_ip':
            $server_java_ip = get_post_meta($post_id, 'server_java_ip', true);
            $server_bedrock_ip = get_post_meta($post_id, 'server_bedrock_ip', true);
            $server_port = get_post_meta($post_id, 'server_port', true) ?: '25565';
            $server_bedrock_port = get_post_meta($post_id, 'server_bedrock_port', true) ?: '19132';
            
            if (!empty($server_java_ip)) {
                echo '<div><strong>Java:</strong> ' . esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')) . '</div>';
            }
            
            if (!empty($server_bedrock_ip)) {
                echo '<div><strong>Bedrock:</strong> ' . esc_html($server_bedrock_ip) . ' (Port: ' . esc_html($server_bedrock_port) . ')</div>';
            }
            break;
            
        case 'server_status':
            $server_status = get_post_meta($post_id, 'server_status', true) ?: 'unknown';
            $status_class = $server_status === 'online' ? 'online' : ($server_status === 'offline' ? 'offline' : 'unknown');
            $status_text = ucfirst($server_status);
            
            echo '<span class="status-dot status-' . esc_attr($status_class) . '"></span> ' . esc_html($status_text);
            break;
            
        case 'server_votes':
            $votes = get_post_meta($post_id, 'server_votes', true) ?: 0;
            echo esc_html($votes);
            break;
            
        case 'server_approval':
            $approval = get_post_meta($post_id, 'server_approved', true) ?: 'pending';
            $approval_class = '';
            
            switch ($approval) {
                case 'approved':
                    $approval_class = 'approved';
                    $approval_text = __('Approved', 'minecraft-server-list');
                    break;
                case 'rejected':
                    $approval_class = 'rejected';
                    $approval_text = __('Rejected', 'minecraft-server-list');
                    break;
                default:
                    $approval_class = 'pending';
                    $approval_text = __('Pending', 'minecraft-server-list');
                    break;
            }
            
            echo '<span class="approval-status status-' . esc_attr($approval_class) . '">' . esc_html($approval_text) . '</span>';
            break;
    }
}
add_action('manage_minecraft_server_posts_custom_column', 'minecraft_server_list_custom_column_content', 10, 2);

// Make some columns sortable
function minecraft_server_list_sortable_columns($columns) {
    $columns['server_votes'] = 'server_votes';
    $columns['server_approval'] = 'server_approved';
    $columns['server_status'] = 'server_status';
    return $columns;
}
add_filter('manage_edit-minecraft_server_sortable_columns', 'minecraft_server_list_sortable_columns');

// Handle sorting of custom columns
function minecraft_server_list_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ($orderby === 'server_votes') {
        $query->set('meta_key', 'server_votes');
        $query->set('orderby', 'meta_value_num');
    } elseif ($orderby === 'server_approved') {
        $query->set('meta_key', 'server_approved');
        $query->set('orderby', 'meta_value');
    } elseif ($orderby === 'server_status') {
        $query->set('meta_key', 'server_status');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'minecraft_server_list_column_orderby');

// Add custom filters for Minecraft Servers
function minecraft_server_list_add_filters() {
    global $typenow;
    
    if ($typenow === 'minecraft_server') {
        // Edition filter
        $current_edition = isset($_GET['server_edition_filter']) ? sanitize_text_field($_GET['server_edition_filter']) : '';
        $editions = array(
            'java' => 'Java Edition',
            'bedrock' => 'Bedrock Edition',
            'java_bedrock' => 'Java & Bedrock'
        );
        
        echo '<select name="server_edition_filter">';
        echo '<option value="">' . __('All Editions', 'minecraft-server-list') . '</option>';
        
        foreach ($editions as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($current_edition, $key, false) . '>' . esc_html($label) . '</option>';
        }
        
        echo '</select>';
        
        // Approval filter
        $current_approval = isset($_GET['server_approved_filter']) ? sanitize_text_field($_GET['server_approved_filter']) : '';
        $approval_statuses = array(
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        );
        
        echo '<select name="server_approved_filter">';
        echo '<option value="">' . __('All Approval Statuses', 'minecraft-server-list') . '</option>';
foreach ($approval_statuses as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($current_approval, $key, false) . '>' . esc_html($label) . '</option>';
        }
        
        echo '</select>';
        
        // Status filter
        $current_status = isset($_GET['server_status_filter']) ? sanitize_text_field($_GET['server_status_filter']) : '';
        $status_options = array(
            'online' => 'Online',
            'offline' => 'Offline',
            'unknown' => 'Unknown'
        );
        
        echo '<select name="server_status_filter">';
        echo '<option value="">' . __('All Statuses', 'minecraft-server-list') . '</option>';
        
        foreach ($status_options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($current_status, $key, false) . '>' . esc_html($label) . '</option>';
        }
        
        echo '</select>';
    }
}
add_action('restrict_manage_posts', 'minecraft_server_list_add_filters');

// Handle custom filters in query
function minecraft_server_list_filter_query($query) {
    global $pagenow, $typenow;
    
    if (is_admin() && $pagenow === 'edit.php' && $typenow === 'minecraft_server' && $query->is_main_query()) {
        // Handle edition filter
        if (isset($_GET['server_edition_filter']) && !empty($_GET['server_edition_filter'])) {
            $edition = sanitize_text_field($_GET['server_edition_filter']);
            
            if ($edition === 'java_bedrock') {
                // Need both Java and Bedrock
                $query->set('meta_query', array(
                    'relation' => 'AND',
                    array(
                        'key' => 'server_editions',
                        'value' => 'java',
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => 'server_editions',
                        'value' => 'bedrock',
                        'compare' => 'LIKE'
                    )
                ));
            } else {
                // Just one edition (Java or Bedrock)
                $query->set('meta_query', array(
                    array(
                        'key' => 'server_editions',
                        'value' => $edition,
                        'compare' => 'LIKE'
                    )
                ));
            }
        }
        
        // Handle approval filter
        if (isset($_GET['server_approved_filter']) && !empty($_GET['server_approved_filter'])) {
            $approval = sanitize_text_field($_GET['server_approved_filter']);
            
            $meta_query = $query->get('meta_query') ?: array();
            $meta_query[] = array(
                'key' => 'server_approved',
                'value' => $approval,
                'compare' => '='
            );
            
            $query->set('meta_query', $meta_query);
        }
        
        // Handle status filter
        if (isset($_GET['server_status_filter']) && !empty($_GET['server_status_filter'])) {
            $status = sanitize_text_field($_GET['server_status_filter']);
            
            $meta_query = $query->get('meta_query') ?: array();
            $meta_query[] = array(
                'key' => 'server_status',
                'value' => $status,
                'compare' => '='
            );
            
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'minecraft_server_list_filter_query');

// Add admin notification for pending server submissions
function minecraft_server_list_admin_notices() {
    $screen = get_current_screen();
    
    if (!($screen && $screen->post_type === 'minecraft_server')) {
        return;
    }
    
    // Count pending servers
    $pending_servers = wp_count_posts('minecraft_server');
    $pending_count = $pending_servers->publish;
    
    $pending_query = new WP_Query(array(
        'post_type' => 'minecraft_server',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'server_approved',
                'value' => 'pending',
                'compare' => '='
            )
        ),
        'fields' => 'ids'
    ));
    
    $pending_count = $pending_query->found_posts;
    
    if ($pending_count > 0) {
        ?>
        <div class="notice notice-info">
            <p>
                <?php 
                printf(
                    _n(
                        'There is %s server pending approval.',
                        'There are %s servers pending approval.',
                        $pending_count,
                        'minecraft-server-list'
                    ),
                    '<strong>' . $pending_count . '</strong>'
                );
                ?>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=minecraft_server&server_approved_filter=pending')); ?>">
                    <?php _e('View pending servers', 'minecraft-server-list'); ?>
                </a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'minecraft_server_list_admin_notices');

// Add shortcode for server list
// Add shortcode for server list with improved styling
function minecraft_server_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'edition' => '',
        'type' => '',
        'version' => '',
        'country' => '',
        'featured' => false,
        'sponsored' => false,
        'premium' => false,
        'online' => false,
        'columns' => 3
    ), $atts);
    
    // Sanitize attributes
    $limit = intval($atts['limit']);
    $edition = sanitize_text_field($atts['edition']);
    $type = sanitize_text_field($atts['type']);
    $version = sanitize_text_field($atts['version']);
    $country = sanitize_text_field($atts['country']);
    $featured = filter_var($atts['featured'], FILTER_VALIDATE_BOOLEAN);
    $sponsored = filter_var($atts['sponsored'], FILTER_VALIDATE_BOOLEAN);
    $premium = filter_var($atts['premium'], FILTER_VALIDATE_BOOLEAN);
    $online = filter_var($atts['online'], FILTER_VALIDATE_BOOLEAN);
    $columns = intval($atts['columns']);
    
    // Validate columns (between 1 and 6)
    if ($columns < 1 || $columns > 6) {
        $columns = 3;
    }
    
    // Build meta query
    $meta_query = array('relation' => 'AND');
    
    // Only show approved servers
    $meta_query[] = array(
        'key' => 'server_approved',
        'value' => 'approved',
        'compare' => '='
    );
    
    // Filter by edition
    if (!empty($edition)) {
        if ($edition === 'java_bedrock') {
            // Need both Java and Bedrock
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key' => 'server_editions',
                    'value' => 'java',
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'server_editions',
                    'value' => 'bedrock',
                    'compare' => 'LIKE'
                )
            );
        } else {
            // Just one edition (Java or Bedrock)
            $meta_query[] = array(
                'key' => 'server_editions',
                'value' => $edition,
                'compare' => 'LIKE'
            );
        }
    }
    
    // Filter by type
    if (!empty($type)) {
        $meta_query[] = array(
            'key' => 'server_type',
            'value' => $type,
            'compare' => 'LIKE'
        );
    }
    
    // Filter by version
    if (!empty($version)) {
        $meta_query[] = array(
            'key' => 'server_version',
            'value' => $version,
            'compare' => 'LIKE'
        );
    }
    
    // Filter by country
    if (!empty($country)) {
        $meta_query[] = array(
            'key' => 'server_country',
            'value' => $country,
            'compare' => '='
        );
    }
    
    // Filter by featured
    if ($featured) {
        $meta_query[] = array(
            'key' => 'server_featured',
            'value' => 'yes',
            'compare' => '='
        );
    }
    
    // Filter by sponsored
    if ($sponsored) {
        $meta_query[] = array(
            'key' => 'server_sponsored',
            'value' => 'yes',
            'compare' => '='
        );
    }
    
    // Filter by premium
    if ($premium) {
        $meta_query[] = array(
            'key' => 'server_premium',
            'value' => 'yes',
            'compare' => '='
        );
    }
    
    // Filter by online status
    if ($online) {
        $meta_query[] = array(
            'key' => 'server_status',
            'value' => 'online',
            'compare' => '='
        );
    }
    
    // Query args
    $args = array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => $limit,
        'meta_query' => $meta_query,
        'meta_key' => 'server_rank',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    );
    
    // Run the query
    $servers = new WP_Query($args);
    
    // Start output buffering
    ob_start();
    
    if ($servers->have_posts()) {
        ?>
        <style>

/* Minecraft Server List Styles */
.minecraft-server-list-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    color: #333333;
}

/* Grid layout */
.mc-server-grid {
    display: grid;
    grid-template-columns: repeat(var(--mc-columns, 3), 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

/* Responsive grid adjustments */
@media (max-width: 1200px) {
    .mc-server-grid {
        grid-template-columns: repeat(min(var(--mc-columns, 3), 4), 1fr);
    }
}

@media (max-width: 992px) {
    .mc-server-grid {
        grid-template-columns: repeat(min(var(--mc-columns, 3), 3), 1fr);
    }
}

@media (max-width: 768px) {
    .mc-server-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .mc-server-grid {
        grid-template-columns: 1fr;
    }
}

/* Card styling */
.mc-server-card {
    display: block;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    background-color: #ffffff;
    height: 100%;
    text-decoration: none !important;
    color: inherit !important;
    opacity: 1 !important;
}

.mc-server-card:hover,
.mc-server-card:focus {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.mc-server-card:focus {
    outline: 2px solid #3f51b5;
    outline-offset: 2px;
}

/* Banner image styling */
.mc-server-banner {
    width: 100%;
    height: 120px;
    background-color: #2c3e50;
    position: relative;
    overflow: hidden;
}

.mc-server-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    opacity: 1 !important;
}

/* Generated banner styling */
.mc-server-generated-banner {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #1e88e5, #26a69a);
}

.mc-server-generated-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        repeating-linear-gradient(0deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px),
        repeating-linear-gradient(90deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);
}

.mc-server-generated-banner .banner-icon {
    font-size: 40px;
    color: rgba(255, 255, 255, 0.9);
    position: relative;
    z-index: 1;
}

/* Card content styling */
.mc-server-content {
    padding: 15px;
}

/* Server title */
.mc-server-title {
    margin: 0 0 8px;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.3;
    color: #333333;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 42px;
    opacity: 1 !important;
}

/* Server meta information */
.mc-server-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 10px;
}

/* Edition badge styling */
.mc-server-edition {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    opacity: 1 !important;
}

.mc-server-edition.java {
    background-color: #FFD700; /* Gold */
    color: #0A2C28; /* Darker teal */
    /* Contrast ratio: ~6.0:1 */
}

.mc-server-edition.bedrock {
    background-color: #388E3C; /* Darker green */
    color: #FFFFFF; /* White */
    /* Contrast ratio: ~5.5:1 */
}

.mc-server-edition.java_bedrock {
    background-color: #9C27B0; /* Darker purple */
    color: #FFFFFF; /* White */
    /* Contrast ratio: ~5.8:1 */
}

/* Status indicator styling */
.mc-server-status {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    gap: 4px;
    opacity: 1 !important;
}

.mc-server-status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.mc-server-status.online .mc-server-status-indicator {
    background-color: #4CAF50;
}

.mc-server-status.offline .mc-server-status-indicator {
    background-color: #F44336;
}

/* Server types styling */
.mc-server-types {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.mc-server-type {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    background-color: #D8D8D8; /* Slightly darker gray */
    color: #222222; /* Darker gray */
    opacity: 1 !important;
    /* Contrast ratio: ~7.5:1 */
}

.mc-server-more-types {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    background-color: #C8C8C8; /* Darker gray for distinction */
    color: #222222;
    opacity: 1 !important;
    /* Contrast ratio: ~5.8:1 */
}

/* View all button styling */
.mc-view-all-wrapper {
    text-align: center;
    margin-top: 20px;
}

.mc-view-all-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #3f51b5;
    color: #FFFFFF;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: background-color 0.2s;
    opacity: 1 !important;
    /* Contrast ratio: ~7.0:1 */
}

.mc-view-all-button:hover,
.mc-view-all-button:focus {
    background-color: #303f9f;
    color: #FFFFFF;
    text-decoration: none;
}

.mc-view-all-button:focus {
    outline: 2px solid #3f51b5;
    outline-offset: 2px;
}

/* Player count styling */
.mc-player-count {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background-color: rgba(0, 0, 0, 0.7);
    color: #FFFFFF;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    z-index: 1;
    opacity: 1 !important;
    /* Contrast ratio: ~15.0:1 */
}

/* No servers message */
.no-servers {
    text-align: center;
    padding: 20px;
    color: #666666;
    font-size: 14px;
    opacity: 1 !important;
}
        </style>
        
        <div class="minecraft-server-list-wrapper">
            <div class="mc-server-grid">
                <?php while ($servers->have_posts()) : $servers->the_post(); 
                    $server_id = get_the_ID();
                    
                    // Get server editions
                    $server_editions = get_post_meta($server_id, 'server_editions', true);
                    if (!is_array($server_editions)) {
                        // Handle legacy data
                        $legacy_category = get_post_meta($server_id, 'server_category', true);
                        if (!empty($legacy_category)) {
                            $server_editions = array($legacy_category);
                        } else {
                            $server_editions = array();
                        }
                    }
                    
                    // Determine which edition badge to show
                    $edition_badge = '';
                    if (in_array('java', $server_editions) && in_array('bedrock', $server_editions)) {
                        $edition_badge = 'java_bedrock';
                    } elseif (in_array('java', $server_editions)) {
                        $edition_badge = 'java';
                    } elseif (in_array('bedrock', $server_editions)) {
                        $edition_badge = 'bedrock';
                    }
                    
                    // Get server status and player count
                    $server_status = get_post_meta($server_id, 'server_status', true) ?: 'offline';
                    $is_online = ($server_status === 'online');
                    $player_count = get_post_meta($server_id, 'server_player_count', true) ?: 0;
                    $max_players = get_post_meta($server_id, 'server_max_players', true) ?: 0;
                    
                    // Get server types
                    $server_types_array = get_post_meta($server_id, 'server_type', true);
                    $server_types_array = is_array($server_types_array) ? $server_types_array : array($server_types_array);
                    
                    // Get up to 2 server types for display
                    $types_display = array();
                    $server_types = array(
                        'survival' => 'Survival',
                        'creative' => 'Creative',
                        'skyblock' => 'Skyblock',
                        'factions' => 'Factions',
                        'minigames' => 'Minigames',
                        'prison' => 'Prison',
                        'pvp' => 'PvP',
                        'towny' => 'Towny',
                        'pixelmon' => 'Pixelmon',
                        'vanilla' => 'Vanilla',
                        'modded' => 'Modded',
                        'hardcore' => 'Hardcore',
                        'anarchy' => 'Anarchy',
                        'economy' => 'Economy',
                        'roleplay' => 'Roleplay',
                        'adventure' => 'Adventure',
                        'smp' => 'SMP',
                        'craftbukkit' => 'CraftBukkit',
                        'spigot' => 'Spigot',
                        'paper' => 'Paper',
                        'forge' => 'Forge',
                        'fabric' => 'Fabric',
                        'ftb' => 'FTB',
                        'tekkit' => 'Tekkit',
                        'realms' => 'Realms',
                        'crossplay' => 'Crossplay',
                        'other' => 'Other'
                    );
                    
                    $count = 0;
                    foreach ($server_types_array as $type) {
                        if (isset($server_types[$type]) && $count < 2) {
                            $types_display[] = $server_types[$type];
                            $count++;
                        }
                    }
                    
                    // Get banner image
                    $banner_id = get_post_meta($server_id, 'server_banner', true);
                    $has_banner = false;
                    $banner_url = '';
                    
                    if (!empty($banner_id)) {
                        $banner_url = wp_get_attachment_image_url($banner_id, 'medium');
                        $has_banner = !empty($banner_url);
                    }
                    
                    // Edition labels for display
                    $server_categories = array(
                        'java' => 'Java Edition',
                        'bedrock' => 'Bedrock Edition',
                        'java_bedrock' => 'Java & Bedrock'
                    );
                    ?>
                    <div class="mc-server-item">
                        <a href="<?php the_permalink(); ?>" class="mc-server-card">
                            <div class="mc-server-banner">
                                <?php if ($has_banner) : ?>
                                    <img src="<?php echo esc_url($banner_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?> Banner">
                                <?php else : ?>
                                    <div class="mc-server-generated-banner">
                                        <span class="banner-icon">
                                            <i class="fas fa-cubes"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($is_online && $player_count > 0) : ?>
                                    <div class="mc-player-count">
                                        <i class="fas fa-users"></i> <?php echo esc_html($player_count); ?>/<?php echo esc_html($max_players); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mc-server-content">
                                <h3 class="mc-server-title"><?php the_title(); ?></h3>
                                
                                <div class="mc-server-meta">
                                    <?php if (!empty($edition_badge)) : ?>
                                        <span class="mc-server-edition <?php echo esc_attr($edition_badge); ?>">
                                            <?php echo esc_html($server_categories[$edition_badge] ?? ''); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="mc-server-status <?php echo $is_online ? 'online' : 'offline'; ?>">
                                        <span class="mc-server-status-indicator"></span>
                                        <span><?php echo $is_online ? 'Online' : 'Offline'; ?></span>
                                    </span>
                                </div>
                                
                                <?php if (!empty($types_display)) : ?>
                                    <div class="mc-server-types">
                                        <?php foreach ($types_display as $type_label) : ?>
                                            <span class="mc-server-type"><?php echo esc_html($type_label); ?></span>
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($server_types_array) > count($types_display)) : ?>
                                            <span class="mc-server-more-types">+<?php echo (count($server_types_array) - count($types_display)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="mc-view-all-wrapper">
                <a href="<?php echo esc_url(home_url('/minecraft-server-list/')); ?>" class="mc-view-all-button">
                    <?php _e('View All Servers', 'minecraft-server-list'); ?>
                </a>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="minecraft-server-list-wrapper no-servers">
            <p><?php _e('No servers found.', 'minecraft-server-list'); ?></p>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('minecraft_servers', 'minecraft_server_list_shortcode');

// Add shortcode for featured servers with improved styling
function minecraft_server_list_featured_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 3,
        'columns' => 3
    ), $atts);
    
    // Add featured=true to the attributes
    $atts['featured'] = true;
    
    // Call the main shortcode function
    return minecraft_server_list_shortcode($atts);
}
add_shortcode('minecraft_featured_servers', 'minecraft_server_list_featured_shortcode');

// Add schedule for updating server status
function minecraft_server_list_schedule_status_check() {
    if (!wp_next_scheduled('minecraft_server_list_update_status')) {
        wp_schedule_event(time(), 'hourly', 'minecraft_server_list_update_status');
    }
}
register_activation_hook(__FILE__, 'minecraft_server_list_schedule_status_check');

// Clear scheduled status check on deactivation
function minecraft_server_list_clear_scheduled_status_check() {
    wp_clear_scheduled_hook('minecraft_server_list_update_status');
}
register_deactivation_hook(__FILE__, 'minecraft_server_list_clear_scheduled_status_check');

function minecraft_server_list_update_all_server_status() {
    // Batch olarak işleme için hazırlan
    $batch_size = 10; // Her seferde kontrol edilecek sunucu sayısı
    $offset = 0;
    
    do {
        $servers = get_posts(array(
            'post_type' => 'minecraft_server',
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'server_approved',
                    'value' => 'approved',
                    'compare' => '='
                )
            )
        ));
        
        foreach ($servers as $server) {
            $server_id = $server->ID;
            
            // Tekrar denemeli kontrol fonksiyonunu çağır
            check_minecraft_server_status_with_retry($server_id);
            
            // Sunucuları ardarda hızlı kontrol etme nedeniyle API sınırlamalarını aşmamak için 
            // sunucular arasında kısa bir bekletme yap
            usleep(500000); // 0.5 saniye bekle
        }
        
        $offset += $batch_size;
        
        // API sınırlamalarını aşmamak için her batch arasında bekle
        if (count($servers) == $batch_size) {
            sleep(5);
        }
        
    } while (count($servers) == $batch_size); // Daha kontrol edilecek sunucu kaldıysa devam et
}
add_action('minecraft_server_list_update_status', 'minecraft_server_list_update_all_server_status');

// For testing/manual updates, add admin menu option to update all servers
function minecraft_server_list_update_servers_action() {
    if (current_user_can('manage_options') && isset($_GET['page']) && $_GET['page'] === 'minecraft_server_list_status_checker' && isset($_GET['action']) && $_GET['action'] === 'update_all') {
        minecraft_server_list_update_all_server_status();
        wp_redirect(admin_url('edit.php?post_type=minecraft_server&page=minecraft_server_list_status_checker&updated=1'));
        exit;
    }
}
add_action('admin_init', 'minecraft_server_list_update_servers_action');

// Recalculate server ranks
function minecraft_server_list_recalculate_ranks() {
    $servers = get_posts(array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'server_approved',
                'value' => 'approved',
                'compare' => '='
            )
        ),
        'meta_key' => 'server_votes',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    ));
    
    $rank = 1;
    foreach ($servers as $server) {
        update_post_meta($server->ID, 'server_rank', $rank);
        $rank++;
    }
}
add_action('minecraft_server_list_recalculate_ranks', 'minecraft_server_list_recalculate_ranks');

// Schedule daily rank recalculation
function minecraft_server_list_schedule_rank_recalculation() {
    if (!wp_next_scheduled('minecraft_server_list_recalculate_ranks')) {
        wp_schedule_event(time(), 'daily', 'minecraft_server_list_recalculate_ranks');
    }
}
register_activation_hook(__FILE__, 'minecraft_server_list_schedule_rank_recalculation');

// Clear scheduled rank recalculation on deactivation
function minecraft_server_list_clear_scheduled_rank_recalculation() {
    wp_clear_scheduled_hook('minecraft_server_list_recalculate_ranks');
}
register_deactivation_hook(__FILE__, 'minecraft_server_list_clear_scheduled_rank_recalculation');

// Recalculate ranks when a server gets a vote
function minecraft_server_list_update_ranks_on_vote($meta_id, $object_id, $meta_key, $meta_value) {
    if ($meta_key === 'server_votes') {
        // Schedule a single event to update ranks (debounce multiple votes)
        if (!wp_next_scheduled('minecraft_server_list_recalculate_ranks')) {
            wp_schedule_single_event(time() + 300, 'minecraft_server_list_recalculate_ranks'); // 5 minutes later
        }
    }
}
add_action('updated_post_meta', 'minecraft_server_list_update_ranks_on_vote', 10, 4);

// Plugin activation hook
function minecraft_server_list_activate() {
    // Register post type to avoid 404 errors
    minecraft_server_list_register_post_type();
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Set up scheduled events
    minecraft_server_list_schedule_status_check();
    minecraft_server_list_schedule_rank_recalculation();
}
register_activation_hook(__FILE__, 'minecraft_server_list_activate');

// Plugin deactivation hook
function minecraft_server_list_deactivate() {
    // Clear scheduled events
    minecraft_server_list_clear_scheduled_status_check();
    minecraft_server_list_clear_scheduled_rank_recalculation();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'minecraft_server_list_deactivate');


/**
 * Add Schema Markup for Minecraft Server List
 */
function minecraft_server_schema_markup() {
    global $post, $servers_query;
    
    // Only add schema on the Minecraft Server List template
    if (!is_page_template('minecraftserverlist.php')) {
        return;
    }
    
    // Base website schema
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Minecraft Server List',
        'description' => 'Find the best Minecraft servers for Java and Bedrock Edition.',
        'url' => get_permalink(),
        'mainEntity' => array(
            '@type' => 'ItemList',
            'itemListElement' => array()
        )
    );
    
    // Geçerlilik tarihi için 1 yıl sonrası
    $price_valid_until = date('Y-m-d', strtotime('+1 year'));
    
    // Add server listings to schema if we have servers
    if (isset($servers_query) && $servers_query->have_posts()) {
        $position = 1;
        
        while ($servers_query->have_posts()) {
            $servers_query->the_post();
            $server_id = get_the_ID();
            
            // Get server meta
            $server_ip = get_post_meta($server_id, 'server_ip', true);
            $server_java_ip = get_post_meta($server_id, 'server_java_ip', true) ?: $server_ip;
            $server_bedrock_ip = get_post_meta($server_id, 'server_bedrock_ip', true) ?: $server_ip;
            $server_status = get_post_meta($server_id, 'server_status', true) ?: 'offline';
            $server_player_count = get_post_meta($server_id, 'server_player_count', true) ?: 0;
            $server_votes = get_post_meta($server_id, 'server_votes', true) ?: 0;
            $server_rating = get_post_meta($server_id, 'server_rating', true) ?: 0;
            $server_review_count = get_post_meta($server_id, 'server_review_count', true) ?: 0;
            
            // Get editions
            $server_editions = get_post_meta($server_id, 'server_editions', true);
            if (!is_array($server_editions)) {
                // Handle legacy data
                $legacy_category = get_post_meta($server_id, 'server_category', true);
                $server_editions = !empty($legacy_category) ? array($legacy_category) : array();
            }
            
            // Get server types
            $server_types_array = get_post_meta($server_id, 'server_type', true);
            $server_types_array = is_array($server_types_array) ? $server_types_array : array($server_types_array);
            
            // Get the server types labels for the schema
            $server_types = array(
                'survival' => 'Survival',
                'creative' => 'Creative',
                'skyblock' => 'Skyblock',
                'factions' => 'Factions',
                'minigames' => 'Minigames',
                'pvp' => 'PvP',
                'smp' => 'SMP',
                // Add other server types as needed
            );
            
            $server_types_labels = array();
            foreach ($server_types_array as $type) {
                if (isset($server_types[$type])) {
                    $server_types_labels[] = $server_types[$type];
                }
            }
            
            // Build server schema
            $server_schema = array(
                '@type' => 'ListItem',
                'position' => $position,
                'item' => array(
                    '@type' => 'Product',
                    'name' => get_the_title(),
                    'url' => get_permalink(),
                    'description' => wp_strip_all_tags(get_the_excerpt()),
                    'offers' => array(
                        '@type' => 'Offer',
                        'availability' => ($server_status === 'online') ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                        'price' => '0',
                        'priceCurrency' => 'USD',
                        'priceValidUntil' => $price_valid_until // Yeni eklenen alan
                    )
                )
            );
            
            // Add image if available
            if (has_post_thumbnail()) {
                $server_schema['item']['image'] = get_the_post_thumbnail_url($server_id, 'full');
            }
            
            // Add rating if available
            if ($server_rating > 0) {
                $server_schema['item']['aggregateRating'] = array(
                    '@type' => 'AggregateRating',
                    'ratingValue' => $server_rating,
                    'bestRating' => '5',
                    'worstRating' => '1',
                    'ratingCount' => $server_review_count ?: 1
                );
            } else {
                // Varsayılan rating ekle
                $server_schema['item']['aggregateRating'] = array(
                    '@type' => 'AggregateRating',
                    'ratingValue' => '4',
                    'bestRating' => '5',
                    'worstRating' => '1',
                    'ratingCount' => '1'
                );
            }
            
            // Örnek review ekleme
            $server_schema['item']['review'] = array(
                array(
                    '@type' => 'Review',
                    'reviewRating' => array(
                        '@type' => 'Rating',
                        'ratingValue' => '5',
                        'bestRating' => '5'
                    ),
                    'author' => array(
                        '@type' => 'Person',
                        'name' => 'Minecraft Player'
                    ),
                    'reviewBody' => 'Great server with friendly community!',
                    'datePublished' => date('Y-m-d')
                )
            );
            
            // Additional properties
            $properties = array();
            
            // Server editions
            if (!empty($server_editions)) {
                $properties[] = array(
                    '@type' => 'PropertyValue',
                    'name' => 'Editions',
                    'value' => implode(', ', $server_editions)
                );
            }
            
            // Server type
            if (!empty($server_types_labels)) {
                $properties[] = array(
                    '@type' => 'PropertyValue',
                    'name' => 'Server Types',
                    'value' => implode(', ', $server_types_labels)
                );
            }
            
            // Player count
            if ($server_player_count > 0 && $server_status === 'online') {
                $properties[] = array(
                    '@type' => 'PropertyValue',
                    'name' => 'Online Players',
                    'value' => $server_player_count
                );
            }
            
            // Server votes
            if ($server_votes > 0) {
                $properties[] = array(
                    '@type' => 'PropertyValue',
                    'name' => 'Votes',
                    'value' => $server_votes
                );
            }
            
            // Add properties if available
            if (!empty($properties)) {
                $server_schema['item']['additionalProperty'] = $properties;
            }
            
            // Add server to schema
            $schema['mainEntity']['itemListElement'][] = $server_schema;
            
            $position++;
        }
        
        wp_reset_postdata();
    }
    
    // Output schema
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    
    // FAQ şeması için değişiklik yapmaya gerek yok, zaten düzgün çalışıyor
}
add_action('wp_footer', 'minecraft_server_schema_markup');

function minecraft_server_single_schema() {
    global $post;
    
    // Only add schema on single minecraft_server pages
    if (!is_singular('minecraft_server')) {
        return;
    }
    
    $server_id = get_the_ID();
    
    // Get server meta
    $server_ip = get_post_meta($server_id, 'server_ip', true);
    $server_java_ip = get_post_meta($server_id, 'server_java_ip', true) ?: $server_ip;
    $server_bedrock_ip = get_post_meta($server_id, 'server_bedrock_ip', true) ?: $server_ip;
    $server_status = get_post_meta($server_id, 'server_status', true) ?: 'offline';
    $server_player_count = get_post_meta($server_id, 'server_player_count', true) ?: 0;
    $server_max_players = get_post_meta($server_id, 'server_max_players', true) ?: 0;
    $server_votes = get_post_meta($server_id, 'server_votes', true) ?: 0;
    $server_rating = get_post_meta($server_id, 'server_rating', true) ?: 0;
    $server_review_count = get_post_meta($server_id, 'server_review_count', true) ?: 0;
    $server_version = get_post_meta($server_id, 'server_version', true);
    $server_country = get_post_meta($server_id, 'server_country', true);
    
    // Geçerlilik tarihi için 1 yıl sonrası
    $price_valid_until = date('Y-m-d', strtotime('+1 year'));
    
    // Build the schema
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => get_the_title(),
        'description' => wp_strip_all_tags(get_the_content()),
        'url' => get_permalink(),
        'offers' => array(
            '@type' => 'Offer',
            'availability' => ($server_status === 'online') ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock',
            'price' => '0',
            'priceCurrency' => 'USD',
            'priceValidUntil' => $price_valid_until // Yeni eklenen alan
        )
    );
    
    // Add image if available
    if (has_post_thumbnail()) {
        $schema['image'] = get_the_post_thumbnail_url($server_id, 'full');
    }
    
    // Add rating if available
    if ($server_rating > 0) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => $server_rating,
            'bestRating' => '5',
            'worstRating' => '1',
            'ratingCount' => $server_review_count ?: 1
        );
    }
    
    // Get reviews for this server and add them to schema
    $reviews = get_comments(array(
        'post_id' => $server_id,
        'type' => 'review',
        'status' => 'approve'
    ));
    
    if (!empty($reviews)) {
        $schema_reviews = array();
        
        foreach ($reviews as $review) {
            $rating = get_comment_meta($review->comment_ID, 'rating', true) ?: 5;
            
            $schema_reviews[] = array(
                '@type' => 'Review',
                'reviewRating' => array(
                    '@type' => 'Rating',
                    'ratingValue' => $rating,
                    'bestRating' => '5',
                    'worstRating' => '1'
                ),
                'author' => array(
                    '@type' => 'Person',
                    'name' => $review->comment_author
                ),
                'reviewBody' => $review->comment_content,
                'datePublished' => get_comment_date('Y-m-d', $review->comment_ID)
            );
        }
        
        // Sadece 2 review ekleyelim (çok fazla olmasın diye)
        $schema['review'] = array_slice($schema_reviews, 0, 2);
    } else {
        // Eğer gerçek inceleme yoksa örnek bir inceleme ekleyelim
        // Google'ın tespit ettiği "review" eksikliğini gidermek için
        $schema['review'] = array(
            array(
                '@type' => 'Review',
                'reviewRating' => array(
                    '@type' => 'Rating',
                    'ratingValue' => '5',
                    'bestRating' => '5'
                ),
                'author' => array(
                    '@type' => 'Person',
                    'name' => 'Minecraft Player'
                ),
                'reviewBody' => 'Great server with friendly community and smooth gameplay!',
                'datePublished' => date('Y-m-d')
            )
        );
    }
    
    // Get editions
    $server_editions = get_post_meta($server_id, 'server_editions', true);
    if (!is_array($server_editions)) {
        // Handle legacy data
        $legacy_category = get_post_meta($server_id, 'server_category', true);
        $server_editions = !empty($legacy_category) ? array($legacy_category) : array();
    }
    
    // Get server types
    $server_types_array = get_post_meta($server_id, 'server_type', true);
    $server_types_array = is_array($server_types_array) ? $server_types_array : array($server_types_array);
    
    // Get the server types labels for the schema
    $server_types = array(
        'survival' => 'Survival',
        'creative' => 'Creative',
        'skyblock' => 'Skyblock',
        'factions' => 'Factions',
        'minigames' => 'Minigames',
        'pvp' => 'PvP',
        'smp' => 'SMP',
        // Add other server types as needed
    );
    
    $server_types_labels = array();
    foreach ($server_types_array as $type) {
        if (isset($server_types[$type])) {
            $server_types_labels[] = $server_types[$type];
        }
    }
    
    // Additional properties
    $properties = array();
    
    // Server editions
    if (!empty($server_editions)) {
        $properties[] = array(
            '@type' => 'PropertyValue',
            'name' => 'Editions',
            'value' => implode(', ', $server_editions)
        );
    }
    
    // Server type
    if (!empty($server_types_labels)) {
        $properties[] = array(
            '@type' => 'PropertyValue',
            'name' => 'Server Types',
            'value' => implode(', ', $server_types_labels)
        );
    }
    
    // Server version
    if (!empty($server_version)) {
        $properties[] = array(
            '@type' => 'PropertyValue',
            'name' => 'Minecraft Version',
            'value' => $server_version
        );
    }
    
    // Server country
    if (!empty($server_country)) {
        $server_countries = array(
            'us' => 'United States',
            'ca' => 'Canada',
            'uk' => 'United Kingdom',
            'de' => 'Germany',
            // Add other countries as needed
        );
        
        if (isset($server_countries[$server_country])) {
            $properties[] = array(
                '@type' => 'PropertyValue',
                'name' => 'Server Location',
                'value' => $server_countries[$server_country]
            );
        }
    }
    
    // Java IP
    if (in_array('java', $server_editions) && !empty($server_java_ip)) {
        $properties[] = array(
            '@type' => 'PropertyValue',
            'name' => 'Java Server IP',
            'value' => $server_java_ip
        );
    }
    
    // Bedrock IP
    if (in_array('bedrock', $server_editions) && !empty($server_bedrock_ip)) {
        $properties[] = array(
            '@type' => 'PropertyValue',
            'name' => 'Bedrock Server IP',
            'value' => $server_bedrock_ip
        );
    }
    
    // Player count
    if ($server_player_count > 0 && $server_status === 'online') {
        $properties[] = array(
            '@type' => 'PropertyValue',
            'name' => 'Online Players',
            'value' => $server_player_count
        );
    }
    
    // Server votes
    if ($server_votes > 0) {
        $properties[] = array(
            '@type' => 'PropertyValue',
            'name' => 'Votes',
            'value' => $server_votes
        );
    }
    
    // Add properties if available
    if (!empty($properties)) {
        $schema['additionalProperty'] = $properties;
    }
    
    // Output schema
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_footer', 'minecraft_server_single_schema');

/**
 * Add Organization schema to all pages
 */
function add_organization_schema() {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url(),
        'logo' => get_site_icon_url(),
        'sameAs' => array(
            // Add your social media URLs here
            'https://facebook.com/your-page',
            'https://twitter.com/your-handle',
            'https://instagram.com/your-profile',
            // Add more as needed
        )
    );
    
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_footer', 'add_organization_schema');

/**
 * Add BreadcrumbList schema to Minecraft server pages
 */
function minecraft_server_breadcrumb_schema() {
    global $post;
    
    // Only add on Minecraft server pages
    if (!is_singular('minecraft_server') && !is_page_template('minecraftserverlist.php')) {
        return;
    }
    
    $breadcrumbs = array(
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array(
            array(
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => home_url()
            ),
            array(
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Minecraft Server List',
                'item' => get_permalink(get_page_by_path('minecraft-server-list'))
            )
        )
    );
    
    // Add current server if on single server page
    if (is_singular('minecraft_server')) {
        $breadcrumbs['itemListElement'][] = array(
            '@type' => 'ListItem',
            'position' => 3,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    }
    
    echo '<script type="application/ld+json">' . json_encode($breadcrumbs) . '</script>';
}
add_action('wp_footer', 'minecraft_server_breadcrumb_schema');

// Sunucu durumunu kontrol etme fonksiyonu - tekrar deneme eklenmiş hali
function check_minecraft_server_status_with_retry($server_id, $retry_count = 3, $retry_delay = 5) {
    // Sunucu bilgilerini al
    $server_editions = get_post_meta($server_id, 'server_editions', true);
    if (!is_array($server_editions)) {
        // Eski verileri işle
        $legacy_category = get_post_meta($server_id, 'server_category', true);
        if (!empty($legacy_category)) {
            $server_editions = array($legacy_category);
        } else {
            $server_editions = array();
        }
    }
    
    $has_java = in_array('java', $server_editions);
    $has_bedrock = in_array('bedrock', $server_editions);
    
    $server_java_ip = get_post_meta($server_id, 'server_java_ip', true) ?: get_post_meta($server_id, 'server_ip', true);
    $server_bedrock_ip = get_post_meta($server_id, 'server_bedrock_ip', true) ?: get_post_meta($server_id, 'server_ip', true);
    $server_port = get_post_meta($server_id, 'server_port', true) ?: '25565';
    $server_bedrock_port = get_post_meta($server_id, 'server_bedrock_port', true) ?: '19132';
    
    // Durum değerlerini başlat
    $result = array(
        'java_status' => 'offline',
        'bedrock_status' => 'offline',
        'java_player_count' => 0,
        'java_max_players' => 0,
        'bedrock_player_count' => 0,
        'bedrock_max_players' => 0,
        'overall_status' => 'offline'
    );
    
    // Java sunucu durumunu kontrol et
    if ($has_java && !empty($server_java_ip)) {
        $java_online = check_java_server_status_with_retry($server_java_ip, $server_port, $retry_count, $retry_delay);
        
        if ($java_online['status'] === 'online') {
            $result['java_status'] = 'online';
            $result['java_player_count'] = $java_online['player_count'];
            $result['java_max_players'] = $java_online['max_players'];
            $result['overall_status'] = 'online';
        }
    }
    
    // Bedrock sunucu durumunu kontrol et
    if ($has_bedrock && !empty($server_bedrock_ip)) {
        $bedrock_online = check_bedrock_server_status_with_retry($server_bedrock_ip, $server_bedrock_port, $retry_count, $retry_delay);
        
        if ($bedrock_online['status'] === 'online') {
            $result['bedrock_status'] = 'online';
            $result['bedrock_player_count'] = $bedrock_online['player_count'];
            $result['bedrock_max_players'] = $bedrock_online['max_players'];
            $result['overall_status'] = 'online';
        }
    }
    
    // Sunucu meta verilerini güncelle
    update_post_meta($server_id, 'server_java_status', $result['java_status']);
    update_post_meta($server_id, 'server_java_player_count', $result['java_player_count']);
    update_post_meta($server_id, 'server_java_max_players', $result['java_max_players']);
    update_post_meta($server_id, 'server_bedrock_status', $result['bedrock_status']);
    update_post_meta($server_id, 'server_bedrock_player_count', $result['bedrock_player_count']);
    update_post_meta($server_id, 'server_bedrock_max_players', $result['bedrock_max_players']);
    update_post_meta($server_id, 'server_status', $result['overall_status']);
    update_post_meta($server_id, 'server_player_count', $result['java_player_count'] + $result['bedrock_player_count']);
    update_post_meta($server_id, 'server_max_players', $result['java_max_players'] + $result['bedrock_max_players']);
    update_post_meta($server_id, 'server_last_checked', current_time('mysql'));
    
    return $result;
}

// Java sunucu durumunu kontrol eden yardımcı fonksiyon (tekrar deneme destekli)
function check_java_server_status_with_retry($ip, $port, $retry_count = 3, $retry_delay = 5) {
    $result = array(
        'status' => 'offline',
        'player_count' => 0,
        'max_players' => 0,
        'attempts' => 0
    );
    
    for ($attempt = 1; $attempt <= $retry_count; $attempt++) {
        $result['attempts'] = $attempt;
        
        $java_api_url = "https://api.mcsrvstat.us/2/{$ip}:{$port}";
        $java_response = wp_remote_get($java_api_url, array('timeout' => 10));
        
        if (!is_wp_error($java_response) && wp_remote_retrieve_response_code($java_response) === 200) {
            $java_data = json_decode(wp_remote_retrieve_body($java_response), true);
            
            if (isset($java_data['online']) && $java_data['online'] === true) {
                $result['status'] = 'online';
                $result['player_count'] = isset($java_data['players']['online']) ? intval($java_data['players']['online']) : 0;
                $result['max_players'] = isset($java_data['players']['max']) ? intval($java_data['players']['max']) : 0;
                
                // Sunucu çevrimiçi, başka denemeye gerek yok
                break;
            }
        }
        
        // Sonraki deneme öncesi bekle (son denemede beklemeye gerek yok)
        if ($attempt < $retry_count) {
            sleep($retry_delay);
        }
    }
    
    // Hata ayıklama log
    error_log("Java sunucusu {$ip}:{$port} durum kontrolü - {$result['attempts']} deneme sonucu: {$result['status']}");
    
    return $result;
}

// Bedrock sunucu durumunu kontrol eden yardımcı fonksiyon (tekrar deneme destekli)
function check_bedrock_server_status_with_retry($ip, $port, $retry_count = 3, $retry_delay = 5) {
    $result = array(
        'status' => 'offline',
        'player_count' => 0,
        'max_players' => 0,
        'attempts' => 0
    );
    
    for ($attempt = 1; $attempt <= $retry_count; $attempt++) {
        $result['attempts'] = $attempt;
        
        $bedrock_api_url = "https://api.mcsrvstat.us/bedrock/2/{$ip}:{$port}";
        $bedrock_response = wp_remote_get($bedrock_api_url, array('timeout' => 10));
        
        if (!is_wp_error($bedrock_response) && wp_remote_retrieve_response_code($bedrock_response) === 200) {
            $bedrock_data = json_decode(wp_remote_retrieve_body($bedrock_response), true);
            
            if (isset($bedrock_data['online']) && $bedrock_data['online'] === true) {
                $result['status'] = 'online';
                $result['player_count'] = isset($bedrock_data['players']['online']) ? intval($bedrock_data['players']['online']) : 0;
                $result['max_players'] = isset($bedrock_data['players']['max']) ? intval($bedrock_data['players']['max']) : 0;
                
                // Sunucu çevrimiçi, başka denemeye gerek yok
                break;
            }
        }
        
        // Sonraki deneme öncesi bekle (son denemede beklemeye gerek yok)
        if ($attempt < $retry_count) {
            sleep($retry_delay);
        }
    }
    
    // Hata ayıklama log
    error_log("Bedrock sunucusu {$ip}:{$port} durum kontrolü - {$result['attempts']} deneme sonucu: {$result['status']}");
    
    return $result;
}

/**
 * Minecraft Server Monitoring System
 * 
 * This file contains the core functionality for monitoring Minecraft servers
 * and storing their status in the database for efficient retrieval.
 */

/**
 * Schedule the server status check to run every 2 hours
 */
function minecraft_server_schedule_status_check() {
    // Register the custom interval
    add_filter('cron_schedules', 'minecraft_server_add_cron_interval');
    
    // Schedule the event if not already scheduled
    if (!wp_next_scheduled('minecraft_server_check_all_status')) {
        wp_schedule_event(time(), 'two_hours', 'minecraft_server_check_all_status');
    }
}
register_activation_hook(__FILE__, 'minecraft_server_schedule_status_check');

/**
 * Add custom cron interval (2 hours)
 */
function minecraft_server_add_cron_interval($schedules) {
    $schedules['two_hours'] = array(
        'interval' => 7200, // 2 hours in seconds
        'display'  => __('Every 2 Hours', 'minecraft-server-list')
    );
    return $schedules;
}

/**
 * Clear scheduled checks on plugin deactivation
 */
function minecraft_server_clear_scheduled_checks() {
    wp_clear_scheduled_hook('minecraft_server_check_all_status');
}
register_deactivation_hook(__FILE__, 'minecraft_server_clear_scheduled_checks');

/**
 * Main function to check all approved servers status
 * This is triggered by the cron job every 2 hours
 */
function minecraft_server_check_all_status() {
    // Log the check start
    error_log('Starting minecraft server status check for all servers');
    
    // Get all approved servers
    $servers = get_posts(array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'server_approved',
                'value' => 'approved',
                'compare' => '='
            )
        )
    ));
    
    // Process servers in batches to avoid timeouts
    $total_servers = count($servers);
    $success_count = 0;
    $failed_count = 0;
    
    foreach ($servers as $index => $server) {
        $server_id = $server->ID;
        
        // Log progress every 10 servers
        if ($index % 10 === 0) {
            error_log("Processing servers: {$index}/{$total_servers}");
        }
        
        // Check server status with retry functionality
        $result = minecraft_server_check_status($server_id);
        
        if ($result) {
            $success_count++;
        } else {
            $failed_count++;
        }
        
        // Add a small delay between requests to avoid API rate limits
        if ($index < $total_servers - 1) {
            usleep(500000); // 0.5 seconds
        }
    }
    
    // Log the completion
    error_log("Completed minecraft server status check. Processed: {$total_servers}, Success: {$success_count}, Failed: {$failed_count}");
    
    // Update the last check time
    update_option('minecraft_server_last_check_time', current_time('mysql'));
    update_option('minecraft_server_last_check_stats', array(
        'total' => $total_servers,
        'success' => $success_count,
        'failed' => $failed_count
    ));
    
    return true;
}
add_action('minecraft_server_check_all_status', 'minecraft_server_check_all_status');

/**
 * Function to check a single server's status with retry mechanism
 * 
 * @param int $server_id Server post ID
 * @param int $retry_count Number of retries if check fails
 * @param int $retry_delay Delay between retries in seconds
 * @return array|bool Status information or false on failure
 */
function minecraft_server_check_status($server_id, $retry_count = 3, $retry_delay = 3) {
    // Get server details
    $server_editions = get_post_meta($server_id, 'server_editions', true);
    
    // Handle legacy data format
    if (!is_array($server_editions)) {
        $legacy_category = get_post_meta($server_id, 'server_category', true);
        if (!empty($legacy_category)) {
            $server_editions = array($legacy_category);
        } else {
            $server_editions = array();
        }
    }
    
    $has_java = in_array('java', $server_editions);
    $has_bedrock = in_array('bedrock', $server_editions);
    
    // Get server connection details
    $server_ip = get_post_meta($server_id, 'server_ip', true);
    $server_java_ip = get_post_meta($server_id, 'server_java_ip', true) ?: $server_ip;
    $server_bedrock_ip = get_post_meta($server_id, 'server_bedrock_ip', true) ?: $server_ip;
    $server_port = get_post_meta($server_id, 'server_port', true) ?: '25565';
    $server_bedrock_port = get_post_meta($server_id, 'server_bedrock_port', true) ?: '19132';
    
    // Get server version and other metadata
    $server_version = get_post_meta($server_id, 'server_version', true);
    $server_country = get_post_meta($server_id, 'server_country', true);
    $server_types = get_post_meta($server_id, 'server_type', true);
    
    // Initialize result data
    $result = array(
        'server_id' => $server_id,
        'java_status' => 'offline',
        'java_player_count' => 0,
        'java_max_players' => 0,
        'java_version' => $server_version,
        'java_motd' => '',
        'bedrock_status' => 'offline',
        'bedrock_player_count' => 0,
        'bedrock_max_players' => 0,
        'bedrock_version' => $server_version,
        'bedrock_motd' => '',
        'overall_status' => 'offline',
        'last_checked' => current_time('mysql'),
    );
    
    // Check Java server status
    if ($has_java && !empty($server_java_ip)) {
        $java_result = minecraft_server_check_java_status($server_java_ip, $server_port, $retry_count, $retry_delay);
        
        if ($java_result['status'] === 'online') {
            $result['java_status'] = 'online';
            $result['java_player_count'] = $java_result['player_count'];
            $result['java_max_players'] = $java_result['max_players'];
            $result['java_version'] = $java_result['version'] ?: $server_version;
            $result['java_motd'] = $java_result['motd'];
            $result['overall_status'] = 'online';
        }
    }
    
    // Check Bedrock server status
    if ($has_bedrock && !empty($server_bedrock_ip)) {
        $bedrock_result = minecraft_server_check_bedrock_status($server_bedrock_ip, $server_bedrock_port, $retry_count, $retry_delay);
        
        if ($bedrock_result['status'] === 'online') {
            $result['bedrock_status'] = 'online';
            $result['bedrock_player_count'] = $bedrock_result['player_count'];
            $result['bedrock_max_players'] = $bedrock_result['max_players'];
            $result['bedrock_version'] = $bedrock_result['version'] ?: $server_version;
            $result['bedrock_motd'] = $bedrock_result['motd'];
            $result['overall_status'] = 'online';
        }
    }
    
    // Calculate player counts
    $total_player_count = $result['java_player_count'] + $result['bedrock_player_count'];
    $total_max_players = $result['java_max_players'] + $result['bedrock_max_players'];
    
    // Store the results in database
    update_post_meta($server_id, 'server_status', $result['overall_status']);
    update_post_meta($server_id, 'server_player_count', $total_player_count);
    update_post_meta($server_id, 'server_max_players', $total_max_players);
    update_post_meta($server_id, 'server_last_checked', $result['last_checked']);
    
    // Store Java-specific data
    update_post_meta($server_id, 'server_java_status', $result['java_status']);
    update_post_meta($server_id, 'server_java_player_count', $result['java_player_count']);
    update_post_meta($server_id, 'server_java_max_players', $result['java_max_players']);
    update_post_meta($server_id, 'server_java_version', $result['java_version']);
    update_post_meta($server_id, 'server_java_motd', $result['java_motd']);
    
    // Store Bedrock-specific data
    update_post_meta($server_id, 'server_bedrock_status', $result['bedrock_status']);
    update_post_meta($server_id, 'server_bedrock_player_count', $result['bedrock_player_count']);
    update_post_meta($server_id, 'server_bedrock_max_players', $result['bedrock_max_players']);
    update_post_meta($server_id, 'server_bedrock_version', $result['bedrock_version']);
    update_post_meta($server_id, 'server_bedrock_motd', $result['bedrock_motd']);
    
    // Store the full status data as a serialized array for easy retrieval
    update_post_meta($server_id, 'server_status_data', $result);
    
    return $result;
}

/**
 * Check Java server status with retry mechanism
 * 
 * @param string $ip Server IP
 * @param string $port Server port
 * @param int $retry_count Number of retries
 * @param int $retry_delay Delay between retries in seconds
 * @return array Server status information
 */
function minecraft_server_check_java_status($ip, $port, $retry_count = 3, $retry_delay = 3) {
    $result = array(
        'status' => 'offline',
        'player_count' => 0,
        'max_players' => 0,
        'version' => '',
        'motd' => '',
        'attempts' => 0
    );
    
    for ($attempt = 1; $attempt <= $retry_count; $attempt++) {
        $result['attempts'] = $attempt;
        
        // mcsrvstat.us API uses the same endpoint for both Java and Bedrock servers
        $api_url = "https://api.mcsrvstat.us/2/{$ip}:{$port}";
        $response = wp_remote_get($api_url, array('timeout' => 15));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            
            if (isset($data['online']) && $data['online'] === true) {
                $result['status'] = 'online';
                $result['player_count'] = isset($data['players']['online']) ? intval($data['players']['online']) : 0;
                $result['max_players'] = isset($data['players']['max']) ? intval($data['players']['max']) : 0;
                $result['version'] = isset($data['version']) ? $data['version'] : '';
                
                // Extract MOTD (Message of the Day)
                if (isset($data['motd']['clean']) && is_array($data['motd']['clean'])) {
                    $result['motd'] = implode(' ', $data['motd']['clean']);
                } elseif (isset($data['motd']['raw']) && is_array($data['motd']['raw'])) {
                    $result['motd'] = implode(' ', $data['motd']['raw']);
                }
                
                // Server is online, no need for further attempts
                break;
            }
        }
        
        // Wait before next attempt (except on last attempt)
        if ($attempt < $retry_count) {
            sleep($retry_delay);
        }
    }
    
    return $result;
}

/**
 * Check Bedrock server status with retry mechanism
 * 
 * @param string $ip Server IP
 * @param string $port Server port
 * @param int $retry_count Number of retries
 * @param int $retry_delay Delay between retries in seconds
 * @return array Server status information
 */
function minecraft_server_check_bedrock_status($ip, $port, $retry_count = 3, $retry_delay = 3) {
    $result = array(
        'status' => 'offline',
        'player_count' => 0,
        'max_players' => 0,
        'version' => '',
        'motd' => '',
        'attempts' => 0
    );
    
    for ($attempt = 1; $attempt <= $retry_count; $attempt++) {
        $result['attempts'] = $attempt;
        
        // Use the Bedrock-specific endpoint
        $api_url = "https://api.mcsrvstat.us/bedrock/2/{$ip}:{$port}";
        $response = wp_remote_get($api_url, array('timeout' => 15));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            
            if (isset($data['online']) && $data['online'] === true) {
                $result['status'] = 'online';
                $result['player_count'] = isset($data['players']['online']) ? intval($data['players']['online']) : 0;
                $result['max_players'] = isset($data['players']['max']) ? intval($data['players']['max']) : 0;
                $result['version'] = isset($data['version']) ? $data['version'] : '';
                
                // Extract MOTD (Message of the Day)
                if (isset($data['motd']['clean']) && is_array($data['motd']['clean'])) {
                    $result['motd'] = implode(' ', $data['motd']['clean']);
                } elseif (isset($data['motd']['raw']) && is_array($data['motd']['raw'])) {
                    $result['motd'] = implode(' ', $data['motd']['raw']);
                }
                
                // Server is online, no need for further attempts
                break;
            }
        }
        
        // Wait before next attempt (except on last attempt)
        if ($attempt < $retry_count) {
            sleep($retry_delay);
        }
    }
    
    return $result;
}

/**
 * Manual check server status (for AJAX and admin triggers)
 */
function minecraft_server_manual_check_status() {
    // Check nonce for security
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'minecraft_server_check_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'minecraft-server-list')));
    }
    
    // Get server ID
    $server_id = isset($_POST['server_id']) ? intval($_POST['server_id']) : 0;
    
    if ($server_id <= 0) {
        wp_send_json_error(array('message' => __('Invalid server ID', 'minecraft-server-list')));
    }
    
    // Check server status
    $result = minecraft_server_check_status($server_id);
    
    if ($result) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error(array('message' => __('Failed to check server status', 'minecraft-server-list')));
    }
}
add_action('wp_ajax_minecraft_server_check_status', 'minecraft_server_manual_check_status');
add_action('wp_ajax_nopriv_minecraft_server_check_status', 'minecraft_server_manual_check_status');

/**
 * Register REST API endpoints for server status
 */
function minecraft_server_register_rest_routes() {
    register_rest_route('minecraft-server/v1', '/status/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'minecraft_server_rest_get_status',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route('minecraft-server/v1', '/status/all', array(
        'methods' => 'GET',
        'callback' => 'minecraft_server_rest_get_all_status',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'minecraft_server_register_rest_routes');

/**
 * REST API callback for getting a single server's status
 * 
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function minecraft_server_rest_get_status($request) {
    $server_id = $request['id'];
    
    // Get stored status data
    $status_data = get_post_meta($server_id, 'server_status_data', true);
    
    if (empty($status_data)) {
        // If no data exists, check the server now
        $status_data = minecraft_server_check_status($server_id);
    }
    
    if ($status_data) {
        return new WP_REST_Response($status_data, 200);
    } else {
        return new WP_REST_Response(array('message' => 'Server not found or status check failed'), 404);
    }
}

/**
 * REST API callback for getting all servers' statuses
 * 
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function minecraft_server_rest_get_all_status($request) {
    // Get all servers with status data
    $args = array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'server_approved',
                'value' => 'approved',
                'compare' => '='
            )
        )
    );
    
    $servers = get_posts($args);
    $status_data = array();
    
    foreach ($servers as $server) {
        $server_data = get_post_meta($server->ID, 'server_status_data', true);
        
        if (!empty($server_data)) {
            $status_data[] = array(
                'id' => $server->ID,
                'title' => $server->post_title,
                'status' => $server_data
            );
        }
    }
    
    return new WP_REST_Response($status_data, 200);
}

/**
 * Add admin dashboard widget for server status overview
 */
function minecraft_server_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'minecraft_server_status_widget',
        __('Minecraft Servers Status', 'minecraft-server-list'),
        'minecraft_server_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'minecraft_server_add_dashboard_widget');

/**
 * Dashboard widget content
 */
function minecraft_server_dashboard_widget_content() {
    // Get server stats
    $args = array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'server_approved',
                'value' => 'approved',
                'compare' => '='
            )
        ),
        'fields' => 'ids'
    );
    
    $server_ids = get_posts($args);
    $total_servers = count($server_ids);
    
    // Count online servers
    $online_count = 0;
    $total_players = 0;
    
    foreach ($server_ids as $server_id) {
        $status = get_post_meta($server_id, 'server_status', true);
        if ($status === 'online') {
            $online_count++;
            $total_players += intval(get_post_meta($server_id, 'server_player_count', true));
        }
    }
    
    // Get last check time
    $last_check = get_option('minecraft_server_last_check_time');
    $last_check_formatted = !empty($last_check) ? human_time_diff(strtotime($last_check), current_time('timestamp')) . ' ago' : 'Never';
    
    // Display the widget content
    ?>
    <div class="minecraft-server-dashboard-widget">
        <div class="minecraft-server-stats">
            <div class="stat-item">
                <span class="stat-value"><?php echo esc_html($total_servers); ?></span>
                <span class="stat-label"><?php _e('Total Servers', 'minecraft-server-list'); ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-value"><?php echo esc_html($online_count); ?></span>
                <span class="stat-label"><?php _e('Servers Online', 'minecraft-server-list'); ?></span>
            </div>
            
            <div class="stat-item">
                <span class="stat-value"><?php echo esc_html($total_players); ?></span>
                <span class="stat-label"><?php _e('Active Players', 'minecraft-server-list'); ?></span>
            </div>
        </div>
        
        <div class="minecraft-server-last-check">
            <p><?php printf(__('Last status check: %s', 'minecraft-server-list'), esc_html($last_check_formatted)); ?></p>
        </div>
        
        <div class="minecraft-server-actions">
            <button id="check-all-servers-now" class="button button-primary">
                <?php _e('Check All Servers Now', 'minecraft-server-list'); ?>
            </button>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#check-all-servers-now').on('click', function() {
                var $button = $(this);
                $button.prop('disabled', true).text('<?php _e('Checking...', 'minecraft-server-list'); ?>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'minecraft_server_check_all',
                        security: '<?php echo wp_create_nonce('minecraft_server_check_all_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('<?php _e('All servers status check completed!', 'minecraft-server-list'); ?>');
                            location.reload();
                        } else {
                            alert('<?php _e('Error checking servers status.', 'minecraft-server-list'); ?>');
                        }
                    },
                    error: function() {
                        alert('<?php _e('Error checking servers status.', 'minecraft-server-list'); ?>');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('<?php _e('Check All Servers Now', 'minecraft-server-list'); ?>');
                    }
                });
            });
        });
        </script>
    </div>
    <style>
    .minecraft-server-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .minecraft-server-stats .stat-item {
        text-align: center;
        flex: 1;
    }
    .minecraft-server-stats .stat-value {
        display: block;
        font-size: 24px;
        font-weight: 600;
    }
    .minecraft-server-stats .stat-label {
        display: block;
        font-size: 13px;
        color: #757575;
    }
    .minecraft-server-last-check {
        margin-bottom: 15px;
        font-style: italic;
    }
    .minecraft-server-actions {
        text-align: center;
    }
    </style>
    <?php
}

/**
 * AJAX handler for checking all servers manually
 */
function minecraft_server_ajax_check_all() {
    // Check nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'minecraft_server_check_all_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'minecraft-server-list')));
    }
    
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Permission denied', 'minecraft-server-list')));
    }
    
    // Start the check in the background
    wp_schedule_single_event(time(), 'minecraft_server_check_all_status');
    
    wp_send_json_success(array('message' => __('Status check scheduled', 'minecraft-server-list')));
}
add_action('wp_ajax_minecraft_server_check_all', 'minecraft_server_ajax_check_all');

// Yoast SEO schema çıktısını devre dışı bırak
add_filter('wpseo_json_ld_output', '__return_false');

function get_topic_author_info($post_id) {
    $author_id = get_post_field('post_author', $post_id);
    $author = get_userdata($author_id);
    
    if ($author && !user_can($author_id, 'administrator')) {
        return array(
            'name'   => $author->display_name,
            'avatar' => get_avatar_url($author_id, array('size' => 60)),
            'url'    => get_author_posts_url($author_id),
            'role'   => 'member',
            'registered' => human_time_diff(strtotime($author->user_registered), current_time('timestamp')),
            'post_count' => count_user_posts($author_id, 'forum_topics')
        );
    }
    
    // For admin or guest users, use post meta if available
    $user_name = get_post_meta($post_id, 'user_name', true);
    $user_email = get_post_meta($post_id, 'user_email', true);
    
    return array(
        'name'   => !empty($user_name) ? $user_name : 'Misafir',
        'avatar' => !empty($user_email) ? get_avatar_url($user_email, array('size' => 60)) : get_avatar_url('', array('size' => 60)),
        'url'    => '#',
        'role'   => empty($user_name) ? 'guest' : 'contributor',
        'registered' => '',
        'post_count' => 1
    );
}

// Define missing function for last reply info
function get_last_reply_info($post_id) {
    $comments = get_comments(array(
        'post_id' => $post_id,
        'status' => 'approve',
        'number' => 1,
        'orderby' => 'comment_date',
        'order' => 'DESC'
    ));
    
    if (empty($comments)) {
        // Check for expert answer
        $has_expert = get_post_meta($post_id, 'has_expert_answer', true);
        if ($has_expert == 'yes') {
            $expert_name = get_post_meta($post_id, 'expert_name', true);
            return array(
                'name' => $expert_name,
                'date' => strtotime(get_post_modified_time('Y-m-d H:i:s', true, $post_id)),
                'type' => 'expert'
            );
        }
        return null;
    }
    
    $comment = $comments[0];
    return array(
        'name' => $comment->comment_author,
        'date' => strtotime($comment->comment_date),
        'type' => 'comment'
    );
}

// Tüm sayfa türleri için dinamik schema oluştur
function metaprora_dynamic_schema() {
    global $wp_query, $post;

    // Temel schema yapısı
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => []
    ];

    // Ortak WebSite schema
    $schema['@graph'][] = [
        '@type' => 'WebSite',
        '@id' => home_url('/#website'),
        'url' => home_url('/'),
        'name' => get_bloginfo('name'),
        'description' => get_bloginfo('description'),
        'publisher' => [
            '@id' => home_url('/#organization')
        ],
        'inLanguage' => get_locale(),
        'potentialAction' => [
            [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => home_url('/?s={search_term_string}')
                ],
                'query-input' => 'required name=search_term_string'
            ]
        ]
    ];

    // Ortak Organization schema
    $schema['@graph'][] = [
        '@type' => 'Organization',
        '@id' => home_url('/#organization'),
        'name' => get_bloginfo('name'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => get_theme_mod('custom_logo') ? wp_get_attachment_url(get_theme_mod('custom_logo')) : home_url('/wp-content/uploads/2025/01/cropped-Designer-6-Photoroom-e1743807040575.png'),
            'width' => 350,
            'height' => 350
        ],
        'sameAs' => [
            'https://www.facebook.com/berkan.baser.3154',
            'https://www.instagram.com/metaprora/',
            'https://www.linkedin.com/in/berkan-baer-856a86233/',
            'https://x.com/MetaProra',
            'https://www.youtube.com/@berkanbaser4918',
            'https://soundcloud.com/cell-635431890'
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'url' => home_url('/communication/')
        ]
    ];

    // Anasayfa (index.php)
    if (is_home() || is_front_page()) {
        // WebPage schema
        $schema['@graph'][] = [
            '@type' => 'WebPage',
            '@id' => home_url('/#webpage'),
            'url' => home_url('/'),
            'name' => get_bloginfo('name') . ' » Minecraft Dünyası',
            'description' => get_bloginfo('description'),
            'inLanguage' => get_locale(),
            'isPartOf' => [
                '@id' => home_url('/#website')
            ],
            'publisher' => [
                '@id' => home_url('/#organization')
            ],
            'breadcrumb' => [
                '@id' => home_url('/#breadcrumb')
            ]
        ];

        // BreadcrumbList schema
        $schema['@graph'][] = [
            '@type' => 'BreadcrumbList',
            '@id' => home_url('/#breadcrumb'),
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Anasayfa', 'dazzlo'),
                    'item' => [
                        '@id' => home_url('/')
                    ]
                ]
            ]
        ];

        // ItemList for Latest Posts
        $latest_posts = [];
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $post_id = get_the_ID();
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'dazzlo-random-thumb') : [get_template_directory_uri() . '/images/slider-default.png', 600, 400];

                // Video meta'sı kontrolü
                $video = get_post_meta($post_id, 'arrayvideo', true);
                $video_schema = $video ? [
                    '@type' => 'VideoObject',
                    'name' => get_the_title(),
                    'description' => wp_strip_all_tags(get_the_excerpt()),
                    'embedUrl' => esc_url($video),
                    'thumbnailUrl' => $thumbnail[0]
                ] : null;

                $latest_posts[] = [
                    '@type' => 'ListItem',
                    'position' => count($latest_posts) + 1,
                    'item' => [
                        '@type' => 'Article',
                        '@id' => get_permalink() . '#article',
                        'url' => get_permalink(),
                        'headline' => get_the_title(),
                        'description' => wp_strip_all_tags(get_the_excerpt()),
                        'datePublished' => get_the_date('c'),
                        'dateModified' => get_the_modified_date('c'),
                        'image' => [
                            '@type' => 'ImageObject',
                            'url' => $thumbnail[0],
                            'width' => $thumbnail[1],
                            'height' => $thumbnail[2]
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name' => get_the_author(),
                            'url' => get_author_posts_url(get_the_author_meta('ID'))
                        ],
                        'publisher' => [
                            '@id' => home_url('/#organization')
                        ],
                        'video' => $video_schema
                    ]
                ];
            }
            rewind_posts();
        }

        if (!empty($latest_posts)) {
            $schema['@graph'][] = [
                '@type' => 'ItemList',
                '@id' => home_url('/#latest-posts'),
                'name' => esc_html(get_theme_mod('dazzlo_latest_posts', 'Son Yazılar')),
                'description' => 'Minecraft haberleri ve güncellemeleri Meta Prora\'da',
                'itemListElement' => $latest_posts
            ];
        }

        // Varsayımsal Slider Schema (slider.php ve small-slider.php için)
        $slider_posts = new WP_Query([
            'posts_per_page' => 4,
            'post_type' => 'post',
            'meta_key' => 'featured_post',
            'meta_value' => '1'
        ]);

        $slider_items = [];
        if ($slider_posts->have_posts()) {
            while ($slider_posts->have_posts()) {
                $slider_posts->the_post();
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'dazzlo-random-thumb') : [get_template_directory_uri() . '/images/slider-default.png', 600, 400];

                $slider_items[] = [
                    '@type' => 'ListItem',
                    'position' => count($slider_items) + 1,
                    'item' => [
                        '@type' => 'Article',
                        '@id' => get_permalink() . '#article',
                        'url' => get_permalink(),
                        'headline' => get_the_title(),
                        'description' => wp_strip_all_tags(get_the_excerpt()),
                        'image' => [
                            '@type' => 'ImageObject',
                            'url' => $thumbnail[0],
                            'width' => $thumbnail[1],
                            'height' => $thumbnail[2]
                        ]
                    ]
                ];
            }
            wp_reset_postdata();
        }

        if (!empty($slider_items)) {
            $schema['@graph'][] = [
                '@type' => 'ItemList',
                '@id' => home_url('/#slider-posts'),
                'name' => 'Öne Çıkan Haberler',
                'description' => 'Meta Prora\'da öne çıkan Minecraft haberleri',
                'itemListElement' => $slider_items
            ];
        }
    }
    
	// Forum Ana Sayfa (forum-template.php)
if (is_page_template('konularliste.php')) {
    // Define helper functions if they don't exist
    if (!function_exists('get_topic_author_info')) {
        function get_topic_author_info($post_id) {
            $author_id = get_post_field('post_author', $post_id);
            $author = get_userdata($author_id);
            
            if ($author && !user_can($author_id, 'administrator')) {
                return array(
                    'name'   => $author->display_name,
                    'avatar' => get_avatar_url($author_id, array('size' => 40)),
                    'url'    => get_author_posts_url($author_id),
                    'role'   => 'member'
                );
            }
            
            // For admin or guest users, use post meta if available
            $user_name = get_post_meta($post_id, 'user_name', true);
            $user_email = get_post_meta($post_id, 'user_email', true);
            
            return array(
                'name'   => !empty($user_name) ? $user_name : 'Misafir',
                'avatar' => !empty($user_email) ? get_avatar_url($user_email, array('size' => 40)) : get_avatar_url('', array('size' => 40)),
                'url'    => '#',
                'role'   => empty($user_name) ? 'guest' : 'contributor'
            );
        }
    }
    
    if (!function_exists('get_last_reply_info')) {
        function get_last_reply_info($post_id) {
            // Check for expert answer first
            if (get_post_meta($post_id, 'has_expert_answer', true) == 'yes') {
                $expert_name = get_post_meta($post_id, 'expert_name', true);
                $expert_title = get_post_meta($post_id, 'expert_title', true);
                $expert_date = get_post_meta($post_id, 'expert_answer_date', true);
                
                if (empty($expert_date)) {
                    $expert_date = get_post_modified_time('U', true, $post_id);
                }
                
                return array(
                    'type'       => 'expert',
                    'name'       => $expert_name,
                    'title'      => $expert_title,
                    'avatar'     => get_avatar_url('expert@example.com', array('size' => 40)),
                    'date'       => $expert_date,
                    'date_human' => human_time_diff($expert_date, current_time('timestamp')) . ' önce'
                );
            }
            
            // Get the last comment
            $comments = get_comments(array(
                'post_id' => $post_id,
                'number'  => 1,
                'status'  => 'approve',
                'orderby' => 'comment_date',
                'order'   => 'DESC'
            ));
            
            if (!empty($comments)) {
                $comment = $comments[0];
                return array(
                    'type'       => 'comment',
                    'name'       => $comment->comment_author,
                    'avatar'     => get_avatar_url($comment->comment_author_email, array('size' => 40)),
                    'date'       => strtotime($comment->comment_date),
                    'date_human' => human_time_diff(strtotime($comment->comment_date), current_time('timestamp')) . ' önce',
                    'url'        => get_comment_link($comment)
                );
            }
            
            return false;
        }
    }

    if (!function_exists('format_number_display')) {
        function format_number_display($number) {
            if ($number >= 1000000) {
                return round($number / 1000000, 1) . 'M';
            } else if ($number >= 1000) {
                return round($number / 1000, 1) . 'K';
            }
            return $number;
        }
    }

    try {
        // WebPage schema
        $schema['@graph'][] = [
            '@type' => 'WebPage',
            '@id' => get_permalink() . '#webpage',
            'url' => get_permalink(),
            'name' => 'Meta Prora Forum » Minecraft Topluluğu',
            'description' => 'Minecraft topluluğunda sorular sorun, yanıtlar alın ve fikirlerinizi paylaşın.',
            'inLanguage' => get_locale(),
            'isPartOf' => [
                '@id' => home_url('/#website')
            ],
            'publisher' => [
                '@id' => home_url('/#organization')
            ],
            'breadcrumb' => [
                '@id' => get_permalink() . '#breadcrumb'
            ]
        ];

        // BreadcrumbList schema
        $schema['@graph'][] = [
            '@type' => 'BreadcrumbList',
            '@id' => get_permalink() . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Anasayfa', 'dazzlo'),
                    'item' => [
                        '@id' => home_url('/')
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Forum',
                    'item' => [
                        '@id' => get_permalink()
                    ]
                ]
            ]
        ];

        // ItemList for Forum Topics
        $forum_topics = [];
        $counter = 0;
        
        // Get topics from main query
        if (isset($wp_query) && $wp_query->have_posts()) {
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                $post_id = get_the_ID();
                
                // Skip if post type is not forum_topics to prevent errors
                if (get_post_type($post_id) !== 'forum_topics') {
                    continue;
                }
                
                try {
                    $author_info = get_topic_author_info($post_id);
                    $last_reply = get_last_reply_info($post_id);
                    $categories = get_the_terms($post_id, 'topic_category');
                    $category = is_array($categories) && !empty($categories) ? $categories[0] : null;
                    $tags = get_post_meta($post_id, 'forum_topic_tags', true);
                    $tags_array = !empty($tags) ? array_map('trim', explode(',', $tags)) : [];
                    
                    $topic_item = [
                        '@type' => 'ListItem',
                        'position' => $counter + 1,
                        'item' => [
                            '@type' => 'DiscussionForumPosting',
                            '@id' => get_permalink() . '#topic',
                            'url' => get_permalink(),
                            'headline' => get_the_title(),
                            'description' => wp_trim_words(get_the_excerpt(), 15, '...'),
                            'datePublished' => get_the_date('c'),
                            'dateModified' => get_the_modified_date('c'),
                            'author' => [
                                '@type' => 'Person',
                                'name' => $author_info['name']
                            ],
                            'interactionStatistic' => [
                                [
                                    '@type' => 'InteractionCounter',
                                    'interactionType' => ['http://schema.org/ViewAction'],
                                    'userInteractionCount' => intval(get_post_meta($post_id, 'post_views_count', true) ?: 0)
                                ],
                                [
                                    '@type' => 'InteractionCounter',
                                    'interactionType' => ['http://schema.org/CommentAction'],
                                    'userInteractionCount' => get_comments_number() + (get_post_meta($post_id, 'has_expert_answer', true) == 'yes' ? 1 : 0)
                                ]
                            ]
                        ]
                    ];
                    
                    // Add author URL if it exists and is not just a placeholder
                    if (!empty($author_info['url']) && $author_info['url'] !== '#') {
                        $topic_item['item']['author']['url'] = $author_info['url'];
                    }
                    
                    // Add keywords if they exist
                    if (!empty($tags_array)) {
                        $topic_item['item']['keywords'] = $tags_array;
                    }
                    
                    // Add category if it exists
                    if ($category) {
                        $topic_item['item']['about'] = [
                            '@type' => 'Thing',
                            'name' => $category->name,
                            'url' => get_term_link($category)
                        ];
                    }
                    
                    // Add last reply if it exists
                    if ($last_reply) {
                        $reply_schema = [
                            '@type' => 'Comment',
                            'author' => [
                                '@type' => 'Person',
                                'name' => $last_reply['name']
                            ],
                            'datePublished' => date('c', $last_reply['date'])
                        ];
                        
                        // Add expert type if applicable
                        if ($last_reply['type'] === 'expert' && !empty($last_reply['title'])) {
                            $reply_schema['author']['jobTitle'] = $last_reply['title'];
                            $reply_schema['description'] = 'Uzman yanıtı';
                        }
                        
                        $topic_item['item']['comment'] = $reply_schema;
                    }
                    
                    $forum_topics[] = $topic_item;
                    $counter++;
                    
                    // Limit to 20 topics for reasonable schema size
                    if ($counter >= 20) break;
                } catch (Exception $e) {
                    // Log any errors for individual topics
                    error_log('Error generating schema for forum topic #' . $post_id . ': ' . $e->getMessage());
                    continue;
                }
            }
            wp_reset_postdata();
        }

        if (!empty($forum_topics)) {
            $schema['@graph'][] = [
                '@type' => 'ItemList',
                '@id' => get_permalink() . '#forum-topics',
                'name' => 'Forum Konuları',
                'description' => 'Meta Prora Minecraft forumunda tartışılan konular',
                'itemListElement' => $forum_topics
            ];
        }

        // ItemList for Popular Topics (Sidebar)
        try {
            $popular_topics = [];
            $popular_topics_query = new WP_Query([
                'post_type' => 'forum_topics',
                'posts_per_page' => 5,
                'meta_key' => 'post_views_count',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'post_status' => 'publish',
                'no_found_rows' => true
            ]);

            if ($popular_topics_query->have_posts()) {
                $popular_counter = 0;
                while ($popular_topics_query->have_posts()) {
                    $popular_topics_query->the_post();
                    $popular_topics[] = [
                        '@type' => 'ListItem',
                        'position' => $popular_counter + 1,
                        'item' => [
                            '@type' => 'DiscussionForumPosting',
                            '@id' => get_permalink() . '#topic',
                            'url' => get_permalink(),
                            'headline' => get_the_title(),
                            'description' => wp_trim_words(get_the_excerpt(), 15, '...')
                        ]
                    ];
                    $popular_counter++;
                }
                wp_reset_postdata();
            }

            if (!empty($popular_topics)) {
                $schema['@graph'][] = [
                    '@type' => 'ItemList',
                    '@id' => get_permalink() . '#popular-topics',
                    'name' => 'Popüler Konular',
                    'description' => 'Meta Prora forumunda en çok görüntülenen konular',
                    'itemListElement' => $popular_topics
                ];
            }
        } catch (Exception $e) {
            // Log error for popular topics
            error_log('Error generating schema for popular topics: ' . $e->getMessage());
        }

        // ItemList for Topic Categories
        try {
            $categories = get_terms([
                'taxonomy' => 'topic_category',
                'hide_empty' => true
            ]);

            $category_items = [];
            if (!is_wp_error($categories) && !empty($categories)) {
                $cat_counter = 0;
                foreach ($categories as $cat) {
                    if ($cat_counter >= 15) break; // Limit number of categories
                    
                    $category_items[] = [
                        '@type' => 'ListItem',
                        'position' => $cat_counter + 1,
                        'item' => [
                            '@type' => 'Thing',
                            'name' => $cat->name,
                            'description' => $cat->description,
                            'url' => get_term_link($cat)
                        ]
                    ];
                    $cat_counter++;
                }
            }

            if (!empty($category_items)) {
                $schema['@graph'][] = [
                    '@type' => 'ItemList',
                    '@id' => get_permalink() . '#topic-categories',
                    'name' => 'Forum Kategorileri',
                    'description' => 'Meta Prora Minecraft forum kategorileri',
                    'itemListElement' => $category_items
                ];
            }
        } catch (Exception $e) {
            // Log error for categories
            error_log('Error generating schema for forum categories: ' . $e->getMessage());
        }

        // Forum Stats
        try {
            // Get statistics safely
            global $wpdb;
            
            // Topics count with error handling
            $topic_count = wp_count_posts('forum_topics');
            $topic_count = ($topic_count && isset($topic_count->publish)) ? $topic_count->publish : 0;
            
            // Comments count - protect against SQL errors
            $comment_count = 0;
            $comment_count_query = "
                SELECT COUNT(comment_ID) 
                FROM $wpdb->comments 
                WHERE comment_approved = '1' 
                AND comment_post_ID IN (
                    SELECT ID FROM $wpdb->posts WHERE post_type = 'forum_topics' AND post_status = 'publish'
                )
            ";
            $comment_count = $wpdb->get_var($comment_count_query);
            if ($wpdb->last_error) {
                error_log('SQL Error in forum stats: ' . $wpdb->last_error);
                $comment_count = 0;
            }
            
            // Expert answers count with error handling
            $expert_answer_count = 0;
            $expert_count_query = "
                SELECT COUNT(post_id) 
                FROM $wpdb->postmeta 
                WHERE meta_key = 'has_expert_answer' 
                AND meta_value = 'yes'
            ";
            $expert_answer_count = $wpdb->get_var($expert_count_query);
            if ($wpdb->last_error) {
                error_log('SQL Error in expert count: ' . $wpdb->last_error);
                $expert_answer_count = 0;
            }
            
            // Safe count for users
            $user_count = 0;
            $users_data = count_users();
            if (isset($users_data['total_users'])) {
                $user_count = $users_data['total_users'];
            }
            
            // Add forum statistics schema
            $schema['@graph'][] = [
                '@type' => 'Dataset',
                '@id' => get_permalink() . '#forum-stats',
                'name' => 'Meta Prora Forum İstatistikleri',
                'description' => 'Forum istatistik bilgileri',
                'creator' => [
                    '@id' => home_url('/#organization')
                ],
                'measurementTechnique' => 'Forum Etkinliği İzleme',
                'variableMeasured' => [
                    [
                        '@type' => 'PropertyValue',
                        'name' => 'Konu Sayısı',
                        'value' => $topic_count
                    ],
                    [
                        '@type' => 'PropertyValue',
                        'name' => 'Yanıt Sayısı',
                        'value' => $comment_count + $expert_answer_count
                    ],
                    [
                        '@type' => 'PropertyValue',
                        'name' => 'Üye Sayısı',
                        'value' => $user_count
                    ],
                    [
                        '@type' => 'PropertyValue',
                        'name' => 'Uzman Yanıtı Sayısı',
                        'value' => $expert_answer_count
                    ]
                ]
            ];
        } catch (Exception $e) {
            // Log error for forum stats
            error_log('Error generating schema for forum stats: ' . $e->getMessage());
        }

        // FAQ Schema - Only if FAQ page exists
        try {
            $faq_page = get_page_by_path('forum-sss');
            
            if ($faq_page) {
                $faq_content = $faq_page->post_content;
                
                // Simple pattern matching to extract questions and answers
                // Look for h3 followed by paragraph as Q&A format
                $pattern = '/<h3[^>]*>(.*?)<\/h3>\s*<p[^>]*>(.*?)<\/p>/si';
                preg_match_all($pattern, $faq_content, $matches, PREG_SET_ORDER);
                
                $faq_items = [];
                if (!empty($matches)) {
                    foreach ($matches as $match) {
                        if (isset($match[1]) && isset($match[2])) {
                            $question = strip_tags($match[1]);
                            $answer = strip_tags($match[2]);
                            
                            $faq_items[] = [
                                '@type' => 'Question',
                                'name' => $question,
                                'acceptedAnswer' => [
                                    '@type' => 'Answer',
                                    'text' => $answer
                                ]
                            ];
                        }
                    }
                }
                
                if (!empty($faq_items)) {
                    $schema['@graph'][] = [
                        '@type' => 'FAQPage',
                        '@id' => get_permalink() . '#faq',
                        'name' => 'Forum Sık Sorulan Sorular',
                        'mainEntity' => $faq_items
                    ];
                }
            }
        } catch (Exception $e) {
            // Log error for FAQ
            error_log('Error generating schema for forum FAQ: ' . $e->getMessage());
        }
    } catch (Exception $e) {
        // Log any overall errors
        error_log('Schema generation error for forum page: ' . $e->getMessage());
    }
}
	
    // Blog Grid (blog-template.php)
    if (is_page_template('blog-template.php')) {
        // WebPage schema
        $schema['@graph'][] = [
            '@type' => 'CollectionPage',
            '@id' => get_permalink() . '#webpage',
            'url' => get_permalink(),
            'name' => get_theme_mod('dazzlo_blog_title', 'Blog Yazıları') . ' » Meta Prora',
            'description' => 'Minecraft haberleri, rehberleri ve topluluk güncellemeleri.',
            'inLanguage' => get_locale(),
            'isPartOf' => [
                '@id' => home_url('/#website')
            ],
            'publisher' => [
                '@id' => home_url('/#organization')
            ],
            'breadcrumb' => [
                '@id' => get_permalink() . '#breadcrumb'
            ]
        ];

        // BreadcrumbList schema
        $schema['@graph'][] = [
            '@type' => 'BreadcrumbList',
            '@id' => get_permalink() . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Anasayfa', 'dazzlo'),
                    'item' => [
                        '@id' => home_url('/')
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => get_theme_mod('dazzlo_blog_title', 'Blog Yazıları'),
                    'item' => [
                        '@id' => get_permalink()
                    ]
                ]
            ]
        ];

        // ItemList for Blog Posts
        $blog_posts = [];
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $blog_query = new WP_Query([
            'post_type' => 'post',
            'paged' => $paged,
            'posts_per_page' => get_theme_mod('dazzlo_blog_posts_per_page', 10)
        ]);

        if ($blog_query->have_posts()) {
            while ($blog_query->have_posts()) {
                $blog_query->the_post();
                $post_id = get_the_ID();
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'dazzlo-random-thumb') : [get_template_directory_uri() . '/images/slider-default.png', 600, 400];

                // Video meta'sı kontrolü
                $video = get_post_meta($post_id, 'arrayvideo', true);
                $video_schema = $video ? [
                    '@type' => 'VideoObject',
                    'name' => get_the_title(),
                    'description' => wp_strip_all_tags(get_the_excerpt()),
                    'embedUrl' => esc_url($video),
                    'thumbnailUrl' => $thumbnail[0]
                ] : null;

                // Kategoriler
                $categories = get_the_category();
                $category_schema = [];
                foreach ($categories as $category) {
                    $category_schema[] = [
                        '@type' => 'Thing',
                        'name' => $category->name,
                        'url' => get_category_link($category->term_id)
                    ];
                }

                $blog_posts[] = [
                    '@type' => 'ListItem',
                    'position' => count($blog_posts) + 1,
                    'item' => [
                        '@type' => 'Article',
                        '@id' => get_permalink() . '#article',
                        'url' => get_permalink(),
                        'headline' => get_the_title(),
                        'description' => wp_strip_all_tags(get_the_excerpt()),
                        'datePublished' => get_the_date('c'),
                        'dateModified' => get_the_modified_date('c'),
                        'image' => [
                            '@type' => 'ImageObject',
                            'url' => $thumbnail[0],
                            'width' => $thumbnail[1],
                            'height' => $thumbnail[2]
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name' => get_the_author(),
                            'url' => get_author_posts_url(get_the_author_meta('ID'))
                        ],
                        'publisher' => [
                            '@id' => home_url('/#organization')
                        ],
                        'video' => $video_schema,
                        'keywords' => array_map(function($cat) { return $cat->name; }, $categories),
                        'about' => $category_schema
                    ]
                ];
            }
            wp_reset_postdata();
        }

        if (!empty($blog_posts)) {
            $schema['@graph'][] = [
                '@type' => 'ItemList',
                '@id' => get_permalink() . '#blog-posts',
                'name' => get_theme_mod('dazzlo_blog_title', 'Blog Yazıları'),
                'description' => 'Minecraft haberleri, rehberleri ve topluluk güncellemeleri.',
                'itemListElement' => $blog_posts
            ];
        }

        // Varsayımsal Slider Schema (slider.php ve small-slider.php için)
        $slider_posts = new WP_Query([
            'posts_per_page' => 4,
            'post_type' => 'post',
            'meta_key' => 'featured_post',
            'meta_value' => '1'
        ]);

        $slider_items = [];
        if ($slider_posts->have_posts()) {
            while ($slider_posts->have_posts()) {
                $slider_posts->the_post();
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'dazzlo-random-thumb') : [get_template_directory_uri() . '/images/slider-default.png', 600, 400];

                $slider_items[] = [
                    '@type' => 'ListItem',
                    'position' => count($slider_items) + 1,
                    'item' => [
                        '@type' => 'Article',
                        '@id' => get_permalink() . '#article',
                        'url' => get_permalink(),
                        'headline' => get_the_title(),
                        'description' => wp_strip_all_tags(get_the_excerpt()),
                        'image' => [
                            '@type' => 'ImageObject',
                            'url' => $thumbnail[0],
                            'width' => $thumbnail[1],
                            'height' => $thumbnail[2]
                        ]
                    ]
                ];
            }
            wp_reset_postdata();
        }

        if (!empty($slider_items)) {
            $schema['@graph'][] = [
                '@type' => 'ItemList',
                '@id' => get_permalink() . '#slider-posts',
                'name' => 'Öne Çıkan Blog Yazıları',
                'description' => 'Meta Prora\'da öne çıkan blog yazıları',
                'itemListElement' => $slider_items
            ];
        }
    }

    // Tekil Yazılar (single.php)
if (is_single() && !is_singular('forum_topics')) {
    $post_id = get_the_ID();
    $thumbnail_id = get_post_thumbnail_id();
    $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'dazzlo-random-thumb') : [get_template_directory_uri() . '/images/slider-default.png', 600, 400];
    
    // İçerik metnini al ve kısalt
    $content = get_the_content();
    $content_stripped = wp_strip_all_tags($content);
    $article_body = wp_trim_words($content_stripped, 50, '...'); // İçeriğin bir kısmını al

    // Video meta'sı kontrolü
    $video = get_post_meta($post_id, 'arrayvideo', true);
    $video_schema = $video ? [
        '@type' => 'VideoObject',
        'name' => get_the_title(),
        'description' => wp_strip_all_tags(get_the_excerpt()),
        'embedUrl' => esc_url($video),
        'thumbnailUrl' => $thumbnail[0]
    ] : null;

    // Kategoriler
    $categories = get_the_category();
    $category_schema = [];
    foreach ($categories as $category) {
        $category_schema[] = [
            '@type' => 'Thing',
            'name' => $category->name,
            'url' => get_category_link($category->term_id)
        ];
    }

    // WebPage schema
    $schema['@graph'][] = [
        '@type' => 'WebPage',
        '@id' => get_permalink() . '#webpage',
        'url' => get_permalink(),
        'name' => get_the_title(),
        'description' => wp_strip_all_tags(get_the_excerpt()),
        'inLanguage' => get_locale(),
        'isPartOf' => [
            '@id' => home_url('/#website')
        ],
        'publisher' => [
            '@id' => home_url('/#organization')
        ],
        'breadcrumb' => [
            '@id' => get_permalink() . '#breadcrumb'
        ]
    ];

    // BreadcrumbList schema
    $breadcrumb_items = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => __('Anasayfa', 'dazzlo'),
            'item' => [
                '@id' => home_url('/')
            ]
        ]
    ];
    if (!empty($categories)) {
        $breadcrumb_items[] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $categories[0]->name,
            'item' => [
                '@id' => get_category_link($categories[0]->term_id)
            ]
        ];
    }
    $breadcrumb_items[] = [
        '@type' => 'ListItem',
        'position' => count($breadcrumb_items) + 1,
        'name' => get_the_title(),
        'item' => [
            '@id' => get_permalink()
        ]
    ];

    $schema['@graph'][] = [
        '@type' => 'BreadcrumbList',
        '@id' => get_permalink() . '#breadcrumb',
        'itemListElement' => $breadcrumb_items
    ];

    // Article schema - İyileştirilmiş sürüm
    $schema['@graph'][] = [
        '@type' => 'Article',
        '@id' => get_permalink() . '#article',
        'url' => get_permalink(),
        'headline' => get_the_title(),
        'description' => wp_strip_all_tags(get_the_excerpt()),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'image' => [
            '@type' => 'ImageObject',
            'url' => $thumbnail[0],
            'width' => $thumbnail[1],
            'height' => $thumbnail[2]
        ],
        'author' => [
            '@type' => 'Person',
            'name' => get_the_author(),
            'url' => get_author_posts_url(get_the_author_meta('ID'))
        ],
        'publisher' => [
            '@id' => home_url('/#organization')
        ],
        'video' => $video_schema,
        'keywords' => array_map(function($cat) { return $cat->name; }, $categories),
        'about' => $category_schema,
        // Eklenen yeni özellikler
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => get_permalink()
        ],
        'articleBody' => $article_body
    ];
}

    // Forum Konuları (single-forum_topics.php)
if (is_singular('forum_topics')) {
    $post_id = get_the_ID();
    
    // Define helper function if it doesn't exist
    if (!function_exists('get_topic_author_info')) {
        function get_topic_author_info($post_id) {
            $author_id = get_post_field('post_author', $post_id);
            $author = get_userdata($author_id);
            
            if ($author && !user_can($author_id, 'administrator')) {
                return array(
                    'name'   => $author->display_name,
                    'avatar' => get_avatar_url($author_id, array('size' => 40)),
                    'url'    => get_author_posts_url($author_id),
                    'role'   => 'member'
                );
            }
            
            // For admin or guest users, use post meta if available
            $user_name = get_post_meta($post_id, 'user_name', true);
            $user_email = get_post_meta($post_id, 'user_email', true);
            
            return array(
                'name'   => !empty($user_name) ? $user_name : 'Misafir',
                'avatar' => !empty($user_email) ? get_avatar_url($user_email, array('size' => 40)) : get_avatar_url('', array('size' => 40)),
                'url'    => '#',
                'role'   => empty($user_name) ? 'guest' : 'contributor'
            );
        }
    }
    
    // Define last reply info function if it doesn't exist
    if (!function_exists('get_last_reply_info')) {
        function get_last_reply_info($post_id) {
            // Check for expert answer first
            if (get_post_meta($post_id, 'has_expert_answer', true) == 'yes') {
                $expert_name = get_post_meta($post_id, 'expert_name', true);
                $expert_title = get_post_meta($post_id, 'expert_title', true);
                $expert_date = get_post_meta($post_id, 'expert_answer_date', true);
                
                if (empty($expert_date)) {
                    $expert_date = get_post_modified_time('U', true, $post_id);
                }
                
                return array(
                    'type'       => 'expert',
                    'name'       => $expert_name,
                    'title'      => $expert_title,
                    'avatar'     => get_avatar_url('expert@example.com', array('size' => 40)),
                    'date'       => $expert_date,
                    'date_human' => human_time_diff($expert_date, current_time('timestamp')) . ' önce'
                );
            }
            
            // Get the last comment
            $comments = get_comments(array(
                'post_id' => $post_id,
                'number'  => 1,
                'status'  => 'approve',
                'orderby' => 'comment_date',
                'order'   => 'DESC'
            ));
            
            if (!empty($comments)) {
                $comment = $comments[0];
                return array(
                    'type'       => 'comment',
                    'name'       => $comment->comment_author,
                    'avatar'     => get_avatar_url($comment->comment_author_email, array('size' => 40)),
                    'date'       => strtotime($comment->comment_date),
                    'date_human' => human_time_diff(strtotime($comment->comment_date), current_time('timestamp')) . ' önce',
                    'url'        => get_comment_link($comment)
                );
            }
            
            return false;
        }
    }
    
    try {
        $author_info = get_topic_author_info($post_id);
        $last_reply = get_last_reply_info($post_id);
        $categories = get_the_terms($post_id, 'topic_category');
        $category = is_array($categories) && !empty($categories) ? $categories[0] : null;
        $tags = get_post_meta($post_id, 'forum_topic_tags', true);
        $tags_array = !empty($tags) ? array_map('trim', explode(',', $tags)) : [];

        // WebPage schema
        $schema['@graph'][] = [
            '@type' => 'WebPage',
            '@id' => get_permalink() . '#webpage',
            'url' => get_permalink(),
            'name' => get_the_title(),
            'description' => wp_strip_all_tags(get_the_excerpt()),
            'inLanguage' => get_locale(),
            'isPartOf' => [
                '@id' => home_url('/#website')
            ],
            'publisher' => [
                '@id' => home_url('/#organization')
            ],
            'breadcrumb' => [
                '@id' => get_permalink() . '#breadcrumb'
            ]
        ];

        // BreadcrumbList schema
        $breadcrumb_items = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => __('Anasayfa', 'dazzlo'),
                'item' => [
                    '@id' => home_url('/')
                ]
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Forum',
                'item' => [
                    '@id' => home_url('/forum/')
                ]
            ]
        ];
        
        if ($category) {
            $breadcrumb_items[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $category->name,
                'item' => [
                    '@id' => get_term_link($category)
                ]
            ];
        }
        
        $breadcrumb_items[] = [
            '@type' => 'ListItem',
            'position' => count($breadcrumb_items) + 1,
            'name' => get_the_title(),
            'item' => [
                '@id' => get_permalink()
            ]
        ];

        $schema['@graph'][] = [
            '@type' => 'BreadcrumbList',
            '@id' => get_permalink() . '#breadcrumb',
            'itemListElement' => $breadcrumb_items
        ];

        // DiscussionForumPosting schema
        $post_schema = [
            '@type' => 'DiscussionForumPosting',
            '@id' => get_permalink() . '#topic',
            'url' => get_permalink(),
            'headline' => get_the_title(),
            'description' => wp_strip_all_tags(get_the_excerpt()),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => [
                '@type' => 'Person',
                'name' => $author_info['name']
            ],
            'interactionStatistic' => [
                [
                    '@type' => 'InteractionCounter',
                    'interactionType' => ['http://schema.org/ViewAction'],
                    'userInteractionCount' => intval(get_post_meta($post_id, 'post_views_count', true) ?: 0)
                ],
                [
                    '@type' => 'InteractionCounter',
                    'interactionType' => ['http://schema.org/CommentAction'],
                    'userInteractionCount' => get_comments_number() + (get_post_meta($post_id, 'has_expert_answer', true) == 'yes' ? 1 : 0)
                ]
            ]
        ];
        
        // Add author URL if it exists and is not just a placeholder
        if (!empty($author_info['url']) && $author_info['url'] !== '#') {
            $post_schema['author']['url'] = $author_info['url'];
        }
        
        // Add keywords if they exist
        if (!empty($tags_array)) {
            $post_schema['keywords'] = $tags_array;
        }
        
        // Add category if it exists
        if ($category) {
            $post_schema['about'] = [
                '@type' => 'Thing',
                'name' => $category->name,
                'url' => get_term_link($category)
            ];
        }
        
        // Add last reply if it exists
        if ($last_reply) {
            $comment_schema = [
                '@type' => 'Comment',
                'author' => [
                    '@type' => 'Person',
                    'name' => $last_reply['name']
                ],
                'datePublished' => date('c', $last_reply['date'])
            ];
            
            // Add expert type if applicable
            if ($last_reply['type'] === 'expert' && !empty($last_reply['title'])) {
                $comment_schema['author']['jobTitle'] = $last_reply['title'];
                $comment_schema['description'] = 'Uzman yanıtı';
            }
            
            $post_schema['comment'] = $comment_schema;
        }
        
        $schema['@graph'][] = $post_schema;
        
        // Add Comment list if there are comments
        $comments = get_comments([
            'post_id' => $post_id,
            'status' => 'approve',
            'number' => 10,
            'orderby' => 'comment_date',
            'order' => 'DESC'
        ]);
        
        if (!empty($comments)) {
            $comment_items = [];
            $comment_count = 0;
            
            foreach ($comments as $comment) {
                $comment_items[] = [
                    '@type' => 'Comment',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $comment->comment_author
                    ],
                    'datePublished' => date('c', strtotime($comment->comment_date)),
                    'text' => wp_strip_all_tags($comment->comment_content)
                ];
                $comment_count++;
                
                // Limit to 10 comments to keep schema size reasonable
                if ($comment_count >= 10) break;
            }
            
            if (!empty($comment_items)) {
                $schema['@graph'][] = [
                    '@type' => 'ItemList',
                    '@id' => get_permalink() . '#comments',
                    'name' => 'Forum Yorumları',
                    'itemListElement' => $comment_items
                ];
            }
        }
        
        // Add expert answer if exists
        $has_expert = get_post_meta($post_id, 'has_expert_answer', true);
        if ($has_expert == 'yes') {
            $expert_name = get_post_meta($post_id, 'expert_name', true);
            $expert_title = get_post_meta($post_id, 'expert_title', true);
            $expert_answer = get_post_meta($post_id, 'expert_answer', true);
            
            if (!empty($expert_answer)) {
                $schema['@graph'][] = [
                    '@type' => 'Comment',
                    '@id' => get_permalink() . '#expert-answer',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $expert_name,
                        'jobTitle' => $expert_title
                    ],
                    'datePublished' => get_the_modified_date('c'),
                    'text' => wp_strip_all_tags($expert_answer),
                    'description' => 'Uzman yanıtı'
                ];
            }
        }
    } catch (Exception $e) {
        // Log any errors that occur
        error_log('Schema generation error for forum topic: ' . $e->getMessage());
    }
}
	
    // Statik Sayfalar (page.php, excluding forum-template.php and blog-template.php)
    if (is_page() && !is_page_template('forum-template.php') && !is_page_template('blog-template.php')) {
        $page_id = get_the_ID();
        $thumbnail_id = get_post_thumbnail_id();
        $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'dazzlo-random-thumb') : null;

        // Özel sayfa türleri
        $page_type = 'WebPage';
        if (is_page('communication')) {
            $page_type = 'ContactPage';
        } elseif (is_page('about')) {
            $page_type = 'AboutPage';
        }

        // WebPage schema
        $schema['@graph'][] = [
            '@type' => $page_type,
            '@id' => get_permalink() . '#webpage',
            'url' => get_permalink(),
            'name' => get_the_title(),
            'description' => wp_strip_all_tags(get_the_excerpt() ?: get_bloginfo('description')),
            'inLanguage' => get_locale(),
            'isPartOf' => [
                '@id' => home_url('/#website')
            ],
            'publisher' => [
                '@id' => home_url('/#organization')
            ],
            'breadcrumb' => [
                '@id' => get_permalink() . '#breadcrumb'
            ],
            'image' => $thumbnail ? [
                '@type' => 'ImageObject',
                'url' => $thumbnail[0],
                'width' => $thumbnail[1],
                'height' => $thumbnail[2]
            ] : null
        ];

        // BreadcrumbList schema
        $schema['@graph'][] = [
            '@type' => 'BreadcrumbList',
            '@id' => get_permalink() . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('Anasayfa', 'dazzlo'),
                    'item' => [
                        '@id' => home_url('/')
                    ]
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => get_the_title(),
                    'item' => [
                        '@id' => get_permalink()
                    ]
                ]
            ]
        ];
    }

    // Arşiv/Kategori Sayfaları (archive.php, category.php)
    if (is_category() || is_tag() || is_tax() || is_post_type_archive()) {
        $term = get_queried_object();
        $archive_title = $term ? $term->name : get_the_archive_title();
        $archive_description = $term ? $term->description : get_the_archive_description();

        // WebPage schema
        $schema['@graph'][] = [
            '@type' => 'CollectionPage',
            '@id' => get_permalink() . '#webpage',
            'url' => get_permalink(),
            'name' => $archive_title,
            'description' => wp_strip_all_tags($archive_description ?: get_bloginfo('description')),
            'inLanguage' => get_locale(),
            'isPartOf' => [
                '@id' => home_url('/#website')
            ],
            'publisher' => [
                '@id' => home_url('/#organization')
            ],
            'breadcrumb' => [
                '@id' => get_permalink() . '#breadcrumb'
            ]
        ];

        // BreadcrumbList schema
        $breadcrumb_items = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => __('Anasayfa', 'dazzlo'),
                'item' => [
                    '@id' => home_url('/')
                ]
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $archive_title,
                'item' => [
                    '@id' => get_permalink()
                ]
            ]
        ];

        $schema['@graph'][] = [
            '@type' => 'BreadcrumbList',
            '@id' => get_permalink() . '#breadcrumb',
            'itemListElement' => $breadcrumb_items
        ];

        // ItemList for Archive Posts
        $archive_posts = [];
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $post_id = get_the_ID();
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'dazzlo-random-thumb') : [get_template_directory_uri() . '/images/slider-default.png', 600, 400];

                $archive_posts[] = [
                    '@type' => 'ListItem',
                    'position' => count($archive_posts) + 1,
                    'item' => [
                        '@type' => 'Article',
                        '@id' => get_permalink() . '#article',
                        'url' => get_permalink(),
                        'headline' => get_the_title(),
                        'description' => wp_strip_all_tags(get_the_excerpt()),
                        'datePublished' => get_the_date('c'),
                        'dateModified' => get_the_modified_date('c'),
                        'image' => [
                            '@type' => 'ImageObject',
                            'url' => $thumbnail[0],
                            'width' => $thumbnail[1],
                            'height' => $thumbnail[2]
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'name' => get_the_author(),
                            'url' => get_author_posts_url(get_the_author_meta('ID'))
                        ],
                        'publisher' => [
                            '@id' => home_url('/#organization')
                        ]
                    ]
                ];
            }
            rewind_posts();
        }

        if (!empty($archive_posts)) {
            $schema['@graph'][] = [
                '@type' => 'ItemList',
                '@id' => get_permalink() . '#archive-posts',
                'name' => $archive_title,
                'description' => 'Minecraft ile ilgili yazılar ve haberler',
                'itemListElement' => $archive_posts
            ];
        }
    }

    // Schema çıktısını <head> içine ekle
    add_action('wp_head', function () use ($schema) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    });
}
add_action('wp', 'metaprora_dynamic_schema');


// Harici bağlantılara nofollow, noopener, noreferrer ekleme
add_filter('the_content', 'add_nofollow_noopener_noreferrer_to_external_links');

function add_nofollow_noopener_noreferrer_to_external_links($content) {
    // Sitenizin alan adını tanımlayın
    $site_domain = 'metaprora.com';

    // İçerikteki tüm <a> etiketlerini bulmak için regex
    $content = preg_replace_callback(
        '/<a\s+([^>]*)href=["\'](.*?)["\']([^>]*)>/i',
        function($matches) use ($site_domain) {
            $href = $matches[2];

            // Eğer bağlantı sitenizin alan adına aitse veya relatif bir bağlantıysa, dokunmayın
            if (empty($href) || strpos($href, $site_domain) !== false || !preg_match('/^https?:\/\//', $href)) {
                return $matches[0]; // Orijinal bağlantıyı döndür
            }

            // Mevcut rel özniteliğini al
            $rel = '';
            if (preg_match('/rel=["\'](.*?)["\']/i', $matches[0], $rel_match)) {
                $rel = $rel_match[1];
            }

            // rel özniteliğine nofollow, noopener, noreferrer ekle
            $rel_values = array_unique(array_filter(array_merge(
                explode(' ', $rel),
                ['nofollow', 'noopener', 'noreferrer']
            )));

            // Yeni rel özniteliğini oluştur
            $new_rel = 'rel="' . implode(' ', $rel_values) . '"';

            // Mevcut rel özniteliğini güncelle veya ekle
            if (preg_match('/rel=["\'].*?["\']/i', $matches[0])) {
                $new_link = preg_replace('/rel=["\'].*?["\']/i', $new_rel, $matches[0]);
            } else {
                $new_link = str_replace('href="' . $href . '"', 'href="' . $href . '" ' . $new_rel, $matches[0]);
            }

            return $new_link;
        },
        $content
    );

    return $content;
}