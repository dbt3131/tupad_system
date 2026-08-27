    <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Report extends CI_Controller { 

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Tupad_Report_Bene_Model');
        $this->load->helper(['url', 'form']);
    }
        
    public function tupad_summ_report() {
        // Get filter inputs from GET request
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        // Fetch report data based on filters
        $data['report_data'] = $this->Tupad_Report_Bene_Model->get_summary_report($start_date, $end_date);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $this->load->view('tupad/tupad_report', $data);
    }

    /**
     * Export Summary Report to Excel (.xls / .xlsx format)
     */
    public function export_excel() {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $report_data = $this->Tupad_Report_Bene_Model->get_summary_report($start_date, $end_date);
        
        $provinces = $report_data['provinces'];
        $bene_types = $report_data['bene_types'];
        $matrix = $report_data['matrix'];

        $filename = "TUPAD_Summary_Report_" . date('Y-m-d') . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        ?>
        <table border="1">
            <thead>
                <tr>
                    <th colspan="<?= count($bene_types) + 1 ?>" style="background-color: #1e3a8a; color: #ffffff; font-size: 14px; text-align: center;">
                        DOLE TUPAD SUMMARY REPORT
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
                    <th style="text-align: left;">PROVINCE</th>
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
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td style="text-align: left;">TOTAL:</td>
                    <?php foreach ($bene_types as $type): ?>
                        <td style="text-align: center;"><?= $column_totals[$type['bene_type_id']] > 0 ? $column_totals[$type['bene_type_id']] : '-'; ?></td>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        </table>
        <?php
        exit;
    }
}