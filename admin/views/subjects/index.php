<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../libs/Auth.php';
require_once __DIR__ . '/../../../includes/auth_check.php';

authCheck(['super_admin']);

$pageTitle = 'Quản Lý Môn Học';
include __DIR__ . '/../layout/header.php';
?>

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">
            <i class="bi bi-book text-primary me-2"></i>
            Quản lý môn học
        </h4>

        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#subjectModal"
                onclick="openAddForm()">
            <i class="bi bi-plus-circle"></i> Thêm môn
        </button>
    </div>

    <!-- SEARCH + FILTER -->
    <div class="card mb-3">
        <div class="card-body row g-2">

            <div class="col-md-3">
                <input type="text"
                       id="searchInput"
                       class="form-control"
                       placeholder="Tìm theo mã hoặc tên">
            </div>

            <div class="col-md-3">
                <select id="facultyFilter" class="form-select">
                    <option value="">-- Tất cả khoa --</option>
                </select>
            </div>

            <div class="col-md-2">
                <select id="sortSelect" class="form-select">
                    <option value="subject_id">Mặc định</option>
                    <option value="subject_code">Mã môn</option>
                    <option value="subject_name">Tên môn</option>
                    <option value="credit_hours">Tín chỉ</option>
                </select>
            </div>

            <div class="col-md-2">
                <select id="orderSelect" class="form-select">
                    <option value="DESC">Giảm dần</option>
                    <option value="ASC">Tăng dần</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100" onclick="loadSubjects(1)">
                    Tìm
                </button>
                <button class="btn btn-secondary w-100" onclick="clearSearch()">
                    Reset
                </button>
            </div>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="card-body">

            <div id="loading" class="text-center py-4" style="display:none;">
                <div class="spinner-border text-primary"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tên</th>
                            <th class="text-center">Tín chỉ</th>
                            <th>Khoa</th>
                            <th>Tiên quyết</th>
                            <th width="120"></th>
                        </tr>
                    </thead>
                    <tbody id="subjectTable"></tbody>
                </table>
            </div>

            <!-- <div id="pagination" class="d-flex justify-content-center mt-3"></div> -->

            <div class="card-footer bg-white">
            <nav class="d-flex justify-content-end">
            <ul class="pagination pagination-sm mb-0" id="pagination">
                <!-- JS render -->
            </ul>
            </nav>
        </div>

        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="subjectModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 id="modalTitle">Thêm môn</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="subjectForm">

                    <input type="hidden" name="subject_id" id="subject_id">

                    <div class="mb-2">
                        <label>Mã môn</label>
                        <input type="text" name="subject_code" id="subject_code" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Tên môn</label>
                        <input type="text" name="subject_name" id="subject_name" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Tín chỉ</label>
                        <input type="number" name="credit_hours" id="credit_hours" class="form-control" min="1" max="12" required>
                    </div>

                    <div class="mb-2">
                        <label>Khoa</label>
                        <select name="faculty_id" id="faculty_id" class="form-select" required></select>
                    </div>

                    <div class="mb-2">
                        <label>Tiên quyết</label>
                        <select name="prerequisite_code" id="prerequisite_code" class="form-select">
                            <option value="">-- Không có --</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Mô tả</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-primary" onclick="saveSubject()">Lưu</button>
            </div>

        </div>
    </div>
</div>

<script>
const API = '/web_QLSV/admin/api/router.php?module=subjects';
let currentPage = 1;

/* ================= INIT ================= */
document.addEventListener('DOMContentLoaded', () => {
    loadFaculties();
    loadPrerequisites();
    loadSubjects();
});

/* ================= LOAD SUBJECT ================= */
function loadSubjects(page = 1) {

    currentPage = page;

    document.getElementById('loading').style.display = 'block';

    const search = document.getElementById('searchInput').value;
    const faculty = document.getElementById('facultyFilter').value;
    const sort = document.getElementById('sortSelect').value;
    const order = document.getElementById('orderSelect').value;
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

/* ================= RENDER TABLE ================= */
function renderTable(data){

     if(!data || data.length === 0){
        document.getElementById('subjectTable').innerHTML =
            `<tr><td colspan="6" class="text-center text-muted">Không có dữ liệu</td></tr>`;
        return;
    }
    let html = '';

    data.forEach(s=>{
        html += `
        <tr>
            <td>${s.subject_code}</td>
            <td>${s.subject_name}</td>
            <td class="text-center">${s.credit_hours}</td>
            <td>${s.faculty_name ?? ''}</td>
            <td>${s.prerequisite_name ?? ''}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="edit(${s.subject_id})">Sửa</button>
                <button class="btn btn-sm btn-danger" onclick="del(${s.subject_id})">Xóa</button>
            </td>
        </tr>`;
    });

    document.getElementById('subjectTable').innerHTML = html;
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


/* ================= SAVE ================= */
function saveSubject(){

    const form = document.getElementById('subjectForm');
    if(!form.checkValidity()){
        form.reportValidity();
        return;
    }

    const fd = new FormData(form);
    const id = document.getElementById('subject_id').value;
    const action = id ? 'update' : 'store';

    fetch(`${API}&action=${action}`,{
        method:'POST',
        body:fd
    })
    .then(r=>r.json())
    .then(res=>{
        if(!res.success){
            alert(res.message);
            return;
        }

        bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
        loadSubjects(currentPage);
    });
}

/* ================= DELETE ================= */
function del(id){
    if(!confirm("Bạn chắc chắn xóa?")) return;

    const fd = new FormData();
    fd.append("subject_id", id);

    fetch(`${API}&action=delete`,{
        method:'POST',
        body:fd
    })
    .then(r=>r.json())
    .then(res=>{
        if(!res.success){
            alert(res.message);
            return;
        }
        loadSubjects(currentPage);
    });
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


/* ================= LOAD PREREQUISITE ================= */
function loadPrerequisites(){
    fetch(`${API}&action=getAll`)
        .then(r=>r.json())
        .then(res=>{
            if(!res.success) return;

            let html='<option value="">-- Không có --</option>';

            res.data.forEach(s=>{
                html += `<option value="${s.subject_code}">${s.subject_name}</option>`;
            });

            document.getElementById('prerequisite_code').innerHTML = html;
        });
}

/* ================= UTILITY ================= */
function openAddForm(){
    document.getElementById('subjectForm').reset();
    document.getElementById('subject_id').value='';
    document.getElementById('modalTitle').innerText="Thêm môn";
}

function edit(id){
    fetch(`${API}&action=show&id=${id}`)
        .then(r=>r.json())
        .then(res=>{
            if(!res.success) return;

            const s = res.data;
            for(let key in s){
                if(document.getElementById(key)){
                    document.getElementById(key).value = s[key] ?? '';
                }
            }

            document.getElementById('modalTitle').innerText="Chỉnh sửa môn";
            new bootstrap.Modal(document.getElementById('subjectModal')).show();
        });
}

function clearSearch(){
    document.getElementById('searchInput').value='';
    document.getElementById('facultyFilter').value='';
    loadSubjects(1);
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
