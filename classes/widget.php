<?php
/**
 * Adds SimpleResponsiveSlider_Widget widget.
 */
class SimpleResponsiveSlider_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'SimpleResponsiveSlider_Widget', // Base ID
			'Simple Responsive Slider', // Name
			array( 'description' => __( 'Show the Simple Responsive Slider', 'simple-responsive-slider' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$slider = get_simple_responsive_slider();

		echo $args['before_widget'];
		if ( ! empty( $title ) ){
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo $slider;
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = $instance[ 'title' ];
		$slider = get_simple_responsive_slider();
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';		
		$instance['slider'] = get_simple_responsive_slider();

		return $instance;
	}

} // class SimpleResponsiveSlider_Widget
// register SimpleResponsiveSlider_Widget widget
function register_simpleresponsiveslider_widget() {
    register_widget( 'SimpleResponsiveSlider_Widget' );
}
add_action( 'widgets_init', 'register_simpleresponsiveslider_widget' );