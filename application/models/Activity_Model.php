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
    public function log_activity($user_id, $activity_id) {
        $uploadedDate = date('Y-m-d H:i:s'); 
        $data = [
            'user_id'       => $user_id,
            'activity_id'   => $activity_id,
            'activity_date' => $uploadedDate
        ];
        
        return $this->db->insert('audit_trail', $data);
    }
}

