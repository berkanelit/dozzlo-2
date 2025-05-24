<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */

get_header(); ?>



<?php get_template_part( 'inc/slider/slider' ); ?>
    <?php get_template_part( 'inc/slider/small-slider' ); ?>



<?php if( is_home() ) { ?>

    
    <div class="below-slider-wrapper">
    <?php	/* Widget */	if ( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar('Below Slider') ) ?>
        </div>



    <?php } ?>


		<div id="content-wrap" class="clearfix">
			<!--  slider -->


			<div id="content" tabindex="-1" class="post-list <?php if(get_theme_mod('dazzlo_general_sidebar_home') == true) : ?>fullwidth<?php endif; ?> ">
            <div class="theiaStickySidebar">

			<!-- layoutboxes code -->
			<?php get_template_part( 'layoutboxes' ); ?>
                <!-- layoutboxes code end -->


                <!-- post navigation -->
				<?php get_template_part( 'template-title' ); ?>

				<div class="post-wrap clearfix list">
                    <?php if( is_home() ) { ?>
                    <?php if(get_theme_mod('dazzlo_latest_posts','Latest Posts')){ ?>
                        <h2><?php echo wp_kses_post(get_theme_mod('dazzlo_latest_posts','Latest Posts')) ?></h2>
                    <?php } } ?>

                    <!-- load the posts -->
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<div <?php post_class('post'); ?>>
							<div class="box">

								<?php if ( has_post_format( 'gallery' , $post->ID ) ) { ?>
									<?php if ( function_exists( 'array_gallery' ) ) { array_gallery(); } ?>
								<?php } ?>

								<!-- load the video -->
								<?php if ( get_post_meta( $post->ID, 'arrayvideo', true ) ) { ?>
									<div class="arrayvideo">
										<?php echo esc_html(get_post_meta( $post->ID, 'arrayvideo', true )) ?>
									</div>

								<?php } else { ?>

									<!-- load the featured image -->
									<?php if ( has_post_thumbnail() ) { ?>


                                            <div class="featured-image-wrap"><a class="featured-image" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'dazzlo-random-thumb' , array('loading' => 'lazy')); ?></a></div>


									<?php }
                                    else {
                                        ?>
                                        <div class="featured-image-wrap">
                                            <a class="featured-image" href="<?php echo esc_url(get_permalink()); ?>">
                                                <img src="<?php echo esc_url(get_template_directory_uri() . '/images/slider-default.png');  ?>" alt="<?php esc_attr_e('No Image', 'dazzlo'); ?>" />
                                            </a>
                                        </div>
                                        <?php
                                    }
									?>

								<?php } ?>


									<div class="title-wrap <?php if(get_theme_mod('dazzlo_general_post_summary') == 'full') : ?>alignleft <?php endif; ?>">


                                        <div class="post-metawrap">
                                            <?php dazzlo_getCategory(); ?>
                                            <div class="postcomment"><?php comments_popup_link( __( '0', 'dazzlo' ), __( '1', 'dazzlo' ), __( '%', 'dazzlo' ),'','' ); ?></div>
                                        </div>

										<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>


                                        <?php

                                                the_excerpt();

                                        ?>



                                    </div><!-- title wrap -->




							</div><!-- box -->
						</div><!-- post-->

					<?php endwhile; ?>
                    <!-- post navigation -->
                    <?php get_template_part( 'template-nav' ); ?>

                </div><!-- post wrap -->


				<?php else: ?>
            </div>
			</div><!-- content -->

			<?php endif; ?>
			<!-- end posts -->

			<!-- comments -->
			<?php if( is_single() && comments_open() ) {
				comments_template();
			} ?>
            </div>
		</div><!--content-->

		<!-- load the sidebar -->
		<?php if(!get_theme_mod('dazzlo_general_sidebar_home')) {
            get_sidebar();
        } ?>
	</div><!-- content wrap -->

	<!-- load footer -->
	<?php get_footer(); ?>
