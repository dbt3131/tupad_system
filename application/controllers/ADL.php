<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ADL extends CI_Controller { 

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('ADL_Model');
        $this->load->model('Tupad_Payroll_Model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['session']);
    }
        
    // Load encoding view with provinces data
    public function ADL_encode() {
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $this->load->view('tupad/ADL_monitoring', $data);
    }

     public function Implementation_encode() {
        $data['ADL'] = $this->ADL_Model->get_ADL();
        $data['ppe_rate'] = $this->ADL_Model->get_ppe_rate();
        $data['gsis_rate'] = $this->ADL_Model->get_gsis_rate();
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $data['payoutSite'] = $this->Tupad_Payroll_Model->get_payout_site();
        $this->load->view('tupad/ADL_Imp_encoding', $data);
    }

    // AJAX endpoint for fetching municipalities based on selected province code
    public function get_municipalities_by_province() {
        $provCode = $this->input->get('provCode');
        if ($provCode) {
            $municipalities = $this->ADL_Model->get_municipalities_by_province($provCode);
            echo json_encode($municipalities);
        } else {
            echo json_encode([]);
        }
    }

    // Handle form submission and store ADL record
    public function store() {
        if ($this->input->method() === 'post') {
            $data = [
                'adl_no'                 => $this->input->post('adl_no', true),
                'adl_date'               => $this->input->post('adl_date', true),
                'date_received'          => $this->input->post('date_received', true),
                'target_benefs'          => $this->input->post('target_benefs', true),
                'adl_province'           => $this->input->post('adl_province', true),
                'area_of_implementation' => $this->input->post('area_of_implementation', true),
                'adl_amount'             => $this->input->post('adl_amount', true),
                'encoded_by'             => $this->session->userdata('user_id') ?? 'System User', // Adjust based on your session implementation
                'encoded_date'           => date('Y-m-d')
            ];

            $insert = $this->ADL_Model->insert_adl($data);

            if ($insert) {
                $this->session->set_flashdata('success', 'ADL record successfully saved!');
            } else {
                $this->session->set_flashdata('error', 'Failed to save ADL record. Please try again.');
            }
        }
        redirect('adl/ADL_encode');
    }

    public function store_transaction() {
    if ($this->input->method() === 'post') {
        $data = [
            'adl_no'                            => $this->input->post('adl_no', true),
            'implementation_reference_no'       => $this->input->post('implementation_reference_no', true),
            'implementation_province'           => $this->input->post('implementation_province', true),
            'implementation_area'               => $this->input->post('implementation_area', true),
            'implementation_proponent'          => $this->input->post('imp_proponent', true),
            'implementation_sponsor'            => $this->input->post('imp_sponsor', true),
            'date_coordinated'                  => $this->input->post('status_date', true),
            'appraisal_date_submitted'          => $this->input->post('appraisal_date_submitted', true),
            'appraisal_date_approved'           => $this->input->post('appraisal_date_approved', true),
            'ppes_issuance_ris'                 => $this->input->post('ppes_issuance_ris', true),
            'ppes_date_issued'                  => $this->input->post('ppes_date_issued', true),
            'ppes_count'                        => $this->input->post('ppes_count', true),
            'ppes_amount'                       => $this->input->post('ppes_amount', true),
            'orientation_date'                  => $this->input->post('orientation_date', true),
            'orientation_benefs'                => $this->input->post('orientation_benefs', true),
            'orientation_employment_period'     => $this->input->post('orientation_employment_period', true),
            'gsis_enrollment_date'              => $this->input->post('gsis_enrollment_date', true),
            'gsis_enrollment_benefs'            => $this->input->post('gsis_enrollment_benefs', true),
            'gsis_enrollment_amount'            => $this->input->post('gsis_enrollment_amount', true),
            'ongoing_implementation_start_date' => $this->input->post('ongoing_implementation_start_date', true),
            'ongoing_implementation_end_date'   => $this->input->post('ongoing_implementation_end_date', true),
            'ongoing_implementation_benefs'     => $this->input->post('ongoing_implementation_benefs', true),
            'completed_employment_period'       => $this->input->post('completed_employment_period', true),
            'completed_employment_benefs'       => $this->input->post('completed_employment_benefs', true),
            'completed_employment_amount'       => $this->input->post('completed_employment_amount', true),
            'completed_employment_documentation'=> $this->input->post('completed_employment_documentation', true),
            'payment_alob_no'                   => $this->input->post('payment_alob_no', true),
            'payment_dv_no'                     => $this->input->post('payment_dv_no', true),
            'payment_check_no'                  => $this->input->post('payment_check_no', true),
            'payment_date'                      => $this->input->post('payment_date', true),
            'payment_amount'                    => $this->input->post('payment_amount', true),
            'payout_date'                       => $this->input->post('payout_date', true),
            'payout_service_cost'               => $this->input->post('payout_service_cost', true),
            'payout_method'                     => $this->input->post('payout_method', true),
            'encoded_date'                      => date('Y-m-d'),
            'encoded_by'                        => $this->session->userdata('user_id') ?? 1
        ];

        $insert = $this->ADL_Model->insert_transaction($data); // Make sure to add insert_transaction function in ADL_Model

        if ($insert) {
            $this->session->set_flashdata('success', 'ADL Transaction record successfully saved!');
        } else {
            $this->session->set_flashdata('error', 'Failed to save transaction record.');
        }
    }
    redirect('adl/Implementation_encode');
}





































}