<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');

    require APPPATH . '/libraries/REST_Controller.php';

    use Restserver\Libraries\REST_Controller;
    class Stores extends REST_Controller {

        
        public function __construct()
        {
            parent::__construct();
            $this->load->model('UserModel', 'users');
            $this->load->model('StoreModel', 'stores');
            $this->load->model('EtalaseModel', 'etalases');
        }
        
    
        public function index_get()
        {
            
        }

        public function index_post()
        {
            if(empty($this->uri->segment(2))){
                $this->store_post();
            }
        }

        public function store_post()
        {
            if(empty($this->post('user_id'))){
                $message = array(
                    "status"    => FALSE,
                    "message"   => "User ID tidak boleh kosong!",
                    "data"      => array()
                );
                $this->response($message, REST_Controller::HTTP_OK); // HTTP_OK (200) being the HTTP response code
            }else{
                $data_store = array(
                    "id"                => $this->uuid->v4(),
                    "name"              => $this->post("name"),
                    "description"       => $this->post("description"),
                    "user_id"           => $this->post("user_id"),
                    "subdistrict_id"    => $this->post("subdistrict_id")
                );

                $query = $this->stores->create($data_store);

                if($query > 0){
                    $data_etalases = array(
                        "id"            => $this->uuid->v4(),
                        "name"          => "Semua Produk",
                        "store_id"      => $data_store['id']
                    );

                    $this->etalases->create($data_etalases);

                    $data_etalases = array(
                        "id"            => $this->uuid->v4(),
                        "name"          => "Best Seller",
                        "store_id"      => $data_store['id']
                    );

                    $this->etalases->create($data_etalases);

                    $this->users->update($data_store['user_id'], ['store_id' => $data_store['id']]);

                    $message = array(
                        "status"    => TRUE,
                        "message"   => "Berhasil membuat toko!",
                        "data"      => $data_store
                    );

                    $this->response($message, REST_Controller::HTTP_OK);
                    
                }else{
                    $message = array(
                        "status"    => FALSE,
                        "message"   => "Gagal membuat toko!, Coba lagi nanti!",
                        "data"      => array()
                    );
                    $this->response($message, REST_Controller::HTTP_OK); // HTTP_OK (200) being the HTTP response code
                }
            }
        }
    }
    
    /* End of file Stores.php */
    
?>