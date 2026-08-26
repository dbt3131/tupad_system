<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TUPAD Payroll Monitoring - TUPAD IS</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>

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

        .table-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 1400px), (min-resolution: 120dppx) {
            #editModal .modal-dialog, 
            #encodeModal .modal-dialog {
                max-width: 95vw !important;
                margin: 1.75rem auto !important;
            }
        }

        #editModal .modal-content, 
        #encodeModal .modal-content {
            max-height: 85vh !important;
            display: flex !important;
            flex-direction: column !important;
        }

        #editModal .modal-header, 
        #encodeModal .modal-header,
        #editModal .modal-footer, 
        #encodeModal .modal-footer {
            flex-shrink: 0 !important;
        }

        #editModal .modal-body, 
        #encodeModal .modal-body {
            overflow-y: auto !important;
            flex-grow: 1 !important;
        }

        .btn-action {
            width: 75px !important;
            height: 30px !important;
            padding: 0 !important;
            line-height: 28px !important;
            font-size: 0.75rem !important;
            display: inline-block !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-journal-check text-primary me-2"></i>TUPAD Payroll Monitoring
                    </h3>
                    <p class="text-muted small mb-0">Manage and track received payroll entries</p>
                </div>
            </div>

            <!-- Table Card -->
            <div class="container-fluid px-0">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span><strong>Received Payrolls Database Matrix</strong></span>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#encodeModal">+ Encode New Payroll</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="payrollsTable" class="table table-bordered table-striped table-sm text-center align-middle" style="font-size: 0.8rem;">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Action</th>
                                        <th>Month Reported (DILEEP)</th>
                                        <th>Date Received</th>
                                        <th>Received By</th>
                                        <th>Nature of Project</th>
                                        <th>Batch No.</th>
                                        <th>Implementor Name</th>
                                        <th>Province</th>
                                        <th>Municipality</th>
                                        <th>Barangay</th>
                                        <th>ADL No.</th>
                                        <th>Fisher Folks Count</th>
                                        <th>Farmers Count</th>
                                        <th>Implementation Ref No.</th>
                                        <th>Payrolled Benefs</th>
                                        <th>Backouts Benefs</th>
                                        <th>Total Benefs</th>
                                        <th>No. of Females</th>
                                        <th>No. of Days</th>
                                        <th>Wage / Day</th>
                                        <th>Total Wages</th>
                                        <th>Payout Site</th>
                                        <th>Payout Amount</th>
                                        <th>ELCAC IP Count</th>
                                        <th>Convergence Project</th>
                                        <th>Fund Source (MDS)</th>
                                        <th>Fund Source (ORS)</th>
                                        <th>Date Processed (TSSD2)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($payrolls)): ?>
                                        <?php foreach ($payrolls as $row): ?>
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-action btn-sm edit-btn" 
                                                        data-id="<?= html_escape($row['id'] ?? ''); ?>"
                                                        data-month_reported_dileep="<?= html_escape($row['month_reported_dileep'] ?? ''); ?>"
                                                        data-date_received="<?= html_escape(date('Y-m-d\TH:i', strtotime($row['date_received'] ?? ''))); ?>"
                                                        data-received_by="<?= html_escape($row['received_by'] ?? ''); ?>"
                                                        data-nature_of_project="<?= html_escape($row['nature_of_project'] ?? ''); ?>"
                                                        data-batch_no="<?= html_escape($row['batch_no'] ?? ''); ?>"
                                                        data-implementor_name="<?= html_escape($row['implementor_name'] ?? ''); ?>"
                                                        data-province="<?= html_escape($row['province'] ?? ''); ?>"
                                                        data-municipality="<?= html_escape($row['municipality'] ?? ''); ?>"
                                                        data-barangay="<?= html_escape($row['barangay'] ?? ''); ?>"
                                                        data-adl_no="<?= html_escape($row['adl_no'] ?? ''); ?>"
                                                        data-fisher_folks_count="<?= html_escape($row['fisher_folks_count'] ?? 0); ?>"
                                                        data-farmers_count="<?= html_escape($row['farmers_count'] ?? 0); ?>"
                                                        data-implementation_reference_no="<?= html_escape($row['implementation_reference_no'] ?? ''); ?>"
                                                        data-no_of_payrolled_benefs="<?= html_escape($row['no_of_payrolled_benefs'] ?? 0); ?>"
                                                        data-no_of_backouts_benefs="<?= html_escape($row['no_of_backouts_benefs'] ?? 0); ?>"
                                                        data-total_no_benefs="<?= html_escape($row['total_no_benefs'] ?? 0); ?>"
                                                        data-no_of_females="<?= html_escape($row['no_of_females'] ?? 0); ?>"
                                                        data-no_of_days="<?= html_escape($row['no_of_days'] ?? 0); ?>"
                                                        data-wage_per_day="<?= html_escape($row['wage_per_day'] ?? ''); ?>"
                                                        data-total_wages="<?= html_escape($row['total_wages'] ?? ''); ?>"
                                                        data-payout_site="<?= html_escape($row['payout_site'] ?? ''); ?>"
                                                        data-payout_amount="<?= html_escape($row['payout_amount'] ?? ''); ?>"
                                                        data-elcac_ip_count="<?= html_escape($row['elcac_ip_count'] ?? 0); ?>"
                                                        data-convergence_project="<?= html_escape($row['convergence_project'] ?? ''); ?>"
                                                        data-fund_source_mds="<?= html_escape($row['fund_source_mds'] ?? ''); ?>"
                                                        data-fund_source_ors="<?= html_escape($row['fund_source_ors'] ?? ''); ?>"
                                                        data-date_processed_tssd2="<?= html_escape(date('Y-m-d\TH:i', strtotime($row['date_processed_tssd2'] ?? ''))); ?>"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        title="Edit Entry">
                                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                                    </button>
                                                </td>
                                                <td><?= html_escape($row['month_reported_dileep'] ?? ''); ?></td>
                                                <td><?= html_escape($row['date_received'] ?? ''); ?></td>
                                                <td><?= html_escape($row['received_by'] ?? ''); ?></td>
                                                <td><?= html_escape($row['nature_of_project'] ?? ''); ?></td>
                                                <td><?= html_escape($row['batch_no'] ?? ''); ?></td>
                                                <td><?= html_escape($row['implementor_name'] ?? ''); ?></td>
                                                <td><?= html_escape($row['province_name'] ?? ''); ?></td>
                                                <td><?= html_escape($row['municipality_name'] ?? ''); ?></td>
                                                <td><?= html_escape($row['barangay'] ?? ''); ?></td>
                                                <td><?= html_escape($row['adl_no'] ?? ''); ?></td>
                                                <td><?= number_format($row['fisher_folks_count'] ?? 0); ?></td>
                                                <td><?= number_format($row['farmers_count'] ?? 0); ?></td>
                                                <td><code><?= html_escape($row['implementation_reference_no'] ?? ''); ?></code></td>
                                                <td><?= number_format($row['no_of_payrolled_benefs'] ?? 0); ?></td>
                                                <td><?= number_format($row['no_of_backouts_benefs'] ?? 0); ?></td>
                                                <td class="fw-bold"><?= number_format($row['total_no_benefs'] ?? 0); ?></td>
                                                <td><?= number_format($row['no_of_females'] ?? 0); ?></td>
                                                <td><?= html_escape($row['no_of_days'] ?? 0); ?></td>
                                                <td><?= html_escape($row['wage_per_day'] ?? ''); ?></td>
                                                <td class="fw-bold text-primary"><?= html_escape($row['total_wages'] ?? ''); ?></td>
                                                <td><?= html_escape($row['payout_site'] ?? ''); ?></td>
                                                <td><?= html_escape($row['payout_amount'] ?? ''); ?></td>
                                                <td><?= number_format($row['elcac_ip_count'] ?? 0); ?></td>
                                                <td><?= html_escape($row['convergence_project'] ?? ''); ?></td>
                                                <td><?= html_escape($row['fund_source_mds'] ?? ''); ?></td>
                                                <td><?= html_escape($row['fund_source_ors'] ?? ''); ?></td>
                                                <td><?= html_escape($row['date_processed_tssd2'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="bg-white border-top p-3 text-center text-muted small">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Encoding Modal -->
    <div class="modal fade" id="encodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form id="addPayrollForm" action="<?= site_url('tupad_payrolls/store'); ?>" method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Encode New Received Payroll</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">1. Receiving & Identification</h6>
                        <div class="row g-3 mb-4">


                                <div class="col-md-3">
                                    <label class="form-label">Reported on DILEEP</label>
                                    <select id="month_reported_dileep" name="month_reported_dileep" class="form-select" required>
                        <option value="">-- Select Month --</option>
                        <?php if (!empty($monthDileep)): ?>
                            <?php foreach ($monthDileep as $md): ?>
                            <option value="<?= $md['month_id']; ?>" <?= set_select('month_id', $md['month_id']); ?>>
                                <?= $md['month_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                            </select>
                                </div>


                            <div class="col-md-3">
                                <label class="form-label">Date Received</label>
                                <input type="datetime-local" name="date_received" class="form-control" required>
                            </div>



                            <div class="col-md-3">
                                <label class="form-label">Received By</label>
                                <select id="received_by" name="received_by" class="form-select" required>
                      <option value="">-- Select Staff --</option>
                      <?php if (!empty($receivedStaff)): ?>
                        <?php foreach ($receivedStaff as $rs): ?>
                          <option value="<?= $rs['staff_id']; ?>" <?= set_select('staff_id', $rs['staff_id']); ?>>
                            <?= $rs['staff_name']; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                        </select>
                            </div>




                            <div class="col-md-3">
                                <label class="form-label">Batch No.</label>
                                <input type="text" name="batch_no" class="form-control" required autocomplete="off">
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">2. Project & Location Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Nature of Project</label>
                                <input type="text" name="nature_of_project" class="form-control" required autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Implementor Name</label>
                                <input type="text" name="implementor_name" class="form-control" required autocomplete="off">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Province</label>
                                <select id="encode_province" name="province" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <?php if (!empty($provinces)): ?>
                                        <?php foreach ($provinces as $prov): ?>
                                            <option value="<?= html_escape($prov['provCode']); ?>" data-code="<?= $prov['provCode']; ?>"><?= html_escape($prov['provDesc']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Municipality</label>
                                <select id="encode_municipality" name="municipality" class="form-select" required disabled>
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Barangay</label>
                                    <input type="text" id="encode_barangay" name="barangay" class="form-control" required placeholder="Enter Barangay">
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">3. Beneficiary Counts & Financial Metrics</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label class="form-label">ADL No.</label>
                                <input type="text" name="adl_no" class="form-control" required autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Implementation Ref No.</label>
                                <input type="text" name="implementation_reference_no" class="form-control" required autocomplete="off">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fisher Folks Count</label>
                                <input type="number" name="fisher_folks_count" class="form-control" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Farmers Count</label>
                                <input type="number" name="farmers_count" class="form-control" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ELCAC IP Count</label>
                                <input type="number" name="elcac_ip_count" class="form-control" value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payrolled Benefs</label>
                                <input type="number" id="encode_no_of_payrolled_benefs" name="no_of_payrolled_benefs" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Backouts Benefs</label>
                                <input type="number" id="encode_no_of_backouts_benefs" name="no_of_backouts_benefs" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Total Benefs</label>
                                <input type="number" id="encode_total_no_benefs" name="total_no_benefs" class="form-control" value="0" readonly tabindex="-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No. of Females</label>
                                <input type="number" name="no_of_females" class="form-control" required>
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">4. Wages, Payout & Processing</h6>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">No. of Days</label>
                                <input type="number" name="no_of_days" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Wage Per Day</label>
                                <input type="text" name="wage_per_day" class="form-control" required autocomplete="off">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Total Wages</label>
                                <input type="text" name="total_wages" class="form-control" required autocomplete="off">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Payout Site</label>
                                <select id="payout_site" name="payout_site" class="form-select" required>
                      <option value="">-- Select Payout Site --</option>
                      <?php if (!empty($payoutSite)): ?>
                        <?php foreach ($payoutSite as $pos): ?>
                          <option value="<?= $pos['payout_site_id']; ?>" <?= set_select('payout_site_id', $pos['payout_site_id']); ?>>
                            <?= $pos['payout_site_name']; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                        </select>
                            </div>



                            <div class="col-md-3">
                                <label class="form-label">Payout Amount</label>
                                <input type="text" id="encode_payout_amount" name="payout_amount" class="form-control" required autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Convergence Project</label>
                                <input type="text" name="convergence_project" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fund Source (MDS)</label>
                                <input type="text" name="fund_source_mds" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fund Source (ORS)</label>
                                <input type="text" name="fund_source_ors" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Processed (TSSD2)</label>
                                <input type="datetime-local" name="date_processed_tssd2" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Submit and Save Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form id="editPayrollForm" method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Received Payroll</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">1. Receiving & Identification</h6>
                        <div class="row g-3 mb-4">
                           
                            <div class="col-md-3">
                                <label class="form-label">Reported on DILEEP</label>
                                <select id="edit_month_reported_dileep" name="edit_month_reported_dileep" class="form-select" required>
                      <option value="">-- Select Month --</option>
                      <?php if (!empty($monthDileep)): ?>
                        <?php foreach ($monthDileep as $md): ?>
                          <option value="<?= $md['month_id']; ?>" <?= set_select('month_id', $md['month_id']); ?>>
                            <?= $md['month_name']; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                        </select>
                            </div>




                            <div class="col-md-3">
                                <label class="form-label">Date Received</label>
                                <input type="datetime-local" id="edit_date_received" name="date_received" class="form-control" required>
                            </div>




                           <div class="col-md-3">
                                <label class="form-label">Received By</label>
                                <select id="edit_received_by" name="received_by" class="form-select" required>
                      <option value="">-- Select Staff --</option>
                      <?php if (!empty($receivedStaff)): ?>
                        <?php foreach ($receivedStaff as $rs): ?>
                          <option value="<?= $rs['staff_id']; ?>" <?= set_select('staff_id', $rs['staff_id']); ?>>
                            <?= $rs['staff_name']; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                        </select>
                            </div>





                            <div class="col-md-3">
                                <label class="form-label">Batch No.</label>
                                <input type="text" id="edit_batch_no" name="batch_no" class="form-control" required>
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">2. Project & Location Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Nature of Project</label>
                                <input type="text" id="edit_nature_of_project" name="nature_of_project" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Implementor Name</label>
                                <input type="text" id="edit_implementor_name" name="implementor_name" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Province</label>
                                <select id="edit_province" name="province" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <?php if (!empty($provinces)): ?>
                                        <?php foreach ($provinces as $prov): ?>
                                            <option value="<?= html_escape($prov['provCode']); ?>" data-code="<?= $prov['provCode']; ?>"><?= html_escape($prov['provDesc']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Municipality</label>
                                <select id="edit_municipality" name="municipality" class="form-select" required disabled>
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                                <div class="col-md-2">
                                <label class="form-label">Barangay</label>
                                 <input type="text" id="edit_barangay" name="edit_barangay" class="form-control">
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">3. Beneficiary Counts & Financial Metrics</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label class="form-label">ADL No.</label>
                                <input type="text" id="edit_adl_no" name="adl_no" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Implementation Ref No.</label>
                                <input type="text" id="edit_implementation_reference_no" name="implementation_reference_no" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fisher Folks Count</label>
                                <input type="number" id="edit_fisher_folks_count" name="fisher_folks_count" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Farmers Count</label>
                                <input type="number" id="edit_farmers_count" name="farmers_count" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ELCAC IP Count</label>
                                <input type="number" id="edit_elcac_ip_count" name="elcac_ip_count" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payrolled Benefs</label>
                                <input type="number" id="edit_no_of_payrolled_benefs" name="no_of_payrolled_benefs" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Backouts Benefs</label>
                                <input type="number" id="edit_no_of_backouts_benefs" name="no_of_backouts_benefs" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Total Benefs</label>
                                <input type="number" id="edit_total_no_benefs" name="total_no_benefs" class="form-control" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No. of Females</label>
                                <input type="number" id="edit_no_of_females" name="no_of_females" class="form-control" required>
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">4. Wages, Payout & Processing</h6>
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">No. of Days</label>
                                <input type="number" id="edit_no_of_days" name="no_of_days" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Wage Per Day</label>
                                <input type="text" id="edit_wage_per_day" name="wage_per_day" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Total Wages</label>
                                <input type="text" id="edit_total_wages" name="total_wages" class="form-control" required>
                            </div>



                            <!-- Payout Site Dropdown -->
<div class="col-md-3">
    <label class="form-label">Payout Site</label>
    <select id="edit_payout_site" name="payout_site" class="form-select" required>
        <option value="">-- Select Payout Site --</option>
        <?php if (!empty($payoutSite)): ?>
            <?php foreach ($payoutSite as $pos): ?>
                <option value="<?= html_escape($pos['payout_site_id']); ?>">
                    <?= html_escape($pos['payout_site_name']); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>



                            <div class="col-md-3">
                                <label class="form-label">Payout Amount</label>
                                <input type="text" id="edit_payout_amount" name="payout_amount" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Convergence Project</label>
                                <input type="text" id="edit_convergence_project" name="convergence_project" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fund Source (MDS)</label>
                                <input type="text" id="edit_fund_source_mds" name="fund_source_mds" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fund Source (ORS)</label>
                                <input type="text" id="edit_fund_source_ors" name="fund_source_ors" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Processed (TSSD2)</label>
                                <input type="datetime-local" id="edit_date_processed_tssd2" name="date_processed_tssd2" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const updatePayrollUrl = "<?= site_url('tupad_payrolls/update/'); ?>";
    const getMunicipalitiesUrl = "<?= site_url('tupad_payrolls/get_municipalities'); ?>";

    $(document).ready(function () {
        // Sidebar toggle logic
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });

        // DataTables Initialisation
        $('#payrollsTable').DataTable({
            "scrollX": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "columnDefs": [
                { "orderable": false, "targets": [0] },
                { "defaultContent": "-", "targets": "_all" }
            ],
            "language": {
                "emptyTable": "No payroll logs found. Click \"+ Encode New Payroll\" to begin."
            }
        });

        // Reusable Province -> Municipality Cascade Handler
        function bindLocationCascade(provElem, cityElem) {
            $(provElem).on('change', function () {
                const provCode = $(this).find(':selected').data('code');
                $(cityElem).html('<option value="">-- Select --</option>').prop('disabled', true);

                if (provCode) {
                    $.post(getMunicipalitiesUrl, { provCode: provCode }, function (cities) {
                        $.each(cities, function (i, city) {
                            $(cityElem).append(
                                $('<option></option>')
                                    .val(city.cityCode)
                                    .text(city.citymunDesc)
                                    .attr('data-code', city.cityCode)
                            );
                        });
                        $(cityElem).prop('disabled', false);
                    }, 'json');
                }
            });
        }

        // Bind location cascades for Encode & Edit forms
        bindLocationCascade('#encode_province', '#encode_municipality');
        bindLocationCascade('#edit_province', '#edit_municipality');

        // Edit Button Trigger
        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            $('#editPayrollForm').attr('action', updatePayrollUrl + id);

            // Populate text inputs
            $('#edit_month_reported_dileep').val($(this).data('month_reported_dileep'));
            $('#edit_date_received').val($(this).data('date_received'));
            $('#edit_received_by').val($(this).data('received_by'));
            $('#edit_nature_of_project').val($(this).data('nature_of_project'));
            $('#edit_batch_no').val($(this).data('batch_no'));
            $('#edit_implementor_name').val($(this).data('implementor_name'));
            
            // Set Barangay Text Field
            $('#edit_barangay').val($(this).data('barangay'));

            // Handle Province & Municipality Dropdowns
            const selProv = $(this).data('province');
            const selCity = $(this).data('municipality');

            $('#edit_province').val(selProv);
            const provCode = $('#edit_province').find(':selected').data('code') || selProv;

            if (provCode) {
                $.post(getMunicipalitiesUrl, { provCode: provCode }, function (cities) {
                    let $muni = $('#edit_municipality');
                    $muni.html('<option value="">-- Select --</option>');
                    
                    let matchedCityCode = null;
                    $.each(cities, function (i, city) {
                        let isSelected = (city.cityCode == selCity || city.citymunDesc == selCity);
                        if (isSelected) matchedCityCode = city.cityCode;

                        $muni.append(
                            $('<option></option>')
                                .val(city.cityCode)
                                .text(city.citymunDesc)
                                .attr('data-code', city.cityCode)
                        );
                    });

                    $muni.val(matchedCityCode || selCity).prop('disabled', false);
                }, 'json');
            } else {
                $('#edit_municipality').html('<option value="">-- Select --</option>').prop('disabled', true);
            }

            // Populate numerical and remaining text inputs
            $('#edit_adl_no').val($(this).data('adl_no'));
            $('#edit_fisher_folks_count').val($(this).data('fisher_folks_count'));
            $('#edit_farmers_count').val($(this).data('farmers_count'));
            $('#edit_implementation_reference_no').val($(this).data('implementation_reference_no'));
            $('#edit_no_of_payrolled_benefs').val($(this).data('no_of_payrolled_benefs'));
            $('#edit_no_of_backouts_benefs').val($(this).data('no_of_backouts_benefs'));
            $('#edit_total_no_benefs').val($(this).data('total_no_benefs'));
            $('#edit_no_of_females').val($(this).data('no_of_females'));
            $('#edit_no_of_days').val($(this).data('no_of_days'));
            $('#edit_wage_per_day').val($(this).data('wage_per_day'));
            $('#edit_total_wages').val($(this).data('total_wages'));
            $('#edit_payout_site').val($(this).data('payout_site'));
            $('#edit_payout_amount').val($(this).data('payout_amount'));
            $('#edit_elcac_ip_count').val($(this).data('elcac_ip_count'));
            $('#edit_convergence_project').val($(this).data('convergence_project'));
            $('#edit_fund_source_mds').val($(this).data('fund_source_mds'));
            $('#edit_fund_source_ors').val($(this).data('fund_source_ors'));
            $('#edit_date_processed_tssd2').val($(this).data('date_processed_tssd2'));
        });
    });

    // Calculate Total Benefs automatically (Payrolled - Backouts)
function calculateEncodeTotalBenefs() {
    const payrolled = parseInt($('#encode_no_of_payrolled_benefs').val()) || 0;
    const backouts = parseInt($('#encode_no_of_backouts_benefs').val()) || 0;
    
    // Calculate difference (prevent negative numbers)
    const total = Math.max(0, payrolled - backouts);
    
    $('#encode_total_no_benefs').val(total);
}

// Trigger calculation whenever inputs change
$(document).on('input change', '#encode_no_of_payrolled_benefs, #encode_no_of_backouts_benefs', function () {
    calculateEncodeTotalBenefs();
});

// Reset calculation on modal open
$('#encodeModal').on('show.bs.modal', function () {
    $('#addPayrollForm')[0].reset();
    $('#encode_municipality').html('<option value="">-- Select --</option>').prop('disabled', true);
    calculateEncodeTotalBenefs();
});

// Calculate Payout Amount based on Payout Site selection and Beneficiaries count
function calculateEncodePayoutAmount() {
    const benefsCount = parseFloat($('#encode_no_of_payrolled_benefs').val()) || 0;
    const payoutSiteVal = $('#payout_site').val();
    let multiplier = 0;

    if (payoutSiteVal === "1") {
        multiplier = 50;
    } else if (payoutSiteVal === "2") {
        multiplier = 20;
    }

    const totalPayout = benefsCount * multiplier;

    if (totalPayout > 0) {
        // Explicitly format with US locale (adds commas and .00)
        const formatted = totalPayout.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Temporarily unbind input handlers in case another script strips commas on change
        $('#encode_payout_amount').off('input.temp').val(formatted);
    } else {
        $('#encode_payout_amount').val('');
    }
}

// Event Bindings
$(document).on('input change', '#encode_no_of_payrolled_benefs, #payout_site', function () {
    calculateEncodePayoutAmount();
});

// Clean payout amount before submitting the encode form
$('#addPayrollForm').on('submit', function () {
    const $amountInput = $('#encode_payout_amount');
    let rawVal = $amountInput.val();

    if (rawVal) {
        // Remove commas and split by decimal to retain only whole numbers
        rawVal = rawVal.replace(/,/g, '').split('.')[0];
        $amountInput.val(rawVal);
    }
});

// 1. Total Beneficiaries Calculation for Edit Modal
function calculateEditTotalBenefs() {
    const payrolled = parseInt($('#edit_no_of_payrolled_benefs').val()) || 0;
    const backouts = parseInt($('#edit_no_of_backouts_benefs').val()) || 0;
    
    const total = Math.max(0, payrolled - backouts);
    $('#edit_total_no_benefs').val(total);
}

// 2. Payout Amount Calculation for Edit Modal
function calculateEditPayoutAmount() {
    const benefsCount = parseFloat($('#edit_no_of_payrolled_benefs').val()) || 0;
    const payoutSiteVal = $('#edit_payout_site').val();
    let multiplier = 0;

    if (payoutSiteVal === "1") {
        multiplier = 50;
    } else if (payoutSiteVal === "2") {
        multiplier = 20;
    }

    const totalPayout = benefsCount * multiplier;

    if (totalPayout > 0) {
        const formatted = totalPayout.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        $('#edit_payout_amount').off('input.temp').val(formatted);
    } else {
        $('#edit_payout_amount').val('');
    }
}

// 3. Event Listeners for Dynamic Calculation in Edit Form
$(document).on('input change', '#edit_no_of_payrolled_benefs, #edit_no_of_backouts_benefs', function () {
    calculateEditTotalBenefs();
});

$(document).on('input change', '#edit_no_of_payrolled_benefs, #edit_payout_site', function () {
    calculateEditPayoutAmount();
});

// 4. Update the Edit Button Click Handler
// Inside your existing $(document).on('click', '.edit-btn', function () { ... })
// Replace the payout site assignment and trigger calculations at the end:

$('#edit_payout_site').val($(this).data('payout_site'));
calculateEditTotalBenefs();
calculateEditPayoutAmount();

// 5. Clean payout amount before submitting the Edit Form
$('#editPayrollForm').on('submit', function () {
    const $amountInput = $('#edit_payout_amount');
    let rawVal = $amountInput.val();

    if (rawVal) {
        rawVal = rawVal.replace(/,/g, '').split('.')[0];
        $amountInput.val(rawVal);
    }
});


</script>
</body>

</html>