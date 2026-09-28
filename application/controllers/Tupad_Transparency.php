<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Tupad_Transparency extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('Tupad_Transparency_Model');
    }

    public function index() {
        $data['provinces'] = $this->Tupad_Transparency_Model->get_provinces();
        $this->load->view('tupad/tupad_transparency', $data);
    }

    public function fetch_data() {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $province_code = $this->input->post('province_code');

        $data = $this->Tupad_Transparency_Model->get_filtered_beneficiaries($start_date, $end_date, $province_code);
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function export_excel() {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $province_code = $this->input->get('province_code');

        $beneficiaries = $this->Tupad_Transparency_Model->get_filtered_beneficiaries($start_date, $end_date, $province_code);

        $spreadsheet = new Spreadsheet();

        // Sheet 1: CONSOLIDATED SHEET (Full columns)
        $consoSheet = $spreadsheet->getActiveSheet();
        $consoSheet->setTitle('CONSOLIDATED SHEET');
        $this->populate_sheet_data($consoSheet, $beneficiaries, 'full');

        // Sheet 2: TRANSPARENCY SHEET (Custom restricted columns: No, Name of Beneficiary, Gender, Age, Province)
        $transSheet = $spreadsheet->createSheet();
        $transSheet->setTitle('TRANSPARENCY SHEET');
        $this->populate_transparency_sheet($transSheet, $beneficiaries);

        // Sheet 3: SENIOR CITIZEN (Full columns, filtered)
        $seniorBeneficiaries = array_filter($beneficiaries, function($row) {
            $type = strtolower($row['bene_type_desc'] ?? '');
            $dep = strtolower($row['tupad_dependent'] ?? '');
            return strpos($type, 'senior') !== false || strpos($dep, 'senior') !== false;
        });
        $seniorSheet = $spreadsheet->createSheet();
        $seniorSheet->setTitle('SENIOR CITIZEN');
        $this->populate_sheet_data($seniorSheet, $seniorBeneficiaries, 'full');

        // Sheet 4: OTHER BENEFICIARIES (Full columns, filtered)
        $otherBeneficiaries = array_filter($beneficiaries, function($row) {
            $type = strtolower($row['bene_type_desc'] ?? '');
            $dep = strtolower($row['tupad_dependent'] ?? '');
            return strpos($type, 'senior') === false && strpos($dep, 'senior') === false;
        });
        $otherSheet = $spreadsheet->createSheet();
        $otherSheet->setTitle('OTHER BENEFICIARIES');
        $this->populate_sheet_data($otherSheet, $otherBeneficiaries, 'full');

        // Output download headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="TUPAD_Report_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    // Helper for standard sheets with all columns
    private function populate_sheet_data($sheet, $data) {
        $headers = [
            'No', 
            'Name', 
            'Sex', 
            'Birthdate', 
            'Age', 
            'Address', 
            'Barangay', 
            'Municipality', 
            'Province', 
            'Beneficiary Type', 
            'Tupad Type'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        $rowNum = 2;
        $counter = 1;
        foreach ($data as $row) {
            $fullName = trim(($row['tupad_lname'] ?? '') . ', ' . ($row['tupad_fname'] ?? '') . ' ' . ($row['tupad_mname'] ?? '') . ' ' . ($row['tupad_ext'] ?? ''));
            $birthdate = trim(($row['tupad_dob_month'] ?? '') . '/' . ($row['tupad_dob_day'] ?? '') . '/' . ($row['tupad_dob_year'] ?? ''));

            $sheet->setCellValue('A' . $rowNum, $counter++);
            $sheet->setCellValue('B' . $rowNum, $fullName);
            $sheet->setCellValue('C' . $rowNum, $row['tupad_gender'] ?? '');
            $sheet->setCellValue('D' . $rowNum, $birthdate === '//' ? '' : $birthdate);
            $sheet->setCellValue('E' . $rowNum, $row['tupad_age'] ?? '');
            $sheet->setCellValue('F' . $rowNum, $row['tupad_street'] ?? '');
            $sheet->setCellValue('G' . $rowNum, $row['brgy_name'] ?? '');
            $sheet->setCellValue('H' . $rowNum, $row['city_name'] ?? '');
            $sheet->setCellValue('I' . $rowNum, $row['province_name'] ?? '');
            $sheet->setCellValue('J' . $rowNum, $row['tupad_dependent'] ?? '');
            $sheet->setCellValue('K' . $rowNum, $row['bene_type_desc'] ?? '');
            
            $rowNum++;
        }
    }

    // Helper specifically for Transparency Sheet matching your requested layout
    private function populate_transparency_sheet($sheet, $data) {
        // Based on your template sample (like Annex B), matching: No, Name of Beneficiary, Gender, Age, Province
        $headers = [
            'No', 
            'Name of Beneficiary', 
            'Gender', 
            'Age', 
            'Province'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        $rowNum = 2;
        $counter = 1;
        foreach ($data as $row) {
            $fullName = trim(($row['tupad_lname'] ?? '') . ', ' . ($row['tupad_fname'] ?? '') . ' ' . ($row['tupad_mname'] ?? '') . ' ' . ($row['tupad_ext'] ?? ''));

            $sheet->setCellValue('A' . $rowNum, $counter++);
            $sheet->setCellValue('B' . $rowNum, $fullName);
            $sheet->setCellValue('C' . $rowNum, $row['tupad_gender'] ?? '');
            $sheet->setCellValue('D' . $rowNum, $row['tupad_age'] ?? '');
            $sheet->setCellValue('E' . $rowNum, $row['province_name'] ?? '');
            
            $rowNum++;
        }
    }


























    
}