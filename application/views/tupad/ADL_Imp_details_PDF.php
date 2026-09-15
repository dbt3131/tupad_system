<?php
// Helper functions to check data and apply styles automatically
function is_no_data($value) {
    $val = trim((string)$value);
    return ($val === '' || $val === '0000-00-00' || $val === '0.00' || $val === '0' || $val === null || $val === 'N/A');
}

function display_val($value, $type = 'text') {
    if (is_no_data($value)) {
        return '<span class="text-danger fw-semibold fst-italic small"><i class="bi bi-exclamation-circle me-1"></i>No Data</span>';
    }
    if ($type === 'currency') return '₱ ' . number_format((float)$value, 2);
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
    
    <!-- Fonts & Bootstrap 5 Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --primary-color: #1e3a8a;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            font-size: 13.5px;
        } 

        .record-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .section-header {
            font-size: 0.9rem;
            letter-spacing: 0.03em;
            background-color: #2e3b49;
            color: #e1e1e1;
            border-left: 4px solid var(--primary-color);
            padding: 8px 12px;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0 6px 6px 0;
        }

        .table-details th {
            width: 25%;
            color: var(--text-muted);
            font-weight: 600;
            background-color: #f8f8f8 !important;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .table-details td {
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
                    <a href="javascript:window.close();" class="btn btn-sm btn-outline-secondary mt-2">
                      <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
                <div>
                    <a href="<?= site_url('adl/export_transaction_excel/' . $transaction['adl_transact_id']); ?>" class="btn btn-success shadow-sm px-4">
                        <i class="bi bi-file-earmark-excel me-2"></i>Download Excel
                    </a>
                </div>
            </div>

            <!-- Content Area Card -->
            <div class="record-card p-4 p-lg-4 mb-4">
                
                <!-- Top Status Badge Banner -->
                <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                    <div>
                        <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill fw-semibold">DOLE TUPAD Program</span>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Reference ID:</span> 
                        <strong class="text-dark"><?= display_val($transaction['implementation_reference_no']); ?></strong>
                    </div>
                </div>

                <!-- SECTION 1: General & Geographic Information -->
                <div class="section-header"><i class="bi bi-geo-alt me-2"></i>General & Geographic Information</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-details align-middle">
                        <tbody>
                            <tr>
                                <th>ADL Number</th>
                                <td><?= display_val($transaction['adl_no']); ?></td>
                                <th>Reference No</th>
                                <td><?= display_val($transaction['implementation_reference_no']); ?></td>
                            </tr>
                            <tr>
                                <th>Province</th>
                                <td><?= display_val($transaction['implementation_province_name'] ?? 'N/A'); ?></td>
                                <th>Area / Municipality</th>
                                <td><?= display_val($transaction['implementation_area_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Barangay</th>
                                <td><?= display_val($transaction['implementation_brgy_name'] ?? 'N/A'); ?></td>
                                <th>District</th>
                                <td><?= display_val($transaction['implementation_district']); ?></td>
                            </tr>
                            <tr>
                                <th>Classification</th>
                                <td><?= display_val($transaction['implementation_classification']); ?></td>
                                <th>Date Coordinated</th>
                                <td><?= display_val($transaction['date_coordinated']); ?></td>
                            </tr>
                            <tr>
                                <th>Proponent</th>
                                <td><?= display_val($transaction['proponent_name']); ?></td>
                                <th>Sponsor</th>
                                <td><?= display_val($transaction['implementation_sponsor']); ?></td>
                            </tr>
                            <tr>
                                <th>Wage Percentage</th>
                                <td><?= display_val($transaction['wage_percentage'], 'percent'); ?></td>
                                <th>Remarks</th>
                                <td><?= display_val($transaction['remarks']); ?></td>
                            </tr>
                            <tr>
                                <th>GPAI Info</th>
                                <td colspan="3"><?= display_val($transaction['gpai_info']); ?></td>
                            </tr>
                            <tr>
                                <th>Wage Info</th>
                                <td colspan="3"><?= display_val($transaction['wage_info']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SECTION 2: Appraisal & PPES Details -->
                <div class="section-header"><i class="bi bi-box-seam me-2"></i>Appraisal & PPES Issuance</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-details align-middle">
                        <tbody>
                            <tr>
                                <th>Appraisal Date Submitted</th>
                                <td><?= display_val($transaction['appraisal_date_submitted']); ?></td>
                                <th>Appraisal Date Approved</th>
                                <td><?= display_val($transaction['appraisal_date_approved']); ?></td>
                            </tr>
                            <tr>
                                <th>PPES Issuance RIS</th>
                                <td><?= display_val($transaction['ppes_issuance_ris']); ?></td>
                                <th>PPES Date Issued</th>
                                <td><?= display_val($transaction['ppes_date_issued']); ?></td>
                            </tr>
                            <tr>
                                <th>PPES Count</th>
                                <td><?= display_val($transaction['ppes_count']); ?></td>
                                <th>PPES Amount</th>
                                <td class="fw-bold text-success"><?= display_val($transaction['ppes_amount'], 'currency'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SECTION 3: Orientation & GSIS Enrollment -->
                <div class="section-header"><i class="bi bi-people me-2"></i>Orientation & GSIS Details</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-details align-middle">
                        <tbody>
                            <tr>
                                <th>Orientation Date</th>
                                <td><?= display_val($transaction['orientation_date']); ?></td>
                                <th>Orientation Period</th>
                                <td><?= display_val($transaction['orientation_employment_period']); ?></td>
                            </tr>
                            <tr>
                                <th>Orientation Beneficiaries</th>
                                <td colspan="3"><?= display_val($transaction['orientation_benefs']); ?></td>
                            </tr>
                            <tr>
                                <th>GSIS Enrollment Date</th>
                                <td><?= display_val($transaction['gsis_enrollment_date']); ?></td>
                                <th>GSIS Beneficiaries</th>
                                <td><?= display_val($transaction['gsis_enrollment_benefs']); ?></td>
                            </tr>
                            <tr>
                                <th>GSIS Amount</th>
                                <td colspan="3" class="fw-bold text-success"><?= display_val($transaction['gsis_enrollment_amount'], 'currency'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SECTION 4: Implementation Status & Completion -->
                <div class="section-header"><i class="bi bi-calendar-check me-2"></i>Implementation & Completion Status</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-details align-middle">
                        <tbody>
                            <tr>
                                <th>Start Date (Ongoing)</th>
                                <td><?= display_val($transaction['ongoing_implementation_start_date']); ?></td>
                                <th>End Date (Ongoing)</th>
                                <td><?= display_val($transaction['ongoing_implementation_end_date']); ?></td>
                            </tr>
                            <tr>
                                <th>Ongoing Beneficiaries</th>
                                <td colspan="3"><?= display_val($transaction['ongoing_implementation_benefs']); ?></td>
                            </tr>
                            <tr>
                                <th>Completed Period</th>
                                <td><?= display_val($transaction['completed_employment_period']); ?></td>
                                <th>Completed Beneficiaries</th>
                                <td><?= display_val($transaction['completed_employment_benefs']); ?></td>
                            </tr>
                            <tr>
                                <th>Completed Amount</th>
                                <td class="fw-bold text-success"><?= display_val($transaction['completed_employment_amount'], 'currency'); ?></td>
                                <th>Completed Documentation</th>
                                <td><?= display_val($transaction['completed_employment_documentation']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SECTION 5: Payments & Payouts -->
                <div class="section-header"><i class="bi bi-cash-stack me-2"></i>Payments & Payouts</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-details align-middle">
                        <tbody>
                            <tr>
                                <th>Payment ALOB No</th>
                                <td><?= display_val($transaction['payment_alob_no']); ?></td>
                                <th>Payment DV No</th>
                                <td><?= display_val($transaction['payment_dv_no']); ?></td>
                            </tr>
                            <tr>
                                <th>Payment Check No</th>
                                <td><?= display_val($transaction['payment_check_no']); ?></td>
                                <th>Payment Date</th>
                                <td><?= display_val($transaction['payment_date']); ?></td>
                            </tr>
                            <tr>
                                <th>Payment Amount</th>
                                <td class="fw-bold text-success"><?= display_val($transaction['payment_amount'], 'currency'); ?></td>
                                <th>Payout Date</th>
                                <td><?= display_val($transaction['payout_date']); ?></td>
                            </tr>
                            <tr>
                                <th>Payout Service Cost</th>
                                <td><?= display_val($transaction['payout_service_cost'], 'currency'); ?></td>
                                <th>Payout Method</th>
                                <td><?= display_val($transaction['payout_method']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SECTION 6: Audit / Metadata Info -->
                <div class="section-header"><i class="bi bi-shield-check me-2"></i>Tracing Information</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-details align-middle">
                        <tbody>
                            <tr>
                                <th>Encoded Date</th>
                                <td><?= display_val($transaction['encoded_date']); ?></td>
                                <th>Encoder Name</th>
                                <td><?= display_val($transaction['encoder_name']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Action Footer inside card -->
                <div class="mt-4 pt-3 text-center border-top no-print">
                    <a href="<?= site_url('adl/export_transaction_excel/' . $transaction['adl_transact_id']); ?>" class="btn btn-outline-success px-4 rounded-pill shadow-sm">
                       <i class="bi bi-file-earmark-excel me-1"></i> Download as Excel
                    </a>
                </div>

            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function () {
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