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

$fm = new WPComment_InputManager($field_meta, 'text');

$use_regex  = $fm->get_meta_value('use_regex');
$input_mask = $fm->get_meta_value('input_mask');
$input_attr = $fm->get_meta_value('attributes');
// wpcomment_pa($input_attr);

// Regex-Input Mask (Since 18.0)
$inputmask = '';
if( $use_regex == 'on' && $input_mask != '' ) {
    $inputmask = 'data-inputmask-regex="'.esc_attr($input_mask).'" ';
}

$input_classes = $fm->input_classes();

// Strip Tags
$default_value = strip_tags($default_value);

// wpcomment_pa($input_classes);
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- If title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo sprintf(__("%s", "wpcomment"), $fm->field_label() ); ?></label>
	<?php endif ?>

	<input 
		type="text" 
		name="<?php echo esc_attr($fm->form_name()); ?>" 
		id="<?php echo esc_attr($fm->data_name()); ?>" 
		class="<?php echo esc_attr($input_classes); ?>" 
		placeholder="<?php echo esc_attr($fm->placeholder()); ?>" 
		autocomplete="off" 
		data-type="text" 
		data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
		data-title="<?php echo esc_attr($fm->title()); ?>" 
		value="<?php echo esc_attr($default_value); ?>" 
		<?php echo $inputmask; ?> 

		<?php 
		// Add input extra attributes
		foreach ($input_attr as $key => $val){ echo $key . '="' . esc_attr($val) .'"'; }
		?>
	>
</div>