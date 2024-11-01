<?php
/**
 * Fields Managers in Admin
 **/
?>
<div class="table-responsive">
    <h2 class="wppcomment-heading-style"><?php _e('WP Comments Fields', "wppcomment"); ?><span></span></h2>
	<table class="table wpcomment_field_table table-striped">
        <thead>
            <tr>            
                <th colspan="9">
                    <button type="button" class="btn btn-primary" data-modal-id="wpcomment_fields_model_id"><?php _e('Add field', "wpcomment"); ?></button>
                    <button type="button" class="btn btn-danger wpcomment_remove_field"><?php _e('Remove', "wpcomment"); ?></button>
                    
                    <button type="button" class="btn btn-info wpcomment_settings_toggle wpcomment_settings float-right"><?php _e('Settings', "wpcomment"); ?></button>
                </th>  
            </tr>
            <tr class="wpcomment-thead-bg">
                <th></th>
                 <th class="wpcomment-check-all-field wpcomment-checkboxe-style">
					<label>
						<input type="checkbox">
						<span></span>
					</label>
                </th>
                <th><?php _e('Status', "wpcomment"); ?></th>
                <th><?php _e('Data Name', "wpcomment"); ?></th>
                <th><?php _e('Type', "wpcomment"); ?></th>
                <th><?php _e('Title', "wpcomment"); ?></th>
                <th><?php _e('Placeholder', "wpcomment"); ?></th>
                <th><?php _e('Required', "wpcomment"); ?></th>
                <th><?php _e('Actions', "wpcomment"); ?></th> 
            </tr>                       
        </thead>
        <tfoot>
            <tr class="wpcomment-thead-bg">
                <th></th>
                <th class="wpcomment-check-all-field wpcomment-checkboxe-style">
					<label>
						<input type="checkbox">
						<span></span>
					</label>
                </th>
                <th><?php _e('Status', "wpcomment"); ?></th>
                <th><?php _e('Data Name', "wpcomment"); ?></th>
                <th><?php _e('Type', "wpcomment"); ?></th>
                <th><?php _e('Title', "wpcomment"); ?></th>
                <th><?php _e('Placeholder', "wpcomment"); ?></th>
                <th><?php _e('Required', "wpcomment"); ?></th>
                <th><?php _e('Actions', "wpcomment"); ?></th>
            </tr>
            <tr>            
                <th colspan="12">
                    <div class="wpcomment-submit-btn text-right">
                    	<span class="wpcomment-meta-save-notice"></span>
                        <input type="submit" class="btn btn-primary" value="<?php _e('Save Fields', 'wp-comment-fields');?>">
                    </div>
                </th>
            </tr> 
        </tfoot>
        <tbody>
        <?php 
        if ( $wpcomment_meta ) {

            $f_index = 1;
            foreach ($wpcomment_meta as $field_index => $field_meta) {

                $field_type   = isset($field_meta['type']) ? $field_meta['type'] : '';
                $the_title    = isset($field_meta['title']) ? $field_meta['title'] : '';
                $the_field_id = isset($field_meta['data_name']) ? $field_meta['data_name'] : '';
                $the_placeholder = isset($field_meta['placeholder']) ? $field_meta['placeholder'] : '';
                $the_required = isset($field_meta['required']) ? $field_meta['required'] : '';
                $field_status    = isset($field_meta['status']) ? $field_meta['status'] : 'on';
                // wpcomment_pa($field_status);
                if ($the_required == 'on' ) {
                    $_ok = 'Yes';
                }else{
                    $_ok = 'No';
                }
        ?>
                
                <tr class="row_no_<?php echo esc_attr($f_index); ?>" id="wpcomment_sort_id_<?php echo esc_attr($f_index); ?>">
                    <td class="wpcomment-sortable-handle">
                        <i class="fa fa-arrows" aria-hidden="true"></i>
                    </td>
                    <td class="wpcomment-check-one-field wpcomment-checkboxe-style">
                    	<label>
							<input type="checkbox" value="<?php echo esc_attr($f_index); ?>">
							<span></span>
						</label>
                    </td>
                    <td>
                    	<div class="onoffswitch">
						    <input <?php echo checked($field_status, 'on'); ?> type="checkbox" class="onoffswitch-checkbox" id="wpcomment-onoffswitch-<?php echo esc_attr($f_index)?>" tabindex="0">
						    <label class="onoffswitch-label" for="wpcomment-onoffswitch-<?php echo esc_attr($f_index)?>">
						        <span class="onoffswitch-inner"></span>
						        <span class="onoffswitch-switch"></span>
						    </label>
						    <input type="hidden" value="<?php echo $field_status; ?>" name="wpcomment[<?php echo esc_attr($f_index); ?>][status]">
						</div>
                    </td>
                    <td class="wpcomment_meta_field_id"><?php echo $the_field_id; ?></td>
                    <td class="wpcomment_meta_field_type"><?php echo $field_type; ?></td>
                    <td class="wpcomment_meta_field_title"><?php echo $the_title; ?></td>
                    <td class="wpcomment_meta_field_plchlder"><?php echo $the_placeholder; ?></td>
                    <td class="wpcomment_meta_field_req"><?php echo $_ok; ?></td> 
                    <td>
                        <button class="btn  wpcomment_copy_field" data-field-type="<?php echo esc_attr($field_type); ?>" title="<?php _e('Copy Field',"wpcomment"); ?>" id="<?php echo esc_attr($f_index); ?>"><span class="dashicons dashicons-admin-page"></span></span></i></button>
                        <button class="btn wpcomment-edit-field" data-modal-id="wpcomment_field_model_<?php echo esc_attr($f_index); ?>" id="<?php echo esc_attr($f_index); ?>" title="<?php _e('Edit Field',"wpcomment"); ?>"><span class="dashicons dashicons-edit"></span></button>
                    </td>
                </tr> 
                <?php   
                $wpcomment_field_index = $f_index;
                $wpcomment_field_index++;
                $f_index++;
            }
        }
    			?>
    	</tbody>
    </table>
</div>