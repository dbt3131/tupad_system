<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_CQPR_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_tupad_report($start_date = null, $end_date = null, $province = null) {
        $this->select_report_fields();
        $this->apply_joins();
        $this->apply_filters($start_date, $end_date, $province);
        
        $query = $this->db->get();
        $results = $query->result_array();

        // Process dynamic calculations per row
        foreach ($results as &$row) {
            $this->calculate_dynamic_fields($row);
        }

        return $results;
    }

    private function select_report_fields() {
        $this->db->select('
            (SELECT tl.nature_of_work FROM tbl_tupad_list tl WHERE tl.reference_no = trans.implementation_reference_no LIMIT 1) as nature_of_works,
            prov.provDesc as implementation_province_desc,
            city.citymunDesc as implementation_city_desc,
            cd.district_no as implementation_district,
            trans.implementation_classification,
            trans.target,
            trans.no_of_days,
            trans.payout_date,
            fs.fund_source_desc as fund_source,
            trans.implementation_province,
            (COALESCE(trans.ppes_amount, 0) + COALESCE(trans.gsis_enrollment_amount, 0) + COALESCE(trans.payout_service_cost, 0) + COALESCE(trans.payment_amount, 0)) AS amount_released,
            CASE 
                WHEN trans.implementation_brgy IS NULL OR TRIM(trans.implementation_brgy) = "" THEN "VARIOUS"
                ELSE cb.brgyDesc 
            END AS implementation_barangay_name,
            (SELECT COUNT(*) FROM tbl_tupad_list sub_tl WHERE sub_tl.reference_no = trans.implementation_reference_no AND (sub_tl.tupad_gender = "Female" OR sub_tl.tupad_gender = "F")) as female_count
        ');
    }

    private function apply_joins() {
        $this->db->from('adl_transactions trans');
        
        // Province Join[cite: 13]
        $this->db->join('refprovince prov', 'prov.provCode = trans.implementation_province', 'left');
        
        // City/Municipality Join[cite: 13]
        $this->db->join('refcitymun city', 'city.cityCode = trans.implementation_area', 'left');

        // District Join (displays district_no instead of district_id)
        $this->db->join('code_district cd', 'cd.district_id = trans.implementation_district', 'left');

        // Barangay Join (displays barangay name or Various if empty)
        $this->db->join('refbrgy cb', 'cb.brgyCode = trans.implementation_brgy', 'left');

        // Fund Source Join (maps fund_source ID to its text description)
        $this->db->join('code_fund_source fs', 'fs.fund_source_id = trans.fund_source', 'left');
    }

    private function apply_filters($start_date, $end_date, $province) {
        // Allow filtering if at least one date is provided, or both
        if (!empty($start_date)) {
            $this->db->where('trans.payout_date >=', $start_date);
        }

        if (!empty($end_date)) {
            $this->db->where('trans.payout_date <=', $end_date);
        }

        if (!empty($province)) {
            $this->db->where('trans.implementation_province', $province);
        }
    }

    private function calculate_dynamic_fields(&$row) {
        $target = isset($row['target']) ? (int)$row['target'] : 0;
        $no_of_days = isset($row['no_of_days']) ? (int)$row['no_of_days'] : 0;

        // Work Period: Short Term (<31 days) vs Long Term (>30 days)[cite: 13]
        if ($no_of_days < 31) {
            $row['short_term'] = $target;
            $row['long_term'] = 0;
        } else {
            $row['short_term'] = 0;
            $row['long_term'] = $target;
        }

        // Total = Short Term + Long Term[cite: 13]
        $row['total_term'] = $row['short_term'] + $row['long_term'];

        // Project Status Rule[cite: 13]
        $row['project_status'] = "ASSISTANCE AWARDED OR RELEASED TO BENEFICIARIES";

        // Convergence Initiative Rule[cite: 13]
        $row['convergence_initiative'] = "";
    }

    public function get_provinces() {
        return $this->db->get('refprovince')->result_array();
    }
}