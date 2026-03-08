<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';

authCheck(['super_admin']);

$pageTitle = 'Audit Log - Lich su he thong';

/* ===============================
   RBAC – chỉ Admin
================================ */
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     http_response_code(403);
//     exit("Bạn không có quyền truy cập");
// }

/* ===============================
   Filters
================================ */
$userFilter   = $_GET['username'] ?? '';
$actionFilter = $_GET['action'] ?? '';
$dateFrom     = $_GET['from'] ?? '';
$dateTo       = $_GET['to'] ?? '';

$allowedActions = ['LOGIN', 'INSERT', 'UPDATE', 'DELETE'];
if ($actionFilter !== '' && !in_array($actionFilter, $allowedActions, true)) {
    $actionFilter = '';
}

$sql = "
    SELECT audit_id, username, action, table_name, record_id,
           old_data, new_data, ip_address, created_at
    FROM audit_logs
    WHERE 1=1
";

$params = [];
$types  = "";

if ($userFilter !== '') {
    $sql .= " AND username LIKE ?";
    $params[] = "%$userFilter%";
    $types .= "s";
}

if ($actionFilter !== '') {
    $sql .= " AND action = ?";
    $params[] = $actionFilter;
    $types .= "s";
}

if ($dateFrom !== '') {
    $sql .= " AND created_at >= ?";
    $params[] = $dateFrom . " 00:00:00";
    $types .= "s";
}

if ($dateTo !== '') {
    $sql .= " AND created_at <= ?";
    $params[] = $dateTo . " 23:59:59";
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$logs = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    padding: 20px;
}

h1 {
    margin-bottom: 15px;
}

.filter-box, .table-box {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,.1);
    padding: 15px;
    margin-bottom: 20px;
}

.filter-box input, .filter-box select {
    padding: 6px;
    margin-right: 8px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1200px;
}

thead {
    background: #34495e;
    color: #fff;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

tr:hover {
    background: #f1f1f1;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    color: #fff;
}

.badge.LOGIN  { background: #2980b9; }
.badge.INSERT { background: #27ae60; }
.badge.UPDATE { background: #f39c12; }
.badge.DELETE { background: #c0392b; }
.badge.OTHER { background: #7f8c8d; }

.ip {
    font-family: monospace;
}

.detail {
    cursor: pointer;
    color: #2980b9;
    font-weight: bold;
}

pre {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 10px;
    border-radius: 6px;
    overflow-x: auto;
    font-size: 13px;
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
}

.modal-content {
    background: #fff;
    width: 70%;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
}

.close {
    float: right;
    cursor: pointer;
    font-weight: bold;
}
</style>

<script>
function showDetail(id) {
    document.getElementById('modal-' + id).style.display = 'block';
}
function closeDetail(id) {
    document.getElementById('modal-' + id).style.display = 'none';
}
</script>

<h1>📋 Lịch sử hoạt động hệ thống (Audit Log)</h1>

<div class="filter-box">
<form method="GET">
  <strong>🔍 Bộ lọc:</strong><br><br>
    User:
    <input type="text" name="username" value="<?= htmlspecialchars($userFilter) ?>">

    Action:
    <select name="action">
        <option value="">-- Tất cả --</option>
        <?php foreach ($allowedActions as $a): ?>
            <option value="<?= $a ?>" <?= $actionFilter==$a?'selected':'' ?>><?= $a ?></option>
        <?php endforeach; ?>
    </select>

    Từ ngày:
    <input type="date" name="from" value="<?= htmlspecialchars($dateFrom) ?>">

    Đến ngày:
    <input type="date" name="to" value="<?= htmlspecialchars($dateTo) ?>">

    <button>Lọc</button>
    <a href="index.php">Reset</a>
</form>
</div>

<div class="table-box">
<table>
<thead>
<tr>
    <th>Thời gian</th>
    <th>User</th>
    <th>Hành động</th>
    <th>Bảng</th>
    <th>Record</th>
    <th>IP</th>
    <th>Chi tiết</th>
</tr>
</thead>
<tbody>

<?php foreach ($logs as $row): ?>
<?php
    $rowAction = strtoupper((string)($row['action'] ?? ''));
    $badgeClass = in_array($rowAction, $allowedActions, true) ? $rowAction : 'OTHER';
?>
<tr>
    <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
    <td><span class="badge <?= $badgeClass ?>">
        <?= htmlspecialchars($rowAction) ?>
    </span></td>
    <td><?= htmlspecialchars($row['table_name'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['record_id'] ?? '') ?></td>
    <td class="ip"><?= htmlspecialchars($row['ip_address'] ?? '') ?></td>
    <td>
        <span class="detail" onclick="showDetail(<?= (int)($row['audit_id'] ?? 0) ?>)">Xem</span>
    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

<?php foreach ($logs as $row): ?>
    <div class="modal" id="modal-<?= (int)($row['audit_id'] ?? 0) ?>">
        <div class="modal-content">
            <span class="close" onclick="closeDetail(<?= (int)($row['audit_id'] ?? 0) ?>)">✖</span>

            <h3>🔍 Chi tiết Audit Log</h3>

            <b>Dữ liệu cũ:</b>
            <pre><?= htmlspecialchars($row['old_data'] ?? 'NULL') ?></pre>

            <b>Dữ liệu mới:</b>
            <pre><?= htmlspecialchars($row['new_data'] ?? 'NULL') ?></pre>

        </div>
    </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
</body>

</html>







