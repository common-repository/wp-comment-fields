<?php
/**
* Select Input Template
* 
* This template can be overridden by copying it to yourtheme/wpcomment/frontend/inputs/select.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new WPComment_InputManager($field_meta, 'select');

$onetime    = $fm->get_meta_value('onetime');
$taxable    = $fm->get_meta_value('onetime_taxable');
$input_attr = $fm->get_meta_value('attributes');

$options = wpcomment_convert_options_to_key_val($fm->options(), $field_meta);
// wpcomment_pa($options);
// If options empty
if ( ! $options ) {
	
	echo '<div class="wpcomment-option-notice">';
        echo '<p>'. __( "Please add some options to render this input.", "wpcomment" ) .'</p>';
    echo '</div>';
    
	return '';
}
			// wpcomment_pa($options);
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<select 
		id="<?php echo esc_attr($fm->data_name()); ?>" 
		name="<?php echo esc_attr($fm->form_name()); ?>" 
		class="<?php echo esc_attr($fm->input_classes()); ?>" 
		data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 

		<?php 
		// Add input extra attributes
		foreach ($input_attr as $key => $val){ echo $key . '="' . $val .'"'; }
		?>
	>
		
		<?php 
		foreach ($options as $key => $value){

			$option_label   = $value['label'];
            $option_id      = isset($value['id']) ? $value['id'] : '';
            
            $wpcomment_has_percent = $opt_percent !== '' ? 'wpcomment-option-has-percent' : '';
            $option_class     = array(
            						"wpcomment-option-{$option_id}"
                                );
                                    
            $option_class = apply_filters('wpcomment_option_classes', implode(" ", $option_class), $field_meta);

            $selected_value = selected( $default_value, $key, false )
		?>
		
			<option
				value="<?php echo esc_attr($key); ?>" 
				class="<?php echo esc_attr($option_class); ?>"  
				data-optionid="<?php echo esc_attr($option_id); ?>" 
				data-label="<?php echo esc_attr($raw_label); ?>" 
				data-title="<?php echo esc_attr($fm->title()); ?>" 
				data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
				<?php echo $selected_value; ?>
			><?php echo esc_html($option_label); ?></option>

		<?php 
		} 
		?>
		
	</select>

</div>