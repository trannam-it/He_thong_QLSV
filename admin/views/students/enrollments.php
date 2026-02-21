<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="topbar">
        <h2>Đăng ký Học phần</h2>
        <a href="/web_QLSV/admin/views/students/index.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Chọn Lớp</label>
            <select id="classSelect" class="form-select">
                <option value="">-- Chọn lớp --</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Chọn Môn học</label>
            <select id="subjectSelect" class="form-select">
                <option value="">-- Chọn môn --</option>
            </select>
        </div>
    </div>

    <div class="card p-3 mb-4">
        <h5 class="card-title">Danh sách Sinh viên Đăng ký</h5>
        <div id="alertArea"></div>
        <table class="table table-striped table-sm" id="enrollmentsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>MSSV</th>
                    <th>Họ Tên</th>
                    <th>Lớp</th>
                    <th>Môn học</th>
                    <th>Ngày đăng ký</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="enrollmentsBody">
                <!-- populated by JS -->
            </tbody>
        </table>
    </div>

</div>

<script>
const apiUrl = '/web_QLSV/admin/api/router.php';
let classes = [], subjects = [], enrollments = [];

function showAlert(message, type='success'){
    const area = document.getElementById('alertArea');
    area.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
}

async function fetchClasses(){
    try{
        const res = await fetch(`${apiUrl}?module=classes&action=index&page=1&limit=500`, { credentials: 'same-origin' });
        if (!res.ok) return;
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j && j.success) {
                classes = j.data;
                populateClassSelect();
            }
        } catch (e) { console.warn('fetchClasses: invalid JSON'); }
    }catch(e){ console.error(e); }
}

async function fetchSubjects(){
    try{
        const res = await fetch(`${apiUrl}?module=subjects&action=index&page=1&limit=500`, { credentials: 'same-origin' });
        if (!res.ok) return;
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j && j.success) {
                subjects = j.data;
                populateSubjectSelect();
            }
        } catch (e) { console.warn('fetchSubjects: invalid JSON'); }
    }catch(e){ console.error(e); }
}

function populateClassSelect(){
    const sel = document.getElementById('classSelect');
    sel.innerHTML = '<option value="">-- Chọn lớp --</option>';
    classes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.class_id;
        opt.textContent = `${c.class_code} - ${c.subject_name || ''} (HK${c.semester}, Năm ${c.year})`;
        sel.appendChild(opt);
    });
}

function populateSubjectSelect(){
    const sel = document.getElementById('subjectSelect');
    sel.innerHTML = '<option value="">-- Chọn môn --</option>';
    subjects.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.subject_id;
        opt.textContent = `${s.subject_code} - ${s.subject_name}`;
        sel.appendChild(opt);
    });
}

async function fetchEnrollments(){
    const classId = document.getElementById('classSelect').value;
    if (!classId) {
        document.getElementById('enrollmentsBody').innerHTML = '';
        return;
    }

    try{
        const res = await fetch(`${apiUrl}?module=classes&action=getEnrollments&id=${classId}&page=1&limit=500`, { credentials: 'same-origin' });
        if (!res.ok) { showAlert('Lỗi khi tải đăng ký', 'danger'); return; }
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) { 
                enrollments = j.data; 
                renderEnrollments(); 
            }
            else showAlert(j.message||'Lỗi', 'danger');
        } catch (e) { console.warn('fetchEnrollments: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

function renderEnrollments(){
    const tbody = document.getElementById('enrollmentsBody');
    tbody.innerHTML = '';
    enrollments.forEach((e, idx) => {
        const tr = document.createElement('tr');
        const enrollDate = new Date(e.created_at).toLocaleDateString('vi-VN');
        
        tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>${escapeHtml(e.student_code)}</td>
            <td>${escapeHtml(e.first_name + ' ' + e.last_name)}</td>
            <td>${escapeHtml(e.class_code || '')}</td>
            <td>${escapeHtml(e.subject_name || '')}</td>
            <td>${enrollDate}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="cancelEnrollment(${e.enrollment_id})">Hủy</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function escapeHtml(str){ return String(str).replace(/[&<>\"]/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' })[s]); }

async function cancelEnrollment(enrollmentId){
    if (!confirm('Hủy đăng ký này?')) return;
    
    const params = new URLSearchParams();
    params.append('module', 'enrollments');
    params.append('action', 'delete');
    params.append('id', enrollmentId);

    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert('Đã hủy đăng ký');
                fetchEnrollments();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('cancelEnrollment: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

document.getElementById('classSelect').addEventListener('change', function(){
    fetchEnrollments();
});

// Init
fetchClasses();
fetchSubjects();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
