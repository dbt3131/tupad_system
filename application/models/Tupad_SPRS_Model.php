<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_SPRS_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_provinces() {
        return $this->db->order_by('provDesc', 'ASC')->get('refprovince')->result_array();
    }

    public function get_sprs_report($start_date = null, $end_date = null, $province = null) {
        // 1. Do not display/fetch data if dates are not filtered yet[cite: 2]
        if (empty($start_date) || empty($end_date)) {
            return [];
        }

        $date_clause = " AND t.payout_date >= " . $this->db->escape($start_date) . 
                       " AND t.payout_date <= " . $this->db->escape($end_date);

        // Required completeness checks for financial/beneficiary columns[cite: 2]
        $completeness_clause = " AND t.payout_service_cost IS NOT NULL AND t.payout_service_cost != ''" .
                               " AND t.payment_amount IS NOT NULL AND t.payment_amount != ''" .
                               " AND t.ppes_amount IS NOT NULL AND t.ppes_amount != ''" .
                               " AND t.gsis_enrollment_amount IS NOT NULL AND t.gsis_enrollment_amount != ''" .
                               " AND t.gsis_enrollment_female IS NOT NULL AND t.gsis_enrollment_female != ''";

        $full_join_clause = $date_clause . $completeness_clause;

        // 2. Get Subsidy, Total Benefs, and Female Benefs from adl_transactions[cite: 1, 2]
        $this->db->select('
            p.provCode, 
            p.provDesc,
            
            -- ==================== SUBSIDY ====================
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days < 31 THEN (t.payout_service_cost + t.payment_amount + t.ppes_amount + t.gsis_enrollment_amount) ELSE 0 END) as sub_2025_short,
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days > 30 THEN (t.payout_service_cost + t.payment_amount + t.ppes_amount + t.gsis_enrollment_amount) ELSE 0 END) as sub_2025_long,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days < 31 THEN (t.payout_service_cost + t.payment_amount + t.ppes_amount + t.gsis_enrollment_amount) ELSE 0 END) as sub_2026_short,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days > 30 THEN (t.payout_service_cost + t.payment_amount + t.ppes_amount + t.gsis_enrollment_amount) ELSE 0 END) as sub_2026_long,

            -- ==================== TOTAL BENEFS (completed_employment_benefs) ====================
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days < 31 THEN t.completed_employment_benefs ELSE 0 END) as benef_2025_short,
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days > 30 THEN t.completed_employment_benefs ELSE 0 END) as benef_2025_long,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days < 31 THEN t.completed_employment_benefs ELSE 0 END) as benef_2026_short,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days > 30 THEN t.completed_employment_benefs ELSE 0 END) as benef_2026_long,

            -- ==================== FEMALE BENEFS (ppes_female) ====================
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days < 31 THEN t.ppes_female ELSE 0 END) as female_2025_short,
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days > 30 THEN t.ppes_female ELSE 0 END) as female_2025_long,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days < 31 THEN t.ppes_female ELSE 0 END) as female_2026_short,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days > 30 THEN t.ppes_female ELSE 0 END) as female_2026_long
        ');
        
        $this->db->from('refprovince p');
        $this->db->join('adl_transactions t', 'p.provCode = t.implementation_province' . $full_join_clause, 'inner', FALSE);
        
        if (!empty($province)) {
            $this->db->where('p.provCode', $province);
        }
        
        $this->db->group_by('p.provCode, p.provDesc');
        $this->db->order_by('p.provDesc', 'ASC');
        
        return $this->db->get()->result_array();
    }
}