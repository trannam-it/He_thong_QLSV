<?php
/**
 * View: Giảng viên đăng ký dạy lớp học phần
 * Variables: $classes, $success, $error, $lecturer
 */
$pageTitle   = 'Đăng ký Lớp Học phần';
$currentPage = 'lecturer_manage_classes';
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-clipboard-check me-2"></i>Đăng ký Dạy Lớp Học phần</h1>
    <div class="page-breadcrumb">
        <a href="<?= BASE_URL ?>/lecturer/">Trang chủ</a> / Đăng ký Dạy Lớp
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

<!-- Enrollment Period Alert -->
<div class="row mb-4">
    <div class="col-12">
        <div id="enrollmentPeriodAlert" class="alert alert-info alert-dismissible" style="display:none">
            <i class="bi bi-calendar-check me-2"></i>
            <strong id="periodStatus">Đang tải...</strong>
            <span id="periodDates" class="ms-2 text-muted"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
</div>

<!-- Info Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-left-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Giảng viên</p>
                        <h5 class="mb-0"><?= htmlspecialchars($lecturer['full_name'] ?? 'N/A') ?></h5>
                    </div>
                    <i class="bi bi-person text-primary ms-auto" style="font-size:2rem"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-left-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Khoa</p>
                        <h5 class="mb-0"><?= htmlspecialchars($lecturer['faculty_name'] ?? 'Chưa xác định') ?></h5>
                    </div>
                    <i class="bi bi-building text-success ms-auto" style="font-size:2rem"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Table -->
<div class="card">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="bi bi-list-check me-2"></i>
            Lớp Học phần Đang Mở Đăng ký
        </h5>
        <div>
            <input type="text" id="searchClasses" class="form-control form-control-sm" 
                   placeholder="Tìm kiếm..." style="width: 200px;">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="classesTable">
            <thead class="table-light">
                <tr>
                    <th>Mã Lớp</th>
                    <th>Môn Học</th>
                    <th>Giảng viên Hiện tại</th>
                    <th class="text-center">Tín chỉ</th>
                    <th class="text-center">Kỳ</th>
                    <th class="text-center">Năm</th>
                    <th class="text-center">Đã ĐK</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size:2rem"></i>
                            <p class="mt-2">Không có lớp nào để đăng ký hiện tại.</p>
                            <small class="text-muted">Hãy chờ kỳ đăng ký mở hoặc có lớp mới tương ứng với các môn bạn dạy.</small>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($classes as $c): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($c['class_code']) ?></code></td>
                        <td>
                            <strong><?= htmlspecialchars($c['subject_name']) ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($c['subject_code']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($c['lecturer_name'] ?? '—') ?></td>
                        <td class="text-center"><span class="badge bg-info"><?= (int)$c['credit_hours'] ?></span></td>
                        <td class="text-center"><?= htmlspecialchars(formatSemester($c['semester'])) ?></td>
                        <td class="text-center"><?= (int)$c['year'] ?></td>
                        <td class="text-center"><span class="badge bg-secondary"><?= (int)$c['enrolled_count'] ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" onclick="registerClass(<?= (int)$c['class_id'] ?>, '<?= htmlspecialchars(addslashes($c['subject_name'])) ?>')">
                                <i class="bi bi-check-circle me-1"></i>Đăng ký
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Load enrollment periods on page load
document.addEventListener('DOMContentLoaded', async function() {
    await loadCurrentEnrollmentPeriod();
});

async function loadCurrentEnrollmentPeriod() {
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
            datesText  = `Mở: ${openDate.toLocaleString('vi-VN')}`;
            // override table message
            const tbl = document.querySelector('#classesTable tbody');
            if (tbl) {
                tbl.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Chưa đến thời gian đăng ký dạy học phần.</td></tr>';
            }
        } else if (now >= openDate && now <= closeDate) {
            alertClass = 'alert-success';
            statusText = `✅ Kỳ ${period.semester} ${period.year} đang mở đăng ký`;
            datesText  = `Đóng: ${closeDate.toLocaleString('vi-VN')}`;
        } else {
            alertClass = 'alert-danger';
            statusText = `❌ Kỳ ${period.semester} ${period.year} đã đóng đăng ký`;
            datesText  = `Đóng từ: ${closeDate.toLocaleString('vi-VN')}`;
        }
        alert.className = `alert ${alertClass} alert-dismissible`;
        statusEl.textContent = statusText;
        datesEl.textContent = datesText;
        alert.style.display = 'block';
    } catch (e) {
        console.error('Error:', e);
    }
}

// Search functionality
document.getElementById('searchClasses')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#classesTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// Register for class
async function registerClass(classId, className) {
    if (!confirm(`Xác nhận đăng ký dạy: ${className}?`)) return;
    
    try {
        const fd = new FormData();
        fd.append('class_id', classId);
        
        const res = await fetch('/web_QLSV/lecturer/api/router.php?resource=class_registration&action=register', {
            method: 'POST',
            body: fd
        });
        
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();
        
        if (data.success) {
            showToast('success', 'Đăng ký thành công! Trang sẽ tải lại...');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message || 'Lỗi không xác định');
        }
    } catch (e) {
        showToast('error', 'Lỗi: ' + e.message);
    }
}

function showToast(type, msg) {
    const colors = { success: '#198754', error: '#dc3545' };
    const el = document.createElement('div');
    el.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; z-index: 999999;
        background: ${colors[type] || '#333'}; color: white;
        padding: 16px 24px; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        font-weight: 500;
    `;
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// Helper function (duplicate from student view - should be shared)
function formatSemester(semester) {
    const map = { 'Spring': 'Học kỳ I', 'Summer': 'Học kỳ Hè', 'Fall': 'Học kỳ II' };
    return map[semester] || semester;
}
</script>

<style>
.border-left-primary {
    border-left: 4px solid #0d6efd;
}
.border-left-success {
    border-left: 4px solid #198754;
}
</style>
