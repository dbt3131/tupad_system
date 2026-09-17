<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOLE Region 3 - eTUPAD-PRISM Dashboard</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Leaflet CSS for Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
      --bg-body: #f4f6f9;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --card-border: #e2e8f0;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: var(--bg-body);
      color: var(--text-main);
    }

    .fs-7 {
      font-size: 0.75rem;
      letter-spacing: 0.05em;
    }

    /* Modern Floating Card Containers */
    .content-card {
      background: #ffffff;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.5rem;
      overflow: hidden;
    }

    /* Modernized Table Design */
    .table {
      border-collapse: separate;
      border-spacing: 0 0.4rem;
      margin-bottom: 0 !important;
    }

    .table thead th {
      background-color: #f8fafc !important;
      color: #475569;
      font-weight: 600;
      border-top: none;
      border-bottom: 1px solid #e2e8f0;
      padding: 1rem 1rem;
    }

    .table tbody tr {
      background-color: #ffffff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.01);
      transition: all 0.2s ease;
    }

    .table tbody tr:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.04);
      background-color: #ffffff !important;
    }

    .table tbody td {
      padding: 1rem 1rem;
      vertical-align: middle;
      border-top: 1px solid #f1f5f9;
      border-bottom: 1px solid #f1f5f9;
    }

    .table tbody td:first-child {
      border-left: 1px solid #f1f5f9;
      border-top-left-radius: 0.5rem;
      border-bottom-left-radius: 0.5rem;
    }

    .table tbody td:last-child {
      border-right: 1px solid #f1f5f9;
      border-top-right-radius: 0.5rem;
      border-bottom-right-radius: 0.5rem;
    }

    /* Form Controls & Inputs */
    .form-control, .form-select {
      border: 1px solid var(--card-border);
      border-radius: 0.5rem;
      padding: 0.5rem 0.75rem;
      background-color: #f8fafc;
      transition: all 0.2s;
      font-size: 0.875rem;
    }

    .form-control:focus, .form-select:focus {
      background-color: #ffffff;
      border-color: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Pagination Style */
    .pagination .page-item .page-link {
      border: none;
      border-radius: 0.375rem;
      margin: 0 3px;
      color: #475569;
      font-weight: 500;
      padding: 0.5rem 0.75rem;
      background-color: #f1f5f9;
    }

    .pagination .page-item.active .page-link {
      background: var(--primary-gradient);
      color: #ffffff;
      box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }

    .pagination .page-item .page-link:hover {
      background-color: #e2e8f0;
      color: #1e293b;
    }
  </style>
</head>
<body>

<?php $this->load->view('templates/navbar'); ?>

  <!-- ================= MAIN CONTENT WRAPPER ================= -->
  <div id="main-content">
    <?php $this->load->view('templates/sidebar'); ?>

    <!-- Main Container -->
    <main class="p-3 p-md-4 flex-grow-1">

      <!-- Page Header -->
      <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-4 gap-2">
        <div>
          <h3 class="fw-bold mb-1 text-dark">Region III TUPAD Overview</h3>
          <p class="text-muted small mb-0">Summary of Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers by Province.</p>
        </div>
      </div>
       
      <!-- Central Luzon Map Preview Section -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="content-card">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
              <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-map text-primary me-2"></i>Central Luzon Geographic Deployment Preview</h6>
                <p class="text-muted small mb-0">Interactive markers indicating active cluster concentrations across Region III provinces.</p>
              </div>

            </div>
            <div class="p-3">
              <!-- Map Container -->
              <div id="centralLuzonMap" style="height: 380px; width: 100%; border-radius: 0.75rem;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Alternative ADL Transactions Table Section with Search and Pagination -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="content-card">
            
            <!-- Modern Toolbar Header -->
            <div class="card-header bg-white py-4 px-4 border-bottom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
              <div>
                <h5 class="fw-bold mb-1 text-dark">
                  <i class="bi bi-file-earmark-text text-primary me-2"></i>ADL Transactions Overview
                </h5>
                <p class="text-muted small mb-0">Active Authorized Disbursement List (ADL) records and fund balances.</p>
              </div>
              
              <!-- Search and Limit Controls Toolbar -->
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="input-group input-group-sm bg-light rounded-pill px-2 border" style="width: 240px;">
                  <span class="input-group-text bg-transparent border-0 text-muted ps-1"><i class="bi bi-search"></i></span>
                  <input type="text" id="altAdlSearch" class="form-control form-control-sm bg-transparent border-0 shadow-none" placeholder="Search ADL records...">
                </div>
                <select id="altAdlLimit" class="form-select form-select-sm rounded-pill px-3 border text-secondary" style="width: 110px;">
                  <option value="5">5 rows</option>
                  <option value="10" selected>10 rows</option>
                  <option value="25">25 rows</option>
                  <option value="50">50 rows</option>
                </select>
              </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="table-responsive p-3">
              <table class="table align-middle text-nowrap" id="altAdlTable">
                <thead>
                  <tr>
                    <th class="ps-4">ADL No.</th>
                    <th>ADL Date</th>
                    <th>Date Received</th>
                    <th>Target Beneficiaries</th>
                    <th>Amount</th>
                    <th class="pe-4 text-end">Balance</th>
                  </tr>
                </thead>
                <tbody id="altAdlBody">
                  <?php if (!empty($adl_records)): ?>
                    <?php foreach ($adl_records as $row): ?>
                      <tr class="adl-row">
                        <td class="ps-4 fw-bold text-dark">
                          <a href="#" class="text-decoration-none text-primary"><?= html_escape($row['adl_no']); ?></a>
                        </td>
                        <td class="text-secondary"><?= html_escape($row['adl_date']); ?></td>
                        <td class="text-secondary"><?= html_escape($row['date_received']); ?></td>
                        <td>
                          <span class="badge bg-secondary-subtle text-dark fw-normal px-2.5 py-1.5 rounded-pill border border-secondary-subtle">
                            <?= number_format($row['target_benefs']); ?>
                          </span>
                        </td>
                        <td class="fw-medium text-success">
                          &#8369;<?= number_format($row['adl_amount'], 2); ?>
                        </td>
                        <td class="pe-4 text-end fw-bold text-primary">
                          &#8369;<?= number_format($row['balance'], 2); ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr id="altNoDataRow">
                      <td colspan="6" class="text-center py-4 text-muted">No ADL records found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <!-- Clean Card Footer with Counter and Pagination -->
            <div class="card-footer bg-white py-3 px-4 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
              <div class="text-muted small" id="altAdlInfo">
                Showing 0 entries
              </div>
              <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="altAdlPagination">
                  <!-- Pagination items injected via script -->
                </ul>
              </nav>
            </div>

          </div>
        </div>
      </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-top p-3 text-center text-muted small">
      &copy; 2026 Department of Labor and Employment - Region III. All rights reserved.
    </footer>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <!-- Dashboard Functionality & Map Integration & Table Script -->
  <script>
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if(sidebarToggle) {
      sidebarToggle.addEventListener('click', () => {
        if (window.innerWidth < 992) {
          sidebar.classList.toggle('show-mobile');
        } else {
          sidebar.classList.toggle('collapsed');
          mainContent.classList.toggle('expanded');
        }
      });
    }

    // Safely capture PHP JSON from your database query
    let provinceData = [];
    try {
      provinceData = <?php echo isset($map_json_data) && !empty($map_json_data) ? $map_json_data : '[]'; ?>;
    } catch(e) {
      console.error("JSON Parse Error:", e);
    }

    // Initialize Leaflet Map centered over Central Luzon (Region III)
    const map = L.map('centralLuzonMap').setView([15.35, 120.75], 8);

    // Add OpenStreetMap Tile Layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Plot Province Circles and Pins strictly from database rows
    provinceData.forEach(item => {
      const workersCount = Number(item.workers) || 0;
      if (workersCount <= 0) return;

      const radiusSize = Math.max(workersCount * 0.45, 5000);

      L.circle([item.lat, item.lng], {
        color: item.color || '#2563eb',
        fillColor: item.color || '#2563eb',
        fillOpacity: 0.4,
        radius: radiusSize
      }).addTo(map).bindPopup(`<strong>${item.name} Province</strong><br>Database Workers: <strong>${workersCount.toLocaleString()}</strong>`);

      const markerHtml = `<div style="background-color: ${item.color || '#2563eb'}; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 6px rgba(0,0,0,0.5);"></div>`;
      const customIcon = L.divIcon({
        html: markerHtml,
        className: 'custom-map-marker',
        iconSize: [16, 16]
      });

      L.marker([item.lat, item.lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(`<b>${item.name}</b><br>Table Count: <b>${workersCount.toLocaleString()}</b> workers`);
    });

    // Standalone Pagination & Search Script for ADL Table
    document.addEventListener("DOMContentLoaded", function () {
      const searchInput = document.getElementById('altAdlSearch');
      const limitSelect = document.getElementById('altAdlLimit');
      const tableBody = document.getElementById('altAdlBody');
      const paginationEl = document.getElementById('altAdlPagination');
      const infoEl = document.getElementById('altAdlInfo');
      
      const allRows = Array.from(tableBody.querySelectorAll('.adl-row'));
      let currentPage = 1;

      function updateTable() {
        const query = searchInput.value.toLowerCase().trim();
        const limit = parseInt(limitSelect.value);

        // Filter rows based on search input
        const filtered = allRows.filter(row => {
          return row.textContent.toLowerCase().includes(query);
        });

        // Pagination calculation
        const totalPages = Math.ceil(filtered.length / limit) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * limit;
        const end = start + limit;

        // Hide all rows first
        allRows.forEach(r => r.style.display = 'none');

        // Show current page slice
        const currentSlice = filtered.slice(start, end);
        currentSlice.forEach(r => r.style.display = '');

        // Update info text
        if (filtered.length > 0) {
          infoEl.innerHTML = `Showing <b>${start + 1}</b> to <b>${Math.min(end, filtered.length)}</b> of <b>${filtered.length}</b> entries`;
        } else {
          infoEl.innerHTML = `No matching records found`;
        }

        buildPagination(totalPages);
      }

      function buildPagination(totalPages) {
        paginationEl.innerHTML = '';

        if (totalPages <= 1) return;

        // Previous Button
        const prevClass = currentPage === 1 ? 'disabled' : '';
        paginationEl.innerHTML += `
          <li class="page-item ${prevClass}">
            <a class="page-link rounded-start-pill px-3" href="#" data-page="${currentPage - 1}">&laquo; Prev</a>
          </li>`;

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
          const activeClass = i === currentPage ? 'active' : '';
          paginationEl.innerHTML += `
            <li class="page-item ${activeClass}">
              <a class="page-link px-3" href="#" data-page="${i}">${i}</a>
            </li>`;
        }

        // Next Button
        const nextClass = currentPage === totalPages ? 'disabled' : '';
        paginationEl.innerHTML += `
          <li class="page-item ${nextClass}">
            <a class="page-link rounded-end-pill px-3" href="#" data-page="${currentPage + 1}">Next &raquo;</a>
          </li>`;

        // Attach click events
        paginationEl.querySelectorAll('.page-link').forEach(link => {
          link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetPage = parseInt(this.getAttribute('data-page'));
            if (!isNaN(targetPage) && targetPage > 0 && targetPage <= totalPages) {
              currentPage = targetPage;
              updateTable();
            }
          });
        });
      }

      // Event bindings
      searchInput.addEventListener('input', () => {
        currentPage = 1;
        updateTable();
      });

      limitSelect.addEventListener('change', () => {
        currentPage = 1;
        updateTable();
      });

      // Run initial execution
      updateTable();
    });
  </script>
</body>
</html>