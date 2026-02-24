<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/dashboard_helper.php';

authCheck(['student']);

$userId = $_SESSION['user_id'];
$pageTitle = "Thời khóa biểu";

$studentInfo = getStudentOverview($conn, $userId);
$student_id = $studentInfo['student_id'];

$sql = "SELECT 
            s.subject_name, 
            c.class_code, 
            c.weekday, 
            c.start_time, 
            c.end_time, 
            c.room,
            l.first_name AS lect_first,
            l.last_name AS lect_last
        FROM enrollments e
        JOIN classes c ON e.class_id = c.class_id
        JOIN subjects s ON c.subject_id = s.subject_id
        JOIN lecturers l ON c.lecturer_id = l.lecturer_id
        WHERE e.student_id = ? AND e.status = 'Registered'
        ORDER BY FIELD(c.weekday, 
            'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
            c.start_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$schedule = [];
while ($row = $result->fetch_assoc()) {
    $schedule[$row['weekday']][] = $row;
}

require_once __DIR__ . '/../includes/header_students.php';
?>

<style>
/* ===== SCHEDULE STYLE ===== */

.schedule-header {
    margin-bottom: 25px;
}

.day-column {
    background: #ffffff;
    border-radius: 10px;
    border-top: 4px solid #6a5acd;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    min-height: 220px;
    display: flex;
    flex-direction: column;
}

.day-header {
    padding: 12px;
    text-align: center;
    font-weight: 600;
    background: #f3f4ff;
    border-bottom: 1px solid #eee;
    color: #6a5acd;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.course-item {
    padding: 12px;
    border-bottom: 1px solid #f1f1f1;
    font-size: 0.85rem;
}

.course-item:last-child {
    border-bottom: none;
}

.course-time {
    color: #e74c3c;
    font-weight: 600;
    display: block;
    margin-bottom: 4px;
}

.course-name {
    font-weight: 700;
    color: #2c3e50;
    display: block;
}

.course-info {
    font-size: 0.75rem;
    color: #7f8c8d;
}

.badge-room {
    background: #eef2ff;
    color: #6a5acd;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
}
</style>

<div class="schedule-header d-flex justify-content-between align-items-center">
    <h4 class="fw-bold m-0 text-uppercase">Lịch học tuần này</h4>
    <div class="text-muted small">Học kỳ II - Năm 2026</div>
</div>

<div class="row g-3">
<?php 
$days = [
    'Monday' => 'Thứ Hai', 
    'Tuesday' => 'Thứ Ba', 
    'Wednesday' => 'Thứ Tư', 
    'Thursday' => 'Thứ Năm', 
    'Friday' => 'Thứ Sáu', 
    'Saturday' => 'Thứ Bảy'
];

foreach ($days as $en => $vi): ?>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="day-column">
            <div class="day-header"><?= $vi ?></div>

            <?php if (!empty($schedule[$en])): ?>
                <?php foreach ($schedule[$en] as $item): ?>
                    <div class="course-item">
                        <span class="course-time">
                            <?= date('H:i', strtotime($item['start_time'])) ?>
                            - 
                            <?= date('H:i', strtotime($item['end_time'])) ?>
                        </span>

                        <span class="course-name">
                            <?= htmlspecialchars($item['subject_name']) ?>
                        </span>

                        <div class="course-info mt-1">
                            <i class="bi bi-geo-alt me-1"></i>
                            <span class="badge-room"><?= $item['room'] ?></span>
                            <br>
                            <i class="bi bi-person me-1"></i>
                            GV: <?= htmlspecialchars($item['lect_last']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-3 text-center text-muted small">
                    Trống
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer_students.php'; ?>