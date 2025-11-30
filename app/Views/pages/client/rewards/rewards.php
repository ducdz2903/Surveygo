<section class="rewards-hero">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="rewards-title">Đổi Điểm</h1>
                    <p class="rewards-subtitle">Chuyển đổi điểm của bạn thành tiền mặt, thẻ quà tặng hoặc các phần
                        thưởng hấp dẫn khác.</p>
                    <div class="hero-stats d-flex gap-4 mt-4">
                        <div class="stat-item">
                            <div class="stat-value text-gradient" id="total-points-stat">1,250</div>
                            <div class="stat-label">Điểm Hiện Có</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value text-gradient" id="equivalent-money-stat">1.25</div>
                            <div class="stat-label">Triệu Đồng</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="points-balance-card">
                    <div class="points-balance-content">
                        <div class="balance-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="balance-text">
                            <span class="balance-label">Tổng Điểm</span>
                            <div class="balance-amount" id="total-points">1,250</div>
                            <span class="balance-subtext">Bạn có thể đổi ngay!</span>
                        </div>
                        <div class="balance-decoration">
                            <span class="decoration-badge">Premium</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="rewards-content">
    <div class="container py-5">
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills rewards-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="cash-tab" data-bs-toggle="tab" data-bs-target="#cash-rewards"
                    type="button" role="tab">
                    <i class="fas fa-wallet me-2"></i>Rút Tiền Mặt
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="card-tab" data-bs-toggle="tab" data-bs-target="#card-rewards" type="button"
                    role="tab">
                    <i class="fas fa-credit-card me-2"></i>Thẻ Quà Tặng
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gift-tab" data-bs-toggle="tab" data-bs-target="#gift-rewards" type="button"
                    role="tab">
                    <i class="fas fa-gift me-2"></i>Quà Tặng
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Cash Rewards Tab -->
            <div class="tab-pane fade show active" id="cash-rewards" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--cash">
                            <div class="reward-badge">Phổ biến</div>
                            <div class="reward-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h3 class="reward-title">100,000 đ</h3>
                            <p class="reward-price"><strong>500</strong> điểm</p>
                            <p class="reward-desc">Rút 100 nghìn đồng vào tài khoản ngân hàng</p>
                            <button class="btn btn-primary w-100 reward-btn" data-points="500" data-amount="100000">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--cash">
                            <div class="reward-badge">Tiết kiệm</div>
                            <div class="reward-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h3 class="reward-title">500,000 đ</h3>
                            <p class="reward-price"><strong>2,300</strong> điểm</p>
                            <p class="reward-desc">Rút 500 nghìn đồng vào tài khoản ngân hàng</p>
                            <button class="btn btn-primary w-100 reward-btn" data-points="2300" data-amount="500000">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--cash">
                            <div class="reward-badge">Tốt nhất</div>
                            <div class="reward-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h3 class="reward-title">1,000,000 đ</h3>
                            <p class="reward-price"><strong>4,500</strong> điểm</p>
                            <p class="reward-desc">Rút 1 triệu đồng vào tài khoản ngân hàng</p>
                            <button class="btn btn-primary w-100 reward-btn" data-points="4500" data-amount="1000000">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Rewards Tab -->
            <div class="tab-pane fade" id="card-rewards" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--card">
                            <div class="reward-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3 class="reward-title">Momo 50K</h3>
                            <p class="reward-price"><strong>250</strong> điểm</p>
                            <p class="reward-desc">Nhận mã Momo 50 nghìn đồng trong 24h</p>
                            <button class="btn btn-secondary-accent w-100 reward-btn" data-points="250"
                                data-amount="50000" data-type="momo">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--card">
                            <div class="reward-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3 class="reward-title">Zalo Pay 50K</h3>
                            <p class="reward-price"><strong>250</strong> điểm</p>
                            <p class="reward-desc">Nhận mã Zalo Pay 50 nghìn đồng trong 24h</p>
                            <button class="btn btn-secondary-accent w-100 reward-btn" data-points="250"
                                data-amount="50000" data-type="zalopay">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--card">
                            <div class="reward-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3 class="reward-title">Apple Gift 100K</h3>
                            <p class="reward-price"><strong>500</strong> điểm</p>
                            <p class="reward-desc">Nhận mã Apple Gift 100 nghìn đồng trong 2h</p>
                            <button class="btn btn-secondary-accent w-100 reward-btn" data-points="500"
                                data-amount="100000" data-type="apple">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--card">
                            <div class="reward-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3 class="reward-title">Google Play 100K</h3>
                            <p class="reward-price"><strong>500</strong> điểm</p>
                            <p class="reward-desc">Nhận mã Google Play 100 nghìn đồng trong 2h</p>
                            <button class="btn btn-secondary-accent w-100 reward-btn" data-points="500"
                                data-amount="100000" data-type="googleplay">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--card">
                            <div class="reward-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3 class="reward-title">Steam 100K</h3>
                            <p class="reward-price"><strong>500</strong> điểm</p>
                            <p class="reward-desc">Nhận mã Steam 100 nghìn đồng trong 2h</p>
                            <button class="btn btn-secondary-accent w-100 reward-btn" data-points="500"
                                data-amount="100000" data-type="steam">
                                <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gift Rewards Tab -->
            <div class="tab-pane fade" id="gift-rewards" role="tabpanel">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--gift">
                            <div class="reward-icon">
                                <i class="fas fa-headphones"></i>
                            </div>
                            <h3 class="reward-title">Tai nghe Bluetooth</h3>
                            <p class="reward-price"><strong>5,000</strong> điểm</p>
                            <p class="reward-desc">Tai nghe không dây chất lượng cao, giao hàng 3-5 ngày</p>
                            <button class="btn btn-warning w-100 reward-btn" data-points="5000" data-type="gift">
                                <i class="fas fa-arrow-right me-2"></i>Đổi ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--gift">
                            <div class="reward-icon">
                                <i class="fas fa-mobile"></i>
                            </div>
                            <h3 class="reward-title">Sạc dự phòng 20K</h3>
                            <p class="reward-price"><strong>1,500</strong> điểm</p>
                            <p class="reward-desc">Pin sạc dự phòng 20000mAh, giao hàng 2-3 ngày</p>
                            <button class="btn btn-warning w-100 reward-btn" data-points="1500" data-type="gift">
                                <i class="fas fa-arrow-right me-2"></i>Đổi ngay
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="reward-card reward-card--gift">
                            <div class="reward-icon">
                                <i class="fas fa-keyboard"></i>
                            </div>
                            <h3 class="reward-title">Bàn phím Cơ</h3>
                            <p class="reward-price"><strong>8,000</strong> điểm</p>
                            <p class="reward-desc">Bàn phím cơ chuyên gaming RGB, giao hàng 5-7 ngày</p>
                            <button class="btn btn-warning w-100 reward-btn" data-points="8000" data-type="gift">
                                <i class="fas fa-arrow-right me-2"></i>Đổi ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Exchange History -->
<section class="history-section">
    <div class="container py-5">
        <h2 class="section-title mb-4">Lịch sử Đổi Điểm</h2>
        <div class="table-responsive">
            <table class="table table-hover history-table">
                <thead>
                    <tr>
                        <th>Ngày Đổi</th>
                        <th>Loại</th>
                        <th>Điểm Tiêu</th>
                        <th>Giá Trị</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024-11-15</td>
                        <td><span class="badge bg-primary">Rút Tiền</span></td>
                        <td>500</td>
                        <td>100,000 đ</td>
                        <td><span class="badge bg-success">Thành công</span></td>
                    </tr>
                    <tr>
                        <td>2024-11-10</td>
                        <td><span class="badge bg-secondary-accent">Momo</span></td>
                        <td>250</td>
                        <td>50,000 đ</td>
                        <td><span class="badge bg-success">Thành công</span></td>
                    </tr>
                    <tr>
                        <td>2024-11-05</td>
                        <td><span class="badge bg-warning">Quà Tặng</span></td>
                        <td>1,500</td>
                        <td>Pin sạc dự phòng</td>
                        <td><span class="badge bg-info">Đang giao</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác Nhận Đổi Điểm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn muốn đổi <strong id="modal-points">0</strong> điểm để nhận <strong id="modal-value">0</strong>?
                </p>
                <p class="text-muted">Điểm của bạn sẽ bị trừ sau khi xác nhận.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Lấy thông tin người dùng từ localStorage
        try {
            const raw = localStorage.getItem('app.user');
            if (raw) {
                const user = JSON.parse(raw);
                const points = user.points || 1250;
                const money = (points / 1000).toLocaleString('vi-VN');

                // Cập nhật tất cả các element hiển thị điểm
                document.getElementById('total-points').textContent = points.toLocaleString('vi-VN');
                document.getElementById('total-points-stat').textContent = points.toLocaleString('vi-VN');
                document.getElementById('equivalent-money-stat').textContent = money;
            }
        } catch (e) {
            console.error('Error reading user data:', e);
        }

        // Xử lý nút Đổi Ngay
        const rewardBtns = document.querySelectorAll('.reward-btn');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        let selectedReward = {};

        rewardBtns.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                selectedReward = {
                    points: this.dataset.points,
                    amount: this.dataset.amount || 'Quà tặng',
                    type: this.dataset.type || 'unknown'
                };

                document.getElementById('modal-points').textContent = selectedReward.points;
                document.getElementById('modal-value').textContent = selectedReward.amount;
                confirmModal.show();
            });
        });

        // Xử lý xác nhận
        document.getElementById('confirmBtn').addEventListener('click', function () {
            showToast('success', 'Đổi điểm thành công! Xin cảm ơn.');
            confirmModal.hide();
        });
    });
</script>