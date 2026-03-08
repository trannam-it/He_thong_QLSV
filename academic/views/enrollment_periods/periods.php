<?php
/**
 * View: Quản lý kỳ đăng ký học phần
 * @var array $periods
 * @var int $academicRoleId
 * @var int $enrollPermId
 */


// compute role/permission IDs for quick grants
// $academicRoleId = 0;
// $enrollPermId  = 0;
// $stmt = $conn->prepare("SELECT id FROM roles WHERE code = 'academic_admin' LIMIT 1");

// if ($stmt) { $stmt->execute(); $r = $stmt->get_result()->fetch_assoc(); $academicRoleId = (int)($r['id'] ?? 0); }
// // $stmt = $conn->prepare("SELECT id FROM permissions WHERE code = 'enrollment.manage_period' LIMIT 1");
// $stmt = $conn->prepare("SELECT id FROM permissions WHERE code = 'enrollment.manage' LIMIT 1");
// if ($stmt) { $stmt->execute(); $r = $stmt->get_result()->fetch_assoc(); $enrollPermId = (int)($r['id'] ?? 0); }

// include __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar-check text-primary me-2"></i>
                Quản lý Đăng ký Học phần
            </h2>
            <p class="text-muted mb-0">Thiết lập thời gian mở/đóng đăng ký các kỳ học</p>

            <?php if (isset($auth) && $auth->hasPermission('roles.assign_perm')): ?>
<div class="alert alert-warning mt-2">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    Nếu vai trò <strong>Quản lý Đào tạo</strong> chưa có quyền 
    <code>enrollment.manage</code>,
    bạn có thể cấp nhanh bằng nút bên dưới hoặc qua
    <a href="<?= BASE_URL ?>/admin/permission_matrix.php" class="alert-link">
        ma trận phân quyền
    </a>.
</div>

<button id="grantEnrollPermBtn" class="btn btn-sm btn-outline-secondary mb-3">
    <i class="bi bi-shield-lock me-1"></i>
    Gán quyền enrollment.manage cho academic_admin
</button>

<script>
document.getElementById('grantEnrollPermBtn')?.addEventListener('click', async function() {
    if (!confirm('Cấp quyền enrollment.manage cho role Quản lý Đào tạo?')) return;

    try {
        const fd = new FormData();
        fd.append('role_id', <?= (int)$academicRoleId ?>);
        fd.append('permission_ids[]', <?= (int)$enrollPermId ?>);

        const res = await fetch('<?= BASE_URL ?>/admin/api/router.php?resource=roles&action=assign_permissions', {
            method: 'POST',
            body: fd
        });

        const data = await res.json();
        alert(data.message);
        if (data.success) location.reload();

    } catch (e) {
        alert('Lỗi: ' + e.message);
    }
});
</script>
<?php endif; ?>

           
          


        </div>
    </div>

    <!-- Form Thêm/Sửa Kỳ Đăng ký -->
    <div class="card mb-4">
        <div class="card-header bg-light d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="bi bi-plus-circle me-2"></i>
                Thêm / Cập nhật Kỳ Đăng ký
            </h5>
        </div>
        <div class="card-body">
            <form id="enrollmentForm">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Học kỳ</label>
                        <select class="form-select" name="semester" required>
                            <option value="">-- Chọn --</option>
                            <option value="Spring">Spring (Xuân)</option>
                            <option value="Summer">Summer (Hè)</option>
                            <option value="Fall">Fall (Thu)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Năm học</label>
                        <input type="number" class="form-control" name="year" value="<?= (int)date('Y') ?>" min="2020" max="2100" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Mở đăng ký</label>
                        <input type="datetime-local" class="form-control" name="enrollment_open" required />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Đóng đăng ký</label>
                        <input type="datetime-local" class="form-control" name="enrollment_close" required />
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="Thêm ghi chú nếu cần..."></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Lưu Kỳ Đăng ký
                    </button>
                    <button type="reset" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách kỳ đăng ký -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-list-check me-2"></i>
                Danh sách Kỳ Đăng ký
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%">Học kỳ</th>
                        <th style="width: 10%">Năm</th>
                        <th style="width: 20%">Mở đăng ký</th>
                        <th style="width: 20%">Đóng đăng ký</th>
                        <th style="width: 15%">Trạng thái</th>
                        <th style="width: 25%">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="periodsTable">
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Đang tải dữ liệu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const API_BASE = '/web_QLSV/academic/api/router.php';

// Load periods on page load
document.addEventListener('DOMContentLoaded', loadPeriods);

// Handle form submit
document.getElementById('enrollmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    savePeriod();
});

async function loadPeriods() {
    try {
        const res = await fetch(`${API_BASE}?resource=enrollment_periods&action=list`);
        if (!res.ok) throw new Error('Network error: ' + res.status);
        
        const data = await res.json();
        if (!data.success) {
            showToast('error', data.message || 'Lỗi tải dữ liệu');
            return;
        }

        const tbody = document.getElementById('periodsTable');
        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Chưa có kỳ đăng ký nào</td></tr>';
            return;
        }

        tbody.innerHTML = data.data.map(p => `
            <tr>
                <td><strong>${p.semester}</strong></td>
                <td>${p.year}</td>
                <td><small class="text-muted">${formatDate(p.enrollment_open)}</small></td>
                <td><small class="text-muted">${formatDate(p.enrollment_close)}</small></td>
                <td>
                    ${p.is_active ? 
                        '<span class="badge bg-success">Đang Active</span>' :
                        '<span class="badge bg-secondary">Không Active</span>'
                    }
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editPeriod(${p.period_id})">
                        <i class="bi bi-pencil me-1"></i> Sửa
                    </button>
                    <button class="btn btn-sm ${p.is_active ? 'btn-outline-danger' : 'btn-outline-success'}" 
                            onclick="togglePeriod(${p.period_id}, ${p.is_active ? 0 : 1})">
                        <i class="bi ${p.is_active ? 'bi-toggle-on' : 'bi-toggle-off'} me-1"></i>
                        ${p.is_active ? 'Vô hiệu hóa' : 'Kích hoạt'}
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        console.error(e);
        showToast('error', 'Lỗi: ' + e.message);
    }
}

async function savePeriod() {
    const form = document.getElementById('enrollmentForm');
    const fd = new FormData(form);

    try {
        const res = await fetch(`${API_BASE}?resource=enrollment_periods&action=store`, {
            method: 'POST',
            body: fd
        });

        if (!res.ok) throw new Error('Network error: ' + res.status);
        const data = await res.json();

        if (data.success) {
            showToast('success', data.message);
            form.reset();
            loadPeriods();
        } else {
            showToast('error', data.message || 'Lỗi không xác định');
        }
    } catch (e) {
        showToast('error', 'Lỗi: ' + e.message);
    }
}

async function togglePeriod(periodId, state) {
    const fd = new FormData();
    fd.append('period_id', periodId);
    fd.append('is_active', state);

    try {
        const res = await fetch(`${API_BASE}?resource=enrollment_periods&action=toggle_active`, {
            method: 'POST',
            body: fd
        });

        if (!res.ok) throw new Error('Network error: ' + res.status);
        const data = await res.json();

        if (data.success) {
            showToast('success', data.message);
            loadPeriods();
        } else {
            showToast('error', data.message);
        }
    } catch (e) {
        showToast('error', 'Lỗi: ' + e.message);
    }
}

function editPeriod(periodId) {
    // Todo: Implement edit modal/form
    alert('Chức năng chỉnh sửa sắp được thêm. Tạm thời vui lòng cập nhật trực tiếp trong cơ sở dữ liệu.');
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const dt = new Date(dateStr);
    return dt.toLocaleString('vi-VN');
}

function showToast(type, msg) {
    const colors = { success: '#198754', error: '#dc3545', warning: '#FFC107' };
    const el = document.createElement('div');
    el.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; z-index: 999999;
        background: ${colors[type] || '#333'}; color: white;
        padding: 16px 24px; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        font-weight: 500;
        animation: toastSlideIn 0.3s ease-out;
    `;
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 300);
    }, 4500);
}
</script>

<style>
@keyframes toastSlideIn {
    from { transform: translateX(500px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>

