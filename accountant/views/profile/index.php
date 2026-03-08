<?php $pageTitle = 'Hồ sơ cá nhân'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-person-circle me-2"></i>Hồ sơ cá nhân</h1>
    <div class="page-breadcrumb"><a href="<?= accUrl() ?>">Trang chủ</a> / Hồ sơ</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-person me-2"></i>Thông tin tài khoản</h5></div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-4">
                    <tr><th>Tài khoản:</th><td><?= htmlspecialchars($user['username']) ?></td></tr>
                    <tr><th>Email:</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
                    <tr><th>Vai trò:</th><td><span class="badge" style="background:#fd7e14;">Kế toán</span></td></tr>
                </table>
                <form method="post" action="<?= accUrl('profile') ?>">
                    <input type="hidden" name="action" value="update_email">
                    <div class="mb-3"><label class="form-label">Cập nhật Email</label><input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>"></div>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Lưu</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-key me-2"></i>Đổi mật khẩu</h5></div>
            <div class="card-body">
                <form method="post" action="<?= accUrl('profile') ?>">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3"><label class="form-label">Mật khẩu hiện tại</label><input type="password" name="old_password" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Mật khẩu mới</label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
                    <div class="mb-3"><label class="form-label">Xác nhận</label><input type="password" name="confirm_password" class="form-control" required></div>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-shield-lock me-1"></i>Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
