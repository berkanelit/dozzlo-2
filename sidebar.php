<?php
/**
 * Template for the sidebar and its widgets.
 *
 * This template handles the sidebar display and widget areas.
 *
 * @package dazzlo
 * @since dazzlo 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<aside id="sidebar" class="site-sidebar" role="complementary">
    <div class="theiaStickySidebar">
        <?php if (is_active_sidebar('sidebar')) : ?>
            <div class="sidebar-widgets">
                <?php dynamic_sidebar('sidebar'); ?>
            </div>
        <?php else : ?>
            <div class="sidebar-no-widgets">
                <p><?php _e('Please add widgets to your sidebar from the Widgets screen in the admin.', 'dazzlo'); ?></p>
                <?php if (current_user_can('edit_theme_options')) : ?>
                    <a href="<?php echo esc_url(admin_url('widgets.php')); ?>" class="button sidebar-widget-link">
                        <i class="fa fa-plus-circle"></i> <?php _e('Add Widgets', 'dazzlo'); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</aside>
