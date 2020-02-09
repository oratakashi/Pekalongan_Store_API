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

        public function cek_email($email)
        {
            return $this->db->get_where('users', ["email"=>$email])->num_rows();
        }

        public function update($id, $data)
        {
            $this->db->update('users', $data, ['id' => $id]);
            
            return $this->db->affected_rows();
        }
    }
    
    /* End of file UserModel.php */
    
?>