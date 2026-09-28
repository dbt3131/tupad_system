<?php
// Helper functions to check data and apply styles automatically
function is_no_data($value) {
    $val = trim((string)$value);
    return ($val === '' || $val === '0000-00-00' || $val === '0.00' || $val === '0' || $val === null || $val === 'N/A');
}

function card_bg($value) {
    return is_no_data($value) ? 'bg-danger-subtle border border-danger border-opacity-25' : 'bg-light border border-light';
}

function display_val($value, $type = 'text') {
    if (is_no_data($value)) {
        return '<span class="text-danger fw-semibold fst-italic"><i class="bi bi-exclamation-circle me-1"></i>No Data</span>';
    }
    if ($type === 'currency') return number_format((float)$value, 2);
    if ($type === 'percent') return html_escape($value) . '%';
    return html_escape($value);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Proponent - DOLE TUPAD</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
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
            font-size: 14px;
        } 

        .record-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .data-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            font-weight: 600;
            display: block;
            margin-bottom: 0.25rem;
        }

        @media print {
            body { background-color: #ffffff !important; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; padding: 0 !important; }
            .record-card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        
        <!-- Sidebar -->
        <?php $this->load->view('templates/sidebar'); ?>

        <!-- Main Workspace Area -->
        <main class="p-3 p-md-4 flex-grow-1">
            
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-file-earmark-plus me-2"></i>Add Proponent Details
                    </h3>
                    <p class="text-muted small mb-0">Department of Labor and Employment &bull; Proponent Registry</p>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= $this->session->flashdata('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= $this->session->flashdata('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Content Area Card / Form -->
            <div class="record-card p-4 p-lg-5 mb-4">
                
                <div class="text-center pb-4 mb-4 border-bottom">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">DOLE TUPAD Program</span>
                    <h4 class="fw-bold text-dark mb-1">New Proponent Information Form</h4>
                    <p class="text-muted small mb-0">Fill in the fields below to register a new proponent into the system.</p>
                </div>

                <!-- Form targeting your controller method -->
                <form action="<?= site_url('adl/store_proponent'); ?>" method="POST" id="proponentForm">
                    
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="proponent_name" class="data-label">Proponent Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-primary"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" autocomplete="OFF" class="form-control" id="proponent_name" name="proponent_name" oninput="this.value = this.value.toUpperCase();" placeholder="Enter full name or organization of proponent" required>
                                </div>
                                <div id="proponentFeedback" class="form-text small mt-1">This will be automatically saved in uppercase format.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8 d-flex justify-content-end gap-2">
                            <a href="<?= site_url('adl/proponent_encode'); ?>" class="btn btn-light border px-4">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                            <button type="submit" id="submitBtn" class="btn px-4 shadow-sm text-white" style="background-color: #0f172a;">
    <i class="bi bi-save me-1"></i> Save Proponent
</button>
                        </div>
                    </div>

                </form>

            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function () {
        let isSubmitting = false; // Flag to track submission state

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

        // Live Duplicate Check via AJAX
        let timeout = null;
        $('#proponent_name').on('input', function () {
            clearTimeout(timeout);
            let proponentName = $(this).val().trim();
            let feedback = $('#proponentFeedback');
            let submitBtn = $('#submitBtn');

            if (proponentName === '') {
                feedback.html('This will be automatically saved in uppercase format.').removeClass('text-danger text-success');
                if (!isSubmitting) submitBtn.prop('disabled', false);
                return;
            }

            timeout = setTimeout(function () {
                $.ajax({
                    url: "<?= site_url('adl/check_duplicate_proponent'); ?>",
                    type: "GET",
                    data: { proponent_name: proponentName },
                    dataType: "json",
                    success: function (response) {
                        if (isSubmitting) return; // Ignore if already submitting

                        if (response.exists) {
                            feedback.html('<i class="bi bi-exclamation-triangle-fill me-1"></i> This proponent name already exists!').addClass('text-danger').removeClass('text-success');
                            submitBtn.prop('disabled', true);
                        } else {
                            feedback.html('<i class="bi bi-check-circle-fill me-1"></i> Proponent name is available.').addClass('text-success').removeClass('text-danger');
                            submitBtn.prop('disabled', false);
                        }
                    }
                });
            }, 300);
        });

        // Prevent multiple submissions completely
        $('#proponentForm').on('submit', function (e) {
            let submitBtn = $('#submitBtn');

            // If already submitting, stop it immediately
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            // Check if the button is disabled due to a duplicate name
            if (submitBtn.prop('disabled')) {
                e.preventDefault();
                alert('Please resolve any errors or use a unique proponent name before saving.');
                return false;
            }

            // Set the lock flag
            isSubmitting = true;

            // Immediately disable and change appearance
            submitBtn.prop('disabled', true);
            submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...');
        });
    });

    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'F12') {
            e.preventDefault();
        }
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
            e.preventDefault();
        }
        if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
            e.preventDefault();
        }
    });
    </script>

</body>

</html>