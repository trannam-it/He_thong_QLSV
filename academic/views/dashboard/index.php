<?php
/**
 * View: Dashboard Quản lý Đào tạo
 */
$pageTitle = 'Dashboard';
?>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div class="page-breadcrumb">Trang chủ / Dashboard</div>
</div>

<div class="alert alert-primary mb-4">
    <h5><i class="bi bi-journal-bookmark-fill me-2"></i>Xin chào, <?= htmlspecialchars($user['username']) ?>!</h5>
    <p class="mb-0">Chào mừng đến với hệ thống Quản lý Đào tạo.
        <?php if ($currentSem): ?>
            Học kỳ hiện tại: <strong><?= htmlspecialchars($currentSem['semester_name']) ?></strong>
        <?php endif; ?>
    </p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-primary text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">Sinh viên đang học</div>
                        <div class="stat-card-value"><?= number_format($stats['total_students']) ?></div>
                    </div>
                    <i class="bi bi-people-fill stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-success text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">Giảng viên</div>
                        <div class="stat-card-value"><?= number_format($stats['total_lecturers']) ?></div>
                    </div>
                    <i class="bi bi-person-badge-fill stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-info text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">Học phần</div>
                        <div class="stat-card-value"><?= number_format($stats['total_subjects']) ?></div>
                    </div>
                    <i class="bi bi-book-half stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-warning text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">Lớp đang mở</div>
                        <div class="stat-card-value"><?= number_format($stats['total_active_classes']) ?></div>
                    </div>
                    <i class="bi bi-door-open-fill stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #6f42c1;">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label text-muted">Đăng ký đang học</div>
                        <div class="stat-card-value text-purple"><?= number_format($stats['total_enrollments']) ?></div>
                    </div>
                    <i class="bi bi-journal-check" style="font-size:2rem;color:#6f42c1;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card" style="border-left: 4px solid #20c997;">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label text-muted">Khoa / Bộ môn</div>
                        <div class="stat-card-value" style="color:#20c997;"><?= number_format($stats['total_faculties']) ?></div>
                    </div>
                    <i class="bi bi-building" style="font-size:2rem;color:#20c997;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Classes -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Lớp học phần gần đây</h5>
        <a href="<?= aUrl('classes') ?>" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã lớp</th>
                        <th>Tên lớp</th>
                        <th>Học phần</th>
                        <th>Giảng viên</th>
                        <th>Học kỳ</th>
                        <th>SV đăng ký</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentClasses as $cls): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($cls['class_code']) ?></span></td>
                        <td><?= htmlspecialchars($cls['class_code']) ?></td>
                        <td><?= htmlspecialchars($cls['subject_name']) ?> <small class="text-muted">(<?= $cls['credits'] ?> TC)</small></td>
                        <td><?= htmlspecialchars($cls['lecturer_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($cls['semester_name'] ?? '—') ?></td>
                        <td>
                            <span class="<?= (int)$cls['enrolled'] >= (int)$cls['max_students'] ? 'text-danger' : 'text-success' ?>">
                                <?= $cls['enrolled'] ?>/<?= $cls['max_students'] ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusMap = ['Active'=>'success','Inactive'=>'secondary','Completed'=>'info','Cancelled'=>'danger'];
                            $sc = $statusMap[$cls['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $sc ?>"><?= htmlspecialchars($cls['status']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentClasses)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
