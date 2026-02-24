<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="topbar">
        <h2>Quản lý Điểm Số</h2>
        <a href="/web_QLSV/admin/views/lecturers/index.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Chọn Lớp</label>
            <select id="classSelect" class="form-select">
                <option value="">-- Chọn lớp --</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Lọc theo</label>
            <select id="filterSelect" class="form-select">
                <option value="">-- Tất cả --</option>
                <option value="complete">Đã hoàn thành</option>
                <option value="incomplete">Chưa hoàn thành</option>
            </select>
        </div>
    </div>

    <div class="card p-3">
        <div id="alertArea"></div>
        <table class="table table-striped table-sm" id="gradesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>MSSV</th>
                    <th>Họ Tên Sinh viên</th>
                    <th>Điểm QT</th>
                    <th>Điểm Giữa kỳ</th>
                    <th>Điểm Cuối kỳ</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="gradesBody">
                <!-- populated by JS -->
            </tbody>
        </table>
    </div>

    <!-- Grade Detail Modal -->
    <div class="modal fade" id="gradeDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết Điểm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="gradeDetailForm">
                    <div class="modal-body">
                        <input type="hidden" id="detail_grade_id">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Sinh viên:</strong> <span id="detail_student_name"></span></p>
                                <p><strong>Lớp:</strong> <span id="detail_class_name"></span></p>
                                <p><strong>Môn học:</strong> <span id="detail_subject_name"></span></p>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <input type="text" id="detail_status" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Quá trình (0-10)</label>
                                    <input type="number" id="detail_qt_score" class="form-control" step="0.5" min="0" max="10">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Giữa kỳ (0-10)</label>
                                    <input type="number" id="detail_mid_score" class="form-control" step="0.5" min="0" max="10">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Cuối kỳ (0-10)</label>
                                    <input type="number" id="detail_final_score" class="form-control" step="0.5" min="0" max="10">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Điểm Tổng kết (tính tự động)</label>
                            <input type="number" id="detail_final_grade" class="form-control" readonly step="0.5">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="detail_is_locked">
                            <label class="form-check-label" for="detail_is_locked">
                                Khóa điểm (ngăn sửa đổi)
                            </label>
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

</div>

<script>
const apiUrl = '/web_QLSV/admin/api/router.php';
let classes = [], grades = [];

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

function populateClassSelect(){
    const sel = document.getElementById('classSelect');
    const val = sel.value;
    sel.innerHTML = '<option value="">-- Chọn lớp --</option>';
    classes.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.class_id;
        opt.textContent = `${c.class_code} - ${c.subject_name || ''} (HK${c.semester}, Năm ${c.year})`;
        sel.appendChild(opt);
    });
    if (val) sel.value = val;
}

async function fetchGradesByClass(classId){
    if (!classId) {
        document.getElementById('gradesBody').innerHTML = '';
        return;
    }

    try{
        const res = await fetch(`${apiUrl}?module=grades&action=index&class_id=${classId}&page=1&limit=500`, { credentials: 'same-origin' });
        if (!res.ok) { showAlert('Lỗi khi tải điểm', 'danger'); return; }
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) { 
                grades = j.data; 
                renderGrades(); 
            }
            else showAlert(j.message||'Lỗi', 'danger');
        } catch (e) { console.warn('fetchGradesByClass: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

function renderGrades(){
    const tbody = document.getElementById('gradesBody');
    tbody.innerHTML = '';
    const filter = document.getElementById('filterSelect').value;

    grades.forEach((g, idx) => {
        // Apply filter
        if (filter === 'complete' && !g.final_grade) return;
        if (filter === 'incomplete' && g.final_grade) return;

        const tr = document.createElement('tr');
        const status = g.final_grade ? '✓ Hoàn thành' : '⏳ Chưa hoàn thành';
        const statusClass = g.final_grade ? 'success' : 'warning';

        tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>${escapeHtml(g.student_code)}</td>
            <td>${escapeHtml(g.student_name)}</td>
            <td>${g.qt_score !== null ? g.qt_score : '-'}</td>
            <td>${g.mid_term_score !== null ? g.mid_term_score : '-'}</td>
            <td>${g.final_exam_score !== null ? g.final_exam_score : '-'}</td>
            <td><span class="badge bg-${statusClass}">${status}</span></td>
            <td>
                <button class="btn btn-sm btn-info" onclick="openGradeDetail(${g.grade_id})">Xem</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function escapeHtml(str){ return String(str).replace(/[&<>\"]/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' })[s]); }

async function openGradeDetail(gradeId){
    try{
        const res = await fetch(`${apiUrl}?module=grades&action=show&id=${gradeId}`, { credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (!j.success) { showAlert(j.message||'Không tìm thấy','danger'); return; }
            const g = j.data;

            document.getElementById('detail_grade_id').value = g.grade_id;
            document.getElementById('detail_student_name').textContent = g.student_name || '';
            document.getElementById('detail_class_name').textContent = g.class_code || '';
            document.getElementById('detail_subject_name').textContent = g.subject_name || '';
            document.getElementById('detail_status').value = g.final_grade ? 'Hoàn thành' : 'Chưa hoàn thành';
            document.getElementById('detail_qt_score').value = g.qt_score || '';
            document.getElementById('detail_mid_score').value = g.mid_term_score || '';
            document.getElementById('detail_final_score').value = g.final_exam_score || '';
            document.getElementById('detail_final_grade').value = g.final_grade || '';
            document.getElementById('detail_is_locked').checked = g.is_locked || false;

            const modal = new bootstrap.Modal(document.getElementById('gradeDetailModal'));
            modal.show();
        } catch (e) { console.warn('openGradeDetail: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
}

// Auto-calculate final grade
document.getElementById('detail_final_score').addEventListener('change', calculateFinalGrade);
document.getElementById('detail_mid_score').addEventListener('change', calculateFinalGrade);
document.getElementById('detail_qt_score').addEventListener('change', calculateFinalGrade);

function calculateFinalGrade(){
    const qt = parseFloat(document.getElementById('detail_qt_score').value) || 0;
    const mid = parseFloat(document.getElementById('detail_mid_score').value) || 0;
    const final = parseFloat(document.getElementById('detail_final_score').value) || 0;
    
    // Formula: QT 20% + Mid 30% + Final 50%
    const finalGrade = (qt * 0.2 + mid * 0.3 + final * 0.5).toFixed(2);
    document.getElementById('detail_final_grade').value = isNaN(finalGrade) ? '' : finalGrade;
}

document.getElementById('gradeDetailForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const gradeId = document.getElementById('detail_grade_id').value;

    const params = new URLSearchParams();
    params.append('module', 'grades');
    params.append('action', 'update');
    params.append('id', gradeId);
    params.append('qt_score', document.getElementById('detail_qt_score').value);
    params.append('mid_term_score', document.getElementById('detail_mid_score').value);
    params.append('final_exam_score', document.getElementById('detail_final_score').value);
    params.append('final_grade', document.getElementById('detail_final_grade').value);
    params.append('is_locked', document.getElementById('detail_is_locked').checked ? 1 : 0);

    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert('Cập nhật điểm thành công');
                bootstrap.Modal.getInstance(document.getElementById('gradeDetailModal')).hide();
                const classId = document.getElementById('classSelect').value;
                if (classId) fetchGradesByClass(classId);
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('gradeDetailForm response invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
});

document.getElementById('classSelect').addEventListener('change', function(e){
    fetchGradesByClass(e.target.value);
});

document.getElementById('filterSelect').addEventListener('change', function(){
    renderGrades();
});

// Init
fetchClasses();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
