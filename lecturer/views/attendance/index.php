<?php
/**
 * View: Điểm danh sinh viên
 * Biến: $lecturer, $myClasses, $classInfo, $classId,
 *       $selDate, $viewTab, $students, $existingAtt,
 *       $historyDates, $historyDetail, $historyDay,
 *       $studentSummary, $totalSessions,
 *       $flashSuccess, $flashError
 */
$pageTitle = 'Điểm danh';
$extraCss  = '
.att-btn-group .btn{min-width:90px;}
.att-badge-present{background:#16a34a;color:#fff;}
.att-badge-absent {background:#dc2626;color:#fff;}
.att-badge-late   {background:#ca8a04;color:#fff;}
.att-badge-excused{background:#2563eb;color:#fff;}
';
?>

<div class="page-header">
    <h1 class="page-title">Điểm danh sinh viên</h1>
    <div class="page-breadcrumb">
        <a href="<?= lUrl() ?>">Trang chủ</a> /
        <a href="<?= lUrl('classes') ?>">Lớp đang dạy</a> / Điểm danh
    </div>
</div>

<!-- Flash -->
<?php if ($flashSuccess): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flashSuccess) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($flashError) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Class selector -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap" id="classForm">
            <input type="hidden" name="page"     value="attendance">
            <input type="hidden" name="tab"      value="<?= htmlspecialchars($viewTab) ?>">
            <input type="hidden" name="att_date" value="<?= htmlspecialchars($selDate) ?>">
            <label class="fw-semibold text-muted small mb-0">Chọn lớp:</label>
            <select name="class_id" class="form-select form-select-sm" style="max-width:380px"
                    onchange="this.form.submit()">
                <option value="">-- Chọn lớp để điểm danh --</option>
                <?php foreach ($myClasses as $mc): ?>
                <option value="<?= $mc['class_id'] ?>" <?= $classId == $mc['class_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mc['class_code']) ?> –
                    <?= htmlspecialchars($mc['subject_name']) ?>
                    (<?= LecturerModel::formatSemester($mc['semester']) ?> <?= $mc['year'] ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<?php if (!$classInfo): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-person-check fs-1 d-block mb-2 opacity-40"></i>
    <p>Chọn lớp học để bắt đầu điểm danh</p>
</div>

<?php else: ?>

<!-- Class info banner -->
<div class="card shadow-sm mb-4"
     style="background:linear-gradient(135deg,#0f766e,#0369a1);color:#fff;border:none">
    <div class="card-body d-flex align-items-center gap-4 flex-wrap py-3">
        <div>
            <div class="fs-5 fw-bold"><?= htmlspecialchars($classInfo['class_code']) ?></div>
            <div class="opacity-90"><?= htmlspecialchars($classInfo['subject_name']) ?></div>
        </div>
        <div class="vr opacity-50 d-none d-md-block" style="height:40px"></div>
        <div class="d-flex gap-4 flex-wrap opacity-90 small">
            <span><i class="bi bi-calendar3 me-1"></i><?= LecturerModel::formatSemester($classInfo['semester']) ?> <?= $classInfo['year'] ?></span>
            <span><i class="bi bi-people me-1"></i><?= count($students) ?> sinh viên</span>
            <span><i class="bi bi-calendar-check me-1"></i><?= $totalSessions ?> buổi đã điểm danh</span>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4">
    <?php foreach ([
        ['take',    'clipboard-check', 'Điểm danh'],
        ['history', 'clock-history',   'Lịch sử điểm danh'],
        ['summary', 'bar-chart',       'Tổng hợp SV'],
    ] as [$tab, $icon, $label]): ?>
    <li class="nav-item">
        <a class="nav-link <?= $viewTab === $tab ? 'active' : '' ?>"
           href="<?= lUrl('attendance') ?>&class_id=<?= $classId ?>&tab=<?= $tab ?><?= $tab === 'take' ? '&att_date=' . urlencode($selDate) : '' ?>">
            <i class="bi bi-<?= $icon ?> me-1"></i><?= $label ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>

<!-- ═══ TAB: TAKE ATTENDANCE ═══ -->
<?php if ($viewTab === 'take'): ?>

<form method="POST" action="<?= lUrl('attendance') ?>" id="attendanceForm">
    <input type="hidden" name="action"   value="save_attendance">
    <input type="hidden" name="class_id" value="<?= $classId ?>">

    <!-- Date picker -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <label class="fw-semibold text-muted small mb-0">
                    <i class="bi bi-calendar-date me-1"></i>Ngày điểm danh:
                </label>
                <input type="date" name="att_date" id="attDate"
                       value="<?= htmlspecialchars($selDate) ?>"
                       max="<?= date('Y-m-d') ?>"
                       class="form-control form-control-sm" style="width:160px">
                <div class="ms-auto d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('Present')">
                        <i class="bi bi-check-all me-1"></i>Tất cả có mặt
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('Absent')">
                        <i class="bi bi-x-circle me-1"></i>Tất cả vắng
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetAll()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Đặt lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($students)): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-people fs-1 d-block mb-2 opacity-40"></i>
        <p>Lớp chưa có sinh viên đăng ký</p>
    </div>
    <?php else: ?>
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold text-muted small">
                <?= count($students) ?> sinh viên
                <?php if (!empty($existingAtt)): ?>
                <span class="badge bg-success ms-2">Đã có điểm danh cho ngày này</span>
                <?php endif; ?>
            </span>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-save me-1"></i>Lưu điểm danh
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th style="width:110px">Mã SV</th>
                            <th>Họ và tên</th>
                            <th class="text-center" style="width:320px">Trạng thái</th>
                            <th style="width:200px">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $i => $sv):
                        $curStatus = $existingAtt[$sv['student_id']]['status'] ?? 'Present';
                        $curNote   = $existingAtt[$sv['student_id']]['note']   ?? '';
                    ?>
                    <tr id="row-<?= $sv['student_id'] ?>">
                        <td class="text-center text-muted small"><?= $i + 1 ?></td>
                        <td><code class="text-primary fw-semibold"><?= htmlspecialchars($sv['student_code']) ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($sv['full_name']) ?></td>
                        <td class="text-center">
                            <input type="hidden"
                                   name="statuses[<?= $sv['student_id'] ?>]"
                                   id="status-<?= $sv['student_id'] ?>"
                                   value="<?= htmlspecialchars($curStatus) ?>">
                            <div class="btn-group btn-group-sm att-btn-group" role="group">
                                <?php foreach ([
                                    'Present' => ['success', 'bi-check-circle', 'Có mặt'],
                                    'Absent'  => ['danger',  'bi-x-circle',     'Vắng'],
                                    'Late'    => ['warning', 'bi-clock',        'Trễ'],
                                    'Excused' => ['primary', 'bi-bookmark',     'Phép'],
                                ] as $val => [$color, $icon, $lbl]): ?>
                                <button type="button"
                                        class="btn btn-outline-<?= $color ?> <?= $curStatus === $val ? 'active' : '' ?>"
                                        data-sid="<?= $sv['student_id'] ?>"
                                        data-val="<?= $val ?>"
                                        onclick="setStatus(<?= $sv['student_id'] ?>, '<?= $val ?>', this)">
                                    <i class="bi <?= $icon ?> me-1"></i><?= $lbl ?>
                                </button>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <input type="text"
                                   name="notes[<?= $sv['student_id'] ?>]"
                                   class="form-control form-control-sm"
                                   placeholder="Ghi chú…"
                                   value="<?= htmlspecialchars($curNote) ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end py-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Lưu điểm danh
            </button>
        </div>
    </div>
    <?php endif; ?>
</form>

<!-- ═══ TAB: HISTORY ═══ -->
<?php elseif ($viewTab === 'history'): ?>

<?php if (!empty($historyDay) && !empty($historyDetail)): ?>
<!-- Detail of a specific day -->
<div class="mb-3">
    <a href="<?= lUrl('attendance') ?>&class_id=<?= $classId ?>&tab=history"
       class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại lịch sử
    </a>
    <span class="ms-2 text-muted small">Chi tiết điểm danh ngày <?= $historyDay ?></span>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã SV</th>
                        <th>Họ và tên</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($historyDetail as $i => $row): ?>
                <tr>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td><code class="text-primary"><?= htmlspecialchars($row['student_code']) ?></code></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td class="text-center">
                        <?php
                        $badgeMap = ['Present'=>'success','Absent'=>'danger','Late'=>'warning','Excused'=>'primary'];
                        $lblMap   = ['Present'=>'Có mặt','Absent'=>'Vắng','Late'=>'Trễ','Excused'=>'Phép'];
                        $bc = $badgeMap[$row['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $bc ?>"><?= $lblMap[$row['status']] ?? $row['status'] ?></span>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($row['note'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>
<!-- History list by date -->
<?php if (empty($historyDates)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-40"></i>
    <p>Chưa có buổi điểm danh nào</p>
</div>
<?php else: ?>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ngày</th>
                        <th class="text-center">Tổng</th>
                        <th class="text-center text-success">Có mặt</th>
                        <th class="text-center text-danger">Vắng</th>
                        <th class="text-center text-warning">Trễ</th>
                        <th class="text-center text-primary">Phép</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($historyDates as $i => $d): ?>
                <tr>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td class="fw-semibold"><?= $d['date'] ?></td>
                    <td class="text-center"><?= $d['total'] ?></td>
                    <td class="text-center text-success fw-bold"><?= $d['present'] ?></td>
                    <td class="text-center text-danger fw-bold"><?= $d['absent'] ?></td>
                    <td class="text-center text-warning fw-bold"><?= $d['late'] ?></td>
                    <td class="text-center text-primary fw-bold"><?= $d['excused'] ?></td>
                    <td class="text-center">
                        <a href="<?= lUrl('attendance') ?>&class_id=<?= $classId ?>&tab=history&hist_date=<?= $d['date'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Chi tiết
                        </a>
                        <form method="POST" action="<?= lUrl('attendance') ?>" style="display:inline"
                              onsubmit="return confirm('Xóa toàn bộ điểm danh ngày <?= $d['date'] ?>?')">
                            <input type="hidden" name="action"   value="delete_attendance_day">
                            <input type="hidden" name="class_id" value="<?= $classId ?>">
                            <input type="hidden" name="del_date" value="<?= $d['date'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- ═══ TAB: SUMMARY ═══ -->
<?php elseif ($viewTab === 'summary'): ?>

<?php if (empty($studentSummary)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-40"></i>
    <p>Chưa có dữ liệu điểm danh</p>
</div>
<?php else: ?>
<div class="card shadow-sm">
    <div class="card-header bg-white py-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-bar-chart me-2 text-info"></i>
            Tổng hợp điểm danh sinh viên (<?= $totalSessions ?> buổi)
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã SV</th>
                        <th>Họ và tên</th>
                        <th class="text-center text-success">Có mặt</th>
                        <th class="text-center text-danger">Vắng</th>
                        <th class="text-center text-warning">Trễ</th>
                        <th class="text-center text-primary">Phép</th>
                        <th class="text-center">Tỷ lệ</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($studentSummary as $i => $sv):
                    $total  = (int)$sv['total_sessions'];
                    $pct    = $total > 0 ? round($sv['present'] / $total * 100) : 0;
                    $barClr = $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td><code class="text-primary"><?= htmlspecialchars($sv['student_code']) ?></code></td>
                    <td class="fw-semibold"><?= htmlspecialchars($sv['full_name']) ?></td>
                    <td class="text-center text-success fw-bold"><?= $sv['present'] ?></td>
                    <td class="text-center text-danger fw-bold"><?= $sv['absent'] ?></td>
                    <td class="text-center text-warning fw-bold"><?= $sv['late'] ?></td>
                    <td class="text-center text-primary fw-bold"><?= $sv['excused'] ?></td>
                    <td style="width:150px">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px">
                                <div class="progress-bar bg-<?= $barClr ?>" style="width:<?= $pct ?>%"></div>
                            </div>
                            <span class="text-muted small"><?= $pct ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; // end tab ?>
<?php endif; // end classInfo ?>

<?php
$extraJs = <<<'JS'
function setStatus(sid, val, btn) {
    document.getElementById('status-' + sid).value = val;
    const group = btn.closest('.btn-group');
    group.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
function markAll(status) {
    document.querySelectorAll('[id^="status-"]').forEach(inp => {
        inp.value = status;
        const sid = inp.id.replace('status-', '');
        const group = document.querySelector(`[data-sid="${sid}"][data-val="${status}"]`)?.closest('.btn-group');
        if (group) {
            group.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
            group.querySelector(`[data-val="${status}"]`)?.classList.add('active');
        }
    });
}
function resetAll() {
    markAll('Present');
}
JS;
?>
