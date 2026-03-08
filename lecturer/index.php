<?php
/**
 * Lecturer Module - Entry Point (Giảng viên)
 *
 * URL Pattern:
 *   /web_QLSV/lecturer/                  → Dashboard
 *   /web_QLSV/lecturer/?page=profile     → Hồ sơ cá nhân
 *   /web_QLSV/lecturer/?page=classes     → Lớp học phụ trách
 *   /web_QLSV/lecturer/?page=grades      → Quản lý điểm
 *   /web_QLSV/lecturer/?page=attendance  → Điểm danh
 *   /web_QLSV/lecturer/?page=register    → Đăng ký giảng dạy
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = dirname(__DIR__);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';

// Guard: chỉ teacher được vào
AppRouter::guardModule(['teacher']);

require_once __DIR__ . '/Router.php';

$router = new LecturerRouter($base);
$router->dispatch();
