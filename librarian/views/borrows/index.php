<?php $pageTitle = 'Mượn / Trả Sách'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-arrow-left-right me-2"></i>Quản lý Mượn / Trả Sách</h1>
    <div class="page-breadcrumb"><a href="<?= lUrl() ?>">Trang chủ</a> / Mượn Trả</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row mb-3">
    <!-- Form cho mượn -->
    <div class="col-md-5 mb-3">
        <div class="card">
            <div class="card-header bg-success text-white"><h6 class="mb-0"><i class="bi bi-box-arrow-in-down me-2"></i>Cho Mượn Sách</h6></div>
            <div class="card-body">
                <form method="post" action="<?= lUrl('borrows') ?>">
                    <input type="hidden" name="action" value="borrow">
                    <div class="mb-2">
                        <label class="form-label">Mã Sinh viên <span class="text-danger">*</span></label>
                        <input type="text" id="svSearch" class="form-control form-control-sm" placeholder="Nhập mã SV hoặc tên..." autocomplete="off">
                        <input type="hidden" name="student_id" id="studentIdInput" required>
                        <div id="svResults" class="list-group mt-1" style="position:absolute;z-index:999;width:100%;max-height:200px;overflow-y:auto;"></div>
                        <div id="svSelected" class="text-success small mt-1"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Sách <span class="text-danger">*</span></label>
                        <select name="book_id" class="form-select form-select-sm" required>
                            <option value="">Chọn sách...</option>
                            <?php foreach ($books as $b): ?>
                            <?php if ((int)$b['available_copies'] > 0): ?>
                            <option value="<?= $b['book_id'] ?>"><?= htmlspecialchars($b['title']) ?> (Còn: <?= $b['available_copies'] ?>)</option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Số ngày mượn</label>
                        <select name="due_days" class="form-select form-select-sm">
                            <option value="7">7 ngày</option>
                            <option value="14" selected>14 ngày</option>
                            <option value="21">21 ngày</option>
                            <option value="30">30 ngày</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-check-circle me-1"></i>Xác nhận Cho Mượn</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Filter -->
    <div class="col-md-7 mb-3">
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Lọc danh sách</h6></div>
            <div class="card-body">
                <form method="get" class="row g-2">
                    <input type="hidden" name="page" value="borrows">
                    <div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Tên sách, mã SV..." value="<?= htmlspecialchars($search) ?>"></div>
                    <div class="col-md-4">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Tất cả trạng thái</option>
                            <option value="Borrowed" <?= $filterStatus==='Borrowed'?'selected':'' ?>>Đang mượn</option>
                            <option value="Overdue" <?= $filterStatus==='Overdue'?'selected':'' ?>>Quá hạn</option>
                            <option value="Returned" <?= $filterStatus==='Returned'?'selected':'' ?>>Đã trả</option>
                            <option value="Lost" <?= $filterStatus==='Lost'?'selected':'' ?>>Mất sách</option>
                        </select>
                    </div>
                    <div class="col-md-3"><button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-filter me-1"></i>Lọc</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Borrows Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách Mượn/Trả <span class="badge bg-primary ms-2"><?= count($borrows) ?></span></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Sinh viên</th><th>Sách</th><th>Mượn ngày</th><th>Hạn trả</th><th>Trả ngày</th><th>Phạt</th><th>Trạng thái</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($borrows as $b): ?>
                    <?php
                    $bc = ['Borrowed'=>'primary','Returned'=>'success','Overdue'=>'danger','Lost'=>'dark'];
                    $bl = ['Borrowed'=>'Đang mượn','Returned'=>'Đã trả','Overdue'=>'Quá hạn','Lost'=>'Mất sách'];
                    $isActive = in_array($b['status'], ['Borrowed','Overdue']);
                    ?>
                    <tr class="<?= $b['status']==='Overdue' ? 'table-warning' : '' ?>">
                        <td><?= htmlspecialchars($b['student_name']) ?><br><small class="text-muted"><?= $b['student_code'] ?></small></td>
                        <td><?= htmlspecialchars($b['title']) ?><br><small class="text-muted"><?= htmlspecialchars($b['author'] ?? '') ?></small></td>
                        <td><?= date('d/m/Y', strtotime($b['borrow_date'])) ?></td>
                        <td class="<?= $b['status']!=='Returned' && $b['due_date'] < date('Y-m-d') ? 'text-danger fw-bold' : '' ?>"><?= date('d/m/Y', strtotime($b['due_date'])) ?></td>
                        <td><?= $b['return_date'] ? date('d/m/Y', strtotime($b['return_date'])) : '—' ?></td>
                        <td><?= $b['fine_amount'] > 0 ? number_format((float)$b['fine_amount']).'đ' : '—' ?></td>
                        <td><span class="badge bg-<?= $bc[$b['status']] ?? 'secondary' ?>"><?= $bl[$b['status']] ?? $b['status'] ?></span></td>
                        <td>
                            <?php if ($isActive): ?>
                            <button type="button" class="btn btn-xs btn-sm btn-outline-success"
                                    data-bs-toggle="modal" data-bs-target="#returnModal"
                                    data-borrow-id="<?= $b['borrow_id'] ?>" data-title="<?= htmlspecialchars($b['title']) ?>">
                                <i class="bi bi-box-arrow-in-left"></i> Trả
                            </button>
                            <form method="post" action="<?= lUrl('borrows') ?>" class="d-inline" onsubmit="return confirm('Đánh dấu mất sách?')">
                                <input type="hidden" name="action" value="mark_lost">
                                <input type="hidden" name="borrow_id" value="<?= $b['borrow_id'] ?>">
                                <button type="submit" class="btn btn-xs btn-sm btn-outline-dark"><i class="bi bi-x-circle"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($borrows)): ?><tr><td colspan="8" class="text-center text-muted py-4">Không có giao dịch.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-box-arrow-in-left me-2"></i>Trả Sách</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="post" action="<?= lUrl('borrows') ?>">
            <div class="modal-body">
                <input type="hidden" name="action" value="return">
                <input type="hidden" name="borrow_id" id="returnBorrowId">
                <p>Sách: <strong id="returnTitle"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Tiền phạt (nếu có)</label>
                    <input type="number" name="fine_amount" class="form-control" min="0" value="0" step="1000">
                    <small class="text-muted">Đơn vị: VNĐ</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check me-1"></i>Xác nhận Trả sách</button>
            </div>
        </form>
    </div></div>
</div>

<?php $extraJs = "
document.querySelectorAll('[data-bs-target=\"#returnModal\"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('returnBorrowId').value = this.dataset.borrowId;
        document.getElementById('returnTitle').textContent = this.dataset.title;
    });
});

// Search student
let svTimer;
document.getElementById('svSearch')?.addEventListener('input', function() {
    clearTimeout(svTimer);
    const q = this.value.trim();
    if (q.length < 2) { document.getElementById('svResults').innerHTML=''; return; }
    svTimer = setTimeout(() => {
        fetch('" . BASE_URL . "/librarian/api/router.php?resource=members&action=search&q=' + encodeURIComponent(q))
            .then(r=>r.json()).then(data=>{
                const ul = document.getElementById('svResults');
                ul.innerHTML = '';
                if (data.success && data.data.length) {
                    data.data.forEach(s => {
                        const li = document.createElement('a');
                        li.className='list-group-item list-group-item-action py-1';
                        li.textContent = s.student_code + ' – ' + s.full_name;
                        li.onclick = (e) => {
                            e.preventDefault();
                            document.getElementById('studentIdInput').value = s.student_id;
                            document.getElementById('svSearch').value = s.student_code + ' – ' + s.full_name;
                            document.getElementById('svSelected').textContent = '✔ Đã chọn: ' + s.full_name;
                            ul.innerHTML = '';
                        };
                        ul.appendChild(li);
                    });
                } else { ul.innerHTML = '<div class=\"list-group-item text-muted small\">Không tìm thấy.</div>'; }
            });
    }, 300);
});
"; ?>
