<?php
/**
 * Odin_Theme_Options class, modified to use this plugin.
 *
 * Built settings page.
 *
 * @package  Odin
 * @category Options
 * @author   WPBrasil
 * @version  1.0
 */
class Simpleresponsiveslider_Options {

    /**
     * Settings tabs.
     *
     * @var array
     */
    protected $tabs = array();

    /**
     * Settings fields.
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Settings construct.
     *
     * @param string $page_title Page title.
     * @param string $slug       Page slug.
     * @param string $capability User capability.
     */
    public function __construct(
        $page_title = 'Theme Settings',
        $slug       = 'simpleresponsiveslider-settings',
        $capability = 'manage_options'
    ) {
        $this->page_title = $page_title;
        $this->slug       = $slug;
        $this->capability = $capability;

        // Actions.
        add_action( 'admin_menu', array( &$this, 'add_page' ) );
        add_action( 'admin_init', array( &$this, 'create_settings' ) );

        if ( isset( $_GET['page'] ) && $_GET['page'] == $slug )
            add_action( 'admin_enqueue_scripts', array( &$this, 'scripts' ) );

    }

    /**
     * Add Settings Theme page.
     *
     * @return void
     */
    public function add_page() {
        add_theme_page(
            $this->page_title,
            $this->page_title,
            $this->capability,
            $this->slug,
            array( &$this, 'settings_page' )
        );
    }

    /**
     * Load options scripts.
     *
     * @return void
     */
    function scripts() {
        // Color Picker.
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        // Media Upload.
        wp_enqueue_media();
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_style( 'thickbox' );

        // jQuery UI.
        wp_enqueue_script( 'jquery-ui-sortable' );
		
		//jCrop
        wp_enqueue_script( 'jcrop' );
		wp_enqueue_style( 'jcrop' );

        // Theme Options.
        wp_register_style( 'simpleresponsiveslider-admin', plugins_url('../assets/css/admin.css', __FILE__), array(), null, 'all' );
        wp_enqueue_style( 'simpleresponsiveslider-admin' );
        wp_register_script( 'simpleresponsiveslider-admin', plugins_url('../assets/js/admin.js', __FILE__), array( 'jquery' ), null, true );
        wp_enqueue_script( 'simpleresponsiveslider-admin' );

        // Localize strings.
        wp_localize_script(
            'simpleresponsiveslider-admin',
            'simpleresponsiveslider_admin_params',
            array(
                'gallery_title'  => __( 'Add images in gallery', 'simple-responsive-slider' ),
                'gallery_button' => __( 'Add in gallery', 'simple-responsive-slider' ),
                'gallery_remove' => __( 'Remove image', 'simple-responsive-slider' ),
                'upload_title'   => __( 'Choose a file', 'simple-responsive-slider' ),
                'upload_button'  => __( 'Add file', 'simple-responsive-slider' ),
            )
        );
    }

    /**
     * Set settings tabs.
     *
     * @param array $tabs Settings tabs.
     */
    public function set_tabs( $tabs ) {
        $this->tabs = $tabs;
    }

    /**
     * Set settings fields
     *
     * @param array $fields Settings fields.
     */
    public function set_fields( $fields ) {
        $this->fields = $fields;
    }

    /**
     * Get current tab.
     *
     * @return string Current tab ID.
     */
    protected function get_current_tab() {
        if ( isset( $_GET['tab'] ) )
            $current_tab = $_GET['tab'];
        else
            $current_tab = $this->tabs[0]['id'];

        return $current_tab;
    }

    /**
     * Get the menu current URL.
     *
     * @return string Current URL.
     */
    private function get_current_url() {
        $url = 'http';
        if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' )
            $url .= 's';

        $url .= '://';

        if ( '80' != $_SERVER['SERVER_PORT'] )
            $url .= $_SERVER['SERVER_NAME'] . ' : ' . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'];
        else
            $url .= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];

        return esc_url( $url );
    }

    /**
     * Get tab navigation.
     *
     * @param  string $current_tab Current tab ID.
     *
     * @return string              Tab Navigation.
     */
    protected function get_navigation( $current_tab ) {

        $html = '<h2 class="nav-tab-wrapper">';        

        foreach ( $this->tabs as $tab ) {

            $current = ( $current_tab == $tab['id'] ) ? ' nav-tab-active' : '';

            $html .= sprintf( '<a href="%s?page=%s&amp;tab=%s" class="nav-tab%s">%s</a>', $this->get_current_url(), $this->slug, $tab['id'], $current, $tab['title'] );
        }

        $html .= '</h2>';

        echo $html;
    }

    /**
     * Built settings page.
     *
     * @return void
     */
    public function settings_page() {
        ?>

        <div class="wrap">

            <?php
                // Display themes screen icon.
                screen_icon( 'themes' );

                // Get current tag.
                $current_tab = $this->get_current_tab();

                // Display the navigation menu.
                $this->get_navigation( $current_tab );

                // Display erros.
                settings_errors();
            ?>

            <form method="post" action="options.php">

                <?php
					
                    foreach ( $this->tabs as $tabs ) {
                        if ( $current_tab == $tabs['id'] ) {

                            // Prints nonce, action and options_page fields.
                            settings_fields( $tabs['id'] );

                            // Prints settings sections and settings fields.
                            do_settings_sections( $tabs['id'] );
                        }
                    }

                    // Display submit button.
                    submit_button('', 'primary button-large');
                ?>

            </form>

        </div>

        <?php
    }

    /**
     * Create settings.
     *
     * @return void
     */
    public function create_settings() {

        // Register settings fields.
        foreach ( $this->fields as $section => $items ) {

            // Register settings sections.
            add_settings_section(
                $section,
                $items['title'],
                '__return_false',
                $items['tab']
            );

            foreach ( $items['options'] as $option ) {

                $type = isset( $option['type'] ) ? $option['type'] : 'text';

                $args = array(
                    'id'          => $option['id'],
                    'tab'         => $items['tab'],
                    'description' => isset( $option['description'] ) ? $option['description'] : '',
                    'name'        => $option['label'],
                    'section'     => $section,
                    'size'        => isset( $option['size'] ) ? $option['size'] : null,
                    'options'     => isset( $option['options'] ) ? $option['options'] : '',
                    'default'     => isset( $option['default'] ) ? $option['default'] : ''
                );

                add_settings_field(
                    $option['id'],
                    $option['label'],
                    array( &$this, 'callback_' . $type ),
                    $items['tab'],
                    $section,
                    $args
                );
            }
        }

        // Register settings.
        foreach ( $this->tabs as $tabs ) {
            register_setting( $tabs['id'], $tabs['id'], array( &$this, 'validate_input' ) );
        }
    }

    /**
     * Get Option.
     *
     * @param  string $tab     Tab that the option belongs
     * @param  string $id      Option ID.
     * @param  string $default Default option.
     *
     * @return array           Item options.
     */
    protected function get_option( $tab, $id, $default = '' ) {
        $options = get_option( $tab );
		
		/*echo '<pre>';
		print_r($options);
		echo '</pre>';*/
		
        if ( isset( $options[$id] ) )
            $default = $options[$id];

        return $default;

    }

    /**
     * Text field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Text field HTML.
     */
    public function callback_text( $args ) {
        $this->callback_input( $args );
    }

    /**
     * Input field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Input field HTML.
     */
    public function callback_input( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = esc_html( $this->get_option( $tab, $id, $args['default'] ) );

        // Sets input size.
        $size = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

        // Sets input type.
        $type = isset( $args['options']['type'] ) ? $args['options']['type'] : 'text';

        // Sets input class.
        $class = isset( $args['options']['class'] ) ? ' ' . $args['options']['class'] : '';

        // Sets input styles.
        $styles = isset( $args['options']['styles'] ) ? ' style="' . $args['options']['styles'] . '"' : '';

        $html = sprintf(
            '<input type="%5$s" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="%4$s-text%6$s"%7$s />',
            $id, $tab, $current, $size, $type, $class, $styles
        );

        // Displays the description.
        if ( $args['description'] )
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );

        echo $html;
    }

    /**
     * Radio field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Radio field HTML.
     */
    public function callback_radio( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = $this->get_option( $tab, $id, $args['default'] );

        $html = '';
        foreach( $args['options'] as $key => $label ) {
            $item_id = $id . '_' . $key;
            $key = sanitize_title( $key );

            $html .= sprintf( '<input type="radio" id="%1$s_%3$s" name="%2$s[%1$s]" value="%3$s"%4$s />', $id, $tab, $key, checked( $current, $key, false ) );
            $html .= sprintf( '<label for="%s"> %s</label><br />', $item_id, $label );
        }

        // Displays the description.
        if ( $args['description'] )
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );

        echo $html;
    }

    /**
     * Select field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Select field HTML.
     */
    public function callback_select( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = $this->get_option( $tab, $id, $args['default'] );

        $html = sprintf( '<select id="%1$s" name="%2$s[%1$s]">', $id, $tab );
        foreach( $args['options'] as $key => $label ) {
            $key = sanitize_title( $key );

            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
        }
        $html .= '</select>';

        // Displays the description.
        if ( $args['description'] )
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );

        echo $html;
    }
	
	/**
     * Image Plupload field for Simple Responsive Slider callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Plupload and Field for this plugin in HTML
     */
    public function callback_image_plupload_for_srs( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];
		
		// Sets current option.
        $current = $this->get_option( $tab, $id, $args['default'] );
		/*echo '<pre>';
		print_r(get_option('simpleresponsiveslider_slides'));
		echo '</pre>';*/
		
        $html = '<div class="simpleresponsiveslider-gallery-container">';
		
		// Adds "adds images in gallery" url.
		$html .= sprintf( '<p class="simpleresponsiveslider-gallery-add hide-if-no-js"><a href="#" class="button secundary-large">%s</a></p>', __( 'Add images', 'simple-responsive-slider' ) );

        // Displays the description.
        if ( $args['description'] )
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
			$html .= sprintf( '<p class="alert-delete" style="display:none">%s</p>', __( 'Save changes to finish deleting the images removed', 'simple-responsive-slider' ));
            $html .= '<ul class="simpleresponsiveslider-gallery-images">';
                if ( ! empty( $current ) ) {
                    // Gets the current images.
                    $attachments = array_filter( explode( ',', $current ) );

                    if ( $attachments ) {
                        foreach ( $attachments as $attachment_id ) {
							$image_crop = wp_get_attachment_image_src( $attachment_id, 'full' );
							$basename_image_crop = basename($image_crop[0]);							
														
							$filename = $basename_image_crop;
							$image_crop_full_path = SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE.'/srs-'.$filename;
							
							//Check if image will cropped
							if (file_exists($image_crop_full_path)) {
								$thumbnail = '<img src="'.SIMPLE_RESPONSIVE_SLIDER_URL_DIR_IMAGE.'/srs-'.$basename_image_crop.'" alt="" />';
							} else {								
								$thumbnail = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
								$thumbnail = '<img src="'.$thumbnail[0].'" alt="" />';
							}
							
							//Get the link and caption values
							$current_link = $this->get_option( $tab, 'image_link-'.$attachment_id, $args['default'] );
							$current_link_target = $this->get_option( $tab, 'image_link_target-'.$attachment_id, $args['default'] );
							$current_disabled = $this->get_option( $tab, 'image_disabled-'.$attachment_id, $args['default'] );
							$current_caption = $this->get_option( $tab, 'image_caption-'.$attachment_id, $args['default'] );
							$is_disabled = '';
							$checked_target = '';
							$checked_disabled = '';
							
							if($current_link_target == '_blank'){
								$checked_target = 'checked';
							}
							if($current_disabled == '1'){
								$checked_disabled = 'checked';
								$is_disabled = 'disabled';
							}
							
                            $html .= sprintf( '<li class="%5$s image" data-attachment_id="%1$s">%2$s<ul class="actions"><li><a href="#" class="button delete" title="%3$s">%3$s</a><a href="?page=simple-responsive-slider&tab=simpleresponsiveslider_crop&editor_image='.$attachment_id.'" class="crop button" title="%4$s">%4$s</a></li></ul>',
                                $attachment_id,
                                $thumbnail,                                
                                __( 'Remove image', 'simple-responsive-slider' ),
                                __( 'Crop image', 'simple-responsive-slider' ),
								$is_disabled
                            );
							
							$html .= '<div class="fields">';							
							$html .= sprintf( '<label class="full" for="image_link-'.$attachment_id.'">%4$s<input type="text" id="image_link-'.$attachment_id.'" name="%2$s[image_link-'.$attachment_id.']" value="'.$current_link.'"></label>', $id, $tab, $current, __( 'Image link', 'simple-responsive-slider' ) );
							$html .= sprintf( '<label class="full" for="image_caption-'.$attachment_id.'">%4$s<textarea id="image_caption-'.$attachment_id.'" name="%2$s[image_caption-'.$attachment_id.']" cols="30" rows="10">'.$current_caption.'</textarea></label>', $id, $tab, $current, __( 'Image caption', 'simple-responsive-slider' ) );
							$html .= sprintf( '<label class="six" for="image_link_target-'.$attachment_id.'">%5$s
												<input type="checkbox" id="image_link_target-'.$attachment_id.'" name="%2$s[image_link_target-'.$attachment_id.']" value="_blank" %4$s>
											</label>', $id, $tab, $current, $checked_target, __( 'Open in new window/tab?', 'simple-responsive-slider' ) );
							$html .= sprintf( '<label class="six right" for="image_disabled-'.$attachment_id.'">%5$s
												<input type="checkbox" id="image_disabled-'.$attachment_id.'" name="%2$s[image_disabled-'.$attachment_id.']" value="1" %4$s>
											</label>', $id, $tab, $current, $checked_disabled, __( 'Disable image', 'simple-responsive-slider' ));
							$html .= '</div>';
							$html .= '</li>';
							
							
							$current_link_target = null;
							$checked_target = null;
							$checked_disabled = null;
							$is_disabled = null;
                        }
                    }
                }
            $html .= '</ul><div class="clear"></div>';

            // Adds the hidden input.            
            $html .= sprintf( '<input type="hidden" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="simpleresponsiveslider-gallery-field" />', $id, $tab, $current );

        $html .= '</div>';


        echo $html;
    }
	
	/**
     * Image Plupload field for Simple Responsive Slider callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string coordinates for crop image.
     */
    public function callback_image_crop_for_srs( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];
		
		$editor_image = isset($_GET['editor_image']) ? $_GET['editor_image'] : '';
		$settings_updated = isset($_GET['settings-updated']) ? $_GET['settings-updated'] : '';
		
		$image_for_crop = get_attached_file( $args['default']);
		$image_for_crop_src = wp_get_attachment_image_src( $args['default'], 'full' );		
		
		// Sets current option.
        $current = $this->get_option( $tab, $id, $args['default'] );
		
		// Get images.
        $other_images = get_option('simpleresponsiveslider_slides');		
		
        $html = '<div class="simpleresponsiveslider-crop-container">';	
		
		if($settings_updated || empty($editor_image)){
			if(empty($editor_image)){
				$term = __('an', 'simple-responsive-slider');
			}else{
				$term = __('other', 'simple-responsive-slider');
			}
			$html .= sprintf('<p class="description">%1$s '.$term.' %2$s</p>', __('Select', 'simple-responsive-slider'), __('image to crop', 'simple-responsive-slider'));
			$images_id = explode(',', $other_images['image_id']);
			if ( !empty($images_id) ) {
				foreach ( $images_id as $other_images_id ) {
					if($other_images_id != $editor_image){					
						$html .= sprintf('<a href="?page=simple-responsive-slider&tab=simpleresponsiveslider_crop&editor_image='.$other_images_id.'" title="%s">', __('Click to crop this image', 'simple-responsive-slider') );
						$html .= wp_get_attachment_image( $other_images_id, array(80, 80) );
						$html .= '</a>';
					}
				}
			}
		}
		
		// Adds the hidden input.
		$html .= sprintf( '<input type="hidden" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="simpleresponsiveslider-crop-image" />', $id, $tab, $current );
		$html .= sprintf( '<input type="hidden" id="x" name="%2$s[x_'.$editor_image.']" value="" class="simpleresponsiveslider-crop-image-coord-x" />', $id, $tab, $current );
		$html .= sprintf( '<input type="hidden" id="y" name="%2$s[y_'.$editor_image.']" value="" class="simpleresponsiveslider-crop-image-coord-y" />', $id, $tab, $current );
		$html .= sprintf( '<input type="hidden" id="x2" name="%2$s[x2_'.$editor_image.']" value="" class="simpleresponsiveslider-crop-image-coord-x" />', $id, $tab, $current );
		$html .= sprintf( '<input type="hidden" id="y2" name="%2$s[y2_'.$editor_image.']" value="" class="simpleresponsiveslider-crop-image-coord-y" />', $id, $tab, $current );
		$html .= sprintf( '<input type="hidden" id="w" name="%2$s[w_'.$editor_image.']" value="" class="simpleresponsiveslider-crop-image-coord-w" />', $id, $tab, $current );
		$html .= sprintf( '<input type="hidden" id="h" name="%2$s[h_'.$editor_image.']" value="" class="simpleresponsiveslider-crop-image-coord-h" />', $id, $tab, $current );
		
		if($editor_image){
		// Displays the description.
		if ( $args['description'] ){
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
		}
        $html .= '<img src="'.$image_for_crop_src[0].'" alt="" id="cropbox" />';
		
		//Get current coordenates
		$current_x = $this->get_option( $tab, 'x_'.$editor_image, $args['default'] );
        $current_y = $this->get_option( $tab, 'y_'.$editor_image, $args['default'] );
        $current_w = $this->get_option( $tab, 'w_'.$editor_image, $args['default'] );
        $current_h = $this->get_option( $tab, 'h_'.$editor_image, $args['default'] );
		
		$settings = get_option('simpleresponsiveslider_settings');		
		
		//Crop and save the image		
		$targ_w = (empty($settings['max_width'])) ? '1000' : $settings['max_width'];
		$targ_h = (empty($settings['min_height'])) ? '250' : $settings['min_height'];		
		$quality = 100;
		$png_quality = $quality / 10;
		if($png_quality > 9){
			$png_quality = 9;
		}

		$image_path = $image_for_crop;
							
		$exif = explode('.', basename($image_path));
		if($exif[1] == 'gif'){
		$img_r = imagecreatefromgif($image_path);
		}elseif($exif[1] == 'jpg'){
			$img_r = imagecreatefromjpeg($image_path);
		}elseif($exif[1] == 'png'){
			$img_r = imagecreatefrompng($image_path);
		}
		
		$dst_r = imagecreatetruecolor( $targ_w, $targ_h );
		
		imagecopyresampled($dst_r,$img_r,0,0,$current_x,$current_y,$targ_w,$targ_h,$current_w,$current_h);
		
		$filename = basename($image_path);
		
		$exif = explode('.', basename($image_path));
		if($exif[1] == 'gif'){
			imagegif($dst_r,SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE.'/srs-'.$filename,$quality);
		}elseif($exif[1] == 'jpg'){
			imagejpeg($dst_r,SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE.'/srs-'.$filename,$quality);
		}elseif($exif[1] == 'png'){
			imagepng($dst_r,SIMPLE_RESPONSIVE_SLIDER_PATH_DIR_IMAGE.'/srs-'.$filename,$png_quality);
		}
		imagedestroy($dst_r);
		}
		
		$html .= '</div>';
		
        echo $html;
    }

    /**
     * Sanitization fields callback.
     *
     * @param  string $input The unsanitized collection of options.
     *
     * @return string        The collection of sanitized values.
     */
    public function validate_input( $input ) {

        // Create our array for storing the validated options
        $output = array();

        // Loop through each of the incoming options
        foreach ( $input as $key => $value ) {

            // Check to see if the current option has a value. If so, process it.
            if ( isset( $input[$key] ) )
                $output[$key] = apply_filters( 'simpleresponsiveslider_theme_options_validate_' . $this->slug, $value );

        }

        return $output;
    }
}