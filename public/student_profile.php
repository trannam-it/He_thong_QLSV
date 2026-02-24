<?php
// 1. Khai báo các file cần thiết (Sử dụng require_once để tránh lỗi lặp lại)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/dashboard_helper.php';

// 2. Kiểm tra quyền sinh viên
authCheck(['student']);

$userId = $_SESSION['user_id'];
// Lấy thông tin sinh viên từ database thông qua hàm helper
$student = getStudentOverview($conn, $userId);

$pageTitle = "Thông tin cá nhân";

// 3. XỬ LÝ CẬP NHẬT DỮ LIỆU KHI FORM ĐƯỢC SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $errors = [];

    // Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }

    // Kiểm tra định dạng số điện thoại (10 số)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Số điện thoại phải là 10 chữ số";
    }

    if (empty($errors)) {
        $sql = "UPDATE students SET phone = ?, email = ? WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $phone, $email, $student['student_id']);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Cập nhật thông tin thành công!";
            // Refresh lại trang để cập nhật dữ liệu mới
            header("Location: student_profile.php");
            exit;
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra trong quá trình lưu dữ liệu";
        }
    } else {
        $_SESSION['error'] = implode('. ', $errors);
    }
}

// 4. HIỂN THỊ HEADER (Đã sửa đúng tên file)
require_once __DIR__ . '/../includes/header_students.php';
?>

<div class="container-fluid">
    <h3 class="mb-4">
        <i class="bi bi-person-circle me-2"></i>
        Thông tin cá nhân
    </h3>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm p-4">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mã sinh viên</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['student_code']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['full_name']) ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Giới tính</label>
                            <input type="text" class="form-control bg-light" value="<?= $student['gender'] === 'Male' ? 'Nam' : 'Nữ' ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày sinh</label>
                            <input type="text" class="form-control bg-light" value="<?= date('d/m/Y', strtotime($student['birth_date'])) ?>" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>" pattern="[0-9]{10}" required>
                            <small class="text-muted">Định dạng: 10 chữ số</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Khoa</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['faculty_name']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Lớp hành chính</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['base_class_name'] ?? 'Chưa phân lớp') ?>" disabled>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Trạng thái học tập</label>
                        <span class="badge bg-<?= ($student['status'] == 'Active') ? 'success' : 'secondary' ?> ms-2">
                            <?= htmlspecialchars($student['status']) ?>
                        </span>
                    </div>

                    <div class="text-end">
                        <a href="/../web_QLSV/public/student.php" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-light p-4">
                <h6 class="text-primary fw-bold"><i class="bi bi-info-circle me-1"></i> Lưu ý</h6>
                <ul class="small mb-0">
                    <li class="mb-2">Chỉ được phép tự cập nhật <b>Email</b> và <b>Số điện thoại</b> để nhận thông báo.</li>
                    <li class="mb-2">Các thông tin định danh như Họ tên, Ngày sinh, Khoa... nếu sai sót vui lòng liên hệ <b>Phòng đào tạo</b> để điều chỉnh.</li>
                    <li>Thông tin này sẽ được dùng để in bằng và chứng chỉ sau này.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
// 5. HIỂN THỊ FOOTER (Đã sửa đúng tên file)
require_once __DIR__ . '/../includes/footer_students.php';
?>