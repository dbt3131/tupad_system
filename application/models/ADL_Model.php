<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ADL_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Fetch all provinces from refprovince table
    public function get_provinces() {
        $query = $this->db->order_by('provDesc', 'ASC')->get('refprovince');
        return $query->result_array();
    }

    // Fetch municipalities/cities based on province code from refcitymun table
    public function get_municipalities_by_province($provCode) {
        $query = $this->db->where('provCode', $provCode)
                          ->order_by('citymunDesc', 'ASC')
                          ->get('refcitymun');
        return $query->result_array();
    }

    // Insert new ADL record into adl_registry table
    public function insert_adl($data) {
        return $this->db->insert('adl_registry', $data);
    }


public function get_ADL()
    {
      $query = $this->db->get('adl_registry'); 
       return $query->result_array();
    }

// ADD THIS MISSING METHOD TO FIX THE ERROR
    public function insert_transaction($data) {
        return $this->db->insert('adl_transactions', $data);
    }











}