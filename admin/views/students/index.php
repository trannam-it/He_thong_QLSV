<?php
$pageTitle = 'Quản lý Sinh viên';
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>
<style>
        /* .pagination .page-link {
        color: #0d6efd;
        border-radius: 6px;
        margin: 0 2px;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    .pagination .page-link:hover {
        background-color: #e9f2ff;
        border-color: #0a58ca;
        color: #0a58ca;
    } */

     /* Pagination base */
.pagination {
    gap: 4px;
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
}

/* Link */
.pagination .page-link {
    color: #0d6efd;
    border-radius: 6px;
    padding: 6px 10px;
    min-width: 34px;
    text-align: center;
}

/* Active */
.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
    font-weight: 600;
}

/* Hover */
.pagination .page-link:hover {
    background-color: #e9f2ff;
    border-color: #0a58ca;
    color: #0a58ca;
}

/* Disabled */
.pagination .page-item.disabled .page-link {
    color: #adb5bd;
    pointer-events: none;
    background-color: #f8f9fa;
}

.pagination {
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
}
.pagination::-webkit-scrollbar {
    display: none;
}


</style>
    <!-- ===== PAGE HEADER ===== -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title mb-1">
                    <i class="bi bi-mortarboard me-2 text-primary"></i>
                    Quản lý Sinh viên
                </h1>
                <p class="text-muted mb-0">Quản lý hồ sơ, học tập và trạng thái sinh viên</p>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle me-1"></i> Thêm sinh viên
                </button>
            </div>
        </div>
    </div>

    <!-- ===== QUICK ACTIONS ===== -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap gap-2">
                <a href="/web_QLSV/admin/views/students/accounts.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-person-badge me-1"></i> Tài khoản
                </a>
                <a href="/web_QLSV/admin/views/students/enrollments.php" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-journal-bookmark me-1"></i> Đăng ký học phần
                </a>
                <a href="/web_QLSV/admin/views/students/progress.php" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-bar-chart-line me-1"></i> Tiến độ học tập
                </a>
                <a href="/web_QLSV/admin/views/students/statistics.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pie-chart me-1"></i> Thống kê
                </a>
            </div>
        </div>
    </div>

    <!-- ===== STUDENT TABLE ===== -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Danh sách sinh viên</strong>

            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Tìm MSSV, họ tên, email...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div id="alertArea" class="p-3"></div>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">STT</th>
                            <th>MSSV</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Giới tính</th>
                            <th>Ngày sinh</th>
                            <th>Khoa</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Tài khoản</th>
                            <th width="140" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="studentsBody">
                        <!-- JS render -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <nav class="d-flex justify-content-end">
            <ul class="pagination pagination-sm mb-0" id="pagination">
                <!-- JS render -->
            </ul>
            </nav>
        </div>


    </div>

    <!-- <div class="card-footer bg-white">
        <nav class="d-flex justify-content-end">
            <ul class="pagination pagination-sm mb-0" id="pagination">
               
            </ul>
        </nav>
    </div> -->



    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Sinh viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin cá nhân</h6>
                                <div class="mb-3">
                                    <label class="form-label">MSSV</label>
                                    <input name="student_code" id="create_code" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Họ</label>
                                    <input name="first_name" id="create_first" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tên</label>
                                    <input name="last_name" id="create_last" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input name="email" id="create_email" type="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Điện thoại</label>
                                    <input name="phone" id="create_phone" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <select name="gender" id="create_gender" class="form-select" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="Male">Nam</option>
                                        <option value="Female">Nữ</option>
                                        <option value="Other">Khác</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ngày sinh</label>
                                    <input name="birth_date" id="create_birth" type="date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin học tập</h6>
                                <div class="mb-3">
                                    <label class="form-label">Khoa / Bộ môn</label>
                                    <select name="faculty_id" id="create_faculty" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                        <!-- populated by JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ngành học</label>
                                    <select name="major_id" id="create_major" class="form-select">
                                        <option value="">-- Chọn ngành --</option>
                                        <!-- populated by JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lớp hành chính</label>
                                    <select name="student_class_id" id="create_class" class="form-select">
                                        <option value="">-- Chọn lớp --</option>
                                        <!-- populated by JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Khóa học (K45, K46...)</label>
                                    <input name="cohort" id="create_cohort" class="form-control" placeholder="K45">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hệ đào tạo</label>
                                    <select name="training_system" id="create_training" class="form-select">
                                        <option value="Chính quy">Chính quy</option>
                                        <option value="Vừa học vừa làm">Vừa học vừa làm</option>
                                        <option value="Liên thông">Liên thông</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" id="create_status" class="form-select" required>
                                        <option value="Studying">Đang học</option>
                                        <option value="Suspended">Bảo lưu</option>
                                        <option value="Dropped">Thôi học</option>
                                        <option value="Graduated">Tốt nghiệp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6>Thông tin khác</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <textarea name="address" id="create_address" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện (URL)</label>
                                    <input name="avatar" id="create_avatar" class="form-control" type="url">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                       <button type="button" class="btn btn-primary" id="createSubmitBtn">Lưu</button>

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
                    <h5 class="modal-title">Sửa Sinh viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin cá nhân</h6>
                                <div class="mb-3">
                                    <label class="form-label">MSSV</label>
                                    <input name="student_code" id="edit_code" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Họ</label>
                                    <input name="first_name" id="edit_first" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tên</label>
                                    <input name="last_name" id="edit_last" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input name="email" id="edit_email" type="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Điện thoại</label>
                                    <input name="phone" id="edit_phone" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <select name="gender" id="edit_gender" class="form-select" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="Male">Nam</option>
                                        <option value="Female">Nữ</option>
                                        <option value="Other">Khác</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ngày sinh</label>
                                    <input name="birth_date" id="edit_birth" type="date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin học tập</h6>
                                <div class="mb-3">
                                    <label class="form-label">Khoa / Bộ môn</label>
                                    <select name="faculty_id" id="edit_faculty" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                        <!-- populated by JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ngành học</label>
                                    <select name="major_id" id="edit_major" class="form-select">
                                        <option value="">-- Chọn ngành --</option>
                                        <!-- populated by JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lớp hành chính</label>
                                    <select name="student_class_id" id="edit_class" class="form-select">
                                        <option value="">-- Chọn lớp --</option>
                                        <!-- populated by JS -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Khóa học (K45, K46...)</label>
                                    <input name="cohort" id="edit_cohort" class="form-control" placeholder="K45">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hệ đào tạo</label>
                                    <select name="training_system" id="edit_training" class="form-select">
                                        <option value="Chính quy">Chính quy</option>
                                        <option value="Vừa học vừa làm">Vừa học vừa làm</option>
                                        <option value="Liên thông">Liên thông</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" id="edit_status" class="form-select" required>
                                        <option value="Studying">Đang học</option>
                                        <option value="Suspended">Bảo lưu</option>
                                        <option value="Dropped">Thôi học</option>
                                        <option value="Graduated">Tốt nghiệp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6>Thông tin khác</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện (URL)</label>
                                    <input name="avatar" id="edit_avatar" class="form-control" type="url">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" id="editSubmitBtn">Lưu</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thay đổi Trạng thái</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="statusForm">
                    <div class="modal-body">
                        <input type="hidden" id="status_student_id">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái mới</label>
                            <select id="status_new_status" class="form-select" required>
                                <option value="">-- Chọn --</option>
                                <option value="Studying">Đang học</option>
                                <option value="Suspended">Bảo lưu</option>
                                <option value="Dropped">Thôi học</option>
                                <option value="Graduated">Tốt nghiệp</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" id="statusSubmitBtn">Lưu</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
/* ================= CONFIG ================= */
const apiUrl = '/web_QLSV/admin/api/router.php';
let students = [];
let faculties = [];
let currentPage = 1;
let currentSearch = '';
const pageLimit = 15;
let searchTimeout;
const edit_id = document.getElementById('edit_id');
const edit_code = document.getElementById('edit_code');
const edit_first = document.getElementById('edit_first');
const edit_last = document.getElementById('edit_last');
const edit_email = document.getElementById('edit_email');
const edit_phone = document.getElementById('edit_phone');
const edit_gender = document.getElementById('edit_gender');
const edit_birth = document.getElementById('edit_birth');
const edit_faculty = document.getElementById('edit_faculty');
const edit_status = document.getElementById('edit_status');

const status_student_id = document.getElementById('status_student_id');
const status_new_status = document.getElementById('status_new_status');

const editModal = document.getElementById('editModal');
const statusModal = document.getElementById('statusModal');
document.getElementById('pagination')
// .addEventListener('click', e => {
//     if (e.target.classList.contains('page-link')) {
//         e.preventDefault();
//         fetchStudents(+e.target.dataset.page, currentSearch);
//     }
// });


/* ================= UTILS ================= */
function showAlert(msg, type = 'success') {
    document.getElementById('alertArea').innerHTML =
        `<div class="alert alert-${type} alert-dismissible fade show">
            ${msg}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
}

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, s =>
        ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])
    );
}

const genderDisplay = g => ({Male:'Nam', Female:'Nữ', Other:'Khác'})[g] || g;
const statusDisplay = s => ({
    Studying:'Đang học',
    Suspended:'Bảo lưu',
    Dropped:'Thôi học',
    Graduated:'Tốt nghiệp'
}[s] || s);

const statusColor = s => ({
    Studying: 'success',
    Suspended: 'warning',
    Dropped: 'danger',
    Graduated: 'info'
}[s] || 'secondary');


/* ================= FETCH ================= */
async function fetchFaculties() {
    const res = await fetch(`${apiUrl}?module=faculties&action=index&page=1&limit=500`);
    const j = await res.json();
    if (j.success) faculties = j.data;
}

function populateFacultySelects() {
    document.querySelectorAll('[name="faculty_id"]').forEach(sel => {
        const old = sel.value;
        sel.innerHTML = '<option value="">-- Chọn khoa --</option>';
        faculties.forEach(f => {
            sel.innerHTML += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
        });
        if (old) sel.value = old;
    });
}

// async function fetchStudents(page = 1, search = '') {
//     currentPage = page;
//     currentSearch = search;

//     let url = `${apiUrl}?module=students&action=index&page=${page}&limit=${pageLimit}`;
//     if (search) url += `&search=${encodeURIComponent(search)}`;

//     try {
//         const res = await fetch(url);
//         const j = await res.json();
//         if (!j.success) throw new Error(j.message);

//         students = j.data;
//         renderStudents();

//         if (j.pagination) {
//             renderPagination(j.pagination.current_page, j.pagination.total_pages);
//         }
//     } catch {
//         showAlert('Không thể tải danh sách sinh viên', 'danger');
//     }
// }

async function fetchStudents(page = 1, search = '') {
    page = Number(page);
    if (!Number.isInteger(page) || page < 1) page = 1;

    currentPage = page;
    currentSearch = search || '';

    let url = `${apiUrl}?module=students&action=index`
            + `&page=${currentPage}`
            + `&limit=${pageLimit}`;

    if (currentSearch.trim() !== '') {
        url += `&search=${encodeURIComponent(currentSearch.trim())}`;
    }

    try {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'API error');

        students = Array.isArray(json.data) ? json.data : [];
        renderStudents();

       const p = document.getElementById('pagination');

        if (json.pagination && json.pagination.pages > 1) {
            renderPagination(
                Number(json.pagination.page),
                Number(json.pagination.pages)
            );
        } else {
            const p = document.getElementById('pagination');
            if (p) p.innerHTML = '';
        }

    } catch (err) {
        // console.error(err);
        // showAlert('Không thể tải danh sách sinh viên', 'danger');
        // document.getElementById('pagination').innerHTML = '';

        const p = document.getElementById('pagination');
        if (p) p.innerHTML = '';
        showAlert('Không thể tải danh sách sinh viên', 'danger');
    }
}



function renderPagination(current, total) {
    const p = document.getElementById('pagination');
    if (!p) return; // 👈 chốt hạ
    p.innerHTML = '';
    if (total <= 1) return;

    const range = 2; // số trang hiển thị hai bên
    let start = Math.max(1, current - range);
    let end   = Math.min(total, current + range);

    // First
    if (current > 1) {
        p.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="1">«</a>
            </li>
        `;
    }

    // Prev
    p.innerHTML += `
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${current - 1}">‹</a>
        </li>
    `;

    // Dots trước
    if (start > 1) {
        p.innerHTML += `
            <li class="page-item disabled">
                <span class="page-link">…</span>
            </li>
        `;
    }

    // Pages
    for (let i = start; i <= end; i++) {
        p.innerHTML += `
            <li class="page-item ${i === current ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `;
    }

    // Dots sau
    if (end < total) {
        p.innerHTML += `
            <li class="page-item disabled">
                <span class="page-link">…</span>
            </li>
        `;
    }

    // Next
    p.innerHTML += `
        <li class="page-item ${current === total ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${current + 1}">›</a>
        </li>
    `;

    // Last
    if (current < total) {
        p.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${total}">»</a>
            </li>
        `;
    }
}



// document.getElementById('pagination').addEventListener('click', e => {
//     if (!e.target.classList.contains('page-link')) return;

//     const page = parseInt(e.target.dataset.page);

//     if (isNaN(page)) return;
//     if (page < 1) return;
//     if (page === currentPage) return;

//     fetchStudents(page, currentSearch);
// });
document.addEventListener('click', function (e) {
    const link = e.target.closest('.page-link');
    if (!link) return;

    e.preventDefault();

    const page = Number(link.dataset.page);
    if (!page || page === currentPage) return;

    fetchStudents(page, currentSearch);
});


/* ================= RENDER ================= */
function renderStudents() {
    const tbody = document.getElementById('studentsBody');
    tbody.innerHTML = '';

    students.forEach((s, i) => {
        tbody.innerHTML += `
        <tr>
            <td>${(currentPage - 1) * pageLimit + i + 1}</td>
            <td>${escapeHtml(s.student_code)}</td>
            <td>${escapeHtml(s.first_name + ' ' + s.last_name)}</td>
            <td>${escapeHtml(s.email)}</td>
            <td>${genderDisplay(s.gender)}</td>
            <td>${s.birth_date}</td>
            <td>${escapeHtml(s.faculty_name || '')}</td>
            <td>
                <span class="badge bg-${statusColor(s.status)}">
                    ${statusDisplay(s.status)}
                </span>
            </td>
            <td class="text-center">
                ${s.user_id
                    ? '<span class="badge bg-success">✓ Có</span>'
                    : '<span class="badge bg-secondary">✗ Không</span>'}
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-info" onclick="openEdit(${s.student_id})">
                    <i class="bi bi-pencil"></i> Sửa
                </button>
                <button class="btn btn-sm btn-warning" onclick="openStatus(${s.student_id})">
                    Trạng thái
                </button>
            </td>
        </tr>`;
    });
}


/* ================= ACTIONS ================= */
async function openEdit(id) {
    const res = await fetch(`${apiUrl}?module=students&action=show&id=${id}`);
    const j = await res.json();
    if (!j.success) return showAlert(j.message, 'danger');

    const s = j.data;
    edit_id.value = s.student_id;
    edit_code.value = s.student_code;
    edit_first.value = s.first_name;
    edit_last.value = s.last_name;
    edit_email.value = s.email;
    edit_phone.value = s.phone || '';
    edit_gender.value = s.gender;
    edit_birth.value = s.birth_date;
    edit_faculty.value = s.faculty_id;
    edit_status.value = s.status;

    new bootstrap.Modal(editModal).show();
}

function openStatus(id) {
    status_student_id.value = id;
    status_new_status.value = '';
    new bootstrap.Modal(statusModal).show();
}

/* ================= EVENTS ================= */
document.getElementById('searchInput').addEventListener('input', e => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchStudents(1, e.target.value);
    }, 300);
});

// document.addEventListener('click', e => {
//     if (e.target.classList.contains('page-link')) {
//         e.preventDefault();
//         fetchStudents(+e.target.dataset.page, currentSearch);
//     }
// });

/* ================= SUBMIT ================= */
document.getElementById('statusSubmitBtn').onclick = async () => {
    if (!status_new_status.value) {
        showAlert('Vui lòng chọn trạng thái', 'warning');
        return;
    }

    const params = new URLSearchParams({
        module:'students',
        action:'changeStatus',
        id: status_student_id.value,
        status: status_new_status.value
    });

    const res = await fetch(apiUrl, { method:'POST', body: params });
    const j = await res.json();

    if (j.success) {
        showAlert('Cập nhật trạng thái thành công');
        bootstrap.Modal.getInstance(statusModal).hide();
        fetchStudents(currentPage, currentSearch);
    } else {
        showAlert(j.message || 'Lỗi', 'danger');
    }
};

/* ================= INIT ================= */
(async () => {
    await fetchFaculties();
    populateFacultySelects();
    fetchStudents();
})();

});

</script>



<?php require_once __DIR__ . '/../layout/footer.php'; ?>
