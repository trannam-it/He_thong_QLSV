<?php include __DIR__ . '/../layout/header.php'; ?>

<?php include __DIR__ . '/../../../includes/alert.php'; ?>

<div class="content-card">
    <h4>Sửa vai trò</h4>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Mã vai trò</label>
            <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($form['code'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Tên vai trò</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($form['name'] ?? '') ?>" required>
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="roles.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>