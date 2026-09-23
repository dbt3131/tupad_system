<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Report extends CI_Controller { 

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Tupad_Report_Model');
         $this->load->model('Tupad_Report_Bene_Model');
        $this->load->helper(['url', 'form']);

         if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }
        
    public function tupad_summ_report() {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $view_type = $this->input->get('view_type') ? $this->input->get('view_type') : 'all';

        $data['report_data'] = $this->Tupad_Report_Bene_Model->get_summary_report($start_date, $end_date, $view_type);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['view_type'] = $view_type;

        $this->load->view('tupad/tupad_report', $data);
    }









public function export_excel() {
        $start_date    = $this->input->get('start_date');
        $end_date      = $this->input->get('end_date');
        $province_id   = $this->input->get('province'); 

        // Fetch data using your model
        $report_data = $this->Tupad_Report_Model->get_implementation_status_report($start_date, $end_date, $province_id);

        // Initialize PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Common border style definition
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF808080'], // Light black/gray border
                ],
            ],
        ];

        $is_summary = ($province_id === 'summary');

        if ($is_summary) {
            // --- SUMMARY EXCEL HEADERS ---
            $sheet->setCellValue('A1', 'PROVINCE');
            $sheet->setCellValue('B1', 'TARGET BENEFICIARIES');
            $sheet->setCellValue('C1', 'TARGET SUBSIDY');
            $sheet->setCellValue('D1', 'NO OF WORK DAYS');
            $sheet->setCellValue('E1', 'IMPLEMENTED BENEFICIARIES');
            $sheet->setCellValue('F1', 'IMPLEMENTED AMOUNT');
            $sheet->setCellValue('G1', 'PAYROLL BENEFICIARIES');
            $sheet->setCellValue('H1', 'PAYROLL AMOUNT');
            $sheet->setCellValue('I1', 'GSIS BENEFICIARIES');
            $sheet->setCellValue('J1', 'GSIS AMOUNT');
            $sheet->setCellValue('K1', 'ONGOING BENEFICIARIES');
            $sheet->setCellValue('L1', 'ONGOING AMOUNT');
            $sheet->setCellValue('M1', 'NOT YET BENEFICIARIES');
            $sheet->setCellValue('N1', 'NOT YET AMOUNT');

            // Aggregate summary per province
            $summary_grouped = [];
            foreach ($report_data as $row) {
                $prov_name = !empty($row['province_name']) ? $row['province_name'] : 'UNKNOWN PROVINCE';
                
                if (!isset($summary_grouped[$prov_name])) {
                    $summary_grouped[$prov_name] = [
                        'target_ben' => 0, 'target_subsidy' => 0, 'work_days' => 0,
                        'impl_ben' => 0, 'impl_amt' => 0,
                        'ppes_ben' => 0, 'ppes_amt' => 0,
                        'gsis_ben' => 0, 'gsis_amt' => 0,
                        'ongoing_ben' => 0, 'ongoing_amt' => 0,
                        'not_yet_ben' => 0, 'not_yet_amt' => 0
                    ];
                }

                $no_of_days  = $row['no_of_days'] ?? 0;
                $wage_amount = $row['wage_amount'] ?? 0;
                $target      = $row['target'] ?? 0;

                $summary_grouped[$prov_name]['target_ben'] += $target;
                $summary_grouped[$prov_name]['target_subsidy'] += ($row['subsidy_cost'] ?? 0);
                $summary_grouped[$prov_name]['work_days'] += $no_of_days;

                // GSIS calculation
                $is_gsis_empty = (empty($row['gsis_enrollment_date']) || $row['gsis_enrollment_date'] == '0000-00-00');
                $is_ppe_empty  = (empty($row['ppes_date_issued']) || $row['ppes_date_issued'] == '0000-00-00');
                if ($is_gsis_empty && !$is_ppe_empty) {
                    $g_ben = $row['ppes_count'] ?? 0;
                    $g_amt = ($g_ben * $no_of_days) * $wage_amount;
                } else {
                    $g_ben = 0; $g_amt = 0;
                }
                $summary_grouped[$prov_name]['gsis_ben'] += $g_ben;
                $summary_grouped[$prov_name]['gsis_amt'] += $g_amt;

                // Payroll calculation
                $completed_period = $row['completed_employment_amount'] ?? '';
                $is_completed_valid = (!empty($completed_period) && $completed_period !== '0000-00-00');
                if ($is_completed_valid) {
                    $p_ben = $row['ongoing_implementation_benefs'] ?? 0;
                    $p_amt = ($p_ben > 0) ? ($p_ben * $no_of_days) * $wage_amount : 0;
                } else {
                    $p_ben = 0; $p_amt = 0;
                }
                $summary_grouped[$prov_name]['ppes_ben'] += $p_ben;
                $summary_grouped[$prov_name]['ppes_amt'] += $p_amt;

                // Implemented calculation
                $raw_payout_date_impl = $row['payout_date'] ?? '';
                $is_valid_past_payout = false;
                if (!empty($raw_payout_date_impl) && $raw_payout_date_impl !== '0000-00-00') {
                    if (new DateTime($raw_payout_date_impl) <= new DateTime('now')) {
                        $is_valid_past_payout = true;
                    }
                }
                if ($is_valid_past_payout && !$is_completed_valid) {
                    $i_ben = $row['gsis_enrollment_benefs'] ?? 0;
                    $i_amt = ($i_ben > 0) ? ($i_ben * $no_of_days) * $wage_amount : 0;
                } else {
                    $i_ben = 0; $i_amt = 0;
                }
                $summary_grouped[$prov_name]['impl_ben'] += $i_ben;
                $summary_grouped[$prov_name]['impl_amt'] += $i_amt;

                // Ongoing calculation
                $gsis_date = $row['gsis_enrollment_date'] ?? '0000-00-00';
                if (!empty($gsis_date) && $gsis_date !== '0000-00-00' && !$is_valid_past_payout && !$is_completed_valid) {
                    $ongoing_start = $row['ongoing_implementation_start_date'] ?? '0000-00-00';
                    $ongoing_end   = $row['ongoing_implementation_end_date'] ?? '0000-00-00';
                    if ($ongoing_start == '0000-00-00' && $ongoing_end == '0000-00-00') {
                        $o_ben = $row['gsis_enrollment_benefs'] ?? 0;
                    } else {
                        $o_ben = $row['ongoing_implementation_benefs'] ?? 0;
                    }
                    $o_amt = ($o_ben > 0) ? ($o_ben * $no_of_days) * $wage_amount : 0;
                } else {
                    $o_ben = 0; $o_amt = 0;
                }
                $summary_grouped[$prov_name]['ongoing_ben'] += $o_ben;
                $summary_grouped[$prov_name]['ongoing_amt'] += $o_amt;

                // Not yet implemented calculation
                $is_empty_val = function($val) {
                    return empty($val) || $val === '0000-00-00' || $val === '0.00' || $val == 0;
                };
                $is_all_empty = (
                    $is_empty_val($row['ppes_date_issued'] ?? '') &&
                    $is_empty_val($row['orientation_date'] ?? '') &&
                    $is_empty_val($row['gsis_enrollment_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_start_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_end_date'] ?? '') &&
                    $is_empty_val($row['completed_employment_amount'] ?? '') &&
                    $is_empty_val($row['payout_amount'] ?? '') &&
                    $is_empty_val($row['payout_date'] ?? '')
                );
                if ($is_all_empty) {
                    $n_ben = $target;
                    $n_amt = ($target * $no_of_days) * $wage_amount;
                } else {
                    $n_ben = $row['not_yet_beneficiaries'] ?? 0;
                    $n_amt = $row['not_yet_amount'] ?? 0;
                }
                $summary_grouped[$prov_name]['not_yet_ben'] += $n_ben;
                $summary_grouped[$prov_name]['not_yet_amt'] += $n_amt;
            }

            // Write to Excel rows
            $row_num = 2;
            $grand_summary = [
                'target_ben' => 0, 'target_subsidy' => 0, 'work_days' => 0,
                'impl_ben' => 0, 'impl_amt' => 0,
                'ppes_ben' => 0, 'ppes_amt' => 0,
                'gsis_ben' => 0, 'gsis_amt' => 0,
                'ongoing_ben' => 0, 'ongoing_amt' => 0,
                'not_yet_ben' => 0, 'not_yet_amt' => 0
            ];

            foreach ($summary_grouped as $prov_name => $s) {
                $grand_summary['target_ben'] += $s['target_ben'];
                $grand_summary['target_subsidy'] += $s['target_subsidy'];
                $grand_summary['work_days'] += $s['work_days'];
                $grand_summary['impl_ben'] += $s['impl_ben'];
                $grand_summary['impl_amt'] += $s['impl_amt'];
                $grand_summary['ppes_ben'] += $s['ppes_ben'];
                $grand_summary['ppes_amt'] += $s['ppes_amt'];
                $grand_summary['gsis_ben'] += $s['gsis_ben'];
                $grand_summary['gsis_amt'] += $s['gsis_amt'];
                $grand_summary['ongoing_ben'] += $s['ongoing_ben'];
                $grand_summary['ongoing_amt'] += $s['ongoing_amt'];
                $grand_summary['not_yet_ben'] += $s['not_yet_ben'];
                $grand_summary['not_yet_amt'] += $s['not_yet_amt'];

                $sheet->setCellValue('A' . $row_num, $prov_name);
                $sheet->setCellValue('B' . $row_num, $s['target_ben']);
                $sheet->setCellValue('C' . $row_num, $s['target_subsidy']);
                $sheet->setCellValue('D' . $row_num, $s['work_days']);
                $sheet->setCellValue('E' . $row_num, $s['impl_ben'] > 0 ? $s['impl_ben'] : '');
                $sheet->setCellValue('F' . $row_num, $s['impl_amt'] > 0 ? $s['impl_amt'] : '');
                $sheet->setCellValue('G' . $row_num, $s['ppes_ben'] > 0 ? $s['ppes_ben'] : '');
                $sheet->setCellValue('H' . $row_num, $s['ppes_amt'] > 0 ? $s['ppes_amt'] : '');
                $sheet->setCellValue('I' . $row_num, $s['gsis_ben'] > 0 ? $s['gsis_ben'] : '');
                $sheet->setCellValue('J' . $row_num, $s['gsis_amt'] > 0 ? $s['gsis_amt'] : '');
                $sheet->setCellValue('K' . $row_num, $s['ongoing_ben'] > 0 ? $s['ongoing_ben'] : '');
                $sheet->setCellValue('L' . $row_num, $s['ongoing_amt'] > 0 ? $s['ongoing_amt'] : '');
                $sheet->setCellValue('M' . $row_num, $s['not_yet_ben'] > 0 ? $s['not_yet_ben'] : '');
                $sheet->setCellValue('N' . $row_num, $s['not_yet_amt'] > 0 ? $s['not_yet_amt'] : '');
                
                $row_num++;
            }

            // Grand Total row
            $sheet->setCellValue('A' . $row_num, 'GRAND TOTAL:');
            $sheet->setCellValue('B' . $row_num, $grand_summary['target_ben']);
            $sheet->setCellValue('C' . $row_num, $grand_summary['target_subsidy']);
            $sheet->setCellValue('D' . $row_num, $grand_summary['work_days']);
            $sheet->setCellValue('E' . $row_num, $grand_summary['impl_ben'] > 0 ? $grand_summary['impl_ben'] : '');
            $sheet->setCellValue('F' . $row_num, $grand_summary['impl_amt'] > 0 ? $grand_summary['impl_amt'] : '');
            $sheet->setCellValue('G' . $row_num, $grand_summary['ppes_ben'] > 0 ? $grand_summary['ppes_ben'] : '');
            $sheet->setCellValue('H' . $row_num, $grand_summary['ppes_amt'] > 0 ? $grand_summary['ppes_amt'] : '');
            $sheet->setCellValue('I' . $row_num, $grand_summary['gsis_ben'] > 0 ? $grand_summary['gsis_ben'] : '');
            $sheet->setCellValue('J' . $row_num, $grand_summary['gsis_amt'] > 0 ? $grand_summary['gsis_amt'] : '');
            $sheet->setCellValue('K' . $row_num, $grand_summary['ongoing_ben'] > 0 ? $grand_summary['ongoing_ben'] : '');
            $sheet->setCellValue('L' . $row_num, $grand_summary['ongoing_amt'] > 0 ? $grand_summary['ongoing_amt'] : '');
            $sheet->setCellValue('M' . $row_num, $grand_summary['not_yet_ben'] > 0 ? $grand_summary['not_yet_ben'] : '');
            $sheet->setCellValue('N' . $row_num, $grand_summary['not_yet_amt'] > 0 ? $grand_summary['not_yet_amt'] : '');

            // Apply borders to summary table range (A1 to N[row_num])
            $sheet->getStyle('A1:N' . $row_num)->applyFromArray($borderStyle);

        } else {
            // --- STANDARD DETAILED VIEW (All Provinces or Specific Province) ---
            $sheet->setCellValue('A1', 'ADL NO.');
            $sheet->setCellValue('B1', 'REFERENCE NO.');
            $sheet->setCellValue('C1', 'SPONSOR');
            $sheet->setCellValue('D1', 'PROVINCE');
            $sheet->setCellValue('E1', 'AREA DESCRIPTION');
            $sheet->setCellValue('F1', 'TARGET BENEFICIARIES');
            $sheet->setCellValue('G1', 'TARGET SUBSIDY');
            $sheet->setCellValue('H1', 'NO OF WORK DAYS');
            $sheet->setCellValue('I1', 'IMPLEMENTED BENEFICIARIES');
            $sheet->setCellValue('J1', 'IMPLEMENTED AMOUNT');
            $sheet->setCellValue('K1', 'PAYOUT DATE');
            $sheet->setCellValue('L1', 'PAYROLL BENEFICIARIES');
            $sheet->setCellValue('M1', 'PAYROLL AMOUNT');
            $sheet->setCellValue('N1', 'GSIS BENEFICIARIES');
            $sheet->setCellValue('O1', 'GSIS AMOUNT');
            $sheet->setCellValue('P1', 'ONGOING BENEFICIARIES');
            $sheet->setCellValue('Q1', 'ONGOING AMOUNT');
            $sheet->setCellValue('R1', 'EMPLOYMENT PERIOD');
            $sheet->setCellValue('S1', 'TARGET PAYOUT');
            $sheet->setCellValue('T1', 'NOT YET BENEFICIARIES');
            $sheet->setCellValue('U1', 'NOT YET AMOUNT');
            $sheet->setCellValue('V1', 'REMARKS');

            $total_target_ben = 0; $total_target_subsidy = 0;
            $total_impl_ben = 0; $total_impl_amt = 0;
            $total_ppes_ben = 0; $total_ppes_amt = 0;
            $total_gsis_ben = 0; $total_gsis_amt = 0;
            $total_ongoing_ben = 0; $total_ongoing_amt = 0;
            $total_not_yet_ben = 0; $total_not_yet_amt = 0;

            foreach ($report_data as $row) {
                $no_of_days  = $row['no_of_days'] ?? 0;
                $wage_amount = $row['wage_amount'] ?? 0;
                $target      = $row['target'] ?? 0;

                $total_target_ben += $target;
                $total_target_subsidy += ($row['subsidy_cost'] ?? 0);

                $is_gsis_empty = (empty($row['gsis_enrollment_date']) || $row['gsis_enrollment_date'] == '0000-00-00');
                $is_ppe_empty  = (empty($row['ppes_date_issued']) || $row['ppes_date_issued'] == '0000-00-00');
                
                if ($is_gsis_empty && !$is_ppe_empty) {
                    $g_ben = $row['ppes_count'] ?? 0;
                    $g_amt = ($g_ben * $no_of_days) * $wage_amount;
                } else {
                    $g_ben = 0; $g_amt = 0;
                }
                $total_gsis_ben += $g_ben;
                $total_gsis_amt += $g_amt;

                $completed_period = $row['completed_employment_amount'] ?? '';
                $is_completed_valid = (!empty($completed_period) && $completed_period !== '0000-00-00');

                if ($is_completed_valid) {
                    $p_ben = $row['ongoing_implementation_benefs'] ?? 0;
                    $p_amt = ($p_ben > 0) ? ($p_ben * $no_of_days) * $wage_amount : 0;
                } else {
                    $p_ben = 0; $p_amt = 0;
                }
                $total_ppes_ben += $p_ben;
                $total_ppes_amt += $p_amt;

                $raw_payout_date_impl = $row['payout_date'] ?? '';
                $is_valid_past_payout = false;
                if (!empty($raw_payout_date_impl) && $raw_payout_date_impl !== '0000-00-00') {
                    if (new DateTime($raw_payout_date_impl) <= new DateTime('now')) {
                        $is_valid_past_payout = true;
                    }
                }

                if ($is_valid_past_payout && !$is_completed_valid) {
                    $i_ben = $row['gsis_enrollment_benefs'] ?? 0;
                    $i_amt = ($i_ben > 0) ? ($i_ben * $no_of_days) * $wage_amount : 0;
                } else {
                    $i_ben = 0; $i_amt = 0;
                }
                $total_impl_ben += $i_ben;
                $total_impl_amt += $i_amt;

                $gsis_date = $row['gsis_enrollment_date'] ?? '0000-00-00';
                if (!empty($gsis_date) && $gsis_date !== '0000-00-00' && !$is_valid_past_payout && !$is_completed_valid) {
                    $ongoing_start = $row['ongoing_implementation_start_date'] ?? '0000-00-00';
                    $ongoing_end   = $row['ongoing_implementation_end_date'] ?? '0000-00-00';
                    if ($ongoing_start == '0000-00-00' && $ongoing_end == '0000-00-00') {
                        $o_ben = $row['gsis_enrollment_benefs'] ?? 0;
                    } else {
                        $o_ben = $row['ongoing_implementation_benefs'] ?? 0;
                    }
                    $o_amt = ($o_ben > 0) ? ($o_ben * $no_of_days) * $wage_amount : 0;
                } else {
                    $o_ben = 0; $o_amt = 0;
                }
                $total_ongoing_ben += $o_ben;
                $total_ongoing_amt += $o_amt;
                
                $is_empty_val = function($val) {
                    return empty($val) || $val === '0000-00-00' || $val === '0.00' || $val == 0;
                };

                $is_all_empty = (
                    $is_empty_val($row['ppes_date_issued'] ?? '') &&
                    $is_empty_val($row['orientation_date'] ?? '') &&
                    $is_empty_val($row['gsis_enrollment_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_start_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_end_date'] ?? '') &&
                    $is_empty_val($row['completed_employment_amount'] ?? '') &&
                    $is_empty_val($row['payout_amount'] ?? '') &&
                    $is_empty_val($row['payout_date'] ?? '')
                );

                if ($is_all_empty) {
                    $n_ben = $target;
                    $n_amt = ($target * $no_of_days) * $wage_amount;
                } else {
                    $n_ben = $row['not_yet_beneficiaries'] ?? 0;
                    $n_amt = $row['not_yet_amount'] ?? 0;
                }
                $total_not_yet_ben += $n_ben;
                $total_not_yet_amt += $n_amt;
            }

            $sheet->setCellValue('A2', 'GRAND TOTAL:');
            $sheet->setCellValue('F2', $total_target_ben);
            $sheet->setCellValue('G2', $total_target_subsidy);
            $sheet->setCellValue('I2', $total_impl_ben > 0 ? $total_impl_ben : '');
            $sheet->setCellValue('J2', $total_impl_amt > 0 ? $total_impl_amt : '');
            $sheet->setCellValue('L2', $total_ppes_ben > 0 ? $total_ppes_ben : '');
            $sheet->setCellValue('M2', $total_ppes_amt > 0 ? $total_ppes_amt : '');
            $sheet->setCellValue('N2', $total_gsis_ben > 0 ? $total_gsis_ben : '');
            $sheet->setCellValue('O2', $total_gsis_amt > 0 ? $total_gsis_amt : '');
            $sheet->setCellValue('P2', $total_ongoing_ben > 0 ? $total_ongoing_ben : '');
            $sheet->setCellValue('Q2', $total_ongoing_amt > 0 ? $total_ongoing_amt : '');
            $sheet->setCellValue('T2', $total_not_yet_ben > 0 ? $total_not_yet_ben : '');
            $sheet->setCellValue('U2', $total_not_yet_amt > 0 ? $total_not_yet_amt : '');

            $row_num = 3;
            foreach ($report_data as $row) {
                $no_of_days  = $row['no_of_days'] ?? 0;
                $wage_amount = $row['wage_amount'] ?? 0;
                $target      = $row['target'] ?? 0;

                $is_gsis_empty = (empty($row['gsis_enrollment_date']) || $row['gsis_enrollment_date'] == '0000-00-00');
                $is_ppe_empty  = (empty($row['ppes_date_issued']) || $row['ppes_date_issued'] == '0000-00-00');
                
                if ($is_gsis_empty && !$is_ppe_empty) {
                    $gsis_beneficiaries = $row['ppes_count'] ?? 0;
                    $gsis_amount = ($gsis_beneficiaries * $no_of_days) * $wage_amount;
                } else {
                    $gsis_beneficiaries = '';
                    $gsis_amount = '';
                }

                $completed_period = $row['completed_employment_amount'] ?? '';
                $is_completed_valid = (!empty($completed_period) && $completed_period !== '0000-00-00');

                if ($is_completed_valid) {
                    $ppes_beneficiaries = $row['ongoing_implementation_benefs'] ?? 0;
                    $ppes_amount = ($ppes_beneficiaries > 0) ? ($ppes_beneficiaries * $no_of_days) * $wage_amount : '';
                } else {
                    $ppes_beneficiaries = '';
                    $ppes_amount = '';
                }

                $raw_payout_date_impl = $row['payout_date'] ?? '';
                $is_valid_past_payout = false;
                if (!empty($raw_payout_date_impl) && $raw_payout_date_impl !== '0000-00-00') {
                    if (new DateTime($raw_payout_date_impl) <= new DateTime('now')) {
                        $is_valid_past_payout = true;
                    }
                }

                if ($is_valid_past_payout && !$is_completed_valid) {
                    $implemented_beneficiaries = $row['gsis_enrollment_benefs'] ?? '';
                    $implemented_amount = (!empty($implemented_beneficiaries) && $implemented_beneficiaries > 0) ? ($implemented_beneficiaries * $no_of_days) * $wage_amount : '';
                    $implemented_payout_date = $raw_payout_date_impl;
                } else {
                    $implemented_beneficiaries = '';
                    $implemented_amount = '';
                    $implemented_payout_date = '';
                }

                $gsis_date = $row['gsis_enrollment_date'] ?? '0000-00-00';
                if (!empty($gsis_date) && $gsis_date !== '0000-00-00' && !$is_valid_past_payout && !$is_completed_valid) {
                    $ongoing_start = $row['ongoing_implementation_start_date'] ?? '0000-00-00';
                    $ongoing_end   = $row['ongoing_implementation_end_date'] ?? '0000-00-00';
                    if ($ongoing_start == '0000-00-00' && $ongoing_end == '0000-00-00') {
                        $ongoing_beneficiaries = $row['gsis_enrollment_benefs'] ?? 0;
                    } else {
                        $ongoing_beneficiaries = $row['ongoing_implementation_benefs'] ?? 0;
                    }
                    $ongoing_amount    = ($ongoing_beneficiaries > 0) ? ($ongoing_beneficiaries * $no_of_days) * $wage_amount : '';
                    $employment_period = $row['orientation_employment_period'] ?? '';
                    $raw_payout_date   = $row['payout_date'] ?? '';
                    $target_payout     = (!empty($raw_payout_date) && $raw_payout_date !== '0000-00-00') ? $raw_payout_date : '';
                } else {
                    $ongoing_beneficiaries = '';
                    $ongoing_amount        = '';
                    $employment_period     = '';
                    $target_payout         = '';
                }
                
                $is_empty_val = function($val) {
                    return empty($val) || $val === '0000-00-00' || $val === '0.00' || $val == 0;
                };

                $is_all_empty = (
                    $is_empty_val($row['ppes_date_issued'] ?? '') &&
                    $is_empty_val($row['orientation_date'] ?? '') &&
                    $is_empty_val($row['gsis_enrollment_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_start_date'] ?? '') &&
                    $is_empty_val($row['ongoing_implementation_end_date'] ?? '') &&
                    $is_empty_val($row['completed_employment_amount'] ?? '') &&
                    $is_empty_val($row['payout_amount'] ?? '') &&
                    $is_empty_val($row['payout_date'] ?? '')
                );

                if ($is_all_empty) {
                    $not_yet_beneficiaries = $target;
                    $not_yet_amount = ($target * $no_of_days) * $wage_amount;
                } else {
                    $not_yet_beneficiaries = $row['not_yet_beneficiaries'] ?? '';
                    $not_yet_amount = $row['not_yet_amount'] ?? '';
                }

                $sheet->setCellValue('A' . $row_num, $row['adl_no'] ?? '');
                $sheet->setCellValue('B' . $row_num, $row['implementation_reference_no'] ?? '');
                $sheet->setCellValue('C' . $row_num, $row['implementation_sponsor'] ?? '');
                $sheet->setCellValue('D' . $row_num, $row['p_name'] ?? '');
                $sheet->setCellValue('E' . $row_num, $row['area_description'] ?? '');
                $sheet->setCellValue('F' . $row_num, $target);
                $sheet->setCellValue('G' . $row_num, $row['subsidy_cost'] ?? 0);
                $sheet->setCellValue('H' . $row_num, $no_of_days);
                $sheet->setCellValue('I' . $row_num, ($implemented_beneficiaries !== '' && $implemented_beneficiaries > 0) ? $implemented_beneficiaries : '');
                $sheet->setCellValue('J' . $row_num, ($implemented_amount !== '' && $implemented_amount > 0) ? $implemented_amount : '');
                $sheet->setCellValue('K' . $row_num, $implemented_payout_date);
                $sheet->setCellValue('L' . $row_num, ($ppes_beneficiaries !== '' && $ppes_beneficiaries > 0) ? $ppes_beneficiaries : '');
                $sheet->setCellValue('M' . $row_num, ($ppes_amount !== '' && $ppes_amount > 0) ? $ppes_amount : '');
                $sheet->setCellValue('N' . $row_num, ($gsis_beneficiaries !== '' && $gsis_beneficiaries > 0) ? $gsis_beneficiaries : '');
                $sheet->setCellValue('O' . $row_num, ($gsis_amount !== '' && $gsis_amount > 0) ? $gsis_amount : '');
                $sheet->setCellValue('P' . $row_num, ($ongoing_beneficiaries !== '' && $ongoing_beneficiaries > 0) ? $ongoing_beneficiaries : '');
                $sheet->setCellValue('Q' . $row_num, ($ongoing_amount !== '' && $ongoing_amount > 0) ? $ongoing_amount : '');
                $sheet->setCellValue('R' . $row_num, $employment_period);
                $sheet->setCellValue('S' . $row_num, $target_payout);
                $sheet->setCellValue('T' . $row_num, ($not_yet_beneficiaries !== '' && $not_yet_beneficiaries > 0) ? $not_yet_beneficiaries : '');
                $sheet->setCellValue('U' . $row_num, ($not_yet_amount !== '' && $not_yet_amount > 0) ? $not_yet_amount : '');
                $sheet->setCellValue('V' . $row_num, $row['remarks'] ?? '');

                $row_num++;
            }

            // Apply borders to detailed table range (A1 to V[row_num - 1])
            $sheet->getStyle('A1:V' . ($row_num - 1))->applyFromArray($borderStyle);
        }

        // --- AUTO-SIZE COLUMNS FOR PROFESSIONAL LOOK ---
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output / Download file
        $filename = 'Tupad_Report_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }




































    public function coa_tupad_report_page() {
   

        $this->load->view('tupad/tupad_coa_report');
    }

    public function coa_tupad_report() {
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');

    $data['report_data'] = $this->Tupad_Report_Model->get_coa_report_data($start_date, $end_date);
    $data['start_date'] = $start_date;
    $data['end_date'] = $end_date;

    // Fetch user details from session (adjust session keys based on your login implementation)
    $data['user_fullname'] = $this->session->userdata('fullname') ?? 'USER FULLNAME';
    $data['user_position'] = $this->session->userdata('position') ?? 'Position/Designation';

    $this->load->view('tupad/tupad_coa_report', $data);
}

public function export_coa_excel() {
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    
    // Get logged-in user ID from session (adjust session key if necessary, e.g., 'user_id' or 'id')
    $user_id = $this->session->userdata('id') ?? $this->session->userdata('user_id');

    $data['report_data'] = $this->Tupad_Report_Model->get_coa_report_data($start_date, $end_date);
    
    // Fetch joined user full name and description string
    $user_info = $this->Tupad_Report_Model->get_user_signature_details($user_id);
    
    $data['user_fullname'] = $user_info['user_fullname'] ?? 'ADMIN USER';
    $data['user_position'] = $user_info['position_description'] ?? 'Position Description';

    $data['start_date'] = $start_date;
    $data['end_date'] = $end_date;

    $filename = "COA_Quarterly_Report_" . date('Y-m-d') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $this->load->view('tupad/tupad_excel_coa_export', $data);
}




public function tupad_implementation_status_report() {
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    $province = $this->input->get('province');

    $data['report_data'] = [];
    if (!empty($start_date) && !empty($end_date)) {
        $data['report_data'] = $this->Tupad_Report_Model->get_implementation_status_report($start_date, $end_date, $province);
    }

    $data['provinces'] = $this->Tupad_Report_Model->get_provinces();
    $data['start_date'] = $start_date;
    $data['end_date'] = $end_date;
    $data['selected_province'] = $province;

    // Load your view file
    $this->load->view('tupad/tupad_implementation_status_report', $data);
}


public function export_implementation_status_excel() {
    $start_date = $this->input->get('start_date');
    $end_date = $this->input->get('end_date');
    $province = $this->input->get('province');

    $data['report_data'] = [];
    if (!empty($start_date) && !empty($end_date)) {
        $data['report_data'] = $this->Tupad_Report_Model->get_implementation_status_report($start_date, $end_date, $province);
    }

    $data['start_date'] = $start_date;
    $data['end_date'] = $end_date;

    $filename = "TUPAD_Implementation_Status_Report_" . date('Y-m-d') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $this->load->view('tupad/tupad_excel_implementation_status_export', $data);
}




    
}