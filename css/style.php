<?php
/**
 * Custom CSS Controller
 * 
 * This file include the inline css to the page.
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

$css = '';

if ($type != 'global') {
    
    $fm = new WPComment_InputManager($field_meta, $type);

    switch ($type) {
        case 'image':
            
                $active_imgborderclr = $fm->get_meta_value('selected_img_bordercolor', '#f00');
                $img_width   = $fm->get_meta_value('image_width', '75px');
                $img_height  = $fm->get_meta_value('image_height', 'auto');
    
            	$css .= '.' . $fm->data_name().' .nm-boxes-outer input:checked + img {
            			border: 2px solid '.esc_attr($active_imgborderclr).' !important;
            		}';
            	$css .= '.' . $fm->data_name().' .pre_upload_image img{
            		   height: '.esc_attr($img_height).' !important;
            		   width : '.esc_attr($img_width).' !important;
            		}';
            break;
    }

}else{
    
    if (wpcomment_get_option('wpcomment_input_box_border_shadow_focus') != '') {
        
        $css .='.wpcomment-wrapper input[type="text"]:focus, 
                .wpcomment-wrapper input[type="date"]:focus, 
                .wpcomment-wrapper input[type="url"]:focus, 
                .wpcomment-wrapper input[type="number"]:focus, 
                .wpcomment-wrapper select:focus, 
                .wpcomment-wrapper textarea:focus{
                    box-shadow:'.esc_attr(wpcomment_get_option('wpcomment_input_box_border_shadow_focus')).'!important;
                }';
    }
}

echo $css;
?>