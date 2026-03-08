<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../libs/Auth.php';
require_once __DIR__ . '/../../../includes/auth_check.php';

authCheck(['super_admin']);

// Quick stats
$stats = $conn->query("
    SELECT COUNT(*)                             AS total,
           COUNT(DISTINCT credit_hours)         AS faculties,
           ROUND(AVG(credit_hours),1)           AS avg_credits,
           SUM(prerequisite_code IS NOT NULL)   AS has_prereq
    FROM subjects
")->fetch_assoc();

$pageTitle = 'Quáº£n lÃ½ MÃ´n há»c';
include __DIR__ . '/../layout/header.php';
?>

<!-- ===== TOAST ===== -->
<div class="position-fixed top-0 end-0 p-3" style="z-index:9999">
    <div id="toast" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMsg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<div class="container-fluid py-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-book text-primary me-2"></i>Quáº£n lÃ½ MÃ´n há»c
            </h4>
            <small class="text-muted">ThÃªm, sá»­a, xÃ³a vÃ  tra cá»©u mÃ´n há»c</small>
        </div>
        <button class="btn btn-primary" onclick="openAddForm()"
                data-bs-toggle="modal" data-bs-target="#subjectModal">
            <i class="bi bi-plus-circle me-1"></i>ThÃªm mÃ´n há»c
        </button>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-book-half text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Tá»•ng mÃ´n há»c</div>
                        <div class="fs-4 fw-bold"><?= (int)$stats['total'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-building text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Loại tín chỉ</div>
                        <div class="fs-4 fw-bold"><?= (int)$stats['faculties'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="bi bi-bookmark-star text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">TB tÃ­n chá»‰</div>
                        <div class="fs-4 fw-bold"><?= $stats['avg_credits'] ?? 'â€”' ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-link-45deg text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">CÃ³ tiÃªn quyáº¿t</div>
                        <div class="fs-4 fw-bold"><?= (int)$stats['has_prereq'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH + FILTER -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="TÃ¬m mÃ£ mÃ´n hoáº·c tÃªn mÃ´nâ€¦" oninput="debounceSearch()">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="facultyFilter" class="form-select form-select-sm" onchange="loadSubjects(1)">
                        <option value="">-- Táº¥t cáº£ khoa --</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="sortSelect" class="form-select form-select-sm" onchange="loadSubjects(1)">
                        <option value="subject_id">Máº·c Ä‘á»‹nh</option>
                        <option value="subject_code">MÃ£ mÃ´n</option>
                        <option value="subject_name">TÃªn mÃ´n</option>
                        <option value="credit_hours">TÃ­n chá»‰</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="orderSelect" class="form-select form-select-sm" onchange="loadSubjects(1)">
                        <option value="DESC">Giáº£m dáº§n</option>
                        <option value="ASC">TÄƒng dáº§n</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary btn-sm w-100"
                            onclick="clearSearch()" title="XÃ³a bá»™ lá»c">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            <span class="fw-semibold text-muted small" id="resultCount"></span>
            <div id="loading" style="display:none">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <span class="ms-1 small text-muted">Äang táº£iâ€¦</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px" class="text-center">#</th>
                            <th style="width:110px">MÃ£ mÃ´n</th>
                            <th>TÃªn mÃ´n há»c</th>
                            <th class="text-center" style="width:90px">TÃ­n chá»‰</th>
                            <th style="width:190px">Khoa</th>
                            <th style="width:160px">TiÃªn quyáº¿t</th>
                            <th class="text-center" style="width:80px">MÃ´ táº£</th>
                            <th class="text-center" style="width:120px">Thao tÃ¡c</th>
                        </tr>
                    </thead>
                    <tbody id="subjectTable">
                        <tr><td colspan="8" class="text-center py-4">
                            <div class="spinner-border text-primary"></div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <nav class="d-flex justify-content-end">
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- ===== MODAL ADD/EDIT ===== -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-book me-2"></i>ThÃªm mÃ´n há»c
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="subjectForm" novalidate>
                    <input type="hidden" name="subject_id" id="subject_id">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                MÃ£ mÃ´n <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="subject_code" id="subject_code"
                                   class="form-control text-uppercase" required
                                   maxlength="20" placeholder="VD: CNTT101">
                            <div class="invalid-feedback">Vui lÃ²ng nháº­p mÃ£ mÃ´n.</div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                TÃªn mÃ´n há»c <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="subject_name" id="subject_name"
                                   class="form-control" required maxlength="100"
                                   placeholder="Nháº­p tÃªn mÃ´n há»c">
                            <div class="invalid-feedback">Vui lÃ²ng nháº­p tÃªn mÃ´n.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Sá»‘ tÃ­n chá»‰ <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="credit_hours" id="credit_hours"
                                   class="form-control" required min="1" max="12" placeholder="1â€“12">
                            <div class="invalid-feedback">TÃ­n chá»‰ tá»« 1â€“12.</div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Khoa <span class="text-danger">*</span>
                            </label>
                            <select name="faculty_id" id="faculty_id" class="form-select" required>
                                <option value="">-- Chá»n khoa --</option>
                            </select>
                            <div class="invalid-feedback">Vui lÃ²ng chá»n khoa.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">MÃ´n há»c tiÃªn quyáº¿t</label>
                            <select name="prerequisite_code" id="prerequisite_code" class="form-select">
                                <option value="">-- KhÃ´ng cÃ³ --</option>
                            </select>
                            <div class="form-text">
                                Sinh viÃªn pháº£i hoÃ n thÃ nh mÃ´n nÃ y trÆ°á»›c khi Ä‘Äƒng kÃ½.
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">MÃ´ táº£</label>
                            <textarea name="description" id="description" class="form-control"
                                      rows="3" placeholder="Ná»™i dung, má»¥c tiÃªu mÃ´n há»câ€¦"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Há»§y
                </button>
                <button class="btn btn-primary" id="btnSave" onclick="saveSubject()">
                    <i class="bi bi-floppy me-1"></i>LÆ°u
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL VIEW DESCRIPTION ===== -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTitle">Chi tiáº¿t mÃ´n há»c</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewBody"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">ÄÃ³ng</button>
            </div>
        </div>
    </div>
</div>

<script>
const API = '/web_QLSV/admin/api/router.php?module=subjects';
let currentPage = 1;
let searchTimer;

document.addEventListener('DOMContentLoaded', () => {
    loadFaculties();
    loadPrerequisites();
    loadSubjects();
});

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadSubjects(1), 350);
}

function loadSubjects(page = 1) {
    currentPage = page;
    document.getElementById('loading').style.display = '';

    const params = new URLSearchParams({
        action:     'index',
        page,
        search:     document.getElementById('searchInput').value,
        faculty_id: document.getElementById('facultyFilter').value,
        sort:       document.getElementById('sortSelect').value,
        order:      document.getElementById('orderSelect').value,
    });

    fetch(`${API}&${params}`)
        .then(r => r.json())
        .then(res => {
            document.getElementById('loading').style.display = 'none';
            if (!res.success) { showToast(res.message, 'danger'); return; }
            renderTable(res.data);
            if (res.pagination)
                renderPagination(+res.pagination.page, +res.pagination.pages, +res.pagination.total);
        })
        .catch(() => {
            document.getElementById('loading').style.display = 'none';
            showToast('Lá»—i káº¿t ná»‘i API', 'danger');
        });
}

function renderTable(data) {
    if (!data || !data.length) {
        document.getElementById('subjectTable').innerHTML =
            `<tr><td colspan="8" class="text-center text-muted py-5">
                <i class="bi bi-journal-x fs-2 d-block mb-2"></i>KhÃ´ng cÃ³ dá»¯ liá»‡u
             </td></tr>`;
        return;
    }

    const creditColor = c => +c >= 4 ? 'danger' : +c === 3 ? 'warning' : 'info';

    let html = '';
    data.forEach((s, i) => {
        const desc = s.description
            ? `<button class="btn btn-sm btn-outline-secondary" title="Xem mÃ´ táº£"
                       onclick="viewDetail(${s.subject_id})">
                   <i class="bi bi-eye"></i>
               </button>`
            : `<span class="text-muted small">â€”</span>`;

        const prereq = s.prerequisite_code
            ? `<span class="badge bg-warning text-dark">
                   <i class="bi bi-arrow-return-right me-1"></i>${s.prerequisite_code}
               </span>`
            : `<span class="text-muted small">â€”</span>`;

        html += `
        <tr>
            <td class="text-center text-muted small">${((currentPage-1)*10)+i+1}</td>
            <td><code class="text-primary fw-semibold">${s.subject_code}</code></td>
            <td class="fw-semibold">${s.subject_name}</td>
            <td class="text-center">
                <span class="badge bg-${creditColor(s.credit_hours)} rounded-pill px-3">
                    ${s.credit_hours} TC
                </span>
            </td>
            <td><small class="text-muted">${s.faculty_name ?? 'â€”'}</small></td>
            <td>${prereq}</td>
            <td class="text-center">${desc}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-warning me-1" title="Chá»‰nh sá»­a"
                        onclick="edit(${s.subject_id})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" title="XÃ³a"
                        onclick="del(${s.subject_id}, '${s.subject_name.replace(/'/g,"\\'")}')">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    document.getElementById('subjectTable').innerHTML = html;
}

function renderPagination(current, total, totalRows) {
    document.getElementById('resultCount').textContent =
        `Trang ${current}/${total} â€” ${totalRows} mÃ´n há»c`;

    const p = document.getElementById('pagination');
    if (total <= 1) { p.innerHTML = ''; return; }

    const range = 2;
    const start = Math.max(1, current - range);
    const end   = Math.min(total, current + range);
    let html = '';

    html += `<li class="page-item ${current===1?'disabled':''}">
        <a class="page-link" href="#" onclick="loadSubjects(${current-1});return false">â€¹</a></li>`;
    if (start > 1) html += `<li class="page-item disabled"><span class="page-link">â€¦</span></li>`;
    for (let i = start; i <= end; i++)
        html += `<li class="page-item ${i===current?'active':''}">
            <a class="page-link" href="#" onclick="loadSubjects(${i});return false">${i}</a></li>`;
    if (end < total) html += `<li class="page-item disabled"><span class="page-link">â€¦</span></li>`;
    html += `<li class="page-item ${current===total?'disabled':''}">
        <a class="page-link" href="#" onclick="loadSubjects(${current+1});return false">â€º</a></li>`;

    p.innerHTML = html;
}

function saveSubject() {
    const form = document.getElementById('subjectForm');
    form.classList.add('was-validated');
    if (!form.checkValidity()) return;

    const btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Äang lÆ°uâ€¦';

    const id     = document.getElementById('subject_id').value;
    const action = id ? 'update' : 'store';

    fetch(`${API}&action=${action}`, { method: 'POST', body: new FormData(form) })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-floppy me-1"></i>LÆ°u';
            form.classList.remove('was-validated');
            if (!res.success) { showToast(res.message, 'danger'); return; }
            bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
            showToast(id ? 'Cáº­p nháº­t thÃ nh cÃ´ng!' : 'ThÃªm mÃ´n há»c thÃ nh cÃ´ng!', 'success');
            loadSubjects(currentPage);
        })
        .catch(() => {
            btn.disabled = false;
            showToast('Lá»—i káº¿t ná»‘i', 'danger');
        });
}

function del(id, name) {
    if (!confirm(`XÃ³a mÃ´n há»c "${name}"?\nHÃ nh Ä‘á»™ng nÃ y khÃ´ng thá»ƒ hoÃ n tÃ¡c.`)) return;
    const fd = new FormData();
    fd.append('subject_id', id);
    fetch(`${API}&action=delete`, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (!res.success) { showToast(res.message, 'danger'); return; }
            showToast('ÄÃ£ xÃ³a mÃ´n há»c!', 'success');
            loadSubjects(currentPage);
        });
}

function openAddForm() {
    document.getElementById('subjectForm').reset();
    document.getElementById('subjectForm').classList.remove('was-validated');
    document.getElementById('subject_id').value = '';
    document.getElementById('subject_code').readOnly = false;
    document.getElementById('modalTitle').innerHTML =
        '<i class="bi bi-plus-circle me-2"></i>ThÃªm mÃ´n há»c';
}

function edit(id) {
    fetch(`${API}&action=show&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) { showToast(res.message, 'danger'); return; }
            const s = res.data;
            ['subject_id','subject_code','subject_name','credit_hours',
             'faculty_id','prerequisite_code','description'].forEach(k => {
                const el = document.getElementById(k);
                if (el) el.value = s[k] ?? '';
            });
            document.getElementById('subject_code').readOnly = true;
            document.getElementById('subjectForm').classList.remove('was-validated');
            document.getElementById('modalTitle').innerHTML =
                '<i class="bi bi-pencil me-2"></i>Chá»‰nh sá»­a mÃ´n há»c';
            new bootstrap.Modal(document.getElementById('subjectModal')).show();
        });
}

function viewDetail(id) {
    fetch(`${API}&action=show&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const s = res.data;
            document.getElementById('viewTitle').textContent = s.subject_name;
            document.getElementById('viewBody').innerHTML = `
                <dl class="row mb-0">
                    <dt class="col-4">MÃ£ mÃ´n</dt>
                    <dd class="col-8"><code>${s.subject_code}</code></dd>
                    <dt class="col-4">TÃ­n chá»‰</dt>
                    <dd class="col-8">${s.credit_hours}</dd>
                    <dt class="col-4">TiÃªn quyáº¿t</dt>
                    <dd class="col-8">${s.prerequisite_code ?? 'â€”'}</dd>
                    <dt class="col-4">MÃ´ táº£</dt>
                    <dd class="col-8">${s.description
                        ?? '<span class="text-muted">ChÆ°a cÃ³ mÃ´ táº£</span>'}</dd>
                </dl>`;
            new bootstrap.Modal(document.getElementById('viewModal')).show();
        });
}

async function loadFaculties() {
    const res  = await fetch('/web_QLSV/admin/api/router.php?module=faculties&action=index&page=1&limit=500');
    const json = await res.json();
    if (!json.success) return;
    const opts = json.data.map(f =>
        `<option value="${f.faculty_id}">${f.faculty_name}</option>`).join('');
    document.getElementById('facultyFilter').innerHTML =
        '<option value="">-- Táº¥t cáº£ khoa --</option>' + opts;
    document.getElementById('faculty_id').innerHTML =
        '<option value="">-- Chá»n khoa --</option>' + opts;
}

function loadPrerequisites() {
    fetch(`${API}&action=getAll`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            document.getElementById('prerequisite_code').innerHTML =
                '<option value="">-- KhÃ´ng cÃ³ --</option>' +
                res.data.map(s =>
                    `<option value="${s.subject_code}">${s.subject_name} (${s.subject_code})</option>`
                ).join('');
        });
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('facultyFilter').value = '';
    document.getElementById('sortSelect').value = 'subject_id';
    document.getElementById('orderSelect').value = 'DESC';
    loadSubjects(1);
}

function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    el.className = `toast align-items-center text-white border-0 bg-${type}`;
    document.getElementById('toastMsg').textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 3000 }).show();
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
