<?php $pageTitle = 'Quản lý Học bổng'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-trophy me-2"></i>Quản lý Học bổng</h1>
    <div class="page-breadcrumb"><a href="<?= accUrl() ?>">Trang chủ</a> / Học bổng</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row">
    <!-- Scholarship list -->
    <div class="col-lg-5 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Danh sách Học bổng</h5>
                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#createSchModal"><i class="bi bi-plus-circle me-1"></i>Tạo mới</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light"><tr><th>Tên học bổng</th><th class="text-end">Giá trị</th><th>HK</th><th class="text-center">Đơn</th><th>Duyệt</th><th>TT</th></tr></thead>
                        <tbody>
                            <?php foreach ($scholarships as $sch): ?>
                            <tr class="<?= $sch['is_active'] ? '' : 'table-secondary' ?>">
                                <td>
                                    <a href="<?= accUrl('scholarships') ?>?scholarship_id=<?= $sch['scholarship_id'] ?>" class="text-decoration-none fw-bold">
                                        <?= htmlspecialchars($sch['name']) ?>
                                    </a>
                                    <?php if ($sch['deadline']): ?><br><small class="text-muted">HH: <?= date('d/m/Y', strtotime($sch['deadline'])) ?></small><?php endif; ?>
                                </td>
                                <td class="text-end"><?= number_format((float)$sch['value']) ?>đ</td>
                                <td><small><?= $sch['semester'] ?>/<?= $sch['year'] ?></small></td>
                                <td class="text-center"><?= $sch['total_apps'] ?></td>
                                <td class="text-center text-success"><?= $sch['approved_apps'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $sch['is_active'] ? 'success' : 'secondary' ?>"><?= $sch['is_active'] ? 'Mở' : 'Đóng' ?></span>
                                    <form method="post" action="<?= accUrl('scholarships') ?>" class="d-inline">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <input type="hidden" name="scholarship_id" value="<?= $sch['scholarship_id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-sm btn-outline-secondary ms-1" title="Bật/Tắt"><i class="bi bi-toggle-on"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($scholarships)): ?><tr><td colspan="6" class="text-center text-muted py-3">Chưa có học bổng.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications -->
    <div class="col-lg-7 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Đơn xin học bổng
                    <?php if ($scholarshipId > 0): ?>
                    <span class="text-muted fs-6 ms-2">– <?= htmlspecialchars(array_column($scholarships, null, 'scholarship_id')[$scholarshipId]['name'] ?? '') ?></span>
                    <?php endif; ?>
                </h5>
            </div>
            <!-- Filter applications -->
            <div class="card-body pb-0">
                <form method="get" class="row g-2 align-items-end">
                    <input type="hidden" name="page" value="scholarships">
                    <?php if ($scholarshipId): ?><input type="hidden" name="scholarship_id" value="<?= $scholarshipId ?>"><?php endif; ?>
                    <div class="col-md-5">
                        <select name="app_status" class="form-select form-select-sm">
                            <option value="">Tất cả trạng thái</option>
                            <option value="Pending" <?= $appStatus==='Pending'?'selected':'' ?>>Đang chờ</option>
                            <option value="Approved" <?= $appStatus==='Approved'?'selected':'' ?>>Đã duyệt</option>
                            <option value="Rejected" <?= $appStatus==='Rejected'?'selected':'' ?>>Từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-3"><button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-filter me-1"></i>Lọc</button></div>
                    <?php if ($scholarshipId): ?><div class="col-md-4"><a href="<?= accUrl('scholarships') ?>" class="btn btn-sm btn-outline-secondary w-100">Xem tất cả</a></div><?php endif; ?>
                </form>
            </div>
            <div class="card-body p-0 mt-2">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light"><tr><th>Sinh viên</th><th>Học bổng</th><th>Nộp ngày</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= htmlspecialchars($app['student_name']) ?><br><small class="text-muted"><?= $app['student_code'] ?></small></td>
                                <td><?= htmlspecialchars($app['scholarship_name']) ?><br><small class="text-success"><?= number_format((float)$app['value']) ?>đ</small></td>
                                <td><small><?= date('d/m/Y', strtotime($app['applied_at'])) ?></small></td>
                                <td>
                                    <?php $acol=['Pending'=>'warning','Approved'=>'success','Rejected'=>'danger']; $albl=['Pending'=>'Chờ duyệt','Approved'=>'Đã duyệt','Rejected'=>'Từ chối']; ?>
                                    <span class="badge bg-<?= $acol[$app['status']] ?? 'secondary' ?>"><?= $albl[$app['status']] ?? $app['status'] ?></span>
                                </td>
                                <td>
                                    <?php if ($app['status'] === 'Pending'): ?>
                                    <button type="button" class="btn btn-xs btn-sm btn-outline-success"
                                            data-bs-toggle="modal" data-bs-target="#reviewModal"
                                            data-app-id="<?= $app['application_id'] ?>"
                                            data-student="<?= htmlspecialchars($app['student_name']) ?>"
                                            data-scholarship="<?= htmlspecialchars($app['scholarship_name']) ?>">
                                        <i class="bi bi-check2"></i> Xét duyệt
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($applications)): ?><tr><td colspan="5" class="text-center text-muted py-4">Không có đơn.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-clipboard-check me-2"></i>Xét duyệt Học bổng</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= accUrl('scholarships') ?>">
            <div class="modal-body">
                <input type="hidden" name="action" value="review">
                <input type="hidden" name="application_id" id="reviewAppId">
                <p>Sinh viên: <strong id="reviewStudent"></strong></p>
                <p>Học bổng: <strong id="reviewScholarship"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Kết quả xét duyệt</label>
                    <div class="d-flex gap-3">
                        <div class="form-check"><input class="form-check-input" type="radio" name="status" value="Approved" id="appApprove" required><label class="form-check-label text-success" for="appApprove"><i class="bi bi-check-circle me-1"></i>Phê duyệt</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="status" value="Rejected" id="appReject"><label class="form-check-label text-danger" for="appReject"><i class="bi bi-x-circle me-1"></i>Từ chối</label></div>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Ghi chú</label><textarea name="note" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Xác nhận</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Create Scholarship Modal -->
<div class="modal fade" id="createSchModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tạo Học bổng mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= accUrl('scholarships') ?>">
            <div class="modal-body">
                <input type="hidden" name="action" value="create_scholarship">
                <div class="row g-2">
                    <div class="col-md-8"><label class="form-label">Tên học bổng *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-4"><label class="form-label">Giá trị (đ) *</label><input type="number" name="value" class="form-control" min="0" step="100000" required></div>
                    <div class="col-md-12"><label class="form-label">Mô tả</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div class="col-md-3"><label class="form-label">GPA tối thiểu</label><input type="number" name="min_gpa" class="form-control" min="0" max="100" step="0.01"></div>
                    <div class="col-md-3"><label class="form-label">GPA tối đa</label><input type="number" name="max_gpa" class="form-control" min="0" max="100" step="0.01"></div>
                    <div class="col-md-3"><label class="form-label">Học kỳ</label><select name="semester" class="form-select"><option value="Spring">Học kỳ 1</option><option value="Summer">Học kỳ hè</option><option value="Fall">Học kỳ 2</option></select></div>
                    <div class="col-md-3"><label class="form-label">Năm</label><input type="number" name="year" class="form-control" value="<?= date('Y') ?>" min="2000"></div>
                    <div class="col-md-3"><label class="form-label">Số suất</label><input type="number" name="quantity" class="form-control" min="1" placeholder="Trống=không giới hạn"></div>
                    <div class="col-md-3"><label class="form-label">Hạn nộp</label><input type="date" name="deadline" class="form-control"></div>
                    <div class="col-md-6 d-flex align-items-end"><div class="form-check"><input type="checkbox" name="is_active" id="isActive" class="form-check-input" checked><label class="form-check-label" for="isActive">Mở nhận đơn ngay</label></div></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Tạo học bổng</button>
            </div>
        </form>
    </div></div>
</div>

<?php $extraJs = "
document.querySelectorAll('[data-bs-target=\"#reviewModal\"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('reviewAppId').value = this.dataset.appId;
        document.getElementById('reviewStudent').textContent = this.dataset.student;
        document.getElementById('reviewScholarship').textContent = this.dataset.scholarship;
    });
});
"; ?>
