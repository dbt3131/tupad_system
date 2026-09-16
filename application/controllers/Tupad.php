<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use Shuchkin\SimpleXLSX;

require_once APPPATH . 'libraries/SimpleXLSX.php';

class Tupad extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tupad_model');
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    private function format_location_code($val)
    {
        if (empty($val)) {
            return '';
        }
        $val = trim((string)$val);
        
        if (is_numeric($val) && strlen($val) === 8) {
            return '0' . $val;
        }
        
        return $val;
    }

    public function tupad_list()
    {
        $data['users'] = $this->User_model->get_all_users();
        $data['user_name'] = $this->session->userdata('reg_fname') ? $this->session->userdata('reg_fname') : 'User';
        $this->load->view('tupad/list', $data);
    }

    public function gsis_letter()
    {
        $data['users'] = $this->User_model->get_all_users();
        $data['user_name'] = $this->session->userdata('reg_fname') ? $this->session->userdata('reg_fname') : 'User';

        // Capture filter dates and inputs from the GET request
        $start_date       = $this->input->get('start_date');
        $end_date         = $this->input->get('end_date');
        $date_effectivity = $this->input->get('date_effectivity');
        $no_of_days       = $this->input->get('no_of_days');

        // Fallbacks if empty
        if (empty($start_date) || empty($end_date)) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        }

        if (empty($date_effectivity)) {
            $date_effectivity = date('Y-m-d', strtotime('+1 day'));
        }

        if (empty($no_of_days)) {
            $no_of_days = 10;
        }

        // Fetch filtered summary data from Tupad_model based on date range
        $data['summary_records'] = $this->Tupad_model->get_gsis_summary_by_date($start_date, $end_date);
        
        // Pass variables back to view to keep form inputs populated
        $data['start_date']       = $start_date;
        $data['end_date']         = $end_date;
        $data['date_effectivity'] = $date_effectivity;
        $data['no_of_days']       = $no_of_days;

        $this->load->view('tupad/gsis_letter_report', $data);
    }








public function upload_tupad_excel()
{
    if (!$this->session->userdata('logged_in')) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        return;
    }

    $uploadedBy = $this->session->userdata('user_id');
    $uploadedDate = date('Y-m-d H:i:s'); 

    // Extract pre-encoded metadata form values
    $area_of_implementation = $this->input->post('area_of_implementation');
    $period_of_coverage     = $this->input->post('period_of_coverage');
    $adl_no                 = $this->input->post('adl_no');
    $reference_no           = $this->input->post('reference_no');
    $nature_of_work         = $this->input->post('nature_of_work');

    $config['upload_path']   = './uploads/';
    $config['allowed_types'] = 'xlsx|xls|csv';
    $config['max_size']      = 10240; 
    $config['encrypt_name']  = TRUE;

    if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0777, true);
    }

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('excel_file')) {
        echo json_encode([
            'status' => 'error',
            'message' => $this->upload->display_errors('', '')
        ]);
        return;
    }

    $fileData = $this->upload->data();
    $filePath = $fileData['full_path'];
    $originalFileName = $fileData['client_name'];

    // Duplicate File Check
    if ($this->Tupad_model->file_exists($originalFileName)) {
        @unlink($filePath);  
        echo json_encode([
            'status' => 'error', 
            'message' => 'Upload stopped: The file "' . $originalFileName . '" has already been imported into the database.'
        ]);
        return;
    }

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $rows = [];

    if ($extension === 'csv') {
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        }
    } else {
        if ($xlsx = SimpleXLSX::parse($filePath)) {
            $rows = $xlsx->rows();
        } else {
            @unlink($filePath);
            echo json_encode([
                'status' => 'error', 
                'message' => 'Excel Parse Error: ' . SimpleXLSX::parseError()
            ]);
            return;
        }
    }

    // ==========================================
    // TEMPLATE HEADER VALIDATION CHECK
    // ==========================================
    $expected_headers = [
        'No', 'tupad_fname', 'tupad_mname', 'tupad_lname', 'tupad_ext', 'gender', 
        'tupad_dob_month', 'tupad_dob_day', 'tupad_dob_year', 'tupad_province', 
        'tupad_municipality', 'tupad_barangay', 'street', 'district', 'IDType', 
        'IDNumber', 'tupad_contact_no', 'bene_type', 'training_Interest', 'skills', 
        'tupad_epayment', 'tupad_account_no', 'tupad_occupation', 'civil_Status', 
        'age', 'average_monthly', 'dependent', 'interested_employment', 'tupad_convergence'
    ];

    if (empty($rows) || count($rows) < 1) {
        @unlink($filePath);
        echo json_encode(['status' => 'error', 'message' => 'The uploaded file is empty.']);
        return;
    }

    $uploaded_headers = array_map('trim', $rows[0]);

    if (count($uploaded_headers) !== count($expected_headers)) {
        @unlink($filePath);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Template Mismatch: Expected ' . count($expected_headers) . ' columns, but found ' . count($uploaded_headers) . ' columns.'
        ]);
        return;
    }

    foreach ($expected_headers as $index => $expected_col) {
        $actual_col = $uploaded_headers[$index] ?? '';
        if (strcasecmp($expected_col, $actual_col) !== 0) {
            @unlink($filePath);
            echo json_encode([
                'status' => 'error', 
                'message' => "Template Mismatch at Column " . ($index + 1) . ": Expected '{$expected_col}', but found '{$actual_col}'."
            ]);
            return;
        }
    }
    // ==========================================

    // 1. Strict cleaner exclusively for names (removes numbers, periods, commas, special characters except hyphens)
    $clean_name = function($val) {
        $val = trim($val ?? '');
        $val = preg_replace('/[^\p{L}\s\-]/u', '', $val);
        $val = preg_replace('/\s+/', ' ', $val);
        return $val;
    };

    // 2. General cleaner for other fields (keeps periods/special characters like in barangays, streets, etc.)
    $clean_general = function($val) {
        $val = trim($val ?? '');
        $val = preg_replace('/\s+/', ' ', $val);
        return $val;
    };

    // Helper function for advanced name validation
    $validate_name_field = function($name, $field_label, $row_num, $is_required = true) {
        $name = trim($name);

        if ($is_required && ($name === '' || mb_strlen($name) < 2)) {
            return "Validation Error (Row {$row_num}): {$field_label} cannot be blank and must be at least 2 characters.";
        }

        if (!$is_required && $name === '') {
            return null; 
        }

        if (preg_match('/[0-9]/', $name)) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' cannot contain numbers.";
        }

        if (strpos($name, '  ') !== false) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' contains double spaces.";
        }

        if (!preg_match('/^[a-zA-ZÑñ\s\-]+$/u', $name)) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' contains invalid special characters.";
        }

        if (str_starts_with($name, '-') || str_ends_with($name, '-')) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' cannot start or end with a hyphen '-'.";
        }

        return null;
    };

    // ==========================================
    // DATA ROW PARSING & DISCREPANCY COLLECTION
    // ==========================================
    $discrepancies = [];
    $firstProvince = null;          
    $originalProvinceLabel = '';    

    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];

        if (empty(array_filter($row))) {
            continue;
        }

        $row_num      = $i + 1;
        // Strict cleaning applied ONLY to name fields
        $fname        = $clean_name($row[1] ?? '');
        $mname        = $clean_name($row[2] ?? '');
        $lname        = $clean_name($row[3] ?? '');
        $gender       = $clean_general($row[5] ?? ''); 
        $dob_month    = $row[6] ?? '';
        $dob_day      = $row[7] ?? '';
        $dob_year     = $row[8] ?? '';
        
        // General cleaning applied to location fields (preserves periods in brgy, etc.)
        $rawProv      = $clean_general($row[9] ?? '');
        $rawCity      = $clean_general($row[10] ?? '');
        $rawBrgy      = $clean_general($row[11] ?? '');

        // Validate First Name (Required)
        $err = $validate_name_field($fname, 'First Name', $row_num, true);
        if ($err) { $discrepancies[] = $err; }

        // Validate Middle Name (Optional)
        $err = $validate_name_field($mname, 'Middle Name', $row_num, false);
        if ($err) { $discrepancies[] = $err; }

        // Validate Last Name (Required)
        $err = $validate_name_field($lname, 'Last Name', $row_num, true);
        if ($err) { $discrepancies[] = $err; }

        // Validate Gender
        if (trim($gender) === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Gender cannot be blank.";
        } else {
            $err = $validate_name_field($gender, 'Gender', $row_num, true);
            if ($err) { $discrepancies[] = $err; }
        }

        // Validate Birth Date Fields (Cannot be blank)
        if (trim($row[6] ?? '') === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Month (tupad_dob_month) cannot be blank.";
        }
        if (trim($row[7] ?? '') === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Day (tupad_dob_day) cannot be blank.";
        }
        if (trim($row[8] ?? '') === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Year (tupad_dob_year) cannot be blank.";
        }

        // =========================================================================
        // STRICT LOCATION VALIDATION
        // =========================================================================
        $prov_blank = ($rawProv === '');
        $mun_blank  = ($rawCity === '');
        $brgy_blank = ($rawBrgy === '');

        if ($prov_blank) {
            $discrepancies[] = "Validation Error (Row {$row_num}): Province (tupad_province) cannot be blank.";
        }
        if ($mun_blank) {
            $discrepancies[] = "Validation Error (Row {$row_num}): Municipality (tupad_municipality) cannot be blank.";
        }
        if ($brgy_blank) {
            $discrepancies[] = "Validation Error (Row {$row_num}): Barangay (tupad_barangay) cannot be blank.";
        }

        $provCodeVal = !empty($rawProv) ? (is_numeric($rawProv) ? $this->format_location_code($rawProv) : $this->Tupad_model->find_province_code_by_desc($rawProv)) : '';

        if (!$prov_blank) {
            if (empty($provCodeVal)) {
                $discrepancies[] = "Validation Error (Row {$row_num}): Unrecognized or misspelled Province entry -> '{$rawProv}' does not exist in the official database.";
            } else {
                $normalizedProv = strtolower($rawProv);
                if ($firstProvince === null) {
                    $firstProvince = $normalizedProv;
                    $originalProvinceLabel = $rawProv;
                } elseif ($normalizedProv !== $firstProvince) {
                    $discrepancies[] = "Validation Error (Row {$row_num}): Mixed provinces detected. File expects province '{$originalProvinceLabel}', but found '{$rawProv}'. All rows must belong to the same province.";
                }
            }
        }

        $cityCodeVal = '';
        if (!$mun_blank) {
            $cityCodeVal = is_numeric($rawCity) ? $this->format_location_code($rawCity) : $this->Tupad_model->find_city_code_by_desc($rawCity, $provCodeVal);

            if (empty($cityCodeVal)) {
                $globalCityCode = is_numeric($rawCity) ? $this->format_location_code($rawCity) : $this->Tupad_model->find_city_code_by_desc($rawCity, null);
                
                if (empty($globalCityCode)) {
                    $discrepancies[] = "Validation Error (Row {$row_num}): Unrecognized or misspelled Municipality entry -> '{$rawCity}' does not exist in the official database.";
                } elseif (!empty($provCodeVal)) {
                    $discrepancies[] = "Validation Error (Row {$row_num}): Unrecognized or misspelled Municipality entry -> '{$rawCity}' does not exist under Province '{$rawProv}'.";
                }
            }
        }

        if (!$brgy_blank) {
            $brgyCodeVal = '';
            if (!empty($cityCodeVal)) {
                $brgyCodeVal = is_numeric($rawBrgy) ? $this->format_location_code($rawBrgy) : $this->Tupad_model->find_barangay_code_by_desc($rawBrgy, $cityCodeVal);
            }

            if (empty($brgyCodeVal)) {
                $targetCityForBrgy = !empty($cityCodeVal) ? $rawCity : (!empty($rawCity) ? $rawCity : 'the specified municipality');
                $discrepancies[] = "Validation Error (Row {$row_num}): Unrecognized or misspelled Barangay entry -> '{$rawBrgy}' does not exist under Municipality '{$targetCityForBrgy}'.";
            } else {
                if (!empty($provCodeVal)) {
                    $prov_prefix = substr($provCodeVal, 0, 4);
                    $city_prov_check = substr($cityCodeVal, 0, 4);

                    if ($prov_prefix !== $city_prov_check) {
                        $discrepancies[] = "Validation Error (Row {$row_num}): Location hierarchy mismatch. Municipality '{$rawCity}' does not belong to Province '{$rawProv}'.";
                    }
                }
            }
        }
    }

    if (!empty($discrepancies)) {
        @unlink($filePath);
        $this->session->set_flashdata('upload_discrepancies', $discrepancies);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Upload failed due to ' . count($discrepancies) . ' data discrepancy/discrepancies found.',
            'reload' => true
        ]);
        return;
    }

    @unlink($filePath); 
    $insertData = [];

    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        if (empty(array_filter($row))) {
            continue;
        }

        $fname = $clean_name($row[1] ?? '');
        $mname = $clean_name($row[2] ?? '');
        $lname = $clean_name($row[3] ?? '');
        $ext   = $clean_name($row[4] ?? '');

        $rawProv = $clean_general($row[9] ?? '');
        $rawCity = $clean_general($row[10] ?? '');
        $rawBrgy = $clean_general($row[11] ?? '');

        $provCode = is_numeric($rawProv) ? $this->format_location_code($rawProv) : $this->Tupad_model->find_province_code_by_desc($rawProv);
        $cityCode = is_numeric($rawCity) ? $this->format_location_code($rawCity) : $this->Tupad_model->find_city_code_by_desc($rawCity, $provCode);
        $brgyCode = is_numeric($rawBrgy) ? $this->format_location_code($rawBrgy) : $this->Tupad_model->find_barangay_code_by_desc($rawBrgy, $cityCode);

        $rawIdType = $clean_general($row[14] ?? '');
        $idType = is_numeric($rawIdType) ? (int)$rawIdType : $this->Tupad_model->find_type_id_by_desc($rawIdType);

        $rawBeneType = $clean_general($row[17] ?? '');
        $beneType = is_numeric($rawBeneType) ? (int)$rawBeneType : $this->Tupad_model->find_bene_type_id_by_desc($rawBeneType);

        $rawConvergence = $clean_general($row[28] ?? '');
        $convergenceId = is_numeric($rawConvergence) ? (int)$rawConvergence : $this->Tupad_model->find_convergence_id_by_desc($rawConvergence);

        $rawEpayment = $clean_general($row[20] ?? '');
        $epaymentId  = is_numeric($rawEpayment) ? (int)$rawEpayment : $this->Tupad_model->find_epayment_id_by_desc($rawEpayment);

        $rawSkills = $clean_general($row[19] ?? '');
        $skillsId  = is_numeric($rawSkills) ? (int)$rawSkills : $this->Tupad_model->find_skills_id_by_desc($rawSkills);

        $insertData[] = [
            'tupad_id_no'                 => $clean_general($row[0] ?? ''),
            'tupad_fname'                 => strtoupper(trim($fname)),
            'tupad_mname'                 => strtoupper(trim($mname)),
            'tupad_lname'                 => strtoupper(trim($lname)),
            'tupad_ext'                   => strtoupper($ext),
            'tupad_gender'                => strtoupper($clean_general($row[5] ?? '')),
            'tupad_dob_month'             => $clean_general($row[6] ?? ''),
            'tupad_dob_day'               => $clean_general($row[7] ?? ''),
            'tupad_dob_year'              => $clean_general($row[8] ?? ''),
            'tupad_province'              => $provCode,
            'tupad_municipality'          => $cityCode,
            'tupad_barangay'              => $brgyCode,
            'tupad_street'                => strtoupper($clean_general($row[12] ?? '')),
            'tupad_district'              => strtoupper($clean_general($row[13] ?? '')),
            'tupad_idtype'                => strtoupper($idType),
            'tupad_idnumber'              => $clean_general($row[15] ?? ''),
            'tupad_contact_no'            => $clean_general($row[16] ?? ''),
            'tupad_type'                  => strtoupper($beneType),
            'tupad_training_Interest'     => strtoupper($clean_general($row[18] ?? '')),
            'tupad_skills'                => $skillsId, 
            'tupad_epayment'              => $epaymentId, 
            'tupad_account_no'            => $clean_general($row[21] ?? ''),
            'tupad_occupation'            => $clean_general($row[22] ?? ''),
            'tupad_civil_status'          => strtoupper($clean_general($row[23] ?? '')),
            'tupad_age'                   => $clean_general($row[24] ?? ''),
            'tupad_average_monthly'       => $clean_general($row[25] ?? ''),
            'tupad_dependent'             => strtoupper($clean_general($row[26] ?? '')),
            'tupad_interested_employment' => $clean_general($row[27] ?? ''),      
            'tupad_convergence'           => $convergenceId,
            'file_name'                   => $originalFileName,
            'user_id'                     => $uploadedBy,
            'uploaded_at'                 => $uploadedDate,
            'area_of_implementation'      => strtoupper($area_of_implementation),
            'period_of_coverage'          => strtoupper($period_of_coverage),
            'adl_no'                      => $adl_no,
            'reference_no'                => $reference_no,
            'nature_of_work'              => strtoupper($nature_of_work)
        ];
    }

    // DATABASE BATCH INSERTION
    if (!empty($insertData)) {
        $inserted = $this->Tupad_model->insert_batch($insertData);
        
        if ($inserted) {
            $this->load->model('Activity_Model'); 
            $user_id = $this->session->userdata('user_id');
            $this->Activity_Model->log_activity($reference_no, $user_id, 1);    

            $this->session->set_flashdata('success', 'Successfully uploaded ' . count($insertData) . ' record(s).');
            echo json_encode(['status' => 'success', 'message' => 'Batch processing completed.']);
            
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save records into database.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'The uploaded file was empty or contained no valid records.']);
    }
}







    public function view_files()
    {
        $data['files'] = $this->Tupad_model->get_uploaded_files();
        $this->load->view('tupad/files_list', $data);
    }

    public function duplicity_check()
    {
        $data['files'] = $this->Tupad_model->get_uploaded_files();
        $this->load->view('tupad/duplicity_checking', $data);
    }

    public function view_file_data()
    {
        $file_name = $this->input->get('file_name');

        if (empty($file_name)) {
            $this->session->set_flashdata('error', 'No file selected.');
            redirect('tupad'); 
            return;
        }

        $decoded_filename = urldecode($file_name);
        $data['file_name'] = $decoded_filename;
        $data['records']   = $this->Tupad_model->get_records_by_filename($decoded_filename);
        $data['provinces'] = $this->Tupad_model->get_provinces();
        
        $this->load->view('tupad/file_details', $data);
    }

    public function view_files_official()
    {
        $data['provinces'] = $this->Tupad_model->get_provinces();
        $data['files']     = $this->Tupad_model->get_uploaded_files();
        $data['records']   = $this->Tupad_model->get_all_records(); 

        $this->load->view('tupad/official_list', $data);
    }

    public function get_records_json()
    {
        $search_data  = $this->input->post('search');
        $search_value = isset($search_data['value']) ? $search_data['value'] : '';

        $limit     = $this->input->post('length');
        $start     = $this->input->post('start');
        $province  = $this->input->post('province');
        $city      = $this->input->post('city');
        $barangay  = $this->input->post('barangay');
        $file_name = $this->input->post('file_name');

        if (empty($province) && empty($city) && empty($barangay)) {
            $output = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => array(),
            );
            echo json_encode($output);
            return;
        }

        $list     = $this->Tupad_model->get_datatables_records($limit, $start, $search_value, $province, $city, $barangay, $file_name);
        $total    = $this->Tupad_model->count_all_records($file_name);
        $filtered = $this->Tupad_model->count_filtered_records($search_value, $province, $city, $barangay, $file_name);

        $output = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($total),
            "recordsFiltered" => intval($filtered),
            "data"            => $list,
        );

        echo json_encode($output);
    }

    public function get_records_by_file_json()
    {
        $draw   = intval($this->input->post('draw'));
        $start  = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        
        $search_data = $this->input->post('search');
        $search      = isset($search_data['value']) ? $search_data['value'] : '';
        $file_name   = $this->input->post('file_name');

        $data            = $this->Tupad_model->get_paged_records_by_file($file_name, $start, $length, $search);
        $totalRecords    = $this->Tupad_model->count_all_records_by_file($file_name);
        $filteredRecords = $this->Tupad_model->count_filtered_records_by_file($file_name, $search);

        $output = array(
            "draw"            => $draw,
            "recordsTotal"    => intval($totalRecords),
            "recordsFiltered" => intval($filteredRecords),
            "data"            => $data
        );

        echo json_encode($output);
    }
    
    public function file_records($file_name = NULL)
    {
        $data['provinces'] = $this->Tupad_model->get_provinces();
        
        if ($file_name) {
            $decoded_filename  = urldecode($file_name);
            $data['file_name'] = $decoded_filename;
        } else {
            $data['file_name'] = '';
        }
        
        $this->load->view('file_records', $data);
    }

    public function get_cities()
    {
        $provCode = $this->input->post('provCode');
        $cities   = $this->Tupad_model->get_cities_by_province($provCode);
        
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($cities));
    }

    public function get_barangays()
    {
        $citymunCode = $this->input->post('citymunCode');
        $barangays   = $this->Tupad_model->get_barangays_by_city($citymunCode);
        
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($barangays));
    }

    public function check_duplicity()
    {
        $match_level = $this->input->post('match_level') ? $this->input->post('match_level') : 'exact';
        $province    = $this->input->post('province');
        $city        = $this->input->post('city');
        $barangay    = $this->input->post('barangay');
        $file_name   = $this->input->post('file_name');

        $data['match_level']       = $match_level;
        $data['selected_province'] = $province;
        $data['selected_city']     = $city;
        $data['selected_barangay'] = $barangay;
        $data['selected_file']     = $file_name;
        
        $data['duplicates'] = $this->Tupad_model->get_multi_level_duplicates($match_level, $province, $city, $barangay, $file_name);
        
        $this->load->view('tupad/duplicity_results_view', $data);
    }

    public function view_duplicate_cluster()
    {
        $match_level = $this->input->get('level');
        $fname       = $this->input->get('fname');
        $mname       = $this->input->get('mname');
        $lname       = $this->input->get('lname');
        $dob_month   = $this->input->get('month');
        $dob_day     = $this->input->get('day');
        $dob_year    = $this->input->get('year');

        $data['cluster_members'] = $this->Tupad_model->get_duplicate_cluster_members(
            $match_level, $fname, $mname, $lname, $dob_month, $dob_day, $dob_year
        );
        
        $data['match_level'] = $match_level;
        $this->load->view('tupad/duplicity_cluster_view', $data);
    }

    public function export_cluster_xlsx()
    {
        $match_level = $this->input->get('level');
        $fname       = $this->input->get('fname');
        $mname       = $this->input->get('mname');
        $lname       = $this->input->get('lname');
        $dob_month   = $this->input->get('month');
        $dob_day     = $this->input->get('day');
        $dob_year    = $this->input->get('year');

        $cluster_members = $this->Tupad_model->get_duplicate_cluster_members(
            $match_level, $fname, $mname, $lname, $dob_month, $dob_day, $dob_year
        );

        $level_labels = [
            'exact'           => 'Exact Match',
            'highly_possible' => 'Highly Possible Match',
            'possible'        => 'Possible Match',
            'probable'        => 'Probable Match'
        ];
        $match_label_text = $level_labels[$match_level] ?? 'Match Group';

        $filename = 'Duplicate_Cluster_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="UTF-8"></head><body>';
        echo '<h3>Duplicity Results: ' . htmlspecialchars($match_label_text) . '</h3>';
        echo '<table border="1">';
        echo '<tr style="background-color: #343a40; color: #ffffff; font-weight: bold;">';
        echo '<th>Match Category</th>';
        echo '<th>TUPAD ID</th>';
        echo '<th>First Name</th>';
        echo '<th>Middle Name</th>';
        echo '<th>Last Name</th>';
        echo '<th>Extension</th>';
        echo '<th>Birthdate (MM/DD/YYYY)</th>';
        echo '<th>Province</th>';
        echo '<th>City/Muni</th>';
        echo '<th>Source File</th>';
        echo '<th>Uploaded At</th>';
        echo '</tr>';

        foreach ($cluster_members as $row) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($match_label_text) . '</td>';
            echo '<td>' . htmlspecialchars($row['tupad_id_no']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tupad_fname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tupad_mname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tupad_lname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tupad_ext']) . '</td>';
            echo '<td>' . htmlspecialchars($row['tupad_dob_month'] . '/' . $row['tupad_dob_day'] . '/' . $row['tupad_dob_year']) . '</td>';
            echo '<td>' . htmlspecialchars($row['province_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['municipality_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['file_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['uploaded_at']) . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</body></html>';
        exit;
    }

    public function view_profile($id)
    {
        $data['record'] = $this->Tupad_model->get_beneficiary_by_id($id);

        if (empty($data['record'])) {
            show_404();
        }

        $this->load->view('tupad/profile_view', $data);
    }

    public function get_files_json()
    {
        $draw   = intval($this->input->post('draw'));
        $start  = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        
        $search_data = $this->input->post('search');
        $search      = isset($search_data['value']) ? $search_data['value'] : '';

        $files = $this->Tupad_model->get_uploaded_files(); 

        $data = [];
        foreach ($files as $f) {
            $uploader         = trim(($f['uploader_fname'] ?? '') . ' ' . ($f['uploader_lname'] ?? ''));
            $uploader_display = !empty($uploader) ? $uploader : 'N/A';
            $date_uploaded    = !empty($f['uploaded_at']) ? date('M d, Y', strtotime($f['uploaded_at'])) : 'N/A';
            
            if (!empty($search)) {
                if (stripos($f['file_name'], $search) === false && stripos($uploader_display, $search) === false) {
                    continue;
                }
            }

            $encoded_filename = urlencode($f['file_name']);

            $status_badge = '
                <div class="d-flex flex-column gap-1">
                    <span class="badge bg-success-subtle text-success fw-semibold">Active: ' . number_format($f['active_records']) . '</span>
                    <span class="badge bg-danger-subtle text-danger fw-semibold">Inactive: ' . number_format($f['inactive_records']) . '</span>
                </div>
            ';

            $is_forwarded = !empty($f['is_forwarded']) && $f['is_forwarded'] == 1;

            if ($is_forwarded) {
                $gsisButton = '
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-secondary disabled" disabled>
                            <i class="bi bi-check-circle-fill me-1"></i> Forwarded
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete-gsis text-white" data-filename="' . htmlspecialchars($f['file_name']) . '" title="Revert GSIS Forward">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>';
            } else {
                $gsisButton = '
                    <button type="button" class="btn btn-sm btn-warning btn-forward-gsis text-dark fw-semibold" data-filename="' . htmlspecialchars($f['file_name']) . '">
                        <i class="bi bi-send-fill me-1"></i> GSIS Letter
                    </button>';
            }

            $actionButtons = '
                <a href="' . site_url('tupad/view_file_data?file_name=' . $encoded_filename) . '" class="btn btn-sm btn-primary me-1">
                    <i class="bi bi-eye me-1"></i> View
                </a>
                <a href="' . site_url('tupad/export_excel?file_name=' . $encoded_filename) . '" class="btn btn-sm btn-success me-1">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> GPAI
                </a>' . $gsisButton;

            $data[] = [
                '<i class="bi bi-file-earmark-excel me-1 text-success"></i>' . htmlspecialchars($f['file_name']),
                htmlspecialchars($f['reference_no'] ?? 'N/A'), 
                $status_badge,                               
                htmlspecialchars($uploader_display),         
                htmlspecialchars($date_uploaded),            
                $actionButtons                               
            ];
        }

        $totalRecords    = count($files);
        $filteredRecords = count($data);

        if ($length != -1) {
            $data = array_slice($data, $start, $length);
        }

        $output = array(
            "draw"            => $draw,
            "recordsTotal"    => intval($totalRecords),
            "recordsFiltered" => intval($filteredRecords),
            "data"            => $data
        );

        echo json_encode($output);
    }

    public function forward_gsis_letter()
    {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
            return;
        }

        $file_name = $this->input->post('file_name');
        if (empty($file_name)) {
            echo json_encode(['status' => 'error', 'message' => 'No file specified.']);
            return;
        }

        $user_name = $this->session->userdata('reg_fname') ? $this->session->userdata('reg_fname') : 'User';
        
        $result = $this->Tupad_model->forward_to_gsis_letter($file_name, $user_name);

        if ($result === 'success') {
            $this->load->model('Activity_Model'); 
            $user_id = $this->session->userdata('user_id');
            $this->Activity_Model->log_activity($file_name, $user_id, 3); 
            echo json_encode([
                'status' => 'success', 
                'message' => 'Details successfully forwarded to GSIS Letter table.'
            ]);
        } elseif ($result === 'exists') {
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Forwarding aborted: Matching details (Reference No., ADL No., and Implementor) already exist in the GSIS Letter table.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Failed to forward details or file contains no records.'
            ]);
        }
    }







public function export_excel()
    {
        $file_name = $this->input->get('file_name');
        $province  = $this->input->get('province');
        $city      = $this->input->get('city');
        $barangay  = $this->input->get('barangay');
        $search    = $this->input->get('search');

        $records = $this->Tupad_model->get_export_data($file_name, $province, $city, $barangay, $search);

        $firstRecord            = !empty($records) ? $records[0] : [];
        $area_of_implementation = $firstRecord['area_of_implementation'] ?? 'N/A';
        $period_of_coverage     = $firstRecord['period_of_coverage'] ?? 'N/A';
        $adl_no                 = $firstRecord['adl_no'] ?? 'N/A';
        $reference_no           = $firstRecord['reference_no'] ?? 'N/A';
        $nature_of_work         = $firstRecord['nature_of_work'] ?? 'N/A';

        $maleCount   = 0;
        $femaleCount = 0;
        $brgySet     = [];

        foreach ($records as $row) {
            $gender = strtoupper(trim($row['tupad_gender'] ?? ''));
            if ($gender === 'M' || $gender === 'MALE') {
                $maleCount++;
            } elseif ($gender === 'F' || $gender === 'FEMALE') {
                $femaleCount++;
            }
            if (!empty($row['barangay_name'])) {
                $brgySet[$row['barangay_name']] = true;
            }
        }

        $totalBeneficiaries = count($records);
        $totalBarangays     = count($brgySet);
        $filename = 'TUPAD_GSIS_Export_' . date('Ymd_His') . '.xlsx';

        // Initialize PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setShowGridlines(true);

        // Define Styles
        $centerStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ];
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // 1. Main Title (Row 1)
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'ENROLLMENT FORM TO GROUP PERSONAL ACCIDENT INSURANCE OF THE GOVERNMENT SERVICE INSURANCE SYSTEM (GSIS)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A1')->applyFromArray($centerStyle);

        // 2. DOLE Header Metadata (Rows 3 to 5)
        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'Republic of the Philippines');
        $sheet->getStyle('A3')->applyFromArray($centerStyle);

        $sheet->mergeCells('A4:J4');
        $sheet->setCellValue('A4', 'Department of Labor and Employment');
        $sheet->getStyle('A4')->applyFromArray($centerStyle);

        $sheet->mergeCells('A5:J5');
        $sheet->setCellValue('A5', 'Employment Programs of DOLE (TUPAD)');
        $sheet->getStyle('A5')->applyFromArray($centerStyle);
        $sheet->getStyle('A5')->getFont()->setBold(true);

        
        // 3. Program Information metadata rows (Rows 7 to 12)
        // Row 7: Program Name (Static label normal, variable bold)
        $prog = "Tulong Panghanapbuhay sa Ating Disadvantaged Workers (TUPAD)";
        $sheet->mergeCells('A7:E7');
        $sheet->setCellValue('A7', "DOLE's Program: " . $prog);
        // Style only the variable part bold using rich text or separate columns if preferred, but since it's a string, we can target specific parts or set the whole variable nicely. 
        // To make just the variable bold, we can split text or use RichText:
        $richText7 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText7->createText("DOLE's Program:");
        $run7 = $richText7->createTextRun($prog);
        $run7->getFont()->setBold(true);
        $sheet->setCellValue('A7', $richText7);

      

        // Row 8: Area of Implementation & Number of Barangay
        $sheet->mergeCells('A8:H8');
        $sheet->setCellValue('A8', 'Area of Implementation, Province: ' . $area_of_implementation);
        // Style only the variable part bold using rich text or separate columns if preferred, but since it's a string, we can target specific parts or set the whole variable nicely. 
        // To make just the variable bold, we can split text or use RichText:
        $richText8 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText8->createText('Area of Implementation, Province: ');
        $run8 = $richText8->createTextRun($area_of_implementation);
        $run8->getFont()->setBold(true);
        $sheet->setCellValue('A8', $richText8);

        $sheet->mergeCells('I8:J8');
        $richTextBarangay = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richTextBarangay->createText('Number of Barangay : ');
        $runBarangay = $richTextBarangay->createTextRun($totalBarangays);
        $runBarangay->getFont()->setBold(true);
        $sheet->setCellValue('I8', $richTextBarangay);

        // Row 9: Period of Coverage & Gender Totals
        $sheet->mergeCells('A9:H9');
        $richText9 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText9->createText('Period of Coverage: ');
        $run9 = $richText9->createTextRun($period_of_coverage);
        $run9->getFont()->setBold(true);
        $sheet->setCellValue('A9', $richText9);

        $sheet->mergeCells('I9:J9');
        $richTextGender = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richTextGender->createText('M- ');
        $runM = $richTextGender->createTextRun($maleCount);
        $runM->getFont()->setBold(true);
        $richTextGender->createText(' F- ');
        $runF = $richTextGender->createTextRun($femaleCount);
        $runF->getFont()->setBold(true);
        $richTextGender->createText(' = T-');
        $runT = $richTextGender->createTextRun($totalBeneficiaries);
        $runT->getFont()->setBold(true);
        $sheet->setCellValue('I9', $richTextGender);

        // Row 10: ADL No.
        $sheet->mergeCells('A10:J10');
        $richText10 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText10->createText('ADL No. ');
        $run10 = $richText10->createTextRun($adl_no);
        $run10->getFont()->setBold(true);
        $sheet->setCellValue('A10', $richText10);

        // Row 11: Reference No.
        $sheet->mergeCells('A11:J11');
        $richText11 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText11->createText('Reference No. ');
        $run11 = $richText11->createTextRun($reference_no);
        $run11->getFont()->setBold(true);
        $sheet->setCellValue('A11', $richText11);

   $sheet->mergeCells('A12:J12');
        $richText12 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
        $richText12->createText('Specific Nature of work : ');
        $run12 = $richText12->createTextRun($nature_of_work);
        $run12->getFont()->setBold(true);
        $sheet->setCellValue('A12', $richText12);
        
        $sheet->getStyle('A12')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(12)->setRowHeight(35);

        // 4. Table Headers (Rows 14 & 15 matching screenshot two-tier structure)
        $sheet->mergeCells('A14:A15');
        $sheet->setCellValue('A14', 'No.');
        
        $sheet->mergeCells('B14:B15');
        $sheet->setCellValue('B14', 'Name of Beneficiary (Last Name, First Name Middle Name Extension Name)');
        
        $sheet->mergeCells('C14:C15');
        $sheet->setCellValue('C14', 'Sex');
        
        $sheet->mergeCells('D14:D15');
        $sheet->setCellValue('D14', 'Birthdate (MM/DD/YYYY)');
        
        $sheet->mergeCells('E14:E15');
        $sheet->setCellValue('E14', 'Age');
        
        $sheet->mergeCells('F14:I14');
        $sheet->setCellValue('F14', 'Address');
        
        $sheet->mergeCells('J14:J15');
        $sheet->setCellValue('J14', 'Beneficiary');

        // Address Subheaders
        $sheet->setCellValue('F15', 'Street');
        $sheet->setCellValue('G15', 'Barangay');
        $sheet->setCellValue('H15', 'City/ Municipality');
        $sheet->setCellValue('I15', 'Province');

        $headerRange = 'A14:J15';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->applyFromArray($centerStyle);
        $sheet->getStyle($headerRange)->applyFromArray($thinBorder);
        
        $sheet->getRowDimension(14)->setRowHeight(25);
        $sheet->getRowDimension(15)->setRowHeight(20);

        // 5. Populate Data Rows
        $rowNum = 16;
        $no = 1;
        foreach ($records as $row) {
            $fullName = trim($row['tupad_lname'] . ', ' . $row['tupad_fname'] . ' ' . $row['tupad_mname'] . ' ' . $row['tupad_ext']);
            
            $dob = '';
            $age = ''; 
            if (!empty($row['tupad_dob_month']) && !empty($row['tupad_dob_day']) && !empty($row['tupad_dob_year'])) {
                $dob = sprintf('%02d/%02d/%04d', $row['tupad_dob_month'], $row['tupad_dob_day'], $row['tupad_dob_year']);
                $birthDate = DateTime::createFromFormat('m/d/Y', $dob);
                if ($birthDate) {
                    $today = new DateTime('today');
                    $age = $today->diff($birthDate)->y;
                }
            } else {
                $age = $row['tupad_age'] ?? '';
            }
            
            $gender = strtoupper(trim($row['tupad_gender'] ?? ''));
            $display_gender = '';
            if ($gender === 'M' || $gender === 'MALE') {
                $display_gender = 'M';
            } elseif ($gender === 'F' || $gender === 'FEMALE') {
                $display_gender = 'F';
            }

            $sheet->setCellValue("A{$rowNum}", $no++);
            $sheet->setCellValue("B{$rowNum}", $fullName);
            $sheet->setCellValue("C{$rowNum}", $display_gender);
            $sheet->setCellValue("D{$rowNum}", $dob);
            $sheet->setCellValue("E{$rowNum}", $age);
            $sheet->setCellValue("F{$rowNum}", $row['tupad_street'] ?? '');
            $sheet->setCellValue("G{$rowNum}", $row['barangay_name'] ?? '');
            $sheet->setCellValue("H{$rowNum}", $row['municipality_name'] ?? '');
            $sheet->setCellValue("I{$rowNum}", $row['province_name'] ?? '');
            $sheet->setCellValue("J{$rowNum}", $row['tupad_dependent'] ?? '');

            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray($thinBorder);
            $sheet->getStyle("A{$rowNum}")->applyFromArray($centerStyle);
            $sheet->getStyle("C{$rowNum}")->applyFromArray($centerStyle);
            $sheet->getStyle("D{$rowNum}")->applyFromArray($centerStyle);
            $sheet->getStyle("E{$rowNum}")->applyFromArray($centerStyle);

            $rowNum++;
        }

        // 6. Fetch User & Position Information Robustly
        $user_id = $this->session->userdata('user_id');
        $regfname = ''; $regmname = ''; $reglname = '';
        $position_desc = 'Administrative Assistant II'; 

        if (!empty($user_id)) {
            $this->db->select('users.*, code_position.position_description');
            $this->db->from('users');
            $this->db->join('code_position', 'code_position.position_id = users.position_id', 'left');
            $this->db->where('users.id', $user_id);
            $user_row = $this->db->get()->row_array();

            if ($user_row) {
                $regfname = $user_row['reg_fname'] ?? $user_row['fname'] ?? $user_row['name'] ?? '';
                $regmname = $user_row['reg_mname'] ?? $user_row['mname'] ?? '';
                $reglname = $user_row['reg_lname'] ?? $user_row['lname'] ?? '';
                
                if (!empty($user_row['position_description'])) {
                    $position_desc = $user_row['position_description'];
                }
            }
        }

        if (empty($regfname)) {
            $regfname = $this->session->userdata('reg_fname') ?? $this->session->userdata('fname') ?? $this->session->userdata('name') ?? '';
        }
        if (empty($reglname)) {
            $reglname = $this->session->userdata('reg_lname') ?? $this->session->userdata('lname') ?? '';
        }

        $regmname = trim((string)$regmname);
        $middle_initial = !empty($regmname) ? strtoupper(substr($regmname, 0, 1)) . '.' : '';
        $name_parts = array_filter([trim($regfname), $middle_initial, trim($reglname)]);
        $prepared_by = !empty($name_parts) ? implode(' ', $name_parts) : ($this->session->userdata('username') ?? 'LAYLA M. ZUBIRI');

        // 7. Signatures Section Matching Screenshot Placement
        $rowNum += 2; 
        $sheet->setCellValue("A{$rowNum}", "Prepared by:");
        $sheet->setCellValue("C{$rowNum}", "Approved by:");
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("C{$rowNum}")->getFont()->setBold(true);

        $rowNum += 3; 
        $sheet->setCellValue("A{$rowNum}", strtoupper($prepared_by));
        $sheet->setCellValue("C{$rowNum}", "AURITA L. LAXAMANA");
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("C{$rowNum}")->getFont()->setBold(true);

        $rowNum++;
        $sheet->setCellValue("A{$rowNum}", $position_desc);
        $sheet->setCellValue("C{$rowNum}", "Chief LEO, TSSD II");

        // 8. Precise Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);   
        $sheet->getColumnDimension('B')->setWidth(35);  
        $sheet->getColumnDimension('C')->setWidth(8);   
        $sheet->getColumnDimension('D')->setWidth(18);  
        $sheet->getColumnDimension('E')->setWidth(8);   
        $sheet->getColumnDimension('F')->setWidth(20);  
        $sheet->getColumnDimension('G')->setWidth(20);  
        $sheet->getColumnDimension('H')->setWidth(20);  
        $sheet->getColumnDimension('I')->setWidth(20);  
        $sheet->getColumnDimension('J')->setWidth(34);  

        // 9. Stream output as a valid .xlsx file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        $this->load->model('Activity_Model'); 
        $this->Activity_Model->log_activity($reference_no, $user_id, 2);  

        exit;
    }
















































    public function set_record_inactive($id = NULL) {
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
            return;
        }

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid record ID.']);
            return;
        }

        $updated = $this->Tupad_model->set_inactive($id);

        if ($updated) {
            echo json_encode(['status' => 'success', 'message' => 'Record has been set to inactive.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update record status.']);
        }
    }

    public function export_gsis_letter_excel()
    {
        $start_date       = $this->input->get('start_date');
        $end_date         = $this->input->get('end_date');
        $date_effectivity = $this->input->get('date_effectivity');
        $no_of_days       = $this->input->get('no_of_days');

        if (empty($start_date) || empty($end_date)) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        }

        if (empty($date_effectivity)) {
            $date_effectivity = date('Y-m-d', strtotime('+1 day'));
        }

        if (empty($no_of_days)) {
            $no_of_days = 10;
        }

        $summary_records = $this->Tupad_model->get_gsis_summary_by_date($start_date, $end_date);

        $filename = 'GSIS_Letter_Report_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="UTF-8"><style>body { font-family: Arial, sans-serif; font-size: 10pt; }</style></head><body>';
        
        echo '<table width="750" border="0" style="margin: 0 auto; font-family: Arial, sans-serif; font-size: 10pt;">';
        
        $current_formatted_date = strtoupper(date('F d, Y'));
        echo '<tr><td align="left" style="text-align: left; font-weight: bold; mso-number-format:\'\@\'; padding-top: 10px; padding-bottom: 15px;">';
        echo $current_formatted_date;
        echo '</td></tr>';

        echo '<tr><td>';
        echo '<br>';
        echo '<b>Ms. KRISTINE JOI G. MACAM</b><br>';
        echo 'Branch Manager<br>';
        echo '<b>Government Service Insurance System (GSIS)</b><br>';
        echo 'Sindalan, City of San Fernando, Pampanga<br><br>';

        echo 'Dear Ms. Macam:<br><br>';

        $formatted_effectivity = date('F d, Y', strtotime($date_effectivity));
        echo 'May we request the attached list of our beneficiaries under Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers (TUPAD) Program be enrolled under GSIS group insurance effective <b>' . $formatted_effectivity . '</b> with a covered period of work of <b>' . htmlspecialchars($no_of_days) . '</b> days. Below is the summary of our remittance:<br><br>';

        echo '<table border="1" cellspacing="0" cellpadding="4" style="border-collapse: collapse; width: 100%;">';
        echo '<colgroup>';
        echo '<col style="width: 35px;">';
        echo '<col style="width: 320px;">';
        echo '<col style="width: 45px;">';
        echo '<col style="width: 45px;">';
        echo '<col style="width: 45px;">';
        echo '<col style="width: 70px;">';
        echo '<col style="width: 90px;">';
        echo '</colgroup>';

        echo '<tr style="background-color: #f8f9fa; font-weight: bold; text-align: center;">';
        echo '<th rowspan="2" style="vertical-align: middle;">#</th>';
        echo '<th rowspan="2" style="vertical-align: middle;">PARTICULAR</th>';
        echo '<th colspan="3">NO. OF BENEFICIARIES</th>';
        echo '<th rowspan="2" style="vertical-align: middle;">RATE</th>';
        echo '<th rowspan="2" style="vertical-align: middle;">AMOUNT</th>';
        echo '</tr>';
        echo '<tr style="background-color: #f8f9fa; font-weight: bold; text-align: center;">';
        echo '<th>MALE</th>';
        echo '<th>FEMALE</th>';
        echo '<th>TOTAL</th>';
        echo '</tr>';

        $total_male = 0;
        $total_female = 0;
        $total_benefs = 0;
        $total_amount = 0;
        $rate = 50.00; 
        $dst = 0;
        
        if (!empty($summary_records)) {
            $i = 1;
            foreach ($summary_records as $row) {
                $m = $row['male'] ?? 0;
                $f = $row['female'] ?? 0;
                $sub_total = $m + $f;
                $amount = $sub_total * $rate;

                $total_male += $m;
                $total_female += $f;
                $total_benefs += $sub_total;
                $total_amount += $amount;
                
                if($total_benefs == '1'){
                    $dst = 0;
                } elseif ($total_benefs >= 2 && $total_benefs <= 4) {
                    $dst = 20.00;
                } elseif ($total_benefs >= 5 && $total_benefs <= 7) {
                    $dst = 50.00;
                } elseif ($total_benefs >= 8 && $total_benefs <= 11) {
                    $dst = 100.00;
                } elseif ($total_benefs >= 12 && $total_benefs <= 15) {
                    $dst = 150.00;
                } elseif ($total_benefs >= 16) {
                    $dst = 200.00;
                } else {
                    $dst = 0;
                }

                echo '<tr>';
                echo '<td style="text-align: center;">' . $i++ . '</td>';
                echo '<td style="word-break: break-word;">' . htmlspecialchars(($row['implementor'] ?? '') . ' (' . ($row['reference_no'] ?? '') . ')') . '</td>';
                echo '<td style="text-align: center;">' . number_format($m) . '</td>';
                echo '<td style="text-align: center;">' . number_format($f) . '</td>';
                echo '<td style="text-align: center; font-weight: bold;">' . number_format($sub_total) . '</td>';
                echo '<td style="text-align: right;">' . number_format($rate, 2) . '</td>';
                echo '<td style="text-align: right;">' . number_format($amount, 2) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7" style="text-align: center; padding: 10px;">No records found for the selected date range.</td></tr>';
        }

        echo '<tr style="font-weight: bold; background-color: #f8f9fa;">';
        echo '<td colspan="2" style="text-align: right;">TOTAL:</td>';
        echo '<td style="text-align: center;">' . number_format($total_male) . '</td>';
        echo '<td style="text-align: center;">' . number_format($total_female) . '</td>';
        echo '<td style="text-align: center;">' . number_format($total_benefs) . '</td>';
        echo '<td></td>';
        echo '<td style="text-align: right;">' . number_format($total_amount, 2) . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="6" style="text-align: right; font-weight: bold;">DST</td>';
        echo '<td style="text-align: right; font-weight: bold;">' . number_format($dst, 2) . '</td>';
        echo '</tr>';

        $grand_total = $total_amount + ($total_amount > 0 ? $dst : 0);
        echo '<tr style="font-weight: bold; background-color: #e2e8f0;">';
        echo '<td colspan="6" style="text-align: right; text-transform: uppercase;">GRAND TOTAL</td>';
        echo '<td style="text-align: right; color: #2563eb;">' . number_format($grand_total, 2) . '</td>';
        echo '</tr>';

        echo '</table><br>';

        echo 'Thank you and warm regards.<br><br>';
        echo 'Very truly yours,<br><br><br>';
        echo '<b>AURITA L. LAXAMANA</b><br>';
        echo 'CHIEF LEO, TSSD II<br>';

        echo '</td></tr>';
        echo '</table>';

        echo '</body></html>';
        exit;
    }

    public function delete_gsis_letter() {
        $file_name = $this->input->post('file_name');

        if (!$file_name) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'No file name provided.']));
        }

        $this->Tupad_model->remove_from_gsis_letter($file_name);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Successfully removed.']));
    }
}