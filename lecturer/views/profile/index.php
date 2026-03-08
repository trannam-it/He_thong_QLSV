<?php
/**
 * View: Thông tin cá nhân Giảng viên
 * Biến: $lecturer, $activeTab, $flashSuccess, $flashError
 */
$pageTitle = 'Thông tin cá nhân';
$extraCss  = '
.profile-hero{background:linear-gradient(135deg,#0f766e 0%,#0369a1 100%);border-radius:1rem;padding:2rem 2.5rem;color:#fff;margin-bottom:1.5rem;position:relative;overflow:hidden;}
.profile-hero::after{content:"";position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.07);}
.profile-avatar{width:88px;height:88px;border-radius:50%;border:4px solid rgba(255,255,255,.4);background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:2.4rem;color:#fff;flex-shrink:0;}
.nav-tabs .nav-link{color:#6b7280;}
.nav-tabs .nav-link.active{color:#0f766e;font-weight:600;border-bottom:2px solid #0f766e;}
#pwdStrengthBar{height:5px;border-radius:3px;transition:width .3s,background .3s;}
';
?>

<!-- Breadcrumb -->
<div class="page-header">
    <h1 class="page-title">Thông tin cá nhân</h1>
    <div class="page-breadcrumb">
        <a href="<?= lUrl() ?>">Trang chủ</a> / Thông tin cá nhân
    </div>
</div>

<!-- Flash -->
<?php if ($flashSuccess): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flashSuccess) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($flashError) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Hero -->
<div class="profile-hero mb-4">
    <div class="d-flex align-items-center gap-4 flex-wrap">
        <div class="profile-avatar"><i class="bi bi-person-fill"></i></div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h4 class="mb-0 fw-bold"><?= htmlspecialchars($lecturer['full_name']) ?></h4>
                <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-50">
                    <?= htmlspecialchars($lecturer['degree']) ?>
                </span>
            </div>
            <div class="opacity-90 small">
                <i class="bi bi-building me-1"></i><?= htmlspecialchars($lecturer['faculty_name']) ?>
                &nbsp;·&nbsp;
                <i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($lecturer['lecturer_code']) ?>
            </div>
        </div>
        <a href="<?= lUrl() ?>" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Về Dashboard
        </a>
    </div>
</div>

<div class="row g-4">

    <!-- LEFT: Tabbed forms -->
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white pb-0">
                <ul class="nav nav-tabs card-header-tabs border-0" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'info' ? 'active' : '' ?>"
                           href="#tabInfo" data-bs-toggle="tab">
                            <i class="bi bi-person me-1"></i>Thông tin cá nhân
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'password' ? 'active' : '' ?>"
                           href="#tabPassword" data-bs-toggle="tab">
                            <i class="bi bi-key me-1"></i>Đổi mật khẩu
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body tab-content pt-4">

                <!-- Tab: Info -->
                <div class="tab-pane fade <?= $activeTab === 'info' ? 'show active' : '' ?>" id="tabInfo">
                    <form method="POST" action="<?= lUrl('profile') ?>" novalidate>
                        <input type="hidden" name="action" value="update_info">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mã giảng viên</label>
                                <input type="text" class="form-control bg-light"
                                       value="<?= htmlspecialchars($lecturer['lecturer_code']) ?>" disabled>
                                <div class="form-text">Không thể thay đổi</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên</label>
                                <input type="text" class="form-control bg-light"
                                       value="<?= htmlspecialchars($lecturer['full_name']) ?>" disabled>
                                <div class="form-text">Liên hệ phòng đào tạo để thay đổi</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Học vị</label>
                                <input type="text" class="form-control bg-light"
                                       value="<?= htmlspecialchars($lecturer['degree']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Khoa</label>
                                <input type="text" class="form-control bg-light"
                                       value="<?= htmlspecialchars($lecturer['faculty_name']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" required
                                           value="<?= htmlspecialchars($lecturer['email'] ?? '') ?>"
                                           placeholder="example@university.edu.vn">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" name="phone" class="form-control" required
                                           pattern="[0-9]{10}" maxlength="10"
                                           value="<?= htmlspecialchars($lecturer['phone'] ?? '') ?>"
                                           placeholder="0xxxxxxxxx">
                                </div>
                                <div class="form-text">10 chữ số</div>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <a href="<?= lUrl() ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-1"></i>Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-floppy me-1"></i>Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab: Password -->
                <div class="tab-pane fade <?= $activeTab === 'password' ? 'show active' : '' ?>" id="tabPassword">
                    <form method="POST" action="<?= lUrl('profile') ?>" novalidate id="pwdForm">
                        <input type="hidden" name="action" value="change_password">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="current_password" id="curPwd"
                                           class="form-control" required placeholder="Nhập mật khẩu hiện tại">
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="togglePwd('curPwd',this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu mới <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="new_password" id="newPwd"
                                           class="form-control" required minlength="6"
                                           placeholder="Ít nhất 6 ký tự"
                                           oninput="checkStrength(this.value); checkMatch()">
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="togglePwd('newPwd',this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="mt-1">
                                    <div class="progress" style="height:5px">
                                        <div id="pwdStrengthBar" class="progress-bar" style="width:0%"></div>
                                    </div>
                                    <div id="pwdStrengthLabel" class="form-text"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="confirm_password" id="confPwd"
                                           class="form-control" required placeholder="Nhập lại mật khẩu"
                                           oninput="checkMatch()">
                                    <button type="button" class="btn btn-outline-secondary"
                                            onclick="togglePwd('confPwd',this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div id="matchMsg" class="form-text"></div>
                            </div>
                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="reset" class="btn btn-outline-secondary" onclick="resetPwdUI()">
                                    <i class="bi bi-x me-1"></i>Hủy
                                </button>
                                <button type="submit" class="btn btn-warning text-white">
                                    <i class="bi bi-shield-check me-1"></i>Đổi mật khẩu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- RIGHT: Summary + Quick links -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Thông tin tài khoản</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0 small">
                    <tbody>
                        <?php foreach ([
                            ['Mã GV',    '<code>' . htmlspecialchars($lecturer['lecturer_code']) . '</code>'],
                            ['Họ tên',   '<span class="fw-semibold">' . htmlspecialchars($lecturer['full_name']) . '</span>'],
                            ['Học vị',   '<span class="badge bg-success">' . htmlspecialchars($lecturer['degree']) . '</span>'],
                            ['Khoa',     htmlspecialchars($lecturer['faculty_name'])],
                            ['Email',    htmlspecialchars($lecturer['email'] ?? '—')],
                            ['ĐT',       htmlspecialchars($lecturer['phone'] ?? '—')],
                        ] as [$k, $v]): ?>
                        <tr class="border-bottom">
                            <th class="text-muted fw-semibold ps-3" style="width:46%"><?= $k ?></th>
                            <td class="pe-3"><?= $v ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-lightning me-2 text-warning"></i>Truy cập nhanh</h6>
            </div>
            <div class="card-body p-2">
                <div class="list-group list-group-flush">
                    <?php foreach ([
                        [lUrl('classes'),    'book-half',     'primary', 'Lớp đang dạy'],
                        [lUrl('grades'),     'pencil-square', 'success', 'Nhập điểm'],
                        [lUrl('attendance'), 'person-check',  'info',    'Điểm danh'],
                        [lUrl('register'),   'journal-plus',  'warning', 'Đăng ký lớp'],
                    ] as [$url, $icon, $color, $label]): ?>
                    <a href="<?= $url ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded border-0 px-2 py-2">
                        <span class="rounded-circle bg-<?= $color ?> bg-opacity-10 p-2 flex-shrink-0">
                            <i class="bi bi-<?= $icon ?> text-<?= $color ?>"></i>
                        </span>
                        <span class="small fw-semibold"><?= $label ?></span>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$extraJs = <<<'JS'
function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    const isText = inp.type === 'text';
    inp.type = isText ? 'password' : 'text';
    btn.querySelector('i').className = 'bi bi-' + (isText ? 'eye' : 'eye-slash');
}
function checkStrength(val) {
    const bar = document.getElementById('pwdStrengthBar');
    const lbl = document.getElementById('pwdStrengthLabel');
    let s = 0, cls = '', txt = '';
    if (val.length >= 6) s++;
    if (val.length >= 10) s++;
    if (/[A-Z]/.test(val)) s++;
    if (/[0-9]/.test(val)) s++;
    if (/[^A-Za-z0-9]/.test(val)) s++;
    if (s <= 1) { cls = 'bg-danger';  txt = 'Yếu'; }
    else if (s <= 3) { cls = 'bg-warning'; txt = 'Trung bình'; }
    else { cls = 'bg-success'; txt = 'Mạnh'; }
    bar.style.width = (s * 20) + '%';
    bar.className = 'progress-bar ' + cls;
    lbl.textContent = val ? txt : '';
}
function checkMatch() {
    const n = document.getElementById('newPwd').value;
    const c = document.getElementById('confPwd').value;
    const el = document.getElementById('matchMsg');
    if (!c) { el.textContent = ''; return; }
    el.textContent = n === c ? '✓ Khớp' : '✗ Không khớp';
    el.className = 'form-text ' + (n === c ? 'text-success' : 'text-danger');
}
function resetPwdUI() {
    document.getElementById('pwdStrengthBar').style.width = '0%';
    document.getElementById('pwdStrengthLabel').textContent = '';
    document.getElementById('matchMsg').textContent = '';
}
JS;
?>
