<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Implementation List - DOLE TUPAD</title>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

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

        .form-control::placeholder {
            color: #797a7846;
            font-style: italic;
            opacity: 1;
        }

        input[type="date"].form-control:invalid::-webkit-datetime-edit {
            color: #797a7846;
            font-style: italic;
        }

        input[type="date"].form-control:valid {
            color: #000000;
            font-style: normal;
        }
        input[type="date"].form-control:valid::-webkit-datetime-edit {
            color: #000000;
            font-style: normal;
        }

        input[type="date"].form-control::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.5);
        }

        select.form-select:invalid {
            color: #797a7846;
            font-style: italic;
        }
        select.form-select option {
            color: #555;
            font-style: normal;
        }
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar');?>

    <div id="main-content">
        <?php $this->load->view('templates/sidebar');?>

        <main class="p-3 p-md-4 flex-grow-1">
            
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
                                    <button type="submit" class="btn w-100 text-white" style="background-color: #0f172a;">Filter</button>
                                    <a href="<?= site_url('adl/transaction_report'); ?>" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="transactionTable" class="table table-hover align-middle w-100">
                                    <thead class="table-light text-uppercase fs-7 text-muted border-bottom">
                                        <tr>
                                            <th class="py-3">Transaction Details</th>
                                            <th class="py-3">Location & Stakeholders</th>
                                            <th class="py-3">Progress Pipeline</th>
                                            <th class="py-3 text-center">Actions</th>
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
                                                    $c8  = !empty($row['completed_employment_period']) && intval($row['completed_employment_benefs']) !== 0 && !empty($row['completed_employment_amount']);
                                                    $c9  = !empty($row['payment_alob_no']) && !empty($row['payment_dv_no']) && !empty($row['payment_check_no']) && !empty($row['payment_amount']) && $isValidDate($row['payment_date']);
                                                    $c10 = $isValidDate($row['payout_date']) && !empty($row['payout_service_cost']) && !empty($row['payout_method']);

                                                    $is_fully_complete = ($c1 && $c2 && $c3 && $c4 && $c5 && $c6 && $c7 && $c8 && $c9 && $c10);
                                                ?>
                                                <tr>
                                                    <!-- Column 1: Core Identification & Dates -->
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <span class="fw-bold text-primary fs-6"><?= html_escape($row['adl_no']); ?></span>
                                                                <?php if ($is_fully_complete): ?>
                                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                                        <i class="bi bi-check-circle-fill me-1"></i> Completed
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                                                        In Progress
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <span class="text-muted small mb-1 font-monospace">Ref: <?= html_escape($row['implementation_reference_no']); ?></span>
                                                            <span class="text-secondary small"><i class="bi bi-calendar3 me-1"></i>Encoded: <?= html_escape($row['encoded_date'] ?? 'N/A'); ?></span>
                                                        </div>
                                                    </td>

                                                    <!-- Column 2: Location & Proponent info -->
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <span class="fw-semibold text-dark mb-1">
                                                                <i class="bi bi-geo-alt text-danger me-1"></i><?= html_escape($row['implementation_area_name'] ?? 'N/A'); ?>, <?= html_escape($row['implementation_province_name'] ?? 'N/A'); ?>
                                                            </span>
                                                            <span class="text-muted small"><strong>Proponent:</strong> <?= html_escape($row['implementation_proponent_name'] ?? 'N/A'); ?></span>
                                                            <span class="text-muted small"><strong>Sponsor:</strong> <?= html_escape($row['implementation_sponsor'] ?: 'None'); ?></span>
                                                        </div>
                                                    </td>

                                                    <!-- Column 3: Compact Status Pipeline Badges -->
                                                    <td style="max-width: 320px;">
                                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                                            <?php if ($c1): ?><span class="badge bg-light text-dark border" title="Coordinated"><i class="bi bi-check text-success"></i> Coordinated</span><?php endif; ?>
                                                            <?php if ($c2): ?><span class="badge bg-light text-dark border" title="Appraisal Submitted"><i class="bi bi-check text-success"></i> Appraisal Sub.</span><?php endif; ?>
                                                            <?php if ($c3): ?><span class="badge bg-light text-dark border" title="Appraisal Approved"><i class="bi bi-check text-success"></i> Appraisal App.</span><?php endif; ?>
                                                            <?php if ($c4): ?><span class="badge bg-light text-dark border" title="Issued PPEs"><i class="bi bi-check text-success"></i> PPEs</span><?php endif; ?>
                                                            <?php if ($c5): ?><span class="badge bg-light text-dark border" title="Oriented"><i class="bi bi-check text-success"></i> Oriented</span><?php endif; ?>
                                                            <?php if ($c6): ?><span class="badge bg-light text-dark border" title="GSIS Enrolled"><i class="bi bi-check text-success"></i> GSIS</span><?php endif; ?>
                                                            <?php if ($c7): ?><span class="badge bg-light text-dark border" title="Implemented"><i class="bi bi-check text-success"></i> Implemented</span><?php endif; ?>
                                                            <?php if ($c8): ?><span class="badge bg-light text-dark border" title="Completed"><i class="bi bi-check text-success"></i> Finished</span><?php endif; ?>
                                                            <?php if ($c9): ?><span class="badge bg-light text-dark border" title="Funding Processed"><i class="bi bi-check text-success"></i> Funded</span><?php endif; ?>
                                                            <?php if ($c10): ?><span class="badge bg-light text-dark border" title="For payout"><i class="bi bi-check text-success"></i> Payout</span><?php endif; ?>
                                                        </div>
                                                    </td>

                                                    <!-- Column 4: Actions -->
                                                    <td class="text-center">
                                                        <div class="dropdown">
                                                            <button class="btn btn-light btn-sm border dropdown-toggle px-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Actions
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                                <?php if (isset($user_assigned_prov) && $user_assigned_prov === $row['implementation_province']): ?>
                                                                    <li>
                                                                        <button type="button" class="dropdown-item edit-btn py-2" data-id="<?= html_escape($row['adl_transact_id']); ?>">
                                                                            <i class="bi bi-pencil-square text-primary me-2"></i> Update Record
                                                                        </button>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <li>
                                                                    <a href="<?= site_url('adl/view_pdf/' . html_escape($row['adl_transact_id'])); ?>" target="_blank" class="dropdown-item py-2">
                                                                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> View PDF Details
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
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

        <!-- EDIT TRANSACTION MODAL -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background-color: #0f172a;">
                        <h5 class="modal-title" id="editModalLabel">
                            <i class="bi bi-pencil-square me-2"></i>Update ADL Transaction Record
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form action="<?= site_url('adl/update_transaction_record'); ?>" method="POST" id="editTransactionForm">
                        <input type="hidden" name="adl_transact_id" id="edit_adl_transact_id">

                        <div class="modal-body p-4">
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

                            <div class="tab-content" id="editEncodingTabsContent">
                                
                                <!-- TAB 1: GENERAL INFORMATION -->
                                <div class="tab-pane fade show active" id="edit-general-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">ADL Number</label>
                                            <select id="edit_adl_no" name="adl_no" class="form-select" disabled title="Cannot be edited">
                                                <option value="">-- Select ADL --</option>
                                                <?php if (!empty($ADL)): ?>
                                                    <?php foreach ($ADL as $ad): ?>
                                                        <option value="<?= html_escape($ad['adl_no']); ?>"><?= html_escape($ad['adl_no']); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Reference No.</label>
                                            <input type="text" id="edit_implementation_reference_no" name="implementation_reference_no" class="form-control" disabled title="Cannot be edited">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Date Coordinated</label>
                                            <input type="text" id="edit_date_coordinated" name="status_date" class="form-control" placeholder="Date Coordinated" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label fw-semibold small">No of Days</label>
                                            <input type="text" id="no_of_days" name="no_of_days" class="form-control" placeholder="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Target</label>
                                            <input type="text" id="target" name="target" class="form-control" placeholder="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Reformulated Target</label>
                                            <input type="text" id="edit_reformulated_target" name="reformulated_target" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col-md-3">
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
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Implementation Area</label>
                                            <select name="implementation_area" id="edit_implementation_area" class="form-select" required>
                                                <option value="" selected disabled>Select Province First</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Implementation Barangay</label>
                                            <select name="implementation_brgy" id="edit_implementation_brgy" class="form-select">
                                                <option value="" selected disabled>Select Municipality First</option>
                                            </select>
                                        </div>
                                        
                                        <!-- DISTRICT SELECT FIELD -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">District</label>
                                            <select name="implementation_district" id="edit_implementation_district" class="form-select" style="width: 100%;" required>
                                                <option value="" selected disabled>-- Select District --</option>
                                                <?php if (!empty($districts)): ?>
                                                    <?php foreach ($districts as $dist): ?>
                                                        <option value="<?= html_escape($dist['district_id']); ?>">
                                                            <?= html_escape($dist['district_no']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Wage Percentage</label>
                                            <select name="wage_percentage" id="edit_wage_percentage" class="form-select" required>
                                                <option value="">--Select Percentage--</option>
                                                <option value="2.5">2.5%</option>
                                                <option value="3">3%</option>                          
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Subsidy Cost</label>
                                            <input type="text" id="edit_subsidy_cost" name="subsidy_cost" class="form-control" placeholder="GPAI Funding" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Admin Cost</label>
                                            <input type="text" id="edit_admin_cost" name="admin_cost" class="form-control" placeholder="GPAI Funding" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">GPAI INFO (Funds)</label>
                                            <input type="text" id="edit_gpai_info" name="gpai_info" class="form-control" placeholder="GPAI Funding" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">WAGE INFO (Funds)</label>
                                            <input type="text" id="edit_wage_info" name="wage_info" class="form-control" placeholder="Wage Funding" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">LGU Classification</label>
                                            <input type="text" id="edit_implementation_classification" name="implementation_classification" class="form-control" placeholder="LGU Class" required>
                                        </div>

                                        <!-- PROPONENT SELECT FIELD -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Proponent</label>
                                            <select name="imp_proponent" id="edit_imp_proponent" class="form-select" style="width: 100%;" required>
                                                <option value="" selected disabled>-- Select or type Proponent --</option>
                                                <?php if (!empty($proponents)): ?>
                                                    <?php foreach ($proponents as $prop): ?>
                                                        <option value="<?= html_escape($prop['proponent_id']); ?>">
                                                            <?= html_escape($prop['proponent_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold small">Sponsor</label>
                                            <input type="text" id="edit_implementation_sponsor" name="imp_sponsor" class="form-control" placeholder="Sponsor" required>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold small">Overall Remarks</label>
                                            <input type="text" id="edit_remarks" name="remarks" class="form-control" placeholder="Remarks">
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 2: APPRAISAL & PPES -->
                                <div class="tab-pane fade" id="edit-appraisal-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Appraisal Date Submitted</label>
                                            <input type="text" id="edit_appraisal_date_submitted" name="appraisal_date_submitted" class="form-control" placeholder="Date Submitted" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold small">Appraisal Date Approved</label>
                                            <input type="text" id="edit_appraisal_date_approved" name="appraisal_date_approved" class="form-control" placeholder="Date Approved" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">PPES RIS No.</label>
                                            <input type="text" id="edit_ppes_issuance_ris" name="ppes_issuance_ris" class="form-control" placeholder="RIS Number">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">PPES Date Issued</label>
                                            <input type="text" id="edit_ppes_date_issued" name="ppes_date_issued" class="form-control" placeholder="Date Issued" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">PPES Count</label>
                                            <input type="number" id="edit_ppes_count" name="ppes_count" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">PPES Female</label>
                                            <input type="number" id="edit_ppes_female" name="ppes_female" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">PPES Amount</label>
                                            <input type="text" id="edit_ppes_amount" name="ppes_amount" class="form-control" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 3: ORIENTATION & GSIS -->
                                <div class="tab-pane fade" id="edit-orientation-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Orientation Date</label>
                                            <input type="text" id="edit_orientation_date" name="orientation_date" class="form-control" placeholder="Date Orientation" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Orientation Beneficiaries</label>
                                            <input type="number" id="edit_orientation_benefs" name="orientation_benefs" class="form-control" placeholder="0" value="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Employment Period</label>
                                            <input type="text" id="edit_orientation_employment_period" name="orientation_employment_period" class="form-control" placeholder="Employment Period">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">GSIS Enrollment Date</label>
                                            <input type="text" id="edit_gsis_enrollment_date" name="gsis_enrollment_date" class="form-control" placeholder="GSIS Enrollment Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">GSIS Beneficiaries</label>
                                            <input type="number" id="edit_gsis_enrollment_benefs" name="gsis_enrollment_benefs" class="form-control" placeholder="0" value="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">GSIS Female</label>
                                            <input type="number" id="edit_gsis_enrollment_female" name="gsis_enrollment_female" class="form-control" placeholder="0" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">GSIS Amount</label>
                                            <input type="text" id="edit_gsis_enrollment_amount" name="gsis_enrollment_amount" class="form-control" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 4: IMPLEMENTATION & COMPLETION STATUS -->
                                <div class="tab-pane fade" id="edit-implementation-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Implementation Start Date</label>
                                            <input type="text" id="edit_ongoing_implementation_start_date" name="ongoing_implementation_start_date" class="form-control" placeholder="Start Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Implementation End Date</label>
                                            <input type="text" id="edit_ongoing_implementation_end_date" name="ongoing_implementation_end_date" class="form-control" placeholder="End Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Ongoing Beneficiaries</label>
                                            <input type="number" id="edit_ongoing_implementation_benefs" name="ongoing_implementation_benefs" class="form-control" placeholder="0" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Completed Period</label>
                                            <input type="text" id="edit_completed_employment_period" name="completed_employment_period" class="form-control" placeholder="Period">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Completed Beneficiaries</label>
                                            <input type="number" id="edit_completed_employment_benefs" name="completed_employment_benefs" class="form-control" placeholder="0" value="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Completed Amount</label>
                                            <input type="text" id="edit_completed_employment_amount" name="completed_employment_amount" class="form-control" placeholder="0.00">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Documentation Status</label>
                                            <input type="text" id="edit_completed_employment_documentation" name="completed_employment_documentation" class="form-control" placeholder="Remarks/Status">
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 5: PAYMENT & PAYOUT DETAILS -->
                                <div class="tab-pane fade" id="edit-payment-pane" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">ALOB No.</label>
                                            <input type="text" id="edit_payment_alob_no" name="payment_alob_no" class="form-control" placeholder="ALOB Number">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">DV No.</label>
                                            <input type="text" id="edit_payment_dv_no" name="payment_dv_no" class="form-control" placeholder="DV Number">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Check No.</label>
                                            <input type="text" id="edit_payment_check_no" name="payment_check_no" class="form-control" placeholder="Check Number">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold small">Payment Date</label>
                                            <input type="text" id="edit_payment_date" name="payment_date" class="form-control" placeholder="Payment Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small">Payment Amount</label>
                                            <input type="text" id="edit_payment_amount" name="payment_amount" class="form-control" placeholder="0.00">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Payout Date</label>
                                            <input type="text" id="edit_payout_date" name="payout_date" class="form-control" placeholder="Payout Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Payout Method</label>
                                            <select id="edit_payout_method" name="payout_method" class="form-select" style="color: #7a7979a9; font-style: italic;" 
                                                onchange="this.style.color='#000000'; this.style.fontStyle='normal';">
                                                <option value="" disabled selected>-- Select Payout Site --</option>
                                                <?php if (!empty($payoutSite)): ?>
                                                    <?php foreach ($payoutSite as $pos): ?>
                                                        <option value="<?= html_escape($pos['payout_site_id']); ?>"><?= html_escape($pos['payout_site_name']); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold small">Service Cost</label>
                                            <input type="text" id="edit_payout_service_cost" name="payout_service_cost" class="form-control" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn px-4 text-white" style="background-color: #0f172a;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function () {
        $('#transactionTable').DataTable({
        "language": {
            "emptyTable": "No transaction records found. Please select a filtered area."
        },
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "order": [[0, "desc"]]
    });

        $('#edit_imp_proponent').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select or type Proponent --',
            allowClear: true,
            dropdownParent: $('#editModal')
        });

        $('#edit_implementation_district').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select District --',
            allowClear: true,
            dropdownParent: $('#editModal')
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
                        
                        $('#edit_adl_transact_id').val(d.adl_transact_id);
                        $('#edit_adl_no').val(d.adl_no);
                        $('#edit_implementation_reference_no').val(d.implementation_reference_no);
                        
                        ['edit_date_coordinated', 'edit_appraisal_date_submitted', 'edit_appraisal_date_approved', 
                         'edit_ppes_date_issued', 'edit_orientation_date', 'edit_gsis_enrollment_date', 
                         'edit_ongoing_implementation_start_date', 'edit_ongoing_implementation_end_date', 
                         'edit_payment_date', 'edit_payout_date'].forEach(id => {
                            let val = d[id.replace('edit_', '')];
                            if (val && val !== '0000-00-00') {
                                $('#' + id).val(val).attr('type', 'date');
                            } else {
                                $('#' + id).val('').attr('type', 'text');
                            }
                        });

                        $('#edit_implementation_province').val(d.implementation_province);
                        
                        loadModalMunicipalities(d.implementation_province, d.implementation_area, d.implementation_brgy);

                        $('#edit_implementation_district').val(d.implementation_district).trigger('change');
                        $('#edit_imp_proponent').val(d.implementation_proponent).trigger('change');

                        $('#edit_wage_percentage').val(d.wage_percentage);
                        $('#edit_gpai_info').val(d.gpai_info);
                        $('#edit_wage_info').val(d.wage_info);
                        $('#edit_subsidy_cost').val(d.subsidy_cost);
                        $('#edit_admin_cost').val(d.admin_cost);

                        $('#edit_remarks').val(d.remarks);

                        $('#edit_implementation_classification').val(d.implementation_classification);
                        $('#edit_implementation_sponsor').val(d.implementation_sponsor);
                        $('#no_of_days').val(d.no_of_days);
                        $('#target').val(d.target);
                        $('#edit_reformulated_target').val(d.reformulated_target);

                        $('#edit_ppes_issuance_ris').val(d.ppes_issuance_ris);
                        $('#edit_ppes_count').val(d.ppes_count);
                        $('#edit_ppes_female').val(d.ppes_female);
                        $('#edit_ppes_amount').val(d.ppes_amount);

                        $('#edit_orientation_benefs').val(d.orientation_benefs);
                        $('#edit_orientation_employment_period').val(d.orientation_employment_period);
                        $('#edit_gsis_enrollment_benefs').val(d.gsis_enrollment_benefs);
                        $('#edit_gsis_enrollment_female').val(d.gsis_enrollment_female);
                        $('#edit_gsis_enrollment_amount').val(d.gsis_enrollment_amount);

                        $('#edit_ongoing_implementation_benefs').val(d.ongoing_implementation_benefs);
                        $('#edit_completed_employment_period').val(d.completed_employment_period);
                        $('#edit_completed_employment_benefs').val(d.completed_employment_benefs);
                        $('#edit_completed_employment_amount').val(d.completed_employment_amount);
                        $('#edit_completed_employment_documentation').val(d.completed_employment_documentation);
                        $('#edit_payment_alob_no').val(d.payment_alob_no);
                        $('#edit_payment_dv_no').val(d.payment_dv_no);
                        $('#edit_payment_check_no').val(d.payment_check_no);
                        $('#edit_payment_amount').val(d.payment_amount);
                        $('#edit_payout_method').val(d.payout_method);
                        $('#edit_payout_service_cost').val(d.payout_service_cost);

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
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });
    </script>
</body>

</html>