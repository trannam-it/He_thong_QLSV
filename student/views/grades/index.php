<?php
// $r['semester']

/**
 * View: Kết quả học tập sinh viên
 * Biến: $student, $allRows, $displayRows, $bySemester, $totalEnrolled,
 *       $totalCompleted, $totalFailed, $totalCredits, $overallGPA,
 *       $gradeDist, $chartLabels, $chartScores,
 *       $filterSem, $filterYear, $filterStatus, $years
 */
$pageTitle   = 'Kết quả học tập';
$currentPage = 'student_grades';
$extraCss    = '
.grade-badge { min-width:42px; display:inline-block; text-align:center; font-size:.9rem; }
.score-bar-wrap { min-width:80px; }
.score-bar { height:6px; border-radius:3px; }
.sem-header {
    background: linear-gradient(90deg,#4f46e5,#7c3aed);
    color:#fff; padding:.5rem 1rem; border-radius:.4rem;
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:.5rem;
}
.table > :not(caption) > * > * { vertical-align:middle; }
@media print {
    .sidebar,.topbar,.no-print { display:none !important; }
    .main-content { margin:0 !important; }
}
';

// Helper functions (local to this view)
function gpaRank(?float $gpa): array {
    if ($gpa === null) return ['—', 'secondary'];
    if ($gpa >= 90) return ['Xuất sắc', 'success'];
    if ($gpa >= 80) return ['Giỏi', 'primary'];
    if ($gpa >= 70) return ['Khá', 'info'];
    if ($gpa >= 60) return ['Trung bình', 'warning'];
    return ['Yếu', 'danger'];
}
function glColor(?string $gl): string {
    return match ($gl) {
        'A+','A'  => 'success',
        'B+','B'  => 'primary',
        'C+','C'  => 'info',
        'D+','D'  => 'warning',
        'F'       => 'danger',
        default   => 'secondary',
    };
}
[$rankLabel, $rankColor] = gpaRank($overallGPA);
$hasGrades = array_sum($gradeDist) > 0;
?>

<!-- Breadcrumb -->
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1 class="page-title">Kết quả học tập</h1>
        <div class="page-breadcrumb">
            <a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Kết quả học tập
        </div>
    </div>
    <div class="no-print d-flex gap-2">
        <button onclick="exportCSV()" class="btn btn-sm btn-outline-success">
            <i class="bi bi-download me-1"></i>Xuất CSV
        </button>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>In bảng điểm
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card primary">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Điểm TB tích lũy</div>
                    <div class="stat-card-value"><?= $overallGPA !== null ? number_format($overallGPA,1) : '—' ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-star-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card success">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Tín chỉ hoàn thành</div>
                    <div class="stat-card-value"><?= $totalCredits ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-bookmark-star"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card warning">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Môn đã hoàn thành</div>
                    <div class="stat-card-value"><?= $totalCompleted ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-patch-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card <?= $totalFailed ? 'danger' : 'info' ?>">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Xếp loại</div>
                    <div class="stat-card-value" style="font-size:1.1rem"><?= $rankLabel ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-award"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="content-card h-100">
            <div class="content-card-header">
                <h5 class="content-card-title">
                    <i class="bi bi-graph-up me-2"></i>Điểm TB theo học kỳ
                </h5>
            </div>
            <div class="content-card-body">
                <?php if (empty($chartLabels)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-bar-chart" style="font-size:2.5rem"></i>
                    <p class="mt-2">Chưa có dữ liệu điểm</p>
                </div>
                <?php else: ?>
                <canvas id="chartSemGPA" height="100"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="content-card h-100">
            <div class="content-card-header">
                <h5 class="content-card-title">
                    <i class="bi bi-pie-chart me-2"></i>Phân phối điểm chữ
                </h5>
            </div>
            <div class="content-card-body d-flex align-items-center justify-content-center">
                <?php if (!$hasGrades): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-pie-chart" style="font-size:2.5rem"></i>
                    <p class="mt-2">Chưa có điểm</p>
                </div>
                <?php else: ?>
                <canvas id="chartDist" style="max-height:220px"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Filter bar -->
<div class="content-card mb-3 no-print">
    <div class="content-card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Học kỳ</label>
                <select name="semester" class="form-select form-select-sm">
                    <option value="">Tất cả học kỳ</option>
                    <option value="Spring" <?= $filterSem==='Spring'?'selected':''?>>Học kỳ I (Spring)</option>
                    <option value="Summer" <?= $filterSem==='Summer'?'selected':''?>>Học kỳ Hè (Summer)</option>
                    <option value="Fall"   <?= $filterSem==='Fall'  ?'selected':''?>>Học kỳ II (Fall)</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Năm học</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">Tất cả năm</option>
                    <?php foreach ($years as $y): ?>
                    <option value="<?= $y ?>" <?= $filterYear==$y?'selected':''?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">Trạng thái</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tất cả</option>
                    <option value="Completed"  <?= $filterStatus==='Completed'  ?'selected':''?>>Hoàn thành</option>
                    <option value="Registered" <?= $filterStatus==='Registered' ?'selected':''?>>Đang học</option>
                    <option value="Failed"     <?= $filterStatus==='Failed'     ?'selected':''?>>Không đạt</option>
                    <option value="Cancelled"  <?= $filterStatus==='Cancelled'  ?'selected':''?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Lọc
                </button>
                <a href="<?= BASE_URL ?>/student/?page=grades" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Grade table by semester -->
<?php if (empty($displayRows)): ?>
<div class="content-card">
    <div class="content-card-body text-center text-muted py-5">
        <i class="bi bi-clipboard-x" style="font-size:3rem"></i>
        <p class="mt-2">Không có kết quả phù hợp với bộ lọc.</p>
    </div>
</div>
<?php else: ?>

<?php
$grouped = [];
foreach ($displayRows as $r) {
    // $key = $r['year'].'_'.$r['semester_name'];semLabel
    $key = $r['year'].'_'.$r['semester'];
    $grouped[$key][] = $r;
}
?>

<?php foreach ($grouped as $semKey => $rows):
    $semInfo  = $bySemester[$semKey] ?? null;
    $semLabel = match($rows[0]['semester']) 
    {
        'Spring' => 'Học kỳ I', 'Summer' => 'Học kỳ Hè', 'Fall' => 'Học kỳ II', default => $rows[0]['semester']
    };
    $semGPA  = $semInfo ? $semInfo['gpa'] : null;
    $semCred = $semInfo ? $semInfo['credits'] : 0;
?>
<div class="content-card mb-3">
    <div class="sem-header" role="button"
         onclick="this.nextElementSibling.classList.toggle('d-none')">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar2-week"></i>
            <strong><?= $semLabel ?> – Năm học <?= $rows[0]['year'] ?></strong>
            <span class="badge bg-white text-dark"><?= count($rows) ?> môn</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <?php if ($semGPA): ?>
            <span class="badge bg-white text-primary">TB: <?= number_format($semGPA,2) ?></span>
            <span class="badge bg-white text-success"><?= $semCred ?> TC</span>
            <?php endif; ?>
            <i class="bi bi-chevron-down"></i>
        </div>
    </div>
    <div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px" class="text-center">#</th>
                        <th>Mã môn</th>
                        <th>Tên môn học</th>
                        <th class="text-center">TC</th>
                        <th>Mã lớp</th>
                        <th>Giảng viên</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center" style="min-width:120px">Điểm số</th>
                        <th class="text-center">Điểm chữ</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $i => $r):
                    $sc = $r['score'];
                    $gl = $r['grade_letter'];
                    $pct = $sc !== null ? min(100, round($sc)) : 0;
                    $barColor = match(true) {
                        $sc >= 80 => '#198754',
                        $sc >= 65 => '#0d6efd',
                        $sc >= 50 => '#ffc107',
                        $sc !== null => '#dc3545',
                        default => '#adb5bd',
                    };
                    $statusLabel = match($r['enroll_status']) {
                        'Completed'  => ['Hoàn thành','success'],
                        'Registered' => ['Đang học','primary'],
                        'Failed'     => ['Không đạt','danger'],
                        'Cancelled'  => ['Đã hủy','secondary'],
                        default      => [$r['enroll_status'],'secondary'],
                    };
                ?>
                <tr>
                    <td class="text-center text-muted small"><?= $i+1 ?></td>
                    <td><code><?= htmlspecialchars($r['subject_code']) ?></code></td>
                    <td><?= htmlspecialchars($r['subject_name']) ?></td>
                    <td class="text-center"><span class="badge bg-info"><?= (int)$r['credit_hours'] ?></span></td>
                    <td><small class="text-muted"><?= htmlspecialchars($r['class_code']) ?></small></td>
                    <td><small><?= htmlspecialchars($r['lecturer_name'] ?? '—') ?></small></td>
                    <td class="text-center">
                        <span class="badge bg-<?= $statusLabel[1] ?>"><?= $statusLabel[0] ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($sc !== null): ?>
                        <div class="score-bar-wrap mx-auto" style="max-width:110px">
                            <div class="fw-bold mb-1"><?= number_format($sc,1) ?></div>
                            <div class="score-bar w-100 bg-light">
                                <div class="score-bar" style="width:<?= $pct ?>%;background:<?= $barColor ?>"></div>
                            </div>
                        </div>
                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($gl): ?>
                        <span class="badge grade-badge bg-<?= glColor($gl) ?>"><?= htmlspecialchars($gl) ?></span>
                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
                <?php if ($semGPA): ?>
                <tfoot>
                    <tr class="table-light fw-semibold">
                        <td colspan="3" class="text-end text-muted">Tổng kết học kỳ:</td>
                        <td class="text-center"><?= $semCred ?> TC</td>
                        <td colspan="3"></td>
                        <td class="text-center"><?= number_format($semGPA,2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php if ($overallGPA !== null): ?>
<div class="content-card mb-4">
    <div class="content-card-body py-3">
        <div class="row align-items-center g-2">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Điểm TB tích lũy</div>
                        <div class="fs-3 fw-bold text-primary"><?= number_format($overallGPA,2) ?>/100</div>
                    </div>
                    <span class="badge bg-<?= $rankColor ?> fs-6 px-3 py-2"><?= $rankLabel ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <?php foreach ($gradeDist as $gl => $cnt): if ($cnt === 0) continue; ?>
                    <span class="badge bg-<?= glColor($gl) ?> px-2"><?= $gl ?>: <?= $cnt ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <div class="progress" style="height:12px;border-radius:6px">
                <div class="progress-bar bg-<?= $rankColor ?>"
                     style="width:<?= min(100,$overallGPA) ?>%;border-radius:6px"></div>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">0</small>
                <small class="text-muted">50</small>
                <small class="text-muted">100</small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
<?php if (!empty($chartLabels)): ?>
new Chart(document.getElementById('chartSemGPA'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Điểm TB',
            data: <?= json_encode($chartScores) ?>,
            backgroundColor: 'rgba(79,70,229,0.7)',
            borderColor: '#4f46e5',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        scales: { y: { min:0, max:100, ticks:{stepSize:20}, grid:{color:'#f0f0f0'} } },
        plugins: {
            legend: {display:false},
            tooltip: {callbacks:{label:ctx=>' '+ctx.parsed.y.toFixed(2)+' / 100'}}
        }
    }
});
<?php endif; ?>

<?php if ($hasGrades): ?>
const distColors = ['#198754','#20c997','#0d6efd','#6ea8fe','#0dcaf0','#6edff6','#ffc107','#ffda6a','#dc3545'];
new Chart(document.getElementById('chartDist'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($gradeDist)) ?>,
        datasets: [{data: <?= json_encode(array_values($gradeDist)) ?>, backgroundColor: distColors, borderWidth: 2}]
    },
    options: {
        responsive: true,
        plugins: {legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}
    }
});
<?php endif; ?>

function exportCSV() {
    const bom = '\uFEFF';
    let rows = [['Mã môn','Tên môn','TC','Mã lớp','Học kỳ','Năm','Trạng thái','Điểm','Điểm chữ']];
    <?php
    $jsRows = [];
    foreach ($allRows as $r) {
        $semLabel = match($r['semester_name']) { 'Spring'=>'HK I','Summer'=>'HK Hè','Fall'=>'HK II',default=>$r['semester_name']};
        $stLabel  = match($r['enroll_status']) { 'Completed'=>'Hoàn thành','Registered'=>'Đang học','Failed'=>'Không đạt','Cancelled'=>'Đã hủy',default=>$r['enroll_status']};
        $jsRows[] = [
            $r['subject_code'], $r['subject_name'], (string)$r['credit_hours'],
            $r['class_code'], $semLabel, (string)$r['year'], $stLabel,
            $r['score'] !== null ? (string)$r['score'] : '',
            $r['grade_letter'] ?? '',
        ];
    }
    echo 'const csvData = '.json_encode($jsRows, JSON_UNESCAPED_UNICODE).';';
    ?>
    rows = rows.concat(csvData);
    const csv = bom + rows.map(r=>r.map(v=>'"'+String(v).replace(/"/g,'""')+'"').join(',')).join('\n');
    const a = document.createElement('a');
    a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    a.download = 'ket_qua_hoc_tap_<?= htmlspecialchars($student['student_code']) ?>.csv';
    a.click();
}
</script>

