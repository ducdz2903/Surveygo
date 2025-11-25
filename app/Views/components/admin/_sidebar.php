<?php
/**
 * Admin Sidebar Component
 */
$currentPath = $currentPath ?? ($_SERVER['REQUEST_URI'] ?? '/');
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h4>
            <div class="logo-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <span>Surveygo Admin</span>
        </h4>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">MENU CHÍNH</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/dashboard') ? 'active' : '' ?>"
                    href="/admin/dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/surveys') ? 'active' : '' ?>"
                    href="/admin/surveys">
                    <i class="fas fa-poll"></i>
                    <span>Khảo sát</span>
                    <span class="badge badge-primary ms-auto">567</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/questions') ? 'active' : '' ?>"
                    href="/admin/questions">
                    <i class="fas fa-question-circle"></i>
                    <span>Câu hỏi</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/users') ? 'active' : '' ?>"
                    href="/admin/users">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                    <span class="badge badge-success ms-auto">1.2K</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/events') ? 'active' : '' ?>"
                    href="/admin/events">
                    <i class="fas fa-calendar"></i>
                    <span>Sự kiện</span>
                </a>
            </li>
        </ul>

        <div class="nav-section">PHÂN TÍCH</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/reports') ? 'active' : '' ?>"
                    href="/admin/reports">
                    <i class="fas fa-chart-pie"></i>
                    <span>Báo cáo</span>
                </a>
            </li>
        </ul>

        <div class="nav-section">HỆ THỐNG</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= str_contains($currentPath, '/admin/settings') ? 'active' : '' ?>"
                    href="/admin/settings">
                    <i class="fas fa-cog"></i>
                    <span>Cài đặt</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/">
                    <i class="fas fa-arrow-left"></i>
                    <span>Về trang chủ</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>