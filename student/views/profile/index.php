<?php
/**
 * View: Thông tin cá nhân sinh viên
 * Biến: $student, $gpaData, $activeTab, $success, $error
 */
$pageTitle   = 'Thông tin cá nhân';
$currentPage = 'student_profile';
$extraCss    = '
.profile-hero {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 1rem; padding: 2rem; color: #fff;
    margin-bottom: 1.5rem; position: relative; overflow: hidden;
}
.profile-hero::after {
    content:""; position:absolute; right:-60px; top:-60px;
    width:220px; height:220px; border-radius:50%;
    background:rgba(255,255,255,.08);
}
.profile-avatar-placeholder {
    width:88px; height:88px; border-radius:50%;
    border:4px solid rgba(255,255,255,.4);
    background:rgba(255,255,255,.2);
    display:flex; align-items:center; justify-content:center;
    font-size:2.4rem; color:#fff;
}
.info-field { padding:.6rem 0; border-bottom:1px solid #f0f0f0; }
.info-field:last-child { border-bottom:none; }
.info-field-label {
    font-size:.78rem; color:#6c757d; font-weight:600;
    text-transform:uppercase; letter-spacing:.05em; margin-bottom:.15rem;
}
.pwd-strength { height:4px; border-radius:2px; margin-top:6px; transition:all .3s; }
';
?>

<!-- Breadcrumb -->
<div class="page-header">
    <h1 class="page-title">Thông tin cá nhân</h1>
    <div class="page-breadcrumb">
        <a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Thông tin cá nhân
    </div>
</div>

<!-- Flash Messages -->
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Profile Hero -->
<div class="profile-hero">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="profile-avatar-placeholder flex-shrink-0">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="flex-grow-1">
            <h4 class="mb-0 fw-bold"><?= htmlspecialchars($student['full_name']) ?></h4>
            <div class="d-flex flex-wrap gap-2 mt-1 align-items-center">
                <span class="badge bg-white text-dark fw-semibold">
                    <i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($student['student_code']) ?>
                </span>
                <span class="badge bg-white text-dark fw-semibold">
                    <i class="bi bi-building me-1"></i><?= htmlspecialchars($student['faculty_name'] ?? '—') ?>
                </span>
                <span class="badge <?= $student['status'] === 'Studying' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= $student['status'] === 'Studying' ? 'Đang học' : htmlspecialchars($student['status']) ?>
                </span>
            </div>
        </div>
        <div class="text-end d-none d-md-block">
            <div style="font-size:.8rem;opacity:.8">GPA hiện tại</div>
            <div style="font-size:2rem;font-weight:800;line-height:1">
                <?= $gpaData['gpa'] ? number_format($gpaData['gpa'], 1) : '—' ?>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card primary">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Điểm GPA</div>
                    <div class="stat-card-value"><?= $gpaData['gpa'] ? number_format($gpaData['gpa'], 2) : '—' ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-star-fill"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card success">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Học phần đã học</div>
                    <div class="stat-card-value"><?= (int)($gpaData['total_courses'] ?? 0) ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-journal-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card warning">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Tín chỉ tích lũy</div>
                    <div class="stat-card-value"><?= (int)($gpaData['total_credits'] ?? 0) ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-bookmark-star"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card info">
            <div class="stat-card-body">
                <div class="stat-card-text">
                    <div class="stat-card-label">Lớp sinh hoạt</div>
                    <div class="stat-card-value" style="font-size:1rem"><?= htmlspecialchars($student['base_class_name'] ?? '—') ?></div>
                </div>
                <div class="stat-card-icon"><i class="bi bi-people-fill"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Main body -->
<div class="row g-4">

    <!-- Left: Tabbed forms -->
    <div class="col-lg-8">
        <div class="content-card">
            <!-- Tab nav -->
            <div class="content-card-header p-0">
                <ul class="nav nav-tabs px-3 pt-3 border-0" id="profileTabs">
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab !== 'password' ? 'active' : '' ?>"
                           data-bs-toggle="tab" href="#tabInfo">
                            <i class="bi bi-person-lines-fill me-1"></i>Thông tin cá nhân
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'password' ? 'active' : '' ?>"
                           data-bs-toggle="tab" href="#tabPassword">
                            <i class="bi bi-shield-lock me-1"></i>Đổi mật khẩu
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <!-- Tab: Info -->
                <div class="tab-pane fade <?= $activeTab !== 'password' ? 'show active' : '' ?>" id="tabInfo">
                    <div class="content-card-body">
                        <form method="POST" action="<?= BASE_URL ?>/student/?page=profile">
                            <input type="hidden" name="action" value="update_info">
                            <p class="text-muted small mb-3">
                                <i class="bi bi-info-circle me-1"></i>
                                Các trường <span class="text-danger">*</span> có thể chỉnh sửa. Thông tin khác liên hệ phòng đào tạo.
                            </p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mã sinh viên</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                        <input type="text" class="form-control bg-light"
                                               value="<?= htmlspecialchars($student['student_code']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Họ và tên</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control bg-light"
                                               value="<?= htmlspecialchars($student['full_name']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Giới tính</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                        <input type="text" class="form-control bg-light"
                                               value="<?= $student['gender'] === 'Male' ? 'Nam' : 'Nữ' ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ngày sinh</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-cake2"></i></span>
                                        <input type="text" class="form-control bg-light"
                                               value="<?= $student['birth_date'] ? date('d/m/Y', strtotime($student['birth_date'])) : '—' ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <span class="text-danger">*</span> Email
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control"
                                               value="<?= htmlspecialchars($student['email']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <span class="text-danger">*</span> Số điện thoại
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="phone" class="form-control"
                                               value="<?= htmlspecialchars($student['phone'] ?? '') ?>"
                                               pattern="[0-9]{10}" maxlength="10" required>
                                    </div>
                                    <div class="form-text">10 chữ số, không có dấu chấm hoặc gạch ngang</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="<?= BASE_URL ?>/student/" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg me-1"></i>Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-floppy me-1"></i>Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tab: Password -->
                <div class="tab-pane fade <?= $activeTab === 'password' ? 'show active' : '' ?>" id="tabPassword">
                    <div class="content-card-body">
                        <form method="POST" action="<?= BASE_URL ?>/student/?page=profile" id="frmPwd">
                            <input type="hidden" name="action" value="change_password">
                            <p class="text-muted small mb-3">
                                <i class="bi bi-shield-check me-1"></i>
                                Mật khẩu mới phải ít nhất 6 ký tự.
                            </p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="current_password" id="curPwd"
                                               class="form-control" required placeholder="Nhập mật khẩu hiện tại">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('curPwd',this)">
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
                                               placeholder="Ít nhất 6 ký tự" oninput="checkStrength(this)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('newPwd',this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="pwd-strength bg-secondary" id="pwdBar" style="width:0%"></div>
                                    <div class="form-text" id="pwdHint"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" name="confirm_password" id="confPwd"
                                               class="form-control" required placeholder="Nhập lại mật khẩu mới"
                                               oninput="checkMatch()">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('confPwd',this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text" id="matchHint"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Nhập lại
                                </button>
                                <button type="submit" class="btn btn-warning text-white">
                                    <i class="bi bi-shield-lock me-1"></i>Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- /tab-content -->
        </div>
    </div><!-- /col-lg-8 -->

    <!-- Right: Academic info + Quick links -->
    <div class="col-lg-4">
        <!-- Academic Info -->
        <div class="content-card mb-4">
            <div class="content-card-header">
                <h5 class="content-card-title">
                    <i class="bi bi-mortarboard me-2"></i>Thông tin học vụ
                </h5>
            </div>
            <div class="content-card-body">
                <div class="info-field">
                    <div class="info-field-label">Khoa</div>
                    <div class="fw-semibold"><?= htmlspecialchars($student['faculty_name'] ?? '—') ?></div>
                </div>
                <div class="info-field">
                    <div class="info-field-label">Lớp sinh hoạt</div>
                    <div class="fw-semibold"><?= htmlspecialchars($student['base_class_name'] ?? 'Chưa phân lớp') ?></div>
                </div>
                <div class="info-field">
                    <div class="info-field-label">Trạng thái</div>
                    <span class="badge <?= getStatusBadgeClass($student['status']) ?>">
                        <?= $student['status'] === 'Studying' ? 'Đang học' : htmlspecialchars($student['status']) ?>
                    </span>
                </div>
                <div class="info-field">
                    <div class="info-field-label">GPA tích lũy</div>
                    <div class="fw-bold fs-5 text-primary"><?= $gpaData['gpa'] ? number_format($gpaData['gpa'], 2) : '—' ?></div>
                </div>
                <div class="info-field">
                    <div class="info-field-label">Tín chỉ hoàn thành</div>
                    <div class="fw-semibold"><?= (int)($gpaData['total_credits'] ?? 0) ?> TC</div>
                </div>
                <div class="info-field">
                    <div class="info-field-label">Tổng học phần</div>
                    <div class="fw-semibold"><?= (int)($gpaData['total_courses'] ?? 0) ?> môn</div>
                </div>
            </div>
        </div>

        <!-- Quick links -->
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="content-card-title">
                    <i class="bi bi-lightning-charge me-2"></i>Truy cập nhanh
                </h5>
            </div>
            <div class="content-card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>/student/?page=grades" class="btn btn-outline-primary btn-sm text-start">
                    <i class="bi bi-bar-chart-line me-2"></i>Xem kết quả học tập
                </a>
                <a href="<?= BASE_URL ?>/student/?page=enrollment" class="btn btn-outline-success btn-sm text-start">
                    <i class="bi bi-journal-plus me-2"></i>Đăng ký học phần
                </a>
                <a href="<?= BASE_URL ?>/student/?page=schedule" class="btn btn-outline-info btn-sm text-start">
                    <i class="bi bi-calendar3 me-2"></i>Xem lịch học
                </a>
                <hr class="my-1">
                <div class="alert alert-light border mb-0 py-2">
                    <small class="text-muted">
                        <i class="bi bi-telephone me-1"></i>Phòng đào tạo: <strong>024.xxxx.xxxx</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$extraJs = '
function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    const icon = btn.querySelector("i");
    if (inp.type === "password") {
        inp.type = "text";
        icon.classList.replace("bi-eye","bi-eye-slash");
    } else {
        inp.type = "password";
        icon.classList.replace("bi-eye-slash","bi-eye");
    }
}
function checkStrength(inp) {
    const val = inp.value;
    const bar = document.getElementById("pwdBar");
    const hint = document.getElementById("pwdHint");
    let score = 0;
    if (val.length >= 6) score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        {w:"20%",cls:"bg-danger",text:"Rất yếu"},
        {w:"40%",cls:"bg-warning",text:"Yếu"},
        {w:"60%",cls:"bg-info",text:"Trung bình"},
        {w:"80%",cls:"bg-primary",text:"Mạnh"},
        {w:"100%",cls:"bg-success",text:"Rất mạnh"},
    ];
    const lv = levels[Math.max(0, score - 1)] || levels[0];
    bar.style.width = val ? lv.w : "0%";
    bar.className = "pwd-strength " + (val ? lv.cls : "bg-secondary");
    hint.textContent = val ? lv.text : "";
    checkMatch();
}
function checkMatch() {
    const n = document.getElementById("newPwd").value;
    const c = document.getElementById("confPwd").value;
    const h = document.getElementById("matchHint");
    if (!c) { h.textContent = ""; return; }
    if (n === c) h.innerHTML = "<span class=\"text-success\"><i class=\"bi bi-check-circle me-1\"></i>Khớp</span>";
    else         h.innerHTML = "<span class=\"text-danger\"><i class=\"bi bi-x-circle me-1\"></i>Chưa khớp</span>";
}
';
?>
