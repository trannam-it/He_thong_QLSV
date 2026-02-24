<?php
session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../controllers/RoleController.php';

authCheck(['super_admin']);

$controller = new RoleController($conn);
$controller->index();
?>


<h2>Danh sách vai trò</h2>
<a href="/web_QLSV/admin/roles_create.php">+ Thêm role</a>


<table>
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Hành động</th>
    </tr>
    <?php foreach ($roles as $r): ?>
    <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td>
            <a href="roles_edit.php?id=<?= $r['id'] ?>">Sửa</a>
            <a href="roles_assign.php?id=<?= $r['id'] ?>">Gán quyền</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
