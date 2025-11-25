<?php
$currentPath = $currentPath ?? ($_SERVER['REQUEST_URI'] ?? '/');
$baseUrl = $baseUrl ?? '';
$urls = $urls ?? [];
$basePath = (string) (parse_url($baseUrl, PHP_URL_PATH) ?? '');
$basePath = $basePath === '/' ? '' : rtrim($basePath, '/');

$normalize = static function (string $path) use ($basePath): string {
    if ($basePath !== '' && str_starts_with($path, $basePath)) {
        $trimmed = substr($path, strlen($basePath));
        return $trimmed === '' ? '/' : $trimmed;
    }

    return $path === '' ? '/' : $path;
};

$current = $normalize($currentPath);

$url = static function (array $urls, string $key, string $fallbackPath = '/') use ($baseUrl) {
    $given = $urls[$key] ?? null;
    if (is_string($given) && $given !== '') {
        return htmlspecialchars($given, ENT_QUOTES, 'UTF-8');
    }

    $normalizedBase = rtrim((string) $baseUrl, '/');
    $normalizedPath = '/' . ltrim((string) $fallbackPath, '/');
    $computed = $normalizedBase === '' ? $normalizedPath : ($normalizedBase . $normalizedPath);
    return htmlspecialchars($computed, ENT_QUOTES, 'UTF-8');
};
?>
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="#" id="navbar-brand">Surveygo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/surveys' ? 'active' : '' ?>"
                        href="<?= rtrim($baseUrl, '/') ?>/surveys">
                        <i class="fas fa-list me-1"></i>Khảo sát
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/quick-poll' ? 'active' : '' ?>"
                        href="<?= rtrim($baseUrl, '/') ?>/quick-poll">
                        <i class="fas fa-bolt me-1"></i>Quick Poll
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/rewards' ? 'active' : '' ?>"
                        href="<?= rtrim($baseUrl, '/') ?>/rewards">
                        <i class="fas fa-gift me-1"></i>Đổi điểm
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/events' ? 'active' : '' ?>"
                        href="<?= rtrim($baseUrl, '/') ?>/events">
                        <i class="fas fa-calendar me-1"></i>Sự kiện
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/contact' ? 'active' : '' ?>"
                        href="<?= rtrim($baseUrl, '/') ?>/contact">
                        <i class="fas fa-envelope me-1"></i>Liên hệ
                    </a>
                </li>
                <!-- User Dropdown (hidden by default) -->
                <li class="nav-item dropdown" id="nav-user" style="display: none;">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i><span id="nav-username">Tài khoản</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= $url($urls, 'home', '/home') ?>">Trang chủ</a></li>
                        <li><a class="dropdown-item" href="<?= $url($urls, 'profile', '/profile') ?>">Trang cá nhân</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $url($urls, 'daily-rewards', '/daily-rewards') ?>">Điểm
                                danh</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><button class="dropdown-item" id="btn-logout" type="button"><i
                                    class="fas fa-sign-out-alt me-1"></i>Đăng xuất</button></li>
                    </ul>
                </li>
            </ul>
            <a class="btn btn-gradient" href="<?= $url($urls, 'register', '/register') ?>" id="register-btn">Đăng ký
                ngay</a>
        </div>
    </div>
</nav>
<script>
    // Client-side navbar auth toggle using localStorage
    function updateNavbarAuth() {
        try {
            var user = null;
            try {
                var raw = localStorage.getItem('app.user');
                user = raw ? JSON.parse(raw) : null;
            } catch (e) { }

            var userDropdown = document.getElementById('nav-user');
            var registerBtn = document.getElementById('register-btn');
            var navUsername = document.getElementById('nav-username');
            var logoutBtn = document.getElementById('btn-logout');
            var navBrand = document.getElementById('navbar-brand');

            if (user && (user.name || user.email)) {
                // User is logged in
                if (userDropdown) userDropdown.style.display = 'block';
                if (registerBtn) registerBtn.style.display = 'none';
                if (navUsername) navUsername.textContent = user.name || user.email || 'Tài khoản';
                // Khi đăng nhập, logo đi tới /home
                if (navBrand) navBrand.href = window.location.origin + '/home';
                // hiển thị item quản lý nếu là admin 
                try {
                    var dropdownMenu = userDropdown ? userDropdown.querySelector('.dropdown-menu') : null;
                    if (dropdownMenu) {
                        var existingAdmin = document.getElementById('nav-admin-item');
                        if (user.role === 'admin') {
                            if (!existingAdmin) {
                                var li = document.createElement('li');
                                li.id = 'nav-admin-item';
                                li.innerHTML = `<a class="dropdown-item" href="<?= $url($urls, 'admin', '/admin') ?>">Trang quản lý</a>`;
                                var divider = dropdownMenu.querySelector('li hr.dropdown-divider');
                                if (divider && divider.parentElement) {
                                    dropdownMenu.insertBefore(li, divider.parentElement);
                                } else {
                                    dropdownMenu.appendChild(li);
                                }
                            }
                        } else {
                            if (existingAdmin) existingAdmin.remove();
                        }
                    }
                } catch (e) { /* ignore DOM errors */ }
            } else {
                // User not logged in
                if (userDropdown) userDropdown.style.display = 'none';
                if (registerBtn) registerBtn.style.display = 'block';
                // Khi chưa đăng nhập, logo đi tới landing page (/)
                if (navBrand) navBrand.href = window.location.origin + '/';
            }

            if (logoutBtn) {
                logoutBtn.addEventListener('click', function () {
                    try { localStorage.removeItem('app.user'); } catch (e) { }
                    window.location.href = window.location.origin + '/login';
                });
            }
        } catch (e) { }
    }

    // Chạy ngay khi script load (không chờ DOM)
    updateNavbarAuth();

    // Chạy lại khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateNavbarAuth);
    }

    // Chạy lại khi focus vào tab (người dùng quay lại)
    window.addEventListener('focus', updateNavbarAuth);

    // Lắng nghe thay đổi localStorage
    window.addEventListener('storage', function (e) {
        if (e.key === 'app.user' || e.key === null) {
            updateNavbarAuth();
        }
    });
</script>