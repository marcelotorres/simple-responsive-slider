<?php
/**
 * Plugin Name: Simple Responsive Slider
 * Plugin URI: http://www.marcelotorresweb.com/simple-responsive-slider/
 * Description: Put a simple, lightweight and responsive slider on your site. The plugin enables the cropped image so easy, plus the addition of links, captions and other settings. This plugin uses the <a href="http://responsiveslides.com/" target="_blank" title="ResponsiveSlides.js | Simple & lightweight responsive slider plugin ">ResponsiveSlides.js</a> made by <a href="http://viljamis.com/" target="_blank">Viljami</a>.
 * Author: marcelotorres
 * Author URI: http://marcelotorresweb.com/
 * Version: 0.2.2.5
 * License: GPLv2 or later
 * Text Domain: simple-responsive-slider
 * Domain Path: /languages/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Sets the plugin path/url.
$upload_dir = wp_upload_dir();
define( 'SIMPLE_RESPONSIVE_SLIDER_URL', plugins_url().'/simple-responsive-slider');
define( 'SIMPLE_RESPONSIVE_SLIDER_PATH', plugin_dir_path( __FILE__ ) );
define( 'SIMPLE_RESPONSIVE_SLIDER_URL_DIR_IMAGE', $upload_dir['baseurl'].'/simple-responsive-slider' );
define( 'SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE', $upload_dir['basedir'].'/simple-responsive-slider' );

//Add custom meta links for plugins page
add_filter( 'plugin_row_meta', 'custom_plugin_row_meta', 10, 2 );
function custom_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'simple-responsive-slider.php' ) !== false ) {
		$new_links = array(
				'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G85Z9XFXWWHCY" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" alt="PayPal - The safer, easier way to pay online!" border="0"></a>'
			);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}

// Create folder of image slider
if (!file_exists(SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE)) {
    mkdir(SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE, 0777, true);
}

// Load textdomain.
load_plugin_textdomain( 'simple-responsive-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Simple_Responsive_Slider class.
 *
 * @since 0.1
 */
class Simple_Responsive_Slider {

    /**
     * Class construct.
     */
    public function __construct() {

        // Styles.
        add_action( 'wp_enqueue_scripts', array( &$this, 'styles' ) );
		
		// Scripts.
        add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );
        add_action( 'wp_head', array( &$this, 'dynamics_scripts' ) );
    }

    /**
     * Register styles.
     */
    public function styles() {
		$settings = get_option('simpleresponsiveslider_settings');
		if($settings['slider_css'] == 'true' || $settings['slider_css'] == ''){
			wp_register_style( 'responsiveslides', plugins_url( '/assets/css/responsiveslides.css', __FILE__ ), array(), '', 'all' );
			wp_enqueue_style( 'responsiveslides');
		}
    }
	
	/**
     * Register scripts.
     */
    public function scripts() {        
		wp_register_script( 'responsiveslides', plugins_url('/assets/js/responsiveslides.min.js', __FILE__), array( 'jquery' ));
		wp_enqueue_script( 'responsiveslides' );
    }
	public function dynamics_scripts() {
	$settings = get_option('simpleresponsiveslider_settings');
	
	$auto = (empty($settings['auto'])) ? 'true' : $settings['auto'];
	$speed = (empty($settings['speed'])) ? '500' : $settings['speed'];
	$timeout = (empty($settings['timeout'])) ? '4000' : $settings['timeout'];
	$pager = (empty($settings['pager'])) ? 'false' : $settings['pager'];
	$nav = (empty($settings['nav'])) ? 'false' : $settings['nav'];
	$random = (empty($settings['nav'])) ? 'false' : $settings['random'];
	$pause = (empty($settings['pause'])) ? 'false' : $settings['pause'];
	$pause_controls = (empty($settings['pause_controls'])) ? 'false' : $settings['pause_controls'];
	$prev_text = (empty($settings['prev_text'])) ? __( 'Prev', 'simple-responsive-slider' ) : $settings['prev_text'];
	$next_text = (empty($settings['next_text'])) ? __( 'Next', 'simple-responsive-slider' ) : $settings['next_text'];
	$max_width = (empty($settings['max_width'])) ? '1000' : $settings['max_width'];
	
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(function() {
			$(".rslides").responsiveSlides({
			  auto: <?php echo $auto;?>,             // Boolean: Animate automatically, true or false
			  speed: <?php echo $speed;?>,            // Integer: Speed of the transition, in milliseconds
			  timeout: <?php echo $timeout;?>,          // Integer: Time between slide transitions, in milliseconds
			  pager: <?php echo $pager;?>,           // Boolean: Show pager, true or false
			  nav: <?php echo $nav;?>,             // Boolean: Show navigation, true or false
			  random: <?php echo $random;?>,          // Boolean: Randomize the order of the slides, true or false
			  pause: <?php echo $pause;?>,           // Boolean: Pause on hover, true or false
			  pauseControls: <?php echo $pause_controls;?>,    // Boolean: Pause when hovering controls, true or false
			  prevText: "<?php echo $prev_text;?>",   // String: Text for the "previous" button
			  nextText: "<?php echo $next_text;?>",       // String: Text for the "next" button
			  maxwidth: "<?php echo $max_width;?>",           // Integer: Max-width of the slideshow, in pixels
			  navContainer: "",       // Selector: Where controls should be appended to, default is after the 'ul'
			  manualControls: "",     // Selector: Declare custom pager navigation
			  namespace: "rslides",   // String: Change the default namespace used
			  before: function(){},   // Function: Before callback
			  after: function(){}     // Function: After callback
			});
		});
	 });
	</script>
	<?php
    }    

	/**
     * HTML of the box.
     *
     * @param  array $settings Simple Responsive Slider settings.
     *
     * @return string          Simple Responsive Slider HTML.
     */
    public function html_box() {
		$slider = get_option('simpleresponsiveslider_slides');
		$settings = get_option('simpleresponsiveslider_settings');		
		$images_id = explode(',', $slider['image_id']);
		
		/*echo '<!--<pre>';
		print_r($slider);
		echo '</pre>-->';*/
		
		$html = '<div class="rslides_container"><ul class="rslides">';		
			foreach($images_id as $id){
				$image_crop = wp_get_attachment_image_src( $id, 'full' );							
				if(!empty($image_crop[0])){
					
					$link_open = '';
					$image_cropped = '';
					$caption = '';
					$link_close = '';
					$link_target = '';
					
					$basename_image_crop = basename($image_crop[0]);				
					
					$upload_dir = wp_upload_dir();
					$filename = $basename_image_crop;
					$image_crop_full_path = SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE.'/srs-'.$filename;
					
					$curr_image_link = isset($slider['image_link-'.$id]) ? $slider['image_link-'.$id] : '';
					$curr_image_link_target = isset($slider['image_link_target-'.$id]) ? $slider['image_link_target-'.$id] : '';
					$curr_image_caption = isset($slider['image_caption-'.$id]) ? $slider['image_caption-'.$id] : '';
					$curr_image_disabled = isset($slider['image_disabled-'.$id]) ? $slider['image_disabled-'.$id] : '';
					
					//Check if image will cropped
					if (file_exists($image_crop_full_path)) {
						$image_cropped = '<img src="'.SIMPLE_RESPONSIVE_SLIDER_URL_DIR_IMAGE.'/srs-'.$basename_image_crop.'" alt="'. strip_tags(get_the_title($id)).'" />';
					} else {								
						$image_cropped = wp_get_attachment_image_src( $id, 'full' );
						$image_cropped = '<img src="'.$image_cropped[0].'" alt="'. strip_tags(get_the_title($id)).'" />';
					}
					if($curr_image_caption){
						$caption = '<p class="caption">'.$curr_image_caption.'</p>';
					}
					if($curr_image_link){
						if($curr_image_link_target){
							$link_target = 'target="_blank"';							
						}
						$link_open = '<a href="'.$curr_image_link.'" title="'.$curr_image_caption.'" '.$link_target.'>';
						$link_close = '</a>';
					}
					if(!($curr_image_disabled) == '1'){
						$html .= '<li>'.$link_open.$image_cropped.$caption.$link_close.'</li>';
					}
					
					$caption = null;
					$link_open = null;
					$link_close = null;
					$link_target = null;
				}
			}
		$html .= '</ul></div>';		
        return $html;
    }
}

// Include admin settings
require_once(SIMPLE_RESPONSIVE_SLIDER_PATH.'simple-responsive-slider-admin.php');

// New object Simple_Responsive_Slider
$simple_responsive_slider = new Simple_Responsive_Slider;

/**
 * Get the Simple Responsive Slider.
 *
 * @return string Simple Responsive Slider HTML.
 */
function get_simple_responsive_slider() {
    global $simple_responsive_slider;

    return $simple_responsive_slider->html_box();
}

/**
 * Shows the Simple Responsive Slider.
 *
 * @return string Simple Responsive Slider HTML.
 */
function show_simpleresponsiveslider() {
    echo get_simple_responsive_slider();
}

/**
 * The Simple Responsive Slider Shortcode.
 *
 * @return string Simple Responsive Slider HTML.
 */

add_shortcode('simpleresponsiveslider', 'simpleresponsiveslider_shortcode');
function simpleresponsiveslider_shortcode() {
	// Make sure we buffer our output
	ob_start();
	$html = get_simple_responsive_slider();
	$output = ob_get_clean();
	 
	return $html . $output;
}