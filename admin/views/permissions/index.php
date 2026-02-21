<?php
session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';

authCheck(['super_admin']);

/**
 * Lấy danh sách permissions
 */
$sql = "SELECT id, code, description, created_at 
        FROM permissions 
        ORDER BY code ASC";
$result = $conn->query($sql);

$permissions = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý quyền</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            margin-bottom: 10px;
        }
        .top-bar {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background: #f4f6f8;
        }
        tr:nth-child(even) {
            background: #fafafa;
        }
        .action a {
            margin-right: 8px;
            text-decoration: none;
            color: #0066cc;
        }
        .action a:hover {
            text-decoration: underline;
        }
        .empty {
            text-align: center;
            color: #777;
            padding: 20px;
        }
    </style>
</head>
<body>

<h2>Danh sách quyền (Permissions)</h2>

<div class="top-bar">
    <a href="create.php">➕ Thêm quyền mới</a>
</div>

<table>
    <tr>
        <th>#</th>
        <th>Mã quyền</th>
        <th>Mô tả</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
    </tr>

    <?php if (empty($permissions)): ?>
        <tr>
            <td colspan="5" class="empty">Chưa có quyền nào</td>
        </tr>
    <?php else: ?>
        <?php foreach ($permissions as $i => $p): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><b><?= htmlspecialchars($p['code']) ?></b></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><?= $p['created_at'] ?></td>
            <td class="action">
                <a href="edit.php?id=<?= $p['id'] ?>">Sửa</a>
                <a href="delete.php?id=<?= $p['id'] ?>"
                   onclick="return confirm('Xóa quyền này?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<br>
<a href="../roles/index.php">⬅ Quay lại quản lý vai trò</a>

</body>
</html>
