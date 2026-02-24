<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';

// Get statistics
$stats = [];

// Total lecturers by faculty
$query = "SELECT f.faculty_id, f.faculty_name, COUNT(l.lecturer_id) as count
          FROM faculties f
          LEFT JOIN lecturers l ON f.faculty_id = l.faculty_id
          GROUP BY f.faculty_id, f.faculty_name
          ORDER BY count DESC";
$statsByFaculty = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Lecturers by education level
$query = "SELECT degree, COUNT(*) as count FROM lecturers GROUP BY degree ORDER BY count DESC";
$statsByDegree = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Teaching load
$query = "SELECT l.lecturer_id, CONCAT(l.first_name, ' ', l.last_name) as lecturer_name, 
          COUNT(DISTINCT c.class_id) as class_count,
          COUNT(DISTINCT e.student_id) as student_count,
          SUM(CASE WHEN g.final_grade IS NOT NULL THEN 1 ELSE 0 END) as grades_entered
          FROM lecturers l
          LEFT JOIN classes c ON l.lecturer_id = c.lecturer_id
          LEFT JOIN enrollments e ON c.class_id = e.class_id
          LEFT JOIN grades g ON c.class_id = g.class_id AND e.student_id = g.student_id
          GROUP BY l.lecturer_id, l.first_name, l.last_name
          ORDER BY class_count DESC
          LIMIT 20";
$teachingLoad = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Unassigned lecturers
$query = "SELECT l.lecturer_id, CONCAT(l.first_name, ' ', l.last_name) as lecturer_name, 
          l.lecturer_code, f.faculty_name, l.degree
          FROM lecturers l
          LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
          WHERE l.lecturer_id NOT IN (SELECT DISTINCT lecturer_id FROM classes WHERE lecturer_id IS NOT NULL)
          ORDER BY l.first_name";
$unassignedLecturers = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<div class="main-content">
    <div class="topbar">
        <h2>Thống kê Giảng viên</h2>
        <div>
            <a href="/web_QLSV/admin/views/lecturers/index.php" class="btn btn-secondary">Quay lại</a>
            <button class="btn btn-primary" onclick="exportReport()">Xuất Excel</button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #495696;">👨‍🏫</div>
                    <h5 class="card-title">Tổng Giảng viên</h5>
                    <h3 id="totalLecturers">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #28a745;">📚</div>
                    <h5 class="card-title">Đã gán Lớp</h5>
                    <h3 id="assignedLecturers">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #ffc107;">⚠️</div>
                    <h5 class="card-title">Chưa gán Lớp</h5>
                    <h3 id="unassignedCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <div class="stat-icon" style="font-size: 32px; color: #17a2b8;">📊</div>
                    <h5 class="card-title">Hoàn thành Điểm (%)</h5>
                    <h3 id="gradeCompletion">0%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Sections -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="card-title mb-3">Giảng viên theo Khoa</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr><th>Khoa</th><th class="text-end">Số lượng</th></tr>
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
                <h5 class="card-title mb-3">Phân bổ Học vị</h5>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr><th>Học vị</th><th class="text-end">Số lượng</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statsByDegree as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['degree'] ?: 'Chưa cập nhật') ?></td>
                            <td class="text-end"><strong><?= $s['count'] ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Teaching Load -->
    <div class="card p-3 mb-4">
        <h5 class="card-title mb-3">Khối lượng Dạy (Top 20)</h5>
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Giảng viên</th>
                    <th class="text-center">Số Lớp</th>
                    <th class="text-center">Số SV</th>
                    <th class="text-center">Điểm Nhập</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachingLoad as $idx => $t): ?>
                <tr>
                    <td><?= $idx + 1 ?></td>
                    <td><?= htmlspecialchars($t['lecturer_name']) ?></td>
                    <td class="text-center"><strong><?= $t['class_count'] ?: 0 ?></strong></td>
                    <td class="text-center"><strong><?= $t['student_count'] ?: 0 ?></strong></td>
                    <td class="text-center">
                        <span class="badge bg-success"><?= $t['grades_entered'] ?: 0 ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Unassigned Lecturers -->
    <div class="card p-3">
        <h5 class="card-title mb-3">Giảng viên Chưa gán Lớp (<?= count($unassignedLecturers) ?>)</h5>
        <div style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mã GV</th>
                        <th>Họ Tên</th>
                        <th>Khoa</th>
                        <th>Học vị</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unassignedLecturers as $idx => $l): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td><?= htmlspecialchars($l['lecturer_code']) ?></td>
                        <td><?= htmlspecialchars($l['lecturer_name']) ?></td>
                        <td><?= htmlspecialchars($l['faculty_name'] ?: 'N/A') ?></td>
                        <td><?= htmlspecialchars($l['degree'] ?: 'N/A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
const apiUrl = '/web_QLSV/admin/api/router.php';

async function loadStatistics() {
    try {
        const res = await fetch(`${apiUrl}?module=lecturers&action=index&page=1&limit=500`, { credentials: 'same-origin' });
        const text = await res.text();
        const j = JSON.parse(text);
        
        if (j.success) {
            const lecturers = j.data;
            document.getElementById('totalLecturers').textContent = lecturers.length;
            
            // Count assigned vs unassigned
            const assigned = lecturers.filter(l => l.class_count > 0 || 0).length;
            document.getElementById('assignedLecturers').textContent = assigned;
            document.getElementById('unassignedCount').textContent = lecturers.length - assigned;
        }
    } catch (e) {
        console.error(e);
    }
}

function exportReport() {
    // Simple CSV export - can be enhanced with PhpOffice\PhpSpreadsheet
    const tables = document.querySelectorAll('table');
    let csv = 'Báo cáo Thống kê Giảng viên\n' + new Date().toLocaleString('vi-VN') + '\n\n';
    
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
    link.download = `bao_cao_thong_ke_gv_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

loadStatistics();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
