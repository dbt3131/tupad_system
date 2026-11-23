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

        /* Redesigned Online Counter */
        .online-counter-box {
            background: linear-gradient(135deg, #f4fbf7 0%, #ebf7f0 100%);
            border: 1px solid #ccebd8;
            border-radius: 14px;
            padding: 10px 14px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(25, 135, 84, 0.06);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .online-counter-box:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.12);
        }

        .online-counter-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e4631;
        }

        .online-pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background-color: #198754;
            box-shadow: 0 0 0 rgba(25, 135, 84, 0.55);
            animation: pulseGreen 2s infinite;
            flex-shrink: 0;
        }

        @keyframes pulseGreen {
            0% {
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.55);
            }
            70% {
                box-shadow: 0 0 0 7px rgba(25, 135, 84, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
            }
        }

        .online-counter-pill {
            background-color: #198754;
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 999px;
            letter-spacing: 0.2px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Popover styling for online users list */
        .popover.online-users-popover {
            border-radius: 12px;
            border: 1px solid #ccebd8;
            box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.15);
            max-width: 260px;
        }

        .popover.online-users-popover .popover-header {
            background-color: #f4fbf7;
            border-bottom: 1px solid #ccebd8;
            color: #1e4631;
            font-weight: 700;
            font-size: 0.8rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .popover.online-users-popover .popover-body {
            max-height: 200px;
            overflow-y: auto;
            font-size: 0.82rem;
            padding: 0.6rem 0.85rem;
        }

        .online-user-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .online-user-list li {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 3px 0;
            color: #212529;
        }

        .online-user-list li .bi-circle-fill {
            color: #198754;
            font-size: 0.45rem;
            flex-shrink: 0;
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

        /* Compact Modern Modal Enhancements */
        .modal-modern .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0.85rem 1.25rem;
        }

        .modal-modern .modal-body {
            max-height: 60vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .modal-modern .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-modern .modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-modern .modal-body::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 999px;
        }

        .modal-modern .modal-body::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        .resource-card-item {
            border: 1px solid #eef2f7;
            border-radius: 10px;
            transition: all 0.2s ease-in-out;
            background: #ffffff;
        }

        .resource-card-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.35rem 0.75rem rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
            background-color: #fafbfc;
        }

        .resource-card-title {
            font-size: 0.88rem;
            line-height: 1.25;
        }

        .resource-card-desc {
            font-size: 0.76rem;
            line-height: 1.3;
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
                    <!-- Redesigned Online Users Counter with Hover Popover -->
                    <?php
                        $count = (int) ($online_count ?? 0);
                        $users_list = (!empty($online_users) && is_array($online_users)) ? $online_users : [];

                        if (!empty($users_list)) {
                            $items_html = '<ul class="online-user-list">';
                            foreach ($users_list as $u) {
                                $fname = trim($u->reg_fname ?? '');
                                $lname = trim($u->reg_lname ?? '');
                                $full_name = trim($fname . ' ' . $lname);
                                if ($full_name === '') {
                                    $full_name = $u->email ?? 'Active User';
                                }
                                $items_html .= '<li><i class="bi bi-circle-fill"></i><span>' . html_escape($full_name) . '</span></li>';
                            }
                            $items_html .= '</ul>';
                        } else {
                            $items_html = '<span class="text-muted">No users currently online.</span>';
                        }
                    ?>
                    <div
                        class="online-counter-box"
                        id="onlineUsersTrigger"
                        data-bs-toggle="popover"
                        data-bs-trigger="hover focus"
                        data-bs-placement="top"
                        data-bs-html="true"
                        data-bs-custom-class="online-users-popover"
                        title="Currently Online (<?= $count; ?>)"
                        data-bs-content="<?= html_escape($items_html); ?>"
                    >
                        <div class="online-counter-label">
                            <span class="online-pulse-dot"></span>
                            <i class="bi bi-people-fill"></i>
                            <span>Active Users</span>
                        </div>
                        <span class="online-counter-pill">
                            <?= $count; ?> Online
                        </span>
                    </div>

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

<!-- Consolidated Links Modal (Compact, Modern, Scrollable, with Search Filter) -->
<div class="modal fade modal-modern" id="consolidatedLinksModal" tabindex="-1" aria-labelledby="consolidatedLinksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-dark d-flex align-items-center mb-0" id="consolidatedLinksModalLabel">
                    <div class="bg-primary bg-opacity-10 rounded-3 text-primary me-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                        <i class="bi bi-link-45deg fs-5"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.95rem;">Consolidated Google Sheets & Drive Links</div>
                        <div class="text-muted fw-normal" style="font-size: 0.74rem;">Centralized tracking databases and documentation portals</div>
                    </div>
                </h6>
                <button type="button" class="btn-close shadow-none" style="font-size: 0.8rem;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-3 bg-light">
                <!-- Search Input Bar -->
                <div class="mb-2">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                        <span class="input-group-text bg-white border-end-0 text-muted py-1 px-2"><i class="bi bi-search" style="font-size: 0.82rem;"></i></span>
                        <input type="text" id="resourceSearchInput" class="form-control border-start-0 ps-1 shadow-none" placeholder="Search any keyword, title, description..." onkeyup="filterResourceLinks()" style="height: 36px; font-size: 0.85rem;">
                    </div>
                </div>

                <!-- Links Container -->
                <div class="row g-2" id="resourceLinksContainer">
                    
                    <!-- Item 1 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1B2vcXZonL0H2vGp8Ajjks00UEiTR9bwcXOQeu3-w2M8/edit?gid=1498179126#gid=1498179126" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded-2 text-success me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-file-earmark-spreadsheet fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">2026 TUPAD Monitoring Sheet</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung sheet kung saan nilalagay mga newly coordinated ADLs.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>
                    
                    <!-- Item 2 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1uc9z9t3DH6IanGlyau1lQ6_tM99h2WuvRQiKQ3PQcKc/edit?gid=522341313#gid=522341313" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded-2 text-success me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-file-earmark-spreadsheet fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">2026 TUPAD Implementation Status</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung pinapasupply na report ni ms. CJ</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>

                    <!-- Item 4 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1eNmQ2AYxFAUq3T2Cq-pXA6xJxEjReyoH6pF24m9_R0U/edit?gid=0#gid=0" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded-2 text-success me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-file-earmark-spreadsheet fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">2026 Tupad Coordinators Directory</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung sheet kung saan nandito lahat ng mga TCs.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>

                    <!-- Item 5 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1QsAzwlI8aqMTaazxCRuyslHHr0zCx1OdvaHlPKcP_to/edit?gid=1617754366#gid=1617754366" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded-2 text-success me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-file-earmark-spreadsheet fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">2026 Tupad Payout Schedules</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung sheet kung saan makikita mga payrolls and schedules.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>

                    <!-- Item 3 -->
                    <div class="col-12 resource-item">
                        <a href="https://docs.google.com/spreadsheets/d/1XrY8_lzSW3S8fN9GTey90EhgsFdEHXkRa2THWaTdDkA/edit?gid=195401770#gid=195401770" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded-2 text-success me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-file-earmark-spreadsheet fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">TUPAD Flagship Innovations</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung sheet kung saan makikita per Region Status of Implementation.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>

                    <!-- Item 6 -->
                    <div class="col-12 resource-item">
                        <a href="https://www.canva.com/design/DAHbIqDdwSg/VasY8dayGof6ZpwimNLovg/edit" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-2 text-primary me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-palette fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">TUPAD Flagship Layout</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung canva for 7 TUPAD Flagship</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>

                    <!-- Item 7 -->
                    <div class="col-12 resource-item">
                        <a href="https://drive.google.com/drive/folders/1f2r5i0ZmO97aWB3PGnLb9HbEtHn9-WfJ?usp=drive_link" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-2 text-warning me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-folder-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">TUPAD Liquidation Drive</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung drive na inaaccess ng COA.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>

                    <!-- Item 8 -->
                    <div class="col-12 resource-item">
                        <a href="https://drive.google.com/drive/folders/1kDzDmWbqDRtRcTycbeyLoMDGf639Wvnq?usp=drive_link" target="_blank" class="resource-card-item py-2 px-3 text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-2 text-warning me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-folder-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark resource-card-title">TUPAD Guidelines</div>
                                    <p class="mb-0 text-muted resource-card-desc">Heto po yung drive kung saan nandito lahat ng mga guidelines ng TUPAD.</p>
                                </div>
                            </div>
                            <div class="text-muted ms-2"><i class="bi bi-chevron-right small"></i></div>
                        </a>
                    </div>
                </div>

                <!-- No Results State (Hidden by default) -->
                <div id="noSearchResults" class="text-center py-3 d-none">
                    <i class="bi bi-search text-muted fs-4 mb-1 d-block"></i>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">No matching resources found.</p>
                </div>
            </div>

            <div class="modal-footer bg-white border-top py-2 px-3">
                <span class="text-muted me-auto" style="font-size: 0.76rem;"><i class="bi bi-shield-lock me-1"></i> Secure DOLE Internal Links</span>
                <button type="button" class="btn btn-dark btn-sm rounded-pill px-3" style="font-size: 0.78rem;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Popover Initialization, Password Toggle & Search Filtering Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const triggerEl = document.getElementById('onlineUsersTrigger');
    if (triggerEl && typeof bootstrap !== 'undefined') {
        new bootstrap.Popover(triggerEl);
    }
});

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