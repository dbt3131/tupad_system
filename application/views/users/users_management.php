<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- DataTables CSS Bootstrap 5 Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
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
            overflow-x: hidden;
        } 

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
        }

        .card-header-custom {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.5rem 1.75rem;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 0.5rem;
            margin-bottom: 0 !important;
        }

        .table thead th {
            background-color: #f8fafc !important;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-top: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
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
            padding: 1rem 1.25rem;
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

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--card-border);
            border-radius: 0.5rem;
            padding: 0.4rem 2rem 0.4rem 0.75rem;
            background-color: #f8fafc;
        }

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
    </style>
</head>

<body>

    <?php $this->load->view('templates/navbar'); ?>

    <div id="main-content">
        <?php $this->load->view('templates/sidebar'); ?>

        <main class="p-3 p-md-4 flex-grow-1">
            <div class="container-fluid px-0">
                <div class="row mb-4 align-items-center">
                    <div class="col-sm-8">
                        <h3 class="fw-bold mb-1 text-dark">User Management</h3>
                        <p class="text-muted mb-0">Manage system users, view descriptions, and update statuses and assignments.</p>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <span class="badge bg-white text-primary border px-3 py-2 shadow-sm rounded-pill fw-semibold">
                            <i class="bi bi-people me-1"></i> System Users
                        </span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('success'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $this->session->flashdata('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Users Table Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Division</th>
                                        <th>Activated</th>
                                        <th>Role</th>
                                        <th>Assigned Province</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($users)): ?>
                                        <?php foreach($users as $user): ?>
                                            <tr>
                                                <td><?= $user->id; ?></td>
                                                <td class="fw-semibold"><?= htmlspecialchars($user->reg_fname . ' ' . $user->reg_lname); ?></td>
                                                <td><?= htmlspecialchars($user->email); ?></td>
                                                <td><?= htmlspecialchars($user->position_description ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($user->office_description ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($user->division_description ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge <?= $user->activated == 1 ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?= $user->activated == 1 ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td><?= $user->role == 1 ? 'Admin' : 'User'; ?></td>
                                                <td><?= htmlspecialchars($user->provDesc ?? 'None'); ?></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-primary edit-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editUserModal"
                                                            data-id="<?= $user->id; ?>"
                                                            data-activated="<?= $user->activated; ?>"
                                                            data-role="<?= $user->role; ?>"
                                                            data-prov="<?= $user->assigned_prov; ?>">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
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

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editUserForm" method="POST" action="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="editUserModalLabel">Update User Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Activated Field -->
                            <div class="mb-3">
                                <label for="modal_activated" class="form-label fw-semibold">Activated Status</label>
                                <select class="form-select" id="modal_activated" name="activated" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <!-- Role Field -->
                            <div class="mb-3">
                                <label for="modal_role" class="form-label fw-semibold">Role</label>
                                <select class="form-select" id="modal_role" name="role" required>
                                    <option value="0">User</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>

                            <!-- Assigned Province Field -->
                            <div class="mb-3">
                                <label for="modal_assigned_prov" class="form-label fw-semibold">Assigned Province</label>
                                <select class="form-select" id="modal_assigned_prov" name="assigned_prov" required>
                                    <option value="">Select Province</option>
                                    <?php if(!empty($provinces)): ?>
                                        <?php foreach($provinces as $prov): ?>
                                            <option value="<?= $prov->provCode; ?>"><?= htmlspecialchars($prov->provDesc); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer class="bg-white border-top p-3 text-center text-muted small no-print">
            &copy; 2026 Department of Labor and Employment. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS & BS5 Setup -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function () {
        $('#usersTable').DataTable();
        
        $('.dataTables_filter input').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('form-select form-select-sm');

        // Populate modal fields on edit button click
        $(document).on('click', '.edit-btn', function () {
            var userId = $(this).data('id');
            var activated = $(this).data('activated');
            var role = $(this).data('role');
            var prov = $(this).data('prov');

            $('#modal_activated').val(activated);
            $('#modal_role').val(role);
            $('#modal_assigned_prov').val(prov);
            
            // Set dynamic form action URL
            $('#editUserForm').attr('action', '<?= site_url("user_management/update/"); ?>' + userId);
        });
    });
    </script>
</body>

</html>