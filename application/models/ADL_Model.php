<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ADL_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Fetch all provinces from refprovince table[cite: 1]
    public function get_provinces() {
        $query = $this->db->order_by('provDesc', 'ASC')->get('refprovince');
        return $query->result_array();
    }

    // Fetch all proponents from code_proponent table[cite: 5]
    public function get_proponents() {
        $query = $this->db->order_by('proponent_name', 'ASC')->get('code_proponent');
        return $query->result_array();
    }

    // Fetch all districts from code_district table[cite: 4]
    public function get_districts() {
        $query = $this->db->order_by('district_no', 'ASC')->get('code_district');
        return $query->result_array();
    }

    // Fetch municipalities/cities based on province code from refcitymun table[cite: 1]
    public function get_municipalities_by_province($provCode) {
        $query = $this->db->where('provCode', $provCode)
                          ->order_by('citymunDesc', 'ASC')
                          ->get('refcitymun');
        return $query->result_array();
    }

    // Insert new ADL record into adl_registry table[cite: 1]
    public function insert_adl($data) {
        return $this->db->insert('adl_registry', $data);
    }
public function get_ADL() {
    $this->db->select('
        adl_registry.*, 
        (COALESCE(t.total_deductions, 0) + COALESCE(m.total_maf, 0)) as total_deductions,
        (CAST(adl_registry.adl_subsidy AS DECIMAL(15,2)) - (COALESCE(t.total_deductions, 0) + COALESCE(m.total_maf, 0))) as balance
    ');
    $this->db->from('adl_registry');
    
    // Subquery for transaction deductions
    $this->db->join('(SELECT adl_no, SUM(COALESCE(payout_service_cost,0) + COALESCE(payment_amount,0) + COALESCE(ppes_amount,0) + COALESCE(gsis_enrollment_amount,0)) as total_deductions FROM adl_transactions GROUP BY adl_no) t', 'adl_registry.adl_no = t.adl_no', 'left');
    
    // Subquery for MAF amount deductions
    $this->db->join('(SELECT adl_source, SUM(COALESCE(maf_amount, 0)) as total_maf FROM adl_maf GROUP BY adl_source) m', 'adl_registry.adl_no = m.adl_source', 'left');
    
    $this->db->order_by('adl_registry.adl_date', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
}

    public function insert_transaction($data) {
        return $this->db->insert('adl_transactions', $data);
    }

    // Fetch PPE rate from ppe_rate table[cite: 1]
    public function get_ppe_rate() {
        $query = $this->db->get('ppe_rate');
        $row = $query->row_array();
        return $row ? floatval($row['ppe_rate']) : 0;
    }

    // Fetch GSIS rate from gsis_rate table[cite: 1]
    public function get_gsis_rate() {
        $query = $this->db->get('gsis_rate');
        $row = $query->row_array();
        return $row ? floatval($row['gsis_rate']) : 0;
    }

    // Fetch all ADL numbers for the filter dropdown[cite: 1]
    public function get_all_adl_numbers() {
        $query = $this->db->select('adl_no, adl_subsidy')->order_by('adl_no', 'DESC')->get('adl_registry');
        return $query->result_array();
    }















    public function get_adl_report_breakdown($adl_no, $province = null, $proponent = null, $district = null) {
        $adl = $this->db->where('adl_no', $adl_no)->get('adl_registry')->row_array();
        
        if (!$adl) {
            return null;
        }

        // Fetch and sum MAF records for this ADL source[cite: 4]
        $maf_records = $this->db->where('adl_source', $adl_no)->get('adl_maf')->result_array();
        $total_maf_amount = 0;
        foreach ($maf_records as $maf) {
            $total_maf_amount += floatval($maf['maf_amount']);
        }

        $this->db->select('
            adl_transactions.*,
            refprovince.provDesc as implementation_province_name,
            refcitymun.citymunDesc as implementation_area_name,
            code_proponent.proponent_name as implementation_proponent_name,
            code_district.district_no as implementation_district_no
        ');
        $this->db->from('adl_transactions');
        $this->db->join('refprovince', 'adl_transactions.implementation_province = refprovince.provCode', 'left');
        $this->db->join('refcitymun', 'adl_transactions.implementation_area = refcitymun.cityCode', 'left');
        $this->db->join('code_proponent', 'adl_transactions.implementation_proponent = code_proponent.proponent_id', 'left');
        $this->db->join('code_district', 'adl_transactions.implementation_district = code_district.district_id', 'left');
        

        $this->db->where('adl_transactions.adl_no', $adl_no);

        if (!empty($province)) {
            $this->db->where('adl_transactions.implementation_province', $province);
        }

        if (!empty($proponent)) {
            $this->db->where('adl_transactions.implementation_proponent', $proponent);
        }

        if (!empty($district)) {
            $this->db->where('adl_transactions.implementation_district', $district);
        }

        $this->db->order_by('adl_transactions.encoded_date', 'DESC');
        $transactions = $this->db->get()->result_array();

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

        // Include MAF total amount in total deductions[cite: 3, 4]
        $total_deductions = $total_service_cost + $total_payment + $total_ppes_amount + $total_gsis_amount + $total_maf_amount;
        $remaining_balance = floatval($adl['adl_subsidy']) - $total_deductions;

        return [
            'adl_no'             => $adl['adl_no'],
            'adl_date'           => $adl['adl_date'],
            'date_received'      => $adl['date_received'],
            'target_benefs'      => $adl['target_benefs'],
            'adl_subsidy'         => floatval($adl['adl_subsidy']),
            'transactions'       => $transactions,
            'total_service_cost' => $total_service_cost,
            'total_payment'      => $total_payment,
            'total_ppes_amount'  => $total_ppes_amount,
            'total_gsis_amount'  => $total_gsis_amount,
            'total_maf_amount'   => $total_maf_amount, // Return MAF total
            'total_ppes_count'   => $total_ppes_count,
            'total_gsis_benefs'  => $total_gsis_benefs,
            'total_deductions'   => $total_deductions,
            'remaining_balance'  => $remaining_balance
        ];
    }

    public function count_transactions_by_adl($adl_no) {
    return $this->db->where('adl_no', $adl_no)->from('adl_transactions')->count_all_results();
}


// Fetch a single ADL record by its ID/Number
public function get_adl_by_no($adl_no) {
    return $this->db->where('adl_no', $adl_no)->get('adl_registry')->row_array();
}

// Update ADL record data
public function update_adl($original_adl_no, $data) {
    return $this->db->where('adl_no', $original_adl_no)->update('adl_registry', $data);
}





















    // DATATABLE FOR IMPLEMENTATION LIST (Updated with proponent join)
    public function get_filtered_transactions($province = null, $area = null, $proponent = null, $district = null) {
        if (empty($province)) {
            return [];
        }

        $this->db->select('
            adl_transactions.*, 
            refcitymun.citymunDesc as implementation_area_name, 
            refprovince.provDesc as implementation_province_name,
            code_proponent.proponent_name as implementation_proponent_name');
        $this->db->from('adl_transactions');
        $this->db->join('refprovince', 'adl_transactions.implementation_province = refprovince.provCode', 'left');
        $this->db->join('refcitymun', 'adl_transactions.implementation_area = refcitymun.cityCode', 'left');
        $this->db->join('code_proponent', 'adl_transactions.implementation_proponent = code_proponent.proponent_id', 'left');
        $this->db->order_by('adl_transactions.encoded_date', 'DESC');
        $this->db->where('adl_transactions.implementation_province', $province);

        if (!empty($area)) {
            $this->db->where('adl_transactions.implementation_area', $area);
        }

        if (!empty($proponent)) {
            $this->db->where('adl_transactions.implementation_proponent', $proponent);
        }

        if (!empty($district)) {
            $this->db->where('adl_transactions.implementation_district', $district);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function check_adl_exists($adl_no) {
        return $this->db->where('adl_no', $adl_no)->get('adl_registry')->num_rows() > 0;
    }

    public function check_transaction_exists($ref_no) {
    return $this->db->where('implementation_reference_no', $ref_no)
                    ->count_all_results('adl_transactions') > 0;
}














    public function get_transaction_by_id($id) {
    $transaction = $this->db->where('adl_transact_id', $id)->get('adl_transactions')->row_array();
    
    if ($transaction && !empty($transaction['implementation_reference_no'])) {
        $ref_no = $transaction['implementation_reference_no'];
        
        // Count total beneficiaries for PPE & GSIS count fields
        $transaction['tupad_total_count'] = $this->db->where('reference_no', $ref_no)
                                                     ->count_all_results('tbl_tupad_list');
                                                     
        // Count female beneficiaries for PPE & GSIS female fields
        $transaction['tupad_female_count'] = $this->db->where('reference_no', $ref_no)
                                                      ->where('UPPER(tupad_gender)', 'FEMALE')
                                                      ->count_all_results('tbl_tupad_list');
    } else {
        $transaction['tupad_total_count'] = 0;
        $transaction['tupad_female_count'] = 0;
    }
    
    return $transaction;
}
















    public function update_transaction($id, $data) {
        return $this->db->where('adl_transact_id', $id)->update('adl_transactions', $data);
    }

    public function get_barangays_by_municipality($citymunCode) {
        $query = $this->db->where('citymunCode', $citymunCode)
                          ->order_by('brgyDesc', 'ASC')
                          ->get('refbrgy'); 
        return $query->result_array();
    }

    public function get_transaction_details($id) {
        $this->db->select('
            t.*,
            prov.provDesc AS implementation_province_name,
            mun.citymunDesc AS implementation_area_name,
            u.reg_fname AS encoder_name,
            cp.proponent_name AS proponent_name,
            brgy.brgyDesc AS implementation_brgy_name
        ');
        $this->db->from('adl_transactions t');
        $this->db->join('refprovince prov', 't.implementation_province = prov.provCode', 'left');
        $this->db->join('refcitymun mun', 't.implementation_area = mun.cityCode', 'left');
        $this->db->join('refbrgy brgy', 't.implementation_brgy = brgy.brgyCode', 'left');
        $this->db->join('users u', 't.encoded_by = u.id', 'left' ); 
        $this->db->join('code_proponent cp', 't.implementation_proponent = cp.proponent_id', 'left' ); 

        $this->db->where('t.adl_transact_id', $id);
        
        return $this->db->get()->row_array();
    }

    // adl reporting data table (Updated with optional proponent and district)[cite: 1]
    public function get_all_or_filtered_transactions($province = null, $proponent = null, $district = null) {
        $this->db->select('
            adl_transactions.*, 
            refcitymun.citymunDesc as implementation_area_name,
            code_proponent.proponent_name as implementation_proponent_name,
            code_district.district_no as implementation_district_no, 
            refprovince.provDesc as implementation_province_name');
        $this->db->from('adl_transactions');
        $this->db->join('refprovince', 'adl_transactions.implementation_province = refprovince.provCode', 'left');
        $this->db->join('refcitymun', 'adl_transactions.implementation_area = refcitymun.cityCode', 'left');
        $this->db->join('code_proponent', 'adl_transactions.implementation_proponent = code_proponent.proponent_id', 'left');
        $this->db->join('code_district', 'adl_transactions.implementation_district = code_district.district_id', 'left');
        $this->db->order_by('adl_transactions.encoded_date', 'DESC');
        
        if (!empty($province)) {
            $this->db->where('adl_transactions.implementation_province', $province);
        }

        if (!empty($proponent)) {
            $this->db->where('adl_transactions.implementation_proponent', $proponent);
        }

        if (!empty($district)) {
            $this->db->where('adl_transactions.implementation_district', $district);
        }

        $query = $this->db->get();
        return $query->result_array();
    }


public function check_proponent_exists($proponent_name) {
    return $this->db->where('proponent_name', strtoupper(trim($proponent_name)))
                    ->get('code_proponent')
                    ->num_rows() > 0;
}

// Add these methods inside ADL_Model class[cite: 1]

// Fetch office options for the MAF program dropdown from code_office table
public function get_offices() {
    $query = $this->db->order_by('office_description', 'ASC')->get('code_office');
    return $query->result_array();
}

// Insert new MAF record into adl_maf table
public function insert_adl_maf($data) {
    return $this->db->insert('adl_maf', $data);
}

// Fetch all MAF records or filter by ADL source
public function get_adl_maf_records($adl_source = null) {
    $this->db->select('adl_maf.*, code_office.office_description, users.reg_fname as encoder_name');
    $this->db->from('adl_maf');
    $this->db->join('code_office', 'adl_maf.maf_program = code_office.office_id', 'left');
    $this->db->join('users', 'adl_maf.maf_by = users.id', 'left');
    
    if (!empty($adl_source)) {
        $this->db->where('adl_maf.adl_source', $adl_source);
    }
    
    $this->db->order_by('adl_maf.maf_date', 'DESC');
    return $this->db->get()->result_array();
}

public function get_remaining_target_by_adl($adl_no) {
    // Get total allowed target from adl_registry[cite: 4]
    $adl = $this->db->select('target_benefs')->where('adl_no', $adl_no)->get('adl_registry')->row_array();
    $max_target = $adl ? intval($adl['target_benefs']) : 0;

    // Get sum of encoded targets from adl_transactions for this adl_no[cite: 5]
    $this->db->select_sum('target', 'total_encoded_target');
    $this->db->where('adl_no', $adl_no);
    $query = $this->db->get('adl_transactions')->row_array();
    $encoded_target = $query ? intval($query['total_encoded_target']) : 0;

    return [
        'max_target' => $max_target,
        'encoded_target' => $encoded_target,
        'remaining_target' => max(0, $max_target - $encoded_target)
    ];
}


public function get_remaining_target_by_adl_except($adl_no, $exclude_transact_id) {
    // Get total allowed target from adl_registry[cite: 4]
    $adl = $this->db->select('target_benefs')->where('adl_no', $adl_no)->get('adl_registry')->row_array();
    $max_target = $adl ? intval($adl['target_benefs']) : 0;

    // Get sum of encoded targets from other transactions for this adl_no[cite: 5]
    $this->db->select_sum('target', 'total_encoded_target');
    $this->db->where('adl_no', $adl_no);
    $this->db->where('adl_transact_id !=', $exclude_transact_id);
    $query = $this->db->get('adl_transactions')->row_array();
    $encoded_target = $query ? intval($query['total_encoded_target']) : 0;

    return [
        'max_target' => $max_target,
        'encoded_target' => $encoded_target,
        'remaining_target' => max(0, $max_target - $encoded_target)
    ];
}


public function get_remaining_subsidy_by_adl($adl_no) {
    // Get total allowed subsidy from adl_registry[cite: 4]
    $adl = $this->db->select('adl_subsidy')->where('adl_no', $adl_no)->get('adl_registry')->row_array();
    $max_subsidy = $adl ? floatval($adl['adl_subsidy']) : 0.00;

    // Get sum of encoded subsidy costs from adl_transactions for this adl_no[cite: 5]
    $this->db->select_sum('subsidy_cost', 'total_encoded_subsidy');
    $this->db->where('adl_no', $adl_no);
    $query = $this->db->get('adl_transactions')->row_array();
    $encoded_subsidy = $query ? floatval($query['total_encoded_subsidy']) : 0.00;

    return [
        'max_subsidy' => $max_subsidy,
        'encoded_subsidy' => $encoded_subsidy,
        'remaining_subsidy' => max(0, $max_subsidy - $encoded_subsidy)
    ];
}

















}