<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo base_url('assets/builds/vendor-admin.css'); ?>">
    <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
</head>
<style>
.upload-blog input {
    opacity: 0;
    position: absolute;
    width: 100%;
    left: 0;
    top: 0;
    height: 100%;
}
img {
    width: 100%;
}
h2 {
    font-weight: 600;
}
.upload-blog {
    background: #e7e7e7;
    position: relative;
    max-width: 240px;
    height: 150px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 2rem;
    color: #adadad;
    border-radius: 30px;
    border-style: dashed;
    border-width: 1px;
    border-color: #000;
    margin: 10px auto;
}
.loader-main {
    position: fixed;
    background: #fffffff0;
    width: 100%;
    height: 100%;
    top: 0;
    display: none;
}
.loader {
    border: 10px solid #f3f3f3;
    border-top: 10px solid #3498db;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    animation: spin 2s linear infinite;
    position: fixed;
    left: 41%;
    transform: translate(-50%, 0%);
    top: 40px;
}
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
.flash-message {
    display: none;
    position: absolute;
    top: 0;
    width: 95%;
    transform: translate(-50%, 7px);
    left: 50%;
    z-index: 99;
}
</style>

<body>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
      <h2 class="fw-bold"><?php echo $invoice->prefix.$invoice->number; ?></h2>
      <h2 class="fw-bold"><?php echo $invoice->client->company; ?></h2><br>
        <form action="">
          <div class="upload-blog">
            <input multiple type="file" id="myFile" name="filename[]" class="form-control" onchange="fileSelected(event)">
            Upload
          </div>
          <input type="hidden" id="id" name="id" value="<?php echo $id;?>">
        </form>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row" id="image-preview-invoice"></div>
  </div>

  <div class="loader-main">
    <div class="loader"></div>
  </div>
  <div id="flash-message" class="flash-message alert">
  </div>

<script>
function fileSelected(event) {
    var fd = new FormData();
    let file = event.target.files;
    for (var i = 0; i < file.length; i++) {
        fd.append('files[]', file[i]);
    }
    var invoice_id = $('#id').val();
    fd.append('invoice_id', invoice_id);
    if (file) {
        // Show loader
        $(".loader-main").show();
        var flash_message = $("#flash-message");
        flash_message.removeClass("alert-success");
        flash_message.removeClass("alert-danger");
        // Send AJAX request
        $.ajax({
            url: "<?=base_url();?>fileupload/upload_item_image",
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                var responseData = JSON.parse(response);
                if (responseData.random_number !== undefined && responseData.random_number !== 0) {
                    $(".loader-main").hide();
                    flash_message.html(responseData.success);
                    flash_message.addClass("alert-success");
                    flash_message.fadeIn().delay(3000).fadeOut();
                    var previewContainer = document.getElementById('image-preview-invoice');
                    for (var i = 0; i < file.length; i++) {
                        (function (file_each) {
                            var imagesContainer = document.createElement('div');
                            imagesContainer.className = 'col-sm-2 col-xs-6 img-blog';
                            imagesContainer.style.whiteSpace = 'nowrap';
                            var reader = new FileReader();
                            var allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                            if (allowedTypes.includes(file_each.type)) {
                                reader.onload = function (event) {
                                    var imageUrl = event.target.result;
                                    var image = new Image();
                                    image.src = imageUrl;
                                    image.alt = 'Image Preview';
                                    image.className = 'preview-image';
                                    image.style.marginRight = '10px';
                                    imagesContainer.appendChild(image);
                                };
                                reader.readAsDataURL(file_each);
                            }
                            previewContainer.appendChild(imagesContainer);
                        })(file[i]); // Pass file_each to the closure
                    }
                    $('#myFile').val('');
                } else {
                    $(".loader-main").hide();
                    flash_message.html(responseData.error);
                    flash_message.addClass("alert-danger");
                    flash_message.fadeIn().delay(3000).fadeOut();
                }
            },
            error: function (xhr, status, error) {

                $(".loader-main").hide();
                flash_message.html(error);
                flash_message.addClass("alert-danger");
                flash_message.fadeIn().delay(3000).fadeOut();
            }
        });
    }
}
</script>
</body>
</html>