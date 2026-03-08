<?php
/**
 * View: Dashboard sinh viên
 * Biến được truyền từ DashboardController
 * $student, $studentId, $gpaData, $schedule, $grades, $success, $error
 */
$pageTitle   = 'Dashboard';
$currentPage = 'student';
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

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div class="page-breadcrumb">
        <a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Dashboard
    </div>
</div>

<!-- Welcome Alert -->
<div class="alert alert-primary mb-4">
    <h5><i class="bi bi-emoji-smile me-2"></i>Xin chào, <?= htmlspecialchars($student['full_name']) ?>!</h5>
    <p class="mb-0">Chào mừng bạn đến với hệ thống quản lý sinh viên. Hôm nay là <?= date('d/m/Y') ?>.</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-primary text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">GPA Tích lũy</div>
                        <div class="stat-card-value"><?= number_format($gpaData['gpa'] ?? 0, 2) ?></div>
                    </div>
                    <i class="bi bi-graph-up stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-success text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">Tín chỉ tích lũy</div>
                        <div class="stat-card-value"><?= $gpaData['total_credits'] ?? 0 ?></div>
                    </div>
                    <i class="bi bi-journal-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-info text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">Môn đã học</div>
                        <div class="stat-card-value"><?= $gpaData['total_courses'] ?? 0 ?></div>
                    </div>
                    <i class="bi bi-book stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card bg-gradient-warning text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label">Trạng thái</div>
                        <div class="stat-card-value" style="font-size:1.1rem"><?= htmlspecialchars($student['status']) ?></div>
                    </div>
                    <i class="bi bi-person-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Lịch học -->
    <div class="col-lg-6 mb-4">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="content-card-title">
                    <i class="bi bi-calendar3 me-2"></i>Lịch học hiện tại
                </h5>
                <a href="<?= BASE_URL ?>/student/?page=schedule" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="content-card-body">
                <?php if (!empty($schedule)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($schedule as $class): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($class['subject_name']) ?></h6>
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($class['lecturer_name']) ?>
                                    <span class="ms-2">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?= formatSemester($class['semester']) ?> - <?= $class['year'] ?>
                                    </span>
                                </small>
                            </div>
                            <span class="badge <?= getStatusBadgeClass($class['status']) ?>">
                                <?= formatEnrollmentStatus($class['status']) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x" style="font-size:3rem"></i>
                    <p class="mt-2">Chưa có lịch học</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kết quả học tập gần nhất -->
    <div class="col-lg-6 mb-4">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="content-card-title">
                    <i class="bi bi-file-text me-2"></i>Kết quả học tập
                </h5>
                <a href="<?= BASE_URL ?>/student/?page=grades" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
            </div>
            <div class="content-card-body">
                <?php if (!empty($grades)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Môn học</th>
                                <th class="text-center">TC</th>
                                <th class="text-center">Điểm</th>
                                <th class="text-center">Chữ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($grades, 0, 5) as $grade): ?>
                            <tr>
                                <td>
                                    <small class="d-block text-muted"><?= htmlspecialchars($grade['subject_code']) ?></small>
                                    <?= htmlspecialchars($grade['subject_name']) ?>
                                </td>
                                <td class="text-center"><?= $grade['credit_hours'] ?></td>
                                <td class="text-center">
                                    <strong><?= $grade['score'] ? number_format($grade['score'], 1) : 'N/A' ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $grade['grade_letter'] ?? 'N/A' ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-clipboard-x" style="font-size:3rem"></i>
                    <p class="mt-2">Chưa có kết quả học tập</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Thông tin cá nhân tóm tắt -->
<div class="row">
    <div class="col-lg-12">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="content-card-title">
                    <i class="bi bi-person-circle me-2"></i>Thông tin cá nhân
                </h5>
                <a href="<?= BASE_URL ?>/student/?page=profile" class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
            </div>
            <div class="content-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Mã sinh viên:</th>
                                <td><?= htmlspecialchars($student['student_code']) ?></td>
                            </tr>
                            <tr>
                                <th>Họ và tên:</th>
                                <td><?= htmlspecialchars($student['full_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Giới tính:</th>
                                <td><?= $student['gender'] === 'Male' ? 'Nam' : 'Nữ' ?></td>
                            </tr>
                            <tr>
                                <th>Ngày sinh:</th>
                                <td><?= date('d/m/Y', strtotime($student['birth_date'])) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Email:</th>
                                <td><?= htmlspecialchars($student['email']) ?></td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td><?= htmlspecialchars($student['phone'] ?? 'Chưa cập nhật') ?></td>
                            </tr>
                            <tr>
                                <th>Khoa:</th>
                                <td><?= htmlspecialchars($student['faculty_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Lớp:</th>
                                <td><?= htmlspecialchars($student['base_class_name'] ?? 'Chưa phân lớp') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
