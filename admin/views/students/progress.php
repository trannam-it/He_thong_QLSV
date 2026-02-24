<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="topbar">
        <h2>Tiến độ Học tập Sinh viên</h2>
        <a href="/web_QLSV/admin/views/students/index.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Tìm Sinh viên</label>
            <input type="text" id="searchInput" class="form-control" placeholder="MSSV, Họ tên...">
        </div>
    </div>

    <div class="card p-3">
        <div id="alertArea"></div>
        <table class="table table-striped table-sm" id="progressTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>MSSV</th>
                    <th>Họ Tên</th>
                    <th>Khoa</th>
                    <th>Tín chỉ đạt</th>
                    <th>GPA</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="progressBody">
                <!-- populated by JS -->
            </tbody>
        </table>
    </div>

    <!-- Progress Detail Modal -->
    <div class="modal fade" id="progressDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết Tiến độ Học tập</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Thông tin Sinh viên</h6>
                            <p><strong>Họ tên:</strong> <span id="detail_name"></span></p>
                            <p><strong>MSSV:</strong> <span id="detail_code"></span></p>
                            <p><strong>Khoa:</strong> <span id="detail_faculty"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Thống kê</h6>
                            <p><strong>Tín chỉ đạt yêu cầu:</strong> <span id="detail_required_credits">0</span></p>
                            <p><strong>Tín chỉ hoàn thành:</strong> <span id="detail_completed_credits">0</span></p>
                            <p><strong>GPA:</strong> <span id="detail_gpa" style="font-size: 1.2em; color: #495696;">0.00</span></p>
                        </div>
                    </div>
                    <hr>
                    <h6>Danh sách Môn học</h6>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Môn học</th>
                                <th>Tín chỉ</th>
                                <th>Điểm</th>
                                <th>Xếp loại</th>
                            </tr>
                        </thead>
                        <tbody id="detail_transcript">
                            <!-- populated by JS -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
const apiUrl = '/web_QLSV/admin/api/router.php';
let students = [];
let searchTimeout;

function showAlert(message, type='success'){
    const area = document.getElementById('alertArea');
    area.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
}

async function fetchStudents(search=''){
    try{
        let url = `${apiUrl}?module=students&action=index&page=1&limit=500`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) { showAlert('Lỗi khi tải sinh viên', 'danger'); return; }
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) { students = j.data; renderProgressTable(); }
            else showAlert(j.message||'Lỗi', 'danger');
        } catch (e) { console.warn('fetchStudents: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

function renderProgressTable(){
    const tbody = document.getElementById('progressBody');
    tbody.innerHTML = '';
    students.forEach((s, idx) => {
        const tr = document.createElement('tr');
        // Dummy data for now - in real implementation, calculate from grades table
        const credits = Math.floor(Math.random() * 80) + 20; // 20-100
        const gpa = (Math.random() * 4).toFixed(2);
        
        let status = '✓ Bình thường';
        let statusColor = 'success';
        if (gpa < 2.0) { status = '⚠️ Cảnh báo'; statusColor = 'danger'; }
        else if (gpa < 2.5) { status = '⚠️ Liên hệ'; statusColor = 'warning'; }
        
        tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>${escapeHtml(s.student_code)}</td>
            <td>${escapeHtml(s.first_name + ' ' + s.last_name)}</td>
            <td>${escapeHtml(s.faculty_name || '')}</td>
            <td>${credits}</td>
            <td><strong>${gpa}</strong></td>
            <td><span class="badge bg-${statusColor}">${status}</span></td>
            <td>
                <button class="btn btn-sm btn-info" onclick="openProgressDetail(${s.student_id})">Chi tiết</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function escapeHtml(str){ return String(str).replace(/[&<>\"]/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' })[s]); }

async function openProgressDetail(studentId){
    try{
        const res = await fetch(`${apiUrl}?module=students&action=getTranscript&student_id=${studentId}`, { credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (!j.success) { showAlert(j.message||'Lỗi','danger'); return; }
            
            const data = j.data;
            const student = students.find(s => s.student_id == studentId);
            
            document.getElementById('detail_name').textContent = `${student.first_name} ${student.last_name}`;
            document.getElementById('detail_code').textContent = student.student_code;
            document.getElementById('detail_faculty').textContent = student.faculty_name || '';
            document.getElementById('detail_completed_credits').textContent = data.statistics?.completed_credits || 0;
            document.getElementById('detail_gpa').textContent = data.statistics?.gpa || '0.00';
            
            // Render transcript
            const tbody = document.getElementById('detail_transcript');
            tbody.innerHTML = '';
            data.grades.forEach(g => {
                const tr = document.createElement('tr');
                let xepLoai = '?';
                if (g.final_grade) {
                    if (g.final_grade >= 8.5) xepLoai = 'A';
                    else if (g.final_grade >= 7.0) xepLoai = 'B';
                    else if (g.final_grade >= 5.5) xepLoai = 'C';
                    else xepLoai = 'F';
                }
                tr.innerHTML = `
                    <td>${escapeHtml(g.subject_name)}</td>
                    <td>3</td>
                    <td>${g.final_grade ? g.final_grade : '-'}</td>
                    <td>${xepLoai}</td>
                `;
                tbody.appendChild(tr);
            });
            
            const modal = new bootstrap.Modal(document.getElementById('progressDetailModal'));
            modal.show();
        } catch (e) { console.warn('openProgressDetail: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng','danger'); }
}

document.getElementById('searchInput').addEventListener('input', function(e){
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchStudents(e.target.value);
    }, 300);
});

// Init
fetchStudents();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
