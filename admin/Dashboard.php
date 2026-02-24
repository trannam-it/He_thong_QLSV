<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/admin_helper.php';
include __DIR__ . '/../includes/alert.php';

authCheck(['super_admin', 'content_admin']);

$pageTitle = 'Dashboard';
$userId = $_SESSION['user_id'];
$adminInfo = getAdminInfo($conn, $userId);

// ===== Statistics =====
$totalStudents   = countTotalStudents($conn);
$totalLecturers  = countTotalLecturers($conn);
$totalClasses    = countTotalClasses($conn);
$totalFaculties  = countTotalFaculties($conn);

// ===== Chart Data =====
$studentsByFaculty = getStudentsByFaculty($conn);
$studentsByStatus = getStudentsByStatus($conn);

// ===== Recent Data =====
$recentStudents = getRecentStudents($conn, 5);

$recentLogs = $conn
    ->query("SELECT * FROM audit_logs ORDER BY audit_id DESC LIMIT 5")
    ->fetch_all(MYSQLI_ASSOC);



// Phân bố kết quả học tập (Chart tròn phải)
// ===== Grade Distribution Data =====
// Phân bố kết quả học tập
// $sql = "
//     SELECT
//         CASE
//             WHEN score >= 8 THEN 'Giỏi'
//             WHEN score >= 6.5 THEN 'Khá'
//             WHEN score >= 5 THEN 'Trung bình'
//             ELSE 'Yếu'
//         END AS grade_level,
//         COUNT(*) AS total
//     FROM grades
//     GROUP BY grade_level
// ";
// $result = $conn->query($sql);
// Grade distribution data for chart
$gradeStats = [
    ['grade_level' => 'Giỏi', 'total' => 0],
    ['grade_level' => 'Khá', 'total' => 0],
    ['grade_level' => 'Trung bình', 'total' => 0],
    ['grade_level' => 'Yếu', 'total' => 0]
];

$sql = "
    SELECT
        CASE
            WHEN score >= 85 THEN 'Giỏi'
            WHEN score >= 70 THEN 'Khá'
            WHEN score >= 50 THEN 'Trung bình'
            ELSE 'Yếu'
        END AS grade_level,
        COUNT(*) AS total
    FROM grades
    WHERE score IS NOT NULL
    GROUP BY grade_level
";

$result = $conn->query($sql);
if ($result) {
    $gradeStats = $result->fetch_all(MYSQLI_ASSOC);
}



// ===== Layout =====
include __DIR__ . '/views/layout/header.php';
?>

  <style>
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .activity-item {
            display: flex;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        .activity-icon i {
            font-size: 1.2rem;
        }
        .activity-content {
            flex: 1;
        }
        .activity-text {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }
        .activity-time {
            color: var(--secondary-color);
            font-size: 0.85rem;
        }
        .bg-success {
            background: var(--success-color);
        }
        .bg-info {
            background: var(--info-color);
        }
        .bg-warning {
            background: var(--warning-color);
        }
        .bg-danger {
            background: var(--danger-color);
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 10px 0;
            min-width: 200px;
            display: none;
            z-index: 1000;
        }
        .dropdown-menu.active {
            display: block;
        }
        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .dropdown-menu a:hover {
            background: var(--light-color);
        }
        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 10px 0;
        }

        /* ================= DASHBOARD STATISTICS ================= */

/* Box tổng */
.stat-box {
    background: #fff;
    border-radius: 12px;
    padding: 20px 16px;
    height: 100%;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.1);
}

/* Viền trái theo màu */
.border-blue   { border-left: 5px solid #4e73df; }
.border-green  { border-left: 5px solid #1cc88a; }
.border-yellow { border-left: 5px solid #f6c23e; }
.border-red    { border-left: 5px solid #e74a3b; }
.border-cyan   { border-left: 5px solid #36b9cc; }
.border-indigo { border-left: 5px solid #6610f2; }

/* Nội dung */
.stat-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Icon */
.stat-icon {
    font-size: 30px;
    margin-bottom: 6px;
    opacity: 0.9;
}

/* Số liệu */
.stat-number {
    font-size: 30px;
    font-weight: 700;
    color: #2c2c2c;
    margin-bottom: 4px;
}

/* Tiêu đề */
.stat-title {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Màu icon */
.text-blue   { color: #4e73df; }
.text-green  { color: #1cc88a; }
.text-yellow { color: #f6c23e; }
.text-red    { color: #e74a3b; }
.text-cyan   { color: #36b9cc; }
.text-indigo { color: #6610f2; }

    </style>

<!-- ================= DASHBOARD CONTENT ================= -->

<div class="container-fluid">

    <!-- PAGE TITLE -->
    <div class="mb-4">
        <h2 class="fw-bold">📊 Dashboard</h2>
        <p class="text-muted mb-0">
            Chào mừng <strong><?= htmlspecialchars($adminInfo['fullname'] ?? 'Admin') ?></strong> - <?= htmlspecialchars($adminInfo['role_name'] ?? '') ?>
        </p>
    </div>

    <!-- STATISTICS -->
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="stat-box border-blue">
                <div class="stat-content">
                    <div class="stat-icon text-blue">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <div class="stat-number"><?= $totalStudents ?></div>
                    <div class="stat-title">SINH VIÊN</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-box border-green">
                <div class="stat-content">
                    <div class="stat-icon text-green">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div class="stat-number"><?= $totalLecturers ?></div>
                    <div class="stat-title">GIẢNG VIÊN</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-box border-red">
                <div class="stat-content">
                    <div class="stat-icon text-red">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stat-number"><?= $totalFaculties ?></div>
                    <div class="stat-title">KHOA</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-box border-indigo">
                <div class="stat-content">
                    <div class="stat-icon text-indigo">
                        <i class="bi bi-door-open-fill"></i>
                    </div>
                    <div class="stat-number"><?= $totalClasses ?></div>
                    <div class="stat-title">LỚP HỌC</div>
                </div>
            </div>
        </div>

    </div>

    <!-- RECENT LOGS
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">
            🕒 Hoạt động gần đây
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Hành động</th>
                        <th>Bảng</th>
                        <th>IP</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLogs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['username']) ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['table_name']) ?></td>
                            <td><?= htmlspecialchars($log['ip_address']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentLogs)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Chưa có hoạt động nào
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div> -->

  <!-- ================== CHARTS ================== -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="content-card-title">📊 Thống kê sinh viên theo khoa</h5>
            </div>
            <div class="content-card-body">
                <canvas id="studentsByFacultyChart" height="165"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="content-card-title">🎯 Tỷ lệ kết quả học tập</h5>
            </div>
            <div class="content-card-body">
                <canvas id="gradeDistributionChart" height="160"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ================== RECENT DATA ================== -->
<div class="row">
    <!-- Sinh viên mới -->
    <div class="col-lg-8">
        <div class="content-card">
            <div class="content-card-header d-flex justify-content-between align-items-center">
                <h5 class="content-card-title mb-0">🧑‍🎓 Sinh viên mới nhập học</h5>
                <a href="admin-students.php" class="btn btn-primary btn-sm">Xem tất cả</a>
            </div>

            <div class="content-card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>MSSV</th>
                            <th>Họ và tên</th>
                            <th>Khoa</th>
                            <th>Lớp</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentStudents as $sv): ?>
                        <tr>
                            <td><?= htmlspecialchars($sv['student_code']) ?></td>
                            <td><?= htmlspecialchars($sv['full_name']) ?></td>
                            <td><?= htmlspecialchars($sv['faculty_name']) ?></td>
                            <td><?= htmlspecialchars($sv['base_class_name'] ?? '—') ?></td>
                            <td>
                                <?php
                                $statusClass = match($sv['status']) {
                                    'Đang học' => 'badge-success',
                                    'Bảo lưu' => 'badge-warning',
                                    'Đã tốt nghiệp' => 'badge-info',
                                    default => 'badge-secondary'
                                };
                                ?>
                                <span class="badge-custom <?= $statusClass ?>">
                                    <?= htmlspecialchars($sv['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentStudents)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Chưa có dữ liệu sinh viên
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Hoạt động -->
    <div class="col-lg-4">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="content-card-title">🕒 Hoạt động gần đây</h5>
            </div>

            <div class="content-card-body">
                <div class="activity-list">
                    <?php foreach ($recentLogs as $log): ?>
                    <?php
                    $iconClass = match($log['action']) {
                        'CREATE' => 'bg-success',
                        'UPDATE' => 'bg-info',
                        'DELETE' => 'bg-danger',
                        default => 'bg-warning'
                    };
                    $icon = match($log['action']) {
                        'CREATE' => 'bi-plus-circle',
                        'UPDATE' => 'bi-pencil',
                        'DELETE' => 'bi-trash',
                        default => 'bi-eye'
                    };
                    $timeAgo = time() - strtotime($log['created_at']);
                    if ($timeAgo < 60) {
                        $timeText = $timeAgo . ' giây trước';
                    } elseif ($timeAgo < 3600) {
                        $timeText = floor($timeAgo / 60) . ' phút trước';
                    } elseif ($timeAgo < 86400) {
                        $timeText = floor($timeAgo / 3600) . ' giờ trước';
                    } else {
                        $timeText = date('d/m/Y H:i', strtotime($log['created_at']));
                    }
                    ?>
                    <div class="activity-item">
                        <div class="activity-icon <?= $iconClass ?>">
                            <i class="bi <?= $icon ?>"></i>
                        </div>
                        <div>
                            <p class="activity-text"><?= htmlspecialchars($log['action']) ?> - <?= htmlspecialchars($log['table_name']) ?></p>
                            <small class="text-muted"><?= htmlspecialchars($log['username']) ?> - <?= $timeText ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recentLogs)): ?>
                    <div class="text-center text-muted py-4">
                        Chưa có hoạt động nào
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<!-- Chuẩn bị dữ liệu -->
<script>
const studentsByFacultyLabels = <?= json_encode(array_column($studentsByFaculty, 'faculty_name')) ?>;
const studentsByFacultyData   = <?= json_encode(array_column($studentsByFaculty, 'total')) ?>;

const gradeLabels = <?= json_encode(array_column($gradeStats, 'grade_level')) ?>;
const gradeData   = <?= json_encode(array_column($gradeStats, 'total')) ?>;
</script>


<!-- Chart sinh viên theo khoa (Bar chart) -->
<script>
new Chart(document.getElementById('studentsByFacultyChart'), {
    type: 'bar',
    data: {
        labels: studentsByFacultyLabels,
        datasets: [{
            label: 'Số sinh viên',
            data: studentsByFacultyData,
            backgroundColor: '#4e73df'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>

<!-- Chart phân bố kết quả học tập (Doughnut) -->
<script>
new Chart(document.getElementById('gradeDistributionChart'), {
    type: 'doughnut',
    data: {
        labels: gradeLabels,
        datasets: [{
            data: gradeData,
            backgroundColor: [
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b'
            ]
        }]
    },
    options: {
        responsive: true
    }
});
</script>



<?php include __DIR__ . '/views/layout/footer.php'; ?>
              