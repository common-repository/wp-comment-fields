<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Color_WPC extends WPComment_Inputs{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		[];
		
		$this -> title 		= __ ( 'Color picker', 'wp-comment-fields' );
		$this -> desc		= __ ( 'Color pallete input', 'wp-comment-fields' );
		$this -> icon		= __ ( '<i class="fa fa-modx" aria-hidden="true"></i>', 'wp-comment-fields' );
		$this -> settings	= self::get_settings();
	
	}
	
	private function get_settings(){
		
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'wp-comment-fields' ),
					'desc' => __ ( 'It will be shown as field label', 'wp-comment-fields' ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', 'wp-comment-fields' ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'wp-comment-fields' ) 
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', 'wp-comment-fields' ),
					'desc' => __ ( 'Small description, it will be display near name title.', 'wp-comment-fields' ) 
			),
			'error_message' => array (
					'type' => 'text',
					'title' => __ ( 'Error message', 'wp-comment-fields' ),
					'desc' => __ ( 'Insert the error message for validation.', 'wp-comment-fields' ),
			),		
			'default_color' => array (
					'type' => 'text',
					'title' => __ ( 'Default color', 'wp-comment-fields' ),
					'desc' => __ ( 'Define default color e.g: #effeff', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'palettes_colors' => array (
					'type' => 'text',
					'title' => __ ( 'Palettes colors', 'wp-comment-fields' ),
					'desc' => __ ( "Color codes seperated by comma e.g: #125, #459, #78b", 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'palettes_width' => array (
					'type' => 'text',
					'title' => __ ( 'Palettes width', 'wp-comment-fields' ),
					'desc' => __ ( "e.g: 500", 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'palettes_mode' => array (
					'type' => 'select',
					'title' => __ ( 'Palettes mode', 'wp-comment-fields' ),
					'desc' => __ ( "Select Mode", 'wp-comment-fields' ),
					'options'=> array('hsl'=>'Hue, Saturation, Lightness','hsv'=>'Hue, Saturation, Value'),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'width' => array (
					'type' => 'select',
					'title' => __ ( 'Width', 'wp-comment-fields' ),
					'desc' => __ ( 'Select width column.', "wpcomment"),
					'options'	=> wpcomment_get_input_cols(),
					'default'	=> 12,
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'visibility' => array (
					'type' => 'select',
					'title' => __ ( 'Visibility', 'wp-comment-fields' ),
					'desc' => __ ( 'Set field visibility based on user.', "wpcomment"),
					'options'	=> wpcomment_field_visibility_options(),
					'default'	=> 'everyone',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'visibility_role' => array (
					'type' => 'text',
					'title' => __ ( 'User Roles', 'wp-comment-fields' ),
					'desc' => __ ( 'Role separated by comma.', "wpcomment"),
					'hidden' => true,
			),
			'show_palettes' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show palettes', 'wp-comment-fields' ),
					'desc' => __ ( 'Tick if need to show a group of common colors beneath the square', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'show_onload' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show on load', 'wp-comment-fields' ),
					'desc' => __ ( 'Display color picker by default, otherwise show on click', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'desc_tooltip' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show tooltip (PRO)', 'wp-comment-fields' ),
					'desc' => __ ( 'Show Description in Tooltip with Help Icon', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'required' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Required', 'wp-comment-fields' ),
					'desc' => __ ( 'Select this if it must be required.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'logic' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Conditions', 'wp-comment-fields' ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', 'wp-comment-fields' )
			),
			'conditions' => array (
					'type' => 'html-conditions',
					'title' => __ ( 'Conditions', 'wp-comment-fields' ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', 'wp-comment-fields' )
			),
		);
		
		$type = 'color';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}