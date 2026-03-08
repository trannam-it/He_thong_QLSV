<?php $pageTitle = 'Kết quả học tập'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-bar-chart me-2"></i>Kết quả học tập</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Kết quả học tập</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="grades">
            <div class="col-md-4">
                <label class="form-label">Học kỳ</label>
                <select name="semester_id" class="form-select" onchange="this.form.submit()">
                    <option value="0">Chọn học kỳ...</option>
                    <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['semester_id'] ?>" <?= $semesterId == $sem['semester_id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['semester_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Lớp học phần</label>
                <select name="class_id" class="form-select">
                    <option value="0">Chọn lớp...</option>
                    <?php foreach ($classes as $cls): ?>
                    <option value="<?= $cls['class_id'] ?>" <?= $classId == $cls['class_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cls['class_code'] . ' – ' . $cls['class_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Xem điểm</button></div>
        </form>
    </div>
</div>

<?php if ($selectedClass): ?>
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row text-center">
            <div class="col-3"><strong>Tổng SV:</strong> <?= (int)($gradeStats['total'] ?? 0) ?></div>
            <div class="col-3"><strong>TB điểm:</strong> <?= number_format((float)($gradeStats['avg_score'] ?? 0), 1) ?></div>
            <div class="col-3 text-success"><strong>Đạt:</strong> <?= (int)($gradeStats['passed'] ?? 0) ?></div>
            <div class="col-3 text-danger"><strong>Trượt:</strong> <?= (int)($gradeStats['failed'] ?? 0) ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <?php if ($selectedClass): ?>
            Điểm lớp: <strong><?= htmlspecialchars($selectedClass['class_code']) ?></strong> – <?= htmlspecialchars($selectedClass['class_name']) ?>
            <?php else: ?>
            Kết quả học tập – Chọn học kỳ và lớp để xem điểm
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Mã SV</th><th>Họ tên</th><th class="text-center">Giữa kỳ</th><th class="text-center">Cuối kỳ</th><th class="text-center">Tổng kết</th><th class="text-center">Chữ</th><th class="text-center">Kết quả</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($grades as $g): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($g['student_code']) ?></span></td>
                        <td><?= htmlspecialchars($g['student_name']) ?></td>
                        <td class="text-center"><?= $g['midterm_score'] ?? '—' ?></td>
                        <td class="text-center"><?= $g['final_score'] ?? '—' ?></td>
                        <td class="text-center fw-bold <?= (float)($g['total_score'] ?? 0) >= 50 ? 'text-success' : 'text-danger' ?>"><?= $g['total_score'] ?? '—' ?></td>
                        <td class="text-center"><span class="badge bg-primary"><?= $g['letter_grade'] ?? '—' ?></span></td>
                        <td class="text-center"><span class="badge bg-<?= $g['is_passed'] ? 'success' : 'danger' ?>"><?= $g['is_passed'] ? 'Đạt' : 'Trượt' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($grades)): ?><tr><td colspan="7" class="text-center text-muted py-4"><?= $classId ? 'Lớp này chưa có điểm.' : 'Vui lòng chọn học kỳ và lớp.' ?></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
