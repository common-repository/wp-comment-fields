<?php
/**
* Text Input Template
* 
* This template can be overridden by copying it to yourtheme/wpcomment/frontend/inputs/text.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new WPComment_InputManager($field_meta, 'divider');

$divider_styles  = $fm->get_meta_value('divider_styles');
$style1_border   = $fm->get_meta_value('style1_border');
$divider_height  = $fm->get_meta_value('divider_height');
$divider_color   = $fm->get_meta_value('divider_color', '#808080');
$txtsize         = $fm->get_meta_value('divider_txtsize', '14px');
$txtclr          = $fm->get_meta_value('divider_txtclr','#2f2121');

$custom_css  = '';
$custom_css .= '.'.$fm->data_name().' .wpcomment-divider-txt {
                    font-size: '.$txtsize.' !important;
                    color: '.$txtclr.' !important;
                }';

if ($divider_styles == 'style1') {
    $custom_css .= '.'.$fm->data_name().' .wpcomment-divider-'.$style1_border.' {
                        border-top: '.$divider_height.' '.$style1_border.' '.$divider_color.' !important;
                        background-color: transparent !important;
                    }';
                    
                    
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-line-clr:before,'.
                    '.'.$fm->data_name().' .wpcomment-divider-line-clr:after {
                        border-top: '.$divider_height.' '.$style1_border.' '.$divider_color.' !important;
                    }';
}

if ($divider_styles == 'style2') {
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-gradient {
                        background: '.$divider_color.';
                        height: '.$divider_height.';
                        line-height: '.$divider_height.' !important;
                    }';
                    
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-gradient:before {
                        background: linear-gradient(to right, white, '.$divider_color.');
                    }';
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-gradient:after {
                        background: linear-gradient(to left, white, '.$divider_color.');
                    }';
}

if ($divider_styles == 'style3') {
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-donotcross {
                        background: '.$divider_color.';
                        height: '.$divider_height.';
                        line-height: '.$divider_height.' !important;
                    }';
}

if ($divider_styles == 'style4') {
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-easy-shadow span:first-child {
                        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(transparent), to('.$divider_color.'));
                    	background-image: -webkit-linear-gradient(180deg, transparent, '.$divider_color.');
                    	background-image: -moz-linear-gradient(180deg, transparent, '.$divider_color.');
                    	background-image: -o-linear-gradient(180deg, transparent, '.$divider_color.');
                    	background-image: linear-gradient(90deg, transparent, '.$divider_color.');
                    }';
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-easy-shadow span:last-child {
                        background-image: -webkit-gradient(linear, 0 0, 0 100%, from('.$divider_color.'), to(transparent));
                        background-image: -webkit-linear-gradient(180deg, '.$divider_color.', transparent);
                    	background-image: -moz-linear-gradient(180deg, '.$divider_color.', transparent);
                    	background-image: -o-linear-gradient(180deg, '.$divider_color.', transparent);
                    	background-image: linear-gradient(90deg, '.$divider_color.', transparent);
                    }';
}
                
if ($divider_styles == 'style5') {
    $custom_css .=  '.'.$fm->data_name().' .wpcomment-divider-fancy-line span {
                        background-color: '.$divider_color.';
                        height: '.$divider_height.';
                        line-height: '.$divider_height.' !important;
                    }';
}

echo '<style>';
    echo esc_attr($custom_css);
echo '</style>';

?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >
	
    <!--Style 1-->
    <?php if($divider_styles == 'style1'){
        if ($fm->field_label()) { ?>
            <h2 class="wpcomment-divider-with-txt wpcomment-divider-line wpcomment-divider-line-clr wpcomment-divider-txt"><?php echo sprintf(__("%s", "wpcomment"), $fm->field_label() ); ?></h2>
    <?php }else{ ?>
            <hr class="wpcomment-divider-<?php echo esc_attr($style1_border); ?>">
    <?php  } } ?>
    
    <!--Style 2-->
    <?php if($divider_styles == 'style2'){ ?>
        <h2 class="wpcomment-divider-with-txt wpcomment-divider-gradient wpcomment-divider-txt"><?php echo sprintf(__("%s", "wpcomment"), $fm->field_label() ); ?></h2>
    <?php } ?>
    
    <!--Style 3-->
    <?php if($divider_styles == 'style3'){ ?>
        <h2 class="wpcomment-divider-with-txt wpcomment-divider-donotcross wpcomment-divider-txt"><?php echo sprintf(__("%s", "wpcomment"), $fm->field_label() ); ?></h2>
    <?php } ?>
    
    <!--Style 4-->
    <?php if($divider_styles == 'style4'){ ?>
        <div class="wpcomment-divider-easy-shadow">
            <span></span>
            <span class="wpcomment-divider-txt"><?php echo sprintf(__("%s", "wpcomment"), $fm->field_label() ); ?></span>
            <span></span>
        </div>
    <?php } ?>
    
    <!--Style 5-->
    <?php if($divider_styles == 'style5'){ ?>
        
        <h1 class="wpcomment-divider-fancy-heading wpcomment-divider-txt"><?php echo sprintf(__("%s", "wpcomment"), $fm->field_label() ); ?></h1>
		<div class="wpcomment-divider-fancy-line"> 
		    <span></span>
		</div>
    <?php } ?>
</div>