<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - PRISM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body { min-height: 100vh; background: linear-gradient(135deg, #020a16, #090411); display: flex; align-items: center; justify-content: center; }
        .card { border: none; border-radius: 20px; overflow: hidden; }
        .form-control { height: 48px; border-radius: 10px; }
        .input-group-text { border-radius: 10px 0 0 10px; background: #f8f9fa; }
        .input-group .form-control { border-radius: 0 10px 10px 0; }
        .btn-custom { height: 48px; border-radius: 10px; font-weight: 600; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
            <div class="card shadow-lg p-4">
                <h4 class="fw-bold mb-3 text-center">Forgot Password</h4>
                <p class="text-muted small text-center mb-4">Enter your registered email address and we'll send you instructions to reset your password.</p>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= html_escape($this->session->flashdata('success')); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                 <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= html_escape($this->session->flashdata('error')); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?= validation_errors('<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>', '</div>'); ?>

                <form method="post" action="<?= site_url('auth/forgot_password'); ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-custom w-100 mb-3">
                        <i class="bi bi-send me-2"></i>Send Reset Link
                    </button>
                    <div class="text-center">
                        <a href="<?= site_url('auth/login'); ?>" class="text-decoration-none small fw-semibold">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>