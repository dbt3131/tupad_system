<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Report_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

public function get_filtered_report($start_date = null, $end_date = null, $assigned_prov = null) {
    if (empty($start_date) || empty($end_date)) {
        return [];
    }

    $this->db->select('
        tupad_allocations.unique_id,
        tupad_allocations.date_coordinated,
        tupad_allocations.adl_no,
        tupad_allocations.reference_no,
        tupad_allocations.assign_prov,
        tupad_allocations.lgu_municipality_city,
        tupad_allocations.batch_no,
        tupad_allocations.final_physical_ppe_requested,
        tupad_allocations.final_physical_target,
        tupad_allocations.period_start,
        tupad_allocations.period_end,
        SUM(tupad_allocations.physical_target) as physical_target,
        SUM(tupad_allocations.total_project_funds) as total_project_funds,
        SUM(tupad_allocations.final_physical_target) as gpai_count,
        SUM(tupad_allocations.final_physical_ppe_requested) as ppe_count,
        refprovince.provDesc as province_name,
        users.reg_fname,
        users.reg_mname,
        users.reg_lname,
        users.reg_extname
    ');
    $this->db->from('tupad_allocations');
    
    $this->db->join('refprovince', 'refprovince.id = tupad_allocations.assign_prov', 'left');
    $this->db->join('users', 'users.id = tupad_allocations.encoded_by', 'left');

    if (!empty($assigned_prov)) {
        $this->db->where('tupad_allocations.assign_prov', $assigned_prov);
    }

    $this->db->where('tupad_allocations.date_coordinated >=', $start_date);
    $this->db->where('tupad_allocations.date_coordinated <=', $end_date);

    $this->db->group_by([
        'tupad_allocations.unique_id',
        'tupad_allocations.date_coordinated',
        'tupad_allocations.adl_no',
        'tupad_allocations.reference_no',
        'tupad_allocations.assign_prov',
        'tupad_allocations.lgu_municipality_city',
        'tupad_allocations.batch_no',
        'tupad_allocations.period_start',
        'tupad_allocations.period_end',
        'refprovince.provDesc',
        'users.reg_fname',
        'users.reg_mname',
        'users.reg_lname',
        'users.reg_extname'
    ]);
    
    $this->db->order_by('tupad_allocations.date_coordinated', 'DESC');
    return $this->db->get()->result_array();
}

    public function get_filtered_report_prov($start_date = null, $end_date = null, $assigned_prov = null) {
        if (empty($start_date) || empty($end_date)) {
            return [];
        }

        $this->db->select('
            tupad_allocations.id,
            tupad_allocations.unique_id,
            tupad_allocations.date_coordinated,
            tupad_allocations.adl_no,
            tupad_allocations.reference_no,
            tupad_allocations.assign_prov,
            tupad_allocations.lgu_municipality_city,
            tupad_allocations.batch_no,
            tupad_allocations.period_start,
            tupad_allocations.orientation_schedule,
            tupad_allocations.period_end,
            tupad_allocations.final_physical_ppe_requested,
            tupad_allocations.remarks,
            tupad_allocations.ppe_request,
            tupad_allocations.ppe_pickup_schedule,
            tupad_allocations.term,
            tupad_allocations.no_of_days_work,
            tupad_allocations.implementation_status,
            tupad_allocations.final_physical_target,
            tupad_allocations.mode_of_payout,
            SUM(tupad_allocations.total_project_funds) as total_project_funds,
            SUM(tupad_allocations.final_physical_target) as gpai_count,
            SUM(tupad_allocations.final_physical_ppe_requested) as ppe_count,
            refprovince.provDesc as province_name,
            users.reg_fname,
            users.reg_mname,
            users.reg_lname,
            users.reg_extname
        ');
        $this->db->from('tupad_allocations');
        
        $this->db->join('refprovince', 'refprovince.id = tupad_allocations.assign_prov', 'left');
        $this->db->join('users', 'users.id = tupad_allocations.encoded_by', 'left');

        if (!empty($assigned_prov)) {
            $this->db->where('tupad_allocations.assign_prov', $assigned_prov);
        }

        $this->db->where('tupad_allocations.date_coordinated >=', $start_date);
        $this->db->where('tupad_allocations.date_coordinated <=', $end_date);

        $this->db->group_by([
            'tupad_allocations.id',
            'tupad_allocations.unique_id',
            'tupad_allocations.date_coordinated',
            'tupad_allocations.adl_no',
            'tupad_allocations.reference_no',
            'tupad_allocations.remarks',
            'tupad_allocations.ppe_request',
            'tupad_allocations.ppe_pickup_schedule',
            'tupad_allocations.assign_prov',
            'tupad_allocations.orientation_schedule',
            'tupad_allocations.lgu_municipality_city',
            'tupad_allocations.batch_no',
            'tupad_allocations.period_start',
            'tupad_allocations.period_end',
            'tupad_allocations.term',
            'tupad_allocations.no_of_days_work',
            'tupad_allocations.implementation_status',
            'tupad_allocations.final_physical_target',
            'tupad_allocations.mode_of_payout',
            'refprovince.provDesc',
            'users.reg_fname',
            'users.reg_mname',
            'users.reg_lname',
            'users.reg_extname'
        ]);
        
        $this->db->order_by('tupad_allocations.date_coordinated', 'DESC');
        return $this->db->get()->result_array();
    }


public function get_coa_report_data($start_date = null, $end_date = null) {
    // Return empty array immediately if dates are not provided or empty
    if (empty($start_date) || empty($end_date)) {
        return [];
    }

    $this->db->select('
        adl_transactions.*,
        adl_registry.adl_subsidy,
        refprovince.provDesc as province_name,
        refcitymun.citymunDesc as municipality_name
    ');
    $this->db->from('adl_transactions');
    $this->db->join('adl_registry', 'adl_registry.adl_no = adl_transactions.adl_no', 'left');
    $this->db->join('refprovince', 'refprovince.provCode = adl_transactions.implementation_province', 'left');
    $this->db->join('refcitymun', 'refcitymun.cityCode = adl_transactions.implementation_area', 'left');

    $this->db->where('adl_transactions.ongoing_implementation_start_date >=', $start_date);
    $this->db->where('adl_transactions.ongoing_implementation_start_date <=', $end_date);

    // --- REQUIRE VALUES ON SPECIFIED COLUMNS ---
    $this->db->where('adl_transactions.gsis_enrollment_date IS NOT NULL');
    $this->db->where('adl_transactions.gsis_enrollment_date !=', '0000-00-00');
    $this->db->where('adl_transactions.gsis_enrollment_date !=', '');

    $this->db->where('adl_transactions.gsis_enrollment_benefs IS NOT NULL');
    $this->db->where('adl_transactions.gsis_enrollment_benefs >', 0);

    $this->db->where('adl_transactions.gsis_enrollment_female IS NOT NULL');
    $this->db->where('adl_transactions.gsis_enrollment_female >', 0);

    $this->db->where('adl_transactions.gsis_enrollment_amount IS NOT NULL');
    $this->db->where('adl_transactions.gsis_enrollment_amount !=', '');
    $this->db->where('adl_transactions.gsis_enrollment_amount !=', '0');

    $this->db->where('adl_transactions.ongoing_implementation_start_date IS NOT NULL');
    $this->db->where('adl_transactions.ongoing_implementation_start_date !=', '0000-00-00');
    $this->db->where('adl_transactions.ongoing_implementation_start_date !=', '');

    $this->db->where('adl_transactions.ongoing_implementation_end_date IS NOT NULL');
    $this->db->where('adl_transactions.ongoing_implementation_end_date !=', '0000-00-00');
    $this->db->where('adl_transactions.ongoing_implementation_end_date !=', '');
    // -------------------------------------------

    $this->db->order_by('adl_transactions.adl_transact_id', 'DESC');
    return $this->db->get()->result_array();
}








public function get_user_signature_details($user_id) {
    $this->db->select("
        TRIM(CONCAT(users.reg_fname, ' ', IF(users.reg_mname != '', CONCAT(SUBSTRING(users.reg_mname, 1, 1), '. '), ''), users.reg_lname, ' ', users.reg_extname)) as user_fullname,
        code_position.position_description
    ");
    $this->db->from('users');
    $this->db->join('code_position', 'code_position.position_id = users.position_id', 'left');
    $this->db->where('users.id', $user_id);
    return $this->db->get()->row_array();
}



public function get_implementation_status_report($start_date = null, $end_date = null) {
    if (empty($start_date) || empty($end_date)) {
        return [];
    }

    // Fetch the wage amount dynamically from the wage_rate table[cite: 8]
    $wage_query = $this->db->select('wage_amount')
                           ->from('wage_rate')
                           ->order_by('wage_id', 'DESC')
                           ->get()
                           ->row_array();
    
    $dynamic_wage = $wage_query['wage_amount'] ?? 600;

    $this->db->select('
        adl_transactions.*,
        cp.proponent_name as p_name,
        refcitymun.citymunDesc as area_description
    ');
    $this->db->from('adl_transactions');
    $this->db->join('refcitymun', 'refcitymun.cityCode = adl_transactions.implementation_area', 'left');
    $this->db->join('code_proponent cp', 'cp.proponent_id = adl_transactions.implementation_proponent', 'left');

    $this->db->group_start();
        $this->db->where('adl_transactions.encoded_date >=', $start_date);
        $this->db->where('adl_transactions.encoded_date <=', $end_date);
        $this->db->or_where('adl_transactions.encoded_date', '0000-00-00');
        $this->db->or_where('adl_transactions.encoded_date IS NULL');
    $this->db->group_end();

    $this->db->order_by('adl_transactions.adl_transact_id', 'DESC');
    $result = $this->db->get()->result_array();

    foreach ($result as &$row) {
        $row['wage_amount'] = $dynamic_wage;
    }

    return $result;
}


















}






