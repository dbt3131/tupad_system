<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GSIS Letter Preparation</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #1e3a8a;
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

        @media print {
            body { background-color: #ffffff; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; }
            .document-sheet { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }

        .document-sheet {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <!-- NAVBAR TEMPLATE VIEW -->
    <?php $this->load->view('templates/navbar'); ?>

    <!-- Main Content Wrapper -->
    <div id="main-content">
        
        <!-- SIDEBAR TEMPLATE VIEW -->
        <?php $this->load->view('templates/sidebar'); ?>

        <!-- Main Workspace -->
        <main class="p-3 p-md-4 flex-grow-1">
            
            <!-- FLASH MESSAGES -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= html_escape($this->session->flashdata('success')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= html_escape($this->session->flashdata('error')); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>GENERATE GSIS LETTERS
                    </h3>
                    <p class="text-muted small mb-0">Filter summary records by generation dates</p>
                </div>
                
                <a href="<?= site_url('tupad/export_gsis_letter_excel?start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date) . '&date_effectivity=' . urlencode($date_effectivity) . '&no_of_days=' . urlencode($no_of_days)); ?>" class="btn btn-success">
    <i class="bi bi-file-earmark-excel me-1"></i> Download Excel Report
</a>
            </div>

          <!-- Date Range & Letter Parameter Filter Form -->
            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <form method="get" action="<?= site_url('tupad/gsis_letter'); ?>" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Start Date (Record Filter)</label>
                            <input type="date" name="start_date" class="form-control" value="<?= html_escape($start_date); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">End Date (Record Filter)</label>
                            <input type="date" name="end_date" class="form-control" value="<?= html_escape($end_date); ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Date Effectivity</label>
                            <input type="date" name="date_effectivity" class="form-control" value="<?= html_escape($date_effectivity); ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">No. of Days</label>
                            <input type="number" name="no_of_days" class="form-control" value="<?= html_escape($no_of_days); ?>" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-1"></i> Apply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- GSIS Official Letter Layout -->
            <div class="container-fluid px-0">
                <div class="document-sheet p-4 p-md-5">
                    
                    <div class="mb-4 fw-bold text-uppercase">
                        <?= strtoupper(date('F d, Y')); ?>
                    </div>

                    <div class="mb-4">
                        <p class="fw-bold mb-0">Ms. KRISTINE JOI G. MACAM</p>
                        <p class="mb-0">Branch Manager</p>
                        <p class="fw-bold mb-0">Government Service Insurance System (GSIS)</p>
                        <p class="mb-0">Sindalan, City of San Fernando, Pampanga</p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1">Dear Ms. Macam:</p>
                    </div>

                    <div class="mb-4 text-justify" style="line-height: 1.6;">
                        May we request the attached list of our beneficiaries under Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers (TUPAD) Program be enrolled under GSIS group insurance effective <strong><?= date('F d, Y', strtotime($date_effectivity)); ?></strong> with a covered period of work of <strong><?= html_escape($no_of_days); ?></strong> days. Below is the summary of our remittance:
                    </div>

                    <!-- Summary Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered text-center align-middle" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle" style="width: 5%;">#</th>
                                    <th rowspan="2" class="align-middle" style="width: 35%;">PARTICULAR</th>
                                    <th colspan="3">NO. OF BENEFICIARIES</th>
                                    <th rowspan="2" class="align-middle" style="width: 12%;">RATE</th>
                                    <th rowspan="2" class="align-middle" style="width: 18%;">AMOUNT</th>
                                </tr>
                                <tr>
                                    <th style="width: 10%;">Male</th>
                                    <th style="width: 10%;">Female</th>
                                    <th style="width: 10%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_male = 0;
                                $total_female = 0;
                                $total_benefs = 0;
                                $total_amount = 0;
                                $rate = 50.00; 
                                $dst = 0;
                                
                                

                                if (!empty($summary_records)): 
                                    $i = 1;
                                    foreach ($summary_records as $row): 
                                        $m = $row['male'] ?? 0;
                                        $f = $row['female'] ?? 0;
                                        $sub_total = $m + $f;
                                        $amount = $sub_total * $rate;

                                        $total_male += $m;
                                        $total_female += $f;
                                        $total_benefs += $sub_total;
                                        $total_amount += $amount;

     if($total_benefs=='1'){
                $dst = 0;
                }
            elseif ($total_benefs >= 2 && $total_benefs <= 4) {
                $dst = 20.00;
            }
            elseif ($total_benefs >= 5 && $total_benefs <= 7) {
                $dst = 50.00;
            }
            elseif ($total_benefs >= 8 && $total_benefs <= 11) {
                $dst = 100.00;
            }
            elseif ($total_benefs >= 12 && $total_benefs <= 15) {
                $dst = 150.00;
            }
             elseif ($total_benefs >= 16) {
                $dst = 200.00;
            }else{
                $dst = 0;
            }



                                ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td class="text-start"><?= html_escape(($row['implementor'] ?? '') . ' (' . ($row['reference_no'] ?? '') . ')'); ?></td>
                                        <td><?= number_format($m); ?></td>
                                        <td><?= number_format($f); ?></td>
                                        <td class="fw-bold"><?= number_format($sub_total); ?></td>
                                        <td><?= number_format($rate, 2); ?></td>
                                        <td class="text-end"><?= number_format($amount, 2); ?></td>
                                    </tr>
                                <?php 
                                    endforeach; 
                                else:
                                ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">No records found for the selected date range.</td>
                                    </tr>
                                <?php endif; ?>

                                <!-- Summary Calculations -->
                                <tr class="fw-bold table-light">
                                    <td colspan="2" class="text-end">TOTAL:</td>
                                    <td><?= number_format($total_male); ?></td>
                                    <td><?= number_format($total_female); ?></td>
                                    <td><?= number_format($total_benefs); ?></td>
                                    <td></td>
                                    <td class="text-end"><?= number_format($total_amount, 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end fw-semibold">DST</td>
                                    <td class="text-end fw-semibold"><?= number_format($dst, 2); ?></td>
                                </tr>
                                <tr class="table-active fw-bold">
                                    <td colspan="6" class="text-end text-uppercase">Grand Total</td>
                                    <td class="text-end text-primary"><?= number_format($total_amount + ($total_amount > 0 ? $dst : 0), 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-5">
                        <p class="mb-4">Thank you and warm regards.</p>
                        <p class="mb-1">Very truly yours,</p>
                    </div>

                    <div class="mt-4 pt-3">
                        <p class="fw-bold mb-0 text-uppercase">AURITA L. LAXAMANA</p>
                        <p class="text-muted small mb-0">CHIEF LEO, TSSD II</p>
                    </div>

                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function () {
        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {
                $('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });
    });
    </script>
</body>

</html>