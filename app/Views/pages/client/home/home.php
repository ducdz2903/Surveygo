<section class="welcome-section pt-5 pb-4">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 welcome-container">
                <h1 class="welcome-title" id="welcome-text">Xin chào! 👋</h1>
                <p class="welcome-text text-muted">Hãy hoàn thành các khảo sát mới để tiếp tục
                    tăng thu nhập của bạn.</p>
            </div>
            <div class="col-lg-6 text-lg-end">
                <div class="points-card d-inline-flex align-items-center justify-content-between w-100 p-3 p-md-4">
                    <div class="points-info text-start">
                        <div class="points-label">Điểm hiện có</div>
                        <div class="points-value" id="user-points">1,250</div>
                    </div>
                    <a href="<?= rtrim($baseUrl, '/') ?>/rewards" class="btn btn-outline-accent flex-shrink-0"> <i class="fas fa-gift me-2"></i>Đổi
                        thưởng ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="quick-actions-section pb-5">
    <div class="container">
        <h2 class="section-title mb-4">Hành động nhanh</h2>
        <div class="row g-3 g-lg-4">
            <div class="col-md-4">
                <a href="<?= rtrim($baseUrl, '/') ?>/profile" class="action-card">
                    <div class="action-icon"
                        style="--icon-bg: var(--primary-color-soft); --icon-color: var(--primary-color);">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="action-content">
                        <h5>Hoàn thành hồ sơ</h5>
                        <p>Nhận +50 điểm và các khảo sát tốt hơn.</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= rtrim($baseUrl, '/') ?>/daily-rewards" class="action-card">
                    <div class="action-icon"
                        style="--icon-bg: var(--success-color-soft); --icon-color: var(--success-color);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="action-content">
                        <h5>Điểm danh hàng ngày</h5>
                        <p>Nhận phần thưởng đăng nhập mỗi ngày.</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= rtrim($baseUrl, '/') ?>/events" class="action-card">
                    <div class="action-icon"
                        style="--icon-bg: var(--accent-color-soft); --icon-color: var(--accent-color);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="action-content">
                        <h5>Mời bạn bè</h5>
                        <p>Nhận hoa hồng từ bạn bè của bạn.</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="charts-section bg-body-light py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="chart-card">
                    <h3 class="chart-title">Điểm kiếm được (6 tháng qua)</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="pointsEarnedChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="chart-card">
                    <h3 class="chart-title">Khảo sát hoàn thành (7 ngày qua)</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="surveysCompletedChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="surveys" class="surveys-section py-5">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Khảo sát mới dành cho bạn <span id="survey-count">(0)</span></h2>
        </div>

        <div class="row g-4" id="surveys-container"></div>
    </div>
</section>

<section class="activity-section bg-body-light py-5">
    <div class="container">
        <h2 class="section-title mb-3">Hoạt động gần đây</h2>
        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-icon activity-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="activity-content">
                    <h4>Hoàn thành khảo sát "Trải nghiệm du lịch"</h4>
                    <p>Bạn đã nhận được +45 điểm</p>
                </div>
                <div class="activity-time">2 giờ trước</div>
            </div>

            <div class="activity-item">
                <div class="activity-icon activity-icon-warning">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="activity-content">
                    <h4>Đổi thưởng thành công</h4>
                    <p>Voucher Shopee 50.000đ</p>
                </div>
                <div class="activity-time">1 ngày trước</div>
            </div>

            <div class="activity-item">
                <div class="activity-icon activity-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="activity-content">
                    <h4>Hoàn thành khảo sát "Thói quen ăn uống"</h4>
                    <p>Bạn đã nhận được +35 điểm</p>
                </div>
                <div class="activity-time">2 ngày trước</div>
            </div>
        </div>
    </div>
</section>

<script>
    let currentPage = 1;
    const pageSize = 6;

    // Fetch surveys từ API với phân trang & lọc
    async function loadSurveys(page = 1, filters = {}) {
        try {
            const queryParams = new URLSearchParams({
                page: page,
                limit: pageSize,
                ...filters,
            });

            const response = await fetch(`/api/surveys?${queryParams}`);
            const result = await response.json();

            if (!result.error && result.data && result.meta) {
                currentPage = result.meta.page;
                renderSurveys(result.data, result.meta);
            } else {
                document.getElementById('surveys-container').innerHTML =
                    '<div class="col-12 text-center"><p class="text-muted">Không có khảo sát nào.</p></div>';
            }
        } catch (error) {
            console.error('Lỗi khi tải khảo sát:', error);
            document.getElementById('surveys-container').innerHTML =
                '<div class="col-12 text-center"><p class="text-danger">Lỗi khi tải khảo sát.</p></div>';
        }
    }

    // Render survey cards + pagination
    function renderSurveys(surveys, meta) {
        const container = document.getElementById('surveys-container');
        const countEl = document.getElementById('survey-count');

        // Update count
        countEl.textContent = `(${meta.total})`;

        if (surveys.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Không có khảo sát nào.</p></div>';
            return;
        }

        const badgeMap = {
            'hoạtĐộng': { class: 'badge-hot', icon: 'fas fa-fire', text: 'Hot' },
            'chờDuyệt': { class: '', icon: 'fas fa-star', text: 'Mới' },
        };

        let html = surveys.map((survey) => {
            const badge = badgeMap[survey.trangThai] || { class: '', icon: 'fas fa-star', text: 'Mới' };

            return `
                    <div class="col-lg-4 col-md-6">
                        <div class="survey-card">
                            <div class="survey-badge ${badge.class}">
                                <i class="${badge.icon} me-1"></i>${badge.text}
                            </div>
                            <div class="survey-header">
                                <h3 class="survey-title">${survey.tieuDe}</h3>
                                <div class="survey-meta">
                                    <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+${survey.diemThuong || 50} điểm</span>
                                    <span><i class="fas fa-clock me-1"></i>~${survey.thoiLuongDuTinh || 10} phút</span>
                                </div>
                            </div>
                            <p class="survey-desc">${survey.moTa || 'Tham gia khảo sát này để kiếm điểm.'}</p>
                            <a href="/surveys/guide?id=${survey.id}" class="btn btn-gradient mt-auto w-100">Bắt đầu ngay</a>
                        </div>
                    </div>
                `;
        }).join('');

        // Add pagination controls nếu có nhiều trang
        if (meta.totalPages > 1) {
            html += `
                    <div class="col-12 d-flex justify-content-center gap-2 mt-4">
                        ${currentPage > 1 ? `<button class="btn btn-sm btn-outline-primary" onclick="loadSurveys(${currentPage - 1})">← Trước</button>` : ''}
                        <span class="btn btn-sm btn-light disabled">Trang ${meta.page}/${meta.totalPages}</span>
                        ${currentPage < meta.totalPages ? `<button class="btn btn-sm btn-outline-primary" onclick="loadSurveys(${currentPage + 1})">Tiếp →</button>` : ''}
                    </div>
                `;
        }

        container.innerHTML = html;
    }

    // Load surveys khi trang tải
    document.addEventListener('DOMContentLoaded', () => loadSurveys(1));

    // Hàm lọc surveys (có thể gọi từ bất kỳ filter button)
    function filterSurveys(filters) {
        loadSurveys(1, filters); // Reset về trang 1 khi lọc
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        try {
            const raw = localStorage.getItem('app.user');
            if (!raw) return;
            const user = JSON.parse(raw);
            const name = (user && (user.name || user.email)) || '';
            if (!name) return;

            const welcomeText = document.getElementById('welcome-text');
            if (welcomeText) {
                welcomeText.textContent = `Xin chào, ${name}! 👋`;
            }

            if (user.points) {
                const userPointsEl = document.getElementById('user-points');
                if (userPointsEl) userPointsEl.textContent = user.points.toLocaleString('vi-VN');
            }
        } catch (_) {
            // ignore
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        try {
            // --- Biểu đồ 1: Điểm kiếm được (Biểu đồ cột) ---
            const ctxPoints = document.getElementById('pointsEarnedChart');
            if (ctxPoints) {
                new Chart(ctxPoints, {
                    type: 'bar',
                    data: {
                        labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
                        datasets: [{
                            label: 'Điểm kiếm được',
                            data: [120, 190, 300, 500, 220, 350],
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } },
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // --- Biểu đồ 2: Khảo sát hoàn thành (Biểu đồ đường) ---
            const ctxSurveys = document.getElementById('surveysCompletedChart');
            if (ctxSurveys) {
                new Chart(ctxSurveys, {
                    type: 'line',
                    data: {
                        labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                        datasets: [{
                            label: 'Khảo sát',
                            data: [3, 5, 2, 4, 6, 1, 3],
                            fill: true,
                            backgroundColor: 'rgba(236, 72, 153, 0.1)',
                            borderColor: 'rgba(236, 72, 153, 1)',
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } },
                        plugins: { legend: { display: false } }
                    }
                });
            }
        } catch (e) {
            console.error("Lỗi khi khởi tạo biểu đồ:", e);
        }
    });
</script>