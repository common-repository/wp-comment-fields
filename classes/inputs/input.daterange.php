<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Daterange_WPC extends WPComment_Inputs{
	
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
		
		$this -> title 		= __ ( 'DateRange Input', 'wp-comment-fields' );
		$this -> desc		= __ ( '<a href="http://www.daterangepicker.com/" target="_blank">More detail</a>', 'wp-comment-fields' );
		$this -> icon		= __ ( '<i class="fa fa-table" aria-hidden="true"></i>', 'wp-comment-fields' );
		$this -> settings	= self::get_settings();
		
	}
	
	private function get_settings(){
		
		$input_meta = array (
			'title' => array (
					'type' => 'text',
					'title' => __ ( 'Title', 'wp-comment-fields' ),
					'desc' => __ ( 'All about Daterangepicker, see daterangepicker', 'wp-comment-fields' ), 
					'link' => __ ( '<a href="http://www.daterangepicker.com/" target="_blank">Daterangepicker</a>', 'wp-comment-fields' ) 
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
			'open_style' => array (
					'type' => 'select',
					'title' => __ ( 'Open Style', 'wp-comment-fields' ),
					'desc' => __ ( 'Default is down.', 'wp-comment-fields' ),
					'options' => array('down'=>'Down', 'up'=>'Up'),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'date_formats' => array (
					'type' => 'text',
					'title' => __ ( 'Format', 'wp-comment-fields' ),
					'desc' => __ ( 'e.g MM-DD-YYYY, DD-MMM-YYYY', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'tp_increment' => array (
					'type' => 'text',
					'title' => __ ( 'Timepicker increment', 'wp-comment-fields' ),
					'desc' => __ ( 'e.g: 30', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'start_date' => array (
					'type' => 'text',
					'title' => __ ( 'Start Date', 'wp-comment-fields' ),
					'desc' => __ ( 'Must be same format as defined in above (Format) field.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'end_date' => array (
					'type' => 'text',
					'title' => __ ( 'End Date', 'wp-comment-fields' ),
					'desc' => __ ( 'Must be same format as defined in above (Format) field.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'min_date' => array (
					'type' => 'text',
					'title' => __ ( 'Min Date', 'wp-comment-fields' ),
					'desc' => __ ( 'e.g: 2017-02-25', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'max_date' => array (
					'type' => 'text',
					'title' => __ ( 'Max Date', 'wp-comment-fields' ),
					'desc' => __ ( 'e.g: 2017-09-15', 'wp-comment-fields' ),
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
			'time_picker' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Timepicker', 'wp-comment-fields' ),
					'desc' => __ ( 'Show Timepicker.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'tp_24hours' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Timepicker 24 Hours', 'wp-comment-fields' ),
					'desc' => __ ( 'Left blank for default', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'tp_seconds' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Timepicker Seconds', 'wp-comment-fields' ),
					'desc' => __ ( 'Left blank for default', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'drop_down' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Dropdown', 'wp-comment-fields' ),
					'desc' => __ ( 'Left blank for default', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'show_weeks' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Show Week Numbers', 'wp-comment-fields' ),
					'desc' => __ ( 'Left blank for default.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'auto_apply' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Auto Apply Changes', 'wp-comment-fields' ),
					'desc' => __ ( 'Hide the Apply/Cancel button.', 'wp-comment-fields' ),
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
		
		$type = 'daterange';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}