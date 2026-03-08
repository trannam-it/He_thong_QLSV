<?php
/**
 * Admin Roles - Entry Point
 * Dùng permission-based access thay vì role-based cứng
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/libs/Auth.php';
require_once __DIR__ . '/libs/PermissionManager.php';

$auth = new Auth($conn);

// Yêu cầu đã đăng nhập
$auth->requireAuthWeb();

// Kiểm tra quyền xem roles (chấp nhận cả super_admin)
if (!$auth->isSuperAdmin() && !$auth->hasPermission('roles.view')) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập trang Quản lý Vai trò.';
    header('Location: /web_QLSV/admin/Dashboard.php');
    exit;
}

// Load view mới
include __DIR__ . '/views/roles/index.php';