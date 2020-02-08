<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class UserModel extends CI_Model {
    
        public function insert($data)
        {
            $this->db->insert('users', $data);
            return $this->db->affected_rows();     
        }

        public function read_id($id)
        {
            return $this->db->get_where('users', ["id"=>$id])->row_array();
        }

        public function login($data)
        {
            return $this->db->get_where('users', $data)->row_array();
        }
    }
    
    /* End of file UserModel.php */
    
?>