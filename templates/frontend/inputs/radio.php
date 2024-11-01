<?php
/**
* Radio Input Template
* 
* This template can be overridden by copying it to yourtheme/wpcomment/frontend/inputs/radio.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new WPComment_InputManager($field_meta, 'radio');
	
$options = wpcomment_convert_options_to_key_val($fm->options(), $field_meta);

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');

// If options empty
if ( ! $options ) {
	
	echo '<div class="wpcomment-option-notice">';
        echo '<p>'. __( "Please add some options to render this input.", "wpcomment" ) .'</p>';
    echo '</div>';
    
	return '';
}

$radio_wrapper_class = apply_filters('wpcomment_radio_wrapper_class','form-check-inline');

?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>


	<?php 
	foreach ($options as $key => $value){ 

		$option_label = $value['label'];
    	$option_id    = $value['option_id'];
        $dom_id       = apply_filters('wpcomment_dom_option_id', $option_id, $field_meta);
                $option_class     = array(
        						"wpcomment-option-{$option_id}",
                            );
                                
        $option_class	= apply_filters('wpcomment_option_classes', implode(" ", $option_class), $field_meta);
        $input_class	= $fm->input_classes()." ".$option_class;

        $checked_option = '';
        if( ! empty($default_value) ){
        
            $default_value = stripcslashes($default_value);
            $checked_option = checked( $default_value, $key, false );
        }

	?>
		<div class="<?php echo esc_attr($radio_wrapper_class); ?>">
			<label class="<?php echo esc_attr($fm->radio_label_classes()); ?>" for="<?php echo esc_attr($dom_id); ?>">
				
				<input 
					type="radio" 
					id="<?php echo esc_attr($dom_id); ?>" 
					name="<?php echo esc_attr($fm->form_name()); ?>" 
					class="<?php echo esc_attr($input_class); ?>" 
					value="<?php echo esc_attr($key); ?>" 
					data-optionid="<?php echo esc_attr($option_id); ?>" 
					data-label="<?php echo esc_attr($raw_label); ?>" 
					data-title="<?php echo esc_attr($fm->title()); ?>" 
					data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
					<?php echo $checked_option; ?>
				>
				<span class="wpcomment-input-option-label wpcomment-label-radio"><?php echo $option_label; ?></span>
			</label>
		</div>

	<?php } ?>
</div>