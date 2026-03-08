<?php

// =========================
//  DATABASE CONFIG
// =========================

$servername = "localhost";
$username   = "root";
$password   = "";
$db_name    = "database_qlsv";

// ⚠ kiểm tra port của Laragon MySQL rồi sửa ở đây:
$port       = 3307; // Nếu Laragon của bạn là 3307 thì sửa lại 3307

// =========================
//  BASE URL CONFIG
// =========================

// Đảm bảo không define lại BASE_URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '/web_QLSV');
}

// =========================
//  CONNECT DATABASE
// =========================
$conn = new mysqli($servername, $username, $password, $db_name, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

?>