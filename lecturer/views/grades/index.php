<?php
/**
 * View: Nhập điểm sinh viên
 * Biến: $lecturer, $myClasses, $classInfo, $classId,
 *       $students, $gradeStats, $openAdd,
 *       $flashSuccess, $flashError
 */
$pageTitle = 'Nhập điểm';
$extraCss  = '.grade-input{width:80px;text-align:center;} .letter-badge{min-width:40px;display:inline-block;text-align:center;}';

function gradeLetterClass(?string $letter): string {
    if (!$letter) return 'bg-light text-muted border';
    if (in_array($letter, ['A+','A']))   return 'bg-success';
    if (in_array($letter, ['B+','B']))   return 'bg-primary';
    if (in_array($letter, ['C+','C']))   return 'bg-info';
    if (in_array($letter, ['D+','D']))   return 'bg-warning text-dark';
    return 'bg-danger';
}
?>

<div class="page-header">
    <h1 class="page-title">Nhập điểm sinh viên</h1>
    <div class="page-breadcrumb">
        <a href="<?= lUrl() ?>">Trang chủ</a> /
        <a href="<?= lUrl('classes') ?>">Lớp đang dạy</a> / Nhập điểm
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
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <input type="hidden" name="page" value="grades">
            <label class="fw-semibold text-muted small mb-0">Chọn lớp:</label>
            <select name="class_id" class="form-select form-select-sm" style="max-width:360px"
                    onchange="this.form.submit()">
                <option value="">-- Chọn lớp để nhập điểm --</option>
                <?php foreach ($myClasses as $mc): ?>
                <option value="<?= $mc['class_id'] ?>" <?= $classId == $mc['class_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mc['class_code']) ?> –
                    <?= htmlspecialchars($mc['subject_name']) ?>
                    (<?= LecturerModel::formatSemester($mc['semester'] ?? null) ?> <?= htmlspecialchars($mc['year'] ?? '-') ?>)
                </option>
                <?php endforeach; ?>
            </select>
            <?php if ($classInfo): ?>
            <a href="<?= lUrl('grades') ?>&class_id=<?= $classId ?>&add=1"
               class="btn btn-success btn-sm ms-auto">
                <i class="bi bi-person-plus me-1"></i>Thêm sinh viên
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (!$classInfo): ?>
<!-- Placeholder -->
<div class="text-center text-muted py-5">
    <i class="bi bi-arrow-up-circle fs-1 d-block mb-2 opacity-40"></i>
    <p>Chọn lớp học để bắt đầu nhập điểm</p>
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
            <span><i class="bi bi-bookmark-star me-1"></i><?= $classInfo['credit_hours'] ?> tín chỉ</span>
            <span><i class="bi bi-calendar3 me-1"></i><?= LecturerModel::formatSemester($classInfo['semester'] ?? null) ?> <?= htmlspecialchars($classInfo['year'] ?? '-') ?></span>
            <span><i class="bi bi-people me-1"></i><?= count($students) ?> sinh viên</span>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="<?= lUrl('grades') ?>&class_id=<?= $classId ?>&add=1"
               class="btn btn-light btn-sm">
                <i class="bi bi-person-plus me-1"></i>Thêm SV
            </a>
            <button onclick="window.print()" class="btn btn-light btn-sm">
                <i class="bi bi-printer me-1"></i>In bảng điểm
            </button>
        </div>
    </div>
</div>

<!-- Grade stats -->
<?php if ($gradeStats): ?>
<?php
$totalSV  = (int)($gradeStats['total_students']  ?? 0);
$gradedSV = (int)($gradeStats['graded_students'] ?? 0);
$pct      = $totalSV > 0 ? round($gradedSV / $totalSV * 100) : 0;
$avgScore = $gradeStats['avg_score'] !== null ? number_format($gradeStats['avg_score'], 1) : '—';
?>
<div class="row g-3 mb-4">
    <?php foreach ([
        ['primary', 'people',       'Tổng SV',       $totalSV],
        ['success', 'check2-circle','Đã nhập điểm',  $gradedSV],
        ['info',    'bar-chart',    'Điểm TB',        $avgScore],
        ['warning', 'percent',      'Hoàn thành',     $pct . '%'],
    ] as [$color, $icon, $label, $val]): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 p-3">
                    <i class="bi bi-<?= $icon ?> text-<?= $color ?> fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= $label ?></div>
                    <div class="fs-3 fw-bold"><?= $val ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Grade form -->
<?php if (!empty($students)): ?>

<!-- Add student modal trigger -->
<?php if ($openAdd): ?>
<div id="addStudentAutoOpen" data-open="1"></div>
<?php endif; ?>

<form method="POST" action="<?= lUrl('grades') ?>" id="gradeForm">
    <input type="hidden" name="action"   value="save_grades">
    <input type="hidden" name="class_id" value="<?= $classId ?>">

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold">
                <i class="bi bi-list-check me-2 text-success"></i>Bảng điểm lớp
                <code class="ms-1"><?= htmlspecialchars($classInfo['class_code']) ?></code>
            </span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm"
                        onclick="clearAllScores()">
                    <i class="bi bi-eraser me-1"></i>Xóa điểm
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-floppy me-1"></i>Lưu tất cả điểm
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="gradeTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:40px">#</th>
                            <th style="width:110px">Mã SV</th>
                            <th>Họ và tên</th>
                            <th class="text-center" style="width:100px">Trạng thái</th>
                            <th class="text-center" style="width:110px">Điểm (0–100)</th>
                            <th class="text-center" style="width:80px">Chữ</th>
                            <th class="text-center" style="width:80px">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $i => $sv): ?>
                    <tr id="row_<?= $sv['enrollment_id'] ?>">
                        <td class="text-center text-muted small"><?= $i + 1 ?></td>
                        <td><code class="text-primary"><?= htmlspecialchars($sv['student_code']) ?></code></td>
                        <td class="fw-semibold">
                            <?= htmlspecialchars($sv['full_name']) ?>
                            <?php if ($sv['email']): ?>
                            <br><small class="text-muted fw-normal"><?= htmlspecialchars($sv['email']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php
                            $sc = $sv['enroll_status'];
                            $bc = $sc === 'Registered' ? 'primary' : ($sc === 'Completed' ? 'success' : 'secondary');
                            ?>
                            <span class="badge bg-<?= $bc ?> small">
                                <?= LecturerModel::formatEnrollStatus($sc) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <input type="number"
                                   name="scores[<?= $sv['enrollment_id'] ?>]"
                                   class="form-control grade-input form-control-sm mx-auto"
                                   min="0" max="100" step="0.5"
                                   placeholder="—"
                                   value="<?= $sv['score'] !== null ? $sv['score'] : '' ?>"
                                   oninput="updateLetter(this, <?= $sv['enrollment_id'] ?>)"
                                   data-eid="<?= $sv['enrollment_id'] ?>">
                        </td>
                        <td class="text-center">
                            <span class="badge letter-badge fs-6 <?= $sv['score'] !== null
                                ? gradeLetterClass($sv['grade_letter'])
                                : 'bg-light text-muted border' ?>"
                                  id="letter_<?= $sv['enrollment_id'] ?>">
                                <?= $sv['grade_letter'] ?? '—' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <form method="POST" action="<?= lUrl('grades') ?>" style="display:inline"
                                  onsubmit="return confirm('Xóa sinh viên này khỏi lớp?')">
                                <input type="hidden" name="action"        value="remove_student">
                                <input type="hidden" name="class_id"      value="<?= $classId ?>">
                                <input type="hidden" name="enrollment_id" value="<?= $sv['enrollment_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-person-dash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end py-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy me-1"></i>Lưu tất cả điểm
            </button>
        </div>
    </div>
</form>

<!-- Add student modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= lUrl('grades') ?>">
                <input type="hidden" name="action"   value="add_student">
                <input type="hidden" name="class_id" value="<?= $classId ?>">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>Thêm sinh viên vào lớp
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Mã sinh viên</label>
                    <input type="text" name="student_code" class="form-control"
                           placeholder="Ví dụ: SV001" required autofocus>
                    <div class="form-text">Nhập mã sinh viên đang trong trạng thái Đang học</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-plus me-1"></i>Thêm vào lớp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php else: ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-people fs-1 d-block mb-2 opacity-40"></i>
    <p>Lớp chưa có sinh viên đăng ký</p>
    <button class="btn btn-success btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="bi bi-person-plus me-1"></i>Thêm sinh viên đầu tiên
    </button>
</div>
<?php endif; ?>
<?php endif; ?>

<?php
$extraJs = <<<'JS'
// Grade letter calculator
function calcLetter(score) {
    if (score >= 90) return 'A+';
    if (score >= 85) return 'A';
    if (score >= 80) return 'B+';
    if (score >= 75) return 'B';
    if (score >= 70) return 'C+';
    if (score >= 65) return 'C';
    if (score >= 60) return 'D+';
    if (score >= 55) return 'D';
    return 'F';
}
function letterClass(l) {
    if (!l || l === '—') return 'bg-light text-muted border';
    if (['A+','A'].includes(l))   return 'bg-success';
    if (['B+','B'].includes(l))   return 'bg-primary';
    if (['C+','C'].includes(l))   return 'bg-info';
    if (['D+','D'].includes(l))   return 'bg-warning text-dark';
    return 'bg-danger';
}
function updateLetter(inp, eid) {
    const badge = document.getElementById('letter_' + eid);
    if (!badge) return;
    const val = parseFloat(inp.value);
    if (inp.value === '' || isNaN(val)) {
        badge.textContent = '—';
        badge.className = 'badge letter-badge fs-6 bg-light text-muted border';
    } else {
        const l = calcLetter(val);
        badge.textContent = l;
        badge.className = 'badge letter-badge fs-6 ' + letterClass(l);
    }
}
function clearAllScores() {
    if (!confirm('Xóa toàn bộ điểm đã nhập (trên màn hình)?')) return;
    document.querySelectorAll('.grade-input').forEach(inp => {
        inp.value = '';
        const eid = inp.dataset.eid;
        const badge = document.getElementById('letter_' + eid);
        if (badge) { badge.textContent = '—'; badge.className = 'badge letter-badge fs-6 bg-light text-muted border'; }
    });
}
// Auto-open add modal
if (document.getElementById('addStudentAutoOpen')) {
    document.addEventListener('DOMContentLoaded', function() {
        const m = document.getElementById('addStudentModal');
        if (m) new bootstrap.Modal(m).show();
    });
}
JS;
?>
