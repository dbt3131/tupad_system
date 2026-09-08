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
    $this->db->select('
        adl_registry.*, 
        refprovince.provDesc, 
        refcitymun.citymunDesc,
        COALESCE(t.total_deductions, 0) as total_deductions,
        (CAST(adl_registry.adl_amount AS DECIMAL(15,2)) - COALESCE(t.total_deductions, 0)) as balance
    ');
    $this->db->from('adl_registry');
    $this->db->join('refprovince', 'adl_registry.adl_province = refprovince.provCode', 'left');
    $this->db->join('refcitymun', 'adl_registry.area_of_implementation = refcitymun.cityCode', 'left');
    $this->db->join('(SELECT adl_no, SUM(COALESCE(payout_service_cost,0) + COALESCE(payment_amount,0) + COALESCE(ppes_amount,0) + COALESCE(gsis_enrollment_amount,0)) as total_deductions FROM adl_transactions GROUP BY adl_no) t', 'adl_registry.adl_no = t.adl_no', 'left');
    
    // Order by ADL Date descending (newest first)
    $this->db->order_by('adl_registry.adl_date', 'DESC');
    
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

public function get_filtered_transactions($province = null, $area = null) {
    if (empty($province)) {
        return [];
    }

    $this->db->select('
        adl_transactions.*, 
        refcitymun.citymunDesc as implementation_area_name, 
        refprovince.provDesc as implementation_province_name');
    $this->db->from('adl_transactions');
    $this->db->join('refprovince', 'adl_transactions.implementation_province = refprovince.provCode', 'left');
    $this->db->join('refcitymun', 'adl_transactions.implementation_area = refcitymun.cityCode', 'left');

    $this->db->where('adl_transactions.implementation_province', $province);

    if (!empty($area)) {
        $this->db->where('adl_transactions.implementation_area', $area);
    }

    $query = $this->db->get();
    return $query->result_array();
}

// Check if ADL No already exists
public function check_adl_exists($adl_no) {
    return $this->db->where('adl_no', $adl_no)->get('adl_registry')->num_rows() > 0;
}

// Check if Implementation Reference No already exists
public function check_transaction_exists($ref_no) {
    return $this->db->where('implementation_reference_no', $ref_no)->get('adl_transactions')->num_rows() > 0;
}

// Fetch single transaction by ID for editing
public function get_transaction_by_id($id) {
    return $this->db->where('adl_transact_id', $id)->get('adl_transactions')->row_array();
}

// Update transaction record
public function update_transaction($id, $data) {
    return $this->db->where('adl_transact_id', $id)->update('adl_transactions', $data);
}














































}