<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TUPAD Summary Report</title>
</head>
<body>
    <table>
        <tr>
            <td colspan="2" style="font-weight: bold; font-size: 14pt; color: #1e3a8a;">TUPAD Summary Report</td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 10pt; color: #64748b;">
                Coverage Period: <?= html_escape($start_date); ?> to <?= html_escape($end_date); ?> | 
                Breakdown: <?= html_escape(strtoupper($view_type)); ?>
            </td>
        </tr>
    </table>
    <br>

    <!-- 1. BENEFICIARY TYPE SUMMARY MATRIX TABLE -->
    <table border="1">
        <thead>
            <tr style="background-color: #2563eb; color: #ffffff;">
                <th style="text-align: left; font-weight: bold;">
                    <?= (isset($view_type) && $view_type == 'province_only') ? 'PROVINCE' : 'PROVINCE / MUNICIPALITY'; ?>
                </th>
                <?php if (!empty($report_data['bene_types'])): ?>
                    <?php foreach ($report_data['bene_types'] as $type): ?>
                        <th style="font-weight: bold;"><?= html_escape($type['bene_type_desc']); ?></th>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $column_totals = [];
            if (!empty($report_data['bene_types'])) {
                foreach ($report_data['bene_types'] as $type) {
                    $column_totals[$type['bene_type_id']] = 0;
                }
            }

            if (isset($view_type) && $view_type == 'province_only') {
                if (!empty($report_data['provinces'])): 
                    foreach ($report_data['provinces'] as $prov): 
            ?>
                <tr>
                    <td style="font-weight: bold; text-align: left;"><?= html_escape($prov['provDesc']); ?></td>
                    <?php 
                    foreach ($report_data['bene_types'] as $type): 
                        $count = isset($report_data['matrix'][$prov['provCode']][$type['bene_type_id']]) 
                                 ? $report_data['matrix'][$prov['provCode']][$type['bene_type_id']] 
                                 : 0;
                        $column_totals[$type['bene_type_id']] += $count;
                    ?>
                        <td><?= $count > 0 ? $count : '-'; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php 
                    endforeach; 
                endif;
            } else {
                if (!empty($report_data['provinces'])): 
                    foreach ($report_data['provinces'] as $prov): 
                        $prov_munis = array_filter($report_data['municipalities'], function($m) use ($prov) {
                            return $m['provCode'] == $prov['provCode'];
                        });

                        if (!isset($view_type) || $view_type == 'all'):
            ?>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="<?= count($report_data['bene_types']) + 1 ?>" style="text-align: left;">
                        <?= html_escape($prov['provDesc']); ?>
                    </td>
                </tr>
            <?php 
                        endif;

                        if (!empty($prov_munis)):
                            foreach ($prov_munis as $muni):
            ?>
                <tr>
                    <td style="text-align: left; padding-left: 15px;">
                        <?= (isset($view_type) && $view_type == 'municipality_only') ? html_escape($prov['provDesc'] . ' — ' . $muni['citymunDesc']) : html_escape($muni['citymunDesc']); ?>
                    </td>
                    <?php 
                    foreach ($report_data['bene_types'] as $type): 
                        $count = isset($report_data['matrix'][$prov['provCode']][$muni['cityCode']][$type['bene_type_id']]) 
                                 ? $report_data['matrix'][$prov['provCode']][$muni['cityCode']][$type['bene_type_id']] 
                                 : 0;
                        $column_totals[$type['bene_type_id']] += $count;
                    ?>
                        <td><?= $count > 0 ? $count : '-'; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php 
                            endforeach;
                        endif; 
                    endforeach; 
                endif; 
            }
            ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #0f172a; color: #ffffff; font-weight: bold;">
                <td style="text-align: left;">TOTAL:</td>
                <?php foreach ($report_data['bene_types'] as $type): ?>
                    <td><?= $column_totals[$type['bene_type_id']] > 0 ? $column_totals[$type['bene_type_id']] : '-'; ?></td>
                <?php endforeach; ?>
            </tr>
        </tfoot>
    </table>
    <br><br>

    <!-- 2. TUPAD CONVERGENCE SUMMARY REPORT MATRIX TABLE -->
    <table border="1">
        <thead>
            <tr style="background-color: #0f172a; color: #ffffff;">
                <th style="text-align: left; font-weight: bold;">
                    <?= (isset($view_type) && $view_type == 'province_only') ? 'PROVINCE' : 'PROVINCE / MUNICIPALITY'; ?>
                </th>
                <?php if (!empty($report_data['convergence_types'])): ?>
                    <?php foreach ($report_data['convergence_types'] as $conv_type): ?>
                        <th style="font-weight: bold;"><?= html_escape($conv_type['convergence_desc']); ?></th>
                    <?php endforeach; ?>
                <?php else: ?>
                    <th style="font-weight: bold;">Convergence Type</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $conv_column_totals = [];
            if (!empty($report_data['convergence_types'])) {
                foreach ($report_data['convergence_types'] as $conv_type) {
                    $conv_column_totals[$conv_type['convergence_id']] = 0;
                }
            }

            if (isset($view_type) && $view_type == 'province_only') {
                if (!empty($report_data['provinces'])): 
                    foreach ($report_data['provinces'] as $prov): 
            ?>
                <tr>
                    <td style="font-weight: bold; text-align: left;"><?= html_escape($prov['provDesc']); ?></td>
                    <?php 
                    if (!empty($report_data['convergence_types'])):
                        foreach ($report_data['convergence_types'] as $conv_type): 
                            $conv_count = isset($report_data['convergence_matrix'][$prov['provCode']][$conv_type['convergence_id']]) 
                                          ? $report_data['convergence_matrix'][$prov['provCode']][$conv_type['convergence_id']] 
                                          : 0;
                            $conv_column_totals[$conv_type['convergence_id']] += $conv_count;
                    ?>
                        <td><?= $conv_count > 0 ? $conv_count : '-'; ?></td>
                    <?php 
                        endforeach; 
                    endif;
                    ?>
                </tr>
            <?php 
                    endforeach; 
                endif;
            } else {
                if (!empty($report_data['provinces'])): 
                    foreach ($report_data['provinces'] as $prov): 
                        $prov_munis = array_filter($report_data['municipalities'], function($m) use ($prov) {
                            return $m['provCode'] == $prov['provCode'];
                        });

                        if (!isset($view_type) || $view_type == 'all'):
            ?>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="<?= count($report_data['convergence_types']) + 1 ?>" style="text-align: left;">
                        <?= html_escape($prov['provDesc']); ?>
                    </td>
                </tr>
            <?php 
                        endif;

                        if (!empty($prov_munis)):
                            foreach ($prov_munis as $muni):
            ?>
                <tr>
                    <td style="text-align: left; padding-left: 15px;">
                        <?= (isset($view_type) && $view_type == 'municipality_only') ? html_escape($prov['provDesc'] . ' — ' . $muni['citymunDesc']) : html_escape($muni['citymunDesc']); ?>
                    </td>
                    <?php 
                    if (!empty($report_data['convergence_types'])):
                        foreach ($report_data['convergence_types'] as $conv_type): 
                            $conv_count = isset($report_data['convergence_matrix'][$prov['provCode']][$muni['cityCode']][$conv_type['convergence_id']]) 
                                          ? $report_data['convergence_matrix'][$prov['provCode']][$muni['cityCode']][$conv_type['convergence_id']] 
                                          : 0;
                            $conv_column_totals[$conv_type['convergence_id']] += $conv_count;
                    ?>
                        <td><?= $conv_count > 0 ? $conv_count : '-'; ?></td>
                    <?php 
                        endforeach; 
                    endif;
                    ?>
                </tr>
            <?php 
                            endforeach;
                        endif; 
                    endforeach; 
                endif; 
            }
            ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #0f172a; color: #ffffff; font-weight: bold;">
                <td style="text-align: left;">TOTAL:</td>
                <?php if (!empty($report_data['convergence_types'])): ?>
                    <?php foreach ($report_data['convergence_types'] as $conv_type): ?>
                        <td><?= $conv_column_totals[$conv_type['convergence_id']] > 0 ? $conv_column_totals[$conv_type['convergence_id']] : '-'; ?></td>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tr>
        </tfoot>
    </table>
</body>
</html>