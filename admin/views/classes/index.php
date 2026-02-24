<?php
$pageTitle = "Quản lý Lớp Cơ Sở";
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<!-- PAGE HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">
            <i class="bi bi-diagram-3 me-2 text-primary"></i>
            Quản lý Lớp Cơ Sở
        </h4>
        <small class="text-muted">Quản lý lớp, sinh viên và giảng viên chủ nhiệm</small>
    </div>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle"></i> Thêm lớp
    </button>
</div>

<!-- TABLE CARD -->
<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Mã Lớp</th>
                        <th>Tên Lớp</th>
                        <th>Khoa</th>
                        <th>GVCN</th>
                        <th>Niên khóa</th>
                        <th class="text-center">Tổng SV</th>
                        <th class="text-center">Đang học</th>
                        <th class="text-center">Tốt nghiệp</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody id="baseClassesBody"></tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="paginationInfo" class="text-muted small"></div>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>

    </div>
</div>

<!-- ================= CREATE MODAL ================= -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Thêm Lớp Cơ Sở</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã lớp *</label>
                            <input name="base_class_code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên lớp *</label>
                            <input name="base_class_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Năm bắt đầu *</label>
                            <input type="number" name="start_year" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Năm kết thúc *</label>
                            <input type="number" name="end_year" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary">Tạo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
const API_URL = '/web_QLSV/admin/api/router.php';
let currentPage = 1;
const perPage = 10;

// ================= LOAD DATA =================
async function loadBaseClasses(page = 1) {
    currentPage = page;

    const res = await fetch(`${API_URL}?module=base_classes&action=index&page=${page}&limit=${perPage}`);
    const data = await res.json();

    const tbody = document.getElementById('baseClassesBody');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');

    tbody.innerHTML = '';
    pagination.innerHTML = '';

    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    Chưa có dữ liệu
                </td>
            </tr>
        `;
        paginationInfo.innerHTML = '';
        return;
    }

    // ===== Render table =====
    data.data.forEach((cls, index) => {
        tbody.innerHTML += `
            <tr>
                <td>${(page - 1) * perPage + index + 1}</td>
                <td><strong>${cls.base_class_code}</strong></td>
                <td>${cls.base_class_name}</td>
                <td>${cls.faculty_name ?? '-'}</td>
                <td>${cls.first_name ? cls.first_name + ' ' + cls.last_name : '-'}</td>
                <td>${cls.start_year} - ${cls.end_year}</td>
                <td class="text-center"><span class="badge rounded-pill bg-primary">${cls.total_students ?? 0}</span></td>
                <td class="text-center"><span class="badge rounded-pill bg-success">${cls.studying_students ?? 0}</span></td>
                <td class="text-center"><span class="badge rounded-pill bg-info">${cls.graduated_students ?? 0}</span></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-danger"
                        onclick="confirmDelete('base_classes', ${cls.base_class_id}, '${cls.base_class_name}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    // ===== Pagination logic =====
    const total = data.pagination?.total ?? 0;
    const totalPages = Math.ceil(total / perPage);

    const start = (page - 1) * perPage + 1;
    const end = Math.min(page * perPage, total);

    paginationInfo.innerHTML = `Hiển thị ${start} - ${end} trên ${total} lớp`;

    if (totalPages <= 1) return;

    // Prev
    pagination.innerHTML += `
        <li class="page-item ${page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadBaseClasses(${page - 1}); return false;">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
    `;

    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${i === page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadBaseClasses(${i}); return false;">
                    ${i}
                </a>
            </li>
        `;
    }

    // Next
    pagination.innerHTML += `
        <li class="page-item ${page === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadBaseClasses(${page + 1}); return false;">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `;
}

// ================= CREATE =================
document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = Object.fromEntries(new FormData(this).entries());

    const result = await apiCall('base_classes', 'store', 'POST', formData);

    if (result.success) {
        showToast('Tạo lớp thành công!');
        bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
        this.reset();
        loadBaseClasses(1);
    } else {
        showToast(result.message, 'error');
    }
});

// INIT
document.addEventListener('DOMContentLoaded', () => {
    loadBaseClasses();
});
</script>