<?php
$pageTitle = "Quản lý điểm số";
include_once __DIR__ . '/../layout/header.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Danh sách điểm sinh viên</strong>
        <div>
            <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel"></i> Import Excel
            </button>
            <button class="btn btn-primary btn-sm" onclick="openAddModal()">
                <i class="bi bi-plus-circle"></i> Thêm điểm
            </button>
        </div>
    </div>

    <div class="card-body">
        <table id="gradeTable" class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Môn học</th>
                    <th>Điểm</th>
                    <th>Xếp loại</th>
                    <th>Trạng thái</th>
                    <th width="140">Thao tác</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- MODAL THÊM / SỬA ĐIỂM -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="gradeForm">
            <div class="modal-header">
                <h5 class="modal-title">Nhập điểm</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="grade_id">

               <div class="mb-3">
                    <label class="form-label">Lớp học phần</label>
                    <select id="class_id" class="form-select" required></select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sinh viên</label>
                    <select id="enrollment_id" class="form-select" required></select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Điểm (0 – 10)</label>
                    <input type="number" step="0.1" min="0" max="10" id="score" class="form-control" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="importForm">
            <div class="modal-header">
                <h5 class="modal-title">Import điểm từ Excel</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="file" id="excelFile" class="form-control" accept=".xlsx" required>
                <small class="text-muted mt-2 d-block">
                    File gồm 2 cột: <b>enrollment_id</b> | <b>score</b>
                </small>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-success">Import</button>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>

<script>
let gradeModal = new bootstrap.Modal(document.getElementById('gradeModal'));

$(document).ready(function () {
    loadGrades();
});

/* =============================
   LOAD DANH SÁCH ĐIỂM
============================= */
async function loadGrades() {
    const res = await apiCall('grades', 'index');
    if (!res.success) {
        showToast(res.message, 'error');
        return;
    }

    const tbody = $('#gradeTable tbody');
    tbody.empty();

    res.data.forEach(g => {
        let lockBadge = g.is_locked == 1
            ? '<span class="badge bg-danger">Đã khóa</span>'
            : '<span class="badge bg-success">Mở</span>';

        let actionBtns = `
            <button class="btn btn-sm btn-warning me-1" onclick="editGrade(${g.grade_id}, ${g.enrollment_id}, ${g.score})" ${g.is_locked == 1 ? 'disabled' : ''}>
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger me-1" onclick="confirmDelete('grades', ${g.grade_id}, 'điểm này')" ${g.is_locked == 1 ? 'disabled' : ''}>
                <i class="bi bi-trash"></i>
            </button>
        `;

        if (g.is_locked == 0) {
            actionBtns += `
                <button class="btn btn-sm btn-secondary" onclick="lockGrade(${g.grade_id})">
                    <i class="bi bi-lock"></i>
                </button>
            `;
        }

        tbody.append(`
            <tr>
                <td>${g.student_code}</td>
                <td>${g.last_name} ${g.first_name}</td>
                <td>${g.subject_name}</td>
                <td>${g.score}</td>
                <td><span class="badge bg-info">${g.grade_letter}</span></td>
                <td>${lockBadge}</td>
                <td>${actionBtns}</td>
            </tr>
        `);
    });
}

/* =============================
   ADD / EDIT
============================= */
function openAddModal() {
    $('#gradeForm')[0].reset();
    $('#grade_id').val('');
    gradeModal.show();
}

function editGrade(id, enrollmentId, score) {
    $('#grade_id').val(id);
    $('#enrollment_id').val(enrollmentId);
    $('#score').val(score);
    gradeModal.show();
}

$('#gradeForm').submit(async function (e) {
    e.preventDefault();

    const id = $('#grade_id').val();
    const data = {
        id: id,
        enrollment_id: $('#enrollment_id').val(),
        score: $('#score').val()
    };

    const action = id ? 'update' : 'store';
    const res = await apiCall('grades', action, 'POST', data);

    if (res.success) {
        showToast('Lưu điểm thành công');
        gradeModal.hide();
        loadGrades();
    } else {
        showToast(res.message, 'error');
    }
});

/* =============================
   IMPORT EXCEL
============================= */
$('#importForm').submit(async function (e) {
    e.preventDefault();

    const file = document.getElementById('excelFile').files[0];
    if (!file) return;

    let formData = new FormData();
    formData.append('file', file);

    try {
        const res = await fetch('/web_QLSV/admin/api/router.php?module=grades&action=import', {
            method: 'POST',
            body: formData
        }).then(r => r.json());

        if (res.success) {
            showToast('Import thành công');
            $('#importModal').modal('hide');
            loadGrades();
        } else {
            showToast(res.message, 'error');
        }
    } catch {
        showToast('Lỗi import', 'error');
    }
});

/* =============================
   LOCK GRADE
============================= */
async function lockGrade(id) {
    if (!confirm('Bạn chắc chắn muốn khóa điểm này?')) return;

    const res = await apiCall('grades', 'lock', 'POST', { id: id });
    if (res.success) {
        showToast('Đã khóa điểm');
        loadGrades();
    } else {
        showToast(res.message, 'error');
    }
}

async function loadClasses() {
    const res = await apiCall('classes', 'listForGrades');
    let html = '<option value="">-- Chọn lớp --</option>';
    res.data.forEach(c => {
        html += `<option value="${c.class_id}">
            ${c.subject_name} - ${c.class_name}
        </option>`;
    });
    $('#class_id').html(html);
}

$('#class_id').change(async function () {
    const classId = this.value;
    if (!classId) return;

    const res = await apiCall('grades', 'studentsByClass', 'GET', { class_id: classId });
    let html = '<option value="">-- Chọn sinh viên --</option>';
    res.data.forEach(s => {
        html += `<option value="${s.enrollment_id}">
            ${s.student_code} - ${s.full_name}
        </option>`;
    });
    $('#enrollment_id').html(html);
});

$semester = $this->db->selectOne('semesters', 'semester_id = ?', [$enrollment['semester_id']]);
if ($semester['is_locked']) {
    return Response::error('Học kỳ đã chốt điểm', 403);
}
loadClasses();

</script>
