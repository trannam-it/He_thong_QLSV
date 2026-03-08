<?php
/**
 * Accountant Module - Entry Point (Kế toán)
 *
 * URL Pattern:
 *   /web_QLSV/accountant/                        → Dashboard
 *   /web_QLSV/accountant/?page=tuition           → Quản lý học phí
 *   /web_QLSV/accountant/?page=scholarships      → Quản lý học bổng
 *   /web_QLSV/accountant/?page=students          → Danh sách sinh viên
 *   /web_QLSV/accountant/?page=reports           → Báo cáo tài chính
 *   /web_QLSV/accountant/?page=profile           → Hồ sơ cá nhân
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = dirname(__DIR__);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';

// Guard: chỉ accountant được vào
AppRouter::guardModule(['accountant']);

require_once __DIR__ . '/Router.php';

$router = new AccountantRouter($base);
$router->dispatch();
