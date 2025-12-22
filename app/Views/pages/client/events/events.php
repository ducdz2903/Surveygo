<?php
// Dữ liệu ban đầu cho thẻ nhiệm vụ ngày (tạm dùng để setup trạng thái UI)
$userData = [
    'hasCheckedIn' => false,
];
?>

<main class="page-content">
    <div class="container">

        <!-- Nhiệm vụ điểm danh hằng ngày -->
        <section class="daily-mission-card <?= $userData['hasCheckedIn'] ? 'checked-in' : '' ?>"
            id="daily-checkin-card">
            <div class="mission-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="mission-content">
                <h3 class="mission-title">Nhiệm Vụ Hằng Ngày</h3>
                <p class="mission-desc">
                    Điểm danh mỗi ngày để nhận <strong>+50 điểm</strong> và duy trì chuỗi tham gia của bạn!
                </p>
            </div>
            <div class="mission-action">
                <a href="<?= rtrim($baseUrl ?? '', '/') ?>/daily-rewards" class="btn btn-light btn-lg" id="btn-checkin"
                    <?= $userData['hasCheckedIn'] ? 'style="pointer-events: none; opacity: 0.6;"' : '' ?>>
                    <?= $userData['hasCheckedIn']
                        ? '<i class="fas fa-check me-2"></i> Đã điểm danh'
                        : '<i class="fas fa-hand-pointer me-2"></i> Điểm Danh Ngay' ?>
                </a>
            </div>
        </section>

        <div class="row g-4 mt-4">
            <!-- Cột feed sự kiện -->
            <div class="col-lg-8">
                <div class="feed-column">
                    <h2 class="section-title">
                        <i class="fas fa-bullhorn me-2"></i>Sự Kiện Nổi Bật
                    </h2>

                    <div id="events-feed-list">
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 mb-0">Đang tải danh sách sự kiện...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột sidebar: ví, vòng quay may mắn, giới thiệu bạn bè -->
            <div class="col-lg-4">
                <div class="sidebar-column">
                    <h2 class="section-title">
                        <i class="fas fa-star me-2"></i>Hoạt Động Khác
                    </h2>

                    <!-- Ví điểm -->
                    <div class="sidebar-card" id="sidebar-wallet">
                        <div class="card-icon" style="color: #28a745;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4 class="card-title">Ví Của Bạn</h4>
                        <p class="card-desc">
                            Bạn đang có <strong id="user-points-display">---</strong> điểm thưởng.
                        </p>
                        <a href="<?= rtrim($baseUrl ?? '', '/') ?>/rewards" class="btn btn-secondary-accent w-100">
                            Đổi thưởng
                        </a>
                    </div>

                    <!-- Vòng quay may mắn -->
                    <div class="sidebar-card" id="sidebar-lucky-draw">
                        <div class="card-icon" style="color: var(--secondary-accent);">
                            <i class="fas fa-ticket"></i>
                        </div>
                        <h4 class="card-title">Rút Thăm May Mắn</h4>
                        <p class="card-desc">
                            Bạn còn <strong id="remaining-spins">0</strong> lượt quay miễn phí. Thử vận may ngay!
                        </p>
                        <button type="button" class="btn btn-secondary-accent w-100" id="quay-ngay-btn">
                            Quay Ngay
                        </button>
                    </div>

                    <!-- Giới thiệu bạn bè -->
                    <div class="sidebar-card" id="sidebar-referral">
                        <div class="card-icon" style="color: var(--accent-color);">
                            <i class="fas fa-user-group"></i>
                        </div>
                        <h4 class="card-title">Mời Bạn Bè</h4>
                        <p class="card-desc">
                            Nhận <strong>500</strong> điểm cho mỗi người bạn mời đăng ký và tham gia.
                        </p>
                        <a href="#" class="btn btn-outline-accent w-100">Lấy link mời</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác suất Lucky Wheel -->
    <div class="modal fade custom-modal" id="probabilitiesModal" tabindex="-1" aria-labelledby="probabilitiesModalLabel"
        aria-hidden="true">
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
                        Tỷ lệ trúng thưởng cho mỗi mức điểm:
                    </p>
                    <ul>
                        <li><span><strong>10 điểm:</strong></span><span class="prize-badge">40%</span></li>
                        <li><span><strong>20 điểm:</strong></span><span class="prize-badge">30%</span></li>
                        <li><span><strong>50 điểm:</strong></span><span class="prize-badge">20%</span></li>
                        <li><span><strong>100 điểm:</strong></span><span class="prize-badge">5%</span></li>
                        <li><span><strong>200 điểm:</strong></span><span class="prize-badge">3%</span></li>
                        <li><span><strong>500 điểm:</strong></span><span class="prize-badge">2%</span></li>
                    </ul>
                    <p class="text-muted mt-3 mb-0" style="font-size: 0.875rem;">
                        Điểm trung bình mỗi lần quay: <strong>~41 điểm</strong>
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

    <!-- Modal kết quả Lucky Wheel -->
    <div class="modal fade custom-modal" id="resultModal" tabindex="-1" aria-labelledby="resultModalLabel"
        aria-hidden="true">
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

    <!-- Script: load danh sách sự kiện & join -->
    <script>
        // Define baseUrl and apiUrl globally so both scripts can use them
        const baseUrl = <?= json_encode(rtrim($baseUrl ?? '', '/')) ?>;
        const apiUrl = (path) => {
            const base = (baseUrl || '').replace(/\/+$/, '');
            const p = '/' + String(path || '').replace(/^\/+/, '');
            return base ? (base + p) : p;
        };

        // Danh sách ảnh sự kiện từ thư viện Asset/event
        const eventImages = [
            'pexels-abbykihano-431722.jpg',
            'pexels-alex-andrews-271121-1983046.jpg',
            'pexels-alxs-919734.jpg',
            'pexels-annamw-1047442.jpg',
            'pexels-apasaric-2078071.jpg',
            'pexels-arts-1164985.jpg',
            'pexels-asadphoto-169190.jpg',
            'pexels-asadphoto-169198.jpg',
            'pexels-asphotography-226737.jpg',
            'pexels-bertellifotografia-2608512.jpg',
            'pexels-bertellifotografia-2608517.jpg',
            'pexels-caleboquendo-3023317.jpg',
            'pexels-designecologist-2526105.jpg',
            'pexels-emma-bauso-1183828-2253831.jpg',
            'pexels-expect-best-79873-1243337.jpg',
            'pexels-fu-zhichao-176355-587741.jpg',
            'pexels-icsa-833425-1708912.jpg',
            'pexels-icsa-833425-1709003.jpg',
            'pexels-jibarofoto-2774556.jpg',
            'pexels-jmark-301987.jpg',
            'pexels-joshsorenson-976866.jpg',
            'pexels-lorentzworks-668137.jpg',
            'pexels-marc-schulte-656598-2952834.jpg',
            'pexels-mark-angelo-sampan-738078-1587927.jpg',
            'pexels-mat-brown-150387-1395964.jpg',
            'pexels-mat-brown-150387-1395967.jpg',
            'pexels-maumascaro-801863.jpg',
            'pexels-melissa-220267-698907.jpg',
            'pexels-mikky-k-158844-625644.jpg',
            'pexels-minan1398-1157557.jpg',
            'pexels-nietjuhart-796606.jpg',
            'pexels-olly-787961.jpg',
            'pexels-picjumbo-com-55570-196652.jpg',
            'pexels-pixabay-159213.jpg',
            'pexels-pixabay-433452.jpg',
            'pexels-pixabay-50675.jpg',
            'pexels-salooart-16408.jpg',
            'pexels-tae-fuller-331517-1616113.jpg',
            'pexels-teddy-2263436.jpg',
            'pexels-thatguycraig000-2306277.jpg',
            'pexels-thatguycraig000-2306281.jpg',
            'pexels-wendywei-1190297.jpg',
            'pexels-wendywei-1540338.jpg',
            'pexels-wendywei-1677710.jpg',
            'pexels-wolfgang-1002140-2747446.jpg',
            'pexels-wolfgang-1002140-2747449.jpg',
            'pexels-ywanphoto-57980.jpg',
            'pexels-zhuhehuai-716276.jpg'
        ];

        // Hàm lấy ảnh cho event dựa trên ID để không trùng lặp
        function getEventImage(eventId) {
            const index = (eventId - 1) % eventImages.length;
            const imageName = eventImages[index];
            return (baseUrl || '') + '/Asset/event/' + imageName;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const eventsContainer = document.getElementById('events-feed-list');


            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function formatDateTime(value) {
                if (!value) return '-';
                const d = new Date(value);
                if (Number.isNaN(d.getTime())) return value;
                return d.toLocaleString('vi-VN');
            }

            function formatDateRange(start, end) {
                const s = formatDateTime(start);
                const e = formatDateTime(end);
                if (!start && !end) return '-';
                if (start && !end) return s;
                if (!start && end) return e;
                return s + ' – ' + e;
            }

            function getStatusBadge(status) {
                const normalized = status || 'upcoming';
                let cls = 'badge ';
                let label = normalized;
                switch (normalized) {
                    case 'upcoming':
                        cls += 'bg-info-subtle text-info border';
                        label = 'Sắp diễn ra';
                        break;
                    case 'ongoing':
                        cls += 'bg-success-subtle text-success border';
                        label = 'Đang diễn ra';
                        break;
                    case 'completed':
                        cls += 'bg-secondary-subtle text-secondary border';
                        label = 'Đã kết thúc';
                        break;
                    default:
                        cls += 'bg-secondary-subtle text-secondary border';
                }
                return `<span class="${cls}">${label}</span>`;
            }

            function renderEvents(events) {
                if (!eventsContainer) return;

                if (!events || events.length === 0) {
                    eventsContainer.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times mb-2 display-6"></i><br>
                            Không tìm thấy sự kiện nào.
                        </div>`;
                    return;
                }

                eventsContainer.innerHTML = events.map(ev => {
                    const title = escapeHtml(ev.title || 'Sự kiện không tên');
                    const location = escapeHtml(ev.location || 'Online');
                    const dateText = formatDateRange(ev.startDate, ev.endDate);
                    const participants = ev.participants ?? 0;
                    const surveys = ev.surveys ?? 0;
                    const status = ev.status || 'upcoming';

                    const statusBadge = getStatusBadge(status);
                    const hasJoined = !!ev.hasJoined;

                    const isUpcoming = status === 'upcoming';
                    const isCompleted = status === 'completed';



                    const joinedLabel = hasJoined
                        ? '<span class="badge bg-success-subtle text-success border ms-1"><i class="fas fa-check me-1"></i>Đã tham gia</span>'
                        : '';

                    let buttonHtml = '';
                    if (hasJoined) {
                        buttonHtml = `<button type="button"
                                   class="btn btn-primary-gradient btn-sm event-join-btn event-view-btn"
                                   data-event-id="${ev.id}">
                               Xem chi tiết <i class="fas fa-arrow-right ms-1"></i>
                           </button>`;
                    } else if (isUpcoming) {
                        buttonHtml = `<button type="button"
                                   class="btn btn-primary-gradient btn-sm event-join-btn"
                                   disabled
                                   title="Sự kiện sắp diễn ra, chưa mở tham gia">
                               Tham gia ngay <i class="fas fa-arrow-right ms-1"></i>
                           </button>`;
                    } else if (isCompleted) {
                        buttonHtml = `<button type="button"
                                   class="btn btn-primary-gradient btn-sm event-join-btn"
                                   disabled
                                   title="Sự kiện đã kết thúc">
                               Tham gia ngay <i class="fas fa-arrow-right ms-1"></i>
                           </button>`;
                    } else {
                        buttonHtml = `<button type="button"
                                   class="btn btn-primary-gradient btn-sm event-join-btn"
                                   data-event-id="${ev.id}">
                               Tham gia ngay <i class="fas fa-arrow-right ms-1"></i>
                           </button>`;
                    }
                    return `
                        <div class="event-card-feed mb-3" data-event-id="${ev.id}">
                            <div class="event-card-img"
                                 style="background-image: url('${getEventImage(ev.id)}'); background-size: cover; background-position: center;">
                            </div>
                            <div class="event-card-content">
                                <span class="event-date d-block mb-1">
                                    <i class="fas fa-calendar-alt me-1"></i>${dateText}
                                </span>
                                <h3 class="event-title mb-1">${title}</h3>
                                <p class="event-desc mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i>${location}
                                </p>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2 small text-muted">
                                    ${statusBadge}
                                    <span><i class="fas fa-users me-1"></i>${participants} tham gia</span>
                                    <span><i class="fas fa-list-check me-1"></i>${surveys} khảo sát</span>
                                    ${joinedLabel}
                                </div>
                                ${buttonHtml}
                            </div>
                        </div>
                    `;
                }).join('');

                attachJoinHandlers();
                attachViewHandlers();
            }

            async function loadEvents() {
                if (!eventsContainer) return;
                eventsContainer.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 mb-0">Đang tải danh sách sự kiện...</p>
                    </div>`;

                try {
                    const params = new URLSearchParams({ page: 1, limit: 10 });
                    const res = await fetch(apiUrl('/api/events?' + params.toString()), {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!res.ok) {
                        throw new Error('Lỗi server: ' + res.status);
                    }

                    const json = await res.json().catch(() => ({}));
                    const data = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
                    renderEvents(data);
                } catch (err) {
                    console.error(err);
                    eventsContainer.innerHTML = `
                        <div class="text-center py-5 text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Không thể tải danh sách sự kiện: ${escapeHtml(err.message || 'Lỗi không xác định')}
                        </div>`;
                }
            }

            function attachJoinHandlers() {
                const buttons = document.querySelectorAll('.event-join-btn');
                buttons.forEach(btn => {
                    if (btn._boundJoinHandler) return;
                    btn._boundJoinHandler = true;

                    btn.addEventListener('click', async function (e) {
                        e.preventDefault();
                        const eventId = this.getAttribute('data-event-id');
                        if (!eventId) return;

                        const originalHtml = this.innerHTML;
                        this.disabled = true;
                        this.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span>Đang tham gia...';

                        try {
                            const res = await fetch(apiUrl(`/api/events/${eventId}/join`), {
                                method: 'POST',
                                headers: { 'Accept': 'application/json' }
                            });
                            const json = await res.json().catch(() => ({}));

                            if (!res.ok || json.error) {
                                throw new Error(json.message || res.statusText || 'Không thể tham gia sự kiện');
                            }

                            // Tham gia thành công: chuyển sang trang khảo sát, lọc theo sự kiện
                            const targetBase = baseUrl || '';
                            const target = targetBase + '/surveys?maSuKien=' + encodeURIComponent(eventId);
                            window.location.href = target;
                        } catch (err) {
                            console.error(err);
                            alert('Không thể tham gia sự kiện: ' + (err.message || 'Lỗi không xác định'));
                        } finally {
                            this.disabled = false;
                            this.innerHTML = originalHtml;
                        }
                    });
                });
            }

            function attachViewHandlers() {
                const buttons = document.querySelectorAll('.event-view-btn');
                buttons.forEach(btn => {
                    if (btn._boundViewHandler) return;
                    btn._boundViewHandler = true;

                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const eventId = this.getAttribute('data-event-id');
                        if (!eventId) return;
                        const targetBase = baseUrl || '';
                        const target = targetBase + '/surveys?maSuKien=' + encodeURIComponent(eventId);
                        window.location.href = target;
                    });
                });
            }

            loadEvents();
        });
    </script>

    <!-- Script: Lucky Wheel & điểm / lượt quay -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const spinBtn = document.getElementById('quay-ngay-btn');
            const pointsDisplay = document.getElementById('user-points-display');
            const remainingSpinsDisplay = document.getElementById('remaining-spins');

            let user = null;
            try {
                const raw = localStorage.getItem('app.user');
                user = raw ? JSON.parse(raw) : null;
            } catch (e) {
                console.error('Lỗi đọc user từ localStorage', e);
            }

            function fetchPoints() {
                if (user && user.id && pointsDisplay) {
                    fetch(apiUrl(`/api/users/points?userId=${user.id}`))
                        .then(res => res.json())
                        .then(data => {
                            if (!data.error && data.data && data.data.balance !== undefined) {
                                pointsDisplay.textContent = data.data.balance;
                            }
                        })
                        .catch(err => console.error(err));
                }
            }

            function fetchRemainingSpins() {
                if (user && user.id && remainingSpinsDisplay) {
                    fetch(apiUrl(`/api/users/points?userId=${user.id}`))
                        .then(res => res.json())
                        .then(data => {
                            if (!data.error && data.data && data.data.lucky_wheel_spins !== undefined) {
                                const spins = data.data.lucky_wheel_spins;
                                remainingSpinsDisplay.textContent = spins;

                                if (spinBtn) {
                                    if (spins <= 0) {
                                        spinBtn.disabled = true;
                                        spinBtn.textContent = 'Hết lượt quay';
                                    } else {
                                        spinBtn.disabled = false;
                                        spinBtn.textContent = 'Quay Ngay';
                                    }
                                }
                            }
                        })
                        .catch(err => console.error(err));
                }
            }

            fetchPoints();
            fetchRemainingSpins();

            if (spinBtn) {
                spinBtn.addEventListener('click', function () {
                    if (!user || !user.id) {
                        alert('Bạn cần đăng nhập để quay thưởng!');
                        window.location.href = '<?= rtrim($baseUrl ?? '', '/') ?>/login';
                        return;
                    }

                    const modalEl = document.getElementById('probabilitiesModal');
                    if (modalEl && window.bootstrap) {
                        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    }
                });
            }

            const confirmSpinBtn = document.getElementById('confirmSpin');
            if (confirmSpinBtn) {
                confirmSpinBtn.addEventListener('click', function () {
                    if (!user || !user.id) {
                        alert('Bạn cần đăng nhập để quay thưởng!');
                        return;
                    }

                    confirmSpinBtn.disabled = true;
                    confirmSpinBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Đang quay...';

                    const probModalEl = document.getElementById('probabilitiesModal');
                    if (probModalEl && window.bootstrap) {
                        const probModal = window.bootstrap.Modal.getInstance(probModalEl);
                        probModal && probModal.hide();
                    }

                    fetch(apiUrl('/api/events/lucky-wheel/spin'), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ userId: user.id })
                    })
                        .then(response => response.json())
                        .then(data => {
                            const resultMessageEl = document.getElementById('resultMessage');
                            if (!resultMessageEl) return;

                            if (data.error) {
                                resultMessageEl.innerHTML =
                                    '<i class="fas fa-exclamation-circle text-danger me-2"></i>' +
                                    (data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                            } else {
                                const pointsAdded = data.data?.points_added || 0;
                                resultMessageEl.innerHTML =
                                    '<i class="fas fa-gift text-success me-2" style="font-size: 2rem;"></i><br>' +
                                    '<strong style="font-size: 1.5rem; color: var(--primary-color);">' +
                                    pointsAdded + ' điểm!</strong><br>' +
                                    '<span class="text-muted">Số dư mới: ' +
                                    (data.data?.new_balance ?? '---') + ' điểm</span>';

                                if (data.data && data.data.new_balance !== undefined && pointsDisplay) {
                                    pointsDisplay.textContent = data.data.new_balance;
                                } else {
                                    fetchPoints();
                                }

                                if (data.data && data.data.spins_remaining !== undefined) {
                                    const spins = data.data.spins_remaining;
                                    remainingSpinsDisplay.textContent = spins;

                                    if (spinBtn) {
                                        if (spins <= 0) {
                                            spinBtn.disabled = true;
                                            spinBtn.textContent = 'Hết lượt quay';
                                        } else {
                                            spinBtn.disabled = false;
                                            spinBtn.textContent = 'Quay Ngay';
                                        }
                                    }
                                } else {
                                    fetchRemainingSpins();
                                }
                            }

                            const resultModalEl = document.getElementById('resultModal');
                            if (resultModalEl && window.bootstrap) {
                                const resultModal = window.bootstrap.Modal.getOrCreateInstance(resultModalEl);
                                resultModal.show();
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            const resultMessageEl = document.getElementById('resultMessage');
                            if (resultMessageEl) {
                                resultMessageEl.innerHTML =
                                    '<i class="fas fa-times-circle text-danger me-2"></i>' +
                                    'Có lỗi xảy ra, vui lòng thử lại sau.';
                            }
                            const resultModalEl = document.getElementById('resultModal');
                            if (resultModalEl && window.bootstrap) {
                                const resultModal = window.bootstrap.Modal.getOrCreateInstance(resultModalEl);
                                resultModal.show();
                            }
                        })
                        .finally(() => {
                            confirmSpinBtn.disabled = false;
                            confirmSpinBtn.innerHTML = 'Xác nhận quay';
                        });
                });
            }
        });
    </script>

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
            color: #fff;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>
</main>