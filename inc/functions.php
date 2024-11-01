<?php
/*
 * this file contains pluing meta information and then shared
 * between pluging and admin classes
 * * [1]
 */

if( ! defined('ABSPATH') ) die('Not Allowed.');

function wpcomment_pa($arr){
	
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

// Get field column
function wpcomment_get_field_colum( $meta ) {
	
	$field_column = isset($meta['width']) ? $meta['width'] : 12;
	
	// Check width has old settings
	if( strpos( $field_column, '%' ) !== false ) {
		
		$field_column = 12;
	} elseif( intval($field_column) > 12 ) {
		$field_column = 12;
	}
	
	return apply_filters('wpcomment_field_col', $field_column, $meta);
}

function wpcomment_translation_options( $option ) {
	
	if( !isset($option['option']) ) return $option;
	
	$option['option'] = wpcomment_wpml_translate($option['option'], 'WPCOMMENT');
	
	// if label is set
	if( isset($option['label']) ) {
		$option['label'] = wpcomment_wpml_translate($option['label'], 'WPCOMMENT');
	}
	return $option;
}

/**
 * WPML
 * registering and translating strings input by users
 */
function wpcomment_register($field_value, $domain) {
		
	if ( ! function_exists ( 'icl_register_string' )) 
		return $field_value;
	
	$field_name = $domain . ' - ' . sanitize_key($field_value);
	//WMPL
	/**
	 * register strings for translation
	 * source: https://wpml.org/wpml-hook/wpml_register_single_string/
	 */
	 
	 do_action( 'wpml_register_single_string', $domain, $field_name, $field_value );
	 
	 //Polylang
	 if( function_exists('pll_register_string') ) {
		pll_register_string($field_name, $field_value);
	 }
}


function wpcomment_wpml_translate($field_value, $domain) {
		
	// $field_value is array then return
	if( is_array($field_value) ) return $field_value;
	
	$field_name = $domain . ' - ' . sanitize_key($field_value);
    $field_value = stripslashes($field_value);
	
	//WMPL
    /**
     * register strings for translation
     * source: https://wpml.org/wpml-hook/wpml_translate_single_string/
     */
    if( has_filter('wpml_translate_single_string') ) {
		$field_value = apply_filters('wpml_translate_single_string', $field_value, $domain, $field_name );
    }
    
    
	// Polylang
	if( function_exists('pll__') ) {
		$field_value = pll__($field_value);
	}
	
	return $field_value;
}
/**
 * adding cart items to order
 * @since 8.2
 **/
function wpcomment_make_meta_data( $wpcomment_meta ){
	
	if( ! isset($wpcomment_meta['wp-comment-fields']['fields']) ) return $wpcomment_meta;
	
	
	$wpcomment_meta			= array();
	$wpcomment_cart_fields	= $wpcomment_meta['wp-comment-fields']['fields'];
	$wpcomment_meta_ids		= apply_filters('wpcomment_meta_ids_in_cart', null, $wpcomment_meta);
	$wpcomment_meta			= wpcomment_generate_cart_meta($wpcomment_cart_fields, $product_id, $wpcomment_meta_ids, $context);
	return apply_filters('wpcomment_meta_data', $wpcomment_meta, $wpcomment_meta, $context);
}

/**
 * This function will process all fields in cart and return into
 * readable form for comment meta
 * @params: $wpcomment_meta
 **/
function wpcomment_output_meta( $comment_meta ) {
	
	$wpcomment_meta = [];
	
	foreach( $comment_meta as $key => $value) {
		
		// if no value
		if( $value == '' ) continue;
		
		// $cart_item['data'] ->post_type == 'product' ? $cart_item['data']->get_id() : $cart_item['data']->get_parent_id();
		$field_meta = wpcomment_get_field_meta_by_dataname( $key );
		$data_name  = $key;
		// wpcomment_pa($value);
		
		// If field deleted while it's in cart
		if( empty($field_meta) ) continue;
		
		$field_type = isset($field_meta['type']) ? $field_meta['type'] : '';
		$field_title= isset($field_meta['title']) ? $field_meta['title'] : '';
		
		// third party plugin for different fields types
		$field_type = apply_filters('wpcomment_make_meta_data_field_type', $field_type, $field_meta);
		$meta_data = array();
	
		switch( $field_type ) {
			
			case 'file':
				$file_thumbs_html = '';
				foreach($value as $file_id => $file_uploaded) {
					$file_name = $file_uploaded['org'];
					$file_thumbs_html .= wpcomment_create_thumb_for_meta($field_meta, $file_name);
				}
				$meta_data = array('type'=>$field_type, 'name'=>$field_title, 'value'=>$file_thumbs_html);
			break;
		case 'image':
				if($value) {
					
					$display = wpcomment_generate_html_for_images($field_meta, $value);
				
					$meta_data = array('type'=>$field_type, 'name'=>$field_title, 'value'=>$value, 'display'=>$display);
				}
				break;
				
			case 'palettes':
				$selected_color = array();
				$color_options = $field_meta['options'];
				$options_filter	 = wpcomment_convert_options_to_key_val($field_meta['options'], $field_meta);
				foreach($value as $color){
					foreach($options_filter as $option_key => $opt){
						
						if( $color == $option_key ){
							$display = !empty($opt['label']) ? $opt['label'] : $opt['option'];
							$selected_color[] = $display;
							$meta_data = array('type'=>$field_type, 'name'=>$field_title, 'value'=>$value, 'display'=>implode(',',$selected_color));
							break;
						}
					}
				}
				
				break;
				
			case 'audio':
				if($value) {
					$wpcomment_file_count = 1;
					foreach($value as $id => $audio_meta) {
						$audio_meta = json_decode(stripslashes($audio_meta), true);
						$audio_url	= stripslashes($audio_meta['link']);
						$audio_html = '<a href="'.esc_url($audio_url).'" title="'.esc_attr($audio_meta['title']).'">'.$audio_meta['title'].'</a>';
						$meta_lable	= $field_title.': '.$wpcomment_file_count++;
						// $wpcomment_meta[$meta_lable] = $audio_html;
						$meta_data = array('type'=>$field_type, 'name'=>$meta_lable, 'value'=>$audio_html);
					}
				}
				break;
				
			case 'checkbox':
				
				$option_posted = $value;
				
				if( is_array($option_posted) ) {
					
					$option_posted = stripslashes_deep($option_posted);
				}
				
				$option_label_array = array();
				$options_data_array = array();
				
				$options_filter	 = wpcomment_convert_options_to_key_val($field_meta['options'], $field_meta);
				
				foreach($option_posted as $posted_value) {
					foreach($options_filter as $option_key => $option) {
						// var_dump($posted_value, $option);
	                    
	                    $option_value = stripslashes(wpcomment_wpml_translate($option['raw'],'wp-comment-fields'));
						
	                    if(  stripcslashes($posted_value) === $option_value ) {
	                        $option_label_array[] = $option['label'];
	                        $options_data_array[] = array('option'=>$option['raw'],'id'=>$option['option_id']);
	                    }
	                }
				}
				
				$meta_data = array('type'=>$field_type, 'name'=>$field_title, 'value'=> implode(', ',$option_label_array));
				break;
				
			case 'select':
			case 'radio':
				
				$posted_value = stripslashes($value);
				
				$option_price	= '';
				$option_data	= array();
				
				$options_filter	 = wpcomment_convert_options_to_key_val($field_meta['options'], $field_meta);
				
				foreach($options_filter as $option_key => $option) {
	                    
                    $option_value = stripslashes(wpcomment_wpml_translate($option['raw'],'WPCOMMENT'));
                    
                    if(  $posted_value == $option_value ) {
                        $option_price = $option['label'];
                        $option_data[] = array('option'=>$option['raw'],'id'=>$option['option_id']);
                        break;
                    }
                }
				
				$meta_data = array('type'=>$field_type, 'name'		=> $field_title, 
									'value'		=> $option_price,
									);
				break;

			case 'section':
				$show_cart = isset($field_meta['comment_display']) && $field_meta['comment_display'] == 'on' ? true : false;
				if( $show_cart )
					$meta_data = array('type'=>$field_type, 'name' => $field_title, 'value' => $value);
				break;
				
			case 'hidden':
				$show_cart = isset($field_meta['comment_display']) && $field_meta['comment_display'] == 'on' ? false : true;
				$meta_data = array('type'=>$field_type, 'name' => $field_title, 'value' => $value, 'hidden' => $show_cart);
				break;
				
			default:

				$value = is_array($value) ? implode(",", $value) : $value;
				// $wpcomment_meta[$field_title] = stripcslashes($value);
				$meta_data = array('type'=>$field_type, 'name'=>$field_title, 'value'=>stripcslashes($value));
				break;
		}
		
		
		$meta_data_field = apply_filters('wpcomment_fields_cart_meta', $meta_data, $key, $field_meta, $comment_meta);
		$wpcomment_meta[$key] = $meta_data_field;
	}
	
	return $wpcomment_meta;
}

/**
* hiding prices for variable product
* only when priced options are used
* 
* @since 8.2
**/
function wpcomment_meta_priced_options( $the_meta ) {
	
	$has_priced_option = false;
	foreach ( $the_meta as $key => $meta ) {
	
		$options		= ( isset($meta['options'] ) ? $meta['options'] : array());
		foreach($options as $opt)
		{
				
			if( isset($opt['price']) && $opt['price'] != '') {
				$has_priced_option = true;
			}
		}
	}
	
	return apply_filters('wpcomment_meta_priced_options', $has_priced_option, $the_meta);
}

/**
 * check if browser is IE
 **/
function wpcomment_if_browser_is_ie()
{
	//print_r($_SERVER['HTTP_USER_AGENT']);
	
	if(!(isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))){
		return false;
	}else{
		return true;
	}
}

function wpcomment_settings_link($links) {
	
	$quote_url = "https://najeebmedia.com/get-quote/";
	$wpcomment_setting_url = admin_url( 'admin.php?page=wpcomment');
	$video_url = '#';
	$wpcomment_deactivate = '#';
	
	$wpcomment_links = array();
	$wpcomment_links[] = sprintf(__('<a href="%s">Add Fields</a>', "wpcomment"), esc_url($wpcomment_setting_url) );
	$wpcomment_links[] = sprintf(__('<a href="%s" target="_blank">Quick Video Guide</a>', "wpcomment"), esc_url($video_url) );
	$wpcomment_links[] = sprintf(__('<a href="%s">Customized Solution</a>', "wpcomment"), esc_url($quote_url) );
	
	foreach($wpcomment_links as $link) {
		
  		array_push( $links, $link );
	}
	
  	return $links;
}

// Get field type by data_name
function wpcomment_get_field_meta_by_dataname( $data_name ) {
	
	$form_obj = new WPComment_Form;
	$wpcomment_fields= $form_obj::$fields;
	
	if( ! $wpcomment_fields ) return '';
	
	// wpcomment_pa($wpcomment_fields);
	$data_name = apply_filters('wpcomment_get_field_by_dataname_dataname', $data_name);
	
	$field_meta = '';
	foreach($wpcomment_fields as $field) {
	
		if( ! wpcomment_is_field_visible($field) ) continue;
		
		if( !empty($field['data_name']) && sanitize_key($field['data_name']) == $data_name) {
			$field_meta = $field;
			break;
		}
	}
	
	return $field_meta;
}

// Is PPOM meta has field of specific type
function wpcomment_has_field_by_type( $product_id, $field_type ) {
	
	$form_obj = new WPComment_Form;
	$wpcomment_fields= $form_obj::$fields;
	if( ! $wpcomment_fields ) return '';
	
	$fields_found = array();
	foreach($wpcomment_fields as $field) {
		
		if( !empty($field['type']) && $field['type'] == $field_type ) {
			$fields_found[] = $field;
		}
	}
	
	return $fields_found;
}

function wpcomment_load_template($file_name, $variables=array('')){

	if( is_array($variables))
    extract( $variables );
    
   $file_path =  WPCOMMENT_PATH . '/templates/'.$file_name;
   $file_path = apply_filters('wpcomment_load_template', $file_path, $file_name, $variables);
    
   if( file_exists($file_path))
   	include ($file_path);
   else
   	die('File not found'.$file_path);
}

// Loading loading input template absolute path provided
function wpcomment_load_input_templates($template_path, $vars=array('')){
	
	// Extract variable from array
	if( $vars != null && is_array($vars) ){ extract( $vars ); }
	
	if( isset($addon_type) ) {
		$full_path  =  $template_path;
	}else{
		$full_path  =  WPCOMMENT_PATH . "/templates/{$template_path}";
	}
	
	// wpcomment_pa($vars);
	
	// For template override
	$full_path  = apply_filters('wpcomment_input_templates_path', $full_path, $template_path, $vars);
	
	// Load Inputs from theme
	// $theme_template = wpcomment_load_templates_from_theme($template_path);
	
	// if( $theme_template != null ) { $full_path = $theme_template; }
    
    if( file_exists( $full_path ) ){
        include( $full_path );
    }else {
        die( "File not found {$full_path}" );
    }
}

// load file from full given path
function wpcomment_load_file($file_path, $variables=array('')){

	if( is_array($variables))
    extract( $variables );
    
   if( file_exists($file_path))
   	include ($file_path);
   else
   	die('File not found'.$file_path);
}

function wpcomment_load_bootstrap_css() {
	
	$return = true;
	if( wpcomment_get_option('wpcomment_disable_bootstrap') == 'yes' ) $return = false;
	
	return apply_filters('wpcomment_bootstrap_css', $return);
}

function wpcomment_load_fontawesome() {
	
	$return = true;
	if( wpcomment_get_option('wpcomment_disable_fontawesome') == 'yes' ) $return = false;
	
	return apply_filters('wpcomment_disable_fontawesome', $return);
}


function wpcomment_convert_options_to_key_val($options, $meta) {
	
	if( empty($options) ) return $options;
	
	if( ! apply_filters('wpcomment_is_option_convertable', true, $meta) ){
		return $options;
	}
	
	$meta_type = isset($meta['type']) ? $meta['type'] : '';
	
	// Do not change options for cropper
	// if( $meta['type'] == 'cropper' ) return $options;
	
	$wpcomment_new_option = array();
	foreach($options as $option) {
		
		$the_option = isset($option['option']) ? stripslashes($option['option']) : '';
		
		//Following input has 'title' instead 'option' in options array
		$option_with_titles_keys = apply_filters('wpcomment_option_with_title_key', array('imageselect', 'image', 'audio') );
		if( in_array($meta_type, $option_with_titles_keys) ) {
		
			$the_option = ! empty($option['title']) ? stripslashes($option['title']) : $option['id'];
		}
		
		if( $the_option != '' ) {
			
			$option = wpcomment_translation_options($option);
			$data_name		= isset($meta['data_name']) ? $meta['data_name'] : '';
			
			// wpcomment_pa($option);
			$option_label	= wpcomment_generate_option_label($option, $meta);
			
		
			$option_id = wpcomment_get_option_id($option, $meta);
			
			$wpcomment_new_option[$the_option] = array('label'		=> $option_label,
													'raw'		=> $the_option,
													'data_name' => $data_name,
													'id'		=> $option_id,		// Legacy key fix
													'option_id' => $option_id);
								
			if($meta_type === 'image' ) {								
				$wpcomment_new_option[$the_option]['link'] = isset($option['link']) ? $option['link'] : '';
				$wpcomment_new_option[$the_option]['url'] = isset($option['url']) ? $option['url'] : '';
				$wpcomment_new_option[$the_option]['image_id'] = $option['id'];
			}
			
			
			$wpcomment_new_option = apply_filters('wpcomment_option_meta',$wpcomment_new_option, $the_option, $option, $meta);
		}
	}
	
	if( !empty($meta['first_option']) ) {
		
		$fo_labeld = wpcomment_wpml_translate($meta['first_option'], 'WPCOMMENT');
		$first_option = array('' => array('label'=> $fo_labeld, 
										'raw'	=> '',
										'option_id' => '__first_option__')
										);
										
		$wpcomment_new_option = $first_option + $wpcomment_new_option;
		// array_unshift( $wpcomment_new_option, $first_option);
	}
	
	// wpcomment_pa($wpcomment_new_option);
	return apply_filters('wpcomment_options_after_changes', $wpcomment_new_option, $options, $meta);
}


// Generating option label with price
function wpcomment_generate_option_label( $option, $meta) {
	
	$meta_type = isset($meta['type']) ? $meta['type'] : '';
	
	$the_option = isset($option['option']) ? $option['option'] : '';
	if( $meta_type == 'imageselect' || $meta_type === 'image') {
		$the_option = isset($option['title']) ? $option['title'] : '';
	}
	
	$option_label = !empty($option['label']) ? $option['label'] : $the_option;
	$option_label = stripcslashes($option_label);
	
	
	return apply_filters('wpcomment_option_label', $option_label, $option, $meta);
}


// Retrun unique option ID
function wpcomment_get_option_id($option, $field_meta=null) {
	
	$data_name  = isset($field_meta['data_name']) ? $field_meta['data_name'] : '';
	$the_option = isset($option['option']) ? $option['option'] : '';
	$field_type = isset($field_meta['type']) ? $field_meta['type'] : '';
	
	switch($field_type) {
		case 'image':
			$the_option = isset($option['title']) ? $option['title'] : '';
			$option['id'] = sanitize_key($the_option);
		break;
		
		case 'imageselect':
			$the_option = isset($option['title']) ? $option['title'] : '';
		break;
	}
	
	
	$default_option = is_null($data_name) ? $the_option : $data_name.'_'.$the_option;
	
	$option_id = empty($option['id']) ? $default_option : $option['id'];

	return apply_filters('wpcomment_option_id', sanitize_key( $option_id ), $option, $data_name );
}

function wpcomment_get_price_including_tax( $price, $product ) {
	
	if(  'incl' !== get_option( 'woocommerce_tax_display_shop' ) ) return $price;
	
	$line_price   = $price;
	$return_price = $line_price;

	$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
	$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
	$tax_amount   = WC_Tax::get_tax_total( $taxes );
	$return_price = round( $line_price + $tax_amount, wc_get_price_decimals() );
	return $return_price;
	
	if ( $product->is_taxable() ) {
		if ( ! wc_prices_include_tax() ) {
			$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
			$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
			$tax_amount   = WC_Tax::get_tax_total( $taxes );
			$return_price = round( $line_price + $tax_amount, wc_get_price_decimals() );
		} else {
			$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
			$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

			/**
			 * If the customer is excempt from VAT, remove the taxes here.
			 * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
			 */
			if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
				$remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
				$remove_tax   = array_sum( $remove_taxes );
				$return_price = round( $line_price - $remove_tax, wc_get_price_decimals() );

			/**
			 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
			 * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
			 * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
			 */
			} elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
				$base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
				$modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );
				$return_price = round( $line_price - array_sum( $base_taxes ) + wc_round_tax_total( array_sum( $modded_taxes ), wc_get_price_decimals() ), wc_get_price_decimals() );
			}
		}
	}
	return apply_filters( 'wpcomment_get_price_including_tax', $return_price, $product);
}

// Check if field conditionally hidden
function wpcomment_is_field_hidden_by_condition( $field_name, $conditionally_hidden=null ) {
	
	if( !isset($_POST['wp-comment-fields']['conditionally_hidden']) && $conditionally_hidden == null ) return false;
	
	$conditionally_hidden = isset($_POST['wp-comment-fields']['conditionally_hidden']) ? sanitize_text_field($_POST['wp-comment-fields']['conditionally_hidden']) : $conditionally_hidden;
	
	$wpcomment_is_hidden = false;
	
	$wpcomment_hidden_fields = explode(",", $conditionally_hidden );
	// Remove duplicates
	$wpcomment_hidden_fields = array_unique( $wpcomment_hidden_fields );
	
	if( in_array($field_name, $wpcomment_hidden_fields) ) {
		
		$wpcomment_is_hidden = true;
	}
	
	return apply_filters('wpcomment_is_field_hidden_by_condition', $wpcomment_is_hidden);
}

// Return thumbs size
function wpcomment_get_thumbs_size() {
	
	return apply_filters('wpcomment_thumbs_size', '100px');
}

// Return file size in kb
function wpcomment_get_filesize_in_kb( $file_name ) {
		
	$base_dir = wpcomment_get_dir_path();
	$file_path = $base_dir . 'confirmed/' . $file_name;
	
	if (file_exists($file_path)) {
		$size = filesize ( $file_path );
		return round ( $size / 1024, 2 ) . ' KB';
	}elseif(file_exists( $base_dir . '/' . $file_name ) ){
		$size = filesize ( $base_dir . '/' . $file_name );
		return round ( $size / 1024, 2 ) . ' KB';
	}
	
}


// Generating html for file input and cropper in order meta from filename
function wpcomment_generate_html_for_files( $file_names, $input_type, $item ) {
	$file_name_array = explode(',', $file_names);
	
	$order_html = '<table>';
	foreach($file_name_array as $file_name) {
		
			$file_edit_path = wpcomment_get_dir_path('edits').wpcomment_file_get_name($file_name, $item->get_product_id());
			
			// Making file thumb download with new path
			$wpcomment_file_url = wpcomment_get_file_download_url( $file_name, $item->get_order_id(), $item->get_product_id());
			$wpcomment_file_thumb_url = wpcomment_is_file_image($file_name) ? wpcomment_get_dir_url(true) . $file_name : WPCOMMENT_URL.'/images/file.png';
			$order_html .= '<tr><td><a href="'.esc_url($wpcomment_file_url).'">';
			$order_html .= '<img class="img-thumbnail" style="width:'.esc_attr(wpcomment_get_thumbs_size()).'" src="'.esc_url($wpcomment_file_thumb_url).'">';
			$order_html .= '</a></td>';
			
	
			
			// Requested by Kevin, hiding downloading file button after order on thank you page
			// @since version 16.6
			if( is_admin() ) {
				$order_html .= '<td><a class="button" href="'.esc_url($wpcomment_file_url).'">';
				$order_html .= __('Download File', "wpcomment");
				$order_html .= '</a></td>';
			}
			$order_html .= '</tr>';
			
			if( $input_type == 'cropper' ) {
				
					$cropped_file_name = wpcomment_file_get_name($file_name, $item->get_product_id());
					$cropped_url = wpcomment_get_dir_url() . 'cropped/' . $cropped_file_name;
					$order_html .= '<tr><td><a href="'.esc_url($cropped_url).'">';
					$order_html .= '<img style="width:'.esc_attr(wpcomment_get_thumbs_size()).'" class="img-thumbnail" src="'.esc_url($cropped_url).'">';
					$order_html .= '</a></td>';
					
					// Requested by Kevin, hiding downloading file button after order on thank you page
					// @since version 16.6
					if( is_admin() ) {
						$order_html .= '<td><a class="button" href="'.esc_url($cropped_url).'">';
						$order_html .= __('Cropped', "wpcomment");
						$order_html .= '</a></td>';
					}
					$order_html .= '</tr>';
					
			} elseif( file_exists($file_edit_path) ) {
				
				$edit_file_name = wpcomment_file_get_name($file_name, $item->get_product_id());
				$edit_url = wpcomment_get_dir_url() . 'edits/' . $edit_file_name;
				$edit_thumb_url = wpcomment_get_dir_url() . 'edits/thumbs/' . $file_name;
				$order_html .= '<tr><td><a href="'.esc_url($edit_url).'">';
				$order_html .= '<img style="width:'.esc_attr(wpcomment_get_thumbs_size()).'" class="img-thumbnail" src="'.esc_url($edit_thumb_url).'">';
				$order_html .= '</a></td>';
				$order_html .= '<td><a class="button" href="'.esc_url($edit_url).'">';
				$order_html .= __('Edited', "wpcomment");
				$order_html .= '</a></td></tr>';
			}
	}
	$order_html .= '</table>';
	
	return apply_filters('wpcomment_order_files_html', $order_html, $file_names, $input_type, $item);
}


// return html for images selected
function wpcomment_generate_html_for_images( $field_meta, $images ) {
	
	
	$wpcomment_html	=  '<p class="wpcomment-img-wrapper">';
	foreach($images as $id => $images_meta) {
		
		$images_meta	= json_decode(stripslashes($images_meta), true);
		// wpcomment_pa($images_meta);
		$image_link		= stripslashes($images_meta['link']);
		$image_label	= isset($images_meta['raw']) ? $images_meta['raw'] : '';
		$wpcomment_html .= '<a href="'.esc_url($image_link).'" class="wpcomment-image-link" title="'.esc_attr($image_label).'">';
		$wpcomment_html	.= '<img class="wpcomment-thumbnail" style="width:'.esc_attr(wpcomment_get_thumbs_size()).'" src="'.esc_url($image_link).'" title="'.esc_attr($image_label).'">';
		$wpcomment_html .= '</a>';
		$wpcomment_html .= '<p class="wpcomment-img-title">'.esc_html($field_meta['title']).'</p>';
	}
	
	$wpcomment_html .= '</p>';
	
	return apply_filters('wpcomment_images_html', $wpcomment_html, $images);
}

// Getting field option price
function wpcomment_get_field_option_price( $field_meta, $option_label ) {
	
	// var_dump($field_meta['options']);
	if( ! isset( $field_meta['options']) || $field_meta['type'] == 'bulkquantity' || $field_meta['type'] == 'cropper' ) return 0;
	
	$option_price = 0;
	foreach( $field_meta['options'] as $option ) {
		
		if( isset($option['option']) && $option['option'] == $option_label && isset($option['price']) && $option['price'] != '' ) {
			
			$option_price = $option['price'];
		}
	}
	
	// For currency switcher
	$option_price = apply_filters('wpcomment_option_price', $option_price);
	
	return apply_filters("wpcomment_field_option_price", wc_format_decimal($option_price), $field_meta, $option_label);
}

// check if PPOM PRO is installed
function wpcomment_pro_is_installed() {
	
	$return = false;
	    
    if( class_exists('wpcomment_PRO') ) 
        $return = true;
    return $return;
}

// Check if field is visible
function wpcomment_is_field_visible( $field ) {
	
	if( ! wpcomment_pro_is_installed() ) return true;
	// wpcomment_pa($field);
	
	$visibility      = isset($field['visibility']) ? $field['visibility'] : 'everyone';
	
	$visibility_role = isset($field['visibility_role']) ? $field['visibility_role'] : '';
	
	$is_visible = false;
	switch( $visibility ) {
		
		case 'everyone':
			$is_visible = true;
			break;
			
		case 'members':
			if( is_user_logged_in() ) {
				$is_visible = true;
			}
			break;
			
		case 'guests':
			if( ! is_user_logged_in() ) {
				$is_visible = true;
			}
			break;
			
		case 'roles':
			$user_roles = wpcomment_get_current_user_role();
			$allowed_roles = explode(',', $visibility_role);
			
			$restult = array_intersect($allowed_roles, $user_roles);
			if( !empty($restult) ) {
				$is_visible = true;
			}
			break;
	}
	
	return apply_filters('wpcomment_is_field_visible', $is_visible, $field);
	
}

// Get logged in user role
function wpcomment_get_current_user_role() {
  
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		return ( array ) $user->roles;
	} else {
		return array();
	}
}

function wpcomment_get_date_formats() {
	
	$formats = array (
						'mm/dd/yy' => 'Default - mm/dd/yyyy',
						'dd/mm/yy' => 'dd/mm/yyyy',
						'yy-mm-dd' => 'ISO 8601 - yy-mm-dd',
						'd M, y' => 'Short - d M, y',
						'd MM, y' => 'Medium - d MM, y',
						'DD, d MM, yy' => 'Full - DD, d MM, yy',
						'\'day\' d \'of\' MM \'in the year\' yy' => 'With text - \'day\' d \'of\' MM \'in the year\' yy',
						'\'Month\' MM \'day\' d \'in the year\' yy' => 'With text - \'Month\' January \'day\' 7 \'in the year\' yy'
				);
				
	return apply_filters('wpcomment_date_formats', $formats);
}

// PPOM Get settings
function wpcomment_get_option($key, $default_val=false) {
	
	// $value = wpcomment_SettingsFramework::get_saved_settings($key, $default_val);
	$value = '';
	return $value;
}

// Checking PPOM version
function wpcomment_get_version() {
	
	if( ! defined('WPCOMMENT_VERSION') ) return 3.0;
	return floatval( WPCOMMENT_VERSION );
}

// Checking PPOM Pro version
function wpcomment_get_pro_version() {
	
	if( ! defined('wpcomment_PRO_VERSION') ) return 3.0;
	return floatval( wpcomment_PRO_VERSION );
}

// wp_is_mobile wrapper
function wpcomment_is_mobile() {
	
	if( ! function_exists('wp_is_mobile') ) return false;
	
	return wp_is_mobile();
}

// check client side validation
function wpcomment_is_client_validation_enabled() {
	
	$validation = false;
	if( WPCOMMENT_ADMIN::get_option('wpcomment_validation', 'yes') == 'yes' ) $validation = true;
	
	return apply_filters('wpcomment_is_client_validation_enabled', $validation);
}

// generating wpcomment conditional data attributes
function wpcomment_get_conditional_data_attributes( $meta ) {
	
	$logic			= isset($meta['logic']) ? wpcomment_wpml_translate($meta['logic'], 'WPCOMMENT') : '';
	$conditions		= isset($meta['conditions']) ? wpcomment_wpml_translate($meta['conditions'], 'WPCOMMENT') : '';
	$type			= isset($meta['type']) ? wpcomment_wpml_translate($meta['type'], 'WPCOMMENT') : '';
	
	$attr_html = '';
	
	$attr_html .= ' data-type="'.esc_attr($type).'"';
	// wpcomment_pa($conditions);
	
	
	if( isset($conditions['rules']) && $logic === 'on' ) {
		
		$bound		= isset($conditions['bound']) ? wpcomment_wpml_translate($conditions['bound'], 'WPCOMMENT') : '';
		$visibility	= isset($conditions['visibility']) ? wpcomment_wpml_translate($conditions['visibility'], 'WPCOMMENT') : '';
		
		$attr_html .= ' data-cond="1"';
		$attr_html .= ' data-cond-total="'.esc_attr(count($conditions['rules'])).'"';
		$attr_html .= ' data-cond-bind="'.esc_attr($bound).'"';
		$attr_html .= ' data-cond-visibility="'.esc_attr(strtolower($visibility)).'"';
		
		$index = 0;
		foreach($conditions['rules'] as $rule){
			
			$counter	= ++$index;
			$input		= "input".$counter;
			$value		= "val".$counter;
			$opr		= "operator".$counter;
			$element	= isset($rule['elements']) ? wpcomment_wpml_translate($rule['elements'], 'WPCOMMENT') : '';
			$element_val= isset($rule['element_values']) ? wpcomment_wpml_translate($rule['element_values'], 'WPCOMMENT') : '';
			$operator	= isset($rule['operators']) ? wpcomment_wpml_translate($rule['operators'], 'WPCOMMENT') : '';
			$attr_html .= ' data-cond-'.$input.'="'.esc_attr($element).'"';
			$attr_html .= ' data-cond-'.$value.'="'.esc_attr($element_val).'"';
			$attr_html .= ' data-cond-'.$opr.'="'.esc_attr($operator).'"';
		}
	}
	
	return apply_filters('wpcomment_field_conditions', $attr_html, $meta);
}

// Check if given type is an addon
function wpcomment_is_field_addon($type){
	
	$wpcomment_meta = wpcomment_get_plugin_meta();
	
	$is_addon = false;
	if( isset($wpcomment_meta[$type]) && $wpcomment_meta[$type]['is_addon']) $is_addon = true;
	
	return $is_addon;
}

function wpcomment_is_legacy_mode(){
	
	$is_legacy = false;
	$enable_legacy = wpcomment_get_option('wpcomment_enable_legacy_inputs_rendering');
	if ($enable_legacy == 'yes') {
		$is_legacy = true;
	}
	
	return $is_legacy;
}

/**
 * Return cols for inputs
*/
function wpcomment_get_input_cols() {
	
	$ppom_cols = array(
		2 => '2 Col',
		3 => '3 Col', 
		4 => '4 Col',
		5 => '5 Col',
		6 => '6 Col',
		7 => '7 Col',
		8 => '8 Col',
		9 => '9 Col',
		10 => '10 Col',
		11 => '11 Col',
		12 => '12 Col'
	);
	
	return apply_filters('ppom_field_cols', $ppom_cols);
}

/**
 * Return visibility options for inputs
*/
function wpcomment_field_visibility_options() {
	
	$visibility_options = array(
		'everyone'	=> __('Everyone'),
		'guests'	=> __('Only Guests'),
		'members'	=> __('Only Members'),
		'roles'		=> __('By Role')
	);
								
	return apply_filters('ppom_field_visibility_options', $visibility_options);
}

/**
 * Get timezone list
*/
function wpcomment_array_get_timezone_list($selected_regions, $show_time) {
	
	if( $selected_regions == 'All' ) {
	    $regions = array(
	        DateTimeZone::AFRICA,
	        DateTimeZone::AMERICA,
	        DateTimeZone::ANTARCTICA,
	        DateTimeZone::ASIA,
	        DateTimeZone::ATLANTIC,
	        DateTimeZone::AUSTRALIA,
	        DateTimeZone::EUROPE,
	        DateTimeZone::INDIAN,
	        DateTimeZone::PACIFIC,
	    );
	} else {
		$selected_regions = explode(",", $selected_regions);
		$tz_regions = array();
		
		foreach($selected_regions as $region) {
			
			switch($region) {
				case 'AFRICA':
					$tz_regions[] = DateTimeZone::AFRICA;
				break;
				case 'AMERICA':
					$tz_regions[] = DateTimeZone::AMERICA;
				break;
				case 'ANTARCTICA':
					$tz_regions[] = DateTimeZone::ANTARCTICA;
				break;
				case 'ASIA':
					$tz_regions[] = DateTimeZone::ASIA;
				break;
				case 'ATLANTIC':
					$tz_regions[] = DateTimeZone::ATLANTIC;
				break;
				case 'AUSTRALIA':
					$tz_regions[] = DateTimeZone::AUSTRALIA;
				break;
				case 'EUROPE':
					$tz_regions[] = DateTimeZone::EUROPE;
				break;
				case 'INDIAN':
					$tz_regions[] = DateTimeZone::INDIAN;
				break;
				case 'PACIFIC':
					$tz_regions[] = DateTimeZone::PACIFIC;
				break;
			}
		}
		$regions = $tz_regions;
	}
	
    $timezones = array();
    foreach( $regions as $region ) {
        $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
    }

    $timezone_offsets = array();
    foreach( $timezones as $timezone ) {
        $tz = new DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by timezone name
    ksort($timezone_offsets);

    $timezone_list = array();
    foreach( $timezone_offsets as $timezone => $offset ) {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate( 'H:i', abs($offset) );

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
        
        $t = new DateTimeZone($timezone);
        $c = new DateTime(null, $t);
        $current_time = $c->format('g:i A');

		if( $show_time == 'on' ) {
        	$timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
		} else {
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
		}
    }

    return $timezone_list;
}

function wpcomment_get_saved_meta() {
	$wpcomment_meta = get_option('wpcomment_meta_fields');
	
	// legacy meta
	if (!$wpcomment_meta) {
		$wpcomment_meta = wpcomment_get_saved_meta_legacy();
		if (!$wpcomment_meta) {
			$wpcomment_meta = [];
		}
	} else {
		$wpcomment_meta = json_decode($wpcomment_meta, true);
		
		// Check if the decoded value is an array
		if (!is_array($wpcomment_meta)) {
			$wpcomment_meta = [];
		}
	}
	
	return $wpcomment_meta;
}

// get and convert legacy comment meta fields
function wpcomment_get_saved_meta_legacy(){
	
	$wpcomment_meta = get_option('wpcomment_meta');
	
	if( !$wpcomment_meta ) return null;
	
	// adding status key
	$wpcomment_meta = array_map(function($c){
		$m = $c;
		$m['status']	= 'on';
		if(isset($m['options'])){
			$options = explode("\n", $m['options']);	
			// wpcomment_pa($options);
			$options	= array_map(function($opt){
				$o['option'] = $opt;
				$o['id'] = sanitize_key($opt);
				return $o;
			}, $options);
			$m['options'] = $options;
			// $m['options'] = wpcomment_convert_options_to_key_val($options, $wpcomment_meta);
		}
		return $m;
	}, $wpcomment_meta);
	
	return $wpcomment_meta;
}

/**
 * just rendering a thumb box
 * 
 **/
function wpcomment_render_images_legacy($image_name) {
	
	$is_thumb = true;
	$legacy = true;
	$thumb_url	= wpcomment_get_dir_url($is_thumb,$legacy) . $image_name;
	$file_url	= wpcomment_get_dir_url() . $image_name;
	
	return apply_filters('wpcomment_imagethumb', sprintf(__('<a href="%s"><img width="75" src="%s" title="%s" /></a>', 'nm-wpcomments'), $file_url, $thumb_url, $image_name), $image_name);
}

/**
 * just renderin file name with link
 **/
function wpcomment_file_link_legacy($file_name) {
	
	$is_thumb = false;
	$legacy = true;
	$file_url = wpcomment_get_dir_url($is_thumb,$legacy) . $file_name;
	return apply_filters('wpcomment_filelink', sprintf(__('<a href="%s"><img width="75" src="%s" title="%s" /></a>', 'nm-wpcomments'), $file_url,$thumb_url, $file_name), $file_name, $thumb_url);
}

// show wpcomment meta fields for given meta against comment
function wpcomment_render_comment_meta($comment_meta){
	
	$output = wpcomment_output_meta($comment_meta['fields']);
	// wpcomment_pa($output);
	
	$comment = '<ul class="wpcomment-ul">';
	foreach ( $output as $o ) {
		
		$display = isset($o['display']) ? $o['display'] : $o['value'];
		$type	 = isset($o['type']) ? $o['type'] : '';
		
		
		// if meta type is image remove the title
		if( ! in_array($type, ['image','file']) ){
			$comment .= '<li class="wpcomment-li"><strong>' . esc_attr($o['name']) . '</strong> - ';
			$comment .= wp_kses_post( $display ) . '</li>';
		} else{
			$comment .= '<li class="wpcomment-li"> ' . wp_kses_post( $display ) . '</li>';
		}
	}
	
	$comment .= '</ul>';
	
	return $comment;
}

function wpcomment_disable_extra_fields(){
	$disable_extrafields = WPCOMMENT_ADMIN()::get_option('wpcomment_disable_frontend');
	$disable_extrafields = $disable_extrafields === 'on' ? true : false;
	return apply_filters('wpcomment_show_extra_fields', $disable_extrafields);
}