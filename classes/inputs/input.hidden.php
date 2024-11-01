<?php
/*
 * Followig class handling hidden input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Hidden_WPC extends WPComment_Inputs{
	
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
		
		$this -> title 		= __ ( 'Hidden Input', "wpcomment" );
		$this -> desc		= __ ( 'regular hidden input', "wpcomment" );
		$this -> icon		= __ ( '<i class="fa fa-hashtag" aria-hidden="true"></i>', 'wp-comment-fields' );
		$this -> settings	= self::get_settings();
		
	}
	
	
	
	
	private function get_settings(){
		
		$input_meta = array (

			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'wp-comment-fields' ),
					'desc' => __ ( 'Label will show in cart', 'wp-comment-fields' ) 
			),
			'data_name' => array (
					'type' => 'text',
					'title' => __ ( 'Data name', "wpcomment" ),
					'desc' => __ ( 'REQUIRED: The identification name of this field, that you can insert into body email configuration. Note:Use only lowercase characters and underscores.', "wpcomment" )
			),
			'field_value' => array (
					'type' => 'text',
					'title' => __ ( 'Field value', "wpcomment" ),
					'desc' => __ ( 'you can pre-set the value of this hidden input.', "wpcomment" )
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
			'cart_display' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show in Cart', 'wp-comment-fields' ),
					'desc' => __ ( 'Display Field Value in Cart', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
		);
		
		$type = 'hidden';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}