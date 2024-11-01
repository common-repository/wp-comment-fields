<?php
/**
 * Manage file uploads, thumbs and generate links for uploaded files
 * 
 **/
if( ! defined('ABSPATH') ) die('Not Allowed.');

// Set/create directory and return path
function wpcomment_files_setup_get_directory( $sub_dir=false ) {
    
    $upload_dir = wp_upload_dir ();
		
	$parent_dir = $upload_dir ['basedir'] . '/' . WPCOMMENT_UPLOAD_DIR_NAME . '/';
	$thumb_dir  = $parent_dir . 'thumbs/';
	
	if($sub_dir){
		$sub_dir = $parent_dir . $sub_dir . '/';
		if(wp_mkdir_p($sub_dir)){
			return $sub_dir;
		}
	}elseif(wp_mkdir_p($parent_dir)){
		if(wp_mkdir_p($thumb_dir)){
			return $parent_dir;
		}
	}
}


// Return upload file dir path
function wpcomment_get_dir_path( $sub_dir=false ) {
	
	$wpcomment_upload_dir = wpcomment_files_setup_get_directory( $sub_dir );
	return apply_filters('wpcomment_dir_path', $wpcomment_upload_dir);
}

// Return upload file dir url
function wpcomment_get_dir_url( $thumb=false, $legacy=false ) {
	
	$upload_dir = wp_upload_dir ();
	$return_url = '';
	
	$dir_name = $legacy ? 'comment_uploads' : WPCOMMENT_UPLOAD_DIR_NAME;
	if ( $thumb ) {
		$return_url = $upload_dir ['baseurl'] . '/' . $dir_name . '/thumbs/';
	}	else {
		$return_url = $upload_dir ['baseurl'] . '/' . $dir_name . '/';
	}
		
	return apply_filters('wpcomment_dir_url', set_url_scheme( $return_url ));
}

// Check if given filenameis image
function wpcomment_is_file_image( $file_name ){
	
	$type = strtolower ( substr ( strrchr ( $file_name , '.' ), 1 ) );
	if (($type == "gif") || ($type == "jpeg") || ($type == "png") || ($type == "pjpeg") || ($type == "jpg"))
		return true;
	else
		return false;
}

// return html for file thumb
function wpcomment_create_thumb_for_meta( $field_meta, $file_name, $cropped=false, $size=null) {
	
	$file_thumb_url = wpcomment_is_file_image($file_name) ? wpcomment_get_dir_url(true) . $file_name : WPCOMMENT_URL.'/images/file.png';
	$file_dir_path = wpcomment_get_dir_path() . $file_name;

	if( ! file_exists($file_dir_path) ) return '';
	
	$file_link = wpcomment_get_dir_url() . $file_name;
	
	$wpcomment_cart_meta_thumb_size = wpcomment_get_thumbs_size();
	
	$wpcomment_html	=  '<p class="wpcomment-file-front-wrapper">';
	$thumb_html = '<img class="wpcomment-file-thumbnail" style="width:'.esc_attr($wpcomment_cart_meta_thumb_size).'" src="'.esc_url($file_thumb_url).'" alt="'.sprintf(__("%s","wpcomment"), $file_name).'">';
	$wpcomment_html	.= '<a href="'.esc_url($file_link).'" class="wpcomment-file-link" itemprop="image" title="'.esc_attr($file_name).'">' . $thumb_html . '</a>';
	$wpcomment_html .= '<p class="wpcomment-file-title">'.esc_html($field_meta['title']).'</p>';
	$wpcomment_html	.=  '</p>';
	
	return apply_filters('wpcomment_meta_file_thumb', $wpcomment_html, $file_name);
}


function wpcomment_upload_file() {
		
	header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
	header ( "Cache-Control: no-store, no-cache, must-revalidate" );
	header ( "Cache-Control: post-check=0, pre-check=0", false );
	header ( "Pragma: no-cache" );
	
	$wpcomment_nonce = $_REQUEST['wpcomment_nonce'];
	$file_upload_nonce_action = "wpcomment_uploading_file_action";
	if ( ! wp_verify_nonce( $wpcomment_nonce, $file_upload_nonce_action ) && apply_filters('wpcomment_verify_upload_file', true) ) {
    	$response ['status'] = 'error';
		$response ['message'] = __ ( 'You cannot upload the file at this time, please refresh the page and try again. Note that your current option choices will be reset.', 'wp-comment-fields' );
		wp_send_json( $response );
	}
	
	// setting up some variables
	$file_dir_path = wpcomment_get_dir_path();
	$response = array ();
	if ($file_dir_path == 'errDirectory') {
		
		$response ['status'] = 'error';
		$response ['message'] = __ ( 'Error while creating directory', 'wp-comment-fields' );
		wp_send_json( $response );
	}
	
	$file_name = '';
	
	if( isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
		$file_name = sanitize_file_name( $_REQUEST['name'] );
	}elseif( isset($_REQUEST['_file']) && $_REQUEST['_file'] != '') {
		$file_name = sanitize_file_name( $_REQUEST['_file'] );
	}
	
	$file_name = apply_filters('wpcomment_uploaded_filename', $file_name);
	
	/* ========== Invalid File type checking ========== */
	$file_type = wp_check_filetype($file_name);
	$extension = $file_type['ext'];
	
	
	$default_restricted = 'php,php4,php5,php6,php7,phtml,exe,shtml';
	$restricted_type = wpcomment_get_option('wpcomment_restricted_file_type', $default_restricted);
	$restricted_type = explode(',', $restricted_type);
	
	if( in_array( strtolower($extension), $restricted_type) ){
		$response ['status'] = 'error';
		$response ['message'] = __ ( 'File type not valid - '.$extension, 'wp-comment-fields' );
		wp_send_json( $response );
	}
	/* ========== Invalid File type checking ========== */
	
	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds
	                        
	// 5 minutes execution time
	@set_time_limit ( 5 * 60 );
	
	// Uncomment this one to fake upload time
	// usleep(5000);
	
	// Get parameters
	$chunk = isset ( $_REQUEST ["chunk"] ) ? intval ( $_REQUEST ["chunk"] ) : 0;
	$chunks = isset ( $_REQUEST ["chunks"] ) ? intval ( $_REQUEST ["chunks"] ) : 0;
	// $file_name = isset ( $_REQUEST ["name"] ) ? sanitize_file_name($_REQUEST ["name"]) : '';
	
	$file_path_thumb = $file_dir_path . 'thumbs';
	$file_name = wp_unique_filename($file_path_thumb, $file_name);
	$file_name = strtolower($file_name);
	$file_path = $file_dir_path . $file_name;
	
	// var_dump($file_path); exit;
	
	// Make sure the fileName is unique but only if chunking is disabled
	if ($chunks < 2 && file_exists ( $file_path )) {
		$ext = strrpos ( $file_name, '.' );
		$file_name_a = substr ( $file_name, 0, $ext );
		$file_name_b = substr ( $file_name, $ext );
		
		$count = 1;
		while ( file_exists ( $file_dir_path . $file_name_a . '_' . $count . $file_name_b ) )
			$count ++;
		
		$file_name = $file_name_a . '_' . $count . $file_name_b;
		$file_path = $file_dir_path . $file_name;
	}
	
	// Remove old temp files
	if ($cleanupTargetDir && is_dir ( $file_dir_path ) && ($dir = opendir ( $file_dir_path ))) {
		while ( ($file = readdir ( $dir )) !== false ) {
			$tmpfilePath = $file_dir_path . $file;
			
			// Remove temp file if it is older than the max age and is not the current file
			if (preg_match ( '/\.part$/', $file ) && (filemtime ( $tmpfilePath ) < time () - $maxFileAge) && ($tmpfilePath != "{$file_path}.part")) {
				@unlink ( $tmpfilePath );
			}
		}
		
		closedir ( $dir );
	} else
		die ( '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}' );
	
	
	
	// Look for the content type header
	if (isset ( $_SERVER ["HTTP_CONTENT_TYPE"] ))
		$contentType = $_SERVER ["HTTP_CONTENT_TYPE"];
	
	if (isset ( $_SERVER ["CONTENT_TYPE"] ))
		$contentType = $_SERVER ["CONTENT_TYPE"];
		
	// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
	if (strpos ( $contentType, "multipart" ) !== false) {
		if (isset ( $_FILES ['file'] ['tmp_name'] ) && is_uploaded_file ( $_FILES ['file'] ['tmp_name'] )) {
			// Open temp file
			$out = fopen ( "{$file_path}.part", $chunk == 0 ? "wb" : "ab" );
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen ( sanitize_text_field($_FILES ['file'] ['tmp_name']), "rb" );
				
				if ($in) {
					while ( $buff = fread ( $in, 4096 ) )
						fwrite ( $out, $buff );
				} else
					die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}' );
				fclose ( $in );
				fclose ( $out );
				@unlink ( sanitize_text_field($_FILES ['file'] ['tmp_name']) );
			} else
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}' );
		} else
			die ( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}' );
	} else {
		// Open temp file
		$out = fopen ( "{$file_path}.part", $chunk == 0 ? "wb" : "ab" );
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen ( "php://input", "rb" );
			
			if ($in) {
				while ( $buff = fread ( $in, 4096 ) )
					fwrite ( $out, $buff );
			} else
				die ( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}' );
			
			fclose ( $in );
			fclose ( $out );
		} else
			die ( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}' );
	}
	
	// Check if file has been uploaded
	if (! $chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off
		rename ( "{$file_path}.part", $file_path );
		
		$data_name  = sanitize_key($_REQUEST['data_name']);
		// $product_id = intval($_REQUEST['product_id']);
		// $file_meta = wpcomment_get_field_meta_by_dataname($product_id , $data_name );
		// $comment_meta = wpcomment_get_saved_meta();
		// wbps_logger_array($comment_meta);
	
		
		// making thumb if images
		if( wpcomment_is_file_image($file_name) ) {
		    
		    $thumb_size = wpcomment_get_thumbs_size();
			$thumb_dir_path = wpcomment_create_image_thumb($file_dir_path, $file_name, $thumb_size);
			if(file_exists($thumb_dir_path)){
				list($fw, $fh) = getimagesize( $file_path );
				$response = array(
						'file_name'			=> $file_name,
						'file_w'			=> $fw,
						'file_h'			=> $fh,
						'nocache'			=> time(),
						'file_url'			=> wpcomment_get_dir_url() . $file_name,
						'html'				=> wpcomment_uploaded_file_preview($file_name, $data_name),
				);
			}else{
				$response = array(
					'file_name'			=> 'ThumbNotFound',
				);
			}
		}else{
			$response = array(
					'file_name'			=> $file_name,
					'file_w'			=> 'na',
					'file_h'			=> 'na',
					'html'				=> wpcomment_uploaded_file_preview($file_name, $data_name),
			);
		}
	}
	// Return JSON-RPC response
	//die ( '{"jsonrpc" : "2.0", "result" : '. json_encode($response) .', "id" : "id"}' );
	die ( json_encode( apply_filters('wpcomment_file_upload', $response, $file_type, $file_dir_path, $file_name)) );
}

// Deleting file
function wpcomment_delete_file() {
	
    $file_name = sanitize_file_name( $_REQUEST ['file_name'] );
    
	$wpcomment_nonce = $_REQUEST['wpcomment_nonce'];
	$file_nonce_action = "wpcomment_deleting_file_action";
	if ( ! wp_verify_nonce( $wpcomment_nonce, $file_nonce_action ) ) {
    	printf(__("Error while deleting file %s", "wpcomment"), $file_name );
    	die(0);
	}
    
    $dir_path = wpcomment_get_dir_path();
    
    $file_path = $dir_path . $file_name;
    
    if ( file_exists($file_path) && unlink ( $file_path )) {
    	
    	if ( wpcomment_is_file_image($file_name)){
    		$thumb_path = $dir_path . 'thumbs/' . $file_name;
    		if(file_exists($thumb_path))
    			unlink ( $thumb_path );
    		
    		$cropped_image_path = $dir_path . 'cropped/' . $file_name;
    		if(file_exists($cropped_image_path))
    			unlink ( $cropped_image_path );
    	}
    	
    	// make sure file is removed
    	if( ! file_exists($file_path) ) {
    		_e( 'File removed', 'wp-comment-fields' );
    	} else {
    		printf(__("Error while deleting file %s", "wpcomment"), $file_path );
    	}
    	
    		
    } else {
    	printf(__("Error while deleting file %s", "wpcomment"), $file_path );
    }

    die ( 0 );
}

// Creating thumb for image
function wpcomment_create_image_thumb( $file_path, $image_name, $thumb_size ) {
    
    $thumb_size = intval($thumb_size);
    $wp_image = wp_get_image_editor ( $file_path . $image_name );
    $image_destination = $file_path . 'thumbs/' . $image_name;
    if (! is_wp_error ( $wp_image )) {
    	$wp_image -> resize ( $thumb_size, $thumb_size, true );
    	$wp_image -> save ( $image_destination );
    }
    
    return $image_destination;
}

// Get file download url after payment
function wpcomment_get_file_download_url( $file_name, $order_id, $product_id ) {
	
	$base_dir_path 		= wpcomment_get_dir_path() . $file_name;
	$confirm_dir		= 'confirmed/'.$order_id;
	$confirmed_dir_path = wpcomment_get_dir_path($confirm_dir);
	$edits_dir_path 	= wpcomment_get_dir_path('edits') . $file_name;
	
	$wpcomment_dir_url		= wpcomment_get_dir_url();
	
	$file_download_url_found		= '';
	
	$file_name			= $product_id . '-' . $file_name;
	
	// Check if file not yet moved to confirm then move it.
	if( file_exists($base_dir_path) ) {
		if(rename ( $base_dir_path, $confirmed_dir_path.$file_name)) {
			$file_download_url_found = $wpcomment_dir_url .'confirmed/' .$order_id .'/' . $file_name;
		}
	} else if( file_exists($confirmed_dir_path.$file_name) ) {
		$file_download_url_found = $wpcomment_dir_url .'confirmed/' .$order_id .'/' . $file_name;
	} else if( file_exists($edits_dir_path) ) {
		$file_download_url_found = $wpcomment_dir_url . 'edits/' . $file_name;
	}
	
	return apply_filters('wpcomment_file_download_url', $file_download_url_found, $file_name);
	
}

// Generate uploaded file preview
function wpcomment_uploaded_file_preview($file_name, $data_name){
	
	$file_dir_path	= wpcomment_get_dir_path();
	$file_path = $file_dir_path . $file_name;
	
	if( !file_exists($file_path) ) return '';
	
	$is_image		= wpcomment_is_file_image($file_name);
		
	$thumb_url = $file_meta = $file_tools = $html = '';
	
	// $settings = json_decode(stripslashes($settings), true);
	// wpcomment_pa($settings);
	$file_id = 'thumb_'.time();

	if($is_image){
		
		list($fw, $fh) 	= getimagesize( $file_path );
		$file_meta		= $fw . '(w) x '.$fh.'(h)';
		$file_meta		.= ' - '.__('Size: ', "wpcomment") . wpcomment_get_filesize_in_kb($file_name);
		
		$thumb_url = wpcomment_get_dir_url( true ) . $file_name . '?nocache='.time();
		
		//large view
		$image_url = wpcomment_get_dir_url() . $file_name . '?nocache='.time();
		$html .= '<div style="display:none" id="u_i_c_big_'.$file_id.'"><p id="thumb-thickbox"><img src="'.$image_url.'" /></p></div>';
		
		// Loading Modals
		// $modal_vars = array('file_id' => $file_id, 'image_full'=>$image_url, 'image_title'=>$file_name);
		// ob_start();
  //      wpcomment_load_template('v10/file-modals.php', $modal_vars);
  //      $html .= ob_get_clean();
		
		// Tools group
		$file_tools .= '<div class="btn-group" role="group" aria-label="Tools" style="text-align: center; display: block;">';
		// $file_tools .= '<a href="#" class="nm-file-tools btn btn-primary u_i_c_tools_del" title="'.__('Remove', "wpcomment").'"><span class="fa fa-times"></span></a>';
		$file_tools .= '<a href="#" class="nm-file-tools btn btn-primary u_i_c_tools_del" title="'.__('Remove', "wpcomment").'">'.__('Delete', 'wp-comment-fields').'</span></a>';
		
		if( apply_filters('wpcomment_show_image_popup', false) ) {
			$file_tools .= '<a href="#" data-toggle="modal" data-target="#modalFile'.esc_attr($file_id).'" class="btn btn-primary"><span class="fa fa-expand"></span></a>';
		}
		
			
		$file_tools .= '</div>';
		
	}else{
		
		$file_meta		.= __('Size: ', "wpcomment") . wpcomment_get_filesize_in_kb($file_name);
		$thumb_url		= WPCOMMENT_URL . '/images/file.png';
		
		$file_tools .= '<a class="btn btn-primary nm-file-tools u_i_c_tools_del" href="" title="'.__('Remove', "wpcomment").'"><span class="fa fa-times"></span></a>';	//delete icon
		// $file_tools .= '<a class="btn btn-primary nm-file-tools u_i_c_tools_del" href="" title="'.__('Remove', "wpcomment").'">Delete</a>';	//delete icon
	}
	
	
	$short_name = wpcomment_files_trim_name( $file_name );
	
	// $html .= '<table class="table table-bordered"><tr>';
	// $html .= '<td style="vertical-align:middle">';
	$html .='<label style="margin-top: 8px;display: block;text-align: center;">
				<div class="pre_upload_image collapse_dropdown_id" data-wpcomment-tooltip="wpcomment_tooltip" title="'.$short_name.'">
					
					<img class="img-thumbnail" style="width:'.esc_attr(wpcomment_get_thumbs_size()).'" data-filename="'.esc_attr($file_name).'" id="'.esc_attr($file_id).'" src="'.esc_url($thumb_url).'" />
				</div>
			</label>';
	$html .= $file_tools;
			
	
	// $html .= '</tr></table>';
	
	// $html .= '<td class="nm-imagetools" style="padding-left: 5px; vertical-align:top"><h4>'.$short_name.'</h4><br>';
	// $html .= '<span class="file-meta">'.$short_name.'</span><br>';
	// $html .= '</td>';
	
	
	return apply_filters('wpcomment_file_preview_html', $html, $file_name, $settings);
}

// Trim long filename to short
function wpcomment_files_trim_name( $file_name ) {
    
    $text_length = strlen($file_name);

    // for different language string 
    $string_utf8 = strlen(utf8_decode($file_name));

	$max_chars = apply_filters('wpcomment_trim_file_maxchar', 20);
	
	if( $text_length > $max_chars && $text_length == $string_utf8 ) {
		$trimmed_filename = substr_replace($file_name, '...', $max_chars/2, $text_length-$max_chars);
	} else {
		$trimmed_filename = $file_name;
	}
	return $trimmed_filename;
}


// Save cropped image fro dataUrl to image
function wpcomment_save_data_url_to_image($data, $file_name) {
	
	$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
	
	$dest_cropped = wpcomment_get_dir_path('cropped') . $file_name;
	file_put_contents( $dest_cropped, $data);
}

// Building options array for croppie
function wpcomment_get_croppie_options( $settings ) {
	
	// wpcomment_pa($settings);
	
	$cropping_settigs = array();
	$viewport_type = !empty($settings['viewport_type']) ? $settings['viewport_type'] : 'square';
	
	
	$viewport_h = !empty($viewport['height']) ? $viewport['height'] : 300;
	$viewport_w = !empty($viewport['width']) ? $viewport['width'] : 300;
	
	
	
	$viewport_settings = wpcomment_get_viewport_settings( $settings );
	
	// view height, width and type
	$cropping_settigs['viewport'] = $viewport_settings;
	// $cropping_settigs['viewport_all'] = $settings['options'];
	
	// Boundary settings
	
	$boundary = !empty($settings['boundary']) ? $settings['boundary'] : '200,200';
	$boundary = explode(",", $boundary);
	
	$boundary_h = $boundary[0];
	$boundary_w = $boundary[1];
	
	if( $viewport_type == 'circle' ) { // If circle then set same heigt and width
		$boundary_w = $boundary_h;
	}
	
	// boundary height, width
	$cropping_settigs['boundary'] = array('height'=>$boundary_h, 'width'=>$boundary_w);
	
	$enable_exif = ( isset($settings['enable_exif']) && $settings['enable_exif'] == 'on') ? true : false;
	// exif
	$cropping_settigs['enableExif'] = $enable_exif;
	
	$enforce_boundary = ( isset($settings['enforce_boundary']) && $settings['enforce_boundary'] == 'on') ? true : false;
	// enforce boundary
	$cropping_settigs['enforceBoundary'] = $enforce_boundary;
	
	$enable_zoom = ( isset($settings['enable_zoom']) && $settings['enable_zoom'] == 'on') ? true : false;
	// show zoomer
	$cropping_settigs['enableZoom'] = $enable_zoom;
	
	$show_zoomer = ( isset($settings['show_zoomer']) && $settings['show_zoomer'] == 'on') ? true : false;
	// show zoomer
	$cropping_settigs['showZoomer'] = $show_zoomer;
	
	
	return apply_filters('wpcomment_croppie_options', $cropping_settigs, $settings);
	
}

function wpcomment_get_viewport_settings( $settings ) {
	
	$viewport_type = !empty($settings['viewport_type']) ? $settings['viewport_type'] : 'square';
	
	$first_viewport_size = array();
	
	if( isset($settings['options']) ) {
		foreach($settings['options'] as $option => $viewport) {
			
			if( ! isset($viewport['width']) ) continue;
			
			$first_viewport_size['width'] = $viewport['width'];
			$first_viewport_size['height'] = $viewport['height'];
			break;
		}
	}
		
	
	$the_viewport =  array('width'=>300,'height'=>300);
	if( !empty($first_viewport_size['width']) && !empty($first_viewport_size['height'])) {
		
		$the_viewport =  array('width'=>$first_viewport_size['width'],'height'=>$first_viewport_size['height']);
	}
	
	$the_viewport['type'] = $viewport_type;
	
	return $the_viewport;
	
}

/*
 * removing ununsed order files
*/
function wpcomment_files_removed_unused_images(){
	
	$dir = wpcomment_get_dir_path();
	
	if(is_dir($dir) && apply_filters('wpcomment_remove_unused_images', true)){

		$dir_handle = opendir($dir);
		while ($file = readdir($dir_handle)){
				
			$file_path = $dir.$file;
			if( is_file ($file_path) ){
				
				// Get Files Created Date
				$file_created_date = date ("Y-m-d H:i:s.", filemtime($file_path));
				$today             = date("Y-m-d H:i:s");
				
				$day_count = wpcomment_files_uploaded_days_count($file_created_date, $today);
				
				if ($day_count > 7) {
					@unlink($file_path);
				}
			}
		}
	}
	
	closedir($dir_handle);
}

function wpcomment_files_uploaded_days_count($date1, $date2){ 
	
    $diff = strtotime($date2) - strtotime($date1); 
    
    return abs(round($diff / 86400)); 
} 

// Return file name with prefix product id
function wpcomment_file_get_name($file_name, $product_id, $cart_item=null) {
	
	$new_file_name = "{$product_id}-{$file_name}";
	return apply_filters('wpcomment_file_name_prefix', $new_file_name, $file_name, $product_id, $cart_item);
}
