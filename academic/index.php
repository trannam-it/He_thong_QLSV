<?php
/**
 * Academic Module - Entry Point (Quản lý Đào tạo)
 *
 * Entry point duy nhất cho module Quản lý Đào tạo.
 * Tất cả URL đều đi qua file này và AcademicRouter sẽ điều hướng
 * đến controller phù hợp.
 *
 * URL Pattern:
 *   /web_QLSV/academic/                       → Dashboard
 *   /web_QLSV/academic/?page=students         → Quản lý sinh viên
 *   /web_QLSV/academic/?page=subjects         → Quản lý học phần
 *   /web_QLSV/academic/?page=classes          → Quản lý lớp học
 *   /web_QLSV/academic/?page=semesters        → Quản lý học kỳ
 *   /web_QLSV/academic/?page=enrollments      → Quản lý đăng ký
 *   /web_QLSV/academic/?page=grades           → Quản lý điểm
 *   /web_QLSV/academic/?page=schedule         → Thời khóa biểu
 *   /web_QLSV/academic/?page=reports          → Báo cáo thống kê
 *   /web_QLSV/academic/?page=profile          → Hồ sơ cá nhân
 */

// ── 1. Khởi động Session ──────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 2. Load Config (DB + BASE_URL) ───────────────────────────────────────────
$base = dirname(__DIR__);  // thư mục gốc web_QLSV
require_once $base . '/config/config.php';

// ── 3. Load AppRouter và guard module ────────────────────────────────────────
require_once $base . '/core/AppRouter.php';
AppRouter::guardModule(['academic_admin']);

// ── 4. Load Router ────────────────────────────────────────────────────────────
require_once __DIR__ . '/Router.php';

// ── 5. Tạo Router và dispatch ─────────────────────────────────────────────────
$router = new AcademicRouter($base);
$router->dispatch();
