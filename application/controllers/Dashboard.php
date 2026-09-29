<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    /**
     * Controller Constructor
     * Process: Initializes parent properties, loads session libraries and URL helpers, 
     * loads the TUPAD model, and checks authentication state to protect dashboard routes 
     * by redirecting unauthenticated users to the login page.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        
        // Load the model handling TUPAD data
        $this->load->model('Tupad_model');
        $this->load->model('ADL_Model');

        // Protect the dashboard: Redirect to login if user is not logged in
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Please log in to access the dashboard.');
            redirect('auth/login');
        }
    }

    /**
     * Dashboard Index Method
     * Process: Loads the ADL model, retrieves active and inactive worker totals, fetches live ADL 
     * records and provincial database statistics, maps regional coordinates for Central Luzon, 
     * parses worker distributions into JSON-encoded map payloads, and renders the dashboard view.
     */
   

public function index() {
        $data = [];

        // Session user name fallback
        $data['user_name'] = $this->session->userdata('reg_fname') 
            ? $this->session->userdata('reg_fname') 
            : 'User';

        // Fetch general dashboard statistics
        $data['total_active_workers'] = number_format($this->Tupad_model->get_total_active_workers());
        $data['total_inactive_workers'] = number_format($this->Tupad_model->get_total_inactive_workers());
        
        // Fetch raw ADL records from your existing working model
        $adl_raw = $this->ADL_Model->get_ADL();
        $adl_records = [];

        // Loop through each ADL entry and sum up breakdown amounts from adl_transactions
        if (!empty($adl_raw)) {
            foreach ($adl_raw as $row) {
                $adl_no = $row['adl_no'] ?? null;
                
                // Initialize breakdown accumulators
                $breakdown = [
                    'ppes_amount' => 0,
                    'gsis_enrollment_amount' => 0,
                    'completed_employment_amount' => 0,
                    'payout_service_cost' => 0,
                    'maf_amount' => 0
                ];

                if ($adl_no) {
                    // Query all rows sharing this adl_no in adl_transactions
                    $child_rows = $this->db->get_where('adl_transactions', ['adl_no' => $adl_no])->result_array();
                    
                    foreach ($child_rows as $child) {
                        $breakdown['ppes_amount'] += floatval($child['ppes_amount'] ?? 0);
                        $breakdown['gsis_enrollment_amount'] += floatval($child['gsis_enrollment_amount'] ?? 0);
                        $breakdown['completed_employment_amount'] += floatval($child['completed_employment_amount'] ?? 0);
                        $breakdown['payout_service_cost'] += floatval($child['payout_service_cost'] ?? 0);
                    }

                    // Query separate MAF table if applicable
                    $maf_rows = $this->db->get_where('adl_maf', ['adl_source' => $adl_no])->result_array();
                    foreach ($maf_rows as $maf_row) {
                        $breakdown['maf_amount'] += floatval($maf_row['maf_amount'] ?? 0);
                    }
                }

                // Merge raw ADL row with the calculated breakdown totals
                $adl_records[] = array_merge($row, $breakdown);
            }
        }

        $data['adl_records'] = $adl_records;

        // Fetch municipal worker stats or other dashboard payloads if needed
        $data['db_stats'] = $this->Tupad_model->get_municipal_worker_stats();

        // Load your dashboard view file (adjust view path if yours is named differently)
        $this->load->view('tupad/dashboard', $data);
    }


}