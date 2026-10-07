<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_SPRS extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Tupad_SPRS_model');
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        // Retrieve GET parameters explicitly
        $data['start_date'] = $this->input->get('start_date', TRUE);
        $data['end_date']   = $this->input->get('end_date', TRUE);
        $data['province']   = $this->input->get('province', TRUE);

        // Fetch dropdown options and report data
        $data['provinces']  = $this->Tupad_SPRS_model->get_provinces();
        $data['report_data'] = $this->Tupad_SPRS_model->get_sprs_report(
            $data['start_date'], 
            $data['end_date'], 
            $data['province']
        );

        $this->load->view('tupad/tupad_sprs_view', $data);

    }
}