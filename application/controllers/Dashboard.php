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

        // Session user name fallback[cite: 8]
        $data['user_name'] = $this->session->userdata('reg_fname') 
            ? $this->session->userdata('reg_fname') 
            : 'User';

        // Fetch general dashboard statistics[cite: 8]
        $data['total_active_workers'] = number_format($this->Tupad_model->get_total_active_workers());
        $data['total_inactive_workers'] = number_format($this->Tupad_model->get_total_inactive_workers());
        
        // Fetch raw ADL records from your existing working model[cite: 8]
        $adl_raw = $this->ADL_Model->get_ADL();
        $adl_records = [];

        // Loop through each ADL entry and sum up breakdown amounts from adl_transactions[cite: 8]
        if (!empty($adl_raw)) {
            foreach ($adl_raw as $row) {
                $adl_no = $row['adl_no'] ?? null;
                
                // Initialize breakdown accumulators[cite: 8]
                $breakdown = [
                    'ppes_amount' => 0,
                    'gsis_enrollment_amount' => 0,
                    'completed_employment_amount' => 0,
                    'payout_service_cost' => 0,
                    'maf_amount' => 0
                ];

                if ($adl_no) {
                    // Query all rows sharing this adl_no in adl_transactions[cite: 8]
                    $child_rows = $this->db->get_where('adl_transactions', ['adl_no' => $adl_no])->result_array();
                    
                    foreach ($child_rows as $child) {
                        $breakdown['ppes_amount'] += floatval($child['ppes_amount'] ?? 0);
                        $breakdown['gsis_enrollment_amount'] += floatval($child['gsis_enrollment_amount'] ?? 0);
                        $breakdown['completed_employment_amount'] += floatval($child['completed_employment_amount'] ?? 0);
                        $breakdown['payout_service_cost'] += floatval($child['payout_service_cost'] ?? 0);
                    }

                    // Query separate MAF table if applicable[cite: 8]
                    $maf_rows = $this->db->get_where('adl_maf', ['adl_source' => $adl_no])->result_array();
                    foreach ($maf_rows as $maf_row) {
                        $breakdown['maf_amount'] += floatval($maf_row['maf_amount'] ?? 0);
                    }
                }

                // Merge raw ADL row with the calculated breakdown totals[cite: 8]
                $adl_records[] = array_merge($row, $breakdown);
            }
        }

        $data['adl_records'] = $adl_records;

        // Fetch municipal worker stats[cite: 8]
        $map_stats = $this->Tupad_model->get_municipal_worker_stats();

        // Region 3 Coordinate Lookup Dictionary (Add more municipalities as needed)
    $coordinates = [
    // BATAAN
    'ABUCAY' => ['lat' => 14.7265, 'lng' => 120.5376],
    'BAGAC' => ['lat' => 14.6133, 'lng' => 120.4078],
    'CITY OF BALANGA' => ['lat' => 14.6760, 'lng' => 120.5405],
    'DINALUPIHAN' => ['lat' => 14.8722, 'lng' => 120.4578],
    'HERMOSA' => ['lat' => 14.8361, 'lng' => 120.5056],
    'LIMAY' => ['lat' => 14.5572, 'lng' => 120.5847],
    'MARIVELES' => ['lat' => 14.4333, 'lng' => 120.4833],
    'MORONG' => ['lat' => 14.6728, 'lng' => 120.2589],
    'ORANI' => ['lat' => 14.8031, 'lng' => 120.5433],
    'ORION' => ['lat' => 14.6194, 'lng' => 120.5739],
    'PILAR' => ['lat' => 14.6742, 'lng' => 120.5744],
    'SAMAL' => ['lat' => 14.7678, 'lng' => 120.5489],

    // BULACAN
    'ANGAT' => ['lat' => 14.9392, 'lng' => 121.1169],
    'BALAGTAS (BIGAA)' => ['lat' => 14.8306, 'lng' => 120.8756],
    'BALIUAG' => ['lat' => 14.9575, 'lng' => 120.8953],
    'BOCAUE' => ['lat' => 14.7969, 'lng' => 120.9333],
    'BULACAN' => ['lat' => 14.7933, 'lng' => 120.8756],
    'BUSTOS' => ['lat' => 14.9497, 'lng' => 120.9564],
    'CALUMPIT' => ['lat' => 14.9122, 'lng' => 120.7631],
    'GUIGUINTO' => ['lat' => 14.8436, 'lng' => 120.8542],
    'HAGONOY' => ['lat' => 14.8400, 'lng' => 120.7397],
    'CITY OF MALOLOS' => ['lat' => 14.8430, 'lng' => 120.8114],
    'MARILAO' => ['lat' => 14.7578, 'lng' => 120.9483],
    'CITY OF MEYCAUAYAN' => ['lat' => 14.7392, 'lng' => 120.9631],
    'NORZAGARAY' => ['lat' => 14.9125, 'lng' => 121.0375],
    'OBANDO' => ['lat' => 14.7106, 'lng' => 120.9333],
    'PANDI' => ['lat' => 14.8631, 'lng' => 120.9639],
    'PAOMBONG' => ['lat' => 14.8364, 'lng' => 120.7764],
    'PLARIDEL' => ['lat' => 14.8878, 'lng' => 120.8600],
    'PULILAN' => ['lat' => 14.8986, 'lng' => 120.8539],
    'SAN ILDEFONSO' => ['lat' => 15.0864, 'lng' => 120.9419],
    'CITY OF SAN JOSE DEL MONTE' => ['lat' => 14.8117, 'lng' => 121.0453],
    'SAN MIGUEL' => ['lat' => 15.1436, 'lng' => 120.9753],
    'SAN RAFAEL' => ['lat' => 15.0022, 'lng' => 120.9328],
    'SANTA MARIA' => ['lat' => 14.8178, 'lng' => 120.9619],
    'DOÑA REMEDIOS TRINIDAD' => ['lat' => 15.1833, 'lng' => 121.1000],

    // NUEVA ECIJA
    'ALIAGA' => ['lat' => 15.4917, 'lng' => 120.8528],
    'BONGABON' => ['lat' => 15.6264, 'lng' => 121.1444],
    'CABANATUAN CITY' => ['lat' => 15.4858, 'lng' => 120.9673],
    'CABIAO' => ['lat' => 15.2475, 'lng' => 120.8647],
    'CARRANGLAN' => ['lat' => 15.9861, 'lng' => 120.9708],
    'CUYAPO' => ['lat' => 15.7000, 'lng' => 120.6667],
    'GABALDON (BITULOK & SABANI)' => ['lat' => 15.4539, 'lng' => 121.3411],
    'CITY OF GAPAN' => ['lat' => 15.3131, 'lng' => 120.9572],
    'GENERAL MAMERTO NATIVIDAD' => ['lat' => 15.6025, 'lng' => 121.0350],
    'GENERAL TINIO (PAPAYA)' => ['lat' => 15.3725, 'lng' => 121.0142],
    'GUIMBA' => ['lat' => 15.6569, 'lng' => 120.7711],
    'JAEN' => ['lat' => 15.3039, 'lng' => 120.9069],
    'LAUR' => ['lat' => 15.5681, 'lng' => 121.1917],
    'LICAB' => ['lat' => 15.5392, 'lng' => 120.7308],
    'LLANERA' => ['lat' => 15.6706, 'lng' => 120.9928],
    'LUPAO' => ['lat' => 15.9231, 'lng' => 120.8711],
    'SCIENCE CITY OF MUÑOZ' => ['lat' => 15.7197, 'lng' => 120.9039],
    'NAMPICUAN' => ['lat' => 15.7533, 'lng' => 120.6389],
    'PALAYAN CITY' => ['lat' => 15.5333, 'lng' => 121.0833],
    'PANTABANGAN' => ['lat' => 15.8281, 'lng' => 121.1897],
    'PEÑARANDA' => ['lat' => 15.3458, 'lng' => 120.9900],
    'QUEZON' => ['lat' => 15.5539, 'lng' => 120.7419],
    'RIZAL' => ['lat' => 15.6961, 'lng' => 121.1075],
    'SAN ANTONIO' => ['lat' => 15.3400, 'lng' => 120.8256],
    'SAN ISIDRO' => ['lat' => 15.2078, 'lng' => 120.8808],
    'SAN JOSE CITY' => ['lat' => 15.7894, 'lng' => 120.9717],
    'SAN LEONARDO' => ['lat' => 15.3619, 'lng' => 120.9550],
    'SANTA ROSA' => ['lat' => 15.4244, 'lng' => 120.9381],
    'SANTO DOMINGO' => ['lat' => 15.5900, 'lng' => 120.8111],
    'TALAVERA' => ['lat' => 15.6017, 'lng' => 120.9333],
    'TALUGTUG' => ['lat' => 15.9806, 'lng' => 120.7933],
    'ZARAGOZA' => ['lat' => 15.4411, 'lng' => 120.8122],

    // PAMPANGA
    'ANGELES CITY' => ['lat' => 15.1450, 'lng' => 120.5887],
    'APALIT' => ['lat' => 14.9547, 'lng' => 120.7617],
    'ARAYAT' => ['lat' => 15.1481, 'lng' => 120.7778],
    'BACOLOR' => ['lat' => 15.0039, 'lng' => 120.6483],
    'CANDABA' => ['lat' => 15.0931, 'lng' => 120.8381],
    'FLORIDABLANCA' => ['lat' => 14.9358, 'lng' => 120.5181],
    'GUAGUA' => ['lat' => 14.9658, 'lng' => 120.6372],
    'LUBAO' => ['lat' => 14.9189, 'lng' => 120.5936],
    'MABALACAT CITY' => ['lat' => 15.2283, 'lng' => 120.5739],
    'MACABEBE' => ['lat' => 14.9089, 'lng' => 120.7067],
    'MAGALANG' => ['lat' => 15.2239, 'lng' => 120.6861],
    'MASANTOL' => ['lat' => 14.8872, 'lng' => 120.6822],
    'MEXICO' => ['lat' => 15.0642, 'lng' => 120.7208],
    'MINALIN' => ['lat' => 14.9750, 'lng' => 120.7167],
    'PORAC' => ['lat' => 15.0800, 'lng' => 120.5489],
    'CITY OF SAN FERNANDO' => ['lat' => 15.0284, 'lng' => 120.6893],
    'SAN LUIS' => ['lat' => 15.0569, 'lng' => 120.7892],
    'SAN SIMON' => ['lat' => 14.9817, 'lng' => 120.7911],
    'SANTA ANA' => ['lat' => 15.1114, 'lng' => 120.8031],
    'SANTA RITA' => ['lat' => 14.9961, 'lng' => 120.6094],
    'SANTO TOMAS' => ['lat' => 14.9936, 'lng' => 120.6864],
    'SASMUAN (Sexmoan)' => ['lat' => 14.9328, 'lng' => 120.6358],

    // TARLAC
    'ANAO' => ['lat' => 15.7369, 'lng' => 120.6128],
    'BAMBAN' => ['lat' => 15.2958, 'lng' => 120.5831],
    'CAMILING' => ['lat' => 15.6908, 'lng' => 120.4358],
    'CAPAS' => ['lat' => 15.3333, 'lng' => 120.5900],
    'CONCEPCION' => ['lat' => 15.3264, 'lng' => 120.6547],
    'GERONA' => ['lat' => 15.6100, 'lng' => 120.6017],
    'LA PAZ' => ['lat' => 15.4456, 'lng' => 120.7289],
    'MAYANTOC' => ['lat' => 15.4989, 'lng' => 120.3667],
    'MONCADA' => ['lat' => 15.4333, 'lng' => 120.5667],
    'PANIQUI' => ['lat' => 15.6833, 'lng' => 120.5833],
    'PURA' => ['lat' => 15.6200, 'lng' => 120.6500],
    'RAMOS' => ['lat' => 15.6558, 'lng' => 120.6378],
    'SAN CLEMENTE' => ['lat' => 15.7533, 'lng' => 120.4289],
    'SAN MANUEL' => ['lat' => 15.8117, 'lng' => 120.6231],
    'SANTA IGNACIA' => ['lat' => 15.6033, 'lng' => 120.4500],
    'CITY OF TARLAC' => ['lat' => 15.4802, 'lng' => 120.5979],
    'VICTORIA' => ['lat' => 15.5800, 'lng' => 120.6800],

    // ZAMBALES
    'SAN JOSE' => ['lat' => 15.7833, 'lng' => 119.9833],
    'BOTOLAN' => ['lat' => 15.2894, 'lng' => 120.0256],
    'CABANGAN' => ['lat' => 15.1764, 'lng' => 120.0353],
    'CANDELARIA' => ['lat' => 15.6333, 'lng' => 119.6500],
    'CASTILLEJOS' => ['lat' => 14.9333, 'lng' => 120.2167],
    'IBA' => ['lat' => 15.3283, 'lng' => 119.9811],
    'MASINLOC' => ['lat' => 15.5765, 'lng' => 119.9572],
    'OLONGAPO CITY' => ['lat' => 14.8386, 'lng' => 120.2842],
    'PALAUIG' => ['lat' => 15.4333, 'lng' => 119.9000],
    'SAN ANTONIO (Zambales)' => ['lat' => 14.9511, 'lng' => 120.2606],
    'SAN FELIPE' => ['lat' => 15.0717, 'lng' => 120.0433],
    'SAN MARCELINO' => ['lat' => 14.9750, 'lng' => 120.1667],
    'SAN NARCISO' => ['lat' => 15.0167, 'lng' => 120.0833],
    'SANTA CRUZ' => ['lat' => 15.7583, 'lng' => 119.8944],
    'SUBIC' => ['lat' => 14.8858, 'lng' => 120.2333],

    // AURORA
    'BALER' => ['lat' => 15.7578, 'lng' => 121.5606],
    'CASIGURAN' => ['lat' => 16.2800, 'lng' => 122.1100],
    'DILASAG' => ['lat' => 16.3533, 'lng' => 122.3500],
    'DINALUNGAN' => ['lat' => 15.9450, 'lng' => 121.7217],
    'DINGALAN' => ['lat' => 15.3850, 'lng' => 121.3967],
    'DIPACULAO' => ['lat' => 15.8333, 'lng' => 121.6167],
    'MARIA AURORA' => ['lat' => 15.7828, 'lng' => 121.4686],
    'SAN LUIS (Aurora)' => ['lat' => 15.6833, 'lng' => 121.5167],
];

        // Inject lat and lng properties into each row for the map
        if (!empty($map_stats)) {
            foreach ($map_stats as &$row) {
                $name = strtoupper(trim($row['municipality_name'] ?? ''));
                if (isset($coordinates[$name])) {
                    $row['lat'] = $coordinates[$name]['lat'];
                    $row['lng'] = $coordinates[$name]['lng'];
                } else {
                    // Default fallback coordinates if municipality is not yet listed in the array
                    $row['lat'] = 15.4792; 
                    $row['lng'] = 120.5963;
                }
            }
            unset($row);
        }

        $data['map_json_data'] = $map_stats;

        // Load your dashboard view file[cite: 8]
        $this->load->view('tupad/dashboard', $data);
    }


}