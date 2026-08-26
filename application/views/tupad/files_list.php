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

            <!-- Page Header & Upload Trigger -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-folder-fill text-primary me-2"></i>Uploaded Batch Files
                    </h3>
                    <p class="text-muted small mb-0">Select an uploaded Excel file to view its individual beneficiary records.</p>
                </div>

                <!-- Modal Trigger Button -->
                <button type="button" class="btn btn-success px-3 py-2 fw-semibold mb-0 cursor-pointer shadow-sm" id="btnOpenModal">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload New Excel
                </button>
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

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function () {
        var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

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
            uploadModal.show();
        });

        // Submit Form via AJAX
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
                    if (response.status === 'success') {
                        uploadModal.hide();
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Save & Upload');
                    alert('An error occurred during file upload.');
                }
            });
        });

        // Forward to GSIS Letter Button Handler via AJAX
        $(document).on('click', '.btn-forward-gsis', function() {
            var fileName = $(this).data('filename');
            if (!confirm('Are you sure you want to forward the details of "' + fileName + '" to the GSIS Letter table?')) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Forwarding...');

            $.ajax({
                url: "<?php echo site_url('tupad/forward_gsis_letter'); ?>",
                type: "POST",
                data: { file_name: fileName },
                dataType: "json",
                success: function(response) {
                    $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i> GSIS Letter');
                    alert(response.message);
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-send-fill me-1"></i> GSIS Letter');
                    alert('An error occurred while forwarding details.');
                }
            });
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
    </script>

</body>

</html>