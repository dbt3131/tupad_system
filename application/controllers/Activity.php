<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        
        // Security Check: Redirect unauthenticated users to login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        // Load required models and helpers
        $this->load->model('Activity_Model');
        $this->load->helper(['url', 'form']);
    }

    public function activity_trail() {
        // Fetch activity logs from the database model
        $data['activities'] = $this->Activity_Model->get_activity_trail();    
        
        // Load the user activity view template and pass the data array
        $this->load->view('users/user_activity', $data);
    }

}