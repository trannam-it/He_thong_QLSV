<?php
$pageTitle = "Quản lý Giảng viên";
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid">

    <!-- ===== PAGE HEADER ===== -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-person-badge-fill text-primary"></i>
                Quản lý Giảng viên
            </h3>
            <small class="text-muted">
                Quản lý thông tin, phân công môn học và lớp giảng dạy
            </small>
        </div>

        <button class="btn btn-primary shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Thêm Giảng viên
        </button>
    </div>

    <!-- ===== QUICK STATS ===== -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Tổng Giảng viên</h6>
                        <h4 class="fw-bold mb-0" id="totalLecturers">0</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-3 me-3">
                        <i class="bi bi-book-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Đang giảng dạy</h6>
                        <h4 class="fw-bold mb-0">--</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info text-white rounded-circle p-3 me-3">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted">Số Khoa</h6>
                        <h4 class="fw-bold mb-0" id="totalFaculties">--</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TABLE CARD ===== -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>
                <strong>Danh sách Giảng viên</strong>
            </div>
            <select id="facultyFilter"
                    class="form-select form-select-sm"
                    style="width:200px;">
                <option value="">-- Tất cả khoa --</option>
            </select>


            <div class="d-flex gap-2">
                <input type="text"
                       id="searchInput"
                       class="form-control form-control-sm"
                       style="width: 250px;"
                       placeholder="Tìm theo tên, mã...">
            </div>

        </div>

        <div class="card-body">
            <div id="alertArea"></div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="lecturersTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Mã GV</th>
                            <th>Họ Tên</th>
                            <th>Email</th>
                            <th>Khoa</th>
                            <th>Học vị</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="lecturersBody">
                        <!-- JS render -->
                    </tbody>
                </table>
            </div>
        </div>

    <!-- <nav>
        <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
    </nav> -->

        <div class="card-footer bg-white">
            <nav class="d-flex justify-content-end">
            <ul class="pagination pagination-sm mb-0" id="pagination">
                <!-- JS render -->
            </ul>
        </nav>

    </div>

     <div class="modal fade" id="assignClassesModal" tabindex="-1">
        <!-- <div class="modal fade" id="createForm" tabindex="-1">  -->
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gán Lớp cho Giảng viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="assignClassesForm">
                    <div class="modal-body">
                        <input type="hidden" id="assignClasses_lecturer_id">
                        <div class="mb-3">
                            <label class="form-label">Chọn Lớp</label>
                            <div id="classesCheckboxes" style="max-height: 400px; overflow-y: auto;">
                                <!-- populated by JS -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Gán</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== MODALS ===== -->
 <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Giảng viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin cá nhân</h6>
                                <div class="mb-3">
                                    <label class="form-label">Mã giảng viên</label>
                                    <input name="lecturer_code" id="create_code" class="form-control" required>
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
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin chuyên môn</h6>
                                <div class="mb-3">
                                    <label class="form-label">Học vị</label>
                                    <select name="degree" id="create_degree" class="form-select" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="Bachelor">Cử nhân</option>
                                        <option value="Master">Thạc sĩ</option>
                                        <option value="PhD">Tiến sĩ</option>
                                        <option value="Professor">Giáo sư</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Khoa / Bộ môn</label>
                                    <!-- <select name="faculty_id" id="create_faculty" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                
                                    </select> -->

                                        <select name="faculty_id"
                                                id="create_faculty"
                                                class="form-select"
                                                required>
                                            <option value="">-- Chọn khoa --</option>
                                        </select>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Tạo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   <!-- <nav>
  <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
</nav> -->

</div>

<!-- 
<script>
    document.addEventListener('DOMContentLoaded', function() {

const apiUrl = '/web_QLSV/admin/api/router.php?module=lecturers';

let lecturers = [], faculties = [], subjects = [], classes = [];
let searchTimeout;

function showAlert(message, type='success'){
    const area = document.getElementById('alertArea');
    area.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
}

 let currentPage = 1;
    let totalPages = 1;

/* ================= INIT ================= */
    loadFaculties();
    loadSubjects();


/* ================= LOAD SUBJECT ================= */
function loadSubjects(page = 1) {

    currentPage = page;

    document.getElementById('loading').style.display = 'block';


    const faculty = document.getElementById('facultyFilter').value;
    const search = document.getElementById('searchInput').value;
    const params = new URLSearchParams({
    action: 'index',
    page: page,
    search: search,
    faculty_id: faculty,
    sort: sort,
    order: order
    });

    // fetch(`${API}&action=index&page=${page}&search=${search}&faculty_id=${faculty}&sort=${sort}&order=${order}`)
        fetch(`${API}&${params.toString()}`)
        .then(r=>r.json())
        .then(res=>{

            document.getElementById('loading').style.display = 'none';

            if(!res.success){
                alert(res.message);
                return;
            }

            renderTable(res.data);
            // renderPagination(res.pagination);
            if (res.pagination && res.pagination.pages > 1) {
                renderPagination(
                    Number(res.pagination.page),
                    Number(res.pagination.pages)
                );
            } else {
                document.getElementById('pagination').innerHTML = '';
            }

        });
}

/* ================= PAGINATION ================= */

function renderPagination(current, total) {

    const p = document.getElementById('pagination');
    if (!p) return;

    p.innerHTML = '';
    if (total <= 1) return;

    const range = 2;
    let start = Math.max(1, current - range);
    let end   = Math.min(total, current + range);

    let html = '<ul class="pagination">';

    if (current > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadSubjects(1);return false;">«</a>
            </li>`;
    }

    html += `
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadSubjects(${current - 1});return false;">‹</a>
        </li>`;

    if (start > 1) {
        html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }

    for (let i = start; i <= end; i++) {
        html += `
            <li class="page-item ${i === current ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadSubjects(${i});return false;">${i}</a>
            </li>`;
    }

    if (end < total) {
        html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }

    html += `
        <li class="page-item ${current === total ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadSubjects(${current + 1});return false;">›</a>
        </li>`;

    if (current < total) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadSubjects(${total});return false;">»</a>
            </li>`;
    }

    html += '</ul>';

    p.innerHTML = html;
}



function renderLecturers(){
    const tbody = document.getElementById('lecturersBody');
    tbody.innerHTML = '';

    document.getElementById('totalLecturers').innerText = lecturers.length;

    lecturers.forEach((l, index) => {
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>${index + 1}</td>
            <td><span class="badge bg-dark">${escapeHtml(l.lecturer_code)}</span></td>
            <td class="fw-semibold">
                ${escapeHtml(l.first_name + ' ' + l.last_name)}
            </td>
            <td>${escapeHtml(l.email)}</td>
            <td>
                <span class="badge bg-info text-dark">
                    ${escapeHtml(l.faculty_name || '')}
                </span>
            </td>
            <td>
                <span class="badge bg-success">
                    ${escapeHtml(l.degree || '')}
                </span>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary"
                            onclick="openEdit(${l.lecturer_id})">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button class="btn btn-outline-success"
                            onclick="openAssignSubjects(${l.lecturer_id})">
                        <i class="bi bi-book"></i>
                    </button>

                    <button class="btn btn-outline-warning"
                            onclick="openAssignClasses(${l.lecturer_id})">
                        <i class="bi bi-diagram-3"></i>
                    </button>

                    <button class="btn btn-outline-danger"
                            onclick="deleteLecturer(${l.lecturer_id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(tr);
    });
}


function escapeHtml(str){ return String(str).replace(/[&<>\"]/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' })[s]); }

async function openEdit(id){
    try{
        const res = await fetch(`${apiUrl}?module=lecturers&action=show&id=${id}`, { credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (!j.success) { showAlert(j.message||'Không tìm thấy','danger'); return; }
            const l = j.data;
            document.getElementById('edit_id').value = l.lecturer_id;
            document.getElementById('edit_code').value = l.lecturer_code;
            document.getElementById('edit_first').value = l.first_name;
            document.getElementById('edit_last').value = l.last_name;
            document.getElementById('edit_email').value = l.email;
            document.getElementById('edit_phone').value = l.phone || '';
            document.getElementById('edit_degree').value = l.degree;
            document.getElementById('edit_faculty').value = l.faculty_id;
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        } catch (e) { console.warn('openEdit: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
}

document.getElementById('createForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const params = new URLSearchParams();
    params.append('module','lecturers');
    params.append('action','store');
    params.append('lecturer_code', document.getElementById('create_code').value);
    params.append('first_name', document.getElementById('create_first').value);
    params.append('last_name', document.getElementById('create_last').value);
    params.append('email', document.getElementById('create_email').value);
    params.append('phone', document.getElementById('create_phone').value);
    params.append('degree', document.getElementById('create_degree').value);
    params.append('faculty_id', document.getElementById('create_faculty').value);
    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) { showAlert('Tạo thành công'); fetchLecturers(); document.getElementById('createForm').reset();
                bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('createForm response invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
});

const editForm = document.getElementById('editForm');
if (editForm) {
    editForm.addEventListener('submit', async function(e){
// document.getElementById('editForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const id = document.getElementById('edit_id').value;
    const params = new URLSearchParams();
    params.append('module','lecturers');
    params.append('action','update');
    params.append('id', id);
    params.append('lecturer_code', document.getElementById('edit_code').value);
    params.append('first_name', document.getElementById('edit_first').value);
    params.append('last_name', document.getElementById('edit_last').value);
    params.append('email', document.getElementById('edit_email').value);
    params.append('phone', document.getElementById('edit_phone').value);
    params.append('degree', document.getElementById('edit_degree').value);
    params.append('faculty_id', document.getElementById('edit_faculty').value);
    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) { showAlert('Cập nhật thành công'); fetchLecturers();
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('editForm response invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
  });
}

async function deleteLecturer(id){
    if(!confirm('Xóa giảng viên này?')) return;
    const params = new URLSearchParams();
    params.append('module','lecturers');
    params.append('action','delete');
    params.append('id', id);
    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) { showAlert('Đã xóa'); fetchLecturers(); }
            else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('deleteLecturer response invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
}

document.getElementById('searchInput').addEventListener('input', function(e){
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchLecturers(e.target.value);
    }, 300);
});

async function fetchSubjects(){
    try{
        const res = await fetch(`${apiUrl}?module=subjects&action=index&page=1&limit=500`, { credentials: 'same-origin' });
        if (!res.ok) return;
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j && j.success) subjects = j.data;
        } catch (e) { console.warn('fetchSubjects: invalid JSON'); }
    }catch(e){ console.error(e); }
}

async function fetchClasses(){
    try{
        const res = await fetch(`${apiUrl}?module=classes&action=index&page=1&limit=500`, { credentials: 'same-origin' });
        if (!res.ok) return;
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j && j.success) classes = j.data;
        } catch (e) { console.warn('fetchClasses: invalid JSON'); }
    }catch(e){ console.error(e); }
}

async function openAssignSubjects(lecturerId){
    if (!subjects.length) await fetchSubjects();
    
    document.getElementById('assignSubjects_lecturer_id').value = lecturerId;
    const container = document.getElementById('subjectsCheckboxes');
    container.innerHTML = '';
    
    subjects.forEach(s => {
        const div = document.createElement('div');
        div.className = 'form-check';
        div.innerHTML = `
            <input class="form-check-input" type="checkbox" value="${s.subject_id}" id="subject_${s.subject_id}">
            <label class="form-check-label" for="subject_${s.subject_id}">
                ${escapeHtml(s.subject_code)} - ${escapeHtml(s.subject_name)}
            </label>
        `;
        container.appendChild(div);
    });
    
    const modal = new bootstrap.Modal(document.getElementById('assignSubjectsModal'));
    modal.show();
}

async function openAssignClasses(lecturerId){
    if (!classes.length) await fetchClasses();
    
    document.getElementById('assignClasses_lecturer_id').value = lecturerId;
    const container = document.getElementById('classesCheckboxes');
    container.innerHTML = '';
    
    classes.forEach(c => {
        const div = document.createElement('div');
        div.className = 'form-check';
        div.innerHTML = `
            <input class="form-check-input" type="checkbox" value="${c.class_id}" id="class_${c.class_id}">
            <label class="form-check-label" for="class_${c.class_id}">
                ${escapeHtml(c.class_code)} - ${escapeHtml(c.subject_name || '')} (${escapeHtml(c.semester)}, ${escapeHtml(c.year)})
            </label>
        `;
        container.appendChild(div);
    });
    
    const modal = new bootstrap.Modal(document.getElementById('assignClassesModal'));
    modal.show();
}

document.getElementById('assignSubjectsForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const lecturerId = document.getElementById('assignSubjects_lecturer_id').value;
    const selectedIds = Array.from(document.querySelectorAll('#subjectsCheckboxes input[type="checkbox"]:checked'))
        .map(el => el.value);
    
    if (!selectedIds.length) {
        showAlert('Vui lòng chọn ít nhất một môn học', 'warning');
        return;
    }
    
    const params = new URLSearchParams();
    params.append('module', 'lecturers');
    params.append('action', 'assignSubjects');
    params.append('lecturer_id', lecturerId);
    selectedIds.forEach(id => params.append('subject_ids[]', id));
    
    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert('Gán môn học thành công');
                bootstrap.Modal.getInstance(document.getElementById('assignSubjectsModal')).hide();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('assignSubjects response invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
});

document.getElementById('assignClassesForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const lecturerId = document.getElementById('assignClasses_lecturer_id').value;
    const selectedIds = Array.from(document.querySelectorAll('#classesCheckboxes input[type="checkbox"]:checked'))
        .map(el => el.value);
    
    if (!selectedIds.length) {
        showAlert('Vui lòng chọn ít nhất một lớp', 'warning');
        return;
    }
    
    const params = new URLSearchParams();
    params.append('module', 'lecturers');
    params.append('action', 'assignClasses');
    params.append('lecturer_id', lecturerId);
    selectedIds.forEach(id => params.append('class_ids[]', id));
    
    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert('Gán lớp thành công');
                bootstrap.Modal.getInstance(document.getElementById('assignClassesModal')).hide();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('assignClasses response invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
});

function renderPagination(){
    const container = document.getElementById('pagination');
    container.innerHTML = '';

    for(let i = 1; i <= totalPages; i++){
        const li = document.createElement('li');
        li.className = 'page-item ' + (i === currentPage ? 'active' : '');
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.onclick = (e) => {
            e.preventDefault();
            fetchLecturers(document.getElementById('searchInput').value, i);
        };
        container.appendChild(li);
    }
}

/* ================= LOAD FACULTY ================= */

async function loadFaculties() {

    const res = await fetch('/web_QLSV/admin/api/router.php?module=faculties&action=index&page=1&limit=500');
    const json = await res.json();

    if (!json.success) return;

    const faculties = json.data || [];

    let options = '<option value="">-- Tất cả khoa --</option>';

    faculties.forEach(f => {
        options += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
    });

    document.getElementById('facultyFilter').innerHTML = options;

    // Modal select
    document.getElementById('faculty_id').innerHTML =
        '<option value="">-- Chọn khoa --</option>' +
        faculties.map(f =>
            `<option value="${f.faculty_id}">${f.faculty_name}</option>`
        ).join('');
}


// Init
fetchFaculties().then(() => {
    populateFacultySelects();
    fetchLecturers();
    fetchSubjects();
    fetchClasses();
});

});

</script> -->
<!-- 
<script>
document.addEventListener('DOMContentLoaded', function(){

    /* ================================
        CONFIG
    ================================== */
    const API = '/web_QLSV/admin/api/router.php?module=lecturers';
    const FACULTY_API = '/web_QLSV/admin/api/router.php?module=faculties';

    let lecturers = [];
    let faculties = [];
    let currentPage = 1;
    let totalPages = 1;
    let searchTimeout = null;


    /* ================================
        LOAD FACULTIES
    ================================== */
    async function loadFaculties(){

        const res = await fetch(`${FACULTY_API}&action=index&page=1&limit=500`);
        const json = await res.json();

        if(!json.success){
            alert(json.message);
            return;
        }

        faculties = json.data;

        // đổ vào filter
        const filter = document.getElementById('facultyFilter');
        filter.innerHTML = `<option value="">-- Tất cả khoa --</option>`;

        faculties.forEach(f=>{
            filter.innerHTML += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
        });

        // đổ vào create form
        const createSel = document.getElementById('create_faculty');
        createSel.innerHTML = '';

        faculties.forEach(f=>{
            createSel.innerHTML += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
        });
    }


    /* ================================
        LOAD LECTURERS
    ================================== */
    async function loadLecturers(page = 1){

        currentPage = page;

        const search  = document.getElementById('searchInput').value;
        const faculty = document.getElementById('facultyFilter').value;

        const params = new URLSearchParams({
            action: 'index',
            page: page,
            limit: 10,
            search: search,
            faculty_id: faculty
        });

        const res = await fetch(`${API}&${params.toString()}`);
        const json = await res.json();

        if(!json.success){
            alert(json.message);
            return;
        }

        lecturers  = json.data;
        totalPages = json.pagination.pages;

        document.getElementById('totalLecturers').innerText =
            json.pagination.total_records ?? lecturers.length;

        renderLecturers();
        renderPagination();
    }


    /* ================================
        RENDER TABLE
    ================================== */
   function renderLecturers(){

    const tbody = document.getElementById('lecturersBody');
    tbody.innerHTML = '';

    if(lecturers.length === 0){
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted">
                    Không có dữ liệu
                </td>
            </tr>`;
        return;
    }

    lecturers.forEach((l,index)=>{

        tbody.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td><span class="badge bg-dark">${l.lecturer_code}</span></td>
                <td class="fw-semibold">
                    ${l.first_name} ${l.last_name}
                </td>
                <td>${l.email}</td>
                <td>
                    <span class="badge bg-info text-dark">
                        ${l.faculty_name ?? ''}
                    </span>
                </td>
                <td>
                    <span class="badge bg-success">
                        ${l.degree ?? ''}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger"
                        onclick="deleteLecturer(${l.lecturer_id})">
                        Xóa
                    </button>
                </td>
            </tr>`;
    });
}



    /* ================================
        PAGINATION
    ================================== */
    function renderPagination(){

        const p = document.getElementById('pagination');
        p.innerHTML = '';

        if(totalPages <= 1) return;

        for(let i=1;i<=totalPages;i++){
            p.innerHTML += `
                <li class="page-item ${i===currentPage?'active':''}">
                    <a class="page-link" href="#"
                        onclick="loadLecturers(${i});return false;">
                        ${i}
                    </a>
                </li>`;
        }
    }


    /* ================================
        CREATE
    ================================== */
    document.getElementById('createForm')
        .addEventListener('submit', async function(e){

        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action','store');

        const res = await fetch(API,{
            method:'POST',
            body: formData
        });

        const json = await res.json();

        if(!json.success){
            alert(json.message);
            return;
        }

        bootstrap.Modal.getInstance(
            document.getElementById('createModal')
        ).hide();

        this.reset();
        loadLecturers(currentPage);
    });


    /* ================================
        DELETE
    ================================== */
    window.deleteLecturer = async function(id){

        if(!confirm('Bạn chắc chắn muốn xóa?')) return;

        const formData = new FormData();
        formData.append('action','delete');
        formData.append('id',id);

        const res = await fetch(API,{
            method:'POST',
            body: formData
        });

        const json = await res.json();

        if(!json.success){
            alert(json.message);
            return;
        }

        loadLecturers(currentPage);
    };


    /* ================================
        EDIT (CƠ BẢN)
    ================================== */
    window.editLecturer = function(id){

        const l = lecturers.find(x=>x.lecturer_id==id);
        if(!l) return;

        document.getElementById('create_full_name').value = l.full_name;
        document.getElementById('create_email').value = l.email;
        document.getElementById('create_phone').value = l.phone ?? '';
        document.getElementById('create_faculty').value = l.faculty_id;

        new bootstrap.Modal(
            document.getElementById('createModal')
        ).show();
    };


    /* ================================
        EVENTS
    ================================== */

    // Search debounce
    document.getElementById('searchInput')
        .addEventListener('input', function(){

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(()=>{
            loadLecturers(1);
        },300);
    });

    // Filter khoa
    document.getElementById('facultyFilter')
        .addEventListener('change', ()=> loadLecturers(1));


    /* ================================
        INIT
    ================================== */
    (async function(){
        await loadFaculties();
        await loadLecturers();
    })();

});

</script> -->

<script>
document.addEventListener('DOMContentLoaded', function(){

/* ===============================
   CONFIG
================================ */
const API = '/web_QLSV/admin/api/router.php?module=lecturers';
const FACULTY_API = '/web_QLSV/admin/api/router.php?module=faculties';

let lecturers = [];
let faculties = [];
let currentPage = 1;
let totalPages = 1;
let searchTimeout = null;


/* ===============================
   LOAD FACULTIES
================================ */
async function loadFaculties(){

    try{
        const res = await fetch(`${FACULTY_API}&action=index&page=1&limit=500`);
        const json = await res.json();

        if(!json.success) return;

        faculties = json.data;

        // FILTER SELECT
        const filter = document.getElementById('facultyFilter');
        filter.innerHTML = `<option value="">-- Tất cả khoa --</option>`;
        faculties.forEach(f=>{
            filter.innerHTML += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
        });

        // CREATE SELECT
        const createSel = document.getElementById('create_faculty');
        createSel.innerHTML = `<option value="">-- Chọn khoa --</option>`;
        faculties.forEach(f=>{
            createSel.innerHTML += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
        });

    }catch(err){
        console.error('Load faculties error:',err);
    }
}


/* ===============================
   LOAD LECTURERS
================================ */
async function loadLecturers(page = 1){

    currentPage = page;

    const search  = document.getElementById('searchInput').value;
    const faculty = document.getElementById('facultyFilter').value;

    const params = new URLSearchParams({
        action: 'index',
        page: page,
        limit: 10,
        search: search,
        faculty_id: faculty
    });

    try{
        const res = await fetch(`${API}&${params.toString()}`);
        const json = await res.json();

        if(!json.success){
            alert(json.message);
            return;
        }

        lecturers  = json.data;
        totalPages = json.pagination.pages;

        document.getElementById('totalLecturers').innerText =
            json.pagination.total_records ?? lecturers.length;

        renderLecturers();
        renderPagination();

    }catch(err){
        console.error('Load lecturers error:',err);
    }
}


/* ===============================
   RENDER TABLE
================================ */
function renderLecturers(){

    const tbody = document.getElementById('lecturersBody');
    tbody.innerHTML = '';

    if(lecturers.length === 0){
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted">
                    Không có dữ liệu
                </td>
            </tr>`;
        return;
    }

    lecturers.forEach((l,index)=>{

        tbody.innerHTML += `
            <tr>
                <td>${index + 1}</td>
                <td><span class="badge bg-dark">${l.lecturer_code}</span></td>
                <td class="fw-semibold">${l.first_name} ${l.last_name}</td>
                <td>${l.email}</td>
                <td>
                    <span class="badge bg-info text-dark">
                        ${l.faculty_name ?? ''}
                    </span>
                </td>
                <td>
                    <span class="badge bg-success">
                        ${l.degree ?? ''}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger"
                        onclick="deleteLecturer(${l.lecturer_id})">
                        Xóa
                    </button>
                </td>
            </tr>`;
    });
}


/* ===============================
   PAGINATION
================================ */
function renderPagination(){

    const p = document.getElementById('pagination');
    p.innerHTML = '';

    if(totalPages <= 1) return;

    for(let i=1;i<=totalPages;i++){
        p.innerHTML += `
            <li class="page-item ${i===currentPage?'active':''}">
                <a class="page-link" href="#"
                    onclick="loadLecturers(${i});return false;">
                    ${i}
                </a>
            </li>`;
    }
}


/* ===============================
   CREATE
================================ */
document.getElementById('createForm')
.addEventListener('submit', async function(e){

    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action','store');

    try{
        const res = await fetch(API,{
            method:'POST',
            body: formData
        });

        const json = await res.json();

        if(!json.success){
            alert(json.message);
            return;
        }

        bootstrap.Modal.getInstance(
            document.getElementById('createModal')
        ).hide();

        this.reset();
        loadLecturers(1);

    }catch(err){
        console.error('Create error:',err);
    }
});


/* ===============================
   DELETE
================================ */
window.deleteLecturer = async function(id){

    if(!confirm('Bạn chắc chắn muốn xóa?')) return;

    const formData = new FormData();
    formData.append('action','delete');
    formData.append('id',id);

    try{
        const res = await fetch(API,{
            method:'POST',
            body: formData
        });

        const json = await res.json();

        if(!json.success){
            alert(json.message);
            return;
        }

        loadLecturers(currentPage);

    }catch(err){
        console.error('Delete error:',err);
    }
};


/* ===============================
   SEARCH & FILTER EVENTS
================================ */
document.getElementById('searchInput')
.addEventListener('input', function(){

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(()=>{
        loadLecturers(1);
    },300);
});

document.getElementById('facultyFilter')
.addEventListener('change', ()=> loadLecturers(1));


/* ===============================
   INIT
================================ */
(async function(){
    await loadFaculties();
    await loadLecturers();
})();

});
</script>


<?php require_once __DIR__ . '/../layout/footer.php'; ?>
