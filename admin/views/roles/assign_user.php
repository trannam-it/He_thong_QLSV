<?php
if (empty($role) || !is_array($role)) {
    header('Location: /web_QLSV/admin/roles.php');
    exit;
}
$roleName = htmlspecialchars($role['name'] ?? '');
$roleId   = (int)($role['id'] ?? 0);
$keyword  = htmlspecialchars($_GET['q'] ?? '');
?>

<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../../../includes/alert.php'; ?>

<div class="content-wrapper">
    <div class="d-flex align-items-center mb-3 gap-2">
        <a href="roles.php" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <h4 class="mb-0">Gán user cho vai trò: <strong><?= $roleName ?></strong></h4>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- ===== Cột trái: Danh sách user đang có role ===== -->
        <div class="col-md-6">
            <div class="content-card h-100">
                <h6 class="mb-3">
                    <i class="bi bi-people-fill text-primary me-1"></i>
                    User đang có vai trò này
                    <span class="badge bg-secondary ms-1"><?= count($assignedUsers) ?></span>
                </h6>

                <?php if (empty($assignedUsers)): ?>
                    <p class="text-muted fst-italic">Chưa có user nào được gán vai trò này.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignedUsers as $u): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-person me-1 text-muted"></i>
                                            <?= htmlspecialchars($u['username']) ?>
                                        </td>
                                        <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                                        <td class="text-end">
                                            <form method="POST"
                                                  action="roles.php?action=assign_user&id=<?= $roleId ?>"
                                                  onsubmit="return confirm('Xóa vai trò khỏi user này?')">
                                                <input type="hidden" name="sub_action" value="remove">
                                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-lg"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== Cột phải: Tìm & gán user ===== -->
        <div class="col-md-6">
            <div class="content-card h-100">
                <h6 class="mb-3">
                    <i class="bi bi-search text-success me-1"></i>
                    Tìm kiếm &amp; gán user
                </h6>

                <!-- Form tìm kiếm -->
                <form method="GET" action="roles.php" class="mb-3">
                    <input type="hidden" name="action" value="assign_user">
                    <input type="hidden" name="id" value="<?= $roleId ?>">
                    <div class="input-group">
                        <input type="text"
                               name="q"
                               class="form-control"
                               placeholder="Nhập username hoặc email..."
                               value="<?= $keyword ?>"
                               autofocus>
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                        <?php if ($keyword !== ''): ?>
                            <a href="roles.php?action=assign_user&id=<?= $roleId ?>"
                               class="btn btn-outline-secondary" title="Xóa tìm kiếm">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if ($keyword === ''): ?>
                    <p class="text-muted fst-italic">Nhập từ khóa để tìm kiếm user.</p>
                <?php elseif (empty($searchResults)): ?>
                    <div class="alert alert-info">
                        Không tìm thấy user nào phù hợp (hoặc tất cả đã được gán vai trò này).
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th class="text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $u): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-person me-1 text-muted"></i>
                                            <?= htmlspecialchars($u['username']) ?>
                                        </td>
                                        <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                                        <td class="text-end">
                                            <form method="POST"
                                                  action="roles.php?action=assign_user&id=<?= $roleId ?>">
                                                <input type="hidden" name="sub_action" value="add">
                                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="bi bi-plus-lg"></i> Gán
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-1">
                        Hiển thị tối đa 30 kết quả. Hãy thu hẹp từ khóa nếu cần.
                    </p>
                <?php endif; ?>

            </div>
        </div>

    </div><!-- row -->
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
