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
            max-width: 900px;
            margin: 0 auto;
        }


/* 1. Style for your Text Input Placeholder */
.form-control::placeholder {
    color: #797a7846;
    font-style: italic;
    opacity: 1;
}

/* 1. Unselected Date Input (Placeholder look - e.g., red/italic) */
input[type="date"].form-control:invalid::-webkit-datetime-edit {
    color: #797a7846;
    font-style: italic;
}

/* 2. Selected Date Input (Turns black and normal style once a date is picked) */
input[type="date"].form-control:valid {
    color: #000000;
    font-style: normal;
}
input[type="date"].form-control:valid::-webkit-datetime-edit {
    color: #000000;
    font-style: normal;
}

/* Calendar icon styling */
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
                    <p class="text-muted small mb-0">Register new Authority to Debit Line (ADL) details</p>
                </div>
            </div>

            <div class="container-fluid px-0">
                <div class="form-card p-4 p-md-5">
                    
                    <!-- Form with submission prevention binding -->
                    <form action="<?= site_url('adl/store'); ?>" method="POST" id="adlForm">
                        <div class="row g-3">
                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">ADL No.</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="adl_no" class="form-control" placeholder="Enter ADL Number" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">ADL Sponsor</label>
                                <div class="input-group">
                                    <input type="text" name="adl_sponsor" class="form-control" placeholder="Enter ADL Sponsor" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">ADL Date</label>
                                <input type="date" name="adl_date" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Date Received</label>
                                <input type="date" name="date_received" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Target Beneficiaries</label>
                                <input type="number" name="target_benefs" class="form-control" min="1" placeholder="Total target beneficiaries" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Amount</label>
                                <input type="number" name="adl_amount" class="form-control" placeholder="Amount" required>
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
                                    <th>ADL Sponsor</th>
                                    <th>Date Received</th>
                                    <th>Target Beneficiaries</th>                             
                                    <th>Amount</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($adl_records)): ?>
                                    <?php foreach ($adl_records as $row): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= html_escape($row['adl_no']); ?></td>
                                            <td><?= html_escape($row['adl_sponsor']); ?></td>
                                            <td><?= html_escape($row['adl_date']); ?></td>
                                            <td><?= html_escape($row['date_received']); ?></td>
                                            <td><?= number_format($row['target_benefs']); ?></td>
                                            <td class="fw-semibold text-success">&#8369;<?= number_format($row['adl_amount'], 2); ?></td>
                                            <td class="fw-semibold text-primary">&#8369;<?= number_format($row['balance'], 2); ?></td>
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
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="duplicateAdlModalLabel">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Duplicate ADL Number Found
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <p class="mb-0">The ADL Number <strong id="modalDuplicateAdlNo"></strong> is already recorded in the database. Please use a unique ADL Number or check existing records.</p>
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
            "order": [[1, "desc"]] // Orders by ADL Date column descending by default
        });

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

        // Prevent Multiple Form Submissions and Check for Duplicate ADL via AJAX
        $('#adlForm').on('submit', function (e) {
            e.preventDefault(); 

            const $form = $(this);
            const $submitBtn = $('#submitBtn');
            const adlNoInput = $('input[name="adl_no"]').val().trim();

            if ($form[0].checkValidity() === false) {
                $form[0].reportValidity();
                return; 
            }

            $.ajax({
                url: "<?= site_url('adl/check_duplicate_adl'); ?>",
                type: "GET",
                data: { adl_no: adlNoInput },
                dataType: "json",
                success: function (response) {
                    if (response.exists) {
                        $('#modalDuplicateAdlNo').text(adlNoInput);
                        const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateAdlModal'));
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

        // Dynamic Dependent Dropdown for Municipalities/Cities based on Province Code
        $('#adl_province').on('change', function () {
            const provCode = $(this).val();
            const $cityMunSelect = $('#area_of_implementation');

            if (provCode) {
                $cityMunSelect.prop('disabled', true).html('<option value="">Loading municipalities...</option>');

                $.ajax({
                    url: "<?= site_url('adl/get_municipalities_by_province'); ?>",
                    type: "GET",
                    data: { provCode: provCode },
                    dataType: "json",
                    success: function (data) {
                        $cityMunSelect.empty().append('<option value="" selected disabled>Select City/Municipality</option>');
                        if (data && data.length > 0) {
                            $.each(data, function (index, item) {
                                $cityMunSelect.append('<option value="' + item.cityCode + '">' + item.citymunDesc + '</option>');
                            });
                            $cityMunSelect.prop('disabled', false);
                        } else {
                            $cityMunSelect.append('<option value="" disabled>No municipalities found</option>');
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