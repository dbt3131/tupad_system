<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit User - CI3 CRUD</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
<div class="row justify-content-center">
<div class="col-sm-10 col-md-7 col-lg-6">
<div class="card shadow-sm">
<div class="card-body p-4">
<h3 class="mb-4">Edit User</h3>

<?php if ($this->session->flashdata('error')): ?>
<div class="alert alert-danger"><?= html_escape($this->session->flashdata('error')); ?></div>
<?php endif; ?>

<?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>

<form method="post" action="<?= site_url('users/edit/' . $user->id); ?>">
<div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="<?= html_escape($user->name); ?>" required></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= html_escape($user->email); ?>" required></div>
<div class="mb-3">
<label class="form-label">New Password</label>
<input type="password" name="password" class="form-control" minlength="6">
<div class="form-text">Leave blank to keep the current password.</div>
</div>
<button class="btn btn-primary">Update</button>
<a href="<?= site_url('users'); ?>" class="btn btn-secondary">Back</a>
</form>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
