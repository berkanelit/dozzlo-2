<?php
/**
 * Theme options via the Customizer.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */

// ------------- Theme Customizer  ------------- //

add_action('customize_register', 'dazzlo_customizer_register');

function dazzlo_customizer_register($wp_customize) {

    // Tema Hakkında
    $wp_customize->add_section('dazzlo_about', array(
        'title'     => esc_html__('Tema Hakkında', 'dazzlo'),
        'priority'  => 1,
        'capability' => 'edit_theme_options'
    ));

    // Light and Dark Logo
    $wp_customize->remove_control('custom_logo');
    
    $wp_customize->add_setting('dazzlo_light_logo', array(
        'sanitize_callback' => 'dazzlo_sanitize_image'
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'dazzlo_light_logo1', array(
            'label'    => esc_html__('Açık Renkli Logo', 'dazzlo'),
            'section'  => 'title_tagline',
            'settings' => 'dazzlo_light_logo'
        )
    ));

    $wp_customize->add_setting('dazzlo_dark_logo', array(
        'sanitize_callback' => 'dazzlo_sanitize_image'
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'dazzlo_dark_logo1', array(
            'label'    => esc_html__('Koyu Renkli Logo', 'dazzlo'),
            'section'  => 'title_tagline',
            'settings' => 'dazzlo_dark_logo'
        )
    ));

    //Top Information Bar
    $wp_customize->add_section('dazzlo_information_bar', array(
        'title'       => esc_html__('Üst Bilgi Çubuğu', 'dazzlo'),
        'description' => esc_html__('Üst bilgi çubuğu ayarlarını yapılandırın.', 'dazzlo'),
        'priority'    => 2
    ));

    $wp_customize->add_setting('dazzlo_information_bar_disable', array(
        'default'    => 'disable',
        'section'  => 'dazzlo_information_bar',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));

    $wp_customize->add_control('dazzlo_information_bar_select_box', array(
        'settings' => 'dazzlo_information_bar_disable',
        'label'    => esc_html__('Üst Bilgi Çubuğu', 'dazzlo'),
        'section'  => 'dazzlo_information_bar',
        'type'     => 'select',
        'choices'  => array(
            'enable'  => esc_html__('Etkinleştir', 'dazzlo'),
            'disable' => esc_html__('Devre Dışı Bırak', 'dazzlo'),
        ),
        'priority' => 1
    ));

    $wp_customize->add_setting(
        'dazzlo_information_text',
        array(
            'default'     => '',
            'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('dazzlo_information_text', array(
            'label'      => esc_html__('Bilgi Çubuğu Metni','dazzlo'),
            'section'    => 'dazzlo_information_bar',
            'settings'   => 'dazzlo_information_text',
            'type'       => 'text',
            'priority'   => 2
        )
    );

    $wp_customize->add_setting(
        'dazzlo_information_link',
        array(
            'sanitize_callback' => 'esc_url_raw'
        )
    );

    $wp_customize->add_control('dazzlo_information_link', array(
            'label'      => esc_html__('Bilgi Çubuğu Bağlantı URL','dazzlo'),
            'section'    => 'dazzlo_information_bar',
            'settings'   => 'dazzlo_information_link',
            'type'       => 'url',
            'priority'   => 3
        )
    );

    // Header Design
    $wp_customize->add_section('dazzlo_header_designs', array(
        'title'       => esc_html__('Başlık Tasarımları', 'dazzlo'),
        'description' => esc_html__('Başlık tasarımını buradan seçin.', 'dazzlo'),
        'priority'    => 3
    ));

    $wp_customize->add_setting('dazzlo_header_design_layout', array(
        'default' => 'header1',
        'section'  => 'dazzlo_header_designs',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));
    
    $wp_customize->add_control('dazzlo_header_design_layout', array(
        'type' => 'radio',
        'label'    => esc_html__('Başlık Tasarım Düzeni', 'dazzlo'),
        'section'  => 'dazzlo_header_designs',
        'choices'  => array(
            'header1'  => esc_html__('Başlık 1', 'dazzlo'),
            'header2' => esc_html__('Başlık 2', 'dazzlo'),
        ),
        'priority' => 10
    ));

    //Slick Slider
    $wp_customize->add_section('dazzlo_customizer_mainslider', array(
        'title'       => esc_html__('Ana Slider Seçenekleri', 'dazzlo'),
        'description' => esc_html__('Ana sliderinizi buradan yapılandırın.', 'dazzlo'),
        'priority'    => 4
    ));
    
    $wp_customize->add_setting('dazzlo_customizer_mainslider_disable', array(
        'default'    => 'enable',
        'section'  => 'dazzlo_customizer_mainslider',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));

    $wp_customize->add_control('dazzlo_mainslider_select_box', array(
        'settings' => 'dazzlo_customizer_mainslider_disable',
        'label'    => esc_html__('Ana Sayfa Ana Slider', 'dazzlo'),
        'section'  => 'dazzlo_customizer_mainslider',
        'type'     => 'select',
        'choices'  => array(
            'enable'  => esc_html__('Etkinleştir', 'dazzlo'),
            'disable' => esc_html__('Devre Dışı Bırak', 'dazzlo'),
        ),
        'priority' => 5
    ));
    
    $wp_customize->add_setting('dazzlo_mainslider_category', array(
        'default' => '0',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_mainslider',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_mainslider_category', array(
            'label'    => esc_html__('Ana Slider için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_mainslider',
            'settings' => 'dazzlo_mainslider_category',
            'priority' => 6
        )
    ));

    $wp_customize->add_setting('dazzlo_mainslider_slides', array(
        'default' => '3',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_mainslider',
    ));

    $wp_customize->add_control('dazzlo_mainslider_slides', array(
            'label'      => esc_html__('Ana Slider için Yazı Sayısı','dazzlo'),
            'section'    => 'dazzlo_customizer_mainslider',
            'settings'   => 'dazzlo_mainslider_slides',
            'type'       => 'number',
            'priority'   => 8
        )
    );

    $wp_customize->add_setting('dazzlo_beforeslider_category', array(
        'default' => '0',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_mainslider',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_beforeslider_category', array(
            'label'    => esc_html__('Slider Öncesi Yazılar için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_mainslider',
            'settings' => 'dazzlo_beforeslider_category',
            'priority' => 9
        )
    ));

    $wp_customize->add_setting('dazzlo_afterslider_category', array(
        'default' => '0',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_mainslider',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_afterslider_category', array(
            'label'    => esc_html__('Slider Sonrası Yazılar için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_mainslider',
            'settings' => 'dazzlo_afterslider_category',
            'priority' => 10
        )
    ));

    //Small Slider
    $wp_customize->add_section('dazzlo_customizer_slider', array(
        'title'       => esc_html__('Küçük Slider Seçenekleri', 'dazzlo'),
        'description' => esc_html__('Küçük sliderinizi buradan yapılandırın.', 'dazzlo'),
        'priority'    => 5
    ));
    
    $wp_customize->add_setting('dazzlo_customizer_slider_disable', array(
        'default'    => 'enable',
        'section'  => 'dazzlo_customizer_slider',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));

    $wp_customize->add_control('dazzlo_slider_select_box', array(
        'settings' => 'dazzlo_customizer_slider_disable',
        'label'    => esc_html__('Ana Sayfa Küçük Slider', 'dazzlo'),
        'section'  => 'dazzlo_customizer_slider',
        'type'     => 'select',
        'choices'  => array(
            'enable'  => esc_html__('Etkinleştir', 'dazzlo'),
            'disable' => esc_html__('Devre Dışı Bırak', 'dazzlo'),
        ),
        'priority' => 5
    ));
    
    $wp_customize->add_setting('dazzlo_slider_category', array(
        'default' => '0',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_slider',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_slider_category', array(
            'label'    => esc_html__('Küçük Slider için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_slider',
            'settings' => 'dazzlo_slider_category',
            'priority' => 7
        )
    ));

    $wp_customize->add_setting('dazzlo_slider_slides', array(
        'default' => '4',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_slider',
    ));

    $wp_customize->add_control('dazzlo_slider_slides', array(
            'label'      => esc_html__('Küçük Slider için Yazı Sayısı','dazzlo'),
            'section'    => 'dazzlo_customizer_slider',
            'settings'   => 'dazzlo_slider_slides',
            'type'       => 'number',
            'priority'   => 8
        )
    );

    // Post Layout Boxes - Layoutbox1
    $wp_customize->add_section('dazzlo_customizer_layoutbox1', array(
        'title'       => esc_html__('Düzen Kutusu 1 Seçenekleri', 'dazzlo'),
        'description' => esc_html__('Düzen Kutusu 1\'i buradan yapılandırın.', 'dazzlo'),
        'priority'    => 6
    ));
    
    $wp_customize->add_setting('dazzlo_customizer_layoutbox1_disable', array(
        'default'    => 'enable',
        'section'  => 'dazzlo_customizer_layoutbox1',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));
    
    $wp_customize->add_control('dazzlo_layoutbox1_select_box', array(
        'settings' => 'dazzlo_customizer_layoutbox1_disable',
        'label'    => esc_html__('Ana Sayfa Düzen Kutusu 1', 'dazzlo'),
        'section'  => 'dazzlo_customizer_layoutbox1',
        'type'     => 'select',
        'choices'  => array(
            'enable'  => esc_html__('Etkinleştir', 'dazzlo'),
            'disable' => esc_html__('Devre Dışı Bırak', 'dazzlo'),
        ),
        'priority' => 6
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox1_title', array(
        'default'     => 'Düzen Kutusu 1',
        'section'  => 'dazzlo_customizer_layoutbox1',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'layoutbox1_title', array(
            'label'      => esc_html__('Düzen Kutusu 1 Başlığı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox1',
            'settings'   => 'dazzlo_layoutbox1_title',
            'type'       => 'text',
            'priority'   => 7
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox1_category', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox1',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_layoutbox1_category', array(
            'label'    => esc_html__('Düzen Kutusu 1 için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_layoutbox1',
            'settings' => 'dazzlo_layoutbox1_category',
            'priority' => 8
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox1_no', array(
        'default' => '5',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox1',
    ));

    $wp_customize->add_control('dazzlo_layoutbox1_no', array(
            'label'      => esc_html__('Düzen Kutusu 1 için Yazı Sayısı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox1',
            'settings'   => 'dazzlo_layoutbox1_no',
            'type'       => 'number',
            'priority'   => 9
        )
    );

    // Post Layout Boxes - Layoutbox2
    $wp_customize->add_section('dazzlo_customizer_layoutbox2', array(
        'title'       => esc_html__('Düzen Kutusu 2 Seçenekleri', 'dazzlo'),
        'description' => esc_html__('Düzen Kutusu 2\'yi buradan yapılandırın.', 'dazzlo'),
        'priority'    => 7
    ));
    
    $wp_customize->add_setting('dazzlo_customizer_layoutbox2_disable', array(
        'default'    => 'enable',
        'section'  => 'dazzlo_customizer_layoutbox2',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));
    
    $wp_customize->add_control('dazzlo_layoutbox2_select_box', array(
        'settings' => 'dazzlo_customizer_layoutbox2_disable',
        'label'    => esc_html__('Ana Sayfa Düzen Kutusu 2', 'dazzlo'),
        'section'  => 'dazzlo_customizer_layoutbox2',
        'type'     => 'select',
        'choices'  => array(
            'enable'  => esc_html__('Etkinleştir', 'dazzlo'),
            'disable' => esc_html__('Devre Dışı Bırak', 'dazzlo'),
        ),
        'priority' => 6
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox2_title', array(
        'default'     => 'Düzen Kutusu 2',
        'section'  => 'dazzlo_customizer_layoutbox2',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'layoutbox2_title', array(
            'label'      => esc_html__('Düzen Kutusu 2 Başlığı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox2',
            'settings'   => 'dazzlo_layoutbox2_title',
            'type'       => 'text',
            'priority'   => 7
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox2_category', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox2',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_layoutbox2_category', array(
            'label'    => esc_html__('Düzen Kutusu 2 için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_layoutbox2',
            'settings' => 'dazzlo_layoutbox2_category',
            'priority' => 8
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox2_no', array(
        'default' => '3',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox2',
    ));

    $wp_customize->add_control('dazzlo_layoutbox2_no', array(
            'label'      => esc_html__('Düzen Kutusu 2 için Yazı Sayısı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox2',
            'settings'   => 'dazzlo_layoutbox2_no',
            'type'       => 'number',
            'priority'   => 9
        )
    );

    // Post Layout Boxes - Layoutbox3
    $wp_customize->add_section('dazzlo_customizer_layoutbox3', array(
        'title'       => esc_html__('Düzen Kutusu 3 Seçenekleri', 'dazzlo'),
        'description' => esc_html__('Düzen Kutusu 3\'ü buradan yapılandırın.', 'dazzlo'),
        'priority'    => 8
    ));
    
    $wp_customize->add_setting('dazzlo_customizer_layoutbox3_disable', array(
        'default'    => 'enable',
        'section'  => 'dazzlo_customizer_layoutbox3',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));
    
    $wp_customize->add_control('dazzlo_layoutbox3_select_box', array(
        'settings' => 'dazzlo_customizer_layoutbox3_disable',
        'label'    => esc_html__('Ana Sayfa Düzen Kutusu 3', 'dazzlo'),
        'section'  => 'dazzlo_customizer_layoutbox3',
        'type'     => 'select',
        'choices'  => array(
            'enable'  => esc_html__('Etkinleştir', 'dazzlo'),
            'disable' => esc_html__('Devre Dışı Bırak', 'dazzlo'),
        ),
        'priority' => 6
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox3_title', array(
        'default'     => 'Düzen Kutusu 3',
        'section'  => 'dazzlo_customizer_layoutbox3',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'layoutbox3_title', array(
            'label'      => esc_html__('Düzen Kutusu 3 Başlığı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox3',
            'settings'   => 'dazzlo_layoutbox3_title',
            'type'       => 'text',
            'priority'   => 7
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox3_category', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox3',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_layoutbox3_category', array(
            'label'    => esc_html__('Düzen Kutusu 3 için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_layoutbox3',
            'settings' => 'dazzlo_layoutbox3_category',
            'priority' => 8
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox3_no', array(
        'default' => '5',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox3',
    ));

    $wp_customize->add_control('dazzlo_layoutbox3_no', array(
            'label'      => esc_html__('Düzen Kutusu 3 için Yazı Sayısı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox3',
            'settings'   => 'dazzlo_layoutbox3_no',
            'type'       => 'number',
            'priority'   => 9
        )
    );

    // Post Layout Boxes - Layoutbox4
    $wp_customize->add_section('dazzlo_customizer_layoutbox4', array(
        'title'       => esc_html__('Düzen Kutusu 4 Seçenekleri', 'dazzlo'),
        'description' => esc_html__('Düzen Kutusu 4\'ü buradan yapılandırın.', 'dazzlo'),
        'priority'    => 9
    ));
    
    $wp_customize->add_setting('dazzlo_customizer_layoutbox4_disable', array(
        'default'    => 'enable',
        'section'  => 'dazzlo_customizer_layoutbox4',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_radio',
    ));
    
    $wp_customize->add_control('dazzlo_layoutbox4_select_box', array(
        'settings' => 'dazzlo_customizer_layoutbox4_disable',
        'label'    => esc_html__('Ana Sayfa Düzen Kutusu 4', 'dazzlo'),
        'section'  => 'dazzlo_customizer_layoutbox4',
        'type'     => 'select',
        'choices'  => array(
            'enable'  => esc_html__('Etkinleştir', 'dazzlo'),
            'disable' => esc_html__('Devre Dışı Bırak', 'dazzlo'),
        ),
        'priority' => 6
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox4_title', array(
        'default'     => 'Düzen Kutusu 4',
        'section'  => 'dazzlo_customizer_layoutbox4',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'layoutbox4_title', array(
            'label'      => esc_html__('Düzen Kutusu 4 Başlığı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox4',
            'settings'   => 'dazzlo_layoutbox4_title',
            'type'       => 'text',
            'priority'   => 7
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox4_category', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox4',
    ));
    
    $wp_customize->add_control(new dazzlo_Customize_Category_Control($wp_customize, 'dazzlo_layoutbox4_category', array(
            'label'    => esc_html__('Düzen Kutusu 4 için Kategori', 'dazzlo'),
            'section'  => 'dazzlo_customizer_layoutbox4',
            'settings' => 'dazzlo_layoutbox4_category',
            'priority' => 8
        )
    ));
    
    $wp_customize->add_setting('dazzlo_layoutbox4_no', array(
        'default' => '6',
        'sanitize_callback' => 'absint',
        'section'  => 'dazzlo_customizer_layoutbox4',
    ));

    $wp_customize->add_control('dazzlo_layoutbox4_no', array(
            'label'      => esc_html__('Düzen Kutusu 4 için Yazı Sayısı','dazzlo'),
            'section'    => 'dazzlo_customizer_layoutbox4',
            'settings'   => 'dazzlo_layoutbox4_no',
            'type'       => 'number',
            'priority'   => 9
        )
    );

    // General Options
    $wp_customize->add_section('dazzlo_general_options', array(
        'title'       => esc_html__('Genel Seçenekler', 'dazzlo'),
        'description' => esc_html__('Genel tema ayarlarınızı buradan yapılandırın.', 'dazzlo'),
        'priority'    => 11
    ));

    $wp_customize->add_setting('dazzlo_latest_posts', array(
        'default'     => 'Son Yazılar',
        'section'  => 'dazzlo_general_options',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'latestposts_title', array(
            'label'      => esc_html__('Son Yazılar Başlığı','dazzlo'),
            'section'    => 'dazzlo_general_options',
            'settings'   => 'dazzlo_latest_posts',
            'type'       => 'text',
            'priority'   => 5
        )
    ));

    $wp_customize->add_setting('dazzlo_general_search_icon', array(
        'section'  => 'dazzlo_general_options',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_checkbox',
    ));

    $wp_customize->add_control('dazzlo_general_search_icon', array(
        'settings' => 'dazzlo_general_search_icon',
        'label'    => esc_html__('Üst Arama Simgesini Gizle', 'dazzlo'),
        'section'  => 'dazzlo_general_options',
        'type'     => 'checkbox',
        'priority' => 6
    ));
    
    $wp_customize->add_setting('dazzlo_general_responsive', array(
        'section'  => 'dazzlo_general_options',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_checkbox',
    ));

    $wp_customize->add_control('dazzlo_general_responsive', array(
        'settings' => 'dazzlo_general_responsive',
        'label'    => esc_html__('Duyarlı Tasarımı Devre Dışı Bırak', 'dazzlo'),
        'section'  => 'dazzlo_general_options',
        'type'     => 'checkbox',
        'priority' => 7
    ));
    
    $wp_customize->add_setting('dazzlo_general_sidebar_home', array(
        'section'  => 'dazzlo_general_options',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_checkbox',
    ));

    $wp_customize->add_control('dazzlo_general_sidebar_home', array(
        'settings' => 'dazzlo_general_sidebar_home',
        'label'    => esc_html__('Ana Sayfa ve Arşiv Sayfalarında Kenar Çubuğunu Devre Dışı Bırak', 'dazzlo'),
        'section'  => 'dazzlo_general_options',
        'type'     => 'checkbox',
        'priority' => 8
    ));
    
    $wp_customize->add_setting('dazzlo_general_sidebar_post', array(
        'section'  => 'dazzlo_general_options',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_checkbox',
    ));

    $wp_customize->add_control('dazzlo_general_sidebar_post', array(
        'settings' => 'dazzlo_general_sidebar_post',
        'label'    => esc_html__('Yazılarda Kenar Çubuğunu Devre Dışı Bırak', 'dazzlo'),
        'section'  => 'dazzlo_general_options',
        'type'     => 'checkbox',
        'priority' => 9
    ));
    
    $wp_customize->add_setting('dazzlo_general_sidebar_page', array(
        'section'  => 'dazzlo_general_options',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_checkbox',
    ));

    $wp_customize->add_control('dazzlo_general_sidebar_page', array(
        'settings' => 'dazzlo_general_sidebar_page',
        'label'    => esc_html__('Sayfalarda Kenar Çubuğunu Devre Dışı Bırak', 'dazzlo'),
        'section'  => 'dazzlo_general_options',
        'type'     => 'checkbox',
        'priority' => 10
    ));

    $wp_customize->add_setting('dazzlo_general_author_post', array(
        'section'  => 'dazzlo_general_options',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'dazzlo_sanitize_checkbox',
    ));

    $wp_customize->add_control('dazzlo_general_author_post', array(
        'settings' => 'dazzlo_general_author_post',
        'label'    => esc_html__('Yazılarda Yazar Kutusunu Devre Dışı Bırak', 'dazzlo'),
        'section'  => 'dazzlo_general_options',
        'type'     => 'checkbox',
        'priority' => 11
    ));

    // Footer Settings
    $wp_customize->add_section('dazzlo_footer_settings', array(
        'title'       => esc_html__('Alt Bilgi Ayarları', 'dazzlo'),
        'description' => esc_html__('Alt bilgi ayarlarınızı buradan yapılandırın.', 'dazzlo'),
        'priority'    => 12
    ));

    $wp_customize->add_setting(
        'footer_copyright',
        array(
            'default'     => 'Telif Hakkı ' . date('Y') . '.',
            'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control('footer_copyright', array(
            'label'      => esc_html__('Alt Bilgi Telif Hakkı Metni','dazzlo'),
            'section'    => 'dazzlo_footer_settings',
            'settings'   => 'footer_copyright',
            'type'       => 'text',
            'priority'   => 1
        )
    );
}


// Ana renk ayarlarını varsayılan özelleştirici paneline ekleme
add_action('customize_register','dazzlo_customizer_options');

function dazzlo_customizer_options($wp_customize) {
    $wp_customize->add_setting(
        'dazzlo_main_color', //ID vermek
        array(
            'default' => '#3d55ef', // Varsayılan değer
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'      => 'refresh'
        )
    );
    
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'dazzlo_custom_main_color', //ID vermek
            array(
                'label'      => esc_html__('Ana Renk', 'dazzlo'), //Özelleştiricideki etiket
                'section'    => 'colors', //Hangi bölümde görüneceği
                'settings'   => 'dazzlo_main_color' //Hangi ayarı uyguladığı
            )
        )
    );
}


/**
 * Resim doğrulama fonksiyonu
 */
function dazzlo_sanitize_image($file, $setting) {
    $mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'gif'          => 'image/gif',
        'png'          => 'image/png',
        'bmp'          => 'image/bmp',
        'tif|tiff'     => 'image/tiff',
        'ico'          => 'image/x-icon'
    );

    // Dosya türünü dosya adından kontrol et
    $file_ext = wp_check_filetype($file, $mimes);

    // Eğer dosya geçerli bir mime türüne sahipse onu, değilse varsayılanı döndür
    return ($file_ext['ext'] ? $file : $setting->default);
}

/**
 * Metin doğrulama fonksiyonu
 */
function dazzlo_sanitize_text($input) {
    return wp_kses_post(force_balance_tags($input));
}

/**
 * Onay kutusu doğrulama fonksiyonu
 */
function dazzlo_sanitize_checkbox($input) {
    // Onay kutusu işaretlendiyse true döndürür
    return ((isset($input) && true === $input) ? true : false);
}

/**
 * Radyo kutusu doğrulama fonksiyonu
 */
function dazzlo_sanitize_radio($input, $setting) {
    $valid = array(
        'excerpt' => 'excerpt',
        'full' => 'full',
        'standard' => 'standard',
        'grid' => 'grid',
        'enable' => 'enable',
        'disable' => 'disable',
        'header1' => 'header1',
        'header2' => 'header2',
    );
    
    if (array_key_exists($input, $valid)) {
        return $input;
    } else {
        return $setting->default;
    }
}

/**
 * Özelleştirici CSS stillerini yükleme
 */
function dazzlo_panels_js() {
    wp_enqueue_style('dazzlo-customizer-ui-css', get_theme_file_uri('/customizer-ui.css'));
}
add_action('customize_controls_enqueue_scripts', 'dazzlo_panels_js');