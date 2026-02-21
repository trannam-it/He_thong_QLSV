<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/admin_helper.php';
include __DIR__ . '/../includes/alert.php';

authCheck(['super_admin', 'content_admin']);

$pageTitle = 'Quản lý Khoa';
$userId = $_SESSION['user_id'];

// ===== Handle Actions =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = trim($_POST['faculty_name'] ?? '');
        $code = trim($_POST['faculty_code'] ?? '');
        $dean = trim($_POST['dean_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($name) || empty($code)) {
            setFlash('error', 'Tên khoa và mã khoa là bắt buộc!');
        } else {
            if (addFaculty($conn, $code, $name, $dean, $email, $phone)) {
                setFlash('success', 'Thêm khoa thành công!');
            } else {
                setFlash('error', 'Mã khoa đã tồn tại!');
            }
        }
        header('Location: faculty.php');
        exit;
    }
    
    if ($action === 'edit') {
        $id = intval($_POST['faculty_id']);
        $name = trim($_POST['faculty_name'] ?? '');
        $code = trim($_POST['faculty_code'] ?? '');
        $dean = trim($_POST['dean_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($name) || empty($code)) {
            setFlash('error', 'Tên khoa và mã khoa là bắt buộc!');
        } else {
            if (updateFaculty($conn, $id, $code, $name, $dean, $email, $phone)) {
                setFlash('success', 'Cập nhật khoa thành công!');
            } else {
                setFlash('error', 'Cập nhật khoa thất bại!');
            }
        }
        header('Location: faculty.php');
        exit;
    }
    
    if ($action === 'delete') {
        $id = intval($_POST['faculty_id']);
        $result = deleteFaculty($conn, $id);
        
        if ($result['success']) {
            setFlash('success', 'Xóa khoa thành công!');
        } else {
            setFlash('error', $result['message']);
        }
        header('Location: faculty.php');
        exit;
    }
}

// ===== Get faculty data =====
$faculties = getAllFaculties($conn);

include __DIR__ . '/views/layout/header.php';
?>

<style>
    .action-btn {
        padding: 6px 12px;
        font-size: 0.875rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-edit {
        background: #36b9cc;
        color: white;
    }
    .btn-edit:hover {
        background: #2c9faf;
    }
    .btn-delete {
        background: #e74a3b;
        color: white;
    }
    .btn-delete:hover {
        background: #c92a2a;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s;
    }
    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideDown 0.3s;
    }
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }
    .modal-close:hover {
        background: #f0f0f0;
        color: #000;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #eee;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    .form-label .required {
        color: #e74a3b;
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s;
    }
    .form-control:focus {
        outline: none;
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
    }
    .btn-primary {
        background: #4e73df;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-primary:hover {
        background: #2e59d9;
    }
    .btn-secondary {
        background: #858796;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-secondary:hover {
        background: #60616f;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .faculty-stats {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }
    .faculty-stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        color: #666;
    }
    .faculty-stat-item i {
        font-size: 1rem;
    }
</style>

<div class="container-fluid">
    
    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">🏢 Quản lý Khoa</h2>
            <p class="text-muted mb-0">Danh sách các khoa trong trường</p>
        </div>
        <button class="btn btn-primary" onclick="openAddModal()">
            <i class="bi bi-plus-circle me-2"></i>Thêm khoa mới
        </button>
    </div>

    <!-- FACULTIES TABLE -->
    <div class="content-card">
        <div class="content-card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã khoa</th>
                        <th>Tên khoa</th>
                        <th>Trưởng khoa</th>
                        <th>Liên hệ</th>
                        <th>Thống kê</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($faculties)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Chưa có dữ liệu khoa
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($faculties as $index => $fac): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= htmlspecialchars($fac['faculty_code']) ?></strong></td>
                            <td><?= htmlspecialchars($fac['faculty_name']) ?></td>
                            <td><?= htmlspecialchars($fac['dean_name'] ?? '—') ?></td>
                            <td>
                                <?php if (!empty($fac['email'])): ?>
                                    <div><i class="bi bi-envelope"></i> <?= htmlspecialchars($fac['email']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($fac['phone'])): ?>
                                    <div><i class="bi bi-telephone"></i> <?= htmlspecialchars($fac['phone']) ?></div>
                                <?php endif; ?>
                                <?php if (empty($fac['email']) && empty($fac['phone'])): ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="faculty-stats">
                                    <span class="faculty-stat-item">
                                        <i class="bi bi-mortarboard text-primary"></i>
                                        <strong><?= $fac['student_count'] ?? 0 ?></strong> SV
                                    </span>
                                    <span class="faculty-stat-item">
                                        <i class="bi bi-person-badge text-success"></i>
                                        <strong><?= $fac['lecturer_count'] ?? 0 ?></strong> GV
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="action-btn btn-edit" onclick='openEditModal(<?= json_encode($fac) ?>)'>
                                        <i class="bi bi-pencil"></i> Sửa
                                    </button>
                                    <button class="action-btn btn-delete" onclick="confirmDelete(<?= $fac['faculty_id'] ?>, '<?= htmlspecialchars($fac['faculty_name'], ENT_QUOTES) ?>')">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ADD MODAL -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="modal-header">
                <h3 class="modal-title">Thêm khoa mới</h3>
                <button type="button" class="modal-close" onclick="closeModal('addModal')">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Mã khoa <span class="required">*</span></label>
                    <input type="text" name="faculty_code" class="form-control" required placeholder="VD: CNTT">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tên khoa <span class="required">*</span></label>
                    <input type="text" name="faculty_name" class="form-control" required placeholder="VD: Công nghệ thông tin">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Trưởng khoa</label>
                    <input type="text" name="dean_name" class="form-control" placeholder="VD: PGS.TS Nguyễn Văn A">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="dean@university.edu.vn">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" placeholder="0123456789" pattern="[0-9]{10,11}">
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('addModal')">Hủy</button>
                <button type="submit" class="btn-primary">Thêm khoa</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="faculty_id" id="edit_faculty_id">
            
            <div class="modal-header">
                <h3 class="modal-title">Chỉnh sửa khoa</h3>
                <button type="button" class="modal-close" onclick="closeModal('editModal')">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Mã khoa <span class="required">*</span></label>
                    <input type="text" name="faculty_code" id="edit_faculty_code" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tên khoa <span class="required">*</span></label>
                    <input type="text" name="faculty_name" id="edit_faculty_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Trưởng khoa</label>
                    <input type="text" name="dean_name" id="edit_dean_name" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" id="edit_phone" class="form-control" pattern="[0-9]{10,11}">
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Hủy</button>
                <button type="submit" class="btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE FORM (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="faculty_id" id="delete_faculty_id">
</form>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.add('active');
}

function openEditModal(faculty) {
    document.getElementById('edit_faculty_id').value = faculty.faculty_id;
    document.getElementById('edit_faculty_code').value = faculty.faculty_code;
    document.getElementById('edit_faculty_name').value = faculty.faculty_name;
    document.getElementById('edit_dean_name').value = faculty.dean_name || '';
    document.getElementById('edit_email').value = faculty.email || '';
    document.getElementById('edit_phone').value = faculty.phone || '';
    
    document.getElementById('editModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function confirmDelete(facultyId, facultyName) {
    if (confirm(`Bạn có chắc chắn muốn xóa khoa "${facultyName}"?\n\nLưu ý: Không thể xóa nếu khoa còn sinh viên hoặc giảng viên!`)) {
        document.getElementById('delete_faculty_id').value = facultyId;
        document.getElementById('deleteForm').submit();
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(modal => {
            modal.classList.remove('active');
        });
    }
});
</script>

<?php include __DIR__ . '/views/layout/footer.php'; ?>
