<?php
/**
 * Librarian Module - Entry Point (Thủ thư)
 *
 * URL Pattern:
 *   /web_QLSV/librarian/                      → Dashboard
 *   /web_QLSV/librarian/?page=books           → Quản lý sách
 *   /web_QLSV/librarian/?page=borrows         → Quản lý mượn sách
 *   /web_QLSV/librarian/?page=members         → Danh sách thành viên
 *   /web_QLSV/librarian/?page=reports         → Báo cáo
 *   /web_QLSV/librarian/?page=profile         → Hồ sơ cá nhân
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base = dirname(__DIR__);
require_once $base . '/config/config.php';
require_once $base . '/core/AppRouter.php';

// Guard: chỉ librarian được vào
AppRouter::guardModule(['librarian']);

require_once __DIR__ . '/Router.php';

$router = new LibrarianRouter($base);
$router->dispatch();
