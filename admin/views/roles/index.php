<?php
/**
 * QUẢN LÝ VAI TRÒ & PHÂN QUYỀN - Dynamic RBAC
 * Giao diện mới: Hiển thị thống kê, số quyền, số user cho mỗi role
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../libs/PermissionManager.php';
require_once __DIR__ . '/../../libs/Auth.php';

$auth = new Auth($conn);
$auth->requireAuthWeb();

// Kiểm tra quyền xem danh sách vai trò
if (!$auth->hasPermission('roles.view') && !$auth->isSuperAdmin()) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
    header('Location: /web_QLSV/admin/Dashboard.php');
    exit;
}

$pageTitle = 'Quản lý Vai trò & Phân quyền';

// Lấy danh sách roles kèm thống kê
$rolesQuery = $conn->query("
    SELECT r.*,
           COUNT(DISTINCT ur.user_id) AS user_count,
           COUNT(DISTINCT rp.permission_id) AS perm_count
    FROM roles r
    LEFT JOIN user_roles ur ON ur.role_id = r.id
    LEFT JOIN role_permissions rp ON rp.role_id = r.id
    GROUP BY r.id
    ORDER BY r.is_system DESC, r.id ASC
");
$roles = $rolesQuery ? $rolesQuery->fetch_all(MYSQLI_ASSOC) : [];

// Tổng số permissions
$totalPerms = (int)$conn->query("SELECT COUNT(*) AS cnt FROM permissions")->fetch_assoc()['cnt'];

include __DIR__ . '/../layout/header.php';
?>

<style>
.role-card {
    border: 1px solid #e3e6f0;
    border-radius: 12px;
    background: #fff;
    transition: all 0.25s ease;
    overflow: hidden;
}
.role-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}
.role-card-header {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #f0f0f0;
}
.role-badge {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #fff;
    flex-shrink: 0;
}
.role-card-body {
    padding: 14px 20px;
}
.role-stat {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #6c757d;
}
.role-stat strong {
    color: #333;
    font-size: 1rem;
}
.perm-progress {
    height: 6px;
    border-radius: 3px;
    background: #e9ecef;
    overflow: hidden;
    margin-top: 4px;
}
.perm-progress-bar {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease;
}
.role-card-footer {
    padding: 12px 20px;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.system-badge {
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 20px;
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffc107;
}
.btn-assign-perm {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-size: 0.8rem;
    padding: 5px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-assign-perm:hover {
    opacity: 0.9;
    color: white;
    transform: translateY(-1px);
}
.modal-perm-matrix .group-header {
    background: linear-gradient(135deg, #667eea20, #764ba210);
    border-left: 4px solid #667eea;
    padding: 10px 15px;
    border-radius: 0 8px 8px 0;
    margin-bottom: 8px;
    margin-top: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-perm-matrix .group-header .group-title {
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 8px;
}
.modal-perm-matrix .perm-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 4px;
    border: 1px solid transparent;
    transition: all 0.15s;
}
.modal-perm-matrix .perm-item:hover {
    background: #f8f9ff;
    border-color: #e8eaf6;
}
.modal-perm-matrix .perm-item input[type="checkbox"] {
    margin-top: 2px;
    cursor: pointer;
    width: 16px;
    height: 16px;
    accent-color: #667eea;
}
.modal-perm-matrix .perm-item label {
    cursor: pointer;
    flex: 1;
}
.modal-perm-matrix .perm-code {
    font-size: 0.75rem;
    background: #e8eaf6;
    color: #3f51b5;
    padding: 1px 6px;
    border-radius: 4px;
    font-family: monospace;
}
.modal-perm-matrix .perm-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: #333;
}
.modal-perm-matrix .perm-desc {
    font-size: 0.78rem;
    color: #6c757d;
    margin-top: 1px;
}
.select-all-group {
    font-size: 0.78rem;
    color: #667eea;
    cursor: pointer;
    font-weight: 500;
    background: none;
    border: none;
    padding: 2px 6px;
}
.select-all-group:hover {
    text-decoration: underline;
}
.perm-count-badge {
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 12px;
    background: #667eea20;
    color: #667eea;
    font-weight: 600;
}
#assignPermModal .modal-dialog {
    max-width: 860px;
}
.modal-perm-matrix {
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 5px;
}
.modal-perm-matrix::-webkit-scrollbar {
    width: 5px;
}
.modal-perm-matrix::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}
.save-btn-floating {
    position: sticky;
    bottom: 0;
    background: white;
    padding: 12px 0 0;
    border-top: 1px solid #eee;
    z-index: 10;
}
.select-counter {
    font-size: 0.85rem;
    color: #6c757d;
}
.select-counter span {
    font-weight: 700;
    color: #667eea;
}
</style>

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i>
                Vai trò & Phân quyền
            </h2>
            <p class="text-muted mb-0">Quản lý vai trò và cấu hình quyền hạn theo từng chức năng cụ thể</p>
        </div>
        <?php if ($auth->hasPermission('roles.create') || $auth->isSuperAdmin()): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-lg me-1"></i> Tạo vai trò mới
        </button>
        <?php endif; ?>
    </div>

    <!-- Alert -->
    <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-primary"><?= count($roles) ?></div>
                <div class="text-muted small">Tổng vai trò</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-success"><?= $totalPerms ?></div>
                <div class="text-muted small">Tổng quyền hạn</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-info">
                    <?= array_sum(array_column($roles, 'user_count')) ?>
                </div>
                <div class="text-muted small">Người dùng được gán role</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-warning">
                    <?= count(array_filter($roles, fn($r) => $r['is_system'])) ?>
                </div>
                <div class="text-muted small">Vai trò hệ thống</div>
            </div>
        </div>
    </div>

    <!-- Roles Grid -->
    <div class="row g-3">
        <?php foreach ($roles as $role):
            $permPercent = $totalPerms > 0 ? round(($role['perm_count'] / $totalPerms) * 100) : 0;
            $color = $role['color'] ?? '#6c757d';
            $isSuperAdmin = ($role['code'] === 'super_admin');
        ?>
        <div class="col-xl-4 col-md-6">
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-badge" style="background: <?= htmlspecialchars($color) ?>">
                        <i class="bi bi-shield-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($role['name']) ?></h6>
                            <?php if ($role['is_system']): ?>
                            <span class="system-badge">Hệ thống</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted font-monospace"><?= htmlspecialchars($role['code']) ?></small>
                    </div>
                </div>

                <div class="role-card-body">
                    <?php if (!empty($role['description'])): ?>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($role['description']) ?></p>
                    <?php endif; ?>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="role-stat">
                                <i class="bi bi-people text-primary"></i>
                                <div>
                                    <strong><?= $role['user_count'] ?></strong>
                                    <div class="text-muted" style="font-size:0.75rem">Người dùng</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-stat">
                                <i class="bi bi-key text-success"></i>
                                <div>
                                    <strong>
                                        <?php if ($isSuperAdmin): ?>
                                        <span class="text-danger">Tất cả</span>
                                        <?php else: ?>
                                        <?= $role['perm_count'] ?>/<?= $totalPerms ?>
                                        <?php endif; ?>
                                    </strong>
                                    <div class="text-muted" style="font-size:0.75rem">Quyền hạn</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!$isSuperAdmin): ?>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Phủ quyền</small>
                            <small class="fw-bold"><?= $permPercent ?>%</small>
                        </div>
                        <div class="perm-progress">
                            <div class="perm-progress-bar"
                                 style="width: <?= $permPercent ?>%; background: <?= htmlspecialchars($color) ?>">
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-1">
                        <span class="badge bg-danger">
                            <i class="bi bi-infinity me-1"></i> Toàn quyền hệ thống
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="role-card-footer">
                    <?php if (($auth->hasPermission('roles.assign_perm') || $auth->isSuperAdmin()) && !$isSuperAdmin): ?>
                    <button class="btn-assign-perm"
                            onclick="openAssignPermModal(<?= $role['id'] ?>, '<?= htmlspecialchars(addslashes($role['name'])) ?>', '<?= htmlspecialchars($color) ?>')">
                        <i class="bi bi-shield-check"></i> Cấp quyền
                    </button>
                    <?php endif; ?>

                    <?php if ($auth->hasPermission('roles.view') || $auth->isSuperAdmin()): ?>
                         <!-- sửa tại đây để link đến trang quản lý user của role này -->
                    
                     <a href="/web_QLSV/admin/views/roles/users.php?role_id=<?= $role['id'] ?>"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-people"></i> Users (<?= $role['user_count'] ?>)
                    </a>
                    <?php endif; ?>

                    <?php if (($auth->hasPermission('roles.edit') || $auth->isSuperAdmin())): ?>
                    <button class="btn btn-outline-warning btn-sm"
                            onclick="openEditRoleModal(<?= $role['id'] ?>, '<?= htmlspecialchars(addslashes($role['code'])) ?>', '<?= htmlspecialchars(addslashes($role['name'])) ?>', '<?= htmlspecialchars(addslashes($role['description'] ?? '')) ?>', '<?= htmlspecialchars($color) ?>')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <?php endif; ?>

                    <?php if (($auth->hasPermission('roles.delete') || $auth->isSuperAdmin()) && !$role['is_system']): ?>
                    <button class="btn btn-outline-danger btn-sm"
                            onclick="deleteRole(<?= $role['id'] ?>, '<?= htmlspecialchars(addslashes($role['name'])) ?>')">
                        <i class="bi bi-trash"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- ========== MODAL: GÁN QUYỀN ========== -->
<div class="modal fade" id="assignPermModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" id="assignPermModalHeader" style="background: linear-gradient(135deg,#667eea,#764ba2); color:white">
                <h5 class="modal-title">
                    <i class="bi bi-shield-check me-2"></i>
                    Cấu hình quyền hạn: <span id="assignRoleName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Search & Stats -->
                <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                    <input type="text" id="permSearch" class="form-control form-control-sm" 
                           style="max-width:250px" placeholder="🔍 Tìm quyền...">
                    <div class="select-counter">
                        Đã chọn: <span id="selectedCount">0</span> / <span id="totalCount">0</span> quyền
                    </div>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllPerms(true)">
                            <i class="bi bi-check-all"></i> Chọn tất cả
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectAllPerms(false)">
                            <i class="bi bi-x-lg"></i> Bỏ chọn
                        </button>
                    </div>
                </div>

                <!-- Permission Matrix -->
                <div class="modal-perm-matrix" id="permMatrix">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Đang tải danh sách quyền...</p>
                    </div>
                </div>

                <div class="save-btn-floating d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Thay đổi có hiệu lực ngay sau khi lưu
                    </small>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary btn-sm" id="savePermBtn" onclick="savePermissions()">
                            <i class="bi bi-save me-1"></i> Lưu quyền
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL: TẠO ROLE ========== -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tạo vai trò mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mã vai trò <span class="text-danger">*</span></label>
                    <input type="text" id="newRoleCode" class="form-control" placeholder="vd: content_manager" pattern="[a-z][a-z0-9_]+">
                    <div class="form-text">Chỉ chữ thường, số và gạch dưới. VD: <code>content_admin</code></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên hiển thị <span class="text-danger">*</span></label>
                    <input type="text" id="newRoleName" class="form-control" placeholder="vd: Quản lý Nội dung">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea id="newRoleDesc" class="form-control" rows="2" placeholder="Mô tả ngắn về vai trò này..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Màu sắc</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" id="newRoleColor" class="form-control form-control-color" value="#667eea" style="width:50px">
                        <span class="text-muted small">Màu badge hiển thị</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="createRole()">
                    <i class="bi bi-plus-lg me-1"></i> Tạo vai trò
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODAL: SỬA ROLE ========== -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Chỉnh sửa vai trò</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editRoleId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mã vai trò</label>
                    <input type="text" id="editRoleCode" class="form-control">
                    <div class="form-text text-warning">Không nên thay đổi mã của vai trò đang được sử dụng</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên hiển thị <span class="text-danger">*</span></label>
                    <input type="text" id="editRoleName" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea id="editRoleDesc" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Màu sắc</label>
                    <input type="color" id="editRoleColor" class="form-control form-control-color" style="width:50px">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning" onclick="updateRole()">
                    <i class="bi bi-save me-1"></i> Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '/web_QLSV/admin/api/router.php';
let currentRoleId = null;

// ─── MỞ MODAL GÁN QUYỀN ───
function openAssignPermModal(roleId, roleName, color) {
    currentRoleId = roleId;
    document.getElementById('assignRoleName').textContent = roleName;
    document.getElementById('permMatrix').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Đang tải...</p>
        </div>`;
    
    const modal = new bootstrap.Modal(document.getElementById('assignPermModal'));
    modal.show();

    fetch(`${API_BASE}?resource=roles&action=get_permissions&role_id=${roleId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);
            renderPermMatrix(data.permissions, data.assigned);
        })
        .catch(err => {
            document.getElementById('permMatrix').innerHTML = `
                <div class="alert alert-danger">Lỗi: ${err.message}</div>`;
        });
}

// ─── RENDER MA TRẬN QUYỀN ───
function renderPermMatrix(permissions, assigned) {
    const assignedSet = new Set(assigned.map(Number));
    
    // Nhóm permissions theo group
    const groups = {};
    permissions.forEach(p => {
        const gKey = p.group_code || 'other';
        if (!groups[gKey]) {
            groups[gKey] = {
                name: p.group_name,
                icon: p.group_icon || 'bi-circle',
                perms: []
            };
        }
        groups[gKey].perms.push(p);
    });

    let html = '';
    let totalCount = 0;
    
    Object.entries(groups).forEach(([gCode, group]) => {
        const groupPerms = group.perms;
        const checkedInGroup = groupPerms.filter(p => assignedSet.has(Number(p.id))).length;
        totalCount += groupPerms.length;
        
        html += `
        <div class="perm-group" data-group="${gCode}">
            <div class="group-header">
                <div class="group-title">
                    <i class="bi ${group.icon}"></i>
                    ${escapeHtml(group.name)}
                    <span class="perm-count-badge">${checkedInGroup}/${groupPerms.length}</span>
                </div>
                <div class="d-flex gap-1">
                    <button type="button" class="select-all-group" onclick="selectGroup('${gCode}', true)">Chọn tất</button>
                    <span class="text-muted">|</span>
                    <button type="button" class="select-all-group" onclick="selectGroup('${gCode}', false)">Bỏ tất</button>
                </div>
            </div>
            <div class="group-perms">`;
        
        groupPerms.forEach(p => {
            const checked = assignedSet.has(Number(p.id)) ? 'checked' : '';
            html += `
            <div class="perm-item" data-group="${gCode}">
                <input type="checkbox" id="perm_${p.id}" name="perms[]" value="${p.id}" ${checked}
                       onchange="updateCounter()">
                <label for="perm_${p.id}">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="perm-name">${escapeHtml(p.name)}</span>
                        <span class="perm-code">${escapeHtml(p.code)}</span>
                    </div>
                    ${p.description ? `<div class="perm-desc">${escapeHtml(p.description)}</div>` : ''}
                </label>
            </div>`;
        });

        html += `</div></div>`;
    });

    document.getElementById('permMatrix').innerHTML = html;
    document.getElementById('totalCount').textContent = totalCount;
    updateCounter();

    // Search handler
    document.getElementById('permSearch').addEventListener('input', filterPerms);
}

function updateCounter() {
    const checked = document.querySelectorAll('#permMatrix input[type="checkbox"]:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    
    // Cập nhật badge từng group
    document.querySelectorAll('.perm-group').forEach(groupEl => {
        const gCode = groupEl.dataset.group;
        const total = groupEl.querySelectorAll('input[type="checkbox"]').length;
        const checked = groupEl.querySelectorAll('input[type="checkbox"]:checked').length;
        const badge = groupEl.querySelector('.perm-count-badge');
        if (badge) badge.textContent = `${checked}/${total}`;
    });
}

function selectAllPerms(checked) {
    document.querySelectorAll('#permMatrix input[type="checkbox"]:not(:disabled)').forEach(cb => {
        cb.checked = checked;
    });
    updateCounter();
}

function selectGroup(gCode, checked) {
    document.querySelectorAll(`.perm-item[data-group="${gCode}"] input[type="checkbox"]`).forEach(cb => {
        cb.checked = checked;
    });
    updateCounter();
}

function filterPerms() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.perm-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(q) ? '' : 'none';
    });
}

// ─── LƯU QUYỀN ───
function savePermissions() {
    const checkboxes = document.querySelectorAll('#permMatrix input[type="checkbox"]:checked');
    const permIds = Array.from(checkboxes).map(cb => cb.value);
    
    const btn = document.getElementById('savePermBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang lưu...';

    const fd = new FormData();
    fd.append('role_id', currentRoleId);
    permIds.forEach(id => fd.append('permission_ids[]', id));

    fetch(`${API_BASE}?resource=roles&action=assign_permissions`, {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', `✅ Đã cập nhật ${data.granted_count} quyền cho vai trò!`);
            bootstrap.Modal.getInstance(document.getElementById('assignPermModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Lỗi khi lưu quyền');
        }
    })
    .catch(err => showToast('error', 'Lỗi kết nối: ' + err.message))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i> Lưu quyền';
    });
}

// ─── TẠO ROLE ───
function createRole() {
    const code  = document.getElementById('newRoleCode').value.trim();
    const name  = document.getElementById('newRoleName').value.trim();
    const desc  = document.getElementById('newRoleDesc').value.trim();
    const color = document.getElementById('newRoleColor').value;

    if (!code || !name) { showToast('error', 'Vui lòng nhập mã và tên vai trò'); return; }

    const fd = new FormData();
    fd.append('code', code);
    fd.append('name', name);
    fd.append('description', desc);
    fd.append('color', color);

    fetch(`${API_BASE}?resource=roles&action=store`, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('createRoleModal')).hide();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(err => showToast('error', err.message));
}

// ─── SỬA ROLE ───
function openEditRoleModal(id, code, name, desc, color) {
    document.getElementById('editRoleId').value = id;
    document.getElementById('editRoleCode').value = code;
    document.getElementById('editRoleName').value = name;
    document.getElementById('editRoleDesc').value = desc;
    document.getElementById('editRoleColor').value = color || '#6c757d';
    new bootstrap.Modal(document.getElementById('editRoleModal')).show();
}

function updateRole() {
    const id    = document.getElementById('editRoleId').value;
    const code  = document.getElementById('editRoleCode').value.trim();
    const name  = document.getElementById('editRoleName').value.trim();
    const desc  = document.getElementById('editRoleDesc').value.trim();
    const color = document.getElementById('editRoleColor').value;

    if (!code || !name) { showToast('error', 'Vui lòng nhập đầy đủ thông tin'); return; }

    const fd = new FormData();
    fd.append('id', id);
    fd.append('code', code);
    fd.append('name', name);
    fd.append('description', desc);
    fd.append('color', color);

    fetch(`${API_BASE}?resource=roles&action=update`, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('editRoleModal')).hide();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', data.message);
        }
    });
}

// ─── XÓA ROLE ───
function deleteRole(id, name) {
    if (!confirm(`Xác nhận xóa vai trò "${name}"?\n\nChú ý: Không thể xóa nếu đang có người dùng sử dụng.`)) return;

    const fd = new FormData();
    fd.append('id', id);

    fetch(`${API_BASE}?resource=roles&action=delete`, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', data.message);
        }
    });
}

// ─── TOAST NOTIFICATION ───
function showToast(type, message) {
    const colors = { success: '#198754', error: '#dc3545', warning: '#ffc107' };
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed; bottom:20px; right:20px; z-index:9999;
        background:${colors[type] || '#333'}; color:white;
        padding:12px 20px; border-radius:8px;
        box-shadow:0 4px 15px rgba(0,0,0,0.2);
        font-size:0.9rem; max-width:350px;
        animation: slideIn 0.3s ease;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3500);
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
}

// Search event
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('permSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.perm-item').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
            document.querySelectorAll('.perm-group').forEach(g => {
                const visible = g.querySelectorAll('.perm-item:not([style*="none"])').length;
                g.style.display = visible > 0 ? '' : 'none';
            });
        });
    }
});
</script>

<style>
@keyframes slideIn {
    from { transform: translateX(100px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>