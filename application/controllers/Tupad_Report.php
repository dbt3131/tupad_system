<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Report extends CI_Controller { 

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Tupad_Report_Model');
        $this->load->helper(['url', 'form']);
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
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $view_type = $this->input->get('view_type') ? $this->input->get('view_type') : 'all';

        $report_data = $this->Tupad_Report_Bene_Model->get_summary_report($start_date, $end_date, $view_type);
        
        $provinces = $report_data['provinces'];
        $municipalities = $report_data['municipalities'];
        $bene_types = $report_data['bene_types'];
        $convergence_types = $report_data['convergence_types'];
        $matrix = $report_data['matrix'];
        $convergence_matrix = $report_data['convergence_matrix'];

        $filename = "TUPAD_Summary_Report_" . ucfirst($view_type) . "_" . date('Y-m-d') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        ?>
        <!-- ================= 1. BENEFICIARY TYPE SUMMARY TABLE ================= -->
        <table border="1">
            <thead>
                <tr>
                    <th colspan="<?= count($bene_types) + 1 ?>" style="background-color: #1e3a8a; color: #ffffff; font-size: 14px; text-align: center;">
                        DOLE TUPAD BENEFICIARY TYPE SUMMARY REPORT (<?= strtoupper(str_replace('_', ' ', $view_type)) ?>)
                    </th>
                </tr>
                <?php if (!empty($start_date) && !empty($end_date)): ?>
                <tr>
                    <th colspan="<?= count($bene_types) + 1 ?>" style="text-align: center; font-style: italic;">
                        Period: <?= html_escape($start_date) ?> to <?= html_escape($end_date) ?>
                    </th>
                </tr>
                <?php endif; ?>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <th style="text-align: left;">
                        <?= ($view_type == 'province_only') ? 'PROVINCE' : 'PROVINCE / MUNICIPALITY' ?>
                    </th>
                    <?php foreach ($bene_types as $type): ?>
                        <th><?= html_escape($type['bene_type_desc']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $column_totals = [];
                foreach ($bene_types as $type) {
                    $column_totals[$type['bene_type_id']] = 0;
                }

                if ($view_type == 'province_only') {
                    foreach ($provinces as $prov):
                ?>
                    <tr>
                        <td style="font-weight: bold; text-align: left;"><?= html_escape($prov['provDesc']); ?></td>
                        <?php 
                        foreach ($bene_types as $type): 
                            $count = isset($matrix[$prov['provCode']][$type['bene_type_id']]) 
                                     ? $matrix[$prov['provCode']][$type['bene_type_id']] 
                                     : 0;
                            $column_totals[$type['bene_type_id']] += $count;
                        ?>
                            <td style="text-align: center;"><?= $count > 0 ? $count : '-'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php 
                    endforeach;
                } else {
                    foreach ($provinces as $prov):
                        $prov_munis = array_filter($municipalities, function($m) use ($prov) {
                            return $m['provCode'] == $prov['provCode'];
                        });

                        if ($view_type == 'all'):
                    ?>
                        <tr style="background-color: #e9ecef;">
                            <td colspan="<?= count($bene_types) + 1 ?>" style="font-weight: bold; text-align: left;">
                                <?= html_escape($prov['provDesc']); ?>
                            </td>
                        </tr>
                    <?php 
                        endif;

                        foreach ($prov_munis as $muni):
                    ?>
                        <tr>
                            <td style="text-align: left; <?= ($view_type == 'all') ? 'padding-left: 20px;' : '' ?>">
                                <?= ($view_type == 'municipality_only') ? html_escape($prov['provDesc'] . ' — ' . $muni['citymunDesc']) : html_escape($muni['citymunDesc']); ?>
                            </td>
                            <?php 
                            foreach ($bene_types as $type): 
                                $count = isset($matrix[$prov['provCode']][$muni['cityCode']][$type['bene_type_id']]) 
                                         ? $matrix[$prov['provCode']][$muni['cityCode']][$type['bene_type_id']] 
                                         : 0;
                                $column_totals[$type['bene_type_id']] += $count;
                            ?>
                                <td style="text-align: center;"><?= $count > 0 ? $count : '-'; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php 
                        endforeach;
                    endforeach;
                }
                ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #cbd5e1; font-weight: bold;">
                    <td style="text-align: left;">TOTAL:</td>
                    <?php foreach ($bene_types as $type): ?>
                        <td style="text-align: center;"><?= $column_totals[$type['bene_type_id']] > 0 ? $column_totals[$type['bene_type_id']] : '-'; ?></td>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        </table>

        <br><br>

        <!-- ================= 2. TUPAD CONVERGENCE SUMMARY TABLE ================= -->
        <table border="1">
            <thead>
                <tr>
                    <th colspan="<?= count($convergence_types) + 1 ?>" style="background-color: #343a40; color: #ffffff; font-size: 14px; text-align: center;">
                        DOLE TUPAD CONVERGENCE SUMMARY REPORT (<?= strtoupper(str_replace('_', ' ', $view_type)) ?>)
                    </th>
                </tr>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <th style="text-align: left;">
                        <?= ($view_type == 'province_only') ? 'PROVINCE' : 'PROVINCE / MUNICIPALITY' ?>
                    </th>
                    <?php foreach ($convergence_types as $conv_type): ?>
                        <th><?= html_escape($conv_type['convergence_desc']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $conv_column_totals = [];
                foreach ($convergence_types as $conv_type) {
                    $conv_column_totals[$conv_type['convergence_id']] = 0;
                }

                if ($view_type == 'province_only') {
                    foreach ($provinces as $prov):
                ?>
                    <tr>
                        <td style="font-weight: bold; text-align: left;"><?= html_escape($prov['provDesc']); ?></td>
                        <?php 
                        foreach ($convergence_types as $conv_type): 
                            $conv_count = isset($convergence_matrix[$prov['provCode']][$conv_type['convergence_id']]) 
                                          ? $convergence_matrix[$prov['provCode']][$conv_type['convergence_id']] 
                                          : 0;
                            $conv_column_totals[$conv_type['convergence_id']] += $conv_count;
                        ?>
                            <td style="text-align: center;"><?= $conv_count > 0 ? $conv_count : '-'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php 
                    endforeach;
                } else {
                    foreach ($provinces as $prov):
                        $prov_munis = array_filter($municipalities, function($m) use ($prov) {
                            return $m['provCode'] == $prov['provCode'];
                        });

                        if ($view_type == 'all'):
                    ?>
                        <tr style="background-color: #e9ecef;">
                            <td colspan="<?= count($convergence_types) + 1 ?>" style="font-weight: bold; text-align: left;">
                                <?= html_escape($prov['provDesc']); ?>
                            </td>
                        </tr>
                    <?php 
                        endif;

                        foreach ($prov_munis as $muni):
                    ?>
                        <tr>
                            <td style="text-align: left; <?= ($view_type == 'all') ? 'padding-left: 20px;' : '' ?>">
                                <?= ($view_type == 'municipality_only') ? html_escape($prov['provDesc'] . ' — ' . $muni['citymunDesc']) : html_escape($muni['citymunDesc']); ?>
                            </td>
                            <?php 
                            foreach ($convergence_types as $conv_type): 
                                $conv_count = isset($convergence_matrix[$prov['provCode']][$muni['cityCode']][$conv_type['convergence_id']]) 
                                              ? $convergence_matrix[$prov['provCode']][$muni['cityCode']][$conv_type['convergence_id']] 
                                              : 0;
                                $conv_column_totals[$conv_type['convergence_id']] += $conv_count;
                            ?>
                                <td style="text-align: center;"><?= $conv_count > 0 ? $conv_count : '-'; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php 
                        endforeach;
                    endforeach;
                }
                ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #cbd5e1; font-weight: bold;">
                    <td style="text-align: left;">TOTAL:</td>
                    <?php foreach ($convergence_types as $conv_type): ?>
                        <td style="text-align: center;"><?= $conv_column_totals[$conv_type['convergence_id']] > 0 ? $conv_column_totals[$conv_type['convergence_id']] : '-'; ?></td>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        </table>
        <?php
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











    
}