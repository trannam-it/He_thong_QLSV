<?php $pageTitle = 'Dashboard'; ?>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="page-header">
    <h1 class="page-title">Dashboard Thư viện</h1>
    <div class="page-breadcrumb">Trang chủ / Dashboard</div>
</div>

<div class="alert alert-success mb-4">
    <h5><i class="bi bi-book-fill me-2"></i>Xin chào, <?= htmlspecialchars($user['username']) ?>!</h5>
    <p class="mb-0">Hệ thống quản lý Thư viện – Hôm nay: <?= date('d/m/Y') ?></p>
</div>

<div class="row mb-4">
    <?php
    $cards = [
        ['label'=>'Tổng đầu sách','value'=>$stats['total_books'],'icon'=>'bi-book','color'=>'success'],
        ['label'=>'Tổng bản sao','value'=>$stats['total_copies'],'icon'=>'bi-layers','color'=>'primary'],
        ['label'=>'Bản sao khả dụng','value'=>$stats['available_copies'],'icon'=>'bi-check-circle','color'=>'info'],
        ['label'=>'Đang được mượn','value'=>$stats['active_borrows'],'icon'=>'bi-arrow-up-right-circle','color'=>'warning'],
        ['label'=>'Quá hạn','value'=>$stats['overdue_count'],'icon'=>'bi-exclamation-triangle','color'=>'danger'],
        ['label'=>'Mượn hôm nay','value'=>$stats['today_borrows'],'icon'=>'bi-calendar-check','color'=>'primary'],
    ];
    foreach ($cards as $card):
    ?>
    <div class="col-lg-2 col-md-4 col-6 mb-3">
        <div class="stat-card bg-gradient-<?= $card['color'] ?> text-white">
            <div class="stat-card-body">
                <div class="stat-card-label"><?= $card['label'] ?></div>
                <div class="stat-card-value"><?= number_format($card['value']) ?></div>
                <i class="bi <?= $card['icon'] ?> mt-1" style="font-size:1.3rem;opacity:.7;"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Giao dịch gần đây</h5>
        <a href="<?= lUrl('borrows') ?>" class="btn btn-sm btn-outline-success">Xem tất cả</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Sinh viên</th><th>Sách</th><th>Ngày mượn</th><th>Hạn trả</th><th>Trả ngày</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBorrows as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['student_name']) ?><br><small class="text-muted"><?= $b['student_code'] ?></small></td>
                        <td><?= htmlspecialchars($b['title']) ?><br><small class="text-muted"><?= htmlspecialchars($b['author'] ?? '') ?></small></td>
                        <td><?= date('d/m/Y', strtotime($b['borrow_date'])) ?></td>
                        <td class="<?= $b['status']!=='Returned' && $b['due_date'] < date('Y-m-d') ? 'text-danger fw-bold' : '' ?>"><?= date('d/m/Y', strtotime($b['due_date'])) ?></td>
                        <td><?= $b['return_date'] ? date('d/m/Y', strtotime($b['return_date'])) : '—' ?></td>
                        <td>
                            <?php
                            $bc = ['Borrowed'=>'primary','Returned'=>'success','Overdue'=>'danger','Lost'=>'dark'];
                            $bl = ['Borrowed'=>'Đang mượn','Returned'=>'Đã trả','Overdue'=>'Quá hạn','Lost'=>'Mất sách'];
                            ?>
                            <span class="badge bg-<?= $bc[$b['status']] ?? 'secondary' ?>"><?= $bl[$b['status']] ?? $b['status'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentBorrows)): ?><tr><td colspan="6" class="text-center text-muted py-4">Chưa có giao dịch.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
