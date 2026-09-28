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
        $totalBeneficiariesCount = count($beneficiaries);

        $spreadsheet = new Spreadsheet();

        // Sheet 1: CONSOLIDATED SHEET (All records) with total count
        $consoSheet = $spreadsheet->getActiveSheet();
        $consoSheet->setTitle('CONSOLIDATED (' . $totalBeneficiariesCount . ')');
        $this->populate_sheet_data($consoSheet, $beneficiaries);

        // Sheet 2: TRANSPARENCY SHEET (Restricted columns format) with total count
        $transSheet = $spreadsheet->createSheet();
        $transSheet->setTitle('TRANSPARENCY (' . $totalBeneficiariesCount . ')');
        $this->populate_transparency_sheet($transSheet, $beneficiaries);

        // Dynamically group records by Beneficiary Type Description
        $groupedByBeneType = [];
        foreach ($beneficiaries as $row) {
            $beneTypeDescription = $row['bene_type_desc'] ?? 'GENERAL';
            
            if (empty($beneTypeDescription)) {
                $beneTypeDescription = 'MARGINALIZED';
            }

            $groupedByBeneType[$beneTypeDescription][] = $row;
        }

        // Create a separate sheet for each unique Beneficiary Type Description with its count
        foreach ($groupedByBeneType as $typeName => $typeRows) {
            $totalCount = count($typeRows);
            
            // Clean sheet name using str_replace to avoid regex errors
            $invalidChars = ['\\', '/', '?', '*', ':', '[', ']'];
            $cleanTypeName = str_replace($invalidChars, '', $typeName);
            
            // Format sheet name: Name + Total Count (e.g., SENIOR CITIZEN (45))
            $suffix = ' (' . $totalCount . ')';
            $maxNameLength = 31 - strlen($suffix);
            
            $sheetName = substr(strtoupper($cleanTypeName), 0, $maxNameLength) . $suffix;
            
            $uniqueSheetName = $sheetName;
            $counter = 1;
            
            while ($spreadsheet->sheetNameExists($uniqueSheetName)) {
                $altSuffix = ' (' . $totalCount . ')_' . $counter++;
                $maxAltLength = 31 - strlen($altSuffix);
                $uniqueSheetName = substr(strtoupper($cleanTypeName), 0, $maxAltLength) . $altSuffix;
            }

            $typeSheet = $spreadsheet->createSheet();
            $typeSheet->setTitle($uniqueSheetName);
            $this->populate_sheet_data($typeSheet, $typeRows);
        }

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

            $beneTypeDescription = $row['bene_type_desc'] ?? '';

            $sheet->setCellValue('A' . $rowNum, $counter++);
            $sheet->setCellValue('B' . $rowNum, $fullName);
            $sheet->setCellValue('C' . $rowNum, $row['tupad_gender'] ?? '');
            $sheet->setCellValue('D' . $rowNum, $birthdate);
            $sheet->setCellValue('E' . $rowNum, $row['tupad_age'] ?? '');
            $sheet->setCellValue('F' . $rowNum, $row['tupad_street'] ?? '');
            $sheet->setCellValue('G' . $rowNum, $row['brgy_name'] ?? '');
            $sheet->setCellValue('H' . $rowNum, $row['city_name'] ?? '');
            $sheet->setCellValue('I' . $rowNum, $row['province_name'] ?? '');
            $sheet->setCellValue('J' . $rowNum, $row['tupad_dependent'] ?? '');
            $sheet->setCellValue('K' . $rowNum, $beneTypeDescription);
          
            $rowNum++;
        }
    }

    // Helper specifically for Transparency Sheet matching your requested layout
    private function populate_transparency_sheet($sheet, $data) {
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