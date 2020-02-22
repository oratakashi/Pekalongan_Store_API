<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Users extends REST_Controller {

    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('RegionModel', 'region');
    }

    public function index_get()
    {
        /**
         * User Profile GET ("users/{{ user_id }})
         * Flow : 
         * Mendapatkan data user by ID
         * Jika subdistrict_id tidak null mendapatkan data subdistrict
         */
        if(!empty($this->uri->segment(2))){
            $id = $this->uri->segment(2);

            $data_user = $this->user->read_id($id);

            $data = array(
                "id" => $data_user['id'],
                "name" => $data_user['name'],
                "email" => $data_user['email'],
                "phone" => $data_user['phone'],
                "photo" => base_url('media/users/').$data_user['photo'],
                "address" => $data_user['address'],
                "updated_at" => $data_user['updated_at'],
                "subdistrict" => null
            );

            if(!empty($data_user['subdistrict_id'])){
                $data_region = $this->region->read_id_subdistrict($data_user['subdistrict_id']);

                $data['subdistrict'] = $data_region;
            }

            if(!empty($data_user)){
                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil mendapatkan data!",
                    "data"      => $data
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "User ID yang anda masukan salah!",
                    "data"      => null
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }            
        }
    }
    
    public function index_post()
    {
        if($this->uri->segment(2)==="register"){

            $id = $this->uuid->v4();

            $data = array(
                "id"        => $id,
                "name"      => $this->post("name"),
                "email"      => $this->post("email"),
                "password"      => sha1($this->post("password"))
            );

            if($this->user->cek_email($this->post("email")) < 1){
                $query = $this->user->insert($data);

                if($query > 0){
                    $data = $this->user->read_id($id);
                    $message = array(
                        "status"    => $query > 0,
                        "message"   => "Berhasil melakukan registrasi!",
                        "data"      => $data
                    );
                    $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
                }else{
                    $message = array(
                        "status"    => $query > 0,
                        "message"   => "Gagal melakukan registrasi!",
                        "data"      => array()
                    );
                    $this->response($message, REST_Controller::HTTP_OK); // BAD_REQUEST (400) being the HTTP response code
                }
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "Email sudah terdaftar!",
                    "data"      => array()
                );
                $this->response($message, REST_Controller::HTTP_OK); // BAD_REQUEST (400) being the HTTP response code
            }
            
        }elseif ($this->uri->segment(2)==="login") {
            $input = array(
                "email"     => $this->post("email"),
                "password"  => sha1($this->post("password"))
            );

            $data = $this->user->login($input);

            if($data){
                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil melakukan login!",
                    "data"      => $data
                );
                $this->set_response($message, REST_Controller::HTTP_OK); // HTTP OK (200) being the HTTP response code
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "Username atau password salah!",
                    "data"      => array()
                );
                $this->set_response($message, REST_Controller::HTTP_OK); // HTTP NOT FOUND (404) being the HTTP response code
            }
        }elseif($this->uri->segment(2)==="images"){
            $id = $this->input->get('id');            

            $photo = "";
            if($_FILES['photo']['error'] === UPLOAD_ERR_OK){
                $config['upload_path']          = './media/users/';
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

                $this->user->update($id, ["photo" => $photo]);

                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil mengupload foto profile!",
                    "data"      => [
                        "filename"  => $photo,
                        "url"       => base_url("media/users/").$photo
                    ]
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "Anda tidak mengupload apapun!",
                    "data"      => null
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }
        }
    }

    public function index_delete()
    {
        if($this->uri->segment(2)==="images"){
            $id = $this->uri->segment(3);

            $query = $this->user->delete_photo($id);

            if($query > 0){
                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil menghapus foto!"
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "Gagal menghapus foto!, Mungkin foto sudah terhapus"
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }
        }
    }
}

/* End of file Users.php */
