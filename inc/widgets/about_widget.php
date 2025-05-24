<?php
/**
 * Plugin Name: About Widget
 */

add_action( 'widgets_init', 'dazzlo_about_load_widget' );

function dazzlo_about_load_widget() {
	register_widget( 'dazzlo_about_widget' );
}

class dazzlo_about_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
    public function __construct()
    {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'dazzlo_about_widget', 'description' => __('An About Me Widget', 'dazzlo') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'dazzlo_about_widget' );

		/* Create the widget. */
        parent::__construct(  'dazzlo_about_widget', __('Dazzlo: About Me', 'dazzlo'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

        $image = !empty($instance['image']) ? $instance['image'] : '';

        $subtitle = !empty($instance['subtitle']) ? $instance['subtitle'] : '';

        $description = !empty($instance['description']) ? $instance['description'] : '';

        $signing = !empty($instance['signing']) ? $instance['signing'] : '';

		/* Before widget (defined by themes). */
        echo $args['before_widget'];

		/* Display the widget title if one was input (before and after defined by themes). */
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        ?>
			
			<div class="about-widget">
				
				<?php if($image) : ?>
				<div class="about-img">
					<img loading="lazy"  src="<?php echo esc_url($image); ?>" alt="<?php echo esc_html($title); ?>" />
				</div>
				<?php endif; ?>
				
				<?php if($subtitle) : ?>
				<span class="about-title"><?php echo wp_kses_post($subtitle); ?></span>
				<?php endif; ?>
				
				<?php if($description) : ?>
				<p><?php echo wp_kses_post($description); ?></p>
				<?php endif; ?>
				
				<?php if($signing) : ?>
				<span class="about-autograph"><img  loading="lazy" src="<?php echo esc_url($signing); ?>" alt="" /></span>
				<?php endif; ?>
				
			</div>
			
		<?php

		/* After widget (defined by themes). */
        echo $args['after_widget'];
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['image'] = esc_url_raw( $new_instance['image'] );
		$instance['subtitle'] = sanitize_text_field($new_instance['subtitle']);
		$instance['description'] = wp_kses_post($new_instance['description']);
		$instance['signing'] = esc_url_raw( $new_instance['signing'] );

		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'About Me', 'image' => '', 'subtitle' => '', 'description' => '', 'signing' => '');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title','dazzlo' ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:96%;" />
		</p>
		
		<!-- image url -->
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'image' )); ?>"><?php esc_html_e( 'Image URL','dazzlo' ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'image' )); ?>" value="<?php echo esc_attr($instance['image']); ?>" style="width:96%;" /><br />
		</p>
		
		<!-- subtitle -->
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'subtitle' )); ?>"><?php esc_html_e( 'Sub title','dazzlo' ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'subtitle' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'subtitle' )); ?>" value="<?php echo esc_attr($instance['subtitle']); ?>" style="width:96%;" /><br />
		</p>
		
		<!-- description -->
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php esc_html_e( 'About me text','dazzlo' ); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'description' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>" style="width:95%;" rows="6"><?php echo esc_html($instance['description']); ?></textarea>
		</p>
		
		<!-- autograph url -->
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'signing' )); ?>"><?php esc_html_e( 'Autograph Image URL','dazzlo' ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'signing' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'signing' )); ?>" value="<?php echo esc_attr($instance['signing']); ?>" style="width:96%;" /><br />
		</p>


	<?php
	}
}

?>