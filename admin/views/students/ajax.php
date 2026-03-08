<?php
require_once __DIR__ . '/../../../config/config.php';

// AJAX endpoint — session-only guard (no redirect)
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['authenticated']) || !in_array($_SESSION['role'] ?? '', ['super_admin', 'content_admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$limit   = 10;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $limit;
$keyword = trim($_GET['keyword'] ?? '');

$params = [];
$where  = '';

if ($keyword !== '') {
    $where  = "WHERE s.student_code LIKE ? OR CONCAT(s.last_name,' ',s.first_name) LIKE ?";
    $like   = '%' . $keyword . '%';
    $params = [$like, $like];
}

// Count
$countRes   = $conn->execute_query("SELECT COUNT(*) AS total FROM students s $where", $params);
$total      = $countRes->fetch_assoc()['total'];
$totalPages = (int)ceil($total / $limit);

// Data
$dataParams   = array_merge($params, [$limit, $offset]);
$result       = $conn->execute_query(
    "SELECT student_id, student_code, first_name, last_name, email, phone
     FROM students s $where ORDER BY student_id DESC LIMIT ? OFFSET ?",
    $dataParams
);

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode([
    'students'    => $students,
    'totalPages'  => $totalPages,
    'currentPage' => $page,
]);

$keyword = $_GET['keyword'] ?? '';

$page = max($page, 1);
$offset = ($page - 1) * $limit;

$where = '';
if ($keyword !== '') {
    $kw = $conn->real_escape_string($keyword);
    $where = "WHERE student_code LIKE '%$kw%' OR full_name LIKE '%$kw%'";
}

/* Tổng số bản ghi */
$totalSql = "SELECT COUNT(*) AS total FROM students $where";
$totalResult = $conn->query($totalSql);
$total = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

/* Lấy dữ liệu */
$sql = "SELECT * FROM students $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode([
    'students' => $students,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);
