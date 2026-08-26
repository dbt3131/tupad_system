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

        // Fetch filtered summary data from Tupad_model based on date range[cite: 9]
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
    // 1. TEMPLATE HEADER VALIDATION CHECK
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

    @unlink($filePath); 

    $insertData = [];
    $clean = function($val) {
        return str_replace('.', '', trim($val ?? ''));
    };

    // Helper function for advanced name validation
    $validate_name_field = function($name, $field_label, $row_num, $is_required = true) {
        $name = trim($name);

        if ($is_required && ($name === '' || mb_strlen($name) < 2)) {
            return "Validation Error (Row {$row_num}): {$field_label} cannot be blank and must be at least 2 characters.";
        }

        if (!$is_required && $name === '') {
            return null; // Optional field and is empty, pass validation
        }

        // Check for numbers
        if (preg_match('/[0-9]/', $name)) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' cannot contain numbers.";
        }

        // Check for double spaces
        if (strpos($name, '  ') !== false) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' contains double spaces.";
        }

        // Check allowed characters (letters, spaces, hyphens)
        if (!preg_match('/^[a-zA-Z\s\-]+$/', $name)) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' contains invalid special characters (only hyphens '-' are allowed).";
        }

        // Check hyphen placement: must not start or end with a hyphen
        if (str_starts_with($name, '-') || str_ends_with($name, '-')) {
            return "Validation Error (Row {$row_num}): {$field_label} '{$name}' cannot start or end with a hyphen '-'. Hyphens must be strictly between characters.";
        }

        return null;
    };

    // ==========================================
    // 2. DATA ROW PARSING & NAME VALIDATION
    // ==========================================
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];

        if (empty(array_filter($row))) {
            continue;
        }

        $row_num = $i + 1;

        $fname = $row[1] ?? '';
        $mname = $row[2] ?? '';
        $lname = $row[3] ?? '';

        // Validate First Name (Required)
        $err = $validate_name_field($fname, 'First Name', $row_num, true);
        if ($err) {
            echo json_encode(['status' => 'error', 'message' => $err]);
            return;
        }

        // Validate Middle Name (Optional)
        $err = $validate_name_field($mname, 'Middle Name', $row_num, false);
        if ($err) {
            echo json_encode(['status' => 'error', 'message' => $err]);
            return;
        }

        // Validate Last Name (Required)
        $err = $validate_name_field($lname, 'Last Name', $row_num, true);
        if ($err) {
            echo json_encode(['status' => 'error', 'message' => $err]);
            return;
        }

        // Location & Reference ID Lookups
        $rawProv = $clean($row[9] ?? '');
        $rawCity = $clean($row[10] ?? '');
        $rawBrgy = $clean($row[11] ?? '');

        $provCode = is_numeric($rawProv) ? $this->format_location_code($rawProv) : $this->Tupad_model->find_province_code_by_desc($rawProv);
        $cityCode = is_numeric($rawCity) ? $this->format_location_code($rawCity) : $this->Tupad_model->find_city_code_by_desc($rawCity, $provCode);
        $brgyCode = is_numeric($rawBrgy) ? $this->format_location_code($rawBrgy) : $this->Tupad_model->find_barangay_code_by_desc($rawBrgy, $cityCode);

        $rawIdType = $clean($row[14] ?? '');
        $idType = is_numeric($rawIdType) ? (int)$rawIdType : $this->Tupad_model->find_type_id_by_desc($rawIdType);

        $rawBeneType = $clean($row[17] ?? '');
        $beneType = is_numeric($rawBeneType) ? (int)$rawBeneType : $this->Tupad_model->find_bene_type_id_by_desc($rawBeneType);

        $rawConvergence = $clean($row[28] ?? '');
        $convergenceId = is_numeric($rawConvergence) ? (int)$rawConvergence : $this->Tupad_model->find_convergence_id_by_desc($rawConvergence);

        $rawEpayment = $clean($row[20] ?? '');
        $epaymentId  = is_numeric($rawEpayment) ? (int)$rawEpayment : $this->Tupad_model->find_epayment_id_by_desc($rawEpayment);

        $rawSkills = $clean($row[19] ?? '');
        $skillsId  = is_numeric($rawSkills) ? (int)$rawSkills : $this->Tupad_model->find_skills_id_by_desc($rawSkills);

        $insertData[] = [
            'tupad_id_no'                 => $clean($row[0] ?? ''),
            'tupad_fname'                 => strtoupper(trim($fname)),
            'tupad_mname'                 => strtoupper(trim($mname)),
            'tupad_lname'                 => strtoupper(trim($lname)),
            'tupad_ext'                   => strtoupper($clean($row[4] ?? '')),
            'tupad_gender'                => strtoupper($clean($row[5] ?? '')),
            'tupad_dob_month'             => $clean($row[6] ?? ''),
            'tupad_dob_day'               => $clean($row[7] ?? ''),
            'tupad_dob_year'              => $clean($row[8] ?? ''),
            'tupad_province'              => $provCode,
            'tupad_municipality'          => $cityCode,
            'tupad_barangay'              => $brgyCode,
            'tupad_street'                => $clean($row[12] ?? ''),
            'tupad_district'              => $clean($row[13] ?? ''),
            'tupad_idtype'                => $idType,
            'tupad_idnumber'              => $clean($row[15] ?? ''),
            'tupad_contact_no'            => $clean($row[16] ?? ''),
            'tupad_type'                  => $beneType,
            'tupad_training_Interest'     => $clean($row[18] ?? ''),
            'tupad_skills'                => $skillsId, 
            'tupad_epayment'              => $epaymentId, 
            'tupad_account_no'            => $clean($row[21] ?? ''),
            'tupad_occupation'            => $clean($row[22] ?? ''),
            'tupad_civil_status'          => $clean($row[23] ?? ''),
            'tupad_age'                   => $clean($row[24] ?? ''),
            'tupad_average_monthly'       => $clean($row[25] ?? ''),
            'tupad_dependent'             => $clean($row[26] ?? ''),
            'tupad_interested_employment' => $clean($row[27] ?? ''),         
            'tupad_convergence'           => $convergenceId,
            'file_name'                   => $originalFileName,
            'user_id'                     => $uploadedBy,
            'uploaded_at'                 => $uploadedDate,
            'area_of_implementation'      => $area_of_implementation,
            'period_of_coverage'          => $period_of_coverage,
            'adl_no'                      => $adl_no,
            'reference_no'                => $reference_no,
            'nature_of_work'              => $nature_of_work
        ];
    }

    // 3. DATABASE BATCH INSERTION
    if (!empty($insertData)) {
        $inserted = $this->Tupad_model->insert_batch($insertData);

        if ($inserted) {
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

    public function view_file_data($file_name = NULL)
    {
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
        echo '<th>Date Hired</th>';
        echo '<th>Province</th>';
        echo '<th>City/Muni</th>';
        echo '<th>Barangay</th>';
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
            echo '<td>' . htmlspecialchars($row['tupad_date_hired'] ?? '') . '</td>';
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

            $actionButtons = '
                <a href="' . site_url('tupad/view_file_data/' . $encoded_filename) . '" class="btn btn-sm btn-primary me-1">
                    <i class="bi bi-eye me-1"></i> View
                </a>
                <a href="' . site_url('tupad/export_excel?file_name=' . $encoded_filename) . '" class="btn btn-sm btn-success me-1">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i>GPAI
                </a>
                <button type="button" class="btn btn-sm btn-warning btn-forward-gsis text-dark fw-semibold" data-filename="' . htmlspecialchars($f['file_name']) . '">
                    <i class="bi bi-send-fill me-1"></i> GSIS Letter
                </button>
            ';

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
            echo json_encode(['status' => 'success', 'message' => 'Details successfully forwarded to GSIS Letter table.']);
        } elseif ($result === 'exists') {
            echo json_encode(['status' => 'error', 'message' => 'Forwarding aborted: Matching details (Reference No., ADL No., and Implementor) already exist in the GSIS Letter table.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to forward details or file contains no records.']);
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

        $filename = 'TUPAD_GSIS_Export_' . date('Ymd_His') . '.xls';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="UTF-8"></head><body>';
        echo '<table border="1">';
        
        echo '<tr><th colspan="10" style="border: none; text-align: center; font-weight:bold; font-size: 14pt; background-color: #ffffff;">ENROLLMENT FORM TO GROUP PERSONAL ACCIDENT INSURANCE OF THE GOVERNMENT SERVICE INSURANCE SYSTEM (GSIS)</th></tr>';
        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;"></td></tr>';
        echo '<tr><td colspan="10" style="border: none; text-align: center; background-color: #ffffff;">Republic of the Philippines</td></tr>';
        echo '<tr><td colspan="10" style="border: none; text-align: center; background-color: #ffffff;">Department of Labor and Employment</td></tr>';
        echo '<tr><td colspan="10" style="border: none; text-align: center; background-color: #ffffff;">Employment Programs of DOLE (TUPAD)</td></tr>';
        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;"></td></tr>';
        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;">DOLE\'s Program: <b>Tulong Panghanapbuhay sa Ating Disadvantaged Workers (TUPAD)</b></td></tr>';
        echo '<tr><td colspan="8" style="border: none; background-color: #ffffff;">Area of Implementation, Province: <b>' . htmlspecialchars($area_of_implementation) . '</b></td><td colspan="2" style="border: none; background-color: #ffffff;">Number of Barangay : <b>' . $totalBarangays . '</b></td></tr>';
        echo '<tr><td colspan="8" style="border: none; background-color: #ffffff;">Period of Coverage: <b>' . htmlspecialchars($period_of_coverage) . '</b></td><td colspan="2" style="border: none; background-color: #ffffff;">M- <b>' . $maleCount . '</b> F- <b>' . $femaleCount . '</b> = T-<b>' . $totalBeneficiaries . '</b></td></tr>';
        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;">ADL No. <b>' . htmlspecialchars($adl_no) . '</b></td></tr>';
        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;">Reference No. <b>' . htmlspecialchars($reference_no) . '</b></td></tr>';
        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;">Specific Nature of work : <b>' . htmlspecialchars($nature_of_work) . '</b></td></tr>';
        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;"></td></tr>';

        echo '<tr style=" font-weight: bold; text-align: center;">';
        echo '<th rowspan="2">No.</th>';
        echo '<th rowspan="2">Name of Beneficiary (Last Name, First Name Middle Name Extension Name)</th>';
        echo '<th rowspan="2">Sex</th>';
        echo '<th rowspan="2">Birthdate (MM/DD/YYYY)</th>';
        echo '<th rowspan="2">Age</th>';
        echo '<th colspan="4">Address</th>';
        echo '<th rowspan="2">Beneficiary</th>';
        echo '</tr>';

        echo '<tr style=" font-weight: bold; text-align: center;">';
        echo '<th>Street</th>';
        echo '<th>Barangay</th>';
        echo '<th>City/ Municipality</th>';
        echo '<th>Province</th>';
        echo '</tr>';

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

            echo '<tr>';
            echo '<td style="text-align: center;">' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($fullName) . '</td>';
            echo '<td style="text-align: center;">' . htmlspecialchars($row['tupad_gender']) . '</td>';
            echo '<td style="text-align: center;">' . htmlspecialchars($dob) . '</td>';
            echo '<td style="text-align: center;">' . htmlspecialchars($age) . '</td>'; 
            echo '<td>' . htmlspecialchars($row['tupad_street']) . '</td>';
            echo '<td>' . htmlspecialchars($row['barangay_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['municipality_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['province_name'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($row['tupad_dependent']) . '</td>';
            echo '</tr>';
        }

       $user_id = $this->session->userdata('user_id');
        $regfname = '';
        $regmname = '';
        $reglname = '';
        $position_desc = 'Livelihood Development Specialist'; 

        if (!empty($user_id)) {
            $this->db->select('users.*, code_position.position_description');
            $this->db->from('users');
            $this->db->join('code_position', 'code_position.position_id = users.position_id', 'left');
            $this->db->where('users.id', $user_id);
            $user_row = $this->db->get()->row_array();

            if ($user_row) {
                $regfname = $user_row['reg_fname'] ?? $user_row['fname'] ?? '';
                $regmname = $user_row['reg_mname'] ?? $user_row['mname'] ?? '';
                $reglname = $user_row['reg_lname'] ?? $user_row['lname'] ?? '';
                
                if (!empty($user_row['position_description'])) {
                    $position_desc = $user_row['position_description'];
                }
            }
        }

        if (empty($regfname)) {
            $regfname = $this->session->userdata('reg_fname') ?? '';
        }

        $regmname = trim((string)$regmname);
        $middle_initial = !empty($regmname) ? strtoupper(substr($regmname, 0, 1)) . '.' : '';

        $name_parts = array_filter([trim($regfname), $middle_initial, trim($reglname)]);
        $prepared_by = !empty($name_parts) ? implode(' ', $name_parts) : 'Not LoggedIn';

        echo '<tr><td colspan="10" style="border: none; background-color: #ffffff;"></td></tr>';
        echo '<tr><td colspan="2" style="border: none; background-color: #ffffff;">Prepared by:</td><td colspan="8" style="border: none; background-color: #ffffff;">Approved by:</td></tr>';
        echo '<tr>';
        echo '<td colspan="2" style="border: none; background-color: #ffffff;"><br><br><b>' . htmlspecialchars($prepared_by) . '</b><br>' . htmlspecialchars($position_desc) . '</td>';
        echo '<td colspan="8" style="border: none; background-color: #ffffff;"><br><br><b>AURITA L. LAXAMANA</b><br>Chief LEO, TSSD II</td>';
        echo '</tr>';

        echo '</table>';
        echo '</body></html>';
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
    $summary_records = $this->Tupad_model->get_gsis_summary_by_date($start_date, $end_date);

    $filename = 'GSIS_Letter_Report_' . date('Ymd_His') . '.xls';

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta charset="UTF-8"><style>body { font-family: Arial, sans-serif; font-size: 10pt; }</style></head><body>';
    
    // Outer wrapper table to constrain width and center content neatly
    echo '<table width="750" style="margin: 0 auto; font-family: Arial, sans-serif; font-size: 10pt;">';
    echo '<tr><td>';

    // Letter Header Date
    echo '<br>';
    echo '<b>' . strtoupper(date('F d, Y')) . '</b><br><br>';

    // Recipient Details
    echo '<b>Ms. KRISTINE JOI G. MACAM</b><br>';
    echo 'Branch Manager<br>';
    echo '<b>Government Service Insurance System (GSIS)</b><br>';
    echo 'Sindalan, City of San Fernando, Pampanga<br><br>';

    // Salutation
    echo 'Dear Ms. Macam:<br><br>';

    // Opening Paragraph
    $formatted_effectivity = date('F d, Y', strtotime($date_effectivity));
    echo 'May we request the attached list of our beneficiaries under Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers (TUPAD) Program be enrolled under GSIS group insurance effective <b>' . $formatted_effectivity . '</b> with a covered period of work of <b>' . htmlspecialchars($no_of_days) . '</b> days. Below is the summary of our remittance:<br><br>';

    // Summary Table Structure with fixed column widths to prevent excessive stretching
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
    $dst = 200.00; 

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

    // Totals & Calculations Row
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

    // Closing & Sign-off block
    echo 'Thank you and warm regards.<br><br>';
    echo 'Very truly yours,<br><br><br>';
    echo '<b>AURITA L. LAXAMANA</b><br>';
    echo 'CHIEF LEO, TSSD II<br>';

    echo '</td></tr>';
    echo '</table>';

    echo '</body></html>';
    exit;
}
}