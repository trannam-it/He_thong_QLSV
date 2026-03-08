<?php $pageTitle = 'Đăng ký học phần'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-journal-plus me-2"></i>Quản lý Đăng ký học phần</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Đăng ký học phần</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="enrollments">
            <div class="col-md-4">
                <select name="semester_id" class="form-select">
                    <option value="0">Tất cả học kỳ</option>
                    <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['semester_id'] ?>" <?= $semesterId == $sem['semester_id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['semester_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <?php foreach (['Studying'=>'Đang học','Completed'=>'Hoàn thành','Failed'=>'Chưa qua','Withdrawn'=>'Rút môn'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $filterStatus === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i>Lọc</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Đăng ký học phần <span class="badge bg-primary ms-2"><?= count($enrollments) ?></span></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Sinh viên</th><th>Mã SV</th><th>Lớp</th><th>Học phần</th><th>TC</th><th>Học kỳ</th><th>Ngày ĐK</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['student_name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($e['student_code']) ?></span></td>
                        <td><?= htmlspecialchars($e['class_code']) ?></td>
                        <td><?= htmlspecialchars($e['subject_name']) ?></td>
                        <td class="text-center"><?= $e['credits'] ?></td>
                        <td><small><?= htmlspecialchars($e['semester_name']) ?></small></td>
                        <td><small><?= $e['enrollment_date'] ? date('d/m/Y', strtotime($e['enrollment_date'])) : '—' ?></small></td>
                        <td>
                            <?php
                            $ecol = ['Studying'=>'primary','Completed'=>'success','Failed'=>'danger','Withdrawn'=>'secondary'];
                            $elbl = ['Studying'=>'Đang học','Completed'=>'Hoàn thành','Failed'=>'Chưa qua','Withdrawn'=>'Rút môn'];
                            ?>
                            <span class="badge bg-<?= $ecol[$e['status']] ?? 'secondary' ?>"><?= $elbl[$e['status']] ?? $e['status'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($enrollments)): ?><tr><td colspan="8" class="text-center text-muted py-4">Không có dữ liệu đăng ký.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
