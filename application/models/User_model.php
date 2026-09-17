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

public function get_user_profile($user_id)
{
    $this->db->select('users.*, 
                       code_position.position_description, 
                       code_office.office_description, 
                       code_division.division_description, 
                       refprovince.provDesc');
    $this->db->from('users');
    $this->db->join('code_position', 'code_position.position_id = users.position_id', 'left');
    $this->db->join('code_office', 'code_office.office_id = users.office_id', 'left');
    $this->db->join('code_division', 'code_division.division_id = users.division_id', 'left');
    $this->db->join('refprovince', 'refprovince.provCode = users.assigned_prov', 'left');
    $this->db->where('users.id', $user_id);
    return $this->db->get()->row_array();
}







}