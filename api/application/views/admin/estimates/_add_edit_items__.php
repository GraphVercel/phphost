<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body">
    <div class="row">
        <div class="col-md-4">
            <?php $this->load->view('admin/invoice_items/item_select'); ?>
        </div>
        <div class="col-md-8 text-right show_quantity_as_wrapper">
            <div class="mtop10">
                <span><?php echo _l('show_quantity_as'); ?></span>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="1" id="1" name="show_quantity_as"
                        data-text="<?php echo _l('estimate_table_quantity_heading'); ?>"
                        <?php echo isset($estimate) && $estimate->show_quantity_as == 1 ? 'checked' : 'checked'; ?>>
                    <label for="1"><?php echo _l('quantity_as_qty'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="2" id="2" name="show_quantity_as"
                        data-text="<?php echo _l('estimate_table_hours_heading'); ?>"
                        <?php echo isset($estimate) && $estimate->show_quantity_as == 2 ? 'checked' : ''; ?>>
                    <label for="2"><?php echo _l('quantity_as_hours'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" id="3" value="3" name="show_quantity_as"
                        data-text="<?php echo _l('estimate_table_quantity_heading'); ?>/<?php echo _l('estimate_table_hours_heading'); ?>"
                        <?php echo isset($estimate) && $estimate->show_quantity_as == 3 ? 'checked' : ''; ?>>
                    <label for="3">
                        <?php echo _l('estimate_table_quantity_heading'); ?>/<?php echo _l('estimate_table_hours_heading'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <!-- added by ak -->
   <style type="text/css">

    .image-container {
    display: flex; 
    flex-wrap: nowrap; 
    }
    .preview-image {
        width: 180px;
        margin-right: 10px;
        margin-bottom: 10px;
        cursor: pointer; /* Add pointer cursor for clickable images */
    }

    /* Modal Content */
    .modal-content {
      display: block;
      margin: auto;
      width: 100%;
      max-width: 700px;
    }

    /* Modal Background Overlay */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      padding-top: 100px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      overflow: auto;
    }

    /* Close Button */
    .close_invoice {
      color: white;
      position: absolute;
      top: 10px;
      right: 25px;
      font-size: 35px;
      font-weight: bold;
    }

    .close_invoice:hover,
    .close_invoice:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    /*images upload design in td by ak*/
    .file-upload {
      position: relative;
      overflow: hidden;
      display: inline-block;
      cursor: pointer;
    }

    .file-input {
      position: absolute;
      left: -9999px;
    }

    .fa-upload {
      margin-right: 5px; /* Adjust spacing as needed */
      color: #007bff;
    }

    /*end img upload design*/
    .item-container {
        position: relative;
        display: inline-block;

    }

    .cross-icon {
        position: absolute;
        top: 0px;
        right: 11px;
        background: #ffffff;
        color: red;
        font-size: 17px;
        cursor: pointer;
        z-index: 1;
        border-radius: 23px;
        height: auto;
        width: 15%;
        padding: 0px 6px 0px 0px;
    }
    </style>
    <div class="table-responsive s_table">
        <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
            <thead>
                <tr>
                    <th></th>
                    <th width="20%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1" aria-hidden="true"
                            data-toggle="tooltip"
                            data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i>
                        <?php echo _l('estimate_table_item_heading'); ?></th>
                    <th width="25%" align="left"><?php echo _l('estimate_table_item_description'); ?></th>
                    <?php
                  $custom_fields = get_custom_fields('items');
                  foreach ($custom_fields as $cf) {
                      echo '<th width="15%" align="left" class="custom_field">' . $cf['name'] . '</th>';
                  }

                  $qty_heading = _l('estimate_table_quantity_heading');
                  if (isset($estimate) && $estimate->show_quantity_as == 2) {
                      $qty_heading = _l('estimate_table_hours_heading');
                  } elseif (isset($estimate) && $estimate->show_quantity_as == 3) {
                      $qty_heading = _l('estimate_table_quantity_heading') . '/' . _l('estimate_table_hours_heading');
                  }
                  ?>
                    <th width="10%" class="qty" align="right"><?php echo $qty_heading; ?></th>
                    <th width="15%" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
                    <th width="20%" align="right"><?php echo _l('estimate_table_tax_heading'); ?></th>
                    <th width="10%" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
                    <th align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                <tr class="main">
                    <td></td>
                    <td>
                        <textarea name="description" rows="4" class="form-control"
                            placeholder="<?php echo _l('item_description_placeholder'); ?>"></textarea>
                    </td>
                    <td>
                        <textarea name="long_description" rows="4" class="form-control"
                            placeholder="<?php echo _l('item_long_description_placeholder'); ?>"></textarea>
                    </td>
                    <?php echo render_custom_fields_items_table_add_edit_preview(); ?>
                    <td>
                        <input type="number" name="quantity" min="0" value="1" class="form-control"
                            placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
                        <input type="text" placeholder="<?php echo _l('unit'); ?>" data-toggle="tooltip" 612
                            data-title="e.q kg, lots, packs" name="unit"
                            class="form-control input-transparent text-right">
                    </td>
                    <td>
                        <input type="number" name="rate" class="form-control"
                            placeholder="<?php echo _l('item_rate_placeholder'); ?>">
                              <!-- added by ak -->
                                <div class="get_itemid"></div>
                                <div id="base-url" data-base-url="<?= base_url(); ?>"></div>
                                <!-- end  -->
                    </td>
                    <td>
                        <?php
                     $default_tax = unserialize(get_option('default_tax'));
                     $select      = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="' . _l('no_tax') . '">';
                     foreach ($taxes as $tax) {
                         $selected = '';
                         if (is_array($default_tax)) {
                             if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                 $selected = ' selected ';
                             }
                         }
                         $select .= '<option value="' . $tax['name'] . '|' . $tax['taxrate'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
                     }
                     $select .= '</select>';
                     echo $select;
                     ?>
                    </td>
                    <td>
                        <!-- added by ak -->
                        <input type="hidden" name="get_rand_number" class="set_rand_number_invoice" value="0">
                        <label for="item_images_invoice" class="file-upload">
                          <input type="file" multiple id="item_images_invoice" onchange="item_img_invoice()" name="item_images_invoice[]" class="form-control file-input">
                          <i class="fas fa-upload"></i> Upload
                        </label>
                        <!-- end -->
                    </td>
                    <td>
                        <?php
                     $new_item = 'undefined';
                     if (isset($estimate)) {
                         $new_item = true;
                     }
                     ?>
                        <button type="button"
                            onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;"
                            class="btn pull-right btn-primary btnSetForItemId"><i class="fa fa-check"></i></button>
                    </td>
                </tr>
                <!-- ADDED BY AK -->
                    <tr class="show_tr" style="background-color: #f8fafc;border: 1px solid #e2e8f0;display: none;">
                        <td colspan="4" class="set_item_images">&nbsp;</td>
                        <td style="padding-left: 0px;" colspan="7" align="right" id="image-preview-invoice">
                            <div style="white-space: nowrap;" class="get_item_images"></div>
                        </td>
                        <!-- The Modal -->
                        <div id="imageModal" class="modal">
                          <span class="close_invoice" onclick="closeModal()">&times;</span>
                          <img class="modal-content" id="modalImage">
                          <div id="caption"></div>
                        </div>
                    </tr>
                    <!-- end -->
                <?php if (isset($estimate) || isset($add_items)) {
                         $i               = 1;
                         $items_indicator = 'newitems';
                         if (isset($estimate)) {
                             $add_items       = $estimate->items;
                             $items_indicator = 'items';
                         }

                         foreach ($add_items as $item) {
                             $manual    = false;
                             $table_row = '<tr class="sortable item">';
                             $table_row .= '<td class="dragger">';
                             if ($item['qty'] == '' || $item['qty'] == 0) {
                                 $item['qty'] = 1;
                             }
                             if (!isset($is_proposal)) {
                                 $estimate_item_taxes = get_estimate_item_taxes($item['id']);
                             } else {
                                 $estimate_item_taxes = get_proposal_item_taxes($item['id']);
                             }
                             if ($item['id'] == 0) {
                                 $estimate_item_taxes = $item['taxname'];
                                 $manual              = true;
                             }
                             $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
                             $amount = $item['rate'] * $item['qty'];
                             $amount = app_format_number($amount);
                             // order input
                             $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
                             $table_row .= '</td>';
                             $table_row .= '<td class="bold description"><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea></td>';
                             $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';
                             $table_row .= render_custom_fields_items_table_in($item, $items_indicator . '[' . $i . ']');
                             $table_row .= '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
                             $unit_placeholder = '';
                             if (!$item['unit']) {
                                 $unit_placeholder = _l('unit');
                                 $item['unit']     = '';
                             }
                             $table_row .= '<input type="text" placeholder="' . $unit_placeholder . '" name="' . $items_indicator . '[' . $i . '][unit]" class="form-control input-transparent text-right" value="' . $item['unit'] . '">';
                             $table_row .= '</td>';
                             $table_row .= '<td class="rate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
                             $table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $estimate_item_taxes, (isset($is_proposal) ? 'proposal' : 'estimate'), $item['id'], true, $manual) . '</td>';
                             $table_row .= '<td class="amount" align="right">' . $amount . '</td>';
                             $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . ','. $i .'); return false;"><i class="fa fa-times"></i></a></td>';
                             $table_row .= '</tr>';

                             // added by ak
                               
                                $imgHtml = '';

                                foreach(item_images_load($item['item_id']) as $img){
                                    $imgHtml .= '<img onclick="previewImage(this)" class="preview-image" src="'.base_url().'assets/invoice_items/thumb/'.$img->image.'" >';
                                }

                                $table_row .= '<tr class="remove_images'.$i.'"><td colspan="4">&nbsp;</td><td style="padding-left: 0px;" colspan="6" align="right"><div style="white-space: nowrap;">';
                                $table_row .= $imgHtml; 
                                $table_row .= '</div></td></tr>';

                                // Construct the HTML for the modal outside of the table
                                $table_row .= '<div id="imageModal" class="modal">
                                                  <span class="close_invoice" onclick="closeModal()">&times;</span>
                                                  <img class="modal-content" id="modalImage">
                                                  <div id="caption"></div>
                                                </div>';

                                // end
                             echo $table_row;
                             $i++;
                         }
                     }
               ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-8 col-md-offset-4">
        <table class="table text-right">
            <tbody>
                <tr id="subtotal">
                    <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_subtotal'); ?> :</span>
                    </td>
                    <td class="subtotal">
                    </td>
                </tr>
                <tr id="discount_area">
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span class="bold tw-text-neutral-700"><?php echo _l('estimate_discount'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group" id="discount-total">

                                    <input type="number"
                                        value="<?php echo(isset($estimate) ? $estimate->discount_percent : 0); ?>"
                                        class="form-control pull-left input-discount-percent<?php if (isset($estimate) && !is_sale_discount($estimate, 'percent') && is_sale_discount_applied($estimate)) {
                   echo ' hide';
               } ?>" min="0" max="100" name="discount_percent">

                                    <input type="number" data-toggle="tooltip"
                                        data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>"
                                        value="<?php echo(isset($estimate) ? $estimate->discount_total : 0); ?>" class="form-control pull-left input-discount-fixed<?php if (!isset($estimate) || (isset($estimate) && !is_sale_discount($estimate, 'fixed'))) {
                   echo ' hide';
               } ?>" min="0" name="discount_total">

                                    <div class="input-group-addon">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <span class="discount-total-type-selected">
                                                    <?php if (!isset($estimate) || isset($estimate) && (is_sale_discount($estimate, 'percent') || !is_sale_discount_applied($estimate))) {
                   echo '%';
               } else {
                   echo _l('discount_fixed_amount');
               }
                                    ?>
                                                </span>
                                                <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" id="discount-total-type-dropdown"
                                                aria-labelledby="dropdown_menu_tax_total_type">
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-percent<?php if (!isset($estimate) || (isset($estimate) && is_sale_discount($estimate, 'percent')) || (isset($estimate) && !is_sale_discount_applied($estimate))) {
                                        echo ' selected';
                                    } ?>">%</a>
                                                </li>
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-fixed<?php if (isset($estimate) && is_sale_discount($estimate, 'fixed')) {
                                        echo ' selected';
                                    } ?>">
                                                        <?php echo _l('discount_fixed_amount'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="discount-total"></td>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span class="bold tw-text-neutral-700"><?php echo _l('estimate_adjustment'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <input type="number" data-toggle="tooltip"
                                    data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if (isset($estimate)) {
                                        echo $estimate->adjustment;
                                    } else {
                                        echo 0;
                                    } ?>" class="form-control pull-left" name="adjustment">
                            </div>
                        </div>
                    </td>
                    <td class="adjustment"></td>
                </tr>
                <tr>
                    <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_total'); ?> :</span>
                    </td>
                    <td class="total">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="removed-items"></div>
</div>

<script type="text/javascript">
        
    //start by ak
    function previewImage(image) {
        // console.log(image)
        var srcWithoutThumb = image.src.replace('/thumb/', '/');
        var modalImage = document.getElementById("modalImage");
        var captionText = document.getElementById("caption");
        var modal = document.getElementById("imageModal");
        modal.style.display = "block";
        modalImage.src = srcWithoutThumb;
        captionText.innerHTML = image.alt;
    }


    function closeModal() {
        var modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }

    //added by ak 
    function manage_invoice_item(items, callback){
        //   
        var random_number = $('input[name="get_rand_number"]').val();
        items.get_rand_number = random_number;
        
        var url = '<?=base_url();?>admin/invoice_items/add_invoice_items';
        $.post(url, items).done(function (response) {
            response = JSON.parse(response);
             if (response.success == true) {
               $('.set_rand_number_invoice').val(0);
               if(response.item_id != '' && response.item_id != 0){
                    callback(response.item_id);
               }
             }
        });
    }
 // for image upload 
   function item_img_invoice() {
        var fd = new FormData(); // Create a new FormData object
        var random_number = $('input[name="get_rand_number"]').val(); 
        var files = $('input[name="item_images_invoice[]"]')[0].files; 

        // Append files and random number to FormData
        for (var i = 0; i < files.length; i++) {
            fd.append('files[]', files[i]);
        }
        fd.append('random', random_number);

        // Append CSRF token to FormData
        fd.append('<?php echo $this->security->get_csrf_token_name() ?>', '<?php echo $this->security->get_csrf_hash() ?>');

        console.log(fd); // Log FormData object to the console

        // Send AJAX request
        $.ajax({
            url: "<?=base_url();?>admin/invoice_items/upload_item_image",
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){

                var responseData = JSON.parse(response);
                // console.log(responseData);
                if(responseData.random_number != 0){
                    // Update UI elements on successful upload
                    $('.set_rand_number_invoice').val(responseData.random_number);
                    $('.show_tr').css('display','table-row');
                    // fetch images loop
                    var images = responseData.images_random;
                    $('.get_item_images').empty();

                    images.forEach(function(image, index) {
                        var imgContainer = $('<div>').addClass('item-container');
                        var imgElement = $('<img onclick="previewImage(this)">').addClass('preview-image item-image').attr('src', '<?=base_url();?>assets/invoice_items/thumb/' + image);
                        var crossIcon = $('<span>').addClass('cross-icon').html('&times;').on('click', function() {
                            uploadedImgDelete(responseData.img_id[index]);
                        });

                        // Append the image and cross icon to the container
                        imgContainer.append(imgElement, crossIcon);
                        $('.get_item_images').append(imgContainer);
                    });

                    // end loop
                    $('.set_item_images').html('<input type="hidden" name="new_item_images" value="' + responseData.images_random.join(',') + '">');
                } else {
                    // Handle file upload failure
                    alert('File not uploaded');
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error(xhr.responseText);
                alert('Error: ' + error);
            }
        });
    }
        // added by ak
        // for image delete
        function uploadedImgDelete(imgid) {
            
            var items = {}; 
            var random_number = $('input[name="get_rand_number"]').val();

            items.imgid = imgid;
            items.random_number = random_number;
            var baseUrl = $('#base-url').data('base-url');
            var url = baseUrl+'admin/invoice_items/delete_item_images';
            $.post(url, items).done(function(response) {
                
                response = JSON.parse(response);
                if (response.success === true) {
                   
                    // fetch images loop
                    images = response.images_random;
                    img_id = response.img_id;
                    $('.get_item_images').empty();
                    images.forEach(function(image, index) {
                        var imgContainer = $('<div>').addClass('item-container');
                        var imgElement = $('<img onclick="previewImage(this)">').addClass('preview-image item-image').attr('src', '<?=base_url();?>assets/invoice_items/thumb/' + image);
                        var crossIcon = $('<span>').addClass('cross-icon').html('&times;').on('click', function() {
                            uploadedImgDelete(response.img_id[index]);
                        });

                        // Append the image and cross icon to the container
                        imgContainer.append(imgElement, crossIcon);
                        $('.get_item_images').append(imgContainer);
                    });
                    // end loop
                    $('.set_item_images').html('<input type="hidden" name="new_item_images" value="' + response.images_random.join(',') + '">');
                    alert_float("success",response.message);
                }
            });
        }
</script>