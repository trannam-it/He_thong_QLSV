<?php
$currentPage = $currentPage ?? LibrarianRouter::getPageName();

function lNavActive(string $page, string $currentPage): string {
    return ($page === $currentPage) ? 'active' : '';
}
function lUrl(string $page = ''): string {
    if (!defined('BASE_URL')) return '/librarian/' . ($page ? '?page=' . urlencode($page) : '');
    return BASE_URL . '/librarian/' . ($page ? '?page=' . urlencode($page) : '');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Library') ?> – Thư viện</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/custom-style.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/public/asset/images/mortarboard.png">
    <?php if (isset($extraCss)): ?><style><?= $extraCss ?></style><?php endif; ?>
</head>
<body>

<aside class="sidebar">
    <a href="<?= lUrl() ?>" class="sidebar-brand" style="background:linear-gradient(135deg,#20c997,#0f8060);">
        <i class="bi bi-book-fill"></i>
        <span>Thư viện</span>
    </a>

    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="<?= lUrl() ?>" class="sidebar-menu-link <?= lNavActive('librarian', $currentPage) ?>">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Quản lý Sách</h6>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('books') ?>" class="sidebar-menu-link <?= lNavActive('librarian_books', $currentPage) ?>">
                <i class="bi bi-book"></i><span>Danh mục Sách</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('borrows') ?>" class="sidebar-menu-link <?= lNavActive('librarian_borrows', $currentPage) ?>">
                <i class="bi bi-arrow-left-right"></i><span>Mượn / Trả Sách</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Bạn đọc</h6>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('members') ?>" class="sidebar-menu-link <?= lNavActive('librarian_members', $currentPage) ?>">
                <i class="bi bi-people"></i><span>Danh sách Bạn đọc</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Hệ thống</h6>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('reports') ?>" class="sidebar-menu-link <?= lNavActive('librarian_reports', $currentPage) ?>">
                <i class="bi bi-graph-up"></i><span>Thống kê báo cáo</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="<?= lUrl('profile') ?>" class="sidebar-menu-link <?= lNavActive('librarian_profile', $currentPage) ?>">
                <i class="bi bi-person-circle"></i><span>Hồ sơ cá nhân</span>
            </a>
        </li>
    </ul>
</aside>

<div class="main-content">
    <nav class="topbar">
        <div class="topbar-left">
            <button id="toggleSidebar" class="toggle-sidebar-btn"><i class="bi bi-list"></i></button>
        </div>
        <div class="topbar-right">
            <div class="user-profile">
                <div style="width:40px;height:40px;border-radius:50%;background:#e9ecef;
                            display:flex;align-items:center;justify-content:center;color:#20c997;
                            border:2px solid #20c997;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($user['username'] ?? 'Librarian') ?></span>
                    <span class="user-role">Thủ thư</span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/public/logout.php" class="topbar-icon-btn" title="Đăng xuất">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </nav>
    <div class="content-wrapper">
