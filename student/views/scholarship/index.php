<?php
/**
 * View: Học bổng sinh viên
 * Biến: $student, $myGpa, $available, $myApps, $msg, $error
 */
$pageTitle   = 'Học bổng';
$currentPage = 'student_scholarship';

function schStatusBadge(string $status): string {
    return match($status) {
        'Approved' => '<span class="badge bg-success">Được duyệt</span>',
        'Rejected' => '<span class="badge bg-danger">Bị từ chối</span>',
        default    => '<span class="badge bg-warning text-dark">Đang chờ</span>',
    };
}
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-trophy me-2"></i>Học bổng</h1>
    <div class="page-breadcrumb"><a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Học bổng</div>
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

<!-- GPA Info -->
<div class="alert <?= $myGpa !== null ? ($myGpa >= 80 ? 'alert-success' : ($myGpa >= 60 ? 'alert-warning' : 'alert-danger')) : 'alert-secondary' ?> mb-4">
    <i class="bi bi-graph-up me-2"></i>
    <strong>GPA tích lũy của bạn:</strong>
    <?= $myGpa !== null ? '<span class="fs-5 fw-bold">' . number_format($myGpa, 2) . '</span> / 100' : 'Chưa có dữ liệu điểm' ?>
    <?php if ($myGpa !== null): ?>
        <?php if ($myGpa >= 90): ?>&nbsp;<span class="badge bg-success">Xuất sắc</span>
        <?php elseif ($myGpa >= 80): ?>&nbsp;<span class="badge bg-primary">Giỏi</span>
        <?php elseif ($myGpa >= 70): ?>&nbsp;<span class="badge bg-info">Khá</span>
        <?php elseif ($myGpa >= 60): ?>&nbsp;<span class="badge bg-warning text-dark">Trung bình</span>
        <?php else: ?>&nbsp;<span class="badge bg-danger">Yếu</span>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#available">
            <i class="bi bi-award me-1"></i>Học bổng khả dụng
            <span class="badge bg-primary ms-1"><?= count($available) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#myapps">
            <i class="bi bi-file-earmark-check me-1"></i>Đơn của tôi
            <span class="badge bg-secondary ms-1"><?= count($myApps) ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Tab: Học bổng khả dụng -->
    <div class="tab-pane fade show active" id="available">
        <?php if (empty($available)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">Hiện không có học bổng nào đang mở.</p>
        </div>
        <?php else: ?>
        <div class="row">
        <?php foreach ($available as $sch):
            $semName   = match($sch['semester']) {'Spring'=>'HK1','Summer'=>'HKHè','Fall'=>'HK2',default=>$sch['semester']};
            $remaining = $sch['quantity'] !== null ? max(0, (int)$sch['quantity'] - (int)$sch['applied_count']) : null;
            $canApply  = !$sch['my_applied']
                && ($sch['deadline'] === null || date('Y-m-d') <= $sch['deadline'])
                && ($remaining === null || $remaining > 0)
                && ($sch['min_gpa'] === null || ($myGpa !== null && $myGpa >= $sch['min_gpa']))
                && ($sch['max_gpa'] === null || ($myGpa === null || $myGpa <= $sch['max_gpa']));
        ?>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><?= htmlspecialchars($sch['name']) ?></span>
                    <span class="badge bg-light text-dark"><?= $semName ?> <?= $sch['year'] ?></span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2"><?= htmlspecialchars($sch['description'] ?? '') ?></p>
                    <div class="mb-2">
                        <i class="bi bi-currency-dollar text-success me-1"></i>
                        <strong>Giá trị:</strong> <?= number_format($sch['value'],0,',','.') ?> đ
                    </div>
                    <?php if ($sch['min_gpa'] !== null || $sch['max_gpa'] !== null): ?>
                    <div class="mb-2">
                        <i class="bi bi-graph-up text-primary me-1"></i>
                        <strong>Điều kiện GPA:</strong>
                        <?php
                        if ($sch['min_gpa'] !== null && $sch['max_gpa'] !== null) echo "từ {$sch['min_gpa']} đến {$sch['max_gpa']}";
                        elseif ($sch['min_gpa'] !== null) echo "≥ {$sch['min_gpa']}";
                        else echo "≤ {$sch['max_gpa']}";
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($sch['quantity'] !== null): ?>
                    <div class="mb-2">
                        <i class="bi bi-people text-info me-1"></i>
                        <strong>Chỉ tiêu:</strong> <?= $remaining ?>/<?= $sch['quantity'] ?> còn lại
                    </div>
                    <?php endif; ?>
                    <?php if ($sch['deadline']): ?>
                    <div class="mb-2">
                        <i class="bi bi-calendar-event text-warning me-1"></i>
                        <strong>Hạn nộp:</strong> <?= date('d/m/Y', strtotime($sch['deadline'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                    <?php if ($sch['my_applied']): ?>
                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Đã đăng ký</span>
                    <?php elseif ($canApply): ?>
                        <form method="POST">
                            <input type="hidden" name="scholarship_id" value="<?= $sch['scholarship_id'] ?>">
                            <input type="hidden" name="apply_scholarship" value="1">
                            <button class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-send me-1"></i>Đăng ký ngay
                            </button>
                        </form>
                    <?php elseif ($remaining !== null && $remaining === 0): ?>
                        <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Hết chỉ tiêu</span>
                    <?php elseif ($sch['deadline'] && date('Y-m-d') > $sch['deadline']): ?>
                        <span class="text-secondary"><i class="bi bi-clock-history me-1"></i>Hết hạn</span>
                    <?php else: ?>
                        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Không đủ điều kiện GPA</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tab: Đơn của tôi -->
    <div class="tab-pane fade" id="myapps">
        <?php if (empty($myApps)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-file-earmark-x fs-1"></i>
            <p class="mt-2">Bạn chưa đăng ký học bổng nào.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th><th>Tên học bổng</th><th>Học kỳ</th>
                        <th>Giá trị</th><th>Ngày đăng ký</th><th>Trạng thái</th><th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($myApps as $i => $app):
                    $semName = match($app['semester']) {'Spring'=>'HK1','Summer'=>'HKHè','Fall'=>'HK2',default=>$app['semester']};
                ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><strong><?= htmlspecialchars($app['name']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($app['description'] ?? '') ?></small></td>
                    <td><?= $semName ?> <?= $app['year'] ?></td>
                    <td class="text-success fw-bold"><?= number_format($app['value'],0,',','.') ?> đ</td>
                    <td><?= date('d/m/Y H:i', strtotime($app['applied_at'])) ?></td>
                    <td><?= schStatusBadge($app['status']) ?></td>
                    <td>
                        <?php if ($app['status'] === 'Pending'): ?>
                        <form method="POST" onsubmit="return confirm('Hủy đơn đăng ký học bổng này?')">
                            <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                            <input type="hidden" name="cancel_application" value="1">
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x-circle"></i>
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
