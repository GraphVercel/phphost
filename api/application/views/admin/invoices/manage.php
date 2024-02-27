<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style type="text/css">
    .image-container {
    display: flex; /* Use flexbox to align images horizontally */
    flex-wrap: nowrap; /* Ensure images do not wrap to the next line */
}

/* Modal Content */
.modal-content {
  display: block;
  margin: auto;
  width: 80%;
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
.close {
  color: white;
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 35px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}


</style>
<div id="wrapper">
	<div class="content">
		<div id="vueApp">
			<div class="row">
				<?php include_once(APPPATH.'views/admin/invoices/filter_params.php'); ?>
				<?php $this->load->view('admin/invoices/list_template'); ?>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
               <!-- The Modal -->
                <div id="imageModal" class="modal">
                  <span onclick="closeModalInvoice()" class="close">&times;</span>
                  <img class="modal-content" id="modalImage">
                  <div id="caption"></div>
                </div>
<div id="modal-wrapper"></div>
<script>var hidden_columns = [2,6,7,8];</script>
<?php init_tail(); ?>
<script>
$(function(){
	init_invoice();
});


//start by ak
var modal = document.getElementById("imageModal");

function previewImage(image) {
    
    var srcWithoutThumb = image.src.replace('/thumb/', '/');
    var modalImage = document.getElementById("modalImage");
    var captionText = document.getElementById("caption");
    modal.style.display = "block";
    modalImage.src = srcWithoutThumb;
    captionText.innerHTML = image.alt;
}
function closeModalInvoice() {
  modal.style.display = "none";
}
window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};
</script>
</body>
</html>