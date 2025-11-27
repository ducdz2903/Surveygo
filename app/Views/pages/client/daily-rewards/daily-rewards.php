<?php
/** @var string $appName */
/** @var array $urls */
/** @var string $baseUrl */

$appName = $appName ?? 'Surveygo';
$urls = $urls ?? [];

// Ensure URLs have absolute base prefix even if controller didn't pass them.
$__base = rtrim((string) ($baseUrl ?? ''), '/');
$__mk = static function (string $base, string $path): string {
    $p = '/' . ltrim($path, '/');
    return $base === '' ? $p : ($base . $p);
};
$urls['home'] = $urls['home'] ?? $__mk($__base, '/');
$urls['login'] = $urls['login'] ?? $__mk($__base, '/login');
$urls['register'] = $urls['register'] ?? $__mk($__base, '/register');

// Đường dẫn tới thư mục public
$__publicBase = $__base . '/public';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName . ' - Phần thưởng hằng ngày', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars($__publicBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($__publicBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/components/footer.css">
    <link rel="stylesheet"
        href="<?= htmlspecialchars($__publicBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/components/navbar.css">

    <link rel="stylesheet"
        href="<?= htmlspecialchars($__publicBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/client/daily-rewards.css">
</head>

<body class="page page--daily-rewards">
    <?php
    if (defined('BASE_PATH')) {
        include BASE_PATH . '/app/Views/components/client/_navbar.php';
    } else {
        echo '';
    }
    ?>

    <main class="page-content">
        <section class="daily-reward-section">
            <div class="container">
                <div class="reward-header">
                    <h1 class="reward-title">
                        <i class="fas fa-gift me-2"></i>Phần thưởng hằng ngày
                    </h1>
                    <p class="reward-subtitle">Điểm danh mỗi ngày để nhận phần thưởng hấp dẫn!</p>
                </div>

                <div class="reward-box">
                    <div class="reward-grid" id="rewardGrid">
                    </div>

                    <div class="claim-container">
                        <button class="btn btn-gradient btn-lg" id="claimBtn">
                            <i class="fas fa-check-circle me-2"></i>Điểm danh hôm nay
                        </button>
                        <div class="claim-status mt-3" id="claimStatus"></div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="note-box">
                    <h2 class="note-title">
                        <i class="fas fa-info-circle me-2"></i>Cách điểm danh
                    </h2>
                    <ul class="note-list">
                        <li>Mỗi ngày bạn có thể điểm danh 1 lần để nhận phần thưởng.</li>
                        <li>Bạn chỉ có thể điểm danh khi nhấn vào nút "Điểm danh hôm nay".</li>
                        <li>Chuỗi điểm danh sẽ bị reset nếu bạn bỏ qua hơn 1 ngày.</li>
                        <li>Càng nhiều ngày liên tiếp, phần thưởng càng lớn.</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <?php
    if (defined('BASE_PATH')) {
        include BASE_PATH . '/app/Views/components/client/_footer.php';
    } else {
        echo '';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL = '<?= htmlspecialchars($__base, ENT_QUOTES, 'UTF-8') ?>';

        // Cấu hình phần thưởng 30 ngày (đồng bộ với backend)
        const rewardDays = [
            // Tuần 1
            { day: 1, points: 10, icon: 'gift' },
            { day: 2, points: 15, icon: 'gift' },
            { day: 3, points: 15, icon: 'gift' },
            { day: 4, points: 20, icon: 'gift' },
            { day: 5, points: 25, icon: 'gift' },
            { day: 6, points: 30, icon: 'gift' },
            { day: 7, points: 50, icon: 'star' }, // Mốc
            // Tuần 2
            { day: 8, points: 20, icon: 'gift' },
            { day: 9, points: 25, icon: 'gift' },
            { day: 10, points: 30, icon: 'gift' },
            { day: 11, points: 30, icon: 'gift' },
            { day: 12, points: 35, icon: 'gift' },
            { day: 13, points: 40, icon: 'gift' },
            { day: 14, points: 75, icon: 'star' }, // Mốc
            // Tuần 3
            { day: 15, points: 30, icon: 'gift' },
            { day: 16, points: 35, icon: 'gift' },
            { day: 17, points: 40, icon: 'gift' },
            { day: 18, points: 40, icon: 'gift' },
            { day: 19, points: 45, icon: 'gift' },
            { day: 20, points: 50, icon: 'gift' },
            { day: 21, points: 100, icon: 'star' }, // Mốc
            // Tuần 4
            { day: 22, points: 40, icon: 'gift' },
            { day: 23, points: 45, icon: 'gift' },
            { day: 24, points: 50, icon: 'gift' },
            { day: 25, points: 50, icon: 'gift' },
            { day: 26, points: 55, icon: 'gift' },
            { day: 27, points: 60, icon: 'gift' },
            { day: 28, points: 125, icon: 'star' }, // Mốc
            // Ngày cuối
            { day: 29, points: 70, icon: 'gift' },
            { day: 30, points: 250, icon: 'crown' } // Mốc lớn
        ];

        function getCurrentUserId() {
            try {
                const raw = localStorage.getItem('app.user');
                if (!raw) {
                    return null;
                }
                const user = JSON.parse(raw);
                return user && user.id ? user.id : null;
            } catch (e) {
                return null;
            }
        }

        // Khởi tạo khi load trang
        document.addEventListener('DOMContentLoaded', async function () {
            await loadRewardData();
            renderRewardCards();
            setupClaimButton();
        });

        // Tải dữ liệu điểm danh từ API
        async function loadRewardData() {
            const claimStatus = document.getElementById('claimStatus');
            const claimBtn = document.getElementById('claimBtn');
            const userId = getCurrentUserId();

            if (!userId) {
                window.rewardData = {
                    lastClaimed: null,
                    currentStreak: 0,
                    claimedDays: []
                };

                if (claimStatus) {
                    claimStatus.innerHTML = '<small class="text-danger">Vui lòng đăng nhập để điểm danh mỗi ngày.</small>';
                }

                if (claimBtn) {
                    claimBtn.disabled = true;
                }
                return;
            }

            try {
                const res = await fetch(BASE_URL + '/api/daily-rewards/status?userId=' + encodeURIComponent(userId));
                const data = await res.json().catch(() => ({}));

                if (!res.ok || data.error) {
                    throw new Error(data.message || 'Không thể tải dữ liệu điểm danh.');
                }

                const payload = data.data || {};
                const lastClaimedDate = payload.lastClaimedDate;

                window.rewardData = {
                    lastClaimed: lastClaimedDate ? new Date(lastClaimedDate).toDateString() : null,
                    currentStreak: payload.currentStreak || 0,
                    claimedDays: []
                };
            } catch (e) {
                window.rewardData = {
                    lastClaimed: null,
                    currentStreak: 0,
                    claimedDays: []
                };

                if (claimStatus) {
                    claimStatus.innerHTML = '<small class="text-danger">Không thể tải dữ liệu điểm danh. Vui lòng thử lại sau.</small>';
                }
            }
        }

        // Vẽ các ô thưởng (xoay vòng sau 30 ngày)
        function renderRewardCards() {
            const grid = document.getElementById('rewardGrid');
            grid.innerHTML = '';

            const today = new Date().toDateString();
            const isTodayClaimed = window.rewardData.lastClaimed === today;
            const totalDays = rewardDays.length;
            const streak = Math.max(0, window.rewardData.currentStreak || 0);

            let claimedCountInCycle = 0;
            let todayIndex = -1;

            if (streak > 0) {
                if (isTodayClaimed) {
                    // Đã điểm danh hôm nay: tất cả ngày trong chu kỳ hiện tại tới ngày này được tính là "claimed"
                    claimedCountInCycle = ((streak - 1) % totalDays) + 1;
                } else {
                    // Chưa điểm danh hôm nay: chuỗi tính tới hôm qua, hôm nay là ngày tiếp theo trong chu kỳ
                    claimedCountInCycle = streak % totalDays;
                    todayIndex = claimedCountInCycle % totalDays; // ngày tiếp theo trong chu kỳ
                }
            } else {
                // Chưa có chuỗi nào, bắt đầu từ ngày 1
                claimedCountInCycle = 0;
                todayIndex = 0;
            }

            rewardDays.forEach((reward, index) => {
                const isClaimed = index < claimedCountInCycle;
                const isToday = index === todayIndex && !isTodayClaimed;

                const isMilestone = (reward.icon === 'star' || reward.icon === 'crown');

                const card = document.createElement('div');
                card.className = `reward-card ${isClaimed ? 'claimed' : ''} ${isToday ? 'today' : ''} ${isMilestone ? 'milestone' : ''}`;

                const iconName = isClaimed ? 'check-circle' : reward.icon;

                card.innerHTML = `
                    <div class="icon">
                        <i class="fas fa-${iconName}"></i>
                    </div>
                    <div class="day">Ngày ${reward.day}</div>
                    <div class="points">+${reward.points} điểm</div>
                `;

                grid.appendChild(card);
            });
        }

        // Cấu hình nút điểm danh
        function setupClaimButton() {
            const claimBtn = document.getElementById('claimBtn');
            const claimStatus = document.getElementById('claimStatus');

            if (!claimBtn) {
                return;
            }

            const today = new Date().toDateString();
            const isTodayClaimed = window.rewardData.lastClaimed === today;

            claimBtn.replaceWith(claimBtn.cloneNode(true));
            const newClaimBtn = document.getElementById('claimBtn');

            if (isTodayClaimed) {
                newClaimBtn.disabled = true;
                newClaimBtn.innerHTML = '<i class="fas fa-check me-2"></i>Đã điểm danh hôm nay';
                if (claimStatus) {
                    claimStatus.innerHTML = '<small class="text-success">Hẹn gặp lại bạn vào ngày mai!</small>';
                }
            } else {
                newClaimBtn.disabled = false;
                newClaimBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Điểm danh hôm nay';
                newClaimBtn.addEventListener('click', claimReward);
                if (claimStatus) {
                    claimStatus.innerHTML = `<small class="text-muted">Chuỗi hiện tại: <strong>${window.rewardData.currentStreak} ngày</strong></small>`;
                }
            }
        }

        // Gọi API để điểm danh
        async function claimReward() {
            const userId = getCurrentUserId();
            const claimStatus = document.getElementById('claimStatus');
            const claimBtn = document.getElementById('claimBtn');

            if (!userId) {
                if (claimStatus) {
                    claimStatus.innerHTML = '<small class="text-danger">Vui lòng đăng nhập để điểm danh.</small>';
                }
                if (claimBtn) {
                    claimBtn.disabled = true;
                }
                return;
            }

            const today = new Date().toDateString();
            const isTodayClaimed = window.rewardData.lastClaimed === today;

            if (isTodayClaimed) {
                showToast('warning', 'Bạn đã điểm danh hôm nay rồi!');
                return;
            }

            if (claimBtn) {
                claimBtn.disabled = true;
                claimBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            }

            try {
                const res = await fetch(BASE_URL + '/api/daily-rewards/claim', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'userId=' + encodeURIComponent(userId),
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || data.error) {
                    const msg = data.message || 'Điểm danh thất bại. Vui lòng thử lại.';
                    if (claimStatus) {
                        claimStatus.innerHTML = `<div class="alert alert-danger" role="alert">${msg}</div>`;
                    }
                    if (claimBtn) {
                        claimBtn.disabled = false;
                        claimBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Điểm danh hôm nay';
                    }
                    return;
                }

                const payload = data.data || {};
                const lastClaimedDate = payload.lastClaimedDate || payload.today;

                window.rewardData.currentStreak = payload.currentStreak || 0;
                window.rewardData.lastClaimed = lastClaimedDate ? new Date(lastClaimedDate).toDateString() : today;
                window.rewardData.claimedDays = window.rewardData.claimedDays || [];
                window.rewardData.claimedDays.push(window.rewardData.lastClaimed);

                const pointsEarned = payload.pointsEarned || 0;

                if (claimStatus) {
                    claimStatus.innerHTML = `
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            Chúc mừng! Bạn vừa nhận được <strong>+${pointsEarned} điểm</strong>
                        </div>
                    `;
                }

                setTimeout(() => {
                    renderRewardCards();
                    setupClaimButton();
                }, 1500);
            } catch (e) {
                if (claimStatus) {
                    claimStatus.innerHTML = '<div class="alert alert-danger" role="alert">Có lỗi xảy ra. Vui lòng thử lại.</div>';
                }
                if (claimBtn) {
                    claimBtn.disabled = false;
                    claimBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Điểm danh hôm nay';
                }
            }
        }
    </script>
</body>

</html>
