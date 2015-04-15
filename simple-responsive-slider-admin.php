<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Include classes for option page
require_once(SIMPLE_RESPONSIVE_SLIDER_PATH.'/classes/options.php');
require_once(SIMPLE_RESPONSIVE_SLIDER_PATH.'/classes/widget.php');

// Add script in <head> backend
function load_script_admin() {
	$curr_page = isset($_GET['page']) ? $_GET['page'] : '';
	$curr_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
	$editor_image = isset($_GET['editor_image']) ? $_GET['editor_image'] : '';
	
	$settings = get_option('simpleresponsiveslider_settings');
	$coord = get_option('simpleresponsiveslider_crop');
	$coord = (empty($coord)) ? '' : $coord;
	
	if($curr_page == 'simple-responsive-slider' && $curr_tab == 'simpleresponsiveslider_crop' && (!empty($editor_image))){
?>
    <script type="text/javascript">
	/**
     * jCrop
     */
	jQuery(document).ready(function($) {
		$(function(){
			$('#cropbox').Jcrop({
				minSize: [ <?php echo $settings['max_width']?>, <?php echo $settings['min_height']?>],
				maxSize: [ <?php echo $settings['max_width']?>, <?php echo $settings['min_height']?> ],
				allowResize: true,
				setSelect:[ <?php echo $coord['x_'.$editor_image];?>, <?php echo $coord['y_'.$editor_image];?>, <?php echo $coord['x2_'.$editor_image];?>, <?php echo $coord['y2_'.$editor_image];?> ],
				onSelect: updateCoords
			});
		});

		function updateCoords(c)
		{
			$('#x').val(c.x);
			$('#y').val(c.y);
			$('#x2').val(c.x2);
			$('#y2').val(c.y2);
			$('#w').val(c.w);
			$('#h').val(c.h);
		};
	});
	</script>
<?php
	}
}
add_action('admin_head', 'load_script_admin');

//New object for create settings
$simpleresponsiveslider_options = new Simpleresponsiveslider_Options( __( 'Simple Responsive Slider', 'simple-responsive-slider' ), 'simple-responsive-slider' );

$curr_page = isset($_GET['page']) ? $_GET['page'] : '';

if ( $wp_version < 3.6 && $curr_page == 'simple-responsive-slider') {
    echo '<div class="error"><p><strong>';
	echo __( 'This plugin not is supported in current WordPress version. <a href="./update-core.php">Please update the WordPress for version 3.6 or above.</a>', 'simple-responsive-slider' );
	echo '</strong></p></div><style type="text/css">p.submit{display:none}</style>';
}else{
//Create tabs
$simpleresponsiveslider_options->set_tabs(
	array(
		array(
			'id' => 'simpleresponsiveslider_slides',
			'title' => __( 'Slides', 'simple-responsive-slider' )
		),		
		array(
			'id' => 'simpleresponsiveslider_crop',
			'title' => __( 'Crop images', 'simple-responsive-slider' )
		),
		array(
			'id' => 'simpleresponsiveslider_settings',
			'title' => __( 'Settings', 'simple-responsive-slider' )
		)
	)
);

//Create fields of the corresponding tabs
$simpleresponsiveslider_options->set_fields(
	array(
		'setting_tab' => array(
			'tab'   => 'simpleresponsiveslider_settings',
			'title' => __( 'Settings', 'simple-responsive-slider' ),
			'options' => array(
				array(
					'id' => 'max_width',
					'label' => __( 'Max-width of the slider (in pixels)', 'simple-responsive-slider' ),
					'type' => 'text',
					'default' => '1000',
					'description' => '', 
					'size' => 'small'
				),
				array(
					'id' => 'min_height',
					'label' => __( 'Max-height of the slider (in pixels)', 'simple-responsive-slider' ),
					'type' => 'text',
					'default' => '250',
					'description' => '',
					'size' => 'small'
				),
				array(
					'id' => 'auto',
					'label' => __( 'Animate automatically', 'simple-responsive-slider' ),
					'type' => 'radio',
					'default' => 'true',
					'description' => '',
					'options' => array( 
						'true' => __('Yes', 'simple-responsive-slider'),
						'false' => __('No', 'simple-responsive-slider'),					
					)
				),
				array(
					'id' => 'speed',
					'label' => __( 'Speed of the transition (in milliseconds)', 'simple-responsive-slider' ),
					'type' => 'text',
					'default' => '500',
					'description' => '',
					'size' => 'small'
				),
				array(
					'id' => 'timeout',
					'label' => __( 'Time between slide transitions (in milliseconds)', 'simple-responsive-slider' ),
					'type' => 'text',
					'default' => '4000',
					'description' => '',
					'size' => 'small'
				),
				array(
					'id' => 'pager',
					'label' => __( 'Show pager', 'simple-responsive-slider' ),
					'type' => 'radio',
					'default' => 'false',
					'description' => '',
					'options' => array( 
						'true' => __('Yes', 'simple-responsive-slider'),
						'false' => __('No', 'simple-responsive-slider'),					
					)
				),
				array(
					'id' => 'nav',
					'label' => __( 'Show navigation', 'simple-responsive-slider' ),
					'type' => 'radio',
					'default' => 'false',
					'description' => '',
					'options' => array( 
						'true' => __('Yes', 'simple-responsive-slider'),
						'false' => __('No', 'simple-responsive-slider'),					
					)
				),
				array(
					'id' => 'random',
					'label' => __( 'Randomize the order of the slides', 'simple-responsive-slider' ),
					'type' => 'radio',
					'default' => 'false',
					'description' => '',
					'options' => array( 
						'true' => __('Yes', 'simple-responsive-slider'),
						'false' => __('No', 'simple-responsive-slider'),					
					)
				),
				array(
					'id' => 'pause',
					'label' => __( 'Pause on hover', 'simple-responsive-slider' ),
					'type' => 'radio',
					'default' => 'false',
					'description' => '',
					'options' => array( 
						'true' => __('Yes', 'simple-responsive-slider'),
						'false' => __('No', 'simple-responsive-slider'),					
					)
				),
				array(
					'id' => 'pause_controls',
					'label' => __( 'Pause when hovering controls', 'simple-responsive-slider' ),
					'type' => 'radio',
					'default' => 'false',
					'description' => '',
					'options' => array( 
						'true' => __('Yes', 'simple-responsive-slider'),
						'false' => __('No', 'simple-responsive-slider'),					
					)
				),
				array(
					'id' => 'prev_text',
					'label' => __( 'Text for the "previous" button', 'simple-responsive-slider' ),
					'type' => 'text',
					'default' => __( 'Back', 'simple-responsive-slider' ),
					'description' => '',
					'size' => 'regular'
				),
				array(
					'id' => 'next_text',
					'label' => __( 'Text for the "next" button', 'simple-responsive-slider' ),
					'type' => 'text',
					'default' => __( 'Next', 'simple-responsive-slider' ),
					'description' => '',
					'size' => 'regular'
				),
				array(
					'id' => 'slider_css',
					'label' => __( 'Load default CSS for the slider', 'simple-responsive-slider' ),
					'type' => 'radio',
					'default' => 'true',
					'description' => sprintf(__( 'If you use a custom CSS, <a href="%s" class="thickbox">click here and copy the default CSS</a> to use as an example.', 'simple-responsive-slider' ), SIMPLE_RESPONSIVE_SLIDER_URL.'/assets/css/responsiveslides.css?TB_iframe=true&width=600&height=550' ),
					'options' => array(						
						'true' => __('Yes', 'simple-responsive-slider'),
						'false' => __('No, I\'ll use a custom CSS', 'simple-responsive-slider'),
					)
				)
			)
		),
		'slider_tab' => array(
			'tab'   => 'simpleresponsiveslider_slides',
			'title' => __( 'Add slide', 'simple-responsive-slider' ),
			'options' => array(
				array(
					'id' => 'image_id',
					'label' => __( 'Add images and their corresponding link and captions', 'simple-responsive-slider' ),
					'type' => 'image_plupload_for_srs',
				)				
			)
		),
		'image_crop_tab' => array(
			'tab'   => 'simpleresponsiveslider_crop',
			'title' => __( 'Crop Images', 'simple-responsive-slider' ),
			'options' => array(
				array(
					'id' => 'image_crop',
					'label' => '',
					'type' => 'image_crop_for_srs',
					'default' => isset( $_GET['editor_image'] ) ? $_GET['editor_image'] : '',
					'description' => __( 'Click and position the crop', 'simple-responsive-slider' ),
				)				
			)
		),
	)
);
}