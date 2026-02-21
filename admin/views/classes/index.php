<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="topbar">
        <h2>Quản lý Lớp Cơ Sở</h2>
        <div>
            <a href="statistics.php" class="btn btn-outline-secondary">Thống kê</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Thêm Lớp</button>
        </div>
    </div>

    <div class="card p-3">
        <div id="alertArea"></div>
        <table class="table table-striped" id="baseClassesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã Lớp</th>
                    <th>Tên Lớp</th>
                    <th>Khoa</th>
                    <th>GVCN</th>
                    <th>Năm</th>
                    <th>Tổng SV</th>
                    <th>Đang học</th>
                    <th>Tốt nghiệp</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="baseClassesBody">
                <!-- populated by JS -->
            </tbody>
        </table>
        <nav aria-label="Page navigation">
            <ul class="pagination" id="pagination">
                <!-- populated by JS -->
            </ul>
        </nav>
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

            if (!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="text-center">Chưa có lớp cơ sở</td></tr>';
            } else {
                data.data.forEach((cls, idx) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${(page - 1) * 20 + idx + 1}</td>
                            <td><strong>${cls.base_class_code}</strong></td>
                            <td>${cls.base_class_name}</td>
                            <td>${cls.faculty_name || '-'}</td>
                            <td>${cls.first_name ? cls.first_name + ' ' + cls.last_name : '-'}</td>
                            <td>${cls.start_year}-${cls.end_year}</td>
                            <td><span class="badge bg-primary">${cls.total_students || 0}</span></td>
                            <td><span class="badge bg-success">${cls.studying_students || 0}</span></td>
                            <td><span class="badge bg-info">${cls.graduated_students || 0}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#manageStudentsModal" onclick="openManageStudents(${cls.base_class_id}, '${cls.base_class_name}')">SV</button>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" onclick="openEdit(${cls.base_class_id})">Sửa</button>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="openDelete(${cls.base_class_id}, '${cls.base_class_name}')">Xóa</button>
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

// Create
document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(`${API_URL}?module=base_classes&action=store`, {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(r => {
        if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Tạo lớp thành công', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
            modal.hide();
            this.reset();
            loadBaseClasses(1);
        } else {
            showAlert(data.message || 'Lỗi', 'danger');
        }
    })
    .catch(err => showAlert('Lỗi: ' + err.message, 'danger'));
});

// Edit
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('edit_id').value;
    const formData = new FormData(this);
    formData.append('id', id);
    
    fetch(`${API_URL}?module=base_classes&action=update`, {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(r => {
        if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Cập nhật lớp thành công', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            modal.hide();
            loadBaseClasses(currentPage);
        } else {
            showAlert(data.message || 'Lỗi', 'danger');
        }
    })
    .catch(err => showAlert('Lỗi: ' + err.message, 'danger'));
});

// Delete
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('delete_id').value;
    
    fetch(`${API_URL}?module=base_classes&action=delete`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}`
    })
    .then(r => {
        if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Xóa lớp thành công', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            modal.hide();
            loadBaseClasses(currentPage);
        } else {
            showAlert(data.message || 'Lỗi', 'danger');
        }
    })
    .catch(err => showAlert('Lỗi: ' + err.message, 'danger'));
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
