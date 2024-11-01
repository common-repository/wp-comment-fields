<?php
/**
* Checkbox Input Template
* 
* This template can be overridden by copying it to yourtheme/wpcomment/frontend/inputs/checkbox.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new WPComment_InputManager($field_meta, 'checkbox');
	
$options = wpcomment_convert_options_to_key_val($fm->options(), $field_meta);
// wpcomment_pa($options);

$raw_label	= $fm->get_meta_value('raw');

// If options empty
if ( ! $options ) {
	
	echo '<div class="wpcomment-option-notice">';
        echo '<p>'. __( "Please add some options to render this input.", "wpcomment" ) .'</p>';
    echo '</div>';
    
	return '';
}

// Defualt Checked Values
$checked_value = array();
if( is_array($default_value) ){
	$checked_value = array_map(function($v){
								$v = stripcslashes($v);
								$v = trim($v);
								return $v;
								}, $default_value);
}

$check_wrapper_class = apply_filters('wpcomment_checkbox_wrapper_class','form-check-inline');

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
        if( count($checked_value) > 0 && in_array($key, $checked_value) && !empty($key)){
        
            $checked_option = checked( $key, $key, false );
        }

	?>
		<div class="<?php echo esc_attr($check_wrapper_class); ?>">
			<label class="<?php echo esc_attr($fm->checkbox_label_classes()); ?>" for="<?php echo esc_attr($dom_id); ?>">
				
				<input 
					type="checkbox" 
					name="<?php echo esc_attr($fm->form_name()); ?>" 
					id="<?php echo esc_attr($dom_id); ?>" 
					class="<?php echo esc_attr($input_class); ?>" 
					data-optionid="<?php echo esc_attr($option_id); ?>" 
					data-label="<?php echo esc_attr($raw_label); ?>"
					data-title="<?php echo esc_attr($fm->title()); ?>"
					data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
					value="<?php echo esc_attr($key); ?>" 
					<?php echo $checked_option; ?>
				>
				<span class="wpcomment-input-option-label wpcomment-label-checkbox"><?php echo $option_label; ?></span>
			</label>
		</div>

	<?php } ?>
</div>