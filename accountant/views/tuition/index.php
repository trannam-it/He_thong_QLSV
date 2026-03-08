<?php $pageTitle = 'Quản lý Học phí'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-receipt me-2"></i>Quản lý Học phí</h1>
    <div class="page-breadcrumb"><a href="<?= accUrl() ?>">Trang chủ</a> / Học phí</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<?php
$statusLabels = ['Unpaid'=>'Chưa nộp','Partial'=>'Nộp 1 phần','Paid'=>'Đã nộp','Overdue'=>'Quá hạn','Exempted'=>'Miễn giảm'];
$statusColors = ['Unpaid'=>'warning','Partial'=>'info','Paid'=>'success','Overdue'=>'danger','Exempted'=>'secondary'];
?>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="tuition">
            <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Mã SV, tên..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tất cả TT</option>
                    <?php foreach ($statusLabels as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $filterStatus === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="semester" class="form-select form-select-sm">
                    <option value="">Tất cả học kỳ</option>
                    <?php foreach (['Spring'=>'Học kỳ 1','Summer'=>'Học kỳ hè','Fall'=>'Học kỳ 2'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $semester === $v ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select form-select-sm">
                    <option value="0">Tất cả năm</option>
                    <?php foreach ($years as $y): ?>
                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-warning btn-sm w-100"><i class="bi bi-search me-1"></i>Lọc</button></div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Hóa đơn học phí <span class="badge bg-warning text-dark ms-2"><?= count($invoices) ?></span></h5>
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#priceModal">
            <i class="bi bi-gear me-1"></i>Cài đơn giá tín chỉ
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light"><tr><th>Sinh viên</th><th>Khoa</th><th>HK</th><th>TC</th><th class="text-end">Phải thu</th><th class="text-end">Đã thu</th><th class="text-end">Còn lại</th><th>Hạn nộp</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                    <?php $remaining = (float)$inv['amount_due'] - (float)$inv['amount_paid']; ?>
                    <tr class="<?= $inv['status']==='Overdue'?'table-danger':($inv['status']==='Unpaid'?'table-warning':'') ?>">
                        <td><?= htmlspecialchars($inv['student_name']) ?><br><small class="text-muted"><?= $inv['student_code'] ?></small></td>
                        <td><small><?= htmlspecialchars($inv['faculty_name'] ?? '—') ?></small></td>
                        <td><?= $inv['semester'] ?>/<?= $inv['year'] ?></td>
                        <td class="text-center"><?= $inv['total_credits'] ?></td>
                        <td class="text-end"><?= number_format((float)$inv['amount_due']) ?>đ</td>
                        <td class="text-end text-success"><?= number_format((float)$inv['amount_paid']) ?>đ</td>
                        <td class="text-end <?= $remaining > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($remaining) ?>đ</td>
                        <td><small><?= $inv['due_date'] ? date('d/m/Y', strtotime($inv['due_date'])) : '—' ?></small></td>
                        <td><span class="badge bg-<?= $statusColors[$inv['status']] ?? 'secondary' ?>"><?= $statusLabels[$inv['status']] ?? $inv['status'] ?></span></td>
                        <td>
                            <?php if (!in_array($inv['status'], ['Paid','Exempted'])): ?>
                            <button type="button" class="btn btn-xs btn-sm btn-outline-success"
                                    data-bs-toggle="modal" data-bs-target="#payModal"
                                    data-inv-id="<?= $inv['invoice_id'] ?>"
                                    data-student="<?= htmlspecialchars($inv['student_name']) ?>"
                                    data-remaining="<?= $remaining ?>">
                                <i class="bi bi-cash"></i> Thu
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-xs btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#statusModal"
                                    data-inv-id="<?= $inv['invoice_id'] ?>"
                                    data-status="<?= $inv['status'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($invoices)): ?><tr><td colspan="10" class="text-center text-muted py-4">Không có dữ liệu.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-cash me-2"></i>Thu học phí</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= accUrl('tuition') ?>">
            <div class="modal-body">
                <input type="hidden" name="action" value="record_payment">
                <input type="hidden" name="invoice_id" id="payInvoiceId">
                <p>Sinh viên: <strong id="payStudentName"></strong></p>
                <p>Còn phải thu: <strong id="payRemaining" class="text-danger"></strong></p>
                <div class="mb-3"><label class="form-label">Số tiền thu <span class="text-danger">*</span></label><input type="number" name="amount" class="form-control" min="1000" required id="payAmount"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check me-1"></i>Xác nhận</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Cập nhật trạng thái</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= accUrl('tuition') ?>">
            <div class="modal-body">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="invoice_id" id="statusInvoiceId">
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" id="statusSelect" class="form-select">
                        <?php foreach ($statusLabels as $v => $l): ?><option value="<?= $v ?>"><?= $l ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Ghi chú</label><textarea name="note" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Price Setting Modal -->
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-gear me-2"></i>Cài đơn giá tín chỉ</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form method="post" action="<?= accUrl('tuition') ?>" class="row g-2 mb-3 align-items-end">
                <input type="hidden" name="action" value="update_price">
                <div class="col-md-3"><label class="form-label">Học kỳ</label><select name="semester" class="form-select form-select-sm"><?php foreach (['Spring'=>'HK1','Summer'=>'Hè','Fall'=>'HK2'] as $v=>$l): ?><option value="<?= $v ?>"><?= $l ?></option><?php endforeach; ?></select></div>
                <div class="col-md-2"><label class="form-label">Năm</label><input type="number" name="year" class="form-control form-control-sm" value="<?= date('Y') ?>" min="2000"></div>
                <div class="col-md-3"><label class="form-label">Đơn giá/TC (đ)</label><input type="number" name="price" class="form-control form-control-sm" min="0" step="1000" value="500000"></div>
                <div class="col-md-2"><label class="form-label">Ghi chú</label><input type="text" name="note" class="form-control form-control-sm"></div>
                <div class="col-md-2"><button type="submit" class="btn btn-warning btn-sm w-100 mt-3"><i class="bi bi-save me-1"></i>Lưu</button></div>
            </form>
            <h6>Đơn giá hiện tại</h6>
            <div class="table-responsive"><table class="table table-sm table-bordered"><thead class="table-light"><tr><th>Học kỳ</th><th>Năm</th><th class="text-end">Đơn giá/TC</th><th>Ghi chú</th></tr></thead><tbody>
                <?php foreach ($settings as $s): ?>
                <tr><td><?= $s['semester'] ?></td><td><?= $s['year'] ?></td><td class="text-end"><?= number_format((float)$s['price_per_credit']) ?>đ</td><td><small><?= htmlspecialchars($s['note'] ?? '') ?></small></td></tr>
                <?php endforeach; ?>
                <?php if (empty($settings)): ?><tr><td colspan="4" class="text-center text-muted">Chưa có cài đặt.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div>
    </div></div>
</div>

<?php $extraJs = "
document.querySelectorAll('[data-bs-target=\"#payModal\"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('payInvoiceId').value = this.dataset.invId;
        document.getElementById('payStudentName').textContent = this.dataset.student;
        const r = parseFloat(this.dataset.remaining) || 0;
        document.getElementById('payRemaining').textContent = r.toLocaleString('vi-VN') + 'đ';
        document.getElementById('payAmount').max = r;
        document.getElementById('payAmount').value = r;
    });
});
document.querySelectorAll('[data-bs-target=\"#statusModal\"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('statusInvoiceId').value = this.dataset.invId;
        document.getElementById('statusSelect').value = this.dataset.status;
    });
});
"; ?>
