<?php
/*
 * working behind the seen
 */

/* 
**========== Direct access not allowed =========== 
*/ 
if( ! defined('ABSPATH') ) die('Not Allowed');

class WPComment_Admin {

	private static $ins;
	private static $options;
	
	function __construct() {
		
		add_action ( 'admin_menu', array (
				$this,
				'add_comment_menu' 
		) );
		
		self::$options = get_option('wpcomment_settings', true);
		
		// Saving settings and fields
		add_action('wp_ajax_wpcomment_save_form_meta', array($this, 'save_meta_fields'));
		add_action('wp_ajax_wpcomment_save_settings', array($this, 'save_form_settings'));
		
		/*
		 * adding meta box in comments edit page
		 */
		add_action( 'add_meta_boxes_comment', array( $this, 'comment_meta_box' ), 1 );
		
		
		// adding wpml support for PPOM Settings
    	// add_filter('woocommerce_admin_settings_sanitize_option', array($this, 'wpcomment_setting_wpml'), 10, 3); 
    	
    	// add_action( 'woocommerce_admin_field_wpcomment_multi_select', array( $this, 'wpcomment_multi_select_role_setting' ),2,10 );
    	
	}
	
	public static function get_instance() {
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
    
    public static function get_option($k, $default=null){
    	
    	return isset(self::$options[$k]) ? self::$options[$k] : $default;
    }

	
	/*
	 * creating menu page for this plugin
	*/
	function add_comment_menu() {
		
	    add_comments_page( __( 'Comment Fields', 'wp-comment-fields' ), 
	    					__( 'Comment Fields', 'wp-comment-fields' ), 
	    						'manage_options', 'wpcomment', 
	    						array($this, 'comment_meta') );
	}
	

	/*
	 * CALLBACKS
	*/
	function comment_meta() {
		
		$wpcomment_inputs = $this->get_all_inputs();
		
		ob_start();
		wpcomment_load_template ( 'admin/settings-home.php', ['wpcomment_inputs'=>$wpcomment_inputs] );
		echo ob_get_clean();
	}
	
	/*
	 * returning NM_Inputs object
	*/
	function get_all_inputs() {
	
		$nm_inputs = WPComment_Inputs();
		// registering all inputs here
	
		$all_inputs = array (
				
				'text' 		=> $nm_inputs->get_input ( 'text' ),
				'textarea' 	=> $nm_inputs->get_input ( 'textarea' ),
				'select' 	=> $nm_inputs->get_input ( 'select' ),
				'radio' 	=> $nm_inputs->get_input ( 'radio' ),
				'checkbox' 	=> $nm_inputs->get_input ( 'checkbox' ),
		);
		
		return apply_filters('wpcomment_all_inputs', $all_inputs, $nm_inputs);
	}
	
	
	/*
	 * saving form meta in admin call
	 */
	function save_meta_fields() {
	    // Verify nonce
	    if ( ! isset( $_REQUEST['wpcomment_nonce'] ) || ! wp_verify_nonce( $_REQUEST['wpcomment_nonce'], 'wpcomment_save_form_meta_nonce' ) ) {
	        wp_send_json_error( 'Invalid nonce.' );
	    }
	
	    // Check user capabilities
	    if ( ! current_user_can( 'manage_options' ) ) {
	        wp_send_json_error( 'Insufficient permissions.' );
	    }
	
	    // Proceed with processing the request
	    $wpcomment_meta = isset( $_REQUEST['wpcomment'] ) ? $_REQUEST['wpcomment'] : '';
	    $wpcomment_meta = apply_filters( 'wpcomment_meta_data_saving', $wpcomment_meta );
	    $wpcomment_meta = wpcomment_sanitize_array_data( $wpcomment_meta );
	
	    // Save meta fields
	    update_option( 'wpcomment_meta_fields', json_encode( $wpcomment_meta ) );
	
	    // Prepare response
	    $args = array( 'page' => 'wpcomment' );
	    $redirect_to = add_query_arg( $args, admin_url( 'admin.php' ) );
	
	    $resp = array(
	        'message'     => __( 'Fields added successfully', 'wp-comment-fields' ),
	        'status'      => 'success',
	        'redirect_to' => $redirect_to,
	    );
	
	    wp_send_json( $resp );
	}

	
	// Saving Global settings
	function save_form_settings(){
		
		// Verify nonce
	    if ( ! isset( $_REQUEST['wpcomment_nonce'] ) || ! wp_verify_nonce( $_REQUEST['wpcomment_nonce'], 'wpcomment_save_form_meta_nonce' ) ) {
	        wp_send_json_error( 'Invalid nonce.' );
	    }
	
	    // Check user capabilities
	    if ( ! current_user_can( 'manage_options' ) ) {
	        wp_send_json_error( 'Insufficient permissions.' );
	    }
		
		$keys = ['wpcomment_heading','wpcomment_cpt','wpcomment_disable_frontend'];
		
		$settings = [];
		foreach($keys as $k){
			if( ! isset($_POST[$k]) ) continue;
			$settings[$k] = sanitize_text_field($_POST[$k]);
		}
		
		update_option('wpcomment_settings', $settings);
		
		$resp = array (
				'message'		=> __ ( 'Settings saved successfully', 'wp-comment-fields' ),
				'status'		=> 'success',
		);
		
		wp_send_json($resp);
	}
	
	/**
	 * rendering comment meta BOX in comments admin page
	 */
	function comment_meta_box( $comment ) {

		add_meta_box( 'wpcomment_mb_show', __( 'Comment Extra Fields' ), array( $this, 'show_comment_fields' ), 'comment', 'normal' );
	}
	
	/**
	 * rendering comment META in comments admin page
	 */
	function show_comment_fields( $comment ) {
		
		$comment_output = WPCOMMENT_FRONTEND()->get_comment_meta_by_comment_id(get_comment_ID());
		
		// $comment_meta = get_comment_meta( get_comment_ID(), 'wpcomment_fields', true );
		// $comment_output = wpcomment_render_comment_meta($comment_meta);
		
		if($comment_output){
			echo apply_filters('wpcomment_meta_admin', $comment_output, $comment);
		}
	}
	
	
	
	function wpcomment_setting_wpml($value, $option, $raw_value) {
    	
    	if( isset($option['type']) && isset($option['type']) == 'text' ) {
    		$value = wpcomment_wpml_translate($value, 'PPOM');
    	}
    	
    	return $value;
    }
}

function WPCOMMENT_ADMIN(){
    return WPComment_Admin::get_instance();
}
if( is_admin() ) {
	WPCOMMENT_ADMIN();
}