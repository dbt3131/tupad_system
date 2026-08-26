<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - CI3 CRUD System</title>

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
            max-height:110px;
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
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?= set_value('email'); ?>" required autofocus>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary btn-login w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>

                    <!-- Register Link -->
                    <p class="text-center mt-4 mb-0">
                        Don't have an account? 
                        <a href="<?= site_url('auth/register'); ?>" class="register-link">Create an account</a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center footer-text mt-3">
                &copy; <?= date('Y'); ?> eTUPAD-PRISM
            </p>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Password Toggle Script -->
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
</script>

</body>
</html>