<?php
/**
* Section|HTML Input Template
* 
* This template can be overridden by copying it to yourtheme/wpcomment/frontend/inputs/section.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new WPComment_InputManager($field_meta, 'section');

$content  = $fm->get_meta_value('html');
$content = wpcomment_wpml_translate($content, 'PPOM');

$field_html = '';
if( $fm->field_label() ) {
    $field_html = $content . $fm->field_label();
}else{
	$field_html = $content;
}

$html_content = apply_filters('the_content', $field_html);
$html_content = apply_filters( 'wpcomment_section_content', $html_content );
?>

<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >
	
	<?php echo stripslashes( $html_content ); ?>

	<div style="clear: both"></div>

	<input 
		type="hidden" 
		name="<?php echo esc_attr($fm->form_name()); ?>" 
		id="<?php echo esc_attr($fm->data_name()); ?>" 
		value="<?php echo esc_attr($field_html); ?>" 
	>
</div>