<?php
/**
 * Basic Settings
 **/

if( !defined("ABSPATH") ) die("Not Allowed");
?>

<div class="wppcomment-basic-setting-section wpcomment-settings-wrapper">
    <form class="wpcomment-settings-form">
        <input type="hidden" name="action" value="wpcomment_save_settings" /> 
        <input type="hidden" name="wpcomment_nonce" value="<?php echo wp_create_nonce('wpcomment_save_form_meta_nonce'); ?>">
        
	<h2 class="wppcomment-heading-style"><?php _e('WP Comments Settings', "wppcomment"); ?><span></span></h2>
	<button type="button" class="btn btn-info wpcomment_settings_toggle"><?php _e('<< Fields', "wpcomment"); ?></button>
	
	<div class="row mt-5">
		<div class="col-md-6 col-sm-12">
			<p class="form-group">
				<label for="wpcomment_heading"><?php _e('Heading will appear before meta, leave blank for none
', "wppcomment"); ?>
                 	
             	</label>
				<input type="text" class="form-control" id="wpcomment_heading" name="wpcomment_heading" value="<?php echo WPCOMMENT_ADMIN()::get_option('wpcomment_heading');?>">
			</p>
		</div>
	</div>
	
	<?php do_action('wpcomment_after_general_settings'); ?>
	
	<input class="button button-primary" type="submit" value="Save" />
	<div class="clearboth"></div>
	</form>
</div>
