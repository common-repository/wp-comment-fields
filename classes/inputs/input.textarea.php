<?php
/*
 * Followig class handling textarea input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Textarea_WPC extends WPComment_Inputs{
	
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
		
		$this -> title 		= __ ( 'Textarea Input', "wpcomment" );
		$this -> desc		= __ ( 'regular textarea input', "wpcomment" );
		$this -> icon		= __ ( '<i class="fa fa-file-text-o" aria-hidden="true"></i>', 'wp-comment-fields' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	private function get_settings(){
		
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', "wpcomment" ),
					'desc' => __ ( 'It will be shown as field label', "wpcomment" ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', "wpcomment" ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', "wpcomment" ) 
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', "wpcomment" ),
					'desc' => __ ( 'Small description, it will be display near name title.', "wpcomment" ) 
			),
			'placeholder' => array (
						'type' => 'text',
						'title' => __ ( 'Placeholder', 'wp-comment-fields' ),
						'desc' => __ ( 'Optional.', 'wp-comment-fields' ) 
			),
			'error_message' => array (
					'type' => 'text',
					'title' => __ ( 'Error message', "wpcomment" ),
					'desc' => __ ( 'Insert the error message for validation.', "wpcomment" ) 
			),
			'default_value' => array (
					'type' => 'text',
					'title' => __ ( 'Post ID', "wpcomment" ),
					'desc' => __ ( 'It will pull content from post. e.g: 22', "wpcomment" )
			),
			'max_length' => array (
					'type' => 'text',
					'title' => __ ( 'Max. Length', "wpcomment" ),
					'desc' => __ ( 'Max. characters allowed, leave blank for default', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'class' => array (
					'type' => 'text',
					'title' => __ ( 'Class', "wpcomment" ),
					'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'width' => array (
					'type' => 'select',
					'title' => __ ( 'Width', 'wp-comment-fields' ),
					'desc' => __ ( 'Select width column', "wpcomment"),
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
			),
			'visibility_role' => array (
					'type' => 'text',
					'title' => __ ( 'User Roles', 'wp-comment-fields' ),
					'desc' => __ ( 'Role separated by comma.', "wpcomment"),
					'hidden' => true,
			),
			'rich_editor' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Rich Editor', "wpcomment" ),
					'desc' => __ ( 'Enable WordPress rich editor.', "wpcomment" ),
					'link' => __ ( '<a target="_blank" href="https://codex.wordpress.org/Function_Reference/wp_editor">Editor</a>', 'wp-comment-fields' ),
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
					'title' => __ ( 'Required', "wpcomment" ),
					'desc' => __ ( 'Select this if it must be required.', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'logic' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Enable Conditions', "wpcomment" ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', "wpcomment" )
			),
			'conditions' => array (
					'type' => 'html-conditions',
					'title' => __ ( 'Conditions', "wpcomment" ),
					'desc' => __ ( 'Tick it to turn conditional logic to work below', "wpcomment" )
			),
		);
		
		$type = 'textarea';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}