<?php
/**
 * Admin Module - Entry Point
 * Redirect đến Dashboard sau khi kiểm tra role
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/AppRouter.php';

// Chỉ cho phép super_admin và content_admin truy cập module này
AppRouter::guardModule(['super_admin', 'content_admin']);

header('Location: ' . BASE_URL . '/admin/Dashboard.php');
exit;
