<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/audit_log.php';

// Chỉ super_admin mới được tạo user
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    die("Access denied");
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$roleCode = trim($_POST['role'] ?? 'student'); // role code: super_admin|content_admin|teacher|student

if ($username === '' || $email === '' || $password === '') {
    die('Thiếu dữ liệu (username/email/password).');
}

// Hash mật khẩu
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// 1) Tạo user (đúng schema DB: users.password_hash)
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $passwordHash);
$stmt->execute();
$user_id = $conn->insert_id;
$stmt->close();

// 2) Gán role qua bảng roles + user_roles
$stmt = $conn->prepare("SELECT id FROM roles WHERE code = ? LIMIT 1");
$stmt->bind_param("s", $roleCode);
$stmt->execute();
$roleRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$roleRow) {
    die('Role không hợp lệ: ' . htmlspecialchars($roleCode));
}

$role_id = (int)$roleRow['id'];
$stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $role_id);
$stmt->execute();
$stmt->close();

/* 🔐 AUDIT LOG – CREATE USER */
writeAuditLog(
    $conn,
    (int)$_SESSION['user_id'],
    $_SESSION['username'],
    'CREATE_USER',
    'users',
    (int)$user_id,
    null,
    ['username' => $username, 'email' => $email, 'role' => $roleCode]
);

header("Location: ../public/home.php");
exit;
