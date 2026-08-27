<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('Activity_Model');
        $this->load->helper(['url', 'form']);
    }

       public function activity_trail() {
        $data['activities'] = $this->Activity_Model->get_activity_trail();
        
        $this->load->view('users/user_activity', $data);
    }

















}