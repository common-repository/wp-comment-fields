<?php
/**
 * Frontend Class
 **/
 
class WPComment_Frontend {
    
    /**
	 * the static object instace
	 */
	private static $ins = null;
	
	
	public static function get_instance()
	{
		// create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
    
    function __construct() {
        
        /*
		 * Action hooks for comment fields
		*/
		add_action( 'comment_form_after_fields', array( $this, 'render_input_fields' ) );
		add_action( 'comment_form_logged_in_after', array( $this, 'render_input_fields' ) );
		
	    /**
	     * Saving comment extra fields
	     **/
	    add_action( 'comment_post', array( $this, 'save_comment_meta_fields' ) );
	    
	    /**
		 * adding comment meta in front view
		 */
		//add_filter( 'comment_text', array( $this, 'render_comment_meta_front_legacy' ), 100 );
		add_filter( 'comment_text', array( $this, 'render_comment_meta_front' ), 100, 2 );
		
		// ajax callbacks
		add_action('wp_ajax_wpcomment_upload_file', 'wpcomment_upload_file');
		add_action('wp_ajax_nopriv_wpcomment_upload_file', 'wpcomment_upload_file');
		add_action('wp_ajax_wpcomment_delete_file', 'wpcomment_delete_file');
		add_action('wp_ajax_nopriv_wpcomment_delete_file', 'wpcomment_delete_file');
    }
    
    // rendering fields above the Comment form as input
    function render_input_fields() {
    	
    	$allow_posts = apply_filters('wpcomment_allowed_posts', ['post']);
        
        if ( ! in_array(get_post_type(), $allow_posts) && ! is_admin() ) return;
        
        $form_obj = new WPComment_Form;
        
        // wpcomment_pa($form_obj::$fields);
        
    	// Check if PPOM fields is empty
    	if( ! $form_obj::$fields ) return '';
    	 
    	$wpcomment_html = '';
    	$template_vars = ['form_obj' => $form_obj];
        
        ob_start();
        	wpcomment_load_input_templates( 'frontend/wpcomment-fields.php', $template_vars );
        $wpcomment_html .= ob_get_clean();
    	
    	echo apply_filters('wpcomment_fields_html', $wpcomment_html, $form_obj);
    }
    
    
    function save_comment_meta_fields($comment_id){
        
        // wpcomment_pa($_POST); exit;
        
        if( !isset($_POST['wpcomment']) ) return '';
        
        add_comment_meta( $comment_id, 'wpcomment_fields', $_POST['wpcomment'] );
    }
    
    
    function render_comment_meta_front( $comment_text, $comment ) {
    	
    	// if admin disable frontend extra fields
    	if( wpcomment_disable_extra_fields() ) return $comment_text;
    	
        $comment_meta_text = $this->get_comment_meta_by_comment_id($comment->comment_ID);
		if($comment_meta_text){
			$comment_text .= $comment_meta_text;
		}
		
		return $comment_text;
	}
	
	// getting comment meta by comment id for global use
	function get_comment_meta_by_comment_id($comment_id){
		
		$comment_meta = get_comment_meta( $comment_id, 'wpcomment_fields', true );
        
        $comment_text = '';
        
        // if wpcomment_fields found then it is latest version
        if( isset($comment_meta['fields']) ) {
        	$comment_text = $this->comment_fields($comment_meta);
        }else{
        	$comment_text = $this->comment_fields_legacy();
        }
        
        return apply_filters('wpcomment_comment_meta', $comment_text);
	}
	
	function comment_fields($comment_meta) {
		
        // wpcomment_pa($comment_meta);
		
		$comment = '';
		if ( $comment_meta ) {
			$comment_meta_heading = WPCOMMENT_ADMIN()::get_option('wpcomment_heading');
			if ( $comment_meta_heading != '' ) {
				$comment .= sprintf(__('<p class="wpcomment-meta">%s</p>', 'wp-comment-fields'), $comment_meta_heading);
			}
			
			$comment .= wpcomment_render_comment_meta($comment_meta);
		}
		
		return $comment;
	}
	
	function comment_fields_legacy() {

		$comment_meta = get_option( 'wpcomment_meta' );
		
		// ppom_pa($comment_meta);
		
		$comment = '';

		if($comment_meta){
			$comment_meta_heading = WPCOMMENT_ADMIN()::get_option('wpcomment_heading');
			if($comment_meta_heading != '')
				$comment = '<p><strong>'.sprintf(__('%s'), $comment_meta_heading).'</strong></p>';
				
			$comment .= '<ul>';
			foreach ($comment_meta as $index => $meta){
			
				$comment_meta_key = $meta['data_name'];
				$comment_meta_type = $meta['type'];
				$comment_title		= isset($meta['title']) ? stripslashes($meta['title']) : 'NO TITLE';
				$comment_meta_val = get_comment_meta(get_comment_ID(), $comment_meta_key, true);
				
				if($comment_meta_val != ''){
					
					switch( $comment_meta_type ) {
						case 'file':
							$comment_files = explode(',', $comment_meta_val);
							
							foreach($comment_files as $file) {
			            		if( wpcomment_is_file_image($file) ){
			            			echo wpcomment_render_images_legacy($file);
			            		}else{
			            			echo wpcomment_file_link_legacy($file);
			            		}
			            	}
						break;
						
						default:
							$comment .= '<li><strong>'.sprintf(__('%s: '), esc_attr($comment_title)).'</strong>';
							$comment .= sprintf(__('%s'), esc_attr($comment_meta_val)).'</li>';
						break;
						
					}
					
					//$comment .= ', ';
				}			
			}
			$comment .= '</ul>';
			//$comment = substr($comment, 0, -2);
		}

		return $comment;
	}
}

function WPCOMMENT_FRONTEND(){
    return WPComment_Frontend::get_instance();
}

WPCOMMENT_FRONTEND();