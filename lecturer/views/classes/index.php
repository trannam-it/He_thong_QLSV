<?php
/**
 * View: Lớp đang dạy
 * Biến: $lecturer, $classes, $stats, $yearList,
 *       $search, $filterSem, $filterYear,
 *       $flashSuccess, $flashError
 */
$pageTitle = 'Lớp đang dạy';
?>

<div class="page-header">
    <h1 class="page-title">Lớp đang dạy</h1>
    <div class="page-breadcrumb">
        <a href="<?= lUrl() ?>">Trang chủ</a> / Lớp đang dạy
    </div>
</div>

<!-- Flash -->
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

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <?php foreach ([
        ['primary', 'book-half',    'Tổng lớp',       (int)($stats['total_cls'] ?? 0)],
        ['success', 'people',       'Tổng SV',         (int)($stats['total_sv']  ?? 0)],
        ['info',    'journal-text', 'Môn học',         (int)($stats['total_sub'] ?? 0)],
        ['warning', 'clock-history','Chờ nhập điểm',   (int)($stats['pending']   ?? 0)],
    ] as [$color, $icon, $label, $val]): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 p-3">
                    <i class="bi bi-<?= $icon ?> text-<?= $color ?> fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= $label ?></div>
                    <div class="fs-3 fw-bold"><?= $val ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filter bar -->
<form method="GET" action="<?= lUrl('classes') ?>" class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Mã lớp hoặc tên môn…"
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="semester_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả học kỳ --</option>

                    <?php foreach ($semesterList as $s): ?>
                    <option value="<?= $s['semester_id'] ?>"
                        <?= $filterSem == $s['semester_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['semester_name']) ?>
                    </option>
                    <?php endforeach; ?>

                </select>

            </div>
            <div class="col-md-2">
                <select name="year" class="form-select form-select-sm">
                    <option value="">-- Tất cả năm --</option>
                    <?php foreach ($yearList as $y): ?>
                    <option value="<?= $y['year'] ?>" <?= $filterYear == $y['year'] ? 'selected' : '' ?>>
                        <?= $y['year'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="page" value="classes">
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Lọc
                </button>
                <a href="<?= lUrl('classes') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
                <a href="<?= lUrl('register') ?>" class="btn btn-success btn-sm ms-auto">
                    <i class="bi bi-plus-circle me-1"></i>Đăng ký lớp mới
                </a>
            </div>
        </div>
    </div>
</form>

<!-- Classes table -->
<div class="card shadow-sm">
    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
        <span class="fw-semibold text-muted small">Hiển thị <?= count($classes) ?> lớp</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($classes)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:40px">#</th>
                        <th style="width:130px">Mã lớp</th>
                        <th>Môn học</th>
                        <th class="text-center" style="width:80px">Tín chỉ</th>
                        <th class="text-center" style="width:110px">Học kỳ</th>
                        <th class="text-center" style="width:70px">Năm</th>
                        <th class="text-center" style="width:80px">SV</th>
                        <th class="text-center" style="width:130px">Đã nhập điểm</th>
                        <th class="text-center" style="width:150px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($classes as $i => $c):
                    $pct      = $c['total_students'] > 0
                                ? round($c['graded_students'] / $c['total_students'] * 100)
                                : 0;
                    $barColor = $pct >= 100 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td class="text-center text-muted small"><?= $i + 1 ?></td>
                    <td>
                        <code class="text-primary fw-semibold"><?= htmlspecialchars($c['class_code']) ?></code>
                        <br><small class="text-muted"><?= htmlspecialchars($c['subject_code']) ?></small>
                    </td>
                    <td class="fw-semibold">
                        <?= htmlspecialchars($c['subject_name']) ?>
                        <?php if ($c['faculty_name']): ?>
                        <br><small class="text-muted fw-normal"><?= htmlspecialchars($c['faculty_name']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info rounded-pill"><?= $c['credit_hours'] ?> TC</span>
                    </td>
                    <td class="text-center">
                         <span class="badge bg-secondary"><?= htmlspecialchars($c['semester_name']) ?></span>
                    </td>
                    <td class="text-center text-muted"><?= $c['year'] ?></td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill"><?= $c['total_students'] ?></span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px">
                                <div class="progress-bar bg-<?= $barColor ?>" style="width:<?= $pct ?>%"></div>
                            </div>
                            <span class="text-muted small" style="width:36px;text-align:right"><?= $pct ?>%</span>
                        </div>
                        <div class="text-muted" style="font-size:.7rem">
                            <?= $c['graded_students'] ?>/<?= $c['total_students'] ?> SV
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="<?= lUrl('grades') ?>&class_id=<?= $c['class_id'] ?>"
                           class="btn btn-sm btn-outline-success" title="Nhập điểm">
                            <i class="bi bi-pencil-square me-1"></i>Điểm
                        </a>
                        <a href="<?= lUrl('attendance') ?>&class_id=<?= $c['class_id'] ?>"
                           class="btn btn-sm btn-outline-info ms-1" title="Điểm danh">
                            <i class="bi bi-person-check"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-40"></i>
            <p class="mb-0">Không tìm thấy lớp học nào</p>
            <a href="<?= lUrl('register') ?>" class="btn btn-primary btn-sm mt-3">
                <i class="bi bi-plus-circle me-1"></i>Đăng ký lớp dạy
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<select name="semester" class="form-select form-select-sm">

