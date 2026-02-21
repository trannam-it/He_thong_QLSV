<?php
// admin/views/layout/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Dashboard' ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

      <!-- Bootstrap 5 CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
 
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
   
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/asset/css/custom-style.css">
    <script src="<?= BASE_URL ?>/public/asset/js/main.js" defer></script>


</head>
<style>
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .activity-item {
            display: flex;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        .activity-icon i {
            font-size: 1.2rem;
        }
        .activity-content {
            flex: 1;
        }
        .activity-text {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }
        .activity-time {
            color: var(--secondary-color);
            font-size: 0.85rem;
        }
        .bg-success {
            background: var(--success-color);
        }
        .bg-info {
            background: var(--info-color);
        }
        .bg-warning {
            background: var(--warning-color);
        }
        .bg-danger {
            background: var(--danger-color);
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 10px 0;
            min-width: 200px;
            display: none;
            z-index: 1000;
        }
        .dropdown-menu.active {
            display: block;
        }
        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .dropdown-menu a:hover {
            background: var(--light-color);
        }
        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 10px 0;
        }
    </style>
<body>

<!-- ================= SIDEBAR ================= -->
<nav class="sidebar">
    <a href="/web_QLSV/admin/Dashboard.php" class="sidebar-brand">
        <i class="bi bi-mortarboard"></i>
        <span>ADMIN PANEL</span>
    </a>

    <ul class="sidebar-menu">

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'Dashboard')!==false?'active':'' ?>"
               href="/web_QLSV/admin/Dashboard.php">
                <i class="bi bi-house"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-heading">QUẢN LÝ NGƯỜI DÙNG</div>

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'users')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/users/index.php">
                <i class="bi bi-people"></i>
                <span>TÀI KHOẢN </span>
            </a>
        </li>

         <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'students')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/students/index.php">
                <i class="bi bi-mortarboard"></i>
                <span>Sinh viên</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'lecturers')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/lecturers/index.php">
                <i class="bi bi-person"></i>
                <span>Giảng viên</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">QUẢN LÝ Hệ thống</div>

         <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'audit')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/roles/index.php">
                <i class="bi bi-clipboard-check"></i>
                <span>PHÂN QUYỀN</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'audit')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/audit_logs/index.php">
                <i class="bi bi-clipboard-check"></i>
                <span>Nhật ký</span>
            </a>
        </li>

        
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'settings')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/settings/index.php">
                <i class="bi bi-gear"></i>
                <span>Cài đặt</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <div class="sidebar-heading">QUẢN LÝ Đào tạo</div>

         <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'faculty')!==false?'active':'' ?>"
               href="/web_QLSV/admin/faculty.php">
                <i class="bi bi-building"></i>
                <span> KHOA </span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'classes')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/classes/index.php">
                <i class="bi bi-diagram-3"></i>
                <span>Lớp cơ sở</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'subjects')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/subjects/index.php">
                <i class="bi bi-book"></i>
                <span> Môn Học</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'subjects')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/grades/index.php">
                <i class="bi bi-graph-up"></i>
                <span> ĐĂNG KÝ HỌC PHẦN</span>
            </a>
        </li>

         <div class="sidebar-divider"></div>
         <div class="sidebar-heading">ĐIỂM & BÁO CÁO</div>

         <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'subjects')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/grades/index.php">
                <i class="bi bi-graph-up"></i>
                <span> ĐIỂM SỐ</span>
            </a>
        </li>

         <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($_SERVER['PHP_SELF'],'subjects')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/reports/index.php">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span> BÁO CÁO & THỐNG KÊ</span>
            </a>
        </li>


        <div class="sidebar-divider"></div>

        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link text-danger"
               href="/web_QLSV/public/logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</nav>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content">

    <!-- ============== TOPBAR ============== -->
    <div class="topbar">

        <!-- LEFT -->
        <div class="topbar-left">
            <button id="toggleSidebar" class="toggle-sidebar-btn">
                <i class="bi bi-list"></i>
            </button>

            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Tìm kiếm...">
            </div>
        </div>

        <!-- RIGHT -->
        <div class="topbar-right">

            <button class="topbar-icon-btn">
                <i class="bi bi-bell"></i>
                <span class="badge">3</span>
            </button>

            <?php if (!empty($_SESSION['user'])): ?>
            <div class="dropdown">

                <div class="user-profile dropdown-toggle">
                    <img
                        class="user-avatar"
                        src="https://ui-avatars.com/api/?name=<?= urlencode($user['fullname']) ?>&background=4e73df&color=fff"
                        alt="avatar">
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($user['fullname']) ?></span>
                          <!-- <strong class="user-name"><?= htmlspecialchars($user['fullname']) ?></strong><br> -->
                        <span class="user-role"><?= htmlspecialchars($user['role_name']) ?></span>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </div>

                <div class="dropdown-menu">
                    <a href="/profile.php"><i class="bi bi-person"></i> Hồ sơ</a>
                    <a href="/change-password.php"><i class="bi bi-key"></i> Đổi mật khẩu</a>
                    <a href="#"><i class="bi bi-gear"></i> Cài đặt</a>
                    <div class="dropdown-divider"></div>
                    <a href="/web_QLSV/public/logout.php" class="text-danger">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                </div>

            </div>
            <?php endif; ?>

        </div>
    </div>
    <!-- ============ END TOPBAR ============ -->

   