<?php $pageTitle = 'Học kỳ'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-calendar3 me-2"></i>Quản lý Học kỳ</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Học kỳ</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row">
    <div class="col-lg-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><?= $editSemester ? '<i class="bi bi-pencil me-2"></i>Sửa học kỳ' : '<i class="bi bi-plus-circle me-2"></i>Thêm học kỳ' ?></h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= aUrl('semesters') ?>">
                    <input type="hidden" name="action" value="<?= $editSemester ? 'update' : 'create' ?>">
                    <?php if ($editSemester): ?>
                    <input type="hidden" name="semester_id" value="<?= $editSemester['semester_id'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Tên học kỳ <span class="text-danger">*</span></label>
                        <input type="text" name="semester_name" class="form-control" required
                               value="<?= htmlspecialchars($editSemester['semester_name'] ?? '') ?>" placeholder="VD: Học kỳ 1 - 2025/2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Loại học kỳ</label>
                        <select name="semester" class="form-select">
                            <?php foreach (['Spring'=>'Học kỳ 1 (Spring)','Summer'=>'Học kỳ hè (Summer)','Fall'=>'Học kỳ 2 (Fall)'] as $val => $lbl): ?>
                            <option value="<?= $val ?>" <?= ($editSemester['semester'] ?? '') == $val ? 'selected' : '' ?>><?= $lbl ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Năm học</label>
                        <input type="number" name="year" class="form-control" min="2000" max="2100"
                               value="<?= $editSemester['year'] ?? date('Y') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control"
                               value="<?= $editSemester['start_date'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control"
                               value="<?= $editSemester['end_date'] ?? '' ?>">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_current" id="isCurrent" class="form-check-input"
                               <?= ($editSemester['is_current'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isCurrent">Học kỳ hiện tại</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?= $editSemester ? 'Cập nhật' : 'Thêm mới' ?></button>
                        <?php if ($editSemester): ?><a href="<?= aUrl('semesters') ?>" class="btn btn-secondary">Hủy</a><?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-3">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Danh sách học kỳ <span class="badge bg-primary ms-2"><?= count($semesters) ?></span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Tên học kỳ</th><th>Loại</th><th>Năm</th><th>Bắt đầu</th><th>Kết thúc</th><th>Hiện tại</th><th>Thao tác</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($semesters as $sem): ?>
                            <tr class="<?= $sem['is_current'] ? 'table-primary' : '' ?>">
                                <td><?= htmlspecialchars($sem['semester_name']) ?></td>
                                <td><?= htmlspecialchars($sem['semester_code']) ?></td>
                                <td><?= $sem['start_date'] ? date('d/m/Y', strtotime($sem['start_date'])) : '—' ?></td>
                                <td><?= $sem['end_date'] ? date('d/m/Y', strtotime($sem['end_date'])) : '—' ?></td>
                                <td>
                                    <?php if ($sem['is_current']): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Đang mở
                                        </span>
                                    <?php else: ?>
                                        <form method="post" action="<?= aUrl('semesters') ?>" class="d-inline">
                                            <input type="hidden" name="action" value="set_current">
                                            <input type="hidden" name="semester_id" value="<?= $sem['semester_id'] ?>">
                                            <button type="submit" class="btn btn-xs btn-sm btn-outline-secondary">
                                                Đặt hiện tại
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= aUrl('semesters') ?>?edit=<?= $sem['semester_id'] ?>" 
                                    class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>

                            <?php endforeach; ?>
                            <?php if (empty($semesters)): ?><tr><td colspan="7" class="text-center text-muted py-4">Chưa có học kỳ.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
