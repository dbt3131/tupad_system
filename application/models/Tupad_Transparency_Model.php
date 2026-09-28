<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Transparency_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

public function get_provinces() {
        return $this->db->get('refprovince')->result();
    }

    // Filtered data query based on date range and province
    public function get_filtered_beneficiaries($start_date = null, $end_date = null, $province_code = null) {
        $this->db->select('tbl_tupad_list.*, adl_transactions.payout_date, refprovince.provDesc as province_name, refcitymun.citymunDesc as city_name, refbrgy.brgyDesc as brgy_name, cb.bene_type_desc');
        $this->db->from('tbl_tupad_list');
        $this->db->join('adl_transactions', 'tbl_tupad_list.reference_no = adl_transactions.implementation_reference_no', 'inner');
        $this->db->join('refprovince', 'tbl_tupad_list.tupad_province = refprovince.provCode', 'left');
        $this->db->join('refcitymun', 'tbl_tupad_list.tupad_municipality = refcitymun.cityCode', 'left');
        $this->db->join('refbrgy', 'tbl_tupad_list.tupad_barangay = refbrgy.brgyCode', 'left');
        $this->db->join('code_type_bene cb', 'tbl_tupad_list.tupad_type = cb.bene_type_id', 'left');


        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('adl_transactions.payout_date >=', $start_date);
            $this->db->where('adl_transactions.payout_date <=', $end_date);
        }

        if (!empty($province_code)) {
            $this->db->where('tbl_tupad_list.tupad_province', $province_code);
        }

        $query = $this->db->get();
        return $query->result_array();
    }










}






