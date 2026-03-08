<?php
$pageTitle = "Quản lý Giảng viên";
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';

// Lấy danh sách học vị từ định nghĩa ENUM trong DB (không in HTML trực tiếp)
$degrees = [];
if (isset($conn)) {
    $res = $conn->query("SHOW COLUMNS FROM lecturers LIKE 'degree'");
    if ($res && $row = $res->fetch_assoc()) {
        $type = $row['Type']; // ví dụ: enum('Bachelor','Master','PhD')
        if (preg_match("/^enum\\((.*)\\)$/", $type, $m)) {
            $vals = explode(',', $m[1]);
            foreach ($vals as $v) {
                $degrees[] = trim($v, "'\"");
            }
        }
    }
}
?>

<div class="container-fluid">

<!-- ================= HEADER ================= -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">
            <i class="bi bi-person-workspace text-primary"></i>
            Quản lý Giảng viên
        </h3>
        <small class="text-muted">Quản lý thông tin và thống kê giảng viên</small>
    </div>

        <a href="<?= BASE_URL ?>/admin/views/lecturers/statistics.php" 
            class="btn btn-success">
            <i class="bi bi-bar-chart"></i> Thống kê
        </a>


        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> Thêm Giảng viên mới
        </button>
    </div>
</div>

<!-- ================= QUICK STATS ================= -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Tổng Giảng viên</h6>
                <h3 id="totalLecturers" class="fw-bold text-primary">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Đang giảng dạy</h6>
                <h3 id="teachingCount" class="fw-bold text-success">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Đang công tác</h6>
                <h3 id="activeCount" class="fw-bold text-info">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Tổng Khoa</h6>
                <h3 id="totalFaculties" class="fw-bold text-dark">0</h3>
            </div>
        </div>
    </div>
</div>

<!-- ================= FILTER ================= -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2">

            <div class="col-md-3">
                <input type="text" id="searchInput"
                       class="form-control"
                       placeholder="Tìm theo mã hoặc tên">
            </div>

            <div class="col-md-2">
                <select id="facultyFilter" class="form-select">
                    <option value="">-- Tất cả khoa --</option>
                </select>
            </div>

            <div class="col-md-2">
                <select id="degreeFilter" class="form-select">
                <option value="">-- Học vị --</option>
                    
                </select>
            </div> 

            <div class="col-md-2">
                <select id="sortOrder" class="form-select">
                    <option value="DESC">Giảm dần</option>
                    <option value="ASC">Tăng dần</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-100" onclick="loadLecturers(1)">
                    Tìm
                </button>
                <button class="btn btn-secondary w-100" onclick="resetFilter()">
                    Reset
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ================= TABLE ================= -->
<div class="card shadow-sm">
    <div class="card-body">

        <div id="alertArea"></div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã GV</th>
                        <th>Họ Tên</th>
                        <th>Email</th>
                        <th>Khoa</th>
                        <th>Học vị</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody id="lecturersBody"></tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <ul class="pagination pagination-sm" id="pagination"></ul>
        </div>

    </div>
</div>

</div>

<!-- ================= CREATE MODAL ================= -->
<!-- <div class="modal fade" id="createModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
    <h5>Thêm Giảng viên</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form id="createForm">
<div class="modal-body">
<div class="row">

<div class="col-md-6">
    <div class="mb-3">
        <label>Mã giảng viên</label>
        <input name="lecturer_code" id="create_code" class="form-control" readonly>
    </div>
    <div class="mb-3">
        <label>Họ</label>
        <input name="first_name" id="create_first" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Tên</label>
        <input name="last_name" id="create_last" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input name="email" id="create_email" type="email" class="form-control" required>
    </div>
</div>

    <div class="col-md-6">
    <div class="mb-3">
        <label>Học vị</label>
        <select name="degree" id="create_degree" class="form-select" required>
            <option value="">-- Chọn Học vị --</option>
           
        </select>
    </div>
    <div class="mb-3">
        <label>Khoa</label>
        <select name="faculty_id" id="create_faculty" class="form-select" required>
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
</div> -->

<!-- ================= CREATE MODAL ================= -->
<div class="modal fade" id="createModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
    <h5 class="fw-bold">Thêm Giảng viên mới</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form id="createForm">
<div class="modal-body">

    <div class="row">

        <!-- ===== THÔNG TIN CÁ NHÂN ===== -->
        <div class="col-md-6 border-end">
            <h6 class="text-primary fw-bold mb-3">
                <i class="bi bi-person"></i> Thông tin cá nhân
            </h6>

            <div class="mb-3">
                <label>Mã giảng viên</label>
                <input name="lecturer_code"
                       id="create_code"
                       class="form-control"
                       readonly>
            </div>

            <div class="mb-3">
                <label>Họ</label>
                <input name="first_name"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label>Tên</label>
                <input name="last_name"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input name="email"
                       type="email"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label>Số điện thoại</label>
                <input name="phone"
                       class="form-control">
            </div>
        </div>

        <!-- ===== THÔNG TIN CHUYÊN MÔN ===== -->
        <div class="col-md-6">
            <h6 class="text-success fw-bold mb-3">
                <i class="bi bi-mortarboard"></i> Thông tin chuyên môn
            </h6>

            <div class="mb-3">
                <label>Học vị</label>
                <select name="degree"
                        id="create_degree"
                        class="form-select"
                        required>
                </select>
            </div>

            <div class="mb-3">
                <label>Khoa</label>
                <select name="faculty_id"
                        id="create_faculty"
                        class="form-select"
                        required>
                </select>
            </div>

            <div class="alert alert-info mt-4">
                <strong>Lưu ý:</strong><br>
                Tài khoản đăng nhập sẽ được tạo tự động.<br>
                Username = Mã GV<br>
                Password mặc định = 123456
            </div>

        </div>

    </div>

</div>

<div class="modal-footer">
    <button type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal">Đóng</button>

    <button type="submit"
            class="btn btn-primary">Tạo giảng viên</button>
</div>

</form>
</div>
</div>
</div>


<!-- ================= SCRIPT ================= -->
<script>

const apiUrl = "<?= BASE_URL ?>/admin/api/router.php";

let faculties = [];
let degrees = <?= json_encode($degrees) ?> || [];
let lecturers = [];
let currentPage = 1;
let totalPages = 1;
// ================= ALERT =================
function showAlert(message, type='success'){
    document.getElementById('alertArea').innerHTML =
        `<div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
}

// ================= FETCH FACULTIES =================

async function fetchFaculties(){
    const res = await fetch(`${apiUrl}?resource=faculties&action=index&page=1&limit=500`);
    const j = await res.json();

    if(j.success){
        faculties = j.data;
        document.getElementById("totalFaculties").innerText = faculties.length;
        populateFacultySelects();
    }
}

function populateFacultySelects(){
    const selects = document.querySelectorAll('[name="faculty_id"], #facultyFilter');

    selects.forEach(sel=>{
        const val = sel.value;
        sel.innerHTML = '<option value="">-- Chọn khoa --</option>';

        faculties.forEach(f=>{
            sel.innerHTML += `<option value="${f.faculty_id}">${f.faculty_name}</option>`;
        });

        sel.value = val;
    });
}

// ================= DEGREES (từ PHP enum hoặc API dữ liệu)
function populateDegreeSelects(){
    const selects = document.querySelectorAll('#degreeFilter, #create_degree, #edit_degree');

    selects.forEach(sel=>{
        const val = sel.value;
        sel.innerHTML = '<option value="">-- Chọn học vị --</option>';

        degrees.forEach(d=>{
            if (typeof d === 'object' && d !== null) {
                // object form from API: { degree_id, degree_name }
                sel.innerHTML += `<option value="${d.degree_id}">${d.degree_name}</option>`;
            } else {
                // enum string form from PHP: 'Master', 'PhD', ...
                const v = String(d);
                sel.innerHTML += `<option value="${v}">${v}</option>`;
            }
        });

        sel.value = val;
    });
}

// ================= NEXT CODE =================
async function fetchNextCode(){
    try{
        const res = await fetch(`${apiUrl}?resource=lecturers&action=nextCode`);
        const j = await res.json();
        if(j.success && j.data && j.data.next_code){
            const el = document.getElementById('create_code');
            if(el) el.value = j.data.next_code;
        }
    }catch(e){
        console.error('Failed to fetch next code', e);
    }
}

// ================= EDIT MODAL =================
function openEditModal(id){
    fetch(`${apiUrl}?resource=lecturers&action=show&id=${id}`)
    .then(r=>r.json())
    .then(j=>{
        if(j.success){
            const l = j.data;
            document.getElementById('edit_id').value = l.lecturer_id;
            document.getElementById('edit_code').value = l.lecturer_code;
            document.getElementById('edit_first').value = l.first_name;
            document.getElementById('edit_last').value = l.last_name;
            document.getElementById('edit_email').value = l.email;
            document.getElementById('edit_phone').value = l.phone || '';
            document.getElementById('edit_degree').value = l.degree || '';
            document.getElementById('edit_faculty').value = l.faculty_id || '';
            new bootstrap.Modal(document.getElementById('editModal')).show();
        } else {
            showAlert(j.message,'danger');
        }
    });
}


// ================= LOAD LECTURERS =================

async function loadLecturers(page=1){

    currentPage = page;

    const search = document.getElementById("searchInput").value;
    const faculty = document.getElementById("facultyFilter").value;
    const degree = document.getElementById("degreeFilter").value;
    const sort = document.getElementById("sortOrder").value;

    let url = `${apiUrl}?resource=lecturers&action=index&page=${page}&limit=10&sort=${sort}`;

    if(search) url += `&search=${encodeURIComponent(search)}`;
    if(faculty) url += `&faculty_id=${faculty}`;
    // if(degree) url += `&degree_id=${degree}`;
    if(degree) url += `&degree=${degree}`;


    const res = await fetch(url);
    const j = await res.json();

   if(j.success){
    lecturers = j.data;
    totalPages = parseInt(j.pagination.pages);
    currentPage = parseInt(j.pagination.page);

    document.getElementById("totalLecturers").innerText = j.pagination.total;

    renderLecturers();
    renderPagination();
}
    else {
        showAlert(j.message,'danger');
    }
}


// ================= RENDER =================


function renderLecturers(){
    const tbody = document.getElementById("lecturersBody");
    tbody.innerHTML = "";

    lecturers.forEach(l=>{
        tbody.innerHTML += `
        <tr>
            <td>${l.lecturer_code}</td>
            <td>${l.first_name} ${l.last_name}</td>
            <td>${l.email}</td>
            <td>${l.faculty_name || ''}</td>
            <td>${l.degree || ''}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-secondary me-1" onclick="openEditModal(${l.lecturer_id})">Sửa</button>
                <button class="btn btn-sm btn-danger" onclick="deleteLecturer(${l.lecturer_id})">Xóa</button>
            </td>
        </tr>`;
    });
}

// ================= PAGINATION =================
function renderPagination(){
    const ul = document.getElementById("pagination");
    ul.innerHTML = "";

    for(let i=1;i<=totalPages;i++){
        ul.innerHTML += `
        <li class="page-item ${i==currentPage?'active':''}">
            <a class="page-link" href="#"
               onclick="loadLecturers(${i})">${i}</a>
        </li>`;
    }
}

// ================= RESET FILTER =================
function resetFilter(){
    document.getElementById("searchInput").value="";
    document.getElementById("facultyFilter").value="";
    document.getElementById("degreeFilter").value="";
    loadLecturers(1);
}

// ================= CREATE =================
document.getElementById("createForm").addEventListener("submit", async function(e){
    e.preventDefault();

    const formData = new FormData(this);
    formData.append("resource","lecturers");
    formData.append("action","store");

    const res = await fetch(apiUrl,{
        method:"POST",
        body:formData
    });

    const j = await res.json();

    if(j.success){
        showAlert("Tạo thành công");
        bootstrap.Modal.getInstance(document.getElementById("createModal")).hide();
        this.reset();
        loadLecturers(currentPage);
    } else {
        showAlert(j.message,"danger");
    }
});

// ================= DELETE =================
async function deleteLecturer(id){
    if(!confirm("Xóa giảng viên này?")) return;

    const params = new URLSearchParams();
    params.append("resource","lecturers");
    params.append("action","delete");
    params.append("id",id);

    const res = await fetch(apiUrl,{
        method:"POST",
        body:params
    });

    const j = await res.json();

    if(j.success){
        showAlert("Đã xóa");
        loadLecturers(currentPage);
    } else {
        showAlert(j.message,"danger");
    }
}

// ================= INIT =================
document.addEventListener("DOMContentLoaded", async function(){
    await fetchFaculties();
    populateDegreeSelects();
    loadLecturers();
});

</script>

<!-- ========== EDIT MODAL ========== -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Chỉnh sửa Giảng viên</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Mã giảng viên</label>
                                <input name="lecturer_code" id="edit_code" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Họ</label>
                                <input name="first_name" id="edit_first" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Tên</label>
                                <input name="last_name" id="edit_last" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input name="email" id="edit_email" type="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Học vị</label>
                                <select name="degree" id="edit_degree" class="form-select" required></select>
                            </div>
                            <div class="mb-3">
                                <label>Khoa</label>
                                <select name="faculty_id" id="edit_faculty" class="form-select" required></select>
                            </div>
                            <div class="mb-3">
                                <label>Số điện thoại</label>
                                <input name="phone" id="edit_phone" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
// handle edit submit
document.getElementById('editForm').addEventListener('submit', async function(e){
        e.preventDefault();
        const form = new FormData(this);
        form.append('resource','lecturers');
        form.append('action','update');

        const res = await fetch(apiUrl, { method: 'POST', body: form });
        const j = await res.json();
        if(j.success){
                showAlert('Cập nhật thành công');
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                loadLecturers(currentPage);
        } else {
                showAlert(j.message,'danger');
        }
});

// Khi mở modal thì tự động lấy mã mới
document.getElementById('createModal')
.addEventListener('show.bs.modal', function () {
    fetchNextCode();
});

</script>
fetchFaculties()