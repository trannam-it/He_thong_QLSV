<?php
session_start();

/* ======================================================
    0. KIỂM TRA QUYỀN TRUY CẬP (ADMIN ONLY)
   ====================================================== */
// 1. Nếu chưa đăng nhập HOẶC role không phải admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Chuyển hướng ngay lập tức về trang chủ công khai
    header("Location: ../public/index.php");
    exit(); // Dừng toàn bộ kịch bản phía sau
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
    2. CẤU HÌNH XUẤT FILE EXCEL
===================== */
// Tên file khi tải về (Ví dụ: Danh_sach_sinh_vien_20231027.xls)
$filename = "Danh_sach_sinh_vien_" . date('Ymd_His') . ".xls";

// Gửi header để trình duyệt hiểu đây là một file Excel tải về
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Truy vấn SQL lấy danh sách sinh viên và tên lớp
$sql = "SELECT s.student_id, s.full_name, s.email, s.phone, s.address, c.class_name 
        FROM students s 
        LEFT JOIN classes c ON s.class_id = c.class_id 
        ORDER BY s.student_id DESC";
        
$result = mysqli_query($conn, $sql);
?>

<meta charset="utf-8">
<table border="1">
    <thead>
        <tr style="background-color: #4361ee; color: #ffffff; font-weight: bold; height: 35px;">
            <th>Mã SV</th>
            <th>Họ và Tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Lớp học</th>
            <th>Địa chỉ</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td style="text-align: center;"><?= $row['student_id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td style="vnd.ms-excel.numberformat:@"><?= htmlspecialchars($row['phone']) ?></td>
                <td style="text-align: center;"><?= htmlspecialchars($row['class_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">Không có dữ liệu sinh viên.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
// Đóng kết nối sau khi xuất dữ liệu
mysqli_close($conn);
?>