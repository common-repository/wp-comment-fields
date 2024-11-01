<?php
/*
 * Followig class handling text input control and their
* dependencies. Do not make changes in code
* Create on: 9 November, 2013
*/

class NM_Divider_WPC extends WPComment_Inputs{
	
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
		
		$this -> title 		= __ ( 'Divider', 'wp-comment-fields' );
		$this -> desc		= __ ( 'regular didider input', 'wp-comment-fields' );
		$this -> icon		= __ ( '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>', 'wp-comment-fields' );
		$this -> settings	= self::get_settings();
		
	}
	
	function wpcomment_divider_style(){

        return array(
        	'style1' => __( 'Style 1', 'wp-comment-fields' ), 
        	'style2' => __( 'Style 2', 'wp-comment-fields' ),			
        	'style3' => __( 'Style 3', 'wp-comment-fields' ), 
            'style4' => __( 'Style 4', 'wp-comment-fields' ),
            'style5' => __( 'Style 5', 'wp-comment-fields' ),
       );
    }
 
     function border_style(){ 
     	
    	return array(
			'solid'  => __( 'Solid', 'wp-comment-fields' ),
			'dotted' => __( 'Dotted', 'wp-comment-fields' ),
			'dashed' => __( 'Dashed', 'wp-comment-fields' ),
			'double' => __( 'Double', 'wp-comment-fields' ),
			'groove' => __( 'Groove', 'wp-comment-fields' ),
			'ridge'  => __( 'Ridge', 'wp-comment-fields' ),
			'inset'  => __( 'Inset', 'wp-comment-fields' ),
			'outset' => __( 'Outset', 'wp-comment-fields' ),
		);
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
				'divider_styles' => array (
						'type' => 'select',
						'title' => __ ( 'Select style', 'wp-comment-fields' ),
						'desc' => __ ( 'Select style you want to render', "wpcomment"),
						'options'	=> $this->wpcomment_divider_style(),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'style1_border' => array (
						'type' => 'select',
						'title' => __ ( 'Style border', 'wp-comment-fields' ),
						'desc' => __ ( 'It will only apply on style 1.', "wpcomment"),
						'options'	=> $this-> border_style(),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_height' => array (
						'type'  => 'text',
						'title' => __ ( 'Divider height', 'wp-comment-fields' ),
						'desc'  => __ ( 'Provide the divider height e.g: 3px.', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_txtsize' => array (
						'type' => 'text',
						'title' => __ ( 'Font size', 'wp-comment-fields' ),
						'desc' => __ ( 'Provide divider text font size e.g: 18px', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_color' => array (
						'type' => 'color',
						'title' => __ ( 'Divider color', 'wp-comment-fields' ),
						'desc' => __ ( 'Choose the divider color.', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				'divider_txtclr' => array (
						'type' => 'color',
						'title' => __ ( 'Divider text color', 'wp-comment-fields' ),
						'desc' => __ ( 'Choose the divider text color.', 'wp-comment-fields' ),
						'col_classes' => array('col-md-3','col-sm-12')
				),
				
		);
		
		$type = 'divider';
		return apply_filters("poom_{$type}_input_setting", $input_meta, $this);
	}
}