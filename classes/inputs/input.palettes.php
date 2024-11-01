<?php
/*
 * Followig class handling radio input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Palettes_WPC extends WPComment_Inputs{
	
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
		
		$this -> title 		= __ ( 'Color Palettes', 'wp-comment-fields' );
		$this -> desc		= __ ( 'color boxes', 'wp-comment-fields' );
		$this -> icon		= __ ( '<i class="fa fa-user-plus" aria-hidden="true"></i>', 'wp-comment-fields' );
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
					'desc' => __ ( 'Insert the error message for validation.', 'wp-comment-fields' ) 
			),
			'selected_palette_bcolor' => array (
					'type' => 'color',
					'title' => __ ( 'Selected Border Color', 'wp-comment-fields' ),
					'desc' => __ ( 'Change selected palette border color, e.g: #fff', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'class' => array (
					'type' => 'text',
					'title' => __ ( 'Class', 'wp-comment-fields' ),
					'desc' => __ ( 'Insert an additional class(es) (separateb by comma) for more personalization.', 'wp-comment-fields' ),
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
			'max_selected' => array (
					'type' => 'number',
					'title' => __ ( 'Max selected', 'wp-comment-fields' ),
					'desc' => __ ( 'Max. selected, leave blank for default.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'options' => array (
						'type' => 'paired-palettes',
						'title' => __ ( 'Add colors', 'wp-comment-fields' ),
						'desc' => __ ( 'Type color code with price (optionally). To write label, use #colorcode - White', 'wp-comment-fields' )
			),
			'selected' => array (
					'type' => 'text',
					'title' => __ ( 'Selected color', 'wp-comment-fields' ),
					'desc' => __ ( 'Type color code given in (Add Options) tab if you want already selected.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'color_width' => array (
					'type' => 'text',
					'title' => __ ( 'Color width', 'wp-comment-fields' ),
					'desc' => __ ( 'default is 50, e.g: 75', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'color_height' => array (
					'type' => 'text',
					'title' => __ ( 'Color height', 'wp-comment-fields' ),
					'desc' => __ ( 'default is 50, e.g: 100', 'wp-comment-fields' ),
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
			'multiple_allowed' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Multiple selections?', 'wp-comment-fields' ),
					'desc' => __ ( 'Allow users to select more then one palette?.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'circle' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show as Circle', 'wp-comment-fields' ),
					'desc' => __ ( 'It will display color palettes as circle', 'wp-comment-fields' ),
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
		
		$type = 'palettes';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}