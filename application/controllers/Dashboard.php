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
   

public function index()
{
    $this->load->model('ADL_Model');

    $data['user_name'] = $this->session->userdata('reg_fname') ? $this->session->userdata('reg_fname') : 'User';
    $data['total_active_workers'] = number_format($this->Tupad_model->get_total_active_workers());
    $data['total_inactive_workers'] = number_format($this->Tupad_model->get_total_inactive_workers());
    $data['adl_records'] = $this->ADL_Model->get_ADL();

    $db_stats = $this->Tupad_model->get_municipal_worker_stats();

    $map_data = [];
    foreach($db_stats as $row) {
        $workers = (int)($row['workers'] ?? 0);
        if($workers <= 0) continue;

        $mun_raw = trim($row['municipality_name'] ?? '');
        $mun_lower = strtolower($mun_raw);
        $prov_name = trim($row['province_name'] ?? 'Zambales');

        // Explicit coordinate assignment for Masinloc and Olongapo
        if (strpos($mun_lower, 'masinloc') !== false) {
            $lat = 15.528553; // Exact coordinates for Masinloc, Zambales
            $lng = 119.960800;
        } elseif (strpos($mun_lower, 'olongapo') !== false) {
            $lat = 14.8292;
            $lng = 120.2828;
        } else {
            // General fallback center for Central Luzon if other towns appear later
            $lat = 15.3500;
            $lng = 120.7500;
        }

        $map_data[] = [
            'name' => $mun_raw !== '' ? $mun_raw : 'Masinloc',
            'province' => $prov_name,
            'lat' => $lat,
            'lng' => $lng,
            'workers' => $workers,
            'color' => $row['color'] ?? '#2563eb'
        ];
    }

    $data['map_json_data'] = json_encode($map_data);
    $this->load->view('tupad/dashboard', $data);
}


}