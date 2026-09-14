<?php
// Helper functions to check data and apply styles automatically
function is_no_data($value) {
    $val = trim((string)$value);
    return ($val === '' || $val === '0000-00-00' || $val === '0.00' || $val === '0' || $val === null || $val === 'N/A');
}

function card_bg($value) {
    return is_no_data($value) ? 'bg-danger-subtle border border-danger border-opacity-25' : 'bg-light border border-light';
}

function display_val($value, $type = 'text') {
    if (is_no_data($value)) {
        return '<span class="text-danger fw-semibold fst-italic"><i class="bi bi-exclamation-circle me-1"></i>No Data</span>';
    }
    if ($type === 'currency') return number_format((float)$value, 2);
    if ($type === 'percent') return html_escape($value) . '%';
    return html_escape($value);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Implementation Details - DOLE TUPAD</title>
    
    <!-- Select2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery -->
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
            font-size: 14px;
        } 

        .record-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s ease-in-out;
        }

        .data-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
        }

        .data-value {
            font-size: 0.95rem;
            color: var(--text-main);
            font-weight: 500;
        }

        @media print {
            body { background-color: #ffffff !important; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; padding: 0 !important; }
            .record-card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        
        <!-- Sidebar -->
        <?php $this->load->view('templates/sidebar'); ?>

        <!-- Main Workspace Area -->
        <main class="p-3 p-md-4 flex-grow-1">
            
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1 text-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>ADL Implementation Record Details
                    </h3>
                    <p class="text-muted small mb-0">Department of Labor and Employment &bull; Transaction Overview</p>
                    <a href="javascript:window.close();" class="btn btn-sm btn-outline-secondary mb-1">
    <i class="bi bi-arrow-left me-1"></i> Back
</a>
                </div>

                <div>
                    <button onclick="window.print()" class="btn btn-primary shadow-sm px-4">
                        <i class="bi bi-printer me-2"></i>Print / Save PDF
                    </button>

             
                </div>
            </div>

            <!-- Content Area Card -->
            <div class="record-card p-4 p-lg-5 mb-4">
                
                <!-- Header / Title inside printable block -->
                <div class="text-center pb-4 mb-4 border-bottom">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">DOLE TUPAD Program</span>
                    <h4 class="fw-bold text-dark mb-1">ADL Implementation Record Details</h4>
                    <p class="text-muted small mb-0">Reference ID: <strong><?= display_val($transaction['implementation_reference_no']); ?></strong></p>
                </div>

                <!-- SECTION 1: General & Location Info -->
                <h5 class="text-primary fw-bold mb-3"><i class="bi bi-geo-alt me-2"></i>General & Geographic Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['adl_no']); ?>"><span class="data-label">ADL Number</span><span class="data-value text-primary fw-bold"><?= display_val($transaction['adl_no']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_reference_no']); ?>"><span class="data-label">Reference No</span><span class="data-value"><?= display_val($transaction['implementation_reference_no']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_province_name'] ?? 'N/A'); ?>"><span class="data-label">Province</span><span class="data-value"><?= display_val($transaction['implementation_province_name'] ?? 'N/A'); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_area_name'] ?? 'N/A'); ?>"><span class="data-label">Area / Municipality</span><span class="data-value"><?= display_val($transaction['implementation_area_name'] ?? 'N/A'); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_brgy_name'] ?? 'N/A'); ?>"><span class="data-label">Barangay</span><span class="data-value"><?= display_val($transaction['implementation_brgy_name'] ?? 'N/A'); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_district']); ?>"><span class="data-label">District</span><span class="data-value"><?= display_val($transaction['implementation_district']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_classification']); ?>"><span class="data-label">Classification</span><span class="data-value"><?= display_val($transaction['implementation_classification']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_proponent']); ?>"><span class="data-label">Proponent</span><span class="data-value"><?= display_val($transaction['implementation_proponent']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['implementation_sponsor']); ?>"><span class="data-label">Sponsor</span><span class="data-value"><?= display_val($transaction['implementation_sponsor']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['date_coordinated']); ?>"><span class="data-label">Date Coordinated</span><span class="data-value"><?= display_val($transaction['date_coordinated']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['remarks']); ?>"><span class="data-label">Remarks</span><span class="data-value"><?= display_val($transaction['remarks']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['wage_percentage']); ?>"><span class="data-label">Wage Percentage</span><span class="data-value"><?= display_val($transaction['wage_percentage'], 'percent'); ?></span></div></div>
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['gpai_info']); ?>"><span class="data-label">GPAI Info</span><span class="data-value"><?= display_val($transaction['gpai_info']); ?></span></div></div>
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['wage_info']); ?>"><span class="data-label">Wage Info</span><span class="data-value"><?= display_val($transaction['wage_info']); ?></span></div></div>
                </div>

                <!-- SECTION 2: Appraisal & PPES Details -->
                <h5 class="text-primary fw-bold mb-3 mt-4"><i class="bi bi-box-seam me-2"></i>Appraisal & PPES Issuance</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['appraisal_date_submitted']); ?>"><span class="data-label">Appraisal Date Submitted</span><span class="data-value"><?= display_val($transaction['appraisal_date_submitted']); ?></span></div></div>
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['appraisal_date_approved']); ?>"><span class="data-label">Appraisal Date Approved</span><span class="data-value"><?= display_val($transaction['appraisal_date_approved']); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['ppes_issuance_ris']); ?>"><span class="data-label">PPES Issuance RIS</span><span class="data-value"><?= display_val($transaction['ppes_issuance_ris']); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['ppes_date_issued']); ?>"><span class="data-label">PPES Date Issued</span><span class="data-value"><?= display_val($transaction['ppes_date_issued']); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['ppes_count']); ?>"><span class="data-label">PPES Count</span><span class="data-value"><?= display_val($transaction['ppes_count']); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['ppes_amount']); ?>"><span class="data-label">PPES Amount</span><span class="data-value"><?= display_val($transaction['ppes_amount'], 'currency'); ?></span></div></div>
                </div>

                <!-- SECTION 3: Orientation & GSIS Enrollment -->
                <h5 class="text-primary fw-bold mb-3 mt-4"><i class="bi bi-people me-2"></i>Orientation & GSIS Details</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['orientation_date']); ?>"><span class="data-label">Orientation Date</span><span class="data-value"><?= display_val($transaction['orientation_date']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['orientation_benefs']); ?>"><span class="data-label">Orientation Beneficiaries</span><span class="data-value"><?= display_val($transaction['orientation_benefs']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['orientation_employment_period']); ?>"><span class="data-label">Orientation Period</span><span class="data-value"><?= display_val($transaction['orientation_employment_period']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['gsis_enrollment_date']); ?>"><span class="data-label">GSIS Enrollment Date</span><span class="data-value"><?= display_val($transaction['gsis_enrollment_date']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['gsis_enrollment_benefs']); ?>"><span class="data-label">GSIS Beneficiaries</span><span class="data-value"><?= display_val($transaction['gsis_enrollment_benefs']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['gsis_enrollment_amount']); ?>"><span class="data-label">GSIS Amount</span><span class="data-value"><?= display_val($transaction['gsis_enrollment_amount'], 'currency'); ?></span></div></div>
                </div>

                <!-- SECTION 4: Implementation Status & Completion -->
                <h5 class="text-primary fw-bold mb-3 mt-4"><i class="bi bi-calendar-check me-2"></i>Implementation & Completion Status</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['ongoing_implementation_start_date']); ?>"><span class="data-label">Start Date (Ongoing)</span><span class="data-value"><?= display_val($transaction['ongoing_implementation_start_date']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['ongoing_implementation_end_date']); ?>"><span class="data-label">End Date (Ongoing)</span><span class="data-value"><?= display_val($transaction['ongoing_implementation_end_date']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['ongoing_implementation_benefs']); ?>"><span class="data-label">Ongoing Beneficiaries</span><span class="data-value"><?= display_val($transaction['ongoing_implementation_benefs']); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['completed_employment_period']); ?>"><span class="data-label">Completed Period</span><span class="data-value"><?= display_val($transaction['completed_employment_period']); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['completed_employment_benefs']); ?>"><span class="data-label">Completed Beneficiaries</span><span class="data-value"><?= display_val($transaction['completed_employment_benefs']); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['completed_employment_amount']); ?>"><span class="data-label">Completed Amount</span><span class="data-value"><?= display_val($transaction['completed_employment_amount'], 'currency'); ?></span></div></div>
                    <div class="col-md-3"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['completed_employment_documentation']); ?>"><span class="data-label">Completed Documentation</span><span class="data-value"><?= display_val($transaction['completed_employment_documentation']); ?></span></div></div>
                </div>

                <!-- SECTION 5: Payments & Payouts -->
                <h5 class="text-primary fw-bold mb-3 mt-4"><i class="bi bi-cash-stack me-2"></i>Payments & Payouts</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payment_alob_no']); ?>"><span class="data-label">Payment ALOB No</span><span class="data-value"><?= display_val($transaction['payment_alob_no']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payment_dv_no']); ?>"><span class="data-label">Payment DV No</span><span class="data-value"><?= display_val($transaction['payment_dv_no']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payment_check_no']); ?>"><span class="data-label">Payment Check No</span><span class="data-value"><?= display_val($transaction['payment_check_no']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payment_date']); ?>"><span class="data-label">Payment Date</span><span class="data-value"><?= display_val($transaction['payment_date']); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payment_amount']); ?>"><span class="data-label">Payment Amount</span><span class="data-value"><?= display_val($transaction['payment_amount'], 'currency'); ?></span></div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payout_date']); ?>"><span class="data-label">Payout Date</span><span class="data-value"><?= display_val($transaction['payout_date']); ?></span></div></div>
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payout_service_cost']); ?>"><span class="data-label">Payout Service Cost</span><span class="data-value"><?= display_val($transaction['payout_service_cost'], 'currency'); ?></span></div></div>
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['payout_method']); ?>"><span class="data-label">Payout Method</span><span class="data-value"><?= display_val($transaction['payout_method']); ?></span></div></div>
                </div>

                <!-- SECTION 6: Audit / Metadata Info -->
                <h5 class="text-primary fw-bold mb-3 mt-4"><i class="bi bi-shield-check me-2"></i>Metadata</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['encoded_date']); ?>"><span class="data-label">Encoded Date</span><span class="data-value"><?= display_val($transaction['encoded_date']); ?></span></div></div>
                    <div class="col-md-6"><div class="p-3 rounded-3 h-100 <?= card_bg($transaction['encoder_name']); ?>"><span class="data-label">Encoded By (User ID)</span><span class="data-value"><?= display_val($transaction['encoder_name']); ?></span></div></div>
                </div>

                <!-- Action Footer inside card -->
                <div class="mt-5 pt-4 text-center border-top no-print">
                    <button onclick="window.print()" class="btn btn-outline-primary px-4 rounded-pill">
                        <i class="bi bi-printer me-1"></i> Print / Save as PDF
                    </button>
                </div>

            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function () {
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