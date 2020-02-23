<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class CategoryModel extends CI_Model {
    
        public function insert($data)
        {
            $this->db->insert('categories', $data);
            return $this->db->affected_rows();
        }

        public function read_id($id)
        {
            return $this->db->get_where('categories', ["id"=>$id])->row_array();
        }

        public function read()
        {
            return $this->db->get('categories')->result_array();
        }
    }
    
    /* End of file CategoryModel.php */
    
?>