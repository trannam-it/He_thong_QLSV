<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../includes/load_data.php';
require __DIR__ . '/../includes/auth_check.php';
require __DIR__ . '/../includes/dashboard_helper.php';

authCheck(['teacher']);

// Lấy thông tin giảng viên
$userId = $_SESSION['user_id'];
$lecturer = getLecturerOverview($conn, $userId);

if (!$lecturer || $lecturer['lecturer_id'] == 0) {
    die('<h1>Lỗi: Không tìm thấy thông tin giảng viên</h1><p>User ID: '.$userId.'</p><p>Vui lòng kiểm tra database lecturers có user_id = '.$userId.'</p>');
}

$totalClasses = countLecturerClasses($conn, $lecturer['lecturer_id']);
$totalStudents = countLecturerStudents($conn, $lecturer['lecturer_id']);
$classes = getLecturerClasses($conn, $lecturer['lecturer_id'], 10);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>

    <!-- Bootstrap 5.3.3 Local -->
    <link rel="stylesheet" href="asset/css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons 1.11.3 Local -->
    <link rel="stylesheet" href="asset/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="asset/css/custom-style.css">

</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <a href="#" class="sidebar-brand">
        <i class="bi bi-person-workspace"></i>
        <span>Teacher Portal</span>
    </a>

    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link active">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Giảng dạy</h6>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-book"></i>
                <span>Môn phụ trách</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-people"></i>
                <span>Danh sách SV</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-calendar-check"></i>
                <span>Điểm danh</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Quản lý điểm</h6>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-pencil-square"></i>
                <span>Nhập điểm</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-graph-up"></i>
                <span>Bảng điểm</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Main Content -->
<div class="main-content">

    <!-- Topbar -->
    <nav class="topbar">
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
                <span class="badge">2</span>
            </button>

            <div class="user-profile">
                <img src="asset/images/default-avatar.png" alt="Avatar" class="user-avatar" onerror="this.src='https://via.placeholder.com/40'">
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($lecturer['full_name']) ?></span>
                    <span class="user-role">Giảng viên</span>
                </div>
            </div>

            <a href="logout.php" class="topbar-icon-btn" title="Đăng xuất">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
            <div class="page-breadcrumb">
                <a href="#">Trang chủ</a> / Dashboard
            </div>
        </div>

        <!-- Welcome / Notice -->
        <div class="alert alert-primary mb-4">
            <h5><i class="bi bi-emoji-smile me-2"></i>Xin chào, <?= htmlspecialchars($lecturer['full_name']) ?>!</h5>
            <p class="mb-0">Chào mừng bạn đến với hệ thống quản lý giảng dạy. Hôm nay là <?= date('d/m/Y') ?>.</p>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-card bg-gradient-primary text-white">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-card-label">Số lớp đang dạy</div>
                                <div class="stat-card-value"><?= $totalClasses ?></div>
                            </div>
                            <i class="bi bi-book stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-card bg-gradient-success text-white">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-card-label">Tổng sinh viên</div>
                                <div class="stat-card-value"><?= $totalStudents ?></div>
                            </div>
                            <i class="bi bi-people stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stat-card bg-gradient-info text-white">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-card-label">Học vị</div>
                                <div class="stat-card-value" style="font-size: 1.5rem;"><?= htmlspecialchars($lecturer['degree']) ?></div>
                            </div>
                            <i class="bi bi-mortarboard stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Rows -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-book me-2"></i>Danh sách lớp giảng dạy
                        </h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </div>
                    <div class="content-card-body">
                        <?php if (!empty($classes)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Mã lớp</th>
                                            <th>Môn học</th>
                                            <th class="text-center">Tín chỉ</th>
                                            <th class="text-center">Học kỳ</th>
                                            <th class="text-center">Năm</th>
                                            <th class="text-center">SL SV</th>
                                            <th class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($classes as $class): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($class['class_code']) ?></strong>
                                                </td>
                                                <td><?= htmlspecialchars($class['subject_name']) ?></td>
                                                <td class="text-center"><?= $class['credit_hours'] ?></td>
                                                <td class="text-center"><?= formatSemester($class['semester']) ?></td>
                                                <td class="text-center"><?= $class['year'] ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?= $class['student_count'] ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="teacher_class_detail.php?id=<?= $class['class_id'] ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="teacher_grades.php?id=<?= $class['class_id'] ?>" class="btn btn-sm btn-outline-success" title="Nhập điểm">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="teacher_attendance.php?id=<?= $class['class_id'] ?>" class="btn btn-sm btn-outline-info" title="Điểm danh">
                                                        <i class="bi bi-calendar-check"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-3">Chưa có lớp học phân công</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks & Info -->
        <div class="row mt-4">
            <div class="col-lg-6 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-list-check me-2"></i>Công việc cần làm
                        </h5>
                    </div>
                    <div class="content-card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 border-0">
                                <div class="d-flex align-items-start">
                                    <input type="checkbox" class="form-check-input me-3 mt-1">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Nhập điểm giữa kỳ môn Lập trình Web</h6>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Hạn: 28/02/2026</small>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 border-0">
                                <div class="d-flex align-items-start">
                                    <input type="checkbox" class="form-check-input me-3 mt-1">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Chuẩn bị tài liệu bài giảng tuần 5</h6>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Hạn: 25/02/2026</small>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 border-0">
                                <div class="d-flex align-items-start">
                                    <input type="checkbox" class="form-check-input me-3 mt-1" checked>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-decoration-line-through text-muted">Điểm danh lớp CNTT101</h6>
                                        <small class="text-success"><i class="bi bi-check-circle me-1"></i>Đã hoàn thành</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-person-circle me-2"></i>Thông tin cá nhân
                        </h5>
                        <a href="teacher_profile.php" class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
                    </div>
                    <div class="content-card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="150">Mã giảng viên:</th>
                                <td><?= htmlspecialchars($lecturer['lecturer_code']) ?></td>
                            </tr>
                            <tr>
                                <th>Họ và tên:</th>
                                <td><?= htmlspecialchars($lecturer['full_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Học vị:</th>
                                <td>
                                    <span class="badge bg-success"><?= htmlspecialchars($lecturer['degree']) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= htmlspecialchars($lecturer['email']) ?></td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td><?= htmlspecialchars($lecturer['phone']) ?></td>
                            </tr>
                            <tr>
                                <th>Khoa:</th>
                                <td><?= htmlspecialchars($lecturer['faculty_name']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS Local -->
<script src="asset/css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="asset/js/main.js"></script>
<script>
    // Toggle sidebar
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
        document.querySelector('.main-content').classList.toggle('expanded');
    });
</script>

</body>
</html>
