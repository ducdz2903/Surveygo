<?php
/** @var string $appName */
$appName = $appName ?? 'Admin - Báo cáo';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/public/assets/css/admin.css" rel="stylesheet">
</head>

<body>
    <?php include BASE_PATH . '/app/Views/components/admin/_sidebar.php'; ?>

    <header class="admin-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Báo cáo & Thống kê</h5>
            <div class="header-actions">
                <div class="user-menu" id="admin-user-menu">
                    <div class="user-avatar">AD</div>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Báo cáo & Thống kê</h2>
                <div class="btn-group">
                    <button class="btn btn-outline-primary"><i class="fas fa-file-pdf me-2"></i>Xuất PDF</button>
                    <button class="btn btn-outline-success"><i class="fas fa-file-excel me-2"></i>Xuất Excel</button>
                </div>
            </div>

            <!-- Top Surveys -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card fade-in">
                        <div class="card-header">
                            <h5><i class="fas fa-trophy me-2 text-warning"></i>Top khảo sát</h5>
                        </div>
                        <div class="card-body">
                            <div id="top-surveys-list"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card fade-in" style="animation-delay: 0.1s">
                        <div class="card-header">
                            <h5><i class="fas fa-users me-2 text-primary"></i>Top người dùng</h5>
                        </div>
                        <div class="card-body">
                            <div id="top-users-list"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card fade-in" style="animation-delay: 0.2s">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-bar me-2 text-success"></i>Phân bố theo danh mục</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card fade-in" style="animation-delay: 0.3s">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line me-2 text-info"></i>Xu hướng tăng trưởng</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="growthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="/public/assets/js/admin-mock-data.js"></script>
    <script>
        // Top Surveys
        document.getElementById('top-surveys-list').innerHTML = AdminMockData.reports.topSurveys.map((survey, index) => `
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 ${index < AdminMockData.reports.topSurveys.length - 1 ? 'border-bottom' : ''}">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div class="fw-bold fs-4 text-muted" style="width: 30px;">${index + 1}</div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${survey.title}</div>
                        <div class="text-muted small">
                            ${survey.responses} phản hồi
                            <span class="ms-2">
                                ${Array(Math.floor(survey.rating)).fill('<i class="fas fa-star text-warning"></i>').join('')}
                                <span class="ms-1">${survey.rating}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        // Top Users
        document.getElementById('top-users-list').innerHTML = AdminMockData.reports.topUsers.map((user, index) => `
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 ${index < AdminMockData.reports.topUsers.length - 1 ? 'border-bottom' : ''}">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div class="fw-bold fs-4 text-muted" style="width: 30px;">${index + 1}</div>
                    <div class="user-avatar" style="width: 40px; height: 40px; background: ${AdminHelpers.getAvatarColor(user.name)}">
                        ${user.name.split(' ').map(n => n[0]).join('')}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${user.name}</div>
                        <div class="text-muted small">${user.surveys} khảo sát • ${user.responses} phản hồi</div>
                    </div>
                </div>
            </div>
        `).join('');

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(AdminMockData.reports.categoryDistribution),
                datasets: [{
                    label: 'Số lượng',
                    data: Object.values(AdminMockData.reports.categoryDistribution),
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.8)',
                        'rgba(46, 204, 113, 0.8)',
                        'rgba(155, 89, 182, 0.8)',
                        'rgba(241, 196, 15, 0.8)',
                        'rgba(231, 76, 60, 0.8)',
                        'rgba(26, 188, 156, 0.8)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Growth Chart
        const growthCtx = document.getElementById('growthChart').getContext('2d');
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Người dùng',
                    data: AdminMockData.trends.userGrowth,
                    borderColor: 'rgba(52, 152, 219, 1)',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Phản hồi',
                    data: AdminMockData.trends.responsesByMonth,
                    borderColor: 'rgba(46, 204, 113, 1)',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        document.getElementById('admin-user-menu')?.addEventListener('click', () => {
            if (confirm('Đăng xuất?')) window.location.href = '/login';
        });
    </script>
</body>

</html>