<?php
/** @var string $appName */
/** @var array $urls */
/** @var string $baseUrl */

$appName = $appName ?? 'Surveygo'; // Tên app của bạn
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

// Giả định đường dẫn public
$__publicBase = $__base . '/public';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale-1">
    <title><?= htmlspecialchars($appName . ' - Phần Thưởng Hằng Ngày', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars($__publicBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($__publicBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/components/footer.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($__publicBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/components/navbar.css">

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
                        <li>Mỗi ngày bạn có thể điểm danh một lần để nhận phần thưởng.</li>
                        <li>Bạn chỉ có thể điểm danh khi ấn vào nút "Điểm danh hôm nay".</li>
                        <li>Chuỗi điểm danh sẽ reset nếu bạn bỏ qua một ngày.</li>
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
        // Daily rewards data (DỮ LIỆU MỚI 30 NGÀY)
        const rewardDays = [
            // Tuần 1
            { day: 1, points: 10, icon: 'gift' },
            { day: 2, points: 15, icon: 'gift' },
            { day: 3, points: 15, icon: 'gift' },
            { day: 4, points: 20, icon: 'gift' },
            { day: 5, points: 25, icon: 'gift' },
            { day: 6, points: 30, icon: 'gift' },
            { day: 7, points: 50, icon: 'star' }, // Milestone
            // Tuần 2
            { day: 8, points: 20, icon: 'gift' },
            { day: 9, points: 25, icon: 'gift' },
            { day: 10, points: 30, icon: 'gift' },
            { day: 11, points: 30, icon: 'gift' },
            { day: 12, points: 35, icon: 'gift' },
            { day: 13, points: 40, icon: 'gift' },
            { day: 14, points: 75, icon: 'star' }, // Milestone
            // Tuần 3
            { day: 15, points: 30, icon: 'gift' },
            { day: 16, points: 35, icon: 'gift' },
            { day: 17, points: 40, icon: 'gift' },
            { day: 18, points: 40, icon: 'gift' },
            { day: 19, points: 45, icon: 'gift' },
            { day: 20, points: 50, icon: 'gift' },
            { day: 21, points: 100, icon: 'star' }, // Milestone
            // Tuần 4
            { day: 22, points: 40, icon: 'gift' },
            { day: 23, points: 45, icon: 'gift' },
            { day: 24, points: 50, icon: 'gift' },
            { day: 25, points: 50, icon: 'gift' },
            { day: 26, points: 55, icon: 'gift' },
            { day: 27, points: 60, icon: 'gift' },
            { day: 28, points: 125, icon: 'star' }, // Milestone
            // Ngày cuối
            { day: 29, points: 70, icon: 'gift' },
            { day: 30, points: 250, icon: 'crown' } // Milestone LỚN
        ];

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadRewardData();
            renderRewardCards();
            setupClaimButton();
        });

        // Load reward data from localStorage (Giữ nguyên)
        function loadRewardData() {
            // ... (Không cần thay đổi hàm này) ...
            const today = new Date().toDateString();
            const savedData = localStorage.getItem('dailyRewards');

            if (!savedData) {
                window.rewardData = {
                    lastClaimed: null,
                    currentStreak: 0,
                    claimedDays: []
                };
            } else {
                window.rewardData = JSON.parse(savedData);

                if (window.rewardData.lastClaimed !== today) {
                    if (window.rewardData.lastClaimed) {
                        const lastDate = new Date(window.rewardData.lastClaimed);
                        const currentDate = new Date(today);
                        const daysDiff = Math.floor((currentDate - lastDate) / (1000 * 60 * 60 * 24));

                        if (daysDiff > 1) {
                            window.rewardData.currentStreak = 0;
                            window.rewardData.claimedDays = [];
                        }
                    }
                }
            }
        }

        // Render reward cards (Giữ nguyên)
        function renderRewardCards() {
            // ... (Không cần thay đổi hàm này, nó vẫn hoạt động) ...
            const grid = document.getElementById('rewardGrid');
            grid.innerHTML = '';

            const today = new Date().toDateString();
            const isTodayClaimed = window.rewardData.lastClaimed === today;

            rewardDays.forEach((reward, index) => {
                const isClaimed = index < window.rewardData.currentStreak;
                const isToday = index === window.rewardData.currentStreak && !isTodayClaimed;

                const isMilestone = (reward.icon === 'star' || reward.icon === 'crown');

                const card = document.createElement('div');

                card.className = `reward-card ${isClaimed ? 'claimed' : ''} ${isToday ? 'today' : ''} ${isMilestone ? 'milestone' : ''}`;

                let iconName = isClaimed ? 'check-circle' : reward.icon;

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

        // Setup claim button (Giữ nguyên)
        function setupClaimButton() {
            // ... (Không cần thay đổi hàm này) ...
            const claimBtn = document.getElementById('claimBtn');
            const claimStatus = document.getElementById('claimStatus');

            const today = new Date().toDateString();
            const isTodayClaimed = window.rewardData.lastClaimed === today;

            if (isTodayClaimed) {
                claimBtn.disabled = true;
                claimBtn.innerHTML = '<i class="fas fa-check me-2"></i>Đã điểm danh hôm nay';
                claimStatus.innerHTML = `<small class="text-success">Hẹn gặp lại bạn vào ngày mai!</small>`;
            } else {
                claimBtn.disabled = false;
                claimBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Điểm danh hôm nay';
                claimBtn.addEventListener('click', claimReward);
                claimStatus.innerHTML = `<small class="text-muted">Chuỗi hiện tại: <strong>${window.rewardData.currentStreak} ngày</strong></small>`;
            }
        }

        // Claim daily reward (Giữ nguyên)
        function claimReward() {
            // ... (Không cần thay đổi hàm này) ...
            const today = new Date().toDateString();
            const isTodayClaimed = window.rewardData.lastClaimed === today;

            if (isTodayClaimed) {
                alert('Bạn đã điểm danh hôm nay rồi!');
                return;
            }

            const claimBtn = document.getElementById('claimBtn');
            claimBtn.disabled = true;
            claimBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';


            window.rewardData.currentStreak++;
            window.rewardData.lastClaimed = today;
            window.rewardData.claimedDays.push(today);

            // Xử lý trường hợp chuỗi > 30 ngày (quay vòng)
            let rewardIndex = window.rewardData.currentStreak - 1;
            if (rewardIndex >= rewardDays.length) {
                // Nếu muốn quay vòng về ngày 1:
                // rewardIndex = 0; 
                // window.rewardData.currentStreak = 1; // reset streak

                // Nếu muốn giữ phần thưởng ngày cuối:
                rewardIndex = rewardDays.length - 1;
            }

            const reward = rewardDays[rewardIndex];
            const pointsEarned = reward ? reward.points : 0;

            localStorage.setItem('dailyRewards', JSON.stringify(window.rewardData));

            const claimStatus = document.getElementById('claimStatus');
            claimStatus.innerHTML = `
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Chúc mừng! Bạn vừa nhận được <strong>+${pointsEarned} điểm</strong>
                </div>
            `;

            setTimeout(() => {
                renderRewardCards();
                setupClaimButton();
                claimStatus.innerHTML = `<small class="text-success">Hẹn gặp lại bạn vào ngày mai!</small>`;
            }, 1500);
        }
    </script>
</body>

</html>
</body>

</html>