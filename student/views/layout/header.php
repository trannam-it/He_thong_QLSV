<?php
/**
 * Student Layout – Header & Sidebar (Bootstrap 5.3)
 */

$currentPage = $currentPage ?? StudentRouter::getPageName();

function navActive(string $page, string $currentPage): string {
    return ($page === $currentPage) ? 'active' : '';
}

function sUrl(string $page = ''): string {
    if (!defined('BASE_URL')) return '/student/' . ($page ? '?page=' . urlencode($page) : '');
    return BASE_URL . '/student/' . ($page ? '?page=' . urlencode($page) : '');
}

$studentName = htmlspecialchars($student['full_name'] ?? 'Sinh viên');
$studentCode = htmlspecialchars($student['student_code'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Student Portal') ?> – Student Portal</title>

    <!-- Bootstrap 5.3.3 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/custom-style.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/public/asset/images/mortarboard.png">

    <?php if (isset($extraCss)): ?>
    <style><?= $extraCss ?></style>
    <?php endif; ?>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <a href="<?= sUrl() ?>" class="sidebar-brand">
        <i class="bi bi-mortarboard-fill"></i>
        <span>Student Portal</span>
    </a>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li class="sidebar-menu-item">
            <a href="<?= sUrl() ?>" class="sidebar-menu-link <?= navActive('student', $currentPage) ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Thông tin</h6>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('profile') ?>" class="sidebar-menu-link <?= navActive('student_profile', $currentPage) ?>">
                <i class="bi bi-person-circle"></i>
                <span>Thông tin cá nhân</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('grades') ?>" class="sidebar-menu-link <?= navActive('student_grades', $currentPage) ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Kết quả học tập</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Học tập</h6>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('enrollment') ?>" class="sidebar-menu-link <?= navActive('student_enrollment', $currentPage) ?>">
                <i class="bi bi-journal-plus"></i>
                <span>Đăng ký học phần</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('schedule') ?>" class="sidebar-menu-link <?= navActive('student_schedule', $currentPage) ?>">
                <i class="bi bi-calendar3"></i>
                <span>Lịch học</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('attendance') ?>" class="sidebar-menu-link <?= navActive('student_attendance', $currentPage) ?>">
                <i class="bi bi-calendar-check"></i>
                <span>Điểm danh</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Dịch vụ</h6>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('tuition') ?>" class="sidebar-menu-link <?= navActive('student_tuition', $currentPage) ?>">
                <i class="bi bi-cash-coin"></i>
                <span>Học phí</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('scholarship') ?>" class="sidebar-menu-link <?= navActive('student_scholarship', $currentPage) ?>">
                <i class="bi bi-trophy"></i>
                <span>Học bổng</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('dormitory') ?>" class="sidebar-menu-link <?= navActive('student_dormitory', $currentPage) ?>">
                <i class="bi bi-building"></i>
                <span>Ký túc xá</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= sUrl('library') ?>" class="sidebar-menu-link <?= navActive('student_library', $currentPage) ?>">
                <i class="bi bi-book"></i>
                <span>Thư viện</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>

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
            <button id="toggleSidebar" class="toggle-sidebar-btn" title="Thu/mở sidebar">
                <i class="bi bi-list"></i>
            </button>
            <?php if (!empty($pageTitle)): ?>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= sUrl() ?>" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></li>
                </ol>
            </nav>
            <?php endif; ?>
        </div>
        <div class="topbar-right">
            <!-- Notification placeholder -->
            <button class="topbar-icon-btn" title="Thông báo">
                <i class="bi bi-bell"></i>
            </button>

            <!-- User dropdown -->
            <div class="dropdown">
                <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                     id="userDropdown" style="cursor:pointer;">
                    <div style="width:38px;height:38px;border-radius:50%;
                                background:linear-gradient(135deg,#4e73df,#224abe);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-weight:700;font-size:1rem;border:2px solid #4e73df;">
                        <?= mb_strtoupper(mb_substr($studentName, 0, 1)) ?>
                    </div>
                    <div class="user-info d-none d-sm-block">
                        <span class="user-name"><?= $studentName ?></span>
                        <span class="user-role"><?= $studentCode ? $studentCode . ' · ' : '' ?>Sinh viên</span>
                    </div>
                    <i class="bi bi-chevron-down d-none d-sm-inline small ms-1"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1">
                    <li>
                        <a class="dropdown-item" href="<?= sUrl('profile') ?>">
                            <i class="bi bi-person-circle me-2 text-primary"></i>Thông tin cá nhân
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= sUrl('profile') ?>?tab=password">
                            <i class="bi bi-key me-2 text-warning"></i>Đổi mật khẩu
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

    <!-- Alert messages -->
    <?php if (!empty($_SESSION['success'])): ?>
    <div class="mx-4 mt-3">
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
    <div class="mx-4 mt-3">
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
