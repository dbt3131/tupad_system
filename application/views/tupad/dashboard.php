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
  <!-- DataTables Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/css/dataTables.bootstrap5.min.css">
  <!-- DataTables Buttons Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
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

    /* Modernized Table Design Compatibility with DataTables */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
      border: 1px solid var(--card-border);
      border-radius: 0.5rem;
      padding: 0.4rem 0.75rem;
      background-color: #f8fafc;
      font-size: 0.875rem;
    }

    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
      background-color: #ffffff;
      border-color: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
      outline: none;
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
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-map text-primary me-2"></i>Central Luzon Geographic Deployment Preview.</h6>
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

      <!-- ADL Transactions Table Section with DataTables & Excel Export -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="content-card">
            
            <!-- Modern Toolbar Header -->
            <div class="card-header bg-white py-4 px-4 border-bottom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
              <div>
                <h5 class="fw-bold mb-1 text-dark">
                  <i class="bi bi-file-earmark-text text-primary me-2"></i>ADL Transactions Overview
                </h5>
                <p class="text-muted small mb-0">Active Authorized Disbursement List (ADL) records, deductions breakdown, and fund balances.</p>
              </div>
              
              <!-- Target container for the Excel download button -->
              <div id="exportButtonContainer"></div>
            </div>

            <!-- Responsive Table Container -->
            <div class="table-responsive p-3">
              <table class="table table-striped table-hover align-middle w-100 text-nowrap" id="altAdlTable">
                <thead class="table-light">
                  <tr>
                    <th class="ps-4">ADL No.</th>
                    <th>ADL Date</th>
                    <th>Date Received</th>
                    <th class="text-end">Target Beneficiaries</th>
                    <th class="text-end">ADL Subsidy</th>
                    <th class="text-end">PPEs Amount</th>
                    <th class="text-end">GSIS Amount</th>
                    <th class="text-end">Completed Emp. Amount</th>
                    <th class="text-end">Payout Cost</th>
                    <th class="text-end">MAF Amount</th>
                    <th class="text-end">Total Deductions</th>
                    <th class="pe-4 text-end">Net Balance</th>
                  </tr>
                </thead>
               <tbody id="altAdlBody">
  <?php if (!empty($adl_records)): ?>
    <?php foreach ($adl_records as $row): ?>
    
      
<?php 
        $subsidy   = floatval($row['adl_subsidy'] ?? 0);
        $ppes      = floatval($row['ppes_amount'] ?? 0);
        $gsis      = floatval($row['gsis_enrollment_amount'] ?? 0);
        $completed = floatval($row['completed_employment_amount'] ?? 0);
        $payout    = floatval($row['payout_service_cost'] ?? 0);
        $maf       = floatval($row['maf_amount'] ?? 0);

        // CHANGE THIS LINE: Force it to explicitly add all breakdown components together
        $total_deductions = $ppes + $gsis + $completed + $payout + $maf;
        
        // Net Balance
        $net_balance = $subsidy - $total_deductions;
      ?>

      <tr class="adl-row">
        <td class="ps-4 fw-bold text-dark">
    <a href="<?= site_url('adl/adl_report'); ?>?adl_no=<?= urlencode($row['adl_no'] ?? ''); ?>" class="text-decoration-none text-primary">
      <?= html_escape($row['adl_no'] ?? ''); ?>
    </a>
  </td>
        <td class="text-secondary"><?= html_escape($row['adl_date'] ?? ''); ?></td>
        <td class="text-secondary"><?= html_escape($row['date_received'] ?? ''); ?></td>
        <td class="text-end">
          <?= number_format($row['target_benefs'] ?? 0); ?>
        </td>
        <td class="text-end fw-medium text-success">
          <?= number_format($subsidy, 2); ?>
        </td>
        <td class="text-end text-secondary"><?= number_format($ppes, 2); ?></td>
        <td class="text-end text-secondary"><?= number_format($gsis, 2); ?></td>
        <td class="text-end text-secondary"><?= number_format($completed, 2); ?></td>
        <td class="text-end text-secondary"><?= number_format($payout, 2); ?></td>
        <td class="text-end text-secondary"><?= number_format($maf, 2); ?></td>
        <td class="text-end fw-medium text-danger">
          <?= number_format($total_deductions, 2); ?>
        </td>
        <td class="pe-4 text-end fw-bold text-primary">
          <?= number_format($net_balance, 2); ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</tbody>
              </table>
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

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <!-- DataTables JS -->
  <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <!-- JSZip (Required for Excel export) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <!-- DataTables Buttons JS & HTML5 Export Plugin -->
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

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
    let municipalityData = [];
    try {
      municipalityData = <?php echo isset($map_json_data) && !empty($map_json_data) ? json_encode($map_json_data) : '[]'; ?>;
    } catch(e) {
      console.error("JSON Parse Error:", e);
    }
console.log("Municipality Map Data:", municipalityData);
    // Initialize Leaflet Map centered over Central Luzon (Region III)
    const map = L.map('centralLuzonMap').setView([15.35, 120.75], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Plot Municipality Circles and Pins
    municipalityData.forEach(item => {
      const workersCount = Number(item.workers) || 0;
      if (workersCount <= 0 || !item.lat || !item.lng) return;

      const radiusSize = Math.max(workersCount * 25, 2000);

      L.circle([item.lat, item.lng], {
        color: item.color || '#2563eb',
        fillColor: item.color || '#2563eb',
        fillOpacity: 0.4,
        radius: radiusSize
      }).addTo(map).bindPopup(`<strong>${item.name}, ${item.province}</strong><br>Active Workers: <strong>${workersCount.toLocaleString()}</strong>`);

      const markerHtml = `<div style="background-color: ${item.color || '#2563eb'}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.4);"></div>`;
      const customIcon = L.divIcon({
        html: markerHtml,
        className: 'custom-map-marker',
        iconSize: [12, 12]
      });

      L.marker([item.lat, item.lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(`<b>${item.province_name}</b> (${item.municipality_name})<br>Workers: <b>${workersCount.toLocaleString()}</b>`);
    });

    // Initialize DataTables with Excel Button Integration
    document.addEventListener("DOMContentLoaded", function () {
      const table = $('#altAdlTable').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        ordering: true,
        responsive: true,
        dom: '<"row mb-3 align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row mt-3 align-items-center"<"col-md-5"i><"col-md-7 text-end"p>>',
        columnDefs: [
          { targets: [3, 4, 5, 6, 7, 8, 9, 10, 11], className: 'text-end' },
          {
            targets: [3],
            render: function (data) {
              let num = parseInt((data || '0').replace(/,/g, ''));
              return isNaN(num) ? '0' : num.toLocaleString('en-US');
            }
          },
          {
            targets: [4, 5, 6, 7, 8, 9, 10, 11],
            render: function (data) {
              let num = parseFloat((data || '0').replace(/[^0-9.-]+/g, ""));
              return isNaN(num) ? '0.00' : num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
          }
        ],



        buttons: [
          {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel me-1"></i> Download Excel',
            className: 'btn btn-success btn-sm shadow-sm',
            title: '',
            filename: 'DOLE_Region3_eTUPAD_ADL_Report',
            exportOptions: {
              columns: ':visible'
            },

            

customize: function (xlsx) {
              var sheet = xlsx.xl.worksheets['sheet1.xml'];
              var styles = xlsx.xl['styles.xml'];

              // 1. Inject custom number formats into styles.xml (Date format ID 176, Currency ID 175)
              var numFmts = styles.getElementsByTagName('numFmts');
              var numFmtIdCurrency = 175;
              var numFmtIdDate = 176;

              if (numFmts.length === 0) {
                var stylesheet = styles.getElementsByTagName('styleSheet')[0];
                var newNumFmts = styles.createElement('numFmts');
                newNumFmts.setAttribute('count', '2');
                
                var newNumFmtCurr = styles.createElement('numFmt');
                newNumFmtCurr.setAttribute('numFmtId', numFmtIdCurrency);
                newNumFmtCurr.setAttribute('formatCode', '#,##0.00');
                newNumFmts.appendChild(newNumFmtCurr);

                var newNumFmtDate = styles.createElement('numFmt');
                newNumFmtDate.setAttribute('numFmtId', numFmtIdDate);
                newNumFmtDate.setAttribute('formatCode', 'yyyy-mm-dd');
                newNumFmts.appendChild(newNumFmtDate);

                stylesheet.insertBefore(newNumFmts, stylesheet.firstChild);
              } else {
                var stylesheet = styles.getElementsByTagName('styleSheet')[0];
                var existingNumFmts = numFmts[0];
                
                var newNumFmtCurr = styles.createElement('numFmt');
                newNumFmtCurr.setAttribute('numFmtId', numFmtIdCurrency);
                newNumFmtCurr.setAttribute('formatCode', '#,##0.00');
                existingNumFmts.appendChild(newNumFmtCurr);

                var newNumFmtDate = styles.createElement('numFmt');
                newNumFmtDate.setAttribute('numFmtId', numFmtIdDate);
                newNumFmtDate.setAttribute('formatCode', 'yyyy-mm-dd');
                existingNumFmts.appendChild(newNumFmtDate);

                existingNumFmts.setAttribute('count', parseInt(existingNumFmts.getAttribute('count') || 0) + 2);
              }

              // 2. Create custom cell styles utilizing index 25 borders
              var cellXfs = styles.getElementsByTagName('cellXfs')[0];
              var borderStyleRef = cellXfs.childNodes[25];

              // Custom Currency Style Index
              var customCurrencyStyleIndex = cellXfs.childNodes.length;
              var newXfCurr = borderStyleRef.cloneNode(true);
              newXfCurr.setAttribute('numFmtId', numFmtIdCurrency);
              newXfCurr.setAttribute('applyNumberFormat', '1');
              cellXfs.appendChild(newXfCurr);

              // Custom Date Style Index
              var customDateStyleIndex = cellXfs.childNodes.length;
              var newXfDate = borderStyleRef.cloneNode(true);
              newXfDate.setAttribute('numFmtId', numFmtIdDate);
              newXfDate.setAttribute('applyNumberFormat', '1');
              cellXfs.appendChild(newXfDate);

              cellXfs.setAttribute('count', cellXfs.childNodes.length);

              // Calculate column totals from applied search filter with safe fallbacks
              var totalBenefs = 0, totalSubsidy = 0, totalPpes = 0, totalGsis = 0, totalCompleted = 0, totalPayout = 0, totalMaf = 0, totalDeductions = 0, totalBalance = 0;

              table.rows({ search: 'applied' }).every(function () {
                var data = this.data();
                totalBenefs += parseInt((data[3] || '0').toString().replace(/[^0-9]/g, '')) || 0;
                totalSubsidy += parseFloat((data[4] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
                totalPpes += parseFloat((data[5] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
                totalGsis += parseFloat((data[6] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
                totalCompleted += parseFloat((data[7] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
                totalPayout += parseFloat((data[8] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
                totalMaf += parseFloat((data[9] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
                totalDeductions += parseFloat((data[10] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
                totalBalance += parseFloat((data[11] || '0').toString().replace(/[^0-9.-]+/g, "")) || 0;
              });

              // Shift rows down by 2 to accommodate title block
              $('row', sheet).each(function () {
                var r = parseInt($(this).attr('r')) + 2;
                $(this).attr('r', r);$(this).find('c').each(function () {
                  var cellRef = $(this).attr('r');
                  var col = cellRef.replace(/[0-9]/g, '');
                  $(this).attr('r', col + r);
                });
              });

              // Format data rows
              $('row', sheet).each(function () {
                var r = parseInt($(this).attr('r'));
                if (r === 3) {
                  $(this).find('c').each(function () { $(this).attr('s', '25'); });                 } else if (r > 3) {$(this).find('c').each(function (index) {
                    var cell = $(this);
                    var rawText = cell.text().replace(/,/g, '').trim();

                    if (index === 1 || index === 2) {
                      // Format columns B and C as proper Excel serial dates if valid format (YYYY-MM-DD)
                      cell.attr('s', customDateStyleIndex);
                      if (rawText.match(/^\d{4}-\d{2}-\d{2}$/)) {
                        var d = new Date(rawText);
                        if (!isNaN(d.getTime())) {
                          // Excel base date calculation offset
                          var excelDateSerial = Math.floor((d - new Date(1899, 11, 30)) / (1000 * 60 * 60 * 24));
                          cell.attr('t', 'n');
                          cell.empty().append('<v>' + excelDateSerial + '</v>');
                        }
                      }
                    } else if (index >= 4 && index <= 11) {
                      cell.attr('s', customCurrencyStyleIndex);
                      var numVal = parseFloat(rawText.replace(/[^0-9.-]+/g, ""));
                      if (!isNaN(numVal)) {
                        cell.attr('t', 'n');
                        cell.empty().append('<v>' + numVal + '</v>');
                      }
                    } else if (index === 3) {
                      cell.attr('s', '25');
                      var intVal = parseInt(rawText);
                      cell.attr('t', 'n');
                      cell.empty().append('<v>' + (isNaN(intVal) ? 0 : intVal) + '</v>');
                    } else {
                      cell.attr('s', '25');
                    }
                  });
                }
              });

              // Add Dynamic Title Header at Row 1
              var titleText = 'DOLE REGION III - eTUPAD ADL TRANSACTIONS OVERVIEW REPORT';
              var row1 = '<row r="1"><c t="inlineStr" r="A1" s="51"><is><t>' + titleText + '</t></is></c></row>';
              $('sheetData', sheet).prepend(row1);

              // Merge title cells across columns A to L
              var mergeCells = sheet.getElementsByTagName('mergeCells');
              if (mergeCells.length === 0) {
                sheet.getElementsByTagName('worksheet')[0].appendChild(sheet.createElement('mergeCells'));
                mergeCells = sheet.getElementsByTagName('mergeCells');
              }
              var mergeCell = sheet.createElement('mergeCell');
              mergeCell.setAttribute('ref', 'A1:L1');
              mergeCells[0].appendChild(mergeCell);
              mergeCells[0].setAttribute('count', parseInt(mergeCells[0].getAttribute('count') || 0) + 1);

              // Append Grand Total row at the end
              var lastRowElem = $('row', sheet).last();
              var lastRowIdx = lastRowElem.length > 0 ? parseInt(lastRowElem.attr('r')) + 1 : 4;

              var totalRow = '<row r="' + lastRowIdx + '">' +
                             '<c t="inlineStr" r="A' + lastRowIdx + '" s="25"><is><t>GRAND TOTAL</t></is></c>' +
                             '<c t="inlineStr" r="B' + lastRowIdx + '" s="25"><is><t></t></is></c>' +
                             '<c t="inlineStr" r="C' + lastRowIdx + '" s="25"><is><t></t></is></c>' +
                             '<c t="n" r="D' + lastRowIdx + '" s="25"><v>' + totalBenefs + '</v></c>' +
                             '<c t="n" r="E' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalSubsidy + '</v></c>' +
                             '<c t="n" r="F' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalPpes + '</v></c>' +
                             '<c t="n" r="G' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalGsis + '</v></c>' +
                             '<c t="n" r="H' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalCompleted + '</v></c>' +
                             '<c t="n" r="I' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalPayout + '</v></c>' +
                             '<c t="n" r="J' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalMaf + '</v></c>' +
                             '<c t="n" r="K' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalDeductions + '</v></c>' +
                             '<c t="n" r="L' + lastRowIdx + '" s="' + customCurrencyStyleIndex + '"><v>' + totalBalance + '</v></c>' +
                           '</row>';

              $('sheetData', sheet).append(totalRow);
            }
          }
        ]
      });

      // Append buttons container to target header wrapper
      table.buttons().container().appendTo('#exportButtonContainer');
    });

    // Security Controls
    document.addEventListener('contextmenu', function (e) {
      e.preventDefault();
    });

    document.addEventListener('keydown', function (e) {
      if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
        e.preventDefault();
      }
      if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
        e.preventDefault();
      }
    });

    $(document).ready(function () {
        // Initialize Select2 with Bootstrap 5 Theme
        $('#filter_province, #filter_proponent, #filter_district').on('change', function () {
            const adlNo = $('#filter_adl_no').val();
            const provCode = $('#filter_province').val();
            const proponentName = $('#filter_proponent').val();
            const districtNo = $('#filter_district').val();

            if (adlNo) {
                loadReportData(adlNo, provCode, proponentName, districtNo);
            }
        });

        // === DEBUG & AUTO-SELECT ADL FROM URL ===
        const urlParams = new URLSearchParams(window.location.search);
        const paramAdlNo = urlParams.get('adl_no');

        console.log("URL param adl_no found:", paramAdlNo);

        if (paramAdlNo) {
            // Verify option exists in dropdown first
            const optionExists = $('#filter_adl_no option').filter(function() {
                return $(this).val().trim() === paramAdlNo.trim();
            }).length > 0;

            console.log("Does option exist in dropdown?", optionExists);

            if (optionExists) {
                // Set value and trigger change safely
                $('#filter_adl_no').val(paramAdlNo).trigger('change').trigger('change.select2');
            } else {
                console.warn("ADL No '" + paramAdlNo + "' was not found in the dropdown list options.");
            }
        }
        // ==========================================
    });
  </script>
</body>
</html>