<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';

$pageTitle = 'Thống kê theo khoa';

$sql = "
    SELECT 
        f.faculty_id,
        f.faculty_code,
        f.faculty_name,
        fa.address AS faculty_address,
        fa.email AS faculty_email,
        COUNT(DISTINCT s.student_id) AS students,
        COUNT(DISTINCT l.lecturer_id) AS lecturers,
        COUNT(DISTINCT bc.base_class_id) AS base_classes
    FROM faculties f
    LEFT JOIN faculty_addresses fa ON fa.faculty_code = f.faculty_code
    LEFT JOIN students s ON s.faculty_id = f.faculty_id
    LEFT JOIN lecturers l ON l.faculty_id = f.faculty_id
    LEFT JOIN base_classes bc ON bc.faculty_id = f.faculty_id
    GROUP BY 
        f.faculty_id,
        f.faculty_code,
        f.faculty_name,
        fa.address,
        fa.email
    ORDER BY f.faculty_id DESC
";

$res = $conn->query($sql);
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}
?>

<!-- ===== PAGE CONTENT ===== -->
<div class="container-fluid mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-building me-2"></i>Thống kê theo khoa
        </h4>

        <a href="index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-hover table-bordered align-middle datatable">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Mã khoa</th>
                        <th>Tên khoa</th>
                        <th>Địa chỉ</th>
                        <th>Sinh viên</th>
                        <th>Giảng viên</th>
                        <th>Lớp cơ sở</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $r['faculty_id'] ?></td>
                        <td><?= htmlspecialchars($r['faculty_code']) ?></td>
                        <td><?= htmlspecialchars($r['faculty_name']) ?></td>
                        <td><?= htmlspecialchars($r['faculty_address'] ?? '—') ?></td>
                        <td>
                            <span class="badge bg-primary"><?= $r['students'] ?></span>
                        </td>
                        <td>
                            <span class="badge bg-success"><?= $r['lecturers'] ?></span>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark"><?= $r['base_classes'] ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>
                    
<!-- Thêm biểu đồ khoa – sinh viên

🔍 Filter theo khoa

📥 Export Excel / PDF

👁 Click vào khoa → xem chi tiết -->

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
