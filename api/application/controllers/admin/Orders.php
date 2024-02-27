<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('orders_model');
    }

    /* List all announcements */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('orders');
        }
        $data['title'] = _l('orders');
        $this->load->view('admin/orders/manage', $data);
    }

    /* Edit announcement or add new if passed id */
    public function order($id = ''){
        if(!empty($_POST['image'])){
            if ($this->input->post()) {
                $data            = $_POST['image'];
                $path = "uploads/orders";
                $image_parts = explode(";base64,", $data);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = uniqid() . '.jpg';
            
                $newFilePath = $path . '/' . $fileName;
                file_put_contents($newFilePath, $image_base64);
                
                if ($id == '') {
                    $this->db->insert(db_prefix() . 'orders_image', [
                        'file_name'         => $fileName,
                        'filetype'         => $image_type,
                        'userid'      => get_staff_user_id(),
                    ]);
                    $insert_id = $this->db->insert_id();
                    
                    if ($insert_id) {
                        foreach($_POST['imagemulti'] as $key=>$image){
                            $path = "uploads/orders";
                            $image_parts = explode(";base64,", $image);
                            $image_type_aux1 = explode("image/", $image_parts[0]);
                            $image_type1 = $image_type_aux1[1];
                            $image_base64 = base64_decode($image_parts[1]);
                            $fileName1 = uniqid() . '.jpg';
                        
                            $newFilePath = $path . '/' . $fileName1;
                            file_put_contents($newFilePath, $image_base64);
    
                            $this->db->insert(db_prefix() . 'orders_additional_image', [
                                'file_name'         => $fileName1,
                                'filetype'         => $image_type1,
                                'userid'      => get_staff_user_id(),
                                'order_id'      => $insert_id,
                            ]);
                        }
                        set_alert('success', _l('added_successfully', _l('order')));
                        redirect(admin_url('orders'));
                    }
                } else {
                    $success = $this->orders_model->update($data, $id);
                    if ($success) {
                        set_alert('success', _l('updated_successfully', _l('order')));
                    }
                    redirect(admin_url('orders'));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('order'));
        } else {
            $data['order'] = $this->orders_model->get($id);
            $title                = _l('edit', _l('order'));
        }
        $data['title'] = $title;
        $this->load->view('admin/orders/order', $data);
    }

    public function view($id)
    {
            $announcement = $this->orders_model->get($id);
            if (!$announcement) {
                blank_page(_l('order_additional_image_not_found'));
            }
            $data['orders_additional_image']         = $announcement;
            $data['title'] = 'Order Additional Images';
            $this->load->view('admin/orders/view', $data);
    }

    /* Delete announcement from database */
    public function delete($id)
    {
        $response = $this->orders_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('orders')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('orders')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
}
