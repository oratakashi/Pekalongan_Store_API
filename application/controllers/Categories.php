<?php
    
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Categories extends REST_Controller {

    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CategoryModel', 'category');
    }

    public function index_get()
    {
        if(empty($this->uri->segment(2))){
            if(empty($this->input->get('limit'))){

                $data_category = $this->category->read();

                $data = array();

                foreach($data_category as $index => $value){
                    $data[$index]['id'] = $value['id'];
                    $data[$index]['name'] = $value['name'];
                    $data[$index]['images'] = base_urL("media/categories/").$value['images'];
                }

                if(!empty($data_category)){
                    $message = array(
                        "status"    => TRUE,
                        "message"   => "Berhasil mendapatkan data!",
                        "data"      => $data
                    );

                    $this->response($message, REST_Controller::HTTP_OK);
                }else{
                    $message = array(
                        "status"    => FALSE,
                        "message"   => "Tidak ada kategori yang tersedia!",
                        "data"      => null
                    );

                    $this->response($message, REST_Controller::HTTP_OK);
                }
            
            }
        }
    }

    public function index_post()
    {
        $id = $this->uuid->v4();

        $photo = "";
        if($_FILES['photo']['error'] === UPLOAD_ERR_OK){
            $config['upload_path']          = './media/categories/';
            $config['allowed_types']        = 'jpg|png|jpeg';
            $config['overwrite']            = true;
            $config['file_name']            = $id;
            
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ( ! $this->upload->do_upload('photo')){
                $error = array('error' => $this->upload->display_errors());
                print_r($error);
            }
            else{
                $upload = array('upload_data' => $this->upload->data());
                $photo = $upload['upload_data']['file_name'];
            }

            $data = array(
                "id"    => $id,
                "name"  => $this->post("name"),
                "images" => $photo
            );

            $query = $this->category->insert($data);

            if($query > 0) {

                $data = $this->category->read_id($id);

                $message = array(
                    "status"    => $query > 0,
                    "message"   => "Berhasil melakukan registrasi!",
                    "data"      => $data
                );
            }

            $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        }else{
            $message = array(
                "status"    => FALSE,
                "message"   => "Anda wajib menyertakan image category!",
                "data"      => null
            );

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

}

/* End of file Categories.php */
    
?>