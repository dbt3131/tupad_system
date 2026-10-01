<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADL Transactions Encoding - DOLE TUPAD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0f172a;
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

        .btn-primary, .btn-outline-primary {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }

        .btn-outline-primary {
            background-color: transparent !important;
            color: #0f172a !important;
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #1e293b !important;
            border-color: #1e293b !important;
        }

        .modal-header {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        @media print {
            body { background-color: #ffffff; }
            #sidebar, .top-navbar, .no-print { display: none !important; }
            #main-content { margin-left: 0 !important; }
        }

        .form-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            max-width: 1100px;
            margin: 0 auto;
        }

        .form-control::placeholder {
            color: #797a7846;
            font-style: italic;
            opacity: 1;
        }

        input[type="date"].form-control:invalid::-webkit-datetime-edit {
            color: #797a7846;
            font-style: italic;
        }

        input[type="date"].form-control:valid {
            color: #000000;
            font-style: normal;
        }
        input[type="date"].form-control:valid::-webkit-datetime-edit {
            color: #000000;
            font-style: normal;
        }

        input[type="date"].form-control::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.5);
        }

        select.form-select:invalid {
            color: #797a7846;
            font-style: italic;
        }
        select.form-select option {
            color: #555;
            font-style: normal;
        }
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        
        <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">
            
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

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-clipboard2-data text-primary me-2"></i>ADL TRANSACTIONS ENCODING
                    </h3>
                    <p class="text-muted small mb-0">Record and monitor transaction details for ADL</p>
                </div>
            </div>

            <div class="container-fluid px-0">
                <div class="form-card p-4 p-md-5">

                    <div class="col-12" id="targetNoticeContainer" style="display: none;">
                        <div class="alert alert-danger py-2 px-3 small mb-2 d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                            <div id="targetNoticeText"></div>
                        </div>
                    </div>

                    <div class="col-12" id="subsidyNoticeContainer" style="display: none;">
                        <div class="alert alert-danger py-2 px-3 small mb-2 d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                            <div id="subsidyNoticeText"></div>
                        </div>
                    </div>

                    <form action="<?= site_url('adl/store_transaction'); ?>" method="POST" id="transactionForm">
                        
                        <h5 class="fw-bold mb-4 text-primary">
                            <i class="bi bi-info-circle me-1"></i> General Information
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">ADL Number</label>
                                <select id="adl_no" name="adl_no" class="form-select" style="width: 100%;" required>
                                    <option value="">-Select ADL-</option>
                                    <?php if (!empty($ADL)): ?>
                                        <?php foreach ($ADL as $ad): ?>
                                            <option value="<?= html_escape($ad['adl_no']); ?>" <?= set_select('adl_no', $ad['adl_no']); ?>>
                                                <?= html_escape($ad['adl_no']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
    <label class="form-label fw-semibold small">Fund Source</label>
    <select name="fund_source" id="fund_source" class="form-select" style="width: 100%;" required>
        <option value="" selected disabled>-- Select Fund Source --</option>
        <?php if (!empty($fund_sources)): ?>
            <?php foreach ($fund_sources as $fs): ?>
                <option value="<?= html_escape($fs['fund_source_id']); ?>" <?= set_select('fund_source', $fs['fund_source_id']); ?>>
                    <?= html_escape($fs['fund_source_desc']); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
                            
                           <div class="col-md-2">
                                <label class="form-label fw-semibold small">Audrey Ref No.</label>
                                <input type="text" name="audrey_reference_no" id="audrey_reference_no" oninput="this.value = this.value.toUpperCase();" placeholder="Manual Reference No" class="form-control" autocomplete='OFF' required>
                                <input type="hidden" name="implementation_reference_no" id="implementation_reference_no" class="form-control" placeholder="Auto-generated" readonly required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Date Coordinated</label>
                                <input type="date" name="status_date" class="form-control" required>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label fw-semibold small"># of Days</label>
                                <input type="text" name="no_of_days" class="form-control" placeholder="0" required autocomplete="OFF">
                            </div>

                            <div class="col-md-1">
                                <label class="form-label fw-semibold small">Benefs</label>
                                <input type="text" name="target" class="form-control" placeholder="0" required autocomplete="OFF">
                            </div>
                            <!--
                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Reformulated Target</label>
                                <input type="text" name="reformulated_target" class="form-control" placeholder="0" autocomplete="OFF">
                            </div> -->




                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Implementation Province</label>
                                <select name="implementation_province" id="implementation_province" class="form-select" required>
                                    <option value="" selected disabled>Select Province</option>
                                    <?php if (!empty($provinces)): ?>
                                        <?php foreach ($provinces as $prov): ?>
                                            <option value="<?= html_escape($prov['provCode']); ?>">
                                                <?= html_escape($prov['provDesc']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Implementation Area</label>
                                <select name="implementation_area" id="implementation_area" class="form-select" required disabled>
                                    <option value="" selected disabled>Select Province First</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Barangay</label>
                                <select name="implementation_brgy" id="implementation_brgy" class="form-select" disabled>
                                    <option value="" selected disabled>Select Municipality First</option>
                                </select>
                            </div>

                           <div class="col-md-3">
                             <label class="form-label fw-semibold small">District</label>
                             <select name="implementation_district" id="implementation_district" class="form-select" style="width: 100%;" required>
                                 <option value="" selected disabled>-- Select District --</option>
                                 <?php if (!empty($districts)): ?>
                                     <?php foreach ($districts as $dist): ?>
                                         <option value="<?= html_escape($dist['district_id']); ?>">
                                             <?= html_escape($dist['district_no']); ?>
                                         </option>
                                     <?php endforeach; ?>
                                 <?php endif; ?>
                             </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Admin Cost %</label>
                                <select name="wage_percentage" id="wage_percentage" class="form-select" required>
                                    <option value="">Percentage</option>
                                    <option value="2.5">2.5%</option>
                                    <option value="3">3%</option>                          
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Subsidy Cost</label>
                                <input type="text" name="subsidy_cost" class="form-control" step="0.01" placeholder="Subsidy Cost" required autocomplete="OFF">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Admin Cost</label>
                                <input type="number" name="admin_cost" class="form-control" step="0.01" placeholder="Admin" required autocomplete="OFF">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">GPAI (Source of Funds)</label>
                                <input type="text" name="gpai_info" class="form-control" placeholder="GPAI Funding" oninput="this.value = this.value.toUpperCase();" autocomplete="OFF">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">WAGE (Source of Funds)</label>
                                <input type="text" name="wage_info" class="form-control" placeholder="Wage Funding" oninput="this.value = this.value.toUpperCase();" autocomplete="OFF">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">LGU Classification</label>
                                <input type="text" name="implementation_classification" class="form-control" placeholder="LGU Class" oninput="this.value = this.value.toUpperCase();" title="Put N/A if the municipality is VARIOUS" autocomplete="OFF" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Proponent</label>
                                <select name="imp_proponent" id="imp_proponent" class="form-select"  style="width: 100%;" required>
                                    <option value="" selected disabled>-- Select or type Proponent --</option>
                                    <?php if (!empty($proponents)): ?>
                                        <?php foreach ($proponents as $prop): ?>
                                            <option value="<?= html_escape($prop['proponent_id']); ?>">
                                                <?= html_escape($prop['proponent_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <i><font color="red" size="2px">* Proponent not listed? <a href="<?= site_url('ADL/proponent_encode'); ?>">Add it here</a></i></font>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Sponsor</label>
                                <input type="text" name="imp_sponsor" class="form-control" placeholder="Sponsor" autocomplete="OFF" oninput="this.value = this.value.toUpperCase();" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Overall Remarks</label>
                                <input type="text" name="remarks" class="form-control" placeholder="Remarks" oninput="this.value = this.value.toUpperCase();" autocomplete="OFF">
                            </div>
                        </div>

                        <input type="hidden" name="encoded_date" value="<?= date('Y-m-d'); ?>">

                        <div class="col-12 mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="<?= site_url('adl/ADL_encode'); ?>" class="btn btn-light border px-4">Cancel</a>
                            <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Save Transaction Record
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <div class="modal fade" id="duplicateTransactionModal" tabindex="-1" aria-labelledby="duplicateTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header">
                            <h5 class="modal-title" id="duplicateTransactionModalLabel">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Duplicate Reference Number Found
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <p class="mb-0">The Implementation Reference Number <strong id="modalDuplicateRefNo"></strong> is already recorded in the database. Please use a unique Reference Number.</p>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {$('#adl_no').select2({
            theme: 'bootstrap-5',
            placeholder: '-Select or Type ADL-',
            allowClear: true
        });

        $(document).on('click', '#sidebarToggle', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {$('#sidebar').toggleClass('show-mobile');
            } else {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            }
        });

        $('#transactionForm').on('submit', function (e) {
            e.preventDefault();

            if (isTargetExceeded || isSubsidyExceeded) {
                alert('Please resolve the limit validation errors before submitting.');
                return;
            }

            const $form =$(this);
            const $submitBtn =$('#submitBtn');
            const refNoInput = $('input[name="implementation_reference_no"]').val().trim();

            $.ajax({
                url: "<?= site_url('adl/check_duplicate_transaction'); ?>",
                type: "GET",
                data: { implementation_reference_no: refNoInput },
                dataType: "json",
                success: function (response) {
                    if (response.exists) {
                        $('#modalDuplicateRefNo').text(refNoInput);
                        const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateTransactionModal'));
                        duplicateModal.show();
                    } else {
                        $submitBtn.prop('disabled', true);$submitBtn.html(`
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Saving Record...
                        `);
                        $form[0].submit();
                    }
                },
                error: function () {
                    alert('Error checking database for duplicate records. Please try again.');
                }
            });
        });

        $(document).on('change', '#implementation_province', function () {
            const provCode = $(this).val();
            const $cityMunSelect =$('#implementation_area');
            const $brgySelect =$('#implementation_brgy');

            $brgySelect.prop('disabled', true).html('<option value="" selected disabled>Select Municipality First</option>');

            if (provCode) {
                $cityMunSelect.prop('disabled', true).html('<option value="">Loading areas...</option>');

                $.ajax({
                    url: "<?= site_url('adl/get_municipalities_by_province'); ?>",
                    type: "GET",
                    data: { provCode: provCode },
                    dataType: "json",
                    success: function (data) {
                        $cityMunSelect.empty().append('<option value="" selected disabled>Select City/Municipality</option>');
                        if (data && data.length > 0) {
                            $.each(data, function (index, item) {
                                const munCode = item.citymunCode || item.cityCode; 
                                const munDesc = item.citymunDesc || item.cityDesc;

                                $cityMunSelect.append('<option value="' + munCode + '">' + munDesc + '</option>');
                            });
                            $cityMunSelect.prop('disabled', false);
                        } else {
                            $cityMunSelect.append('<option value="" disabled>No implementation areas found</option>');
                        }
                    },
                    error: function () {
                        $cityMunSelect.prop('disabled', false).html('<option value="" disabled>Error loading data</option>');
                    }
                });
            } else {
                $cityMunSelect.prop('disabled', true).html('<option value="" selected disabled>Select Province First</option>');
            }
        });

        $(document).on('change', '#implementation_area', function () {
            const citymunCode = $(this).val();
            const $brgySelect =$('#implementation_brgy');

            if (citymunCode) {
                $brgySelect.prop('disabled', true).html('<option value="">Loading barangays...</option>');

                $.ajax({
                    url: "<?= site_url('adl/get_barangays_by_municipality'); ?>",
                    type: "GET",
                    data: { citymunCode: citymunCode },
                    dataType: "json",
                    success: function (data) {
                        $brgySelect.empty().append('<option value="" selected disabled>Select Barangay</option>');
                        if (data && data.length > 0) {
                            $.each(data, function (index, item) {
                                $brgySelect.append('<option value="' + item.brgyCode + '">' + item.brgyDesc + '</option>');
                            });
                            $brgySelect.prop('disabled', false);
                        } else {
                            $brgySelect.append('<option value="" disabled>No barangays found</option>');
                        }
                    },
                    error: function () {
                        $brgySelect.prop('disabled', false).html('<option value="" disabled>Error loading data</option>');
                    }
                });
            } else {
                $brgySelect.prop('disabled', true).html('<option value="" selected disabled>Select Municipality First</option>');
            }
        });
    });

    $(document).ready(function() {$('#imp_proponent').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select or type Proponent --',
            allowClear: true
        });

        $('#implementation_district').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select District --',
            allowClear: true
        });
    });




    let cachedTotalAdlSubsidy = 0;

    function formatSubsidyVal(val) {
        let num = parseFloat(val.toString().replace(/,/g, '')) || 0;
        if (num === 0) return '0k';
        if (num < 1000000) {
            return Math.round(num / 1000) + 'k';
        } else {
            let mVal = num / 1000000;
            // Rounds to 3 decimals, then strips trailing zeros (e.g., 2.000 becomes 2, 2.100 becomes 2.1)
            let formatted = parseFloat(mVal.toFixed(3)).toString();
            return formatted + 'M';
        }
    }

    function generateReferenceNo() {
        const adlNo = $('#adl_no').val();
        const province = $('#implementation_province').val() ? $('#implementation_province option:selected').text().trim() : '';
        const municipality = $('#implementation_area').val() ? $('#implementation_area option:selected').text().trim() : '';
        const district = $('#implementation_district').val() ? $('#implementation_district option:selected').text().trim() : '';
        const transactionSubsidy = $('input[name="subsidy_cost"]').val() || '0';

        if (adlNo) {
            $.ajax({
                url: "<?= site_url('adl/get_generated_reference_no'); ?>",
                type: "GET",
                data: {
                    adl_no: adlNo,
                    province: province,
                    municipality: municipality,
                    district: district
                },
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        cachedTotalAdlSubsidy = response.total_adl_subsidy || 0;
                        const formattedTransSubsidy = formatSubsidyVal(transactionSubsidy);
                        const formattedTotalSubsidy = formatSubsidyVal(cachedTotalAdlSubsidy);
                        
                        // Combine base reference with smart decimal-trimmed subsidy costs
                        const finalRefNo = response.ref_no + '_' + formattedTransSubsidy + '_' + formattedTotalSubsidy;
                        $('#implementation_reference_no').val(finalRefNo);
                    }
                }
            });
        } else {
            $('#implementation_reference_no').val('');
        }
    }

    $(document).on('change', '#adl_no, #implementation_province, #implementation_area, #implementation_district', function() {
        generateReferenceNo();
    });

    $(document).on('input', 'input[name="subsidy_cost"]', function() {
        generateReferenceNo();
    });








    let isTargetExceeded = false;
    let isSubsidyExceeded = false;

    function updateFormSubmitState() {
        const $submitBtn =$('#submitBtn');
        if (isTargetExceeded || isSubsidyExceeded) {
            $submitBtn.prop('disabled', true);
        } else {
            $submitBtn.prop('disabled', false);
        }
    }

    let targetLimitData = { max: 0, encoded: 0, remaining: 0 };

    function validateTargetLimit() {
        const adlNo = $('#adl_no').val();
        const currentInputTarget = parseFloat($('input[name="target"]').val()) || 0;
        const $noticeContainer = $('#targetNoticeContainer');
        const $noticeText = $('#targetNoticeText');

        if (!adlNo) {
            if ($noticeContainer.length) $noticeContainer.hide();
            isTargetExceeded = false;
            updateFormSubmitState();
            return;
        }

        $.ajax({
            url: "<?= site_url('adl/check_adl_target_limit'); ?>",
            type: "GET",
            data: { adl_no: adlNo },
            dataType: "json",
            success: function (response) {
                if (response.status && response.data) {
                    targetLimitData.max = response.data.max_target;
                    targetLimitData.encoded = response.data.encoded_target;
                    targetLimitData.remaining = response.data.remaining_target;

                    if ((targetLimitData.encoded + currentInputTarget) > targetLimitData.max) {
                        if ($noticeText.length) {
                            $noticeText.html(`<strong>Exceeded Target Limit!</strong> Max Allowed: <b>${targetLimitData.max}</b> | Already Encoded: <b>${targetLimitData.encoded}</b> | Remaining: <b>${targetLimitData.remaining}</b>.`);
                        }
                        if ($noticeContainer.length) $noticeContainer.show();
                        isTargetExceeded = true;
                    } else {
                        if ($noticeContainer.length) $noticeContainer.hide();
                        isTargetExceeded = false;
                    }
                    updateFormSubmitState();
                }
            }
        });
    }

    let subsidyLimitData = { max: 0, encoded: 0, remaining: 0 };

    function validateSubsidyLimit() {
        const adlNo = $('#adl_no').val();
        const rawSubsidy = $('input[name="subsidy_cost"]').val() || "0";
        const currentInputSubsidy = parseFloat(rawSubsidy.toString().replace(/,/g, '')) || 0;
        const $noticeContainer = $('#subsidyNoticeContainer');
        const $noticeText = $('#subsidyNoticeText');

        if (!adlNo) {
            if ($noticeContainer.length) $noticeContainer.hide();
            isSubsidyExceeded = false;
            updateFormSubmitState();
            return;
        }

        $.ajax({
            url: "<?= site_url('adl/check_adl_subsidy_limit'); ?>",
            type: "GET",
            data: { adl_no: adlNo },
            dataType: "json",
            success: function (response) {
                if (response.status && response.data) {
                    subsidyLimitData.max = response.data.max_subsidy;
                    subsidyLimitData.encoded = response.data.encoded_subsidy;
                    subsidyLimitData.remaining = response.data.remaining_subsidy;

                    if ((subsidyLimitData.encoded + currentInputSubsidy) > subsidyLimitData.max) {
                        if ($noticeText.length) {
                            $noticeText.html(`<strong>Exceeded Subsidy Limit!</strong> Max Allowed: <b>₱${subsidyLimitData.max.toLocaleString(undefined, {minimumFractionDigits: 2})}</b> | Already Encoded: <b>₱${subsidyLimitData.encoded.toLocaleString(undefined, {minimumFractionDigits: 2})}</b>.`);
                        }
                        if ($noticeContainer.length) $noticeContainer.show();
                        isSubsidyExceeded = true;
                    } else {
                        if ($noticeContainer.length) $noticeContainer.hide();
                        isSubsidyExceeded = false;
                    }
                    updateFormSubmitState();
                }
            }
        });
    }

    $('#fund_source').select2({
    theme: 'bootstrap-5',
    placeholder: '-- Select Fund Source --',
    allowClear: true
});

    $(document).on('change', '#adl_no', function () {
        validateTargetLimit();
        validateSubsidyLimit();
    });

    $(document).on('input', 'input[name="target"]', function () {
        validateTargetLimit();
    });

    $(document).on('input', 'input[name="subsidy_cost"]', function () {
        validateSubsidyLimit();
    });

    document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
});

document.addEventListener('keydown', function (e) {
    // Disable F12
    if (e.key === 'F12') {
        e.preventDefault();
    }
    
    // Disable Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, Ctrl+U
    if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) {
        e.preventDefault();
    }
    
    if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
        e.preventDefault();
    }
});
</script>
</body>

</html>