<?php
/**
 * PPOM Fields Manager Class
**/

/* 
**========== Direct access not allowed =========== 
*/ 
if( ! defined('ABSPATH') ) die('Not Allowed');
 

 class WPComment_Fields_Meta {
 
    private static $ins;
    

    function __construct() {
              
        add_action('admin_enqueue_scripts', array($this, 'load_script'));
    }
    

    public static function get_instance() {
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
    

    /* 
    **============ Load all scripts =========== 
    */ 
    function load_script($hook) {

		if( ! isset($_GET['page']) || $_GET['page'] != "wpcomment") return;
		
		$suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        
        // Preloader script
        wp_enqueue_script('wpcomment-perload', WPCOMMENT_URL."/js/admin/pre-load.js", array('jquery'), WPCOMMENT_VERSION, true);
        
        // Bootstrap Files
        wp_enqueue_style('wpcomment-bs', WPCOMMENT_URL."/css/bootstrap/bootstrap.min.css");

        // PPOM Meta Table File
        // wp_enqueue_script('wpcomment-meta-table', WPCOMMENT_URL."/js/admin/wpcomment-meta-table.js", array('jquery'), WPCOMMENT_VERSION, true);
        
        // Font-awesome File
        if( wpcomment_load_fontawesome() ) {
        	wp_enqueue_style('wpcomment-fontawsome', WPCOMMENT_URL."/css/font-awesome/css/font-awesome.min.css");
        }

        // Swal Files
        wp_enqueue_style('wpcomment-swal', WPCOMMENT_URL."/css/sweetalert.css");
        wp_enqueue_script('wpcomment-swal', WPCOMMENT_URL."/js/sweetalert.js", array('jquery'), WPCOMMENT_VERSION, true); 
        
        // Select2 Files
        wp_enqueue_style('wpcomment-select2', WPCOMMENT_URL."/css/select2.css");
        wp_enqueue_script('wpcomment-select2', WPCOMMENT_URL."/js/select2.js", array('jquery'), WPCOMMENT_VERSION, true);
        
        if (isset($_GET['do_meta']) && $_GET['do_meta'] == 'edit') {
        	
	        // CSS Code Editor Files
	    	wp_enqueue_style('wpcomment-codemirror-theme', WPCOMMENT_URL."/css/codemirror-theme.css");
	    	$css_code_editor = wp_enqueue_code_editor(array( 'type' => 'text/css' ));
	    	// wpcomment_pa($css_code_editor); exit;
			if (false !== $css_code_editor) {
	    		$css_code_editor['codemirror']['autoRefresh'] = true;
	    		$css_code_editor['codemirror']['theme']       = 'darcula';
				wp_add_inline_script(
			        'code-editor',
			        sprintf(
			        	'jQuery( function() { wp.codeEditor.initialize( "wpcomment-css-editor", %s ); } );',
						wp_json_encode( $css_code_editor )
		        	)
				);
	    	}
	    	
	    	// Js Code Editor Files
	    	$js_code_editor = wp_enqueue_code_editor(array( 'type' => 'text/javascript' ));
			if (false !== $js_code_editor) {
	    		$js_code_editor['codemirror']['autoRefresh'] = true;
	    		$js_code_editor['codemirror']['theme']       = 'darcula';
				wp_add_inline_script(
			        'code-editor',
			        sprintf(
			        	'jQuery( function() { wp.codeEditor.initialize( "wpcomment-js-editor", %s ); } );',
						wp_json_encode( $js_code_editor )
		        	)
				);
	    	}
        }
        
        // Tabletojson JS File 
        wp_enqueue_script('wpcomment-tabletojson', WPCOMMENT_URL."/js/admin/jquery.tabletojson.min.js", array('jquery'), WPCOMMENT_VERSION, true);

        // Datatable Files
        // wp_enqueue_style('wpcomment-datatables', WPCOMMENT_URL."/js/datatable/datatables.min.css");
        // wp_enqueue_script('wpcomment-datatables', WPCOMMENT_URL."/js/datatable/jquery.dataTables.min.js", array('jquery'), WPCOMMENT_VERSION, true);

        // Description Tooltips JS File
        wp_enqueue_script('wpcomment-tooltip', WPCOMMENT_URL."/js/wpcomment-tooltip.js", array('jquery'), WPCOMMENT_VERSION, true);
		
		// Add the color picker css file       
        wp_enqueue_style( 'wp-color-picker' ); 
      
        // PPOM Admin Files
        wp_enqueue_style('wpcomment-field', WPCOMMENT_URL."/css/wpcomment-admin.css", '', WPCOMMENT_VERSION);
        wp_enqueue_script('wpcomment-field', WPCOMMENT_URL."/js/admin/wpcomment-admin.js", array('wpcomment-swal','wpcomment-select2','wpcomment-tabletojson','wpcomment-tooltip','jquery-ui-core', 'jquery-ui-sortable','wpcomment-perload','wp-color-picker'), WPCOMMENT_VERSION, true);

		wp_enqueue_media ();

        $wpcomment_admin_meta = array(
	      'plugin_admin_page' => admin_url( 'admin.php?page=wpcomment'),
	      'loader'  		=> WPCOMMENT_URL.'/images/loading.gif',
	    );

        // localize wpcomment_vars
	    wp_localize_script( 'wpcomment-field', 'wpcomment_vars', $wpcomment_admin_meta);
	    // wp_localize_script( 'wpcomment-meta-table', 'wpcomment_vars', $wpcomment_admin_meta);
    }


    /* 
    **============ Render all fields =========== 
    */
    function render_field_settings() {
        
        $html  = '';        
        $html .= '<div id="wpcomment-fields-wrapper">';
        foreach( WPCOMMENT_ADMIN()->get_all_inputs() as $fields_type => $meta ) {
           	
           	$field_title = isset($meta -> title) ? $meta -> title : null;
           	$field_desc  = isset($meta -> desc) ? $meta -> desc : null;
           	$settings    = isset($meta -> settings) ? $meta -> settings : array();

           	$settings = $this->wpcomment_tabs_panel_classes($settings);

            // new model
            $html .= '<div class="wpcomment-modal-box wpcomment-slider wpcomment-field-'.esc_attr($fields_type).'">';
			    $html .= '<header>';
			        $html .= '<h3>'.sprintf(__("%s","wpcomment"), $field_title).'</h3>';
			    $html .= '</header>';
			    $html .= '<div class="wpcomment-modal-body">';

			        $html .= $this->render_field_meta($settings, $fields_type);

			    $html .= '</div>';
			    $html .= '<footer>';
			    	$html .= '<span class="wpcomment-req-field-id"></span>';
                   	$html .= '<button type="button" class="btn btn-default wpcomment-close-checker wpcomment-close-fields wpcomment-js-modal-close" style="margin-right: 5px;">'.esc_html__( 'close', 'wp-comment-fields' ).'</button>';
                    $html .= '<button type="button" class="btn btn-primary wpcomment-field-checker wpcomment-add-field" data-field-type="'.esc_attr($field_title).'">'.esc_html__( 'Add Field', 'wp-comment-fields' ).'</button>';
			    $html .= '</footer>';
			$html .= '</div>';
        }

        $html .= '</div>';
        echo wpcomment_esc_html($html);
    }

    /* 
    **============ Render all fields meta =========== 
    */
    function render_field_meta($field_meta, $fields_type, $field_index='', $save_meta='') {
    	// wpcomment_pa($save_meta);
    	$html  = '';
       	$html .= '<div data-table-id="'.esc_attr($fields_type).'" class="row wpcomment-tabs wpcomment-fields-actions" data-field-no="'.esc_attr($field_index).'">';
       		$html .= '<input type="hidden" name="wpcomment['.$field_index.'][type]" value="'.$fields_type.'" class="wpcomment-meta-field" data-metatype="type">';
			$html .= '<div class="col-md-12 wpcomment-tabs-header">';
				

				$wpcomment_field_tabs = $this->wpcomment_fields_tabs($fields_type);
				foreach ($wpcomment_field_tabs as $tab_index => $tab_meta) {
					
					$tab_label  = isset($tab_meta['label']) ? $tab_meta['label'] : '';
					$tab_class  = isset($tab_meta['class']) ? $tab_meta['class'] : '';
					$tab_depend = isset($tab_meta['field_depend']) ? $tab_meta['field_depend'] : array();
					$not_allowed = isset($tab_meta['not_allowed']) ? $tab_meta['not_allowed'] : array();
					$tab_class  = implode(' ',$tab_class);

					if ( in_array('all', $tab_depend) && !in_array($fields_type, $not_allowed)) {
					
						$html .= '<label for="'.esc_attr($tab_index).'" id="'.esc_attr($tab_index).'" class="'.esc_attr($tab_class).'">'.$tab_label.'</label>';
					}else if( in_array($fields_type, $tab_depend) ){
						
						$html .= '<label for="'.esc_attr($tab_index).'" id="'.esc_attr($tab_index).'" class="'.esc_attr($tab_class).'">'.$tab_label.'</label>';
					}
				}

			
			$html .= '</div>';
        if ($field_meta) {
            
            foreach ($field_meta as $fields_meta_key => $meta) {
                
                $title      = isset($meta['title']) ? $meta['title'] : '';
                $desc       = isset($meta['desc']) ? $meta['desc'] : '';   
                $type       = isset($meta['type']) ? $meta['type'] : '';
                $link       = isset($meta['link']) ? $meta['link'] : '';
                $values     = isset($save_meta[$fields_meta_key]) ? $save_meta[$fields_meta_key] : '';

                $default_value		= isset($meta ['default']) ? $meta ['default'] : '';
                // wpcomment_pa($fields_meta_key);
			
				if ( empty( $values) ){
					$values = $default_value;
				}

                $input_wrapper_classes = isset($meta['col_classes']) ? $meta['col_classes'] : array();
                // $input_wrapper_classes = array('col-md-6', 'col-sm-6');
                
                if (empty($input_wrapper_classes)) {
                	$input_wrapper_classes = array('col-md-6', 'col-sm-12');
                }
				
				$panel_classes = isset($meta['tabs_class']) ? $meta['tabs_class'] : array_merge($input_wrapper_classes, array('wpcomment_handle_fields_tab'));
				$panel_classes[] = 'wpcomment-control-all-fields-tabs';

				if ($type == 'checkbox') {
					$panel_classes[] = 'wpcomment-checkboxe-style';
				}
				if (!empty($panel_classes)) {
					$panel_classes = implode(' ',$panel_classes);
				}

                $html .= '<div data-meta-id="'.esc_attr($fields_meta_key).'" class="'.esc_attr($panel_classes).'">';
	                $html .= '<div class="form-group">';

	                    $html .= '<label>'.sprintf(__("%s","wpcomment"), $title).'';
	                        $html .= '<span class="wpcomment-helper-icon" data-wpcomment-tooltip="wpcomment_tooltip" title="'.sprintf(__("%s","wpcomment"),$desc).'">';
	                            $html .= '<i class="dashicons dashicons-editor-help"></i>';
	                        $html .= '</span>'.$link.'';
	                    $html .= '</label>';
	                    $html .= $this-> render_all_input_types( $fields_meta_key, $meta, $fields_type, $field_index, $values );

	                $html .= '</div>';
	            $html .= '</div>';
                  
            }
        }

        $html .= '</div>';

        return ($html);        
    }


	/*
	* this function is rendring input field for settings
	*/
	function render_all_input_types($name, $data, $fields_type, $field_index, $values ) {
		// wpcomment_pa($values);

		$type		   = (isset( $data ['type'] ) ? $data ['type'] : '');
		
		$options	   = (isset( $data ['options'] ) ? $data ['options'] : '');
		$placeholders  = isset($data['placeholders']) ? $data['placeholders'] : '';
		
		$existing_name = 'name="wpcomment['.esc_attr($field_index).']['.esc_attr($name).']"';

		$html_input    = '';
		
		if(!is_array($values))
			$values = stripslashes($values);
		
		switch ($type) {
			
			case 'number':
			case 'text' :
				// wpcomment_pa($values);
				$html_input .= '<input data-metatype="'.esc_attr($name).'" type="'.esc_attr($type).'"  value="' . esc_html( $values ). '" class="form-control wpcomment-meta-field"';

				if( $field_index != '') {

                  $html_input .= $existing_name;
                }

				$html_input .= ' />';
				break;
				
			case 'color':
				$html_input .= '<div class="wpcomment-color-picker-cloner">';
					$html_input .= '<input data-alpha-enabled="true" data-metatype="'.esc_attr($name).'" type="text"  value="' . esc_html( $values ). '" class="form-control wpcomment-meta-field wpcomment-color-picker-init"';
	
					if( $field_index != '') {
	
	                  $html_input .= $existing_name;
	                }
	
					$html_input .= '>';
				$html_input .= '</div>';
				break;
			
			case 'textarea' :

				$html_input .= '<textarea data-metatype="'.esc_attr($name).'" class="form-control wpcomment-meta-field wpcomment-adjust-box-height"';
				
				if( $field_index != '') {

                  $html_input .= $existing_name;
                }
				
				$html_input .= '>' . esc_html( $values ) . '</textarea>';

				break;
			
			case 'select' :

				$html_input .= '<select id="'.$name.'" data-metatype="'.esc_attr($name).'" class="form-control wpcomment-meta-field"';
				
				if( $field_index != '') {

                  $html_input .= $existing_name;
                }

				$html_input .= '>';

				foreach ( $options as $key => $val ) {
					$selected = ($key == $values) ? 'selected="selected"' : '';
					$html_input .= '<option value="' . $key . '" ' . $selected . '>' . esc_html( $val ) . '</option>';
				}
				$html_input .= '</select>';

				break;
			
			case 'paired' :
				
				$plc_option = (!empty($placeholders)) ? $placeholders[0] : __('Option',"wpcomment");
				$plc_id = (isset($placeholders[3]) && !empty($placeholders)) ? $placeholders[3] : __('Unique Option ID)', "wpcomment");

				$opt_index0  = 1;
				$html_input .= '<ul class="wpcomment-options-container wpcomment-options-sortable">';
				
				if($values){
					// wpcomment_pa($values);
					$last_array_id = max(array_keys($values));

					foreach ($values as $opt_index => $option){

							$option_id = wpcomment_get_option_id($option);
							$html_input .= '<li class="data-options wpcomment-sortable-handle" style="display: flex;" data-condition-type="simple_options">';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" class="option-title form-control wpcomment-option-keys" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.$plc_option.'" data-metatype="option" data-opt-index="'.esc_attr($opt_index).'">';
							$html_input .= '<input type="text" class="option-id form-control wpcomment-option-keys" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][id]" value="'.esc_attr($option_id).'" placeholder="'.$plc_id.'" data-metatype="id" data-opt-index="'.esc_attr($opt_index).'">';

							$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
							$html_input .= '<button class="btn btn-danger wpcomment-remove-option"><i class="fa fa-minus" aria-hidden="true"></i></button>';

						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;

					}
				}else{
					$html_input .= '<li class="data-options" style="display: flex;" data-condition-type="simple_options">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" class="option-title form-control wpcomment-option-keys" placeholder="'.$plc_option.'" data-metatype="option">';
						$html_input .= '<input type="text" class="option-id form-control wpcomment-option-keys" placeholder="'.$plc_id.'" data-metatype="id">';

						$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '<button class="btn btn-danger wpcomment-remove-option"><i class="fa fa-minus" aria-hidden="true"></i></button>';

					$html_input .= '</li>';
				}
				$html_input .= '<input type="hidden" id="wpcomment-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;
				
				case 'paired-palettes';
				
				$plc_option = (!empty($placeholders)) ? $placeholders[0] : __('Option',"wpcomment");
				$plc_price = (!empty($placeholders)) ? $placeholders[1] : __('Price (optional)', "wpcomment");
				$plc_label = (!empty($placeholders)) ? $placeholders[2] : __('Label', "wpcomment");
				$plc_id = (isset($placeholders[3]) && !empty($placeholders)) ? $placeholders[3] : __('Unique Option ID)', "wpcomment");
				
				$opt_index0  = 1;
				$html_input .= '<ul class="wpcomment-options-container wpcomment-options-sortable '.$type.'">';
				
				if($values){
					// wpcomment_pa($values);
					$last_array_id = max(array_keys($values));

					foreach ($values as $opt_index => $option){

						$label = isset($option['label']) ? $option['label'] : '';
						$isfixed = isset($option['isfixed']) ? $option['isfixed'] : '';
						
						$option_id = wpcomment_get_option_id($option);
						$html_input .= '<li class="data-options wpcomment-sortable-handle" style="display: flex;">';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" class="option-title form-control wpcomment-option-keys" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.$plc_option.'" data-metatype="option" data-opt-index="'.esc_attr($opt_index).'">';
							$html_input .= '<input type="text" class="option-label form-control wpcomment-option-keys" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][label]" value="'.esc_attr($label).'" placeholder="'.$plc_label.'" data-metatype="label" data-opt-index="'.esc_attr($opt_index).'">';
							$html_input .= '<input type="text" class="option-id form-control wpcomment-option-keys" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][id]" value="'.esc_attr($option_id).'" placeholder="'.$plc_id.'" data-metatype="id" data-opt-index="'.esc_attr($opt_index).'">';
							
							$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
							$html_input .= '<button class="btn btn-danger wpcomment-remove-option"><i class="fa fa-minus" aria-hidden="true"></i></button>';

						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;

					}
				}else{
					$html_input .= '<li class="data-options" style="display: flex;">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" class="option-title form-control wpcomment-option-keys" placeholder="'.$plc_option.'" data-metatype="option">';
						
						$html_input .= '<input type="text" class="option-label form-control wpcomment-option-keys" placeholder="'.$plc_label.'" data-metatype="label">';

						$html_input .= '<input type="text" class="option-id form-control wpcomment-option-keys" placeholder="'.$plc_id.'" data-metatype="id">';
						
					
						$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '<button class="btn btn-danger wpcomment-remove-option"><i class="fa fa-minus" aria-hidden="true"></i></button>';
						
					$html_input .= '</li>';
				}
				$html_input .= '<input type="hidden" id="wpcomment-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;


			case 'font_paired' :
				
				$plc_option = (!empty($placeholders)) ? $placeholders[0] : __('Data Name',"wpcomment");
				$plc_price = (!empty($placeholders)) ? $placeholders[1] : __('Font Name', "wpcomment");
			
				$opt_index0  = 1;
				$html_input .= '<ul class="wpcomment-options-container wpcomment-options-sortable">';
				
				if($values){
					$last_array_id = max(array_keys($values));

					foreach ($values as $opt_index => $option){

						$weight = isset($option['weight']) ? $option['weight'] : '';
						
						$html_input .= '<li class="data-options wpcomment-sortable-handle" style="display: flex;">';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" class="option-title form-control wpcomment-option-keys" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][dataname]" value="'.esc_attr(stripslashes($option['dataname'])).'" placeholder="'.$plc_option.'" data-metatype="dataname" data-opt-index="'.esc_attr($opt_index).'">';
							$html_input .= '<input type="text" class="form-control wpcomment-option-keys" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][font_name]" value="'.esc_attr($option['font_name']).'" placeholder="'.$plc_price.'" data-metatype="font_name" data-opt-index="'.esc_attr($opt_index).'">';
							

							$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;

					}
				}else{
					$html_input .= '<li class="data-options" style="display: flex;">';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" class="option-title form-control wpcomment-option-keys" placeholder="'.$plc_option.'" data-metatype="dataname">';
						$html_input .= '<input type="text" class="form-control wpcomment-option-keys" placeholder="'.$plc_price.'" data-metatype="font_name">';

						$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired"><i class="fa fa-plus" aria-hidden="true"></i></button>';
					$html_input .= '</li>';
				}
				$html_input .= '<input type="hidden" id="wpcomment-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;
				
				
			case 'paired-cropper' :
				
				$opt_index0  = 1;
				$html_input .= '<ul class="wpcomment-options-container wpcomment-cropper-boundary">';
				
				if($values){
					// wpcomment_pa($values);
					$last_array_id = max(array_keys($values));
					foreach ($values as $opt_index => $option){
						
						$price = isset($option['price']) ? $option['price'] : '';
												
						$html_input .= '<li class="data-options" style=display:flex;>';
							$html_input .= '<span class="dashicons dashicons-move"></span>';
							$html_input .= '<input type="text" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][option]" value="'.esc_attr(stripslashes($option['option'])).'" placeholder="'.__('Label',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="option" data-opt-index="'.esc_attr($opt_index).'">';
							$html_input .= '<input type="text" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][width]" value="'.esc_attr(stripslashes($option['width'])).'" placeholder="'.__('Width',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="width" data-opt-index="'.esc_attr($opt_index).'">';
							$html_input .= '<input type="text" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][height]" value="'.esc_attr($option['height']).'" placeholder="'.__('Height',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="height" data-opt-index="'.esc_attr($opt_index).'">';
							$html_input .= '<input type="text" name="wpcomment['.esc_attr($field_index).'][options]['.esc_attr($opt_index).'][price]" value="'.esc_attr($price).'" placeholder="'.__('Price (optional)',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="price" data-opt-index="'.esc_attr($opt_index).'">';

							$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired-cropper"><i class="fa fa-plus" aria-hidden="true"></i></button>';
							$html_input .= '<button class="btn btn-danger wpcomment-remove-option"><i class="fa fa-minus" aria-hidden="true"></i></button>';

						$html_input .= '</li>';

						$opt_index0 =  $last_array_id;
                    	$opt_index0++;
					}
				}else{
					$html_input .= '<li class="data-options" style=display:flex;>';
						$html_input .= '<span class="dashicons dashicons-move"></span>';
						$html_input .= '<input type="text" placeholder="'.__('option',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="option">';
						$html_input .= '<input type="text" placeholder="'.__('Width',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="width">';
						$html_input .= '<input type="text" placeholder="'.__('Height',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="height">';
						$html_input .= '<input type="text" placeholder="'.__('Price (optional)',"wpcomment").'" class="form-control wpcomment-option-keys" data-metatype="price">';

						$html_input .= '<button class="btn btn-success wpcomment-add-option" data-option-type="paired-cropper"><i class="fa fa-plus" aria-hidden="true"></i></button>';
						$html_input .= '<button class="btn btn-danger wpcomment-remove-option"><i class="fa fa-minus" aria-hidden="true"></i></button>';

					$html_input .= '</li>';
				}
					$html_input .= '<input type="hidden" id="wpcomment-meta-opt-index" value="'.esc_attr($opt_index0).'">';
				$html_input	.= '<ul/>';
				
				break;
				
			case 'checkbox' :
				
				if ($options) {
					foreach ( $options as $key => $val ) {
						
						parse_str ( $values, $saved_data );
						$checked = '';
						if ( isset( $saved_data ['editing_tools'] ) && $saved_data ['editing_tools']) {
							if (in_array($key, $saved_data['editing_tools'])) {
								$checked = 'checked="checked"';
							}else{
								$checked = '';
							}
						}
						
						// For event Calendar Addon
						if ( isset( $saved_data ['cal_addon_disable_days'] ) && $saved_data ['cal_addon_disable_days']) {
							if (in_array($key, $saved_data['cal_addon_disable_days'])) {
								$checked = 'checked="checked"';
							}else{
								$checked = '';
							}
						}
						// $html_input .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
						$html_input .= '<label style="float:left;">';
							$html_input .= '<input type="checkbox" value="' . $key . '" name="wpcomment['.esc_attr($field_index).']['.esc_attr($name).'][]" ' . $checked . '> ' . $val . '<br>';
							$html_input .= '<span></span>';
						$html_input .= '</label>';
					}
				} else {
					$checked = ( (isset($values) && $values != '' ) ? 'checked = "checked"' : '' );
						
						$html_input .= '<label style="float:left;">';
							$html_input .= '<input type="checkbox" class="wpcomment-meta-field" data-metatype="'.esc_attr($name).'" ' . $checked . '';
					
							if( $field_index != '') {

		                  		$html_input .= $existing_name;
		                	}
					
							$html_input .= '>';
							
							$html_input .= '<span></span>';
						$html_input .= '</label>';

				}
				break;
				
			case 'html-conditions' :
				
				$condition_index = 1;
				$rule_i = 1;
				if($values){
					// wpcomment_pa($values);
					$condition_rules = isset($values['rules']) ? $values['rules'] : array();
					$last_array_id   = max(array_keys($condition_rules));

					$visibility_show = ($values['visibility'] == 'Show') ? 'selected="selected"' : '';
					$visibility_hide = ($values['visibility'] == 'Hide') ? 'selected="selected"' : '';
					$bound_all       = ($values['bound'] == 'All') ? 'selected="selected"' : '';
					$bound_any       = ($values['bound'] == 'Any') ? 'selected="selected"' : '';
					
					$html_input	= '<div class="row wpcomment-condition-style-wrap">';
						$html_input	.= '<div class="col-md-3 col-sm-3">';
							$html_input	.= '<select name="wpcomment['.esc_attr($field_index).'][conditions][visibility]" class="form-control wpcomment-condition-visible-bound" data-metatype="visibility">';
								$html_input .= '<option '.$visibility_show.' value="Show">'.__( 'Show', 'wp-comment-fields' ).'</option>';
								$html_input .= '<option '.$visibility_hide.' value="Hide">'.__( 'Hide', 'wp-comment-fields' ).'</option>';
							$html_input	.= '</select>';
						$html_input .= '</div>';

						$html_input	.= '<div class="col-md-2 col-sm-2">';
							$html_input .= '<p>'.__( 'only if', 'wp-comment-fields' ).'</p>';
						$html_input .= '</div>';

						$html_input	.= '<div class="col-md-3 col-sm-3">';
							$html_input	.= '<select name="wpcomment['.esc_attr($field_index).'][conditions][bound]" class="form-control wpcomment-condition-visible-bound" data-metatype="bound">';
								$html_input .= '<option '.$bound_all.' value="All">'.__( 'All', 'wp-comment-fields' ).'</option>';
								$html_input .= '<option '.$bound_any.' value="Any">'.__( 'Any', 'wp-comment-fields' ).'</option>';
							$html_input	.= '</select>';
						$html_input .= '</div>';

						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input .='<p>'.__( 'of the following matches', 'wp-comment-fields' ).'</p>';
						$html_input .= '</div>';
					$html_input .= '</div>';

					$html_input .= '<div class="row wpcomment-condition-clone-js">';
					foreach ($condition_rules as $rule_index => $condition){

						$element_values   = isset($condition['element_values']) ? stripslashes($condition['element_values']) : '';
						$element          = isset($condition['elements']) ? stripslashes($condition['elements']) : '';
						$operator_is 	  = ($condition['operators'] == 'is') ? 'selected="selected"' : '';
						$operator_not 	  = ($condition['operators'] == 'not') ? 'selected="selected"' : '';
						$operator_greater = ($condition['operators'] == 'greater than') ? 'selected="selected"' : '';
						$operator_less 	  = ($condition['operators'] == 'less than') ? 'selected="selected"' : '';
						
							$html_input .= '<div class="webcontact-rules" id="rule-box-'.esc_attr($rule_i).'">';
								$html_input .= '<div class="col-md-12 col-sm-12"><label>'.__('Rule ', "wpcomment") . $rule_i++ .'</label></div>';
								
								// conditional elements
								$html_input .= '<div class="col-md-4 col-sm-4">';
									$html_input .= '<select name="wpcomment['.esc_attr($field_index).'][conditions][rules]['.esc_attr($rule_index).'][elements]" class="form-control wpcomment-conditional-keys" data-metatype="elements"
										data-existingvalue="'.esc_attr($element).'" >';
										$html_input .= '<option>'.$element.'</option>';
									$html_input .= '</select>';
								$html_input .= '</div>';

								// is value meta
								$html_input .= '<div class="col-md-2 col-sm-2">';
									$html_input .= '<select name="wpcomment['.esc_attr($field_index).'][conditions][rules]['.esc_attr($rule_index).'][operators]" class="form-control wpcomment-conditional-keys" data-metatype="operators">';
										$html_input	.= '<option '.$operator_is.' value="is">'. __('is', "wpcomment").'</option>';
										$html_input .= '<option '.$operator_not.' value="not">'. __('not', "wpcomment").'</option>';
										$html_input .= '<option '.$operator_greater.' value="greater than">'. __('greater than', "wpcomment").'</option>';
										$html_input .= '<option '.$operator_less.' value="less than">'. __('less than', "wpcomment").'</option>';
									$html_input	.= '</select> ';
								$html_input .= '</div>';

								// conditional elements values
								$html_input .= '<div class="col-md-4 col-sm-4">';

									$html_input .= '<select name="wpcomment['.esc_attr($field_index).'][conditions][rules]['.esc_attr($rule_index).'][element_values]" class="form-control wpcomment-conditional-keys" data-metatype="element_values"
										data-existingvalue="'.esc_attr($element_values).'" >';
										$html_input .= '<option>'.$element_values.'</option>';
									$html_input .= '</select>';

									// $html_input .= '<input type="text" name="wpcomment['.esc_attr($field_index).'][conditions][rules]['.esc_attr($rule_index).'][element_values]" class="form-control wpcomment-conditional-keys" value="'.esc_attr($element_values).'" placeholder="Enter Option" data-metatype="element_values">';
								$html_input .= '</div>';

								// Add and remove btn
								$html_input .= '<div class="col-md-2 col-sm-2">';
									$html_input .= '<button class="btn btn-success wpcomment-add-rule" data-index="5"><i class="fa fa-plus" aria-hidden="true"></i></button>';
								$html_input .= '</div>';
							$html_input .= '</div>';

						$condition_index = $last_array_id;
                    	$condition_index++;
					}
					$html_input .= '</div>';
				}else{

					$html_input .= '<div class="row wpcomment-condition-style-wrap">';
						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input	.= '<select class="form-control wpcomment-condition-visible-bound" data-metatype="visibility">';
								$html_input .= '<option value="Show">'.__('Show', "wpcomment").'</option>';
								$html_input .= '<option value="Hide">'. __('Hide', "wpcomment").'</option>';
							$html_input	.= '</select> ';
						$html_input .= '</div>';
						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input	.= '<select class="form-control wpcomment-condition-visible-bound" data-metatype="bound">';
								$html_input .= '<option value="All">'. __('All', "wpcomment").'</option>';
								$html_input .= '<option value="Any">'. __('Any', "wpcomment").'</option>';
							$html_input	.= '</select> ';
						$html_input .= '</div>';
						$html_input	.= '<div class="col-md-4 col-sm-4">';
							$html_input .='<p>'. __(' of the following matches', "wpcomment").'</p>';
						$html_input .= '</div>';
					$html_input .= '</div>';

					$html_input .= '<div class="row wpcomment-condition-clone-js">';
						$html_input .= '<div class="webcontact-rules" id="rule-box-'.esc_attr($rule_i).'">';
							$html_input .= '<div class="col-md-12 col-sm-12"><label>'.__('Rule ', "wpcomment") . $rule_i++ .'</label></div>';
							
							// conditional elements
							$html_input .= '<div class="col-md-4 col-sm-4">';
								$html_input .= '<select data-metatype="elements" class="wpcomment-conditional-keys form-control"></select>';
							$html_input .= '</div>';
							
							// is
							$html_input .= '<div class="col-md-2 col-sm-2">';
								$html_input .= '<select data-metatype="operators" class="wpcomment-conditional-keys form-control">';
									$html_input	.= '<option value="is">'. __('is', "wpcomment").'</option>';
									$html_input .= '<option value="not">'. __('not', "wpcomment").'</option>';
									$html_input .= '<option value="greater than">'. __('greater than', "wpcomment").'</option>';
									$html_input .= '<option value="less than">'. __('less than', "wpcomment").'</option>';
								$html_input	.= '</select> ';
							$html_input .= '</div>';

							// conditional elements values
							$html_input .= '<div class="col-md-4 col-sm-4">';

								$html_input .= '<select data-metatype="element_values" class="wpcomment-conditional-keys form-control"></select>';


								// $html_input .= '<input type="text" class="form-control wpcomment-conditional-keys" placeholder="Enter Option" data-metatype="element_values">';
							$html_input .= '</div>';

							// Add and remove btn
							$html_input .= '<div class="col-md-2 col-sm-2">';
								$html_input .= '<button class="btn btn-success wpcomment-add-rule" data-index="5"><i class="fa fa-plus" aria-hidden="true"></i></button>';
							$html_input .= '</div>';

						$html_input .= '</div>';
					$html_input .= '</div>';
				}
				$html_input .= '<input type="hidden" class="wpcomment-condition-last-id" value="'.esc_attr($condition_index).'">';

				break;
				
				case 'pre-images' :
				
					$html_input	.= '<div class="pre-upload-box table-responsive">';
					
						$html_input	.= '<button class="btn btn-info wpcomment-pre-upload-image-btn" data-metatype="images">'.__('Select/Upload Image', "wpcomment").'</button>';
						// wpcomment_pa($value);

						$opt_index0  = 0;
						$html_input .= '<ul class="wpcomment-options-container">';
						if ($values) {
							
							$last_array_id = max(array_keys($values));

							foreach ($values as $opt_index => $pre_uploaded_image){
						
								$image_link 	= (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
								$image_id		= (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
								$image_url  	= (isset($pre_uploaded_image['url']) ? $pre_uploaded_image['url'] : '');
								
								$image_name = isset($pre_uploaded_image['link']) ? basename($pre_uploaded_image['link']) : '';

								$html_input .= '<li class="data-options" data-condition-type="image_options">';
									$html_input .= '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';	
									$html_input .= '<span class="wpcomment-uploader-img-title">'.$image_name.'</span>';
									$html_input .= '<div style="display: flex;">';
										$html_input .= '<div class="wpcomment-uploader-img-center">';
											$html_input .= '<img width="60" src="'.esc_url($image_link).'" style="width: 34px;">';
										$html_input .= '</div>';
										$html_input .= '<input type="hidden" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][link]" value="'.esc_url($image_link).'" data-opt-index="'.esc_attr($opt_index).'" data-metatype="link">';
										$html_input .= '<input type="hidden" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][id]" value="'.esc_attr($image_id).'" data-opt-index="'.esc_attr($opt_index).'" data-metatype="id">';
										$html_input .= '<input class="form-control wpcomment-image-option-title" type="text" placeholder="Title" value="'.esc_attr(stripslashes($pre_uploaded_image['title'])).'" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][title]" data-opt-index="'.esc_attr($opt_index).'" data-metatype="title">';
										// $html_input .= '<input class="form-control" type="text" placeholder="URL" value="'.esc_url(stripslashes($pre_uploaded_image['url'])).'" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][url]" data-opt-index="'.esc_attr($opt_index).'" data-metatype="url">';

										$html_input .= '<button class="btn btn-danger wpcomment-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
									$html_input .= '</div>';
								$html_input .= '</li>';

								$opt_index0 =  $last_array_id;
	                    		$opt_index0++;
							}
						}
						$html_input .= '</ul>';
						$html_input .= '<input type="hidden" id="wpcomment-meta-opt-index" value="'.esc_attr($opt_index0).'">';
					
					$html_input .= '</div>';
				
				break;

				case 'imageselect' :
				
					$html_input	.= '<div class="pre-upload-box table-responsive">';
					
						$html_input	.= '<button class="btn btn-info wpcomment-pre-upload-image-btn" data-metatype="imageselect">'.__('Select/Upload Image', "wpcomment").'</button>';

						$opt_index0  = 0;
						$html_input .= '<ul class="wpcomment-options-container">';
						if ($values && is_array($values)) {
							
							$last_array_id = max(array_keys($values));

							foreach ($values as $opt_index => $pre_uploaded_image){
						
								$image_link = (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
								$image_id   = (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
								$image_description  = (isset($pre_uploaded_image['description']) ? $pre_uploaded_image['description'] : '');
								
								$image_name = isset($pre_uploaded_image['link']) ? basename($pre_uploaded_image['link']) : '';

								$html_input .= '<li class="data-options" data-condition-type="image_options">';
									$html_input .= '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';	
									$html_input .= '<span class="wpcomment-uploader-img-title">'.$image_name.'</span>';
									$html_input .= '<div style="display: flex;">';
										$html_input .= '<div class="wpcomment-uploader-img-center">';
											$html_input .= '<img width="60" src="'.esc_url($image_link).'" style="width: 34px;">';
										$html_input .= '</div>';
										$html_input .= '<input type="hidden" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][link]" value="'.esc_url($image_link).'" data-opt-index="'.esc_attr($opt_index).'" data-metatype="link">';
										$html_input .= '<input type="hidden" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][id]" value="'.esc_attr($image_id).'" data-opt-index="'.esc_attr($opt_index).'" data-metatype="id">';
										$html_input .= '<input class="form-control wpcomment-image-option-title" type="text" placeholder="Title" value="'.esc_attr(stripslashes($pre_uploaded_image['title'])).'" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][title]" data-opt-index="'.esc_attr($opt_index).'" data-metatype="title">';
										$html_input .= '<input class="form-control" type="text" placeholder="Price" value="'.esc_attr(stripslashes($pre_uploaded_image['price'])).'" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][price]" data-opt-index="'.esc_attr($opt_index).'" data-metatype="price">';
										$html_input .= '<input class="form-control" type="text" placeholder="Description" value="'.esc_attr($image_description).'" name="wpcomment['.esc_attr($field_index).'][images]['.esc_attr($opt_index).'][description]" data-opt-index="'.esc_attr($opt_index).'" data-metatype="description">';
										$html_input .= '<button class="btn btn-danger wpcomment-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
									$html_input .= '</div>';
								$html_input .= '</li>';

								$opt_index0 =  $last_array_id;
	                    		$opt_index0++;
							}
						}
						$html_input .= '</ul>';
						$html_input .= '<input type="hidden" id="wpcomment-meta-opt-index" value="'.esc_attr($opt_index0).'">';
					
					$html_input .= '</div>';
				
				break;
				
				case 'pre-audios' :
				
					$html_input	.= '<div class="pre-upload-box">';
					$html_input	.= '<button class="btn btn-info wpcomment-pre-upload-image-btn" data-metatype="audio">'.__('Select Audio/Video', "wpcomment").'</button>';
					
					$html_input .= '<ul class="wpcomment-options-container">';
					$opt_index0  = 0;
						// wpcomment_pa($values);
					if ($values) {
						$last_array_id = max(array_keys($values));
						foreach ($values as $opt_index => $pre_uploaded_image){
					
							$image_link  = (isset($pre_uploaded_image['link']) ? $pre_uploaded_image['link'] : '');
							$image_id    = (isset($pre_uploaded_image['id']) ? $pre_uploaded_image['id'] : '');
							$image_url   = (isset($pre_uploaded_image['url']) ? $pre_uploaded_image['url'] : '');
							$media_title = (isset($pre_uploaded_image['title']) ? stripslashes($pre_uploaded_image['title']) : '');
							$media_price = (isset($pre_uploaded_image['price']) ? stripslashes($pre_uploaded_image['price']) : '');
							
							$html_input .= '<li class="data-options">';
								$html_input .= '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';
								$html_input .= '<div style="display: flex;">';
									$html_input .= '<div class="wpcomment-uploader-img-center">';
										$html_input .= '<span class="dashicons dashicons-admin-media" style="margin-top: 5px;"></span>';
									$html_input .= '</div>';
									$html_input .= '<input type="hidden" name="wpcomment['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][link]" value="'.esc_url($image_link).'" data-opt-index="'.esc_attr($opt_index).'" data-metatype="link">';
									$html_input .= '<input type="hidden" name="wpcomment['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][id]" value="'.esc_attr($image_id).'" data-opt-index="'.esc_attr($opt_index).'" data-metatype="id">';
									$html_input .= '<input class="form-control" type="text" placeholder="Title" value="'.esc_attr($media_title).'" name="wpcomment['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][title]" data-opt-index="'.esc_attr($opt_index).'" data-metatype="title">';
									$html_input .= '<input class="form-control" type="text" placeholder="Price (fix or %)" value="'.esc_attr($media_price).'" name="wpcomment['.esc_attr($field_index).'][audio]['.esc_attr($opt_index).'][price]" data-opt-index="'.esc_attr($opt_index).'" data-metatype="price">';
									$html_input .= '<button class="btn btn-danger wpcomment-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
								$html_input .= '</div>';
							$html_input .= '</li>';

							$opt_index0 =  $last_array_id;
                    		$opt_index0++;
					
						}
					}
						$html_input .= '</ul>';
						$html_input .= '<input type="hidden" id="wpcomment-meta-opt-index" value="'.esc_attr($opt_index0).'">';
					$html_input .= '</div>';
				
				break;
		}
		
		return apply_filters('wpcomment_render_input_types', $html_input, $type, $name, $values, $options, $field_index);
	}
    

    function wpcomment_fields_tabs($fields_type){
	
		$tabs = array();

		$tabs = array ( 
				'fields_tab' => array (
						'label' => __ ( 'Fields', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label', 'wpcomment-active-tab'),
						'field_depend'=> array('all')
				),
				'condition_tab' => array (
						'label' => __ ( 'Conditions', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label','wpcomment-condition-tab-js'),
						'field_depend'=> array('all'),
						'not_allowed'=> array('hidden','koll')
				),
				'add_option_tab' => array (
						'label' => __ ( 'Add Options', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label'),
						'field_depend'=> array('select','radio','checkbox','cropper', 'cropper2', 'quantities','pricematrix','palettes','fixedprice','bulkquantity')
				),
				'add_images_tab' => array (
						'label' => __ ( 'Add Images', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label'),
						'field_depend'=> array('image','imageselect')
				),
				'add_audio_video_tab' => array (
						'label' => __ ( 'Add Audio/Video', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label'),
						'field_depend'=> array('audio')
				),

				// Font Picker Addon tabs
				'fonts_family_tab' => array (
						'label' => __ ( 'Fonts Family', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label'),
						'field_depend'=> array('fonts')
				),
				'custom_fonts_tab' => array (
						'label' => __ ( 'Custom Fonts', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label'),
						'field_depend'=> array('fonts')
				),
				
				'image_dimension_tab' => array (
						'label' => __ ( 'Image Dimensions', 'wp-comment-fields' ),
						'class' => array('wpcomment-tabs-label'),
						'field_depend'=> array('file')
				),

				
			);

		return apply_filters('wpcomment_fields_tabs_show', $tabs, $fields_type);

	}


	function wpcomment_tabs_panel_classes($settings){


		foreach ($settings as $fields_meta_key => $meta) {

			$type       = isset($meta['type']) ? $meta['type'] : '';

			if ($type == 'html-conditions') {

				$settings['conditions']['tabs_class'] = array('wpcomment_handle_condition_tab','col-md-12');
			}else if($type == 'paired' || $type == 'paired-cropper' 
						|| $type == 'paired-quantity' || 
						$type == 'paired-pricematrix' || 
						$type == 'bulk-quantity' || $type == 'paired-palettes') { 
				//Bulk Quantity Addon Tabs
				//Fixed Price Addon Tabs

				$settings['options']['tabs_class'] = array('wpcomment_handle_add_option_tab','col-md-12');
			}else if( $type == 'pre-images' || $type == 'imageselect') { // Image DropDown Addon Tabs

				$settings['images']['tabs_class'] = array('wpcomment_handle_add_images_tab','col-md-12');
			}else if( $type == 'pre-audios' ) {

				$settings['audio']['tabs_class'] = array('wpcomment_handle_add_audio_video_tab','col-md-12');
			}else if($fields_meta_key== 'logic') {
				
				$settings['logic']['tabs_class'] = array('wpcomment_handle_condition_tab','col-md-12');
			}

			// Fonts Picker Addon tabs
			if ($fields_meta_key == 'fonts') {
				$settings['fonts']['tabs_class'] = array('wpcomment_handle_fonts_family_tab','col-md-12');
			}elseif ($fields_meta_key == 'custom_fonts') {
				$settings['custom_fonts']['tabs_class'] = array('wpcomment_handle_custom_fonts_tab','col-md-12');
			}
			
			// Image Dimensions Options (File Input)
			if ($fields_meta_key == 'min_img_h') {
				$settings['min_img_h']['tabs_class'] = array('wpcomment_handle_image_dimension_tab','col-md-6');
				$settings['max_img_h']['tabs_class'] = array('wpcomment_handle_image_dimension_tab','col-md-6');
				$settings['min_img_w']['tabs_class'] = array('wpcomment_handle_image_dimension_tab','col-md-6');
				$settings['max_img_w']['tabs_class'] = array('wpcomment_handle_image_dimension_tab','col-md-6');
				$settings['img_dimension_error']['tabs_class'] = array('wpcomment_handle_image_dimension_tab','col-md-6');
			}

		}

		return apply_filters('wpcomment_tabs_panel_classes', $settings);
	}

}

WPCOMMENT_FIELDS_META();
function WPCOMMENT_FIELDS_META(){
    return WPComment_Fields_Meta::get_instance();
}