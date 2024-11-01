"use strict";
jQuery(function($) {

    var loader = new ImageLoader(wpcomment_vars.loader);
    // define your 'onreadystatechange'
    loader.loadEvent = function(url, imageAsDom) {

        $("#wpcomment-pre-loading").hide();
        $(".wpcomment-admin-wrap").show();
    }
    loader.load();

    /*********************************
     *       PPOM Form Design JS       *
     **********************************/


    /*-------------------------------------------------------
        
        ------ Its Include Following Function -----

        1- Submit PPOM Form Fields
        2- Hide And Show Import & Export & Product Meta blocks
        3- Get Last Field Index
        4- Show And Hide Visibility Role Field
        5- Remove Unsaved Fields
        6- Check And Uncheck All Fields
        7- Remove Check Fields
        8- On Fields Options Handle Add Option Last
        9- Edit Existing Fields
        10- Add New Fields
        11- Update Existing Fields
        12- Clone New Fields
        13- Clone Existing Fields
        14- Saving WP Comment Settings
        15- Open Product Modal In Existing Meta File (removed)
        16- Handle Fields Tabs
        17- Handle Media Images Of Following Inputs Types
        18- Add Fields Conditions
        19- Add Fields Options
        20- Auto Generate Option IDs
        21- Create Field data_name By Thier Title
        22- Fields Sortable
        23- Fields Option Sortable
        24- Fields Dataname Must Be Required
        25- Fields Add Option Index Controle Funtion
        26- Fields Add Condition Index Controle Function
        27- Get All Fields Title On Condition Element Value After Click On Condition Tab
        28- validate API WooCommerce Product
    ------------------------------------------------------------*/


    /**
        PPOM Model
    **/
    var append_overly_model = ("<div class='wpcomment-modal-overlay wpcomment-js-modal-close'></div>");

    $(document).on('click', '[data-modal-id]', function(e) {
        e.preventDefault();
        $("body").append(append_overly_model);
        var modalBox = $(this).attr('data-modal-id');
        $('#' + modalBox).fadeIn();
    });

    wpcomment_close_popup();

    function wpcomment_close_popup() {

        $(".wpcomment-js-modal-close, .wpcomment-modal-overlay").click(function(e) {

            var target = $(e.target);
            if (target.hasClass("wpcomment-modal-overlay")) {
                return false;
            }
            $(".wpcomment-modal-box, .wpcomment-modal-overlay").fadeOut('fast', function() {
                $(".wpcomment-modal-overlay").remove();
            });

        });
    }


    $('.wpcomment-color-picker-init').wpColorPicker();


    /**
        1- Submit PPOM Form Fields
    **/
    $(".wpcomment-save-fields-meta").submit(function(e) {
        e.preventDefault();

        jQuery(".wpcomment-meta-save-notice").html('<img src="' + wpcomment_vars.loader + '">').show();

        $('.wpcomment-unsave-data').remove();

        var data = $(this).serialize();

        $.post(ajaxurl, data, function(resp) {

            const bg_color = resp.status == 'success' ? '#4e694859' : '#ee8b94';
            jQuery(".wpcomment-meta-save-notice").html(resp.message).css({ 'background-color': bg_color, 'padding': '8px', 'border-left': '5px solid #008c00' });
            if (resp.status == 'success') {
                if (resp.redirect_to != '') {
                    window.location = resp.redirect_to;
                }
                else {
                    window.location.reload(true);
                }
            }
        }, 'json');

    });


    /**
        2- Hide And Show Import & Export & Product Meta blocks
    **/
    $('.wpcomment-import-export-btn').on('click', function(event) {
        event.preventDefault();
        $('.wpcomment-more-plugins-block').hide();
        $(".wpcomment-import-export-block").show();
        $(".wpcomment-product-meta-block").hide();
    });

    $('.wpcomment-cancle-import-export-btn').on('click', function(event) {
        event.preventDefault();
        $('.wpcomment-more-plugins-block').show();
        $(".wpcomment-import-export-block").hide();
        $(".wpcomment-product-meta-block").show();
    });


    /**
        3- Get Last Field Index
    **/
    var field_no = $('#field_index').val();


    /**
        4- Show And Hide Visibility Role Field
    **/
    $('.wpcomment-slider').find('[data-meta-id="visibility_role"]').removeClass('wpcomment_handle_fields_tab').hide();
    $('.wpcomment_save_fields_model .wpcomment-slider').each(function(i, div) {
        var visibility_value = $(div).find('[data-meta-id="visibility"] select').val();
        if (visibility_value == 'roles') {
            $(div).find('[data-meta-id="visibility_role"]').show();
        }
    });
    $(document).on('change', '[data-meta-id="visibility"] select', function(e) {
        e.preventDefault();

        var div = $(this).closest('.wpcomment-slider');
        var visibility_value = $(this).val();
        console.log(visibility_value);
        if (visibility_value == 'roles') {
            div.find('[data-meta-id="visibility_role"]').show();
        }
        else {
            div.find('[data-meta-id="visibility_role"]').hide();
        }
    });


    /**
        5- Remove Unsaved Fields
    **/
    $(document).on('click', '.wpcomment-close-fields', function(event) {
        event.preventDefault();

        $(this).closest('.wpcomment-slider').addClass('wpcomment-unsave-data');
    });


    /**
        6- Check And Uncheck All Fields
    **/
    $('.wpcomment-main-field-wrapper').on('change', '.onoffswitch-checkbox', function(event) {
        var div = $(this).closest('div');
        if ($(this).prop('checked')) {
            div.find('input[type="hidden"]').val('on');
        }
        else {
            div.find('input[type="hidden"]').val('off');
        }
    });

    $('.wpcomment-main-field-wrapper').on('click', '.wpcomment-check-all-field input', function(event) {
        if ($(this).prop('checked')) {
            $('.wpcomment_field_table .wpcomment-checkboxe-style input[type="checkbox"]').prop('checked', true);
        }
        else {
            $('.wpcomment_field_table .wpcomment-checkboxe-style input[type="checkbox"]').prop('checked', false);
        }
    });
    $('.wpcomment-main-field-wrapper').on('change', '.wpcomment_field_table tbody .wpcomment-checkboxe-style input[type="checkbox"]', function(event) {
        if ($('.wpcomment_field_table tbody .wpcomment-checkboxe-style input[type="checkbox"]:checked').length == $('.wpcomment_field_table tbody .wpcomment-checkboxe-style input[type="checkbox"]').length) {
            $('.wpcomment-check-all-field input').prop('checked', true);
        }
        else {
            $('.wpcomment-check-all-field input').prop('checked', false);
        }
    });


    /**
        7- Remove Check Fields
    **/
    $('.wpcomment-main-field-wrapper').on('click', '.wpcomment_remove_field', function(e) {
        e.preventDefault();

        var check_field = $('.wpcomment-check-one-field input[type="checkbox"]:checked');

        if (check_field.length > 0) {
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                cancelButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true
            }, function(isConfirm) {
                if (!isConfirm) return;

                $('.wpcomment_field_table').find('.wpcomment-check-one-field input').each(function(i, meta_field) {

                    if (this.checked) {
                        var field_id = $(meta_field).val();
                        console.log(field_id)
                        $(meta_field).parent().parent().parent('.row_no_' + field_id + '').remove();
                    }
                    $('.wpcomment_save_fields_model').find('#wpcomment_field_model_' + field_id + '').remove();
                });
            });
        }
        else {
            swal("Please at least check one field!", "", "error");
        }
    });


    /**
        8- On Fields Options Handle Add Option Last
    **/
    $('.webcontact-rules').each(function(i, meta_field) {

        var selector_btn = $(this).closest('.wpcomment-slider');
        selector_btn.find('.wpcomment-add-rule').not(':last').removeClass('wpcomment-add-rule').addClass('wpcomment-remove-rule')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<i class="fa fa-minus" aria-hidden="true"></i>');

    });
    // $('.data-options').each(function(i, meta_field){

    //     var selector_btn = $(this).closest('.wpcomment-slider');
    //     selector_btn.find('.wpcomment-add-option').not(':last').removeClass('wpcomment-add-option').addClass('wpcomment-remove-option')
    //    .removeClass('btn-success').addClass('btn-danger')
    //    .html('<i class="fa fa-minus" aria-hidden="true"></i>');

    // });


    /**
        9- Edit Existing Fields
    **/
    $(document).on('click', '.wpcomment-edit-field', function(event) {
        event.preventDefault();

        var the_id = $(this).attr('id');
        $('#wpcomment_field_model_' + the_id + '').find('.wpcomment-close-checker').removeClass('wpcomment-close-fields');
    });


    /**
        10- Add New Fields
    **/
    $(document).on('click', '.wpcomment-add-field', function(event) {
        event.preventDefault();

        var $this = $(this);
        var ui = wpcomment_required_data_name($this);
        if (ui == false) {
            return;
        }

        var copy_model_id = $(this).attr('data-copy-model-id');
        var id = $(this).attr('data-field-index');
        id = Number(id);
        console.log(id);

        var field_title = $('#wpcomment_field_model_' + id + '').find('.wpcomment-modal-body .wpcomment-fields-actions').attr('data-table-id');
        var data_name = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="data_name"] input').val();
        var title = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="title"] input').val();
        var placeholder = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="placeholder"] input').val();
        var required = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="required"] input').prop('checked');
        var type = $(this).attr('data-field-type');

        console.log(field_title);

        if (required == true) {
            var _ok = 'Yes';
        }
        else {
            _ok = 'No';
        }
        if (placeholder == null) {
            placeholder = '-';
        }

        var html = '<tr class="row_no_' + id + '" id="wpcomment_sort_id_' + id + '">';
        html += '<td class="wpcomment-sortable-handle"><i class="fa fa-arrows" aria-hidden="true"></i></td>';
        html += '<td class="wpcomment-check-one-field wpcomment-checkboxe-style">';
        html += '<label>';
        html += '<input type="checkbox" value="' + id + '">';
        html += '<span></span>';
        html += '</label>';
        html += '</td>';

        html += '<td>';
        html += '<div class="onoffswitch">';
        html += '<input checked type="checkbox" name="wpcomment[' + id + '][status]" class="onoffswitch-checkbox" id="wpcomment-onoffswitch-' + id + '" tabindex="0">';
        html += '<label class="onoffswitch-label" for="wpcomment-onoffswitch-' + id + '">';
        html += '<span class="onoffswitch-inner"></span>';
        html += '<span class="onoffswitch-switch"></span>';
        html += '</label>';
        html += '</div>';
        html += '</td>';

        // html += '<td class="wpcomment-check-one-field"><input type="checkbox" value="'+id+'"></td>';
        html += '<td class="wpcomment_meta_field_id">' + data_name + '</td>';
        html += '<td class="wpcomment_meta_field_type">' + type + '</td>';
        html += '<td class="wpcomment_meta_field_title">' + title + '</td>';
        html += '<td class="wpcomment_meta_field_plchlder">' + placeholder + '</td>';
        html += '<td class="wpcomment_meta_field_req">' + _ok + '</td>';
        html += '<td>';
        html += '<button class="wpcomment_copy_field btn" id="' + id + '" data-field-type="' + field_title + '" style="margin-right: 4px;"><i class="fa fa-clone" aria-hidden="true"></i></button>';
        html += '<button class="wpcomment-edit-field btn" id="' + id + '" data-modal-id="wpcomment_field_model_' + id + '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
        html += '</td>';
        html += '</tr>';

        // console.log(copy_model_id);
        if (copy_model_id != '' && copy_model_id != undefined) {
            $(html).find('.wpcomment_field_table tbody').end().insertAfter('#wpcomment_sort_id_' + copy_model_id + '');
        }
        else {
            $(html).appendTo('.wpcomment_field_table tbody');
        }

        $(".wpcomment-modal-box, .wpcomment-modal-overlay").fadeOut('fast', function() {
            $(".wpcomment-modal-overlay").remove();
        });

        $(this).removeClass('wpcomment-add-field').addClass('wpcomment-update-field');
        $(this).html('Update Field');

    });


    /**
        11- Update Existing Fields
    **/
    $(document).on('click', '.wpcomment-update-field', function(event) {
        event.preventDefault();

        var $this = $(this);
        var ui = wpcomment_required_data_name($this);

        if (ui == false) {
            return;
        }

        var id = $(this).attr('data-field-index');
        id = Number(id);

        var data_name = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="data_name"] input').val();
        var title = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="title"] input').val();
        var placeholder = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="placeholder"] input').val();
        var required = $('#wpcomment_field_model_' + id + '').find('[data-meta-id="required"] input').prop('checked');
        var type = $(this).attr('data-field-type');

        if (required == true) {
            var _ok = 'Yes';
        }
        else {
            _ok = 'No';
        }

        var row = $('.wpcomment_field_table tbody').find('.row_no_' + id);

        row.find(".wpcomment_meta_field_title").html(title);
        row.find(".wpcomment_meta_field_id").html(data_name);
        row.find(".wpcomment_meta_field_type").html(type);
        row.find(".wpcomment_meta_field_plchlder").html(placeholder);
        row.find(".wpcomment_meta_field_req").html(_ok);

        $(".wpcomment-modal-box, .wpcomment-modal-overlay").fadeOut('fast', function() {
            $(".wpcomment-modal-overlay").remove();
        });
    });


    /**
        12- Clone New Fields
    **/
    var option_index = 0;
    $(document).on('click', '.wpcomment_select_field', function(event) {
        event.preventDefault();

        $('#wpcomment_fields_model_id').find('.wpcomment-js-modal-close').trigger('click');

        var field_type = $(this).data('field-type');
        var clone_new_field = $(".wpcomment-field-" + field_type + ":last").clone();

        // field attr name apply on all fields meta with wpcomment-meta-field class
        clone_new_field.find('.wpcomment-meta-field').each(function(i, meta_field) {
            var field_name = 'wpcomment[' + field_no + '][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });

        // fields options sortable
        clone_new_field.find(".wpcomment-options-sortable").sortable();

        // add fields index in data-field-no
        clone_new_field.find(".wpcomment-fields-actions").attr('data-field-no', field_no);

        // fields conditions handle name attr
        clone_new_field.find('.wpcomment-condition-visible-bound').each(function(i, meta_field) {
            var field_name = 'wpcomment[' + field_no + '][conditions][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });

        clone_new_field.find('.wpcomment-fields-actions [data-meta-id="visibility_role"]').hide();

        var field_model_id = 'wpcomment_field_model_' + field_no + '';

        clone_new_field.find('.wpcomment_save_fields_model').end().appendTo('.wpcomment_save_fields_model').attr('id', field_model_id);
        clone_new_field.find('.wpcomment-field-checker').attr('data-field-index', field_no);
        clone_new_field.find('.wpcomment-field-checker').addClass('wpcomment-add-fields-js-action');

        // var color_picker_input = clone_new_field.find('.wpcomment-color-picker-init').clone();
        // clone_new_field.find('.wpcomment-color-picker-cloner').html(color_picker_input);
        // clone_new_field.find('.wpcomment-color-picker-init').wpColorPicker();
        // $('.wpcomment-color-picker-init').wpColorPicker();
        // $('.wpcomment-color-picker-init').wpColorPicker();

        // $('.wpcomment-color-picker-init').wpColorPicker('destroy');


        clone_new_field.addClass('wpcomment_sort_id_' + field_no + '');
        var field_index = field_no;

        // handle multiple options
        var wpcomment_option_type = '';
        var option_selector = clone_new_field.find('.wpcomment-option-keys');
        var option_controller = clone_new_field.find('.wpcomment-fields-option');
        var add_cond_selector = clone_new_field.find('.wpcomment-conditional-keys');

        // for address addon
        var address_selector = clone_new_field.find('.wpcomment-checkout-field');
        var address_table_id = clone_new_field.find('.wpcomment_address_table');
        wpcomment_create_address_index(address_selector, field_index, address_table_id);

        var wpcolor_selector = clone_new_field.find('.wpcomment-color-picker-cloner');
        wpcomment_wp_color_handler(wpcolor_selector, field_index, option_index);

        wpcomment_create_option_index(option_selector, field_index, option_index, wpcomment_option_type);
        wpcomment_option_controller(option_controller, field_index, option_index, wpcomment_option_type);
        wpcomment_add_condition_set_index(add_cond_selector, field_index, field_type, option_index);

        // popup fields on model
        wpcomment_close_popup();
        $('#wpcomment_field_model_' + field_no + '').fadeIn();

        field_no++;
    });


    /**
        13- Clone Existing Fields
    **/
    var copy_no = 0;
    $('.wpcomment-main-field-wrapper').on('click', '.wpcomment_copy_field', function(e) {
        e.preventDefault();

        var model_id_no = $(this).attr('id');

        var field_type = $(this).data('field-type');
        // console.log(model_id_no);

        var clone_new_field = $('.wpcomment_save_fields_model #wpcomment_field_model_' + model_id_no + '').clone(true);
        // clone_new_field.find('.wpcomment_save_fields_model').end().appendTo('.wpcomment_save_fields_model').attr('id','wpcomment_field_model_'+field_no+'');
        clone_new_field.find('.wpcomment_save_fields_model').end().insertAfter('#wpcomment_field_model_' + model_id_no + '').attr('id', 'wpcomment_field_model_' + field_no + '');
        clone_new_field.find('.wpcomment-add-fields-js-action').attr('data-field-index', field_no);
        clone_new_field.find('.wpcomment-close-fields').attr('data-field-index', field_no);
        clone_new_field.find('.wpcomment-js-modal-close').addClass('wpcomment-close-fields');
        clone_new_field.find('.wpcomment-add-fields-js-action').removeClass('wpcomment-update-field');
        clone_new_field.find('.wpcomment-add-fields-js-action').attr('data-copy-model-id', model_id_no);
        clone_new_field.find('.wpcomment-add-fields-js-action').addClass('wpcomment-add-field');
        clone_new_field.find('.wpcomment-add-fields-js-action').addClass('wpcomment-insertafter-field');
        clone_new_field.find('.wpcomment-add-fields-js-action').html('Add Field');
        clone_new_field.removeClass('wpcomment_sort_id_' + model_id_no + '');
        clone_new_field.addClass('wpcomment_sort_id_' + field_no + '');

        // field attr name apply on all fields meta with wpcomment-meta-field class 
        clone_new_field.find('.wpcomment-meta-field').each(function(i, meta_field) {
            var field_name = 'wpcomment[' + field_no + '][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });

        // fields options sortable
        clone_new_field.find(".wpcomment-options-sortable").sortable();

        // add fields index in data-field-no
        clone_new_field.find(".wpcomment-fields-actions").attr('data-field-no', field_no);

        // fields conditions handle name attr
        clone_new_field.find('.wpcomment-condition-visible-bound').each(function(i, meta_field) {
            var field_name = 'wpcomment[' + field_no + '][conditions][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });

        clone_new_field.find('.wpcomment-fields-actions [data-meta-id="visibility_role"]').hide();


        var field_index = field_no;

        // handle multiple options
        var wpcomment_option_type = 'wpcomment_copy_option';
        var option_selector = clone_new_field.find('.wpcomment-option-keys');
        var add_cond_selector = clone_new_field.find('.wpcomment-conditional-keys');
        var eventcalendar_selector = clone_new_field.find('.wpcomment-eventcalendar-field');
        var image_option_selector = clone_new_field.find('[data-table-id="image"] .data-options, [data-table-id="imageselect"] .data-options');

        // reset option to one
        // clone_new_field.find('[data-table-id="image"] .data-options').remove();
        clone_new_field.find('[data-table-id="audio"] .pre-upload-box li').remove();
        // clone_new_field.find('[data-table-id="imageselect"] .pre-upload-box li').remove();
        // clone_new_field.find('.data-options').not(':last').remove();
        clone_new_field.find('.webcontact-rules').not(':last').remove();

        // set existing conditions meta
        $(clone_new_field).find('select[data-metatype="elements"]').each(function(i, condition_element) {

            var existing_value1 = $(condition_element).attr("data-existingvalue");
            if ($.trim(existing_value1) !== '') {
                jQuery(condition_element).val(existing_value1);
            }
        });
        $(clone_new_field).find('select[data-metatype="element_values"]').each(function(i, condition_element) {

            var div = $(this).closest('.webcontact-rules');
            var existing_value1 = $(condition_element).attr("data-existingvalue");

            if ($.trim(existing_value1) !== '') {
                jQuery(condition_element).val(existing_value1);
            }
        });

        var wpcolor_selector = clone_new_field.find('.wpcomment-color-picker-cloner');
        wpcomment_wp_color_handler(wpcolor_selector, field_index, option_index);

        wpcomment_create_option_index(option_selector, field_index, option_index, wpcomment_option_type);
        var option_controller = clone_new_field.find('.wpcomment-fields-option');

        wpcomment_option_controller(option_controller, field_index, option_index, wpcomment_option_type);
        wpcomment_add_condition_set_index(add_cond_selector, field_index, field_type, option_index);

        // for eventcalendar changing index
        wpcomment_eventcalendar_set_index(eventcalendar_selector, field_index);

        // set index for all images fields
        image_option_selector.find('input').each(function(img_index, img_meta) {

            var opt_in = $(img_meta).attr('data-opt-index');
            var field_name = 'wpcomment[' + field_index + '][images][' + opt_in + '][' + $(img_meta).attr('data-metatype') + ']';
            $(img_meta).attr('name', field_name);
        });

        // popup fields on model
        $("body").append(append_overly_model);
        wpcomment_close_popup();
        $('#wpcomment_field_model_' + field_no + '').fadeIn();

        field_no++;
    });


    /**
        14- Saving WP Comment Settings
    **/
    $(".wpcomment-settings-form").on('submit', function(ev) {

        ev.preventDefault();
        var data = $(this).serialize();

        // return;
        $.post(ajaxurl, data, function(resp) {

            alert(resp.message);

        }, 'json');
    });


    /**
        16- Handle Fields Tabs
    **/
    $('.wpcomment-control-all-fields-tabs').hide();
    $('.wpcomment_handle_fields_tab').show();
    $(document).on('click', '.wpcomment-tabs-label', function() {

        var id = $(this).attr('id');
        var selectedTab = $(this).parent();
        var fields_wrap = selectedTab.parent();
        selectedTab.find('.wpcomment-tabs-label').removeClass('wpcomment-active-tab');
        $(this).addClass('wpcomment-active-tab');
        var content_box = fields_wrap.find('.wpcomment-control-all-fields-tabs');
        content_box.hide();
        fields_wrap.find('.wpcomment_handle_' + id).fadeIn(200);
    });


    /**
        17- Handle Media Images Of Following Inputs Types
            17.1- Pre-Images Type
            17.2- Audio Type
            17.3- Imageselect Type
    **/
    var $uploaded_image_container;
    $(document).on('click', '.wpcomment-pre-upload-image-btn', function(e) {
        e.preventDefault();

        var meta_type = $(this).attr('data-metatype');
        $uploaded_image_container = $(this).closest('div');
        var image_append = $uploaded_image_container.find('ul');
        var option_index = parseInt($uploaded_image_container.find('#wpcomment-meta-opt-index').val());
        var main_wrapper = $(this).closest('.wpcomment-slider');
        var field_index = main_wrapper.find('.wpcomment-fields-actions').attr('data-field-no');
        var price_placeholder = 'Price (fix or %)';

        var wp_media_type = 'image';
        if (meta_type == 'audio') {
            wp_media_type = 'audio,video';
        }

        var button = $(this),
            custom_uploader = wp.media({
                title: 'Choose File',
                library: {
                    type: wp_media_type
                },
                button: {
                    text: 'Upload'
                },
                multiple: true
            }).on('select', function() {

                var attachments = custom_uploader.state().get('selection').toJSON();

                attachments.map((meta, index) => {
                    // console.log(meta);
                    var fileurl = meta.url;
                    var fileid = meta.id;
                    var filename = meta.filename;
                    var file_title = meta.title;


                    var img_icon = '<img width="60" src="' + fileurl + '" style="width: 34px;">';
                    var url_field = '<input placeholder="url" type="text" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][url]" class="form-control" data-opt-index="' + option_index + '" data-metatype="url">';

                    if (meta.type !== 'image') {
                        var img_icon = '<img width="60" src="' + meta.icon + '" style="width: 34px;">';
                        url_field = '';
                    }

                    var price_metatype = 'price';
                    var stock_metatype = 'stock';
                    var stock_placeholder = 'Stock';

                    // Set name key for imageselect addon
                    if (meta_type == 'imageselect') {
                        var class_name = 'data-options ui-sortable-handle';
                        var condidtion_attr = 'image_options';
                        meta_type = 'images';
                        price_placeholder = 'Price';
                        url_field = '<input placeholder="Description" type="text" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][description]" class="form-control" data-opt-index="' + option_index + '" data-metatype="description">';
                    }
                    else if (meta_type == 'images') {
                        var class_name = 'data-options ui-sortable-handle';
                        var condidtion_attr = 'image_options';
                    }
                    else if (meta_type == 'conditional_meta') {
                        meta_type = 'images';
                        var class_name = 'data-options ui-sortable-handle';
                        var condidtion_attr = 'image_options';
                        price_placeholder = 'Meta IDs';
                        price_metatype = 'meta_id';
                    }
                    else {
                        var class_name = '';
                        var condidtion_attr = '';
                    }

                    if (fileurl) {
                        var image_box = '';
                        image_box += '<li class="' + class_name + '" data-condition-type="' + condidtion_attr + '">';
                        image_box += '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';
                        image_box += '<span class="wpcomment-uploader-img-title"></span>';
                        image_box += '<div style="display: flex;">';
                        image_box += '<div class="wpcomment-uploader-img-center">';
                        image_box += img_icon;
                        image_box += '</div>';
                        image_box += '<input type="hidden" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][link]" value="' + fileurl + '" data-opt-index="' + option_index + '" data-metatype="link">';
                        image_box += '<input type="hidden" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][id]" value="' + fileid + '" data-opt-index="' + option_index + '" data-metatype="id">';
                        image_box += '<input type="text" placeholder="Title" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][title]" class="form-control wpcomment-image-option-title" data-opt-index="' + option_index + '" data-metatype="title" value="' + file_title + '">';
                        image_box += '<input class="form-control" type="text" placeholder="' + price_placeholder + '" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][' + price_metatype + ']" class="form-control" data-opt-index="' + option_index + '" data-metatype="' + price_metatype + '">';

                        if (meta_type != 'audio') {
                            image_box += '<input class="form-control" type="text" placeholder="' + stock_placeholder + '" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][' + stock_metatype + ']" class="form-control" data-opt-index="' + option_index + '" data-metatype="' + stock_metatype + '">';
                        }

                        image_box += url_field;
                        image_box += '<button class="btn btn-danger wpcomment-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
                        image_box += '</div>';
                        image_box += '</li>';

                        $(image_box).appendTo(image_append);

                        option_index++;
                    }

                });

                $uploaded_image_container.find('#wpcomment-meta-opt-index').val(option_index);

            }).open();
    });

    var $uploaded_image_container;
    $(document).on('click', '.wpcomment-pre-upload-image-btnsd', function(e) {

        e.preventDefault();
        var meta_type = $(this).attr('data-metatype');
        $uploaded_image_container = $(this).closest('div');
        var image_append = $uploaded_image_container.find('ul');
        var option_index = parseInt($uploaded_image_container.find('#wpcomment-meta-opt-index').val());
        $uploaded_image_container.find('#wpcomment-meta-opt-index').val(option_index + 1);
        var main_wrapper = $(this).closest('.wpcomment-slider');
        var field_index = main_wrapper.find('.wpcomment-fields-actions').attr('data-field-no');
        var price_placeholder = 'Price (fix or %)';
        wp.media.editor.send.attachment = function(props, attachment) {
            // console.log(attachment);
            var existing_images;
            var fileurl = attachment.url;
            var fileid = attachment.id;
            var img_icon = '<img width="60" src="' + fileurl + '" style="width: 34px;">';
            var url_field = '<input placeholder="url" type="text" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][url]" class="form-control" data-opt-index="' + option_index + '" data-metatype="url">';

            if (attachment.type !== 'image') {
                var img_icon = '<img width="60" src="' + attachment.icon + '" style="width: 34px;">';
                url_field = '';
            }

            var price_metatype = 'price';
            var stock_metatype = 'stock';
            var stock_placeholder = 'Stock';

            // Set name key for imageselect addon
            if (meta_type == 'imageselect') {
                var class_name = 'data-options ui-sortable-handle';
                var condidtion_attr = 'image_options';
                meta_type = 'images';
                price_placeholder = 'Price';
                url_field = '<input placeholder="Description" type="text" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][description]" class="form-control" data-opt-index="' + option_index + '" data-metatype="description">';
            }
            else if (meta_type == 'images') {
                var class_name = 'data-options ui-sortable-handle';
                var condidtion_attr = 'image_options';
            }
            else if (meta_type == 'conditional_meta') {
                meta_type = 'images';
                var class_name = 'data-options ui-sortable-handle';
                var condidtion_attr = 'image_options';
                price_placeholder = 'Meta IDs';
                price_metatype = 'meta_id';
            }
            else {
                var class_name = '';
                var condidtion_attr = '';
            }

            if (fileurl) {
                var image_box = '';
                image_box += '<li class="' + class_name + '" data-condition-type="' + condidtion_attr + '">';
                image_box += '<span class="dashicons dashicons-move" style="margin-bottom: 7px;margin-top: 2px;"></span>';
                image_box += '<span class="wpcomment-uploader-img-title"></span>';
                image_box += '<div style="display: flex;">';
                image_box += '<div class="wpcomment-uploader-img-center">';
                image_box += img_icon;
                image_box += '</div>';
                image_box += '<input type="hidden" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][link]" value="' + fileurl + '" data-opt-index="' + option_index + '" data-metatype="link">';
                image_box += '<input type="hidden" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][id]" value="' + fileid + '" data-opt-index="' + option_index + '" data-metatype="id">';
                image_box += '<input type="text" placeholder="Title" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][title]" class="form-control wpcomment-image-option-title" data-opt-index="' + option_index + '" data-metatype="title">';
                image_box += '<input class="form-control" type="text" placeholder="' + price_placeholder + '" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][' + price_metatype + ']" class="form-control" data-opt-index="' + option_index + '" data-metatype="' + price_metatype + '">';
                image_box += '<input class="form-control" type="text" placeholder="' + stock_placeholder + '" name="wpcomment[' + field_index + '][' + meta_type + '][' + option_index + '][' + stock_metatype + ']" class="form-control" data-opt-index="' + option_index + '" data-metatype="' + stock_metatype + '">';
                image_box += url_field;
                image_box += '<button class="btn btn-danger wpcomment-pre-upload-delete" style="height: 35px;"><i class="fa fa-times" aria-hidden="true"></i></button>';
                image_box += '</div>';
                image_box += '</li>';

                $(image_box).appendTo(image_append);
            }
        }

        wp.media.editor.open(this);

        return false;
    });
    $(document).on('click', '.wpcomment-pre-upload-delete', function(e) {

        e.preventDefault();
        $(this).closest('li').remove();
    });


    /**
        18- Add Fields Conditions
    **/
    $(document).on('click', '.wpcomment-add-rule', function(e) {

        e.preventDefault();

        var div = $(this).closest('.wpcomment-slider');
        var option_index = parseInt(div.find('.wpcomment-condition-last-id').val());
        div.find('.wpcomment-condition-last-id').val(option_index + 1);

        var field_index = div.find('.wpcomment-fields-actions').attr('data-field-no');
        var condition_clone = $('.webcontact-rules:last').clone();

        var append_item = div.find('.wpcomment-condition-clone-js');
        condition_clone.find(append_item).end().appendTo(append_item);

        var field_type = '';
        var add_cond_selector = condition_clone.find('.wpcomment-conditional-keys');
        wpcomment_add_condition_set_index(add_cond_selector, field_index, field_type, option_index);

        $('.wpcomment-slider').find('.webcontact-rules:not(:last) .wpcomment-add-rule')
            .removeClass('wpcomment-add-rule').addClass('wpcomment-remove-rule')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<i class="fa fa-minus" aria-hidden="true"></i>');
    }).on('click', '.wpcomment-remove-rule', function(e) {

        $(this).parents('.webcontact-rules:first').remove();
        e.preventDefault();
        return false;
    });


    /**
        19- Add Fields Options
    **/
    $(document).on('click', '.wpcomment-add-option', function(e) {

        e.preventDefault();

        var main_wrapper = $(this).closest('.wpcomment-slider');
        var wpcomment_option_type = 'wpcomment_new_option';

        var li = $(this).closest('li');
        var ul = li.closest('ul');
        var clone_item = li.clone();

        clone_item.find(ul).end().appendTo(ul);

        var option_index = parseInt(ul.find('#wpcomment-meta-opt-index').val());
        ul.find('#wpcomment-meta-opt-index').val(option_index + 1);
        console.log(option_index);

        var field_index = main_wrapper.find('.wpcomment-fields-actions').attr('data-field-no');
        var option_selector = clone_item.find('.wpcomment-option-keys');
        var option_controller = clone_item.find('.wpcomment-fields-option');

        wpcomment_option_controller(option_controller, field_index, option_index, wpcomment_option_type);
        wpcomment_create_option_index(option_selector, field_index, option_index, wpcomment_option_type);

        // $('.wpcomment-slider').find('.data-options:not(:last) .wpcomment-add-option')
        // .removeClass('wpcomment-add-option').addClass('wpcomment-remove-option')
        // .removeClass('btn-success').addClass('btn-danger')
        // .html('<i class="fa fa-minus" aria-hidden="true"></i>');
    }).on('click', '.wpcomment-remove-option', function(e) {

        var selector_btn = $(this).closest('.wpcomment-slider');
        var option_num = selector_btn.find('.data-options').length;

        if (option_num > 1) {
            $(this).parents('.data-options:first').remove();
        }
        else {
            alert('Cannot Remove More Option');
        }

        e.preventDefault();
        return false;
    });


    /**
        20- Auto Generate Option IDs
    **/
    $(document).on('keyup', '.option-title', function() {

        var closes_id = $(this).closest('li').find('.option-id');
        var option_id = $(this).val().replace(/[^A-Z0-9]/ig, "_");
        option_id = option_id.toLowerCase();
        $(closes_id).val(option_id);
    });


    /**
        21- Create Field data_name By Thier Title
    **/
    $(document).on('keyup', '[data-meta-id="title"] input[type="text"]', function() {

        var $this = $(this);
        var field_id = $this.val().toLowerCase().replace(/[^A-Za-z\d]/g, '_');
        var selector = $this.closest('.wpcomment-slider');

        var wp_field = selector.find('.wpcomment-fields-actions').attr('data-table-id');
        if (wp_field == 'shipping_fields' || wp_field == 'billing_fields') {
            return;
        }
        selector.find('[data-meta-id="data_name"] input[type="text"]').val(field_id);
    });


    /**
        22- Fields Sortable
    **/
    function insertAt(parent, element, index, dir) {
        var el = parent.children().eq(index);

        element[dir == 'top' ? 'insertBefore' : 'insertAfter'](el);
    }
    $(".wpcomment_field_table tbody").sortable({
        stop: function(evt, ui) {

            let parent = $('.wpcomment_save_fields_model'),
                el = parent.find('.' + ui.item.attr('id')),
                dir = 'top';
            if (ui.offset.top > ui.originalPosition.top) {
                dir = 'bottom';
            }
            insertAt(parent, el, ui.item.index(), dir);
        }
    });


    /**
        23- Fields Option Sortable
    **/
    $(".wpcomment-options-sortable").sortable();

    $("ul.wpcomment-options-container").sortable({
        revert: true
    });


    /**
        24- Fields Dataname Must Be Required
    **/
    function wpcomment_required_data_name($this) {
        var selector = $this.closest('.wpcomment-slider');
        var data_name = selector.find('[data-meta-id="data_name"] input[type="text"]').val();
        if (data_name == '') {
            var msg = 'Data Name must be required';
            var is_ok = false;
        }
        else {
            msg = '';
            is_ok = true;
        }
        selector.find('.wpcomment-req-field-id').html(msg);
        return is_ok;
    }


    /**
        WP Color Picker Controller
    **/
    function wpcomment_wp_color_handler(wpcolor_selector, field_index, option_index) {

        wpcolor_selector.each(function(i, meta_field) {
            var color_picker_input = $(meta_field).find('.wpcomment-color-picker-init').clone();
            $(meta_field).html(color_picker_input);
            color_picker_input.wpColorPicker();
        });
    }


    /**
        25- Fields Add Option Index Controle Funtion
    **/
    function wpcomment_create_option_index(option_selector, field_index, option_index, wpcomment_option_type) {

        option_selector.each(function(i, meta_field) {


            if (wpcomment_option_type == 'wpcomment_copy_option') {
                var opt_in = $(meta_field).attr('data-opt-index');
                if (opt_in !== undefined) {
                    option_index = opt_in;
                }
            }
            $(meta_field).attr('data-opt-index', option_index);


            var field_name = 'wpcomment[' + field_index + '][options][' + option_index + '][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });
    }


    function wpcomment_option_controller(option_selector, field_index, option_index, wpcomment_option_type) {

        option_selector.each(function(i, meta_field) {

            console.log(wpcomment_option_type);
            if (wpcomment_option_type == 'wpcomment_copy_option') {
                var opt_in = $(meta_field).attr('data-opt-index');
                if (opt_in !== undefined) {
                    option_index = opt_in;
                }
            }
            $(meta_field).attr('data-opt-index', option_index);


            var field_name = 'wpcomment[' + field_index + '][' + $(meta_field).attr('data-optiontype') + '][' + option_index + '][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });
    }


    /**
        26- Fields Add Condition Index Controle Function
    **/
    function wpcomment_add_condition_set_index(add_c_selector, opt_field_no, field_type, opt_no) {
        add_c_selector.each(function(i, meta_field) {
            // var field_name = 'wpcomment['+field_no+']['+$(meta_field).attr('data-metatype')+']';
            var field_name = 'wpcomment[' + opt_field_no + '][conditions][rules][' + opt_no + '][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });
    }

    // address addon
    function wpcomment_create_address_index(address_selector, field_index, address_table_id) {
        address_selector.each(function(i, meta_field) {
            var field_id = $(meta_field).attr('data-fieldtype');
            var core_field_type = $(address_table_id).attr('data-addresstype');
            var field_name = 'wpcomment[' + field_index + '][' + core_field_type + '][' + field_id + '][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });
    }


    // eventcalendar inputs changing
    function wpcomment_eventcalendar_set_index(add_c_selector, opt_field_no) {

        add_c_selector.each(function(i, meta_field) {

            var date = $(meta_field).attr('data-date');
            var field_name = 'wpcomment[' + opt_field_no + '][calendar][' + date + '][' + $(meta_field).attr('data-metatype') + ']';
            $(meta_field).attr('name', field_name);
        });
    }


    /**
        27- Get All Fields Title On Condition Element Value After Click On Condition Tab
    **/
    // populate_conditional_elements();

    $(document).on('change', 'select[data-metatype="elements"]', function(e) {
        e.preventDefault();

        var element_name = $(this).val();
        var div = $(this).closest('.wpcomment-slider');

        var selected_rule_box = $(this).closest('.webcontact-rules');
        var element_value_box = selected_rule_box.find('select[data-metatype="element_values"]');

        $(".wpcomment-slider").each(function(i, item) {

            var data_name = $(item).find('input[data-metatype="data_name"]').val();

            if (data_name == element_name) {

                // resetting
                jQuery(element_value_box).html('');

                $(item).find('.data-options').each(function(i, condition_val) {

                    var condition_type = $(condition_val).attr('data-condition-type');
                    if (condition_type == 'simple_options') {
                        var con_val = $(condition_val).find('input[data-metatype="option"]').val();
                    }
                    else if (condition_type == 'image_options') {
                        var con_val = $(condition_val).find('.wpcomment-image-option-title').val();
                    }

                    if ($.trim(con_val) !== '') {


                        var val_id = $.trim(con_val);

                        var $html = '';
                        $html += '<option value="' +
                            wpcomment_escape_html(val_id) + '">' +
                            con_val +
                            '</option>';

                        $($html).appendTo(element_value_box);
                    }
                });
            }
        });
    });

    $(document).on('change', '[data-meta-id="conditions"] select[data-metatype="element_values"]', function(e) {
        e.preventDefault();

        var element_values = $(this).val();
        $(this).attr('data-existingvalue', element_values);
    });

    $(document).on('click', '.wpcomment-condition-tab-js', function(e) {
        e.preventDefault();

        var div = $(this).closest('.wpcomment-slider');
        var elements = div.find('select[data-metatype="elements"]');

        elements.each(function(i, item) {

            var conditional_elements = item.value;
            var exiting_meta = $(item).attr('data-existingvalue', conditional_elements);
        });

        populate_conditional_elements(elements);

    });

    function populate_conditional_elements(elements) {

        // resetting
        jQuery('select[data-metatype="elements"]').html('');

        jQuery(".wpcomment-slider").each(function(i, item) {

            var conditional_elements = jQuery(item).find(
                'input[data-metatype="title"]').val();
            var conditional_elements_value = jQuery(item).find(
                'input[data-metatype="data_name"]').val();

            if ($.trim(conditional_elements_value) !== '') {

                var $html = '';
                $html += '<option value="' +
                    conditional_elements_value + '">' +
                    conditional_elements +
                    '</option>';

                $($html).appendTo('select[data-metatype="elements"]');
            }

        });

        // setting the existing conditional elements
        $(".wpcomment-slider").each(function(i, item) {

            $(item).find('select[data-metatype="elements"]').each(function(i, condition_element) {

                var existing_value1 = $(condition_element).attr("data-existingvalue");

                if ($.trim(existing_value1) !== '') {
                    jQuery(condition_element).val(existing_value1);
                }

            });
        });



        // setting the existing conditional elements values
        $(".wpcomment-slider").each(function(i, item) {

            $(item).find('select[data-metatype="element_values"]').each(function(i, condition_element) {

                var div = $(this).closest('.webcontact-rules');
                var existing_value1 = $(condition_element).attr("data-existingvalue");

                div.find('select[data-metatype="elements"]').trigger('change');
                if ($.trim(existing_value1) !== '') {
                    jQuery(condition_element).val(existing_value1);
                }
            });
        });

    }


    /**
        28- validate API WooCommerce Product
    **/
    function validate_api_WPC(form) {

        jQuery(form).find("#nm-sending-api").html(
            '<img src="' + nm_personalizedproduct_vars.doing + '">');

        var data = jQuery(form).serialize();
        data = data + '&action=nm_personalizedproduct_validate_api';

        jQuery.post(ajaxurl, data, function(resp) {

            //console.log(resp);
            jQuery(form).find("#nm-sending-api").html(resp.message);
            if (resp.status == 'success') {
                window.location.reload(true);
            }
        }, 'json');


        return false;
    }

    function wpcomment_escape_html(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    /**
     * 29 Toggling settings and fields
     * */
    // by defualt wpcomment-settings-wrapper is hidden
    $('.wpcomment-settings-wrapper').hide();
    $(document).on('click', 'button.wpcomment_settings_toggle', function(e) {
        e.preventDefault();
        $('.wpcomment-main-field-wrapper').toggle();
        $('.wpcomment-settings-wrapper').toggle();
        
    })
});
