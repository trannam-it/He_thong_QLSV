<?php
/**
 * View: Ký túc xá sinh viên
 * Biến: $student, $availableRooms, $myRegistrations, $msg, $error
 */
$pageTitle   = 'Ký túc xá';
$currentPage = 'student_dormitory';
$extraCss    = '
.room-card { transition:box-shadow .2s; cursor:pointer; }
.room-card:hover { box-shadow:0 4px 16px rgba(78,115,223,.25); }
.room-card.full { opacity:.6; }
.bed-dot { display:inline-block; width:12px; height:12px; border-radius:50%; margin:2px; }
.bed-dot.occupied { background:#e74a3b; }
.bed-dot.free { background:#1cc88a; }
';

function dormStatusBadge(string $status): string {
    return match($status) {
        'Active'    => '<span class="badge bg-success">Đang ở</span>',
        'Pending'   => '<span class="badge bg-warning text-dark">Chờ duyệt</span>',
        'Ended'     => '<span class="badge bg-secondary">Đã hết hạn</span>',
        'Cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
        default     => '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>',
    };
}
function roomTypeName(string $type): string {
    return match($type) {
        'Single' => 'Phòng đơn', 'Double' => 'Phòng đôi',
        'Triple' => 'Phòng 3 người', 'Quad' => 'Phòng 4 người', default => $type,
    };
}

// Tìm đăng ký đang active/pending
$activeReg = null;
foreach ($myRegistrations as $r) {
    if (in_array($r['status'], ['Active','Pending'])) { $activeReg = $r; break; }
}
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-building me-2"></i>Ký túc xá</h1>
    <div class="page-breadcrumb"><a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Ký túc xá</div>
</div>

<?php if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Trạng thái hiện tại -->
<?php if ($activeReg): ?>
<div class="alert alert-success mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h6 class="mb-1">
                <i class="bi bi-house-check-fill me-2"></i>
                <?= $activeReg['status'] === 'Active' ? 'Bạn đang ở phòng:' : 'Đơn đăng ký đang chờ duyệt:' ?>
            </h6>
            <strong>Phòng <?= htmlspecialchars($activeReg['room_number']) ?></strong>
            (<?= roomTypeName($activeReg['room_type'] ?? '') ?>)
            <br>
            <small>
                Giá: <?= number_format($activeReg['price_per_month'] ?? 0, 0, ',', '.') ?> đ/tháng &nbsp;|&nbsp;
                Từ: <?= date('d/m/Y', strtotime($activeReg['start_date'])) ?>
                <?php if ($activeReg['end_date']): ?> đến: <?= date('d/m/Y', strtotime($activeReg['end_date'])) ?><?php endif; ?>
            </small>
            &nbsp;&nbsp;<?= dormStatusBadge($activeReg['status']) ?>
        </div>
        <?php if (in_array($activeReg['status'], ['Pending','Active'])): ?>
        <form method="POST" onsubmit="return confirm('Xác nhận hủy đăng ký ký túc xá?')">
            <input type="hidden" name="registration_id" value="<?= $activeReg['registration_id'] ?>">
            <input type="hidden" name="cancel_registration" value="1">
            <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-x-lg me-1"></i>Hủy đăng ký
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách phòng còn chỗ -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Phòng ký túc xá còn chỗ trống</h5>
    </div>
    <div class="card-body">
        <?php if (empty($availableRooms)): ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-building-x fs-1"></i>
            <p class="mt-2">Hiện không có phòng trống nào.</p>
        </div>
        <?php else: ?>
        <div class="row g-3">
        <?php foreach ($availableRooms as $room): ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card room-card h-100 <?= $room['available_beds'] <= 0 ? 'full' : '' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold mb-0">Phòng <?= htmlspecialchars($room['room_number']) ?></h6>
                        <span class="badge bg-<?= $room['available_beds'] > 0 ? 'success' : 'danger' ?>">
                            <?= $room['available_beds'] > 0 ? 'Còn chỗ' : 'Hết chỗ' ?>
                        </span>
                    </div>
                    <p class="text-muted small mb-1"><?= roomTypeName($room['room_type']) ?></p>
                    <div class="mb-2">
                        <small class="text-muted">Số giường:</small>
                        <?php
                        // $occupied = (int)$room['capacity'] - (int)$room['available_beds'];
                        // for ($i = 0; $i < (int)$room['capacity']; $i++):
                            $occupied = (int)$room['total_beds'] - (int)$room['available_beds'];
                            for ($i = 0; $i < (int)$room['total_beds']; $i++):

                        ?>
                        <span class="bed-dot <?= $i < $occupied ? 'occupied' : 'free' ?>"></span>
                        <?php endfor; ?>
                        <small class="ms-1 text-muted"><?= $room['available_beds'] ?> còn trống</small>
                    </div>
                    <div class="fw-bold text-primary"><?= number_format($room['price_per_month'], 0, ',', '.') ?> đ/tháng</div>
                </div>
                <div class="card-footer bg-transparent">
                    <?php if ($room['available_beds'] > 0 && !$activeReg): ?>
                    <button class="btn btn-primary btn-sm w-100"
                            data-bs-toggle="modal" data-bs-target="#registerModal"
                            data-roomid="<?= $room['room_id'] ?>"
                            data-roomname="Phòng <?= htmlspecialchars($room['room_number']) ?>">
                        <i class="bi bi-plus-circle me-1"></i>Đăng ký phòng này
                    </button>
                    <?php elseif ($activeReg): ?>
                    <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                        <i class="bi bi-lock me-1"></i>Bạn đã có phòng
                    </button>
                    <?php else: ?>
                    <button class="btn btn-outline-danger btn-sm w-100" disabled>Hết chỗ</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Lịch sử đăng ký -->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử đăng ký ký túc xá</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($myRegistrations)): ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox fs-2"></i>
            <p class="mt-2">Chưa có lịch sử đăng ký.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Phòng</th><th>Loại phòng</th>
                        <th>Từ ngày</th><th>Đến ngày</th>
                        <th class="text-center">Giá/tháng</th>
                        <th class="text-center">Trạng thái</th><th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($myRegistrations as $reg): ?>
                <tr>
                    <td><strong>Phòng <?= htmlspecialchars($reg['room_number']) ?></strong></td>
                    <td><?= roomTypeName($reg['room_type'] ?? '') ?></td>
                    <td><?= date('d/m/Y', strtotime($reg['start_date'])) ?></td>
                    <td><?= $reg['end_date'] ? date('d/m/Y', strtotime($reg['end_date'])) : '—' ?></td>
                    <td class="text-center"><?= number_format($reg['price_per_month'] ?? 0, 0, ',', '.') ?> đ</td>
                    <td class="text-center"><?= dormStatusBadge($reg['status']) ?></td>
                    <td>
                        <?php if (in_array($reg['status'], ['Pending','Active'])): ?>
                        <form method="POST" onsubmit="return confirm('Hủy đăng ký này?')">
                            <input type="hidden" name="registration_id" value="<?= $reg['registration_id'] ?>">
                            <input type="hidden" name="cancel_registration" value="1">
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Đăng ký phòng -->
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Đăng ký ký túc xá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p id="modalRoomName" class="fw-bold text-primary mb-3"></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <input type="hidden" name="room_id" id="modalRoomId">
                    <input type="hidden" name="register_room" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Xác nhận đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('registerModal').addEventListener('show.bs.modal', e => {
    const btn = e.relatedTarget;
    document.getElementById('modalRoomId').value = btn.dataset.roomid;
    document.getElementById('modalRoomName').textContent = btn.dataset.roomname;
});
</script>
