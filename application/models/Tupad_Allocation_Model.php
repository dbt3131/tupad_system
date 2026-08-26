<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Allocation_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Fetch all encoded allocation entries (restricted by session province)
    public function get_all() {
        $assigned_no = $this->session->userdata('assigned_prov');
        if (!empty($assigned_no)) {
            $this->db->where('assign_prov', $assigned_no);
        }
        return $this->db->order_by('id', 'DESC')->get('tupad_allocations')->result_array();
    }

    // Fetch allocations for the index view
    public function get_allocations($assigned_prov = null) {
        $assigned_no = !empty($assigned_prov) ? $assigned_prov : $this->session->userdata('assigned_prov');

        if (!empty($assigned_no)) {
            $this->db->where('assign_prov', $assigned_no);
        }
        
        return $this->db->order_by('unique_id', 'DESC')->get('tupad_allocations')->result_array();
    }

    // Insert a new encoding entry row
    public function insert_entry($data) {
        return $this->db->insert('tupad_allocations', $data);
    }

    // Check if LGU exists in refcitymun table
    public function check_lgu_exists($municipality) {
        $this->db->where('citymunDesc', $municipality);
        $query = $this->db->get('refcitymun');
        return $query->num_rows() > 0;
    }

    // Fetch uploaded file summaries
    public function get_uploaded_files_alloc() {
        $sql = "SELECT t.file_name, 
                       COUNT(t.id) as total_records, 
                       MAX(t.uploaded_at) as uploaded_at,
                       u.reg_fname as uploader_fname, 
                       u.reg_lname as uploader_lname
                FROM tbl_tupad_list t
                LEFT JOIN users u ON u.id = t.user_id
                WHERE t.file_name IS NOT NULL
                GROUP BY t.file_name, u.reg_fname, u.reg_lname
                ORDER BY t.file_name ASC";

        return $this->db->query($sql)->result_array();
    }

    // Fetch a single allocation entry by ID for editing
    public function get_entry($id) {
        return $this->db->get_where('tupad_allocations', array('id' => $id))->row_array();
    }

    // Update an existing allocation entry row
    public function update_entry($id, $data) {
        return $this->db->where('id', $id)->update('tupad_allocations', $data);
    }
}