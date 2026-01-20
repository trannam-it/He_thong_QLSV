<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/audit_log.php';

// Chỉ admin mới được tạo user
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$username = trim($_POST['username']);
$password = $_POST['password'];
$role     = $_POST['role'];

// Hash mật khẩu
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("
    INSERT INTO users (username, password, role)
    VALUES (?, ?, ?)
");
$stmt->bind_param("sss", $username, $passwordHash, $role);
$stmt->execute();

$user_id = $conn->insert_id;

/* 🔐 AUDIT LOG – CREATE USER */
writeAuditLog(
    $conn,
    $_SESSION['user_id'],
    $_SESSION['username'],
    'CREATE_USER',
    'users',
    $user_id,
    null,
    json_encode([
        'username' => $username,
        'role' => $role
    ])
);

header("Location: ../public/user_manage.php");
exit;
