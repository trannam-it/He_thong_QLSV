<?php
/**
 * Admin Layout Header - Dynamic Sidebar theo Permissions
 * Sidebar menu items hiển thị dựa trên quyền của user hiện tại
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo Auth và PermissionManager đã được load
if (!class_exists('Auth')) {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once dirname(__DIR__, 3) . '/includes/auth_check.php';
    require_once dirname(__DIR__, 2) . '/libs/PermissionManager.php';
    require_once dirname(__DIR__, 2) . '/libs/Auth.php';
}
if (!isset($auth)) {
    $auth = new Auth($conn);
}

$user = $_SESSION['user'] ?? null;
$isSuperAdmin = $auth->isSuperAdmin();

/**
 * Helper: Kiểm tra có nên hiện menu item không
 */
function canSeeMenu(Auth $auth, array $permissions): bool {
    if ($auth->isSuperAdmin()) return true;
    foreach ($permissions as $p) {
        if ($auth->hasPermission($p)) return true;
    }
    return false;
}

$self = $_SERVER['PHP_SELF'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin Dashboard') ?></title>

    <!-- Bootstrap 5.3.3 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <link rel="shortcut icon" href="<?= defined('BASE_URL') ? BASE_URL : '/web_QLSV' ?>/public/asset/images/mortarboard.png">
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '/web_QLSV' ?>/public/asset/css/custom-style.css">
    <style>
        /* ========== SIDEBAR MENU GROUPS ========== */
        .sidebar-section-label {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px 4px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            margin-top: 6px;
        }
        .sidebar-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.12);
        }

        /* ========== PERMISSION BADGE IN SIDEBAR ========== */
        .sidebar-menu-link .perm-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 0.6rem;
            margin-left: auto;
        }

        /* ========== DROPDOWN STYLES ========== */
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
        .dropdown-menu.active { display: block; }
        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .dropdown-menu a:hover { background: #f0f4ff; }
        .dropdown-divider { height: 1px; background: #eee; margin: 10px 0; }

        /* ========== ACTIVITY STYLES ========== */
        .activity-list { display: flex; flex-direction: column; gap: 15px; }
        .activity-item {
            display: flex; gap: 15px; padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .activity-item:last-child { border-bottom: none; padding-bottom: 0; }
        .activity-icon {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; flex-shrink: 0;
        }
        .activity-icon i { font-size: 1.2rem; }
        .activity-text { margin: 0; font-weight: 600; }
        .activity-time { font-size: 0.85rem; }
    </style>
</head>
<body>

<!-- ==================== SIDEBAR ==================== -->
<nav class="sidebar">
    <a href="/web_QLSV/admin/Dashboard.php" class="sidebar-brand">
        <i class="bi bi-mortarboard"></i>
        <span>ADMIN PANEL</span>
    </a>

    <ul class="sidebar-menu">

        <!-- DASHBOARD - luôn hiện -->
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'Dashboard')!==false?'active':'' ?>"
               href="/web_QLSV/admin/Dashboard.php">
                <i class="bi bi-house-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- ===== NHÓM: QUẢN LÝ NGƯỜI DÙNG ===== -->
        <?php if (canSeeMenu($auth, ['users.view','students.view','lecturers.view'])): ?>
        <div class="sidebar-section-label"><span>NGƯỜI DÙNG</span></div>

        <?php if (canSeeMenu($auth, ['users.view'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'users')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/users/index.php">
                <i class="bi bi-people"></i>
                <span>Tài khoản</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['students.view','students.create'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'students')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/students/index.php">
                <i class="bi bi-mortarboard"></i>
                <span>Sinh viên</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['lecturers.view','lecturers.create'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'lecturers')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/lecturers/index.php">
                <i class="bi bi-person-badge"></i>
                <span>Giảng viên</span>
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <!-- ===== NHÓM: QUẢN LÝ ĐÀO TẠO ===== -->
        <?php if (canSeeMenu($auth, ['faculties.view','subjects.view','classes.view','base_classes.view','semesters.view'])): ?>
        <div class="sidebar-section-label"><span>ĐÀO TẠO</span></div>

        <?php if (canSeeMenu($auth, ['faculties.view','faculties.create'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'faculty')!==false?'active':'' ?>"
               href="/web_QLSV/admin/faculty.php">
                <i class="bi bi-building"></i>
                <span>Khoa / Ngành</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['base_classes.view','base_classes.manage'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'/classes/')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/classes/index.php">
                <i class="bi bi-diagram-3"></i>
                <span>Lớp cơ sở</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['subjects.view','subjects.create'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'subjects')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/subjects/index.php">
                <i class="bi bi-book"></i>
                <span>Môn học</span>
            </a>
        </li>
        <?php endif; ?>


      
        <?php endif; ?>

        <!-- ===== NHÓM: ĐIỂM & BÁO CÁO ===== -->
        <?php if (canSeeMenu($auth, ['grades.view','grades.view_all','grades.enter','attendance.view','reports.view'])): ?>
        <div class="sidebar-section-label"><span>BÁO CÁO</span></div>


        <?php if (canSeeMenu($auth, ['reports.view','reports.student','reports.grade','reports.finance'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'reports')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/reports/index.php">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Báo cáo & Thống kê</span>
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

    

        <!-- ===== NHÓM: QUẢN TRỊ HỆ THỐNG ===== -->
        <?php if (canSeeMenu($auth, ['roles.view','permissions.view','system.audit_logs','system.backup','system.settings']) || $isSuperAdmin): ?>
        <div class="sidebar-section-label"><span>HỆ THỐNG</span></div>

        <?php if (canSeeMenu($auth, ['roles.view','roles.assign_perm','permissions.view'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'roles')!==false?'active':'' ?>"
               href="/web_QLSV/admin/roles.php">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Vai trò & Quyền hạn</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['permissions.view','permissions.create'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'permissions')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/permissions/index.php">
                <i class="bi bi-key-fill"></i>
                <span>Danh sách Quyền hạn</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['roles.assign_perm'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'permission_matrix')!==false?'active':'' ?>"
               href="/web_QLSV/admin/permission_matrix.php">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>Ma trận Phân quyền</span>
            </a>
        </li>
        <?php endif; ?>


        <?php if (canSeeMenu($auth, ['system.audit_logs'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'audit')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/audit_logs/index.php">
                <i class="bi bi-journal-text"></i>
                <span>Nhật ký hệ thống</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['system.settings'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'settings')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/settings/index.php">
                <i class="bi bi-gear-fill"></i>
                <span>Cài đặt</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (canSeeMenu($auth, ['system.backup'])): ?>
        <li class="sidebar-menu-item">
            <a class="sidebar-menu-link <?= strpos($self,'backup')!==false?'active':'' ?>"
               href="/web_QLSV/admin/views/backup/index.php">
                <i class="bi bi-cloud-arrow-up"></i>
                <span>Sao lưu & Khôi phục</span>
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

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
<!-- ==================== END SIDEBAR ==================== -->

<!-- ==================== MAIN CONTENT ==================== -->
<div class="main-content">

    <!-- ============== TOPBAR ============== -->
    <div class="topbar">
        <div class="topbar-left">
            <button id="toggleSidebar" class="toggle-sidebar-btn">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Tìm kiếm...">
            </div>
        </div>

        <div class="topbar-right">
            <button class="topbar-icon-btn">
                <i class="bi bi-bell"></i>
                <span class="badge">3</span>
            </button>

            <?php if (!empty($_SESSION['user'])): 
                $u = $_SESSION['user'];
            ?>
            <div class="dropdown">
                <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                    <img class="user-avatar"
                         src="https://ui-avatars.com/api/?name=<?= urlencode($u['fullname'] ?? 'Admin') ?>&background=4e73df&color=fff"
                         alt="avatar">
                    <div class="user-info d-none d-sm-block">
                        <span class="user-name"><?= htmlspecialchars($u['fullname'] ?? 'Admin') ?></span>
                        <span class="user-role"><?= htmlspecialchars($u['role_name'] ?? '') ?></span>
                    </div>
                    <i class="bi bi-chevron-down d-none d-sm-inline small ms-1"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1">
                    <li>
                        <a class="dropdown-item" href="/web_QLSV/admin/views/info/index.php">
                            <i class="bi bi-person me-2 text-primary"></i> Hồ sơ cá nhân
                        </a>
                    </li>
                    <?php if ($isSuperAdmin || $auth->hasPermission('roles.view')): ?>
                    <li>
                        <a class="dropdown-item" href="/web_QLSV/admin/roles.php">
                            <i class="bi bi-shield-lock me-2 text-warning"></i> Quản lý phân quyền
                        </a>
                    </li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="/web_QLSV/public/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- ============ END TOPBAR ============ -->