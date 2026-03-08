<?php
/**
 * Accountant Layout – Header & Sidebar (Bootstrap 5.3)
 */
$currentPage = $currentPage ?? AccountantRouter::getPageName();

function accNavActive(string $page, string $currentPage): string {
    return ($page === $currentPage) ? 'active' : '';
}
function accUrl(string $page = ''): string {
    if (!defined('BASE_URL')) return '/accountant/' . ($page ? '?page=' . urlencode($page) : '');
    return BASE_URL . '/accountant/' . ($page ? '?page=' . urlencode($page) : '');
}

$userName = htmlspecialchars($user['username'] ?? 'Kế toán');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Kế toán') ?> – Kế toán</title>

    <!-- Bootstrap 5.3.3 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/custom-style.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/public/asset/images/mortarboard.png">

    <style>
        .sidebar { background: linear-gradient(180deg,#e65100 0%,#bf360c 100%) !important; }
        .sidebar-brand { background: linear-gradient(135deg,#fd7e14,#e55a00) !important; }
    </style>

    <?php if (isset($extraCss)): ?><style><?= $extraCss ?></style><?php endif; ?>
</head>
<body>

<aside class="sidebar">
    <a href="<?= accUrl() ?>" class="sidebar-brand">
        <i class="bi bi-cash-coin"></i>
        <span>Kế toán</span>
    </a>

    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="<?= accUrl() ?>" class="sidebar-menu-link <?= accNavActive('accountant', $currentPage) ?>">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Tài chính</h6>

        <li class="sidebar-menu-item">
            <a href="<?= accUrl('tuition') ?>" class="sidebar-menu-link <?= accNavActive('accountant_tuition', $currentPage) ?>">
                <i class="bi bi-receipt"></i><span>Học phí</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= accUrl('scholarships') ?>" class="sidebar-menu-link <?= accNavActive('accountant_scholarships', $currentPage) ?>">
                <i class="bi bi-trophy"></i><span>Học bổng</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Tra cứu</h6>

        <li class="sidebar-menu-item">
            <a href="<?= accUrl('students') ?>" class="sidebar-menu-link <?= accNavActive('accountant_students', $currentPage) ?>">
                <i class="bi bi-person-lines-fill"></i><span>Tài chính Sinh viên</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Hệ thống</h6>

        <li class="sidebar-menu-item">
            <a href="<?= accUrl('reports') ?>" class="sidebar-menu-link <?= accNavActive('accountant_reports', $currentPage) ?>">
                <i class="bi bi-file-earmark-bar-graph"></i><span>Báo cáo Tài chính</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= accUrl('profile') ?>" class="sidebar-menu-link <?= accNavActive('accountant_profile', $currentPage) ?>">
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

<div class="main-content">
    <nav class="topbar">
        <div class="topbar-left">
            <button id="toggleSidebar" class="toggle-sidebar-btn"><i class="bi bi-list"></i></button>
            <?php if (!empty($pageTitle)): ?>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= accUrl() ?>" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></li>
                </ol>
            </nav>
            <?php endif; ?>
        </div>
        <div class="topbar-right">
            <div class="dropdown">
                <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                    <div style="width:38px;height:38px;border-radius:50%;
                                background:linear-gradient(135deg,#fd7e14,#e55a00);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-weight:700;font-size:1rem;">
                        <?= mb_strtoupper(mb_substr($userName, 0, 1)) ?>
                    </div>
                    <div class="user-info d-none d-sm-block">
                        <span class="user-name"><?= $userName ?></span>
                        <span class="user-role">Kế toán</span>
                    </div>
                    <i class="bi bi-chevron-down d-none d-sm-inline small ms-1"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1">
                    <li>
                        <a class="dropdown-item" href="<?= accUrl('profile') ?>">
                            <i class="bi bi-person-circle me-2 text-warning"></i>Hồ sơ cá nhân
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

    <div class="content-wrapper">
