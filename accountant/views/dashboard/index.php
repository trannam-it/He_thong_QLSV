<?php $pageTitle = 'Dashboard Kế toán'; ?>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="page-header">
    <h1 class="page-title">Dashboard Kế toán</h1>
    <div class="page-breadcrumb">Trang chủ / Dashboard</div>
</div>

<div class="alert" style="background:linear-gradient(135deg,#fff3cd,#ffeaa0);border:1px solid #fd7e14;" class="mb-4">
    <h5><i class="bi bi-cash-coin me-2" style="color:#fd7e14;"></i>Xin chào, <?= htmlspecialchars($user['username']) ?>!</h5>
    <p class="mb-0" style="color:#6c757d;">Hệ thống Quản lý Tài chính Sinh viên – Hôm nay: <?= date('d/m/Y') ?></p>
</div>

<div class="row mb-4">
    <?php
    $cards = [
        ['label'=>'Hóa đơn chưa thu','value'=>$stats['unpaid_invoices'],'icon'=>'bi-file-earmark-x','color'=>'warning','fmt'=>false],
        ['label'=>'Hóa đơn quá hạn','value'=>$stats['overdue_invoices'],'icon'=>'bi-exclamation-circle','color'=>'danger','fmt'=>false],
        ['label'=>'Tổng còn phải thu','value'=>$stats['total_receivable'],'icon'=>'bi-arrow-up-circle','color'=>'danger','fmt'=>true],
        ['label'=>'Đã thu được','value'=>$stats['total_collected'],'icon'=>'bi-check-circle','color'=>'success','fmt'=>true],
        ['label'=>'HB đang chờ duyệt','value'=>$stats['pending_scholarship_apps'],'icon'=>'bi-hourglass-split','color'=>'info','fmt'=>false],
        ['label'=>'Tổng HB đã duyệt','value'=>$stats['total_scholarship_amount'],'icon'=>'bi-trophy','color'=>'primary','fmt'=>true],
    ];
    foreach ($cards as $c):
    ?>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stat-card bg-gradient-<?= $c['color'] ?> text-white">
            <div class="stat-card-body">
                <div class="stat-card-label"><?= $c['label'] ?></div>
                <div class="stat-card-value" style="font-size:1.3rem;"><?= $c['fmt'] ? number_format($c['value']).'đ' : number_format($c['value']) ?></div>
                <i class="bi <?= $c['icon'] ?> mt-1" style="font-size:1.2rem;opacity:.6;"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-receipt me-2"></i>Hóa đơn học phí gần đây</h5>
        <a href="<?= accUrl('tuition') ?>" class="btn btn-sm btn-outline-warning">Xem tất cả</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light"><tr><th>Sinh viên</th><th>Khoa</th><th>Học kỳ</th><th class="text-end">Phải thu</th><th class="text-end">Đã thu</th><th class="text-end">Còn lại</th><th>Trạng thái</th></tr></thead>
                <tbody>
                    <?php foreach ($recentInvoices as $inv): ?>
                    <?php
                    $remaining = (float)$inv['amount_due'] - (float)$inv['amount_paid'];
                    $sc = ['Unpaid'=>'warning','Partial'=>'info','Paid'=>'success','Overdue'=>'danger','Exempted'=>'secondary'];
                    $sl = ['Unpaid'=>'Chưa nộp','Partial'=>'Nộp 1 phần','Paid'=>'Đã nộp','Overdue'=>'Quá hạn','Exempted'=>'Miễn giảm'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($inv['student_name']) ?><br><small class="text-muted"><?= $inv['student_code'] ?></small></td>
                        <td><small><?= htmlspecialchars($inv['faculty_name'] ?? '—') ?></small></td>
                        <td><?= $inv['semester'] ?>/<?= $inv['year'] ?></td>
                        <td class="text-end"><?= number_format((float)$inv['amount_due']) ?>đ</td>
                        <td class="text-end text-success"><?= number_format((float)$inv['amount_paid']) ?>đ</td>
                        <td class="text-end <?= $remaining > 0 ? 'text-danger' : 'text-success' ?>"><?= number_format($remaining) ?>đ</td>
                        <td><span class="badge bg-<?= $sc[$inv['status']] ?? 'secondary' ?>"><?= $sl[$inv['status']] ?? $inv['status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentInvoices)): ?><tr><td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
