<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NTP Encoding & Management</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS Bootstrap 5 Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            --bg-body: #f4f6f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        } 

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header-custom {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem 1.75rem;
        }

        .form-control::placeholder, .form-select::placeholder {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: none;
        }

        .form-control, .form-select {
            font-size: 0.875rem;
            padding: 0.45rem 0.75rem;
            text-transform: uppercase;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 75px;
        }

        .form-label {
            font-size: 0.825rem;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 0.5rem;
            margin-bottom: 0 !important;
        }

        .table thead th {
            background-color: #f8fafc !important;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-top: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
        }

        .table tbody tr {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.04);
            background-color: #ffffff !important;
        }

        .table tbody td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody td:first-child {
            border-left: 1px solid #f1f5f9;
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }

        .table tbody td:last-child {
            border-right: 1px solid #f1f5f9;
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.4rem 2rem 0.4rem 0.75rem;
            background-color: #f8fafc;
        }

        .pagination .page-item .page-link {
            border: none;
            border-radius: 0.375rem;
            margin: 0 3px;
            color: #475569;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            background-color: #f1f5f9;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        /* --- 100% Exact Official DOLE NTP Document Styling (A4 Portrait Match) --- */
        @page {
            size: A4 portrait;
            margin: 10mm 15mm;
        }

        .ntp-document {
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            padding: 6mm 8mm;
            background: #ffffff;
            line-height: 1.35;
            font-size: 13px;
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .ntp-header-container {
            width: 100%;
            padding-bottom: 4px;
            margin-bottom: 12px;
        }
        .ntp-header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ntp-header-table td {
            vertical-align: middle;
            padding: 0 6px;
        }
        .ntp-header-left-text h6 { 
            font-size: 9.5px; 
            margin: 0 0 1px 0; 
            font-weight: normal; 
        }
        .ntp-header-left-text h5 { 
            font-size: 11.5px; 
            margin: 0 0 2px 0; 
            font-weight: bold; 
            letter-spacing: 0.2px; 
        }
        .ntp-header-left-text p { 
            font-size: 8.5px; 
            margin: 0 0 1px 0; 
            color: #111; 
        }

        .ntp-ref-no {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }
        
        .ntp-title-wrapper {
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 2px solid #000000;
            padding-bottom: 6px;
        }
        .ntp-title {
            font-weight: bold;
            font-size: 14px;
            margin: 0;
            text-decoration: none;
        }

        .ntp-intro {
            font-size: 13px;
            margin-bottom: 10px;
            text-align: justify;
        }

        .ntp-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .ntp-details-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 13px;
        }
        .ntp-details-table td.label {
            font-weight: bold;
            width: 220px;
            white-space: nowrap;
        }

        .ntp-body-text {
            font-size: 13px;
            margin-bottom: 10px;
            text-align: justify;
        }

        .ntp-signatory-table {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }
        .ntp-signatory-table td {
            vertical-align: top;
        }

        @media print {
            body { 
                background-color: #ffffff !important; 
                -webkit-print-color-adjust: exact; 
            }
            #sidebar, .top-navbar, .no-print, header, footer { 
                display: none !important; 
            }
            #main-content { 
                margin-left: 0 !important; 
                padding: 0 !important; 
            }
            .modal { 
                position: absolute !important; 
                left: 0 !important; 
                top: 0 !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                overflow: visible !important; 
                width: 100% !important; 
                background: #ffffff !important; 
            }
            .modal-dialog { 
                max-width: 100% !important; 
                width: 100% !important; 
                margin: 0 !important; 
            }
            .modal-content { 
                border: none !important; 
                box-shadow: none !important; 
                background: #ffffff !important; 
            }
            .modal-body { 
                padding: 0 !important; 
                background: #ffffff !important; 
            }
            .ntp-document { 
                padding: 0 !important; 
                margin: 0 auto !important; 
                width: 100% !important; 
                max-width: none !important; 
                background: #ffffff !important; 
            }
        }
    </style>
</head>

<body>

    <?php if(file_exists(APPPATH.'views/templates/navbar.php')) { $this->load->view('templates/navbar'); } ?>

    <div id="main-content">
        <?php if(file_exists(APPPATH.'views/templates/sidebar.php')) { $this->load->view('templates/sidebar'); } ?>

        <main class="p-3 p-md-4 flex-grow-1">
            <div class="container-fluid px-0">
                <div class="row mb-4 align-items-center">
                    <div class="col-sm-8">
                        <h3 class="fw-bold mb-1 text-dark">NTP Encoding & Management</h3>
                        <p class="text-muted mb-0">Encode new Notice to Proceed records, review details, approve, and generate PDF printouts.</p>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('success'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Encoding Form Card -->
                <div class="card no-print">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold m-0 text-dark d-flex align-items-center">
                            <i class="bi bi-file-earmark-plus text-primary me-2"></i> Encode New Notice to Proceed (NTP)
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="ntpForm" action="<?= site_url('NTP/save_ntp'); ?>" method="POST">
                            <input type="hidden" name="NTP_province" id="province_name_input">
                            <input type="hidden" name="NTP_municipality" id="municipality_name_input">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">NTP Reference No.</label>
                                    <input type="text" name="ntp_reference_no" id="ntp_reference_no" class="form-control" placeholder="e.g. TUPAD26-ZAM-D1-NTP-250" required>
                                    <div id="duplicate_feedback" class="invalid-feedback fw-bold" style="display: none;">NTP Reference No. already exists!</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">ADL No.</label>
                                    <select name="ADL_no" class="form-select" required>
                                        <option value="">Select ADL No.</option>
                                        <?php if(!empty($adl_list)): ?>
                                            <?php foreach($adl_list as $adl): ?>
                                                <option value="<?= htmlspecialchars($adl->adl_no); ?>"><?= htmlspecialchars($adl->adl_no); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Implementation Ref No.</label>
                                    <input type="text" name="implementation_reference_no" class="form-control" placeholder="e.g. Ref No.2026_08_0582..." required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Date & Time Received</label>
                                    <input type="datetime-local" name="date_received" id="date_received_input" class="form-control" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Nature of Project</label>
                                    <input type="text" name="nature_of_project" class="form-control" placeholder="e.g. Light Works, Social Community Projects" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Province (Filter)</label>
                                    <select id="province_select" class="form-select" required>
                                        <option value="">Select Province</option>
                                        <?php foreach($provinces as $code => $name): ?>
                                            <option value="<?= $code; ?>"><?= $name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Municipality / City (Filter)</label>
                                    <select id="municipality_select" class="form-select" required disabled>
                                        <option value="">Select Municipality / City</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Project Location (Final Output / Editable)</label>
                                    <textarea name="project_location" id="project_location_input" class="form-control" placeholder="e.g. Botolan, Zambales" required></textarea>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">No. of Beneficiaries</label>
                                    <input type="number" name="no_of_beneficiaries" class="form-control" placeholder="e.g. 1963" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Period of Work</label>
                                    <input type="text" name="period_of_work" class="form-control" placeholder="e.g. 10 days" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Amount of Assistance (PHP)</label>
                                    <input type="number" step="0.01" name="amount_of_assistance" class="form-control" placeholder="e.g. 12944176.97" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Mode of Implementation</label>
                                    <input type="text" name="mode_of_implementation" class="form-control" value="Direct Administration" required>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <button type="reset" class="btn btn-light me-2 border"><i class="bi bi-x-circle me-1"></i> Reset</button>
                                    <button type="submit" id="submitBtn" class="btn btn-primary fw-semibold"><i class="bi bi-check-circle me-1"></i> Save NTP Entry</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Encoded NTP DataTable Card -->
                <div class="card no-print">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold m-0 text-dark d-flex align-items-center">
                            <i class="bi bi-table text-primary me-2"></i> Encoded NTP Records
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="ntpTable" class="table table-hover align-middle w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ref No.</th>
                                        <th>ADL No.</th>
                                        <th>Location</th>
                                        <th>Date Received</th>
                                        <th>Beneficiaries</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date Approved</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($ntp_list)): ?>
                                        <?php foreach($ntp_list as $row): ?>
                                            <tr>
                                                <td><?= $row->ntp_id; ?></td>
                                                <td class="fw-semibold text-primary"><?= htmlspecialchars($row->ntp_reference_no); ?></td>
                                                <td><?= htmlspecialchars($row->ADL_no); ?></td>
                                                <td><?= nl2br(htmlspecialchars($row->project_location)); ?></td>
                                                <td><?= !empty($row->date_received) ? date('M d, Y h:i A', strtotime($row->date_received)) : ''; ?></td>
                                                <td><?= number_format($row->no_of_beneficiaries); ?></td>
                                                <td>₱<?= number_format((float)$row->amount_of_assistance, 2); ?></td>
                                                <td>
                                                    <span class="badge <?= $row->approval_status == 'Approved' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                        <?= $row->approval_status; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= !empty($row->date_approved) ? date('M d, Y', strtotime($row->date_approved)) : '<span class="text-muted">—</span>'; ?>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <?php if($row->approval_status != 'Approved'): ?>
                                                            <button type="button" 
                                                                    class="btn btn-outline-success open-approve-modal" 
                                                                    data-id="<?= $row->ntp_id; ?>"
                                                                    data-ref="<?= htmlspecialchars($row->ntp_reference_no); ?>"
                                                                    title="Approve">
                                                                <i class="bi bi-check-lg"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-success" disabled title="Approved"><i class="bi bi-patch-check"></i></button>
                                                        <?php endif; ?>

                                                        <button class="btn btn-dark view-ntp-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#ntpModal"
                                                                data-ref="<?= htmlspecialchars($row->ntp_reference_no); ?>"
                                                                data-nature="<?= htmlspecialchars($row->nature_of_project); ?>"
                                                                data-location="<?= htmlspecialchars($row->project_location); ?>"
                                                                data-beneficiaries="<?= number_format($row->no_of_beneficiaries); ?>"
                                                                data-period="<?= htmlspecialchars($row->period_of_work); ?>"
                                                                data-amount="<?= number_format((float)$row->amount_of_assistance, 2); ?>"
                                                                data-mode="<?= htmlspecialchars($row->mode_of_implementation); ?>"
                                                                data-implref="<?= htmlspecialchars($row->implementation_reference_no); ?>"
                                                                data-adl="<?= htmlspecialchars($row->ADL_no); ?>"
                                                                data-approvaldate="<?= !empty($row->approval_date) ? htmlspecialchars($row->approval_date) : ''; ?>"
                                                                title="Generate PDF">
                                                            <i class="bi bi-file-earmark-pdf"></i>
                                                        </button>
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
        </main>
    </div>

    <!-- Approval Date Modal -->
    <div class="modal fade no-print" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approvalForm" method="POST" action="">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="approvalModalLabel"><i class="bi bi-check-circle me-2"></i> Approve NTP Entry</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">You are about to approve NTP Reference No: <strong id="modal_ntp_ref" class="text-primary"></strong></p>
                        <div class="mb-3">
                            <label for="approval_date_input" class="form-label fw-semibold">Approval Date:</label>
                            <input type="date" name="approval_date" id="approval_date_input" class="form-control" required>
                            <div class="form-text">This date will reflect on the printed document signature block.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success fw-semibold"><i class="bi bi-check-lg me-1"></i> Confirm Approval</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- NTP Document Preview Modal -->
    <div class="modal fade" id="ntpModal" tabindex="-1" aria-labelledby="ntpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header no-print">
                    <h5 class="modal-title fw-bold" id="ntpModalLabel">NTP Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Printed Document Area matching exact A4 size & layout -->
                    <div id="ntpPrintArea" class="ntp-document">
                        
                        <!-- Header Container -->
                        <div class="ntp-header-container">
                            <table class="ntp-header-table">
                                <tr>
                                    <td style="width: 80px; text-align: left; vertical-align: middle; padding-right: 12px;">
                                        <img src="<?= base_url('assets/images/dolelogo.png'); ?>" alt="DOLE Logo" style="width: 72px; height: auto; display: block;" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/f/f4/Department_of_Labor_and_Employment_%28DOLE%29.svg';">
                                    </td>
                                    <td class="ntp-header-left-text" style="vertical-align: middle;">
                                        <h6>Republic of the Philippines</h6>
                                        <h5>DEPARTMENT OF LABOR AND EMPLOYMENT</h5>
                                        <p><strong>Regional Office No. III (Central Luzon)</strong></p>
                                        <p>Matalino cor. Mahusay St., DMGC, Maimpis, City of San Fernando, Pampanga 2000</p>
                                        <p>ro3.dole.gov.ph | ro3@dole.gov.ph | 0968 865 0303 | DOLE Central Luzon</p>
                                    </td>
                                    <td style="width: 140px; text-align: right; vertical-align: middle; padding-left: 12px; white-space: nowrap;">
                                        <img src="<?= base_url('assets/images/bp_logo.png'); ?>" alt="Bagong Pilipinas Logo" style="height: 42px; width: auto; display: inline-block; vertical-align: middle; margin-right: 4px;" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/8/84/Bagong_Pilipinas_logo.svg';">
                                        <img src="<?= base_url('assets/images/iso_logo.png'); ?>" alt="ISO Logo" style="height: 42px; width: auto; display: inline-block; vertical-align: middle;" onerror="this.style.display='none';">
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div id="print_ref_no" class="ntp-ref-no"></div>

                        <!-- Title with solid dividing line underneath -->
                        <div class="ntp-title-wrapper">
                            <div class="ntp-title">
                                Notice to Proceed for the Implementation of TUPAD Program
                            </div>
                        </div>

                        <div class="ntp-intro">
                            This pertains to the request for availment of the <strong>Tulong Panghanapbuhay sa Ating Disadvantaged Workers (TUPAD) Program</strong>, with the following details:
                        </div>

                        <!-- Precise Details Table -->
                        <table class="ntp-details-table">
                            <tr>
                                <td class="label">Nature of Project:</td>
                                <td><span id="print_nature"></span></td>
                            </tr>
                            <tr>
                                <td class="label">Project Location:</td>
                                <td><span id="print_location" style="white-space: pre-line;"></span></td>
                            </tr>
                            <tr>
                                <td class="label">No. of Beneficiaries:</td>
                                <td><span id="print_beneficiaries"></span></td>
                            </tr>
                            <tr>
                                <td class="label">Period of Work:</td>
                                <td><span id="print_period"></span></td>
                            </tr>
                            <tr>
                                <td class="label">Amount of Assistance:</td>
                                <td>Php <span id="print_amount"></span></td>
                            </tr>
                            <tr>
                                <td class="label">Mode of Implementation:</td>
                                <td><span id="print_mode"></span></td>
                            </tr>
                            <tr>
                                <td class="label">Ref No.:</td>
                                <td><span id="print_implref"></span></td>
                            </tr>
                            <tr>
                                <td class="label">ADL No.:</td>
                                <td><span id="print_adl"></span></td>
                            </tr>
                        </table>

                        <div class="ntp-body-text">
                            The DOLE Tarlac Field Office is advised to proceed with the implementation of the above-mentioned TUPAD project for a period of <span id="print_period_text_2"></span>, subject to existing rules and regulations.
                        </div>

                        <div class="ntp-body-text" style="margin-bottom: 20px;">
                            The implementation of TUPAD could be postponed or terminated if there are found deviations on the approved proposal.
                        </div>

                        <!-- Signatory block matching physical photo format -->
                        <table class="ntp-signatory-table">
                            <tr>
                                <td style="width: 45%;"></td>
                                <td style="width: 55%; text-align: center;">
                                    <div style="font-weight: bold; font-size: 13.5px; text-decoration: underline; margin-bottom: 2px;">ALEJANDRO V. INZA CRUZ</div>
                                    <div style="font-size: 13px; margin-bottom: 14px;">OIC - Regional Director</div>
                                    <div style="font-weight: bold; font-size: 13px;">Date: <span id="print_date" style="border-bottom: 1px solid #000; display: inline-block; min-width: 150px; text-align: center; font-weight: normal;">&nbsp;</span></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer no-print">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print();"><i class="bi bi-printer me-1"></i> Print / Save as PDF (A4)</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS & BS5 Setup -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function () {
        $('#ntpTable').DataTable();

        // Auto-populate current date and time for date_received field
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        $('#date_received_input').val(now.toISOString().slice(0, 16));

        // Default approval date to today's date
        var todayStr = new Date().toISOString().slice(0, 10);
        $('#approval_date_input').val(todayStr);

        // Open approval modal dynamically
        $(document).on('click', '.open-approve-modal', function() {
            var ntpId = $(this).data('id');
            var ntpRef = $(this).data('ref');

            $('#modal_ntp_ref').text(ntpRef);
            $('#approvalForm').attr('action', "<?= site_url('NTP/approve_ntp/'); ?>" + ntpId);
            
            var approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
            approvalModal.show();
        });

        // Live Duplicate NTP Reference No. Checker via AJAX
        $('#ntp_reference_no').on('blur keyup', function () {
            var ntpRef = $(this).val().trim();
            if (ntpRef !== '') {
                $.ajax({
                    url: "<?= site_url('NTP/check_duplicate'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: { ntp_reference_no: ntpRef },
                    success: function (response) {
                        if (response.exists) {
                            $('#ntp_reference_no').addClass('is-invalid');
                            $('#duplicate_feedback').show();
                            $('#submitBtn').prop('disabled', true);
                        } else {
                            $('#ntp_reference_no').removeClass('is-invalid');
                            $('#duplicate_feedback').hide();
                            $('#submitBtn').prop('disabled', false);
                        }
                    }
                });
            } else {
                $('#ntp_reference_no').removeClass('is-invalid');
                $('#duplicate_feedback').hide();
                $('#submitBtn').prop('disabled', false);
            }
        });

        // Cascading Dropdown for Province -> Municipality/City
        $('#province_select').on('change', function () {
            var provCode = $(this).val();
            var provName = $('#province_select option:selected').text().toUpperCase();
            var munSelect = $('#municipality_select');

            $('#province_name_input').val(provName);
            $('#municipality_name_input').val('');

            munSelect.html('<option value="">Select Municipality / City</option>').prop('disabled', true);

            if (provCode !== '') {
                $.ajax({
                    url: "<?= site_url('NTP/get_municipalities'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: { provCode: provCode },
                    success: function (response) {
                        if (response.length > 0) {
                            $.each(response, function (index, item) {
                                var munName = item.citymunDesc.toUpperCase();
                                munSelect.append('<option value="' + munName + ', ' + provName + '">' + munName + '</option>');
                            });
                            munSelect.prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert('Failed to load municipalities. Please try again.');
                    }
                });
            }
        });

        // Prepend municipality selection to the first line of textarea
        $('#municipality_select').on('change', function () {
            var selectedLocation = $(this).val();
            var munText = $('#municipality_select option:selected').text();
            var $textarea = $('#project_location_input');
            var currentVal = $textarea.val().trim();

            if (selectedLocation !== '') {
                $('#municipality_name_input').val(munText);

                if (currentVal !== '') {
                    $textarea.val(selectedLocation + "\n" + currentVal);
                } else {
                    $textarea.val(selectedLocation);
                }
            }
        });

        // Force uppercase input live on text inputs and textareas
        $(document).on('input', 'input[type="text"], textarea', function() {
            var start = this.selectionStart;
            var end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
        });

        // Populate modal with selected row details for PDF printing
        $(document).on('click', '.view-ntp-btn', function () {
            $('#print_ref_no').text($(this).data('ref'));
            $('#print_nature').text($(this).data('nature'));
            $('#print_location').text($(this).data('location'));
            $('#print_beneficiaries').text($(this).data('beneficiaries'));
            $('#print_period').text($(this).data('period'));
            $('#print_period_text_2').text($(this).data('period'));
            $('#print_amount').text($(this).data('amount'));
            $('#print_mode').text($(this).data('mode'));
            $('#print_implref').text($(this).data('implref'));
            $('#print_adl').text($(this).data('adl'));
            
            // Format and display approval date if available
            var rawApprovalDate = $(this).data('approvaldate');
            if (rawApprovalDate) {
                var parsedDate = new Date(rawApprovalDate);
                var formattedDate = !isNaN(parsedDate) ? parsedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).toUpperCase() : rawApprovalDate;
                $('#print_date').text(formattedDate);
            } else {
                $('#print_date').html('&nbsp;');
            }
        });
    });
    </script>
</body>

</html>