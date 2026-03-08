<?php
/**
 * View: Đăng ký học phần
 * Biến: $student, $myEnrollments, $available, $cntRegistered, $cntCompleted,
 *       $totalCredits, $success, $error
 */
$pageTitle   = 'Đăng ký học phần';
$currentPage = 'student_enrollment';

// Helper: format schedule_raw => readable HTML
function formatScheduleRaw(?string $raw): string {
    if (!$raw) return '<span class="text-muted">—</span>';
    $dayNames = [2=>'T.Hai',3=>'T.Ba',4=>'T.Tư',5=>'T.Năm',6=>'T.Sáu',7=>'T.Bảy'];
    $lines = [];
    foreach (explode(';', $raw) as $seg) {
        $parts = explode('|', $seg);
        if (count($parts) < 3) continue;
        [$day, $start, $end, $room] = array_pad($parts, 4, '');
        $dayLabel  = $dayNames[(int)$day] ?? "Ngày $day";
        $roomLabel = $room ? " <small class='text-muted'>($room)</small>" : '';
        $lines[] = "<span class='badge bg-light text-dark border me-1 mb-1'>{$dayLabel} tiết {$start}–{$end}{$roomLabel}</span>";
    }
    return implode('', $lines);
}
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-journal-plus me-2"></i>Đăng ký học phần</h1>
    <div class="page-breadcrumb">
        <a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Đăng ký học phần
    </div>
</div>

<!-- Alerts -->
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
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
    <div class="col-12 mb-3">
        <div id="enrollmentPeriodAlert" class="alert alert-info alert-dismissible" style="display:none">
            <i class="bi bi-calendar-check me-2"></i>
            <strong id="periodStatus">Đang tải...</strong>
            <span id="periodDates" class="ms-2 text-muted"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-gradient-primary text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Đang học</div>
                    <div class="stat-card-value"><?= $cntRegistered ?></div></div>
                    <i class="bi bi-journal-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-gradient-success text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Hoàn thành</div>
                    <div class="stat-card-value"><?= $cntCompleted ?></div></div>
                    <i class="bi bi-patch-check stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-gradient-info text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Tín chỉ tích lũy</div>
                    <div class="stat-card-value"><?= $totalCredits ?></div></div>
                    <i class="bi bi-bookmark-star stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-gradient-warning text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-card-label">Có thể đăng ký</div>
                    <div class="stat-card-value"><?= count($available) ?></div></div>
                    <i class="bi bi-plus-circle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-0" id="enrollTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tabAvailable">
            <i class="bi bi-search me-1"></i>Học phần mở đăng ký
            <span class="badge bg-primary ms-1"><?= count($available) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tabMy">
            <i class="bi bi-list-check me-1"></i>Học phần của tôi
            <span class="badge bg-secondary ms-1"><?= count($myEnrollments) ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">

    <!-- Tab 1: Lớp mở đăng ký -->
    <div class="tab-pane fade show active" id="tabAvailable">
        <div class="content-card" style="border-top-left-radius:0;border-top-right-radius:0">
            <div class="content-card-header">
                <h5 class="content-card-title">Học phần có thể đăng ký</h5>
                <div class="d-flex gap-2">
                    <input type="text" id="searchAvail" class="form-control form-control-sm"
                           placeholder="Tìm kiếm môn học, mã lớp..." style="width:240px">
                </div>
            </div>
            <div class="content-card-body p-0">
                <?php if (empty($available)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-journal-x" style="font-size:3rem"></i>
                    <p class="mt-2">Không có học phần nào để đăng ký.</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tblAvailable">
                        <thead class="table-light">
                            <tr>
                                <th>Mã môn</th><th>Tên môn học</th><th>Mã lớp</th>
                                <th class="text-center">Tín chỉ</th><th>Giảng viên</th>
                                <th>Lịch học</th><th class="text-center">Học kỳ</th>
                                <th class="text-center">Năm</th><th class="text-center">Đã ĐK</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($available as $c): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($c['subject_code']) ?></code></td>
                            <td><?= htmlspecialchars($c['subject_name']) ?></td>
                            <td><code><?= htmlspecialchars($c['class_code']) ?></code></td>
                            <td class="text-center"><span class="badge bg-info"><?= (int)$c['credit_hours'] ?> TC</span></td>
                            <td><?= htmlspecialchars($c['lecturer_name'] ?? '—') ?></td>
                            <td style="min-width:160px"><?= formatScheduleRaw($c['schedule_raw'] ?? null) ?></td>
                            <td class="text-center"><?= htmlspecialchars($c['semester_name'] ?? '—') ?></td>
                            <td class="text-center">—</td>
                            <td class="text-center"><span class="badge bg-secondary"><?= (int)$c['enrolled_count'] ?> SV</span></td>
                            <td class="text-center">
                                <form method="POST" style="display:inline"
                                      onsubmit="return confirm('Xác nhận đăng ký: <?= htmlspecialchars(addslashes($c['subject_name'])) ?>?')">
                                    <input type="hidden" name="action"   value="register">
                                    <input type="hidden" name="class_id" value="<?= $c['class_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle me-1"></i>Đăng ký
                                    </button>
                                </form>
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

    <!-- Tab 2: Học phần của tôi -->
    <div class="tab-pane fade" id="tabMy">
        <div class="content-card" style="border-top-left-radius:0">
            <div class="content-card-header">
                <h5 class="content-card-title">Học phần đã đăng ký</h5>
                <div class="d-flex gap-2">
                    <select id="filterStatus" class="form-select form-select-sm" style="width:180px">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="Enrolled">Đang học</option>
                        <option value="Completed">Hoàn thành</option>
                        <option value="Withdrawn">Đã hủy</option>
                    </select>
                </div>
            </div>
            <div class="content-card-body p-0">
                <?php if (empty($myEnrollments)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-clipboard-x" style="font-size:3rem"></i>
                    <p class="mt-2">Bạn chưa đăng ký học phần nào.</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tblMy">
                        <thead class="table-light">
                            <tr>
                                <th>Mã môn</th><th>Tên môn học</th><th>Mã lớp</th>
                                <th class="text-center">Tín chỉ</th><th>Giảng viên</th>
                                <th class="text-center">Học kỳ</th><th class="text-center">Điểm</th>
                                <th class="text-center">Trạng thái</th><th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($myEnrollments as $e):
                            $badgeClass  = getStatusBadgeClass($e['status']);
                            $statusLabel = formatEnrollmentStatus($e['status']);
                        ?>

                        <tr data-status="<?= $e['status'] ?>">
                            <td><code><?= htmlspecialchars($e['subject_code']) ?></code></td>
                            <td><?= htmlspecialchars($e['subject_name']) ?></td>
                            <td><code><?= htmlspecialchars($e['class_code']) ?></code></td>
                            <td class="text-center"><span class="badge bg-info"><?= (int)$e['credit_hours'] ?> TC</span></td>
                            <td><?= htmlspecialchars($e['lecturer_name'] ?? '—') ?></td>
                            <td class="text-center"><?= htmlspecialchars(formatSemester($e['semester'])) ?> <?= (int)$e['year'] ?></td>
                            <td class="text-center">
                                <?php if ($e['score'] !== null): ?>
                                    <strong><?= number_format($e['score'],1) ?></strong>
                                    <span class="badge bg-primary ms-1"><?= htmlspecialchars($e['grade_letter'] ?? '') ?></span>
                                <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                            </td>
                            <td class="text-center"><span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span></td>
                            <td class="text-center">
                                <?php if ($e['status'] === 'Enrolled'): ?>
                                <form method="POST" style="display:inline"
                                      onsubmit="return confirm('Bạn chắc chắn muốn hủy đăng ký: <?= htmlspecialchars(addslashes($e['subject_name'])) ?>?')">
                                    <input type="hidden" name="action"        value="cancel">
                                    <input type="hidden" name="enrollment_id" value="<?= $e['enrollment_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle me-1"></i>Hủy
                                    </button>
                                </form>
                                <?php else: ?><span class="text-muted small">—</span><?php endif; ?>
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

</div><!-- /tab-content -->

<script>
// Load enrollment period status on page load
document.addEventListener('DOMContentLoaded', async function() {
    await loadEnrollmentPeriodStatus();
});

async function loadEnrollmentPeriodStatus() {
    try {
        const res = await fetch('/web_QLSV/student/api/router.php?resource=enrollment&action=current_period');
        if (!res.ok) return;
        
        const data = await res.json();
        if (!data.success || !data.data) return;
        
        const period = data.data;
        const alert = document.getElementById('enrollmentPeriodAlert');
        const statusEl = document.getElementById('periodStatus');
        const datesEl = document.getElementById('periodDates');
        
        const openDate = new Date(period.enrollment_open);
        const closeDate = new Date(period.enrollment_close);
        const now = new Date();
        
        let alertClass = 'alert-info';
        let statusText = '';
        let datesText = '';
        
        if (now < openDate) {
            alertClass = 'alert-warning';
            statusText = `📅 Kỳ ${period.semester} ${period.year} sắp mở đăng ký vào ${openDate.toLocaleString('vi-VN')}`;
            datesText = `Mở: ${openDate.toLocaleString('vi-VN')}`;
            // override available table message
            const tbl = document.querySelector('#tblAvailable tbody');
            if (tbl) {
                tbl.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Chưa đến thời gian đăng ký học phần.</td></tr>';
            }
        } else if (now >= openDate && now <= closeDate) {
            alertClass = 'alert-success';
            statusText = `✅ Kỳ ${period.semester} ${period.year} đang mở đăng ký`;
            datesText = `Đóng: ${closeDate.toLocaleString('vi-VN')}`;
        } else {
            alertClass = 'alert-danger';
            statusText = `❌ Kỳ ${period.semester} ${period.year} đã đóng đăng ký`;
            datesText = `Đóng từ: ${closeDate.toLocaleString('vi-VN')}`;
        }
        
        alert.className = `alert ${alertClass} alert-dismissible`;
        statusEl.textContent = statusText;
        datesEl.textContent = datesText;
        alert.style.display = 'block';
    } catch (e) {
        console.error('Error loading enrollment period:', e);
    }
}

document.getElementById('searchAvail')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tblAvailable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
document.getElementById('filterStatus')?.addEventListener('change', function() {
    const val = this.value;
    document.querySelectorAll('#tblMy tbody tr').forEach(row => {
        row.style.display = (!val || row.dataset.status === val) ? '' : 'none';
    });
});
</script>
<!-- formatEnrollmentStatus -->
