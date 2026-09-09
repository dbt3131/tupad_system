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
    /* Custom Modern Dashboard Additions */
    .fs-7 {
      font-size: 0.75rem;
      letter-spacing: 0.05em;
    }
    .content-card {
      border: 1px solid rgba(0, 0, 0, 0.04);
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.03);
    }
    .table > :not(caption) > * > * {
      padding: 0.85rem 1rem;
    }
    .pagination .page-item .page-link {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 2px;
      border-radius: 6px !important;
    }
    .pagination .page-item.active .page-link {
      background-color: var(--bs-primary);
      color: white;
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
          <h3 class="fw-bold mb-1">Region III TUPAD Overview</h3>
          <p class="text-muted small mb-0">Summary of Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers by Province.</p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-download"></i> Export Data
          </button>
          <button class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Add Worker Batch
          </button>
        </div>
      </div>
       
      <!-- Central Luzon Map Preview Section -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="content-card bg-white rounded-4 overflow-hidden">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50">
              <div>
                <h6 class="fw-bold mb-0"><i class="bi bi-map text-primary me-2"></i>Central Luzon Geographic Deployment Preview</h6>
                <p class="text-muted small mb-0">Interactive markers indicating active cluster concentrations across Region III provinces.</p>
              </div>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle">GIS Live View</span>
            </div>
            <div class="p-3">
              <!-- Map Container -->
              <div id="centralLuzonMap" style="height: 380px; width: 100%; border-radius: 8px;"></div>
            </div>
          </div>
        </div>
      </div>

<!-- Modern ADL Transactions Table Section -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="content-card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
            
            <!-- Card Header with Search and Entries Dropdown -->
            <div class="p-4 border-bottom bg-light bg-opacity-50 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
              <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-table text-primary me-2"></i>ADL Transactions Overview</h5>
                <p class="text-muted small mb-0">Active Authorized Disbursement List (ADL) records and fund balances.</p>
              </div>
            </div>

            <!-- Responsive Table Container -->
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light text-uppercase fs-7 text-secondary fw-semibold">
                  <tr>
                    <th class="ps-4 py-3">ADL No.</th>
                    <th class="py-3">ADL Date</th>
                    <th class="py-3">Date Received</th>
                    <th class="py-3">Target Beneficiaries</th>
                    <th class="py-3">Amount</th>
                    <th class="pe-4 py-3 text-end">Balance</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($adl_records)): ?>
                    <?php foreach ($adl_records as $row): ?>
                      <tr>
                        <td class="ps-4 fw-bold text-dark">
                          <a href="#" class="text-decoration-none text-primary"><?= html_escape($row['adl_no']); ?></a>
                        </td>
                        <td class="text-secondary"><?= html_escape($row['adl_date']); ?></td>
                        <td class="text-secondary"><?= html_escape($row['date_received']); ?></td>
                        <td>
                          <span class="badge bg-secondary-subtle text-dark fw-normal px-2 py-1">
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
                    <tr>
                      <td colspan="6" class="text-center py-4 text-muted">No ADL records found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <!-- Card Footer -->
            <div class="p-3 px-4 border-top bg-light bg-opacity-25 d-flex justify-content-between align-items-center">
              <div class="text-muted small">
                Total Registered Records: <span class="fw-semibold text-dark"><?= !empty($adl_records) ? count($adl_records) : 0; ?></span>
              </div>
            </div>

          </div>
        </div>
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

  <!-- Dashboard Functionality & Charts & Dynamic Database Map Integration -->
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

    // Extract arrays for Chart.js dynamically
    const provinces = provinceData.map(p => p.name);
    const servedWorkers = provinceData.map(p => Number(p.workers));
    const chartColors = provinceData.map(p => p.color || '#2563eb');

    // Chart 1: Bar Chart
    const ctxBar = document.getElementById('provinceBarChart').getContext('2d');
    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: provinces,
        datasets: [{
          label: 'Served TUPAD Workers',
          data: servedWorkers,
          backgroundColor: '#2563eb',
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
          x: { grid: { display: false } }
        }
      }
    });

    // Chart 2: Doughnut Chart
    const ctxDoughnut = document.getElementById('provinceDoughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
      type: 'doughnut',
      data: {
        labels: provinces,
        datasets: [{
          data: servedWorkers,
          backgroundColor: chartColors,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
        }
      }
    });
  </script>
</body>
</html>