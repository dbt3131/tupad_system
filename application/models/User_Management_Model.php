<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Management_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Fetch all users along with their position, office, division, and province descriptions
     */
    public function get_users() {
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
        $this->db->order_by('users.id', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Fetch all provinces for the dropdown
     */
    public function get_provinces() {
        $this->db->select('provCode, provDesc');
        $this->db->from('refprovince');
        $this->db->order_by('provDesc', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Update user details (activated, role, assigned_prov)
     */
    public function update_user($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }
}