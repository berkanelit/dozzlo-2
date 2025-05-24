<?php
/**
 * Template for the Post Layout Boxes on the homepage.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */
?>

    <!--  scroller -->
<?php if( is_home() ) {

    $default_value = __('SEE MORE POSTS', 'dazzlo');
?>
    <div class="layoutboxes">
<?php
    if (get_theme_mod('dazzlo_customizer_layoutbox1_disable') != 'disable') { ?>

        <div class="layoutbox1">
            <div class="layoutbox1-title">
                <?php
                if(get_theme_mod('dazzlo_layoutbox1_title','Layout Box 1')){ ?>
                    <h2><?php echo wp_kses_post(get_theme_mod('dazzlo_layoutbox1_title','Layout Box 1')) ?></h2>
                    <?php
                }

                ?>

            </div>

            <div class="dazzlo_layoutbox1  clearfix">
                <?php
                $dazzlo_image_box1 = "dazzlo-medium-image";
                $dazzlo_numberbox1 = get_theme_mod('dazzlo_layoutbox1_no');
                $dazzlo_categorybox1 = get_theme_mod('dazzlo_layoutbox1_category');
                if($dazzlo_numberbox1 > 5){
                    $dazzlo_nobox1 = 5;
                }
                else{
                    $dazzlo_nobox1 = 5;
                }
                $dazzlo_featured_list_args_box1 = array(
                    'posts_per_page' => $dazzlo_nobox1,
                    'cat' => $dazzlo_categorybox1
                );
                $dazzlo_featured_list_posts_box1 = new WP_Query($dazzlo_featured_list_args_box1);
                ?>
                <?php $counter = 0; // Initialize the counter variable ?>

                <?php while ($dazzlo_featured_list_posts_box1->have_posts()) : $dazzlo_featured_list_posts_box1->the_post() ?>


            <?php if ($counter === 0) : ?>
                <div class="item-wrapped-left">
                    <?php endif; ?>

                    <?php if ($counter === 1) : ?>
                    <div class="item-wrapped-right">
                        <?php endif; ?>

                    <div class="item-layoutbox1">
                        <div class="layoutbox1-wrap">

                            <?php if (has_post_thumbnail()) {
                                $image_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true); // Retrieve existing alt text
                                $image_alt = !empty($image_alt) ? $image_alt : get_the_title(get_post_thumbnail_id($post->ID)); // Use title if alt text is empty
                                $dazzlo_image_src_box1 = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $dazzlo_image_box1); ?>
                                <div class="image-layoutbox1">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <img class="feat-item" src="<?php if (!$dazzlo_image_src_box1) {
                                            echo esc_url(get_template_directory_uri() . '/images/slider-default.png');
                                        } else {
                                            echo esc_url($dazzlo_image_src_box1[0]);
                                        } ?>" alt="<?php echo esc_attr($image_alt); ?>" />
                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="image-layoutbox1">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <img  class="feat-item" src="<?php echo esc_url(get_template_directory_uri() . '/images/slider-default.png');  ?>" alt="<?php esc_attr_e('No Image', 'dazzlo'); ?>" />
                                    </a>
                                </div>
                            <?php } ?>

                            <div class="feat-item-wrapper">
                                <div class="feat-overlay">
                                    <div class="feat-inner">
                                        <div class="scroll-post">
                                            <?php echo dazzlo_getCategory(); ?>
                                        </div>
                                        <h3 class="feat-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>

                                        <div class="layoutbox1-meta">
                                            <div class="post-date">
                                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_date()); ?></a>
                                            </div>
                                            <div
                                                    class="postcomment"><?php comments_popup_link(__('0', 'dazzlo'), __('1', 'dazzlo'), __('%', 'dazzlo'), '', ''); ?></div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>

                    </div>

                    <?php if ($counter === 0) : ?>
                        </div>
                        <?php endif; ?>


                        <?php $counter++; // Increment the counter variable ?>


                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>



            </div>


        </div><!-- Layoutbox1 -->

    <?php }

    if (get_theme_mod('dazzlo_customizer_layoutbox2_disable') != 'disable') { ?>

        <div class="layoutbox2">
            <div class="layoutbox2-title">
                <?php
                if(get_theme_mod('dazzlo_layoutbox2_title','Layout Box 2')){ ?>
                    <h2><?php echo wp_kses_post(get_theme_mod('dazzlo_layoutbox2_title','Layout Box 2')) ?></h2>
                    <?php
                }

                ?>

            </div>

            <div class="dazzlo_layoutbox2  clearfix">
                <?php
                $dazzlo_image_box2 = "dazzlo-full-thumb";
                $dazzlo_numberbox2 = get_theme_mod('dazzlo_layoutbox2_no');
                $dazzlo_categorybox2 = get_theme_mod('dazzlo_layoutbox2_category');
                if($dazzlo_numberbox2 < 1){
                    $dazzlo_nobox2 = 3;
                }
                else{
                    $dazzlo_nobox2 = $dazzlo_numberbox2;
                }
                $dazzlo_featured_list_args_box2 = array(
                    'posts_per_page' => $dazzlo_nobox2,
                    'cat' => $dazzlo_categorybox2
                );
                $dazzlo_featured_list_posts_box2 = new WP_Query($dazzlo_featured_list_args_box2);
                ?>

                <?php while ($dazzlo_featured_list_posts_box2->have_posts()) : $dazzlo_featured_list_posts_box2->the_post() ?>

                    <div class="item-layoutbox2">
                        <div class="layoutbox2-wrap">

                            <?php if (has_post_thumbnail()) {

                                $image_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true); // Retrieve existing alt text
                                $image_alt = !empty($image_alt) ? $image_alt : get_the_title(get_post_thumbnail_id($post->ID)); // Use title if alt text is empty


                                $dazzlo_image_src_box2 = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $dazzlo_image_box2); ?>
                                <div class="image-layoutbox2">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">


                                        <img class="feat-item" src="<?php if (!$dazzlo_image_src_box2) {
                                            echo esc_url(get_template_directory_uri() . '/images/slider-default.png');
                                        } else {
                                            echo esc_url($dazzlo_image_src_box2[0]);
                                        } ?>" alt="<?php echo esc_attr($image_alt); ?>" />


                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="image-layoutbox2">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">

                                        <img  class="feat-item" src="<?php echo esc_url(get_template_directory_uri() . '/images/slider-default.png');  ?>" alt="<?php esc_attr_e('No Image', 'dazzlo'); ?>" />

                                    </a>
                                </div>
                            <?php } ?>

                            <div class="feat-item-wrapper">
                                <div class="feat-overlay">
                                    <div class="feat-inner">
                                        <div class="scroll-post">
                                            <?php echo dazzlo_getCategory(); ?>
                                        </div>
                                        <h3 class="feat-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>

                                        <div class="layoutbox2-meta">
                                            <div class="post-date">
                                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_date()); ?></a>
                                            </div>
                                            <div
                                                    class="postcomment"><?php comments_popup_link(__('0', 'dazzlo'), __('1', 'dazzlo'), __('%', 'dazzlo'), '', ''); ?></div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>

                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>

        </div><!-- Layoutbox2 -->
        <?php
    }

    if (get_theme_mod('dazzlo_customizer_layoutbox3_disable') != 'disable') { ?>

        <div class="layoutbox3">
            <div class="layoutbox3-title">
                <?php
                if(get_theme_mod('dazzlo_layoutbox3_title','Layout Box 3')){ ?>
                    <h2><?php echo wp_kses_post(get_theme_mod('dazzlo_layoutbox3_title','Layout Box 3')) ?></h2>
                    <?php
                }

                ?>

            </div>

            <div class="dazzlo_layoutbox3  clearfix">
                <?php
                $dazzlo_image_box3 = "dazzlo-medium-image";
                $dazzlo_numberbox3 = get_theme_mod('dazzlo_layoutbox3_no');
                $dazzlo_categorybox3 = get_theme_mod('dazzlo_layoutbox3_category');
                if($dazzlo_numberbox3 > 5){
                    $dazzlo_nobox3 = 5;
                }
                else{
                    $dazzlo_nobox3 = 5;
                }
                $dazzlo_featured_list_args_box3 = array(
                    'posts_per_page' => $dazzlo_nobox3,
                    'cat' => $dazzlo_categorybox3
                );
                $dazzlo_featured_list_posts_box3 = new WP_Query($dazzlo_featured_list_args_box3);
                ?>

                <?php while ($dazzlo_featured_list_posts_box3->have_posts()) : $dazzlo_featured_list_posts_box3->the_post() ?>

                    <div class="item-layoutbox3">
                        <div class="layoutbox3-wrap">

                            <?php if (has_post_thumbnail()) {
                                $image_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true); // Retrieve existing alt text
                                $image_alt = !empty($image_alt) ? $image_alt : get_the_title(get_post_thumbnail_id($post->ID)); // Use title if alt text is empty
                                $dazzlo_image_src_box3 = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $dazzlo_image_box3); ?>
                                <div class="image-layoutbox3">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">

                                        <img class="feat-item" src="<?php if (!$dazzlo_image_src_box3) {
                                            echo esc_url(get_template_directory_uri() . '/images/slider-default.png');
                                        } else {
                                            echo esc_url($dazzlo_image_src_box3[0]);
                                        } ?>" alt="<?php echo esc_attr($image_alt); ?>" />


                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="image-layoutbox3">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <img  class="feat-item" src="<?php echo esc_url(get_template_directory_uri() . '/images/slider-default.png');  ?>" alt="<?php esc_attr_e('No Image', 'dazzlo'); ?>" />
                                    </a>
                                </div>
                            <?php } ?>

                            <div class="feat-item-wrapper">
                                <div class="feat-overlay">
                                    <div class="feat-inner">
                                        <div class="scroll-post">
                                            <?php echo dazzlo_getCategory(); ?>
                                        </div>
                                        <h3 class="feat-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>

                                        <div class="description">
                                            <?php the_excerpt(); ?>
                                        </div>

                                        <div class="layoutbox3-meta">
                                            <div class="post-date">
                                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_date()); ?></a>
                                            </div>
                                            <div
                                                    class="postcomment"><?php comments_popup_link(__('0', 'dazzlo'), __('1', 'dazzlo'), __('%', 'dazzlo'), '', ''); ?></div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>

                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>

        </div><!-- Layoutbox3 -->
        <?php
    }

    if (get_theme_mod('dazzlo_customizer_layoutbox4_disable') != 'disable') { ?>

        <div class="layoutbox4">
            <div class="layoutbox4-title">
                <?php
                if(get_theme_mod('dazzlo_layoutbox4_title','Layout Box 4')){ ?>
                    <h2><?php echo wp_kses_post(get_theme_mod('dazzlo_layoutbox4_title','Layout Box 4')) ?></h2>
                    <?php
                }

                ?>

            </div>

            <div class="dazzlo_layoutbox4  clearfix">
                <?php
                $dazzlo_image_box4 = "dazzlo-medium-image";
                $dazzlo_numberbox4 = get_theme_mod('dazzlo_layoutbox4_no');
                $dazzlo_categorybox4 = get_theme_mod('dazzlo_layoutbox4_category');
                if($dazzlo_numberbox4 > 6){
                    $dazzlo_nobox4 = 6;
                }
                else{
                    $dazzlo_nobox4 = 6;
                }
                $dazzlo_featured_list_args_box4 = array(
                    'posts_per_page' => $dazzlo_nobox4,
                    'cat' => $dazzlo_categorybox4
                );
                $dazzlo_featured_list_posts_box4 = new WP_Query($dazzlo_featured_list_args_box4);
                ?>

                <?php while ($dazzlo_featured_list_posts_box4->have_posts()) : $dazzlo_featured_list_posts_box4->the_post() ?>

                    <div class="item-layoutbox4">
                        <div class="layoutbox4-wrap">

                            <?php if (has_post_thumbnail()) {
                                $image_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true); // Retrieve existing alt text
                                $image_alt = !empty($image_alt) ? $image_alt : get_the_title(get_post_thumbnail_id($post->ID)); // Use title if alt text is empty
                                $dazzlo_image_src_box4 = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $dazzlo_image_box4); ?>
                                <div class="image-layoutbox4">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <img class="feat-item" src="<?php if (!$dazzlo_image_src_box4) {
                                            echo esc_url(get_template_directory_uri() . '/images/slider-default.png');
                                        } else {
                                            echo esc_url($dazzlo_image_src_box4[0]);
                                        } ?>" alt="<?php echo esc_attr($image_alt); ?>" />
                                    </a>
                                </div>
                            <?php } else { ?>
                                <div class="image-layoutbox4">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <img  class="feat-item" src="<?php echo esc_url(get_template_directory_uri() . '/images/slider-default.png');  ?>" alt="<?php esc_attr_e('No Image', 'dazzlo'); ?>" />
                                    </a>
                                </div>
                            <?php } ?>

                            <div class="feat-item-wrapper">
                                <div class="feat-overlay">
                                    <div class="feat-inner">
                                        <div class="scroll-post">
                                            <?php echo dazzlo_getCategory(); ?>
                                        </div>
                                        <h3 class="feat-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>

                                        <div class="layoutbox4-meta">
                                            <div class="post-date">
                                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_date()); ?></a>
                                            </div>
                                            <div
                                                    class="postcomment"><?php comments_popup_link(__('0', 'dazzlo'), __('1', 'dazzlo'), __('%', 'dazzlo'), '', ''); ?></div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>

                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>

        </div><!-- Layoutbox4 -->
        <?php
    }
   ?>
    </div>
    <?php
}  ?>