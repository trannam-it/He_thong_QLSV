<?php
/**
 * View: Học phí sinh viên
 * Biến: $student, $invoices, $totalDue, $totalPaid, $totalDebt, $unpaidCount, $msg, $error
 */
$pageTitle   = 'Học phí';
$currentPage = 'student_tuition';
$extraCss    = '
.invoice-card { border-left:4px solid #4e73df; }
.invoice-card.paid { border-left-color:#1cc88a; }
.invoice-card.overdue { border-left-color:#e74a3b; }
.invoice-card.partial { border-left-color:#f6c23e; }
.progress-thin { height:6px; }
';

function tuitionStatusBadge(string $status): string {
    return match($status) {
        'Paid'     => '<span class="badge bg-success">Đã nộp</span>',
        'Partial'  => '<span class="badge bg-warning text-dark">Nộp một phần</span>',
        'Overdue'  => '<span class="badge bg-danger">Quá hạn</span>',
        'Exempted' => '<span class="badge bg-info">Được miễn giảm</span>',
        default    => '<span class="badge bg-secondary">Chưa nộp</span>',
    };
}
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-cash-coin me-2"></i>Học phí</h1>
    <div class="page-breadcrumb"><a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Học phí</div>
</div>

<?php if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card bg-gradient-primary text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Tổng tiền phải nộp</div>
                    <div class="stat-card-value"><?= number_format($totalDue,0,',','.') ?> đ</div></div>
                    <i class="bi bi-receipt stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card bg-gradient-success text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Đã nộp</div>
                    <div class="stat-card-value"><?= number_format($totalPaid,0,',','.') ?> đ</div></div>
                    <i class="bi bi-check-circle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card <?= $totalDebt > 0 ? 'bg-gradient-danger' : 'bg-gradient-success' ?> text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Còn nợ</div>
                    <div class="stat-card-value"><?= number_format(max(0,$totalDebt),0,',','.') ?> đ</div></div>
                    <i class="bi bi-exclamation-circle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="stat-card bg-gradient-warning text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Hoá đơn chưa nộp</div>
                    <div class="stat-card-value"><?= $unpaidCount ?></div></div>
                    <i class="bi bi-file-earmark-text stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách hoá đơn -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh sách hoá đơn học phí</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($invoices)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">Chưa có hoá đơn học phí nào.</p>
        </div>
        <?php else: ?>
        <div class="p-3">
        <?php foreach ($invoices as $inv):
            $semName = match($inv['semester']) { 'Spring'=>'Học kỳ 1','Summer'=>'Học kỳ Hè','Fall'=>'Học kỳ 2',default=>$inv['semester']};
            $pct     = $inv['amount_due'] > 0 ? min(100, round($inv['amount_paid'] / $inv['amount_due'] * 100)) : 0;
            $cardCls = match($inv['status']) { 'Paid'=>'paid','Overdue'=>'overdue','Partial'=>'partial',default=>''};
        ?>
        <div class="card mb-3 invoice-card <?= $cardCls ?>">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="mb-1 fw-bold"><?= $semName ?> – Năm <?= $inv['year'] ?></h6>
                        <small class="text-muted">
                            <?= $inv['total_credits'] ?> tín chỉ × <?= number_format($inv['price_per_credit'],0,',','.') ?> đ/TC
                        </small>
                        <?php if ($inv['due_date']): ?>
                        <br><small class="text-muted">Hạn nộp: <?= date('d/m/Y', strtotime($inv['due_date'])) ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="fw-bold fs-5 text-primary"><?= number_format($inv['amount_due'],0,',','.') ?> đ</div>
                        <small class="text-success">Đã nộp: <?= number_format($inv['amount_paid'],0,',','.') ?> đ</small>
                        <div class="progress progress-thin mt-1">
                            <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                        </div>
                        <small class="text-muted"><?= $pct ?>% hoàn thành</small>
                    </div>
                    <div class="col-md-2 text-center">
                        <?= tuitionStatusBadge($inv['status']) ?>
                        <?php if ($inv['paid_at']): ?>
                        <br><small class="text-muted"><?= date('d/m/Y', strtotime($inv['paid_at'])) ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3 text-end">
                        <?php if (!in_array($inv['status'], ['Paid','Exempted'])): ?>
                        <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#payModal"
                                data-invid="<?= $inv['invoice_id'] ?>"
                                data-semname="<?= htmlspecialchars($semName . ' – ' . $inv['year']) ?>"
                                data-due="<?= $inv['amount_due'] ?>"
                                data-paid="<?= $inv['amount_paid'] ?>">
                            <i class="bi bi-credit-card me-1"></i>Nộp học phí
                        </button>
                        <?php else: ?>
                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Hoàn tất</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Lưu ý:</strong> Học phí được tính dựa trên số tín chỉ đăng ký trong từng học kỳ.
    Giá 1 tín chỉ hiện tại: <strong>550.000 đ</strong>. Chức năng nộp học phí là <strong>giả lập</strong> để quản lý.
</div>

<!-- Modal Nộp học phí -->
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>Nộp học phí</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p id="modalSemInfo" class="text-muted mb-3"></p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Số tiền còn lại phải nộp:</label>
                        <div id="modalRemainingText" class="fs-5 text-danger fw-bold"></div>
                    </div>
                    <div class="mb-3">
                        <label for="payAmount" class="form-label">Số tiền nộp (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="payAmount" name="amount" min="1000" step="1000" required>
                        <div class="form-text">Nhập số tiền bạn muốn nộp (có thể nộp một phần).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán (giả lập)</label>
                        <select class="form-select" disabled>
                            <option>Chuyển khoản ngân hàng</option>
                            <option>Tiền mặt tại phòng tài vụ</option>
                            <option>Ví điện tử MoMo</option>
                        </select>
                    </div>
                    <input type="hidden" name="invoice_id" id="modalInvoiceId">
                    <input type="hidden" name="pay_invoice" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Xác nhận nộp</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const payModal = document.getElementById('payModal');
payModal.addEventListener('show.bs.modal', e => {
    const btn = e.relatedTarget;
    const due  = parseFloat(btn.dataset.due);
    const paid = parseFloat(btn.dataset.paid);
    const rem  = due - paid;
    document.getElementById('modalInvoiceId').value = btn.dataset.invid;
    document.getElementById('modalSemInfo').textContent = 'Học kỳ: ' + btn.dataset.semname;
    document.getElementById('modalRemainingText').textContent =
        new Intl.NumberFormat('vi-VN').format(rem) + ' đ';
    const inp = document.getElementById('payAmount');
    inp.value = rem; inp.max = rem;
});
</script>
