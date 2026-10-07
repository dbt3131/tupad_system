<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DOLE TUPAD - SPRS Report</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <!-- SheetJS with Style Support -->
     <!-- xlsx-js-style CDN -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.full.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #1e3a8a;
            --primary-hover: #172554;
            --accent-color: #2563eb;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        } 

        #sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            margin-left: calc(var(--sidebar-width) * -1);
        }

        #main-content.expanded {
            margin-left: 0;
        }

        .cqpr-table th, .cqpr-table td {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
                margin-left: 0 !important;
            }
            #sidebar.show-mobile {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0 !important;
            }
        }

        @media print {
            body { background-color: #ffffff; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; }
        }
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar'); ?>
    <?php $this->load->view('templates/sidebar'); ?>

    <div id="main-content">
        <main class="p-3 p-md-4 flex-grow-1">
  
            <!-- ================= REPORTS HUB PAGE ================= -->
            <div class="container-fluid px-2 py-2">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-dark mb-1">SPRS Report</h1>
                        <p class="text-muted mb-0">Access, filter, and monitor TUPAD project reports.</p>
                    </div>
                </div>

                <!-- Reminder Notice -->
                <div class="alert alert-info border-0 shadow-sm mb-4 no-print bg-white border-start border-primary border-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill text-primary fs-5 me-2"></i>
                        <div class="small">
                            <strong>Reminder:</strong> Information needed for this report are PPEs, GSIS, Service Cost and Final Wages Amount.
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="card border-0 shadow-sm mb-4 no-print">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">Start Date</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= isset($start_date) ? $start_date : '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= isset($end_date) ? $end_date : '' ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Province</label>
                                <select name="province" class="form-select form-select-sm">
                                    <option value="">-- ALL PROVINCES --</option>
                                    <?php foreach ($provinces as $prov): ?>
                                        <option value="<?= $prov['provCode'] ?>" <?= (isset($selected_province) && $selected_province == $prov['provCode']) ? 'selected' : '' ?>>
                                            <?= $prov['provDesc'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                         <div class="col-md-2 d-flex gap-2">
    <button type="submit" class="btn btn-primary btn-sm w-100">
        <i class="bi bi-filter"></i> Filter
    </button>
    <a href="<?= site_url('tupad_sprs') ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
        <i class="bi bi-arrow-counterclockwise"></i>
    </a>
    <!-- Excel Export Button -->
    <button type="button" id="exportExcel" class="btn btn-success btn-sm" title="Export to Excel">
        <i class="bi bi-file-earmark-excel"></i>
    </button>
</div>


                        </form>
                    </div>
                </div>

                <!-- Report Tables Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center align-middle cqpr-table">
                               <thead class="table-light">
    <tr>
        <!-- SUBSIDY HEADERS -->
        <th colspan="6" class="bg-secondary text-white">SUBSIDY <br> <font color="yellow">(From adl_transactions)</font></th>
        <!-- TOTAL BENEFS HEADERS -->
        <th colspan="6" class="bg-dark text-white">TOTAL BENEFS <br> <font color="yellow">(From adl_transactions)</font></th>
        <!-- FEMALE BENEFS HEADERS -->
        <th colspan="6" class="bg-secondary text-white">FEMALE BENEFS <br> <font color="yellow">(From adl_transactions)</font></th>
    </tr>
    <!-- Rest of your header rows remain the same -->
...
                                    <tr>
                                        <!-- Subsidy Subheaders -->
                                        <th rowspan="2">PROVINCE</th>
                                        <th colspan="2">2025 CONTINUING</th>
                                        <th colspan="2">2026 Fund</th>
                                        <th rowspan="2">Total (Per FO)</th>

                                        <!-- Total Benefs Subheaders -->
                                        <th rowspan="2">PROVINCE</th>
                                        <th colspan="2">2025 CONTINUING</th>
                                        <th colspan="2">2026 Fund</th>
                                        <th rowspan="2">Total (Per FO)</th>

                                        <!-- Female Benefs Subheaders -->
                                        <th rowspan="2">PROVINCE</th>
                                        <th colspan="2">2025 CONTINUING</th>
                                        <th colspan="2">2026 Fund</th>
                                        <th rowspan="2">Total (Per FO)</th>
                                    </tr>
                                    <tr>
                                        <!-- Subsidy columns -->
                                        <th>SHORT TERM</th>
                                        <th>LONG TERM</th>
                                        <th>SHORT TERM</th>
                                        <th>LONG TERM</th>
                                        
                                        <!-- Total Benefs columns -->
                                        <th>SHORT TERM</th>
                                        <th>LONG TERM</th>
                                        <th>SHORT TERM</th>
                                        <th>LONG TERM</th>

                                        <!-- Female Benefs columns -->
                                        <th>SHORT TERM</th>
                                        <th>LONG TERM</th>
                                        <th>SHORT TERM</th>
                                        <th>LONG TERM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Initialize Accumulators for Subsidy
                                    $t_sub_25_s = 0; $t_sub_25_l = 0; $t_sub_26_s = 0; $t_sub_26_l = 0;
                                    // Initialize Accumulators for Total Benefs
                                    $t_ben_25_s = 0; $t_ben_25_l = 0; $t_ben_26_s = 0; $t_ben_26_l = 0;
                                    // Initialize Accumulators for Female Benefs
                                    $t_fem_25_s = 0; $t_fem_25_l = 0; $t_fem_26_s = 0; $t_fem_26_l = 0;

                                    if (!empty($report_data)):
                                        foreach ($report_data as $row): 
                                            // Row Totals Per FO
                                            $sub_fo_total  = $row['sub_2025_short'] + $row['sub_2025_long'] + $row['sub_2026_short'] + $row['sub_2026_long'];
                                            $ben_fo_total  = $row['benef_2025_short'] + $row['benef_2025_long'] + $row['benef_2026_short'] + $row['benef_2026_long'];
                                            $fem_fo_total  = $row['female_2025_short'] + $row['female_2025_long'] + $row['female_2026_short'] + $row['female_2026_long'];

                                            // Add to Column Totals
                                            $t_sub_25_s += $row['sub_2025_short']; $t_sub_25_l += $row['sub_2025_long']; 
                                            $t_sub_26_s += $row['sub_2026_short']; $t_sub_26_l += $row['sub_2026_long'];

                                            $t_ben_25_s += $row['benef_2025_short']; $t_ben_25_l += $row['benef_2025_long']; 
                                            $t_ben_26_s += $row['benef_2026_short']; $t_ben_26_l += $row['benef_2026_long'];

                                            $t_fem_25_s += $row['female_2025_short']; $t_fem_25_l += $row['female_2025_long']; 
                                            $t_fem_26_s += $row['female_2026_short']; $t_fem_26_l += $row['female_2026_long'];
                                    ?>
                                    <tr>
                                        <!-- SUBSIDY COLUMNS -->
                                        <td class="text-start fw-semibold"><?= $row['provDesc']; ?></td>
                                        <td class="text-end"><?= $row['sub_2025_short'] > 0 ? number_format($row['sub_2025_short'], 2) : '-'; ?></td>
                                        <td class="text-end"><?= $row['sub_2025_long'] > 0 ? number_format($row['sub_2025_long'], 2) : '-'; ?></td>
                                        <td class="text-end"><?= $row['sub_2026_short'] > 0 ? number_format($row['sub_2026_short'], 2) : '-'; ?></td>
                                        <td class="text-end"><?= $row['sub_2026_long'] > 0 ? number_format($row['sub_2026_long'], 2) : '-'; ?></td>
                                        <td class="text-end fw-bold"><?= $sub_fo_total > 0 ? number_format($sub_fo_total, 2) : '-'; ?></td>

                                        <!-- TOTAL BENEFS COLUMNS -->
                                        <td class="text-start fw-semibold bg-light"><?= $row['provDesc']; ?></td>
                                        <td><?= $row['benef_2025_short'] > 0 ? number_format($row['benef_2025_short']) : '-'; ?></td>
                                        <td><?= $row['benef_2025_long'] > 0 ? number_format($row['benef_2025_long']) : '-'; ?></td>
                                        <td><?= $row['benef_2026_short'] > 0 ? number_format($row['benef_2026_short']) : '-'; ?></td>
                                        <td><?= $row['benef_2026_long'] > 0 ? number_format($row['benef_2026_long']) : '-'; ?></td>
                                        <td class="fw-bold"><?= $ben_fo_total > 0 ? number_format($ben_fo_total) : '-'; ?></td>

                                        <!-- FEMALE BENEFS COLUMNS -->
                                        <td class="text-start fw-semibold"><?= $row['provDesc']; ?></td>
                                        <td><?= $row['female_2025_short'] > 0 ? number_format($row['female_2025_short']) : '-'; ?></td>
                                        <td><?= $row['female_2025_long'] > 0 ? number_format($row['female_2025_long']) : '-'; ?></td>
                                        <td><?= $row['female_2026_short'] > 0 ? number_format($row['female_2026_short']) : '-'; ?></td>
                                        <td><?= $row['female_2026_long'] > 0 ? number_format($row['female_2026_long']) : '-'; ?></td>
                                        <td class="fw-bold"><?= $fem_fo_total > 0 ? number_format($fem_fo_total) : '-'; ?></td>
                                    </tr>
                                    <?php 
                                        endforeach; 
                                    else:
                                    ?>
                                    <tr>
                                        <td colspan="17" class="text-center text-muted py-3">No records found matching the selected filters.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="table-warning fw-bold">
                                    <tr>
                                        <!-- SUBSIDY TOTALS -->
                                        <td class="text-start">TOTAL</td>
                                        <td class="text-end"><?= number_format($t_sub_25_s, 2); ?></td>
                                        <td class="text-end"><?= number_format($t_sub_25_l, 2); ?></td>
                                        <td class="text-end"><?= number_format($t_sub_26_s, 2); ?></td>
                                        <td class="text-end"><?= number_format($t_sub_26_l, 2); ?></td>
                                        <td class="text-end"><?= number_format($t_sub_25_s + $t_sub_25_l + $t_sub_26_s + $t_sub_26_l, 2); ?></td>

                                        <!-- TOTAL BENEFS TOTALS -->
                                        <td class="text-start">TOTAL</td>
                                        <td><?= number_format($t_ben_25_s); ?></td>
                                        <td><?= number_format($t_ben_25_l); ?></td>
                                        <td><?= number_format($t_ben_26_s); ?></td>
                                        <td><?= number_format($t_ben_26_l); ?></td>
                                        <td><?= number_format($t_ben_25_s + $t_ben_25_l + $t_ben_26_s + $t_ben_26_l); ?></td>

                                        <!-- FEMALE BENEFS TOTALS -->
                                        <td class="text-start">TOTAL</td>
                                        <td><?= number_format($t_fem_25_s); ?></td>
                                        <td><?= number_format($t_fem_25_l); ?></td>
                                        <td><?= number_format($t_fem_26_s); ?></td>
                                        <td><?= number_format($t_fem_26_l); ?></td>
                                        <td><?= number_format($t_fem_25_s + $t_fem_25_l + $t_fem_26_s + $t_fem_26_l); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; <?= date('Y'); ?> Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    // Toggle Sidebar
    $(document).on('click', '#sidebarToggle', function (e) {
        e.preventDefault();
        if ($(window).width() < 992) {
            $('#sidebar').toggleClass('show-mobile');
        } else {
            $('#sidebar').toggleClass('collapsed');
            $('#main-content').toggleClass('expanded');
        }
    });

    // Export Table to Excel with Matching Styling and Autofit Columns
    $('#exportExcel').on('click', function () {
        if (typeof XLSX === 'undefined' || typeof XLSX.writeFile === 'undefined') {
            alert('Excel library failed to load. Please check your internet connection or CDN link.');
            return;
        }

        var rowCount = $('.cqpr-table tbody tr').length;
        if (rowCount === 1 && $('.cqpr-table tbody tr td').hasClass('text-muted')) {
            alert('No data available to export.');
            return;
        }

        // Define styling objects matching your Bootstrap theme
        var headerStyleGray = {
            font: { name: "Arial", sz: 10, bold: true, color: { rgb: "FFFFFF" } },
            fill: { fgColor: { rgb: "6C757D" } }, // Secondary Gray
            alignment: { horizontal: "center", vertical: "center", wrapText: true },
            border: {
                top: { style: "thin", color: { rgb: "CCCCCC" } },
                bottom: { style: "thin", color: { rgb: "CCCCCC" } },
                left: { style: "thin", color: { rgb: "CCCCCC" } },
                right: { style: "thin", color: { rgb: "CCCCCC" } }
            }
        };

        var headerStyleDark = {
            font: { name: "Arial", sz: 10, bold: true, color: { rgb: "FFFFFF" } },
            fill: { fgColor: { rgb: "212529" } }, // Dark Theme
            alignment: { horizontal: "center", vertical: "center", wrapText: true },
            border: {
                top: { style: "thin", color: { rgb: "CCCCCC" } },
                bottom: { style: "thin", color: { rgb: "CCCCCC" } },
                left: { style: "thin", color: { rgb: "CCCCCC" } },
                right: { style: "thin", color: { rgb: "CCCCCC" } }
            }
        };

        var footerStyle = {
            font: { name: "Arial", sz: 10, bold: true, color: { rgb: "000000" } },
            fill: { fgColor: { rgb: "FFC107" } }, // Warning Yellow
            alignment: { horizontal: "center", vertical: "center" },
            border: {
                top: { style: "medium", color: { rgb: "000000" } },
                bottom: { style: "medium", color: { rgb: "000000" } },
                left: { style: "thin", color: { rgb: "CCCCCC" } },
                right: { style: "thin", color: { rgb: "CCCCCC" } }
            }
        };

        var cellDataStyleLeft = {
            font: { name: "Arial", sz: 9 },
            alignment: { horizontal: "left", vertical: "center" },
            border: { top: { style: "thin", color: { rgb: "EFEFEF" } }, bottom: { style: "thin", color: { rgb: "EFEFEF" } }, left: { style: "thin", color: { rgb: "EFEFEF" } }, right: { style: "thin", color: { rgb: "EFEFEF" } } }
        };

        var cellDataStyleRight = {
            font: { name: "Arial", sz: 9 },
            alignment: { horizontal: "right", vertical: "center" },
            border: { top: { style: "thin", color: { rgb: "EFEFEF" } }, bottom: { style: "thin", color: { rgb: "EFEFEF" } }, left: { style: "thin", color: { rgb: "EFEFEF" } }, right: { style: "thin", color: { rgb: "EFEFEF" } } }
        };

        var cellDataStyleRightBold = {
            font: { name: "Arial", sz: 9, bold: true },
            alignment: { horizontal: "right", vertical: "center" },
            border: { top: { style: "thin", color: { rgb: "EFEFEF" } }, bottom: { style: "thin", color: { rgb: "EFEFEF" } }, left: { style: "thin", color: { rgb: "EFEFEF" } }, right: { style: "thin", color: { rgb: "EFEFEF" } } }
        };

        // Construct rows manually
        var ws_data = [];

        // Row 1: Main Categories
        ws_data.push(["SUBSIDY", "", "", "", "", "", "TOTAL BENEFS", "", "", "", "", "", "FEMALE BENEFS", "", "", "", "", ""]);
        // Row 2: Sub-headers tier 1
        ws_data.push(["PROVINCE", "2025 CONTINUING", "", "2026 Fund", "", "Total (Per FO)", "PROVINCE", "2025 CONTINUING", "", "2026 Fund", "", "Total (Per FO)", "PROVINCE", "2025 CONTINUING", "", "2026 Fund", "", "Total (Per FO)"]);
        // Row 3: Sub-headers tier 2
        ws_data.push(["", "SHORT TERM", "LONG TERM", "SHORT TERM", "LONG TERM", "", "", "SHORT TERM", "LONG TERM", "SHORT TERM", "LONG TERM", "", "", "SHORT TERM", "LONG TERM", "SHORT TERM", "LONG TERM", ""]);

        // Extract body rows
        $('.cqpr-table tbody tr').each(function () {
            var cols = [];
            $(this).find('td').each(function () {
                cols.push($(this).text().trim());
            });
            if (cols.length > 0) {
                ws_data.push(cols);
            }
        });

        // Extract footer rows
        $('.cqpr-table tfoot tr').each(function () {
            var cols = [];
            $(this).find('td').each(function () {
                cols.push($(this).text().trim());
            });
            if (cols.length > 0) {
                ws_data.push(cols);
            }
        });

        var ws = XLSX.utils.aoa_to_sheet(ws_data);

        // Apply Merges
        ws['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 5 } },  // SUBSIDY
            { s: { r: 0, c: 6 }, e: { r: 0, c: 11 } }, // TOTAL BENEFS
            { s: { r: 0, c: 12 }, e: { r: 0, c: 17 } } // FEMALE BENEFS
        ];

        // Calculate and apply Autofit Column Widths dynamically
        var colWidths = [];
        var range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let maxLen = 10; // Minimum default width
            for (let R = range.s.r; R <= range.e.r; ++R) {
                let cell_address = XLSX.utils.encode_cell({ r: R, c: C });
                if (ws[cell_address] && ws[cell_address].v) {
                    let valLen = ws[cell_address].v.toString().length;
                    if (valLen > maxLen) {
                        maxLen = valLen;
                    }
                }
            }
            colWidths.push({ wch: maxLen + 4 }); // Add comfortable padding
        }
        ws['!cols'] = colWidths;

        // Apply styles to cells
        for (let R = range.s.r; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                let cell_address = XLSX.utils.encode_cell({ r: R, c: C });
                if (!ws[cell_address]) continue;

                if (R === 0) {
                    ws[cell_address].s = (C === 6) ? headerStyleDark : headerStyleGray;
                } else if (R === 1 || R === 2) {
                    ws[cell_address].s = (C >= 6 && C <= 11) ? headerStyleDark : headerStyleGray;
                } else if (R === range.e.r) {
                    ws[cell_address].s = footerStyle;
                } else {
                    let isProvinceCol = (C === 0 || C === 6 || C === 12);
                    let isTotalCol = (C === 5 || C === 11 || C === 17);

                    if (isProvinceCol) {
                        ws[cell_address].s = cellDataStyleLeft;
                    } else if (isTotalCol) {
                        ws[cell_address].s = cellDataStyleRightBold;
                    } else {
                        ws[cell_address].s = cellDataStyleRight;
                    }
                }
            }
        }

        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "SPRS Report");

        var filename = "SPRS_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx";
        XLSX.writeFile(wb, filename);
    });
});
</script>

</body>

</html>