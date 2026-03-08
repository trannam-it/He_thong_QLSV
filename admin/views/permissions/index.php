<?php
/**
 * QUẢN LÝ QUYỀN HẠN (Permissions) - Dynamic RBAC
 * Giao diện mới: Hiển thị grouped theo nhóm, CRUD đầy đủ
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/includes/auth_check.php';
require_once dirname(__DIR__, 2) . '/libs/PermissionManager.php';
require_once dirname(__DIR__, 2) . '/libs/Auth.php';

$auth = new Auth($conn);
$auth->requireAuthWeb();

if (!$auth->isSuperAdmin() && !$auth->hasPermission('permissions.view')) {
    $_SESSION['error'] = 'Bạn không có quyền quản lý quyền hạn.';
    header('Location: /web_QLSV/admin/Dashboard.php');
    exit;
}

$pageTitle = 'Quản lý Quyền hạn';

// Lấy danh sách permission groups
$groups = $conn->query("SELECT * FROM permission_groups ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);

// Lấy tất cả permissions kèm group info
$permsResult = $conn->query("
    SELECT p.*, pg.name AS group_name, pg.icon AS group_icon, pg.code AS group_code,
           (SELECT COUNT(*) FROM role_permissions rp WHERE rp.permission_id = p.id) AS role_count
    FROM permissions p
    INNER JOIN permission_groups pg ON pg.id = p.group_id
    ORDER BY pg.sort_order, p.id
");
$allPerms = $permsResult ? $permsResult->fetch_all(MYSQLI_ASSOC) : [];

// Group permissions
$permsByGroup = [];
foreach ($allPerms as $p) {
    $permsByGroup[$p['group_code']][] = $p;
}

include __DIR__ . '/../layout/header.php';
?>

<style>
.perm-group-card {
    border: 1px solid #e3e6f0;
    border-radius: 10px;
    margin-bottom: 16px;
    overflow: hidden;
    background: #fff;
}
.perm-group-header {
    background: linear-gradient(135deg, #f8f9ff, #eef0fb);
    border-bottom: 1px solid #e3e6f0;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}
.perm-group-header:hover { background: #eef0fb; }
.perm-group-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
}
.perm-group-title { font-weight: 600; color: #495057; flex-grow: 1; }
.perm-group-count {
    font-size: 0.78rem; padding: 3px 10px; border-radius: 20px;
    background: #667eea20; color: #667eea; font-weight: 600;
}
.perm-table th { background: #f8f9fa; font-size: 0.82rem; font-weight: 600; }
.perm-table td { font-size: 0.85rem; vertical-align: middle; }
.perm-code-badge {
    font-family: monospace; font-size: 0.78rem;
    background: #e8f4fd; color: #0d6efd;
    padding: 2px 8px; border-radius: 4px;
}
.system-perm { background: #fff8f0; }
.role-usage-badge {
    font-size: 0.72rem; padding: 2px 7px; border-radius: 12px;
    background: #d1fae5; color: #065f46;
}
.role-usage-zero {
    background: #fee2e2; color: #991b1b;
}
.btn-perm-action {
    padding: 3px 8px; font-size: 0.78rem; border-radius: 5px;
}
.group-filter-pill {
    padding: 4px 14px; border-radius: 20px; border: 1.5px solid #dee2e6;
    background: white; font-size: 0.82rem; cursor: pointer;
    transition: all 0.2s;
}
.group-filter-pill:hover, .group-filter-pill.active {
    background: #667eea; color: white; border-color: #667eea;
}
</style>

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-key-fill text-warning me-2"></i>
                Quản lý Quyền hạn
            </h2>
            <p class="text-muted mb-0">
                Toàn bộ quyền hạn trong hệ thống — phân nhóm theo module chức năng
            </p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($auth->isSuperAdmin() || $auth->hasPermission('permissions.create')): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermModal">
                <i class="bi bi-plus-lg me-1"></i> Thêm quyền mới
            </button>
            <?php endif; ?>
            <a href="/web_QLSV/admin/roles.php" class="btn btn-outline-secondary">
                <i class="bi bi-shield-lock me-1"></i> Quản lý Vai trò
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-primary"><?= count($allPerms) ?></div>
                <div class="text-muted small">Tổng quyền hạn</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-warning">
                    <?= count(array_filter($allPerms, fn($p) => $p['is_system'])) ?>
                </div>
                <div class="text-muted small">Quyền hệ thống (không xóa được)</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-success"><?= count($groups) ?></div>
                <div class="text-muted small">Nhóm quyền</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card text-center py-3">
                <div class="fs-1 fw-bold text-danger">
                    <?= count(array_filter($allPerms, fn($p) => $p['role_count'] == 0)) ?>
                </div>
                <div class="text-muted small">Chưa gán cho role nào</div>
            </div>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <button class="group-filter-pill active" onclick="filterGroup('all', this)">
            Tất cả (<?= count($allPerms) ?>)
        </button>
        <?php foreach ($groups as $g): 
            $cnt = count($permsByGroup[$g['code']] ?? []);
            if ($cnt === 0) continue;
        ?>
        <button class="group-filter-pill" onclick="filterGroup('<?= htmlspecialchars($g['code']) ?>', this)">
            <i class="bi <?= htmlspecialchars($g['icon']) ?> me-1"></i>
            <?= htmlspecialchars($g['name']) ?> (<?= $cnt ?>)
        </button>
        <?php endforeach; ?>
    </div>

    <!-- Permissions grouped -->
    <?php foreach ($permsByGroup as $gCode => $perms): 
        $groupInfo = array_values(array_filter($groups, fn($g) => $g['code'] === $gCode))[0] ?? ['name'=>$gCode,'icon'=>'bi-circle','code'=>$gCode];
    ?>
    <div class="perm-group-card perm-group-block" data-group="<?= htmlspecialchars($gCode) ?>">
        <div class="perm-group-header" onclick="toggleGroup(this)">
            <div class="perm-group-icon">
                <i class="bi <?= htmlspecialchars($groupInfo['icon'] ?? 'bi-circle') ?>"></i>
            </div>
            <span class="perm-group-title"><?= htmlspecialchars($groupInfo['name']) ?></span>
            <span class="perm-group-count"><?= count($perms) ?> quyền</span>
            <i class="bi bi-chevron-down ms-2 text-muted toggle-icon"></i>
        </div>
        <div class="group-body">
            <table class="table perm-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Mã quyền (code)</th>
                        <th>Tên hiển thị</th>
                        <th>Mô tả</th>
                        <th style="width:80px">Dùng bởi</th>
                        <th style="width:80px">Loại</th>
                        <?php if ($auth->isSuperAdmin() || $auth->hasPermission('permissions.edit') || $auth->hasPermission('permissions.delete')): ?>
                        <th style="width:100px">Hành động</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($perms as $i => $p): ?>
                    <tr class="<?= $p['is_system'] ? 'system-perm' : '' ?>">
                        <td class="text-muted small"><?= $p['id'] ?></td>
                        <td>
                            <span class="perm-code-badge"><?= htmlspecialchars($p['code']) ?></span>
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($p['description'] ?? '') ?></td>
                        <td>
                            <span class="role-usage-badge <?= $p['role_count']==0?'role-usage-zero':'' ?>">
                                <?= $p['role_count'] ?> role
                            </span>
                        </td>
                        <td>
                            <?php if ($p['is_system']): ?>
                            <span class="badge bg-warning text-dark" title="Quyền hệ thống - không xóa được">
                                <i class="bi bi-lock-fill"></i> System
                            </span>
                            <?php else: ?>
                            <span class="badge bg-light text-secondary">Custom</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($auth->isSuperAdmin() || $auth->hasPermission('permissions.edit') || $auth->hasPermission('permissions.delete')): ?>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if ($auth->isSuperAdmin() || $auth->hasPermission('permissions.edit')): ?>
                                <button class="btn btn-outline-warning btn-perm-action"
                                        onclick="openEditPerm(<?= $p['id'] ?>, <?= $p['group_id'] ?>, '<?= htmlspecialchars(addslashes($p['code'])) ?>', '<?= htmlspecialchars(addslashes($p['name'])) ?>', '<?= htmlspecialchars(addslashes($p['description'] ?? '')) ?>', <?= $p['is_system'] ?>)"
                                        title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php endif; ?>
                                <?php if (!$p['is_system'] && ($auth->isSuperAdmin() || $auth->hasPermission('permissions.delete'))): ?>
                                <button class="btn btn-outline-danger btn-perm-action"
                                        onclick="deletePerm(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')"
                                        title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ===== MODAL: TẠO QUYỀN MỚI ===== -->
<div class="modal fade" id="createPermModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Thêm quyền hạn mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-1"></i>
                    Sau khi tạo quyền mới, admin có thể vào
                    <strong>Vai trò & Phân quyền</strong> để gán quyền này cho role phù hợp.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nhóm quyền <span class="text-danger">*</span></label>
                    <select id="newPermGroup" class="form-select">
                        <?php foreach ($groups as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mã quyền (code) <span class="text-danger">*</span></label>
                    <input type="text" id="newPermCode" class="form-control font-monospace" 
                           placeholder="vd: reports.custom_export">
                    <div class="form-text">Định dạng: <code>module.action</code> — chỉ chữ thường, số, dấu chấm</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên hiển thị <span class="text-danger">*</span></label>
                    <input type="text" id="newPermName" class="form-control" placeholder="vd: Xuất báo cáo tùy chỉnh">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea id="newPermDesc" class="form-control" rows="2" 
                              placeholder="Mô tả quyền này cho phép làm gì..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="createPerm()">
                    <i class="bi bi-plus-lg me-1"></i> Tạo quyền hạn
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL: SỬA QUYỀN ===== -->
<div class="modal fade" id="editPermModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Chỉnh sửa quyền hạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editPermId">
                <input type="hidden" id="editPermIsSystem">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nhóm quyền</label>
                    <select id="editPermGroup" class="form-select">
                        <?php foreach ($groups as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mã quyền</label>
                    <input type="text" id="editPermCode" class="form-control font-monospace">
                    <div id="editPermCodeWarning" class="form-text text-warning d-none">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Đây là quyền hệ thống — không nên đổi mã code
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên hiển thị</label>
                    <input type="text" id="editPermName" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea id="editPermDesc" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning" onclick="updatePerm()">
                    <i class="bi bi-save me-1"></i> Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API = '/web_QLSV/admin/api/router.php';

// ─── FILTER GROUPS ───
function filterGroup(code, btn) {
    document.querySelectorAll('.group-filter-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    document.querySelectorAll('.perm-group-block').forEach(block => {
        if (code === 'all' || block.dataset.group === code) {
            block.style.display = '';
        } else {
            block.style.display = 'none';
        }
    });
}

// ─── TOGGLE GROUP ───
function toggleGroup(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.toggle-icon');
    if (body.style.display === 'none') {
        body.style.display = '';
        icon.classList.replace('bi-chevron-right', 'bi-chevron-down');
    } else {
        body.style.display = 'none';
        icon.classList.replace('bi-chevron-down', 'bi-chevron-right');
    }
}

// ─── CREATE PERMISSION ───
function createPerm() {
    const groupId = document.getElementById('newPermGroup').value;
    const code    = document.getElementById('newPermCode').value.trim();
    const name    = document.getElementById('newPermName').value.trim();
    const desc    = document.getElementById('newPermDesc').value.trim();

    if (!groupId || !code || !name) {
        showToast('error', 'Vui lòng điền đầy đủ thông tin bắt buộc');
        return;
    }

    const fd = new FormData();
    fd.append('group_id', groupId);
    fd.append('code', code);
    fd.append('name', name);
    fd.append('description', desc);

    fetch(`${API}?resource=permissions&action=store`, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('createPermModal')).hide();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', data.message);
        }
    });
}

// ─── OPEN EDIT MODAL ───
function openEditPerm(id, groupId, code, name, desc, isSystem) {
    document.getElementById('editPermId').value = id;
    document.getElementById('editPermIsSystem').value = isSystem;
    document.getElementById('editPermGroup').value = groupId;
    document.getElementById('editPermCode').value = code;
    document.getElementById('editPermName').value = name;
    document.getElementById('editPermDesc').value = desc;
    
    const warning = document.getElementById('editPermCodeWarning');
    if (isSystem) {
        warning.classList.remove('d-none');
    } else {
        warning.classList.add('d-none');
    }

    new bootstrap.Modal(document.getElementById('editPermModal')).show();
}

// ─── UPDATE PERMISSION ───
function updatePerm() {
    const id      = document.getElementById('editPermId').value;
    const groupId = document.getElementById('editPermGroup').value;
    const code    = document.getElementById('editPermCode').value.trim();
    const name    = document.getElementById('editPermName').value.trim();
    const desc    = document.getElementById('editPermDesc').value.trim();

    if (!code || !name) {
        showToast('error', 'Vui lòng điền đầy đủ thông tin');
        return;
    }

    const fd = new FormData();
    fd.append('id', id);
    fd.append('group_id', groupId);
    fd.append('code', code);
    fd.append('name', name);
    fd.append('description', desc);

    fetch(`${API}?resource=permissions&action=update`, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('editPermModal')).hide();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('error', data.message);
        }
    });
}

// ─── DELETE PERMISSION ───
function deletePerm(id, name) {
    if (!confirm(`Xóa quyền "${name}"?\n\nHành động này sẽ thu hồi quyền này khỏi tất cả các role đang dùng.`)) return;

    const fd = new FormData();
    fd.append('id', id);

    fetch(`${API}?resource=permissions&action=delete`, { method: 'POST', body: fd })
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

// ─── TOAST ───
function showToast(type, msg) {
    const colors = { success: '#198754', error: '#dc3545' };
    const el = document.createElement('div');
    el.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:9999;
        background:${colors[type]||'#333'};color:white;
        padding:12px 20px;border-radius:8px;
        box-shadow:0 4px 15px rgba(0,0,0,0.2);
        font-size:.9rem;max-width:360px;`;
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity='0'; setTimeout(()=>el.remove(),300); }, 3500);
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>