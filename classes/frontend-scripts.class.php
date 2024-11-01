<?php
/**
 * WPComment Frontend Scripts Class
 * 
 * It will register/enqueue all wpcomment scripts to frontent.
*/


if ( ! defined( 'ABSPATH' ) ) { exit; }


class wpcomment_FRONTEND_SCRIPTS {
	
	/**
	 * Return scripts URL.
	 * 
	 * @var URL
	 *
	*/
	private static $scripts_url =  '';
	
	/**
	 * Return current wpcomment version.
	 * 
	 * @var string
	 *
	*/
	private static $version =  '';
	
	
	/**
	 * Return main scripts framework class.
	 *
	*/
	private static $scripts_class;
	

	/**
	 * Main Init
	 */
	public static function init() {
		
		self::$scripts_url   = WPCOMMENT_SCRIPTS::get_url();
		self::$version       = WPCOMMENT_SCRIPTS::get_version();
		
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts_admin' ) );
	}


	/**
	 * Register all PPOM Scripts.
	 */
	private static function get_scripts() {
		
		$register_scripts = array(
			'wpcomment-sm-popup' => array(
				'src'     => self::$scripts_url.'/js/wpcomment-simple-popup.js',
				'deps'    => array( 'jquery' ),
				'version' => '1.0',
			),
			'wpcomment-plusminus-lib' => array(
				'src'     => self::$scripts_url.'/js/wpcomment-plusminus.js',
				'deps'    => array( 'jquery' ),
				'version' => '1.0',
			),
			'wpcomment-tooltip' => array(
				'src'     => self::$scripts_url.'/js/wpcomment-tooltip.js',
				'deps'    => array( 'jquery' ),
				'version' => '1.0',
			),
			'wpcomment-inputmask' => array(
				'src'     => self::$scripts_url.'/js/inputmask/jquery.inputmask.min.js',
				'deps'    => array( 'jquery' ),
				'version' => '5.0.6',
			),
			'iris' => array(
				'src'     => admin_url( 'js/iris.min.js' ),
				'deps'    => array( 
			            		'jquery',
			            		'jquery-ui-core',
			            		'jquery-ui-draggable', 
			            		'jquery-ui-slider'
			        		),
				'version' => '1.0.7',
			),
			'wpcomment-zoom' => array(
				'src'     => self::$scripts_url.'/js/image-tooltip.js',
				'deps'    => array( 'jquery' ),
				'version' => self::$version,
			),
			'wpcomment-bs-slider' => array(
				'src'     => self::$scripts_url.'/js/bs-slider/bootstrap-slider.min.js',
				'deps'    => array( 'jquery' ),
				'version' => '10.0.0',
			),
			'wpcomment-file-upload' => array(
				'src'     => self::$scripts_url.'/js/file-upload.js',
				'deps'    => array('jquery', 'plupload'),
				'version' => self::$version,
			),
			'wpcomment-inputs' => array(
				'src'     => self::$scripts_url.'/js/wpcomment.inputs.js',
				'deps'    => array( 'jquery', 'jquery-ui-datepicker' ),
				'version' => self::$version,
			),
			'wpcomment-tooltip-lib' => array(
				'src'     => self::$scripts_url.'/js/tooltip/tooltip.js',
				'deps'    => array( 'jquery' ),
				'version' => self::$version,
			),
			'wpcomment-validate' => array(
				'src'     => self::$scripts_url.'/js/validate.js',
				'deps'    => array('jquery'),
				'version' => self::$version,
			),
		);
		
		return apply_filters('wpcomment_frontend_scripts_before_register', $register_scripts);
	}
	

	/**
	 * Register Styles
	 */
	private static function get_styles() {
		
		$register_styles = array(
			'wpcomment-main' => array(
				'src'     => self::$scripts_url.'/css/wpcomment-style.css',
				'deps'    => array(),
				'version' => self::$version,
			),
			'wpcomment-sm-popup' => array(
				'src'     => self::$scripts_url.'/css/wpcomment-simple-popup.css',
				'deps'    => array(),
				'version' => self::$version,
			),
			'wpcomment-bootstrap' => array(
				'src'     => self::$scripts_url.'/css/bootstrap/bootstrap.min.css',
				'deps'    => array(),
				'version' => '4.0.0',
			),
			'wpcomment-bootstrap-modal' => array(
				'src'     => self::$scripts_url.'/css/bootstrap/bootstrap.modal.css',
				'deps'    => array(),
				'version' => '4.0.0',
			),
			'jqueryui' => array(
				'src'     => self::$scripts_url.'/js/ui/css/smoothness/jquery-ui-1.10.3.custom.min.css',
				'deps'    => array(),
				'version' => '1.10.3',
			),
			'wpcomment-bs-slider-css' => array(
				'src'     => self::$scripts_url.'/js/bs-slider/bootstrap-slider.min.css',
				'deps'    => array(),
				'version' => '10.0.0',
			),
			'wpcomment-croppie-lib' => array(
				'src'     => self::$scripts_url.'/js/croppie/croppie.css',
				'deps'    => array(),
				'version' => '2.6.4',
			),
			'wpcomment-modal-lib' => array(
				'src'     => self::$scripts_url.'/css/wpcomment-modal.css',
				'deps'    => array(),
				'version' => '1.1.1',
			),
			'wpcomment-divider-input' => array(
				'src'     => self::$scripts_url.'/css/divider.css',
				'deps'    => array(),
				'version' => '1.0',
			),
			'wpcomment-tooltip-lib' => array(
				'src'     => self::$scripts_url.'/js/tooltip/tooltip.css',
				'deps'    => array(),
				'version' => '1.0',
			),
			
		);
		
		return apply_filters('wpcomment_frontend_styles_before_register', $register_styles);
	}
	
	/**
	 * Load Admin Scripts
	 **/
	public static function load_scripts_admin($hook){
		
		// loading scripts only comments.php
		if( 'comment.php' !== $hook ) return;
		
		// Get all styles & scripts
		$all_scripts = self::get_scripts();
		$all_styles  = self::get_styles();
		
		// Register all styles & scripts
		WPCOMMENT_SCRIPTS::register_scripts($all_scripts);
		WPCOMMENT_SCRIPTS::register_styles($all_styles);
		
		self::load_scripts_comments();
	}
	
	
	/**
	 * Load Frontend Scripts.
	 */
	public static function load_scripts() {
		
		$allow_posts = apply_filters('wpcomment_allowed_posts', ['post']);
        
        if ( ! in_array(get_post_type(), $allow_posts) ) return;
		
		global $post;

		// Get all styles & scripts
		$all_scripts = self::get_scripts();
		$all_styles  = self::get_styles();
		
		// Register all styles & scripts
		WPCOMMENT_SCRIPTS::register_scripts($all_scripts);
		WPCOMMENT_SCRIPTS::register_styles($all_styles);
		
		if (!is_object($post)) return;
		
		self::load_scripts_comments();
		
	}
	
	
	/**
	 * Load Frontend Scripts by product ID.
	 */
	public static function load_scripts_comments() {
		
		$form_obj = new WPComment_Form;

		if ( $form_obj::$fields ) {
			
			$wpcomment_meta_fields	= $form_obj::$fields;

			if( ! $wpcomment_meta_fields ) return '';
			
		    $wpcomment_conditional_fields  = array();
		    $croppie_options		  = array();
		    $global_js_vars		      = array();
		    $file_js_vars		      = array();
		    $input_js_vars		      = array();
		    $wpcomment_file_inputs		  = array();
		    $inputs_meta_updated      = array();
		    $show_price_per_unit	  = false;
			
			WPCOMMENT_SCRIPTS::enqueue_style( 'wpcomment-tooltip-lib' );
			WPCOMMENT_SCRIPTS::enqueue_script( 'wpcomment-tooltip-lib' );
			
		    WPCOMMENT_SCRIPTS::enqueue_style( 'wpcomment-main' );
		    WPCOMMENT_SCRIPTS::enqueue_style( 'wpcomment-sm-popup' );
    		WPCOMMENT_SCRIPTS::enqueue_script( 'wpcomment-sm-popup' );
    		
    		self::add_inline_css('global');
    		
    		// WPCOMMENT_SCRIPTS::inline_style('wpcomment-main', $wpcomment->inline_css);
		    
		    if( wpcomment_load_bootstrap_css() ) {
    			WPCOMMENT_SCRIPTS::enqueue_style( 'wpcomment-bootstrap' );
    			WPCOMMENT_SCRIPTS::enqueue_style( 'wpcomment-bootstrap-modal' );
		    }
					
			$enable_file_rename = apply_filters('wpcomment_upload_file_rename', true, $wpcomment_meta_fields);
			
			$file_js_vars['enable_file_rename'] = $enable_file_rename;
			
			/* Global JS Inputs Vars */
			$global_js_vars = array(
				'ajaxurl'    => admin_url( 'admin-ajax.php', (is_ssl() ? 'https' : 'http') ),
				'plugin_url' => WPCOMMENT_URL,
			);
			
			
			if( $wpcomment_meta_fields ) {
				
			    foreach($wpcomment_meta_fields as $field){
					
					$type			= isset($field['type']) ? $field['type'] : '';
					$title			= ( isset($field['title']) ? $field ['title'] : '');
					$data_name		= ( isset($field['data_name']) ? $field ['data_name'] : $title);
					$data_name		= sanitize_key( $data_name );
					$field['data_name'] = $data_name;
					$field['title']		= stripslashes($title);
					
					// updated single inputs meta to new variable
					$fields_meta = $field;
					
					if( ! wpcomment_is_field_visible($fields_meta) ) continue;
					
					// change input type in js file
					$fields_meta['field_type'] = apply_filters('wpcomment_js_fields', $type, $fields_meta);
					
				
					// Allow other types to be hooked
					$type = apply_filters('wpcomment_load_input_script_type', $type, $field);
					
					switch( $type ) {
					    
					    case 'text':
					    	if( !empty($field['input_mask']) ) {
					    		WPCOMMENT_SCRIPTS::enqueue_script( 'wpcomment-inputmask' );
			                }
			            	break;
			            	
					    case 'date':
					        if(isset($field['jquery_dp']) && $field['jquery_dp'] == 'on') {
					        	WPCOMMENT_SCRIPTS::enqueue_style( 'jqueryui' );
					        }
					        break;
					        
						case 'daterange':
							// Check if value is in GET 
							if( !empty($_GET[$data_name]) ) {
								
								$value    = sanitize_key($_GET[$data_name]);
								$to_dates = explode(' - ', $value);
								$fields_meta['start_date'] = $to_dates[0];
								$fields_meta['end_date'] = $to_dates[0];
							}
				        break;
					        
						case 'color':
							WPCOMMENT_SCRIPTS::enqueue_script( 'iris' );
							
							if( !empty($_GET[$data_name]) ) {
								
								$fields_meta['default_color'] = sanitize_key($_GET[$data_name]);
							}
			    	    	break;
			    	    	
			    	    case 'image':
			    	    	WPCOMMENT_SCRIPTS::enqueue_script( 'wpcomment-zoom' );
			    	    	self::add_inline_css('image', $field);
			    	    	break;
			    	    	
			    	   case 'file':
							
			    	    	$wpcomment_file_inputs[] = $field;
			    	    	
			    	    	WPCOMMENT_SCRIPTS::enqueue_script( 'wpcomment-file-upload' );
			    	    	
			    	    	break;
							
						case 'divider':
							WPCOMMENT_SCRIPTS::enqueue_style( 'wpcomment-divider-input' );
							break;
					}
					
					$inputs_meta_updated[] = $fields_meta;
					
					// Conditional fields
					if( isset($field['logic']) && $field['logic'] == 'on' && !empty($field['conditions']) ){
						
						$field_conditions = $field['conditions'];
						
						//WPML Translation
						$condition_rules = $field_conditions['rules'];
						$rule_index = 0;
						foreach($condition_rules as $rule) {
							if( !isset($field_conditions['rules'][$rule_index]['element_values']) ) continue;
							
							$field_conditions['rules'][$rule_index]['element_values'] = wpcomment_wpml_translate($rule['element_values'], 'PPOM');
							$rule_index++;
						}
						
						$wpcomment_conditional_fields[$data_name] = $field_conditions;
					}
						
					/**
					 * creating action space to render hooks for more addons
					 **/
					do_action('wpcomment_hooks_inputs', $field, $data_name);
			    }
			}
			
			WPCOMMENT_SCRIPTS::enqueue_script( 'wpcomment-inputs' );
			
		    // WPCOMMENT_SCRIPTS::inline_script('wpcomment-inputs', htmlspecialchars_decode($wpcomment->inline_js));

		    WPCOMMENT_SCRIPTS::inline_style( 'wpcomment-main', html_entity_decode(get_option('wpcomment_css_output')) );
			
			$file_js_vars['file_inputs'] = $wpcomment_file_inputs;
			
			$input_js_vars['field_meta']               = $inputs_meta_updated;
			
			
			// $input_js_vars = apply_filters('wpcomment_input_vars', $input_js_vars, $product);
			
			// Conditional fields
			if( !empty($wpcomment_conditional_fields) || apply_filters('wpcomment_enqueue_conditions_js', false)) {
				$wpcomment_conditions_script = 'wpcomment-conditions';
				$wpcomment_conditions_script = apply_filters('wpcomment_conditional_script_file', $wpcomment_conditions_script);
				
				wpcomment_SCRIPTS::enqueue_script( $wpcomment_conditions_script, self::$scripts_url."/js/{$wpcomment_conditions_script}.js", array('jquery','wpcomment-inputs') );
				
				self::set_localize_data( $wpcomment_conditions_script, 'wpcomment_input_vars', $input_js_vars, $global_js_vars );
			}
			
			if( wpcomment_is_client_validation_enabled() ){
				WPCOMMENT_SCRIPTS::enqueue_script( 'wpcomment-validate' );
			}
			
			self::set_localize_data( 'wpcomment-file-upload', 'wpcomment_file_vars', $file_js_vars, $global_js_vars );
			self::set_localize_data( 'wpcomment-inputs', 'wpcomment_input_vars', $input_js_vars, $global_js_vars );
			self::set_localize_data( 'wpcomment-sm-popup', 'wpcomment_tooltip_vars' );
		}
		
		do_action('wpcomment_after_scripts_loaded', $form_obj);
	}
	
	
	private static function set_localize_data($handle, $var_name, $js_vars=array(), $global_js_vars=array()){
		
		if (!wp_script_is( $handle )) { return; }
		
		$localize_data = [];
		switch ($handle) {
			
			case 'wpcomment-file-upload':
				
				$localize_data = array(
					'file_upload_path_thumb' => wpcomment_get_dir_url(true),
					'file_upload_path'       => wpcomment_get_dir_url(),
					'mesage_max_files_limit' => __(' files allowed only', "wpcomment"),
					'delete_file_msg'	     => __("Are you sure?", "wpcomment"),
					'aviary_api_key'	     => '',
					'plupload_runtime'	     => (wpcomment_if_browser_is_ie()) ? 'html5,html4' : 'html5,silverlight,html4,browserplus,gear',
					'wpcomment_file_upload_nonce' => wp_create_nonce( 'wpcomment_uploading_file_action' ),
					'wpcomment_file_delete_nonce' => wp_create_nonce( 'wpcomment_deleting_file_action' ),
				);
				
				break;
			
			case 'wpcomment-inputs':
			case 'wpcomment-conditions-v2':
				
				
				$localize_data = array(
					'wpcomment_validate_nonce'       => wp_create_nonce( 'wpcomment_validating_action' ),
					'validate_msg'				=> __("is a required field",'wp-comment-fields'),
					'image_max_msg'				=> __("You can only select a maximum of",'wp-comment-fields'),
					'image_min_msg'				=> __("You can only select a minimum of",'wp-comment-fields'),
				);
				
				break;
				
				case 'wpcomment-sm-popup':
					$localize_data = array(
						'wpcomment_tooltip_position'    => wpcomment_get_option('wpcomment_input_tooltip_position', 'top'),
						'wpcomment_tooltip_trigger'     => wpcomment_get_option('wpcomment_input_tooltip_trigger'),
						'wpcomment_tooltip_interactive' => wpcomment_get_option('wpcomment_input_tooltip_interactive'),
						'wpcomment_tooltip_animation'   => wpcomment_get_option('wpcomment_input_tooltip_animation','fade'),
						'wpcomment_tooltip_maxwidth'    => wpcomment_get_option('wpcomment_input_tooltip_maxwidth','500'),
						'wpcomment_tooltip_borderclr'   => wpcomment_get_option('wpcomment_input_tooltip_borderclr'),
						'wpcomment_tooltip_bgclr'       => wpcomment_get_option('wpcomment_input_tooltip_bgclr'),
						'wpcomment_tooltip_txtclr'      => wpcomment_get_option('wpcomment_input_tooltip_txtclr'),
					);
				break;
		}
		
		$localize_data = array_merge($js_vars, $localize_data, $global_js_vars);
		
		$localize_data = apply_filters( $var_name, $localize_data );
		
		WPCOMMENT_SCRIPTS::localize_script( $handle, $var_name, $localize_data );
	}
	
	
	public static function add_inline_css($type, $field_meta=array()){
		
		ob_start();
		include WPCOMMENT_PATH .'/css/style.php';
		$inline_styles = ob_get_clean();
		
		WPCOMMENT_SCRIPTS::inline_style('wpcomment-main', $inline_styles);
	}
}

wpcomment_FRONTEND_SCRIPTS::init();