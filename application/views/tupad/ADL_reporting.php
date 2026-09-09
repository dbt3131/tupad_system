<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Breakdown Reporting - DOLE TUPAD</title>
<!-- Select2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-printer me-1"></i> Print Report
                </button>
            </div>

            <!-- Filter Selection Card -->
            <div class="card border-0 shadow-sm mb-4 no-print">
               <div class="card-body">
                   <div class="row align-items-center">
                       <div class="col-md-6">
                           <label class="form-label fw-semibold small">Search & Select ADL Number:</label>
                           <select id="filter_adl_no" class="form-select" style="width: 100%;">
                               <option value="" selected disabled>-- Select or type ADL Number --</option>
                               <?php if (!empty($adl_list)): ?>
                                   <?php foreach ($adl_list as $item): ?>
                                       <option value="<?= html_escape($item['adl_no']); ?>">
                                           <?= html_escape($item['adl_no']); ?> (&#8369;<?= number_format($item['adl_amount'], 2); ?>)
                                       </option>
                                   <?php endforeach; ?>
                               <?php endif; ?>
                           </select>
                       </div>
                   </div>
               </div>
            </div>

            <!-- Report Display Container (Hidden until selected) -->
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

                <!-- 2. New Detailed Implementation Breakdown Table (Province, Area, counts vs amounts) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-secondary">
                            <i class="bi bi-geo-alt me-2"></i>Detailed Implementation & Itemized Breakdown List
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th>Implementation Province / Area</th>
                                        <th>Reference No. / ADL No.</th>
                                        <th>PPES (Count vs Amount)</th>
                                        <th>GSIS Enrollment (Benefs vs Amount)</th>
                                        <th>Payout Service Fee</th>
                                        <th>Salaries Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="detailedTransactionTableBody">
                                    <!-- Populated dynamically via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Initial Placeholder instruction -->
            <div id="placeholderContainer" class="card border-0 shadow-sm text-center py-5">
                <div class="card-body py-5">
                    <i class="bi bi-arrow-up-circle fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Please select an ADL number from the dropdown filter above to view its transaction breakdown report.</p>
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

<script>
$(document).ready(function () {
    // Initialize Select2 with Bootstrap 5 Theme
    $('#filter_adl_no').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Select or type ADL Number --',
        allowClear: true
    });

    // Handle dropdown/typing selection change event
    $('#filter_adl_no').on('change', function () {
        const adlNo = $(this).val();

        if (!adlNo) {
            $('#reportContainer').hide();
            $('#placeholderContainer').fadeIn();
            return;
        }

        // Trigger AJAX report loading
        $.ajax({
            url: "<?= site_url('adl/get_report_data'); ?>",
            type: "GET",
            data: { adl_no: adlNo },
            dataType: "json",
            success: function (response) {
                if (response.status && response.data) {
                    const d = response.data;
                    
                    // Populate summary metric cards
                    $('#displayAdlNo').text(d.adl_no);
                    $('#lblAdlAmount').text('₱' + d.adl_amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#lblTotalDeductions').text('₱' + d.total_deductions.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#lblRemainingBalance').text('₱' + d.remaining_balance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    // Populate original detailed table breakdown rows (Summary)
                    $('#valServiceCost').text('₱' + d.total_service_cost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#valPaymentAmount').text('₱' + d.total_payment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#valPpesAmount').text('₱' + d.total_ppes_amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#valGsisAmount').text('₱' + d.total_gsis_amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#valTableTotalDeductions').text('₱' + d.total_deductions.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    // Populate the new detailed items table rows (Province, Area, ADL no, Counts vs Amounts)
                    let detailedHtml = '';
                    if (d.transactions && d.transactions.length > 0) {
                        d.transactions.forEach(function(tx) {
                            let provinceName = tx.implementation_province_name || tx.implementation_province;
                            let areaName = tx.implementation_area_name || tx.implementation_area;
                            
                            let ppesAmt = parseFloat(tx.ppes_amount) || 0;
                            let gsisAmt = parseFloat(tx.gsis_enrollment_amount) || 0;
                            let serviceCost = parseFloat(tx.payout_service_cost) || 0;
                            let paymentAmt = parseFloat(tx.payment_amount) || 0;

                            detailedHtml += `<tr>
                                <td>
                                    <strong>${areaName}</strong><br>
                                    <small class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i>${provinceName}</small>
                                </td>
                                <td>
                                    <strong>ADL:</strong> ${tx.adl_no}<br>
                                    <small class="text-muted">Ref: <code>${tx.implementation_reference_no}</code></small>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-info text-dark">${tx.ppes_count || 0} items</span><br>
                                    ₱${ppesAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-secondary">${tx.gsis_enrollment_benefs || 0} benefs</span><br>
                                    ₱${gsisAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                                </td>
                                <td class="text-end text-danger">₱${serviceCost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="text-end text-danger">₱${paymentAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            </tr>`;
                        });
                    } else {
                        detailedHtml = `<tr><td colspan="6" class="text-center text-muted py-3">No individual transaction mappings found for this ADL number.</td></tr>`;
                    }
                    $('#detailedTransactionTableBody').html(detailedHtml);

                    // Show report container and hide placeholder instructions
                    $('#placeholderContainer').hide();
                    $('#reportContainer').fadeIn();
                } else {
                    alert('No transaction records found for this ADL number.');
                }
            },
            error: function () {
                alert('Error fetching report details. Please try again.');
            }
        });
    });

    // Sidebar Toggle Handler
    $(document).on('click', '#sidebarToggle', function (e) {
        e.preventDefault();
        if ($(window).width() < 992) {
            $('#sidebar').toggleClass('show-mobile');
        } else {
            $('#sidebar').toggleClass('collapsed');
            $('#main-content').toggleClass('expanded');
        }
    });
});
</script>
</body>

</html>