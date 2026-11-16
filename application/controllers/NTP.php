<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NTP extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        
        // Security Check: Redirect unauthenticated users to login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        // Load required models and helpers
        $this->load->model('NTP_Model');
        $this->load->helper(['url', 'form']);
    }

    public function ntp_encoding() {
        // Fetch all NTP records to populate table
        $data['ntp_list'] = $this->NTP_Model->get_all_ntp();
        
        // Fetch ADL records for dropdown selection
        $data['adl_list'] = $this->NTP_Model->get_adl_list();

        // Fetch provinces for dropdown
        $data['provinces'] = $this->NTP_Model->get_provinces();

        // Load the view template and pass the data array
        $this->load->view('tupad/ADL_NTP', $data);
    }

    /**
     * AJAX endpoint to fetch municipalities based on province code
     */
    public function get_municipalities() {
        if ($this->input->is_ajax_request()) {
            $provCode = $this->input->post('provCode', TRUE);
            $municipalities = $this->NTP_Model->get_municipalities_by_province($provCode);
            header('Content-Type: application/json');
            echo json_encode($municipalities);
        } else {
            show_404();
        }
    }

    /**
     * AJAX endpoint to check duplicate NTP Reference No.
     */
    public function check_duplicate() {
        if ($this->input->is_ajax_request()) {
            $ntp_ref = strtoupper($this->input->post('ntp_reference_no', TRUE));
            $exists = $this->NTP_Model->check_duplicate_ntp($ntp_ref);
            header('Content-Type: application/json');
            echo json_encode(array('exists' => $exists));
        } else {
            show_404();
        }
    }

    public function save_ntp() {
        $ntp_ref = strtoupper($this->input->post('ntp_reference_no', TRUE));

        // Server-side duplicate check
        if ($this->NTP_Model->check_duplicate_ntp($ntp_ref)) {
            $this->session->set_flashdata('error', 'Error: NTP Reference No. already exists in the database.');
            redirect('NTP/ntp_encoding');
        }

        $data = array(
            'ntp_reference_no'            => $ntp_ref,
            'nature_of_project'           => strtoupper($this->input->post('nature_of_project', TRUE)),
            'NTP_province'                => strtoupper($this->input->post('NTP_province', TRUE)),
            'NTP_municipality'            => strtoupper($this->input->post('NTP_municipality', TRUE)),
            'project_location'            => strtoupper($this->input->post('project_location', TRUE)),
            'no_of_beneficiaries'         => $this->input->post('no_of_beneficiaries', TRUE),
            'period_of_work'              => strtoupper($this->input->post('period_of_work', TRUE)),
            'amount_of_assistance'        => $this->input->post('amount_of_assistance', TRUE),
            'mode_of_implementation'      => strtoupper($this->input->post('mode_of_implementation', TRUE)),
            'implementation_reference_no' => strtoupper($this->input->post('implementation_reference_no', TRUE)),
            'ADL_no'                      => strtoupper($this->input->post('ADL_no', TRUE)),
            'date_received'               => $this->input->post('date_received', TRUE),
            'date_encoded'                => date('Y-m-d'),
            'encoded_by'                  => $this->session->userdata('user_id') ? $this->session->userdata('user_id') : 1,
            'approval_status'             => 'Pending'
        );

        if ($this->NTP_Model->insert_ntp($data)) {
            $this->session->set_flashdata('success', 'NTP record saved successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to save NTP record.');
        }

        redirect('NTP/ntp_encoding');
    }

    public function approve_ntp($ntp_id) {
        $approval_date = $this->input->post('approval_date', TRUE);
        
        // Fallback to today if date wasn't supplied
        if (empty($approval_date)) {
            $approval_date = date('Y-m-d');
        }

        $update_data = array(
            'approval_status' => 'Approved',
            'date_approved'   => $approval_date
        );

        if ($this->NTP_Model->update_approval_status($ntp_id, $update_data)) {
            $this->session->set_flashdata('success', 'NTP entry approved successfully!');
        } else {
            $this->session->set_flashdata('error', 'Failed to approve NTP entry.');
        }

        redirect('NTP/ntp_encoding');
    }
}