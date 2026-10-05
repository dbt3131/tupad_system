<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Tupad_CQPR extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Tupad_CQPR_model');
        $this->load->helper(['url', 'form']);
    }

public function index() {
    $start_date = $this->input->get('start_date');
    $end_date   = $this->input->get('end_date');
    $province   = $this->input->get('province');

    $data['provinces'] = $this->Tupad_CQPR_model->get_provinces();

    // STRICT CHECK: Only fetch records if at least one filter is selected
    if (!empty($start_date) || !empty($end_date) || !empty($province)) {
        $data['reports'] = $this->Tupad_CQPR_model->get_tupad_report($start_date, $end_date, $province);
    } else {
        $data['reports'] = []; // Passes an empty array so no rows display
    }
    
    $data['start_date'] = $start_date;
    $data['end_date']   = $end_date;
    $data['selected_province'] = $province;

    $this->load->view('tupad/tupad_cqpr', $data);
}





    public function export_xlsx() {
        $start_date = $this->input->get('start_date');
        $end_date   = $this->input->get('end_date');
        $province   = $this->input->get('province');

        $reports = $this->Tupad_CQPR_model->get_tupad_report($start_date, $end_date, $province);

        // Format date range for the header
        $date_range_title = "ALL RECORDS";
        if (!empty($start_date) && !empty($end_date)) {
            $date_range_title = strtoupper(date('F j, Y', strtotime($start_date)) . ' to ' . date('F j, Y', strtotime($end_date)));
        } elseif (!empty($start_date)) {
            $date_range_title = "FROM " . strtoupper(date('F j, Y', strtotime($start_date)));
        } elseif (!empty($end_date)) {
            $date_range_title = "UNTIL " . strtoupper(date('F j, Y', strtotime($end_date)));
        }

        // Initialize PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CQPR Report');

        // Styles definitions
        $border_thin = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $header_style = [
            'font' => ['name' => 'Arial', 'size' => 10, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, 
                'vertical' => Alignment::VERTICAL_CENTER, 
                'wrapText' => true
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $doc_code_style = [
            'font' => ['name' => 'Arial', 'size' => 9, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $title_center = [
            'font' => ['name' => 'Arial', 'size' => 10, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ];

        // 1. Document Control Code Box (Right Side - Columns M to Q, Rows 1 to 3)
        $sheet->setCellValue('M1', 'DOLE-QF-COP-05.01');
        $sheet->setCellValue('M2', 'Revision No. 07');
        $sheet->setCellValue('M3', 'Effective Date: 24 March 2026');
        
        $sheet->mergeCells('M1:Q1');
        $sheet->mergeCells('M2:Q2');
        $sheet->mergeCells('M3:Q3');
        $sheet->getStyle('M1:Q3')->applyFromArray($doc_code_style);

        // 2. Title Blocks (Centered across columns A to Q)
        $sheet->setCellValue('A4', 'Consolidated Quarterly Progress Report');
        $sheet->setCellValue('A5', 'DOLE Integrated Livelihood and Emergency Employment Program (DILEEP)');
        $sheet->setCellValue('A6', $date_range_title);
        
        $sheet->setCellValue('A8', 'DOLE Regional Office No. III');
        $sheet->setCellValue('A9', 'Pangkabuhayan sa Ating Disadvantaged Workers Program');

        $sheet->mergeCells('A4:Q4');
        $sheet->mergeCells('A5:Q5');
        $sheet->mergeCells('A6:Q6');
        $sheet->mergeCells('A8:Q8');
        $sheet->mergeCells('A9:Q9');

        $sheet->getStyle('A4:Q9')->applyFromArray($title_center);

        // 3. Multi-tier Header Row 1 (Row 11)
        $sheet->setCellValue('A11', "Name & Nature of Project\n(i.e. declogging of canal, tree planting, basic repair of infrastructures)");
        $sheet->setCellValue('B11', "Name of Implementer\n(specify the DOLE Office or Name of Co-partner)");
        $sheet->setCellValue('C11', "Project Location");
        $sheet->setCellValue('H11', "Work Period Category");
        $sheet->setCellValue('J11', "Total");
        $sheet->setCellValue('K11', "Beneficiaries");
        $sheet->setCellValue('M11', "Amount Released (Php)");
        $sheet->setCellValue('N11', "Date Released");
        $sheet->setCellValue('O11', "Fund Source\n(Please specify: Regular GAA, Sin Tax, Continuing Fund, others)");
        $sheet->setCellValue('P11', "Project Status*");
        $sheet->setCellValue('Q11', "Convergence Initiatives");

        // Merge cells for multi-tier headers (Row 11)
        $sheet->mergeCells('C11:G11'); 
        $sheet->mergeCells('H11:I11'); 
        $sheet->mergeCells('K11:L11'); 

        // 4. Multi-tier Header Row 2 (Subheaders - Row 12)
        $sheet->setCellValue('C12', "Barangay");
        $sheet->setCellValue('D12', "City/Municipality");
        $sheet->setCellValue('E12', "Province");
        $sheet->setCellValue('F12', "District");
        $sheet->setCellValue('G12', "Income Class");
        $sheet->setCellValue('H12', "Short-term");
        $sheet->setCellValue('I12', "Long-term");
        $sheet->setCellValue('K12', "Female");
        $sheet->setCellValue('L12', "Type");

        // Vertical Merges for standalone columns
        $sheet->mergeCells('A11:A12');
        $sheet->mergeCells('B11:B12');
        $sheet->mergeCells('J11:J12');
        $sheet->mergeCells('M11:M12');
        $sheet->mergeCells('N11:N12');
        $sheet->mergeCells('O11:O12');
        $sheet->mergeCells('P11:P12');
        $sheet->mergeCells('Q11:Q12');

        // Apply Header styling and row heights
        $sheet->getStyle('A11:Q12')->applyFromArray($header_style);
        $sheet->getRowDimension(11)->setRowHeight(40);
        $sheet->getRowDimension(12)->setRowHeight(25);

        // 5. Data Rows starting from Row 13
        $row_num = 13;
        if (!empty($reports)) {
            foreach ($reports as $row) {
                $implementer_name = "DOLE RO3 / " . $row['implementation_province_desc'] . " FIELD OFFICE (DIRECT ADMINISTRATION)";
                
                $sheet->setCellValue('A' . $row_num, $row['nature_of_works']);
                $sheet->setCellValue('B' . $row_num, $implementer_name);
                $sheet->setCellValue('C' . $row_num, $row['implementation_barangay_name']);
                $sheet->setCellValue('D' . $row_num, $row['implementation_city_desc']);
                $sheet->setCellValue('E' . $row_num, $row['implementation_province_desc']);
                $sheet->setCellValue('F' . $row_num, $row['implementation_district']);
                $sheet->setCellValue('G' . $row_num, $row['implementation_classification']);
                $sheet->setCellValue('H' . $row_num, (int)$row['short_term']);
                $sheet->setCellValue('I' . $row_num, (int)$row['long_term']);
                $sheet->setCellValue('J' . $row_num, (int)$row['total_term']);
                $sheet->setCellValue('K' . $row_num, (int)$row['female_count']);
                $sheet->setCellValue('L' . $row_num, $row['tupad_types']);
                $sheet->setCellValue('M' . $row_num, (float)$row['amount_released']);
                $sheet->setCellValue('N' . $row_num, $row['payout_date']);
                $sheet->setCellValue('O' . $row_num, $row['fund_source']);
                $sheet->setCellValue('P' . $row_num, $row['project_status']);
                $sheet->setCellValue('Q' . $row_num, $row['tupad_convergence']);

                // Enable text wrapping across all data cells for a clean professional appearance
                $sheet->getStyle('A' . $row_num . ':Q' . $row_num)->getAlignment()->setWrapText(true);
                $sheet->getStyle('A' . $row_num . ':Q' . $row_num)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Specific alignments for data types
                $sheet->getStyle('A' . $row_num . ':B' . $row_num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C' . $row_num . ':L' . $row_num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('N' . $row_num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('O' . $row_num . ':Q' . $row_num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Amount column formatting
                $sheet->getStyle('M' . $row_num)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('M' . $row_num)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Let row height adjust dynamically based on content wrap, with a safe baseline minimum
                $sheet->getRowDimension($row_num)->setRowHeight(-1);

                $row_num++;
            }

            // Apply borders to the full block of records
            $sheet->getStyle('A13:Q' . ($row_num - 1))->applyFromArray($border_thin);
        }

        // 6. Professional Column Widths Setup
        $column_widths = [
            'A' => 32, // Name & Nature of Project
            'B' => 38, // Name of Implementer
            'C' => 16, // Barangay
            'D' => 20, // City/Municipality
            'E' => 18, // Province
            'F' => 12, // District
            'G' => 14, // Income Class
            'H' => 14, // Short-term
            'I' => 14, // Long-term
            'J' => 12, // Total
            'K' => 12, // Female
            'L' => 28, // Type
            'M' => 20, // Amount Released
            'N' => 15, // Date Released
            'O' => 22, // Fund Source
            'P' => 32, // Project Status
            'Q' => 22  // Convergence Initiatives
        ];

        foreach ($column_widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Stream file download headers
        $filename = "CQPR_Report_" . date('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
}