<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';

/* ===============================
   1. Sinh viên theo khoa
================================ */
$statsByFaculty = [];
$sqlFaculty = "
    SELECT f.faculty_id, f.faculty_name, COUNT(s.student_id) AS count
    FROM faculties f
    LEFT JOIN students s ON f.faculty_id = s.faculty_id
    GROUP BY f.faculty_id, f.faculty_name
    ORDER BY count DESC
";
$result = $conn->query($sqlFaculty);
if ($result) {
    $statsByFaculty = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die('Lỗi SQL Faculty: ' . $conn->error);
}

/* ===============================
   2. Sinh viên theo trạng thái
================================ */
$statsByStatus = [];
$sqlStatus = "
    SELECT status, COUNT(*) AS count
    FROM students
    GROUP BY status
";
$result = $conn->query($sqlStatus);
if ($result) {
    $statsByStatus = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die('Lỗi SQL Status: ' . $conn->error);
}

/* ===============================
   3. Top 10 Sinh viên GPA cao nhất
   GPA = AVG(grades.total_score)
================================ */
$topStudents = [];
$sqlTop = "
    SELECT 
        s.student_id,
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        s.student_code,
        f.faculty_name,
        COUNT(DISTINCT e.class_id) AS course_count,
        ROUND(AVG(g.total_score), 2) AS avg_gpa
    FROM students s
    JOIN enrollments e ON s.student_id = e.student_id
    JOIN grades g ON e.enrollment_id = g.enrollment_id
    LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
    WHERE g.total_score IS NOT NULL
    GROUP BY 
        s.student_id,
        s.first_name,
        s.last_name,
        s.student_code,
        f.faculty_name
    ORDER BY avg_gpa DESC
    LIMIT 10
";


$result = $conn->query($sqlTop);
if ($result) {
    $topStudents = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die('Lỗi SQL Top Students: ' . $conn->error);
}
?>



<div class="main-content">
    <div class="topbar">
        <h2>Thống kê Sinh viên</h2>
        <div>
            <a href="/web_QLSV/admin/views/students/index.php" class="btn btn-secondary">Quay lại</a>
            <button class="btn btn-primary" onclick="exportReport()">Xuất Excel</button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php
        $totalStudents = array_sum(array_map(fn($s) => $s['count'], $statsByFaculty));
        $studyingCount = 0;
        $suspendedCount = 0;
        $droppedCount = 0;
        $graduatedCount = 0;
        
        foreach ($statsByStatus as $s) {
            if ($s['status'] === 'Studying') $studyingCount = $s['count'];
            elseif ($s['status'] === 'Suspended') $suspendedCount = $s['count'];
            elseif ($s['status'] === 'Dropped') $droppedCount = $s['count'];
            elseif ($s['status'] === 'Graduated') $graduatedCount = $s['count'];
        }
        ?>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #495696;">👥</div>
                    <h5 class="card-title">Tổng Sinh viên</h5>
                    <h3><?= $totalStudents ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #28a745;">✓</div>
                    <h5 class="card-title">Đang học</h5>
                    <h3><?= $studyingCount ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #ffc107;">⏸️</div>
                    <h5 class="card-title">Bảo lưu / Tạm nghỉ</h5>
                    <h3><?= $suspendedCount ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #17a2b8;">🎓</div>
                    <h5 class="card-title">Tốt nghiệp</h5>
                    <h3><?= $graduatedCount ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="card-title mb-3">Sinh viên theo Khoa</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr><th>Khoa</th><th class="text-end">Số SV</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statsByFaculty as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['faculty_name']) ?></td>
                            <td class="text-end"><strong><?= $s['count'] ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="card-title mb-3">Phân bổ Trạng thái</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr><th>Trạng thái</th><th class="text-end">Số lượng</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statsByStatus as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['status']) ?></td>
                            <td class="text-end"><strong><?= $s['count'] ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Students -->
    <div class="card p-3">
        <h5 class="card-title mb-3">Top 10 Sinh viên (GPA cao nhất)</h5>
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>MSSV</th>
                    <th>Họ Tên</th>
                    <th>Khoa</th>
                    <th>Số Môn</th>
                    <th>GPA Trung bình</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topStudents as $idx => $s): ?>
                <tr>
                    <td><?= $idx + 1 ?></td>
                    <td><?= htmlspecialchars($s['student_code']) ?></td>
                    <td><?= htmlspecialchars($s['student_name']) ?></td>
                    <td><?= htmlspecialchars($s['faculty_name'] ?: 'N/A') ?></td>
                    <td><?= $s['course_count'] ?: 0 ?></td>
                    <td><strong><?= $s['avg_gpa'] ? number_format($s['avg_gpa'], 2) : '0.00' ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function exportReport() {
    const tables = document.querySelectorAll('table');
    let csv = 'Báo cáo Thống kê Sinh viên\n' + new Date().toLocaleString('vi-VN') + '\n\n';
    
    tables.forEach((table, idx) => {
        const title = table.closest('.card')?.querySelector('.card-title')?.textContent;
        if (title) csv += title + '\n';
        
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            csv += Array.from(cells).map(cell => '"' + cell.textContent.trim() + '"').join(',') + '\n';
        });
        csv += '\n\n';
    });
    
    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    link.download = `bao_cao_thong_ke_sv_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
