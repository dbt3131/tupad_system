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
        
    public function ADL_encode() {
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $data['adl_records'] = $this->ADL_Model->get_ADL();
        $this->load->view('tupad/ADL_monitoring', $data);
    }

    public function Implementation_encode() {
        $data['ADL'] = $this->ADL_Model->get_ADL();
        $data['ppe_rate'] = $this->ADL_Model->get_ppe_rate();
        $data['gsis_rate'] = $this->ADL_Model->get_gsis_rate();
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $data['proponents'] = $this->ADL_Model->get_proponents(); // Added proponent source[cite: 5]
        $data['districts'] = $this->ADL_Model->get_districts();     // Added district source[cite: 4]
        $data['payoutSite'] = $this->Tupad_Payroll_Model->get_payout_site();
        $this->load->view('tupad/ADL_Imp_encoding', $data);
    }

    public function get_municipalities_by_province() {
        $provCode = $this->input->get('provCode');
        if ($provCode) {
            $municipalities = $this->ADL_Model->get_municipalities_by_province($provCode);
            echo json_encode($municipalities);
        } else {
            echo json_encode([]);
        }
    }

    public function store() {
        if ($this->input->method() === 'post') {
            $data = [
                'adl_no'                 => $this->input->post('adl_no', true),
                'adl_date'               => $this->input->post('adl_date', true),
                'date_received'          => $this->input->post('date_received', true),
                'target_benefs'          => $this->input->post('target_benefs', true),
                'adl_amount'             => $this->input->post('adl_amount', true),
                'encoded_by'             => $this->session->userdata('user_id') ?? 'System User',
                'encoded_date'           => date('Y-m-d H:i:s')
            ];

            $insert = $this->ADL_Model->insert_adl($data);

            if ($insert) {
                 $this->load->model('Activity_Model');
                 $reference_no = $this->input->post('adl_no', true);
                 $user_id = $this->session->userdata('user_id');
                 $this->Activity_Model->log_activity($reference_no, $user_id, 6); 

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
                'implementation_reference_no'       => strtoupper($this->input->post('implementation_reference_no', true)),
                'implementation_province'           => $this->input->post('implementation_province', true),
                'implementation_area'               => $this->input->post('implementation_area', true),
                'implementation_brgy'               => $this->input->post('implementation_brgy', true),
                'implementation_district'           => strtoupper($this->input->post('implementation_district', true)),
                'implementation_classification'     => strtoupper($this->input->post('implementation_classification', true)),
                'implementation_proponent'          => strtoupper($this->input->post('imp_proponent', true)),
                'implementation_sponsor'            => strtoupper($this->input->post('imp_sponsor', true)),
                'no_of_days'                        => strtoupper($this->input->post('no_of_days', true)),
                'target'                            => strtoupper($this->input->post('target', true)),
                'reformulated_target'               => strtoupper($this->input->post('reformulated_target', true)),
                'remarks'                           => strtoupper($this->input->post('remarks', true)),
                'wage_percentage'                   => strtoupper($this->input->post('wage_percentage', true)),
                'gpai_info'                         => strtoupper($this->input->post('gpai_info', true)),
                'wage_info'                         => strtoupper($this->input->post('wage_info', true)),
                'date_coordinated'                  => $this->input->post('status_date', true),
                'appraisal_date_submitted'          => $this->input->post('appraisal_date_submitted', true),
                'appraisal_date_approved'           => $this->input->post('appraisal_date_approved', true),
                'ppes_issuance_ris'                 => strtoupper($this->input->post('ppes_issuance_ris', true)),
                'ppes_date_issued'                  => $this->input->post('ppes_date_issued', true),
                'ppes_count'                        => $this->input->post('ppes_count', true),
                'ppes_female'                       => $this->input->post('ppes_female', true),
                'ppes_amount'                       => $this->input->post('ppes_amount', true),
                'orientation_date'                  => $this->input->post('orientation_date', true),
                'orientation_benefs'                => $this->input->post('orientation_benefs', true),
                'orientation_employment_period'     => strtoupper($this->input->post('orientation_employment_period', true)),
                'gsis_enrollment_date'              => $this->input->post('gsis_enrollment_date', true),
                'gsis_enrollment_benefs'            => $this->input->post('gsis_enrollment_benefs', true),
                'gsis_enrollment_female'            => $this->input->post('gsis_enrollment_female', true),
                'gsis_enrollment_amount'            => $this->input->post('gsis_enrollment_amount', true),
                'ongoing_implementation_start_date' => $this->input->post('ongoing_implementation_start_date', true),
                'ongoing_implementation_end_date'   => $this->input->post('ongoing_implementation_end_date', true),
                'ongoing_implementation_benefs'     => $this->input->post('ongoing_implementation_benefs', true),
                'completed_employment_period'       => strtoupper($this->input->post('completed_employment_period', true)),
                'completed_employment_benefs'       => $this->input->post('completed_employment_benefs', true),
                'completed_employment_amount'       => $this->input->post('completed_employment_amount', true),
                'completed_employment_documentation'=> strtoupper($this->input->post('completed_employment_documentation', true)),
                'payment_alob_no'                   => $this->input->post('payment_alob_no', true),
                'payment_dv_no'                     => $this->input->post('payment_dv_no', true),
                'payment_check_no'                  => $this->input->post('payment_check_no', true),
                'payment_date'                      => $this->input->post('payment_date', true),
                'payment_amount'                    => $this->input->post('payment_amount', true),
                'payout_date'                       => $this->input->post('payout_date', true),
                'payout_service_cost'               => $this->input->post('payout_service_cost', true),
                'payout_method'                     => $this->input->post('payout_method', true),
                'encoded_date'                      => date('Y-m-d H:i:s'),
                'encoded_by'                        => $this->session->userdata('user_id') ?? 1
            ];

            $insert = $this->ADL_Model->insert_transaction($data);

            if ($insert) {
                $this->load->model('Activity_Model');
                $reference_no = $this->input->post('implementation_reference_no', true);
                $user_id = $this->session->userdata('user_id');
                $this->Activity_Model->log_activity($reference_no, $user_id, 5);    

                $this->session->set_flashdata('success', 'ADL Transaction record successfully saved!');
            } else {
                $this->session->set_flashdata('error', 'Failed to save transaction record.');
            }
        }
        redirect('adl/Implementation_encode');
    }

    public function ADL_report() {
        $data['adl_list'] = $this->ADL_Model->get_all_adl_numbers();
        $data['provinces'] = $this->ADL_Model->get_provinces(); 
        $data['proponents'] = $this->ADL_Model->get_proponents(); // Added proponent source[cite: 5]
        $data['districts'] = $this->ADL_Model->get_districts();     // Added district source[cite: 4]
        $data['detailed_transactions'] = $this->ADL_Model->get_all_or_filtered_transactions(); 
        $this->load->view('tupad/ADL_reporting', $data);
    }

    public function get_detailed_transactions_ajax() {
        $province = $this->input->get('province');
        $proponent = $this->input->get('proponent');
        $district = $this->input->get('district');
        $transactions = $this->ADL_Model->get_all_or_filtered_transactions($province, $proponent, $district);
        echo json_encode(['status' => true, 'data' => $transactions]);
    }

    public function get_report_data() {
        $adl_no = $this->input->get('adl_no');
        $province = $this->input->get('province'); 
        $proponent = $this->input->get('proponent');
        $district = $this->input->get('district');

        if ($adl_no) {
            $report = $this->ADL_Model->get_adl_report_breakdown($adl_no, $province, $proponent, $district);
            echo json_encode(['status' => true, 'data' => $report]);
        } else {
            echo json_encode(['status' => false, 'data' => null]);
        }
    }

    public function transaction_report() {
        $province = $this->input->get('implementation_province');
        $area = $this->input->get('implementation_area');
        $proponent = $this->input->get('implementation_proponent');
        $district = $this->input->get('implementation_district');

        $data['provinces'] = $this->ADL_Model->get_provinces();
        $data['proponents'] = $this->ADL_Model->get_proponents(); // Added proponent source[cite: 5]
        $data['districts'] = $this->ADL_Model->get_districts();     // Added district source[cite: 4]
        
        $data['transactions'] = $this->ADL_Model->get_filtered_transactions($province, $area, $proponent, $district);
        
        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('users', ['id' => $user_id])->row_array();
        $data['user_assigned_prov'] = $user ? $user['assigned_prov'] : '';

        $data['ADL'] = $this->ADL_Model->get_ADL();
        $data['ppe_rate'] = $this->ADL_Model->get_ppe_rate();
        $data['gsis_rate'] = $this->ADL_Model->get_gsis_rate();
        $data['payoutSite'] = $this->Tupad_Payroll_Model->get_payout_site();

        $data['selected_province'] = $province;
        $data['selected_area'] = $area;
        $data['selected_proponent'] = $proponent;
        $data['selected_district'] = $district;

        $this->load->view('tupad/ADL_Imp_List', $data);
    }

    public function check_duplicate_adl() {
        $adl_no = $this->input->get('adl_no');
        if ($adl_no) {
            $exists = $this->ADL_Model->check_adl_exists($adl_no);
            echo json_encode(['exists' => $exists]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    public function check_duplicate_transaction() {
        $ref_no = $this->input->get('implementation_reference_no');
        if ($ref_no) {
            $exists = $this->ADL_Model->check_transaction_exists($ref_no);
            echo json_encode(['exists' => $exists]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    public function get_transaction_details() {
        $id = $this->input->get('id');
        if ($id) {
            $transaction = $this->ADL_Model->get_transaction_by_id($id);
            echo json_encode(['status' => true, 'data' => $transaction]);
        } else {
            echo json_encode(['status' => false, 'data' => null]);
        }
    }

    public function update_transaction_record() {
        if ($this->input->method() === 'post') {
            $id = $this->input->post('adl_transact_id', true);
            
            $province = $this->input->post('implementation_province', true);
            $area_brgy = $this->input->post('implementation_brgy', true);
            $area = $this->input->post('implementation_area', true);

            $data = [
                'implementation_province'           => $province,
                'implementation_brgy'               => $area_brgy,
                'implementation_area'               => $area,
                'implementation_district'           => strtoupper($this->input->post('implementation_district', true)),
                'implementation_classification'     => strtoupper($this->input->post('implementation_classification', true)),
                'implementation_proponent'          => strtoupper($this->input->post('imp_proponent', true)),
                'implementation_sponsor'            => strtoupper($this->input->post('imp_sponsor', true)),
                'wage_percentage'                   => strtoupper($this->input->post('wage_percentage', true)),
                'gpai_info'                         => strtoupper($this->input->post('gpai_info', true)),
                'wage_info'                         => strtoupper($this->input->post('wage_info', true)),
                'remarks'                           => strtoupper($this->input->post('remarks', true)),
                'no_of_days'                        => strtoupper($this->input->post('no_of_days', true)),
                'target'                            => strtoupper($this->input->post('target', true)),
                'reformulated_target'               => strtoupper($this->input->post('reformulated_target', true)),
                'date_coordinated'                  => $this->input->post('status_date', true),
                'appraisal_date_submitted'          => $this->input->post('appraisal_date_submitted', true),
                'appraisal_date_approved'           => $this->input->post('appraisal_date_approved', true),
                'ppes_issuance_ris'                 => strtoupper($this->input->post('ppes_issuance_ris', true)),
                'ppes_date_issued'                  => $this->input->post('ppes_date_issued', true),
                'ppes_count'                        => $this->input->post('ppes_count', true),
                'ppes_female'                       => $this->input->post('ppes_female', true),
                'ppes_amount'                       => $this->input->post('ppes_amount', true),
                'orientation_date'                  => $this->input->post('orientation_date', true),
                'orientation_benefs'                => $this->input->post('orientation_benefs', true),
                'orientation_employment_period'     => strtoupper($this->input->post('orientation_employment_period', true)),
                'gsis_enrollment_date'              => $this->input->post('gsis_enrollment_date', true),
                'gsis_enrollment_benefs'            => $this->input->post('gsis_enrollment_benefs', true),
                'gsis_enrollment_female'            => $this->input->post('gsis_enrollment_female', true),
                'gsis_enrollment_amount'            => $this->input->post('gsis_enrollment_amount', true),
                'ongoing_implementation_start_date' => $this->input->post('ongoing_implementation_start_date', true),
                'ongoing_implementation_end_date'   => $this->input->post('ongoing_implementation_end_date', true),
                'ongoing_implementation_benefs'     => $this->input->post('ongoing_implementation_benefs', true),
                'completed_employment_period'       => strtoupper($this->input->post('completed_employment_period', true)),
                'completed_employment_benefs'       => $this->input->post('completed_employment_benefs', true),
                'completed_employment_amount'       => $this->input->post('completed_employment_amount', true),
                'completed_employment_documentation'=> strtoupper($this->input->post('completed_employment_documentation', true)),
                'payment_alob_no'                   => strtoupper($this->input->post('payment_alob_no', true)),
                'payment_dv_no'                     => strtoupper($this->input->post('payment_dv_no', true)),
                'payment_check_no'                  => strtoupper($this->input->post('payment_check_no', true)),
                'payment_date'                      => $this->input->post('payment_date', true),
                'payment_amount'                    => $this->input->post('payment_amount', true),
                'payout_date'                       => $this->input->post('payout_date', true),
                'payout_service_cost'               => $this->input->post('payout_service_cost', true),
                'payout_method'                     => $this->input->post('payout_method', true)
            ];

            $update = $this->ADL_Model->update_transaction($id, $data);

            if ($update) {
                $this->session->set_flashdata('success', 'ADL Transaction record successfully updated!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update transaction record.');
            }

            $query_string = http_build_query([
                'implementation_province' => $province,
                'implementation_area'     => $area
            ]);

            redirect('adl/transaction_report?' . $query_string);
        }
    }

    public function get_barangays_by_municipality() {
        $citymunCode = $this->input->get('citymunCode');
        $this->load->model('ADL_Model');

        if ($citymunCode) {
            $barangays = $this->ADL_Model->get_barangays_by_municipality($citymunCode);
            echo json_encode($barangays);
        } else {
            echo json_encode([]);
        }
    }

    public function view_pdf($id) {
        $this->load->model('ADL_model');
        $data['transaction'] = $this->ADL_model->get_transaction_details($id);

        if (empty($data['transaction'])) {
            show_404();
        }

        $this->load->view('tupad/ADL_Imp_details_PDF', $data);
    }
}