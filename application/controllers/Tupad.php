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
        $this->load->model('Activity_model');
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

    // Duplicate reference no check
    if (!empty($reference_no) && $this->Tupad_model->reference_no_exists($reference_no)) {
        @unlink($filePath);  
        echo json_encode([
            'status' => 'error', 
            'message' => 'Upload stopped: The Reference No. "' . $reference_no . '" has already been registered in the database.'
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
        $actual_col = isset($uploaded_headers[$index]) ? $uploaded_headers[$index] : '';
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

    // 1. Strict cleaner exclusively for names
    $clean_name = function($val) {
        $val = trim(isset($val) ? $val : '');
        $val = preg_replace('/[^\p{L}\s\-]/u', '', $val);
        $val = preg_replace('/\s+/', ' ', $val);
        return $val;
    };

    // 2. General cleaner for other fields
    $clean_general = function($val) {
        $val = trim(isset($val) ? $val : '');
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

        if (substr($name, 0, 1) === '-' || substr($name, -1) === '-') {
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

        $row_num   = $i + 1;
        $fname     = $clean_name(isset($row[1]) ? $row[1] : '');
        $mname     = $clean_name(isset($row[2]) ? $row[2] : '');
        $lname     = $clean_name(isset($row[3]) ? $row[3] : '');
        $gender    = $clean_general(isset($row[5]) ? $row[5] : ''); 
        
        $rawProv   = $clean_general(isset($row[9]) ? $row[9] : '');
        $rawCity   = $clean_general(isset($row[10]) ? $row[10] : '');
        $rawBrgy   = $clean_general(isset($row[11]) ? $row[11] : '');

        // Validate First Name (Required)
        $err = $validate_name_field($fname, 'First Name', $row_num, true);
        if ($err) { $discrepancies[] = $err; }

        // Validate Middle Name (Optional)
        $err = $validate_name_field($mname, 'Middle Name', $row_num, false);
        if ($err) { $discrepancies[] = $err; }

        // Validate Last Name (Required)
        $err = $validate_name_field($lname, 'Last Name', $row_num, true);
        if ($err) { $discrepancies[] = $err; }

        // ==========================================
        // VALIDATE GENDER (Column F: MALE or FEMALE)
        // ==========================================
        $gender_upper = strtoupper(trim($gender));
        if ($gender_upper === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Gender cannot be blank.";
        } elseif ($gender_upper !== 'MALE' && $gender_upper !== 'FEMALE') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Gender '{$gender}' is invalid. It must be either MALE or FEMALE.";
        }

        // ==========================================
        // BIRTH DATE VALIDATION (Columns G, H, I)
        // ==========================================
        $dob_month_raw = trim(isset($row[6]) ? $row[6] : '');
        $dob_day_raw   = trim(isset($row[7]) ? $row[7] : '');
        $dob_year_raw  = trim(isset($row[8]) ? $row[8] : '');

        $dob_has_error = false;

        // Column G: Month (1 to 12)
        if ($dob_month_raw === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Month (tupad_dob_month) cannot be blank.";
            $dob_has_error = true;
        } elseif (!is_numeric($dob_month_raw) || (int)$dob_month_raw < 1 || (int)$dob_month_raw > 12) {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Month (tupad_dob_month) must be a numeric value between 1 and 12.";
            $dob_has_error = true;
        }

        // Column H: Day (1 to 31)
        if ($dob_day_raw === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Day (tupad_dob_day) cannot be blank.";
            $dob_has_error = true;
        } elseif (!is_numeric($dob_day_raw) || (int)$dob_day_raw < 1 || (int)$dob_day_raw > 31) {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Day (tupad_dob_day) must be a numeric value between 1 and 31.";
            $dob_has_error = true;
        }

        // Column I: Year (1915 to 2020)
        if ($dob_year_raw === '') {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Year (tupad_dob_year) cannot be blank.";
            $dob_has_error = true;
        } elseif (!is_numeric($dob_year_raw) || (int)$dob_year_raw < 1915 || (int)$dob_year_raw > 2020) {
            $discrepancies[] = "Validation Error (Row {$row_num}): Birth Year (tupad_dob_year) must be between 1915 and 2020.";
            $dob_has_error = true;
        }

        // Calendar Date Validity Check
        if (!$dob_has_error) {
            $m = (int)$dob_month_raw;
            $d = (int)$dob_day_raw;
            $y = (int)$dob_year_raw;

            if (!checkdate($m, $d, $y)) {
                $discrepancies[] = "Validation Error (Row {$row_num}): The date combination ({$m}/{$d}/{$y}) is invalid according to standard calendar rules.";
            }
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

        $fname = $clean_name(isset($row[1]) ? $row[1] : '');
        $mname = $clean_name(isset($row[2]) ? $row[2] : '');
        $lname = $clean_name(isset($row[3]) ? $row[3] : '');
        $ext   = $clean_name(isset($row[4]) ? $row[4] : '');

        $rawProv = $clean_general(isset($row[9]) ? $row[9] : '');
        $rawCity = $clean_general(isset($row[10]) ? $row[10] : '');
        $rawBrgy = $clean_general(isset($row[11]) ? $row[11] : '');

        $provCode = is_numeric($rawProv) ? $this->format_location_code($rawProv) : $this->Tupad_model->find_province_code_by_desc($rawProv);
        $cityCode = is_numeric($rawCity) ? $this->format_location_code($rawCity) : $this->Tupad_model->find_city_code_by_desc($rawCity, $provCode);
        $brgyCode = is_numeric($rawBrgy) ? $this->format_location_code($rawBrgy) : $this->Tupad_model->find_barangay_code_by_desc($rawBrgy, $cityCode);

        $rawIdType = $clean_general(isset($row[14]) ? $row[14] : '');
        $idType = is_numeric($rawIdType) ? (int)$rawIdType : $this->Tupad_model->find_type_id_by_desc($rawIdType);

        $rawBeneType = $clean_general(isset($row[17]) ? $row[17] : '');
        $beneType = is_numeric($rawBeneType) ? (int)$rawBeneType : $this->Tupad_model->find_bene_type_id_by_desc($rawBeneType);

        $rawConvergence = $clean_general(isset($row[28]) ? $row[28] : '');
        $convergenceId = is_numeric($rawConvergence) ? (int)$rawConvergence : $this->Tupad_model->find_convergence_id_by_desc($rawConvergence);

        $rawEpayment = $clean_general(isset($row[20]) ? $row[20] : '');
        $epaymentId  = is_numeric($rawEpayment) ? (int)$rawEpayment : $this->Tupad_model->find_epayment_id_by_desc($rawEpayment);

        $rawSkills = $clean_general(isset($row[19]) ? $row[19] : '');
        $skillsId  = is_numeric($rawSkills) ? (int)$rawSkills : $this->Tupad_model->find_skills_id_by_desc($rawSkills);

        $insertData[] = [
            'tupad_id_no'                 => $clean_general(isset($row[0]) ? $row[0] : ''),
            'tupad_fname'                 => strtoupper(trim($fname)),
            'tupad_mname'                 => strtoupper(trim($mname)),
            'tupad_lname'                 => strtoupper(trim($lname)),
            'tupad_ext'                   => strtoupper($ext),
            'tupad_gender'                => strtoupper($clean_general(isset($row[5]) ? $row[5] : '')),
            'tupad_dob_month'             => $clean_general(isset($row[6]) ? $row[6] : ''),
            'tupad_dob_day'               => $clean_general(isset($row[7]) ? $row[7] : ''),
            'tupad_dob_year'              => $clean_general(isset($row[8]) ? $row[8] : ''),
            'tupad_province'              => $provCode,
            'tupad_municipality'          => $cityCode,
            'tupad_barangay'              => $brgyCode,
            'tupad_street'                => strtoupper($clean_general(isset($row[12]) ? $row[12] : '')),
            'tupad_district'              => strtoupper($clean_general(isset($row[13]) ? $row[13] : '')),
            'tupad_idtype'                => strtoupper($idType),
            'tupad_idnumber'              => $clean_general(isset($row[15]) ? $row[15] : ''),
            'tupad_contact_no'            => $clean_general(isset($row[16]) ? $row[16] : ''),
            'tupad_type'                  => strtoupper($beneType),
            'tupad_training_Interest'     => strtoupper($clean_general(isset($row[18]) ? $row[18] : '')),
            'tupad_skills'                => $skillsId, 
            'tupad_epayment'              => $epaymentId, 
            'tupad_account_no'            => $clean_general(isset($row[21]) ? $row[21] : ''),
            'tupad_occupation'            => $clean_general(isset($row[22]) ? $row[22] : ''),
            'tupad_civil_status'          => strtoupper($clean_general(isset($row[23]) ? $row[23] : '')),
            'tupad_age'                   => $clean_general(isset($row[24]) ? $row[24] : ''),
            'tupad_average_monthly'       => $clean_general(isset($row[25]) ? $row[25] : ''),
            'tupad_dependent'             => strtoupper($clean_general(isset($row[26]) ? $row[26] : '')),
            'tupad_interested_employment' => $clean_general(isset($row[27]) ? $row[27] : ''),      
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
        $sheet->mergeCells('A7:F7');
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
        $run12 = $richText12->createTextRun(strtoupper($nature_of_work));
        $run12->getFont()->setBold(true);
        $sheet->setCellValue('A12', $richText12);
        
        $sheet->getStyle('A12')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A12')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // Dynamically calculate row height based on text length and merged columns width (~155 characters per line)
        $totalLength = strlen('Specific Nature of work : ' . $nature_of_work);
        $estimatedLines = max(1, ceil($totalLength / 150)); 
        $sheet->getRowDimension(12)->setRowHeight($estimatedLines * 13);




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
            $sheet->setCellValue("F{$rowNum}", strtoupper($row['tupad_street'] ?? ''));
            $sheet->setCellValue("G{$rowNum}", strtoupper($row['barangay_name'] ?? ''));
            $sheet->setCellValue("H{$rowNum}", $row['municipality_name'] ?? '');
            $sheet->setCellValue("I{$rowNum}", $row['province_name'] ?? '');
            $sheet->setCellValue("J{$rowNum}", $row['tupad_dependent'] ?? '');

            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->applyFromArray($thinBorder);
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFont()->setSize(10);
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
        $sheet->getColumnDimension('C')->setWidth(6);   
        $sheet->getColumnDimension('D')->setWidth(16);  
        $sheet->getColumnDimension('E')->setWidth(6);   
        $sheet->getColumnDimension('F')->setWidth(23);  
        $sheet->getColumnDimension('G')->setWidth(23);  
        $sheet->getColumnDimension('H')->setWidth(22);  
        $sheet->getColumnDimension('I')->setWidth(18);  
        $sheet->getColumnDimension('J')->setWidth(32);  

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

        $filename = 'GSIS_Letter_Report_' . date('Ymd_His') . '.xlsx';
         $user_id = $this->session->userdata('user_id');

         $this->load->model('Activity_Model'); 
         $this->Activity_Model->log_activity($filename, $user_id, 7);  

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

        // Current Date Header
        $current_formatted_date = strtoupper(date('F d, Y'));
        $sheet->setCellValue('A1', $current_formatted_date);
        $sheet->getStyle('A1')->getFont()->setBold(true);

        // Recipient Address Block
        $sheet->setCellValue('A3', "Ms. KRISTINE JOI G. MACAM");
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->setCellValue('A4', "Branch Manager");
        $sheet->setCellValue('A5', "Government Service Insurance System (GSIS)");
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->setCellValue('A6', "Sindalan, City of San Fernando, Pampanga");

        // Salutation
        $sheet->setCellValue('A8', "Dear Ms. Macam:");

        // Introductory Paragraph
        $formatted_effectivity = date('F d, Y', strtotime($date_effectivity));
        $intro_text = "May we request the attached list of our beneficiaries under Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers (TUPAD) Program be enrolled under GSIS group insurance effective " . $formatted_effectivity . " with a covered period of work of " . $no_of_days . " days. Below is the summary of our remittance:";
        $sheet->mergeCells('A10:G10');
        $sheet->setCellValue('A10', $intro_text);
        $sheet->getStyle('A10')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(10)->setRowHeight(43);

        // Table Headers (Rows 12 & 13)
        $sheet->mergeCells('A12:A13');
        $sheet->setCellValue('A12', '#');
        $sheet->mergeCells('B12:B13');
        $sheet->setCellValue('B12', 'PARTICULAR');
        $sheet->mergeCells('C12:E12');
        $sheet->setCellValue('C12', 'NO. OF BENEFICIARIES');
        $sheet->setCellValue('C13', 'MALE');
        $sheet->setCellValue('D13', 'FEMALE');
        $sheet->setCellValue('E13', 'TOTAL');
        $sheet->mergeCells('F12:F13');
        $sheet->setCellValue('F12', 'RATE');
        $sheet->mergeCells('G12:G13');
        $sheet->setCellValue('G12', 'AMOUNT');

        $headerRange = 'A12:G13';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->applyFromArray($centerStyle);
        $sheet->getStyle($headerRange)->applyFromArray($thinBorder);
        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');

        // Populate Summary Records
        $rowNum = 14;
        $i = 1;
        $total_male = 0;
        $total_female = 0;
        $total_benefs = 0;
        $total_amount = 0;
        $rate = 50.00;
        $dst = 0;

        if (!empty($summary_records)) {
            foreach ($summary_records as $row) {
                $m = $row['male'] ?? 0;
                $f = $row['female'] ?? 0;
                $sub_total = $m + $f;
                $amount = $sub_total * $rate;

                $total_male += $m;
                $total_female += $f;
                $total_benefs += $sub_total;
                $total_amount += $amount;

                $particular = ($row['implementor'] ?? '') . ' (' . ($row['reference_no'] ?? '') . ')';

                $sheet->setCellValue("A{$rowNum}", $i++);
                $sheet->setCellValue("B{$rowNum}", $particular);
                $sheet->setCellValue("C{$rowNum}", $m);
                $sheet->setCellValue("D{$rowNum}", $f);
                $sheet->setCellValue("E{$rowNum}", $sub_total);
                $sheet->setCellValue("F{$rowNum}", $rate);
                $sheet->setCellValue("G{$rowNum}", $amount);

                $sheet->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($thinBorder);
                $sheet->getStyle("A{$rowNum}")->applyFromArray($centerStyle);
                $sheet->getStyle("B{$rowNum}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("C{$rowNum}")->applyFromArray($centerStyle);
                $sheet->getStyle("D{$rowNum}")->applyFromArray($centerStyle);
                $sheet->getStyle("E{$rowNum}")->getFont()->setBold(true);
                $sheet->getStyle("E{$rowNum}")->applyFromArray($centerStyle);
                $sheet->getStyle("F{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');

                $rowNum++;
            }
        } else {
            $sheet->mergeCells("A{$rowNum}:G{$rowNum}");
            $sheet->setCellValue("A{$rowNum}", "No records found for the selected date range.");
            $sheet->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($thinBorder);
            $sheet->getStyle("A{$rowNum}")->applyFromArray($centerStyle);
            $rowNum++;
        }

        // Calculate DST
        if ($total_benefs == 1) {
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

        // Total Row
        $sheet->mergeCells("A{$rowNum}:B{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", "TOTAL:");
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
        $sheet->setCellValue("C{$rowNum}", $total_male);
        $sheet->setCellValue("D{$rowNum}", $total_female);
        $sheet->setCellValue("E{$rowNum}", $total_benefs);
        $sheet->setCellValue("F{$rowNum}", "");
        $sheet->setCellValue("G{$rowNum}", $total_amount);

        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($thinBorder);
        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("C{$rowNum}")->applyFromArray($centerStyle);
        $sheet->getStyle("D{$rowNum}")->applyFromArray($centerStyle);
        $sheet->getStyle("E{$rowNum}")->applyFromArray($centerStyle);
        $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');

        $rowNum++;

        // DST Row
        $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", "DST");
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
        $sheet->setCellValue("G{$rowNum}", $dst);

        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($thinBorder);
        $sheet->getStyle("G{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');

        $rowNum++;

        // Grand Total Row
        $grand_total = $total_amount + ($total_amount > 0 ? $dst : 0);
        $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", "GRAND TOTAL");
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
        $sheet->setCellValue("G{$rowNum}", $grand_total);

        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($thinBorder);
        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->getFont()->setBold(true);
        $sheet->getStyle("G{$rowNum}")->getFont()->getColor()->setARGB('FF2563EB');
        $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("A{$rowNum}:G{$rowNum}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');

        $rowNum += 2;

        // Closing & Signatures
        $sheet->setCellValue("A{$rowNum}", "Thank you and warm regards.");
        $rowNum += 2;
        $sheet->setCellValue("A{$rowNum}", "Very truly yours,");
        $rowNum += 3;
        $sheet->setCellValue("A{$rowNum}", "AURITA L. LAXAMANA");
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
        $rowNum++;
        $sheet->setCellValue("A{$rowNum}", "CHIEF LEO, TSSD II");

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(18);

        // Stream output as a valid .xlsx file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
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




































public function export_gsis_sequences_excel()
{
    $start_date       = $this->input->get('start_date');
    $end_date         = $this->input->get('end_date');
    $date_effectivity = $this->input->get('date_effectivity');
    $no_of_days       = $this->input->get('no_of_days');

    if (empty($start_date) || empty($end_date)) {
        $start_date = date('Y-m-01');
        $end_date   = date('Y-m-t');
    }

    // Fetch summary records within date range
    $summary_records = $this->Tupad_model->get_gsis_summary_by_date($start_date, $end_date);
    
    // 1. Calculate the grand total count of actual beneficiary entries across all groups
    $total_entries = 0;
    if (!empty($summary_records)) {
        foreach ($summary_records as $summary) {
            $reference_no = $summary['reference_no'] ?? '';
            $implementor  = $summary['implementor'] ?? $summary['area_of_implementation'] ?? '';

            $group_count = $this->db->where([
                'reference_no'           => $reference_no,
                'area_of_implementation' => $implementor
            ])->count_all_results('tbl_tupad_list');

            $total_entries += $group_count;
        }
    }

   // 2. Format filename: September 17, 2026_LIST OF [X] TUPAD Beneficiaries.xlsx
    $today = date('F d, Y');
    $filename = $today . '_LIST OF ' . $total_entries . ' TUPAD Beneficiaries.xlsx';

    // Initialize PhpSpreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setShowGridlines(true);

    // Styling definitions
    $centerStyle = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ]
    ];
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText'   => true
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color'       => ['argb' => 'FF000000'],
            ],
        ],
    ];
    $thinBorder = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color'       => ['argb' => 'FF000000'],
            ],
        ],
    ];

    $rowNum = 1;

    if (!empty($summary_records)) {
        foreach ($summary_records as $summary) {
            $reference_no = $summary['reference_no'] ?? '';
            $implementor  = $summary['implementor'] ?? $summary['area_of_implementation'] ?? '';
            $adl_no       = $summary['adl_no'] ?? $summary['adl_number'] ?? '';
            $nature_work  = $summary['nature_of_work'] ?? $summary['type_of_work'] ?? '';

            // Fetch records for this specific group from tbl_tupad_list
            $records = $this->db->get_where('tbl_tupad_list', [
                'reference_no'           => $reference_no,
                'area_of_implementation' => $implementor
            ])->result_array();

            // Pull period coverage checking every possible column variant in summary or records
            $period = '';
            foreach ([$summary, (!empty($records) ? $records[0] : [])] as $source) {
                if (!empty($source['period_coverage'])) { $period = $source['period_coverage']; break; }
                if (!empty($source['period_of_coverage'])) { $period = $source['period_of_coverage']; break; }
                if (!empty($source['date_effectivity'])) { $period = $source['date_effectivity']; break; }
                if (!empty($source['coverage_period'])) { $period = $source['coverage_period']; break; }
                if (!empty($source['period'])) { $period = $source['period']; break; }
            }

            // 1. Group Meta Headers (Above Table)
            $sheet->setCellValue("A{$rowNum}", "Area of Implementation, Province: " . $implementor);
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
            $rowNum++;

            if (!empty($period)) {
                $sheet->setCellValue("A{$rowNum}", "Period of Coverage: " . $period);
                $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
                $rowNum++;
            }

            if (!empty($adl_no)) {
                $sheet->setCellValue("A{$rowNum}", "ADL No. " . $adl_no);
                $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
                $rowNum++;
            }

            $sheet->setCellValue("A{$rowNum}", "Reference No. " . $reference_no);
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
            $rowNum++;

            if (!empty($nature_work)) {
                $sheet->setCellValue("A{$rowNum}", "Specific Nature of work : " . $nature_work);
                $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
                $rowNum++;
            }

            // 2. Table Header
            $sheet->setCellValue("A{$rowNum}", "No.");
            $sheet->setCellValue("B{$rowNum}", "Name of Beneficiary (Last Name, First Name Middle Name Extension Name)");
            
            $sheet->getStyle("A{$rowNum}:B{$rowNum}")->applyFromArray($headerStyle);
            $sheet->getRowDimension($rowNum)->setRowHeight(30);
            $rowNum++;

            // 3. Populate Beneficiaries per Group
            $counter = 1; 
            if (!empty($records)) {
                foreach ($records as $record) {
                    $lname = trim($record['tupad_lname'] ?? $record['last_name'] ?? $record['lname'] ?? '');
                    $fname = trim($record['tupad_fname'] ?? $record['first_name'] ?? $record['fname'] ?? '');
                    $mname = trim($record['tupad_mname'] ?? $record['middle_name'] ?? $record['mname'] ?? '');
                    $ext   = trim($record['tupad_ext'] ?? $record['extension'] ?? $record['ext'] ?? '');

                    $fullName = $lname;
                    if (!empty($lname) && !empty($fname)) {
                        $fullName .= ', ' . $fname;
                    } elseif (!empty($fname)) {
                        $fullName = $fname;
                    }
                    if (!empty($mname)) {
                        $fullName .= ' ' . $mname;
                    }
                    if (!empty($ext)) {
                        $fullName .= ' ' . $ext;
                    }

                    $sheet->setCellValue("A{$rowNum}", $counter++);
                    $sheet->setCellValue("B{$rowNum}", mb_strtoupper($fullName, 'UTF-8'));

                    $sheet->getStyle("A{$rowNum}:B{$rowNum}")->applyFromArray($thinBorder);
                    $sheet->getStyle("A{$rowNum}")->applyFromArray($centerStyle);

                    $rowNum++;
                }
            }

            // Add 2 blank rows spacing between groups
            $rowNum += 2;
        }
    } else {
        $sheet->setCellValue("A1", "No records found for the selected date range.");
    }

    // Column Widths
    $sheet->getColumnDimension('A')->setWidth(10);
    $sheet->getColumnDimension('B')->setWidth(55);

    // Stream output as an Excel file with the dynamic count filename
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}








}