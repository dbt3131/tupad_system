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


public function get_ADL() {
    $this->db->select('adl_registry.*, refprovince.provDesc, refcitymun.citymunDesc');
    $this->db->from('adl_registry');
    $this->db->join('refprovince', 'adl_registry.adl_province = refprovince.provCode', 'left');
    $this->db->join('refcitymun', 'adl_registry.area_of_implementation = refcitymun.cityCode', 'left');
    $query = $this->db->get();
    return $query->result_array();
}

// ADD THIS MISSING METHOD TO FIX THE ERROR
    public function insert_transaction($data) {
        return $this->db->insert('adl_transactions', $data);
    }

    // Fetch PPE rate from ppe_rate table
public function get_ppe_rate() {
    $query = $this->db->get('ppe_rate');
    $row = $query->row_array();
    return $row ? floatval($row['ppe_rate']) : 0;
}

// Fetch GSIS rate from gsis_rate table
public function get_gsis_rate() {
    $query = $this->db->get('gsis_rate');
    $row = $query->row_array();
    return $row ? floatval($row['gsis_rate']) : 0;
}

// Fetch all ADL numbers for the filter dropdown
public function get_all_adl_numbers() {
    $query = $this->db->select('adl_no, adl_amount')->order_by('adl_no', 'DESC')->get('adl_registry');
    return $query->result_array();
}

// Fetch ADL details and sum up transaction breakdowns for a specific adl_no
public function get_adl_report_breakdown($adl_no) {
    // Get main ADL registry information
    $adl = $this->db->where('adl_no', $adl_no)->get('adl_registry')->row_array();
    
    if (!$adl) {
        return null;
    }

    // Get aggregated transaction amounts for this specific ADL
    $this->db->select('
        COALESCE(SUM(payout_service_cost), 0) as total_service_cost,
        COALESCE(SUM(payment_amount), 0) as total_payment,
        COALESCE(SUM(ppes_amount), 0) as total_ppes,
        COALESCE(SUM(gsis_enrollment_amount), 0) as total_gsis
    ');
    $this->db->where('adl_no', $adl_no);
    $query = $this->db->get('adl_transactions');
    $totals = $query->row_array();

    // Calculate total deductions and remaining balance
    $total_deductions = $totals['total_service_cost'] + $totals['total_payment'] + $totals['total_ppes'] + $totals['total_gsis'];
    $remaining_balance = floatval($adl['adl_amount']) - $total_deductions;

    return [
        'adl_no'             => $adl['adl_no'],
        'adl_date'           => $adl['adl_date'],
        'date_received'      => $adl['date_received'],
        'target_benefs'      => $adl['target_benefs'],
        'adl_amount'         => floatval($adl['adl_amount']),
        'payout_service_cost'=> floatval($totals['total_service_cost']),
        'payment_amount'     => floatval($totals['total_payment']),
        'ppes_amount'        => floatval($totals['total_ppes']),
        'gsis_amount'        => floatval($totals['total_gsis']),
        'total_deductions'   => $total_deductions,
        'remaining_balance'  => $remaining_balance
    ];
}








}