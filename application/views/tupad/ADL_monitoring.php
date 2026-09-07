<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Encoding - DOLE TUPAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
                    
                    <form action="<?= site_url('adl/store'); ?>" method="POST">
                        <div class="row g-3">
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">ADL No.</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="adl_no" class="form-control" placeholder="Enter ADL Number" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">ADL Date</label>
                                <input type="date" name="adl_date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Date Received</label>
                                <input type="date" name="date_received" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Target Beneficiaries</label>
                                <input type="number" name="target_benefs" class="form-control" min="1" placeholder="Total target beneficiaries" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Province</label>
                                <select name="adl_province" id="adl_province" class="form-select" required>
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
                                <label class="form-label fw-semibold small">Area of Implementation (City/Municipality)</label>
                                <select name="area_of_implementation" id="area_of_implementation" class="form-select" required disabled>
                                    <option value="" selected disabled>Select Province First</option>
                                </select>
                            </div>

                            <input type="hidden" name="encoded_date" value="<?= date('Y-m-d'); ?>">

                            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="<?= site_url('adl/ADL_encode'); ?>" class="btn btn-light border px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Save ADL Record
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

        // Dynamic Dependent Dropdown for Municipalities/Cities based on Province Code
        $('#adl_province').on('change', function () {
            const provCode = $(this).val();
            const $cityMunSelect = $('#area_of_implementation');

            if (provCode) {
                $cityMunSelect.prop('disabled', true).html('<option value="">Loading municipalities...</option>');

                // Perform AJAX request to fetch cities/municipalities matching the selected provCode
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