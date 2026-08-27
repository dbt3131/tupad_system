<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Report_Bene_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Log user activity to the audit trail
     * @param int $user_id
     * @param int $activity_id (representing the type of action or ID)
     */

    public function get_summary_report($start_date = null, $end_date = null, $view_type = 'all') {
        $this->db->order_by('provDesc', 'ASC'); 
        $provinces = $this->db->get('refprovince')->result_array();

        $this->db->order_by('citymunDesc', 'ASC');
        $municipalities = $this->db->get('refcitymun')->result_array();

        $this->db->order_by('bene_type_id', 'ASC');
        $bene_types = $this->db->get('code_type_bene')->result_array();

        // Build query based on view selection
        if ($view_type == 'province_only') {
            $this->db->select('t.tupad_province, t.tupad_type, COUNT(t.tupad_idnumber) as total_count');
            $this->db->from('tbl_tupad_list t');
            
            if (!empty($start_date) && !empty($end_date)) {
                $this->db->where('DATE(t.uploaded_at) >=', $start_date);
                $this->db->where('DATE(t.uploaded_at) <=', $end_date);
            }
            $this->db->group_by(['t.tupad_province', 't.tupad_type']);
        } else {
            // 'all' or 'municipality_only'
            $this->db->select('t.tupad_province, t.tupad_municipality, t.tupad_type, COUNT(t.tupad_idnumber) as total_count');
            $this->db->from('tbl_tupad_list t');
            
            if (!empty($start_date) && !empty($end_date)) {
                $this->db->where('DATE(t.uploaded_at) >=', $start_date);
                $this->db->where('DATE(t.uploaded_at) <=', $end_date);
            }
            $this->db->group_by(['t.tupad_province', 't.tupad_municipality', 't.tupad_type']);
        }

        $query = $this->db->get()->result_array();

        $matrix = [];
        if ($view_type == 'province_only') {
            foreach ($query as $row) {
                $matrix[$row['tupad_province']][$row['tupad_type']] = $row['total_count'];
            }
        } else {
            foreach ($query as $row) {
                $matrix[$row['tupad_province']][$row['tupad_municipality']][$row['tupad_type']] = $row['total_count'];
            }
        }

        return [
            'provinces' => $provinces,
            'municipalities' => $municipalities,
            'bene_types' => $bene_types,
            'matrix' => $matrix,
            'view_type' => $view_type
        ];
    }
}