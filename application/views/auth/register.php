<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - CI3 CRUD System</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --primary-color: #2563eb;
      --primary-hover: #1d4ed8;
      --bg-gradient-start: #0f172a;
      --bg-gradient-end: #1e293b;
      --card-bg: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --input-bg: #f8fafc;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(12px, 3vw, 40px) clamp(8px, 2vw, 20px);
      color: var(--text-main);
    }

    .register-card {
      border: none;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
      background: var(--card-bg);
      width: 100%;
    }

    /* Fixed header layout to prevent logo clipping/disappearing on zoom */
    .register-card-header-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      width: 100%;
      padding: clamp(20px, 4vw, 36px) clamp(16px, 4vw, 36px) clamp(12px, 3vw, 20px);
      border-bottom: 1px solid var(--border-color);
      background: #ffffff;
    }

    .logo-badge {
      position: relative;
      width: clamp(70px, 12vw, 90px);
      height: clamp(70px, 12vw, 90px);
      margin: 0 auto 12px;
      border-radius: 50%;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
      border: 3px solid #f1f5f9;
      flex-shrink: 0;
    }

    .rounded-logo {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .system-logo {
      max-height: clamp(36px, 6vw, 50px);
      width: auto;
      max-width: 100%;
      object-fit: contain;
      margin-bottom: 8px;
    }

    .register-body {
      padding: clamp(18px, 4vw, 36px);
    }

    .form-label {
      font-size: 0.85rem;
      letter-spacing: 0.01em;
      color: #334155;
    }

    .input-group {
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid var(--border-color);
      transition: all 0.2s ease;
    }

    .input-group:focus-within {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .input-group-text {
      background-color: var(--input-bg);
      border: none;
      color: var(--text-muted);
      padding-left: 14px;
      padding-right: 10px;
    }

    .form-control, .form-select {
      border: none;
      height: 48px;
      background-color: var(--input-bg);
      font-size: 0.95rem;
      color: var(--text-main);
    }

    .form-control:focus, .form-select:focus {
      background-color: #ffffff;
      box-shadow: none;
    }

    .btn-toggle-password {
      border: none;
      background-color: var(--input-bg);
      color: var(--text-muted);
      padding: 0 16px;
      transition: color 0.2s;
    }

    .btn-toggle-password:hover {
      color: var(--primary-color);
    }

    .btn-register {
      height: 50px;
      border-radius: 10px;
      font-weight: 600;
      background-color: var(--primary-color);
      border: none;
      font-size: 1rem;
      transition: all 0.2s ease;
    }

    .btn-register:hover {
      background-color: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
    }

    .login-link {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s;
    }

    .login-link:hover {
      color: var(--primary-hover);
      text-decoration: underline;
    }

    .footer-text {
      color: #94a3b8;
      font-size: 0.85rem;
    }

    @media (max-width: 575.98px) {
      .register-card {
        border-radius: 12px;
      }
      .form-control, .form-select {
        font-size: 16px;
      }
    }
  </style>
</head>

<body>
  <div class="container-fluid px-2 px-sm-3">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-11 col-md-10 col-lg-8 col-xl-7">
        
        <div class="card register-card">
          <!-- Header -->
          <div class="register-card-header-wrapper">
            <div class="logo-badge">
              <img src="<?= base_url('assets/images/dolelogo.png'); ?>" alt="DOLE Logo" class="rounded-logo">
            </div>
            <div class="text-center w-100">
              <div class="prism-logo-container mb-1 mt-1">
                <img src="<?= base_url('assets/images/Prism_Logo.png'); ?>" alt="Prism Logo" class="system-logo">
              </div>
              <h4 class="fw-bold mb-1 fs-5 fs-sm-4">Create Your Account</h4>
              <p class="text-muted small mb-0">Fill in the details below to register into PRISM System</p>
            </div>
          </div>

          <!-- Registration Form Body -->
          <div class="register-body">
            <!-- CI3 Validation Errors -->
            <?= validation_errors(
              '<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert"><i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><div>',
              '</div><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
            ); ?>

            <form method="post" action="<?= site_url('auth/register'); ?>" onsubmit="return validatePasswords();">
              
              <!-- SECTION 1: Personal Details -->
              <div class="row g-2 g-sm-3 mb-3">
                <div class="col-12">
                  <label for="reg_empno" class="form-label fw-semibold">Employee No.</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                    <input type="text" id="reg_empno" name="reg_empno" class="form-control" oninput="this.value = this.value.replace(/[^0-9-]/g, '')" placeholder="e.g. 2026-01234" value="<?= set_value('reg_empno'); ?>" required autofocus>
                  </div>
                </div>

                <div class="col-12 col-sm-6">
                  <label for="reg_fname" class="form-label fw-semibold">First Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" id="reg_fname" name="reg_fname" class="form-control" oninput="this.value = this.value.toUpperCase();" placeholder="First Name" value="<?= set_value('reg_fname'); ?>" required>
                  </div>
                </div>

                <div class="col-12 col-sm-6">
                  <label for="reg_mname" class="form-label fw-semibold">Middle Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" id="reg_mname" name="reg_mname" class="form-control" oninput="this.value = this.value.toUpperCase();" placeholder="Middle Name" value="<?= set_value('reg_mname'); ?>">
                  </div>
                </div>

                <div class="col-12 col-sm-8">
                  <label for="reg_lname" class="form-label fw-semibold">Last Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" id="reg_lname" name="reg_lname" class="form-control" oninput="this.value = this.value.toUpperCase();" placeholder="Last Name" value="<?= set_value('reg_lname'); ?>" required>
                  </div>
                </div>

                <div class="col-12 col-sm-4">
                  <label for="reg_extname" class="form-label fw-semibold">Ext. Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" id="reg_extname" name="reg_extname" class="form-control" oninput="this.value = this.value.toUpperCase();" placeholder="Jr., III" value="<?= set_value('reg_extname'); ?>">
                  </div>
                </div>
              </div>

              <hr class="my-3 my-sm-4 text-border opacity-50">

              <!-- SECTION 2: Organization Details -->
              <div class="row g-2 g-sm-3 mb-3">
                <div class="col-12">
                  <label for="position_id" class="form-label fw-semibold">Job Position</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                    <select id="position_id" name="position_id" class="form-select" required>
                      <option value="">-- Select Position --</option>
                      <?php if (!empty($positions)): ?>
                        <?php foreach ($positions as $pos): ?>
                          <option value="<?= $pos['position_id']; ?>" <?= set_select('position_id', $pos['position_id']); ?>>
                            <?= $pos['position_description']; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>

                <div class="col-12 col-sm-6">
                  <label for="office_id" class="form-label fw-semibold">Office</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <select id="office_id" name="office_id" class="form-select" required>
                      <option value="">-- Select Office --</option>
                      <?php if (!empty($office)): ?>
                        <?php foreach ($office as $off): ?>
                          <option value="<?= $off['office_id']; ?>" <?= set_select('office_id', $off['office_id']); ?>>
                            <?= $off['office_description']; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>

                <div class="col-12 col-sm-6">
                  <label for="division_id" class="form-label fw-semibold">Division</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                    <select id="division_id" name="division_id" class="form-select" required>
                      <option value="">-- Select Division --</option>
                      <?php if (!empty($division)): ?>
                        <?php foreach ($division as $div): ?>
                          <option value="<?= $div['division_id']; ?>" <?= set_select('division_id', $div['division_id']); ?>>
                            <?= $div['division_description']; ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
              </div>

              <hr class="my-3 my-sm-4 text-border opacity-50">

              <!-- SECTION 3: Account Credentials -->
              <div class="row g-2 g-sm-3 mb-4">
                <div class="col-12">
                  <label for="email" class="form-label fw-semibold">Email Address</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@domain.gov.ph" value="<?= set_value('email'); ?>" required>
                  </div>
                </div>

                <div class="col-12 col-sm-6">
                  <label for="password" class="form-label fw-semibold">Password</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min. 6 characters" minlength="6" required>
                    <button type="button" class="btn-toggle-password" onclick="togglePassword('password', 'passwordIcon')">
                      <i class="bi bi-eye" id="passwordIcon"></i>
                    </button>
                  </div>
                </div>

                <div class="col-12 col-sm-6">
                  <label for="password_confirm" class="form-label fw-semibold">Confirm Password</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Re-enter password" minlength="6" required>
                    <button type="button" class="btn-toggle-password" onclick="togglePassword('password_confirm', 'confirmPasswordIcon')">
                      <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                    </button>
                  </div>
                </div>
                <div class="col-12 mt-1">
                  <div id="passwordMessage" class="form-text small"></div>
                </div>
              </div>

              <!-- Submit Button -->
              <button type="submit" class="btn btn-primary btn-register w-100 mb-3">
                <i class="bi bi-person-check-fill me-2"></i> Register Account
              </button>
            </form>

            <!-- Login Redirect -->
            <p class="text-center text-muted mb-0 small fs-6">
              Already registered?
              <a href="<?= site_url('auth/login'); ?>" class="login-link ms-1">Sign In</a>
            </p>
          </div>
        </div>

        <!-- Footer -->
        <p class="text-center footer-text mt-3 mt-sm-4 mb-0">
          &copy; <?= date('Y'); ?> eTUPAD-PRISM. All rights reserved.
        </p>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Toggle Password Visibility
    function togglePassword(inputId, iconId) {
      const passwordInput = document.getElementById(inputId);
      const icon = document.getElementById(iconId);

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.replace("bi-eye", "bi-eye-slash");
      } else {
        passwordInput.type = "password";
        icon.classList.replace("bi-eye-slash", "bi-eye");
      }
    }

    // Dynamic Password Match Validation
    function validatePasswords() {
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("password_confirm").value;
      const message = document.getElementById("passwordMessage");

      if (confirmPassword.length > 0 && password !== confirmPassword) {
        message.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i> Passwords do not match.</span>';
        return false;
      } else if (confirmPassword.length > 0 && password === confirmPassword) {
        message.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i> Passwords match.</span>';
        return true;
      }
      return true;
    }

    document.getElementById("password_confirm").addEventListener("keyup", validatePasswords);
    document.getElementById("password").addEventListener("keyup", validatePasswords);
  </script>
</body>
</html>