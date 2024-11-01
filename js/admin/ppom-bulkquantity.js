"use strict"
jQuery(function($){

	/*********************************
    *   PPOM Bulk Quantity Addon JS  *
    **********************************/


    /*-------------------------------------------------------
        
        ------ Its Include Following Function -----

        1- Body Selector
        2- Add New Quantity Row
        3- Remove Quantity Row
        4- Remove Variation Colunm
        5- Add Bulk Variation Colunm
        6- Save Bulk Quantity Meta
        7- Edit Bulk Quantity Meta
    --------------------------------------------------------*/


	/**
        1- Body Selector
    **/
	var body = $('body');


	/**
        2- Add New Quantity Row 
    **/
	body.on('click', 'button.wpcomment-add-bulk-qty-row', function (e) {
	    e.preventDefault();

	    var main_wrapper = $(this).closest('.wpcomment-slider');
	    var field_index     = main_wrapper.find('.wpcomment-fields-actions').attr('data-field-no');
	    var bulk_div = $(this).closest('div');
	    var bulk_qty_val = bulk_div.find('.wpcomment-bulk-qty-val').val();
	    var table = $(this).closest('div.table-content'),
	    tbody = table.find('tbody'),
	    thead = table.find('thead');

        var clon_qty_section = tbody.find('tr:last-child').clone();
        clon_qty_section.find('.wpcomment-bulk-qty-val-picker').val(bulk_qty_val);
        clon_qty_section.appendTo(tbody);
	    
	});


	/**
        3- Remove Quantity Row
    **/
	body.on('click', 'span.wpcomment-rm-bulk-qty', function (e) {
	    e.preventDefault();

	    var count = $(this).closest('tbody').find('tr').length;
	    if ( count < 2 ) {
	        alert('sorry! you can not remove more textbox');
	        return;
	    }
	    $(this).closest('tr').remove();
	});


	/**
        4- Remove Variation Colunm
    **/
	body.on('click', 'span.wpcomment-rm-bulk-variation', function (e) {
	    e.preventDefault();

	    var cell = $(this).closest('th'),       
	    index = cell.index() + 1;
	    cell.closest('table').find('th, td').filter(':nth-child(' + index + ')').remove();
	});


	/**
        5- Add Bulk Variation Colunm 
    **/
	body.on('click', 'button.wpcomment-add-bulk-variation-col', function (e) {
	    e.preventDefault();

	    var buk_div = $(this).closest('div');
	    var bulk_variation_val = buk_div.find('.wpcomment-bulk-variation-val').val();
	    console.log(bulk_variation_val);
	    var table = $(this).closest('div.table-content').find('table'),
	    thead = table.find('thead'),
	    lastTheadRow = thead.find('tr:last-child'),
	    tbody = table.find('tbody');
	    var closest_td = tbody.find('td:last-child');
	    
	    $('<th>', {
	        'html': ' <span class="wpcomment-bulk-variation-meta"> '+bulk_variation_val+' </span> <span class="remove wpcomment-rm-bulk-variation"><i class="fa fa-times" aria-hidden="true"></i></span>'
	    }).appendTo(lastTheadRow);
	    $('<td>', {
	        'html': '<input type="text" class="form-control" />'
	    }).insertAfter(closest_td);
	});


	/**
        6- Save Bulk Quantity Meta
    **/
	$('body').on('click', '.wpcomment-save-bulk-json', function(event) {
	    event.preventDefault();

	    var bulk_wrap = $(this).closest('.wpcomment-bulk-quantity-wrapper');
	    bulk_wrap.find('table').find('input').each(function(index, el) {
	    	console.log($(this).val());
	        var td_wrap = $(this);
	        td_wrap.closest('td').html($(this).val());
	    });
	    var bulkData = bulk_wrap.find('table').tableToJSON();
	    bulk_wrap.find('.wpcomment-saved-bulk-data').val(JSON.stringify(bulkData)); 

	    // hide action
	    $(this).hide();
	    bulk_wrap.find('.wpcomment-bulk-action-wrap').hide();
	    bulk_wrap.find('.wpcomment-edit-bulk-json').show();
	});


	/**
        7- Edit Bulk Quantity Meta 
    **/
	$('body').on('click', '.wpcomment-edit-bulk-json', function(event) {
	    event.preventDefault();
	    var bulk_wrap = $(this).closest('.wpcomment-bulk-quantity-wrapper');
	    bulk_wrap.find('table').find('tbody tr td').each(function(index, el) {

	    	var class_name = $(el).attr('id');
	        var td_wrap = $(this);
	        var cross_icon = '<span class="remove wpcomment-rm-bulk-qty"><i class="fa fa-times" aria-hidden="true"></i></span>';
	        if (class_name == 'wpcomment-bulkqty-adjust-cross') {
	        	var input = ''+cross_icon+'<input type="text" class="form-control wpcomment-bulk-qty-val-picker" value="'+$(this).text()+'">';
	        }else{
	        	var input = '<input type="text" class="form-control" value="'+$(this).text()+'">';
	        }

	        td_wrap.closest('td').html(input);
	    });

	    // show action
	    $(this).hide();
	    bulk_wrap.find('.wpcomment-bulk-action-wrap').show();
	    bulk_wrap.find('.wpcomment-save-bulk-json').show();
	});

});