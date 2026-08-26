<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add User - CI3 CRUD</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
<div class="row justify-content-center">
<div class="col-sm-10 col-md-7 col-lg-6">
<div class="card shadow-sm">
<div class="card-body p-4">
<h3 class="mb-4">Add User</h3>
<?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
<form method="post" action="<?= site_url('users/create'); ?>">
<div class="mb-3"><label class="form-label">Employee No</label><input type="text" name="employee_no" class="form-control" value="<?= set_value('employee_no'); ?>" required></div>
<div class="mb-3"><label class="form-label">First Name</label><input type="text" name="fname" class="form-control" value="<?= set_value('fname'); ?>" required></div>
<div class="mb-3"><label class="form-label">Middle Name</label><input type="text" name="mname" class="form-control" value="<?= set_value('mname'); ?>"></div>
<div class="mb-3"><label class="form-label">Last Name</label><input type="text" name="lname" class="form-control" value="<?= set_value('lname'); ?>" required></div>
<div class="mb-3"><label class="form-label">Ext Name</label><input type="text" name="extname" class="form-control" value="<?= set_value('extname'); ?>" required></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= set_value('email'); ?>" required></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" minlength="6" required></div>
<div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirm" class="form-control" minlength="6" required></div>
<button class="btn btn-primary">Save</button>
<a href="<?= site_url('users'); ?>" class="btn btn-secondary">Back</a>
</form>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
