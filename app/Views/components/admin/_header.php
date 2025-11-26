<?php
$headerTitle = $headerTitle ?? ($title ?? 'Surveygo Admin');
$headerIcon = $headerIcon ?? 'fas fa-chart-line';
$notificationCount = isset($notificationCount) ? (int)$notificationCount : 0;
?>
<header class="admin-header">
    <div class="d-flex justify-content-between align-items-center w-100">
        <h5 class="mb-0"><i class="<?= htmlspecialchars($headerIcon, ENT_QUOTES, 'UTF-8') ?> me-2"></i><?= htmlspecialchars($headerTitle, ENT_QUOTES, 'UTF-8') ?></h5>

        <div class="header-actions">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm..." id="search-input">
            </div>

            <div class="notification-icon">
                <i class="fas fa-bell"></i>
                <span class="notification-badge"><?= $notificationCount > 0 ? $notificationCount : '0' ?></span>
            </div>

            <div class="user-menu">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">AD</div>
                        <span id="admin-username">Admin</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="/admin/settings"><i class="fas fa-cog me-2"></i>Cài đặt</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" id="admin-logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const userJson = localStorage.getItem('app.user');
            if (userJson) {
                const user = JSON.parse(userJson);
                const usernameEl = document.getElementById('admin-username');
                const avatarEl = document.querySelector('.user-avatar');
                if (usernameEl && user.name) {
                    usernameEl.textContent = user.name;
                }
                if (avatarEl && user.name) {
                    avatarEl.textContent = (user.name || '').split(' ').map(s=>s[0]).slice(0,2).join('').toUpperCase();
                }
            }
        } catch (e) {
            console.error('Error loading user:', e);
        }

        const logoutBtn = document.getElementById('admin-logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function (e) {
                e.preventDefault();
                localStorage.removeItem('app.user');
                window.location.href = '/login';
            });
        }
    });
</script>