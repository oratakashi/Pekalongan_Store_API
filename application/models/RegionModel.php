<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class RegionModel extends CI_Model {
    
        public function search_subdistrict($keyword)
        {
            $this->db->like('subdistrict_name', $keyword);
            return $this->db->get('subdistricts')->result_array();
        }

        public function search_village($keyword)
        {
            $this->db->like('village_name', $keyword);
            return $this->db->get('view_region')->result_array();
        }

        public function read_id_subdistrict($id)
        {
            return $this->db->get_where('subdistricts', ["subdistrict_id" => $id])->row_array();
        }

        public function read_id_village($id)
        {
            return $this->db->get_where('view_region', ["village_id" => $id])->row_array();
        }
    
    }
    
    /* End of file RegionModel.php */
    
?>