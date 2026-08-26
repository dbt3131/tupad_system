<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Records</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .container-fluid {
    width: 100%;
    max-width: 100%;
}

main.flex-grow-1 {
    width: 100%;
    max-width: 100%;
}
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
            margin: 0;
            padding: 0;
        }

        /* --- MAIN CONTENT & NAVBAR --- */
        #main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease-in-out;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
            max-width: 100%;
            overflow-x: hidden;
        }

        #main-content.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* Responsive Fixes for Mobile / Small Screens (< 992px) */
        @media (max-width: 991.98px) {
            #main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        <?php $this->load->view('templates/sidebar'); ?>
        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1">
<div class="container-fluid px-2 px-md-3">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Duplicate Group Inspection
        </h4>
        <?php 
            $level_labels = [
                'exact' => ['text' => 'Exact Match', 'class' => 'bg-danger'],
                'highly_possible' => ['text' => 'Highly Possible Match', 'class' => 'bg-warning text-dark'],
                'possible' => ['text' => 'Possible Match', 'class' => 'bg-info text-dark'],
                'probable' => ['text' => 'Probable Match', 'class' => 'bg-primary']
            ];
            $lvl = $match_level ?? 'exact';
            $badge_text = $level_labels[$lvl]['text'] ?? 'Match Group';
            $badge_class = $level_labels[$lvl]['class'] ?? 'bg-secondary';
        ?>
        <span class="badge <?php echo $badge_class; ?> fs-6">Duplicity Results: <?php echo $badge_text; ?></span>
    </div>
    <div>
        <a href="<?php echo site_url('tupad/export_cluster_xlsx?' . $_SERVER['QUERY_STRING']); ?>" class="btn btn-success btn-sm me-2">
            <i class="bi bi-file-earmark-excel me-1"></i> Export to Excel
        </a>
        <a href="javascript:window.history.back();" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Results
        </a>
    </div>
</div>

        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Profile</th>
                            <th>Full Name</th>
                            <th>Birthdate</th>
                            <th>Province</th>
                            <th>City/Municipality</th>
                            <th>Barangay</th>
                            <th>Source File Name</th>
                            <th>Uploaded At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cluster_members as $member): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo site_url('tupad/view_profile/' . $member['id']); ?>" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="bi bi-eye"></i> View Profile
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars(strtoupper($member['tupad_fname'] . ' ' . $member['tupad_mname'] . ' ' . $member['tupad_lname'] . ' ' . $member['tupad_ext'])); ?></td>
                                <td><?php echo htmlspecialchars($member['tupad_dob_month'] . '/' . $member['tupad_dob_day'] . '/' . $member['tupad_dob_year']); ?></td>
                                <td><?php echo htmlspecialchars($member['province_name']); ?></td>
                                <td><?php echo htmlspecialchars($member['municipality_name']); ?></td>
                                <td><?php echo htmlspecialchars($member['barangay_name']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($member['file_name']); ?></span></td>
                                <td><?php echo htmlspecialchars($member['uploaded_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>























        </main>

        <!-- Footer -->
        <footer class="bg-white border-top p-3 text-center text-muted small">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- jQuery & Bootstrap JS Bundle -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>  
    $(document).ready(function () {
        // Sidebar Toggle Trigger (Burger Menu)
        $(document).on('click', '#sidebarToggle', function () {
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