<?php $pageTitle = 'Báo cáo Thống kê'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-graph-up me-2"></i>Báo cáo Thống kê</h1>
    <div class="page-breadcrumb"><a href="<?= lUrl() ?>">Trang chủ</a> / Báo cáo</div>
</div>

<!-- Overview -->
<div class="row mb-4">
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-success text-white"><div class="stat-card-body"><div class="stat-card-label">Tổng đầu sách</div><div class="stat-card-value"><?= number_format($stats['total_books']) ?></div><i class="bi bi-book stat-icon" style="font-size:1.5rem;opacity:.5;"></i></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-primary text-white"><div class="stat-card-body"><div class="stat-card-label">Đang được mượn</div><div class="stat-card-value"><?= number_format($stats['active_borrows']) ?></div><i class="bi bi-arrow-up stat-icon" style="font-size:1.5rem;opacity:.5;"></i></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-danger text-white"><div class="stat-card-body"><div class="stat-card-label">Quá hạn</div><div class="stat-card-value"><?= number_format($stats['overdue_count']) ?></div><i class="bi bi-exclamation-triangle stat-icon" style="font-size:1.5rem;opacity:.5;"></i></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card bg-gradient-warning text-white"><div class="stat-card-body"><div class="stat-card-label">Tổng tiền phạt</div><div class="stat-card-value"><?= number_format($totalFines) ?>đ</div><i class="bi bi-cash stat-icon" style="font-size:1.5rem;opacity:.5;"></i></div></div></div>
</div>

<div class="row">
    <!-- Top borrowed books -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-trophy me-2"></i>Top 10 Sách được mượn nhiều nhất</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light"><tr><th>#</th><th>Sách</th><th>Thể loại</th><th class="text-center">Lượt mượn</th><th class="text-center">Đang mượn</th></tr></thead>
                        <tbody>
                            <?php foreach ($topBooks as $i => $b): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($b['title']) ?><br><small class="text-muted"><?= htmlspecialchars($b['author'] ?? '') ?></small></td>
                                <td><span class="badge bg-info text-dark" style="font-size:.7rem;"><?= htmlspecialchars($b['category'] ?? '—') ?></span></td>
                                <td class="text-center fw-bold"><?= $b['borrow_count'] ?></td>
                                <td class="text-center"><?= $b['current_borrows'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-tags me-2"></i>Thống kê theo Thể loại</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light"><tr><th>Thể loại</th><th class="text-center">Đầu sách</th><th class="text-center">Bản sao</th><th class="text-center">Khả dụng</th><th class="text-center">Lượt mượn</th></tr></thead>
                        <tbody>
                            <?php foreach ($categoryStats as $cs): ?>
                            <tr>
                                <td><?= htmlspecialchars($cs['category'] ?? '—') ?></td>
                                <td class="text-center"><?= $cs['book_count'] ?></td>
                                <td class="text-center"><?= $cs['total_copies'] ?></td>
                                <td class="text-center <?= (int)$cs['available_copies'] === 0 ? 'text-danger' : 'text-success' ?>"><?= $cs['available_copies'] ?></td>
                                <td class="text-center"><?= $cs['borrow_count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Stats -->
<div class="card">
    <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-calendar-month me-2"></i>Thống kê mượn/trả theo tháng (6 tháng gần nhất)</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light"><tr><th>Tháng</th><th class="text-center">Lượt mượn</th><th class="text-center">Lượt trả</th><th class="text-center">Tiền phạt thu</th></tr></thead>
                <tbody>
                    <?php foreach ($monthlyStats as $ms): ?>
                    <tr>
                        <td><?= $ms['month'] ?></td>
                        <td class="text-center"><?= $ms['borrows'] ?></td>
                        <td class="text-center"><?= $ms['returns'] ?></td>
                        <td class="text-center"><?= number_format((float)$ms['fines']) ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($monthlyStats)): ?><tr><td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
