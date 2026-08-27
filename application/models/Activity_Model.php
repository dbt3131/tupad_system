<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Log user activity to the audit trail
     * @param int $user_id
     * @param int $activity_id (representing the type of action or ID)
     */
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
                    audit_trail.remarks,
                    audit_trail_code.activity_desc
                FROM audit_trail
                LEFT JOIN users ON users.id = audit_trail.user_id
                LEFT JOIN audit_trail_code ON audit_trail_code.id = audit_trail.activity_id
                ORDER BY audit_trail.activity_date DESC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    
















}

