<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Implementation List - DOLE TUPAD</title>
    
    <!-- Select2 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

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
        } 

        .nav-tabs .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1rem;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background-color: transparent;
            border-bottom: 3px solid var(--primary-color);
            font-weight: 600;
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
            
            <!-- FLASH MESSAGES -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= html_escape($this->session->flashdata('success')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= html_escape($this->session->flashdata('error')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-folder2-open text-primary me-2"></i>ADL Implementation List
                    </h3>
                    <p class="text-muted small mb-0">List of recorded ADL Implementation</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <!-- Filter Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" action="" class="row g-3" id="filterForm">
                                <div class="col-md-5">
                                    <label for="implementation_province" class="form-label fw-semibold">Implementation Province</label>
                                    <select name="implementation_province" id="implementation_province" class="form-select">
                                        <option value="">-- Select Province --</option>
                                        <?php foreach ($provinces as $prov): ?>
                                            <option value="<?= $prov['provCode']; ?>" <?= ($selected_province == $prov['provCode']) ? 'selected' : ''; ?>>
                                                <?= $prov['provDesc']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label for="implementation_area" class="form-label fw-semibold">Implementation Area</label>
                                    <select name="implementation_area" id="implementation_area" class="form-select">
                                        <option value="">-- Select Area / Municipality --</option>
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    <a href="<?= site_url('adl/transaction_report'); ?>" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="transactionTable" class="table table-striped table-hover align-middle w-100">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Date Encoded</th>
                                            <th>ADL No.</th>
                                            <th>ADL Reference No.</th>
                                            <th>Implementation Province</th>
                                            <th>Implementation Area</th>
                                            <th>Proponent</th>
                                            <th>Sponsor</th>
                                            <th>Date Coordinated</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($transactions)): ?>
                                            <?php foreach ($transactions as $row): ?>
                                                <?php 
                                                    $isValidDate = function($date) {
                                                        return !empty($date) && $date !== '0000-00-00';
                                                    };

                                                    $c1  = $isValidDate($row['date_coordinated']);
                                                    $c2  = $isValidDate($row['appraisal_date_submitted']);
                                                    $c3  = $isValidDate($row['appraisal_date_approved']);
                                                    $c4  = !empty($row['ppes_issuance_ris']) && $isValidDate($row['ppes_date_issued']) && intval($row['ppes_count']) !== 0 && floatval($row['ppes_amount']) !== 0.0;
                                                    $c5  = $isValidDate($row['orientation_date']) && !empty($row['orientation_employment_period']);
                                                    $c6  = $isValidDate($row['gsis_enrollment_date']) && intval($row['gsis_enrollment_benefs']) !== 0 && floatval($row['gsis_enrollment_amount']) !== 0.0;
                                                    $c7  = $isValidDate($row['ongoing_implementation_start_date']) && $isValidDate($row['ongoing_implementation_end_date']) && intval($row['ongoing_implementation_benefs']) !== 0;
                                                    $c8  = !empty($row['completed_employment_period']) && intval($row['completed_employment_benefs']) !== 0 && !empty($row['completed_employment_amount']) && !empty($row['completed_employment_documentation']);
                                                    $c9  = !empty($row['payment_alob_no']) && !empty($row['payment_dv_no']) && !empty($row['payment_check_no']) && !empty($row['payment_amount']) && $isValidDate($row['payment_date']);
                                                    $c10 = $isValidDate($row['payout_date']) && !empty($row['payout_service_cost']) && !empty($row['payout_method']);

                                                    $is_fully_complete = ($c1 && $c2 && $c3 && $c4 && $c5 && $c6 && $c7 && $c8 && $c9 && $c10);
                                                ?>
                                                <tr>
                                                     <td><?= html_escape($row['encoded_date'] ?? 'N/A'); ?></td>
                                                    <td>
                                                        <?php if ($is_fully_complete): ?>
                                                            <div class="mb-1">
                                                                <span class="badge bg-success text-white shadow-sm">
                                                                    <i class="bi bi-check-circle-fill me-1"></i> Fully Completed
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <strong><?= html_escape($row['adl_no']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <div class="mb-1 d-flex flex-wrap gap-1">
                                                            <?php if ($c1): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Coordinated</span><?php endif; ?>
                                                            <?php if ($c2): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Appraisal Submitted</span><?php endif; ?>
                                                            <?php if ($c3): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Appraisal Approved</span><?php endif; ?>
                                                            <?php if ($c4): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Issued PPEs</span><?php endif; ?>
                                                            <?php if ($c5): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Oriented</span><?php endif; ?>
                                                            <?php if ($c6): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>GSIS Enrolled</span><?php endif; ?>
                                                            <?php if ($c7): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Implemented</span><?php endif; ?>
                                                            <?php if ($c8): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span><?php endif; ?>
                                                            <?php if ($c9): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Funding Processed</span><?php endif; ?>
                                                            <?php if ($c10): ?><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>For payout</span><?php endif; ?>
                                                        </div>
                                                        <strong><?= html_escape($row['implementation_reference_no']); ?></strong>
                                                    </td>
                                                    <td><?= html_escape($row['implementation_province_name'] ?? 'N/A'); ?></td>
                                                    <td><?= html_escape($row['implementation_area_name'] ?? 'N/A'); ?></td>
                                                    <td><?= html_escape($row['implementation_proponent']); ?></td>
                                                    <td><?= html_escape($row['implementation_sponsor']); ?></td>
                                                    <td><?= html_escape($row['date_coordinated']); ?></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="<?= $row['adl_transact_id']; ?>">
                                                            <i class="bi bi-pencil-square me-1"></i> Update
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>

        <!-- EDIT TRANSACTION MODAL (TAB BY TAB) -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Update ADL Transaction Record
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form action="<?= site_url('adl/update_transaction_record'); ?>" method="POST" id="editTransactionForm">
                        <input type="hidden" name="adl_transact_id" id="edit_adl_transact_id">

                        <div class="modal-body p-4">
                            <!-- TAB NAVIGATION HEADERS -->
                            <ul class="nav nav-tabs mb-4" id="editEncodingTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="edit-general-tab" data-bs-toggle="tab" data-bs-target="#edit-general-pane" type="button" role="tab">
                                        <i class="bi bi-info-circle me-1"></i> General Info
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="edit-appraisal-tab" data-bs-toggle="tab" data-bs-target="#edit-appraisal-pane" type="button" role="tab">
                                        <i class="bi bi-clipboard-check me-1"></i> Appraisal & PPES
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="edit-orientation-tab" data-bs-toggle="tab" data-bs-target="#edit-orientation-pane" type="button" role="tab">
                                        <i class="bi bi-people me-1"></i> Orientation & GSIS
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="edit-implementation-tab" data-bs-toggle="tab" data-bs-target="#edit-implementation-pane" type="button" role="tab">
                                        <i class="bi bi-briefcase me-1"></i> Implementation Status
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="edit-payment-tab" data-bs-toggle="tab" data-bs-target="#edit-payment-pane" type="button" role="tab">
                                        <i class="bi bi-cash-stack me-1"></i> Payment & Payout
                                    </button>
                                </li>
                            </ul>

                            <!-- TAB CONTENT PANES -->
                            <div class="tab-content" id="editEncodingTabsContent">
                                
                                <!-- TAB 1: GENERAL INFORMATION -->
                                <div class="tab-pane fade show active" id="edit-general-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">ADL Number</label>
                                            <select id="edit_adl_no" name="adl_no" class="form-select" required>
                                                <option value="">-- Select ADL --</option>
                                                <?php if (!empty($ADL)): ?>
                                                    <?php foreach ($ADL as $ad): ?>
                                                        <option value="<?= html_escape($ad['adl_no']); ?>"><?= html_escape($ad['adl_no']); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Implementation Reference No.</label>
                                            <input type="text" id="edit_implementation_reference_no" name="implementation_reference_no" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Date Coordinated</label>
                                            <input type="date" id="edit_date_coordinated" name="status_date" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Implementation Province</label>
                                            <select name="implementation_province" id="edit_implementation_province" class="form-select" required>
                                                <option value="" selected disabled>Select Province</option>
                                                <?php if (!empty($provinces)): ?>
                                                    <?php foreach ($provinces as $prov): ?>
                                                        <option value="<?= html_escape($prov['provCode']); ?>"><?= html_escape($prov['provDesc']); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Implementation Area (City/Municipality)</label>
                                            <select name="implementation_area" id="edit_implementation_area" class="form-select" required>
                                                <option value="" selected disabled>Select Province First</option>
                                            </select>
                                        </div>
                                        
                                        <!-- UPDATED FIELDS ADDED -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Implementation Barangay</label>
                                            <select name="implementation_brgy" id="edit_implementation_brgy" class="form-select">
                                                <option value="" selected disabled>Select Municipality First</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Implementation District</label>
                                            <input type="text" id="edit_implementation_district" name="implementation_district" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Implementation Classification</label>
                                            <input type="text" id="edit_implementation_classification" name="implementation_classification" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Proponent</label>
                                            <input type="text" id="edit_implementation_proponent" name="imp_proponent" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Sponsor</label>
                                            <input type="text" id="edit_implementation_sponsor" name="imp_sponsor" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 2: APPRAISAL & PPES -->
                                <div class="tab-pane fade" id="edit-appraisal-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Appraisal Date Submitted</label>
                                            <input type="date" id="edit_appraisal_date_submitted" name="appraisal_date_submitted" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Appraisal Date Approved</label>
                                            <input type="date" id="edit_appraisal_date_approved" name="appraisal_date_approved" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">PPES Issuance RIS</label>
                                            <input type="text" id="edit_ppes_issuance_ris" name="ppes_issuance_ris" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">PPES Date Issued</label>
                                            <input type="date" id="edit_ppes_date_issued" name="ppes_date_issued" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">PPES Count</label>
                                            <input type="number" id="edit_ppes_count" name="ppes_count" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">PPES Amount</label>
                                            <input type="text" id="edit_ppes_amount" name="ppes_amount" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 3: ORIENTATION & GSIS -->
                                <div class="tab-pane fade" id="edit-orientation-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Orientation Date</label>
                                            <input type="date" id="edit_orientation_date" name="orientation_date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Orientation Beneficiaries</label>
                                            <input type="number" id="edit_orientation_benefs" name="orientation_benefs" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Employment Period</label>
                                            <input type="text" id="edit_orientation_employment_period" name="orientation_employment_period" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">GSIS Enrollment Date</label>
                                            <input type="date" id="edit_gsis_enrollment_date" name="gsis_enrollment_date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">GSIS Beneficiaries</label>
                                            <input type="number" id="edit_gsis_enrollment_benefs" name="gsis_enrollment_benefs" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">GSIS Amount</label>
                                            <input type="text" id="edit_gsis_enrollment_amount" name="gsis_enrollment_amount" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 4: IMPLEMENTATION & COMPLETION STATUS -->
                                <div class="tab-pane fade" id="edit-implementation-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Ongoing Start Date</label>
                                            <input type="date" id="edit_ongoing_implementation_start_date" name="ongoing_implementation_start_date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Ongoing End Date</label>
                                            <input type="date" id="edit_ongoing_implementation_end_date" name="ongoing_implementation_end_date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Ongoing Beneficiaries</label>
                                            <input type="number" id="edit_ongoing_implementation_benefs" name="ongoing_implementation_benefs" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Completed Period</label>
                                            <input type="text" id="edit_completed_employment_period" name="completed_employment_period" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Completed Beneficiaries</label>
                                            <input type="number" id="edit_completed_employment_benefs" name="completed_employment_benefs" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Completed Amount</label>
                                            <input type="text" id="edit_completed_employment_amount" name="completed_employment_amount" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Documentation Status</label>
                                            <input type="text" id="edit_completed_employment_documentation" name="completed_employment_documentation" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 5: PAYMENT & PAYOUT DETAILS -->
                                <div class="tab-pane fade" id="edit-payment-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">ALOB No.</label>
                                            <input type="text" id="edit_payment_alob_no" name="payment_alob_no" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">DV No.</label>
                                            <input type="text" id="edit_payment_dv_no" name="payment_dv_no" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Check No.</label>
                                            <input type="text" id="edit_payment_check_no" name="payment_check_no" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Payment Date</label>
                                            <input type="date" id="edit_payment_date" name="payment_date" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Payment Amount</label>
                                            <input type="text" id="edit_payment_amount" name="payment_amount" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Payout Date</label>
                                            <input type="date" id="edit_payout_date" name="payout_date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Payout Method</label>
                                            <select id="edit_payout_method" name="payout_method" class="form-select">
                                                <option value="">-- Select Payout Site --</option>
                                                <?php if (!empty($payoutSite)): ?>
                                                    <?php foreach ($payoutSite as $pos): ?>
                                                        <option value="<?= html_escape($pos['payout_site_id']); ?>"><?= html_escape($pos['payout_site_name']); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Service Cost</label>
                                            <input type="text" id="edit_payout_service_cost" name="payout_service_cost" class="form-control">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function () {
        $('#transactionTable').DataTable({
            "language": {
                "emptyTable": "No transaction records found. Please select a filtered area."
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "order": [[1, "desc"]]
        });

        const ppeRate = parseFloat("<?= $ppe_rate ?? 325; ?>") || 0;
        const gsisRate = parseFloat("<?= $gsis_rate ?? 50; ?>") || 0;

        $('#edit_ppes_count').on('input', function () {
            const count = parseFloat($(this).val()) || 0;
            $('#edit_ppes_amount').val((count * ppeRate).toFixed(2));
        });

        $('#edit_gsis_enrollment_benefs').on('input', function () {
            const benefs = parseFloat($(this).val()) || 0;
            $('#edit_gsis_enrollment_amount').val((benefs * gsisRate).toFixed(2));
        });

        // Load Municipalities for Filter dropdown
        function loadMunicipalities(provCode, selectedArea = '') {
            if (provCode) {
                $.ajax({
                    url: "<?= site_url('adl/get_municipalities_by_province'); ?>",
                    type: "GET",
                    data: { provCode: provCode },
                    dataType: "json",
                    success: function (data) {
                        $('#implementation_area').empty().append('<option value="">-- Select Area / Municipality --</option>');
                        $.each(data, function (key, value) {
                            let isSelected = (value.cityCode == selectedArea || value.citymunCode == selectedArea) ? 'selected' : '';
                            $('#implementation_area').append('<option value="' + (value.cityCode || value.citymunCode) + '" ' + isSelected + '>' + value.citymunDesc + '</option>');
                        });
                    }
                });
            } else {
                $('#implementation_area').empty().append('<option value="">-- Select Area / Municipality --</option>');
            }
        }

        $('#implementation_province').change(function () {
            loadMunicipalities($(this).val());
        });

        let initialProv = $('#implementation_province').val();
        let initialArea = "<?= $selected_area ?? ''; ?>";
        if (initialProv) {
            loadMunicipalities(initialProv, initialArea);
        }

        // Helper function for loading barangays inside the edit modal
        function loadModalBarangays(citymunCode, selectedBrgy = '') {
            const $brgySelect = $('#edit_implementation_brgy');
            if (citymunCode) {
                $brgySelect.prop('disabled', true).html('<option value="">Loading barangays...</option>');
                $.ajax({
                    url: "<?= site_url('adl/get_barangays_by_municipality'); ?>",
                    type: "GET",
                    data: { citymunCode: citymunCode },
                    dataType: "json",
                    success: function (data) {
                        $brgySelect.empty().append('<option value="" selected disabled>Select Barangay</option>');
                        if (data && data.length > 0) {
                            $.each(data, function (index, item) {
                                let isSelected = (item.brgyCode == selectedBrgy) ? 'selected' : '';
                                $brgySelect.append('<option value="' + item.brgyCode + '" ' + isSelected + '>' + item.brgyDesc + '</option>');
                            });
                            $brgySelect.prop('disabled', false);
                        } else {
                            $brgySelect.append('<option value="" disabled>No barangays found</option>');
                            $brgySelect.prop('disabled', false);
                        }
                    },
                    error: function () {
                        $brgySelect.prop('disabled', false).html('<option value="" disabled>Error loading data</option>');
                    }
                });
            } else {
                $brgySelect.prop('disabled', true).html('<option value="" selected disabled>Select Municipality First</option>');
            }
        }

        // Helper function for loading municipalities inside the edit modal
        function loadModalMunicipalities(provCode, selectedArea = '', selectedBrgy = '') {
            const $cityMunSelect = $('#edit_implementation_area');
            if (provCode) {
                $.ajax({
                    url: "<?= site_url('adl/get_municipalities_by_province'); ?>",
                    type: "GET",
                    data: { provCode: provCode },
                    dataType: "json",
                    success: function (data) {
                        $cityMunSelect.empty().append('<option value="" selected disabled>Select City/Municipality</option>');
                        $.each(data, function (index, item) {
                            let munCode = item.cityCode || item.citymunCode;
                            let isSelected = (munCode == selectedArea) ? 'selected' : '';
                            $cityMunSelect.append('<option value="' + munCode + '" ' + isSelected + '>' + item.citymunDesc + '</option>');
                        });

                        if (selectedArea) {
                            loadModalBarangays(selectedArea, selectedBrgy);
                        }
                    }
                });
            } else {
                $cityMunSelect.empty().append('<option value="" selected disabled>Select Province First</option>');
                $('#edit_implementation_brgy').empty().append('<option value="" selected disabled>Select Municipality First</option>');
            }
        }

        $('#edit_implementation_province').change(function () {
            loadModalMunicipalities($(this).val());
            $('#edit_implementation_brgy').empty().append('<option value="" selected disabled>Select Municipality First</option>');
        });

        $('#edit_implementation_area').change(function () {
            loadModalBarangays($(this).val());
        });

        // Open Edit Modal and Fetch Record Data via AJAX
        $(document).on('click', '.edit-btn', function () {
            const transactionId = $(this).data('id');

            $.ajax({
                url: "<?= site_url('adl/get_transaction_details'); ?>",
                type: "GET",
                data: { id: transactionId },
                dataType: "json",
                success: function (response) {
                    if (response.status && response.data) {
                        const d = response.data;
                        
                        // Populate Fields
                        $('#edit_adl_transact_id').val(d.adl_transact_id);
                        $('#edit_adl_no').val(d.adl_no);
                        $('#edit_implementation_reference_no').val(d.implementation_reference_no);
                        $('#edit_date_coordinated').val(d.date_coordinated);
                        $('#edit_implementation_province').val(d.implementation_province);
                        
                        // Load and set municipality and barangay dependencies
                        loadModalMunicipalities(d.implementation_province, d.implementation_area, d.implementation_brgy);

                        // Populate new fields
                        $('#edit_implementation_district').val(d.implementation_district);
                        $('#edit_implementation_classification').val(d.implementation_classification);

                        $('#edit_implementation_proponent').val(d.implementation_proponent);
                        $('#edit_implementation_sponsor').val(d.implementation_sponsor);
                        $('#edit_appraisal_date_submitted').val(d.appraisal_date_submitted);
                        $('#edit_appraisal_date_approved').val(d.appraisal_date_approved);
                        $('#edit_ppes_issuance_ris').val(d.ppes_issuance_ris);
                        $('#edit_ppes_date_issued').val(d.ppes_date_issued);
                        $('#edit_ppes_count').val(d.ppes_count);
                        $('#edit_ppes_amount').val(d.ppes_amount);
                        $('#edit_orientation_date').val(d.orientation_date);
                        $('#edit_orientation_benefs').val(d.orientation_benefs);
                        $('#edit_orientation_employment_period').val(d.orientation_employment_period);
                        $('#edit_gsis_enrollment_date').val(d.gsis_enrollment_date);
                        $('#edit_gsis_enrollment_benefs').val(d.gsis_enrollment_benefs);
                        $('#edit_gsis_enrollment_amount').val(d.gsis_enrollment_amount);
                        $('#edit_ongoing_implementation_start_date').val(d.ongoing_implementation_start_date);
                        $('#edit_ongoing_implementation_end_date').val(d.ongoing_implementation_end_date);
                        $('#edit_ongoing_implementation_benefs').val(d.ongoing_implementation_benefs);
                        $('#edit_completed_employment_period').val(d.completed_employment_period);
                        $('#edit_completed_employment_benefs').val(d.completed_employment_benefs);
                        $('#edit_completed_employment_amount').val(d.completed_employment_amount);
                        $('#edit_completed_employment_documentation').val(d.completed_employment_documentation);
                        $('#edit_payment_alob_no').val(d.payment_alob_no);
                        $('#edit_payment_dv_no').val(d.payment_dv_no);
                        $('#edit_payment_check_no').val(d.payment_check_no);
                        $('#edit_payment_date').val(d.payment_date);
                        $('#edit_payment_amount').val(d.payment_amount);
                        $('#edit_payout_date').val(d.payout_date);
                        $('#edit_payout_method').val(d.payout_method);
                        $('#edit_payout_service_cost').val(d.payout_service_cost);

                        // Show Modal
                        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                        editModal.show();
                    } else {
                        alert('Failed to retrieve transaction record details.');
                    }
                },
                error: function () {
                    alert('An error occurred while fetching the transaction record.');
                }
            });
        });
    });
    </script>
</body>

</html>