<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700"><?php echo "All Images"; ?></h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700" style="text-align: center;"><?php echo "Photo Image"; ?></h4><hr>
                            <div class="col-md-4">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">Order Id: <?php echo $orders_additional_image[0]['order_id']; ?></h4>
                            </div>
                            <div class="col-md-4">
                                <img width="400" src="<?php echo base_url('uploads/orders/' . $orders_additional_image['0']['file_name'])?>" class="img-responsive">
                            </div>
                        </div></br></br>
                       <div class="row">
                           <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700" style="text-align: center;"><?php echo "Additional Images"; ?></h4><hr>
                    <?php foreach($orders_additional_image as $image){?>
                        <div class="col-md-4">
                            <img width="400" src="<?php echo base_url('uploads/orders/' . $image['file_name'])?>" class="img-responsive">
                        </div>
                    <?php }?>
                    </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <?php init_tail(); ?>
    </body>

    </html>
