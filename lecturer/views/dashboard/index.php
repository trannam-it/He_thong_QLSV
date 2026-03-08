<?php
/**
 * View: Dashboard Giảng viên
 * Biến nhận từ DashboardController:
 * $lecturer, $totalClasses, $totalStudents, $totalSubjects,
 * $totalClassesAll, $pendingGrades, $classes,
 * $flashSuccess, $flashError
 */

$pageTitle = 'Dashboard';

// Helper format semester dùng trong view
function vFormatSemester(string $sem): string {
    return LecturerModel::formatSemester($sem);
}
?>

<!-- Breadcrumb -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div class="page-breadcrumb">
        <a href="<?= lUrl() ?>">Trang chủ</a> / Dashboard
    </div>
</div>

<!-- Flash messages -->
<?php if ($flashSuccess): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flashSuccess) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($flashError) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ===== HERO BANNER ===== -->
<div class="mb-4 rounded-3 p-4 text-white"
     style="background:linear-gradient(135deg,#0f766e 0%,#0369a1 100%);position:relative;overflow:hidden;">
    <div style="position:absolute;right:-70px;top:-70px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.07);"></div>
    <div style="position:absolute;right:80px;bottom:-50px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.05);"></div>
    <div class="d-flex align-items-center gap-3 flex-wrap position-relative">
        <div style="width:80px;height:80px;border-radius:50%;border:4px solid rgba(255,255,255,.4);
                    background:rgba(255,255,255,.2);display:flex;align-items:center;
                    justify-content:center;font-size:2.2rem;color:#fff;flex-shrink:0;">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold"><?= htmlspecialchars($lecturer['full_name']) ?></h4>
                <span class="badge" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);font-size:.8rem;padding:.25rem .8rem;border-radius:999px;">
                    <?= htmlspecialchars($lecturer['degree']) ?>
                </span>
            </div>
            <div class="opacity-90 small mb-2">
                <i class="bi bi-building me-1"></i><?= htmlspecialchars($lecturer['faculty_name']) ?>
                &nbsp;·&nbsp;
                <i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($lecturer['lecturer_code']) ?>
            </div>
            <div class="d-flex gap-2 flex-wrap opacity-90 small">
                <?php if ($lecturer['email']): ?>
                <span><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($lecturer['email']) ?></span>
                <?php endif; ?>
                <?php if ($lecturer['phone']): ?>
                <span><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($lecturer['phone']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- Mini stats -->
        <div class="d-flex gap-2 flex-wrap">
            <?php foreach ([
                [$totalClasses,    'Lớp năm nay'],
                [$totalStudents,   'Sinh viên'],
                [$totalSubjects,   'Môn học'],
                [$totalClassesAll, 'Tổng lớp'],
            ] as [$val, $lbl]): ?>
            <div style="background:rgba(255,255,255,.15);border-radius:.6rem;padding:.5rem 1rem;text-align:center;min-width:90px;">
                <div style="font-size:1.5rem;font-weight:700;line-height:1;"><?= $val ?></div>
                <div style="font-size:.7rem;opacity:.85;margin-top:.15rem;"><?= $lbl ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ===== 4 STAT CARDS ===== -->
<div class="row g-3 mb-4">
    <?php foreach ([
        ['primary', 'book-half',    'Lớp đang dạy',  $totalClasses,    'Năm ' . date('Y')],
        ['success', 'people',       'Tổng sinh viên', $totalStudents,   'Đang đăng ký'],
        ['info',    'journal-text', 'Môn phụ trách',  $totalSubjects,   'Môn học độc lập'],
        ['warning', 'mortarboard',  'Học vị',         htmlspecialchars($lecturer['degree']), htmlspecialchars($lecturer['faculty_name'])],
    ] as [$color, $icon, $label, $val, $sub]): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 p-3">
                    <i class="bi bi-<?= $icon ?> text-<?= $color ?> fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= $label ?></div>
                    <div class="fs-3 fw-bold"><?= $val ?></div>
                    <div class="text-muted" style="font-size:.72rem"><?= $sub ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ===== CLASSES TABLE ===== -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-book me-2 text-primary"></i>Danh sách lớp giảng dạy
            <?php if ($pendingGrades > 0): ?>
            <span style="font-size:.7rem;background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:999px;padding:.1rem .55rem;vertical-align:middle;" class="ms-2">
                <i class="bi bi-clock me-1"></i><?= $pendingGrades ?> điểm chờ nhập
            </span>
            <?php endif; ?>
        </h6>
        <a href="<?= lUrl('profile') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-person me-1"></i>Hồ sơ
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($classes)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px" class="text-center">#</th>
                        <th>Mã lớp</th>
                        <th>Môn học</th>
                        <th class="text-center" style="width:80px">Tín chỉ</th>
                        <th class="text-center" style="width:100px">Học kỳ</th>
                        <th class="text-center" style="width:80px">Năm</th>
                        <th class="text-center" style="width:90px">SL SV</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $i => $c): ?>
                    <tr>
                        <td class="text-center text-muted small"><?= $i + 1 ?></td>
                        <td><code class="text-primary fw-semibold"><?= htmlspecialchars($c['class_code']) ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($c['subject_name']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-info rounded-pill"><?= $c['credit_hours'] ?> TC</span>
                        </td>
                        <td class="text-center">
                                <span class="badge bg-secondary"><?= htmlspecialchars($c['semester_name']) ?></span>
                        </td>
                        <td class="text-center text-muted"><?= htmlspecialchars($c['year'] ?? '-') ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary rounded-pill"><?= $c['student_count'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-40"></i>
            <p class="mb-0">Chưa có lớp học được phân công</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== BOTTOM ROW ===== -->
<div class="row g-4">
    <!-- Personal Info -->
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Thông tin cá nhân
                </h6>
                <a href="<?= lUrl('profile') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i>Chỉnh sửa
                </a>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <?php foreach ([
                            ['Mã giảng viên', '<code>' . htmlspecialchars($lecturer['lecturer_code']) . '</code>'],
                            ['Họ và tên',     '<span class="fw-semibold">' . htmlspecialchars($lecturer['full_name']) . '</span>'],
                            ['Học vị',        '<span class="badge bg-success">' . htmlspecialchars($lecturer['degree']) . '</span>'],
                            ['Khoa',          htmlspecialchars($lecturer['faculty_name'])],
                            ['Email',         $lecturer['email'] ? '<a href="mailto:' . htmlspecialchars($lecturer['email']) . '" class="text-decoration-none"><i class="bi bi-envelope me-1 text-muted"></i>' . htmlspecialchars($lecturer['email']) . '</a>' : '<span class="text-muted">—</span>'],
                            ['Điện thoại',    $lecturer['phone'] ? '<a href="tel:' . htmlspecialchars($lecturer['phone']) . '" class="text-decoration-none"><i class="bi bi-telephone me-1 text-muted"></i>' . htmlspecialchars($lecturer['phone']) . '</a>' : '<span class="text-muted">—</span>'],
                        ] as [$th, $td]): ?>
                        <tr>
                            <th style="width:160px;color:#6b7280;font-weight:600;"><?= $th ?></th>
                            <td><?= $td ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Links + Summary -->
    <div class="col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-lightning me-2 text-warning"></i>Truy cập nhanh
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="list-group list-group-flush">
                    <?php foreach ([
                        [lUrl('classes'),    'book-half',     'primary', 'Lớp đang dạy',    'Xem danh sách lớp'],
                        [lUrl('grades'),     'pencil-square', 'success', 'Nhập điểm',       'Nhập điểm sinh viên'],
                        [lUrl('attendance'), 'person-check',  'info',    'Điểm danh',       'Quản lý điểm danh'],
                        [lUrl('register'),   'journal-plus',  'warning', 'Đăng ký lớp dạy', 'Đăng ký môn học mới'],
                    ] as [$url, $icon, $color, $label, $sub]): ?>
                    <a href="<?= $url ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded border-0 px-3 py-2">
                        <span class="rounded-circle bg-<?= $color ?> bg-opacity-10 p-2">
                            <i class="bi bi-<?= $icon ?> text-<?= $color ?>"></i>
                        </span>
                        <div>
                            <div class="fw-semibold small"><?= $label ?></div>
                            <div class="text-muted" style="font-size:.72rem"><?= $sub ?></div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Teaching summary -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-bar-chart me-2 text-info"></i>Tóm tắt giảng dạy
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Tỷ lệ lớp / môn</span>
                    <span class="fw-semibold">
                        <?= $totalSubjects > 0 ? round($totalClassesAll / $totalSubjects, 1) : '—' ?>
                        <span class="text-muted fw-normal small">lớp/môn</span>
                    </span>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Lớp năm nay / Tổng lớp</span>
                        <span><?= $totalClasses ?> / <?= $totalClassesAll ?></span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar bg-primary"
                             style="width:<?= $totalClassesAll > 0 ? round($totalClasses / $totalClassesAll * 100) : 0 ?>%">
                        </div>
                    </div>
                </div>
                <?php if ($pendingGrades > 0): ?>
                <div class="alert alert-warning py-2 px-3 mb-0 small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Có <strong><?= $pendingGrades ?></strong> sinh viên chưa được nhập điểm.
                </div>
                <?php else: ?>
                <div class="alert alert-success py-2 px-3 mb-0 small">
                    <i class="bi bi-check-circle me-1"></i>
                    Tất cả sinh viên đã được nhập điểm.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


