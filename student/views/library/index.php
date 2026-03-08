<?php
/**
 * View: Thư viện sinh viên
 * Biến: $student, $books, $history, $keyword, $activeBorrows,
 *       $totalBorrowed, $overdueCount, $totalFine, $msg, $error
 */
$pageTitle   = 'Thư viện';
$currentPage = 'student_library';
$extraCss    = '
.book-card { transition:transform .15s, box-shadow .15s; }
.book-card:hover { transform:translateY(-3px); box-shadow:0 6px 20px rgba(0,0,0,.12); }
.book-cover-placeholder {
    width:100%; height:140px; background:linear-gradient(135deg,#4e73df,#764ba2);
    display:flex; align-items:center; justify-content:center; color:#fff; font-size:2.5rem;
}
.category-pill { font-size:.72rem; }
';

function borrowStatusBadge(string $status): string {
    return match($status) {
        'Borrowed' => '<span class="badge bg-primary">Đang mượn</span>',
        'Overdue'  => '<span class="badge bg-danger">Quá hạn</span>',
        'Returned' => '<span class="badge bg-success">Đã trả</span>',
        'Lost'     => '<span class="badge bg-dark">Mất sách</span>',
        default    => '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>',
    };
}

// Lấy ID sách đang mượn (để disable nút)
$borrowingBookIds = array_unique(array_filter(
    array_map(fn($b) => $b['status'] !== 'Returned' ? ($b['book_id'] ?? null) : null, $history)
));
$activeBorrowCount = $activeBorrows;
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-book me-2"></i>Thư viện</h1>
    <div class="page-breadcrumb"><a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Thư viện</div>
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
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-gradient-primary text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Đang mượn</div>
                    <div class="stat-card-value"><?= $activeBorrows ?>/3</div></div>
                    <i class="bi bi-bookmark-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-gradient-info text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Tổng đã mượn</div>
                    <div class="stat-card-value"><?= $totalBorrowed ?></div></div>
                    <i class="bi bi-journals stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card <?= $overdueCount > 0 ? 'bg-gradient-danger' : 'bg-gradient-success' ?> text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Quá hạn</div>
                    <div class="stat-card-value"><?= $overdueCount ?></div></div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card <?= $totalFine > 0 ? 'bg-gradient-warning' : 'bg-gradient-success' ?> text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Tổng phí phạt</div>
                    <div class="stat-card-value" style="font-size:1.1rem"><?= number_format($totalFine,0,',','.') ?> đ</div></div>
                    <i class="bi bi-cash stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-0">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tabBooks">
            <i class="bi bi-search me-1"></i>Tìm kiếm sách
            <span class="badge bg-primary ms-1"><?= count($books) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tabHistory">
            <i class="bi bi-clock-history me-1"></i>Lịch sử mượn
            <span class="badge bg-secondary ms-1"><?= count($history) ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">

    <!-- Tab 1: Danh sách sách -->
    <div class="tab-pane fade show active" id="tabBooks">
        <div class="content-card" style="border-top-left-radius:0;border-top-right-radius:0">
            <div class="content-card-header">
                <h5 class="content-card-title">Danh sách sách thư viện</h5>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="q" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($keyword) ?>"
                           placeholder="Tìm theo tên, tác giả, ISBN..."
                           style="width:260px">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if ($keyword): ?>
                    <a href="<?= BASE_URL ?>/student/?page=library" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="content-card-body">
                <?php if (empty($books)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-book-half fs-1"></i>
                    <p class="mt-2">Không tìm thấy sách phù hợp.</p>
                </div>
                <?php else: ?>
                <div class="row g-3">
                <?php foreach ($books as $book):
                    $isBorrowing = in_array($book['book_id'], $borrowingBookIds);
                    $noMore = (int)$book['available_copies'] <= 0;
                    $maxBorrow = $activeBorrows >= 3;
                ?>
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="card h-100 book-card border-0 shadow-sm">
                        <div class="book-cover-placeholder">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title fw-bold mb-1" title="<?= htmlspecialchars($book['title']) ?>">
                                <?= htmlspecialchars(mb_strimwidth($book['title'], 0, 40, '...')) ?>
                            </h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($book['author'] ?? '—') ?>
                            </p>
                            <?php if (!empty($book['category'])): ?>
                            <span class="badge bg-info category-pill"><?= htmlspecialchars($book['category']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($book['isbn'])): ?>
                            <p class="text-muted" style="font-size:.7rem;margin-top:4px">ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                            <small class="<?= $noMore ? 'text-danger' : 'text-success' ?>">
                                <i class="bi bi-<?= $noMore ? 'x-circle' : 'check-circle' ?> me-1"></i>
                                <?= $noMore ? 'Hết sách' : $book['available_copies'].' bản' ?>
                            </small>
                            <?php if (!$noMore && !$isBorrowing && !$maxBorrow): ?>
                            <form method="POST">
                                <input type="hidden" name="book_id"    value="<?= $book['book_id'] ?>">
                                <input type="hidden" name="due_days"   value="14">
                                <input type="hidden" name="borrow_book" value="1">
                                <button type="submit" class="btn btn-primary btn-sm"
                                        onclick="return confirm('Mượn: <?= htmlspecialchars(addslashes($book['title'])) ?>?')">
                                    <i class="bi bi-bookmark-plus me-1"></i>Mượn
                                </button>
                            </form>
                            <?php elseif ($isBorrowing): ?>
                            <span class="text-muted small"><i class="bi bi-bookmark-check me-1"></i>Đang mượn</span>
                            <?php elseif ($maxBorrow): ?>
                            <span class="text-warning small"><i class="bi bi-exclamation me-1"></i>Đã đạt giới hạn</span>
                            <?php else: ?>
                            <span class="text-muted small">Hết sách</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 2: Lịch sử mượn -->
    <div class="tab-pane fade" id="tabHistory">
        <div class="content-card" style="border-top-left-radius:0">
            <div class="content-card-body p-0">
                <?php if (empty($history)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard-x fs-1"></i>
                    <p class="mt-2">Chưa có lịch sử mượn sách.</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sách</th><th>Tác giả</th>
                                <th>Ngày mượn</th><th>Hạn trả</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Phí phạt</th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($history as $bor): ?>
                        <tr class="<?= $bor['status'] === 'Overdue' ? 'table-danger' : '' ?>">
                            <td><strong><?= htmlspecialchars($bor['title']) ?></strong></td>
                            <td><?= htmlspecialchars($bor['author'] ?? '—') ?></td>
                            <td><?= date('d/m/Y', strtotime($bor['borrow_date'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($bor['due_date'])) ?></td>
                            <td class="text-center"><?= borrowStatusBadge($bor['status']) ?></td>
                            <td class="text-center">
                                <?php if ($bor['fine_amount'] > 0): ?>
                                <span class="text-danger"><?= number_format($bor['fine_amount'], 0, ',', '.') ?> đ</span>
                                <?php else: ?>—<?php endif; ?>
                            </td>
                            <td>
                                <?php if (in_array($bor['status'], ['Borrowed','Overdue'])): ?>
                                <form method="POST" onsubmit="return confirm('Xác nhận trả sách?')">
                                    <input type="hidden" name="borrow_id"   value="<?= $bor['borrow_id'] ?>">
                                    <input type="hidden" name="return_book" value="1">
                                    <button class="btn btn-sm btn-success">
                                        <i class="bi bi-arrow-return-left me-1"></i>Trả sách
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
