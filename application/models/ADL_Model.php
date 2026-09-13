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
        COALESCE(t.total_deductions, 0) as total_deductions,
        (CAST(adl_registry.adl_amount AS DECIMAL(15,2)) - COALESCE(t.total_deductions, 0)) as balance
    ');
    $this->db->from('adl_registry');
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






// Update get_adl_report_breakdown in ADL_Model.php
public function get_adl_report_breakdown($adl_no, $province = null) {
    // Get main ADL registry information
    $adl = $this->db->where('adl_no', $adl_no)->get('adl_registry')->row_array();
    
    if (!$adl) {
        return null;
    }

    // Get all transactions for this specific ADL with joined province and area names
    $this->db->select('
        adl_transactions.*,
        refprovince.provDesc as implementation_province_name,
        refcitymun.citymunDesc as implementation_area_name
    ');
    $this->db->from('adl_transactions');
    $this->db->join('refprovince', 'adl_transactions.implementation_province = refprovince.provCode', 'left');
    $this->db->join('refcitymun', 'adl_transactions.implementation_area = refcitymun.cityCode', 'left');
    $this->db->where('adl_transactions.adl_no', $adl_no);

    // Apply optional province filter if selected by the user
    if (!empty($province)) {
        $this->db->where('adl_transactions.implementation_province', $province);
    }

    // Sort from newest to oldest based on encoded date (or implementation date)
    $this->db->order_by('adl_transactions.encoded_date', 'DESC');

    $transactions = $this->db->get()->result_array();

    // Calculate totals across all transactions for this ADL
    $total_service_cost = 0;
    $total_payment = 0;
    $total_ppes_amount = 0;
    $total_gsis_amount = 0;
    $total_ppes_count = 0;
    $total_gsis_benefs = 0;

    foreach ($transactions as $tx) {
        $total_service_cost += floatval($tx['payout_service_cost']);
        $total_payment += floatval($tx['payment_amount']);
        $total_ppes_amount += floatval($tx['ppes_amount']);
        $total_gsis_amount += floatval($tx['gsis_enrollment_amount']);
        $total_ppes_count += intval($tx['ppes_count']);
        $total_gsis_benefs += intval($tx['gsis_enrollment_benefs']);
    }

    $total_deductions = $total_service_cost + $total_payment + $total_ppes_amount + $total_gsis_amount;
    $remaining_balance = floatval($adl['adl_amount']) - $total_deductions;

    return [
        'adl_no'             => $adl['adl_no'],
        'adl_date'           => $adl['adl_date'],
        'date_received'      => $adl['date_received'],
        'target_benefs'      => $adl['target_benefs'],
        'adl_amount'         => floatval($adl['adl_amount']),
        'transactions'       => $transactions,
        'total_service_cost' => $total_service_cost,
        'total_payment'      => $total_payment,
        'total_ppes_amount'  => $total_ppes_amount,
        'total_gsis_amount'  => $total_gsis_amount,
        'total_ppes_count'   => $total_ppes_count,
        'total_gsis_benefs'  => $total_gsis_benefs,
        'total_deductions'   => $total_deductions,
        'remaining_balance'  => $remaining_balance
    ];
}






//DATATABLE FOR IMPLEMENTATION LIST
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
    $this->db->order_by('adl_transactions.encoded_date', 'DESC');
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

// Fetch barangays based on city/municipality code
    public function get_barangays_by_municipality($citymunCode) {
        $query = $this->db->where('citymunCode', $citymunCode)
                          ->order_by('brgyDesc', 'ASC')
                          ->get('refbrgy'); 
        return $query->result_array();
    }

public function get_transaction_details($id)
{
    $this->db->select('
        t.*,
        prov.provDesc AS implementation_province_name,
        mun.citymunDesc AS implementation_area_name,
        u.reg_fname AS encoder_name,
        brgy.brgyDesc AS implementation_brgy_name
    ');
    $this->db->from('adl_transactions t');
    $this->db->join('refprovince prov', 't.implementation_province = prov.provCode', 'left');
    $this->db->join('refcitymun mun', 't.implementation_area = mun.cityCode', 'left');
    $this->db->join('refbrgy brgy', 't.implementation_brgy = brgy.brgyCode', 'left');
    $this->db->join('users u', 't.encoded_by = u.id', 'left' ); 
    $this->db->where('t.adl_transact_id', $id);
    
    return $this->db->get()->row_array();
}

//adl reporting data table
public function get_all_or_filtered_transactions($province = null) {
    $this->db->select('
        adl_transactions.*, 
        refcitymun.citymunDesc as implementation_area_name, 
        refprovince.provDesc as implementation_province_name');
    $this->db->from('adl_transactions');
    $this->db->join('refprovince', 'adl_transactions.implementation_province = refprovince.provCode', 'left');
    $this->db->join('refcitymun', 'adl_transactions.implementation_area = refcitymun.cityCode', 'left');
    $this->db->order_by('adl_transactions.encoded_date', 'DESC');
    
    if (!empty($province)) {
        $this->db->where('adl_transactions.implementation_province', $province);
    }

    $query = $this->db->get();
    return $query->result_array();
}








































}