<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/dashboard_helper.php';

authCheck(['student']);

$userId = $_SESSION['user_id'];
$pageTitle = "Kết quả học tập";

$studentInfo = getStudentOverview($conn, $userId);
$student_id = $studentInfo['student_id'];

function convertToSystem4($score) {
    if ($score === null || $score === '') 
        return ['h4' => '-', 'chu' => '-', 'color' => '#6c757d'];

    $s = ($score > 10) ? $score / 10 : $score;

    if ($s >= 8.5) return ['h4' => 4.0, 'chu' => 'A',  'color' => '#28a745'];
    if ($s >= 8.0) return ['h4' => 3.5, 'chu' => 'B+', 'color' => '#17a2b8'];
    if ($s >= 7.0) return ['h4' => 3.0, 'chu' => 'B',  'color' => '#17a2b8'];
    if ($s >= 6.5) return ['h4' => 2.5, 'chu' => 'C+', 'color' => '#ffc107'];
    if ($s >= 5.5) return ['h4' => 2.0, 'chu' => 'C',  'color' => '#ffc107'];
    if ($s >= 5.0) return ['h4' => 1.5, 'chu' => 'D+', 'color' => '#fd7e14'];
    if ($s >= 4.0) return ['h4' => 1.0, 'chu' => 'D',  'color' => '#fd7e14'];
    return ['h4' => 0.0, 'chu' => 'F', 'color' => '#dc3545'];
}

$sql = "SELECT 
            s.subject_code, s.subject_name, s.credit_hours, 
            g.score AS score10, e.status, e.enrollment_id,
            (SELECT COUNT(*) FROM attendance WHERE enrollment_id = e.enrollment_id AND status = 'Present') as p_count,
            (SELECT COUNT(*) FROM attendance WHERE enrollment_id = e.enrollment_id) as total_days
        FROM enrollments e
        JOIN classes c ON e.class_id = c.class_id
        JOIN subjects s ON c.subject_id = s.subject_id
        LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
        WHERE e.student_id = ?
        ORDER BY c.year DESC, c.semester DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

require_once __DIR__ . '/../includes/header_students.php';
?>

<style>
.grade-header {
    margin-bottom: 25px;
}

.custom-table-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 0.15rem 1rem rgba(0,0,0,0.05);
    overflow: hidden;
}

.table thead {
    background: #6a5acd;
    color: white;
}

.table th {
    font-size: 0.75rem;
    text-transform: uppercase;
    padding: 14px;
    border: none;
}

.table td {
    padding: 14px;
    vertical-align: middle;
}

.score-val {
    font-weight: 700;
}

.att-text {
    font-size: 0.7rem;
    color: #6c757d;
}

.badge-completed {
    background: #d1e7dd;
    color: #0f5132;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-learning {
    background: #fff3cd;
    color: #856404;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
</style>

<div class="grade-header">
    <h4 class="fw-bold text-uppercase m-0">Bảng điểm chi tiết</h4>
</div>

<div class="custom-table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 text-center">
            <thead>
                <tr>
                    <th class="text-start ps-4">Học phần</th>
                    <th>Tín chỉ</th>
                    <th>Hệ 10</th>
                    <th>Hệ 4</th>
                    <th>Chữ</th>
                    <th>Chuyên cần</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    $raw_score = $row['score10'];
                    $display_score = ($raw_score > 10) ? $raw_score / 10 : $raw_score;
                    $grade = convertToSystem4($raw_score);
                    $att_pct = ($row['total_days'] > 0) 
                        ? round(($row['p_count'] / $row['total_days']) * 100) 
                        : 0;
                ?>
                <tr>
                    <td class="text-start ps-4">
                        <div class="fw-bold"><?= htmlspecialchars($row['subject_name']) ?></div>
                        <small class="text-muted"><?= $row['subject_code'] ?></small>
                    </td>

                    <td><?= $row['credit_hours'] ?></td>

                    <td class="score-val">
                        <?= ($raw_score !== null) ? number_format($display_score, 1) : '-' ?>
                    </td>

                    <td class="score-val text-primary">
                        <?= $grade['h4'] ?>
                    </td>

                    <td class="score-val" style="color: <?= $grade['color'] ?>">
                        <?= $grade['chu'] ?>
                    </td>

                    <td>
                        <div class="progress mb-1" style="height:6px; width:80px; margin:0 auto;">
                            <div class="progress-bar bg-info" style="width:<?= $att_pct ?>%"></div>
                        </div>
                        <span class="att-text">
                            <?= $row['p_count'] ?>/<?= $row['total_days'] ?> buổi
                        </span>
                    </td>

                    <td>
                        <?php if ($row['status'] == 'Completed'): ?>
                            <span class="badge-completed">Hoàn thành</span>
                        <?php else: ?>
                            <span class="badge-learning">Đang học</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer_students.php'; ?>