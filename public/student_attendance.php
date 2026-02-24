<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/dashboard_helper.php';

authCheck(['student']);

$userId = $_SESSION['user_id'];
$pageTitle = "Chi tiết điểm danh";

$studentInfo = getStudentOverview($conn, $userId);
$student_id = $studentInfo['student_id'];

$sql = "SELECT 
            s.subject_name, 
            c.class_code, 
            a.attendance_date, 
            a.status, 
            a.note
        FROM attendance a
        JOIN enrollments e ON a.enrollment_id = e.enrollment_id
        JOIN classes c ON e.class_id = c.class_id
        JOIN subjects s ON c.subject_id = s.subject_id
        WHERE e.student_id = ?
        ORDER BY a.attendance_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$attendance_list = [];
while ($row = $result->fetch_assoc()) {
    $attendance_list[] = $row;
}

require_once __DIR__ . '/../includes/header_students.php';
?>

<style>
/* ===== ATTENDANCE STYLE ===== */

.attendance-header {
    margin-bottom: 25px;
}

.attendance-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0.15rem 1rem rgba(0,0,0,0.05);
    overflow: hidden;
}

.table thead {
    background: #6a5acd;
    color: white;
}

.table thead th {
    padding: 14px;
    font-size: 0.85rem;
    text-transform: uppercase;
    border: none;
    font-weight: 600;
}

.table tbody td {
    padding: 14px;
    vertical-align: middle;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
}

.bg-present { background: #d1e7dd; color: #0f5132; }
.bg-absent  { background: #f8d7da; color: #842029; }
.bg-late    { background: #fff3cd; color: #856404; }
</style>

<div class="attendance-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="fw-bold m-0 text-uppercase">Nhật ký điểm danh</h4>
        <p class="text-muted small mb-0">Chi tiết các buổi học đã tham gia</p>
    </div>

    <span class="badge bg-white text-dark border p-2">
        <i class="bi bi-person-circle me-1"></i>
        <?= htmlspecialchars($studentInfo['full_name']) ?>
    </span>
</div>

<div class="attendance-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Môn học</th>
                    <th>Lớp</th>
                    <th class="text-center">Trạng thái</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attendance_list)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Bạn chưa có dữ liệu điểm danh nào.
                        </td>
                    </tr>
                <?php else: foreach ($attendance_list as $row): ?>
                    <tr>
                        <td class="fw-bold">
                            <?= date('d/m/Y', strtotime($row['attendance_date'])) ?>
                        </td>

                        <td><?= htmlspecialchars($row['subject_name']) ?></td>

                        <td class="text-primary small fw-semibold">
                            <?= $row['class_code'] ?>
                        </td>

                        <td class="text-center">
                            <?php
                                $status = $row['status'];
                                $label = ($status == 'Present') ? 'Có mặt' :
                                         (($status == 'Absent') ? 'Vắng mặt' : 'Muộn');
                                $class = 'bg-' . strtolower($status);
                            ?>
                            <span class="status-badge <?= $class ?>">
                                <?= $label ?>
                            </span>
                        </td>

                        <td class="small text-muted">
                            <?= htmlspecialchars($row['note'] ?? '-') ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_students.php'; ?>