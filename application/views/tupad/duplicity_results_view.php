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


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Duplicity Results: <span class="text-uppercase text-primary"><?php echo htmlspecialchars(str_replace('_', ' ', $match_level)); ?> Match</span></h4>
            <a href="<?php echo site_url('tupad/duplicity_check'); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Config</a>
        </div>

        <?php if (empty($duplicates)): ?>
            <div class="alert alert-info shadow-sm">No duplicate records found matching the <strong><?php echo htmlspecialchars(str_replace('_', ' ', $match_level)); ?></strong> criteria level.</div>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Actions</th>
                                <th>Occurrences</th>
                                <th>Full Name</th>
                                <th>Birthdate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($duplicates as $row): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo site_url('tupad/view_duplicate_cluster?level=' . $match_level . 
    '&fname=' . urlencode($row['tupad_fname']) . 
    '&mname=' . urlencode($row['tupad_mname']) . 
    '&lname=' . urlencode($row['tupad_lname']) . 
    '&month=' . urlencode($row['tupad_dob_month']) . 
    '&day=' . urlencode($row['tupad_dob_day']) . 
    '&year=' . urlencode($row['tupad_dob_year']) . 
    '&province=' . urlencode($row['tupad_province']) . 
    '&city=' . urlencode($row['tupad_municipality']) . 
    '&barangay=' . urlencode($row['tupad_barangay']) . 
    '&file_name=' . urlencode($row['file_name'])); ?>" 
    class="btn btn-sm btn-warning">
    <i class="bi bi-eye"></i> View Duplicate Group
</a>
                                    </td>
                                    <td><span class="badge bg-danger fs-6"><?php echo $row['duplicate_count']; ?> records</span></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['tupad_fname'] . ' ' . $row['tupad_mname'] . ' ' . $row['tupad_lname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tupad_dob_month'] . '/' . $row['tupad_dob_day'] . '/' . $row['tupad_dob_year']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
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