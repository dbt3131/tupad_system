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

      <form action="<?php echo site_url('tupad/check_duplicity'); ?>" method="POST">
                    <div class="mb-3">
                        <label for="match_level" class="form-label fw-semibold">Select Duplicity Match Strictness Level:</label>
                        <select name="match_level" id="match_level" class="form-select" required>
                            <option value="exact">Exact Match (Full Name + Exact Birthdate)</option>
                            <option value="highly_possible">Highly Possible Match (Full Name + Exact Birthdate)</option>
                            <option value="possible">Possible Match (First & Last Name + Birth Year & Month)</option>
                            <option value="probable">Probable Match (Last Name + Exact Birthdate)</option>
                        </select>
                        <div class="form-text">Choose how strict the matching criteria engine should evaluate database rows.</div>
                    </div>
                    
                    <input type="hidden" name="province" value="">
                    <input type="hidden" name="city" value="">
                    <input type="hidden" name="barangay" value="">
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i> Run Duplicity Analysis
                    </button>
                </form>






















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