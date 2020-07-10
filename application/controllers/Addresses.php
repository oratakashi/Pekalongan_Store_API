<?php
    
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;
class Addresses extends REST_Controller {

    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AddressModel', "address");
        $this->load->model('RegionModel', "region");
    }
    

    public function index_get()
    {
        if(empty($this->uri->segment(2))){
            $message = array(
                "status"    => FALSE,
                "message"   => "User ID Tidak dikenali!",
                "data"      => null
            );

            $this->response($message, REST_Controller::HTTP_OK);
        }else{
            $id = $this->uri->segment(2);
            
            $data_address = $this->address->read_by_user($id);            

            if(count($data_address) < 1){
                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil mendapatkan data!",
                    "data"      => null
                );
                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                $data_result = array();
                foreach ($data_address as $index => $value) {
                    $data_result[$index] = array(
                        "id"            => $value['id'],
                        "name"          => $value['name'],
                        "receiver_name" => $value['receiver_name'],
                        "phone"         => $value['phone'],
                        "village"       => $this->region->read_id_village($value['village_id']),
                        "street"         => $value['street'],
                        "default"         => $value['default']
                    );
                }
                

                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil mendapatkan data!",
                    "data"      => $data_result
                );
                $this->response($message, REST_Controller::HTTP_OK);
            }
        }
    }

    /**
     * Tambah Data Alamat, Jika data blm ada default akan di set menjadi "y" 
     * Jika Data Sudah ada maka default di set menjadi "n"
     */

    public function index_post()
    {
        $id = $this->uuid->v4();

        if(empty($this->post("user_id")) || empty($this->post("village_id"))){
             $message = array(
                "status"    => FALSE,
                "message"   => "User ID Ataupun Village ID Wajib di isi!",
                "data"      => null
            );

            $this->response($message, REST_Controller::HTTP_OK);
        }else{
            $data = array(
                "id"                =>  $id,
                "user_id"           => $this->post("user_id"),
                "name"              => $this->post("name"),
                "receiver_name"     => $this->post("receiver_name"),
                "phone"             => $this->post("phone"),
                "village_id"        => $this->post("village_id"),
                "street"            => $this->post("street"),
            );

            $query = $this->address->insert($data);

            if($query > 0){

                $data_address = $this->address->read_by_user($data['user_id']);

                if(count($data_address) == 1){
                    $this->address->update_active($id, "y");
                }

                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil menyimpan data!",
                    "data"      => $this->address->read_id($id)
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "Gagal menyimpan data alamat baru!",
                    "data"      => null
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }
        }
    }

    /**
     * Jika user mengirimkan parameter status maka hanya akan mendapatkan status
     * dan jika user tidak mengirimkan status maka akan mengambil data lainya
     */

    public function index_put()
    {
        if(!empty($this->uri->segment(2)) && !empty($this->put("user_id"))){
            $id = $this->uri->segment(2);
            $user_id = $this->put("user_id");
            if(empty($this->put("status"))){
                $data = array(
                    "name" => $this->put("name"),
                    "receiver_name" => $this->put("receiver_name"),
                    "phone" => $this->put("phone"),
                    "village_id" => $this->put("village_id"),
                    "street" => $this->put("street")
                );

                $this->address->update($id, $data);

                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil merubah alamat!",
                    "data"      => $this->address->read_id($id)
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }else{
                $this->address->disable_active($user_id);

                $this->address->update_active($id, "y");

                $message = array(
                    "status"    => TRUE,
                    "message"   => "Berhasil merubah alamat utama!"
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }
        }else{
            $message = array(
                "status"    => FALSE,
                "message"   => "Address ID & User ID Wajib di isi!",
                "data"      => null
            );

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }

    /**
     * Jika User menghapus alamat aktif, maka alamat bawahnya otomatis menjadi aktif
     */

    public function index_delete()
    {
        if(!empty($this->uri->segment(2))){
            $id = $this->uri->segment(2);

            $data_address = $this->address->read_id($id);

            if($data_address){
                if($data_address['default']=='y'){
                    $user_id = $data_address['user_id'];

                    $data_address = $this->address->read_by_user($user_id);

                    if(count($data_address)==1){
                        $query = $this->address->delete($id);
                        if($query > 0){
                            $message = array(
                                "status"    => TRUE,
                                "message"   => "Alamat berhasil di hapus!"
                            );

                            $this->response($message, REST_Controller::HTTP_OK);
                        }else{
                            $message = array(
                                "status"    => FALSE,
                                "message"   => "Alamat berhasil di hapus!"
                            );

                            $this->response($message, REST_Controller::HTTP_OK);
                        }
                    }else{
                        /**
                         * Mendapatkan data pada index ke 1, karena urutanya alamat utama selalu index ke 0
                         */
                        $data_address = $data_address[1];

                        $query = $this->address->delete($id);

                        if($query > 0){

                            $this->address->update_active($data_address['id'], "y");

                            $message = array(
                                "status"    => TRUE,
                                "message"   => "Alamat berhasil di hapus!"
                            );

                            $this->response($message, REST_Controller::HTTP_OK);
                        }else{
                            $message = array(
                                "status"    => FALSE,
                                "message"   => "Alamat berhasil di hapus!"
                            );

                            $this->response($message, REST_Controller::HTTP_OK);
                        }
                    }
                }else{
                    $query = $this->address->delete($id);
                    if($query > 0){
                        $message = array(
                                "status"    => TRUE,
                                "message"   => "Alamat berhasil di hapus!"
                            );

                        $this->response($message, REST_Controller::HTTP_OK);
                    }else{
                        $message = array(
                            "status"    => FALSE,
                            "message"   => "Gagal menghapus alamat!"
                        );

                        $this->response($message, REST_Controller::HTTP_OK);
                    }
                }
            }else{
                $message = array(
                    "status"    => FALSE,
                    "message"   => "Address ID tidak dikenali!"
                );

                $this->response($message, REST_Controller::HTTP_OK);
            }
        }else{
            $message = array(
                "status"    => FALSE,
                "message"   => "Address ID Wajib di isi!"
            );

            $this->response($message, REST_Controller::HTTP_OK);
        }
    }
}

/* End of file Addresses.php */
    
?>