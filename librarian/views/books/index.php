<?php $pageTitle = 'Danh mục Sách'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-book me-2"></i>Danh mục Sách</h1>
    <div class="page-breadcrumb"><a href="<?= lUrl() ?>">Trang chủ</a> / Sách</div>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row">
    <!-- Form thêm/sửa -->
    <div class="col-lg-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><?= ($editBook && !$showHistory) ? '<i class="bi bi-pencil me-1"></i>Sửa sách' : '<i class="bi bi-plus-circle me-1"></i>Thêm sách mới' ?></h5>
            </div>
            <div class="card-body">
                <?php if ($showHistory && $editBook): ?>
                <h6 class="fw-bold mb-2"><?= htmlspecialchars($editBook['title']) ?></h6>
                <?php if (empty($bookHistory)): ?>
                <p class="text-muted">Chưa có lịch sử mượn.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Sinh viên</th><th>Mượn</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                            <?php foreach ($bookHistory as $h): ?>
                            <tr>
                                <td><?= htmlspecialchars($h['student_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($h['borrow_date'])) ?></td>
                                <td><span class="badge bg-<?= ['Borrowed'=>'primary','Returned'=>'success','Overdue'=>'danger','Lost'=>'dark'][$h['status']] ?? 'secondary' ?>"><?= $h['status'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <a href="<?= lUrl('books') ?>" class="btn btn-secondary btn-sm mt-2">← Quay lại</a>
                <?php else: ?>
                <form method="post" action="<?= lUrl('books') ?>">
                    <input type="hidden" name="action" value="<?= $editBook ? 'update' : 'create' ?>">
                    <?php if ($editBook): ?><input type="hidden" name="book_id" value="<?= $editBook['book_id'] ?>"><?php endif; ?>
                    <div class="mb-2"><label class="form-label">Tên sách *</label><input type="text" name="title" class="form-control form-control-sm" required value="<?= htmlspecialchars($editBook['title'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="form-label">Tác giả</label><input type="text" name="author" class="form-control form-control-sm" value="<?= htmlspecialchars($editBook['author'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="form-label">ISBN</label><input type="text" name="isbn" class="form-control form-control-sm" value="<?= htmlspecialchars($editBook['isbn'] ?? '') ?>"></div>
                    <div class="mb-2">
                        <label class="form-label">Thể loại</label>
                        <input type="text" name="category" class="form-control form-control-sm" list="catList" value="<?= htmlspecialchars($editBook['category'] ?? '') ?>">
                        <datalist id="catList"><?php foreach ($categories as $cat): ?><option value="<?= htmlspecialchars($cat) ?>"><?php endforeach; ?></datalist>
                    </div>
                    <div class="mb-2"><label class="form-label">Nhà xuất bản</label><input type="text" name="publisher" class="form-control form-control-sm" value="<?= htmlspecialchars($editBook['publisher'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="form-label">Năm xuất bản</label><input type="number" name="published_year" class="form-control form-control-sm" min="1900" max="2099" value="<?= $editBook['published_year'] ?? '' ?>"></div>
                    <div class="row g-1 mb-2">
                        <div class="col-6"><label class="form-label">Tổng bản sao</label><input type="number" name="total_copies" class="form-control form-control-sm" min="0" value="<?= $editBook['total_copies'] ?? 1 ?>"></div>
                        <div class="col-6"><label class="form-label">Khả dụng</label><input type="number" name="available_copies" class="form-control form-control-sm" min="0" value="<?= $editBook['available_copies'] ?? 1 ?>"></div>
                    </div>
                    <div class="mb-2"><label class="form-label">Mô tả</label><textarea name="description" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($editBook['description'] ?? '') ?></textarea></div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-save me-1"></i><?= $editBook ? 'Cập nhật' : 'Thêm' ?></button>
                        <?php if ($editBook): ?><a href="<?= lUrl('books') ?>" class="btn btn-secondary btn-sm">Hủy</a><?php endif; ?>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Danh sách sách -->
    <div class="col-lg-8 mb-3">
        <div class="card mb-2"><div class="card-body py-2">
            <form method="get" class="row g-2 align-items-end">
                <input type="hidden" name="page" value="books">
                <div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm tên sách, tác giả, ISBN..." value="<?= htmlspecialchars($search) ?>"></div>
                <div class="col-md-4">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">Tất cả thể loại</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3"><button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Tìm</button></div>
            </form>
        </div></div>

        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Sách <span class="badge bg-success ms-2"><?= count($books) ?></span></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light"><tr><th>Tên sách</th><th>Tác giả</th><th>Thể loại</th><th class="text-center">Tổng</th><th class="text-center">Còn</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            <?php foreach ($books as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['title']) ?><br><small class="text-muted"><?= htmlspecialchars($b['isbn'] ?? '') ?></small></td>
                                <td><small><?= htmlspecialchars($b['author'] ?? '—') ?></small></td>
                                <td><span class="badge bg-info text-dark" style="font-size:.7rem;"><?= htmlspecialchars($b['category'] ?? '—') ?></span></td>
                                <td class="text-center"><?= $b['total_copies'] ?></td>
                                <td class="text-center <?= (int)$b['available_copies'] === 0 ? 'text-danger' : 'text-success' ?>"><?= $b['available_copies'] ?></td>
                                <td>
                                    <a href="<?= lUrl('books') ?>?edit=<?= $b['book_id'] ?>" class="btn btn-xs btn-sm btn-outline-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= lUrl('books') ?>?history=<?= $b['book_id'] ?>" class="btn btn-xs btn-sm btn-outline-info" title="Lịch sử"><i class="bi bi-clock-history"></i></a>
                                    <form method="post" action="<?= lUrl('books') ?>" class="d-inline" onsubmit="return confirm('Xóa sách này?')">
                                        <input type="hidden" name="action" value="delete"><input type="hidden" name="book_id" value="<?= $b['book_id'] ?>">
                                        <button type="submit" class="btn btn-xs btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($books)): ?><tr><td colspan="6" class="text-center text-muted py-4">Không có sách.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
