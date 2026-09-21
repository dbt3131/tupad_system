<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Encoding - DOLE TUPAD</title>

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

        /* Custom Button & Modal Header Overrides */
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
            max-width: 900px;
            margin: 0 auto;
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
                        <i class="bi bi-journal-plus text-primary me-2"></i>ADL ENCODING
                    </h3>
                    <p class="text-muted small mb-0">Register new Advice of Disbursement Limits</p>
                </div>
            </div>

            <div class="container-fluid px-0">
                <div class="form-card p-4 p-md-5">
                    
                    <form action="<?= site_url('adl/store'); ?>" method="POST" id="adlForm">
                        <div class="row g-3">
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">ADL No.</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="adl_no" class="form-control" placeholder="Enter ADL Number" oninput="this.value = this.value.toUpperCase();" autocomplete="OFF" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">ADL Subsidy Cost</label>
                                <div class="input-group">
                                    <input type="number" name="adl_subsidy" class="form-control" step="0.01" oninput="this.value = this.value.toUpperCase();" placeholder="Enter ADL Subsidy" autocomplete="OFF" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">ADL Admin Cost</label>
                                <input type="number" name="adl_admin_cost" class="form-control" step="0.01" placeholder="Enter ADL Admin Cost" autocomplete="OFF" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">ADL Date</label>
                                <input type="date" name="adl_date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Date Received</label>
                                <input type="date" name="date_received" class="form-control" required>
                            </div>

                            <input type="hidden" name="encoded_date" value="<?= date('Y-m-d'); ?>">

                            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="<?= site_url('adl/ADL_encode'); ?>" class="btn btn-light border px-4">Cancel</a>
                                <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Save ADL Record
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

            <!-- Data Table Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-list-check me-2"></i>Registered ADL Records List
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="adlTable" class="table table-striped table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>ADL No.</th>
                                    <th>ADL Date</th>
                                    <th>ADL Subsidy Cost</th>
                                    <th>ADL Admin Cost</th>
                                    <th>Date Received</th>
                                    <th>Target Beneficiaries</th>                                          
                                    <th>Balance</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($adl_records)): ?>
                                    <?php foreach ($adl_records as$row): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= html_escape($row['adl_no']); ?></td>
                                            <td><?= html_escape($row['adl_date']); ?></td>
                                            <td class="fw-semibold text-success">&#8369;<?= number_format($row['adl_subsidy'], 2); ?></td>  
                                            <td class="fw-semibold text-success">&#8369;<?= number_format($row['adl_admin_cost'], 2); ?></td>                    
                                            <td><?= html_escape($row['date_received']); ?></td>
                                            <td><?= number_format($row['target_benefs']); ?></td>                                       
                                            <td class="fw-semibold text-primary">&#8369;<?= number_format($row['balance'], 2); ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary open-maf-modal" 
                                                    data-adl-no="<?= html_escape($row['adl_no']); ?>"
                                                    title="Add MAF Record">
                                                    <i class="bi bi-folder-plus"></i> Add MAF
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary open-edit-adl-modal" 
                                                     data-adl-no="<?= html_escape($row['adl_no']); ?>"
                                                     title="Edit ADL Record">
                                                     <i class="bi bi-pencil-square"></i> Edit
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

            <!-- Duplicate ADL Warning Modal -->
            <div class="modal fade" id="duplicateAdlModal" tabindex="-1" aria-labelledby="duplicateAdlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title" id="duplicateAdlModalLabel">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Duplicate ADL Number Found
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <!-- Target element to display the conflicting ADL number -->
                            <p class="mb-0">The ADL Number <strong id="modalDuplicateAdlNo" class="text-danger"></strong> is already recorded in the database. Please use a unique ADL Number or check existing records.</p>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit ADL Modal -->
            <div class="modal fade" id="editAdlModal" tabindex="-1" aria-labelledby="editAdlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="<?= site_url('adl/update_adl'); ?>" method="POST" id="editAdlForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editAdlModalLabel">
                                    <i class="bi bi-pencil-square me-2"></i>Edit ADL Record
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="original_adl_no" id="edit_original_adl_no">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">ADL No.</label>
                                    <input type="text" name="adl_no" id="edit_adl_no" class="form-control" oninput="this.value = this.value.toUpperCase();" disabled title="You cannot edit this portion">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">ADL Subsidy Cost</label>
                                    <input type="text" name="adl_subsidy" step="0.01" id="edit_adl_subsidy" class="form-control" oninput="this.value = this.value.toUpperCase();" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">ADL Admin Cost</label>
                                    <input type="number" step="0.01" name="adl_admin_cost" id="edit_adl_admin_cost" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">ADL Date</label>
                                    <input type="date" name="adl_date" id="edit_adl_date" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Date Received</label>
                                    <input type="date" name="date_received" id="edit_date_received" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">ADL Benefs</label>
                                    <input type="number" name="adl_benefs" id="edit_adl_benefs" class="form-control" required placeholder="0">
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" id="updateAdlBtn" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Update Record
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MAF Entry Modal -->
            <div class="modal fade" id="adlMafModal" tabindex="-1" aria-labelledby="adlMafModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="<?= site_url('adl/store_adl_maf'); ?>" method="POST" id="mafForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="adlMafModalLabel">
                                    <i class="bi bi-file-earmark-plus me-2"></i>Add MAF Record
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="adl_no" id="modal_adl_no">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">MAF No.</label>
                                    <input type="text" name="maf_no" class="form-control" placeholder="Enter MAF No." required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">MAF Program</label>
                                    <select name="maf_program" class="form-select" required>
                                        <option value="" selected disabled>Select Program / Office</option>
                                        <?php if (!empty($offices)): ?>
                                            <?php foreach ($offices as$office): ?>
                                                <option value="<?= html_escape($office['office_id']); ?>">
                                                    <?= html_escape($office['office_description']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">MAF Amount</label>
                                    <input type="number" step="0.01" name="maf_amount" class="form-control" placeholder="Enter Amount" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">MAF Remarks</label>
                                    <input type="text" name="maf_remarks" class="form-control" placeholder="Remarks">
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" id="saveMafBtn" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Save MAF
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>

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
        // Initialize DataTable with built-in search, pagination, and sorting
        $('#adlTable').DataTable({
            "language": {
                "emptyTable": "No ADL records found."
            },
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "order": [[1, "desc"]]
        });

        // Sidebar Toggle Handler
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {$('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });

        // Open MAF Modal handler and pass ADL No.
        $(document).on('click', '.open-maf-modal', function () {
            const adlNo = $(this).data('adl-no');$('#modal_adl_no').val(adlNo);
            const mafModal = new bootstrap.Modal(document.getElementById('adlMafModal'));
            mafModal.show();
        });

        // Prevent Multiple Form Submissions and Check for Duplicate ADL via AJAX
        $('#adlForm').on('submit', function (e) {
            e.preventDefault(); 

            const $form =$(this);
            const $submitBtn =$('#submitBtn');
            const adlNoInput = $('input[name="adl_no"]').val().trim();

            if ($form[0].checkValidity() === false) {$form[0].reportValidity();
                return; 
            }

            // AJAX call to check duplicate ADL number server-side
            $.ajax({
                url: "<?= site_url('adl/check_duplicate_adl'); ?>",
                type: "GET",
                data: { adl_no: adlNoInput },
                dataType: "json",
                success: function (response) {
                    if (response.exists) {
                        // Inject the specific ADL number into the duplicate warning modal text
                        $('#modalDuplicateAdlNo').text(adlNoInput);
                        
                        // Trigger the modal view
                        const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateAdlModal'));
                        duplicateModal.show();
                    } else {
                        $submitBtn.prop('disabled', true);$submitBtn.html(`
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

        // Prevent Multiple Form Submissions for MAF Modal
        $('#mafForm').on('submit', function (e) {
            const $form =$(this);
            const $submitBtn =$('#saveMafBtn');

            if ($form[0].checkValidity() === false) {$form[0].reportValidity();
                return; 
            }

            $submitBtn.prop('disabled', true);$submitBtn.html(`
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Saving MAF...
            `);
        });
    });

    // Edit ADL Modal Population via AJAX
    $(document).on('click', '.open-edit-adl-modal', function () {
        const adlNo = $(this).data('adl-no');$.ajax({
            url: "<?= site_url('adl/get_adl_details'); ?>",
            type: "GET",
            data: { adl_no: adlNo },
            dataType: "json",
            success: function (response) {
                if (response.status && response.data) {
                    const data = response.data;
                    $('#edit_original_adl_no').val(data.adl_no);
                    $('#edit_adl_no').val(data.adl_no);
                    $('#edit_adl_subsidy').val(data.adl_subsidy);
                    $('#edit_adl_benefs').val(data.target_benefs);
                    $('#edit_adl_date').val(data.adl_date);
                    $('#edit_date_received').val(data.date_received);
                    $('#edit_adl_admin_cost').val(data.adl_admin_cost);

                    const editModal = new bootstrap.Modal(document.getElementById('editAdlModal'));
                    editModal.show();
                } else {
                    alert('Record not found in the database.');
                }
            },
            error: function (xhr, status, error) {
                console.log(error);
                alert('AJAX Error: Failed to communicate with the server.');
            }
        });
    });
    </script>
</body>

</html>