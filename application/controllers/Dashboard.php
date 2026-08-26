

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

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

    public function index()
{
    $data['user_name'] = $this->session->userdata('reg_fname') ? $this->session->userdata('reg_fname') : 'User';

    // Get total worker count for summary card
    $total_active = $this->Tupad_model->get_total_active_workers();
    $data['total_active_workers'] = number_format($total_active);

    $total_inactive = $this->Tupad_model->get_total_inactive_workers();
    $data['total_inactive_workers'] = number_format($total_inactive);

    // Fetch live rows from database table tbl_tupad_list
    $db_stats = $this->Tupad_model->get_provincial_worker_stats();

    // Central Luzon geographic mapping reference
    $coords_map = [
        'Aurora' => ['lat' => 15.7518, 'lng' => 121.5640, 'color' => '#0ea5e9'],
        'Bataan' => ['lat' => 14.6760, 'lng' => 120.5421, 'color' => '#6366f1'],
        'Bulacan' => ['lat' => 14.8000, 'lng' => 120.9500, 'color' => '#2563eb'],
        'Nueva Ecija' => ['lat' => 15.4828, 'lng' => 120.9704, 'color' => '#10b981'],
        'Pampanga' => ['lat' => 15.0620, 'lng' => 120.6823, 'color' => '#f59e0b'],
        'Tarlac' => ['lat' => 15.4802, 'lng' => 120.5979, 'color' => '#8b5cf6'],
        'Zambales' => ['lat' => 15.3262, 'lng' => 120.0430, 'color' => '#ec4899']
        
    ];

    $map_data = [];
    foreach($coords_map as $prov_name => $coords) {
        $worker_count = 0;
        
        // Find if database returned a count for this specific province
        foreach($db_stats as $row) {
            if(strcasecmp(trim($row['name']), $prov_name) == 0) {
                $worker_count = (int)$row['workers'];
                break;
            }
        }

        $map_data[] = [
            'name' => $prov_name,
            'lat' => $coords['lat'],
            'lng' => $coords['lng'],
            'workers' => $worker_count, // Actual count from tbl_tupad_list (0 if no rows exist yet)
            'color' => $coords['color']
        ];
    }

    $data['map_json_data'] = json_encode($map_data);
    $this->load->view('tupad/dashboard', $data);
}









}