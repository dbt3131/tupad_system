<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beneficiary Profile - <?php echo htmlspecialchars($record['tupad_fname'] . ' ' . $record['tupad_lname']); ?></title>

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
           color: #475569; /* Slightly darker slate for better contrast */
           border-bottom: 2px solid #cbd5e1; /* Darker border color to make it clearly visible */
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
            
            <!-- Header & Back Button -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <a href="javascript:window.history.back();" class="btn btn-sm btn-outline-secondary mb-1">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i>Beneficiary Profile Information
                    </h4>
                </div>
            </div>

            <!-- Profile Details Card -->
            <div class="profile-card p-3 p-md-4">
                
                <!-- Section: Personal Information -->
                <div class="section-title">Personal & Identification Details</div>
                <div class="row g-3">
               
                    <div class="col-md-3">
                        <div class="info-label">Status</div>
                        <div>
                            <?php if (isset($record['tupad_active']) && $record['tupad_active'] == '0'): ?>
                                <span class="badge bg-success-subtle text-success fw-semibold">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary fw-semibold">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['tupad_gender'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars(($record['tupad_dob_month'] ?? '') . '/' . ($record['tupad_dob_day'] ?? '') . '/' . ($record['tupad_dob_year'] ?? '')); ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="info-label">First Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['tupad_fname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Middle Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['tupad_mname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Last Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['tupad_lname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Extension Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['tupad_ext'] ?? 'N/A'); ?></div>
                    </div>
                </div>

                <!-- Section: Location Information -->
                <div class="section-title">Geographic Location</div>
                <div class="row g-3">
                     <div class="col-md-3">
                        <div class="info-label">District</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['tupad_district'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Province</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['province_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Municipality / City</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['municipality_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Barangay</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['barangay_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Street / House No.</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['tupad_street'] ?? 'N/A'); ?></div>
                    </div>
                </div>

                <!-- Section: Employment & Classification -->
                <div class="section-title">Employment & Program Classification</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="info-label">Type of Benefs</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['bene_type_desc'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Type of ID</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['type_desc'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Electronic Payment</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['epayment_desc'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($record['tupad_account_no'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Skills / Training Interest</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['skills_desc'] ?? 'N/A'); ?></div>
                    </div>
                </div>

                <!-- Section: System Audit / Meta Info -->
                <div class="section-title">System & Batch Information</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="info-label">Source Batch File</div>
                        <div class="info-value text-success"><i class="bi bi-file-earmark-excel me-1"></i><?php echo htmlspecialchars($record['file_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Uploaded By</div>
                        <div class="info-value"><?php echo htmlspecialchars(trim(($record['user_fname'] ?? '') . ' ' . ($record['reg_lname'] ?? '')) ?: 'N/A'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Date Uploaded</div>
                        <div class="info-value"><?php echo htmlspecialchars($record['uploaded_at'] ?? 'N/A'); ?></div>
                    </div>
                </div>

            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
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