<?php $pageTitle = 'Thời khóa biểu'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-calendar-week me-2"></i>Thời khóa biểu</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Thời khóa biểu</div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="schedule">
            <div class="col-md-5">
                <select name="semester_id" class="form-select" onchange="this.form.submit()">
                    <option value="0">Tất cả học kỳ</option>
                    <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['semester_id'] ?>" <?= $semesterId == $sem['semester_id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['semester_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i>Lọc</button></div>
        </form>
    </div>
</div>

<?php
$dayNames = [2=>'Thứ Hai',3=>'Thứ Ba',4=>'Thứ Tư',5=>'Thứ Năm',6=>'Thứ Sáu',7=>'Thứ Bảy',1=>'Chủ Nhật'];
?>

<?php if (empty($schedule)): ?>
<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Không có thời khóa biểu cho học kỳ đã chọn.</div>
<?php else: ?>
<?php foreach ($dayNames as $day => $dayName): ?>
    <?php if (!isset($grouped[$day])): continue; endif; ?>
    <div class="card mb-3">
        <div class="card-header" style="background:linear-gradient(135deg,#0d6efd,#0a58ca);color:#fff;">
            <h6 class="mb-0"><i class="bi bi-calendar-day me-2"></i><?= $dayName ?></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Tiết</th><th>Mã lớp</th><th>Tên lớp</th><th>Học phần</th><th>Giảng viên</th><th>Phòng</th><th>Học kỳ</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grouped[$day] as $s): ?>
                        <tr>
                            <td><span class="badge bg-warning text-dark">Tiết <?= $s['start_period'] ?>–<?= $s['end_period'] ?></span></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($s['class_code']) ?></span></td>
                            <td><?= htmlspecialchars($s['class_code']) ?></td>
                            <td><?= htmlspecialchars($s['subject_name']) ?></td>
                            <td><?= htmlspecialchars($s['lecturer_name'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($s['room'] ?? '—') ?></td>
                            <td><small><?= htmlspecialchars($s['semester_name']) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
