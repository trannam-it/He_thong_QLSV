<?php $pageTitle = 'Bạn đọc'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-people me-2"></i>Danh sách Bạn đọc</h1>
    <div class="page-breadcrumb"><a href="<?= lUrl() ?>">Trang chủ</a> / Bạn đọc</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<?php if ($memberDetail): ?>
<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($memberDetail['full_name']) ?></h5>
                <a href="<?= lUrl('members') ?>" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Mã SV:</strong> <?= htmlspecialchars($memberDetail['student_code']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($memberDetail['email']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Khoa:</strong> <?= htmlspecialchars($memberDetail['faculty_name'] ?? '—') ?></p>
                        <p><strong>Đang mượn:</strong> <span class="badge bg-<?= (int)$memberDetail['overdue_borrows'] > 0 ? 'danger' : 'primary' ?>"><?= $memberDetail['active_borrows'] ?></span></p>
                    </div>
                </div>
                <h6 class="fw-bold mb-2">Lịch sử mượn sách (<?= count($memberHistory) ?>)</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Sách</th><th>Mượn</th><th>Hạn</th><th>Trả</th><th>Phạt</th><th>TT</th></tr></thead>
                        <tbody>
                            <?php foreach ($memberHistory as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['title']) ?></td>
                                <td><?= date('d/m/Y', strtotime($h['borrow_date'])) ?></td>
                                <td class="<?= $h['status']!=='Returned' && $h['due_date'] < date('Y-m-d') ? 'text-danger' : '' ?>"><?= date('d/m/Y', strtotime($h['due_date'])) ?></td>
                                <td><?= $h['return_date'] ? date('d/m/Y', strtotime($h['return_date'])) : '—' ?></td>
                                <td><?= $h['fine_amount'] > 0 ? number_format((float)$h['fine_amount']).'đ' : '—' ?></td>
                                <td><span class="badge bg-<?= ['Borrowed'=>'primary','Returned'=>'success','Overdue'=>'danger','Lost'=>'dark'][$h['status']] ?? 'secondary' ?>"><?= $h['status'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($memberHistory)): ?><tr><td colspan="6" class="text-center text-muted">Chưa có lịch sử.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Bạn đọc đã từng mượn sách <span class="badge bg-success ms-2"><?= count($members) ?></span></h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light"><tr><th>Mã SV</th><th>Họ tên</th><th>Email</th><th>Khoa</th><th class="text-center">Đang mượn</th><th class="text-center">Quá hạn</th><th class="text-center">Tổng mượn</th><th>Thao tác</th></tr></thead>
                <tbody>
                    <?php foreach ($members as $m): ?>
                    <tr class="<?= (int)$m['overdue_borrows'] > 0 ? 'table-warning' : '' ?>">
                        <td><?= htmlspecialchars($m['student_code']) ?></td>
                        <td><?= htmlspecialchars($m['full_name']) ?></td>
                        <td><small><?= htmlspecialchars($m['email']) ?></small></td>
                        <td><small><?= htmlspecialchars($m['faculty_name'] ?? '—') ?></small></td>
                        <td class="text-center"><span class="badge bg-<?= (int)$m['active_borrows'] > 0 ? 'primary' : 'secondary' ?>"><?= $m['active_borrows'] ?></span></td>
                        <td class="text-center"><span class="badge bg-<?= (int)$m['overdue_borrows'] > 0 ? 'danger' : 'secondary' ?>"><?= $m['overdue_borrows'] ?></span></td>
                        <td class="text-center"><?= $m['total_borrows'] ?></td>
                        <td><a href="<?= lUrl('members') ?>?detail=<?= $m['student_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($members)): ?><tr><td colspan="8" class="text-center text-muted py-4">Chưa có bạn đọc.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
