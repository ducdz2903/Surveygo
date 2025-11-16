<?php
/** @var string $appName */
/** @var array $urls */

$appName = $appName ?? 'PHP Application';
$urls = $urls ?? [];

// Hàm trợ giúp cho URL được giả định:
$url = static fn($urls_array, $key, $default) => $urls_array[$key] ?? $default;
?>
<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName . ' - Home', ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


    <link rel="stylesheet" href="public/assets/css/home.css">

    <link rel="stylesheet" href="public/assets/css/app.css">

    <link rel="stylesheet" href="public/assets/css/footer.css">

    <link rel="stylesheet" href="public/assets/css/navbar.css">

    <style>
        <?php @include __DIR__ . '/home.css'; ?>
    </style>
</head>

<body class="page page--home">
    <?php include BASE_PATH . '/app/Views/partials/_navbar.php'; ?>

    <section class="welcome-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="welcome-title" id="welcome-text">Xin chào! 👋</h1>
                    <p class="welcome-text text-muted">Hãy hoàn thành các khảo sát mới để tiếp tục
                        tăng thu nhập của bạn.</p>
                </div>
                <div class="col-lg-6 text-lg-end mt-4 mt-lg-0">
                    <div class="points-card d-inline-flex align-items-center justify-content-between w-100 p-3 p-md-4">
                        <div class="points-info text-start">
                            <div class="points-label">Điểm hiện có</div>
                            <div class="points-value" id="user-points">1,250</div>
                        </div>
                        <a href="<?= $url($urls, 'rewards', '/rewards') ?>"
                            class="btn btn-outline-accent flex-shrink-0">
                            <i class="fas fa-gift me-2"></i>Đổi thưởng ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="charts-section">
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

    <section id="surveys" class="surveys-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Khảo sát mới dành cho bạn (6)</h2>
                <a href="<?= $url($urls, 'surveys', '/surveys') ?>" class="view-all">Xem tất cả <i
                        class="fas fa-arrow-right ms-1"></i></a>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="survey-card">
                        <div class="survey-badge">
                            <i class="fas fa-star me-1"></i>Mới
                        </div>
                        <div class="survey-header">
                            <h3 class="survey-title">Khảo sát về thói quen mua sắm online
                            </h3>
                            <div class="survey-meta">
                                <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+50 điểm</span>
                                <span><i class="fas fa-clock me-1"></i>10 phút</span>
                            </div>
                        </div>
                        <p class="survey-desc">Chia sẻ ý kiến của bạn về trải nghiệm mua sắm
                            trực tuyến và xu hướng tiêu dùng.</p>
                        <a href="#" class="btn btn-gradient mt-auto w-100">Bắt đầu ngay</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="survey-card">
                        <div class="survey-badge badge-hot">
                            <i class="fas fa-fire me-1"></i>Hot
                        </div>
                        <div class="survey-header">
                            <h3 class="survey-title">Đánh giá sản phẩm công nghệ</h3>
                            <div class="survey-meta">
                                <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+40 điểm</span>
                                <span><i class="fas fa-clock me-1"></i>8 phút</span>
                            </div>
                        </div>
                        <p class="survey-desc">Cho chúng tôi biết suy nghĩ của bạn về các sản
                            phẩm điện tử và công nghệ mới.</p>
                        <a href="#" class="btn btn-gradient mt-auto w-100">Bắt đầu ngay</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="survey-card">
                        <div class="survey-badge">
                            <i class="fas fa-star me-1"></i>Mới
                        </div>
                        <div class="survey-header">
                            <h3 class="survey-title">Khảo sát về sức khỏe & thể thao</h3>
                            <div class="survey-meta">
                                <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+60 điểm</span>
                                <span><i class="fas fa-clock me-1"></i>12 phút</span>
                            </div>
                        </div>
                        <p class="survey-desc">Chia sẻ thói quen tập luyện và quan điểm về lối
                            sống lành mạnh của bạn.</p>
                        <a href="#" class="btn btn-gradient mt-auto w-100">Bắt đầu ngay</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="survey-card">
                        <div class="survey-badge">
                            <i class="fas fa-star me-1"></i>Mới
                        </div>
                        <div class="survey-header">
                            <h3 class="survey-title">Khảo sát về thói quen ăn uống</h3>
                            <div class="survey-meta">
                                <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+55 điểm</span>
                                <span><i class="fas fa-clock me-1"></i>10 phút</span>
                            </div>
                        </div>
                        <p class="survey-desc">Chia sẻ sở thích ăn uống của bạn và các xu hướng
                            tiêu dùng thực phẩm.</p>
                        <a href="#" class="btn btn-gradient mt-auto w-100">Bắt đầu ngay</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="survey-card">
                        <div class="survey-badge badge-hot">
                            <i class="fas fa-fire me-1"></i>Hot
                        </div>
                        <div class="survey-header">
                            <h3 class="survey-title">Khảo sát về du lịch và du lịch</h3>
                            <div class="survey-meta">
                                <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+70 điểm</span>
                                <span><i class="fas fa-clock me-1"></i>15 phút</span>
                            </div>
                        </div>
                        <p class="survey-desc">Cho chúng tôi biết về những trải nghiệm du lịch
                            yêu thích của bạn và các điểm đến mơ ước.</p>
                        <a href="#" class="btn btn-gradient mt-auto w-100">Bắt đầu ngay</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="survey-card">
                        <div class="survey-badge">
                            <i class="fas fa-star me-1"></i>Mới
                        </div>
                        <div class="survey-header">
                            <h3 class="survey-title">Khảo sát về giải trí & truyền hình</h3>
                            <div class="survey-meta">
                                <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+45 điểm</span>
                                <span><i class="fas fa-clock me-1"></i>8 phút</span>
                            </div>
                        </div>
                        <p class="survey-desc">Chia sẻ sở thích giải trí, phim ảnh và các chương
                            trình TV yêu thích của bạn.</p>
                        <a href="#" class="btn btn-gradient mt-auto w-100">Bắt đầu ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="activity-section">
        <div class="container">
            <h2 class="section-title mb-3">Hoạt động gần đây</h2>
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="activity-content">
                        <h4>Hoàn thành khảo sát "Trải nghiệm du lịch"</h4>
                        <p>Bạn đã nhận được +45 điểm</p>
                    </div>
                    <div class="activity-time">2 giờ trước</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon activity-icon-reward">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="activity-content">
                        <h4>Đổi thưởng thành công</h4>
                        <p>Voucher Shopee 50.000đ</p>
                    </div>
                    <div class="activity-time">1 ngày trước</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon">
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

    <?php include BASE_PATH . '/app/Views/partials/_footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // Cá nhân hóa bằng localStorage: đọc thông tin người dùng đã lưu sau đăng nhập
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

                // Cập nhật điểm nếu có
                if (user.points) {
                    const userPointsEl = document.getElementById('user-points');
                    const totalPointsEl = document.getElementById('total-points');
                    if (userPointsEl) userPointsEl.textContent = user.points.toLocaleString('vi-VN');
                    if (totalPointsEl) totalPointsEl.textContent = user.points.toLocaleString('vi-VN');
                }
            } catch (_) {
                // ignore
            }
        });

        // Khởi tạo Chart.js biểu đồ
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
                                // Dữ liệu giả lập (thay thế bằng dữ liệu thật)
                                data: [120, 190, 300, 500, 220, 350],
                                backgroundColor: 'rgba(99, 102, 241, 0.8)', // Màu primary
                                borderColor: 'rgba(99, 102, 241, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
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
                                // Dữ liệu giả lập (thay thế bằng dữ liệu thật)
                                data: [3, 5, 2, 4, 6, 1, 3], // Tổng = 24 (khớp stat-card)
                                fill: true,
                                backgroundColor: 'rgba(236, 72, 153, 0.1)', // Màu accent
                                borderColor: 'rgba(236, 72, 153, 1)',
                                tension: 0.3 // Làm mịn đường cong
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            } catch (e) {
                console.error("Lỗi khi khởi tạo biểu đồ:", e);
            }

        });
    </script>

</body>


</html>