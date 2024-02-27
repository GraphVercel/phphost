<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo $title; ?>
                </h4>
                <?php  echo form_open_multipart('admin/orders/order', ['id' => 'staff_profile_table', 'autocomplete' => 'off']); ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="form-group" style="width: 100%;">
                            <div class="col-md-12" style="text-align: center;">
                                <label for="order_image" class="control-label"><h4>Photos Upload</h4></label></br></br>
                                <span id="photouploadbutton" style="padding: 11px 140px;border:1px solid;cursor: pointer" onclick="photouploadclick()"><i class="fa-solid fa-camera fa-2xl"></i></span></br></br></br>
                            </div>
                            <div class="row" id="photoupload">
                                <div class="col-md-6">
                                    <div id="my_camera"></div>
                                    <br/>
                                    <input type=button value="Take Snapshot" onClick="take_snapshot()"></br></br>
                                    <input type="hidden" name="image" class="image-tag">
                                </div>
                                <div class="col-md-6">
                                    <div id="results"></div>
                                </div>
                            </div>
                        </div>
                        </br>
                        <div class="form-group">
                            <div class="col-md-12" style="text-align: center;">
                                <label for="order_additional_image" class="control-label"><h4>Additional Photos Upload</h4></label></br></br>
                                <span id="additionalphotouploadbutton" style="padding: 11px 140px;border:1px solid;cursor: pointer" onclick="photouploadadditionalclick()"><i class="fa-solid fa-camera fa-2xl"></i></span></br></br>
                            </div>
                            <div class="row" id="additionalphotoupload">
                                <div class="col-md-6">
                                    <div id="my_camera_multi"></div>
                                    <br/>
                                    <input type=button value="Take Snapshot" onClick="take_snapshot_multi()"></br></br>
                                    <!-- <input type="hidden" name="image[]" class="image-tag"> -->
                                </div>
                                <div class="col-md-6">
                                    <div id="resultsmulti"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script language="JavaScript">
    Webcam.set({
        width: 300,
        height: 250,
        image_format: 'jpeg',
        jpeg_quality: 90,
        constraints: { facingMode: 'environment' }
    });
  
    Webcam.attach( '#my_camera' );
  
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );
    }
</script>
<script language="JavaScript">
    Webcam.set({
        width: 300,
        height: 250,
        image_format: 'jpeg',
        jpeg_quality: 90,
        constraints: { facingMode: 'environment' }
    });
  
    Webcam.attach( '#my_camera_multi' );
  
    function take_snapshot_multi() {
        Webcam.snap( function(data_uri1) {
            var parent = document.getElementById('resultsmulti');
            var newChild = '<img src="'+data_uri1+'"/><input type="hidden" name="imagemulti[]" value="'+data_uri1+'" class="image-tag1"></br></br>';
            parent.insertAdjacentHTML('beforeend', newChild);
            childNumber++;
        } );
    }

    document.getElementById('photoupload').style.display = 'none';
    function photouploadclick(){
        document.getElementById('photoupload').style.display = 'inline';
        document.getElementById('photouploadbutton').style.display = 'none';
    }

    document.getElementById('additionalphotoupload').style.display = 'none';
    function photouploadadditionalclick(){
        document.getElementById('additionalphotoupload').style.display = 'inline';
        document.getElementById('additionalphotouploadbutton').style.display = 'none';
    }
</script>
</body>

</html>