<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_CQPR extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Tupad_CQPR_model');
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        $start_date = $this->input->get('start_date');
        $end_date   = $this->input->get('end_date');
        $province   = $this->input->get('province');

        $data['provinces'] = $this->Tupad_CQPR_model->get_provinces();
        $data['reports']   = $this->Tupad_CQPR_model->get_tupad_report($start_date, $end_date, $province);
        
        $data['start_date'] = $start_date;
        $data['end_date']   = $end_date;
        $data['selected_province'] = $province;

        $this->load->view('tupad/tupad_cqpr', $data);
    }
}