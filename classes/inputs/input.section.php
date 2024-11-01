<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Section_WPC extends WPComment_Inputs{
	
	/*
	 * input control settings
	 */
	var $title, $desc, $settings;
	
	
	/*
	 * check if section is started
	 */
	var $is_section_stared;
	/*
	 * this var is pouplated with current plugin meta
	*/
	var $plugin_meta;
	
	function __construct(){
		
		[];
		
		$this -> title 		= __ ( 'HTML', 'wp-comment-fields' );
		$this -> desc		= __ ( 'HTML content', 'wp-comment-fields' );
		$this -> icon		= __ ( '<i class="fa fa-code" aria-hidden="true"></i>', 'wp-comment-fields' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	private function get_settings(){
		
		$input_meta = array (
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', 'wp-comment-fields' ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', 'wp-comment-fields' ) 
			),
			'width' => array (
					'type' => 'select',
					'title' => __ ( 'Width', 'wp-comment-fields' ),
					'desc' => __ ( 'Select width column.', "wpcomment"),
					'options'	=> wpcomment_get_input_cols(),
					'default'	=> 12,
			),
			'description' => array (
					'type' => 'textarea',
					'title' => __ ( 'Description', 'wp-comment-fields' ),
					'desc' => __ ( 'Small description, it will be display near name title.', 'wp-comment-fields' ) 
			),
			'html' => array (
					'type' => 'textarea',
					'title' => __ ( 'Content', 'wp-comment-fields' ),
					'desc' => __ ( 'Add your text/HTML here.', 'wp-comment-fields' )
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
			'desc_tooltip' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show tooltip (PRO)', 'wp-comment-fields' ),
					'desc' => __ ( 'Show Description in Tooltip with Help Icon', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'cart_display' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show in Cart', 'wp-comment-fields' ),
					'desc' => __ ( 'Display Field Value in Cart', 'wp-comment-fields' ),
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
		
		$type = 'section';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}