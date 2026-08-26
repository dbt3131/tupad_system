<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Payrolls extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('Tupad_Payroll_Model');
        $this->load->helper(['url', 'form']);
    }

    /**
     * Display payroll monitoring view with database records & initial province list
     */
    public function payroll_encode() {
        $data['payrolls'] = $this->Tupad_Payroll_Model->get_all_payrolls();
        $data['provinces'] = $this->Tupad_Payroll_Model->get_provinces();
        $data['payoutSite'] = $this->Tupad_Payroll_Model->get_payout_site();
        $data['monthDileep'] = $this->Tupad_Payroll_Model->get_month();
        $data['receivedStaff'] = $this->Tupad_Payroll_Model->get_staff_received();

        $this->load->view('tupad_payrolls/tupad_payrolls_monitoring', $data);
    }

    /**
     * AJAX Endpoint: Get municipalities for a selected province
     */
    public function get_municipalities() {
        $provCode = $this->input->post('provCode', TRUE);
        $municipalities = $this->Tupad_Payroll_Model->get_municipalities_by_province($provCode);
        
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($municipalities));
    }

    /**
     * AJAX Endpoint: Get barangays for a selected municipality
     */
    public function get_barangays() {
        $cityCode = $this->input->post('cityCode', TRUE);
        $barangays = $this->Tupad_Payroll_Model->get_barangays_by_municipality($cityCode);
        
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($barangays));
    }

    /**
     * Store a newly encoded payroll record
     */
    public function store() {
        $data = $this->get_form_input();
        

        if ($this->Tupad_Payroll_Model->insert_payroll($data)) {
            $this->session->set_flashdata('success', 'Payroll record successfully encoded.');
        } else {
            $this->session->set_flashdata('error', 'Failed to encode payroll record.');
        }

         
        redirect('tupad_payrolls/payroll_encode');
    }

    /**
     * Update an existing payroll record
     */
    public function update($id) {
        if (!$id) {
            redirect('tupad_payrolls/payroll_encode');
        }

        $data = $this->get_form_input();

        if ($this->Tupad_Payroll_Model->update_payroll($id, $data)) {
            $this->session->set_flashdata('success', 'Payroll record updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update payroll record.');
        }

        redirect('tupad_payrolls/payroll_encode');
    }

       

    /**
     * Helper method to parse form submissions
     */
    private function get_form_input() {
        $uploadedBy = $this->session->userdata('user_id');
        $uploadedDate = date('Y-m-d H:i:s');
        return [
            'month_reported_dileep'         => $this->input->post('month_reported_dileep', TRUE),
            'date_received'                 => $this->input->post('date_received', TRUE),
            'received_by'                   => $this->input->post('received_by', TRUE),
            'nature_of_project'             => $this->input->post('nature_of_project', TRUE),
            'batch_no'                      => $this->input->post('batch_no', TRUE),
            'province'                      => $this->input->post('province', TRUE),
            'municipality'                  => $this->input->post('municipality', TRUE),
            'barangay'                      => $this->input->post('barangay', TRUE),
            'adl_no'                        => $this->input->post('adl_no', TRUE),
            'fisher_folks_count'            => (int)$this->input->post('fisher_folks_count', TRUE),
            'farmers_count'                 => (int)$this->input->post('farmers_count', TRUE),
            'implementation_reference_no'   => $this->input->post('implementation_reference_no', TRUE),
            'no_of_payrolled_benefs'        => (int)$this->input->post('no_of_payrolled_benefs', TRUE),
            'no_of_backouts_benefs'         => (int)$this->input->post('no_of_backouts_benefs', TRUE),
            'total_no_benefs'               => (int)$this->input->post('total_no_benefs', TRUE),
            'no_of_females'                 => (int)$this->input->post('no_of_females', TRUE),
            'no_of_days'                    => (int)$this->input->post('no_of_days', TRUE),
            'wage_per_day'                  => $this->input->post('wage_per_day', TRUE),
            'total_wages'                   => $this->input->post('total_wages', TRUE),
            'payout_site'                   => $this->input->post('payout_site', TRUE),
            'payout_amount'                 => $this->input->post('payout_amount', TRUE),
            'elcac_ip_count'                => (int)$this->input->post('elcac_ip_count', TRUE),
            'convergence_project'           => $this->input->post('convergence_project', TRUE),
            'fund_source_mds'               => $this->input->post('fund_source_mds', TRUE),
            'fund_source_ors'               => $this->input->post('fund_source_ors', TRUE),
            'date_processed_tssd2'          => $this->input->post('date_processed_tssd2', TRUE),
            'date_encoded'                  => $uploadedDate,
            'encoded_by'                    => $uploadedBy,
        ];
    }
}