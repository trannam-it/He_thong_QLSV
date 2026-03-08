<?php
/**
 * View: Quản lý Học phần (Academic)
 */
$pageTitle = 'Học phần';
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-book-half me-2"></i>Quản lý Học phần</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Học phần</div>
</div>

<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row">
    <!-- Form Thêm/Sửa -->
    <div class="col-lg-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><?= $editSubject ? '<i class="bi bi-pencil me-2"></i>Sửa học phần' : '<i class="bi bi-plus-circle me-2"></i>Thêm học phần' ?></h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= aUrl('subjects') ?>">
                    <input type="hidden" name="action" value="<?= $editSubject ? 'update' : 'create' ?>">
                    <?php if ($editSubject): ?>
                    <input type="hidden" name="subject_id" value="<?= $editSubject['subject_id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Mã học phần <span class="text-danger">*</span></label>
                        <input type="text" name="subject_code" class="form-control" required
                               value="<?= htmlspecialchars($editSubject['subject_code'] ?? '') ?>" placeholder="VD: CS101">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên học phần <span class="text-danger">*</span></label>
                        <input type="text" name="subject_name" class="form-control" required
                               value="<?= htmlspecialchars($editSubject['subject_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số tín chỉ <span class="text-danger">*</span></label>
                        <input type="number" name="credits" class="form-control" min="1" max="10" required
                               value="<?= $editSubject['credits'] ?? 3 ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Khoa</label>
                        <select name="faculty_id" class="form-select">
                            <option value="">Chọn khoa...</option>
                            <?php foreach ($faculties as $f): ?>
                            <option value="<?= $f['faculty_id'] ?>"
                                <?= ($editSubject['faculty_id'] ?? '') == $f['faculty_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['faculty_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Học phần tiên quyết</label>
                        <select name="prerequisite_id" class="form-select">
                            <option value="">Không có</option>
                            <?php foreach ($allSubjects as $s): ?>
                            <?php if (!$editSubject || $s['subject_id'] != $editSubject['subject_id']): ?>
                            <option value="<?= $s['subject_id'] ?>"
                                <?= ($editSubject['prerequisite_id'] ?? '') == $s['subject_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['subject_code'] . ' - ' . $s['subject_name']) ?>
                            </option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($editSubject['description'] ?? '') ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i><?= $editSubject ? 'Cập nhật' : 'Thêm mới' ?>
                        </button>
                        <?php if ($editSubject): ?>
                        <a href="<?= aUrl('subjects') ?>" class="btn btn-secondary">Hủy</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Danh sách Học phần -->
    <div class="col-lg-8 mb-3">
        <!-- Filter -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="get" class="row g-2 align-items-end">
                    <input type="hidden" name="page" value="subjects">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Tìm mã, tên học phần..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tìm</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Danh sách học phần <span class="badge bg-primary ms-2"><?= count($subjects) ?></span></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã</th>
                                <th>Tên học phần</th>
                                <th class="text-center">TC</th>
                                <th>Khoa</th>
                                <th>Tiên quyết</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subjects as $s): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($s['subject_code']) ?></span></td>
                                <td><?= htmlspecialchars($s['subject_name']) ?></td>
                                <td class="text-center"><span class="badge bg-info text-dark"><?= $s['credits'] ?></span></td>
                                <td><small><?= htmlspecialchars($s['faculty_name'] ?? '—') ?></small></td>
                                <td><small class="text-muted"><?= $s['prerequisite_id'] ? 'Có' : '—' ?></small></td>
                                <td>
                                    <a href="<?= aUrl('subjects') ?>?edit=<?= $s['subject_id'] ?>" class="btn btn-xs btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" action="<?= aUrl('subjects') ?>" class="d-inline"
                                          onsubmit="return confirm('Xóa học phần này?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="subject_id" value="<?= $s['subject_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($subjects)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Không có dữ liệu.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
