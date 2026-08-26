<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uploaded Files - TUPAD IS</title>

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
       
        .stat-card {
            border: 1px solid var(--card-border);
            border-radius: 12px;
            background-color: #ffffff;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .table-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .table-card .card-header {
            background: #ffffff;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--card-border);
        }

        .table-container {
            padding: 1.25rem;
        }

        .table-container .dt-layout-row:last-child {
            margin-top: 15px;
        }

        .cursor-pointer {
            cursor: pointer;
        }    

        @media (max-width: 1400px), (min-resolution: 120dppx) {
            #editModal .modal-dialog, 
            #encodeModal .modal-dialog,
            #statusModal .modal-dialog {
                max-width: 95vw !important;
                margin: 1.75rem auto !important;
            }
        }

        #editModal .modal-content, 
        #encodeModal .modal-content,
        #statusModal .modal-content {
            max-height: 85vh !important;
            display: flex !important;
            flex-direction: column !important;
        }

        #editModal .modal-header, 
        #encodeModal .modal-header,
        #statusModal .modal-header,
        #editModal .modal-footer, 
        #encodeModal .modal-footer,
        #statusModal .modal-footer {
            flex-shrink: 0 !important;
        }

        #editModal .modal-body, 
        #encodeModal .modal-body,
        #statusModal .modal-body {
            overflow-y: auto !important;
            flex-grow: 1 !important;
        }

        .action-btn {
            width: 80px;            
            height: 32px;           
            padding: 0 6px;         
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;     
            white-space: nowrap;    
        }

        #allocationsTable .btn-action {
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
            
            <!-- FLASH MESSAGES SECTION -->
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
                        <i class="bi bi-folder-fill text-primary me-2"></i>Encoded Allocations
                    </h3>
                    <p class="text-muted small mb-0">Encode carefully</p>
                </div>
            </div>

            <!-- File List Table Card -->
            <div class="container-fluid px-0">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span><strong>2026 TUPAD Allocations Database Matrix</strong></span>
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#encodeModal">+ Encode New Allocation</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="allocationsTable" class="table table-bordered table-striped table-sm text-center align-middle" style="font-size: 0.8rem;">
                                <thead class="table-secondary">
                                    <tr>
                                        <th rowspan="2">Action</th>
                                        <th rowspan="2">Status Modal</th>
                                        <th rowspan="2">Group Toggle</th>
                                        <th rowspan="2">PRISM ID</th>
                                        <th rowspan="2">Date Coordinated</th>
                                        <th rowspan="2">Batch No</th>
                                        <th colspan="2" class="text-center">Source of Fund (MDS/CB-ORS No.)</th>
                                        <th rowspan="2">ADL No.</th>
                                        <th rowspan="2">Reference No.</th>
                                        <th rowspan="2">Sponsor</th>
                                        <th rowspan="2">Proponent / Recipient</th>
                                        <th rowspan="2">LGU Classification</th>
                                        <th colspan="3" class="text-center">Area of Implementation</th>
                                        <th rowspan="2">Physical Target</th>
                                        <th rowspan="2">Per Capita</th>
                                        <th rowspan="2">PPE Rate</th>
                                        <th rowspan="2">No. of Days</th>
                                        <th colspan="2" class="text-center">Period of Work</th>
                                        <th rowspan="2">Subsidy (95%)</th>
                                        <th rowspan="2">Admin Cost (3%)</th>
                                        <th rowspan="2">Total Project Funds</th>
                                        <th colspan="4" class="text-center">Group Personal Accident Insurance (GPAI)</th>
                                        <th colspan="5" class="text-center">PPE Issuance</th>
                                        <th colspan="2" class="text-center">ID Issuance</th>
                                        <th colspan="3" class="text-center">Payment Status</th>
                                    </tr>
                                    <tr>
                                        <th>GPAI</th>
                                        <th>Wage</th>
                                        <th>District</th>
                                        <th>LGU/Mun/City</th>
                                        <th>Barangay</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>No. of Benefs</th>
                                        <th>No. of Female Benefs</th>
                                        <th>Insurance Amount</th>
                                        <th>Effectivity Date</th>
                                        <th>RIS Number</th>
                                        <th>Requested</th>
                                        <th>Issued</th>
                                        <th>Balance</th>
                                        <th>Issued Date</th>
                                        <th>Issued ID</th>
                                        <th>Issued ID Date</th>
                                        <th>Benefs Paid</th>
                                        <th>Benefs Female Paid</th>
                                        <th>Wage Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($allocations)): 
                                        foreach ($allocations as $row): 
                                            $has_duplicates = false;
                                            $uid = trim(strtoupper($row['unique_id'] ?? ''));
                                            
                                            if (!empty($uid) && $uid !== 'TEMP' && isset($unique_counts[$uid]) && $unique_counts[$uid] > 1) {
                                                $has_duplicates = true;
                                            }
                                    ?>
                                        <tr data-unique-id="<?= html_escape($uid); ?>">
                                            <td>
                                                <button type="button" class="btn btn-primary btn-action btn-sm edit-btn" 
                                                    data-id="<?= html_escape($row['id'] ?? ''); ?>"
                                                    data-unique_id="<?= html_escape($uid); ?>"
                                                    data-date_coordinated="<?= html_escape($row['date_coordinated'] ?? ''); ?>"
                                                    data-batch_no="<?= html_escape($row['batch_no'] ?? ''); ?>"
                                                    data-reference_no="<?= html_escape($row['reference_no'] ?? ''); ?>"
                                                    data-adl_no="<?= html_escape($row['adl_no'] ?? ''); ?>"
                                                    data-sponsor="<?= html_escape($row['sponsor'] ?? ''); ?>"
                                                    data-source_fund_gpai="<?= html_escape($row['source_fund_gpai'] ?? ''); ?>"
                                                    data-source_fund_wage="<?= html_escape($row['source_fund_wage'] ?? ''); ?>"
                                                    data-proponent_recipient="<?= html_escape($row['proponent_recipient'] ?? ''); ?>"
                                                    data-term="<?= html_escape($row['term'] ?? ''); ?>"
                                                    data-lgu_classification="<?= html_escape($row['lgu_classification'] ?? ''); ?>"
                                                    data-district="<?= html_escape($row['district'] ?? ''); ?>"
                                                    data-lgu_municipality_city="<?= html_escape($row['lgu_municipality_city'] ?? ''); ?>"
                                                    data-barangay="<?= html_escape($row['barangay'] ?? ''); ?>"
                                                    data-physical_target="<?= html_escape($row['physical_target'] ?? ''); ?>"
                                                    data-per_capita="<?= html_escape($row['per_capita'] ?? ''); ?>"
                                                    data-ppe_rate="<?= html_escape($row['ppe_rate'] ?? ''); ?>"
                                                    data-no_of_days_work="<?= html_escape($row['no_of_days_work'] ?? ''); ?>"
                                                    data-period_start="<?= html_escape($row['period_start'] ?? ''); ?>"
                                                    data-period_end="<?= html_escape($row['period_end'] ?? ''); ?>"
                                                    data-subsidy="<?= html_escape($row['subsidy'] ?? ''); ?>"
                                                    data-admin_cost="<?= html_escape($row['admin_cost'] ?? ''); ?>"
                                                    data-final_physical_target="<?= html_escape($row['final_physical_target'] ?? ''); ?>"
                                                    data-number_of_females="<?= html_escape($row['number_of_females'] ?? ''); ?>"                                            
                                                    data-gpai_date="<?= html_escape($row['gpai_date'] ?? ''); ?>"
                                                    data-final_physical_ppe_requested="<?= html_escape($row['final_physical_ppe_requested'] ?? ''); ?>"
                                                    data-final_physical_ppe_issued="<?= html_escape($row['final_physical_ppe_issued'] ?? ''); ?>"
                                                    data-date_issued_ppe="<?= html_escape($row['date_issued_ppe'] ?? ''); ?>"
                                                    data-issued_id="<?= html_escape($row['issued_id'] ?? ''); ?>"
                                                    data-date_id_issued="<?= html_escape($row['date_id_issued'] ?? ''); ?>"
                                                    data-ris_number="<?= html_escape($row['ppe_ris_number'] ?? ''); ?>"
                                                    data-benefs_paid="<?= html_escape($row['benefs_paid'] ?? ''); ?>"
                                                    data-percentage="<?= html_escape($row['percentage'] ?? ''); ?>"
                                                    data-benefs_female_paid="<?= html_escape($row['benefs_female_paid'] ?? ''); ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    title="Edit Entry">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                    class="btn btn-warning btn-sm status-modal-btn btn-action" 
                                                    data-id="<?= html_escape($row['id'] ?? ''); ?>"
                                                    data-implementation_status="<?= html_escape($row['implementation_status'] ?? ''); ?>"
                                                    data-absent_days="<?= html_escape($row['absent_days'] ?? ''); ?>"
                                                    data-orientation_schedule="<?= html_escape($row['orientation_schedule'] ?? ''); ?>"
                                                    data-ppe_request="<?= html_escape($row['ppe_request'] ?? ''); ?>"
                                                    data-ppe_pickup_schedule="<?= html_escape($row['ppe_pickup_schedule'] ?? ''); ?>"
                                                    data-remarks="<?= html_escape($row['remarks'] ?? ''); ?>"
                                                    data-mode_of_payout="<?= html_escape($row['mode_of_payout'] ?? ''); ?>"
                                                    data-work_program_date_submission="<?= html_escape($row['work_program_date_submission'] ?? ''); ?>"
                                                    data-benefs_paid="<?= html_escape($row['benefs_paid'] ?? ''); ?>"
                                                    data-benefs_female_paid="<?= html_escape($row['benefs_female_paid'] ?? ''); ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#statusModal"
                                                    title="Update Implementation Status">
                                                    <i class="bi bi-gear-fill me-1"></i> Status
                                                </button>
                                            </td>
                                            <td>
                                                <?php if ($has_duplicates): ?>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm toggle-group" data-unique-id="<?= html_escape($uid); ?>">
                                                        <i class="bi bi-eye-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>   
                                            <td><?= html_escape($row['unique_id'] ?? ''); ?></td>
                                            <td><?= html_escape($row['date_coordinated'] ?? ''); ?></td>
                                            <td><?= html_escape($row['batch_no'] ?? ''); ?></td>
                                            <td><?= html_escape($row['source_fund_gpai'] ?? ''); ?></td>
                                            <td><?= html_escape($row['source_fund_wage'] ?? ''); ?></td>
                                            <td><?= html_escape($row['adl_no'] ?? ''); ?></td>
                                            <td><code><?= html_escape($row['reference_no'] ?? ''); ?></code></td>
                                            <td><span class="badge bg-secondary"><?= html_escape($row['sponsor'] ?? ''); ?></span></td>
                                            <td><?= html_escape($row['proponent_recipient'] ?? ''); ?></td>
                                            <td><?= html_escape($row['lgu_classification'] ?? ''); ?></td>
                                            <td><?= html_escape($row['district'] ?? ''); ?></td>
                                            <td><?= html_escape($row['lgu_municipality_city'] ?? ''); ?></td>
                                            <td><?= !empty($row['barangay']) ? html_escape($row['barangay']) : '-'; ?></td>
                                            <td class="text-center"><?= number_format($row['physical_target'] ?? 0); ?></td>
                                            <td><?= number_format($row['per_capita'] ?? 0, 2); ?></td>
                                            <td><?= number_format($row['ppe_rate'] ?? 0, 2); ?></td>
                                            <td><?= html_escape($row['no_of_days_work'] ?? ''); ?></td>
                                            <td><?= html_escape($row['period_start'] ?? ''); ?></td>
                                            <td><?= html_escape($row['period_end'] ?? ''); ?></td>
                                            <td>₱<?= number_format($row['subsidy'] ?? 0, 2); ?></td>
                                            <td>₱<?= number_format($row['admin_cost'] ?? 0, 2); ?></td>
                                            <td class="fw-bold text-primary">₱<?= number_format($row['total_project_funds'] ?? 0, 2); ?></td>
                                            <td class="text-center"><?= html_escape($row['final_physical_target'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['number_of_females'] ?? ''); ?></td>
                                            <td class="fw-bold text-primary"><?= number_format($row['insurance_amount'] ?? 0, 2); ?></td>
                                            <td><?= html_escape($row['gpai_date'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['ppe_ris_number'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['final_physical_ppe_requested'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['final_physical_ppe_issued'] ?? ''); ?></td>                                      
                                     
                                            <?php 
                                                $requested = floatval($row['final_physical_ppe_requested'] ?? 0);
                                                $issued = floatval($row['final_physical_ppe_issued'] ?? 0);
                                                $balance = $requested - $issued;
                                                $absent_days = $row['absent_days'] ?? '';
                                                $paid_benefs = floatval($row['benefs_paid'] ?? 0);
                                                if ($absent_days !== ""){
                                                    $per_day = 600;
                                                    $absent_days_computation = floatval($absent_days) * $per_day;
                                                    $wage_amount_gross = $paid_benefs * 6000;
                                                    $wage_amount = $wage_amount_gross - $absent_days_computation;
                                                } else {
                                                    $wage_amount = $paid_benefs * 6000;
                                                }  
                                                $is_nonzero = ($balance != 0);
                                            ?>
                                            <td class="<?= $is_nonzero ? 'bg-danger text-white fw-bold' : ''; ?> text-center">
                                                <?= html_escape($balance); ?>
                                            </td>

                                            <td class="text-center"><?= html_escape($row['date_issued_ppe'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['issued_id'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['date_id_issued'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['benefs_paid'] ?? ''); ?></td>
                                            <td class="text-center"><?= html_escape($row['benefs_female_paid'] ?? ''); ?></td>
                                            <td class="text-center"><?= number_format($wage_amount, 2); ?></td>
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

    <!-- Datalist Options -->
    <datalist id="cityList">
        <?php if (!empty($cities)): ?>
            <?php foreach ($cities as $c): ?>
                <option value="<?= html_escape($c->citymunDesc); ?>"></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </datalist>

    <datalist id="brgylist">
        <?php if (!empty($brgys)): ?>
            <?php foreach ($brgys as $b): ?>
                <option value="<?= html_escape($b->brgyDesc); ?>"></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </datalist>

    <!-- Encoding Form Modal -->
    <div class="modal fade" id="encodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form id="addForm" action="<?= site_url('tupad_allocations/store'); ?>" method="post">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Encode New TUPAD Allocation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">1. Basic Identifiers & References</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label class="form-label">Date Coordinated</label>
                                <input type="date" name="date_coordinated" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Batch No.</label>
                                <input type="number" id="batch_no" name="batch_no" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Reference No.</label>
                                <input type="text" name="reference_no" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ADL No.</label>
                                <input type="text" name="adl_no" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sponsor</label>
                                <input type="text" name="sponsor" class="form-control" required autocomplete="OFF">
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">2. Fund Source & Proponent Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Source Fund (GPAI)</label>
                                <input type="text" name="source_fund_gpai" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Source Fund (Wage)</label>
                                <input type="text" name="source_fund_wage" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Proponent/Recipient</label>
                                <input type="text" name="proponent_recipient" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Term</label>
                                <select id="term" name="term" class="form-select" required>
                                    <option value="" disabled selected>Select Term</option>
                                    <option value="SHORT">SHORT</option>
                                    <option value="LONG">LONG</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Percentage</label>
                                <select id="percentage" name="percentage" class="form-select" required>
                                    <option value="" disabled selected>Select Percentage</option>
                                    <option value="3">3%</option>
                                    <option value="2.5">2.5%</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">3. Area of Implementation</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">LGU Classification</label>
                                <input type="text" id="lgu_classification" name="lgu_classification" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">District</label>
                                <input type="text" name="district" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">LGU Municipality/City</label>
                                <input type="text" id="lgu_municipality_city" name="lgu_municipality_city" class="form-control" list="cityList" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Barangay</label>
                                <input type="text" id="add_barangay" name="barangay" class="form-control" list="brgylist" autocomplete="OFF">
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">4. Targets, Period & Financial Metrics</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Physical Target</label>
                                <input type="number" name="physical_target" class="form-control" required autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Per Capita</label>
                                <input type="number" step="0.01" name="per_capita" class="form-control" autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">PPE Rate</label>
                                <input type="number" step="0.01" name="ppe_rate" class="form-control" autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No. of Days Work</label>
                                <input type="number" name="no_of_days_work" class="form-control" autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Period Start</label>
                                <input type="date" name="period_start" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Period End</label>
                                <input type="date" name="period_end" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Subsidy (95%)</label>
                                <input type="number" step="0.01" name="subsidy" class="form-control" autocomplete="OFF">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Admin Cost (3%)</label>
                                <input type="number" step="0.01" name="admin_cost" class="form-control" autocomplete="OFF">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Submit and Save Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Form Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form id="editForm" method="post">
                    <input type="hidden" id="edit_term_hidden" name="term_hidden" value="">

                    <div class="modal-header text-white" style="background-color: rgb(13 101 253);">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit TUPAD Allocation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal Body Nav Tabs Header -->
                    <div class="bg-light border-bottom px-3 pt-2">
                        <ul class="nav nav-tabs" id="editTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="edit-tab1-tab" data-bs-toggle="tab" data-bs-target="#edit-tab1" type="button" role="tab"><i class="bi bi-info-circle me-1"></i> Basic & Proponent</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="edit-tab2-tab" data-bs-toggle="tab" data-bs-target="#edit-tab2" type="button" role="tab"><i class="bi bi-geo-alt me-1"></i> Area & Metrics</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="edit-tab3-tab" data-bs-toggle="tab" data-bs-target="#edit-tab3" type="button" role="tab"><i class="bi bi-shield-check me-1"></i> GPAI Insurance</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="edit-tab4-tab" data-bs-toggle="tab" data-bs-target="#edit-tab4" type="button" role="tab"><i class="bi bi-box-seam me-1"></i> PPE & ID Issuance</button>
                            </li>
                        </ul>
                    </div>

                    <div class="modal-body">
                        <div class="tab-content" id="editTabContent">

                            <!-- TAB 1: BASIC & PROPONENT -->
                            <div class="tab-pane fade show active" id="edit-tab1" role="tabpanel">
                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: rgb(13 101 253);">1. Basic Identifiers & References</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-2">
                                        <label class="form-label">Date Coordinated</label>
                                        <input type="date" id="edit_date_coordinated" name="date_coordinated" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Batch No.</label>
                                        <input type="number" id="edit_batch_no" name="batch_no" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Reference No.</label>
                                        <input type="text" id="edit_reference_no" name="reference_no" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">ADL No.</label>
                                        <input type="text" id="edit_adl_no" name="adl_no" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Sponsor</label>
                                        <input type="text" id="edit_sponsor" name="sponsor" class="form-control" required autocomplete="OFF">
                                    </div>
                                </div>

                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: rgb(13 101 253);">2. Fund Source & Proponent Details</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label class="form-label">Source Fund (GPAI)</label>
                                        <input type="text" id="edit_source_fund_gpai" name="source_fund_gpai" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Source Fund (Wage)</label>
                                        <input type="text" id="edit_source_fund_wage" name="source_fund_wage" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Proponent/Recipient</label>
                                        <input type="text" id="edit_proponent_recipient" name="proponent_recipient" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Term</label>
                                        <select id="edit_term" name="term" class="form-select" required>
                                            <option value="" disabled selected>Select Term</option>
                                            <option value="SHORT">SHORT</option>
                                            <option value="LONG">LONG</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Percentage</label>
                                        <select id="edit_percentage" name="percentage" class="form-select" required>
                                            <option value="" disabled selected>Select Percentage</option>
                                            <option value="3">3%</option>
                                            <option value="2.5">2.5%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: AREA & METRICS -->
                            <div class="tab-pane fade" id="edit-tab2" role="tabpanel">
                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: rgb(13 101 253);">3. Area of Implementation</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label class="form-label">LGU Classification</label>
                                        <input type="text" id="edit_lgu_classification" name="lgu_classification" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">District</label>
                                        <input type="text" id="edit_district" name="district" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">LGU Municipality/City</label>
                                        <input type="text" id="edit_lgu_municipality_city" name="lgu_municipality_city" class="form-control" list="cityList" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Barangay</label>
                                        <input type="text" id="edit_barangay" name="barangay" class="form-control" list="brgylist" placeholder="Not Applicable" autocomplete="OFF">
                                    </div>
                                </div>

                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: rgb(13 101 253);">4. Targets, Period & Financial Metrics</h6>
                                <div class="row g-3 mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label">Physical Target</label>
                                        <input type="number" id="edit_physical_target" name="physical_target" class="form-control" required autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Per Capita</label>
                                        <input type="number" step="0.01" id="edit_per_capita" name="per_capita" class="form-control" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">PPE Rate</label>
                                        <input type="number" step="0.01" id="edit_ppe_rate" name="ppe_rate" class="form-control" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">No. of Days Work</label>
                                        <input type="number" id="edit_no_of_days_work" name="no_of_days_work" class="form-control" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Period Start</label>
                                        <input type="date" id="edit_period_start" name="period_start" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Period End</label>
                                        <input type="date" id="edit_period_end" name="period_end" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Subsidy (95%)</label>
                                        <input type="number" step="0.01" id="edit_subsidy" name="subsidy" class="form-control" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Admin Cost (3%)</label>
                                        <input type="number" step="0.01" id="edit_admin_cost" name="admin_cost" class="form-control" autocomplete="OFF">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: GPAI INSURANCE -->
                            <div class="tab-pane fade" id="edit-tab3" role="tabpanel">
                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: rgb(13 101 253);">5. GSIS - Group Personnel Accident Insurance</h6>
                                <div class="row g-3 mb-2">
                                    <div class="col-md-4">
                                        <label class="form-label">GPAI Actual Target</label>
                                        <input type="number" id="edit_final_physical" name="final_physical" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Number of Females</label>
                                        <input type="number" id="edit_no_female" name="no_female" class="form-control" placeholder="0" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date Enrolled</label>
                                        <input type="date" id="edit_gpai_date" name="gpai_date" class="form-control" autocomplete="OFF">                            
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: PPE & ID ISSUANCE -->
                            <div class="tab-pane fade" id="edit-tab4" role="tabpanel">
                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: rgb(13 101 253);">6. PPE Issuance</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label class="form-label">RIS Number</label>
                                        <input type="text" id="edit_ris_number" name="ris_number" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">PPE Requested</label>
                                        <input type="number" id="edit_final_physical_ppe_requested" name="final_physical_ppe_requested" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">PPE Issued</label>
                                        <input type="number" id="edit_final_physical_ppe_issued" name="final_physical_ppe_issued" class="form-control" placeholder="0" autocomplete="OFF">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date Issued</label>
                                        <input type="date" id="edit_ppe_date" name="ppe_date" class="form-control" autocomplete="OFF">
                                        <input type="hidden" id="edit_unique_id" name="unique_id" class="form-control" autocomplete="OFF">
                                    </div>
                                </div>                       

                                <h6 class="fw-bold border-bottom pb-2 mb-3" style="color: rgb(13 101 253);">7. ID Issuance</h6>
                                <div class="row g-3 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label">No of ID issued</label>
                                        <input type="number" id="edit_issued_id" name="issued_id" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date ID Issued</label>
                                        <input type="date" id="edit_date_id_issued" name="date_id_issued" class="form-control" autocomplete="OFF">
                                    </div>
                                </div> 
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="save_as_new_checkbox">
                            <label class="form-check-label text-dark fw-semibold" for="save_as_new_checkbox">
                                Check this if additional GPAI/PPE entry
                            </label>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="editModalSubmitBtn" class="text-white btn" style="background-color: rgb(13 101 253);">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status & Monitoring Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="statusForm" action="<?= site_url('tupad_allocations/update_monitoring_status'); ?>" method="post">
                    <input type="hidden" id="status_allocation_id" name="allocation_id">
                    
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Update Implementation & Status Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Implementation Status</label>
                                <select id="modal_implementation_status" name="implementation_status" class="form-select">
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="1">Paid</option>
                                    <option value="0">Not Yet Paid</option>
                                </select>
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="toggle_status_field" name="has_extra_status" value="1">
                                    <label class="form-check-label fw-semibold" for="toggle_status_field">Require Additional Status Detail</label>
                                </div>
                            </div>

                            <div class="col-md-12 d-none" id="status_textbox_container">
                                <label class="form-label fw-semibold">Less Days / Status Detail <span class="text-danger">*</span></label>
                                <input type="text" id="modal_status_detail" name="absent_days" class="form-control" placeholder="Enter required status detail...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No of Benefs Paid</label>
                                <input type="text" id="modal_benefs_paid" name="benefs_paid" class="form-control" placeholder="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No of Female Benefs</label>
                                <input type="text" id="modal_benefs_female_paid" name="benefs_female_paid" class="form-control" placeholder="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Orientation Schedule</label>
                                <input type="date" id="modal_orientation_schedule" name="orientation_schedule" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">PPE Request</label>
                                <input type="text" id="modal_ppe_request" name="ppe_request" class="form-control" placeholder="Enter PPE Request details">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">PPE Pickup Schedule</label>
                                <input type="date" id="modal_ppe_pickup_schedule" name="ppe_pickup_schedule" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mode of Payout</label>
                                <input type="text" id="modal_mode_of_payout" name="mode_of_payout" class="form-control" placeholder="e.g., Direct Payment, LGU, etc.">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Work Program Date Submission</label>
                                <input type="date" id="modal_work_program_date_submission" name="work_program_date_submission" class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea id="modal_remarks" name="remarks" class="form-control" rows="3" placeholder="Additional status notes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold"><i class="bi bi-save me-1"></i> Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const validCities = [
            <?php if (!empty($cities)): ?>
                <?php foreach ($cities as $c): ?>
                    "<?= addslashes(strtoupper(trim($c->citymunDesc))); ?>",
                <?php endforeach; ?>
            <?php endif; ?>
        ];

        const validBrgys = [
            <?php if (!empty($brgys)): ?>
                <?php foreach ($brgys as $b): ?>
                    "<?= addslashes(strtoupper(trim($b->brgyDesc))); ?>",
                <?php endforeach; ?>
            <?php endif; ?>
        ];

        const updateActionUrl = "<?= site_url('tupad_allocations/update/'); ?>";
        const storeAsNewUrl = "<?= site_url('tupad_allocations/store_as_new'); ?>";
        let activeEditId = '';

        const readOnlyFields = [
            '#edit_date_coordinated', '#edit_batch_no', '#edit_reference_no', 
            '#edit_adl_no', '#edit_sponsor', '#edit_source_fund_gpai', 
            '#edit_source_fund_wage', '#edit_proponent_recipient', '#edit_term', 
            '#edit_lgu_classification', '#edit_district', '#edit_lgu_municipality_city', 
            '#edit_barangay', '#edit_physical_target', '#edit_per_capita', 
            '#edit_ppe_rate', '#edit_no_of_days_work', '#edit_period_start', 
            '#edit_period_end', '#edit_subsidy', '#edit_admin_cost'
        ];

        function validateLocationInputs(cityInputId, brgyInputId) {
            const cityValue = $(cityInputId).val().trim().toUpperCase();
            const brgyValue = $(brgyInputId).val().trim().toUpperCase();

            $(cityInputId).removeClass('is-invalid');
            $(brgyInputId).removeClass('is-invalid');

            if (cityValue !== "" && !validCities.includes(cityValue)) {
                alert("Invalid Municipality/City! Please select a valid option from the dropdown list.");
                $(cityInputId).addClass('is-invalid').focus();
                return false;
            }

            if (brgyValue !== "" && brgyValue !== "NOT APPLICABLE" && brgyValue !== "-" && !validBrgys.includes(brgyValue)) {
                alert("Invalid Barangay! Please select a valid option from the dropdown list.");
                $(brgyInputId).addClass('is-invalid').focus();
                return false;
            }

            return true;
        }

        function adjustModalHeight() {
            $('.modal.show').each(function() {
                var $modal = $(this);
                var $dialog = $modal.find('.modal-dialog');
                var $header = $modal.find('.modal-header');
                var $footer = $modal.find('.modal-footer');
                var $body = $modal.find('.modal-body');

                if ($dialog.hasClass('modal-dialog-scrollable')) {
                    var windowHeight = $(window).height();
                    var maxH = windowHeight * 0.85; 
                    
                    $dialog.css({ 'max-height': maxH + 'px', 'height': maxH + 'px' });
                    var contentH = maxH;
                    var headerH = $header.outerHeight() || 0;
                    var footerH = $footer.outerHeight() || 0;
                    
                    $body.css({
                        'max-height': (contentH - headerH - footerH) + 'px',
                        'overflow-y': 'auto'
                    });
                }
            });
        }

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

            // Initialize DataTables
            const table = $('#allocationsTable').DataTable({
                "scrollX": true,
                "pageLength": 10,
                "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 1, 2] },
                    { "defaultContent": "-", "targets": "_all" }
                ],
                "language": {
                    "emptyTable": "No database logs found. Click \"+ Encode New Allocation\" to begin."
                }
            });

            function applyGroupCollapsing() {
                const groups = {};
                
                $('#allocationsTable tbody tr').each(function () {
                    const uid = $(this).attr('data-unique-id');
                    if (uid && uid !== 'TEMP') {
                        if (!groups[uid]) {
                            groups[uid] = [];
                        }
                        groups[uid].push($(this));
                    }
                });

                $.each(groups, function (uid, rows) {
                    if (rows.length > 1) {
                        const toggleBtn = rows[0].find('.toggle-group');
                        const isExpanded = toggleBtn.hasClass('group-expanded');

                        for (let i = 1; i < rows.length; i++) {
                            if (isExpanded) {
                                rows[i].show();
                            } else {
                                rows[i].hide();
                            }
                        }
                    }
                });
            }

            applyGroupCollapsing();

            table.on('draw', function () {
                applyGroupCollapsing();
            });

            $(document).on('click', '.toggle-group', function () {
                const icon = $(this).find('i');
                const isExpanded = $(this).hasClass('group-expanded');

                if (isExpanded) {
                    $(this).removeClass('group-expanded btn-dark').addClass('btn-outline-secondary');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                    $(this).attr('title', 'Show other entries with same Unique ID');
                } else {
                    $(this).addClass('group-expanded btn-dark').removeClass('btn-outline-secondary');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                    $(this).attr('title', 'Hide other entries with same Unique ID');
                }

                applyGroupCollapsing();
            });

            // Form Submit Interceptions
            $('#addForm').on('submit', function (e) {
                if (!validateLocationInputs('#lgu_municipality_city', '#add_barangay')) {
                    e.preventDefault();
                }
            });

            $('#editForm').on('submit', function (e) {
                if (!validateLocationInputs('#edit_lgu_municipality_city', '#edit_barangay')) {
                    e.preventDefault();
                    return;
                }

                if ($('#edit_term').is(':disabled')) {
                    $('#edit_term_hidden').val($('#edit_term').val());
                } else {
                    $('#edit_term_hidden').val('');
                }
            });

            $(document).on('change', '#edit_term', function() {
                $('#edit_term_hidden').val($(this).val());
            });

            // Edit button handler
            $(document).on('click', '.edit-btn', function () {
                activeEditId = $(this).data('id');
                const currentUniqueId = $(this).data('unique_id');
                
                $('#edit-tab1-tab').tab('show');
                $('#editModalSubmitBtn').prop('disabled', false).removeAttr('title');

                readOnlyFields.forEach(selector => {
                    if ($(selector).is('select')) {
                        $(selector).removeClass('pe-none bg-light').removeAttr('tabindex');
                    } else {
                        $(selector).prop('readonly', false).removeClass('bg-light');
                    }
                });
                
                $('#save_as_new_checkbox').prop('checked', false);
                $('#editForm').attr('action', updateActionUrl + activeEditId);
                $('#editModalSubmitBtn').html('<i class="bi bi-save me-1"></i> Save Changes');

                $('#edit_date_coordinated').val($(this).data('date_coordinated'));
                $('#edit_unique_id').val(currentUniqueId);
                $('#edit_batch_no').val($(this).data('batch_no'));
                $('#edit_reference_no').val($(this).data('reference_no'));
                $('#edit_adl_no').val($(this).data('adl_no'));
                $('#edit_sponsor').val($(this).data('sponsor'));
                $('#edit_source_fund_gpai').val($(this).data('source_fund_gpai'));
                $('#edit_source_fund_wage').val($(this).data('source_fund_wage'));
                $('#edit_proponent_recipient').val($(this).data('proponent_recipient'));
                $('#edit_lgu_classification').val($(this).data('lgu_classification'));
                $('#edit_district').val($(this).data('district'));
                $('#edit_lgu_municipality_city').val($(this).data('lgu_municipality_city'));
                $('#edit_barangay').val($(this).data('barangay'));
                $('#edit_physical_target').val($(this).data('physical_target'));
                $('#edit_per_capita').val($(this).data('per_capita'));
                $('#edit_ppe_rate').val($(this).data('ppe_rate'));
                $('#edit_no_of_days_work').val($(this).data('no_of_days_work'));
                $('#edit_period_start').val($(this).data('period_start'));
                $('#edit_period_end').val($(this).data('period_end'));
                $('#edit_subsidy').val($(this).data('subsidy'));
                $('#edit_admin_cost').val($(this).data('admin_cost'));  
                $('#edit_insurance_amount').val($(this).data('insurance_amount'));
                $('#edit_final_physical').val($(this).data('final_physical_target')); 
                $('#edit_no_female').val($(this).data('number_of_females')); 
                $('#edit_percentage').val($(this).data('percentage'));
                
                const termVal = ($(this).data('term') || '').toUpperCase();
                $('#edit_term').val(termVal); 
                $('#edit_term_hidden').val(termVal);

                $('#edit_gpai_date').val($(this).data('gpai_date')); 
                $('#edit_ris_number').val($(this).data('ris_number')); 
                $('#edit_final_physical_ppe_issued').val($(this).data('final_physical_ppe_issued')); 
                $('#edit_final_physical_ppe_requested').val($(this).data('final_physical_ppe_requested')); 
                $('#edit_ppe_date').val($(this).data('date_issued_ppe')); 
                $('#edit_issued_id').val($(this).data('issued_id')); 
                $('#edit_date_id_issued').val($(this).data('date_id_issued')); 
            });

            // Toggle mandatory status input field
            $(document).on('change', '#toggle_status_field', function() {
                if ($(this).is(':checked')) {
                    $('#status_textbox_container').removeClass('d-none');
                    $('#modal_status_detail').prop('required', true);
                } else {
                    $('#status_textbox_container').addClass('d-none');
                    $('#modal_status_detail').prop('required', false).val('');
                }
            });

            // Status Modal Trigger
            $(document).on('click', '.status-modal-btn', function () {
                $('#status_allocation_id').val($(this).data('id'));
                $('#modal_implementation_status').val($(this).data('implementation_status'));

                const absentDaysVal = $(this).data('absent_days');
                if (absentDaysVal && String(absentDaysVal).trim() !== '') {
                    $('#toggle_status_field').prop('checked', true).trigger('change');
                    $('#modal_status_detail').val(absentDaysVal);
                } else {
                    $('#toggle_status_field').prop('checked', false).trigger('change');
                }

                $('#modal_benefs_paid').val($(this).data('benefs_paid'));
                $('#modal_benefs_female_paid').val($(this).data('benefs_female_paid'));
                $('#modal_orientation_schedule').val($(this).data('orientation_schedule'));
                $('#modal_ppe_request').val($(this).data('ppe_request'));
                $('#modal_ppe_pickup_schedule').val($(this).data('ppe_pickup_schedule'));
                $('#modal_mode_of_payout').val($(this).data('mode_of_payout'));
                $('#modal_work_program_date_submission').val($(this).data('work_program_date_submission'));
                $('#modal_remarks').val($(this).data('remarks'));
            });

            // Checkbox change for Save As New Entry
            $(document).on('change', '#save_as_new_checkbox', function () {
                if ($(this).is(':checked')) {
                    $('#editForm').attr('action', storeAsNewUrl);
                    $('#editModalSubmitBtn').html('<i class="bi bi-plus-circle me-1"></i> Record Additional GPAI');
                    $('#editModalSubmitBtn').prop('disabled', false).removeAttr('title');

                    readOnlyFields.forEach(selector => {
                        if ($(selector).is('select')) {
                            $(selector).addClass('pe-none bg-light').attr('tabindex', '-1');
                        } else {
                            $(selector).prop('readonly', true).addClass('bg-light');
                        }
                    });

                    $('#edit_final_physical').val('');
                    $('#edit_no_female').val('');
                    $('#edit_gpai_date').val('');
                    $('#edit_final_physical_ppe_requested').val('');
                    $('#edit_final_physical_ppe_issued').val('');
                    $('#edit_ppe_date').val('');
                    $('#edit_issued_id').val('');
                    $('#edit_date_id_issued').val('');
                    $('#edit_ris_number').val(''); 

                    $('#edit-tab3-tab').tab('show');

                } else {
                    $('#editForm').attr('action', updateActionUrl + activeEditId);
                    $('#editModalSubmitBtn').html('<i class="bi bi-save me-1"></i> Save Changes');
                    $('#editModalSubmitBtn').prop('disabled', false).removeAttr('title');

                    readOnlyFields.forEach(selector => {
                        if ($(selector).is('select')) {
                            $(selector).removeClass('pe-none bg-light').removeAttr('tabindex');
                        } else {
                            $(selector).prop('readonly', false).removeClass('bg-light');
                        }
                    });

                    const $btn = $('.edit-btn[data-id="' + activeEditId + '"]');
                    if ($btn.length) {
                        $('#edit_final_physical').val($btn.data('final_physical_target')); 
                        $('#edit_no_female').val($btn.data('number_of_females')); 
                        $('#edit_gpai_date').val($btn.data('gpai_date')); 
                        $('#edit_final_physical_ppe_requested').val($btn.data('final_physical_ppe_requested')); 
                        $('#edit_final_physical_ppe_issued').val($btn.data('final_physical_ppe_issued')); 
                        $('#edit_ppe_date').val($btn.data('date_issued_ppe')); 
                        $('#edit_issued_id').val($btn.data('issued_id')); 
                        $('#edit_date_id_issued').val($btn.data('date_id_issued')); 
                        $('#edit_ris_number').val($btn.data('ris_number')); 
                    }
                }
            });

            // Adjust modal height dynamically
            $(document).on('shown.bs.modal', '.modal', function() {
                adjustModalHeight();
            });
            $(window).on('resize', function() {
                adjustModalHeight();
            });
        });
    </script>
</body>

</html>