<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';

$pageTitle = "Thống kê Giảng viên";

// ====== TỔNG GIẢNG VIÊN ======
$totalLecturers = $conn->query("
    SELECT COUNT(*) c FROM lecturers
")->fetch_assoc()['c'] ?? 0;

// ====== THỐNG KÊ THEO KHOA ======
$statsByFaculty = $conn->query("
    SELECT f.faculty_name, COUNT(l.lecturer_id) total
    FROM faculties f
    LEFT JOIN lecturers l ON l.faculty_id = f.faculty_id
    GROUP BY f.faculty_id
    ORDER BY total DESC
")->fetch_all(MYSQLI_ASSOC);

// ====== THỐNG KÊ THEO HỌC VỊ (ENUM) ======
$statsByDegree = $conn->query("
    SELECT degree, COUNT(*) total
    FROM lecturers
    GROUP BY degree
    ORDER BY total DESC
")->fetch_all(MYSQLI_ASSOC);

// ====== DANH SÁCH + SỐ MÔN ======
$lecturers = $conn->query("
    SELECT l.lecturer_code,
           CONCAT(l.first_name,' ',l.last_name) fullname,
           f.faculty_name,
           l.degree,
           COUNT(DISTINCT c.subject_id) subject_count
    FROM lecturers l
    LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
    LEFT JOIN classes c ON l.lecturer_id = c.lecturer_id
    GROUP BY l.lecturer_id
    ORDER BY subject_count DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">
            <i class="bi bi-bar-chart-line text-primary"></i>
            Thống kê Giảng viên
        </h4>
        <a href="<?= BASE_URL ?>admin/views/lecturers/index.php"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- CARD -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h6 class="text-muted">Tổng Giảng viên</h6>
                    <h2 class="fw-bold text-primary"><?= $totalLecturers ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- BIỂU ĐỒ -->
    <div class="row mb-4">

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Theo Khoa</h6>
                    <canvas id="facultyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Theo Học vị</h6>
                    <canvas id="degreeChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Số môn đang dạy</h6>

            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã GV</th>
                        <th>Họ tên</th>
                        <th>Khoa</th>
                        <th>Học vị</th>
                        <th class="text-center">Số môn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lecturers as $l): ?>
                    <tr>
                        <td><?= $l['lecturer_code'] ?></td>
                        <td><?= $l['fullname'] ?></td>
                        <td><?= $l['faculty_name'] ?></td>
                        <td><?= $l['degree'] ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary">
                                <?= $l['subject_count'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<script>
const facultyLabels = <?= json_encode(array_column($statsByFaculty,'faculty_name')) ?>;
const facultyData = <?= json_encode(array_column($statsByFaculty,'total')) ?>;

new Chart(document.getElementById('facultyChart'), {
    type: 'bar',
    data: {
        labels: facultyLabels,
        datasets: [{
            label: 'Số giảng viên',
            data: facultyData
        }]
    }
});

const degreeLabels = <?= json_encode(array_column($statsByDegree,'degree')) ?>;
const degreeData = <?= json_encode(array_column($statsByDegree,'total')) ?>;

new Chart(document.getElementById('degreeChart'), {
    type: 'pie',
    data: {
        labels: degreeLabels,
        datasets: [{
            data: degreeData
        }]
    }
});
</script>
