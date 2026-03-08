<?php
/**
 * View: Đăng ký lớp dạy
 * Biến: $lecturer, $flash, $subjects, $myClasses,
 *       $currentYear, $currentSem
 */
$pageTitle = 'Đăng ký lớp dạy';
?>

<div class="page-header">
    <h1 class="page-title">Đăng ký lớp dạy</h1>
    <div class="page-breadcrumb">
        <a href="<?= lUrl() ?>">Trang chủ</a> / Đăng ký lớp dạy
    </div>
</div>

<!-- Flash -->
<?php if (!empty($flash['msg'])): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-3">
    <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
    <?= $flash['msg'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- LEFT: Registration form -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-journal-plus me-2 text-success"></i>Đăng ký giảng dạy
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= lUrl('register') ?>" id="registerForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Môn học <span class="text-danger">*</span>
                        </label>
                        <select name="subject_id" id="subjectSelect" class="form-select" required
                                onchange="updatePreview()">
                            <option value="">-- Chọn môn học --</option>
                            <?php foreach ($subjects as $s): ?>
                            <option value="<?= $s['subject_id'] ?>"
                                    data-code="<?= htmlspecialchars($s['subject_code']) ?>"
                                    data-credits="<?= $s['credit_hours'] ?>"
                                    <?= (isset($_POST['subject_id']) && $_POST['subject_id'] == $s['subject_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['subject_name']) ?>
                                (<?= htmlspecialchars($s['subject_code']) ?>, <?= $s['credit_hours'] ?> TC)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Danh sách môn học hiện có trong hệ thống</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold">
                                Học kỳ <span class="text-danger">*</span>
                            </label>
                            <select name="semester" id="semesterSelect" class="form-select" required
                                    onchange="updatePreview()">
                                <?php foreach (['Spring' => 'Học kỳ I (Spring)', 'Summer' => 'Học kỳ Hè (Summer)', 'Fall' => 'Học kỳ II (Fall)'] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= ($currentSem === $v || (isset($_POST['semester']) && $_POST['semester'] === $v)) ? 'selected' : '' ?>>
                                    <?= $l ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold">
                                Năm học <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="year" id="yearInput" class="form-control"
                                   required min="2020" max="2040"
                                   value="<?= isset($_POST['year']) ? (int)$_POST['year'] : $currentYear ?>"
                                   oninput="updatePreview()">
                        </div>
                    </div>

                    <!-- Preview -->
                    <div id="previewCard" class="alert alert-info py-2 px-3 small mb-3 d-none">
                        <i class="bi bi-info-circle me-1"></i>
                        <span id="previewText"></span>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success" id="btnRegister">
                            <i class="bi bi-journal-check me-1"></i>Đăng ký lớp dạy
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info -->
        <div class="card shadow-sm mt-3">
            <div class="card-body small text-muted">
                <h6 class="text-dark fw-semibold mb-2">
                    <i class="bi bi-info-circle me-1 text-info"></i>Hướng dẫn
                </h6>
                <ul class="mb-0 ps-3">
                    <li>Chọn môn học, học kỳ và năm học muốn đăng ký giảng dạy.</li>
                    <li>Nếu đã có lớp mở cho môn đó mà chưa có giảng viên, bạn sẽ được gán vào lớp đó.</li>
                    <li>Nếu chưa có lớp nào, hệ thống sẽ tạo lớp mới với mã tự sinh.</li>
                    <li>Một giảng viên chỉ có thể dạy 1 lớp của cùng môn trong cùng học kỳ và năm.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RIGHT: My registrations -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-calendar-check me-2 text-primary"></i>Lịch sử đăng ký giảng dạy
                </h6>
                <span class="badge bg-primary"><?= count($myClasses) ?> lớp</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($myClasses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px" class="text-center">#</th>
                                <th>Mã lớp</th>
                                <th>Môn học</th>
                                <th class="text-center">TC</th>
                                <th class="text-center">Học kỳ</th>
                                <th class="text-center">Năm</th>
                                <th class="text-center">SV</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($myClasses as $i => $c): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td><code class="text-primary fw-semibold"><?= htmlspecialchars($c['class_code']) ?></code></td>
                            <td><?= htmlspecialchars($c['subject_name']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-info rounded-pill"><?= $c['credit_hours'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= LecturerModel::formatSemester($c['semester']) ?></span>
                            </td>
                            <td class="text-center text-muted"><?= $c['year'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill"><?= $c['student_count'] ?></span>
                            </td>
                            <td class="text-center">
                                <a href="<?= lUrl('grades') ?>&class_id=<?= $c['class_id'] ?>"
                                   class="btn btn-sm btn-outline-success" title="Nhập điểm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-40"></i>
                    <p class="mb-0">Chưa có lớp nào được đăng ký</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php
$extraJs = <<<'JS'
function updatePreview() {
    const sel  = document.getElementById('subjectSelect');
    const opt  = sel.options[sel.selectedIndex];
    const sem  = document.getElementById('semesterSelect').value;
    const year = document.getElementById('yearInput').value;
    const card = document.getElementById('previewCard');
    const txt  = document.getElementById('previewText');
    if (!opt.value || !sem || !year) { card.classList.add('d-none'); return; }
    const semMap = {Spring:'Học kỳ I', Summer:'Học kỳ Hè', Fall:'Học kỳ II'};
    const code = opt.dataset.code;
    card.classList.remove('d-none');
    txt.innerHTML = `Dự kiến: <strong>${code}-0X</strong> — ${opt.text.split('(')[0].trim()} — ${semMap[sem]} ${year}`;
}
document.getElementById('registerForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('btnRegister');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý…';
});
updatePreview();
JS;
?>
