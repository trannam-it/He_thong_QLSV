<?php
session_start();
/* Increase limits for large DB dumps */
@ini_set('max_execution_time', 300);
@ini_set('memory_limit', '256M');

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/admin_helper.php';

authCheck(['super_admin']);

$pageTitle   = 'Sao lưu & Khôi phục';
$adminInfo   = getAdminInfo($conn, $_SESSION['user_id']);
$backupDir   = __DIR__ . '/../../backups/';
$backupUrl   = '/web_QLSV/admin/backups/';

/* Ensure backup directory exists and is protected */
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    file_put_contents($backupDir . '.htaccess', "Order Allow,Deny\nDeny from all\n");
}

/* ──────────────────────────────────────
   Pure-PHP SQL dump generator
   ────────────────────────────────────── */
function generateSqlDump($conn, $dbName): string {
    $out  = '';
    $out .= "-- ==============================================\n";
    $out .= "-- Database Backup: $dbName\n";
    $out .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $out .= "-- ==============================================\n\n";
    $out .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $out .= "SET FOREIGN_KEY_CHECKS = 0;\n";
    $out .= "SET NAMES utf8mb4;\n\n";

    $tables = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetch_all(MYSQLI_NUM);

    foreach ($tables as $row) {
        $table = $row[0];
        $out  .= "-- ----------------------------------------\n";
        $out  .= "-- Table: `$table`\n";
        $out  .= "-- ----------------------------------------\n\n";

        // CREATE TABLE
        $createRes = $conn->query("SHOW CREATE TABLE `$table`");
        $createRow = $createRes->fetch_assoc();
        $createSql = $createRow['Create Table'] ?? '';
        $out .= "DROP TABLE IF EXISTS `$table`;\n";
        $out .= $createSql . ";\n\n";

        // Data rows
        $dataRes = $conn->query("SELECT * FROM `$table`");
        if ($dataRes && $dataRes->num_rows > 0) {
            // Get columns
            $colNames = [];
            $fieldInfo = $dataRes->fetch_fields();
            foreach ($fieldInfo as $f) {
                $colNames[] = "`{$f->name}`";
            }
            $colList = implode(', ', $colNames);

            while ($dataRow = $dataRes->fetch_assoc()) {
                $vals = [];
                foreach ($dataRow as $v) {
                    if ($v === null) {
                        $vals[] = 'NULL';
                    } else {
                        $vals[] = "'" . $conn->real_escape_string($v) . "'";
                    }
                }
                $out .= "INSERT INTO `$table` ($colList) VALUES (" . implode(', ', $vals) . ");\n";
            }
            $out .= "\n";
        }
    }

    // Stored procedures & triggers (optional keep-simple)
    $out .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    return $out;
}

$flash = ['type' => '', 'msg' => ''];

/* ──────────────────────────────────────
   POST handlers
   ────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* ── Create & Download backup ── */
    if ($action === 'create_backup') {
        $note     = preg_replace('/[^a-z0-9_\-]/i', '', trim($_POST['note'] ?? ''));
        $filename = 'backup_' . date('Ymd_His') . ($note ? "_$note" : '') . '.sql';
        $filepath = $backupDir . $filename;

        $sql = generateSqlDump($conn, $conn->query("SELECT DATABASE()")->fetch_row()[0]);
        file_put_contents($filepath, $sql);

        /* Stream download */
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    /* ── Download existing file ── */
    if ($action === 'download_backup') {
        $file = basename($_POST['filename'] ?? '');
        $path = $backupDir . $file;
        if ($file && file_exists($path) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
        $_SESSION['bk_error'] = 'Tệp không tồn tại.';
        header('Location: index.php'); exit;
    }

    /* ── Delete backup file ── */
    if ($action === 'delete_backup') {
        $file = basename($_POST['filename'] ?? '');
        $path = $backupDir . $file;
        if ($file && file_exists($path) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            unlink($path);
            $_SESSION['bk_success'] = "Đã xóa tệp sao lưu: $file";
        } else {
            $_SESSION['bk_error'] = 'Không tìm thấy tệp.';
        }
        header('Location: index.php'); exit;
    }

    /* ── Restore from uploaded SQL file ── */
    if ($action === 'restore_backup') {
        $confirmed = ($_POST['confirm_restore'] ?? '') === '1';
        if (!$confirmed) {
            $_SESSION['bk_error'] = 'Vui lòng xác nhận trước khi khôi phục.';
            header('Location: index.php'); exit;
        }

        if (!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['bk_error'] = 'Không nhận được tệp. Vui lòng thử lại.';
            header('Location: index.php'); exit;
        }

        $origName = $_FILES['sql_file']['name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if ($ext !== 'sql') {
            $_SESSION['bk_error'] = 'Chỉ chấp nhận tệp .sql.';
            header('Location: index.php'); exit;
        }

        $sqlContent = file_get_contents($_FILES['sql_file']['tmp_name']);
        if (empty($sqlContent)) {
            $_SESSION['bk_error'] = 'Tệp SQL trống.';
            header('Location: index.php'); exit;
        }

        /* Auto-save the uploaded file to backups folder */
        $savedName = 'restore_' . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9_.\-]/i', '', $origName);
        file_put_contents($backupDir . $savedName, $sqlContent);

        /* Execute SQL statements — split on ";\n" */
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        $statements = array_filter(
            array_map('trim', preg_split('/;\s*\n/', $sqlContent)),
            fn($s) => $s !== '' && !str_starts_with($s, '--')
        );
        $ok = 0; $fail = 0; $errors = [];
        foreach ($statements as $stmt) {
            if ($conn->query($stmt)) {
                $ok++;
            } else {
                $fail++;
                $errors[] = substr($conn->error, 0, 120);
            }
        }
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");

        if ($fail === 0) {
            $_SESSION['bk_success'] = "Khôi phục thành công! $ok câu lệnh đã thực thi.";
        } else {
            $_SESSION['bk_success'] = "$ok câu lệnh thành công.";
            $_SESSION['bk_error']   = "$fail câu lệnh thất bại. Lỗi đầu tiên: {$errors[0]}";
        }
        header('Location: index.php'); exit;
    }
}

/* Read flash */
$flash['type'] = 'success'; $flash['msg'] = $_SESSION['bk_success'] ?? '';
if (!$flash['msg'])  { $flash['type'] = 'danger';  $flash['msg'] = $_SESSION['bk_error'] ?? ''; }
unset($_SESSION['bk_success'], $_SESSION['bk_error']);

/* List backup files */
$backupFiles = [];
foreach (glob($backupDir . '*.sql') as $f) {
    $backupFiles[] = [
        'name'     => basename($f),
        'size'     => filesize($f),
        'modified' => filemtime($f),
    ];
}
usort($backupFiles, fn($a, $b) => $b['modified'] - $a['modified']);

/* ────── Helper to format bytes ────── */
function formatBytes(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

include __DIR__ . '/../layout/header.php';
?>

<div class="content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title"><i class="bi bi-cloud-arrow-up me-2"></i>Sao lưu & Khôi phục</h1>
        <div class="page-breadcrumb">
            <a href="/web_QLSV/admin/Dashboard.php">Dashboard</a> / Sao lưu & Khôi phục
        </div>
    </div>

    <!-- Flash messages -->
    <?php if ($flash['msg']): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mb-4">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- ══════════════════════════════
             LEFT COLUMN
             ══════════════════════════════ -->
        <div class="col-lg-5">

            <!-- Create Backup Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-download me-2"></i>Tạo bản sao lưu mới
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Hệ thống sẽ tạo tệp <code>.sql</code> chứa toàn bộ cấu trúc và dữ liệu
                        của database <strong><?= htmlspecialchars($conn->query("SELECT DATABASE()")->fetch_row()[0]) ?></strong>.
                    </p>
                    <form method="POST">
                        <input type="hidden" name="action" value="create_backup">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Ghi chú (tùy chọn)</label>
                            <input type="text" name="note" class="form-control form-control-sm"
                                   placeholder="vd: truoc_cap_nhat, weekly…"
                                   pattern="[a-zA-Z0-9_\-]*"
                                   title="Chỉ dùng chữ, số, gạch dưới, gạch ngang">
                            <div class="form-text">Không dấu, không khoảng trắng</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-cloud-download me-2"></i>Tạo & tải về ngay
                        </button>
                    </form>
                </div>
            </div>

            <!-- Restore Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-cloud-upload me-2"></i>Khôi phục từ tệp SQL
                </div>
                <div class="card-body">
                    <div class="alert alert-warning py-2 small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Cảnh báo:</strong> Thao tác này sẽ <strong>ghi đè toàn bộ dữ liệu hiện tại</strong>.
                        Hãy chắc chắn đã có bản sao lưu trước khi tiến hành.
                    </div>
                    <form method="POST" enctype="multipart/form-data" id="restoreForm">
                        <input type="hidden" name="action" value="restore_backup">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Chọn tệp sao lưu (.sql)</label>
                            <input type="file" name="sql_file" class="form-control form-control-sm"
                                   accept=".sql" required
                                   id="sqlFileInput">
                            <div class="form-text" id="fileSizeInfo"></div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox"
                                   name="confirm_restore" value="1" id="confirmCheck">
                            <label class="form-check-label text-danger fw-semibold small" for="confirmCheck">
                                Tôi hiểu rằng dữ liệu hiện tại sẽ bị ghi đè
                            </label>
                        </div>
                        <button type="button" class="btn btn-danger w-100"
                                onclick="confirmRestore()">
                            <i class="bi bi-arrow-repeat me-2"></i>Khôi phục
                        </button>
                    </form>
                </div>
            </div>

        </div><!-- /left col -->

        <!-- ══════════════════════════════
             RIGHT COLUMN – File List
             ══════════════════════════════ -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="fw-semibold">
                        <i class="bi bi-archive me-1"></i>Danh sách bản sao lưu
                        <span class="badge bg-secondary ms-1"><?= count($backupFiles) ?></span>
                    </span>
                    <small class="text-muted">Lưu tại: <code>admin/backups/</code></small>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($backupFiles)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-archive fs-1 d-block mb-2 opacity-40"></i>
                        <p class="mb-0">Chưa có bản sao lưu nào</p>
                        <small>Nhấn "Tạo & tải về ngay" để tạo bản sao lưu đầu tiên</small>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0"
                               id="backupTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:34px" class="text-center">#</th>
                                    <th>Tên tệp</th>
                                    <th class="text-center" style="width:90px">Kích thước</th>
                                    <th class="text-center" style="width:145px">Thời gian</th>
                                    <th class="text-center" style="width:120px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($backupFiles as $i => $f): ?>
                            <tr>
                                <td class="text-center text-muted small"><?= $i + 1 ?></td>
                                <td>
                                    <i class="bi bi-file-earmark-code text-success me-1"></i>
                                    <span class="fw-semibold small"><?= htmlspecialchars($f['name']) ?></span>
                                </td>
                                <td class="text-center small text-muted">
                                    <?= formatBytes($f['size']) ?>
                                </td>
                                <td class="text-center small text-muted">
                                    <?= date('H:i d/m/Y', $f['modified']) ?>
                                </td>
                                <td class="text-center">
                                    <!-- Download -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action"   value="download_backup">
                                        <input type="hidden" name="filename" value="<?= htmlspecialchars($f['name']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                                title="Tải về">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </form>
                                    <!-- Delete -->
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger ms-1"
                                            title="Xóa bản sao lưu"
                                            onclick="confirmDeleteBackup('<?= htmlspecialchars($f['name'], ENT_QUOTES) ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($backupFiles)): ?>
                <div class="card-footer bg-white border-top-0 py-2 text-muted small">
                    Tổng dung lượng:
                    <strong><?= formatBytes(array_sum(array_column($backupFiles, 'size'))) ?></strong>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /row -->
</div><!-- /content-wrapper -->

<!-- Delete confirm modal -->
<div class="modal fade" id="deleteBackupModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Xóa bản sao lưu
                </h6>
            </div>
            <div class="modal-body pt-1">
                <p class="mb-0 small" id="deleteBackupBody"></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-danger btn-sm" id="deleteBackupConfirmBtn">Xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden delete form -->
<form method="POST" id="deleteBackupForm" style="display:none">
    <input type="hidden" name="action"   value="delete_backup">
    <input type="hidden" name="filename" id="deleteBackupFilename" value="">
</form>

<!-- Restore confirm modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 bg-danger text-white py-2">
                <h6 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Xác nhận khôi phục
                </h6>
            </div>
            <div class="modal-body">
                <p>Bạn sắp thực thi tệp SQL để <strong>khôi phục database</strong>.</p>
                <p class="text-danger mb-0">
                    <strong>Toàn bộ dữ liệu hiện tại sẽ bị ghi đè và không thể hoàn tác!</strong>
                    Đảm bảo bạn đã tạo bản sao lưu mới nhất trước khi tiếp tục.
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-danger" id="proceedRestoreBtn">
                    <i class="bi bi-arrow-repeat me-1"></i>Tiến hành khôi phục
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/* ── Delete backup ── */
var deleteModal   = new bootstrap.Modal(document.getElementById('deleteBackupModal'));
function confirmDeleteBackup(name) {
    document.getElementById('deleteBackupBody').textContent =
        'Bạn muốn xóa tệp "' + name + '"? Hành động này không thể hoàn tác.';
    document.getElementById('deleteBackupFilename').value = name;
    document.getElementById('deleteBackupConfirmBtn').onclick = function () {
        document.getElementById('deleteBackupForm').submit();
    };
    deleteModal.show();
}

/* ── Restore ── */
var restoreModal = new bootstrap.Modal(document.getElementById('restoreModal'));
function confirmRestore() {
    if (!document.getElementById('sqlFileInput').files.length) {
        alert('Vui lòng chọn tệp .sql trước.'); return;
    }
    if (!document.getElementById('confirmCheck').checked) {
        alert('Vui lòng tích vào ô xác nhận.'); return;
    }
    document.getElementById('proceedRestoreBtn').onclick = function () {
        document.getElementById('restoreForm').submit();
    };
    restoreModal.show();
}

/* ── Show file size in UI ── */
document.getElementById('sqlFileInput')?.addEventListener('change', function () {
    var info = document.getElementById('fileSizeInfo');
    if (this.files.length) {
        var sz = this.files[0].size;
        var display = sz >= 1048576 ? (sz/1048576).toFixed(2) + ' MB'
                    : sz >= 1024    ? (sz/1024).toFixed(1)    + ' KB'
                    : sz            + ' B';
        info.textContent = 'Kích thước: ' + display;
    } else {
        info.textContent = '';
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
