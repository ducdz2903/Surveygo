<?php
/**
 * Admin Header Component
 */
?>
<header class="admin-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="admin-brand">
                <h5 class="mb-0">Surveygo Admin</h5>
            </div>

            <div class="admin-user-menu">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <span id="admin-username">Admin</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="/admin/settings"><i class="fas fa-cog me-2"></i>Cài đặt</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#" id="admin-logout"><i
                                    class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
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
                if (usernameEl && user.name) {
                    usernameEl.textContent = user.name;
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