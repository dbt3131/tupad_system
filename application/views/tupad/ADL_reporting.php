<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Breakdown Reporting - DOLE TUPAD</title>
    <!-- Select2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Buttons Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #1e3a8a;
            --primary-light: #2563eb;
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

        /* Advanced DataTables Styling Overrides */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.375rem;
            border: 1px solid var(--card-border);
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.15);
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.375rem;
            border: 1px solid var(--card-border);
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        .table-advanced th {
            background-color: #f1f5f9 !important;
            color: #334155;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--card-border) !important;
        }
        .table-advanced td {
            font-size: 0.875rem;
            color: #334155;
        }
        .pagination .page-item .page-link {
            font-size: 0.875rem;
            color: var(--primary-color);
            border-color: var(--card-border);
        }
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
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

    <div id="main-content">
        
        <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>ADL Financial Breakdown Report
                    </h3>
                    <p class="text-muted small mb-0">Filter by ADL number to review transaction distributions</p>
                </div>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="bi bi-printer me-1"></i> Print Report
                </button>
            </div>

            <!-- Filter Selection Card -->
            <div class="card border-0 shadow-sm mb-4 no-print">
               <div class="card-body">
                   <div class="row align-items-center">
                       <div class="col-md-12">
                           <label class="form-label fw-semibold small">Search & Select ADL Number:</label>
                           <select id="filter_adl_no" class="form-select" style="width: 100%;">
                               <option value="" selected disabled>-- Select or type ADL Number --</option>
                               <?php if (!empty($adl_list)): ?>
                                   <?php foreach ($adl_list as $item): ?>
                                       <option value="<?= html_escape($item['adl_no']); ?>" data-amount="<?= $item['adl_amount']; ?>">
                                           <?= html_escape($item['adl_no']); ?> (&#8369;<?= number_format($item['adl_amount'], 2); ?>)
                                       </option>
                                   <?php endforeach; ?>
                               <?php endif; ?>
                           </select>
                       </div>
                   </div>
               </div>
            </div>

            <!-- Report Display Container -->
            <div id="reportContainer" style="display: none;">
                
                <!-- Summary Metrics Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-primary text-white p-3">
                            <span class="small text-white-50 uppercase fw-semibold">Original ADL Amount</span>
                            <h3 class="fw-bold mb-0 mt-1" id="lblAdlAmount">&#8369;0.00</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-danger text-white p-3">
                            <span class="small text-white-50 uppercase fw-semibold">Total Deductions</span>
                            <h3 class="fw-bold mb-0 mt-1" id="lblTotalDeductions">&#8369;0.00</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm bg-success text-white p-3">
                            <span class="small text-white-50 uppercase fw-semibold">Remaining Balance</span>
                            <h3 class="fw-bold mb-0 mt-1" id="lblRemainingBalance">&#8369;0.00</h3>
                        </div>
                    </div>
                </div>

                <!-- 1. Original Summary Transaction Breakdown Table -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-list-check me-2"></i>Transaction Breakdown Summary for ADL: <span id="displayAdlNo" class="text-dark"></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Transaction Category / Component</th>
                                        <th class="text-end">Amount Breakdown</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Payout Service Cost</td>
                                        <td class="text-end text-danger" id="valServiceCost">&#8369;0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Payment Amount</td>
                                        <td class="text-end text-danger" id="valPaymentAmount">&#8369;0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">PPES Amount</td>
                                        <td class="text-end text-danger" id="valPpesAmount">&#8369;0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">GSIS Enrollment Amount</td>
                                        <td class="text-end text-danger" id="valGsisAmount">&#8369;0.00</td>
                                    </tr>
                                    <tr class="table-secondary fw-bold">
                                        <td>Total Combined Deductions</td>
                                        <td class="text-end text-danger" id="valTableTotalDeductions">&#8369;0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Initial Placeholder instruction -->
            <div id="placeholderContainer" class="card border-0 shadow-sm text-center py-5 mb-4">
                <div class="card-body py-5">
                    <i class="bi bi-arrow-up-circle fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Please select an ADL number from the dropdown filter above to view its transaction breakdown report.</p>
                </div>
            </div>

            <!-- 2. Detailed Implementation Breakdown Table -->
            <div id="detailedTableCard" class="card border-0 shadow-sm mb-4" style="display: none;">
                <div class="card-header bg-white py-3 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <h5 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-geo-alt me-2"></i>Detailed Implementation & Itemized Breakdown List
                    </h5>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Province Filter Dropdown -->
                            <div style="width: 180px;">
                                <select id="filter_province" class="form-select" style="width: 100%;">
                                    <option value="">-- All Provinces --</option>
                                    <?php if (!empty($provinces)): ?>
                                        <?php foreach ($provinces as $prov): ?>
                                            <option value="<?= html_escape($prov['provCode']); ?>">
                                                <?= html_escape($prov['provDesc']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <!-- Proponent Filter Dropdown -->
                            <div style="width: 180px;">
                                <select id="filter_proponent" class="form-select" style="width: 100%;">
                                    <option value="">-- All Proponents --</option>
                                    <?php if (!empty($proponents)): ?>
                                        <?php foreach ($proponents as $prop): ?>
                                            <option value="<?= html_escape($prop['proponent_id']); ?>">
                                                <?= html_escape($prop['proponent_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <!-- District Filter Dropdown -->
                            <div style="width: 140px;">
                                <select id="filter_district" class="form-select" style="width: 100%;">
                                    <option value="">-- All Districts --</option>
                                    <?php if (!empty($districts)): ?>
                                        <?php foreach ($districts as $dist): ?>
                                            <option value="<?= html_escape($dist['district_id']); ?>">
                                                District <?= html_escape($dist['district_no']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div id="exportButtonContainer"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="detailedTransactionsTable" class="table table-striped table-hover align-middle w-100 table-advanced">
                            <thead class="align-middle">
                                <tr>
                                    <th>Implementation Province</th>
                                    <th>Implementation Area</th>
                                    <th>ADL No.</th>
                                    <th>Reference No.</th>
                                    <th class="text-end">PPES Count</th>
                                    <th class="text-end">PPEs Amount</th>
                                    <th class="text-end">GSIS Enrollment Benefs</th>
                                    <th class="text-end">GSIS Enrollment Amount</th>
                                    <th class="text-end">Payout Service Fee</th>
                                    <th class="text-end">Salaries Amount</th>
                                </tr>
                            </thead>
                            <tbody id="detailedTransactionTableBody">
                                <!-- Populated dynamically via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTables JS CDN -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- JSZip (Required for Excel export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- DataTables Buttons JS & HTML5 Export Plugin -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2 with Bootstrap 5 Theme
        $('#filter_adl_no').select2({ theme: 'bootstrap-5', placeholder: '-- Select or type ADL Number --', allowClear: true });
        $('#filter_province').select2({ theme: 'bootstrap-5', placeholder: '-- All Provinces --', allowClear: true });
        $('#filter_proponent').select2({ theme: 'bootstrap-5', placeholder: '-- All Proponents --', allowClear: true });
        $('#filter_district').select2({ theme: 'bootstrap-5', placeholder: '-- All Districts --', allowClear: true });

        // Initialize DataTable with Advanced Layout & Formatting
        const table = $('#detailedTransactionsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ordering: true,
            responsive: true,
            dom: '<"row mb-3 align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row mt-3 align-items-center"<"col-md-5"i><"col-md-7 text-end"p>>',
            columnDefs: [
                {
                    targets: [4, 5, 6, 7, 8, 9],
                    className: 'text-end'
                },
                {
                    targets: [4, 6],
                    render: function (data, type, row) {
                        let num = parseInt(data);
                        if (isNaN(num)) return '0';
                        return num.toLocaleString('en-US');
                    }
                },
                {
                    targets: [5, 7, 8, 9],
                    render: function (data, type, row) {
                        let num = parseFloat(data);
                        if (isNaN(num)) return '0.00';
                        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }
            ],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Download Excel',
                    className: 'btn btn-success btn-sm shadow-sm',
                    title: '',
                    filename: 'ADL_Financial_Breakdown_Report',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var styles = xlsx.xl['styles.xml'];

                        // 1. Inject custom number format into styles.xml for decimal values (#,##0.00)
                        var numFmts = styles.getElementsByTagName('numFmts');
                        var numFmtId = 175;
                        if (numFmts.length === 0) {
                            var stylesheet = styles.getElementsByTagName('styleSheet')[0];
                            var newNumFmts = styles.createElement('numFmts');
                            newNumFmts.setAttribute('count', '1');
                            var newNumFmt = styles.createElement('numFmt');
                            newNumFmt.setAttribute('numFmtId', numFmtId);
                            newNumFmt.setAttribute('formatCode', '#,##0.00');
                            newNumFmts.appendChild(newNumFmt);
                            stylesheet.insertBefore(newNumFmts, stylesheet.firstChild);
                        } else {
                            var newNumFmt = styles.createElement('numFmt');
                            newNumFmt.setAttribute('numFmtId', numFmtId);
                            newNumFmt.setAttribute('formatCode', '#,##0.00');
                            numFmts[0].appendChild(newNumFmt);
                            numFmts[0].setAttribute('count', parseInt(numFmts[0].getAttribute('count') || 0) + 1);
                        }

                        // 2. Create custom cell style utilizing index 25 borders
                        var cellXfs = styles.getElementsByTagName('cellXfs')[0];
                        var customStyleIndex = cellXfs.childNodes.length;
                        var borderStyleRef = cellXfs.childNodes[25];
                        var newXf = borderStyleRef.cloneNode(true);
                        newXf.setAttribute('numFmtId', numFmtId);
                        newXf.setAttribute('applyNumberFormat', '1');
                        cellXfs.appendChild(newXf);
                        cellXfs.setAttribute('count', cellXfs.childNodes.length);

                        // Calculate column totals from applied data search
                        var totalPpesCount = 0;
                        var totalPpesAmt = 0;
                        var totalGsisBenefs = 0;
                        var totalGsisAmt = 0;
                        var totalService = 0;
                        var totalSalaries = 0;

                        table.rows({ search: 'applied' }).every(function () {
                            var data = this.data();
                            totalPpesCount += parseInt(data[4].toString().replace(/,/g, '')) || 0;
                            totalPpesAmt += parseFloat(data[5].toString().replace(/,/g, '')) || 0;
                            totalGsisBenefs += parseInt(data[6].toString().replace(/,/g, '')) || 0;
                            totalGsisAmt += parseFloat(data[7].toString().replace(/,/g, '')) || 0;
                            totalService += parseFloat(data[8].toString().replace(/,/g, '')) || 0;
                            totalSalaries += parseFloat(data[9].toString().replace(/,/g, '')) || 0;
                        });

                        // Shift rows down by 2 to accommodate title block
                        $('row', sheet).each(function () {
                            var r = parseInt($(this).attr('r')) + 2;
                            $(this).attr('r', r);
                            $(this).find('c').each(function () {
                                var cellRef = $(this).attr('r');
                                var col = cellRef.replace(/[0-9]/g, '');
                                $(this).attr('r', col + r);
                            });
                        });

                        // Process rows: Row 3 gets borders (s="25"), Row 4+ gets full numeric typing & formatting
                        $('row', sheet).each(function () {
                            var r = parseInt($(this).attr('r'));
                            
                            if (r === 3) {
                                // Apply clean thin borders to header row cells without altering text
                                $(this).find('c').each(function () {
                                    $(this).attr('s', '25');
                                });
                            } else if (r > 3) {
                                $(this).find('c').each(function (index) {
                                    var cell = $(this);
                                    var rawText = cell.text().replace(/,/g, '').trim();

                                    // Amount Columns (5, 7, 8, 9) -> Numbers with Decimals
                                    if (index === 5 || index === 7 || index === 8 || index === 9) {
                                        cell.attr('s', customStyleIndex);
                                        var numVal = parseFloat(rawText);
                                        if (!isNaN(numVal) && rawText !== '') {
                                            cell.attr('t', 'n');
                                            cell.empty().append('<v>' + numVal + '</v>');
                                        } else {
                                            cell.attr('t', 'inlineStr');
                                            cell.empty().append('<is><t>-</t></is>');
                                        }
                                    } 
                                    // Count Columns (4, 6) -> Integers
                                    else if (index === 4 || index === 6) {
                                        cell.attr('s', '25');
                                        var intVal = parseInt(rawText);
                                        if (!isNaN(intVal)) {
                                            cell.attr('t', 'n');
                                            cell.empty().append('<v>' + intVal + '</v>');
                                        } else {
                                            cell.attr('t', 'n');
                                            cell.empty().append('<v>0</v>');
                                        }
                                    } 
                                    // Text Columns -> Apply clean thin borders, preserve text strings
                                    else {
                                        cell.attr('s', '25');
                                    }
                                });
                            }
                        });

                        // Fetch selected ADL Number and Amount dynamically
                        var selectedOption = $('#filter_adl_no').find('option:selected');
                        var adlNoText = selectedOption.val() ? selectedOption.val() : 'N/A';
                        var adlAmountVal = selectedOption.data('amount') ? parseFloat(selectedOption.data('amount')) : 0;
                        
                        function formatNum(num) {
                            return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }

                        var titleText = 'ADL BREAKDOWN REPORT - ADL No: ' + adlNoText + ' (Amount: ₱' + formatNum(adlAmountVal) + ')';

                        // Insert title header at row 1
                        var row1 = '<row r="1">' +
                                       '<c t="inlineStr" r="A1" s="51">' +
                                           '<is><t>' + titleText + '</t></is>' +
                                       '</c>' +
                                   '</row>';
                        
                        $('sheetData', sheet).prepend(row1);

                        // Add Merge Cells rule to span title across columns A to J
                        var mergeCells = sheet.getElementsByTagName('mergeCells');
                        if (mergeCells.length === 0) {
                            var worksheet = sheet.getElementsByTagName('worksheet')[0];
                            worksheet.appendChild(sheet.createElement('mergeCells'));
                            mergeCells = sheet.getElementsByTagName('mergeCells');
                        }
                        var mergeCell = sheet.createElement('mergeCell');
                        mergeCell.setAttribute('ref', 'A1:J1');
                        mergeCells[0].appendChild(mergeCell);
                        mergeCells[0].setAttribute('count', parseInt(mergeCells[0].getAttribute('count') || 0) + 1);

                        // Append Grand Total row with proper numeric formats and clean borders
                        var lastRowElem = $('row', sheet).last();
                        var lastRowIdx = lastRowElem.length > 0 ? parseInt(lastRowElem.attr('r')) + 1 : 4;

                        var totalRow = '<row r="' + lastRowIdx + '">' +
                                           '<c t="inlineStr" r="A' + lastRowIdx + '" s="25"><is><t>GRAND TOTAL</t></is></c>' +
                                           '<c t="inlineStr" r="B' + lastRowIdx + '" s="25"><is><t></t></is></c>' +
                                           '<c t="inlineStr" r="C' + lastRowIdx + '" s="25"><is><t></t></is></c>' +
                                           '<c t="inlineStr" r="D' + lastRowIdx + '" s="25"><is><t></t></is></c>' +
                                           '<c t="n" r="E' + lastRowIdx + '" s="25"><v>' + totalPpesCount + '</v></c>' +
                                           '<c t="n" r="F' + lastRowIdx + '" s="' + customStyleIndex + '"><v>' + totalPpesAmt + '</v></c>' +
                                           '<c t="n" r="G' + lastRowIdx + '" s="25"><v>' + totalGsisBenefs + '</v></c>' +
                                           '<c t="n" r="H' + lastRowIdx + '" s="' + customStyleIndex + '"><v>' + totalGsisAmt + '</v></c>' +
                                           '<c t="n" r="I' + lastRowIdx + '" s="' + customStyleIndex + '"><v>' + totalService + '</v></c>' +
                                           '<c t="n" r="J' + lastRowIdx + '" s="' + customStyleIndex + '"><v>' + totalSalaries + '</v></c>' +
                                       '</row>';

                        $('sheetData', sheet).append(totalRow);
                    }
                }
            ]
        });

        table.buttons().container().appendTo('#exportButtonContainer');

        // Function to load report data via AJAX
        function loadReportData(adlNo, provinceCode = '', proponentName = '', districtNo = '') {
            $.ajax({
                url: "<?= site_url('adl/get_report_data'); ?>",
                type: "GET",
                data: { 
                    adl_no: adlNo, 
                    province: provinceCode,
                    proponent: proponentName,
                    district: districtNo
                },
                dataType: "json",
                success: function (response) {
                    if (response.status && response.data) {
                        const d = response.data;
                        
                        if (!provinceCode && !proponentName && !districtNo) {
                            $('#displayAdlNo').text(d.adl_no);
                            $('#lblAdlAmount').text('₱' + d.adl_amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#lblTotalDeductions').text('₱' + d.total_deductions.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#lblRemainingBalance').text('₱' + d.remaining_balance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                            $('#valServiceCost').text('₱' + d.total_service_cost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#valPaymentAmount').text('₱' + d.total_payment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#valPpesAmount').text('₱' + d.total_ppes_amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#valGsisAmount').text('₱' + d.total_gsis_amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#valTableTotalDeductions').text('₱' + d.total_deductions.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }

                        table.clear();

                        if (d.transactions && d.transactions.length > 0) {
                            d.transactions.forEach(function(tx) {
                                let provinceName = tx.implementation_province_name || tx.implementation_province;
                                let areaName = tx.implementation_area_name || tx.implementation_area;
                                
                                let ppesCount = parseInt(tx.ppes_count) || 0;
                                let ppesAmt = parseFloat(tx.ppes_amount) || 0;
                                let gsisBenefs = parseInt(tx.gsis_enrollment_benefs) || 0;
                                let gsisAmt = parseFloat(tx.gsis_enrollment_amount) || 0;
                                let serviceCost = parseFloat(tx.payout_service_cost) || 0;
                                let paymentAmt = parseFloat(tx.payment_amount) || 0;

                                table.row.add([
                                    provinceName,
                                    areaName,
                                    tx.adl_no,
                                    tx.implementation_reference_no,
                                    ppesCount,
                                    ppesAmt,
                                    gsisBenefs,
                                    gsisAmt,
                                    serviceCost,
                                    paymentAmt
                                ]);
                            });
                        }
                        
                        table.draw();

                        $('#placeholderContainer').hide();
                        $('#reportContainer').fadeIn();
                        $('#detailedTableCard').fadeIn();
                    } else {
                        alert('No transaction records found matching your filters.');
                    }
                },
                error: function () {
                    alert('Error fetching report details. Please try again.');
                }
            });
        }

        // Handle ADL selection change event
        $('#filter_adl_no').on('change', function () {
            const adlNo = $(this).val();

            if (!adlNo) {
                $('#reportContainer').hide();
                $('#detailedTableCard').hide();
                $('#placeholderContainer').fadeIn();
                $('#filter_province').val('').trigger('change.select2');
                $('#filter_proponent').val('').trigger('change.select2');
                $('#filter_district').val('').trigger('change.select2');
                return;
            }

            $('#filter_province').val('').trigger('change.select2');
            $('#filter_proponent').val('').trigger('change.select2');
            $('#filter_district').val('').trigger('change.select2');

            loadReportData(adlNo, '', '', '');
        });

        // Handle Filter Changes
        $('#filter_province, #filter_proponent, #filter_district').on('change', function () {
            const adlNo = $('#filter_adl_no').val();
            const provCode = $('#filter_province').val();
            const proponentName = $('#filter_proponent').val();
            const districtNo = $('#filter_district').val();

            if (adlNo) {
                loadReportData(adlNo, provCode, proponentName, districtNo);
            }
        });
    });
    </script>
</body>

</html>