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

      <!-- KPI Summary Cards -->
      <div class="row g-3 mb-4">
        <!-- Card 1 -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Total Active Tupad Workers</span>
                <h3 class="fw-bold my-1"><?php echo isset($total_active_workers) ? $total_active_workers : '0'; ?></h3>
              </div>
              <div class="icon-badge bg-primary-subtle text-primary">
                <i class="bi bi-people-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Total Inactive Tupad Workers</span>
               <h3 class="fw-bold my-1"><?php echo isset($total_inactive_workers) ? $total_inactive_workers : '0'; ?></h3>
              </div>
              <div class="icon-badge bg-info-subtle text-info">
               <i class="bi bi-people-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Total Funds Disbursed</span>
                <h3 class="fw-bold my-1">₱ 229.1M</h3>
              </div>
              <div class="icon-badge bg-success-subtle text-success">
                <i class="bi bi-currency-dollar"></i>
              </div>
            </div>
          </div>
        </div>

       <!-- Card 4 -->
        <div class="col-12 col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span class="text-muted small fw-medium">Total Funds</span><br>
             
                <h3 class="fw-bold my-1">₱ 229.1M</h3>
              </div>
              <div class="icon-badge bg-success-subtle text-success">
                <i class="bi bi-currency-dollar"></i>
              </div>
            </div>
          </div>
        </div>

      <!-- Central Luzon Map Preview Section -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="content-card">
            <div class="content-card-header d-flex justify-content-between align-items-center">
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

      <!-- Charts Section -->
      <div class="row g-3 mb-4">
        <!-- Bar Chart -->
        <div class="col-12 col-lg-8">
          <div class="content-card h-100">
            <div class="content-card-header">
              <h6 class="fw-bold mb-0">TUPAD Beneficiaries per Province</h6>
              <span class="badge bg-light text-dark border">FY 2026</span>
            </div>
            <div class="p-3">
              <canvas id="provinceBarChart" style="max-height: 320px;"></canvas>
            </div>
          </div>
        </div>

        <!-- Doughnut / Share Chart -->
        <div class="col-12 col-lg-4">
          <div class="content-card h-100">
            <div class="content-card-header">
              <h6 class="fw-bold mb-0">Regional Share %</h6>
            </div>
            <div class="p-3 d-flex align-items-center justify-content-center">
              <canvas id="provinceDoughnutChart" style="max-height: 280px;"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Data Table Section -->
      <div class="content-card">
        <div class="content-card-header">
          <h6 class="fw-bold mb-0">Provincial Breakdown Details</h6>
          <div class="input-group input-group-sm" style="max-width: 240px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search province...">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Province</th>
                <th>Target Workers</th>
                <th>Served Workers</th>
                <th>Completion Rate</th>
                <th>Budget Allocated</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="fw-semibold">Bulacan</span></td>
                <td>11,000</td>
                <td>10,450</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 6px;">
                      <div class="progress-bar bg-success" style="width: 95%;"></div>
                    </div>
                    <small class="fw-bold">95%</small>
                  </div>
                </td>
                <td>₱ 52,250,000</td>
                <td><span class="badge bg-success-subtle text-success">Ongoing</span></td>
              </tr>
              <tr>
                <td><span class="fw-semibold">Pampanga</span></td>
                <td>10,000</td>
                <td>9,800</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 6px;">
                      <div class="progress-bar bg-success" style="width: 98%;"></div>
                    </div>
                    <small class="fw-bold">98%</small>
                  </div>
                </td>
                <td>₱ 49,000,000</td>
                <td><span class="badge bg-success-subtle text-success">Ongoing</span></td>
              </tr>
              <tr>
                <td><span class="fw-semibold">Nueva Ecija</span></td>
                <td>9,500</td>
                <td>8,900</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 6px;">
                      <div class="progress-bar bg-primary" style="width: 93%;"></div>
                    </div>
                    <small class="fw-bold">93%</small>
                  </div>
                </td>
                <td>₱ 44,500,000</td>
                <td><span class="badge bg-success-subtle text-success">Ongoing</span></td>
              </tr>
              <tr>
                <td><span class="fw-semibold">Tarlac</span></td>
                <td>6,500</td>
                <td>6,100</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 6px;">
                      <div class="progress-bar bg-primary" style="width: 93%;"></div>
                    </div>
                    <small class="fw-bold">93%</small>
                  </div>
                </td>
                <td>₱ 30,500,000</td>
                <td><span class="badge bg-success-subtle text-success">Ongoing</span></td>
              </tr>
              <tr>
                <td><span class="fw-semibold">Bataan</span></td>
                <td>5,000</td>
                <td>4,750</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 6px;">
                      <div class="progress-bar bg-info" style="width: 95%;"></div>
                    </div>
                    <small class="fw-bold">95%</small>
                  </div>
                </td>
                <td>₱ 23,750,000</td>
                <td><span class="badge bg-success-subtle text-success">Ongoing</span></td>
              </tr>
              <tr>
                <td><span class="fw-semibold">Zambales</span></td>
                <td>4,500</td>
                <td>3,820</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 6px;">
                      <div class="progress-bar bg-warning" style="width: 84%;"></div>
                    </div>
                    <small class="fw-bold">84%</small>
                  </div>
                </td>
                <td>₱ 19,100,000</td>
                <td><span class="badge bg-warning-subtle text-warning">Pending Review</span></td>
              </tr>
              <tr>
                <td><span class="fw-semibold">Aurora</span></td>
                <td>2,200</td>
                <td>2,000</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height: 6px;">
                      <div class="progress-bar bg-success" style="width: 90%;"></div>
                    </div>
                    <small class="fw-bold">90%</small>
                  </div>
                </td>
                <td>₱ 10,000,000</td>
                <td><span class="badge bg-success-subtle text-success">Ongoing</span></td>
              </tr>
            </tbody>
          </table>
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

  <!-- Dashboard Functionality & Charts & Map -->
  <!-- Dashboard Functionality & Charts & Dynamic Database Map Integration -->
  <script>
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggle = document.getElementById('sidebarToggle');

    sidebarToggle.addEventListener('click', () => {
      if (window.innerWidth < 992) {
        sidebar.classList.toggle('show-mobile');
      } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
      }
    });

    // Safely capture PHP JSON from your database query
    let provinceData = [];
    try {
      provinceData = <?php echo isset($map_json_data) && !empty($map_json_data) ? $map_json_data : '[]'; ?>;
    } catch(e) {
      console.error("JSON Parse Error:", e);
    }

    console.log("Active Province Dataset from Database:", provinceData);

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
      
      // Skip rendering if worker count is 0 and you don't want empty markers
      if (workersCount <= 0) return;

      const radiusSize = Math.max(workersCount * 0.45, 5000);

      // Draw proportional boundary zone overlay
      L.circle([item.lat, item.lng], {
        color: item.color || '#2563eb',
        fillColor: item.color || '#2563eb',
        fillOpacity: 0.4,
        radius: radiusSize
      }).addTo(map).bindPopup(`<strong>${item.name} Province</strong><br>Database Workers: <strong>${workersCount.toLocaleString()}</strong>`);

      // Add clickable marker pin
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
</body>
</html>