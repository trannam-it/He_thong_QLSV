<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';

authCheck(['super_admin', 'content_admin', 'teacher']);

$pageTitle = "Quản lý điểm số";
include_once __DIR__ . '/../layout/header.php';
?>

<!-- ===== FILTER BAR ===== -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1 fw-semibold">Lọc theo lớp học phần</label>
                <select id="filterClass" class="form-select form-select-sm">
                    <option value="">-- Tất cả lớp --</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-secondary" onclick="resetFilter()">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== TABLE CARD ===== -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-journal-check me-1"></i>Danh sách điểm sinh viên</strong>
        <div>
            <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-spreadsheet"></i> Import CSV
            </button>
            <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                <i class="bi bi-plus-circle"></i> Thêm điểm
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <table id="gradeTable" class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Lớp HP</th>
                    <th>Môn học</th>
                    <th class="text-center">Điểm</th>
                    <th class="text-center">Xếp loại</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-center" width="130">Thao tác</th>
                </tr>
            </thead>
            <tbody id="gradeTbody">
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <span class="spinner-border spinner-border-sm me-1"></span> Đang tải...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== MODAL THÊM / SỬA ĐIỂM ===== -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="gradeForm" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalLabel">Nhập điểm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="grade_id">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Lớp học phần <span class="text-danger">*</span></label>
                    <select id="class_id" class="form-select" required>
                        <option value="">-- Đang tải... --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Sinh viên <span class="text-danger">*</span></label>
                    <select id="enrollment_id" class="form-select" required>
                        <option value="">-- Chọn lớp trước --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Điểm số <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" min="0" max="100" id="score"
                           class="form-control" placeholder="VD: 8.5 hoặc 85" required>
                    <div class="form-text">Nhập theo thang 0–10 hoặc 0–100</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary" id="saveGradeBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="saveSpinner"></span>
                    Lưu điểm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== MODAL IMPORT CSV ===== -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" id="importForm">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import điểm từ CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info py-2">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        File CSV gồm 2 cột theo thứ tự: <code>enrollment_id</code>, <code>score</code><br>
                        Dòng đầu tiên là tiêu đề (sẽ bị bỏ qua).
                    </small>
                </div>
                <input type="file" id="csvFile" class="form-control" accept=".csv" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload me-1"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>

<script>
/* =====================================================
   GRADES MANAGEMENT – admin/views/grades/index.php
===================================================== */
const gradeModalEl  = document.getElementById('gradeModal');
const importModalEl = document.getElementById('importModal');
const gradeModal    = new bootstrap.Modal(gradeModalEl);

/* ---- HTML escape helper ---- */
function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ---- Grade-letter badge colour ---- */
function gradeColor(letter) {
    const map = { A: 'success', 'B+': 'info', B: 'info', 'C+': 'warning', C: 'warning', D: 'secondary', F: 'danger' };
    return map[letter] || 'secondary';
}

/* ==============================================
   LOAD DANH SÁCH LỚP (FILTER + MODAL DROPDOWN)
============================================== */
async function loadClassOptions(targetSelector, selectedId = null) {
    const res = await apiCall('classes', 'listForGrades');
    if (!res.success) {
        showToast(res.message || 'Không tải được danh sách lớp', 'error');
        return;
    }

    let html = '<option value="">-- Chọn lớp --</option>';
    (res.data || []).forEach(c => {
        const semester = c.semester ? ` – ${escapeHtml(c.semester)} ${escapeHtml(c.year)}` : '';
        const label    = escapeHtml(c.subject_name) + ' (' + escapeHtml(c.class_name) + semester + ')';
        const selected = String(c.class_id) === String(selectedId) ? ' selected' : '';
        html += `<option value="${c.class_id}"${selected}>${label}</option>`;
    });

    document.querySelectorAll(targetSelector).forEach(el => {
        el.innerHTML = html;
        if (selectedId) el.value = selectedId;
    });
}

/* ===========================================
   LOAD SINH VIÊN THEO LỚP
=========================================== */
async function loadStudentsByClass(classId, selectedEnrollmentId = null) {
    const el = document.getElementById('enrollment_id');
    el.innerHTML = '<option value="">Đang tải...</option>';

    if (!classId) {
        el.innerHTML = '<option value="">-- Chọn lớp trước --</option>';
        return;
    }

    const res = await apiCall('grades', 'studentsByClass', 'GET', { class_id: classId });
    if (!res.success) {
        showToast(res.message || 'Không tải được sinh viên', 'error');
        el.innerHTML = '<option value="">-- Lỗi tải sinh viên --</option>';
        return;
    }

    if (!res.data || res.data.length === 0) {
        el.innerHTML = '<option value="">-- Không có sinh viên --</option>';
        return;
    }

    let html = '<option value="">-- Chọn sinh viên --</option>';
    res.data.forEach(s => {
        const label = escapeHtml(s.student_code) + ' – ' + escapeHtml(s.full_name);
        html += `<option value="${s.enrollment_id}">${label}</option>`;
    });
    el.innerHTML = html;

    if (selectedEnrollmentId) el.value = selectedEnrollmentId;
}

/* ===========================================
   LOAD DANH SÁCH ĐIỂM (TABLE)
=========================================== */
let currentFilterClassId = '';

async function loadGrades(classId = '') {
    const tbody = document.getElementById('gradeTbody');
    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">
        <span class="spinner-border spinner-border-sm me-1"></span> Đang tải...</td></tr>`;

    const params = classId ? { class_id: classId } : null;
    const res = await apiCall('grades', 'index', 'GET', params);

    if (!res.success) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${escapeHtml(res.message)}</td></tr>`;
        showToast(res.message, 'error');
        return;
    }

    const rows = res.data || [];
    if (rows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Chưa có điểm nào</td></tr>';
        return;
    }

    let html = '';
    rows.forEach(g => {
        const isLocked  = g.is_locked == 1;
        const lockBadge = isLocked
            ? '<span class="badge bg-danger"><i class="bi bi-lock-fill"></i> Đã khóa</span>'
            : '<span class="badge bg-success"><i class="bi bi-unlock-fill"></i> Mở</span>';

        const fullName    = escapeHtml(((g.last_name || '') + ' ' + (g.first_name || '')).trim());
        const letterColor = gradeColor(g.grade_letter);

        let actions = `
            <button class="btn btn-sm btn-warning me-1" title="Sửa"
                onclick="editGrade(${g.grade_id}, ${g.enrollment_id}, ${g.score ?? 0}, ${g.class_id ?? 0})"
                ${isLocked ? 'disabled' : ''}>
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger me-1" title="Xóa"
                onclick="confirmDelete('grades', ${g.grade_id}, '${fullName}')"
                ${isLocked ? 'disabled' : ''}>
                <i class="bi bi-trash"></i>
            </button>`;

        if (!isLocked) {
            actions += `
            <button class="btn btn-sm btn-outline-secondary" title="Khóa điểm"
                onclick="lockGrade(${g.grade_id})">
                <i class="bi bi-lock"></i>
            </button>`;
        }

        html += `
        <tr>
            <td>${escapeHtml(g.student_code)}</td>
            <td>${fullName}</td>
            <td><code>${escapeHtml(g.class_code)}</code></td>
            <td>${escapeHtml(g.subject_name)}</td>
            <td class="text-center fw-semibold">${g.score !== null ? escapeHtml(g.score) : '—'}</td>
            <td class="text-center"><span class="badge bg-${letterColor}">${escapeHtml(g.grade_letter) || '—'}</span></td>
            <td class="text-center">${lockBadge}</td>
            <td class="text-center text-nowrap">${actions}</td>
        </tr>`;
    });

    tbody.innerHTML = html;
}

/* ===========================================
   FILTER BY CLASS
=========================================== */
function resetFilter() {
    document.getElementById('filterClass').value = '';
    currentFilterClassId = '';
    loadGrades();
}

document.getElementById('filterClass').addEventListener('change', function () {
    currentFilterClassId = this.value;
    loadGrades(this.value);
});

/* ===========================================
   ADD / EDIT MODAL
=========================================== */
function openAddModal() {
    document.getElementById('gradeForm').reset();
    document.getElementById('grade_id').value = '';
    document.getElementById('gradeModalLabel').textContent = 'Thêm điểm';
    document.getElementById('enrollment_id').innerHTML = '<option value="">-- Chọn lớp trước --</option>';
    loadClassOptions('#class_id').then(() => {
        document.getElementById('class_id').value = '';
    });
    gradeModal.show();
}

async function editGrade(id, enrollmentId, score, classId) {
    document.getElementById('grade_id').value = id;
    document.getElementById('score').value = score;
    document.getElementById('gradeModalLabel').textContent = 'Sửa điểm';

    await loadClassOptions('#class_id', classId);
    if (classId) {
        await loadStudentsByClass(classId, enrollmentId);
    }
    gradeModal.show();
}

/* class_id change → load students */
document.getElementById('class_id').addEventListener('change', function () {
    loadStudentsByClass(this.value);
});

/* ===========================================
   SAVE (CREATE / UPDATE)
=========================================== */
document.getElementById('gradeForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const id   = document.getElementById('grade_id').value;
    const data = {
        id:            id,
        enrollment_id: document.getElementById('enrollment_id').value,
        score:         document.getElementById('score').value
    };

    if (!data.enrollment_id) {
        showToast('Vui lòng chọn sinh viên', 'error');
        return;
    }

    const spinner = document.getElementById('saveSpinner');
    const btn     = document.getElementById('saveGradeBtn');
    spinner.classList.remove('d-none');
    btn.disabled = true;

    const action = id ? 'update' : 'store';
    const res = await apiCall('grades', action, 'POST', data);

    spinner.classList.add('d-none');
    btn.disabled = false;

    if (res.success) {
        showToast(id ? 'Cập nhật điểm thành công' : 'Thêm điểm thành công');
        gradeModal.hide();
        loadGrades(currentFilterClassId);
    } else {
        showToast(res.message || 'Lỗi lưu điểm', 'error');
    }
});

/* ===========================================
   LOCK GRADE
=========================================== */
async function lockGrade(id) {
    if (!confirm('Bạn chắc chắn muốn khóa điểm này? Sau khi khóa không thể chỉnh sửa.')) return;

    const res = await apiCall('grades', 'lock', 'POST', { id: id });
    if (res.success) {
        showToast('Đã khóa điểm thành công');
        loadGrades(currentFilterClassId);
    } else {
        showToast(res.message || 'Không thể khóa điểm', 'error');
    }
}

/* ===========================================
   IMPORT CSV
=========================================== */
document.getElementById('importForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const file = document.getElementById('csvFile').files[0];
    if (!file) {
        showToast('Vui lòng chọn file CSV', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    try {
        const res = await fetch('/web_QLSV/admin/api/router.php?module=grades&action=import', {
            method: 'POST',
            body: formData
        }).then(r => r.json());

        if (res.success) {
            showToast(`Import thành công: ${res.data?.inserted ?? 0} bản ghi`);
            bootstrap.Modal.getInstance(importModalEl)?.hide();
            loadGrades(currentFilterClassId);
        } else {
            showToast(res.message || 'Import thất bại', 'error');
        }
    } catch (err) {
        console.error(err);
        showToast('Lỗi kết nối khi import', 'error');
    }
});

/* ===========================================
   INIT
=========================================== */
$(document).ready(function () {
    loadClassOptions('#filterClass');
    loadGrades();
});
</script>
