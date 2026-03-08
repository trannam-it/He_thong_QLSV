<?php
/**
 * Permission Matrix - Bảng ma trận quyền hạn theo Role
 * Admin xem tổng quan tất cả roles vs permissions trong 1 bảng
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/libs/Auth.php';
require_once __DIR__ . '/libs/PermissionManager.php';

$auth = new Auth($conn);
$auth->requireAuthWeb();

if (!$auth->isSuperAdmin() && !$auth->hasPermission('roles.assign_perm')) {
    $_SESSION['error'] = 'Bạn không có quyền xem Ma trận Quyền hạn.';
    header('Location: /web_QLSV/admin/Dashboard.php');
    exit;
}

$pageTitle = 'Ma trận Quyền hạn';

// Lấy tất cả roles (trừ super_admin)
$roles = $conn->query("
    SELECT id, code, name, color FROM roles 
    WHERE code != 'super_admin'
    ORDER BY id
")->fetch_all(MYSQLI_ASSOC);

// Lấy tất cả permissions grouped
$permsQuery = $conn->query("
    SELECT p.*, pg.name AS group_name, pg.icon AS group_icon, pg.code AS group_code
    FROM permissions p
    INNER JOIN permission_groups pg ON pg.id = p.group_id
    ORDER BY pg.sort_order, p.id
");
$allPerms = $permsQuery->fetch_all(MYSQLI_ASSOC);

// Lấy toàn bộ role_permissions (để build matrix)
$rpQuery = $conn->query("SELECT role_id, permission_id FROM role_permissions");
$matrix = [];
while ($row = $rpQuery->fetch_assoc()) {
    $matrix[$row['role_id']][$row['permission_id']] = true;
}

// Group permissions
$permsByGroup = [];
foreach ($allPerms as $p) {
    $permsByGroup[$p['group_code']]['name']  = $p['group_name'];
    $permsByGroup[$p['group_code']]['icon']  = $p['group_icon'];
    $permsByGroup[$p['group_code']]['perms'][] = $p;
}

include __DIR__ . '/views/layout/header.php';
?>

<style>
.matrix-table {
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.82rem;
    width: 100%;
}
.matrix-table thead th {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 10px 12px;
    text-align: center;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
    font-size: 0.78rem;
}
.matrix-table thead th:first-child {
    text-align: left;
    min-width: 220px;
    position: sticky;
    left: 0;
    z-index: 20;
}
.matrix-table .group-header-row td {
    background: linear-gradient(135deg, #f0f4ff, #e8eaf6);
    font-weight: 700;
    color: #3f51b5;
    padding: 8px 14px;
    position: sticky;
    left: 0;
    font-size: 0.82rem;
}
.matrix-table tbody tr:hover td { background: #f0f4ff !important; }
.matrix-table td {
    padding: 6px 8px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}
.matrix-table td:first-child {
    position: sticky;
    left: 0;
    background: white;
    z-index: 5;
    border-right: 2px solid #e8eaf6;
    padding-left: 20px;
}
.matrix-table tr:hover td:first-child { background: #f0f4ff; }
.perm-code-sm {
    font-family: monospace;
    font-size: 0.7rem;
    color: #0d6efd;
    background: #e8f4fd;
    padding: 1px 5px;
    border-radius: 3px;
}
.check-cell { text-align: center; }
.check-icon { font-size: 1.1rem; cursor: pointer; transition: transform 0.15s; }
.check-icon:hover { transform: scale(1.3); }
.check-on  { color: #198754; }
.check-off { color: #dee2e6; }
.role-header-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 6px;
    color: white;
    font-size: 0.72rem;
    font-weight: 600;
}
.matrix-wrapper {
    overflow-x: auto;
    max-height: 75vh;
    overflow-y: auto;
    border: 1px solid #e3e6f0;
    border-radius: 10px;
}
.matrix-wrapper::-webkit-scrollbar { width: 6px; height: 6px; }
.matrix-wrapper::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
.legend-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
.stats-mini { font-size: 0.72rem; color: #6c757d; }
.btn-toggle-all {
    font-size: 0.72rem;
    padding: 2px 6px;
    border-radius: 4px;
    background: rgba(255,255,255,0.2);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
    cursor: pointer;
    transition: all 0.15s;
    display: block;
    margin: 3px auto 0;
    white-space: nowrap;
}
.btn-toggle-all:hover { background: rgba(255,255,255,0.4); }
.saving-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.4);
    display: flex; align-items: center; justify-content: center;
    z-index: 9999; display: none;
}
</style>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>
                Ma trận Quyền hạn
            </h2>
            <p class="text-muted mb-0">
                Xem và chỉnh sửa toàn bộ phân quyền theo từng vai trò — click vào ô để bật/tắt quyền
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="/web_QLSV/admin/roles.php" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-shield-lock me-1"></i> Quản lý Vai trò
            </a>
            <a href="/web_QLSV/admin/views/permissions/index.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-key me-1"></i> Danh sách Quyền
            </a>
            <button class="btn btn-success btn-sm" id="saveAllBtn" onclick="saveAll()">
                <i class="bi bi-save me-1"></i> Lưu tất cả thay đổi
            </button>
        </div>
    </div>

    <!-- Legend + Stats -->
    <div class="d-flex align-items-center gap-4 mb-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <span class="legend-dot bg-success"></span>
            <small>Đã có quyền</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="legend-dot bg-light border"></span>
            <small>Chưa có quyền</small>
        </div>
        <div class="ms-auto">
            <span class="badge bg-warning text-dark">
                <i class="bi bi-info-circle me-1"></i>
                Super Admin luôn có toàn quyền (không cần cấu hình)
            </span>
        </div>
    </div>

    <!-- Matrix Table -->
    <div class="matrix-wrapper" id="matrixWrapper">
        <table class="matrix-table" id="permMatrix">
            <thead>
                <tr>
                    <th style="text-align:left; vertical-align:bottom">
                        <div class="mb-1">Quyền hạn</div>
                        <small class="d-block" style="opacity:.8; font-weight:400">(<?= count($allPerms) ?> quyền)</small>
                    </th>
                    <?php foreach ($roles as $r): ?>
                    <th>
                        <div>
                            <span class="role-header-badge" style="background:<?= htmlspecialchars($r['color']) ?>">
                                <?= htmlspecialchars($r['name']) ?>
                            </span>
                        </div>
                        <button class="btn-toggle-all" onclick="toggleRoleAll(<?= $r['id'] ?>, this)">
                            <i class="bi bi-toggle2-off me-1"></i>Đảo ngược
                        </button>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($permsByGroup as $gCode => $group):
                    $groupPerms = $group['perms'];
                ?>
                <!-- GROUP HEADER -->
                <tr class="group-header-row">
                    <td colspan="<?= count($roles) + 1 ?>">
                        <i class="bi <?= htmlspecialchars($group['icon']) ?> me-2"></i>
                        <?= htmlspecialchars($group['name']) ?>
                        <span class="badge bg-primary ms-2"><?= count($groupPerms) ?></span>
                    </td>
                </tr>

                <?php foreach ($groupPerms as $p): ?>
                <tr data-perm-id="<?= $p['id'] ?>">
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
                        <span class="perm-code-sm"><?= htmlspecialchars($p['code']) ?></span>
                        <?php if (!empty($p['description'])): ?>
                        <div class="text-muted" style="font-size:0.72rem"><?= htmlspecialchars($p['description']) ?></div>
                        <?php endif; ?>
                    </td>
                    <?php foreach ($roles as $r):
                        $hasIt = isset($matrix[$r['id']][$p['id']]);
                    ?>
                    <td class="check-cell" 
                        data-role-id="<?= $r['id'] ?>" 
                        data-perm-id="<?= $p['id'] ?>"
                        data-original="<?= $hasIt ? '1' : '0' ?>"
                        data-current="<?= $hasIt ? '1' : '0' ?>"
                        onclick="toggleCell(this)">
                        <i class="bi check-icon <?= $hasIt ? 'bi-check-circle-fill check-on' : 'bi-circle check-off' ?>"></i>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer action bar -->
    <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded">
        <div id="changeCount" class="text-muted small">
            <i class="bi bi-info-circle me-1"></i>Chưa có thay đổi nào
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="undoAll()">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Hoàn tác tất cả
            </button>
            <button class="btn btn-success" id="saveAllBtn2" onclick="saveAll()">
                <i class="bi bi-save me-1"></i> Lưu tất cả thay đổi
            </button>
        </div>
    </div>
</div>

<!-- Saving overlay -->
<div class="saving-overlay" id="savingOverlay">
    <div class="bg-white rounded p-4 text-center shadow">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <div class="fw-semibold">Đang lưu...</div>
    </div>
</div>

<script>
const API = '/web_QLSV/admin/api/router.php';
let pendingChanges = {}; // { roleId: { add: [permIds], remove: [permIds] } }

// ─── TOGGLE CELL ───
function toggleCell(cell) {
    const roleId = parseInt(cell.dataset.roleId);
    const permId = parseInt(cell.dataset.permId);
    const cur    = cell.dataset.current === '1';
    const orig   = cell.dataset.original === '1';
    const newVal = !cur;

    cell.dataset.current = newVal ? '1' : '0';

    const icon = cell.querySelector('.check-icon');
    if (newVal) {
        icon.className = 'bi check-icon bi-check-circle-fill check-on';
    } else {
        icon.className = 'bi check-icon bi-circle check-off';
    }

    // Track change
    if (!pendingChanges[roleId]) {
        pendingChanges[roleId] = { add: new Set(), remove: new Set() };
    }

    if (newVal !== orig) {
        // Changed from original
        if (newVal) {
            pendingChanges[roleId].add.add(permId);
            pendingChanges[roleId].remove.delete(permId);
        } else {
            pendingChanges[roleId].remove.add(permId);
            pendingChanges[roleId].add.delete(permId);
        }
    } else {
        // Reverted to original
        pendingChanges[roleId].add.delete(permId);
        pendingChanges[roleId].remove.delete(permId);
    }

    updateChangeCount();
    
    // Highlight cell
    cell.style.background = (newVal !== orig) ? (newVal ? '#d4edda' : '#f8d7da') : '';
}

// ─── TOGGLE ALL FOR A ROLE ───
function toggleRoleAll(roleId, btn) {
    const cells = document.querySelectorAll(`td[data-role-id="${roleId}"]`);
    const allOn = Array.from(cells).every(c => c.dataset.current === '1');
    cells.forEach(cell => {
        if ((allOn && cell.dataset.current === '1') || (!allOn && cell.dataset.current === '0')) {
            toggleCell(cell);
        }
    });
}

// ─── COUNT CHANGES ───
function updateChangeCount() {
    let total = 0;
    Object.values(pendingChanges).forEach(r => {
        total += r.add.size + r.remove.size;
    });
    const el = document.getElementById('changeCount');
    if (total === 0) {
        el.innerHTML = '<i class="bi bi-info-circle me-1"></i>Chưa có thay đổi nào';
        el.className = 'text-muted small';
    } else {
        el.innerHTML = `<i class="bi bi-exclamation-triangle me-1 text-warning"></i><strong>${total}</strong> thay đổi chưa lưu`;
        el.className = 'text-warning small';
    }
}

// ─── UNDO ALL ───
function undoAll() {
    if (!confirm('Hoàn tác tất cả thay đổi chưa lưu?')) return;
    document.querySelectorAll('td[data-role-id]').forEach(cell => {
        const orig = cell.dataset.original === '1';
        cell.dataset.current = orig ? '1' : '0';
        const icon = cell.querySelector('.check-icon');
        if (orig) {
            icon.className = 'bi check-icon bi-check-circle-fill check-on';
        } else {
            icon.className = 'bi check-icon bi-circle check-off';
        }
        cell.style.background = '';
    });
    pendingChanges = {};
    updateChangeCount();
}

// ─── SAVE ALL ───
async function saveAll() {
    let totalChanges = 0;
    Object.values(pendingChanges).forEach(r => {
        totalChanges += r.add.size + r.remove.size;
    });

    if (totalChanges === 0) {
        showToast('warning', 'Không có thay đổi nào để lưu!');
        return;
    }

    if (!confirm(`Lưu ${totalChanges} thay đổi phân quyền?`)) return;

    document.getElementById('savingOverlay').style.display = 'flex';

    // Với mỗi role có thay đổi, lấy toàn bộ perm IDs hiện tại rồi save
    const roleIds = Object.keys(pendingChanges).filter(rid => {
        const r = pendingChanges[rid];
        return r.add.size > 0 || r.remove.size > 0;
    });

    let successCount = 0;
    let errorCount = 0;

    for (const roleId of roleIds) {
        // Collect all current permission IDs for this role
        const cells = document.querySelectorAll(`td[data-role-id="${roleId}"]`);
        const permIds = Array.from(cells)
            .filter(c => c.dataset.current === '1')
            .map(c => parseInt(c.dataset.permId));

        const fd = new FormData();
        fd.append('role_id', roleId);
        permIds.forEach(id => fd.append('permission_ids[]', id));

        console.log(`[saveAll] Saving role ${roleId} with ${permIds.length} permissions`);

        try {
            const res = await fetch(`${API}?resource=roles&action=assign_permissions`, {
                method: 'POST',
                body: fd
            });

            console.log(`[saveAll] Role ${roleId}: HTTP ${res.status}`);

            if (!res.ok) {
                errorCount++;
                const txt = await res.text();
                console.error(`[saveAll] Network error for role ${roleId}: ${res.status}`, txt);
                showToast('error', `Lỗi mạng khi lưu vai trò ${roleId}: HTTP ${res.status}`);
                continue;
            }

            let data = null;
            try {
                data = await res.json();
                console.log(`[saveAll] Role ${roleId} JSON response:`, data);
            } catch (err) {
                console.error(`[saveAll] Invalid JSON for role ${roleId}:`, err);
                showToast('error', `Server trả về dữ liệu không hợp lệ cho role ${roleId}`);
                errorCount++;
                continue;
            }

            if (data && data.success) {
                successCount++;
                console.log(`[saveAll] Role ${roleId} saved successfully`);
                // Mark cells as saved
                cells.forEach(cell => {
                    cell.dataset.original = cell.dataset.current;
                    cell.style.background = '';
                });
            } else {
                errorCount++;
                const msg = (data && data.message) ? data.message : 'Lỗi không xác định';
                console.error(`[saveAll] Error for role ${roleId}:`, msg);
                showToast('error', `Lỗi khi lưu vai trò ${roleId}: ${msg}`);
            }
        } catch (e) {
            errorCount++;
            console.error(`[saveAll] Exception for role ${roleId}:`, e);
            showToast('error', `Lỗi khi gọi API cho role ${roleId}: ${e.message}`);
        }
    }

    console.log(`[saveAll] Completed: ${successCount} success, ${errorCount} errors`);
    document.getElementById('savingOverlay').style.display = 'none';
    pendingChanges = {};
    updateChangeCount();

    if (errorCount === 0) {
        showToast('success', `✅ Đã lưu thành công phân quyền cho ${successCount} vai trò!`);
    } else {
        showToast('error', `Lưu ${successCount} vai trò thành công, ${errorCount} vai trò thất bại.`);
    }
}

// ─── TOAST ───
function showToast(type, msg) {
    console.log(`[showToast] ${type}: ${msg}`);
    const colors = { success: '#198754', error: '#dc3545', warning: '#FFC107' };
    const textColors = { success: 'white', error: 'white', warning: '#000' };
    
    const bg = colors[type] || '#333';
    const textColor = textColors[type] || 'white';
    
    const el = document.createElement('div');
    el.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; z-index: 999999;
        background: ${bg}; color: ${textColor};
        padding: 16px 24px; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        font-size: .95rem; max-width: 420px; word-wrap: break-word;
        font-weight: 500;
        opacity: 1;
        transition: opacity 0.3s ease;
        animation: toastSlideIn 0.3s ease-out;
    `;
    el.textContent = msg;
    document.body.appendChild(el);
    
    console.log('[showToast] Toast created and appended to DOM');
    
    setTimeout(() => {
        el.style.opacity = '0';
        setTimeout(() => {
            el.remove();
            console.log('[showToast] Toast removed from DOM');
        }, 300);
    }, 4500);
}

// Keyboard shortcut: Ctrl+S to save
document.addEventListener('keydown', e => {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        saveAll();
    }
});
</script>

<style>
@keyframes slideIn {
    from { transform: translateX(100px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
@keyframes toastSlideIn {
    from { transform: translateX(500px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>

<?php include __DIR__ . '/views/layout/footer.php'; ?>

