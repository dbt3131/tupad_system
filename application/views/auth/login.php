<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PRISM</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #020a16, #090411);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .login-header {
            background: #ffffff;
            text-align: center;
            padding: 30px 20px 10px;
        }

        .login-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .rounded-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .login-logo-rect {
            max-width: 520px;
            max-height: 110px;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .login-body {
            padding: 30px;
        }

        .form-control {
            height: 48px;
            border-radius: 10px;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fa;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }

        .btn-login {
            height: 48px;
            border-radius: 10px;
            font-weight: 600;
        }

        .register-link {
            text-decoration: none;
            font-weight: 600;
        }

        .footer-text {
            color: #6c757d;
            font-size: 13px;
        }

        /* Modern Wide Modal Enhancements */
        .modal-modern .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.25rem 1.5rem;
        }

        .resource-card-item {
            border: 1px solid #eef2f7;
            border-radius: 12px;
            transition: all 0.25s ease-in-out;
            background: #ffffff;
        }

        .resource-card-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
            background-color: #fafbfc;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
            
            <div class="card login-card shadow-lg">
                <!-- Header -->
                <div class="login-header">
                    <div class="login-icon">
                        <img src="<?= base_url('assets/images/dolelogo.png'); ?>" alt="DOLE Logo" class="rounded-logo">
                    </div>

                    <div class="login-icon-img text-center mb-3">
                        <img src="<?= base_url('assets/images/Prism_Logo.png'); ?>" alt="Prism Logo" class="img-fluid login-logo-rect">
                    </div>
                </div>

                <!-- Login Form -->
                <div class="login-body">
                    <!-- Flash Messages -->
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= html_escape($this->session->flashdata('success')); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= html_escape($this->session->flashdata('error')); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Validation Errors -->
                    <?= validation_errors('<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>', '</div>'); ?>

                    <form method="post" action="<?= site_url('auth/login'); ?>">
                        <!-- Hidden CAPTCHA Numbers -->
                        <input type="hidden" name="num1" value="<?= isset($num1) ? $num1 : 0; ?>">
                        <input type="hidden" name="num2" value="<?= isset($num2) ? $num2 : 0; ?>">

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?= set_value('email'); ?>" required autofocus>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- CAPTCHA Question Field -->
                        <div class="mb-4">
                            <label for="captcha" class="form-label fw-semibold">
                                Security Check: <span class="text-primary"><?= isset($captcha_question) ? $captcha_question : 'What is ? + ?'; ?></span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="number" id="captcha" name="captcha" class="form-control" placeholder="Enter the sum" required>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary btn-login w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>

                    <!-- Forgot Password Link -->
                    <div class="mt-3 text-end">
                        <a href="<?= site_url('auth/forgot_password'); ?>" class="text-decoration-none small fw-semibold">Forgot Password?</a>
                    </div>

                    <!-- Register Link -->
                    <p class="text-center mt-4 mb-0">
                        Don't have an account? 
                        <a href="<?= site_url('auth/register'); ?>" class="register-link">Create an account</a>
                    </p>

                    <!-- Consolidated Links Modal Trigger -->
                    <div class="text-center mt-3 pt-3 border-top">
                        <a href="#" class="text-muted small text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#consolidatedLinksModal">
                            <i class="bi bi-folder2-open me-1 text-primary"></i> Consolidated Resources & Links
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center footer-text mt-3">
                &copy; <?= date('Y'); ?> eTUPAD-PRISM
            </p>

        </div>
    </div>
</div>

<!-- Consolidated Links Modal (Wide, Modern, with Search Filter) -->
<div class="modal fade modal-modern" id="consolidatedLinksModal" tabindex="-1" aria-labelledby="consolidatedLinksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="consolidatedLinksModalLabel">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-link-45deg fs-5"></i>
                    </div>
                    <div>
                        <span>Consolidated Google Sheets & Drive Links</span>
                        <div class="text-muted fw-normal" style="font-size: 0.8rem;">Centralized tracking databases and documentation portals</div>
                    </div>
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <!-- Search Input Bar -->
                <div class="mb-3">
                    <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="resourceSearchInput" class="form-control border-start-0 ps-0 shadow-none" placeholder="Search any keyword, title, description..." onkeyup="filterResourceLinks()" style="height: 44px; font-size: 0.95rem;">
                    </div>
                </div>

                <!-- Links Container -->
                <div class="row g-3" id="resourceLinksContainer">
                    
                    <!-- Item 1 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1uc9z9t3DH6IanGlyau1lQ6_tM99h2WuvRQiKQ3PQcKc/edit?gid=522341313#gid=522341313" target="_blank" class="resource-card-item p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                                    <i class="bi bi-file-earmark-spreadsheet fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">2026 TUPAD Monitoring Sheet</h6>
                                    <p class="mb-0 text-muted small">Heto po yung sheet kung saan nilalagay mga newly coordinated ADLs.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3"><i class="bi bi-chevron-right"></i></div>
                        </a>
                    </div>
                    
                    <!-- Item 2 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1uc9z9t3DH6IanGlyau1lQ6_tM99h2WuvRQiKQ3PQcKc/edit?gid=522341313#gid=522341313" target="_blank" class="resource-card-item p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                                    <i class="bi bi-file-earmark-spreadsheet fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">2026 TUPAD Implementation Status</h6>
                                    <p class="mb-0 text-muted small">Heto po yung pinapasupply na report ni ms. CJ</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3"><i class="bi bi-chevron-right"></i></div>
                        </a>
                    </div>

                    <!-- Item 3 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1eNmQ2AYxFAUq3T2Cq-pXA6xJxEjReyoH6pF24m9_R0U/edit?gid=0#gid=0" target="_blank" class="resource-card-item p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                                    <i class="bi bi-file-earmark-spreadsheet fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">2026 Tupad Coordinators Directory</h6>
                                    <p class="mb-0 text-muted small">Heto po yung sheet kung saan nandito lahat ng mga TCs.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3"><i class="bi bi-chevron-right"></i></div>
                        </a>
                    </div>

                    <!-- Item 4 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1QsAzwlI8aqMTaazxCRuyslHHr0zCx1OdvaHlPKcP_to/edit?gid=1617754366#gid=1617754366" target="_blank" class="resource-card-item p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                                    <i class="bi bi-file-earmark-spreadsheet fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">2026 Tupad Payout Schedules</h6>
                                    <p class="mb-0 text-muted small">Heto po yung sheet kung saan makikita mga payrolls and schedules.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3"><i class="bi bi-chevron-right"></i></div>
                        </a>
                    </div>

                    <!-- Item 5 -->
                    <div class="col-12 resource-item">
                        <a href="https://drive.google.com/drive/folders/1f2r5i0ZmO97aWB3PGnLb9HbEtHn9-WfJ?usp=drive_link" target="_blank" class="resource-card-item p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning me-3">
                                    <i class="bi bi-folder-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">TUPAD Liquidation Drive</h6>
                                    <p class="mb-0 text-muted small">Heto po yung drive na inaaccess ng COA.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3"><i class="bi bi-chevron-right"></i></div>
                        </a>
                    </div>

                    <!-- Item 6 -->
                    <div class="col-12 resource-item">
                        <a href="https://drive.google.com/drive/folders/1kDzDmWbqDRtRcTycbeyLoMDGf639Wvnq?usp=drive_link" target="_blank" class="resource-card-item p-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning me-3">
                                    <i class="bi bi-folder-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">TUPAD Guidelines</h6>
                                    <p class="mb-0 text-muted small">Heto po yung drive kung saan nandito lahat ng mga guidelines ng TUPAD.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-3"><i class="bi bi-chevron-right"></i></div>
                        </a>
                    </div>

                </div>

                <!-- No Results State (Hidden by default) -->
                <div id="noSearchResults" class="text-center py-4 d-none">
                    <i class="bi bi-search text-muted fs-2 mb-2 d-block"></i>
                    <p class="text-muted mb-0 small">No matching resources found.</p>
                </div>
            </div>

            <div class="modal-footer bg-white border-top py-3 px-4">
                <span class="text-muted small me-auto"><i class="bi bi-shield-lock me-1"></i> Secure DOLE Internal Links</span>
                <button type="button" class="btn btn-dark btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Password Toggle & Search Filtering Script -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function filterResourceLinks() {
    const query = document.getElementById('resourceSearchInput').value.toLowerCase().trim();
    const items = document.querySelectorAll('.resource-item');
    const noResults = document.getElementById('noSearchResults');
    let visibleCount = 0;

    // Split the query into individual words so multi-word or any word searches work
    const searchWords = query.split(/\s+/).filter(word => word.length > 0);

    items.forEach(item => {
        // Automatically reads all inner text (titles, descriptions, etc.) inside the card
        const itemText = item.innerText.toLowerCase();

        if (searchWords.length === 0) {
            item.style.display = '';
            visibleCount++;
        } else {
            // Check if every typed search term exists somewhere inside the item's text
            const matchesAll = searchWords.every(word => itemText.includes(word));

            if (matchesAll) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        }
    });

    if (visibleCount === 0) {
        noResults.classList.remove('d-none');
    } else {
        noResults.classList.add('d-none');
    }
}
</script>

</body>
</html>