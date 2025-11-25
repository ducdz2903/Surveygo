<?php
/** @var string $appName */
/** @var string $baseUrl */
/** @var array $urls */

$appName = $appName ?? 'Admin Dashboard';
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
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Dashboard</h5>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm...">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </div>
                <div class="user-menu" id="admin-user-menu">
                    <div class="user-avatar">AD</div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">Admin</div>
                        <div style="font-size: 0.75rem; color: #999;">Quản trị viên</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Tổng quan hệ thống</h2>
                    <p class="text-muted mb-0">Cập nhật lúc: <span id="current-time"></span></p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Xuất báo cáo
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card primary fade-in">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6>Tổng Users</h6>
                                <h3 id="stat-users">0</h3>
                                <div class="trend up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+12.5%</span>
                                </div>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card success fade-in" style="animation-delay: 0.1s">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6>Khảo sát</h6>
                                <h3 id="stat-surveys">0</h3>
                                <div class="trend up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+8.2%</span>
                                </div>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-poll"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card warning fade-in" style="animation-delay: 0.2s">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6>Phản hồi</h6>
                                <h3 id="stat-responses">0</h3>
                                <div class="trend up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>+15.7%</span>
                                </div>
                            </div>
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card danger fade-in" style="animation-delay: 0.3s">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6>Sự kiện</h6>
                                <h3 id="stat-events">0</h3>
                                <div class="trend down">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>-2.3%</span>
                                </div>
                            </div>
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <div class="card fade-in" style="animation-delay: 0.4s">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-chart-bar me-2"></i>Thống kê khảo sát theo tháng</h5>
                                <select class="form-select form-select-sm" style="width: auto;">
                                    <option>2024</option>
                                    <option>2023</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="surveyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card fade-in" style="animation-delay: 0.5s">
                        <div class="card-header">
                            <h5><i class="fas fa-pie-chart me-2"></i>Loại khảo sát</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="typeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity and Quick Stats -->
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card fade-in" style="animation-delay: 0.6s">
                        <div class="card-header">
                            <h5><i class="fas fa-history me-2"></i>Hoạt động gần đây</h5>
                        </div>
                        <div class="card-body">
                            <ul class="activity-list" id="activity-list">
                                <li class="text-center text-muted py-4">Đang tải...</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card fade-in" style="animation-delay: 0.7s">
                        <div class="card-header">
                            <h5><i class="fas fa-fire me-2"></i>Top khảo sát</h5>
                        </div>
                        <div class="card-body">
                            <div id="top-surveys">
                                <p class="text-center text-muted py-4">Đang tải...</p>
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
        // Update current time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleString('vi-VN');
        }
        updateTime();
        setInterval(updateTime, 60000);

        // Animate numbers
        function animateNumber(element, target, duration = 1000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = start;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target.toLocaleString('vi-VN');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString('vi-VN');
                }
            }, 16);
        }

        // Load statistics
        animateNumber(document.getElementById('stat-users'), AdminMockData.stats.totalUsers);
        animateNumber(document.getElementById('stat-surveys'), AdminMockData.stats.totalSurveys);
        animateNumber(document.getElementById('stat-responses'), AdminMockData.stats.totalResponses);
        animateNumber(document.getElementById('stat-events'), AdminMockData.stats.totalEvents);

        // Survey Chart
        const surveyCtx = document.getElementById('surveyChart').getContext('2d');
        new Chart(surveyCtx, {
            type: 'bar',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Khảo sát',
                    data: AdminMockData.trends.surveysByMonth,
                    backgroundColor: 'rgba(52, 152, 219, 0.8)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Type Chart
        const typeCtx = document.getElementById('typeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Khảo sát thường', 'Quick Poll'],
                datasets: [{
                    data: [AdminMockData.trends.surveyTypes.regular, AdminMockData.trends.surveyTypes.quickPoll],
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.8)',
                        'rgba(46, 204, 113, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Load activities
        const activityList = document.getElementById('activity-list');
        activityList.innerHTML = AdminMockData.activities.map(activity => `
            <li class="activity-item slide-in">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-${activity.color} bg-opacity-10 text-${activity.color}" style="width: 40px; height: 40px; font-size: 1rem;">
                        <i class="fas ${activity.icon}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div><strong>${activity.user}</strong> ${activity.action} <strong>${activity.target}</strong></div>
                        <div class="activity-time">${activity.time}</div>
                    </div>
                </div>
            </li>
        `).join('');

        // Load top surveys
        const topSurveys = document.getElementById('top-surveys');
        topSurveys.innerHTML = AdminMockData.reports.topSurveys.slice(0, 5).map((survey, index) => `
            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size: 0.9rem;">${index + 1}. ${survey.title}</div>
                    <div class="text-muted" style="font-size: 0.8rem;">
                        ${survey.responses} phản hồi
                        <span class="ms-2">
                            ${Array(Math.floor(survey.rating)).fill('<i class="fas fa-star text-warning"></i>').join('')}
                        </span>
                    </div>
                </div>
            </div>
        `).join('');

        // User menu logout
        document.getElementById('admin-user-menu')?.addEventListener('click', function () {
            if (confirm('Bạn có chắc muốn đăng xuất?')) {
                localStorage.removeItem('app.user');
                window.location.href = '/login';
            }
        });
    </script>
</body>

</html>