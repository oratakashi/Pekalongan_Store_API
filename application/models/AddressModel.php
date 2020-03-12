<?php
    
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class AddressModel extends CI_Model {
    
        public function insert($data)
        {
            $this->db->insert('addresses', $data);
            return $this->db->affected_rows();            
        }

        public function read_by_user($user_id)
        {         
            return $this->db->get_where('addresses', ["user_id" => $user_id])->result_array();            
        }    

        public function read_id($id)
        {
            return $this->db->get_where('addresses', ["id" => $id])->row_array();
        }

        public function update_active($id, $status)
        {
            $this->db->where('id', $id);            
            $this->db->update('addresses', ["default" => $status]);
            return $this->db->affected_rows();
        }

        public function disable_active($id)
        {
            $this->db->where('user_id', $id);
            $this->db->update('addresses', ["default" => "n"]);
            return $this->db->affected_rows();
        }

        public function update($id, $data)
        {
            $this->db->where('id', $id);
            $this->db->update('addresses', $data);
            return $this->db->affected_rows();            
        }

        public function delete($id)
        {
            $this->db->where('id', $id);
            $this->db->delete('addresses');
            return $this->db->affected_rows();            
        }
    }
    
    /* End of file AddressModel.php */
    
?>