<?php $pageTitle = 'Báo cáo thống kê'; ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-file-earmark-bar-graph me-2"></i>Báo cáo thống kê</h1>
    <div class="page-breadcrumb"><a href="<?= aUrl() ?>">Trang chủ</a> / Báo cáo</div>
</div>

<!-- Overview stats -->
<div class="row mb-4">
    <?php
    $statsData = [
        ['label'=>'Sinh viên đang học','value'=>$dashboardStats['total_students'],'icon'=>'bi-people','color'=>'primary'],
        ['label'=>'Giảng viên','value'=>$dashboardStats['total_lecturers'],'icon'=>'bi-person-badge','color'=>'success'],
        ['label'=>'Học phần','value'=>$dashboardStats['total_subjects'],'icon'=>'bi-book','color'=>'info'],
        ['label'=>'Lớp đang mở','value'=>$dashboardStats['total_active_classes'],'icon'=>'bi-door-open','color'=>'warning'],
    ];
    foreach ($statsData as $sd):
    ?>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-gradient-<?= $sd['color'] ?> text-white">
            <div class="stat-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-card-label"><?= $sd['label'] ?></div>
                        <div class="stat-card-value"><?= number_format($sd['value']) ?></div>
                    </div>
                    <i class="bi <?= $sd['icon'] ?> stat-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="reports">
            <div class="col-md-5">
                <label class="form-label">Chọn học kỳ để xem báo cáo điểm</label>
                <select name="semester_id" class="form-select">
                    <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['semester_id'] ?>" <?= $semesterId == $sem['semester_id'] ? 'selected' : '' ?>><?= htmlspecialchars($sem['semester_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Xem báo cáo</button></div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Grade Report by Semester -->
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-bar-chart me-2"></i>Kết quả điểm theo học kỳ: <?= htmlspecialchars($currentSem['semester_name'] ?? '') ?></h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Học phần</th><th class="text-center">TC</th><th class="text-center">Tổng SV</th><th class="text-center">TB điểm</th><th class="text-center">Đạt</th><th class="text-center">Trượt</th><th class="text-center">Tỷ lệ đạt</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gradeReport as $rp): ?>
                            <?php
                            $total = (int)$rp['total'];
                            $passed = (int)$rp['passed'];
                            $rate = $total > 0 ? round($passed/$total*100,1) : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($rp['subject_name']) ?><br><small class="text-muted"><?= $rp['class_code'] ?></small></td>
                                <td class="text-center"><?= $rp['credits'] ?></td>
                                <td class="text-center"><?= $total ?></td>
                                <td class="text-center"><?= number_format((float)($rp['avg_score'] ?? 0),1) ?></td>
                                <td class="text-center text-success fw-bold"><?= $passed ?></td>
                                <td class="text-center text-danger"><?= (int)$rp['failed'] ?></td>
                                <td class="text-center">
                                    <div class="progress" style="height:6px;min-width:60px;">
                                        <div class="progress-bar bg-<?= $rate>=80?'success':($rate>=50?'warning':'danger') ?>" style="width:<?= $rate ?>%"></div>
                                    </div>
                                    <small><?= $rate ?>%</small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($gradeReport)): ?><tr><td colspan="7" class="text-center text-muted py-4">Không có dữ liệu.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Stats by Faculty -->
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-building me-2"></i>Thống kê theo Khoa</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Khoa</th><th class="text-center">SV đang học</th><th class="text-center">Đăng ký HP</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollmentStats as $es): ?>
                            <tr>
                                <td><?= htmlspecialchars($es['faculty_name']) ?></td>
                                <td class="text-center"><?= number_format((int)$es['total_students']) ?></td>
                                <td class="text-center"><?= number_format((int)$es['total_enrollments']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
