<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Text_WPC extends WPComment_Inputs{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		$this -> title 		= __ ( 'Text Input', 'wp-comment-fields' );
		$this -> desc		= __ ( 'regular text input', 'wp-comment-fields' );
		$this -> icon		= __ ( '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>', 'wp-comment-fields' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		$regex_help_url = 'https://github.com/RobinHerbots/Inputmask#any-option-can-also-be-passed-through-the-use-of-a-data-attribute-use-data-inputmask-the-name-of-the-optionvalue';
		
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
				'placeholder' => array (
						'type' => 'text',
						'title' => __ ( 'Placeholder', 'wp-comment-fields' ),
						'desc' => __ ( 'Optionally placeholder.', 'wp-comment-fields' ) 
				),
				'error_message' => array (
						'type' => 'text',
						'title' => __ ( 'Error message', 'wp-comment-fields' ),
						'desc' => __ ( 'Insert the error message for validation.', 'wp-comment-fields' ) 
				),
				'maxlength' => array (
						'type' => 'text',
						'title' => __ ( 'Max. Length', 'wp-comment-fields' ),
						'desc' => __ ( 'Max. characters allowed, leave blank for default', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				
				'minlength' => array (
						'type' => 'text',
						'title' => __ ( 'Min. Length', 'wp-comment-fields' ),
						'desc' => __ ( 'Min. characters allowed, leave blank for default', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'default_value' => array (
						'type' => 'text',
						'title' => __ ( 'Set default value', 'wp-comment-fields' ),
						'desc' => __ ( 'Pre-defined value for text input', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'class' => array (
						'type' => 'text',
						'title' => __ ( 'Class', 'wp-comment-fields' ),
						'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				// 'input_mask' => array (
				// 		'type' => 'text',
				// 		'title' => __ ( 'Input Masking', 'wp-comment-fields' ),
				// 		'desc' => __ ( 'Click options to see all Masking Options', 'wp-comment-fields' ),
				// 		'link' => __ ( '<a href="https://github.com/RobinHerbots/Inputmask" target="_blank">Options</a>', 'wp-comment-fields' ),
				// 		'col_classes' => array('col-md-3','col-sm-12')
				// ),
				'width' => array (
						'type' => 'select',
						'title' => __ ( 'Width', 'wp-comment-fields' ),
						'desc' => __ ( 'Type field width in % e.g: 50%', "wpcomment"),
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
				// 'use_regex' => array (
				// 		'type' => 'checkbox',
				// 		'title' => __ ( 'Use Regex Expresession', 'wp-comment-fields' ),
				// 		'link' => __ ( '<a target="_blank" href="'.esc_url($regex_help_url).'">See More</a>', 'wp-comment-fields' ),
				// 		'col_classes' => array('col-md-3','col-sm-12')
				// ),
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
						'desc' => __ ( 'Tick it to turn conditional logic to work below', 'wp-comment-fields' ),
				),
				'conditions' => array (
						'type' => 'html-conditions',
						'title' => __ ( 'Conditions', 'wp-comment-fields' ),
						'desc' => __ ( 'Tick it to turn conditional logic to work below', 'wp-comment-fields' )
				),
				
		);
		
		$type = 'text';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}