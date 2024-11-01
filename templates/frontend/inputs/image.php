<?php
/**
* Image Input Template
* 
* This template can be overridden by copying it to yourtheme/wpcomment/frontend/inputs/image.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new WPComment_InputManager($field_meta, 'image');
$multiple_allowed = $fm->get_meta_value('multiple_allowed');

$input_classes = $fm->input_classes();

// var_dump($input_classes);

$images = wpcomment_convert_options_to_key_val($fm->images(), $field_meta);
// wpcomment_pa($images);

// If Image empty
if ( ! $images ) {
	echo '<div class="wpcomment-option-notice">';
        echo '<p>'. __( "Images are required, please add it.", "wpcomment" ) .'</p>';
    echo '</div>';
	return;
}

$custom_attr = array();

?>


<div class="<?php echo esc_attr($fm->field_inner_wrapper_classes()); ?>" >

	<!-- if title of field exist -->
	<?php if ($fm->field_label()): ?>
		<label class="<?php echo esc_attr($fm->label_classes()); ?>" for="<?php echo esc_attr($fm->data_name()); ?>" ><?php echo $fm->field_label(); ?></label>
	<?php endif ?>

	<div class="nm-boxes-outer">
		<?php 
		$img_index = 0;

		if ($images) {
			
			foreach ($images as $image) {
				
				$image_full   = isset($image['link']) ? $image['link'] : 0;
				$image_id     = isset($image['image_id']) ? $image['image_id'] : 0;
				$image_title  = isset($image['raw']) ? stripslashes($image['raw']) : '';
				$image_label  = isset($image['label']) ? stripslashes($image['label']) : '';
				$option_id    = $fm->data_name().'-'.$image_id;
				
				// Actually image URL is link
				$image_link = isset($image['url']) ? $image['url'] : '';
				$image_url          = apply_filters('wpcomment_image_input_url', wp_get_attachment_thumb_url( $image_id ), $image, $field_meta);
				
				$option_class     = array(
		        						"wpcomment-option-{$option_id}",
		                            );
		                                
		        $option_class	= apply_filters('wpcomment_option_classes', implode(" ", $option_class), $field_meta);
		        $option_class	.= $input_classes;
				
				$checked_option = '';
				if( ! empty($default_value) ){
				    if( is_array($default_value) ) {
				        foreach($default_value as $img_data) {
				            if( isset($img_data['image_id']) && $image['image_id'] == $img_data['image_id'] ) {
				                $checked_option = 'checked="checked"';
				            }
				        }
				    } else {
				        $checked_option = ($image['raw'] == $default_value ? 'checked=checked' : '' );
				    }
	            }
	            
	            // Builder Addons
	            $builder = isset($image['builder']) ? $image['builder'] : '';
	            $custom_attr = apply_filters('wpcomment_image_input_custom_attributes', $custom_attr, $image, $field_meta);
				// Loading Modals
				$modal_vars = array('image_id' => $image_id, 'image_full' => $image_full, 'image_title' => $image_label);
				wpcomment_load_input_templates( 'frontend/component/image/image-modals.php', $modal_vars);
	            ?>

	            <label>
	            	<span class="pre_upload_image <?php echo esc_attr($fm->input_classes()) ?>" >
	            		
	            		<?php if ($multiple_allowed == 'on'){ ?>
							<input 
								type="checkbox" 
								name="<?php echo esc_attr($fm->form_name()); ?>[]" 
								id="<?php echo esc_attr($option_id); ?>" 
								data-label="<?php echo esc_attr($image_title); ?>"
								class="<?php echo esc_attr($option_class)?>"
								data-title="<?php echo esc_attr($fm->title()); ?>" 
								data-optionid="<?php echo esc_attr($option_id); ?>" 
				                data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
				                value="<?php echo esc_attr(json_encode($image)); ?>" 
				                <?php echo esc_attr($checked_option); ?>
							>
						<?php }else{ ?>
							<input 
								type="radio" 
								name="<?php echo esc_attr($fm->form_name()); ?>[]" 
								id="<?php echo esc_attr($option_id); ?>" 
								data-label="<?php echo esc_attr($image_title); ?>" 
								class="<?php echo esc_attr($option_class)?>"
								data-title="<?php echo esc_attr($fm->title()); ?>" 
								data-type="image" 
								data-optionid="<?php echo esc_attr($option_id); ?>" 
				                data-data_name="<?php echo esc_attr($fm->data_name()); ?>" 
				                value="<?php echo esc_attr(json_encode($image)); ?>" 
				                <?php echo esc_attr($checked_option); ?> 
				                
				                	<?php 
										// Add input extra attributes
										if (!empty($custom_attr)) {
											foreach ($custom_attr as $key => $val){ echo $key . '="' . $val .'"'; }
										}
									?>
							>
						<?php } 

						if ( $image['image_id'] != '' ) {
							if ( isset($image['url']) && $image['url'] != '' ) {
							?>
								<a href="<?php echo esc_url($image_link); ?>">
									<img src="<?php echo esc_url($image_url); ?>">
								</a>
							<?php	
							}else{
								$image_url = wp_get_attachment_thumb_url( $image['image_id'] );
							?>
								<img data-image-tooltip="<?php echo wp_get_attachment_url($image['image_id']); ?>" src="<?php echo esc_url($image_url); ?>" class="img-thumbnail wpcomment-zoom-<?php echo esc_attr($fm->data_name()); ?>" title="<?php echo esc_attr($image_label); ?>" data-wpcomment-tooltip="wpcomment_tooltip">
								<!--<label class="wpcomment-img-style1-label"> <?php echo $image_label; ?> </label>-->
							<?php
							}
						}else{

							if (isset($image['url']) && $image['url'] != '') {
							?>
								<a href="<?php echo esc_url($image_link); ?>">
									<img width="150" height="150" src="<?php echo esc_url($image['link']); ?>">
								</a>
							<?php
							}else{
							?>	
								<img class="img-thumbnail wpcomment-zoom-<?php echo esc_attr($fm->data_name()); ?>" data-image-tooltip="<?php echo esc_url($image['link']); ?>" src="<?php echo esc_url($image['link']); ?>">
							<?php
							}
						?>
						<?php
						}
						?>
						
						<!--<a href="#" class="wpcomment-image-overlay" data-model-id="modalImage<?php echo esc_attr($image_id); ?>">View</a>-->

	            	</span> <!-- pre_upload_image -->
	            </label>
	            <?php
	            $img_index++;
			}
		}
		?>
		<div style="clear:both"></div>
		</div> <!-- nm-boxes-outer -->
	<?php 
	
	?>
</div>