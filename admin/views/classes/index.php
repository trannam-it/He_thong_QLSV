<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
authCheck(['super_admin', 'content_admin']);
require_once __DIR__ . '/../layout/header.php';
?>
<style>
/* ── Page layout ─────────────────────────────────────── */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px 28px 10px;
}
.page-header h2 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #343a40;
    margin: 0;
}
.page-header h2 i {
    color: var(--primary-color);
    margin-right: 8px;
}

/* ── Card / Table ────────────────────────────────────── */
.table-card {
    margin: 0 28px 28px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
}
.table-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
}
.table-card-header span {
    font-size: .85rem;
    color: #6c757d;
}
.table-card-body {
    padding: 0;
    overflow-x: auto;
}

#baseClassesTable {
    width: 100%;
    margin: 0;
    font-size: .9rem;
}
#baseClassesTable thead th {
    background: #f8f9fc;
    color: #495057;
    font-weight: 600;
    font-size: .8rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 12px 14px;
    border-bottom: 2px solid #e9ecef;
    white-space: nowrap;
}
#baseClassesTable tbody td {
    padding: 11px 14px;
    vertical-align: middle;
    border-bottom: 1px solid #f2f2f2;
    color: #444;
}
#baseClassesTable tbody tr:last-child td {
    border-bottom: none;
}
#baseClassesTable tbody tr:hover td {
    background: #f5f8ff;
}
.table-card-footer {
    padding: 12px 20px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}
.pagination { margin: 0; }

/* ── Badges ──────────────────────────────────────────── */
.badge-total   { background: #4e73df22; color: #4e73df; }
.badge-active  { background: #1cc88a22; color: #1cc88a; }
.badge-grad    { background: #36b9cc22; color: #36b9cc; }
.badge-stat {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 600;
}

/* ── Action buttons ──────────────────────────────────── */
.btn-action { gap: 4px; display: flex; flex-wrap: nowrap; }
.btn-action .btn { padding: 4px 9px; font-size: .78rem; border-radius: 6px; }

/* ── Alert area ──────────────────────────────────────── */
#alertArea { padding: 0 28px; }
#alertArea:empty { padding: 0; }
</style>

<!-- ── Page header ───────────────── -->
<div class="page-header">
    <h2><i class="bi bi-diagram-3"></i>Quản lý Lớp Cơ Sở</h2>
    <div class="d-flex gap-2">
        <a href="statistics.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-bar-chart-line me-1"></i>Thống kê
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i>Thêm Lớp
        </button>
    </div>
</div>

<div id="alertArea"></div>

<!-- ── Main table card ───────────── -->
<div class="table-card">
    <div class="table-card-header">
        <strong style="font-size:.95rem">Danh sách lớp cơ sở</strong>
        <span id="tableCount"></span>
    </div>
    <div class="table-card-body">
        <table id="baseClassesTable">
            <thead>
                <tr>
                    <th style="width:46px">#</th>
                    <th>Mã Lớp</th>
                    <th>Tên Lớp</th>
                    <th>Khoa</th>
                    <th>GVCN</th>
                    <th>Năm</th>
                    <th style="width:80px;text-align:center">Tổng SV</th>
                    <th style="width:90px;text-align:center">Đang học</th>
                    <th style="width:90px;text-align:center">Tốt nghiệp</th>
                    <th style="width:160px">Hành động</th>
                </tr>
            </thead>
            <tbody id="baseClassesBody">
            </tbody>
        </table>
    </div>
    <div class="table-card-footer">
        <nav><ul class="pagination pagination-sm" id="pagination"></ul></nav>
    </div>
</div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Lớp Cơ Sở</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mã lớp *</label>
                                    <input name="base_class_code" id="create_code" class="form-control" required 
                                           placeholder="VD: CNTT2023A">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tên lớp *</label>
                                    <input name="base_class_name" id="create_name" class="form-control" required
                                           placeholder="VD: Công nghệ thông tin K23">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Khoa *</label>
                                    <select name="faculty_id" id="create_faculty" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giảng viên chủ nhiệm *</label>
                                    <select name="lecturer_id" id="create_lecturer" class="form-select" required>
                                        <option value="">-- Chọn giảng viên --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Năm bắt đầu *</label>
                                    <input type="number" name="start_year" id="create_start_year" class="form-control" 
                                           min="2000" max="2099" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Năm kết thúc *</label>
                                    <input type="number" name="end_year" id="create_end_year" class="form-control" 
                                           min="2000" max="2099" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button class="btn btn-primary">Tạo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Lớp Cơ Sở</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mã lớp *</label>
                                    <input name="base_class_code" id="edit_code" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tên lớp *</label>
                                    <input name="base_class_name" id="edit_name" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Khoa *</label>
                                    <select name="faculty_id" id="edit_faculty" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giảng viên chủ nhiệm *</label>
                                    <select name="lecturer_id" id="edit_lecturer" class="form-select" required>
                                        <option value="">-- Chọn giảng viên --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Năm bắt đầu *</label>
                                    <input type="number" name="start_year" id="edit_start_year" class="form-control" 
                                           min="2000" max="2099" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Năm kết thúc *</label>
                                    <input type="number" name="end_year" id="edit_end_year" class="form-control" 
                                           min="2000" max="2099" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Manage Students Modal -->
    <div class="modal fade" id="manageStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quản lý Sinh viên - <span id="class_name_title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#studentsList">Danh sách SV</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#assignStudent">Gán SV theo khóa/ngành</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Students List Tab -->
                        <div id="studentsList" class="tab-pane fade show active">
                            <div id="studentsAlertArea"></div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="classStudentsTable">
                                    <thead>
                                        <tr>
                                            <th>MSSV</th>
                                            <th>Họ tên</th>
                                            <th>Email</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody id="classStudentsBody">
                                        <!-- populated by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Assign Students Tab -->
                        <div id="assignStudent" class="tab-pane fade">
                            <form id="assignStudentsForm">
                                <input type="hidden" id="assign_class_id">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Chọn khoa</label>
                                        <select id="assign_faculty" class="form-select">
                                            <option value="">-- Tất cả khoa --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary mt-4">Gán sinh viên</button>
                                    </div>
                                </div>
                            </form>
                            <div id="assignAlertArea"></div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="availableStudentsTable">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAllStudents"></th>
                                            <th>MSSV</th>
                                            <th>Họ tên</th>
                                            <th>Khoa</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody id="availableStudentsBody">
                                        <!-- populated by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteForm">
                    <input type="hidden" id="delete_id">
                    <div class="modal-body">
                        Bạn có chắc chắn muốn xóa lớp này không? <strong id="delete_name"></strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-danger">Xóa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
const API_URL = '/web_QLSV/admin/api/router.php';

let currentPage = 1;
let currentClassId = null;

// Load faculties for select boxes
function loadFaculties(selectId) {
    fetch(`${API_URL}?module=faculties&action=index&limit=1000`)
        .then(r => {
            if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
            return r.json();
        })
        .then(data => {
            if (data.data) {
                const select = document.getElementById(selectId);
                const currentValue = select.value;
                select.innerHTML = '<option value="">-- Chọn khoa --</option>';
                data.data.forEach(f => {
                    select.innerHTML += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
                });
                if (currentValue) select.value = currentValue;
            }
        })
        .catch(err => console.error('Error loading faculties:', err));
}

// Load lecturers for select boxes
function loadLecturers(selectId, facultyId = null) {
    let url = `${API_URL}?module=lecturers&action=index&limit=1000`;
    if (facultyId) {
        url += `&faculty_id=${facultyId}`;
    }
    fetch(url)
        .then(r => {
            if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
            return r.json();
        })
        .then(data => {
            if (data.data) {
                const select = document.getElementById(selectId);
                const currentValue = select.value;
                select.innerHTML = '<option value="">-- Chọn giảng viên --</option>';
                data.data.forEach(l => {
                    select.innerHTML += `<option value="${l.lecturer_id}">${l.first_name} ${l.last_name} (${l.lecturer_code})</option>`;
                });
                if (currentValue) select.value = currentValue;
            }
        })
        .catch(err => console.error('Error loading lecturers:', err));
}

// Load base classes
function loadBaseClasses(page = 1) {
    currentPage = page;
    fetch(`${API_URL}?module=base_classes&action=index&page=${page}&limit=20`)
        .then(r => {
            if (!r.ok) {
                return r.text().then(text => {
                    throw new Error(`HTTP ${r.status}: ${text}`);
                });
            }
            return r.json();
        })
        .then(data => {
            const tbody = document.getElementById('baseClassesBody');
            tbody.innerHTML = '';

            const total = data.pagination ? data.pagination.total : 0;
            document.getElementById('tableCount').textContent = `${total} lớp`;

            if (!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">Chưa có lớp cơ sở nào</td></tr>';
            } else {
                data.data.forEach((cls, idx) => {
                    const name  = cls.base_class_name.replace(/'/g, "&#39;");
                    tbody.innerHTML += `
                        <tr>
                            <td style="color:#aaa;font-size:.82rem">${(page - 1) * 20 + idx + 1}</td>
                            <td><strong style="color:#4e73df">${cls.base_class_code}</strong></td>
                            <td>${cls.base_class_name}</td>
                            <td>${cls.faculty_name || '<span class="text-muted">-</span>'}</td>
                            <td>${cls.first_name ? cls.first_name + ' ' + cls.last_name : '<span class="text-muted">-</span>'}</td>
                            <td><span style="font-size:.85rem">${cls.start_year}–${cls.end_year}</span></td>
                            <td style="text-align:center"><span class="badge-stat badge-total">${cls.total_students || 0}</span></td>
                            <td style="text-align:center"><span class="badge-stat badge-active">${cls.studying_students || 0}</span></td>
                            <td style="text-align:center"><span class="badge-stat badge-grad">${cls.graduated_students || 0}</span></td>
                            <td>
                                <div class="btn-action">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#manageStudentsModal"
                                            onclick="openManageStudents(${cls.base_class_id}, '${name}')"
                                            title="Quản lý sinh viên"><i class="bi bi-people"></i></button>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                            onclick="openEdit(${cls.base_class_id})"
                                            title="Sửa"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            onclick="openDelete(${cls.base_class_id}, '${name}')"
                                            title="Xóa"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }

            // Pagination
            const totalPages = Math.ceil(data.pagination.total / 20);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                pagination.innerHTML += `
                    <li class="page-item ${i === page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadBaseClasses(${i}); return false;">${i}</a>
                    </li>
                `;
            }
        })
        .catch(err => {
            console.error('Load error:', err);
            showAlert('Lỗi load dữ liệu: ' + err.message, 'danger');
        });
}

// Open edit modal
function openEdit(id) {
    fetch(`${API_URL}?module=base_classes&action=show&id=${id}`)
        .then(r => {
            if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
            return r.json();
        })
        .then(data => {
            if (data.data) {
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_code').value = data.data.base_class_code;
                document.getElementById('edit_name').value = data.data.base_class_name;
                document.getElementById('edit_faculty').value = data.data.faculty_id;
                document.getElementById('edit_lecturer').value = data.data.lecturer_id;
                document.getElementById('edit_start_year').value = data.data.start_year;
                document.getElementById('edit_end_year').value = data.data.end_year;

                loadFaculties('edit_faculty');
                loadLecturers('edit_lecturer');
            }
        })
        .catch(err => console.error('Error loading class:', err));
}

// Open delete modal
function openDelete(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
}

// Open manage students modal
function openManageStudents(classId, className) {
    currentClassId = classId;
    document.getElementById('class_name_title').textContent = className;
    document.getElementById('assign_class_id').value = classId;
    loadClassStudents(classId);
    loadFaculties('assign_faculty');
}

// Load students of a class
function loadClassStudents(classId) {
    fetch(`${API_URL}?module=base_classes&action=getStudents&class_id=${classId}&limit=100`)
        .then(r => {
            if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
            return r.json();
        })
        .then(data => {
            const tbody = document.getElementById('classStudentsBody');
            tbody.innerHTML = '';
            if (data.data && data.data.length > 0) {
                data.data.forEach(s => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${s.student_code}</td>
                            <td>${s.first_name} ${s.last_name}</td>
                            <td>${s.email}</td>
                            <td><span class="badge bg-secondary">${s.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="removeStudent(${s.student_id}, ${classId})">Xóa</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Chưa có sinh viên</td></tr>';
            }
        })
        .catch(err => console.error('Error loading students:', err));
}

// Remove student from class
function removeStudent(studentId, classId) {
    if (confirm('Xóa sinh viên khỏi lớp?')) {
        fetch(`${API_URL}?module=base_classes&action=removeStudent`, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `student_id=${studentId}&base_class_id=${classId}`
        })
        .then(r => {
            if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('Xóa sinh viên thành công', 'success', 'studentsAlertArea');
                loadClassStudents(classId);
            } else {
                showAlert(data.message || 'Lỗi', 'danger', 'studentsAlertArea');
            }
        })
        .catch(err => showAlert('Lỗi: ' + err.message, 'danger', 'studentsAlertArea'));
    }
}

// Đóng modal an toàn — xóa cả backdrop để tránh màn xám
function closeModal(modalId, callback) {
    const el = document.getElementById(modalId);
    const instance = bootstrap.Modal.getInstance(el);
    if (!instance) { cleanupModalDOM(); if (callback) callback(); return; }
    el.addEventListener('hidden.bs.modal', function handler() {
        el.removeEventListener('hidden.bs.modal', handler);
        cleanupModalDOM();
        if (callback) callback();
    });
    instance.hide();
}

function cleanupModalDOM() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
}

// Create
document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const btn = form.querySelector('button[type=submit], button:not([type=button])');
    if (btn) { btn.disabled = true; btn.textContent = 'Đang lưu...'; }

    fetch(`${API_URL}?module=base_classes&action=store`, {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal('createModal', () => {
                form.reset();
                showAlert('Tạo lớp thành công', 'success');
                loadBaseClasses(1);
            });
        } else {
            showAlert(data.message || 'Lỗi tạo lớp', 'danger');
        }
    })
    .catch(err => showAlert('Lỗi kết nối: ' + err.message, 'danger'))
    .finally(() => { if (btn) { btn.disabled = false; btn.textContent = 'Tạo'; } });
});

// Edit
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const id = document.getElementById('edit_id').value;
    const formData = new FormData(form);
    formData.append('id', id);
    const btn = form.querySelector('button[type=submit], button:not([type=button])');
    if (btn) { btn.disabled = true; btn.textContent = 'Đang lưu...'; }

    fetch(`${API_URL}?module=base_classes&action=update`, {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal('editModal', () => {
                showAlert('Cập nhật lớp thành công', 'success');
                loadBaseClasses(currentPage);
            });
        } else {
            showAlert(data.message || 'Lỗi cập nhật', 'danger');
        }
    })
    .catch(err => showAlert('Lỗi kết nối: ' + err.message, 'danger'))
    .finally(() => { if (btn) { btn.disabled = false; btn.textContent = 'Lưu'; } });
});

// Delete
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('delete_id').value;
    const btn = this.querySelector('button.btn-danger');
    if (btn) { btn.disabled = true; btn.textContent = 'Đang xóa...'; }

    fetch(`${API_URL}?module=base_classes&action=delete`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeModal('deleteModal', () => {
                showAlert('Xóa lớp thành công', 'success');
                loadBaseClasses(currentPage);
            });
        } else {
            showAlert(data.message || 'Lỗi xóa lớp', 'danger');
        }
    })
    .catch(err => showAlert('Lỗi kết nối: ' + err.message, 'danger'))
    .finally(() => { if (btn) { btn.disabled = false; btn.textContent = 'Xóa'; } });
});

// Assign Students Form
document.getElementById('assignStudentsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const classId = document.getElementById('assign_class_id').value;
    const facultyId = document.getElementById('assign_faculty').value;
    
    if (!facultyId) {
        showAlert('Vui lòng chọn Filter (khoa)', 'warning', 'assignAlertArea');
        return;
    }

    const filters = JSON.stringify({faculty_id: facultyId || null});

    fetch(`${API_URL}?module=base_classes&action=bulkAssignStudents`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `base_class_id=${classId}&filters=${encodeURIComponent(filters)}`
    })
    .then(r => {
        if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showAlert(`Đã gán ${data.data.count} sinh viên`, 'success', 'assignAlertArea');
            loadClassStudents(classId);
        } else {
            showAlert(data.message || 'Lỗi', 'danger', 'assignAlertArea');
        }
    })
    .catch(err => showAlert('Lỗi: ' + err.message, 'danger', 'assignAlertArea'));
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadFaculties('create_faculty');
    loadLecturers('create_lecturer');
    loadFaculties('edit_faculty');
    loadLecturers('edit_lecturer');
    loadBaseClasses(1);
});

function showAlert(message, type = 'info', elementId = 'alertArea') {
    const alertArea = document.getElementById(elementId);
    const html = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    alertArea.innerHTML = html;
    setTimeout(() => alertArea.innerHTML = '', 5000);
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
