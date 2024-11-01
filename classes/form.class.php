<?php
/**
 * PPOM Frontend Form Rendering Class
 *
 * It control inputs base templates
 *
 * @version  1.0
 */
 
/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }
 
class WPComment_Form {
	
	private static $ins = null;
    public static $fields;

	function __construct(){
	    
	    $wpcomment_meta = wpcomment_get_saved_meta();
	    // filter out if fields are not active
    	$wpcomment_meta = array_filter($wpcomment_meta, function($f){
    		return 'on' === $f['status'];
    	});
    	
	    self::$fields = $wpcomment_meta;
	}

	public static function get_instance() {
	    
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
    
    /**
	 * PPOM main wrapper 2 classes
	 * 
	 * @return string
	 */
    function wrapper_inner_classes() {
        
        $classes = ['form-row','wpcomment-rendering-fields','align-items-center','wpcomment-section-collapse'];
                        
        $classes = apply_filters('wpcomment_wrapper2_classes', $classes);
                            
        return implode(' ', $classes);
    }
    
    /**
	 * PPOM fields rendering callback
	 * 
	 * @return fields html
	 */
    function fields_render(){

	    $posted_values = '';
        
        $section_started = false;
        $wpcomment_field_counter = 0;
        $wpcomment_collapse_counter = 0;
        $allow_nextprev = wpcomment_get_option('wpcomment-collapse-nextprev');

    	// posted value being
    	// used wpcomment-pro
        $posted_values = apply_filters('wpcomment_default_values', $posted_values, $_POST, self::$fields);
        
        foreach( self::$fields as $meta ) {
            
    		$type      = isset($meta['type']) ? $meta['type'] : '';
            $title     = isset($meta['title']) ? wpcomment_wpml_translate($meta['title'], 'wp-comment-fields') : '';
    		$data_name = isset($meta['data_name']) ? $meta['data_name'] : $title;
            $wpcomment_field_counter++;
            
    		// Set ID on meta against dataname
    		$meta['id'] = $data_name;
    		
            // Dataname senatize
	        $data_name = sanitize_key( $data_name );
	        
            $field_html    = '';
        
            // checking field visibility
			if( ! wpcomment_is_field_visible($meta) ) continue;
			
            if( empty($data_name) ) { printf(__("Please provide data name property for %s", "wpcomment"), $title); continue; }
            
    		$default_value = $this->get_field_default_value($posted_values, $data_name, $meta);
            
            $wpcomment_cond_data = wpcomment_get_conditional_data_attributes($meta);

            // Text|Email|Date|Number
            $wpcomment_field_attributes = $this->get_attributes($meta, $type);

            // Set inputs attr into meta
            $meta['attributes'] = $wpcomment_field_attributes;
            
            $field_wrapper_class = $this->field_main_wrapper_classes($meta);
            
            
            // Collapse Fields Section
    		if( $type == 'collapse' ) {
    			$collapse_type	= isset($meta['collapse_type']) ? $meta['collapse_type'] : '';
    
    			if( $section_started ) {
    				
    				echo '<div class="wpcomment-loop-fields" style="clear:both"></div>';
    				
    				if ($allow_nextprev == 'yes') {
    					echo '<div class="wpcomment-collapse-nextprev-btn" data-collapse-index="'.esc_attr($wpcomment_collapse_counter).'">';
    		    			echo '<button class="wpcomment-collapse-prev">'.__("Prev", "wpcomment").'</button>';
		    			    echo '<button class="wpcomment-collapse-next">'.__("Next", "wpcomment").'</button>';
    	    			echo '</div>';
    				}
    				echo '</div>';
    			}
    
    			if ($collapse_type == 'end') {
    				echo '<div class="wpcomment-collapsed-child-end">';
    			}
    
    			if ($collapse_type != 'end' ) {
        			echo '<h4 data-collapse-id="'.esc_attr($data_name).'" class="wpcomment-collapsed-title">'.$title.'</h4>';
        			echo '<div class="collapsed-child">';
    			}
    				
    			$section_started = true;
    			$wpcomment_collapse_counter++;
    		}
    		
    		// skip collapse div
		    if ($type == 'collapse') continue;
            
			
			
            $field_wrapper_div = '<div data-data_name='.esc_attr($data_name).' '.$wpcomment_cond_data.' class="'.esc_attr($field_wrapper_class).'">';
            $field_html .= apply_filters('wpcomment_field_wrapper_div', $field_wrapper_div, $meta);

                /**
                 * creating action space to render more addons
                 * 
                 * Legacy Hook: wpcomment_rendering_inputs
                 * 
                 * Template based load addons Hook: wpcomment_rendering_inputs_{$type}
                 * 
                 * Updated by Najeeb on May 24, 2021
                 * Now the CORE inputs will be rendered via function rather hooks
                **/
                ob_start();
                
                
                $core_inputs = $this->get_core_inputs();
                
                if( in_array($type, $core_inputs) ) {
                    $this->render_input_template($meta, $default_value);
                }
                
                do_action("wpcomment_rendering_inputs_{$type}", $meta, $default_value);
                
                
    			$field_html .= ob_get_clean();

            $field_html .= '</div>';
            
            if( count(self::$fields) == $wpcomment_field_counter && $section_started ) {
    			$field_html .= '</div>';
    		}
			
			// Filter: nmforms_input_htmls
            // @TODO need to change with relevant name
            echo apply_filters("nmforms_input_html", $field_html, $meta, $default_value);
            
    	}
    }
    
    function render_input_template($meta, $default_value){
        
        $type = isset($meta['type']) ? $meta['type'] : '';     
        
        $template_path  = "frontend/inputs/{$type}.php";
        $template_vars	= array( 
                            'field_meta'    => $meta, 
                            'default_value' => $default_value
                        );
        
        $template_vars   = apply_filters('wpcomment_input_templates_vars', $template_vars, $this);                        
        
        wpcomment_load_input_templates( $template_path, $template_vars );
    }
    
    /**
	 * Field Main Wrapper Classes
	 * 
	 * @hook wpcomment_field_main_wrapper_class
	 */
    function field_main_wrapper_classes($meta) {
        
        $width    = $this->field_wrapper_width($meta);
        $dataname = isset($meta['data_name']) ?  sanitize_key($meta['data_name']): '';

        $classes   = array();
        $classes[] = 'wpcomment-field-wrapper';
        $classes[] = 'wpcomment-col';
        $classes[] = 'col-md-'. $width;
        $classes[] = $dataname;
        $classes[] = "wpcomment-wrapper_outer-{$dataname}";

        $wrapper_classes = implode(' ',$classes);
        
        $wrapper_classes =  apply_filters('wpcomment_field_main_wrapper_class', $wrapper_classes, $classes, $meta);
        
        return $wrapper_classes;
    }
    
    /**
	 * Field Colunm Width
	 *
	 * @hook wpcomment_{$field_type}_input_meta_width
	 */
    function field_wrapper_width($input_meta) {

        $field_column = isset($input_meta['width'] ) ? $input_meta['width']: 12;
    
        // Check width has old settings
        if( strpos( $field_column, '%' ) !== false ) {
            
            $field_column = 12;
        } elseif( intval($field_column) > 12 ) {
            $field_column = 12;
        }
        
        return apply_filters("wpcomment_input_meta_width", $field_column, $input_meta);
    }
    
    /**
	 * Rendering form extra contents
	 */
    function form_contents(){

        ob_start();
	    	wpcomment_load_input_templates( 'frontend/component/form-data.php', 
	    	apply_filters('wpcomment_form_extra_contents', $this)
	    	);
	    echo ob_get_clean();
    }

    /**
	 * Check If PPOM Fields Empty
	 */
	function has_wpcomment_fields(){
        
        $return = false;
		if( self::$fields ) {
            $return = true;
        }

        return apply_filters('has_wpcomment_fields', $return);
	}
	
	/**
	 * Get default input/posted values 
	 * 
	 * @return defual_value
	 */
	function get_field_default_value($posted_values, $data_name, $meta){

        $default_value = isset($meta['default_value'] ) ?  $meta['default_value']: '';
        $type          = isset($meta['type']) ? $meta['type'] : '';

        // current values from $_GET/$_POST
        if( isset($posted_values[$data_name]) ) {

            switch ($type) {
            
                case 'image':
                    $image_data  = $posted_values[$data_name];
                    unset($default_value);
                    foreach($image_data as $data){
                        $default_value[] = json_decode( stripslashes($data), true);
                    }
                    break;
                
                default:
                    $default_value  = $posted_values[$data_name];
                    break;
                }
                
        } else if( isset($_GET[$data_name]) ) {
            // When Cart Edit addon used
            $default_value  = sanitize_text_field($_GET[$data_name]);
        }else if( isset($_POST['wp-comment-fields']['fields'][$data_name]) && apply_filters('wpcomment_retain_after_add_to_cart', true) ) {
		    $default_value  = sanitize_text_field($_POST['wp-comment-fields']['fields'][$data_name]);
	    } else {
            // Default values in settings
            switch ($type) {
                
                case 'textarea':
                    
                    if( is_numeric($default_value) ) {
                        $content_post = get_post( intval($default_value) );
                        $content = !empty($content_post) ? $content_post->post_content : '';
                        $content = apply_filters('the_content', $content);
                        $default_value = str_replace(']]>', ']]&gt;', $content);
                    }
                    break;
                    
                case 'checkbox':
                    $default_value = isset($meta['checked']) ? explode("\r\n", $meta['checked']) : '';
                    break;
                    
                case 'select':
                case 'radio':
                case 'timezone':
                case 'palettes':
                case 'image':
                case 'cropper':
                    $default_value = isset($meta['selected']) ? $meta['selected'] : '';
                    break;
            }
        }
        
        // Stripslashes: default values
        $default_value = ! is_array($default_value) ? stripslashes($default_value) : $default_value;

        return apply_filters("wpcomment_field_default_value", $default_value, $meta);
    }
    
    
    /**
     * Get PPOM inputs
    */
    function get_core_inputs(){
    	
    	$coreinputs = array(
    			'text',
    			'textarea',
    			'select',
    			'radio',
    			'checkbox',
    			'email',
    			'date',
    			'number',
    			'hidden',
    			'daterange',
    			'color',
    			'file',
    			'image',
    			'timezone',
    			'section',
    			'palettes',
    			'divider'
    	);
    		
    	return apply_filters('wpcomment_core_input_types', $coreinputs);
    }
    
    
    // Since 15.1: checking if all meta has unique datanames
    public function has_unique_datanames() {
        
        if( ! self::$fields ) return false;
        
        $has_unique = true;
        $datanames_array = array();
        
        // wpcomment_pa(self::$fields);
        
        foreach( self::$fields as $field ) {
            
            $type = isset($field['type']) ? $field['type'] : '';
            
            // ignore collapased fields
            if( $type == 'collapse' ) continue;
            
            if( !isset($field['data_name']) ) {
                $has_unique = false;
                break;
            }
            
            if( in_array($field['data_name'], $datanames_array) ) {
                
                $has_unique = false;
                break;
            }
            
            $datanames_array[] = $field['data_name'];
            
        }
        
        return apply_filters('wpcomment_has_unique_fields', $has_unique, $this);
    }
    
    
    // While rendering fields return attributes for fields
    function get_attributes($field_meta, $type) {
    	
    	$wpcomment_attribtues = array();
    	
    	$wpcomment_attribtues['data-errormsg']  = isset($field_meta['error_message']) ? wpcomment_wpml_translate($field_meta['error_message'], 'PPOM') : null;
    	
    	switch( $type ) {
    	    
    	    case 'text':
    	        
    	        $wpcomment_attribtues['maxlength'] = isset($field_meta['maxlength']) ? $field_meta['maxlength'] : null;
    	        $wpcomment_attribtues['minlength'] = isset($field_meta['minlength']) ? $field_meta['minlength'] : null;
    	        break;
    	        
    	   case 'textarea':
    	        
    	        $wpcomment_attribtues['maxlength'] = isset($field_meta['max_length']) ? $field_meta['max_length'] : null;
    	        break;
    	        
    	        
    	   case 'number':
    	        
    	        $wpcomment_attribtues['min'] = isset($field_meta['min']) ? $field_meta['min'] : null;
    	        $wpcomment_attribtues['max'] = isset($field_meta['max']) ? $field_meta['max'] : null;
    	        $wpcomment_attribtues['step'] = isset($field_meta['step']) ? $field_meta['step'] : null;
    	        break;
    	        
    	}
    	
    	return apply_filters('wpcomment_field_attributes', $wpcomment_attribtues, $field_meta,    $type);
    }
}