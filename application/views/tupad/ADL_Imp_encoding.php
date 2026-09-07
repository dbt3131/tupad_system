<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Transactions Encoding - DOLE TUPAD</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
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
    </style>
</head>

<body>

    <!-- NAVBAR TEMPLATE VIEW -->
    <?php $this->load->view('templates/navbar'); ?>

    <!-- Main Content Wrapper -->
    <div id="main-content">
        
        <!-- SIDEBAR TEMPLATE VIEW -->
        <?php $this->load->view('templates/sidebar'); ?>

        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1">
            
            <!-- FLASH MESSAGES -->
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

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-clipboard2-data text-primary me-2"></i>ADL TRANSACTIONS ENCODING
                    </h3>
                    <p class="text-muted small mb-0">Record and monitor transaction details for Authority to Debit Line</p>
                </div>
            </div>

            <!-- Encoding Form Container -->
            <div class="container-fluid px-0">
                <div class="form-card p-4 p-md-5">
                    
                    <form action="<?= site_url('adl/store_transaction'); ?>" method="POST" id="transactionForm">
                        
                        <!-- TAB NAVIGATION HEADERS -->
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

                        <!-- TAB CONTENT PANES -->
                        <div class="tab-content" id="encodingTabsContent">
                            
                            <!-- TAB 1: GENERAL INFORMATION -->
                            <div class="tab-pane fade show active" id="general-pane" role="tabpanel" aria-labelledby="general-tab">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">ADL Number</label>
                                        <select id="adl_no" name="adl_no" class="form-select" required>
                                            <option value="">-- Select ADL --</option>
                                            <?php if (!empty($ADL)): ?>
                                                <?php foreach ($ADL as $ad): ?>
                                                    <option value="<?= html_escape($ad['adl_no']); ?>" <?= set_select('adl_no', $ad['adl_no']); ?>>
                                                        <?= html_escape($ad['adl_no']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Implementation Reference No.</label>
                                        <input type="text" name="implementation_reference_no" class="form-control" placeholder="Reference No." required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Date Coordinated</label>
                                        <input type="date" name="status_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
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
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Implementation Area (City/Municipality)</label>
                                        <select name="implementation_area" id="implementation_area" class="form-select" required disabled>
                                            <option value="" selected disabled>Select Province First</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Proponent</label>
                                        <input type="text" name="imp_proponent" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Sponsor</label>
                                        <input type="text" name="imp_sponsor" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: APPRAISAL & PPES -->
                            <div class="tab-pane fade" id="appraisal-pane" role="tabpanel" aria-labelledby="appraisal-tab">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Appraisal Date Submitted</label>
                                        <input type="date" name="appraisal_date_submitted" class="form-control" >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Appraisal Date Approved</label>
                                        <input type="date" name="appraisal_date_approved" class="form-control" >
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">PPES Issuance RIS</label>
                                        <input type="text" name="ppes_issuance_ris" class="form-control" placeholder="RIS Number">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">PPES Date Issued</label>
                                        <input type="date" name="ppes_date_issued" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">PPES Count</label>
                                        <input type="number" name="ppes_count" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">PPES Amount</label>
                                        <input type="text" name="ppes_amount" class="form-control" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: ORIENTATION & GSIS -->
                            <div class="tab-pane fade" id="orientation-pane" role="tabpanel" aria-labelledby="orientation-tab">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Orientation Date</label>
                                        <input type="date" name="orientation_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Orientation Beneficiaries</label>
                                        <input type="number" name="orientation_benefs" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Employment Period</label>
                                        <input type="text" name="orientation_employment_period" class="form-control" placeholder="e.g., 10 Days">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">GSIS Enrollment Date</label>
                                        <input type="date" name="gsis_enrollment_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">GSIS Beneficiaries</label>
                                        <input type="number" name="gsis_enrollment_benefs" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">GSIS Amount</label>
                                        <input type="text" name="gsis_enrollment_amount" class="form-control" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: IMPLEMENTATION & COMPLETION STATUS -->
                            <div class="tab-pane fade" id="implementation-pane" role="tabpanel" aria-labelledby="implementation-tab">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Ongoing Start Date</label>
                                        <input type="date" name="ongoing_implementation_start_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Ongoing End Date</label>
                                        <input type="date" name="ongoing_implementation_end_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Ongoing Beneficiaries</label>
                                        <input type="number" name="ongoing_implementation_benefs" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Completed Period</label>
                                        <input type="text" name="completed_employment_period" class="form-control" placeholder="Period">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Completed Beneficiaries</label>
                                        <input type="number" name="completed_employment_benefs" class="form-control" value="0">
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
                                        <input type="text" name="payment_alob_no" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">DV No.</label>
                                        <input type="text" name="payment_dv_no" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Check No.</label>
                                        <input type="text" name="payment_check_no" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Payment Amount</label>
                                        <input type="text" name="payment_amount" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Payout Date</label>
                                        <input type="date" name="payout_date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small">Service Cost</label>
                                        <input type="text" name="payout_service_cost" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Payout Method</label>
                                        <input type="text" name="payout_method" class="form-control" placeholder="e.g., Direct Cash / Palawan / MLhuillier">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Hidden Meta Fields -->
                        <input type="hidden" name="encoded_date" value="<?= date('Y-m-d'); ?>">

                        <!-- Submit Action Buttons -->
                        <div class="col-12 mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="<?= site_url('adl/ADL_encode'); ?>" class="btn btn-light border px-4">Cancel</a>
                            <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Save Transaction Record
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts -->
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

        // Prevent Multiple Form Submissions
        $('#transactionForm').on('submit', function (e) {
            const $form = $(this);
            const $submitBtn = $('#submitBtn');

            if ($form[0].checkValidity() === false) {
                // If native HTML5 validation fails, optionally switch back to the tab containing the error
                return; 
            }

            $submitBtn.prop('disabled', true);
            $submitBtn.html(`
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Saving Record...
            `);
        });

        // Dynamic Dependent Dropdown for Implementation Area (City/Municipality) based on Province Code[cite: 5, 6]
        $('#implementation_province').on('change', function () {
            const provCode = $(this).val();
            const $cityMunSelect = $('#implementation_area');

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
                                // Mapped using standard refcitymun column fields (citymunCode and citymunDesc)[cite: 5]
                                $cityMunSelect.append('<option value="' + item.cityCode + '">' + item.citymunDesc + '</option>');
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
    });
    </script>
</body>

</html>