<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_CQPR_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

public function get_tupad_report($start_date = null, $end_date = null, $province = null) {
    // If no filters are provided at all, stop here and return nothing
    if (empty($start_date) && empty($end_date) && empty($province)) {
        return [];
    }

    $this->select_report_fields();
    $this->apply_joins();
    $this->apply_filters($start_date, $end_date, $province);
    
    $query = $this->db->get();
    $results = $query->result_array();

    foreach ($results as &$row) {
        $this->calculate_dynamic_fields($row);
    }

    return $results;
}






private function select_report_fields() {
        $this->db->select('
            (SELECT GROUP_CONCAT(DISTINCT cc.convergence_desc  SEPARATOR ", ") 
             FROM tbl_tupad_list tl 
             JOIN code_convergence cc ON cc.convergence_id = tl.tupad_convergence 
             WHERE tl.reference_no = trans.implementation_reference_no 
             AND tl.tupad_active = 0) as tupad_convergence,
             
            (SELECT tl.nature_of_work 
             FROM tbl_tupad_list tl 
             WHERE tl.reference_no = trans.implementation_reference_no 
             AND tl.tupad_active = 0 
             LIMIT 1) as nature_of_works,
             
            prov.provDesc as implementation_province_desc,
            city.citymunDesc as implementation_city_desc,
            cd.district_no as implementation_district,
            trans.implementation_classification,
            trans.target,
            
   
            trans.completed_employment_benefs,
            
            trans.no_of_days,
            trans.payout_date,
            fs.fund_source_desc as fund_source,
            trans.implementation_province,
            (COALESCE(trans.ppes_amount, 0) + COALESCE(trans.gsis_enrollment_amount, 0) + COALESCE(trans.payout_service_cost, 0) + COALESCE(trans.payment_amount, 0)) AS amount_released,
            CASE 
                WHEN trans.implementation_brgy IS NULL OR TRIM(trans.implementation_brgy) = "" THEN "VARIOUS"
                ELSE cb.brgyDesc 
            END AS implementation_barangay_name,
            
            
            trans.ppes_female as female_count,
             
            (SELECT GROUP_CONCAT(DISTINCT tb.bene_type_desc SEPARATOR ", ") 
             FROM tbl_tupad_list tl_type 
             JOIN code_type_bene tb ON tb.bene_type_id = tl_type.tupad_type 
             WHERE tl_type.reference_no = trans.implementation_reference_no 
             AND tl_type.tupad_active = 0) as tupad_types
        ');
    }

    private function apply_joins() {
        $this->db->from('adl_transactions trans');
        
        // Province Join
        $this->db->join('refprovince prov', 'prov.provCode = trans.implementation_province', 'left');
        
        // City/Municipality Join
        $this->db->join('refcitymun city', 'city.cityCode = trans.implementation_area', 'left');

        // District Join
        $this->db->join('code_district cd', 'cd.district_id = trans.implementation_district', 'left');

        // Barangay Join
        $this->db->join('refbrgy cb', 'cb.brgyCode = trans.implementation_brgy', 'left');

        // Fund Source Join
        $this->db->join('code_fund_source fs', 'fs.fund_source_id = trans.fund_source', 'left');
    }

    private function apply_filters($start_date, $end_date,$province) {
        if (!empty($start_date)) {
            $this->db->where('trans.payout_date >=',$start_date);
        }

        if (!empty($end_date)) {
            $this->db->where('trans.payout_date <=',$end_date);
        }

        if (!empty($province)) {
            $this->db->where('trans.implementation_province',$province);
        }

        // Ensures rows only appear if they have at least one `tupad_active = 0` entry in tbl_tupad_list
        $this->db->where('EXISTS (SELECT 1 FROM tbl_tupad_list ex_tl WHERE ex_tl.reference_no = trans.implementation_reference_no AND ex_tl.tupad_active = 0)');
    }

    private function calculate_dynamic_fields(&$row) {
        // Uses the encoded completed_employment_benefs value directly for short/long term splitting
        $completed_benefs = isset($row['completed_employment_benefs']) ? (int)$row['completed_employment_benefs'] : 0;
        $no_of_days = isset($row['no_of_days']) ? (int)$row['no_of_days'] : 0;

        // Work Period: Short Term (<31 days) vs Long Term (>30 days)
        if ($no_of_days < 31) {
            $row['short_term'] =$completed_benefs;
            $row['long_term'] = 0;         } else {$row['short_term'] = 0;
            $row['long_term'] =$completed_benefs;
        }

        // Total = Short Term + Long Term
        $row['total_term'] = $row['short_term'] +$row['long_term'];

        // Clean and deduplicate tupad types (removes repeating words/categories across comma & slash separators)
        if (!empty($row['tupad_types'])) {
            // Replace slashes with commas to unify separators
            $normalized = str_replace('/', ',', $row['tupad_types']);$items = explode(',', $normalized);$clean_items = [];
            foreach ($items as$item) {
                $item = trim($item);
                if (!empty($item)) {
                    // Use uppercase as key to ensure case-insensitive uniqueness
                    $clean_items[strtoupper($item)] =$item;
                }
            }
            // Rejoin unique items cleanly with a comma and space
            $row['tupad_types'] = implode(', ',$clean_items);
        } else {
            $row['tupad_types'] = '';
        }

        // Project Status Rule
        $row['project_status'] = "ASSISTANCE AWARDED OR RELEASED TO BENEFICIARIES";

        // Convergence Initiative Rule
        $row['convergence_initiative'] = "";
    }











    public function get_provinces() {
        return $this->db->get('refprovince')->result_array();
    }
}