<?php
/**
 * View: Lịch học sinh viên
 * Biến: $student, $semesterOptions, $filterSemesterId,
 *       $rows, $grid, $colorMap, $classes, $hasSchedule,
 *       $periodTimes, $dayNames
 */
$pageTitle   = 'Lịch học';
$currentPage = 'student_schedule';
$extraCss    = '
.timetable { border-collapse:collapse; width:100%; table-layout:fixed; }
.timetable th, .timetable td { border:1px solid #dee2e6; vertical-align:top; padding:0; }
.timetable .col-period { width:90px; }
.timetable .col-day    { width:calc((100% - 90px) / 6); }
.period-label { background:#f8f9fa; text-align:center; padding:6px 4px; font-size:.78rem; color:#495057; line-height:1.4; }
.day-header  { background:#343a40; color:#fff; text-align:center; padding:10px 4px; font-weight:600; font-size:.85rem; }
.empty-cell  { background:#fff; min-height:52px; height:52px; }
.break-row td { background:#fff3cd; text-align:center; font-size:.78rem; color:#856404; padding:3px; }
.class-block { border-radius:4px; padding:6px 8px; margin:3px; color:#fff; font-size:.78rem; line-height:1.4; height:calc(100% - 6px); min-height:46px; }
.class-block .subject { font-weight:700; font-size:.82rem; margin-bottom:2px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
.class-block .room    { opacity:.9; }
.class-block .code    { opacity:.75; font-size:.72rem; }
.legend-dot { width:14px; height:14px; border-radius:3px; display:inline-block; margin-right:6px; flex-shrink:0; }
.timetable-wrapper { overflow-x:auto; }
@media (max-width:768px) { .timetable .col-period{width:70px;} .class-block .subject{font-size:.75rem;} }
@media print { .sidebar,.topbar,.no-print{display:none!important;} .main-content{margin:0!important;padding:0!important;} }
';
?>

<!-- Page Header -->
<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h1 class="page-title"><i class="bi bi-calendar3 me-2"></i>Lịch học</h1>
        <div class="page-breadcrumb"><a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Lịch học</div>
    </div>
    <div class="no-print">
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>In lịch
        </button>
    </div>
</div>

<!-- Semester Filter -->
<?php if (!empty($semesterOptions)): ?>
<div class="card shadow-sm mb-3 no-print">
    <div class="card-body py-2">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <label class="fw-semibold mb-0">Học kỳ:</label>
            <?php foreach ($semesterOptions as $opt):
                // $active = ($opt['semester'] === $filterSemester && (int)$opt['year'] === $filterYear);
                // $lab    = formatSemester($opt['semester']) . ' ' . $opt['year'];
                $active = ((int)$opt['semester_id'] === (int)$filterSemesterId);
                $lab    = $opt['semester_name'];

            ?>
            <a href="?semester_id=<?= $opt['semester_id'] ?>"
               class="btn btn-sm <?= $active ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= htmlspecialchars($lab) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (empty($classes)): ?>
<div class="content-card">
    <div class="content-card-body text-center py-5 text-muted">
        <i class="bi bi-calendar-x" style="font-size:3rem"></i>
        <p class="mt-2">Bạn chưa đăng ký học phần nào<?= $filterSemester ? ' trong học kỳ này' : '' ?>.</p>
        <a href="<?= BASE_URL ?>/student/?page=enrollment" class="btn btn-primary btn-sm mt-2">
            <i class="bi bi-journal-plus me-1"></i>Đăng ký học phần
        </a>
    </div>
</div>
<?php else: ?>

<!-- Legend -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <span class="fw-semibold me-1">Chú thích:</span>
            <?php foreach ($classes as $cid => $c): ?>
            <div class="d-flex align-items-center">
                <span class="legend-dot" style="background:<?= $colorMap[$cid] ?>"></span>
                <small><?= htmlspecialchars($c['subject_code']) ?> – <?= htmlspecialchars($c['subject_name']) ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Timetable Grid -->
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">
        <i class="bi bi-table me-1"></i>Thời khóa biểu
        <?php if ($filterSemester && $filterYear): ?>
        – <?= htmlspecialchars(formatSemester($filterSemester)) ?> <?= $filterYear ?>
        <?php endif; ?>
    </div>
    <div class="card-body p-2">
        <?php if (!$hasSchedule): ?>
        <div class="alert alert-warning mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Các lớp bạn đăng ký chưa có thông tin lịch học cụ thể. Vui lòng liên hệ phòng đào tạo.
        </div>
        <?php else: ?>
        <div class="timetable-wrapper">
        <table class="timetable">
            <colgroup>
                <col class="col-period">
                <?php for ($d = 2; $d <= 7; $d++): ?><col class="col-day"><?php endfor; ?>
            </colgroup>
            <thead>
                <tr>
                    <th class="period-label" style="background:#343a40;color:#fff;text-align:center;vertical-align:middle;font-size:.8rem;">
                        Tiết / Thứ
                    </th>
                    <?php for ($d = 2; $d <= 7; $d++): ?>
                    <th class="day-header"><?= $dayNames[$d] ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
            <?php for ($period = 1; $period <= 10; $period++):
                if ($period === 6): ?>
                <tr class="break-row">
                    <td colspan="7"><i class="bi bi-cup-hot me-1"></i>Nghỉ trưa 11:35 – 13:00</td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="period-label">
                        <div class="fw-bold">Tiết <?= $period ?></div>
                        <div><?= $periodTimes[$period][0] ?></div>
                        <div><?= $periodTimes[$period][1] ?></div>
                    </td>
                    <?php for ($day = 2; $day <= 7; $day++):
                        $cell = $grid[$day][$period] ?? null;
                        if ($cell !== null && $cell['type'] === 'occupied') continue;
                        if ($cell === null): ?>
                            <td class="empty-cell"></td>
                        <?php else:
                            $d = $cell['data'];
                            $color = $colorMap[$d['class_id']]; ?>
                            <td rowspan="<?= $cell['span'] ?>" style="padding:0;vertical-align:top;">
                                <div class="class-block" style="background:<?= $color ?>">
                                    <div class="subject"><?= htmlspecialchars($d['subject_name']) ?></div>
                                    <?php if ($d['room']): ?>
                                    <div class="room"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($d['room']) ?></div>
                                    <?php endif; ?>
                                    <div class="code"><?= htmlspecialchars($d['class_code']) ?></div>
                                    <div class="code">Tiết <?= $d['start_period'] ?>–<?= $d['end_period'] ?></div>
                                </div>
                            </td>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- List View -->
<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">
        <i class="bi bi-list-columns me-1"></i>Chi tiết các môn đang học
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:8px"></th>
                        <th>Môn học</th><th>Mã lớp</th>
                        <th class="text-center">Tín chỉ</th>
                        <th>Giảng viên</th><th>Lịch học</th><th>Phòng</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $byClass = [];
                foreach ($rows as $r) {
                    if (!isset($byClass[$r['class_id']])) $byClass[$r['class_id']] = ['info' => $r, 'schedules' => []];
                    if ($r['schedule_id']) $byClass[$r['class_id']]['schedules'][] = $r;
                }
                foreach ($byClass as $cid => $entry):
                    $info   = $entry['info'];
                    $scheds = $entry['schedules'];
                    $color  = $colorMap[$cid];
                    $schedStrings = [];
                    foreach ($scheds as $s) {
                        $schedStrings[] = $dayNames[$s['day_of_week']]
                            . ' tiết ' . $s['start_period'] . '–' . $s['end_period']
                            . ' (' . $periodTimes[$s['start_period']][0] . ' – ' . $periodTimes[$s['end_period']][1] . ')';
                    }
                    $rooms = array_unique(array_filter(array_column($scheds, 'room')));
                ?>
                <tr>
                    <td style="background:<?= $color ?>;padding:0;width:6px"></td>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($info['subject_name']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($info['subject_code']) ?></small>
                    </td>
                    <td><code><?= htmlspecialchars($info['class_code']) ?></code></td>
                    <td class="text-center"><span class="badge bg-info"><?= (int)$info['credit_hours'] ?> TC</span></td>
                    <td><?= htmlspecialchars($info['lecturer_name'] ?? '—') ?></td>
                    <td>
                        <?php if (!empty($schedStrings)): ?>
                            <?php foreach ($schedStrings as $ss): ?>
                            <div><i class="bi bi-clock me-1 text-muted"></i><?= htmlspecialchars($ss) ?></div>
                            <?php endforeach; ?>
                        <?php else: ?><span class="text-muted">Chưa có lịch</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($rooms)): ?>
                            <i class="bi bi-geo-alt me-1 text-muted"></i><?= htmlspecialchars(implode(', ', $rooms)) ?>
                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php endif; ?>

$active = ($opt['semester'] === $filterSemester && (int)$opt['year'] === $filterYear);
$lab    = formatSemester($opt['semester']) . ' ' . $opt['year'];
