<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Users extends REST_Controller {

    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        
    }

    public function index_get()
    {
        $users = [
            ['id' => 1, 'name' => 'John', 'email' => 'john@example.com', 'fact' => 'Loves coding'],
            ['id' => 2, 'name' => 'Jim', 'email' => 'jim@example.com', 'fact' => 'Developed on CodeIgniter'],
            ['id' => 3, 'name' => 'Jane', 'email' => 'jane@example.com', 'fact' => 'Lives in the USA', ['hobbies' => ['guitar', 'cycling']]],
        ];
        $this->response($users, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
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
                    $this->response(NULL, REST_Controller::HTTP_OK); // BAD_REQUEST (400) being the HTTP response code
                }
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "Email sudah terdaftar!",
                    "data"      => array()
                );
                $this->response(NULL, REST_Controller::HTTP_OK); // BAD_REQUEST (400) being the HTTP response code
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
        }
    }

}

/* End of file Users.php */
