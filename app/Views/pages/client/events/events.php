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
    'points' => 1250,
    'level' => 'Bạc',
    'hasCheckedIn' => false // false = chưa điểm danh, true = đã điểm danh
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trung tâm Nhiệm vụ - Surveygo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="public/assets/css/app.css">
    <link rel="stylesheet" href="public/assets/css/components/navbar.css">
    <link rel="stylesheet" href="public/assets/css/components/footer.css">
    <link rel="stylesheet" href="public/assets/css/client/events.css">
</head>

<body class="page-mission-hub">
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

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

                        <div class="sidebar-card" id="sidebar-lucky-draw">
                            <div class="card-icon" style="color: var(--secondary-accent);">
                                <i class="fas fa-ticket"></i>
                            </div>
                            <h4 class="card-title">Rút Thăm May Mắn</h4>
                            <p class="card-desc">Bạn có <strong>3</strong> lượt quay miễn phí. Thử vận may ngay!</p>
                            <a href="#" class="btn btn-secondary-accent w-100">Quay Ngay</a>
                        </div>

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
    </main>

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/assets/js/mission-hub.js" defer></script>
</body>

</html>