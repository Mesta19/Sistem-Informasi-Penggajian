<?php defined('BASE_PATH') || define('BASE_PATH', APPPATH); ?>
<?php include BASE_PATH . 'Views/templates/header.php'; ?>
<?php include BASE_PATH . 'Views/templates/sidebar.php'; ?>

<?php
$isEdit = isset($user);
$title = $isEdit ? 'Edit User' : 'Tambah User';
$actionUrl = $isEdit ? base_url('admin/user/update/' . $user['id_user']) : base_url('admin/user/store');
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    <a href="<?= base_url('admin/user') ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengguna</h6>
    </div>
    <div class="card-body">
        <form action="<?= $actionUrl ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password <?= $isEdit ? '<span class="text-muted">(kosongkan jika tidak ingin mengubah)</span>' : '' ?></label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" minlength="8" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" title="Password harus minimal 8 karakter dan berisi kombinasi huruf serta angka." style="border-top-right-radius: 0; border-bottom-right-radius: 0;" <?= $isEdit ? '' : 'required' ?>>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-top-right-radius: 0.35rem; border-bottom-right-radius: 0.35rem; border-left: 0; border-color: #d1d3e2;">
                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <small class="form-text text-muted">Minimal 8 karakter, wajib memiliki kombinasi huruf dan angka.</small>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="Admin" <?= (old('role', $user['role'] ?? '') === 'Admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="Karyawan" <?= (old('role', $user['role'] ?? '') === 'Karyawan') ? 'selected' : '' ?>>Karyawan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="aktif">Status Aktif</label>
                <select class="form-control" id="aktif" name="aktif">
                    <option value="1" <?= (old('aktif', $user['aktif'] ?? '1') == '1') ? 'selected' : '' ?>>Aktif</option>
                    <option value="0" <?= (old('aktif', $user['aktif'] ?? '1') == '0') ? 'selected' : '' ?>>Non-aktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/user') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var passwordField = document.getElementById('password');
            var icon = this.querySelector('i');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    }
});
</script>

<?php include BASE_PATH . 'Views/templates/footer.php'; ?>
