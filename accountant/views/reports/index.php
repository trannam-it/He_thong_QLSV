<?php $pageTitle = 'Báo cáo Tài chính'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-file-earmark-bar-graph me-2"></i>Báo cáo Tài chính</h1>
    <div class="page-breadcrumb"><a href="<?= accUrl() ?>">Trang chủ</a> / Báo cáo</div>
</div>

<!-- Overview -->
<div class="row mb-4">
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-danger text-white"><div class="stat-card-body"><div class="stat-card-label">Tổng còn phải thu</div><div class="stat-card-value" style="font-size:1.1rem;"><?= number_format($stats['total_receivable']) ?>đ</div></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-success text-white"><div class="stat-card-body"><div class="stat-card-label">Tổng đã thu</div><div class="stat-card-value" style="font-size:1.1rem;"><?= number_format($stats['total_collected']) ?>đ</div></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-warning text-white"><div class="stat-card-body"><div class="stat-card-label">Hóa đơn chưa thu</div><div class="stat-card-value"><?= number_format($stats['unpaid_invoices']) ?></div></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-primary text-white"><div class="stat-card-body"><div class="stat-card-label">HB đã chi</div><div class="stat-card-value" style="font-size:1.1rem;"><?= number_format($stats['total_scholarship_amount']) ?>đ</div></div></div></div>
</div>

<div class="row">
    <!-- Tuition by semester -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-receipt me-2"></i>Học phí theo Học kỳ</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light"><tr><th>HK</th><th>Năm</th><th class="text-center">Hóa đơn</th><th class="text-end">Phải thu</th><th class="text-end">Đã thu</th><th class="text-end">Còn nợ</th><th class="text-center">Đã nộp</th><th class="text-center">Chưa nộp</th></tr></thead>
                        <tbody>
                            <?php foreach ($tuitionReport as $tr): ?>
                            <?php $outstanding = (float)$tr['total_due'] - (float)$tr['total_paid']; ?>
                            <tr>
                                <td><?= $tr['semester'] ?></td>
                                <td><?= $tr['year'] ?></td>
                                <td class="text-center"><?= $tr['total_invoices'] ?></td>
                                <td class="text-end"><?= number_format((float)$tr['total_due']) ?>đ</td>
                                <td class="text-end text-success"><?= number_format((float)$tr['total_paid']) ?>đ</td>
                                <td class="text-end <?= $outstanding > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($outstanding) ?>đ</td>
                                <td class="text-center text-success"><?= $tr['paid_count'] ?></td>
                                <td class="text-center text-danger"><?= $tr['unpaid_count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($tuitionReport)): ?><tr><td colspan="8" class="text-center text-muted py-3">Chưa có dữ liệu.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scholarship Financial Summary -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-trophy me-2"></i>Học bổng đã chi</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light"><tr><th>Học bổng</th><th>HK</th><th class="text-center">Đơn</th><th class="text-center">Duyệt</th><th class="text-end">Tổng chi</th></tr></thead>
                        <tbody>
                            <?php foreach ($scholarshipReport as $sr): ?>
                            <tr>
                                <td><?= htmlspecialchars($sr['name']) ?></td>
                                <td><?= $sr['semester'] ?>/<?= $sr['year'] ?></td>
                                <td class="text-center"><?= $sr['total_apps'] ?></td>
                                <td class="text-center text-success"><?= $sr['approved'] ?></td>
                                <td class="text-end text-success"><?= number_format((float)$sr['total_disbursed']) ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($scholarshipReport)): ?><tr><td colspan="5" class="text-center text-muted py-3">Chưa có dữ liệu.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
