<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- style css -->
 <style>
        /* Style for preview images */
        #image-preview {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .preview-image {
            width: 150px;
            margin-right: 10px;
            margin-bottom: 10px;
            cursor: pointer; /* Add pointer cursor for clickable images */
        }

        .preview-image img {
            width: 100%;
            height: auto;
        }

        /* Style for modal */
        #image-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 50%;
            width: 100%;
            height: auto;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }

        #modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            padding: 20px;
            text-align: center;
        }

        #modal-content img {
            width: 100%;
            height: auto;
            max-height: 100%;
        }

        .close {
            color: white;
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 35px;
            cursor: pointer;
        }
    </style>
<!-- end css -->
<div class="modal fade" id="sales_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('invoice_item_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('invoice_item_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open_multipart('admin/invoice_items/manage', ['id' => 'invoice_item_form']); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                        <?php echo render_input('description', 'invoice_item_add_edit_description'); ?>
                        <?php echo render_textarea('long_description', 'invoice_item_long_description'); ?>
                        
                        <div class="form-group">
                            <label for="rate" class="control-label">
                            <?php echo _l('invoice_item_add_edit_rate_currency', $base_currency->name . ' <small>(' . _l('base_currency_string') . ')</small>'); ?></label>
                            <input type="number" id="rate" name="rate" class="form-control" value="">
                        </div>
                        <?php
                            foreach ($currencies as $currency) {
                                if ($currency['isdefault'] == 0 && total_rows(db_prefix() . 'clients', ['default_currency' => $currency['id']]) > 0) { ?>
                                <div class="form-group">
                                    <label for="rate_currency_<?php echo $currency['id']; ?>" class="control-label">
                                        <?php echo _l('invoice_item_add_edit_rate_currency', $currency['name']); ?></label>
                                        <input type="number" id="rate_currency_<?php echo $currency['id']; ?>" name="rate_currency_<?php echo $currency['id']; ?>" class="form-control" value="">
                                    </div>
                             <?php   }
                            }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                             <div class="form-group">
                                <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
                                <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('no_tax'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($taxes as $tax) { ?>
                                    <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                         <div class="form-group">
                            <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
                            <select class="selectpicker display-block" disabled data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('no_tax'); ?>">
                                <option value=""></option>
                                <?php foreach ($taxes as $tax) { ?>
                                <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="clearfix mbot15"></div>
                <?php echo render_input('unit', 'unit'); ?>
                
                <div id="custom_fields_items">
                    <?php echo render_custom_fields('items'); ?>
                </div>

                <?php echo render_select('group_id', $items_groups, ['id', 'name'], 'item_group'); ?>
                <!-- add images for item -->
                <input type="hidden" name="get_rand_number" class="set_rand_number" value="0">
                <div class="form-group" app-field-wrapper="item_images">
                    <label for="item_images" class="control-label">Item Images (Select Multiple)</label>
                    <input type="file" multiple id="item_images" onchange="item_img()" name="item_images[]" class="form-control">
                    <div id="uploaded_images"></div>
                </div>
                <div id="image-preview"></div>
                 <!-- Modal for image preview -->
                <div id="image-modal">
                    <div id="modal-content">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <img id="modal-image">
                    </div>
                </div>
                <!-- end add image for item -->
                <?php hooks()->do_action('before_invoice_item_modal_form_close'); ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
</div>
<script>
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if(typeof(jQuery) != 'undefined'){
        init_item_js();
    } else {
     window.addEventListener('load', function () {
       var initItemsJsInterval = setInterval(function(){
            if(typeof(jQuery) != 'undefined') {
                init_item_js();
                clearInterval(initItemsJsInterval);
            }
         }, 1000);
     });
  }
// Items add/edit
function manage_invoice_items(form) {

    // Serialize the form data
    var data = $(form).serialize();

    var url = form.action;
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            // pre load
            
            var item_select = $('#item_select');
            
            if ($("body").find('.accounting-template').length > 0) {
                if (!item_select.hasClass('ajax-search')) {
                    var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                    if (group.length == 0) {
                        var _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
                        if (item_select.find('[data-group-id="0"]').length == 0) {
                            item_select.find('option:first-child').after(_option);
                        } else {
                            item_select.find('[data-group-id="0"]').after(_option);
                        }
                    } else {
                        group.prepend('<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>');
                    }
                }
                if (!item_select.hasClass('ajax-search')) {
                    item_select.selectpicker('refresh');
                } else {

                    item_select.contents().filter(function () {
                        return !$(this).is('.newitem') && !$(this).is('.newitem-divider');
                    }).remove();

                    var clonedItemsAjaxSearchSelect = item_select.clone();
                    item_select.selectpicker('destroy').remove();
                    $("body").find('.items-select-wrapper').append(clonedItemsAjaxSearchSelect);
                    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
                }
                
                add_item_to_preview(response.item.itemid);
            } else {
                // Is general items view
                $('.table-invoice-items').DataTable().ajax.reload(null, false);
            }
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
    return false;
}


function item_img() {
  
        var fd = new FormData();
        var files = $('input[name="item_images[]"]')[0].files;
        for (var i = 0; i < files.length; i++) {
            fd.append('files[]', files[i]);
        }
        // Append CSRF token to FormData
        fd.append('<?php echo $this->security->get_csrf_token_name() ?>', '<?php echo $this->security->get_csrf_hash() ?>');
        $.ajax({
            url: "<?=base_url();?>admin/invoice_items/upload_item_image",
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
                if(response != 0){
                    
                    $('.set_rand_number').val(response);
                    $('.show_tr').css('display','table-row');
                    
                }else{
                    alert('file not uploaded');
                }
            },
        });
}


function init_item_js() {
     // Add item to preview from the dropdown for invoices estimates
    $("body").on('change', 'select[name="item_select"]', function () {
        var itemid = $(this).selectpicker('val');
        if (itemid != '') {
            
            add_item_to_preview(itemid);
        }
    });

    // Items modal show action
    $("body").on('show.bs.modal', '#sales_item_modal', function (event) {

        $('.affect-warning').addClass('hide');
        var $itemModal = $('#sales_item_modal');
        $('input[name="itemid"]').val('');
        $('input[name="item_images"]').val('');
        $('#image-preview').html('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');

        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);

            requestGetJSON('invoice_items/get_item_by_id/' + id).done(function (response) {
                $itemModal.find('input[name="description"]').val(response.description);
                $itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
                $itemModal.find('input[name="rate"]').val(response.rate);
                $itemModal.find('input[name="unit"]').val(response.unit);
                $('select[name="tax"]').selectpicker('val', response.taxid).change();
                $('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
                $itemModal.find('#group_id').selectpicker('val', response.group_id);
                $.each(response, function (column, value) {
                    if (column.indexOf('rate_currency_') > -1) {
                        $itemModal.find('input[name="' + column + '"]').val(value);
                    }
                });

                $('#custom_fields_items').html(response.custom_fields_html);

                init_selectpicker();
                init_color_pickers();
                init_datepicker();

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_item_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#sales_item_modal', function (event) {
        $('#item_select').selectpicker('val', '');
    });

   validate_item_form();
}


function validate_item_form(){
    // Set validation for invoice item form
    appValidateForm($('#invoice_item_form'), {
        description: 'required',
        rate: {
            required: true,
        }
    }, manage_invoice_items);
}
</script>
 <script>
   // Function to handle file input change event
      document.getElementById('item_images').addEventListener('change', function() {
        var previewContainer = document.getElementById('image-preview');
        var files = this.files;

        // Clear previous previews
        previewContainer.innerHTML = '';

        // Loop through selected files and create previews
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            var allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];

            // Check if the file type is allowed
            if (allowedTypes.includes(file.type)) {
                reader.onload = function(event) {
                    var imageUrl = event.target.result;
                    var preview = document.createElement('div');
                    preview.className = 'preview-image';
                    preview.innerHTML = '<img src="' + imageUrl + '" alt="Image Preview" onclick="openModal(\'' + imageUrl + '\')">';
                    previewContainer.appendChild(preview);
                }
                reader.readAsDataURL(file);
            }
        }
    });


        // Function to open modal and display clicked image
        function openModal(imageUrl) {
            var modal = document.getElementById('image-modal');
            var modalImage = document.getElementById('modal-image');
            modalImage.src = imageUrl;
            modal.style.display = 'block';
        }

        // Function to close modal
        function closeModal() {
            var modal = document.getElementById('image-modal');
            modal.style.display = 'none';
        }
</script>
