<?php
/**
 * Lecturer Layout – Header & Sidebar
 *
 * Biến $lecturer và $currentPage được truyền từ BaseLecturerController::render()
 * $currentPage: 'teacher', 'teacher_profile', 'teacher_classes', ...
 */

$currentPage = $currentPage ?? LecturerRouter::getPageName();

function lNavActive(string $page, string $currentPage): string
{
    return ($page === $currentPage) ? 'active' : '';
}

function lUrl(string $page = ''): string
{
    if (!defined('BASE_URL')) return '/lecturer/' . ($page ? '?page=' . urlencode($page) : '');
    return BASE_URL . '/lecturer/' . ($page ? '?page=' . urlencode($page) : '');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Teacher Portal') ?> – Teacher Portal</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/custom-style.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/public/asset/images/mortarboard.png">
    <?php if (isset($extraCss)): ?>
    <style><?= $extraCss ?></style>
    <?php endif; ?>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <a href="<?= lUrl() ?>" class="sidebar-brand">
        <i class="bi bi-person-workspace"></i>
        <span>Teacher Portal</span>
    </a>

    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="<?= lUrl() ?>" class="sidebar-menu-link <?= lNavActive('teacher', $currentPage) ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Cá nhân</h6>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('profile') ?>" class="sidebar-menu-link <?= lNavActive('teacher_profile', $currentPage) ?>">
                <i class="bi bi-person-circle"></i>
                <span>Thông tin cá nhân</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Giảng dạy</h6>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('classes') ?>" class="sidebar-menu-link <?= lNavActive('teacher_classes', $currentPage) ?>">
                <i class="bi bi-book-half"></i>
                <span>Lớp đang dạy</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('grades') ?>" class="sidebar-menu-link <?= lNavActive('teacher_grades', $currentPage) ?>">
                <i class="bi bi-pencil-square"></i>
                <span>Nhập điểm</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('attendance') ?>" class="sidebar-menu-link <?= lNavActive('teacher_attendance', $currentPage) ?>">
                <i class="bi bi-person-check"></i>
                <span>Điểm danh</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('register') ?>" class="sidebar-menu-link <?= lNavActive('teacher_register', $currentPage) ?>">
                <i class="bi bi-journal-plus"></i>
                <span>Đăng ký lớp dạy</span>
            </a>
        </li>
        <li class="sidebar-menu-item">
            <a href="<?= lUrl('class_registration') ?>" class="sidebar-menu-link <?= lNavActive('teacher_class_registration', $currentPage) ?>">
                <i class="bi bi-journal-plus"></i>
                <span>Đăng ký lớp dạy</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Tài khoản</h6>

        <li class="sidebar-menu-item">
            <a href="<?= BASE_URL ?>/public/logout.php" class="sidebar-menu-link text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</aside>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

    <!-- Topbar -->
    <nav class="topbar">
        <div class="topbar-left">
            <button id="toggleSidebar" class="toggle-sidebar-btn">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Tìm kiếm…">
            </div>
        </div>
        <div class="topbar-right">
            <?php if (!empty($pendingGrades) && $pendingGrades > 0): ?>
            <a href="<?= lUrl('grades') ?>" class="topbar-icon-btn" title="Điểm chờ nhập">
                <i class="bi bi-bell"></i>
                <span class="badge"><?= $pendingGrades ?></span>
            </a>
            <?php endif; ?>
            <div class="user-profile">
                <div style="width:40px;height:40px;border-radius:50%;background:#e9ecef;
                            display:flex;align-items:center;justify-content:center;color:#6c757d;
                            border:2px solid #dee2e6;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($lecturer['full_name']) ?></span>
                    <span class="user-role">Giảng viên</span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/public/logout.php" class="topbar-icon-btn" title="Đăng xuất">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </nav>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
