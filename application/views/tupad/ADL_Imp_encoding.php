<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Transactions Encoding - DOLE TUPAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0f172a;
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

        /* Custom Button & Modal Header Overrides for #0f172a */
        .btn-primary, .btn-outline-primary {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }

        .btn-outline-primary {
            background-color: transparent !important;
            color: #0f172a !important;
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #1e293b !important;
            border-color: #1e293b !important;
        }

        .modal-header {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        @media print {
            body { background-color: #ffffff; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; }
        }

        .form-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            max-width: 1100px;
            margin: 0 auto;
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

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: var(--primary-light);
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

    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        
        <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">
            
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= html_escape($this->session->flashdata('success')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= html_escape($this->session->flashdata('error')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-clipboard2-data text-primary me-2"></i>ADL TRANSACTIONS ENCODING
                    </h3>
                    <p class="text-muted small mb-0">Record and monitor transaction details for ADL</p>
                </div>
            </div>

            <div class="container-fluid px-0">
                <div class="form-card p-4 p-md-5">
                    
                    <form action="<?= site_url('adl/store_transaction'); ?>" method="POST" id="transactionForm">
                        
                        <ul class="nav nav-tabs mb-4" id="encodingTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button" role="tab">
                                    <i class="bi bi-info-circle me-1"></i> General Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="appraisal-tab" data-bs-toggle="tab" data-bs-target="#appraisal-pane" type="button" role="tab">
                                    <i class="bi bi-clipboard-check me-1"></i> Appraisal & PPES
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="orientation-tab" data-bs-toggle="tab" data-bs-target="#orientation-pane" type="button" role="tab">
                                    <i class="bi bi-people me-1"></i> Orientation & GSIS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="implementation-tab" data-bs-toggle="tab" data-bs-target="#implementation-pane" type="button" role="tab">
                                    <i class="bi bi-briefcase me-1"></i> Implementation Status
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment-pane" type="button" role="tab">
                                    <i class="bi bi-cash-stack me-1"></i> Payment & Payout
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="encodingTabsContent">
                            
                            <!-- TAB 1: GENERAL INFORMATION -->
                            <div class="tab-pane fade show active" id="general-pane" role="tabpanel" aria-labelledby="general-tab">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">ADL Number</label>
                                        <select id="adl_no" name="adl_no" class="form-select" style="width: 100%;" required>
                                            <option value="">-Select ADL-</option>
                                            <?php if (!empty($ADL)): ?>
                                                <?php foreach ($ADL as $ad): ?>
                                                    <option value="<?= html_escape($ad['adl_no']); ?>" <?= set_select('adl_no', $ad['adl_no']); ?>>
                                                        <?= html_escape($ad['adl_no']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                   <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Audrey Reference No.</label>
                                        <input type="text" name="audrey_reference_no" id="audrey_reference_no" oninput="this.value = this.value.toUpperCase();" class="form-control" required>
                                        <input type="hidden" name="implementation_reference_no" id="implementation_reference_no" class="form-control" placeholder="Auto-generated" readonly required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">Date Coordinated</label>
                                        <input type="date" name="status_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label fw-semibold small"># of Days</label>
                                        <input type="text" name="no_of_days" class="form-control" placeholder="0" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label fw-semibold small">Benefs</label>
                                        <input type="text" name="target" class="form-control" placeholder="0" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">Reformulated Target</label>
                                        <input type="text" name="reformulated_target" class="form-control" placeholder="0" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Implementation Province</label>
                                        <select name="implementation_province" id="implementation_province" class="form-select" required>
                                            <option value="" selected disabled>Select Province</option>
                                            <?php if (!empty($provinces)): ?>
                                                <?php foreach ($provinces as $prov): ?>
                                                    <option value="<?= html_escape($prov['provCode']); ?>">
                                                        <?= html_escape($prov['provDesc']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Implementation Area</label>
                                        <select name="implementation_area" id="implementation_area" class="form-select" required disabled>
                                            <option value="" selected disabled>Select Province First</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Barangay</label>
                                        <select name="implementation_brgy" id="implementation_brgy" class="form-select" disabled>
                                            <option value="" selected disabled>Select Municipality First</option>
                                        </select>
                                    </div>

                                   <div class="col-md-3">
                                     <label class="form-label fw-semibold small">District</label>
                                     <select name="implementation_district" id="implementation_district" class="form-select" style="width: 100%;" required>
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
                                        <label class="form-label fw-semibold small">Admin Cost %</label>
                                        <select name="wage_percentage" id="wage_percentage" class="form-select" required>
                                            <option value="">Percentage</option>
                                            <option value="2.5">2.5%</option>
                                            <option value="3">3%</option>                          
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">Subsidy Cost</label>
                                        <input type="text" name="subsidy_cost" class="form-control" step="0.01" placeholder="Subsidy Cost" required autocomplete="OFF">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">Admin Cost</label>
                                        <input type="number" name="admin_cost" class="form-control" step="0.01" placeholder="Admin" required autocomplete="OFF">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">GPAI (Source of Funds)</label>
                                        <input type="text" name="gpai_info" class="form-control" placeholder="GPAI Funding" required autocomplete="OFF">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">WAGE (Source of Funds)</label>
                                        <input type="text" name="wage_info" class="form-control" placeholder="Wage Funding" required autocomplete="OFF">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">LGU Classification</label>
                                        <input type="text" name="implementation_classification" class="form-control" placeholder="LGU Class" autocomplete="OFF" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Proponent</label>
                                        <select name="imp_proponent" id="imp_proponent" class="form-select" style="width: 100%;" required>
                                            <option value="" selected disabled>-- Select or type Proponent --</option>
                                            <?php if (!empty($proponents)): ?>
                                                <?php foreach ($proponents as $prop): ?>
                                                    <option value="<?= html_escape($prop['proponent_id']); ?>">
                                                        <?= html_escape($prop['proponent_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <i><font color="red" size="2px">* Proponent not listed? <a href="<?= site_url('ADL/proponent_encode'); ?>">Add it here</a></i></font>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Sponsor</label>
                                        <input type="text" name="imp_sponsor" class="form-control" placeholder="Sponsor" autocomplete="OFF" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Overall Remarks</label>
                                        <input type="text" name="remarks" class="form-control" placeholder="Remarks" autocomplete="OFF">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: APPRAISAL & PPES -->
                            <div class="tab-pane fade" id="appraisal-pane" role="tabpanel" aria-labelledby="appraisal-tab">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Appraisal Date Submitted</label>
                                        <input type="text" name="appraisal_date_submitted" class="form-control" placeholder="Date Submitted" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Appraisal Date Approved</label>
                                        <input type="text" name="appraisal_date_approved" class="form-control" placeholder="Date Approved" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'" >
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">PPES RIS No.</label>
                                        <input type="text" name="ppes_issuance_ris" class="form-control" placeholder="RIS Number" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">PPES Date Issued</label>
                                        <input type="text" name="ppes_date_issued" class="form-control" placeholder="Date Issued" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">PPES Count</label>
                                        <input type="number" name="ppes_count" id="ppes_count" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">PPES Female</label>
                                        <input type="number" name="ppes_female" id="ppes_female" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">PPES Amount</label>
                                        <input type="text" name="ppes_amount" id="ppes_amount" class="form-control" placeholder="0.00" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: ORIENTATION & GSIS -->
                            <div class="tab-pane fade" id="orientation-pane" role="tabpanel" aria-labelledby="orientation-tab">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Orientation Date</label>
                                        <input type="text" name="orientation_date" class="form-control" placeholder="Date Orientation" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Orientation Beneficiaries</label>
                                        <input type="number" name="orientation_benefs" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Employment Period</label>
                                        <input type="text" name="orientation_employment_period" class="form-control" placeholder="Employment Period">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">GSIS Enrollment Date</label>
                                        <input type="text" name="gsis_enrollment_date" class="form-control" placeholder="GSIS Enrollment Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">GSIS Beneficiaries</label>
                                        <input type="number" name="gsis_enrollment_benefs" id="gsis_benefs" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">GSIS Female</label>
                                        <input type="number" name="gsis_enrollment_female" id="gsis_female" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">GSIS Amount</label>
                                        <input type="text" name="gsis_enrollment_amount" id="gsis_amount" class="form-control" placeholder="0.00" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: IMPLEMENTATION & COMPLETION STATUS -->
                            <div class="tab-pane fade" id="implementation-pane" role="tabpanel" aria-labelledby="implementation-tab">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Implementation Start Date</label>
                                        <input type="text" name="ongoing_implementation_start_date" class="form-control" placeholder="Start Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Implementation End Date</label>
                                        <input type="text" name="ongoing_implementation_end_date" class="form-control" placeholder="End Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Ongoing Beneficiaries</label>
                                        <input type="number" name="ongoing_implementation_benefs" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Completed Period</label>
                                        <input type="text" name="completed_employment_period" class="form-control" placeholder="Period">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Completed Beneficiaries</label>
                                        <input type="number" name="completed_employment_benefs" id="completed_benefs" class="form-control" placeholder="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Completed Amount</label>
                                        <input type="text" name="completed_employment_amount" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Documentation Status</label>
                                        <input type="text" name="completed_employment_documentation" class="form-control" placeholder="Remarks/Status">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: PAYMENT & PAYOUT DETAILS -->
                            <div class="tab-pane fade" id="payment-pane" role="tabpanel" aria-labelledby="payment-tab">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">ALOB No.</label>
                                        <input type="text" name="payment_alob_no" class="form-control" placeholder="ALOB Number">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">DV No.</label>
                                        <input type="text" name="payment_dv_no" class="form-control" placeholder="DV Number">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">Check No.</label>
                                        <input type="text" name="payment_check_no" class="form-control" placeholder="Check Number">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold small">Payment Date</label>
                                        <input type="text" name="payment_date" class="form-control" placeholder="Payment Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Payment Amount</label>
                                        <input type="text" name="payment_amount" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Payout Date</label>
                                        <input type="text" name="payout_date" class="form-control" placeholder="Payout Date" onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Payout Method</label>
                                        <select id="payout_method" name="payout_method" class="form-select" style="color: #7a7979a9; font-style: italic;" 
                                            onchange="this.style.color='#000000'; this.style.fontStyle='normal';">
                                            <option value="" disabled selected>-- Select Payout Site --</option>
                                            <?php if (!empty($payoutSite)): ?>
                                                <?php foreach ($payoutSite as $pos): ?>
                                                    <option value="<?= html_escape($pos['payout_site_id']); ?>" data-rate="<?= html_escape($pos['service_cost'] ?? 0); ?>">
                                                        <?= html_escape($pos['payout_site_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                         <?php endif; ?>
                                         </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Service Cost</label>
                                        <input type="text" name="payout_service_cost" class="form-control" id="payout_service_cost" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <input type="hidden" name="encoded_date" value="<?= date('Y-m-d'); ?>">

                        <div class="col-12 mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="<?= site_url('adl/ADL_encode'); ?>" class="btn btn-light border px-4">Cancel</a>
                            <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Save Transaction Record
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <div class="modal fade" id="duplicateTransactionModal" tabindex="-1" aria-labelledby="duplicateTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title" id="duplicateTransactionModalLabel">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Duplicate Reference Number Found
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <p class="mb-0">The Implementation Reference Number <strong id="modalDuplicateRefNo"></strong> is already recorded in the database. Please use a unique Reference Number.</p>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function () {
        $('#adl_no').select2({
            theme: 'bootstrap-5',
            placeholder: '-Select or Type ADL-',
            allowClear: true
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

        const ppeRate = parseFloat("<?= $ppe_rate ?? 325; ?>") || 0;
        const gsisRate = parseFloat("<?= $gsis_rate ?? 50; ?>") || 0;

        $('#ppes_count').on('input', function () {
            const count = parseFloat($(this).val()) || 0;
            const totalAmount = count * ppeRate;
            $('#ppes_amount').val(totalAmount.toFixed(2));
        });

        $('#gsis_benefs').on('input', function () {
            const benefs = parseFloat($(this).val()) || 0;
            const totalGsisAmount = benefs * gsisRate;
            $('#gsis_amount').val(totalGsisAmount.toFixed(2));
        });

        $('#transactionForm').on('submit', function (e) {
            e.preventDefault();

            const $form = $(this);
            const $submitBtn = $('#submitBtn');
            const refNoInput = $('input[name="implementation_reference_no"]').val().trim();

            $.ajax({
                url: "<?= site_url('adl/check_duplicate_transaction'); ?>",
                type: "GET",
                data: { implementation_reference_no: refNoInput },
                dataType: "json",
                success: function (response) {
                    if (response.exists) {
                        $('#modalDuplicateRefNo').text(refNoInput);
                        const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateTransactionModal'));
                        duplicateModal.show();
                    } else {
                        $submitBtn.prop('disabled', true);
                        $submitBtn.html(`
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Saving Record...
                        `);
                        $form[0].submit();
                    }
                },
                error: function () {
                    alert('Error checking database for duplicate records. Please try again.');
                }
            });
        });

        $(document).on('change', '#implementation_province', function () {
            const provCode = $(this).val();
            const $cityMunSelect = $('#implementation_area');
            const $brgySelect = $('#implementation_brgy');

            $brgySelect.prop('disabled', true).html('<option value="" selected disabled>Select Municipality First</option>');

            if (provCode) {
                $cityMunSelect.prop('disabled', true).html('<option value="">Loading areas...</option>');

                $.ajax({
                    url: "<?= site_url('adl/get_municipalities_by_province'); ?>",
                    type: "GET",
                    data: { provCode: provCode },
                    dataType: "json",
                    success: function (data) {
                        $cityMunSelect.empty().append('<option value="" selected disabled>Select City/Municipality</option>');
                        if (data && data.length > 0) {
                            $.each(data, function (index, item) {
                                const munCode = item.citymunCode || item.cityCode; 
                                const munDesc = item.citymunDesc || item.cityDesc;

                                $cityMunSelect.append('<option value="' + munCode + '">' + munDesc + '</option>');
                            });
                            $cityMunSelect.prop('disabled', false);
                        } else {
                            $cityMunSelect.append('<option value="" disabled>No implementation areas found</option>');
                        }
                    },
                    error: function () {
                        $cityMunSelect.prop('disabled', false).html('<option value="" disabled>Error loading data</option>');
                    }
                });
            } else {
                $cityMunSelect.prop('disabled', true).html('<option value="" selected disabled>Select Province First</option>');
            }
        });

        $(document).on('change', '#implementation_area', function () {
            const citymunCode = $(this).val();
            const $brgySelect = $('#implementation_brgy');

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
                                $brgySelect.append('<option value="' + item.brgyCode + '">' + item.brgyDesc + '</option>');
                            });
                            $brgySelect.prop('disabled', false);
                        } else {
                            $brgySelect.append('<option value="" disabled>No barangays found</option>');
                        }
                    },
                    error: function () {
                        $brgySelect.prop('disabled', false).html('<option value="" disabled>Error loading data</option>');
                    }
                });
            } else {
                $brgySelect.prop('disabled', true).html('<option value="" selected disabled>Select Municipality First</option>');
            }
        });
    });

    $(document).ready(function() {
        $('#imp_proponent').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select or type Proponent --',
            allowClear: true
        });

        $('#implementation_district').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select District --',
            allowClear: true
        });
    });

    // Calculate Payout Service Cost dynamically
    function calculatePayoutServiceCost() {
        const benefs = parseFloat($('#completed_benefs').val()) || 0;
        const selectedOption = $('#payout_method').find(':selected');
        const rate = parseFloat(selectedOption.data('rate')) || 0;
        const totalServiceCost = benefs * rate;
        $('#payout_service_cost').val(totalServiceCost.toFixed(2));
    }

    $('#completed_benefs').on('input', calculatePayoutServiceCost);
    $('#payout_method').on('change', calculatePayoutServiceCost);

/**
 * Generate Reference Number (AJAX)
 * Automatically builds the reference number using ADL No, auto-incrementing sequence, 
 * province, municipality, and district values.
 */


// Automatically generate reference number using dropdown text descriptions
    function generateReferenceNo() {
        const adlNo = $('#adl_no').val();
        
        // Grab the text (descriptions) instead of the values (codes)
        const province = $('#implementation_province').val() ? $('#implementation_province option:selected').text().trim() : '';
        const municipality = $('#implementation_area').val() ? $('#implementation_area option:selected').text().trim() : '';
        const district = $('#implementation_district').val() ? $('#implementation_district option:selected').text().trim() : '';

        if (adlNo) {
            $.ajax({
                url: "<?= site_url('adl/get_generated_reference_no'); ?>",
                type: "GET",
                data: {
                    adl_no: adlNo,
                    province: province,
                    municipality: municipality,
                    district: district
                },
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        $('#implementation_reference_no').val(response.ref_no);
                    }
                }
            });
        } else {
            $('#implementation_reference_no').val('');
        }
    }

    // Trigger generation when any of the key fields change
    $(document).on('change', '#adl_no, #implementation_province, #implementation_area, #implementation_district', function() {
        generateReferenceNo();
    });
    </script>
</body>

</html>