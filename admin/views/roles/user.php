<?php
/**
 * Xem người dùng thuộc một vai trò cụ thể
 * Dynamic RBAC - permission-based access
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/includes/auth_check.php';
require_once dirname(__DIR__, 1) . '/libs/PermissionManager.php';
require_once dirname(__DIR__, 1) . '/libs/Auth.php';

$auth = new Auth($conn);
$auth->requireAuthWeb();

if (!$auth->isSuperAdmin() && !$auth->hasPermission('roles.view')) {
    $_SESSION['error'] = 'Bạn không có quyền xem danh sách vai trò.';
    header('Location: /web_QLSV/admin/Dashboard.php');
    exit;
}

$roleId = (int)($_GET['role_id'] ?? 0);
if (!$roleId) {
    header('Location: /web_QLSV/admin/roles.php');
    exit;
}

// Lấy thông tin role
$roleStmt = $conn->prepare("
    SELECT r.*,
           COUNT(DISTINCT rp.permission_id) AS perm_count,
           COUNT(DISTINCT ur.user_id) AS user_count
    FROM roles r
    LEFT JOIN role_permissions rp ON rp.role_id = r.id
    LEFT JOIN user_roles ur ON ur.role_id = r.id
    WHERE r.id = ?
    GROUP BY r.id
");
$roleStmt->bind_param('i', $roleId);
$roleStmt->execute();
$role = $roleStmt->get_result()->fetch_assoc();

if (!$role) {
    $_SESSION['error'] = 'Không tìm thấy vai trò.';
    header('Location: /web_QLSV/admin/roles.php');
    exit;
}

// Lấy danh sách users thuộc role này
$usersStmt = $conn->prepare("
    SELECT u.id, u.username, u.email, u.is_active, u.last_login, ur.assigned_at
    FROM users u
    INNER JOIN user_roles ur ON ur.user_id = u.id
    WHERE ur.role_id = ?
    ORDER BY ur.assigned_at DESC
");
$usersStmt->bind_param('i', $roleId);
$usersStmt->execute();
$users = $usersStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Lấy permissions của role này
$permsStmt = $conn->prepare("
    SELECT p.code, p.name, pg.name AS group_name, pg.icon AS group_icon
    FROM permissions p
    INNER JOIN role_permissions rp ON rp.permission_id = p.id
    INNER JOIN permission_groups pg ON pg.id = p.group_id
    WHERE rp.role_id = ?
    ORDER BY pg.sort_order, p.id
");
$permsStmt->bind_param('i', $roleId);
$permsStmt->execute();
$rolePerms = $permsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Nhóm permissions theo group
$permsByGroup = [];
foreach ($rolePerms as $p) {
    $permsByGroup[$p['group_name']][] = $p;
}

$pageTitle = 'Chi tiết Vai trò: ' . $role['name'];
include __DIR__ . '/../layout/header.php';
?>

<style>
.role-detail-header {
    background: linear-gradient(135deg, <?= htmlspecialchars($role['color'] ?? '#667eea') ?>22, <?= htmlspecialchars($role['color'] ?? '#764ba2') ?>11);
    border: 1px solid <?= htmlspecialchars($role['color'] ?? '#667eea') ?>44;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}
.role-color-dot {
    width: 52px; height: 52px; border-radius: 12px;
    background: <?= htmlspecialchars($role['color'] ?? '#667eea') ?>;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: white;
}
.user-row:hover { background: #f8f9ff !important; }
.perm-tag {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 8px; border-radius: 6px;
    background: #f0f4ff; color: #4655ab;
    font-size: 0.75rem; font-family: monospace;
    border: 1px solid #d6dbf5;
    margin: 2px;
}
.online-dot { width: 8px; height: 8px; border-radius: 50%; }
.online-dot.active { background: #22c55e; }
.online-dot.inactive { background: #ef4444; }
</style>

<div class="container-fluid py-4">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/web_QLSV/admin/Dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/web_QLSV/admin/roles.php">Vai trò & Quyền hạn</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($role['name']) ?></li>
        </ol>
    </nav>

    <!-- Role Detail Header -->
    <div class="role-detail-header">
        <div class="d-flex align-items-start gap-3">
            <div class="role-color-dot">
                <i class="bi bi-shield-fill"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold mb-0"><?= htmlspecialchars($role['name']) ?></h3>
                    <?php if ($role['is_system']): ?>
                    <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill me-1"></i>Hệ thống</span>
                    <?php endif; ?>
                    <?php if ($role['code'] === 'super_admin'): ?>
                    <span class="badge bg-danger"><i class="bi bi-infinity me-1"></i>Toàn quyền</span>
                    <?php endif; ?>
                </div>
                <div class="font-monospace text-muted small mb-2"><?= htmlspecialchars($role['code']) ?></div>
                <?php if (!empty($role['description'])): ?>
                <p class="text-muted mb-0"><?= htmlspecialchars($role['description']) ?></p>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-3 text-center">
                <div>
                    <div class="fs-2 fw-bold" style="color:<?= htmlspecialchars($role['color'] ?? '#667eea') ?>">
                        <?= count($users) ?>
                    </div>
                    <div class="text-muted small">Người dùng</div>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-success">
                        <?= $role['code'] === 'super_admin' ? '∞' : $role['perm_count'] ?>
                    </div>
                    <div class="text-muted small">Quyền hạn</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        <!-- LEFT: Users List -->
        <div class="col-lg-7">
            <div class="content-card">
                <div class="content-card-header d-flex align-items-center justify-content-between">
                    <h5 class="content-card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        Người dùng có vai trò này (<?= count($users) ?>)
                    </h5>
                    <?php if ($auth->isSuperAdmin() || $auth->hasPermission('users.assign_role')): ?>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                        <i class="bi bi-person-plus me-1"></i> Thêm user
                    </button>
                    <?php endif; ?>
                </div>
                <div class="content-card-body p-0">
                    <?php if (empty($users)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-person-x display-4 d-block mb-2 opacity-25"></i>
                        Chưa có người dùng nào được gán vai trò này
                    </div>
                    <?php else: ?>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Trạng thái</th>
                                <th>Đăng nhập gần nhất</th>
                                <th>Gán lúc</th>
                                <?php if ($auth->isSuperAdmin() || $auth->hasPermission('users.assign_role')): ?>
                                <th></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr class="user-row">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['username']) ?>&size=32&background=667eea&color=fff"
                                             class="rounded-circle" width="32" height="32">
                                        <div>
                                            <div class="fw-semibold small"><?= htmlspecialchars($u['username']) ?></div>
                                            <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="online-dot <?= $u['is_active'] ? 'active' : 'inactive' ?>"></span>
                                        <span class="small"><?= $u['is_active'] ? 'Hoạt động' : 'Bị khóa' ?></span>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '—' ?>
                                </td>
                                <td class="text-muted small">
                                    <?= $u['assigned_at'] ? date('d/m/Y', strtotime($u['assigned_at'])) : '—' ?>
                                </td>
                                <?php if ($auth->isSuperAdmin() || $auth->hasPermission('users.assign_role')): ?>
                                <td>
                                    <button class="btn btn-outline-danger btn-sm"
                                            onclick="removeUserRole(<?= $u['id'] ?>, <?= $roleId ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>')"
                                            title="Gỡ vai trò này">
                                        <i class="bi bi-person-dash"></i>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: Permissions List -->
        <div class="col-lg-5">
            <div class="content-card">
                <div class="content-card-header d-flex align-items-center justify-content-between">
                    <h5 class="content-card-title mb-0">
                        <i class="bi bi-key me-2"></i>
                        Quyền hạn đã cấp
                    </h5>
                    <?php if ($role['code'] !== 'super_admin' && ($auth->isSuperAdmin() || $auth->hasPermission('roles.assign_perm'))): ?>
                    <a href="/web_QLSV/admin/roles.php" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Sửa quyền
                    </a>
                    <?php endif; ?>
                </div>
                <div class="content-card-body">
                    <?php if ($role['code'] === 'super_admin'): ?>
                    <div class="text-center py-3">
                        <i class="bi bi-infinity text-danger display-4"></i>
                        <p class="text-danger fw-bold mt-2">Có toàn bộ quyền hạn hệ thống</p>
                    </div>
                    <?php elseif (empty($rolePerms)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-key-x display-4 d-block mb-2 opacity-25"></i>
                        Chưa có quyền nào được cấp cho vai trò này
                        <?php if ($auth->isSuperAdmin() || $auth->hasPermission('roles.assign_perm')): ?>
                        <div class="mt-2">
                            <a href="/web_QLSV/admin/roles.php" class="btn btn-sm btn-primary">
                                Cấp quyền ngay
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <?php foreach ($permsByGroup as $groupName => $perms): ?>
                    <div class="mb-3">
                        <div class="fw-semibold small text-muted mb-2">
                            <i class="bi <?= htmlspecialchars($perms[0]['group_icon'] ?? 'bi-circle') ?> me-1"></i>
                            <?= htmlspecialchars($groupName) ?>
                            <span class="badge bg-light text-secondary ms-1"><?= count($perms) ?></span>
                        </div>
                        <div>
                            <?php foreach ($perms as $p): ?>
                            <span class="perm-tag"><?= htmlspecialchars($p['code']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL: Gán user vào role -->
<div class="modal fade" id="assignUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>
                    Gán người dùng vào: <?= htmlspecialchars($role['name']) ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small py-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Mỗi tài khoản chỉ có thể có 1 vai trò. Gán vai trò mới sẽ thay thế vai trò cũ.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tìm người dùng</label>
                    <input type="text" id="userSearchInput" class="form-control"
                           placeholder="Nhập username hoặc email..."
                           oninput="searchUsers(this.value)">
                </div>
                <div id="userSearchResults"></div>
            </div>
        </div>
    </div>
</div>

<script>
const API = '/web_QLSV/admin/api/router.php';
let searchTimeout;

function searchUsers(q) {
    clearTimeout(searchTimeout);
    if (!q.trim()) {
        document.getElementById('userSearchResults').innerHTML = '';
        return;
    }
    searchTimeout = setTimeout(() => {
        fetch(`${API}?resource=roles&action=search_users&q=${encodeURIComponent(q)}&role_id=<?= $roleId ?>`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const container = document.getElementById('userSearchResults');
            if (!data.data.length) {
                container.innerHTML = '<p class="text-muted small text-center py-2">Không tìm thấy</p>';
                return;
            }
            container.innerHTML = data.data.map(u => `
                <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-1">
                    <div>
                        <strong class="small">${escHtml(u.username)}</strong>
                        <span class="text-muted small ms-1">(${escHtml(u.email)})</span>
                        ${u.current_role ? `<span class="badge bg-secondary ms-1">${escHtml(u.current_role)}</span>` : ''}
                    </div>
                    <button class="btn btn-sm btn-primary" onclick="assignUser(${u.id}, '${escHtml(u.username)}')">
                        <i class="bi bi-plus me-1"></i>Gán
                    </button>
                </div>
            `).join('');
        });
    }, 400);
}

function assignUser(userId, username) {
    if (!confirm(`Gán "${username}" vào vai trò "${<?= json_encode($role['name']) ?>}"?`)) return;

    const fd = new FormData();
    fd.append('user_id', userId);
    fd.append('role_id', <?= $roleId ?>);
    fd.append('action', 'add');

    fetch(`${API}?resource=roles&action=assign_user`, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('assignUserModal')).hide();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', data.message);
        }
    });
}

function removeUserRole(userId, roleId, username) {
    if (!confirm(`Gỡ vai trò của "${username}"?`)) return;

    const fd = new FormData();
    fd.append('user_id', userId);
    fd.append('role_id', roleId);
    fd.append('action', 'remove');

    fetch(`${API}?resource=roles&action=assign_user`, { method: 'POST', body: fd })
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

function showToast(type, msg) {
    const colors = { success: '#198754', error: '#dc3545' };
    const el = document.createElement('div');
    el.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:9999;
        background:${colors[type]||'#333'};color:white;
        padding:12px 20px;border-radius:8px;
        box-shadow:0 4px 15px rgba(0,0,0,0.2);font-size:.9rem;`;
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, 3200);
}

function escHtml(s) {
    return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>