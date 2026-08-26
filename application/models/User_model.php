<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function register($data)
    {
        return $this->db->insert('users', $data);
    }

    public function get_user_by_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->get('users')
            ->row();
    }

    public function get_all_users()
    {
        return $this->db
            ->order_by('id', 'DESC')
            ->get('users')
            ->result();
    }

    public function get_user($id)
    {
        return $this->db
            ->where('id', $id)
            ->get('users')
            ->row();
    }

    public function update_user($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update('users', $data);
    }

    public function delete_user($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete('users');
    }

    public function email_exists($email, $exclude_id = NULL)
    {
        $this->db->where('email', $email);

        if ($exclude_id !== NULL) {
            $this->db->where('id !=', $exclude_id);
        }

        return $this->db->count_all_results('users') > 0;
    }

    public function get_position()
    {
      $query = $this->db->get('code_position'); 
       return $query->result_array();
    }

    public function get_office()
    {
      $query = $this->db->get('code_office'); 
       return $query->result_array();
    }

    public function get_division()
    {
      $query = $this->db->get('code_division'); 
       return $query->result_array();
    }

    public function activate_user($user_id)
    {
        $this->db->where('id', $user_id);
        return $this->db->update('users', array('activated' => 1));
    }

}
