<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class EtalaseModel extends CI_Model {
    
        public function create($data)
        {
            $this->db->insert('etalases', $data);
            
            return $this->db->affected_rows();
        }
    
    }
    
    /* End of file EtalaseModel.php */
    
?>