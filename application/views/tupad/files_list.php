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

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">

    <!-- Intro.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css">
    <!-- Intro.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>

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
       
        /* --- CARDS & STATS --- */
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

        .icon-badge {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
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

        /* --- ULTRA-MODERN GLASSMORPHISM INTRO.JS THEME --- */
        .introjs-tooltip {
            background: linear-gradient(135deg, rgba(30, 27, 75, 0.95), rgba(15, 23, 42, 0.98)) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            color: #f1f5f9 !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
            border: none !important;
            padding: 1.5rem !important;
            max-width: 370px !important;
            font-family: 'Inter', system-ui, sans-serif !important;
        }

        .introjs-tooltiptext {
            padding: 0 !important;
            color: #cbd5e1 !important;
            font-size: 0.925rem !important;
            line-height: 1.6 !important;
        }

        .introjs-arrow.top, .introjs-arrow.top-right, .introjs-arrow.top-left {
            border-bottom-color: rgba(30, 27, 75, 0.95) !important;
        }
        .introjs-arrow.bottom, .introjs-arrow.bottom-right, .introjs-arrow.bottom-left {
            border-top-color: rgba(15, 23, 42, 0.98) !important;
        }
        .introjs-arrow.right {
            border-left-color: rgba(30, 27, 75, 0.95) !important;
        }
        .introjs-arrow.left {
            border-right-color: rgba(30, 27, 75, 0.95) !important;
        }

        .introjs-header {
            padding: 0 0 12px 0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            margin-bottom: 12px !important;
        }

        .introjs-tooltip-title {
            color: #a855f7 !important; /* Modern Neon Purple / Violet */
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            letter-spacing: -0.01em;
        }

        .introjs-bullets ul li a {
            background: rgba(255, 255, 255, 0.2) !important;
            border-radius: 50% !important;
            width: 8px !important;
            height: 8px !important;
            transition: all 0.3s ease;
        }

        .introjs-bullets ul li a.active {
            background: #a855f7 !important; /* Violet active bar */
            width: 24px !important;
            border-radius: 4px !important;
        }

        .introjs-button {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #e2e8f0 !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            padding: 7px 16px !important;
            text-shadow: none !important;
            box-shadow: none !important;
            transition: all 0.2s ease;
        }

        .introjs-button:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.25) !important;
        }

        .introjs-nextbutton {
            background: linear-gradient(135deg, #7c3aed, #a855f7) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4) !important;
        }

        .introjs-nextbutton:hover {
            background: linear-gradient(135deg, #6d28d9, #9333ea) !important;
            color: #ffffff !important;
        }

        .introjs-disabled {
            opacity: 0.3 !important;
        }

        /* Glassmorphism Spotlight Layer */
        .introjs-helperLayer {
            border-radius: 12px !important;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.65), 0 0 20px rgba(168, 85, 247, 0.5) !important;
            border: 2px solid #a855f7 !important;
        }
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar'); ?>

    <!-- Main Content Wrapper -->
    <div id="main-content">
        
        <?php $this->load->view('templates/sidebar'); ?>
        <!-- Main Workspace -->
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

            <!-- TEMPORARY DISPLAY OF UPLOAD DISCREPANCIES -->
            <?php if ($this->session->flashdata('upload_discrepancies')): ?>
                <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4 border-warning" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-2 text-warning"></i>
                        <h5 class="alert-heading fw-bold mb-0 text-dark">Excel Data Discrepancies Found</h5>
                    </div>
                    <p class="small text-muted mb-2">The uploaded file was rejected because of the following name/field validation errors:</p>
                    <div class="bg-white border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($this->session->flashdata('upload_discrepancies') as $error): ?>
                                <li class="small text-danger mb-1 fw-medium"><?= html_escape($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header & Upload Trigger -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-folder-fill text-primary me-2"></i>Uploaded Batch Files  
                        <button type="button" class="btn btn-link text-secondary p-0 ms-2 fs-4" id="btnOpenGuide" title="Upload Guide & Instructions">
                            <i class="bi bi-question-circle-fill"></i>
                         </button>
                    </h3>
                    <p class="text-muted small mb-0">Select an uploaded Excel file to view its individual beneficiary records.</p>
                </div>

                <!-- Tour & Action Group Buttons -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Tour Button -->
                    <button type="button" class="btn btn-outline-primary px-3 py-2 fw-semibold mb-0 cursor-pointer shadow-sm" id="btnStartTour">
                        <i class="bi bi-compass me-1"></i> Start Tour
                    </button>   

                    <!-- Modal Trigger Button -->
                    <button type="button" class="btn btn-success px-3 py-2 fw-semibold mb-0 cursor-pointer shadow-sm" id="btnOpenModal">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload New Excel
                    </button>
                </div>
            </div>

            <!-- User Guide Modal -->
            <div class="modal fade" id="guideModal" tabindex="-1" aria-labelledby="guideModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold" id="guideModalLabel">
                                <i class="bi bi-info-circle-fill text-primary me-2"></i>TUPAD Excel Upload Guide
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h6 class="fw-bold text-dark">Important Instructions for Uploading:</h6>
                            <ul class="small text-muted mb-3 ps-3">
                                <li class="mb-1">Kapag mali po spelling, blanko or wala po sa PROVINCE ng REGION 3 ang nakalagay sa TUPAD_PROVINCE column. Mag nonotify din ang TUPAD_MUNICIPALITY at TUPAD_BARANGAY na HINDI sila makita sa PROVINCE ng REGION 3. <br><font color="GREEN">Need lang po natin itama ang TUPAD_PROVINCE.</font> </li><br>
                                <li class="mb-1">Kapag mali po spelling, blanko or wala po sa MUNICIPALITY ng PROVINCE ang nakalagay sa TUPAD_MUNICIPALITY column. Mag nonotify din ang TUPAD_BARANGAY na HINDI sila makita sa MUNICIPALITY ng PROVINCE. <br><font color="GREEN">Need lang po natin itama ang TUPAD_MUNICIPALITY.</font> </li><br>
                                <li class="mb-1">Kapag meron po nakita ang system ng character kagaya ng *&!@#$%^()=+",.;: automatic po niya ito tatanggalin.</li>
                                <li class="mb-1">Kapag meron pong invalid DATE OF BIRTH na nakita hindi po tutuloy ang uploading process.</li>
                                <li class="mb-1">Kapag meron po nakita ng blanko kagaya ng FIRST NAME, LAST NAME, BIRTH MONTH, BIRTH DAY, BIRTH YEAR and GENDER hindi rin po tutuloy ang uploading process.</li>          
                            </ul>
                            <h6 class="fw-bold text-dark">Steps to Upload:</h6>
                            <ol class="small text-muted mb-0 ps-3">
                                <li class="mb-1">Click the <strong>Upload New Excel</strong> button.</li>
                                <li class="mb-1">Fill out the required metadata fields (Area of Implementation, Period of Coverage, ADL No., Reference No., Nature of Work).<br><font color="GREEN">Makikita po mga ito sa 'Details' sheet nung <b>TUPAD_Benefs_Profile_Template_2026</b>. </font></li>
                                <li class="mb-1">Select your formatted `.xlsx`.</li>
                                <li class="mb-1">Click <strong>Save & Upload</strong> to process.</li>
                            </ol>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Got it</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload & Metadata Modal -->
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="uploadModalLabel">
                                <i class="bi bi-file-earmark-arrow-up text-primary me-2"></i>Encode Upload Details
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="uploadBatchForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label fw-semibold">Select Excel/CSV File</label>
                                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv" required>
                                </div>
                                <div class="mb-3">
                                    <label for="area_of_implementation" class="form-label fw-semibold">Area of Implementation</label>
                                    <input type="text" class="form-control" id="area_of_implementation" name="area_of_implementation" required>
                                </div>
                                <div class="mb-3">
                                    <label for="period_of_coverage" class="form-label fw-semibold">Period of Coverage</label>
                                    <input type="text" class="form-control" id="period_of_coverage" name="period_of_coverage" required>
                                </div>
                                <div class="mb-3">
                                    <label for="adl_no" class="form-label fw-semibold">ADL No.</label>
                                    <input type="text" class="form-control" id="adl_no" name="adl_no" required>
                                </div>
                                <div class="mb-3">
                                    <label for="reference_no" class="form-label fw-semibold">Reference No.</label>
                                    <input type="text" class="form-control" id="reference_no" name="reference_no" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nature_of_work" class="form-label fw-semibold">Nature of Work</label>
                                    <input type="text" class="form-control" id="nature_of_work" name="nature_of_work" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="btnSubmitBatch">
                                <i class="bi bi-check-circle me-1"></i> Save & Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTICS CARDS SECTION -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-medium">Total Files Uploaded</span>
                                <h3 class="fw-bold my-1">
                                    <?= !empty($files) ? count($files) : 0; ?>
                                </h3>
                                <span class="badge bg-success-subtle text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Active Uploads</span>
                            </div>
                            <div class="icon-badge bg-primary-subtle text-primary">
                                <i class="bi bi-file-earmark-excel-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small fw-medium">Total Saved Records</span>
                                <h3 class="fw-bold my-1">
                                    <?php 
                                        $total_rows = 0;
                                        if(!empty($files)){
                                            foreach($files as $f){
                                                $total_rows += $f['total_records'];
                                            }
                                        }
                                        echo $total_rows;
                                    ?>
                                </h3>
                                <span class="badge bg-info-subtle text-info fw-semibold"><i class="bi bi-database me-1"></i>Database Entries</span>
                            </div>
                            <div class="icon-badge bg-info-subtle text-info">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File List Table Card -->
            <div class="table-card">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h6 class="mb-1 fw-bold">Uploaded Excel Archives</h6>
                        <small class="text-muted">List of all imported batch files</small>
                    </div>

                    <div class="input-group input-group-sm search-box" style="max-width: 250px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="fileSearch" class="form-control" placeholder="Search files...">
                    </div>
                </div>

                <div class="table-responsive table-container">
                    <table id="filesTable" class="table table-striped table-hover align-middle text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Reference No.</th> 
                                <th>Total Records</th>
                                <th>Uploaded By</th>  
                                <th>Date Uploaded</th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables AJAX populates rows here -->
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- REUSABLE DYNAMIC MODAL -->
    <div class="modal fade" id="appModal" tabindex="-1" aria-labelledby="appModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="appModalLabel">Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="appModalBody">
                    <!-- Dynamic message inserted here -->
                </div>
                <div class="modal-footer" id="appModalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        function showCustomAlert(message, title = 'Notification') {
            $('#appModalLabel').text(title);
            $('#appModalBody').html(message);
            $('#appModalFooter').html('<button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>');
            
            var appModalEl = document.getElementById('appModal');
            var appModal = bootstrap.Modal.getOrCreateInstance(appModalEl);
            appModal.show();
        }

        function showCustomConfirm(message, onConfirm, title = 'Confirmation') {
            $('#appModalLabel').text(title);
            $('#appModalBody').html(message);
            $('#appModalFooter').html(`
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="appModalConfirmBtn">Confirm</button>
            `);
            
            var appModalEl = document.getElementById('appModal');
            var appModal = bootstrap.Modal.getOrCreateInstance(appModalEl);

            $('#appModalConfirmBtn').off('click').on('click', function() {
                appModal.hide();
                $(appModalEl).one('hidden.bs.modal', function () {
                    if (typeof onConfirm === 'function') {
                        onConfirm();
                        onConfirm = null; 
                    }
                });
            });

            appModal.show();
        }

        // Sidebar toggle
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });

        // Modal Open Trigger
        $('#btnOpenModal').on('click', function() {
            $('#uploadBatchForm')[0].reset();
            var uploadModalEl = document.getElementById('uploadModal');
            var uploadModal = bootstrap.Modal.getOrCreateInstance(uploadModalEl);
            uploadModal.show();
        });

        // Guide Modal Open Trigger
        $('#btnOpenGuide').on('click', function() {
            var guideModalEl = document.getElementById('guideModal');
            var guideModal = bootstrap.Modal.getOrCreateInstance(guideModalEl);
            guideModal.show();
        });

        // Start Tour Button Trigger with Auto-Modal Opening & Field Sequence
        $('#btnStartTour').on('click', function() {
            var tour = introJs().setOptions({
                steps: [
                    {
                        element: document.querySelector('#btnOpenGuide'),
                        intro: "Please read this first so you are guided through the <B>UPLOADING VALIDATION PROCESS.</B>",
                        position: 'bottom'
                    },
                    {
                        element: document.querySelector('#btnOpenModal'),
                        intro: "To upload your FOR <b>GSIS ENROLLMENT LIST</b>, click this button.",
                        position: 'bottom'
                    },    
                    {
                        element: document.querySelector('#excel_file'),
                        intro: "<b>Select Excel File:</b> Click here to select your <B>FOR GSIS ENROLLMENT FILE</B>.",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('#area_of_implementation'),
                        intro: "<b>Area of Implementation:</b> Put the area of implementation of your <B>FOR GSIS ENROLLMENT</B> file here, these details can be seen on the <B>DETAILS</B> sheet of the <B>TUPAD_Benefs_Profile_Template_2026</B>",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('#period_of_coverage'),
                        intro: "<b>Period of Coverage:</b> Put the period coverage of the <b>GSIS INSURANCE</b> <font size='1px'><i>(eg. January 1, 2026 to January 1, 2027).</ofnt><i>",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('#adl_no'),
                        intro: "<b>ADL No.:</b> Put the ADL No. here, these details can be seen on the <B>DETAILS</B> sheet of the <B>TUPAD_Benefs_Profile_Template_2026</B>",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('#reference_no'),
                        intro: "<b>Reference No.:</b> Put the Reference No. here, these details can be seen on the <B>DETAILS</B> sheet of the <B>TUPAD_Benefs_Profile_Template_2026</B> ",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('#nature_of_work'),
                        intro: "<b>Nature of Work:</b> Put the nature of work here, these details can be seen on the <B>DETAILS</B> sheet of the <B>TUPAD_Benefs_Profile_Template_2026</B>",
                        position: 'right'
                    },
                    {
                        element: document.querySelector('#btnSubmitBatch'),
                        intro: "<b>Save & Upload:</b> Click this button to start the uploading process.",
                        position: 'top'
                    },
                    {
                        element: document.querySelector('#filesTable'),
                        intro: "The uploaded <B>GSIS ENROLLMENT</B> lists can be viewed here, you will only be able to see your <B>ASSIGNED PROVINCE</B> in this portion",
                        position: 'top'
                    },
                ],
                showProgress: true,
                showStepNumbers: false,
                exitOnOverlayClick: false
            });

            tour.onbeforechange(function (targetElement) {
                var modalFieldIds = ['excel_file', 'area_of_implementation', 'period_of_coverage', 'adl_no', 'reference_no', 'nature_of_work', 'btnSubmitBatch'];
                var uploadModalEl = document.getElementById('uploadModal');
                var uploadModal = bootstrap.Modal.getOrCreateInstance(uploadModalEl);
                
                // Automatically open modal when reaching modal fields, or close it when reaching the main table
                if (modalFieldIds.includes(targetElement.id)) {
                    uploadModal.show();
                } else if (targetElement.id === 'filesTable') {
                    uploadModal.hide();
                }

                // Refresh tooltip coordinates after DOM/modal animations finish rendering
                setTimeout(function() {
                    tour.refresh();
                }, 400);
            });

            tour.start();
        });

        // Submit Form via AJAX (Upload Modal with fallback cleanups)
        $('#btnSubmitBatch').on('click', function() {
            var form = $('#uploadBatchForm')[0];
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            var formData = new FormData(form);
            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Uploading...');

            $.ajax({
                url: "<?php echo site_url('tupad/upload_tupad_excel'); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Save & Upload');
                    
                    if (response.status === 'success' || response.success === true) {
                        var uploadModalEl = document.getElementById('uploadModal');
                        var uploadModal = bootstrap.Modal.getOrCreateInstance(uploadModalEl);
                        uploadModal.hide();
                        location.reload();
                    } else {
                        $('#uploadModal').modal('hide');
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('overflow', '');

                        if (response.reload === true) {
                            location.reload();
                        } else {
                            var errorMsg = response.message || response.error || response.msg || 'An error occurred during upload.';
                            showCustomAlert(errorMsg, 'Upload Notice');
                        }
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Save & Upload');
                    
                    var errorMsg = 'An error occurred during file upload.';
                    if (xhr.responseJSON) {
                        errorMsg = xhr.responseJSON.message || xhr.responseJSON.error || xhr.responseJSON.msg || errorMsg;
                    }
                    
                    $('#uploadModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');

                    showCustomAlert(errorMsg, 'System Error');
                }
            });
        });

        // Forward to GSIS Letter Button Handler via AJAX
        $(document).on('click', '.btn-forward-gsis', function() {
            var $btn = $(this);
            var fileName = $btn.data('filename');
            
            showCustomConfirm('Are you sure you want to forward the details of "' + fileName + '" to the GSIS Letter table?', function() {
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Forwarding...');

                $.ajax({
                    url: "<?php echo site_url('tupad/forward_gsis_letter'); ?>",
                    type: "POST",
                    data: { file_name: fileName },
                    dataType: "json",
                    success: function(response) {
                        var isSuccess = (response.status === 'success' || response.success === true || response.status === true);
                        
                        if (isSuccess) {
                            $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i> GSIS Letter');
                            var msg = response.message || response.msg || 'Successfully forwarded to GSIS Letter.';
                            showCustomAlert(msg, 'GSIS Forward');
                            $('#filesTable').DataTable().ajax.reload(null, false);
                        } else if (response.status === 'exists') {
                            $btn.prop('disabled', true)
                                .addClass('disabled btn-secondary')
                                .removeClass('btn-warning')
                                .html('<i class="bi bi-check-circle-fill me-1"></i> Forwarded');
                            
                            var warningMsg = response.message || 'Duplicate details encountered.';
                            showCustomAlert(warningMsg, 'Duplicate / Notice');
                            $('#filesTable').DataTable().ajax.reload(null, false);
                        } else {
                            $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i> GSIS Letter');
                            var warningMsg = response.message || response.error || response.msg || 'Notice encountered.';
                            showCustomAlert(warningMsg, 'Notice');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i> GSIS Letter');
                        var errorMsg = 'An error occurred while forwarding details.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showCustomAlert(errorMsg, 'System Error');
                    }
                });
            }, 'Confirm Forward');
        });

        // Revert / Delete GSIS Letter Button Handler via AJAX
        $(document).on('click', '.btn-delete-gsis', function() {
            var $btn = $(this);
            var fileName = $btn.data('filename');
            
            showCustomConfirm('Are you sure you want to remove "' + fileName + '" from the GSIS Letter table? This will revert its status.', function() {
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                $.ajax({
                    url: "<?php echo site_url('tupad/delete_gsis_letter'); ?>",
                    type: "POST",
                    data: { file_name: fileName },
                    dataType: "json",
                    success: function(response) {
                        var isSuccess = (response.status === 'success' || response.success === true || response.status === true);
                        
                        if (isSuccess) {
                            showCustomAlert(response.message || 'Successfully removed.', 'Success');
                            $('#filesTable').DataTable().ajax.reload(null, false);
                        } else {
                            $btn.prop('disabled', false).html('<i class="bi bi-trash-fill"></i>');
                            showCustomAlert(response.message || response.error || 'Failed to delete GSIS entry.', 'Error');
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.prop('disabled', false).html('<i class="bi bi-trash-fill"></i>');
                        showCustomAlert('DEBUG ERROR: ' + (xhr.responseText ? xhr.responseText.substring(0, 150) : error), 'System Error');
                    }
                });
            }, 'Confirm Revert');
        });

        const table = $('#filesTable').DataTable({
            processing: true,
            serverSide: true,
            serverMethod: 'post',
            ajax: {
                url: "<?php echo site_url('tupad/get_files_json'); ?>"
            },
            columns: [
                { orderable: true },  // Col 0: File Name
                { orderable: true },  // Col 1: Reference No.
                { orderable: true },  // Col 2: Total Records
                { orderable: false }, // Col 3: Uploaded By
                { orderable: false }, // Col 4: Date Uploaded
                { orderable: false }  // Col 5: Action
            ],
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            order: [[0, 'asc']],
            dom: 'rtip',
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading files...',
                info: "Showing _START_ to _END_ of _TOTAL_ files",
                infoEmpty: "No files available",
                zeroRecords: "No matching files found",
                paginate: {
                    previous: "<i class='bi bi-chevron-left'></i>",
                    next: "<i class='bi bi-chevron-right'></i>"
                }
            }
        });

        // Custom External Search input listener
        $('#fileSearch').on('keyup', function () {
            table.search(this.value).draw();
        });
    });

    if (response.status === 'limit_exceeded') {
    // Trigger your modal here
    $('#errorModalMessage').text(response.message);
    $('#errorModal').modal('show');
}
</script>

</body>

</html>