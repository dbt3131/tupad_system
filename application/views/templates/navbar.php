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
    <img src="<?php echo base_url('assets/images/dolelogo.png'); ?>" alt="DOLE Logo" width="42" height="42" class="d-inline-block align-text-top">
    <span class="fw-semibold text-secondary d-none d-sm-inline text-truncate" style="max-width: 250px;"> eTUPAD-PRISM | DOLE R3</span>
  </div>

  <!-- Right Navbar Elements -->
  <div class="d-flex align-items-center gap-3">
    
    <!-- Online Users Dropdown -->
    <div class="dropdown">
      <?php 
        // Fetch online users if not already passed globally
        $CI =& get_instance();
        $CI->load->model('User_model');
        $online_users = $CI->User_model->get_online_users();
        $online_count = count($online_users);
      ?>
      <a href="#" class="btn btn-light border position-relative d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-circle-fill text-success fs-6"></i>
        <span class="d-none d-md-inline">Online</span>
        <span class="badge bg-success rounded-pill"><?= $online_count; ?></span>
      </a>

      <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 220px; max-height: 300px; overflow-y: auto;">
        <li><h6 class="dropdown-header">Active Users (<?= $online_count; ?>)</h6></li>
        <li><hr class="dropdown-divider"></li>
        <?php if (!empty($online_users)): ?>
          <?php foreach ($online_users as $online_user): ?>
            <li>
              <span class="dropdown-item-text d-flex align-items-center gap-2 py-2">
                <span class="p-1 bg-success border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                <span class="text-truncate" style="max-width: 180px;" title="<?= html_escape($online_user->reg_fname . ' ' . $online_user->reg_lname); ?>">
                  <?= html_escape($online_user->reg_fname . ' ' . $online_user->reg_lname); ?>
                </span>
              </span>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li><span class="dropdown-item-text text-muted text-center">No other online users</span></li>
        <?php endif; ?>
      </ul>
    </div>

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
        <li><a class="dropdown-item" href="<?= site_url('users/user_prof'); ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout'); ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>