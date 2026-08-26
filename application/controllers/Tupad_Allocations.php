<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Allocations extends CI_Controller {

    public function __construct() {
        parent::__construct();
    $this->load->library('session');
    if (!$this->session->userdata('logged_in')) {
        redirect('auth/login');
    }


        $this->load->model('Tupad_Allocation_Model');
        $this->load->model('Tupad_Model');
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        $data['cities'] = $this->db->get('refcitymun')->result();
        $data['brgys'] = $this->db->get('refbrgy')->result();
        
        $user_prov = $this->session->userdata('assigned_prov');
        $data['allocations'] = $this->Tupad_Allocation_Model->get_allocations($user_prov);
        $data['user_assigned_prov'] = $user_prov;

        $unique_counts = [];
        if (!empty($data['allocations'])) {
            foreach ($data['allocations'] as $row) {
                $uid = trim(strtoupper($row['unique_id']));
                if (!empty($uid) && $uid !== 'TEMP') {
                    $unique_counts[$uid] = isset($unique_counts[$uid]) ? $unique_counts[$uid] + 1 : 1;
                }
            }
        }
        $data['unique_counts'] = $unique_counts;

        $this->load->view('tupad/tupad_encoding_view', $data);
    }

    public function store() {
        $municipality = $this->input->post('lgu_municipality_city');
        $uploadedBy = $this->session->userdata('user_id');
        $uploadedDate = date('Y-m-d H:i:s'); 
        $assigned_prov = $this->session->userdata('assigned_prov');

        $isValidMun = $this->Tupad_Allocation_Model->check_lgu_exists($municipality);

        if (!$isValidMun) {
            $this->session->set_flashdata('error', 'Encoding failed: The specified Municipality/City is invalid or does not exist in the database record.');
            redirect('tupad_allocations');
            return;
        }

        $subsidy = floatval($this->input->post('subsidy'));
        $admin_cost = floatval($this->input->post('admin_cost'));

        $data = [
            'date_coordinated'      => $this->input->post('date_coordinated'),
            'unique_id'             => 'TEMP',
            'batch_no'              => $this->input->post('batch_no'),
            'source_fund_gpai'      => strtoupper($this->input->post('source_fund_gpai')),
            'source_fund_wage'      => strtoupper($this->input->post('source_fund_wage')),
            'adl_no'                => strtoupper($this->input->post('adl_no')),
            'reference_no'          => strtoupper($this->input->post('reference_no')),
            'sponsor'               => strtoupper($this->input->post('sponsor')),
            'proponent_recipient'   => strtoupper($this->input->post('proponent_recipient')),
            'term'                  => strtoupper($this->input->post('term')),
            'percentage'            => strtoupper($this->input->post('percentage')),
            'lgu_classification'    => strtoupper($this->input->post('lgu_classification')),
            'district'              => strtoupper($this->input->post('district')),
            'lgu_municipality_city' => $municipality,
            'barangay'              => strtoupper($this->input->post('barangay')),
            'physical_target'       => strtoupper($this->input->post('physical_target')),
            'per_capita'            => strtoupper($this->input->post('per_capita')),
            'ppe_rate'              => strtoupper($this->input->post('ppe_rate')),
            'no_of_days_work'       => strtoupper($this->input->post('no_of_days_work')),
            'period_start'          => $this->input->post('period_start'),
            'period_end'            => $this->input->post('period_end'),
            'subsidy'               => $subsidy,
            'admin_cost'            => $admin_cost,
            'total_project_funds'   => ($subsidy + $admin_cost),
            'created_at'            => $uploadedDate,
            'encoded_by'            => $uploadedBy,
            'assign_prov'           => $assigned_prov,
        ];

        $this->Tupad_Allocation_Model->insert_entry($data);
        $insertId = $this->db->insert_id();
        $uniqueID = $uploadedBy . '-' . date('Ymd') . '-' . $insertId;
        $this->Tupad_Allocation_Model->update_entry($insertId, ['unique_id' => $uniqueID]);

        $this->session->set_flashdata('success', 'Record successfully encoded.');
        redirect('tupad_allocations');
    }

    public function store_as_new() {
        $uploadedBy = $this->session->userdata('user_id');
        $uploadedDate = date('Y-m-d H:i:s'); 
        $assigned_prov = $this->session->userdata('assigned_prov');
       

        $subsidy = floatval($this->input->post('subsidy'));
        $uniqueID = $this->input->post('unique_id');
         $remarks_parts = "Parts of PRISM ID: ".$uniqueID;
        $admin_cost = floatval($this->input->post('admin_cost'));
        $target = floatval($this->input->post('final_physical'));
        $insurance_amount = $target * 50;

        $data = [
            'date_coordinated'      => $this->input->post('date_coordinated'),
            'unique_id'             => $uniqueID,
            'batch_no'              => $this->input->post('batch_no'),
            'source_fund_gpai'      => strtoupper($this->input->post('source_fund_gpai')),
            'source_fund_wage'      => strtoupper($this->input->post('source_fund_wage')),
            'adl_no'                => strtoupper($this->input->post('adl_no')),
            'reference_no'          => strtoupper($this->input->post('reference_no')),
            'sponsor'               => strtoupper($this->input->post('sponsor')),
            'proponent_recipient'   => strtoupper($this->input->post('proponent_recipient')),
            'term'                  => $this->input->post('term'),
            'lgu_classification'    => strtoupper($this->input->post('lgu_classification')),
            'district'              => strtoupper($this->input->post('district')),
            'lgu_municipality_city' => strtoupper($this->input->post('lgu_municipality_city')),
            'implementation_status' => strtoupper($this->input->post('implementation_status')),
            'barangay'              => strtoupper($this->input->post('barangay')),
            'physical_target'       => strtoupper($this->input->post('physical_target')),
            'per_capita'            => strtoupper($this->input->post('per_capita')),
            'ppe_rate'              => strtoupper($this->input->post('ppe_rate')),
            'no_of_days_work'       => strtoupper($this->input->post('no_of_days_work')),
            'period_start'          => $this->input->post('period_start'),
            'period_end'            => $this->input->post('period_end'),
            'subsidy'               => $subsidy,
            'admin_cost'            => $admin_cost,
            'total_project_funds'   => ($subsidy + $admin_cost),
            'final_physical_target' => $target,
            'number_of_females'     => $this->input->post('no_female'),
            'gpai_date'             => $this->input->post('gpai_date'),
            'final_physical_ppe_requested'    => $this->input->post('final_physical_ppe_requested'),
            'final_physical_ppe_issued' => $this->input->post('final_physical_ppe_issued'),
            'date_issued_ppe'       => $this->input->post('ppe_date'),
            'issued_id'             => $this->input->post('issued_id'),
            'date_id_issued'        => $this->input->post('date_id_issued'),
            'ppe_ris_number'        => strtoupper($this->input->post('ris_number')),
            'remarks'               => $remarks_parts,
            'insurance_amount'      => $insurance_amount,
            'created_at'            => $uploadedDate,
            'encoded_by'            => $uploadedBy,
            'assign_prov'           => $assigned_prov
        ];

        $this->Tupad_Allocation_Model->insert_entry($data);
        $this->session->set_flashdata('success', 'Record successfully duplicated and saved as new entry.');
        redirect('tupad_allocations');
    }

    public function tupad_encode() {
        $data['allocations'] = $this->Tupad_Allocation_Model->get_all();
        $data['files'] = $this->Tupad_Allocation_Model->get_uploaded_files_alloc();
        $data['cities'] = $this->db->get('refcitymun')->result();
        $data['brgys'] = $this->db->get('refbrgy')->result();
        
        $user_prov = $this->session->userdata('assigned_prov');
        $data['user_assigned_prov'] = $user_prov;

        // Added unique_counts generation here so it won't trigger an undefined variable error
        $unique_counts = [];
        if (!empty($data['allocations'])) {
            foreach ($data['allocations'] as $row) {
                $uid = trim(strtoupper($row['unique_id']));
                if (!empty($uid) && $uid !== 'TEMP') {
                    $unique_counts[$uid] = isset($unique_counts[$uid]) ? $unique_counts[$uid] + 1 : 1;
                }
            }
        }
        $data['unique_counts'] = $unique_counts;

        $this->load->view('tupad/tupad_encoding_view', $data);
    }

    public function tupad_monitoring() {
        $data['allocations'] = $this->Tupad_Allocation_Model->get_all();
        
        $user_prov = $this->session->userdata('assigned_prov');
        $data['user_assigned_prov'] = $user_prov;

        $this->load->view('tupad/tupad_monitoring_report', $data);
    }

    public function get_cities_json() {
        $provCode = $this->input->get('provCode');
        if (!$provCode) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

        $conn = new mysqli("localhost", "root", "", "dole_tupad_db");
        if ($conn->connect_error) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }

        $provCode = $conn->real_escape_string($provCode);
        $result = $conn->query("SELECT citymunCode, citymunDesc FROM refcitymun WHERE provCode = '$provCode' ORDER BY citymunDesc ASC");
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        $conn->close();

        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function get_city_autocomplete() {
        $keyword = $this->input->get('term', TRUE);
        
        if (!empty($keyword)) {
            $this->db->like('citymunDesc', $keyword);
            $query = $this->db->get('refcitymun');
            
            $result = [];
            foreach ($query->result() as $row) {
                $classification = isset($row->lgu_class) ? $row->lgu_class : '1st'; 
                
                $result[] = [
                    'label' => $row->citymunDesc . ' (' . $classification . ')',
                    'value' => $row->citymunDesc,
                    'classification' => $classification
                ];
            }
            
            return $this->output->set_content_type('application/json')->set_output(json_encode($result));
        }
    }

    public function get_allocation_json() {
        $id = $this->input->get('id');
        if (!$id) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([]));
        }
        
        $data = $this->Tupad_Allocation_Model->get_entry($id);
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function update($id) {
        $subsidy = floatval($this->input->post('subsidy'));
        $admin_cost = floatval($this->input->post('admin_cost'));
        $target = $this->input->post('final_physical');
        $insurance_amount = $target * 50;
        
        $data = [
            'date_coordinated'      => $this->input->post('date_coordinated'),
            'batch_no'              => $this->input->post('batch_no'),
            'source_fund_gpai'      => strtoupper($this->input->post('source_fund_gpai')),
            'source_fund_wage'      => strtoupper($this->input->post('source_fund_wage')),
            'adl_no'                => strtoupper($this->input->post('adl_no')),
            'reference_no'          => strtoupper($this->input->post('reference_no')),
            'sponsor'               => strtoupper($this->input->post('sponsor')),
            'proponent_recipient'   => strtoupper($this->input->post('proponent_recipient')),
            'lgu_classification'    => strtoupper($this->input->post('lgu_classification')),
            'district'              => strtoupper($this->input->post('district')),
            'lgu_municipality_city' => strtoupper($this->input->post('lgu_municipality_city')),
            'barangay'              => strtoupper($this->input->post('barangay')),
            'ppe_ris_number'        => strtoupper($this->input->post('ris_number')),
            'physical_target'       => $this->input->post('physical_target'),
            'per_capita'            => $this->input->post('per_capita'),
            'ppe_rate'              => $this->input->post('ppe_rate'),
            'no_of_days_work'       => $this->input->post('no_of_days_work'),
            'period_start'          => $this->input->post('period_start'),
            'period_end'            => $this->input->post('period_end'),
            'subsidy'               => $subsidy,
            'admin_cost'            => $admin_cost,
            'total_project_funds'   => ($subsidy + $admin_cost),
            'final_physical_target' => $this->input->post('final_physical'),
            'number_of_females'     => $this->input->post('no_female'),
            'gpai_date'             => $this->input->post('gpai_date'),
            'final_physical_ppe_requested'    => $this->input->post('final_physical_ppe_requested'),
            'final_physical_ppe_issued' => $this->input->post('final_physical_ppe_issued'),
            'date_issued_ppe'       => $this->input->post('ppe_date'),
            'insurance_amount'      => $insurance_amount,
            'issued_id      '       => $this->input->post('issued_id'),
            'date_id_issued      '  => $this->input->post('date_id_issued'),
            'ppe_request'           => $this->input->post('final_physical_ppe_requested'),
        ];

        $this->Tupad_Allocation_Model->update_entry($id, $data);
        redirect('tupad_allocations');
    }


private function _get_common_view_data($assigned_prov = null) {
    $data['cities'] = $this->db->get('refcitymun')->result();
    $data['brgys'] = $this->db->get('refbrgy')->result();
    $data['user_assigned_prov'] = $assigned_prov;
    $data['allocations'] = $this->Tupad_Allocation_Model->get_allocations($assigned_prov);

    // Compute unique counts safely
    $unique_counts = [];
    if (!empty($data['allocations'])) {
        foreach ($data['allocations'] as $row) {
            $uid = trim(strtoupper($row['unique_id']));
            if (!empty($uid) && $uid !== 'TEMP') {
                $unique_counts[$uid] = isset($unique_counts[$uid]) ? $unique_counts[$uid] + 1 : 1;
            }
        }
    }
    $data['unique_counts'] = $unique_counts;

    return $data;
}

public function update_monitoring_status() {
    $id = $this->input->post('allocation_id');

    $data = [
        'implementation_status'        => $this->input->post('implementation_status'),
        'absent_days'                => $this->input->post('absent_days') ? strtoupper($this->input->post('absent_days')) : NULL,
        'benefs_paid'                  => $this->input->post('benefs_paid'),
        'benefs_female_paid'           => $this->input->post('benefs_female_paid'),
        'orientation_schedule'         => $this->input->post('orientation_schedule') ? $this->input->post('orientation_schedule') : NULL,
        'ppe_pickup_schedule'          => $this->input->post('ppe_pickup_schedule') ? $this->input->post('ppe_pickup_schedule') : NULL,
        'remarks'                      => strtoupper($this->input->post('remarks')),
        'ppe_request'                  => strtoupper($this->input->post('ppe_request')),
        'mode_of_payout'               => strtoupper($this->input->post('mode_of_payout')),
        'work_program_date_submission' => $this->input->post('work_program_date_submission') ? $this->input->post('work_program_date_submission') : NULL,
    ];

    $this->Tupad_Allocation_Model->update_entry($id, $data);
    $this->session->set_flashdata('success', 'Monitoring details updated successfully.');
    redirect('tupad_allocations');
}


























}