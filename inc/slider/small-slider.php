<?php
    /**
     * Template for the post excerpt slider on the homepage.
     *
     * @package dazzlo
     * @since dazzlo 1.0
     */
    ?>

    <!--  scroller -->
    <?php if( is_home() && get_theme_mod( 'dazzlo_customizer_slider_disable' ) != 'disable' ) {
    ?>
    <div class="slider-wrapper23 small">
            <div class="dazzlo_slides container">
                    <?php
                    $dazzlo_image_size = "dazzlo-widget-small-thumb";
                    $dazzlo_number23 = get_theme_mod( 'dazzlo_slider_slides' );
                    $dazzlo_category=get_theme_mod('dazzlo_slider_category');

                        $dazzlo_featured_list_args  = array(
                            'posts_per_page' => $dazzlo_number23,
                            'cat' => $dazzlo_category
                        );
                        $dazzlo_featured_list_posts = new WP_Query( $dazzlo_featured_list_args );
                    ?>

                    <?php while( $dazzlo_featured_list_posts->have_posts() ) : $dazzlo_featured_list_posts->the_post() ?>
                <div class="item-slide">


                <div class="slide-wrap">


                    <?php if ( has_post_thumbnail() ) {
                        $image_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true); // Retrieve existing alt text
                        $image_alt = !empty($image_alt) ? $image_alt : get_the_title(get_post_thumbnail_id($post->ID)); // Use title if alt text is empty
                        $dazzlo_image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), $dazzlo_image_size ); ?>
                        <div class="image-slide">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <img src="<?php echo $dazzlo_image[0]; ?>" alt="<?php echo esc_attr($image_alt); ?>" />
                            </a>
                        </div>
                    <?php }
                    else { ?>
                        <div class="image-slide">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/images/slider-default.png');  ?>" alt="<?php esc_attr_e('No Image', 'dazzlo'); ?>" />
                            </a>
                        </div>
                    <?php } ?>
                    <div class="feat-item-wrapper">
                        <div class="feat-overlay">
                            <div class="feat-inner">
                                <h2 class="feat-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                            </div>
                        </div>

                        <div class="slider-meta">
                            <div class="post-date">
                                <a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_date()); ?></a>
                            </div>
                        </div>

                    </div>


                </div>

                </div>

            <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
            </div><!-- slides -->
    </div>



    <?php } ?>