<?php
/**
 * View: Quản lý Sinh viên (Academic)
 */
$pageTitle = 'Sinh viên';
$statusMap = ['Studying'=>'Đang học','Graduated'=>'Đã tốt nghiệp','Suspended'=>'Tạm dừng','Expelled'=>'Đuổi học'];
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-person-lines-fill me-2"></i>Danh sách Sinh viên</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Sinh viên</div>
</div>

<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="get" action="<?= aUrl('students') ?>" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="students">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Mã SV, tên, email..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <?php foreach ($statusMap as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $filterStatus === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Khoa</label>
                <select name="faculty_id" class="form-select">
                    <option value="0">Tất cả khoa</option>
                    <?php foreach ($faculties as $f): ?>
                    <option value="<?= $f['faculty_id'] ?>" <?= $filterFaculty == $f['faculty_id'] ? 'selected' : '' ?>><?= htmlspecialchars($f['faculty_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Lọc</button>
            </div>
        </form>
    </div>
</div>

<!-- Student Detail Modal -->
<?php if ($studentDetail): ?>
<div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i>Chi tiết Sinh viên: <?= htmlspecialchars($studentDetail['first_name'] . ' ' . $studentDetail['last_name']) ?></h5>
                <a href="<?= aUrl('students') ?><?= $search ? '&search='.urlencode($search) : '' ?>" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th width="40%">Mã SV:</th><td><?= htmlspecialchars($studentDetail['student_code']) ?></td></tr>
                            <tr><th>Họ tên:</th><td><?= htmlspecialchars($studentDetail['first_name'] . ' ' . $studentDetail['last_name']) ?></td></tr>
                            <tr><th>Email:</th><td><?= htmlspecialchars($studentDetail['email']) ?></td></tr>
                            <tr><th>Điện thoại:</th><td><?= htmlspecialchars($studentDetail['phone'] ?? '—') ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr><th width="40%">Khoa:</th><td><?= htmlspecialchars($studentDetail['faculty_name'] ?? '—') ?></td></tr>
                            <tr><th>Lớp:</th><td><?= htmlspecialchars($studentDetail['base_class_name'] ?? '—') ?></td></tr>
                            <tr><th>Trạng thái:</th><td><span class="badge bg-<?= $studentDetail['status']==='Studying'?'success':'secondary' ?>"><?= $statusMap[$studentDetail['status']] ?? $studentDetail['status'] ?></span></td></tr>
                            <tr><th>Ngày sinh:</th><td><?= $studentDetail['birth_date'] ? date('d/m/Y', strtotime($studentDetail['birth_date'])) : '—' ?></td></tr>
                        </table>
                    </div>
                </div>
                <?php if ($studentGrades): ?>
                <h6 class="fw-bold mb-2"><i class="bi bi-bar-chart me-2"></i>Kết quả học tập</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light"><tr><th>Học phần</th><th>TC</th><th>Lớp</th><th>Học kỳ</th><th>Giữa kỳ</th><th>Cuối kỳ</th><th>Tổng</th><th>Chữ</th><th>Kết quả</th></tr></thead>
                        <tbody>
                            <?php foreach ($studentGrades as $g): ?>
                            <tr>
                                <td><?= htmlspecialchars($g['subject_name']) ?></td>
                                <td class="text-center"><?= $g['credits'] ?></td>
                                <td><?= htmlspecialchars($g['class_code']) ?></td>
                                <td><?= htmlspecialchars($g['semester_name']) ?></td>
                                <td class="text-center"><?= $g['midterm_score'] ?? '—' ?></td>
                                <td class="text-center"><?= $g['final_score'] ?? '—' ?></td>
                                <td class="text-center fw-bold"><?= $g['total_score'] ?? '—' ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= $g['letter_grade'] ?? '—' ?></span></td>
                                <td class="text-center"><span class="badge bg-<?= $g['is_passed'] ? 'success' : 'danger' ?>"><?= $g['is_passed'] ? 'Qua' : 'Trượt' ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">Chưa có kết quả học tập.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Students Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Danh sách sinh viên <span class="badge bg-primary ms-2"><?= count($students) ?></span></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã SV</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Khoa</th>
                        <th>Lớp</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($s['student_code']) ?></span></td>
                        <td><?= htmlspecialchars($s['full_name']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= htmlspecialchars($s['faculty_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($s['base_class_name'] ?? '—') ?></td>
                        <td>
                            <?php
                            $sc2 = ['Studying'=>'success','Graduated'=>'info','Suspended'=>'warning','Expelled'=>'danger'];
                            ?>
                            <span class="badge bg-<?= $sc2[$s['status']] ?? 'secondary' ?>"><?= $statusMap[$s['status']] ?? $s['status'] ?></span>
                        </td>
                        <td>
                            <a href="<?= aUrl('students') ?>?detail=<?= $s['student_id'] ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filterStatus) ?>&faculty_id=<?= $filterFaculty ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Không tìm thấy sinh viên nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
