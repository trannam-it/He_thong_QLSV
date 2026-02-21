<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../config/config.php';
require '../includes/load_data.php';
require '../includes/auth_check.php';
require '../includes/dashboard_helper.php';

authCheck(['student']);

// Lấy thông tin sinh viên
$userId = $_SESSION['user_id'];
$student = getStudentOverview($conn, $userId);

if (!$student || $student['student_id'] == 0) {
    die('<h1>Lỗi: Không tìm thấy thông tin sinh viên</h1><p>User ID: '.$userId.'</p><p>Vui lòng kiểm tra database students có user_id = '.$userId.'</p>');
}

$gpaData = calculateStudentGPA($conn, $student['student_id']);
$schedule = getStudentSchedule($conn, $student['student_id'], 5);
$grades = getStudentGrades($conn, $student['student_id'], 10);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <!-- Bootstrap 5.3.3 Local -->
    <link rel="stylesheet" href="asset/css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons 1.11.3 Local -->
    <link rel="stylesheet" href="asset/css/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="asset/css/custom-style.css">
    <link rel="shortcut icon" href="asset/images/mortarboard.png">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <a href="student.php" class="sidebar-brand">
        <i class="bi bi-mortarboard-fill"></i>
        <span>Student Portal</span>
    </a>

    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="student.php" class="sidebar-menu-link active">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Thông tin</h6>

        <li class="sidebar-menu-item">
            <a href="student_profile.php" class="sidebar-menu-link">
                <i class="bi bi-person-circle"></i>
                <span>Thông tin cá nhân</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="student_grades.php" class="sidebar-menu-link">
                <i class="bi bi-file-text"></i>
                <span>Kết quả học tập</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Học tập</h6>

        <li class="sidebar-menu-item">
            <a href="student_schedule.php" class="sidebar-menu-link">
                <i class="bi bi-calendar3"></i>
                <span>Lịch học</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="student_attendance.php" class="sidebar-menu-link">
                <i class="bi bi-calendar-check"></i>
                <span>Điểm danh</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Khác</h6>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-cash-coin"></i>
                <span>Học phí</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-trophy"></i>
                <span>Học bổng</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-building"></i>
                <span>Ký túc xá</span>
            </a>
        </li>

        <li class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <i class="bi bi-book"></i>
                <span>Thư viện</span>
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
                <span class="badge">3</span>
            </button>

            <div class="user-profile">
                <img src="asset/images/default-avatar.png" alt="Avatar" class="user-avatar" onerror="this.src='https://via.placeholder.com/40'">
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($student['full_name']) ?></span>
                    <span class="user-role">Sinh viên</span>
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
                <a href="student.php">Trang chủ</a> / Dashboard
            </div>
        </div>

        <!-- Welcome Alert -->
        <div class="alert alert-primary mb-4">
            <h5><i class="bi bi-emoji-smile me-2"></i>Xin chào, <?= htmlspecialchars($student['full_name']) ?>!</h5>
            <p class="mb-0">Chào mừng bạn đến với hệ thống quản lý sinh viên. Hôm nay là <?= date('d/m/Y') ?>.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card bg-gradient-primary text-white">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-card-label">GPA Tích lũy</div>
                                <div class="stat-card-value"><?= number_format($gpaData['gpa'] ?? 0, 2) ?></div>
                            </div>
                            <i class="bi bi-graph-up stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card bg-gradient-success text-white">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-card-label">Tín chỉ tích lũy</div>
                                <div class="stat-card-value"><?= $gpaData['total_credits'] ?? 0 ?></div>
                            </div>
                            <i class="bi bi-journal-check stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card bg-gradient-info text-white">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-card-label">Môn đã học</div>
                                <div class="stat-card-value"><?= $gpaData['total_courses'] ?? 0 ?></div>
                            </div>
                            <i class="bi bi-book stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card bg-gradient-warning text-white">
                    <div class="stat-card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-card-label">Trạng thái</div>
                                <div class="stat-card-value" style="font-size: 1.2rem;"><?= $student['status'] ?></div>
                            </div>
                            <i class="bi bi-person-check stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Lịch học -->
            <div class="col-lg-6 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-calendar3 me-2"></i>Lịch học hiện tại
                        </h5>
                        <a href="student_schedule.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </div>
                    <div class="content-card-body">
                        <?php if (!empty($schedule)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($schedule as $class): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($class['subject_name']) ?></h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($class['lecturer_name']) ?>
                                                    <span class="ms-2">
                                                        <i class="bi bi-calendar me-1"></i><?= formatSemester($class['semester']) ?> - <?= $class['year'] ?>
                                                    </span>
                                                </small>
                                            </div>
                                            <span class="badge <?= getStatusBadgeClass($class['status']) ?>">
                                                <?= formatEnrollmentStatus($class['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                                <p class="mt-2">Chưa có lịch học</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Kết quả học tập gần nhất -->
            <div class="col-lg-6 mb-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-file-text me-2"></i>Kết quả học tập
                        </h5>
                        <a href="student_grades.php" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                    </div>
                    <div class="content-card-body">
                        <?php if (!empty($grades)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Môn học</th>
                                            <th class="text-center">TC</th>
                                            <th class="text-center">Điểm</th>
                                            <th class="text-center">Chữ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($grades, 0, 5) as $grade): ?>
                                            <tr>
                                                <td>
                                                    <small class="d-block text-muted"><?= htmlspecialchars($grade['subject_code']) ?></small>
                                                    <?= htmlspecialchars($grade['subject_name']) ?>
                                                </td>
                                                <td class="text-center"><?= $grade['credit_hours'] ?></td>
                                                <td class="text-center">
                                                    <strong><?= $grade['score'] ? number_format($grade['score'], 1) : 'N/A' ?></strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?= $grade['grade_letter'] ?? 'N/A' ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-clipboard-x" style="font-size: 3rem;"></i>
                                <p class="mt-2">Chưa có kết quả học tập</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin cá nhân -->
        <div class="row">
            <div class="col-lg-12">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-person-circle me-2"></i>Thông tin cá nhân
                        </h5>
                        <a href="student_profile.php" class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
                    </div>
                    <div class="content-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Mã sinh viên:</th>
                                        <td><?= htmlspecialchars($student['student_code']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Họ và tên:</th>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Giới tính:</th>
                                        <td><?= $student['gender'] === 'Male' ? 'Nam' : 'Nữ' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ngày sinh:</th>
                                        <td><?= date('d/m/Y', strtotime($student['birth_date'])) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="150">Email:</th>
                                        <td><?= htmlspecialchars($student['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Số điện thoại:</th>
                                        <td><?= htmlspecialchars($student['phone']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Khoa:</th>
                                        <td><?= htmlspecialchars($student['faculty_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Lớp:</th>
                                        <td><?= htmlspecialchars($student['base_class_name'] ?? 'Chưa phân lớp') ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS Local -->
<script src="asset/css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle sidebar
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
        document.querySelector('.main-content').classList.toggle('expanded');
    });
</script>

</body>
</html>
