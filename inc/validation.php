<?php
/**
 * WordPress Escaping and Sanitization
 **/
 
// esc html content before rendering
function wpcomment_esc_html($content) {
	
	global $allowedposttags;
	$allowed_atts = array(
		'align'      => array(),
		'class'      => array(),
		'type'       => array(),
		'id'         => array(),
		'dir'        => array(),
		'lang'       => array(),
		'style'      => array(),
		'xml:lang'   => array(),
		'src'        => array(),
		'alt'        => array(),
		'href'       => array(),
		'rel'        => array(),
		'rev'        => array(),
		'target'     => array(),
		'novalidate' => array(),
		'type'       => array(),
		'value'      => array(),
		'name'       => array(),
		'tabindex'   => array(),
		'action'     => array(),
		'method'     => array(),
		'for'        => array(),
		'width'      => array(),
		'height'     => array(),
		'data'       => array(),
		'data-metatype'       => array(),
		'data-meta-id'       => array(),
		'data-opt-index'       => array(),
		'data-condition-type'       => array(),
		'title'      => array(),
	);
	$allowedposttags['form']     = $allowed_atts;
	$allowedposttags['label']    = $allowed_atts;
	$allowedposttags['input']    = $allowed_atts;
	$allowedposttags['select']    = $allowed_atts;
	$allowedposttags['option']    = $allowed_atts;
	$allowedposttags['textarea'] = $allowed_atts;
	$allowedposttags['iframe']   = $allowed_atts;
	$allowedposttags['script']   = $allowed_atts;
	$allowedposttags['style']    = $allowed_atts;
	$allowedposttags['strong']   = $allowed_atts;
	$allowedposttags['small']    = $allowed_atts;
	$allowedposttags['table']    = $allowed_atts;
	$allowedposttags['span']     = $allowed_atts;
	$allowedposttags['abbr']     = $allowed_atts;
	$allowedposttags['code']     = $allowed_atts;
	$allowedposttags['pre']      = $allowed_atts;
	$allowedposttags['div']      = $allowed_atts;
	$allowedposttags['img']      = $allowed_atts;
	$allowedposttags['h1']       = $allowed_atts;
	$allowedposttags['h2']       = $allowed_atts;
	$allowedposttags['h3']       = $allowed_atts;
	$allowedposttags['h4']       = $allowed_atts;
	$allowedposttags['h5']       = $allowed_atts;
	$allowedposttags['h6']       = $allowed_atts;
	$allowedposttags['ol']       = $allowed_atts;
	$allowedposttags['ul']       = $allowed_atts;
	$allowedposttags['li']       = $allowed_atts;
	$allowedposttags['em']       = $allowed_atts;
	$allowedposttags['hr']       = $allowed_atts;
	$allowedposttags['br']       = $allowed_atts;
	$allowedposttags['tr']       = $allowed_atts;
	$allowedposttags['td']       = $allowed_atts;
	$allowedposttags['p']        = $allowed_atts;
	$allowedposttags['a']        = $allowed_atts;
	$allowedposttags['b']        = $allowed_atts;
	$allowedposttags['i']        = $allowed_atts;
	
	// $allowed_tags = wp_kses_allowed_html('post');
	
	return wp_kses(stripslashes_deep($content), $allowedposttags);
}

// sanitization array data before saving data
function wpcomment_sanitize_array_data($array) {
    foreach ( $array as $key => &$value ) {
        if ( is_array( $value ) ) {
            $array[$key] = wpcomment_sanitize_array_data($value);
        }
        else {
            if( in_array($key, wpcomment_fields_with_html()) ){
                $array[$key] = wpcomment_esc_html($value);
            }else{
            	$array[$key] = sanitize_text_field( $value );
            }
        }
    }
    return $array;
}


// wpcomment_fields keys requires html
function wpcomment_fields_with_html() {
    
    $have_html = ['description', 'heading', 'html','checked'];
    return apply_filters('wpcomment_fields_with_html', $have_html);
}