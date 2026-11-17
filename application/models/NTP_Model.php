<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NTP_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Fetch all encoded NTP records ordered by latest ID
     */
public function get_all_ntp() {
        $this->db->order_by('ntp_id', 'DESC');
        $query = $this->db->get('adl_ntp');
        return $query->result();
    }

    /**
     * Insert a new NTP record
     */
    public function insert_ntp($data) {
        return $this->db->insert('adl_ntp', $data);
    }

    /**
     * Check if NTP Reference No. already exists
     */
    public function check_duplicate_ntp($ntp_reference_no) {
        $this->db->where('ntp_reference_no', $ntp_reference_no);
        $query = $this->db->get('adl_ntp');
        return $query->num_rows() > 0;
    }

    /**
     * Update approval status and approval date for a specific NTP record
     */
    public function update_approval_status($ntp_id, $data) {
        $this->db->where('ntp_id', $ntp_id);
        return $this->db->update('adl_ntp', $data);
    }

    /**
     * Fetch all ADL records from adl_registry table
     */
    public function get_adl_list() {
        $this->db->order_by('adl_no', 'ASC');
        $query = $this->db->get('adl_registry');
        return $query->result();
    }

    /**
     * Fetch Central Luzon (Region 3) Provinces
     */
    public function get_provinces() {
        return array(
            '030800000' => 'Bataan',
            '031400000' => 'Bulacan',
            '034900000' => 'Nueva Ecija',
            '035400000' => 'Pampanga',
            '036900000' => 'Tarlac',
            '037100000' => 'Zambales',
            '037700000' => 'Aurora'
        );
    }

    /**
     * Fetch Municipalities/Cities by Province Code from refcitymun
     */
    public function get_municipalities_by_province($provCode) {
        $this->db->where('provCode', $provCode);
        $this->db->order_by('citymunDesc', 'ASC');
        $query = $this->db->get('refcitymun');
        return $query->result();
    }

/**
     * Fetch all signatories from adl_ntp_signatory table
     */
    public function get_signatories() {
        $this->db->order_by('ntp_signatory_name', 'ASC');
        $query =$this->db->get('adl_ntp_signatory');
        return $query->result();
    }


}