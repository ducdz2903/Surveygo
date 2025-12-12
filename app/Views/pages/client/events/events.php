<?php
// Dữ liệu mẫu (Tận dụng lại)
$events = [
    [
        'id' => 'evt_001',
        'title' => 'Sự kiện Khảo sát tháng 11',
        'desc' => 'Tham gia khảo sát đặc biệt để nhận điểm thưởng gấp đôi!',
        'date' => '10/11/2025 - 20/11/2025',
        'image' => 'https://media-cdn-v2.laodong.vn/Storage/NewsPortal/2020/6/18/813624/Anh-Thien-Nhien-Dep--04.jpg'
    ],
    [
        'id' => 'evt_002',
        'title' => 'Khảo sát đặc biệt: Lối sống Gen Z',
        'desc' => 'Hoàn thành khảo sát 10 phút và nhận ngay 500 điểm thưởng.',
        'date' => '12/11/2025 - 15/11/2025',
        'image' => 'https://media-cdn-v2.laodong.vn/Storage/NewsPortal/2020/6/18/813624/Anh-Thien-Nhien-Dep--04.jpg'
    ]
];
$winners = [
    [
        'id' => 'win_001',
        'title' => 'Công bố: Mini Game Rút thăm may mắn tháng 10',
        'announce_date' => '05/11/2025',
    ],
    [
        'id' => 'win_002',
        'title' => 'Công bố: Sự kiện Giới thiệu bạn bè 2025',
        'announce_date' => '02/11/2025',
    ]
];

// Giả lập dữ liệu người dùng
$userData = [
    'points' => 2000,
    'level' => 'Bạc',
    'hasCheckedIn' => false // false = chưa điểm danh, true = đã điểm danh
];
?>


<main class="page-content">
    <div class="container">

        <section class="daily-mission-card <?= $userData['hasCheckedIn'] ? 'checked-in' : '' ?>"
            id="daily-checkin-card">
            <div class="mission-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="mission-content">
                <h3 class="mission-title">Nhiệm Vụ Hàng Ngày</h3>
                <p class="mission-desc">Điểm danh mỗi ngày để nhận <strong>+50 điểm</strong> và duy trì chuỗi của
                    bạn!</p>
            </div>
            <div class="mission-action">
                <a href="<?= rtrim($baseUrl, '/') ?>/daily-rewards" class="btn btn-light btn-lg" id="btn-checkin"
                    <?= $userData['hasCheckedIn'] ? 'style="pointer-events: none; opacity: 0.6;"' : '' ?>>
                    <?= $userData['hasCheckedIn'] ? '<i class="fas fa-check me-2"></i> Đã điểm danh' : '<i class="fas fa-hand-pointer me-2"></i> Điểm Danh Ngay' ?>
                </a>
            </div>
        </section>

        <div class="row g-4 mt-4">

            <div class="col-lg-8">
                <div class="feed-column">
                    <h2 class="section-title"><i class="fas fa-bullhorn me-2"></i>Sự Kiện Nổi Bật</h2>

                    <?php foreach ($events as $event): ?>
                        <div class="event-card-feed" data-event-id="<?= $event['id'] ?>">
                            <div class="event-card-img"
                                style="background-image: url('<?= htmlspecialchars($event['image'], ENT_QUOTES, 'UTF-8') ?>')">
                            </div>
                            <div class="event-card-content">
                                <span class="event-date">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= htmlspecialchars($event['date'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <h3 class="event-title"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <p class="event-desc"><?= htmlspecialchars($event['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                                <a href="#" class="btn btn-primary-gradient btn-sm event-join-btn">
                                    Tham gia ngay <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <h2 class="section-title mt-4"><i class="fas fa-trophy me-2"></i>Vinh Danh</h2>

                    <div class="winner-list">
                        <?php foreach ($winners as $winner): ?>
                            <div class="winner-item" data-winner-id="<?= $winner['id'] ?>">
                                <div class="winner-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="winner-info">
                                    <span
                                        class="winner-title"><?= htmlspecialchars($winner['title'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="winner-date">Ngày công bố:
                                        <?= htmlspecialchars($winner['announce_date'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <a href="#" class="btn btn-outline-primary btn-sm winner-check-btn">
                                    Xem chi tiết
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-column">
                    <h2 class="section-title"><i class="fas fa-star me-2"></i>Hoạt Động Khác</h2>

                    <div class="sidebar-card" id="sidebar-wallet">
                        <div class="card-icon" style="color: #28a745;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 class="card-title">Ví Của Tôi</h4>
                        <p class="card-desc">Bạn đang có <strong id="user-points-display">---</strong> điểm thưởng.</p>
                        <a href="/rewards" class="btn btn-secondary-accent w-100">Đổi Quà</a>
                    </div>

                    <div class="sidebar-card" id="sidebar-lucky-draw">
                        <div class="card-icon" style="color: var(--secondary-accent);">
                            <i class="fas fa-ticket"></i>
                        </div>
                        <h4 class="card-title">Rút Thăm May Mắn</h4>
                        <p class="card-desc">Bạn có <strong id="remaining-spins">3</strong> lượt quay miễn phí. Thử vận may ngay!</p>
                        <button type="button" class="btn btn-secondary-accent w-100" id="quay-ngay-btn">Quay Ngay</button>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const spinBtn = document.getElementById('quay-ngay-btn');
                            const pointsDisplay = document.getElementById('user-points-display');
                            const remainingSpinsDisplay = document.getElementById('remaining-spins');
                            
                            // Lấy user từ localStorage
                            let user = null;
                            try {
                                const raw = localStorage.getItem('app.user');
                                user = raw ? JSON.parse(raw) : null;
                            } catch (e) {
                                console.error("Lỗi đọc user từ localStorage", e);
                            }

                            // Fetch points từ API
                            function fetchPoints() {
                                if (user && user.id && pointsDisplay) {
                                    fetch(`/api/users/points?userId=${user.id}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (!data.error) {
                                                pointsDisplay.textContent = data.data.balance;
                                            }
                                        })
                                        .catch(err => console.error(err));
                                }
                            }

                            // Fetch số lượt quay còn lại từ backend
                            function fetchRemainingSpins() {
                                if (user && user.id && remainingSpinsDisplay) {
                                    fetch(`/api/users/points?userId=${user.id}`)
                                        .then(res => res.json())
                                        .then(data => {
                                            if (!data.error && data.data.lucky_wheel_spins !== undefined) {
                                                const spins = data.data.lucky_wheel_spins;
                                                remainingSpinsDisplay.textContent = spins;
                                                
                                                // Disable button if no spins
                                                const spinBtn = document.getElementById('quay-ngay-btn');
                                                if (spins <= 0 && spinBtn) {
                                                    spinBtn.disabled = true;
                                                    spinBtn.textContent = 'Hết lượt quay';
                                                } else if (spinBtn) {
                                                    spinBtn.disabled = false;
                                                    spinBtn.textContent = 'Quay Ngay';
                                                }
                                            }
                                        })
                                        .catch(err => console.error(err));
                                }
                            }

                            // Fetch ngay khi load
                            fetchPoints();
                            fetchRemainingSpins();

                            // Xử lý click nút "Quay Ngay" - Hiển thị modal xác suất
                            if (spinBtn) {
                                spinBtn.addEventListener('click', function() {
                                    if (!user || !user.id) {
                                        alert("Bạn cần đăng nhập để quay thưởng!");
                                        window.location.href = '/login';
                                        return;
                                    }
                                    
                                    // Hiển thị modal xác suất
                                    var modal = new bootstrap.Modal(document.getElementById('probabilitiesModal'));
                                    modal.show();
                                });
                            }

                            // Xử lý click "Xác nhận quay" trong modal
                            const confirmSpinBtn = document.getElementById('confirmSpin');
                            if (confirmSpinBtn) {
                                confirmSpinBtn.addEventListener('click', function() {
                                    // Disable nút để tránh spam
                                    confirmSpinBtn.disabled = true;
                                    confirmSpinBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang quay...';

                                    // Đóng modal xác suất
                                    var probModal = bootstrap.Modal.getInstance(document.getElementById('probabilitiesModal'));
                                    probModal.hide();

                                    // Gọi API backend để quay thưởng
                                    fetch('/api/events/lucky-wheel/spin', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                userId: user.id
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.error) {
                                                // Hiển thị lỗi trong modal result
                                                document.getElementById('resultMessage').innerHTML = 
                                                    '<i class="fas fa-exclamation-circle text-danger me-2"></i>' + data.message;
                                            } else {
                                                // Hiển thị kết quả thành công
                                                const pointsAdded = data.data.points_added || 0;
                                                document.getElementById('resultMessage').innerHTML = 
                                                    '<i class="fas fa-gift text-success me-2" style="font-size: 2rem;"></i><br>' +
                                                    '<strong style="font-size: 1.5rem; color: var(--primary-color);">' + pointsAdded + ' điểm!</strong><br>' +
                                                    '<span class="text-muted">Số dư mới: ' + (data.data.new_balance || '---') + ' điểm</span>';
                                                
                                                // Cập nhật điểm hiển thị
                                                if(data.data && data.data.new_balance !== undefined) {
                                                    if(pointsDisplay) pointsDisplay.textContent = data.data.new_balance;
                                                } else {
                                                    fetchPoints(); // Fallback
                                                }


                                                // Cập nhật số lượt còn lại
                                                if (data.data && data.data.spins_remaining !== undefined) {
                                                    remainingSpinsDisplay.textContent = data.data.spins_remaining;
                                                    
                                                    // Disable if no more spins
                                                    if (data.data.spins_remaining <= 0) {
                                                        const spinBtn = document.getElementById('quay-ngay-btn');
                                                        if (spinBtn) {
                                                            spinBtn.disabled = true;
                                                            spinBtn.textContent = 'Hết lượt quay';
                                                        }
                                                    }
                                                } else {
                                                    fetchRemainingSpins();  // Fallback: gọi lại API
                                                }
                                            }

                                            // Hiển thị modal kết quả
                                            var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                                            resultModal.show();
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            document.getElementById('resultMessage').innerHTML = 
                                                '<i class="fas fa-times-circle text-danger me-2"></i>Có lỗi xảy ra, vui lòng thử lại sau.';
                                            
                                            var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
                                            resultModal.show();
                                        })
                                        .finally(() => {
                                            // Enable lại nút
                                            confirmSpinBtn.disabled = false;
                                            confirmSpinBtn.innerHTML = 'Xác nhận quay';
                                        });
                                });
                            }
                        });
                    </script>

                    <div class="sidebar-card" id="sidebar-referral">
                        <div class="card-icon" style="color: var(--accent-color);">
                            <i class="fas fa-user-group"></i>
                        </div>
                        <h4 class="card-title">Mời Bạn Bè</h4>
                        <p class="card-desc">Nhận <strong>500</strong> điểm cho mỗi người bạn mời thành công.</p>
                        <a href="#" class="btn btn-outline-accent w-100">Lấy link mời</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

  

    <!-- Modal hiển thị xác suất nhận thưởng -->
    <div class="modal fade custom-modal" id="probabilitiesModal" tabindex="-1" aria-labelledby="probabilitiesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="probabilitiesModalLabel">
                        <i class="fas fa-gift me-2"></i>Xác suất nhận thưởng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Tỷ lệ trúng thưởng cho mỗi giải:
                    </p>
                    <ul>
                        <li>
                            <span><strong>10 điểm:</strong></span>
                            <span class="prize-badge">40%</span>
                        </li>
                        <li>
                            <span><strong>20 điểm:</strong></span>
                            <span class="prize-badge">30%</span>
                        </li>
                        <li>
                            <span><strong>50 điểm:</strong></span>
                            <span class="prize-badge">20%</span>
                        </li>
                        <li>
                            <span><strong>100 điểm:</strong></span>
                            <span class="prize-badge">5%</span>
                        </li>
                        <li>
                            <span><strong>200 điểm:</strong></span>
                            <span class="prize-badge">3%</span>
                        </li>
                        <li>
                            <span><strong>500 điểm:</strong></span>
                            <span class="prize-badge">2%</span>
                        </li>
                    </ul>
                    <p class="text-muted mt-3 mb-0" style="font-size: 0.875rem;">
                        💡 Điểm trung bình mỗi lần quay: <strong>~41 điểm</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary-gradient" id="confirmSpin">
                        <i class="fas fa-play-circle me-1"></i>Xác nhận quay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal hiển thị kết quả -->
    <div class="modal fade custom-modal" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resultModalLabel">
                        <i class="fas fa-trophy me-2"></i>Kết quả quay thưởng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p id="resultMessage" class="mb-0 fs-5"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary-gradient w-100" data-bs-dismiss="modal">
                        <i class="fas fa-check-circle me-1"></i>Nhận điểm
                    </button>
                </div>
            </div>
        </div>
    </div>

      <!-- Custom Modal Styles -->
    <style>
        .custom-modal .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100vh - 1rem);
        }
        .custom-modal .modal-content {
            background: var(--hub-card-bg, white);
            border: 1px solid var(--hub-card-border, #e2e8f0);
            border-radius: 16px;
            box-shadow: var(--hub-card-shadow, 0 4px 20px rgba(0, 0, 0, 0.08));
            color: var(--hub-text-primary, #1e293b);
        }
        .custom-modal .modal-header {
            border-bottom: 1px solid var(--hub-card-border, #e2e8f0);
            padding: 1.5rem;
        }
        .custom-modal .modal-body {
            padding: 1.5rem;
        }
        .custom-modal .modal-body ul {
            list-style: none;
            padding: 0;
        }
        .custom-modal .modal-body li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--hub-card-border, #e2e8f0);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .custom-modal .modal-body li:last-child {
            border-bottom: none;
        }
        .custom-modal .modal-footer {
            border-top: 1px solid var(--hub-card-border, #e2e8f0);
            padding: 1.5rem;
        }
        .custom-modal .btn {
            border-radius: 8px;
            font-weight: 600;
        }
        .custom-modal .prize-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>
</main>