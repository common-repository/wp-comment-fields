<?php 
/*
** PPOM New Form Meta
*/

/* 
**========== Direct access not allowed =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

	// get class instance
	$form_meta = WPCOMMENT_FIELDS_META();
	
	$wpcomment					= '';
	$wpcomment_meta = wpcomment_get_saved_meta();
	$wpcomment_field_index = 1;
	
?>


<div id="wpcomment-pre-loading"></div>
    
<div class="wpcomment-admin-wrap woocommerce wpcomment-wrapper" style="display:none">

<h1 class="wpcomment-heading-style"><?php _e ( 'Create Comment Fields', "wpcomment" );?></h1>
<p><?php _e ( 'Create different meta groups for different products.', "wpcomment" );?></p>

<div class="wpcomment-admin-fields-wrapper">

	<!-- All fields inputs name show -->
	<div id="wpcomment_fields_model_id" class="wpcomment-modal-box wpcomment-fields-name-model">
	    <header> 
	        <h3><?php _e('Select Field', "wpcomment"); ?></h3>
	    </header>
	    <div class="wpcomment-modal-body">
	        <ul class="list-group list-inline">
                <?php
                foreach ( $wpcomment_inputs as $field_type => $meta ) {

                	if( $meta != NULL ){
                    	$fields_title = isset($meta -> title)? $meta -> title : null;
                    	$fields_icon = isset($meta -> icon)? $meta -> icon : null;
                    ?> 
	                    <li class="wpcomment_select_field list-group-item"  data-field-type="<?php echo esc_attr($field_type); ?>" >
	                        <span class="wpcomment-fields-icon">
	                        	<?php echo $fields_icon;  ?>
	                        </span>
	                        <span>
	                            <?php echo $fields_title;  ?>
	                        </span>
	                    </li>
                    <?php 
            		} 
                }
                ?>
            </ul>
	    </div>
	    <footer>
	    	<button type="button" class="btn btn-default close-model wpcomment-js-modal-close"><?php _e('Close' , "wpcomment"); ?></button>
	    </footer>
	</div>

	<div class="wpcomment-main-field-wrapper">
		<form class="wpcomment-save-fields-meta">
		<input type="hidden" name="action" value="wpcomment_save_form_meta">
		<input type="hidden" name="wpcomment_nonce" value="<?php echo wp_create_nonce('wpcomment_save_form_meta_nonce'); ?>">

		    <!-- saving all fields via model -->
		    <div class="wpcomment_save_fields_model">
		        <?php 
		        if ( $wpcomment_meta) {

		            $f_index = 1;
		            foreach ($wpcomment_meta as $field_index => $field_meta) {

		            	$field_type   = isset($field_meta['type']) ? $field_meta['type'] : '';
                        $the_title    = isset($field_meta['title']) ? $field_meta['title'] : '';
                        $the_field_id = isset($field_meta['data_name']) ? $field_meta['data_name'] : '';
                        $the_placeholder = isset($field_meta['placeholder']) ? $field_meta['placeholder'] : '';
                        $defualt_fields  = isset($wpcomment_inputs[$field_type]-> settings) ? $wpcomment_inputs[$field_type]-> settings : array();
						$defualt_fields = apply_filters("wpcomment_settings_{$field_type}", $defualt_fields, $field_type);
                        $defualt_fields = $form_meta->wpcomment_tabs_panel_classes($defualt_fields);
		        ?>

		                <!-- New PPOM Model  -->
		                <div id="wpcomment_field_model_<?php echo esc_attr($f_index); ?>" class="wpcomment-modal-box wpcomment-slider wpcomment_sort_id_<?php echo esc_attr($f_index); ?>">
						    <div class="wpcomment-model-content">
						    	
							    <header> 
							        <h3>
							        	<?php echo $field_type; ?>
							        	<span class="wpcomment-dataname-reader">(<?php echo $the_field_id; ?>)</span>
							        </h3>
							    </header>
							    <div class="wpcomment-modal-body">
							        <?php
		                            echo $form_meta->render_field_meta($defualt_fields, $field_type, $f_index, $field_meta);
		                        	?>
							    </div>
							    <footer> 
							        <span class="wpcomment-req-field-id"></span>
	                                <button type="button" class="btn btn-default close-model wpcomment-js-modal-close"><?php _e('Close', "wpcomment"); ?></button>
	                                <button class="btn btn-primary wpcomment-update-field wpcomment-add-fields-js-action" data-field-index='<?php echo esc_attr($f_index); ?>' data-field-type='<?php echo esc_attr($field_type); ?>' ><?php _e('Update Field', "wpcomment"); ?></button> 
							    </footer>
						    <?php 
	                        $wpcomment_field_index = $f_index;
	                        $wpcomment_field_index++;
	                        $f_index++;
	                        ?> 
							</div>
						</div>
		            <?php
		            }
		        }

		        echo '<input type="hidden" id="field_index" value="'.esc_attr($wpcomment_field_index).'">';
		        ?>
		    </div>

		    <!-- all fields append on table -->
		    <?php wpcomment_load_template('admin/fields.php',['wpcomment_meta'=>$wpcomment_meta]); ?>
		</form>
	</div>
	
	<?php
	/**
	 * Will use when required
	 * */
	wpcomment_load_template('admin/basic-settings.php');
	?>

</div>

<div class="checker">
    <?php  $form_meta->render_field_settings( ); ?>
</div>

</div>