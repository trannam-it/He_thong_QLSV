<?php
/**
 * Academic Layout – Header & Sidebar (Bootstrap 5.3)
 */

// $currentPage = $currentPage ?? AcademicRouter::getPageName();

// function aNavActive(string $page, string $currentPage): string {
//     return ($page === $currentPage) ? 'active' : '';
// }
// function aUrl(string $page = ''): string {
//     if (!defined('BASE_URL')) return '/academic/' . ($page ? '?page=' . urlencode($page) : '');
//     return BASE_URL . '/academic/' . ($page ? '?page=' . urlencode($page) : '');
// }

// $userName = htmlspecialchars($user['username'] ?? 'Admin');

$currentPage = $currentPage ?? AcademicRouter::getPageName();

if (!function_exists('aNavActive')) {
    function aNavActive(string $page, string $currentPage): string {
        return ($page === $currentPage) ? 'active' : '';
    }
}

if (!function_exists('aUrl')) {
    function aUrl(string $page = ''): string {
        if (!defined('BASE_URL')) return '/academic/' . ($page ? '?page=' . urlencode($page) : '');
        return BASE_URL . '/academic/' . ($page ? '?page=' . urlencode($page) : '');
    }
}

$userName = htmlspecialchars($user['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Academic') ?> – Quản lý Đào tạo</title>

    <!-- Bootstrap 5.3.3 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/custom-style.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/public/asset/images/mortarboard.png">

    <style>
        .sidebar { background: linear-gradient(180deg,#0d6efd 0%,#0a58ca 100%) !important; }
        .sidebar-brand { background: linear-gradient(135deg,#0d6efd,#0a58ca) !important; }
    </style>

    <?php if (isset($extraCss)): ?>
    <style><?= $extraCss ?></style>
    <?php endif; ?>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <a href="<?= aUrl() ?>" class="sidebar-brand">
        <i class="bi bi-journal-bookmark-fill"></i>
        <span>Quản lý Đào tạo</span>
    </a>

    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="<?= aUrl() ?>" class="sidebar-menu-link <?= aNavActive('academic', $currentPage) ?>">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Chương trình học</h6>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('subjects') ?>" class="sidebar-menu-link <?= aNavActive('academic_subjects', $currentPage) ?>">
                <i class="bi bi-book-half"></i><span>Học phần</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('semesters') ?>" class="sidebar-menu-link <?= aNavActive('academic_semesters', $currentPage) ?>">
                <i class="bi bi-calendar3"></i><span>Học kỳ</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('classes') ?>" class="sidebar-menu-link <?= aNavActive('academic_classes', $currentPage) ?>">
                <i class="bi bi-people"></i><span>Lớp học phần</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('schedule') ?>" class="sidebar-menu-link <?= aNavActive('academic_schedule', $currentPage) ?>">
                <i class="bi bi-calendar-week"></i><span>Thời khóa biểu</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Sinh viên</h6>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('students') ?>" class="sidebar-menu-link <?= aNavActive('academic_students', $currentPage) ?>">
                <i class="bi bi-person-lines-fill"></i><span>Danh sách sinh viên</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('enrollments') ?>" class="sidebar-menu-link <?= aNavActive('academic_enrollments', $currentPage) ?>">
                <i class="bi bi-journal-plus"></i><span>Đăng ký học phần</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('enrollment_periods') ?>" class="sidebar-menu-link <?= aNavActive('academic_enroll_periods', $currentPage) ?>">
                <i class="bi bi-calendar-check"></i><span>Kỳ đăng ký</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('grades') ?>" class="sidebar-menu-link <?= aNavActive('academic_grades', $currentPage) ?>">
                <i class="bi bi-bar-chart"></i><span>Kết quả học tập</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Hệ thống</h6>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('reports') ?>" class="sidebar-menu-link <?= aNavActive('academic_reports', $currentPage) ?>">
                <i class="bi bi-file-earmark-bar-graph"></i><span>Báo cáo thống kê</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= aUrl('profile') ?>" class="sidebar-menu-link <?= aNavActive('academic_profile', $currentPage) ?>">
                <i class="bi bi-person-circle"></i><span>Hồ sơ cá nhân</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <li class="sidebar-menu-item">
            <a href="<?= BASE_URL ?>/public/logout.php" class="sidebar-menu-link text-danger">
                <i class="bi bi-box-arrow-right"></i><span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</aside>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">
    <nav class="topbar">
        <div class="topbar-left">
            <button id="toggleSidebar" class="toggle-sidebar-btn">
                <i class="bi bi-list"></i>
            </button>
            <?php if (!empty($pageTitle)): ?>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= aUrl() ?>" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></li>
                </ol>
            </nav>
            <?php endif; ?>
        </div>
        <div class="topbar-right">
            <div class="dropdown">
                <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                    <div style="width:38px;height:38px;border-radius:50%;
                                background:linear-gradient(135deg,#0d6efd,#0a58ca);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-weight:700;font-size:1rem;">
                        <?= mb_strtoupper(mb_substr($userName, 0, 1)) ?>
                    </div>
                    <div class="user-info d-none d-sm-block">
                        <span class="user-name"><?= $userName ?></span>
                        <span class="user-role">Quản lý Đào tạo</span>
                    </div>
                    <i class="bi bi-chevron-down d-none d-sm-inline small ms-1"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1">
                    <li>
                        <a class="dropdown-item" href="<?= aUrl('profile') ?>">
                            <i class="bi bi-person-circle me-2 text-primary"></i>Hồ sơ cá nhân
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/public/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['success'])): ?>
    <div class="mx-4 mt-3">
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
    <div class="mx-4 mt-3">
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
