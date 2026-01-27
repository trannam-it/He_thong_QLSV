<?php
session_start();

/* ======================================================
    0. KIỂM TRA QUYỀN TRUY CẬP (ADMIN ONLY)
   ====================================================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/index.php");
    exit();
}

/* =====================
    1. KẾT NỐI DATABASE
===================== */
$conn = mysqli_connect("localhost", "root", "", "student_management");
if (!$conn) { 
    die("Kết nối thất bại: " . mysqli_connect_error()); 
}
mysqli_set_charset($conn, "utf8mb4");

/* =====================
    2. LẤY THÔNG TIN LỚP & MÔN
===================== */
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// Lấy danh sách lớp và môn để làm bộ lọc
$classes = mysqli_query($conn, "SELECT * FROM classes");
$subjects = mysqli_query($conn, "SELECT * FROM subjects");

// Xử lý lưu điểm hàng loạt
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_all_grades'])) {
    foreach ($_POST['grades'] as $std_id => $score) {
        if ($score !== "") {
            $score = mysqli_real_escape_string($conn, $score);
            $upsert_sql = "INSERT INTO grades (student_id, subject_id, score) 
                           VALUES ($std_id, $subject_id, '$score')
                           ON DUPLICATE KEY UPDATE score = '$score'";
            mysqli_query($conn, $upsert_sql);
        }
    }
    $message = "<div class='alert alert-success'>Đã cập nhật điểm thành công cho cả lớp!</div>";
}

// Lấy danh sách sinh viên của lớp và điểm môn học tương ứng
$students_data = [];
if ($class_id > 0 && $subject_id > 0) {
    $sql = "SELECT s.student_id, s.full_name, g.score 
            FROM students s 
            LEFT JOIN grades g ON s.student_id = g.student_id AND g.subject_id = $subject_id 
            WHERE s.class_id = $class_id";
    $res = mysqli_query($conn, $sql);
    $students_data = mysqli_fetch_all($res, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập điểm theo lớp - SMS ADMIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .score-input { width: 100px; text-align: center; border-radius: 8px; }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="fw-bold text-center mb-4 text-primary">NHẬP ĐIỂM THEO LỚP & MÔN</h2>

    <div class="card main-card p-4 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-bold small">CHỌN MÔN HỌC</label>
                <select name="subject_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Chọn môn học --</option>
                    <?php while($sub = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?= $sub['subject_id'] ?>" <?= $subject_id == $sub['subject_id'] ? 'selected' : '' ?>>
                            <?= $sub['subject_code'] ?> - <?= $sub['subject_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-bold small">CHỌN LỚP HỌC</label>
                <select name="class_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">-- Chọn lớp học --</option>
                    <?php while($cl = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?= $cl['class_id'] ?>" <?= $class_id == $cl['class_id'] ? 'selected' : '' ?>>
                            Lớp <?= $cl['class_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-filter"></i> Lọc</button>
            </div>
        </form>
    </div>

    <?= $message ?>

    <?php if ($class_id > 0 && $subject_id > 0): ?>
    <form method="POST">
        <div class="card main-card p-4">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Họ và Tên Sinh viên</th>
                        <th class="text-center">Điểm số (0-10)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students_data) > 0): ?>
                        <?php foreach($students_data as $std): ?>
                        <tr>
                            <td>#<?= $std['student_id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($std['full_name']) ?></td>
                            <td class="text-center">
                                <input type="number" step="0.01" min="0" max="10" 
                                       name="grades[<?= $std['student_id'] ?>]" 
                                       class="form-control score-input mx-auto shadow-sm" 
                                       value="<?= $std['score'] ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center py-4">Lớp này chưa có sinh viên.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if (count($students_data) > 0): ?>
            <div class="text-end mt-3">
                <button type="submit" name="save_all_grades" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-cloud-arrow-up me-2"></i>LƯU TẤT CẢ ĐIỂM
                </button>
            </div>
            <?php endif; ?>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-info text-center">Vui lòng chọn <b>Môn học</b> và <b>Lớp học</b> để bắt đầu nhập điểm.</div>
    <?php endif; ?>
</div>

</body>
</html>