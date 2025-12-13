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
                        <div class="trend up mt-2" id="user-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span id="user-growth-percent">+0%</span>
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
                        <div class="trend up mt-2" id="survey-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span id="survey-growth-percent">+0%</span>
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
                        <div class="trend up mt-2" id="response-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span id="response-growth-percent">+0%</span>
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
                        <div class="trend down mt-2" id="event-trend">
                            <i class="fas fa-arrow-down"></i>
                            <span id="event-growth-percent">-0%</span>
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
        // --- 1. Cập nhật thời gian ---
        function updateTime() {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            if(timeElement) {
                timeElement.textContent = now.toLocaleString('vi-VN');
            }
        }
        updateTime();
        setInterval(updateTime, 60000);

        // --- 2. Kiểm tra dữ liệu mẫu ---
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

        // --- 3. Hiệu ứng chuyển động số ---
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

        // --- 3. Tải và hiển thị thống kê người dùng ---
        async function loadUserStats() {
            try {
                const response = await fetch('/api/admin/user-stats');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const stats = result.data;
                    
                    // Hiệu ứng hiển thị tổng số người dùng
                    animateNumber('stat-users', stats.total_users);
                    
                    // Cập nhật hiển thị xu hướng
                    const trendDiv = document.getElementById('user-trend');
                    const growthSpan = document.getElementById('user-growth-percent');
                    
                    if (trendDiv && growthSpan) {
                        // Cập nhật hướng xu hướng
                        if (stats.is_growth_positive) {
                            trendDiv.className = 'trend up mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-up';
                            growthSpan.textContent = `+${stats.growth_percentage}%`;
                        } else {
                            trendDiv.className = 'trend down mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-down';
                            growthSpan.textContent = `${stats.growth_percentage}%`;
                        }
                    }
                }
            } catch (error) {
                console.error('Lỗi khi tải user stats:', error);
                // Giữ giá trị mặc định khi có lỗi
            }
        }
        
        loadUserStats();
        
        // --- 3.2. Tải và hiển thị thống kê khảo sát ---
        async function loadSurveyStats() {
            try {
                const response = await fetch('/api/admin/survey-stats');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const stats = result.data;
                    
                    // Hiệu ứng hiển thị tổng số khảo sát
                    animateNumber('stat-surveys', stats.total_surveys);
                    
                    // Cập nhật hiển thị xu hướng
                    const trendDiv = document.getElementById('survey-trend');
                    const growthSpan = document.getElementById('survey-growth-percent');
                    
                    if (trendDiv && growthSpan) {
                        // Cập nhật hướng xu hướng và phần trăm
                        if (stats.is_growth_positive) {
                            trendDiv.className = 'trend up mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-up';
                            growthSpan.textContent = `+${stats.growth_percentage}%`;
                        } else {
                            trendDiv.className = 'trend down mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-down';
                            growthSpan.textContent = `${stats.growth_percentage}%`;
                        }
                    }
                }
            } catch (error) {
                console.error('Lỗi khi tải survey stats:', error);
                // Giữ giá trị mặc định khi có lỗi, nhưng vẫn hiển thị hiệu ứng
                animateNumber('stat-surveys', AdminMockData.stats.totalSurveys);
            }
        }
        
        loadSurveyStats();
        
        // --- 3.3. Tải và hiển thị thống kê phản hồi ---
        async function loadResponseStats() {
            try {
                const response = await fetch('/api/admin/response-stats');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const stats = result.data;
                    
                    // Hiệu ứng hiển thị tổng số phản hồi
                    animateNumber('stat-responses', stats.total_responses);
                    
                    // Cập nhật hiển thị xu hướng
                    const trendDiv = document.getElementById('response-trend');
                    const growthSpan = document.getElementById('response-growth-percent');
                    
                    if (trendDiv && growthSpan) {
                        // Cập nhật hướng xu hướng
                        if (stats.is_growth_positive) {
                            trendDiv.className = 'trend up mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-up';
                            growthSpan.textContent = `+${stats.growth_percentage}%`;
                        } else {
                            trendDiv.className = 'trend down mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-down';
                            growthSpan.textContent = `${stats.growth_percentage}%`;
                        }
                    }
                }
            } catch (error) {
                console.error('Lỗi khi tải response stats:', error);
                // Giữ giá trị mặc định khi có lỗi, nhưng vẫn hiển thị hiệu ứng
                animateNumber('stat-responses', AdminMockData.stats.totalResponses);
            }
        }
        
        loadResponseStats();
        
        // --- 3.4. Tải và hiển thị thống kê sự kiện ---
        async function loadEventStats() {
            try {
                const response = await fetch('/api/admin/event-stats');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const stats = result.data;
                    
                    // Hiệu ứng hiển thị tổng số sự kiện
                    animateNumber('stat-events', stats.total_events);
                    
                    // Cập nhật hiển thị xu hướng
                    const trendDiv = document.getElementById('event-trend');
                    const growthSpan = document.getElementById('event-growth-percent');
                    
                    if (trendDiv && growthSpan) {
                        // Cập nhật hướng xu hướng
                        if (stats.is_growth_positive) {
                            trendDiv.className = 'trend up mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-up';
                            growthSpan.textContent = `+${stats.growth_percentage}%`;
                        } else {
                            trendDiv.className = 'trend down mt-2';
                            trendDiv.querySelector('i').className = 'fas fa-arrow-down';
                            growthSpan.textContent = `${stats.growth_percentage}%`;
                        }
                    }
                }
            } catch (error) {
                console.error('Lỗi khi tải event stats:', error);
                // Giữ giá trị mặc định khi có lỗi, nhưng vẫn hiển thị hiệu ứng
                animateNumber('stat-events', AdminMockData.stats.totalEvents);
            }
        }
        
        loadEventStats();

        // --- 4. Biểu đồ khảo sát ---
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

        // --- 5. Biểu đồ loại khảo sát ---
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

        // --- 6. Hiển thị danh sách hoạt động ---
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

        // --- 7. Hiển thị khảo sát hàng đầu ---
        async function loadTopSurveys() {
            const topSurveysContainer = document.getElementById('top-surveys');
            if (!topSurveysContainer) return;
            
            try {
                const response = await fetch('/api/admin/top-surveys?limit=5');
                const result = await response.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    topSurveysContainer.innerHTML = result.data.map((survey, index) => {
                        // Tạo hiển thị đánh giá trung bình nếu có
                        let ratingHTML = '';
                        if (survey.avg_rating && survey.avg_rating > 0) {
                            const fullStars = Math.floor(survey.avg_rating);
                            const hasHalfStar = (survey.avg_rating % 1) >= 0.5;
                            
                            let starsHTML = '';
                            // Ngôi sao đầy đủ
                            for (let i = 0; i < fullStars; i++) {
                                starsHTML += '<i class="fas fa-star" style="font-size:0.7rem"></i>';
                            }
                            // Nửa ngôi sao
                            if (hasHalfStar && fullStars < 5) {
                                starsHTML += '<i class="fas fa-star-half-alt" style="font-size:0.7rem"></i>';
                            }
                            // Ngôi sao rỗng
                            const totalStars = hasHalfStar ? fullStars + 1 : fullStars;
                            for (let i = totalStars; i < 5; i++) {
                                starsHTML += '<i class="far fa-star" style="font-size:0.7rem"></i>';
                            }
                            
                            ratingHTML = `
                                <span class="ms-2 text-warning">
                                    ${starsHTML}
                                    <span class="text-dark ms-1" style="font-size:0.75rem">${survey.avg_rating.toFixed(1)}</span>
                                </span>
                            `;
                        }
                        
                        return `
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom last-no-border">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-truncate" style="max-width: 200px;">${index + 1}. ${survey.tieuDe}</div>
                                    <div class="small text-muted">
                                        ${survey.response_count} phản hồi${ratingHTML}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    topSurveysContainer.innerHTML = '<p class="text-center text-muted py-4">Chưa có dữ liệu khảo sát</p>';
                }
            } catch (error) {
                console.error('Lỗi khi tải top surveys:', error);
                topSurveysContainer.innerHTML = '<p class="text-center text-danger py-4">Lỗi khi tải dữ liệu</p>';
            }
        }
        
        loadTopSurveys();
    });
</script>