<?php
/**
 * Admin Sidebar Component
 */
$currentPath = $currentPath ?? ($_SERVER['REQUEST_URI'] ?? '/');
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-tachometer-alt me-2"></i>Admin Panel</h4>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/dashboard') ? 'active' : '' ?>"
                    href="/admin/dashboard">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/surveys') ? 'active' : '' ?>"
                    href="/admin/surveys">
                    <i class="fas fa-poll me-2"></i>Quản lý Khảo sát
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/questions') ? 'active' : '' ?>"
                    href="/admin/questions">
                    <i class="fas fa-question-circle me-2"></i>Quản lý Câu hỏi
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/users') ? 'active' : '' ?>"
                    href="/admin/users">
                    <i class="fas fa-users me-2"></i>Quản lý User
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/events') ? 'active' : '' ?>"
                    href="/admin/events">
                    <i class="fas fa-calendar me-2"></i>Quản lý Sự kiện
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/reports') ? 'active' : '' ?>"
                    href="/admin/reports">
                    <i class="fas fa-chart-bar me-2"></i>Báo cáo & Thống kê
                </a>
            </li>

            <li class="nav-item mt-3">
                <a class="nav-link <?= str_contains($currentPath, '/admin/settings') ? 'active' : '' ?>"
                    href="/admin/settings">
                    <i class="fas fa-cog me-2"></i>Cài đặt
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/">
                    <i class="fas fa-arrow-left me-2"></i>Về trang chủ
                </a>
            </li>
        </ul>
    </nav>
</aside>