<?php 
/**
 * PPOM Main HTML Template
 * 
 * Rendering all fields on product page
 * 
 * @version 1.0
 * 
 **/
 
/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

global $post;

// check if duplicate wpcomment fields render
if( ! $form_obj->has_unique_datanames() ) {
	$duplicate_found = apply_filters('wpcomment_duplicate_datanames_text', __('Some of your fields has duplicated datanames, please fix it', 'wp-comment-fields' ) );

	echo '<div class="error">'.esc_html($duplicate_found).'</div>';

	return '';
}

?>

<div class="wpcomment-wrapper">
	
	<!-- Render hidden inputs -->
	<?php $form_obj->form_contents(); ?>

	<div class="<?php echo esc_attr($form_obj->wrapper_inner_classes());?>">
		
		<?php
		/*
		** hook before wpcomment fields 
		*/
		do_action('wpcomment_before_wpcomment_fields', $form_obj);
		?>

		<?php $form_obj->fields_render(); ?>
		
		<?php
		/*
		** hook after wpcomment fields 
		*/
		do_action('wpcomment_after_wpcomment_fields', $form_obj);
		?>

	</div> <!-- end form-row -->
	
	
	<div id="wpcomment-error-container" class="woocommerce-notices-wrapper"></div>
	
	<div style="clear:both"></div>
	
</div>  <!-- end wpcomment-wrapper -->