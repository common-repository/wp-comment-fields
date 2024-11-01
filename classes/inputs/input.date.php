<?php
/*
 * Followig class handling date input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Date_WPC extends WPComment_Inputs{
	
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
		
		$this -> title 		= __ ( 'Date Input', 'wp-comment-fields' );
		$this -> desc		= __ ( 'regular date input', 'wp-comment-fields' );
		$this -> icon		= __ ( '<i class="fa fa-calendar" aria-hidden="true"></i>', 'wp-comment-fields' );
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
			'placeholder' => array (
					'type' => 'text',
					'title' => __ ( 'Placeholder', 'wp-comment-fields' ),
					'desc' => __ ( 'Optional.', 'wp-comment-fields' ) 
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
			'default_value' => array (
					'type' => 'text',
					'title' => __ ( 'Default Date', 'wp-comment-fields' ),
					'desc' => __ ( 'User format YYYY-MM-DD e.g: 2017-05-25.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'date_formats' => array (
					'type' => 'select',
					'title' => __ ( 'Date formats', 'wp-comment-fields' ),
					'desc' => __ ( 'Select date format. (if jQuery enabled below)', 'wp-comment-fields' ),
					'options' => wpcomment_get_date_formats(),
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
			'max_date' => array (
					'type' => 'text',
					'title' => __ ( 'Max Date', 'wp-comment-fields' ),
					'desc' => __ ( 'Max. date, enter a date or use shortcode (example: +10d)', 'wp-comment-fields' ),
					'link' => __ ( '<a target="_blank" href="http://api.jqueryui.com/datepicker/#option-yearRange">Example</a>', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'year_range' => array (
					'type' => 'text',
					'title' => __ ( 'Year Range', 'wp-comment-fields' ),
					'desc' => __ ( 'e.g: 1950:2016. (if jQuery enabled below) Set start/end year like used example.', 'wp-comment-fields' ),
					'link' => __ ( '<a target="_blank" href="http://api.jqueryui.com/datepicker/#option-yearRange">Example</a>', 'wp-comment-fields' ),
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
			'jquery_dp' => array (
					'type' => 'checkbox',
					'title' => __ ( 'jQuery Datepicker', 'wp-comment-fields' ),
					'desc' => __ ( 'It will load jQuery fancy datepicker.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'no_weekends' => array (
					'type' => 'checkbox',
					'title' => __ ( 'Disable Weekends', 'wp-comment-fields' ),
					'desc' => __ ( 'It will disable Weekends.', 'wp-comment-fields' ),
					'col_classes' => array('col-md-3','col-sm-12')
			),
			'past_dates' => array (
			    'type' => 'checkbox',
			    'title' => __ ( 'Disable Past Dates', 'wp-comment-fields' ),
			    'desc' => __ ( 'Disable dates.', 'wp-comment-fields' ),
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
		
		$type = 'date';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}