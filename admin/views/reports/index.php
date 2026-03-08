<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';

authCheck(['super_admin', 'content_admin']);

$pageTitle = 'Báo cáo & Thống kê';

/* ============================================================
   1. TỔNG QUAN
============================================================ */
$totalStudents  = $conn->query("SELECT COUNT(*) FROM students WHERE status = 'Studying'")->fetch_row()[0] ?? 0;
$totalLecturers = $conn->query("SELECT COUNT(*) FROM lecturers")->fetch_row()[0] ?? 0;
$totalClasses   = $conn->query("SELECT COUNT(*) FROM classes")->fetch_row()[0] ?? 0;
$totalGrades    = $conn->query("SELECT COUNT(*) FROM grades WHERE score IS NOT NULL")->fetch_row()[0] ?? 0;
$totalEnroll    = $conn->query("SELECT COUNT(*) FROM enrollments")->fetch_row()[0] ?? 0;
$totalSubjects  = $conn->query("SELECT COUNT(*) FROM subjects")->fetch_row()[0] ?? 0;

/* ============================================================
   2. PHÂN BỐ ĐIỂM (grade_letter)
============================================================ */
$gradeDist = $conn->query("
    SELECT grade_letter, COUNT(*) AS total
    FROM grades
    WHERE grade_letter IS NOT NULL AND grade_letter != ''
    GROUP BY grade_letter
    ORDER BY FIELD(grade_letter,'A','B+','B','C+','C','D','F')
")->fetch_all(MYSQLI_ASSOC);

/* ============================================================
   3. SINH VIÊN THEO KHOA
============================================================ */
$byFaculty = $conn->query("
    SELECT f.faculty_name, COUNT(s.student_id) AS total
    FROM faculties f
    LEFT JOIN students s ON s.faculty_id = f.faculty_id AND s.status = 'Studying'
    GROUP BY f.faculty_id, f.faculty_name
    ORDER BY total DESC
")->fetch_all(MYSQLI_ASSOC);

/* ============================================================
   4. TRẠNG THÁI ĐĂNG KÝ
============================================================ */
$enrollStatus = $conn->query("
    SELECT status, COUNT(*) AS total
    FROM enrollments
    GROUP BY status
    ORDER BY total DESC
")->fetch_all(MYSQLI_ASSOC);

/* ============================================================
   5. TOP 10 SINH VIÊN GPA CAO NHẤT
============================================================ */
$topStudents = $conn->query("
    SELECT
        s.student_code,
        CONCAT(s.last_name, ' ', s.first_name)  AS full_name,
        f.faculty_name,
        COUNT(DISTINCT e.enrollment_id)          AS total_courses,
        ROUND(AVG(g.score), 2)                   AS avg_score
    FROM students s
    JOIN enrollments e  ON e.student_id    = s.student_id
    JOIN grades g       ON g.enrollment_id = e.enrollment_id
    LEFT JOIN faculties f ON f.faculty_id  = s.faculty_id
    WHERE g.score IS NOT NULL
    GROUP BY s.student_id, s.student_code, s.last_name, s.first_name, f.faculty_name
    ORDER BY avg_score DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

/* ============================================================
   6. LỚP HỌC PHẦN CÓ NHIỀU SINH VIÊN NHẤT
============================================================ */
// $topClasses = $conn->query("
//     SELECT
//         c.class_code,
//         sub.subject_name,
//         CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,
//         c.semester, c.year,
//         COUNT(e.enrollment_id) AS enrolled
//     FROM classes c
//     JOIN subjects sub   ON sub.subject_id  = c.subject_id
//     LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
//     LEFT JOIN enrollments e ON e.class_id  = c.class_id
//     GROUP BY c.class_id, c.class_code, sub.subject_name, l.last_name, l.first_name, c.semester, c.year
//     ORDER BY enrolled DESC
//     LIMIT 10
// ")->fetch_all(MYSQLI_ASSOC);

$topClasses = $conn->query("
    SELECT
        c.class_code,
        sub.subject_name,
        CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,
        s.semester_name,
        COUNT(e.enrollment_id) AS enrolled
    FROM classes c
    JOIN subjects sub   ON sub.subject_id  = c.subject_id
    LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
    LEFT JOIN semesters s ON s.semester_id = c.semester_id
    LEFT JOIN enrollments e ON e.class_id  = c.class_id
    GROUP BY c.class_id
    ORDER BY enrolled DESC
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);


/* ============================================================
   7. THỐNG KÊ ĐIỂM THEO KHOA
============================================================ */
$gradeByFaculty = $conn->query("
    SELECT
        f.faculty_name,
        COUNT(g.grade_id)      AS graded,
        ROUND(AVG(g.score), 2) AS avg_score,
        SUM(g.grade_letter = 'A')  AS cnt_A,
        SUM(g.grade_letter = 'F')  AS cnt_F
    FROM faculties f
    JOIN students s     ON s.faculty_id    = f.faculty_id
    JOIN enrollments e  ON e.student_id    = s.student_id
    JOIN grades g       ON g.enrollment_id = e.enrollment_id
    WHERE g.score IS NOT NULL
    GROUP BY f.faculty_id, f.faculty_name
    ORDER BY avg_score DESC
")->fetch_all(MYSQLI_ASSOC);

include_once __DIR__ . '/../layout/header.php';
?>

<!-- ==================== SUMMARY CARDS ==================== -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['icon' => 'people-fill',       'color' => 'primary',   'label' => 'Sinh viên đang học', 'value' => number_format($totalStudents)],
        ['icon' => 'person-badge-fill', 'color' => 'success',   'label' => 'Giảng viên',         'value' => number_format($totalLecturers)],
        ['icon' => 'building-fill',     'color' => 'info',      'label' => 'Lớp học phần',       'value' => number_format($totalClasses)],
        ['icon' => 'book-fill',         'color' => 'warning',   'label' => 'Môn học',            'value' => number_format($totalSubjects)],
        ['icon' => 'journal-check',     'color' => 'secondary', 'label' => 'Đăng ký học phần',  'value' => number_format($totalEnroll)],
        ['icon' => 'award-fill',        'color' => 'danger',    'label' => 'Điểm đã nhập',       'value' => number_format($totalGrades)],
    ];
    foreach ($cards as $c): ?>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body py-3">
                <div class="mb-1">
                    <span class="badge bg-<?= $c['color'] ?> p-2 fs-5">
                        <i class="bi bi-<?= $c['icon'] ?>"></i>
                    </span>
                </div>
                <div class="fw-bold fs-4"><?= $c['value'] ?></div>
                <small class="text-muted"><?= $c['label'] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ==================== CHARTS ROW ==================== -->
<div class="row g-3 mb-4">

    <!-- Grade distribution (bar) -->
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-bar-chart-fill me-1"></i> Phân bố xếp loại điểm
            </div>
            <div class="card-body">
                <canvas id="chartGrade" height="200"></canvas>
                <?php if (empty($gradeDist)): ?>
                <p class="text-muted text-center mt-3">Chưa có dữ liệu điểm</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Students by faculty (doughnut) -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-pie-chart-fill me-1"></i> Sinh viên theo khoa
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartFaculty" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Enrollment status (doughnut) -->
    <div class="col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold">
                <i class="bi bi-clipboard-data-fill me-1"></i> Trạng thái học phần
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartEnroll" height="220"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- ==================== TOP STUDENTS ==================== -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-trophy-fill text-warning me-1"></i> Top 10 Sinh viên GPA cao nhất</span>
        <button class="btn btn-sm btn-outline-success" onclick="exportTableCSV('tblTopStudents', 'top_students.csv')">
            <i class="bi bi-download me-1"></i>Xuất CSV
        </button>
    </div>
    <div class="card-body p-0">
        <table id="tblTopStudents" class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center">#</th>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Khoa</th>
                    <th class="text-center">Số môn</th>
                    <th class="text-center">ĐTB</th>
                    <th class="text-center">Xếp loại</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($topStudents)): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
            <?php else: ?>
                <?php foreach ($topStudents as $i => $s):
                    $avg = (float)$s['avg_score'];
                    if ($avg >= 8.5)       { $badge = 'success'; $rank = 'Xuất sắc'; }
                    elseif ($avg >= 7.0)   { $badge = 'info';    $rank = 'Giỏi'; }
                    elseif ($avg >= 5.5)   { $badge = 'warning'; $rank = 'Khá'; }
                    else                   { $badge = 'danger';  $rank = 'TB'; }
                ?>
                <tr>
                    <td class="text-center fw-bold"><?= $i + 1 ?></td>
                    <td><code><?= htmlspecialchars($s['student_code']) ?></code></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['faculty_name'] ?? '—') ?></td>
                    <td class="text-center"><?= (int)$s['total_courses'] ?></td>
                    <td class="text-center fw-semibold"><?= $s['avg_score'] ?></td>
                    <td class="text-center"><span class="badge bg-<?= $badge ?>"><?= $rank ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== GRADE BY FACULTY ==================== -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-graph-up me-1"></i> Thống kê điểm theo khoa</span>
        <button class="btn btn-sm btn-outline-success" onclick="exportTableCSV('tblGradeByFaculty', 'grade_by_faculty.csv')">
            <i class="bi bi-download me-1"></i>Xuất CSV
        </button>
    </div>
    <div class="card-body p-0">
        <table id="tblGradeByFaculty" class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Khoa</th>
                    <th class="text-center">Số điểm</th>
                    <th class="text-center">ĐTB</th>
                    <th class="text-center">Số A</th>
                    <th class="text-center">Số F</th>
                    <th style="min-width:160px">Biểu đồ</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($gradeByFaculty)): ?>
                <tr><td colspan="6" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
            <?php else:
                $maxAvg = max(array_column($gradeByFaculty, 'avg_score')) ?: 10;
                foreach ($gradeByFaculty as $r):
                    $pct = $maxAvg > 0 ? round(($r['avg_score'] / $maxAvg) * 100) : 0;
                    $barColor = $r['avg_score'] >= 7 ? 'bg-success' : ($r['avg_score'] >= 5 ? 'bg-warning' : 'bg-danger');
            ?>
                <tr>
                    <td><?= htmlspecialchars($r['faculty_name']) ?></td>
                    <td class="text-center"><?= (int)$r['graded'] ?></td>
                    <td class="text-center fw-semibold"><?= $r['avg_score'] ?></td>
                    <td class="text-center"><span class="badge bg-success"><?= (int)$r['cnt_A'] ?></span></td>
                    <td class="text-center"><span class="badge bg-danger"><?= (int)$r['cnt_F'] ?></span></td>
                    <td>
                        <div class="progress" style="height:14px">
                            <div class="progress-bar <?= $barColor ?>" style="width:<?= $pct ?>%">
                                <?= $r['avg_score'] ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== TOP CLASSES ==================== -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-journals me-1"></i> Lớp học phần đông nhất</span>
        <button class="btn btn-sm btn-outline-success" onclick="exportTableCSV('tblTopClasses', 'top_classes.csv')">
            <i class="bi bi-download me-1"></i>Xuất CSV
        </button>
    </div>
    <div class="card-body p-0">
        <table id="tblTopClasses" class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Mã lớp</th>
                    <th>Môn học</th>
                    <th>Giảng viên</th>
                    <th class="text-center">Học kỳ</th>
                    <th class="text-center">Năm</th>
                    <th class="text-center">SV đăng ký</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($topClasses)): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
            <?php else: ?>
                <?php foreach ($topClasses as $i => $c): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><code><?= htmlspecialchars($c['class_code']) ?></code></td>
                    <td><?= htmlspecialchars($c['subject_name']) ?></td>
                    <td><?= htmlspecialchars($c['lecturer_name'] ?? '—') ?></td>
                    <td class="text-center"><?= htmlspecialchars($c['semester']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($c['year']) ?></td>
                    <td class="text-center">
                        <span class="badge bg-primary"><?= (int)$c['enrolled'] ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
/* ===========================================================
   CHART DATA từ PHP
=========================================================== */
const gradeLabels = <?= json_encode(array_column($gradeDist, 'grade_letter')) ?>;
const gradeCounts = <?= json_encode(array_map('intval', array_column($gradeDist, 'total'))) ?>;

const facultyLabels = <?= json_encode(array_column($byFaculty, 'faculty_name')) ?>;
const facultyCounts = <?= json_encode(array_map('intval', array_column($byFaculty, 'total'))) ?>;

const enrollLabels = <?= json_encode(array_column($enrollStatus, 'status')) ?>;
const enrollCounts = <?= json_encode(array_map('intval', array_column($enrollStatus, 'total'))) ?>;

/* ===========================================================
   COLOUR PALETTE
=========================================================== */
const GRADE_COLORS = {
    'A'  : '#198754',
    'B+' : '#0dcaf0',
    'B'  : '#0d6efd',
    'C+' : '#ffc107',
    'C'  : '#fd7e14',
    'D'  : '#6c757d',
    'F'  : '#dc3545'
};

const PALETTE = [
    '#4e73df','#1cc88a','#36b9cc','#f6c23e',
    '#e74a3b','#858796','#5a5c69','#d4a4eb'
];

/* ===========================================================
   1. BAR – Phân bố xếp loại
=========================================================== */
if (gradeLabels.length > 0) {
    new Chart(document.getElementById('chartGrade'), {
        type: 'bar',
        data: {
            labels: gradeLabels,
            datasets: [{
                label: 'Số lượng',
                data: gradeCounts,
                backgroundColor: gradeLabels.map(l => GRADE_COLORS[l] || '#6c757d'),
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} sinh viên`
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, precision: 0 }
            }
        }
    });
}

/* ===========================================================
   2. DOUGHNUT – Sinh viên theo khoa
=========================================================== */
if (facultyLabels.length > 0) {
    new Chart(document.getElementById('chartFaculty'), {
        type: 'doughnut',
        data: {
            labels: facultyLabels,
            datasets: [{
                data: facultyCounts,
                backgroundColor: PALETTE,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} SV`
                    }
                }
            }
        }
    });
}

/* ===========================================================
   3. DOUGHNUT – Trạng thái đăng ký
=========================================================== */
const ENROLL_COLORS = {
    'Registered' : '#0d6efd',
    'Completed'  : '#198754',
    'Cancelled'  : '#dc3545',
    'Failed'     : '#ffc107'
};
if (enrollLabels.length > 0) {
    new Chart(document.getElementById('chartEnroll'), {
        type: 'doughnut',
        data: {
            labels: enrollLabels,
            datasets: [{
                data: enrollCounts,
                backgroundColor: enrollLabels.map(l => ENROLL_COLORS[l] || '#6c757d'),
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed}`
                    }
                }
            }
        }
    });
}

/* ===========================================================
   EXPORT TABLE → CSV
=========================================================== */
function exportTableCSV(tableId, filename) {
    const rows = document.querySelectorAll(`#${tableId} tr`);
    const csvLines = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const line = Array.from(cols).map(td => {
            let text = td.innerText.replace(/\n/g, ' ').trim();
            // Escape commas and quotes
            if (text.includes(',') || text.includes('"')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            return text;
        });
        csvLines.push(line.join(','));
    });

    const bom = '\uFEFF'; // UTF-8 BOM for Excel
    const blob = new Blob([bom + csvLines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}
</script>
