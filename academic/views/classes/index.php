<?php $pageTitle = 'Lớp học phần'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-people me-2"></i>Quản lý Lớp học phần</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Lớp học phần</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>


<?php if ($canCreate): ?>
<div class="card mb-3">
    <div class="card-header">
        <strong>Tạo lớp học phần</strong>
    </div>
    <div class="card-body">
        <form method="post">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Mã lớp</label>
                    <input type="text" name="class_code" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Học phần</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- Chọn học phần --</option>
                        <?php foreach ($subjects as $sub): ?>
                            <option value="<?= $sub['subject_id'] ?>">
                                <?= htmlspecialchars($sub['subject_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Học kỳ</label>
                    <select name="semester_id" class="form-select" required>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem['semester_id'] ?>">
                                <?= htmlspecialchars($sem['semester_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Giảng viên</label>
                    <select name="lecturer_id" class="form-select">
                        <option value="">-- Chưa phân công --</option>
                        <?php foreach ($lecturers as $lec): ?>
                            <option value="<?= $lec['lecturer_id'] ?>">
                                <?= htmlspecialchars($lec['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Số lượng tối đa</label>
                    <input type="number" name="max_students" class="form-control" value="50">
                </div>

                <div class="col-md-3 align-self-end">
                    <button class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Tạo lớp
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="classes">
            <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Tìm mã lớp, tên lớp, học phần..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-4">
                <select name="semester_id" class="form-select">
                    <option value="0">Tất cả học kỳ</option>
                    <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['semester_id'] ?>" <?= $semesterId == $sem['semester_id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['semester_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Lọc</button></div>
        </form>
    </div>
</div>

<?php if ($classDetail): ?>
<!-- Modal chi tiết lớp -->
<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-people me-2"></i>Chi tiết: <?= htmlspecialchars($classDetail['class_code']) ?> – <?= htmlspecialchars($classDetail['class_name']) ?></h5>
                <a href="<?= aUrl('classes') ?>" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th>Học phần:</th><td><?= htmlspecialchars($classDetail['subject_name']) ?> (<?= $classDetail['credits'] ?> TC)</td></tr>
                            <tr><th>Giảng viên:</th><td><?= htmlspecialchars($classDetail['lecturer_name'] ?? '—') ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th>Học kỳ:</th><td><?= htmlspecialchars($classDetail['semester_name'] ?? '—') ?></td></tr>
                            <tr><th>Trạng thái:</th><td><span class="badge bg-<?= $classDetail['status']==='Active'?'success':'secondary' ?>"><?= $classDetail['status'] ?></span></td></tr>
                        </table>
                    </div>
                </div>
                <h6 class="fw-bold mb-2">Danh sách sinh viên (<?= count($classStudents) ?>)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Mã SV</th><th>Họ tên</th><th>Email</th><th>Khoa</th><th>Trạng thái ĐK</th></tr></thead>
                        <tbody>
                            <?php foreach ($classStudents as $sv): ?>
                            <tr>
                                <td><?= htmlspecialchars($sv['student_code']) ?></td>
                                <td><?= htmlspecialchars($sv['full_name']) ?></td>
                                <td><?= htmlspecialchars($sv['email']) ?></td>
                                <td><?= htmlspecialchars($sv['faculty_name'] ?? '—') ?></td>
                                <td><span class="badge bg-<?= $sv['enrollment_status']==='Studying'?'success':'secondary' ?>"><?= $sv['enrollment_status'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($classStudents)): ?><tr><td colspan="5" class="text-center text-muted">Chưa có sinh viên đăng ký.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Lớp học phần <span class="badge bg-primary ms-2"><?= count($classes) ?></span></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Mã lớp</th><th>Tên lớp</th><th>Học phần</th><th>Giảng viên</th><th>Học kỳ</th><th>SV đăng ký</th><th>Trạng thái</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $cls): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($cls['class_name']) ?></span></td>
                        <td><?= htmlspecialchars($cls['class_name']) ?></td>
                        <td><?= htmlspecialchars($cls['subject_name']) ?> <small class="text-muted">(<?= $cls['credits'] ?> TC)</small></td>
                        <td><?= htmlspecialchars($cls['lecturer_name'] ?? '—') ?></td>
                        <td><small><?= htmlspecialchars($cls['semester_name'] ?? '—') ?></small></td>
                        <td class="text-center <?= (int)$cls['enrolled'] >= (int)$cls['max_students'] ? 'text-danger' : 'text-success' ?>"><?= $cls['enrolled'] ?>/<?= $cls['max_students'] ?></td>
                        <td><span class="badge bg-<?= $cls['status']==='Active'?'success':'secondary' ?>"><?= $cls['status'] ?></span></td>
                        <td>
                            <a href="<?= aUrl('classes') ?>?detail=<?= $cls['class_id'] ?>&search=<?= urlencode($search) ?>&semester_id=<?= $semesterId ?>"
                               class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($classes)): ?><tr><td colspan="8" class="text-center text-muted py-4">Không có dữ liệu.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
