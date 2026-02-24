<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';

authCheck(['student']);

$studentName = $_SESSION['fullname'] ?? 'Sinh viên';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= $pageTitle ?? 'Student Portal' ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    margin: 0;
    background: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}

.sidebar {
    width: 260px;
    height: 100vh;
    position: fixed;
    background: linear-gradient(180deg,#6a5acd,#5b2be0);
    color: white;
    padding: 25px 20px;
}

.sidebar h4 {
    font-weight: 600;
    margin-bottom: 30px;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #eaeaea;
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 8px;
    transition: 0.3s;
}

.sidebar a:hover,
.sidebar a.active {
    background: rgba(255,255,255,0.15);
    color: white;
}

.sidebar .section-title {
    font-size: 12px;
    opacity: 0.7;
    margin: 20px 0 10px;
    font-weight: 600;
}

.main-content {
    margin-left: 260px;
    padding: 30px;
}
</style>
</head>

<body>

<div class="sidebar">

    <h4><i class="bi bi-mortarboard"></i> Student Portal</h4>

    <a href="/../web_QLSV/public/student.php">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="section-title">THÔNG TIN</div>

    <a href="/../web_QLSV/public/student_profile.php">
        <i class="bi bi-person-circle"></i> Thông tin cá nhân
    </a>

    <a href="/../web_QLSV/public/student_grades.php">
        <i class="bi bi-file-earmark-text"></i> Kết quả học tập
    </a>

    <div class="section-title">HỌC TẬP</div>

    <a href="/../web_QLSV//public/student_schedule.php">
        <i class="bi bi-calendar-event"></i> Lịch học
    </a>

    <a href="/../web_QLSV/public/student_attendance.php">
        <i class="bi bi-check2-square"></i> Điểm danh
    </a>

    <div class="section-title">KHÁC</div>

    <a href="#">
        <i class="bi bi-cash-stack"></i> Học phí
    </a>

    <a href="#">
        <i class="bi bi-trophy"></i> Học bổng
    </a>

    <a href="#">
        <i class="bi bi-building"></i> Ký túc xá
    </a>

    <a href="#">
        <i class="bi bi-book"></i> Thư viện
    </a>

    <hr>

    <a href="/../web_QLSV/public/logout.php">
        <i class="bi bi-box-arrow-right"></i> Đăng xuất
    </a>

</div>

<div class="main-content">