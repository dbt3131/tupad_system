<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ADL Controller
 * Manages ADL monitoring, implementation encoding, transaction logs, reporting, 
 * exports, and dynamic data filtering for the TUPAD system.
 */
class ADL extends CI_Controller { 

    /**
     * Controller Constructor
     * Initializes core database connections, models, helpers, and session libraries.
     */
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('ADL_Model');
        $this->load->model('Tupad_Payroll_Model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['session']);
        
           if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

 
        
    /**
     * ADL Encode View
     * Fetches province options and existing ADL records to load the monitoring view.
     */
    public function ADL_encode() {
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $data['adl_records'] = $this->ADL_Model->get_ADL();
         $data['offices'] = $this->ADL_Model->get_offices(); // Fetch code_office records
        $this->load->view('tupad/ADL_monitoring', $data);
    }

    /**
     * Implementation Encode View
     * Gathers configuration parameters (rates, locations, proponents, districts) and loads the encoding form.
     */
    public function Implementation_encode() {
        $data['ADL'] = $this->ADL_Model->get_ADL();
        $data['ppe_rate'] = $this->ADL_Model->get_ppe_rate();
        $data['gsis_rate'] = $this->ADL_Model->get_gsis_rate();
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $data['proponents'] = $this->ADL_Model->get_proponents(); 
        $data['districts'] = $this->ADL_Model->get_districts();     
        $data['payoutSite'] = $this->Tupad_Payroll_Model->get_payout_site();
        $this->load->view('tupad/ADL_Imp_encoding', $data);
    }

    /**
     * Get Municipalities by Province (AJAX)
     * Retrieves municipalities dynamically based on the passed province code.
     */
    public function get_municipalities_by_province() {
        $provCode = $this->input->get('provCode');
        if ($provCode) {
            $municipalities = $this->ADL_Model->get_municipalities_by_province($provCode);
            echo json_encode($municipalities);
        } else {
            echo json_encode([]);
        }
    }

    /**
     * Store ADL Record
     * Validates and inserts a new ADL master entry, records an activity trail log, and sets feedback flash data.
     */
/**
     * Store ADL Record
     * Validates uniqueness on the server-side, typecasts critical numeric inputs, 
     * inserts a new ADL master entry, records an activity trail log, and sets feedback flash data.
     */
    public function store() {
        if ($this->input->method() === 'post') {
            $adl_no = trim($this->input->post('adl_no', true));

            // SERVER-SIDE DUPLICATE CHECK (Prevents pen-test client-side bypasses)
            if ($this->ADL_Model->check_adl_exists($adl_no)) {
                $this->session->set_flashdata('error', 'Security Block: The ADL Number already exists in the database.');
                redirect('adl/ADL_encode');
                return;
            }

            $data = [
                'adl_no'        => $adl_no,
                'adl_sponsor'   => strtoupper($this->input->post('adl_sponsor', true)),
                'adl_date'      => $this->input->post('adl_date', true),
                'date_received' => $this->input->post('date_received', true),
                'target_benefs' => (int) $this->input->post('target_benefs', true), // Explicit typecasting
                'adl_amount'    => (float) $this->input->post('adl_amount', true), // Explicit typecasting
                'encoded_by'    => $this->session->userdata('user_id') ?? 'System User',
                'encoded_date'  => date('Y-m-d H:i:s')
            ];

            $insert = $this->ADL_Model->insert_adl($data);

            if ($insert) {
                $this->load->model('Activity_Model');
                $user_id = $this->session->userdata('user_id');
                $this->Activity_Model->log_activity($adl_no, $user_id, 6); 

                $this->session->set_flashdata('success', 'ADL record successfully saved!');
            } else {
                $this->session->set_flashdata('error', 'Failed to save ADL record. Please try again.');
            }
        }
        redirect('adl/ADL_encode');
    }

    /**
     * Store Implementation Transaction
     * Captures form submission inputs, standardizes string casing, inserts the transaction record, and updates activity logs.
     */
/**
/**
     * Store Implementation Transaction
     * Safely cleans currency/amount fields (stripping commas for proper decimal parsing), 
     * validates uniqueness server-side, and inserts the record.
     */
    public function store_transaction() {
        if ($this->input->method() === 'post') {
            $ref_no = strtoupper(trim($this->input->post('implementation_reference_no', true)));

            // SERVER-SIDE DUPLICATE CHECK (Prevents pen-test bypasses)
            if ($this->ADL_Model->check_transaction_exists($ref_no)) {
                $this->session->set_flashdata('error', 'Security Block: The Implementation Reference Number already exists.');
                redirect('adl/Implementation_encode');
                return;
            }

            // Helper closure to safely clean and parse decimal/currency inputs with commas
            $clean_amount = function($field) {
                $val = $this->input->post($field, true);
                return $val !== null && $val !== '' ? (float) str_replace(',', '', $val) : 0.00;
            };

            $data = [
                'adl_no'                            => $this->input->post('adl_no', true),
                'implementation_reference_no'       => $ref_no,
                'implementation_province'           => $this->input->post('implementation_province', true),
                'implementation_area'               => $this->input->post('implementation_area', true),
                'implementation_brgy'               => $this->input->post('implementation_brgy', true),
                'implementation_district'           => strtoupper($this->input->post('implementation_district', true)),
                'implementation_classification'     => strtoupper($this->input->post('implementation_classification', true)),
                'implementation_proponent'          => strtoupper($this->input->post('imp_proponent', true)),
                'implementation_sponsor'            => strtoupper($this->input->post('imp_sponsor', true)),
                'no_of_days'                        => (int) $this->input->post('no_of_days', true),
                'target'                            => (int) $this->input->post('target', true),
                'reformulated_target'               => (int) $this->input->post('reformulated_target', true),
                'remarks'                           => strtoupper($this->input->post('remarks', true)),
                'wage_percentage'                   => $clean_amount('wage_percentage'),
                'subsidy_cost'                      => $clean_amount('subsidy_cost'),
                'admin_cost'                        => $clean_amount('admin_cost'),
                'gpai_info'                         => strtoupper($this->input->post('gpai_info', true)),
                'wage_info'                         => strtoupper($this->input->post('wage_info', true)),
                'date_coordinated'                  => $this->input->post('status_date', true),
                'appraisal_date_submitted'          => $this->input->post('appraisal_date_submitted', true),
                'appraisal_date_approved'           => $this->input->post('appraisal_date_approved', true),
                'ppes_issuance_ris'                 => strtoupper($this->input->post('ppes_issuance_ris', true)),
                'ppes_date_issued'                  => $this->input->post('ppes_date_issued', true),
                'ppes_count'                        => (int) $this->input->post('ppes_count', true),
                'ppes_female'                       => (int) $this->input->post('ppes_female', true),
                'ppes_amount'                       => $clean_amount('ppes_amount'),
                'orientation_date'                  => $this->input->post('orientation_date', true),
                'orientation_benefs'                => (int) $this->input->post('orientation_benefs', true),
                'orientation_employment_period'     => strtoupper($this->input->post('orientation_employment_period', true)),
                'gsis_enrollment_date'              => $this->input->post('gsis_enrollment_date', true),
                'gsis_enrollment_benefs'            => (int) $this->input->post('gsis_enrollment_benefs', true),
                'gsis_enrollment_female'            => (int) $this->input->post('gsis_enrollment_female', true),
                'gsis_enrollment_amount'            => $clean_amount('gsis_enrollment_amount'),
                'ongoing_implementation_start_date' => $this->input->post('ongoing_implementation_start_date', true),
                'ongoing_implementation_end_date'   => $this->input->post('ongoing_implementation_end_date', true),
                'ongoing_implementation_benefs'     => (int) $this->input->post('ongoing_implementation_benefs', true),
                'completed_employment_period'       => strtoupper($this->input->post('completed_employment_period', true)),
                'completed_employment_benefs'       => (int) $this->input->post('completed_employment_benefs', true),
                'completed_employment_amount'       => $clean_amount('completed_employment_amount'),
                'completed_employment_documentation'=> strtoupper($this->input->post('completed_employment_documentation', true)),
                'payment_alob_no'                   => strtoupper($this->input->post('payment_alob_no', true)),
                'payment_dv_no'                     => strtoupper($this->input->post('payment_dv_no', true)),
                'payment_check_no'                  => strtoupper($this->input->post('payment_check_no', true)),
                'payment_date'                      => $this->input->post('payment_date', true),
                'payment_amount'                    => $clean_amount('payment_amount'),
                'payout_date'                       => $this->input->post('payout_date', true),
                'payout_service_cost'               => $clean_amount('payout_service_cost'),
                'payout_method'                     => $this->input->post('payout_method', true),
                'encoded_date'                      => date('Y-m-d H:i:s'),
                'encoded_by'                        => $this->session->userdata('user_id') ?? 1
            ];

            $insert = $this->ADL_Model->insert_transaction($data);

            if ($insert) {
                $this->load->model('Activity_Model');
                $user_id = $this->session->userdata('user_id');
                $this->Activity_Model->log_activity($ref_no, $user_id, 5);    

                $this->session->set_flashdata('success', 'ADL Transaction record successfully saved!');
            } else {
                $this->session->set_flashdata('error', 'Failed to save transaction record.');
            }
        }
        redirect('adl/Implementation_encode');
    }




public function get_generated_reference_no() {
    $adl_no = $this->input->get('adl_no');
    $province = trim($this->input->get('province'));
    $municipality = trim($this->input->get('municipality'));
    $district = trim($this->input->get('district'));

    if (!$adl_no) {
        echo json_encode(['status' => false, 'ref_no' => '']);
        return;
    }

    // Get count of existing transactions for this ADL No to form the 3-digit sequence (e.g., 001, 002)
    $count = $this->ADL_Model->count_transactions_by_adl($adl_no);
    $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

    // Construct format: ADL_NO-SEQUENCE-PROVINCE-MUNICIPALITY-DISTRICT
    $ref_parts = [$adl_no, $sequence];
    
    if (!empty($province) && $province !== 'Select Province') { 
        $ref_parts[] = strtoupper($province); 
    }
    if (!empty($municipality) && $municipality !== 'Select City/Municipality' && $municipality !== 'Select Province First') { 
        $ref_parts[] = strtoupper($municipality); 
    }
    if (!empty($district) && $district !== '-- Select District --') { 
        $ref_parts[] = strtoupper($district); 
    }

    $ref_no = implode('-', $ref_parts);

    echo json_encode(['status' => true, 'ref_no' => $ref_no]);
}





















    /**
     * ADL Report View
     * Compiles filters and source lists (provinces, proponents, districts) to render the tracking/reporting dashboard.
     */
    public function ADL_report() {
      
        $data['adl_list'] = $this->ADL_Model->get_all_adl_numbers();
        $data['provinces'] = $this->ADL_Model->get_provinces(); 
        $data['proponents'] = $this->ADL_Model->get_proponents(); 
        $data['districts'] = $this->ADL_Model->get_districts();  
        $data['detailed_transactions'] = $this->ADL_Model->get_all_or_filtered_transactions(); 
        $this->load->view('tupad/ADL_reporting', $data);
    }

    /**
     * Get Detailed Transactions (AJAX)
     * Fetches transaction entries dynamically based on province, proponent, or district inputs.
     */
    public function get_detailed_transactions_ajax() {
        $province = $this->input->get('province');
        $proponent = $this->input->get('proponent');
        $district = $this->input->get('district');
        $transactions = $this->ADL_Model->get_all_or_filtered_transactions($province, $proponent, $district);
        echo json_encode(['status' => true, 'data' => $transactions]);
    }

    /**
     * Get Report Breakdown Data (AJAX)
     * Returns structured report metrics filtered by a specific ADL number and auxiliary criteria.
     */
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

    /**
     * Transaction Report List View
     * Loads implementation transaction listings based on applied parameter filters and assigns regional user rights.
     */
    public function transaction_report() {
        
        $province = $this->input->get('implementation_province');
        $area = $this->input->get('implementation_area');
        $proponent = $this->input->get('implementation_proponent');
        $district = $this->input->get('implementation_district');

        $data['provinces'] = $this->ADL_Model->get_provinces();
        $data['proponents'] = $this->ADL_Model->get_proponents(); 
        $data['districts'] = $this->ADL_Model->get_districts();    
        
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

    /**
     * Check Duplicate ADL (AJAX)
     * Verifies if an input ADL number already exists within database records.
     */
    public function check_duplicate_adl() {
        $adl_no = $this->input->get('adl_no');
        if ($adl_no) {
            $exists = $this->ADL_Model->check_adl_exists($adl_no);
            echo json_encode(['exists' => $exists]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    /**
     * Check Duplicate Transaction (AJAX)
     * Verifies if a specific implementation reference number already exists.
     */
    public function check_duplicate_transaction() {
        $ref_no = $this->input->get('implementation_reference_no');
        if ($ref_no) {
            $exists = $this->ADL_Model->check_transaction_exists($ref_no);
            echo json_encode(['exists' => $exists]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    /**
     * Get Transaction Details (AJAX)
     * Fetches details of a specific transaction record using its unique ID identifier.
     */
    public function get_transaction_details() {
        $id = $this->input->get('id');
        if ($id) {
            $transaction = $this->ADL_Model->get_transaction_by_id($id);
            echo json_encode(['status' => true, 'data' => $transaction]);
        } else {
            echo json_encode(['status' => false, 'data' => null]);
        }
    }

    /**
     * Update Transaction Record
     * Modifies an existing implementation record via POST data, applies updates, and redirects back to the filtered report.
     */
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
                'subsidy_cost'                      => strtoupper($this->input->post('subsidy_cost', true)),
                'admin_cost'                        => strtoupper($this->input->post('admin_cost', true)),
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

    /**
     * Get Barangays by Municipality (AJAX)
     * Queries and returns a list of barangays based on a provided city/municipality code.
     */
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

    /**
     * View PDF Details
     * Loads the PDF report summary view for a given transaction ID, throwing a 404 if data is absent.
     */
    public function view_pdf($id) {
        $this->load->model('ADL_model');
        $data['transaction'] = $this->ADL_model->get_transaction_details($id);

        if (empty($data['transaction'])) {
            show_404();
        }

        $this->load->view('tupad/ADL_Imp_details_PDF', $data);
    }

    /**
     * Proponent Encode View
     * Loads province references to display the proponent entry form dashboard.
     */
    public function proponent_encode() {
        $data['provinces'] = $this->ADL_Model->get_provinces();
        $this->load->view('tupad/proponent_encoding', $data);
    }

    /**
     * Store Proponent Record
     * Validates uniqueness, formats names to uppercase, and saves a new proponent profile into the database.
     */
    public function store_proponent() {
        if ($this->input->method() === 'post') {
            $proponent_name = strtoupper(trim($this->input->post('proponent_name', true)));

            if ($this->ADL_Model->check_proponent_exists($proponent_name)) {
                $this->session->set_flashdata('error', 'Proponent name already exists in the database.');
                redirect('adl/proponent_encode');
            }

            $data = [
                'proponent_name' => $proponent_name,
                'encoded_date'   => date('Y-m-d'),
                'encoded_by'     => $this->session->userdata('user_id') ?? 1
            ];

            $insert = $this->db->insert('code_proponent', $data);

            if ($insert) {
                $this->session->set_flashdata('success', 'Proponent record successfully saved!');
            } else {
                $this->session->set_flashdata('error', 'Failed to save proponent record.');
            }
        }
        redirect('adl/proponent_encode');
    }

    /**
     * Check Duplicate Proponent (AJAX)
     * Asynchronously checks whether a proponent name already exists in code references.
     */
    public function check_duplicate_proponent() {
        $proponent_name = $this->input->get('proponent_name');
        if ($proponent_name) {
            $exists = $this->ADL_Model->check_proponent_exists($proponent_name);
            echo json_encode(['exists' => $exists]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    /**
     * Export Transaction Excel
     * Generates and outputs an Excel-compatible spreadsheet file containing full record data for a specific transaction.
     */
    public function export_transaction_excel($id) {
        $this->load->model('ADL_Model');
        $transaction = $this->ADL_Model->get_transaction_details($id);

        if (empty($transaction)) {
            show_404();
        }

        $clean = function($val) {
            return is_null($val) ? '' : strip_tags($val);
        };

        $headers = [
            'ADL Number', 'Reference No', 'Province', 'Area / Municipality', 'Barangay', 
            'District', 'Classification', 'Proponent', 'Sponsor', 'Date Coordinated', 
            'Remarks', 'Wage Percentage', 'GPAI Info', 'Wage Info', 
            'Appraisal Date Submitted', 'Appraisal Date Approved', 'PPES Issuance RIS', 
            'PPES Date Issued', 'PPES Count', 'PPES Amount', 
            'Orientation Date', 'Orientation Beneficiaries', 'Orientation Period', 
            'GSIS Enrollment Date', 'GSIS Beneficiaries', 'GSIS Amount', 
            'Start Date (Ongoing)', 'End Date (Ongoing)', 'Ongoing Beneficiaries', 
            'Completed Period', 'Completed Beneficiaries', 'Completed Amount', 'Completed Documentation', 
            'Payment ALOB No', 'Payment DV No', 'Payment Check No', 'Payment Date', 'Payment Amount', 
            'Payout Date', 'Payout Service Cost', 'Payout Method', 
            'Encoded Date', 'Encoded By'
        ];

        $row_data = [
            $clean($transaction['adl_no'] ?? ''),
            $clean($transaction['implementation_reference_no'] ?? ''),
            $clean($transaction['implementation_province_name'] ?? ''),
            $clean($transaction['implementation_area_name'] ?? ''),
            $clean($transaction['implementation_brgy_name'] ?? ''),
            $clean($transaction['implementation_district'] ?? ''),
            $clean($transaction['implementation_classification'] ?? ''),
            $clean($transaction['proponent_name'] ?? ''),
            $clean($transaction['implementation_sponsor'] ?? ''),
            $clean($transaction['date_coordinated'] ?? ''),
            $clean($transaction['remarks'] ?? ''),
            $clean($transaction['wage_percentage'] ?? ''),
            $clean($transaction['gpai_info'] ?? ''),
            $clean($transaction['wage_info'] ?? ''),
            $clean($transaction['appraisal_date_submitted'] ?? ''),
            $clean($transaction['appraisal_date_approved'] ?? ''),
            $clean($transaction['ppes_issuance_ris'] ?? ''),
            $clean($transaction['ppes_date_issued'] ?? ''),
            $clean($transaction['ppes_count'] ?? ''),
            $clean($transaction['ppes_amount'] ?? ''),
            $clean($transaction['orientation_date'] ?? ''),
            $clean($transaction['orientation_benefs'] ?? ''),
            $clean($transaction['orientation_employment_period'] ?? ''),
            $clean($transaction['gsis_enrollment_date'] ?? ''),
            $clean($transaction['gsis_enrollment_benefs'] ?? ''),
            $clean($transaction['gsis_enrollment_amount'] ?? ''),
            $clean($transaction['ongoing_implementation_start_date'] ?? ''),
            $clean($transaction['ongoing_implementation_end_date'] ?? ''),
            $clean($transaction['ongoing_implementation_benefs'] ?? ''),
            $clean($transaction['completed_employment_period'] ?? ''),
            $clean($transaction['completed_employment_benefs'] ?? ''),
            $clean($transaction['completed_employment_amount'] ?? ''),
            $clean($transaction['completed_employment_documentation'] ?? ''),
            $clean($transaction['payment_alob_no'] ?? ''),
            $clean($transaction['payment_dv_no'] ?? ''),
            $clean($transaction['payment_check_no'] ?? ''),
            $clean($transaction['payment_date'] ?? ''),
            $clean($transaction['payment_amount'] ?? ''),
            $clean($transaction['payout_date'] ?? ''),
            $clean($transaction['payout_service_cost'] ?? ''),
            $clean($transaction['payout_method'] ?? ''),
            $clean($transaction['encoded_date'] ?? ''),
            $clean($transaction['encoder_name'] ?? '')
        ];

        $filename = "ADL_Record_" . (!empty($transaction['implementation_reference_no']) ? $transaction['implementation_reference_no'] : $id) . ".xls";
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        echo '<table border="1">';
        echo '<tr style="background-color: #1e3a8a; color: #ffffff; font-weight: bold;">';
        foreach ($headers as $header) {
            echo '<th>' . html_escape($header) . '</th>';
        }
        echo '</tr>';

        echo '<tr>';
        foreach ($row_data as $val) {
            echo '<td>' . html_escape($val) . '</td>';
        }
        echo '</tr>';
        echo '</table>';
        exit;
    }



// Add method to store the MAF record
public function store_adl_maf() {
    if ($this->input->method() === 'post') {
        $data = [
            'adl_source'  => $this->input->post('adl_no', true), // Linked to ADL record
            'maf_amount'  => $this->input->post('maf_amount', true),
            'maf_program' => $this->input->post('maf_program', true),
            'maf_no'      => $this->input->post('maf_no', true),
            'maf_remarks' => strtoupper($this->input->post('maf_remarks', true)),
            'maf_date'    => date('Y-m-d H:i:s'),
            'maf_by'      => $this->session->userdata('user_id') ?? 1
        ];

        $insert = $this->ADL_Model->insert_adl_maf($data);

        if ($insert) {
            $this->session->set_flashdata('success', 'MAF record successfully saved!');
        } else {
            $this->session->set_flashdata('error', 'Failed to save MAF record. Please try again.');
        }
    }
    redirect('adl/ADL_encode');
}







}