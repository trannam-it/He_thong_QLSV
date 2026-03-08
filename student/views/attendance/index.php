<?php
/**
 * View: Điểm danh sinh viên
 * Trạng thái: Module đang phát triển
 */
$pageTitle   = 'Điểm danh';
$currentPage = 'student_attendance';
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Điểm danh</h1>
    <div class="page-breadcrumb">
        <a href="<?= BASE_URL ?>/student/">Trang chủ</a> / Điểm danh
    </div>
</div>

<!-- Under Development Notice -->
<div class="row justify-content-center mt-4">
    <div class="col-md-8">
        <div class="content-card text-center py-5">
            <i class="bi bi-calendar-check text-warning" style="font-size:4rem;"></i>
            <h3 class="mt-3 mb-2">Tính năng đang phát triển</h3>
            <p class="text-muted mb-4">
                Module điểm danh đang được xây dựng và sẽ sớm được cập nhật.<br>
                Vui lòng quay lại sau.
            </p>
            <a href="<?= BASE_URL ?>/student/" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i>Quay về Dashboard
            </a>
        </div>
    </div>
</div>
