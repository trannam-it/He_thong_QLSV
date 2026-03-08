<?php
/**
 * Student Module - Entry Point (Sinh viên)
 *
 * URL Pattern:
 *   /web_QLSV/student/                        → Dashboard
 *   /web_QLSV/student/?page=profile           → Hồ sơ cá nhân
 *   /web_QLSV/student/?page=grades            → Xem điểm
 *   /web_QLSV/student/?page=enrollment        → Đăng ký học phần
 *   /web_QLSV/student/?page=schedule          → Thời khóa biểu
 *   /web_QLSV/student/?page=attendance        → Điểm danh
 *   /web_QLSV/student/?page=tuition           → Học phí
 *   /web_QLSV/student/?page=scholarship       → Học bổng
 *   /web_QLSV/student/?page=library           → Thư viện
 *   /web_QLSV/student/?page=dormitory         → Ký túc xá
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = dirname(__DIR__);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';

// Guard: chỉ student được vào
AppRouter::guardModule(['student']);

require_once __DIR__ . '/Router.php';

$router = new StudentRouter($base);
$router->dispatch();
