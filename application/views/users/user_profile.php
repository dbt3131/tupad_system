<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Profile</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #1e3a8a;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
        }

        #main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .profile-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .section-title {
           font-size: 0.75rem;
           text-transform: uppercase;
           letter-spacing: 0.05em;
           font-weight: 700;
           color: #475569;
           border-bottom: 2px solid #cbd5e1;
           padding-bottom: 6px;
           margin-bottom: 1rem;
           margin-top: 1.25rem;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .info-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.1rem;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: #0f172a;
        }

        @media (max-width: 991.98px) {
            #main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>

    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">

           <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-person me-2"></i>User Profile
                    </h3>
                    <p class="text-muted small mb-0">User Personal Data.</p>
                </div>
            </div>

            <!-- Profile Details Card -->
            <div class="profile-card p-3 p-md-4">
                
                <!-- Section: Personal Information -->
                <div class="section-title">Personal Information</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="info-label">First Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['reg_fname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Middle Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['reg_mname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Last Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['reg_lname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Extension Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['reg_extname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></div>
                    </div>
                </div>

                <!-- Section: Assignment & Organizational Information -->
                <div class="section-title">Organizational & Assignment Details</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="info-label">Position</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['position_description'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Office</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['office_description'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Division</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['division_description'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Assigned Province</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['provDesc'] ?? 'N/A'); ?></div>
                    </div>
                </div>

            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '#sidebarToggle', function (e) {
                e.preventDefault();
                
                if ($(window).width() < 992) {
                    $('#sidebar').toggleClass('show-mobile');
                } else {
                    $('#sidebar').toggleClass('collapsed');
                    $('#main-content').toggleClass('expanded');
                }
            });
        });
    </script>
</body>
</html>