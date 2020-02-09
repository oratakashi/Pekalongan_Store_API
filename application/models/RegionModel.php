<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class RegionModel extends CI_Model {
    
        public function search($keyword)
        {
            $this->db->like('subdistrict_name', $keyword);
            return $this->db->get('subdistricts')->result_array();
        }
    
    }
    
    /* End of file RegionModel.php */
    
?>