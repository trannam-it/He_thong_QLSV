<?php
if (empty($role) || !is_array($role)) {
    header('Location: /web_QLSV/admin/roles.php');
    exit;
}

$permissions = $permissions ?? [];
$assigned = $assigned ?? [];

$roleName = $role['name'] ?? '';
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<?php include __DIR__ . '/../../../includes/alert.php'; ?>

<div class="content-card">
    <h4>Gán quyền cho: <?= htmlspecialchars($roleName) ?></h4>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($permissions as $p): ?>
            <label>
                <input type="checkbox" name="permissions[]"
                       value="<?= $p['id'] ?? 0 ?>"
                       <?= in_array($p['id'] ?? null, $assigned, true) ? 'checked' : '' ?>>
                <?= htmlspecialchars($p['name'] ?? $p['code'] ?? '') ?>
            </label>
            <br>
        <?php endforeach; ?>

        <button class="btn btn-primary mt-3">Lưu quyền</button>
        <a href="roles.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>