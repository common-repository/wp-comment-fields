<?php
/**
 * PPOM Inputs Meta Manager Class
 *
 * It control the inputs meta data. It show all the global inputs settings.
 *
 * @version  1.0
 */

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

class WPComment_InputManager {
    
    /**
	 * Return all wpcomment inputs meta data
	 *
	 * @var array
	 */
    public static $input_meta;
    
    /**
	 * Return input type
	 *
	 * @var string
	 */
    public $input_type;
    
    /* ======= Class Construct ======== */
    function __construct( $input_meta, $input_type ) {
        
        self::$input_meta  = $input_meta;
        
        $this->input_type = $input_type;
    }


    /**
	 * Field Title
	 *
	 * @hook wpcomment_{$field_type}_input_meta_title
	 */
    function title() {

        $title = isset( self::$input_meta['title'] ) ?  stripslashes( self::$input_meta['title'] ): '';
        
        $title = wpcomment_wpml_translate($title, 'wp-comment-fields');
        
        return apply_filters("wpcomment_input_meta_title", $title, self::$input_meta);
    }


    /**
	 * Field Desciption
	 *
	 * @hook wpcomment_{$field_type}_input_meta_desc
	 * @hook wpcomment_description_content
	 */
    function desc() {

        $desc = isset( self::$input_meta['description'] ) ?  stripslashes( self::$input_meta['description'] ): '';
        
        $desc = wpcomment_wpml_translate($desc, 'wp-comment-fields');
        
        // old Filter
        $desc = apply_filters( 'wpcomment_description_content', $desc, self::$input_meta );

        return apply_filters("wpcomment_input_meta_desc", $desc, self::$input_meta);
    }

    
    /**
	 * Field Required
	 *
	 * @hook wpcomment_{$field_type}_input_meta_required
	 */
    function required() {

        $required = isset( self::$input_meta['required'] ) ?  self::$input_meta['required']: '';
        return apply_filters("wpcomment_input_meta_required", $required, self::$input_meta);
    }
    
    
    /**
	 * Field Desc Tooltip
	 */
    function enable_tooltip() {

        $required = isset( self::$input_meta['desc_tooltip'] ) ?  self::$input_meta['desc_tooltip']: '';
        return $required;
    }


    /**
	 * Field dataname (Field Unique ID)
	 *
	 * @hook wpcomment_{$field_type}_input_meta_data_name
	 */
    function data_name() {

        $data_name = isset( self::$input_meta['data_name'] ) ?  sanitize_key( self::$input_meta['data_name'] ): $this->title();
        
        return apply_filters("wpcomment_input_meta_data_name", $data_name, self::$input_meta);
    }


    /**
	 * Field Placeholder
	 *
	 * @hook wpcomment_{$field_type}_input_meta_placeholder
	 */
    function placeholder() {

        $placeholder = isset( self::$input_meta['placeholder'] ) ?  stripslashes( self::$input_meta['placeholder'] ): '';
        
        $placeholder = wpcomment_wpml_translate($placeholder, 'wp-comment-fields');
        
        return apply_filters("wpcomment_input_meta_placeholder", $placeholder, self::$input_meta);
    }


    /**
	 * Field Error Message
	 *
	 * @hook wpcomment_{$field_type}_input_meta_error_msg
	 */
    function error_msg() {

        $error_msg = isset( self::$input_meta['error_message'] ) ?  self::$input_meta['error_message']: '';
        
        $error_msg = wpcomment_wpml_translate($error_msg, 'wp-comment-fields');
        
        return apply_filters("wpcomment_input_meta_error_msg", $error_msg, self::$input_meta);
    }


    /**
	 * Field Label
	 * 
	 * Show Asterisk If Require On
	 * 
	 * Show Description If Not Null
	 *
	 * @hook wpcomment_field_description
	 * @hook wpcomment_{$field_type}_input_meta_label_html
	 */
    function field_label($tooltip=true, $desc=true,  $asterisk=true){
        
        $asterisk_symbol    = ( !empty($this->required()) && $this->title() != '' ) ? '<span class="show_required"> *</span>' : '';

        $show_desc   = ( !empty( $this->desc() ) ) ? '<span class="show_description wpcomment-input-desc">'. $this->desc() .'</span>' : '';
        
        if ($desc) {
            $show_desc   = apply_filters('wpcomment_field_description', $show_desc, self::$input_meta);
        }

        $field_label = $this->title();
        // $field_label = $this->title() . $asterisk_symbol . $show_desc;
        
        if ($asterisk) {
            $field_label = $field_label . $asterisk_symbol;
        }
        
        if ($tooltip) {
            $field_label = $field_label . $show_desc;
        }
        
        return apply_filters("wpcomment_input_meta_label_html", $field_label, self::$input_meta);
    }
    
    
    /**
	 * Field Desciption With Tooltip
	 * 
	 * @hook wpcomment_input_meta_tooltip_desc
	 */
    function tooltip(){
        
        $show_desc   = ( !empty( $this->desc() ) ) ? '<span class="show_description wpcomment-input-desc">'. $this->desc() .'</span>' : '';
        $show_desc   = apply_filters('wpcomment_field_description', $show_desc, self::$input_meta);
        
        return apply_filters("wpcomment_input_meta_tooltip_desc", $show_desc, self::$input_meta);
    }
    
    
    /**
	 * Field Multiple Options
	 * 
	 * Checkbox|Radio|Select|Image|Pallete
	 *
	 * @hook wpcomment_{$field_type}_input_meta_multi_options
	 */
    function options() {

        $options = isset( self::$input_meta['options'] ) ?  self::$input_meta['options']: array();

        if(is_array($options)){
            $options = array_map("wpcomment_translation_options", $options);
        }

        return apply_filters("wpcomment_input_meta_multi_options", $options, self::$input_meta);
    }
    

    /**
	 * Images Options
	 * 
	 * @hook wpcomment_{$field_type}_input_meta_images
	 */
    function images() {

        $images = isset( self::$input_meta['images'] ) ?  self::$input_meta['images']: array();
        
        return apply_filters("wpcomment_input_meta_images", $images, self::$input_meta);
    }
    
    
    /**
	 * Audio/Video Options
	 * 
	 * @hook wpcomment_{$field_type}_input_meta_audio
	 */
    function audio_video() {

        $audios = isset( self::$input_meta['audio'] ) ?  self::$input_meta['audio']: array();
        
        return apply_filters("wpcomment_input_meta_audio", $audios, self::$input_meta);
    }


    /*===================================
        Wrapper Classes Section
    ===================================*/


    /**
	 * Field inner Wrapper Classes
	 * 
	 * @hook wpcomment_input_wrapper_class
	 */
    function field_inner_wrapper_classes() {

        $classes = ['form-group'];
        $wrapper_classes = implode(' ',$classes);
        // return apply_filters_deprecated( 'wpcomment_input_wrapper_class', array( $wrapper_classes, self::$input_meta ), '21.3', 'wpcomment_input_wrapper_classes' );
        return apply_filters('wpcomment_input_wrapper_class', $wrapper_classes, self::$input_meta);
    }


    /**
	 * Field Label Classes
	 * 
	 * @hook wpcomment_{$this->input_type}_input_label_classes
	 */
    function label_classes() {

        $classes = ['form-control-label'];
        
        $label_classes =  apply_filters("wpcomment_input_label_classes", $classes, self::$input_meta);
        
        $label_classes = implode(' ',$label_classes);

        return $label_classes;
    }
    
    
    /**
	 * Field Classes Array
	 * 
	 * @hook wpcomment_{$this->input_type}_input_meta_classes
	 * @hook wpcomment_input_classes
	 */
    function input_classes_array() {

        $classes   = isset( self::$input_meta['class'] ) ? explode(',',self::$input_meta['class']): array();

        if( !empty( $classes ) ) {
            $classes[] = 'form-control';
        } else {
            $classes = array('form-control');
        }
        
        if ($this->input_type == 'color') {
            $classes[] = 'text';
        }else{
            $classes[] = $this->input_type;
        }
        
        $classes[] = 'wpcomment-input';
        
        if($this->required()){
            $classes[] = "wpcomment-required";
        }
        
        if (($this->input_type == 'radio' && ($key = array_search('form-control', $classes)) !== false) || 
            ($this->input_type == 'checkbox' && ($key = array_search('form-control', $classes)) !== false ) ||
            ($this->input_type == 'image' && ($key = array_search('form-control', $classes)) !== false)) {
			unset($classes[$key]);
            $classes[] = 'wpcomment-check-input';
		}
		
		if ($this->input_type == 'select' && ($key = array_search('form-control', $classes)) !== false){
		    unset($classes[$key]);
		    $classes[] = 'form-control';
		    $classes[] = 'form-select';
		}
        
        $classes = apply_filters("wpcomment_input_meta_classes", $classes, self::$input_meta);

        return $classes;
    }


    /**
	 * Field Classes
	 * 
	 * @hook wpcomment_{$this->input_type}_input_meta_classes
	 * @hook wpcomment_input_classes
	 */
    function input_classes() {
		
		$classes = $this->input_classes_array();
        
        // $input_classes = apply_filters_deprecated( 'wpcomment_input_classes', array( $classes, self::$input_meta ), '21.3', 'wpcomment_form_input_classes' );
        $input_classes = apply_filters('wpcomment_input_classes', $classes, self::$input_meta);

        $input_classes = implode(' ',$input_classes);
        
        return $input_classes;
    }
    
    
    /**
	 * Radio Input label classes
	 * 
	 * @hook wpcomment_radio_input_label_classes
	 */
    function radio_label_classes() {

        $classes = ['form-check-label'];
        
        $label_class =  apply_filters('wpcomment_radio_input_label_classes', $classes, self::$input_meta);
        
        $label_class = implode(' ',$label_class);

        return $label_class;
    }
    
    
    /**
	 * Checkbox Input label classes
	 * 
	 * @hook wpcomment_checkbox_input_label_classes
	 */
    function checkbox_label_classes() {

        $classes = ['form-check-label'];
        
        $label_class =  apply_filters('wpcomment_checkbox_input_label_classes', $classes, self::$input_meta);
        
        $label_class = implode(' ',$label_class);

        return $label_class;
    }


    /**
	 * Generate Field Attribute Name Key
	 * 
	 * @hook wpcomment_{$this->input_type}_input_name_attr
	 */
    function form_name() {

        $form_name = "wpcomment[fields][".esc_attr($this->data_name())."]";
        if( $this->input_type == 'checkbox' ) {
            $form_name .= '[]';
        }
       
        return apply_filters("wpcomment_input_name_attr", $form_name, self::$input_meta);
    }

    
    /**
	 * Get input meta value by key
	 * 
	 * @hook wpcomment_{$this->input_type}_input_meta_value_by_key
	 */
    function get_meta_value($key, $default=null) {
        
        $value = ! is_null($default) ? $default : '';
        if( isset(self::$input_meta[$key]) && self::$input_meta[$key] !='' ) {
            $value = self::$input_meta[$key];
        }
        
        $value = apply_filters("wpcomment_{$this->input_type}_input_meta_value_by_key", $value, $key, self::$input_meta);
        
        return apply_filters('wpcomment_field_meta_value', $value, $key, self::$input_meta);
    }
}