<!-- Top Navbar -->
<style>
html {
    scrollbar-gutter: stable;
}

@supports not (scrollbar-gutter: stable) {
    html {
        overflow-y: scroll;
    }
}

  .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.8rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }


#sidebarToggle {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1051;
}
</style>

<nav class="top-navbar d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-light border shadow-sm p-2" id="sidebarToggle" aria-label="Toggle Sidebar">
      <i class="bi bi-list fs-5"></i>
    </button>
    <img src="<?php echo base_url('assets/images/dolelogo.png'); ?>" alt="DOLE Logo" width="42" height="42" class="d-inline-block align-text-top"><span class="fw-semibold text-secondary d-none d-sm-inline text-truncate" style="max-width: 250px;"> eTUPAD-PRISM | DOLE R3</span>
  </div>

  <!-- Right Navbar Elements -->
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-light position-relative rounded-circle p-2" aria-label="Notifications">
      <i class="bi bi-bell fs-5"></i>
      <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
        <span class="visually-hidden">New alerts</span>
      </span>
    </button>

    <div class="vr mx-1"></div>

    <div class="dropdown">
      <?php 
        $session_all = $this->session->all_userdata();

        $candidates = [
            $this->session->userdata('first_name'),
            $this->session->userdata('fname'),
            $this->session->userdata('user_name'),
            $this->session->userdata('full_name'),
            $this->session->userdata('username'),
            $this->session->userdata('name'),
            $user_name ?? null
        ];

        foreach ($session_all as $key => $val) {
            $candidates[] = $val;
        }

        $display_name = 'User';
        foreach ($candidates as $val) {
            if (!empty($val) && is_string($val)) {
                $trimmed = trim($val);
                if (!is_numeric($trimmed) && preg_match('/[a-zA-Z]/', $trimmed)) {
                    $display_name = $trimmed;
                    break;
                }
            }
        }
      ?>

      <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
          <?= strtoupper(substr($display_name, 0, 1)); ?>
        </div>
        <span class="fw-medium d-none d-md-inline"><?= html_escape($display_name); ?></span>
      </a>

      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout'); ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
