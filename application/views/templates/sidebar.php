<!-- ================= SIDEBAR ================= -->
<?php 
$current_controller = strtolower($this->uri->segment(1)); 
$current_method     = strtolower($this->uri->segment(2)); 

$is_tupad_active   = ($current_controller === 'tupad');
$is_alloc_active   = in_array($current_controller, ['tupad_allocations', 'tupad_monitoring']);
$is_payroll_active = in_array($current_controller, ['tupad_payrolls']);
$is_adl_active     = in_array($current_controller, ['ADL']);
$is_config_active  = in_array($current_controller, ['ADL']); // (Kept original variable mapping)
$is_report_active  = in_array($current_controller, ['ADL', 'tupad_report', 'tupad_report']);
?>

<style>
    :root {
      --sidebar-width: 270px;
      --sidebar-bg: #090d16;
      --sidebar-hover: rgba(255, 255, 255, 0.06);
      --primary-light: #3b82f6;
      --text-muted: #94a3b8;
      --card-border: #1e293b;
    }

    /* --- MODERNIZED SIDEBAR STYLES --- */
    #sidebar {
      width: var(--sidebar-width);
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background-color: var(--sidebar-bg);
      color: #ffffff;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 1020;
      display: flex;
      flex-direction: column;
      border-right: 1px solid var(--card-border);
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
    }

    #sidebar.collapsed {
      margin-left: calc(-1 * var(--sidebar-width));
    }

    .sidebar-brand {
      padding: 1.5rem;
      font-weight: 700;
      font-size: 1.1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .sidebar-menu {
      padding: 1rem 0.75rem;
      list-style: none;
      margin: 0;
      flex-grow: 1;
      overflow-y: auto;
    }

    .sidebar-menu::-webkit-scrollbar {
      width: 5px;
    }
    .sidebar-menu::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
    }

    .sidebar-menu .nav-item {
      margin-bottom: 0.25rem;
    }

    .sidebar-menu .nav-link {
      color: var(--text-muted);
      padding: 0.75rem 1rem;
      display: flex;
      align-items: center;
      gap: 0.85rem;
      font-weight: 400 !important;
      font-size: 0.9rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      text-decoration: none;
    }

    .sidebar-menu .nav-link i {
      font-size: 1.1rem;
      transition: transform 0.2s;
    }

    .sidebar-menu .nav-link:hover {
      color: #ffffff;
      background-color: var(--sidebar-hover);
    }

    .sidebar-menu .nav-link:not(.collapsed) {
      color: #ffffff;
      background-color: rgba(59, 130, 246, 0.1);
    }

    .sidebar-menu .nav-link .dropdown-chevron {
      transition: transform 0.3s ease;
      font-size: 0.75rem !important;
    }

    .sidebar-menu .nav-link:not(.collapsed) .dropdown-chevron {
      transform: rotate(180deg);
    }

    /* Submenu Modernization */
    .sidebar-menu .submenu {
      padding-left: 1.5rem;
      padding-top: 0.25rem;
      padding-bottom: 0.25rem;
      list-style: none;
      position: relative;
    }

    .sidebar-menu .submenu::before {
      content: '';
      position: absolute;
      left: 1.35rem;
      top: 0;
      bottom: 0;
      width: 1px;
      background-color: rgba(255, 255, 255, 0.08);
    }

    .sidebar-menu .submenu .nav-sub-link {
      display: flex;
      align-items: center;
      padding: 0.6rem 0.85rem;
      font-size: 0.85rem;
      color: var(--text-muted);
      text-decoration: none;
      border-radius: 6px;
      transition: all 0.2s;
      font-weight: 400 !important;
    }

    .sidebar-menu .submenu .nav-sub-link i {
      font-size: 0.4rem; 
      margin-right: 10px;
      color: var(--text-muted);
      transition: color 0.2s;
    }

    .sidebar-menu .submenu .nav-sub-link:hover,
    .sidebar-menu .submenu .nav-sub-link.active {
      color: #ffffff;
      background-color: var(--sidebar-hover);
      font-weight: 400 !important;
    }

    .sidebar-menu .submenu .nav-sub-link.active i {
      color: var(--primary-light);
    }

    /* Keep existing body/main content setups responsive behavior intact */
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
      overflow-x: hidden;
    }

    #main-content {
      margin-left: var(--sidebar-width);
      transition: all 0.3s ease-in-out;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    #main-content.expanded {
      margin-left: 0;
    }

    @media (max-width: 991.98px) {
      #sidebar {
        margin-left: calc(-1 * var(--sidebar-width));
      }
      #sidebar.show-mobile {
        margin-left: 0;
      }
      #main-content {
        margin-left: 0;
      }
    }
</style>

<aside id="sidebar">
  <div class="sidebar-brand">
    <br>
  </div>

  <ul class="sidebar-menu">
    <!-- Dashboard -->
    <li class="nav-item">
      <a href="<?= site_url('dashboard/index'); ?>" 
         class="nav-link <?= ($current_controller === 'dashboard' || $current_controller === '') ? 'active' : ''; ?>">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Activities -->
    <li class="nav-item">
      <a href="<?= site_url('activity/activity_trail'); ?>" 
         class="nav-link <?= ($current_controller === 'activity') ? 'active' : ''; ?>">
        <i class="bi bi-clock-history"></i>
        <span>Activities</span>
      </a>
    </li>

    <!-- TUPAD Workers Dropdown -->
    <li class="nav-item">
      <a href="#tupadSubmenu" 
         class="nav-link <?= $is_tupad_active ? '' : 'collapsed'; ?>" 
         data-bs-toggle="collapse" 
         aria-expanded="<?= $is_tupad_active ? 'true' : 'false'; ?>">
        <i class="bi bi-people-fill"></i>
        <span>TUPAD Workers</span>
        <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
      </a>
      
      <ul class="collapse submenu <?= $is_tupad_active ? 'show' : ''; ?>" id="tupadSubmenu">
        <li>
          <a href="<?= site_url('tupad/view_files'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'view_files') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>Uploaded Files</span>
          </a>
        </li>
        <li>
          <a href="<?= site_url('tupad/view_files_official'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'view_files_official') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>Data Management</span>
          </a>
        </li>
        <li>
          <a href="<?= site_url('tupad/duplicity_check'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'duplicity_check') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>Duplicity Checking</span>
          </a>
        </li>
        <li>
          <a href="<?= site_url('tupad/gsis_letter'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'gsis_letter') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>GSIS Letters</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- TUPAD ADL Dropdown -->
    <li class="nav-item">
      <a href="#ADLsubmenu" 
         class="nav-link <?= $is_adl_active ? '' : 'collapsed'; ?>" 
         data-bs-toggle="collapse" 
         aria-expanded="<?= $is_adl_active ? 'true' : 'false'; ?>">
        <i class="bi bi-list-check"></i>
        <span>Tupad ADL</span>
        <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
      </a>
      
      <ul class="collapse submenu <?= $is_adl_active ? 'show' : ''; ?>" id="ADLsubmenu">
        <li>
          <a href="<?= site_url('ADL/ADL_encode'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'ADL' && $current_method === 'adl_encode') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>Encode ADLs</span>
          </a>
        </li>
        <li>
          <a href="<?= site_url('ADL/implementation_encode'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'ADL' && $current_method === 'implementation_encode') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>Encode Implementation</span>
          </a>
        </li>
        <li>
          <a href="<?= site_url('ADL/transaction_report'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'ADL' && $current_method === 'transaction_report') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>List of Implementation</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- TUPAD Config Dropdown -->
    <li class="nav-item">
      <a href="#proponentSubMenu" 
         class="nav-link <?= $is_config_active ? '' : 'collapsed'; ?>" 
         data-bs-toggle="collapse" 
         aria-expanded="<?= $is_config_active ? 'true' : 'false'; ?>">
        <i class="bi bi-gear"></i>
        <span>TUPAD Config</span>
        <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
      </a>
      
      <ul class="collapse submenu <?= $is_config_active ? 'show' : ''; ?>" id="proponentSubMenu">
        <li>
          <a href="<?= site_url('ADL/proponent_encode'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'ADL' && $current_method === 'proponent_encode') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>Encode Proponent</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Reports Dropdown -->
    <li class="nav-item">
      <a href="#reportMenu" 
         class="nav-link <?= $is_report_active ? '' : 'collapsed'; ?>" 
         data-bs-toggle="collapse" 
         aria-expanded="<?= $is_report_active ? 'true' : 'false'; ?>">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Reports</span>
        <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
      </a>
      
      <ul class="collapse submenu <?= $is_report_active ? 'show' : ''; ?>" id="reportMenu">
        <li>
          <a href="<?= site_url('ADL/ADL_report'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'ADL' && $current_method === 'adl_report') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>ADL Distribution Report</span>
          </a>
        </li>
        <li>
          <a href="<?= site_url('Tupad_Report/coa_tupad_report_page'); ?>" 
             class="nav-sub-link <?= (strtolower($current_controller) === 'tupad_report' && $current_method === 'coa_tupad_report_page') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>ADL - COA Quarterly Report</span>
          </a>
        </li>
          <li>
          <a href="<?= site_url('Tupad_Report/tupad_implementation_status_report'); ?>" 
             class="nav-sub-link <?= (strtolower($current_controller) === 'tupad_report' && $current_method === 'tupad_implementation_status_report') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>ADL - TUPAD Implementation Status</span>
          </a>
        </li>
        <li>
          <a href="<?= site_url('tupad_report/tupad_summ_report'); ?>" 
             class="nav-sub-link <?= (strtolower($current_controller) === 'tupad_report' && $current_method === 'tupad_summ_report') ? 'active' : ''; ?>">
            <i class="bi bi-circle-fill"></i>
            <span>Benefs Summary Report</span>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</aside>

<script>
    let idleTime = 0;
    const idleTimeLimit = 5; // Time limit in minutes (e.g., 5 minutes)

    // Increment the idle timer counter every minute (or 5 mins as originally set)
    const idleInterval = setInterval(timerIncrement, 300000); 

    window.onload = resetIdleTimer;
    window.onmousemove = resetIdleTimer;
    window.onmousedown = resetIdleTimer;
    window.onclick = resetIdleTimer;
    window.onkeypress = resetIdleTimer;
    window.addEventListener('scroll', resetIdleTimer, true);

    function timerIncrement() {
        idleTime++;
        if (idleTime >= idleTimeLimit) {
            window.location.href = "<?= site_url('auth/logout'); ?>";
        }
    }

    function resetIdleTimer() {
        idleTime = 0;
    }
</script>