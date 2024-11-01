<?php
/*
 * Followig class handling file input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_File_WPC extends WPComment_Inputs{
	
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
		
		$this -> title 		= __ ( 'File Input', "wpcomment" );
		$this -> desc		= __ ( 'regular file input', "wpcomment" );
		$this -> icon		= __ ( '<i class="fa fa-file" aria-hidden="true"></i>', 'wp-comment-fields' );
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
			'error_message' => array (
					'type' => 'text',
					'title' => __ ( 'Error message', "wpcomment" ),
					'desc' => __ ( 'Insert the error message for validation.', "wpcomment" ) 
			),
			'file_cost' => array (
					'type' => 'text',
					'title' => __ ( 'File cost/price', "wpcomment" ),
					'desc' => __ ( 'This will be added into cart', "wpcomment" ),
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
					'desc' => __ ( 'Select width column.', "wpcomment"),
					'options'	=> wpcomment_get_input_cols(),
					'default'	=> 12,
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'button_label_select' => array (
					'type' => 'text',
					'title' => __ ( 'Button label (select files)', "wpcomment" ),
					'desc' => __ ( 'Type button label e.g: Select Photos', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'button_class' => array (
					'type' => 'text',
					'title' => __ ( 'Button class', "wpcomment" ),
					'desc' => __ ( 'Type class for both (select, upload) buttons', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),			
			'files_allowed' => array (
					'type' => 'text',
					'title' => __ ( 'Files allowed', "wpcomment" ),
					'desc' => __ ( 'Type number of files allowed per upload by user, e.g: 3', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'file_types' => array (
					'type' => 'text',
					'title' => __ ( 'File types', "wpcomment" ),
					'desc' => __ ( 'File types allowed seperated by comma, e.g: jpg,pdf,zip', "wpcomment" ),
					'default' => 'jpg,pdf,zip',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'file_size' => array (
					'type' => 'text',
					'title' => __ ( 'File size', "wpcomment" ),
					'desc' => __ ( 'Type size with units in kb|mb per file uploaded by user, e.g: 3mb', "wpcomment" ),
					'default' => '1mb',
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'min_img_h' => array (
					'type' => 'text',
					'title' => __ ( 'Min Height', "wpcomment" ),
					'desc' => __ ( 'Provide minimum image height.', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'max_img_h' => array (
					'type' => 'text',
					'title' => __ ( 'Max Height', "wpcomment" ),
					'desc' => __ ( 'Provide maximum image height.', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'min_img_w' => array (
					'type' => 'text',
					'title' => __ ( 'Min Width', "wpcomment" ),
					'desc' => __ ( 'Provide minimum image width.', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'max_img_w' => array (
					'type' => 'text',
					'title' => __ ( 'Max Width', "wpcomment" ),
					'desc' => __ ( 'Provide maximum image width.', "wpcomment" ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'img_dimension_error' => array (
					'type' => 'text',
					'title' => __ ( 'Error Message', "wpcomment" ),
					'desc' => __ ( 'Provide image dimension error message. It will display on frontend while uploading the image.', "wpcomment" ),
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
			'onetime' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Fixed Fee', "wpcomment" ),
					'desc' => __ ( 'Add one time fee to cart total.', "wpcomment" ),
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
		
		$type = 'file';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}