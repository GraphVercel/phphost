<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15 preview-top-wrapper">
    <div class="panel_s tw-mt-6">
        <div class="panel-body">
        <?php if(empty($lead->acceptance_firstname)){?>
          <?php echo form_open(site_url('term_conditions/add_signature/' . $lead->id.'/'.$lead->hash)); ?>
            <div class="col-md-8 col-md-offset-2">
                <h2>Terms & Services Agreement</h2>
                <?php echo $lead->term_description; ?>
                </br></br>
                <div class="col-sm-6">
                    <input type="text" name="acceptance_firstname" id="acceptance_firstname" placeholder="First name" class="form-control" required="true" value="<?php echo (isset($lead) ? $lead->acceptance_firstname : '') ?>">
                </div>
                <div class="col-sm-6">
                    <input type="text" name="acceptance_lastname" id="acceptance_lastname" placeholder="Last name" class="form-control" required="true" value="<?php echo (isset($lead) ? $lead->acceptance_lastname : '') ?>">
                </div>
                </br></br></br>
                <p class="bold" id="signatureLabel"><?php echo _l('signature'); ?></p>
                <div class="signature-pad--body">
                    <canvas id="signature" height="130" width="550"></canvas>
                </div>
                <input type="text" style="width:1px; height:1px; border:0px;" tabindex="-1" name="signature" id="signatureInput">
                <div class="dispay-block">
                    <button type="button" class="btn btn-default btn-xs clear" tabindex="-1" data-action="clear"><?php echo _l('clear'); ?></button>
                    <!-- <button type="button" class="btn btn-default btn-xs" tabindex="-1" data-action="undo"><?php echo _l('undo'); ?></button> -->
                </div></br>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="term_check" name="term_check">
                    <label class="form-check-label" for="term_check">I agree to the terms above as well as using an electronic signature</label>
                </div>
                <div class="col-sm-12" style="text-align:end">
                    <button type="submit" class="btn btn-success"><?php echo _l('I Agree'); ?></button>
                </div>
           </div>
           <?php echo form_close(); ?>
        <?php }elseif(!empty($lead->card_number)){?>
        <div class="col-md-6 col-md-offset-3" style="text-align: center;">
          <h1> Thank You!</h1>
        </div>
        <?php }elseif($lead->check_status==0){?>
        <div class="col-md-6 col-md-offset-3" style="text-align: center;">
          <h1> Thank You!</h1>
        </div>
        <?php }else{?>
          <?php echo form_open(site_url('term_conditions/add_card_information/' . $lead->id.'/'.$lead->hash)); ?>
          <div class="col-md-6 col-md-offset-3">
              <div class="col-sm-12">
              <h2>Card on File</h2>
              <?php echo $lead->term_description; ?></br>
              </div>
              <div class="col-sm-12" style="padding-bottom: 10px;">
                  <input type="text" name="card_number" id="card_number" placeholder="Card Number" class="form-control" required="true" value="<?php echo (isset($lead) ? $lead->card_number : '') ?>">
              </div>
              <div class="col-sm-6">
                  <input type="text" name="card_name" id="card_name" placeholder="Name on Card" class="form-control" required="true" value="<?php echo (isset($lead) ? $lead->card_name : '') ?>">
              </div></br></br>
              <div class="col-sm-3">
                  <input type="date" name="card_date" id="card_date" placeholder="mm/yy" class="form-control" required="true" value="<?php echo (isset($lead) ? $lead->card_date : '') ?>">
              </div>
              <div class="col-sm-3">
                  <input type="text" name="card_vv" id="card_vv" placeholder="CVV" class="form-control" required="true" value="<?php echo (isset($lead) ? $lead->card_vv : '') ?>">
              </div>
              <div class="col-sm-12" style="text-align:center;padding-top: 20px;">
                    <button type="submit" class="btn btn-success"><?php echo _l('Submit'); ?></button>
              </div>
          </div>
          <?php echo form_close(); ?>
          <?php }?>
        </div>
    </div>
</div>

<?php
  $this->app_scripts->theme('signature-pad','assets/plugins/signature-pad/signature_pad.min.js');
?>
<script>
  $(function(){
   SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
     var canvas = this._ctx.canvas;
       // First duplicate the canvas to not alter the original
       var croppedCanvas = document.createElement('canvas'),
       croppedCtx = croppedCanvas.getContext('2d');

       croppedCanvas.width = canvas.width;
       croppedCanvas.height = canvas.height;
       croppedCtx.drawImage(canvas, 0, 0);

       // Next do the actual cropping
       var w = croppedCanvas.width,
       h = croppedCanvas.height,
       pix = {
         x: [],
         y: []
       },
       imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
       x, y, index;

       for (y = 0; y < h; y++) {
         for (x = 0; x < w; x++) {
           index = (y * w + x) * 4;
           if (imageData.data[index + 3] > 0) {
             pix.x.push(x);
             pix.y.push(y);

           }
         }
       }
       pix.x.sort(function(a, b) {
         return a - b
       });
       pix.y.sort(function(a, b) {
         return a - b
       });
       var n = pix.x.length - 1;

       w = pix.x[n] - pix.x[0];
       h = pix.y[n] - pix.y[0];
       var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

       croppedCanvas.width = w;
       croppedCanvas.height = h;
       croppedCtx.putImageData(cut, 0, 0);

       return croppedCanvas.toDataURL();
     };


     function signaturePadChanged() {

       var input = document.getElementById('signatureInput');
       var $signatureLabel = $('#signatureLabel');
       $signatureLabel.removeClass('text-danger');

       if (signaturePad.isEmpty()) {
         $signatureLabel.addClass('text-danger');
         input.value = '';
         return false;
       }

       $('#signatureInput-error').remove();
       var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
       partBase64 = partBase64.split(',')[1];
       input.value = partBase64;
     }

     var canvas = document.getElementById("signature");
     var clearButton = wrapper.querySelector("[data-action=clear]");
     var undoButton = wrapper.querySelector("[data-action=undo]");
     var identityFormSubmit = document.getElementById('identityConfirmationForm');

     var signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd:function(){
        signaturePadChanged();
      }
    });

     clearButton.addEventListener("click", function(event) {
       signaturePad.clear();
       signaturePadChanged();
     });

     undoButton.addEventListener("click", function(event) {
       var data = signaturePad.toData();
       if (data) {
           data.pop(); // remove the last dot or line
           signaturePad.fromData(data);
           signaturePadChanged();
         }
       });

     $('#identityConfirmationForm').submit(function() {
       signaturePadChanged();
     });
   });
 </script>