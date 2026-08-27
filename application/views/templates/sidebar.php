<!-- ================= SIDEBAR ================= -->
<?php 
$current_controller = strtolower($this->uri->segment(1)); 
$current_method     = strtolower($this->uri->segment(2)); 

$is_tupad_active  = ($current_controller === 'tupad');
// Include tupad_monitoring as active for this menu section
$is_alloc_active  = in_array($current_controller, ['tupad_allocations', 'tupad_monitoring']);
$is_payroll_active  = in_array($current_controller, ['tupad_payrolls']);
?>

<style>
.sidebar-menu .submenu {
  padding-left: 20px;
  list-style: none;
}

.sidebar-menu .submenu .nav-sub-link {
  display: flex;
  align-items: center;
  padding: 8px 15px;
  font-size: 0.9rem;
  color: #ccc;
  text-decoration: none;
}

.sidebar-menu .submenu .nav-sub-link i {
  font-size: 0.5rem; /* smaller bullet icon */
  margin-right: 10px;
}

.sidebar-menu .submenu .nav-sub-link.active,
.sidebar-menu .submenu .nav-sub-link:hover {
  color: #fff;
  font-weight: bold;
}  

    :root {
      --sidebar-width: 260px;
      --primary-color: #1e3a8a; /* DOLE Dark Blue */
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
    }

    /* --- SIDEBAR STYLES --- */
    #sidebar {
      width: var(--sidebar-width);
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #0f172a;
      color: #ffffff;
      transition: all 0.3s ease-in-out;
      z-index: 1020;
      display: flex;
      flex-direction: column;
    }

    #sidebar.collapsed {
      margin-left: calc(-1 * var(--sidebar-width));
    }

    .sidebar-brand {
      padding: 1.25rem 1.5rem;
      font-weight: 700;
      font-size: 1.1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .sidebar-menu {
      padding: 1rem 0;
      list-style: none;
      margin: 0;
      flex-grow: 1;
    }

    .sidebar-menu .nav-link {
      color: #94a3b8;
      padding: 0.8rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.85rem;
      font-weight: 500;
      font-size: 0.925rem;
      transition: all 0.2s;
    }

    .sidebar-menu .nav-link:hover,
    .sidebar-menu .nav-link.active {
      color: #ffffff;
      background-color: rgba(255, 255, 255, 0.08);
      border-left: 4px solid var(--primary-light);
    }

    /* --- MAIN CONTENT & NAVBAR --- */
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

    .top-navbar {
      background-color: #ffffff;
      border-bottom: 1px solid var(--card-border);
      padding: 0.8rem 1.5rem;
      position: sticky;
      top: 0;
      z-index: 1030;
    }

    /* --- CARDS & WIDGETS --- */
    .stat-card {
      border: 1px solid var(--card-border);
      border-radius: 12px;
      background-color: #ffffff;
      padding: 1.25rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }

    .icon-badge {
      width: 48px;
      height: 48px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
    }

    .content-card {
      background: #ffffff;
      border: 1px solid var(--card-border);
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      margin-bottom: 1.5rem;
    }

    .content-card-header {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid var(--card-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    /* Table adjustments */
    .table th {
      font-weight: 600;
      font-size: 0.825rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      background-color: #f8fafc;
      border-bottom-width: 1px;
    }

    .table td {
      vertical-align: middle;
      font-size: 0.9rem;
    }

    /* Responsive Overlay for Mobile */
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

    /* Force normal font-weight across all sidebar links to stop any bolding */
.sidebar-menu .nav-link,
.sidebar-menu .nav-link.active,
.sidebar-menu .submenu .nav-sub-link,
.sidebar-menu .submenu .nav-sub-link.active {
  font-weight: 400 !important;
}

    

</style>




<aside id="sidebar">
  <div class="sidebar-brand">
  <br>
  </div>

  <ul class="sidebar-menu">
    <!-- Dashboard -->
    <li>
      <a href="<?= site_url('dashboard/index'); ?>" 
         class="nav-link <?= ($current_controller === 'dashboard' || $current_controller === '') ? 'active' : ''; ?>">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    </li>

     <li>
      <a href="<?= site_url('activity/activity_trail'); ?>" 
         class="nav-link <?= ($current_controller === 'activity' || $current_controller === '') ? 'active' : ''; ?>">
        <i class="bi bi-speedometer2"></i>
        <span>Activities</span>
      </a>
    </li>

    <!-- TUPAD Workers Dropdown -->
 <li class="nav-item dropdown">
      <a href="#tupadSubmenu" 
         class="nav-link <?= $is_tupad_active ? '' : 'collapsed'; ?>" 
         data-bs-toggle="collapse" 
         aria-expanded="<?= $is_tupad_active ? 'true' : 'false'; ?>">
        <i class="bi bi-people-fill"></i>
        <span>TUPAD Workers</span>
        <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
      </a>
      
      <!-- Subcategory Menu -->
      <ul class="collapse submenu list-unstyled <?= $is_tupad_active ? 'show' : ''; ?>" id="tupadSubmenu">
        <li>
          <a href="<?= site_url('tupad/view_files'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'view_files') ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i>
            <span>Uploaded Files</span>
          </a>
        </li>
      </ul>

       <ul class="collapse submenu list-unstyled <?= $is_tupad_active ? 'show' : ''; ?>" id="tupadSubmenu">
        <li>
          <a href="<?= site_url('tupad/gsis_letter'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'gsis_letter') ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i>
            <span>GSIS Letters</span>
          </a>
        </li>
      </ul>


      <ul class="collapse submenu list-unstyled <?= $is_tupad_active ? 'show' : ''; ?>" id="tupadSubmenu">
        <li>
          <a href="<?= site_url('tupad/view_files_official'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'view_files') ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i>
            <span>Data Management</span>
          </a>
        </li>
      </ul>

      <ul class="collapse submenu list-unstyled <?= $is_tupad_active ? 'show' : ''; ?>" id="tupadSubmenu">
        <li>
          <a href="<?= site_url('tupad/duplicity_check'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad' && $current_method === 'view_files') ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i>
            <span>Duplicity Checking</span>
          </a>
        </li>
      </ul>
</li>

<!-- TUPAD Workers Dropdown -->
    <li class="nav-item dropdown">
      <a href="#allocSubmenu" 
         class="nav-link <?= $is_alloc_active ? '' : 'collapsed'; ?>" 
         data-bs-toggle="collapse" 
         aria-expanded="<?= $is_alloc_active ? 'true' : 'false'; ?>">
        <i class="bi bi-journal-text"></i>
        <span>Tupad Allocations</span>
        <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
      </a>
      
      <!-- Subcategory Menu -->
      <ul class="collapse submenu list-unstyled <?= $is_alloc_active ? 'show' : ''; ?>" id="allocSubmenu">
        <li>
          <a href="<?= site_url('tupad_allocations/tupad_encode'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad_allocations' &&$current_method === 'tupad_encode') ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i>
            <span>Encode Allocations</span>
          </a>
        </li>
      </ul>
    </li>

     <ul class="collapse submenu list-unstyled <?= $is_alloc_active ? 'show' : ''; ?>" id="allocSubmenu">
        <li>
          <a href="<?= site_url('tupad_allocations/tupad_monitoring'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad_allocations' &&$current_method === 'tupad_monitoring') ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i>
            <span>Tupad Summary Report</span>
          </a>
        </li>
      </ul>
    </li>

    <ul class="collapse submenu list-unstyled <?= $is_alloc_active ? 'show' : ''; ?>" id="allocSubmenu">
        <li>
        <a href="<?= site_url('tupad_monitoring/tupad_monitoring_prov'); ?>" 
         class="nav-sub-link <?= ($current_controller === 'tupad_monitoring' && $current_method === 'tupad_monitoring_prov') ? 'active' : ''; ?>">
        <i class="bi bi-circle"></i>
        <span>Tupad Monitoring Report</span>
      </a>
        </li>
      </ul>
    </li>

<!-- TUPAD Workers Dropdown -->
    <li class="nav-item dropdown">
      <a href="#payrollSubmenu" 
         class="nav-link <?= $is_payroll_active ? '' : 'collapsed'; ?>" 
         data-bs-toggle="collapse" 
         aria-expanded="<?= $is_payroll_active ? 'true' : 'false'; ?>">
        <i class="bi bi-journal-text"></i>
        <span>Tupad Payrolls</span>
        <i class="bi bi-chevron-down ms-auto dropdown-chevron"></i>
      </a>
      
      <!-- Subcategory Menu -->
      <ul class="collapse submenu list-unstyled <?= $is_payroll_active ? 'show' : ''; ?>" id="payrollSubmenu">
        <li>
          <a href="<?= site_url('tupad_payrolls/payroll_encode'); ?>" 
             class="nav-sub-link <?= ($current_controller === 'tupad_payrolls' &&$current_method === 'payroll_encode') ? 'active' : ''; ?>">
            <i class="bi bi-circle"></i>
            <span>Encode Payrolls</span>
          </a>
        </li>
      </ul>
    </li>

     
    </li>






    
  </ul>
</aside>