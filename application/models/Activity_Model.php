<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function log_activity($reference_no, $user_id, $activity_id) {
        $uploadedDate = date('Y-m-d H:i:s'); 
        $data = [
            'user_id'       => $user_id,
            'activity_id'   => $activity_id,
            'remarks'       => $reference_no,
            'activity_date' => $uploadedDate
        ];
        
        return $this->db->insert('audit_trail', $data);
    }

    public function get_activity_trail() {
        $sql = "SELECT 
                    audit_trail.trail_id, 
                    audit_trail.activity_date, 
                    users.reg_fname, 
                    users.division_id,
                    code_position.position_description,
                    cd.division_description,
                    audit_trail.remarks,
                    audit_trail_code.activity_desc
                FROM audit_trail
                LEFT JOIN users ON users.id = audit_trail.user_id
                LEFT JOIN code_position ON code_position.position_id = users.position_id
                LEFT JOIN audit_trail_code ON audit_trail_code.id = audit_trail.activity_id
                LEFT JOIN code_division cd ON cd.division_id = users.division_id
                ORDER BY audit_trail.activity_date DESC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_summary_reports() {
        $sql = "SELECT 
                    u.reg_fname,
                    COUNT(at.trail_id) AS transactions_made,
                    GROUP_CONCAT(DISTINCT atc.activity_desc SEPARATOR ', ') AS type_of_transactions
                FROM `audit_trail` at
                JOIN `users` u ON u.id = at.user_id
                LEFT JOIN `audit_trail_code` atc ON at.activity_id = atc.id
                GROUP BY u.id, u.reg_fname
                ORDER BY transactions_made DESC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
}