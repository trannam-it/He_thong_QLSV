<?php
require '../config/config.php';
require '../includes/load_data.php';
require '../includes/auth_check.php';
require '../includes/dashboard_helper.php';

authCheck(['student']);

$userId = $_SESSION['user_id'];
$student = getStudentOverview($conn, $userId);

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    
    // Validate
    $errors = [];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Số điện thoại phải là 10 chữ số";
    }
    
    if (empty($errors)) {
        $sql = "UPDATE students SET phone = ?, email = ? WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $phone, $email, $student['student_id']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật thông tin thành công!";
            header("Location: student_profile.php");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật";
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode('. ', $errors);
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Student</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>asset/css/custom-style.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>asset/images/mortarboard.png">
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
            <a href="student.php" class="sidebar-menu-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-divider"></div>
        <h6 class="sidebar-heading">Thông tin</h6>

        <li class="sidebar-menu-item">
            <a href="student_profile.php" class="sidebar-menu-link active">
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
        </div>

        <div class="topbar-right">
            <div class="user-profile">
                <img src="<?= BASE_URL ?>asset/images/default-avatar.png" alt="Avatar" class="user-avatar" onerror="this.src='https://via.placeholder.com/40'">
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
            <h1 class="page-title">Thông tin cá nhân</h1>
            <div class="page-breadcrumb">
                <a href="student.php">Trang chủ</a> / Thông tin cá nhân
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Profile Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-person-badge me-2"></i>Thông tin sinh viên
                        </h5>
                    </div>
                    <div class="content-card-body">
                        <form method="POST" action="student_profile.php">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Mã sinh viên</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['student_code']) ?>" disabled>
                                    <small class="text-muted">Không thể thay đổi</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['full_name']) ?>" disabled>
                                    <small class="text-muted">Không thể thay đổi</small>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Giới tính</label>
                                    <input type="text" class="form-control" value="<?= $student['gender'] === 'Male' ? 'Nam' : 'Nữ' ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ngày sinh</label>
                                    <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($student['birth_date'])) ?>" disabled>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label"><span class="text-danger">*</span> Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><span class="text-danger">*</span> Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>" pattern="[0-9]{10}" required>
                                    <small class="text-muted">10 chữ số</small>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Khoa</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['faculty_name']) ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lớp</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['base_class_name'] ?? 'Chưa phân lớp') ?>" disabled>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Trạng thái</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['status']) ?>" disabled>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="student.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="content-card">
                    <div class="content-card-header">
                        <h5 class="content-card-title">
                            <i class="bi bi-info-circle me-2"></i>Hướng dẫn
                        </h5>
                    </div>
                    <div class="content-card-body">
                        <h6>Thông tin có thể chỉnh sửa:</h6>
                        <ul class="mb-3">
                            <li>Email</li>
                            <li>Số điện thoại</li>
                        </ul>

                        <h6>Lưu ý:</h6>
                        <ul>
                            <li>Email phải đúng định dạng</li>
                            <li>Số điện thoại phải là 10 chữ số</li>
                            <li>Các thông tin khác cần liên hệ phòng đào tạo để thay đổi</li>
                        </ul>

                        <div class="alert alert-info mt-3">
                            <small>
                                <i class="bi bi-telephone me-1"></i>
                                Liên hệ phòng đào tạo: <strong>024.xxxx.xxxx</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('toggleSidebar')?.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
        document.querySelector('.main-content').classList.toggle('expanded');
    });
</script>

</body>
</html>
