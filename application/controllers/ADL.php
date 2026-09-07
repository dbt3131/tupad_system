<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ADL extends CI_Controller { 

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('ADL_Model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['session']);
    }
        
    // Load encoding view with provinces data
    public function ADL_encode() {
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $this->load->view('tupad/ADL_monitoring', $data);
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
                'encoded_by'             => $this->session->userdata('username') ?? 'System User', // Adjust based on your session implementation
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
}