<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Monitoring extends CI_Controller {

    public function __construct() {
        parent::__construct();
$this->load->library('session');
    if (!$this->session->userdata('logged_in')) {
        redirect('auth/login');
    }

         $this->load->model('Tupad_Allocation_Model');
        $this->load->model('Tupad_Report_Model');
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        $start_date = $this->input->get('start_date', true);
        $end_date   = $this->input->get('end_date', true);
        $is_pdf     = $this->input->get('pdf', true);
        $user_prov  = $this->session->userdata('assigned_prov');

        // FORCE EMPTY DATA IF DATES ARE NOT SET
        if (empty($start_date) || empty($end_date)) {
            $data['allocations'] = []; // This guarantees zero records
            $data['start_date']  = '';
            $data['end_date']    = '';
        } else {
            $data['allocations'] = $this->Tupad_Report_Model->get_filtered_report($start_date, $end_date, $user_prov);
            $data['start_date']  = $start_date;
            $data['end_date']    = $end_date;

            if ($is_pdf == '1') {
                $this->load->view('tupad/tupad_monitoring_pdf', $data);
                return;
            }
        }

        $this->load->view('tupad/tupad_monitoring_report', $data);
    }

 public function tupad_monitoring_prov() {
        $start_date = $this->input->get('start_date', true);
        $end_date   = $this->input->get('end_date', true);
        $is_pdf     = $this->input->get('pdf', true);
        $user_prov  = $this->session->userdata('assigned_prov');

        // FORCE EMPTY DATA IF DATES ARE NOT SET
        if (empty($start_date) || empty($end_date)) {
            $data['allocations'] = []; // This guarantees zero records
            $data['start_date']  = '';
            $data['end_date']    = '';
        } else {
            $data['allocations'] = $this->Tupad_Report_Model->get_filtered_report_prov($start_date, $end_date, $user_prov);
            $data['start_date']  = $start_date;
            $data['end_date']    = $end_date;

            if ($is_pdf == '1') {
                $this->load->view('tupad/tupad_monitoring_pdf_prov', $data);
                return;
           }
        }

        $this->load->view('tupad/tupad_monitoring_report_prov', $data);
    }













}