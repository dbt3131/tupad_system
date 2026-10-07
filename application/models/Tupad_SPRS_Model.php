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
        // 1. Do not display/fetch data if dates are not filtered yet
        if (empty($start_date) || empty($end_date)) {
            return [];
        }

        $date_clause = " AND t.payout_date >= " . $this->db->escape($start_date) . 
                       " AND t.payout_date <= " . $this->db->escape($end_date);

        // 2. Get Subsidy and Female data using INNER JOIN (only shows provinces with values)
        $this->db->select('
            p.provCode, 
            p.provDesc,
            
            -- ==================== SUBSIDY ====================
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days < 31 THEN (COALESCE(t.payout_service_cost,0) + COALESCE(t.payment_amount,0) + COALESCE(t.ppes_amount,0) + COALESCE(t.gsis_enrollment_amount,0)) ELSE 0 END) as sub_2025_short,
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days > 30 THEN (COALESCE(t.payout_service_cost,0) + COALESCE(t.payment_amount,0) + COALESCE(t.ppes_amount,0) + COALESCE(t.gsis_enrollment_amount,0)) ELSE 0 END) as sub_2025_long,
            
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days < 31 THEN (COALESCE(t.payout_service_cost,0) + COALESCE(t.payment_amount,0) + COALESCE(t.ppes_amount,0) + COALESCE(t.gsis_enrollment_amount,0)) ELSE 0 END) as sub_2026_short,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days > 30 THEN (COALESCE(t.payout_service_cost,0) + COALESCE(t.payment_amount,0) + COALESCE(t.ppes_amount,0) + COALESCE(t.gsis_enrollment_amount,0)) ELSE 0 END) as sub_2026_long,

            -- ==================== FEMALE BENEFS ====================
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days < 31 THEN COALESCE(t.gsis_enrollment_female, 0) ELSE 0 END) as female_2025_short,
            SUM(CASE WHEN t.fund_source = 2 AND t.no_of_days > 30 THEN COALESCE(t.gsis_enrollment_female, 0) ELSE 0 END) as female_2025_long,
            
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days < 31 THEN COALESCE(t.gsis_enrollment_female, 0) ELSE 0 END) as female_2026_short,
            SUM(CASE WHEN t.fund_source = 1 AND t.no_of_days > 30 THEN COALESCE(t.gsis_enrollment_female, 0) ELSE 0 END) as female_2026_long
        ');
        
        $this->db->from('refprovince p');
        $this->db->join('adl_transactions t', 'p.provCode = t.implementation_province' . $date_clause, 'inner', FALSE);
        
        if (!empty($province)) {
            $this->db->where('p.provCode', $province);
        }
        
        $this->db->group_by('p.provCode, p.provDesc');
        $this->db->order_by('p.provDesc', 'ASC');
        $subsidy_results = $this->db->get()->result_array();

        if (empty($subsidy_results)) {
            return [];
        }

        // 3. Get Actual Beneficiary Counts from tbl_tupad_list using INNER JOIN
        $this->db->select('
            p.provCode,
            COUNT(CASE WHEN t.fund_source = 2 AND t.no_of_days < 31 THEN b.reference_no END) as benef_2025_short,
            COUNT(CASE WHEN t.fund_source = 2 AND t.no_of_days > 30 THEN b.reference_no END) as benef_2025_long,
            COUNT(CASE WHEN t.fund_source = 1 AND t.no_of_days < 31 THEN b.reference_no END) as benef_2026_short,
            COUNT(CASE WHEN t.fund_source = 1 AND t.no_of_days > 30 THEN b.reference_no END) as benef_2026_long
        ');
        $this->db->from('refprovince p');
        $this->db->join('adl_transactions t', 'p.provCode = t.implementation_province' . $date_clause, 'inner', FALSE);
        $this->db->join('tbl_tupad_list b', 't.implementation_reference_no = b.reference_no', 'inner');

        if (!empty($province)) {
            $this->db->where('p.provCode', $province);
        }

        $this->db->group_by('p.provCode');
        $benef_results = $this->db->get()->result_array();

        // Index beneficiary counts by provCode
        $benef_map = [];
        foreach ($benef_results as $b) {
            $benef_map[$b['provCode']] = $b;
        }

        // 4. Merge beneficiary counts into report results
        foreach ($subsidy_results as &$row) {
            $code = $row['provCode'];
            $row['benef_2025_short'] = isset($benef_map[$code]) ? $benef_map[$code]['benef_2025_short'] : 0;
            $row['benef_2025_long']  = isset($benef_map[$code]) ? $benef_map[$code]['benef_2025_long'] : 0;
            $row['benef_2026_short'] = isset($benef_map[$code]) ? $benef_map[$code]['benef_2026_short'] : 0;
            $row['benef_2026_long']  = isset($benef_map[$code]) ? $benef_map[$code]['benef_2026_long'] : 0;
        }

        return $subsidy_results;
    }
}