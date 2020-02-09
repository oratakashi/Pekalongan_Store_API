<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    require APPPATH . '/libraries/REST_Controller.php';

    use Restserver\Libraries\REST_Controller;
    class Regions extends REST_Controller {

        
        public function __construct()
        {
            parent::__construct();
            $this->load->model('RegionModel', 'regions');
        }
        
    
        public function index_get()
        {
            if($this->uri->segment(2)=="subdistrict"){
                if(!empty($this->get("keyword"))){
                    $this->search_get();
                }
            }
        }

        public function search_get()
        {
            $message = array(
                "status"    => TRUE,
                "message"   => "Berhasil mendapatkan data!",
                "data"      => $this->regions->search($this->get("keyword"))
            );
            $this->response($message, REST_Controller::HTTP_OK); // HTTP_OK (200) being the HTTP response code
        }
    }
    
    /* End of file Region.php */
    
?>