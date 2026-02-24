<?php
require_once '../config/db.php'; // chỉnh đúng đường dẫn DB

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
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
