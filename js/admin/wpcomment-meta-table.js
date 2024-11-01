"use strict";
jQuery(function($){
    
    /*********************************
    *   PPOM Existing Table Meta JS  *
    **********************************/

    /*-------------------------------------------------------
        
        ------ Its Include Following Function -----

        1- Apply DataTable JS Library To PPOM Meta List
        2- Delete Selected Products
        3- Check And Uncheck All Existing Product Meta List
        4- Loading Products In Modal DataTable
        5- Delete Single Product Meta
    --------------------------------------------------------*/


	/**
        1- Apply DataTable JS Library To PPOM Meta List
    **/
	$('#wpcomment-meta-table').DataTable();
	var append_overly_model =  ("<div class='wpcomment-modal-overlay wpcomment-js-modal-close'></div>");

    /**
        2- Delete Selected Products
    **/
    $('body').on('click', '#wpcomment_delete_selected_products_btn', function(e){
        e.preventDefault();
        
        var checkedProducts_ids;
        var check_field = $('.wpcomment_product_checkbox:checked');
		checkedProducts_ids = $('.wpcomment_product_checkbox:checked').map(function() {
		    return parseInt(this.value);
		}).get();
        
        if (check_field.length > 0  ) {
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                cancelButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true
                }, function (isConfirm) {
                    if (!isConfirm) return;

                    $('#wpcomment_delete_selected_products_btn').html('Deleting...');
					
					var data = {
						action 			: 'wpcomment_delete_selected_meta',
						productmeta_ids	: checkedProducts_ids
					};

			        $.post(ajaxurl, data, function(resp){
			        	$('#wpcomment_delete_selected_products_btn').html('Delete');
			        	if (resp) {
					        swal({title: "Done", text: resp, type: "success" ,confirmButtonColor: '#217ac8'},
						        function(){ 
						            location.reload();
					        });
			        	}else{
	        	 			swal(resp, "", "error");
			        	}
			        });
            });
        }else{
            swal("Please at least check one Meta!", "", "error");
        }
    });


    /**
        3- Check And Uncheck All Existing Product Meta List
    **/
	$('.wpcomment_product_checkbox').on('click', function(event){
		
		var checkboxProducts = $('.wpcomment_product_checkbox').map(function() {
		    return this.value;
		}).get();

		var checkedProducts = $('.wpcomment_product_checkbox:checked').map(function() {
		    return this.value;
		}).get();

		if (checkboxProducts.length == checkedProducts.length ) {
			$('#wpcomment-all-select-products-head-btn, #wpcomment-all-select-products-foot-btn').prop('checked', true);
		}else{
			$('#wpcomment-all-select-products-head-btn, #wpcomment-all-select-products-foot-btn').prop('checked', false);
		};

		$('#selected_products_count').html();
		$('#selected_products_count').html(checkedProducts.length);
	});
	$('#wpcomment-all-select-products-head-btn, #wpcomment-all-select-products-foot-btn').on('click', function(event){
		
		$('#wpcomment-meta-table input:checkbox').not(this).prop('checked', this.checked);
		var checkedProducts = $('.wpcomment_product_checkbox:checked').map(function() {
		    return this.value;
		}).get();
		$('#selected_products_count').html();
		$('#selected_products_count').html(checkedProducts.length);
	});


	/**
        4- Loading Products In Modal DataTable
    **/
    $('#wpcomment-meta-table_wrapper').on('click','a.wpcomment-products-modal', function(e){
        
        e.preventDefault();

        $(".wpcomment-table").DataTable();
        var wpcomment_id = $(this).data('wpcomment_id'); 
        var get_url = ajaxurl+'?action=wpcomment_get_products&wpcomment_id='+wpcomment_id;
	    var model_id = $(this).attr('data-formmodal-id');
	    
	    $.get( get_url, function(html){
	        $('#wpcomment-product-modal .wpcomment-modal-body').html(html);
	        $("#wpcomment_id").val(wpcomment_id);
        	$("body").append(append_overly_model);
	        $(".wpcomment-table").DataTable();
	        $('#'+model_id).fadeIn();

	    });
    });


    /**
        5- Delete Single Product Meta
    **/
	$('body').on('click','a.wpcomment-delete-single-product', function(e){


		e.preventDefault();
		var productmeta_id = $(this).attr('data-product-id');

        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55 ",
            cancelButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true
            }, function (isConfirm) {
                if (!isConfirm) return;
				$("#del-file-" + productmeta_id).html('<img src="' + wpcomment_vars.loader + '">');

				var data = {
					action 			: 'wpcomment_delete_meta',
					productmeta_id	: productmeta_id
				};

		        $.post(ajaxurl, data, function(resp){
		        	$("#del-file-" + productmeta_id).html('<span class="dashicons dashicons-no"></span>');
		        	if (resp) {
				        swal({title: "Done", text: resp, type: "success" ,confirmButtonColor: '#217ac8'},
					        function(){ 
					            location.reload();
				        });
		        	}else{
        	 			swal(resp, "", "error");
		        	}
		        });
        });
    });

});