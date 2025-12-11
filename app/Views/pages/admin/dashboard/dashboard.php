<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Dashboard</h4>
            <p class="text-muted mb-0">Cập nhật lúc: <span id="current-time"></span></p>
        </div>
        <button class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Xuất báo cáo
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary fade-in">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Tổng Users</h6>
                        <h3 class="mb-0" id="stat-users">0</h3>
                        <div class="trend up mt-2">
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
                        <h6 class="text-muted mb-2">Khảo sát</h6>
                        <h3 class="mb-0" id="stat-surveys">0</h3>
                        <div class="trend up mt-2">
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
                        <h6 class="text-muted mb-2">Phản hồi</h6>
                        <h3 class="mb-0" id="stat-responses">0</h3>
                        <div class="trend up mt-2">
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
                        <h6 class="text-muted mb-2">Sự kiện</h6>
                        <h3 class="mb-0" id="stat-events">0</h3>
                        <div class="trend down mt-2">
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

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card h-100 fade-in" style="animation-delay: 0.4s">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Thống kê khảo sát</h5>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option>2024</option>
                            <option>2023</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="surveyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 fade-in" style="animation-delay: 0.5s">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-pie-chart me-2 text-success"></i>Loại khảo sát</h5>
                </div>
                <div class="card-body d-flex align-items-center">
                    <div class="chart-container w-100" style="height: 250px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card fade-in" style="animation-delay: 0.6s">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-history me-2 text-info"></i>Hoạt động gần đây</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="activity-list list-group list-group-flush" id="activity-list">
                        <li class="list-group-item text-center text-muted py-4">Đang tải...</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card fade-in" style="animation-delay: 0.7s">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-fire me-2 text-danger"></i>Top khảo sát</h5>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="/public/assets/js/admin-mock-data.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Update Time ---
        function updateTime() {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            if(timeElement) {
                timeElement.textContent = now.toLocaleString('vi-VN');
            }
        }
        updateTime();
        setInterval(updateTime, 60000);

        // --- 2. Mock Data Check ---
        // Đảm bảo biến AdminMockData tồn tại (từ file admin-mock-data.js)
        if (typeof AdminMockData === 'undefined') {
            console.warn('AdminMockData chưa được load. Sử dụng dữ liệu mẫu tạm thời.');
            window.AdminMockData = {
                stats: { totalUsers: 150, totalSurveys: 45, totalResponses: 1200, totalEvents: 5 },
                trends: {
                    surveysByMonth: [12, 19, 3, 5, 2, 3, 10, 15, 20, 25, 22, 30],
                    surveyTypes: { regular: 70, quickPoll: 30 }
                },
                activities: [],
                reports: { topSurveys: [] }
            };
        }

        // --- 3. Animate Numbers ---
        function animateNumber(elementId, target, duration = 1000) {
            const element = document.getElementById(elementId);
            if (!element) return;

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

        animateNumber('stat-users', AdminMockData.stats.totalUsers);
        animateNumber('stat-surveys', AdminMockData.stats.totalSurveys);
        animateNumber('stat-responses', AdminMockData.stats.totalResponses);
        animateNumber('stat-events', AdminMockData.stats.totalEvents);

        // --- 4. Survey Chart ---
        const surveyCanvas = document.getElementById('surveyChart');
        if (surveyCanvas) {
            new Chart(surveyCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    datasets: [{
                        label: 'Khảo sát',
                        data: AdminMockData.trends.surveysByMonth,
                        backgroundColor: 'rgba(52, 152, 219, 0.8)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // --- 5. Type Chart ---
        const typeCanvas = document.getElementById('typeChart');
        if (typeCanvas) {
            new Chart(typeCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Khảo sát thường', 'Quick Poll'],
                    datasets: [{
                        data: [AdminMockData.trends.surveyTypes.regular, AdminMockData.trends.surveyTypes.quickPoll],
                        backgroundColor: ['#3498db', '#2ecc71'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        // --- 6. Render Activity List ---
        async function loadActivityLogs() {
            const activityList = document.getElementById('activity-list');
            if (!activityList) return;
            
            // Hàm dịch action thành tiếng Việt
            const translateAction = (action) => {
                const translations = {
                    'survey_submitted': 'Hoàn thành khảo sát',
                    'survey_created': 'Tạo khảo sát',
                    'event_created': 'Tạo sự kiện',
                    'question_created': 'Tạo câu hỏi',
                    'participated_event': 'Tham gia sự kiện',
                    'reward_redeemed': 'Đổi thưởng',
                    'profile_updated': 'Cập nhật hồ sơ',
                    'login': 'Đăng nhập',
                    'logout': 'Đăng xuất',
                    'contact_message': 'Gửi liên hệ',
                    'feedback_submitted': 'Gửi phản hồi',
                    'daily_reward_claimed': 'Nhận thưởng hàng ngày',
                    'user_created': 'Tạo người dùng',
                    'user_updated': 'Cập nhật người dùng',
                    'user_deleted': 'Xóa người dùng',
                    'reward_created': 'Tạo phần thưởng',
                    'reward_updated': 'Cập nhật phần thưởng',
                    'reward_deleted': 'Xóa phần thưởng',
                };
                return translations[action] || action.replace(/_/g, ' ');
            };
            
            try {
                const response = await fetch('/api/admin/activity-logs?limit=5');
                const result = await response.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    const actionIcons = {
                        'survey_submitted': { icon: 'fas fa-check-circle', color: 'success' },
                        'survey_created': { icon: 'fas fa-plus-circle', color: 'primary' },
                        'event_created': { icon: 'fas fa-calendar-plus', color: 'primary' },
                        'question_created': { icon: 'fas fa-lightbulb', color: 'warning' },
                        'participated_event': { icon: 'fas fa-calendar-check', color: 'success' },
                        'reward_redeemed': { icon: 'fas fa-gift', color: 'danger' },
                        'profile_updated': { icon: 'fas fa-user-edit', color: 'info' }
                    };
                    
                    activityList.innerHTML = result.data.map(activity => {
                        const iconConfig = actionIcons[activity.action] || { icon: 'fas fa-history', color: 'secondary' };
                        const createdDate = new Date(activity.created_at);
                        const timeText = createdDate.toLocaleString('vi-VN');
                        const translatedAction = translateAction(activity.action);
                        
                        return `
                            <li class="list-group-item border-0 py-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="stat-icon bg-${iconConfig.color} bg-opacity-10 text-${iconConfig.color} rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="${iconConfig.icon}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small"><strong>${activity.user_name || 'Unknown'}</strong> ${translatedAction}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">${timeText}</div>
                                    </div>
                                </div>
                            </li>
                        `;
                    }).join('');
                } else {
                    activityList.innerHTML = '<li class="list-group-item text-center text-muted py-4">Không có hoạt động nào</li>';
                }
            } catch (error) {
                console.error('Lỗi khi tải activity logs:', error);
                activityList.innerHTML = '<li class="list-group-item text-center text-danger py-4">Lỗi khi tải hoạt động</li>';
            }
        }
        
        loadActivityLogs();
        
        const activityList = document.getElementById('activity-list');
        if (activityList && AdminMockData.activities.length > 0) {
             activityList.innerHTML = '<li class="list-group-item text-center text-muted">Không có hoạt động nào</li>';
        }

        // --- 7. Render Top Surveys ---
        const topSurveys = document.getElementById('top-surveys');
        if (topSurveys && AdminMockData.reports.topSurveys.length > 0) {
            topSurveys.innerHTML = AdminMockData.reports.topSurveys.slice(0, 5).map((survey, index) => `
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom last-no-border">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-truncate" style="max-width: 200px;">${index + 1}. ${survey.title}</div>
                        <div class="small text-muted">
                            ${survey.responses} phản hồi
                            <span class="ms-2 text-warning">
                                ${'<i class="fas fa-star" style="font-size:0.7rem"></i>'.repeat(Math.floor(survey.rating))}
                                <span class="text-dark ms-1">${survey.rating}</span>
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    });
</script>