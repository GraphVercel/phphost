<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_items extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
    }

    /* List all available items */
    public function index()
    {
        if (staff_cant('view', 'items')) {
            access_denied('Invoice Items');
        }

        $this->load->model('taxes_model');
        $data['taxes']        = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('invoice_items');
        $this->load->view('admin/invoice_items/manage', $data);
    }

    public function table()
    {
        if (staff_cant('view', 'items')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('invoice_items');
    }

    /* Edit or update items / ajax request /*/
    public function manage()
    {
        if (staff_can('view',  'items')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $data['added_by'] = "item";
                if ($data['itemid'] == '') {
                    if (staff_cant('create', 'items')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    
                    $data['added_by'] = "item";
                    
                    $id      = $this->invoice_items_model->add($data);
                    $data['item_id'] = $id;
                    $this->invoice_items_model->item_image_update($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('sales_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item'    => $this->invoice_items_model->get($id),
                    ]);
                } else {
                    if (staff_cant('edit', 'items')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->invoice_items_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('sales_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
    }

    public function add_invoice_items(){
        if ($this->input->post()) {
                $data = $this->input->post();
                $data['added_by'] = "invoice";
                $data['group_id'] = 0;
                if(isset($data['qty']) && $data['qty'] != ''){
                    unset($data['qty']);
                }
                
                if (isset($data['taxname'])) {
                    unset($data['taxname']);
                }
                $id = $this->invoice_items_model->add($data);
                $data['item_id'] = $id;
                  // echo "<pre>";
                  //   print_r($data);die;
                $success = $this->invoice_items_model->item_image_update($data);
                $message = '';
                    if ($success) {
                        $message = 'Item Added successfully';
                    }
                    echo json_encode([
                        'item_id' => $id,
                        'success' => $success,
                        'message' => $message,
                    ]);
        }
        
    }


    //  public function upload_item_image() {

    //      if (!file_exists(rtrim('assets', '/') . '/invoice_items/')) {
    //         mkdir(rtrim('assets', '/') . '/invoice_items/', 0777, true);
    //     }
    //     $upload_path = "assets/invoice_items/";

    //     $file_paths = array();
    //     $random_number = rand(10000, 99999);
    //     foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
    //         $original_filename = $_FILES['files']['name'][$key];
    //         $filename = str_replace(' ', '_', $original_filename);
    //         $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
    //         $allowed_extensions = array('png', 'jpg', 'jpeg');
    //         // Check if the file extension is allowed
    //         if (!in_array(strtolower($file_extension), $allowed_extensions)) {
    //             echo 'Error: Only PNG, JPG, and JPEG files are allowed.';
    //             return;
    //         }
    //         $location = $upload_path . $filename;
    //         //
           
    //         if (move_uploaded_file($tmp_name, $location)) {

               
    //             if (is_image($location)) {
    //                 create_img_invoice_thumb($upload_path, $filename, '180', '140');
    //             }
    //             $file_paths[] = $location;
    //             $insertData = array(
    //                 'item_id' => null,
    //                 'image' => $filename, // Use the uploaded file name
    //                 'key_code' => $random_number,
    //                 'status' => 1
    //             );
    //             // Insert data into the 'item_images' table
    //             $this->db->insert(db_prefix() . 'item_images', $insertData);
               
    //         } else {
    //             echo 'Error uploading file: ' . $filename;
    //             return;
    //         }
    //     }

    //     // Return JSON response with file paths
    //     echo $random_number;
    // }
    
    public function upload_item_image() {

         if (!file_exists(rtrim('assets', '/') . '/invoice_items/')) {
            mkdir(rtrim('assets', '/') . '/invoice_items/', 0777, true);
        }
        $upload_path = "assets/invoice_items/";

        $file_paths = array();
      

        $random_number = $this->input->post('random');

        if ($random_number != 0) { // Removed unnecessary closing parenthesis
            $random_number = $random_number;
        } else {
            $random_number = rand(10000, 99999);
        }


        
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            $original_filename = $_FILES['files']['name'][$key];
            $filename = str_replace(' ', '_', $original_filename);
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
            $allowed_extensions = array('png', 'jpg', 'jpeg');
            // Check if the file extension is allowed
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                echo 'Error: Only PNG, JPG, and JPEG files are allowed.';
                return;
            }
            $location = $upload_path . $filename;
            //
           
            if (move_uploaded_file($tmp_name, $location)) {

               
                if (is_image($location)) {
                    create_img_invoice_thumb($upload_path, $filename, '180', '140');
                }
                $file_paths[] = $location;
                $insertData = array(
                    'item_id' => null,
                    'image' => $filename, // Use the uploaded file name
                    'key_code' => $random_number,
                    'status' => 1
                );
                if ($this->input->post('item_id')) {
                    $insertData['item_id']=$this->input->post('item_id');
                }
                // Insert data into the 'item_images' table
                $this->db->insert(db_prefix() . 'item_images', $insertData);
                $insert_id = $this->db->insert_id();
               
            } else {
                echo 'Error uploading file: ' . $filename;
                return;
            }
        }
        // get images against random_number
        $images_random = item_images_load_against_random_number($random_number);
        // Return JSON response with file paths
        if($insert_id){
            echo json_encode(['random_number' => $random_number, 'images_random' => $images_random, 'insert_id' => $insert_id]);
        }else{
            echo json_encode(['random_number' => $random_number, 'images_random' => $images_random]);
        }
        
    }
    public function delete_item_image(){
       if($this->input->post(id)){
           $response = $this->invoice_items_model->delete_item_image($this->input->post(id));
           if($response){
               echo json_encode(['success' => "Image Removed Successfully", 'error_code' => 0]);
           }else{
               echo json_encode(['error' => "Try Again", 'error_code' => 1]);
           }
       }else{
           echo json_encode(['error' => "Invalid Id", 'error_code' => 1]);
       }
    }
    public function get_item_images($itemid){

         if ($this->input->is_ajax_request()) {
            
             $item_images = item_images_load($itemid);
             $images = [];
             foreach($item_images as $row){
                $images[] = $row->image;
             }
            echo json_encode($images);
        }
       
    }

    public function import()
    {
        if (staff_cant('create', 'items')) {
            access_denied('Items Import');
        }

        $this->load->library('import/import_items', [], 'import');

        $this->import->setDatabaseFields($this->db->list_fields(db_prefix() . 'items'))
            ->setCustomFields(get_custom_fields('items'));

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if (
            $this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''
        ) {
            $this->import->setSimulation($this->input->post('simulate'))
                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                ->setFilename($_FILES['file_csv']['name'])
                ->perform();

            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }

        $data['title'] = _l('import');
        $this->load->view('admin/invoice_items/import', $data);
    }

    public function add_group()
    {
        if ($this->input->post() && staff_can('create',  'items')) {
            $this->invoice_items_model->add_group($this->input->post());
            set_alert('success', _l('added_successfully', _l('item_group')));
        }
    }

    public function update_group($id)
    {
        if ($this->input->post() && staff_can('edit',  'items')) {
            $this->invoice_items_model->edit_group($this->input->post(), $id);
            set_alert('success', _l('updated_successfully', _l('item_group')));
        }
    }

    public function delete_group($id)
    {
        if (staff_can('delete',  'items')) {
            if ($this->invoice_items_model->delete_group($id)) {
                set_alert('success', _l('deleted', _l('item_group')));
            }
        }
        redirect(admin_url('invoice_items?groups_modal=true'));
    }

    /* Delete item*/
    public function delete($id)
    {
        if (staff_cant('delete', 'items')) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('invoice_items'));
        }

        $response = $this->invoice_items_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('invoice_item')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
        redirect(admin_url('invoice_items'));
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_items');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids                   = $this->input->post('ids');
            $has_permission_delete = staff_can('delete',  'items');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->invoice_items_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }

    public function search()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->invoice_items_model->search($this->input->post('q')));
        }
    }

    /* Get item by id / ajax */
    public function get_item_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $item                     = $this->invoice_items_model->get($id);
            $item->long_description   = nl2br($item->long_description);
            $item->custom_fields_html = render_custom_fields('items', $id, [], ['items_pr' => true]);
            $item->custom_fields      = [];

            $cf = get_custom_fields('items');

            foreach ($cf as $custom_field) {
                $val = get_custom_field_value($id, $custom_field['id'], 'items_pr');
                if ($custom_field['type'] == 'textarea') {
                    $val = clear_textarea_breaks($val);
                }
                $custom_field['value'] = $val;
                $item->custom_fields[] = $custom_field;
            }

            echo json_encode($item);
        }
    }

    /* Copy Item */
    public function copy($id)
    {
        if (staff_cant('create', 'items')) {
            access_denied('Create Item');
        }

        $data = (array) $this->invoice_items_model->get($id);

        $id = $this->invoice_items_model->copy($data);

        if ($id) {
            set_alert('success', _l('item_copy_success'));
            return redirect(admin_url('invoice_items?id=' . $id));
        }

        set_alert('warning', _l('item_copy_fail'));
        return redirect(admin_url('invoice_items'));
    }
}
