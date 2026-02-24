<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="topbar">
        <h2>Quản lý Tài khoản Sinh viên</h2>
        <a href="/web_QLSV/admin/views/students/index.php" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Tìm Sinh viên</label>
            <input type="text" id="searchInput" class="form-control" placeholder="MSSV, Họ tên, Email...">
        </div>
        <div class="col-md-3">
            <label class="form-label">Lọc theo</label>
            <select id="filterSelect" class="form-select">
                <option value="">-- Tất cả --</option>
                <option value="has_account">Có tài khoản</option>
                <option value="no_account">Chưa có tài khoản</option>
                <option value="locked">Khóa</option>
            </select>
        </div>
    </div>

    <div class="card p-3">
        <div id="alertArea"></div>
        <table class="table table-striped table-sm" id="accountsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>MSSV</th>
                    <th>Họ Tên</th>
                    <th>Email</th>
                    <th>Tài khoản</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="accountsBody">
                <!-- populated by JS -->
            </tbody>
        </table>
    </div>

    <!-- Create Account Modal -->
    <div class="modal fade" id="createAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo Tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createAccountForm">
                    <div class="modal-body">
                        <input type="hidden" id="createAccount_student_id">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" id="createAccount_username" class="form-control" required>
                            <small class="text-muted">Thường là MSSV hoặc email</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu tạm</label>
                            <input type="password" id="createAccount_password" class="form-control" required>
                            <small class="text-muted">Sinh viên phải đổi khi lần đăng nhập đầu</small>
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

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Mật khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="resetPasswordForm">
                    <div class="modal-body">
                        <input type="hidden" id="resetPassword_student_id">
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" id="resetPassword_new_password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Reset</button>
                    </div>
                </form>
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
            if (j.success) { students = j.data; renderAccounts(); }
            else showAlert(j.message||'Lỗi', 'danger');
        } catch (e) { console.warn('fetchStudents: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

function renderAccounts(){
    const tbody = document.getElementById('accountsBody');
    tbody.innerHTML = '';
    const filter = document.getElementById('filterSelect').value;

    students.forEach((s, idx) => {
        // Apply filter
        if (filter === 'has_account' && !s.user_id) return;
        if (filter === 'no_account' && s.user_id) return;

        const tr = document.createElement('tr');
        const accountBadge = s.user_id 
            ? '<span class="badge bg-success">✓ Có</span>' 
            : '<span class="badge bg-secondary">✗ Không</span>';
        
        const statusBadge = s.user_id
            ? '<span class="badge bg-success">Hoạt động</span>'
            : '<span class="badge bg-secondary">N/A</span>';

        tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>${escapeHtml(s.student_code)}</td>
            <td>${escapeHtml(s.first_name + ' ' + s.last_name)}</td>
            <td>${escapeHtml(s.email)}</td>
            <td>${accountBadge}</td>
            <td>${statusBadge}</td>
            <td>
                ${!s.user_id ? `<button class="btn btn-sm btn-success" onclick="openCreateAccount(${s.student_id})">Tạo TK</button>` : ''}
                ${s.user_id ? `<button class="btn btn-sm btn-warning" onclick="openResetPassword(${s.student_id})">Reset MK</button>` : ''}
                ${s.user_id ? `<button class="btn btn-sm btn-danger" onclick="toggleLockAccount(${s.student_id})">Khóa/Mở</button>` : ''}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function escapeHtml(str){ return String(str).replace(/[&<>\"]/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' })[s]); }

function openCreateAccount(studentId){
    document.getElementById('createAccount_student_id').value = studentId;
    const student = students.find(s => s.student_id == studentId);
    if (student) {
        document.getElementById('createAccount_username').value = student.student_code;
    }
    const modal = new bootstrap.Modal(document.getElementById('createAccountModal'));
    modal.show();
}

function openResetPassword(studentId){
    document.getElementById('resetPassword_student_id').value = studentId;
    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}

async function toggleLockAccount(studentId){
    if (!confirm('Khóa/Mở khóa tài khoản này?')) return;
    
    const student = students.find(s => s.student_id == studentId);
    if (!student || !student.user_id) {
        showAlert('Sinh viên không có tài khoản', 'danger');
        return;
    }

    const params = new URLSearchParams();
    params.append('module', 'students');
    params.append('action', 'lockAccount');
    params.append('student_id', studentId);
    params.append('is_locked', 1); // Toggle - simplified for now

    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert(j.message);
                fetchStudents();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('toggleLockAccount: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
}

document.getElementById('createAccountForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const studentId = document.getElementById('createAccount_student_id').value;
    const username = document.getElementById('createAccount_username').value;
    const password = document.getElementById('createAccount_password').value;

    if (!username || !password) {
        showAlert('Vui lòng nhập tên đăng nhập và mật khẩu', 'warning');
        return;
    }

    const params = new URLSearchParams();
    params.append('module', 'students');
    params.append('action', 'createAccount');
    params.append('student_id', studentId);
    params.append('username', username);
    params.append('password', password);

    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert('Tạo tài khoản thành công');
                bootstrap.Modal.getInstance(document.getElementById('createAccountModal')).hide();
                document.getElementById('createAccountForm').reset();
                fetchStudents();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('createAccountForm: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
});

document.getElementById('resetPasswordForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const studentId = document.getElementById('resetPassword_student_id').value;
    const newPassword = document.getElementById('resetPassword_new_password').value;

    if (!newPassword) {
        showAlert('Vui lòng nhập mật khẩu mới', 'warning');
        return;
    }

    const params = new URLSearchParams();
    params.append('module', 'students');
    params.append('action', 'resetPassword');
    params.append('student_id', studentId);
    params.append('new_password', newPassword);

    try{
        const res = await fetch(apiUrl, { method: 'POST', body: params, credentials: 'same-origin' });
        const text = await res.text();
        try {
            const j = JSON.parse(text);
            if (j.success) {
                showAlert('Reset mật khẩu thành công');
                bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
                document.getElementById('resetPasswordForm').reset();
                fetchStudents();
            } else showAlert(j.message || 'Lỗi', 'danger');
        } catch (e) { console.warn('resetPasswordForm: invalid JSON'); }
    }catch(e){ console.error(e); showAlert('Lỗi mạng', 'danger'); }
});

document.getElementById('searchInput').addEventListener('input', function(e){
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchStudents(e.target.value);
    }, 300);
});

document.getElementById('filterSelect').addEventListener('change', function(){
    renderAccounts();
});

// Init
fetchStudents();
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
