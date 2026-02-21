<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="topbar">
        <h2>Quản lý Lớp Giảng viên</h2>
        <a href="/web_QLSV/admin/views/lecturers/index.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Chọn Giảng viên</label>
            <select id="lecturerSelect" class="form-select">
                <option value="">-- Chọn giảng viên --</option>
            </select>
        </div>
    </div>

    <div class="card p-3">
        <div id="alertArea"></div>
        <table class="table table-striped" id="classesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã Lớp</th>
                    <th>Môn Học</th>
                    <th>Khóa / Học kỳ</th>
                    <th>SV Đăng ký</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="classesBody">
                <!-- populated by JS -->
            </tbody>
        </table>
    </div>

    <!-- Reassign Lecturer Modal -->
    <div class="modal fade" id="reassignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gán lại Giảng viên cho Lớp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="reassignForm">
                    <div class="modal-body">
                        <input type="hidden" id="reassign_class_id">
                        <div class="mb-3">
                            <label class="form-label">Giảng viên</label>
                            <select id="reassign_lecturer" class="form-select" required>
                                <option value="">-- Chọn giảng viên --</option>
                            </select>
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

</div>

<script>
const apiUrl = '/web_QLSV/admin/api/router.php';
let lecturers = [], lecturerClasses = [];

function showAlert(message, type='success'){
    const area = document.getElementById('alertArea');
    area.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
}

async function fetchLecturers(){
    try{
        const res = await fetch(`${apiUrl}?module=lecturers&action=index&page=1&limit=500`, { credentials: 'same-origin' });
        if (!res.ok) return;
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j && j.success) {
                lecturers = j.data;
                populateLecturerSelect();
            }
        } catch (e) { console.warn('fetchLecturers: invalid JSON'); }
    }catch(e){ console.error(e); }
}

function populateLecturerSelect(){
    const selects = document.querySelectorAll('[id*="lecturer"]');
    selects.forEach(sel => {
        const val = sel.value;
        sel.innerHTML = '<option value="">-- Chọn giảng viên --</option>';
        lecturers.forEach(l => {
            const opt = document.createElement('option');
            opt.value = l.lecturer_id;
            opt.textContent = `${l.lecturer_code} - ${l.first_name} ${l.last_name}`;
            sel.appendChild(opt);
        });
        if (val) sel.value = val;
    });
}

async function fetchLecturerClasses(lecturerId){
    if (!lecturerId) {
        document.getElementById('classesBody').innerHTML = '';
        return;
    }

    try{
        const res = await fetch(`${apiUrl}?module=lecturers&action=getLecturerClasses&lecturer_id=${lecturerId}`, { credentials: 'same-origin' });
        if (!res.ok) { showAlert('Lỗi khi tải lớp', 'danger'); return; }
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) { lecturerClasses = j.data; renderClasses(); }
            else showAlert(j.message||'Lỗi', 'danger');
        } catch (e) { console.warn('fetchLecturerClasses: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

function renderClasses(){
    const tbody = document.getElementById('classesBody');
    tbody.innerHTML = '';
    lecturerClasses.forEach((c, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>${escapeHtml(c.class_code)}</td>
            <td>${escapeHtml(c.subject_name||'')}</td>
            <td>Năm ${escapeHtml(c.year)}, HK${escapeHtml(c.semester)}</td>
            <td>${c.student_count || 0}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="openReassign(${c.class_id})">Gán lại GV</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function escapeHtml(str){ return String(str).replace(/[&<>\"]/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' })[s]); }

async function openReassign(classId){
    document.getElementById('reassign_class_id').value = classId;
    const modal = new bootstrap.Modal(document.getElementById('reassignModal'));
    modal.show();
}

document.getElementById('lecturerSelect').addEventListener('change', function(e){
    fetchLecturerClasses(e.target.value);
});

document.getElementById('reassignForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const classId = document.getElementById('reassign_class_id').value;
    const lecturerId = document.getElementById('reassign_lecturer').value;

    if (!lecturerId) {
        showAlert('Vui lòng chọn giảng viên', 'warning');
        return;
    }

    const params = new URLSearchParams();
    params.append('module', 'classes');
    params.append('action', 'update');
    params.append('id', classId);
    params.append('lecturer_id', lecturerId);

    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert('Gán giảng viên thành công');
                bootstrap.Modal.getInstance(document.getElementById('reassignModal')).hide();
                const currentLecturerId = document.getElementById('lecturerSelect').value;
                if (currentLecturerId) fetchLecturerClasses(currentLecturerId);
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('reassignForm response invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
});

// Init
fetchLecturers();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
