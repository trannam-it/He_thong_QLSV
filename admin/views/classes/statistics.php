<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="topbar">
        <h2>Thống kê Lớp Cơ Sở</h2>
        <div>
            <a href="index.php" class="btn btn-outline-secondary">Quay lại danh sách</a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#yearStats">Thống kê theo Khóa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#facultyStats">Thống kê theo Khoa</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Year Statistics Tab -->
        <div id="yearStats" class="tab-pane fade show active">
            <div class="row">
                <div class="col-md-12">
                    <div class="card p-3">
                        <h5 class="card-title">Thống kê Sinh viên theo Khóa</h5>
                        <div id="yearAlertArea"></div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="yearStatsTable">
                                <thead>
                                    <tr>
                                        <th>Khóa</th>
                                        <th>Số Lớp</th>
                                        <th>Số Khoa</th>
                                        <th>Tổng SV</th>
                                        <th>Đang học</th>
                                        <th>Tốt nghiệp</th>
                                        <th>Bảo lưu</th>
                                        <th>Thôi học</th>
                                        <th>Tỷ lệ TN (%)</th>
                                    </tr>
                                </thead>
                                <tbody id="yearStatsBody">
                                    <!-- populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts for Year Statistics -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="card-title">Biểu đồ Tổng SV theo Khóa</h5>
                        <canvas id="yearTotalChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="card-title">Biểu đồ Tỷ lệ Trạng thái theo Khóa</h5>
                        <canvas id="yearStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faculty Statistics Tab -->
        <div id="facultyStats" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">
                    <div class="card p-3">
                        <h5 class="card-title">Thống kê Sinh viên theo Khoa</h5>
                        <div id="facultyAlertArea"></div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="facultyStatsTable">
                                <thead>
                                    <tr>
                                        <th>Khoa</th>
                                        <th>Mã Khoa</th>
                                        <th>Số Lớp</th>
                                        <th>Tổng SV</th>
                                        <th>Đang học</th>
                                        <th>Tốt nghiệp</th>
                                        <th>Bảo lưu</th>
                                        <th>Thôi học</th>
                                    </tr>
                                </thead>
                                <tbody id="facultyStatsBody">
                                    <!-- populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts for Faculty Statistics -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="card-title">Biểu đồ Tổng SV theo Khoa</h5>
                        <canvas id="facultyTotalChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5 class="card-title">Biểu đồ Tỷ lệ Trạng thái theo Khoa</h5>
                        <canvas id="facultyStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const API_URL = '/web_QLSV/admin/api/router.php';

let yearCharts = {}, facultyCharts = {};

// Load Year Statistics
function loadYearStatistics() {
    fetch(`${API_URL}?module=base_classes&action=getYearStatistics`)
        .then(r => {
            if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
            return r.json();
        })
        .then(data => {
            if (!data.data || data.data.length === 0) {
                showAlert('Không có dữ liệu', 'info', 'yearAlertArea');
                return;
            }

            const tbody = document.getElementById('yearStatsBody');
            tbody.innerHTML = '';

            const yearLabels = [];
            const totalStudents = [];
            const studyingStudents = [];
            const graduatedStudents = [];
            const suspendedStudents = [];
            const droppedStudents = [];

            data.data.forEach(stat => {
                const total = parseInt(stat.total_students) || 0;
                const studying = parseInt(stat.studying) || 0;
                const graduated = parseInt(stat.graduated) || 0;
                const suspended = parseInt(stat.suspended) || 0;
                const dropped = parseInt(stat.dropped) || 0;
                const tnRatio = total > 0 ? ((graduated / total) * 100).toFixed(1) : 0;

                tbody.innerHTML += `
                    <tr>
                        <td><strong>K${stat.year}</strong></td>
                        <td>${stat.total_classes}</td>
                        <td>${stat.total_faculties}</td>
                        <td><span class="badge bg-primary">${total}</span></td>
                        <td><span class="badge bg-success">${studying}</span></td>
                        <td><span class="badge bg-info">${graduated}</span></td>
                        <td><span class="badge bg-warning">${suspended}</span></td>
                        <td><span class="badge bg-danger">${dropped}</span></td>
                        <td><strong>${tnRatio}%</strong></td>
                    </tr>
                `;

                yearLabels.push(`K${stat.year}`);
                totalStudents.push(total);
                studyingStudents.push(studying);
                graduatedStudents.push(graduated);
                suspendedStudents.push(suspended);
                droppedStudents.push(dropped);
            });

            // Draw charts
            drawYearChart('yearTotalChart', yearLabels, totalStudents);
            drawYearStatusChart('yearStatusChart', yearLabels, studyingStudents, graduatedStudents, suspendedStudents, droppedStudents);
        })
        .catch(err => {
            console.error('Load error:', err);
            showAlert('Lỗi load dữ liệu: ' + err.message, 'danger', 'yearAlertArea');
        });
}

// Load Faculty Sta{
            if (!r.ok) return r.text().then(text => { throw new Error(`HTTP ${r.status}: ${text}`); });
            return r.json();
        }
function loadFacultyStatistics() {
    fetch(`${API_URL}?module=base_classes&action=getFacultyStatistics`)
        .then(r => r.json())
        .then(data => {
            if (!data.data || data.data.length === 0) {
                showAlert('Không có dữ liệu', 'info', 'facultyAlertArea');
                return;
            }

            const tbody = document.getElementById('facultyStatsBody');
            tbody.innerHTML = '';

            const facultyLabels = [];
            const totalStudents = [];
            const studyingStudents = [];
            const graduatedStudents = [];
            const suspendedStudents = [];
            const droppedStudents = [];

            data.data.forEach(stat => {
                const total = parseInt(stat.total_students) || 0;
                const studying = parseInt(stat.studying) || 0;
                const graduated = parseInt(stat.graduated) || 0;
                const suspended = parseInt(stat.suspended) || 0;
                const dropped = parseInt(stat.dropped) || 0;

                tbody.innerHTML += `
                    <tr>
                        <td><strong>${stat.faculty_name}</strong></td>
                        <td>${stat.faculty_code}</td>
                        <td>${stat.total_classes}</td>
                        <td><span class="badge bg-primary">${total}</span></td>
                        <td><span class="badge bg-success">${studying}</span></td>
                        <td><span class="badge bg-info">${graduated}</span></td>
                        <td><span class="badge bg-warning">${suspended}</span></td>
                        <td><span class="badge bg-danger">${dropped}</span></td>
                    </tr>
                `;

                facultyLabels.push(stat.faculty_code);
                totalStudents.push(total);
                studyingStudents.push(studying);
                graduatedStudents.push(graduated);
                suspendedStudents.push(suspended);
                droppedStudents.push(dropped);
            });

            // Draw charts
            drawFacult{
            console.error('Load error:', err);
            showAlert('Lỗi load dữ liệu: ' + err.message, 'danger', 'facultyAlertArea');
        }
            drawFacultyStatusChart('facultyStatusChart', facultyLabels, studyingStudents, graduatedStudents, suspendedStudents, droppedStudents);
        })
        .catch(err => showAlert('Lỗi load dữ liệu: ' + err, 'danger', 'facultyAlertArea'));
}

// Draw Year Total Chart
function drawYearChart(canvasId, labels, data) {
    if (yearCharts[canvasId]) yearCharts[canvasId].destroy();
    
    const ctx = document.getElementById(canvasId).getContext('2d');
    yearCharts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tổng Sinh viên',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// Draw Year Status Chart
function drawYearStatusChart(canvasId, labels, studying, graduated, suspended, dropped) {
    if (yearCharts[canvasId]) yearCharts[canvasId].destroy();
    
    const ctx = document.getElementById(canvasId).getContext('2d');
    yearCharts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Đang học',
                    data: studying,
                    backgroundColor: 'rgba(75, 192, 75, 0.6)'
                },
                {
                    label: 'Tốt nghiệp',
                    data: graduated,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                },
                {
                    label: 'Bảo lưu',
                    data: suspended,
                    backgroundColor: 'rgba(255, 193, 7, 0.6)'
                },
                {
                    label: 'Thôi học',
                    data: dropped,
                    backgroundColor: 'rgba(244, 67, 54, 0.6)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
}

// Draw Faculty Total Chart
function drawFacultyChart(canvasId, labels, data) {
    if (facultyCharts[canvasId]) facultyCharts[canvasId].destroy();
    
    const ctx = document.getElementById(canvasId).getContext('2d');
    facultyCharts[canvasId] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tổng Sinh viên',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });
}

// Draw Faculty Status Chart
function drawFacultyStatusChart(canvasId, labels, studying, graduated, suspended, dropped) {
    if (facultyCharts[canvasId]) facultyCharts[canvasId].destroy();
    
    const ctx = document.getElementById(canvasId).getContext('2d');
    facultyCharts[canvasId] = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Đang học',
                    data: studying,
                    borderColor: 'rgba(75, 192, 75, 1)',
                    backgroundColor: 'rgba(75, 192, 75, 0.2)'
                },
                {
                    label: 'Tốt nghiệp',
                    data: graduated,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)'
                },
                {
                    label: 'Bảo lưu',
                    data: suspended,
                    borderColor: 'rgba(255, 193, 7, 1)',
                    backgroundColor: 'rgba(255, 193, 7, 0.2)'
                },
                {
                    label: 'Thôi học',
                    data: dropped,
                    borderColor: 'rgba(244, 67, 54, 1)',
                    backgroundColor: 'rgba(244, 67, 54, 0.2)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: {
                r: { beginAtZero: true }
            }
        }
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadYearStatistics();
    loadFacultyStatistics();
});

function showAlert(message, type = 'info', elementId) {
    const alertArea = document.getElementById(elementId);
    if (alertArea) {
        const html = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        alertArea.innerHTML = html;
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
