<?php $pageTitle = 'Tài chính Sinh viên'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-person-lines-fill me-2"></i>Tài chính Sinh viên</h1>
    <div class="page-breadcrumb"><a href="<?= accUrl() ?>">Trang chủ</a> / Sinh viên</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="students">
            <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Mã SV, tên..." value="<?= htmlspecialchars($search) ?>"></div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="Studying" <?= $filterStatus==='Studying'?'selected':'' ?>>Đang học</option>
                    <option value="Graduated" <?= $filterStatus==='Graduated'?'selected':'' ?>>Tốt nghiệp</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-warning w-100"><i class="bi bi-search me-1"></i>Tìm</button></div>
        </form>
    </div>
</div>

<!-- Student Detail Modal -->
<?php if ($studentDetail): ?>
<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Hóa đơn học phí: <?= htmlspecialchars($studentDetail['full_name']) ?></h5>
                <a href="<?= accUrl('students') ?>" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <div class="row mb-3 text-center">
                    <div class="col-4"><strong>Tổng phải thu:</strong><br><span class="text-danger"><?= number_format((float)($studentDetail['total_due'] ?? 0)) ?>đ</span></div>
                    <div class="col-4"><strong>Đã nộp:</strong><br><span class="text-success"><?= number_format((float)($studentDetail['total_paid'] ?? 0)) ?>đ</span></div>
                    <div class="col-4"><strong>Còn nợ:</strong><br><span class="<?= (float)($studentDetail['outstanding'] ?? 0) > 0 ? 'text-danger fw-bold' : 'text-muted' ?>"><?= number_format((float)($studentDetail['outstanding'] ?? 0)) ?>đ</span></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Học kỳ</th><th>Năm</th><th>TC</th><th class="text-end">Phải thu</th><th class="text-end">Đã nộp</th><th>Hạn</th><th>TT</th></tr></thead>
                        <tbody>
                            <?php
                            $sc2 = ['Unpaid'=>'warning','Partial'=>'info','Paid'=>'success','Overdue'=>'danger','Exempted'=>'secondary'];
                            $sl2 = ['Unpaid'=>'Chưa nộp','Partial'=>'1 phần','Paid'=>'Đã nộp','Overdue'=>'Quá hạn','Exempted'=>'Miễn'];
                            foreach ($studentInvoices as $inv):
                            ?>
                            <tr>
                                <td><?= $inv['semester'] ?></td>
                                <td><?= $inv['year'] ?></td>
                                <td class="text-center"><?= $inv['total_credits'] ?></td>
                                <td class="text-end"><?= number_format((float)$inv['amount_due']) ?>đ</td>
                                <td class="text-end text-success"><?= number_format((float)$inv['amount_paid']) ?>đ</td>
                                <td><?= $inv['due_date'] ? date('d/m/Y', strtotime($inv['due_date'])) : '—' ?></td>
                                <td><span class="badge bg-<?= $sc2[$inv['status']] ?? 'secondary' ?>"><?= $sl2[$inv['status']] ?? $inv['status'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($studentInvoices)): ?><tr><td colspan="7" class="text-center text-muted">Chưa có hóa đơn.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Danh sách sinh viên <span class="badge bg-warning text-dark ms-2"><?= count($students) ?></span></h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light"><tr><th>Mã SV</th><th>Họ tên</th><th>Khoa</th><th class="text-end">Tổng phải thu</th><th class="text-end">Đã nộp</th><th class="text-end">Còn nợ</th><th>Thao tác</th></tr></thead>
                <tbody>
                    <?php foreach ($students as $sv): ?>
                    <tr class="<?= (float)($sv['outstanding'] ?? 0) > 0 ? 'table-warning' : '' ?>">
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($sv['student_code']) ?></span></td>
                        <td><?= htmlspecialchars($sv['full_name']) ?></td>
                        <td><small><?= htmlspecialchars($sv['faculty_name'] ?? '—') ?></small></td>
                        <td class="text-end"><?= number_format((float)($sv['total_due'] ?? 0)) ?>đ</td>
                        <td class="text-end text-success"><?= number_format((float)($sv['total_paid'] ?? 0)) ?>đ</td>
                        <td class="text-end <?= (float)($sv['outstanding'] ?? 0) > 0 ? 'text-danger fw-bold' : 'text-muted' ?>"><?= number_format((float)($sv['outstanding'] ?? 0)) ?>đ</td>
                        <td><a href="<?= accUrl('students') ?>?detail=<?= $sv['student_id'] ?>&search=<?= urlencode($search) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-eye"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?><tr><td colspan="7" class="text-center text-muted py-4">Không tìm thấy.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
