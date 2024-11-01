<?php
/**
* Image Modal Template
* 
* This template can be overridden by copying it to yourtheme/wpcomment/frontend/component/image/image-modals.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$modal_id = 'modalImage'.$image_id;
?>

<div id="<?php echo esc_attr($modal_id)?>" class="wpcomment-popup-wrapper wpcomment-popup-handle">
    <div class="wpcomment-popup-inner-section">
        <header class="wpcomment-popup-header"> 
            <!-- <a href="#" class="js-modal-close close">×</a> -->
            <h3><?php echo $image_title?></h3>
        </header>
        <div class="wpcomment-popup-body images">
            <img src="<?php echo esc_url($image_full) ?>">
        </div>
        <footer class="wpcomment-popup-footer"> 
            <a href="#" class="wpcomment-popup-button wpcomment-popup-close-js">Close</a> 
        </footer>
    </div>
</div>