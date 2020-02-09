<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class StoreModel extends CI_Model {
    
        public function create($data)
        {
            $this->db->insert('stores', $data);
            return $this->db->affected_rows();            
        }
    
    }
    
    /* End of file StoreModel.php */
    
?> 