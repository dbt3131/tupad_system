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



















}