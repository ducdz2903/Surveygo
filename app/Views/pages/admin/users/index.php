<?php
/** @var string $appName */
$appName = $appName ?? 'Admin - Quản lý Users';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/public/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . '/app/Views/components/admin/_sidebar.php'; ?>

    <header class="admin-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Quản lý Users</h5>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm user..." id="search-input">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </div>
                <div class="user-menu" id="admin-user-menu">
                    <div class="user-avatar">AD</div>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Quản lý Users</h2>
                    <p class="text-muted mb-0">Tổng số: <strong id="total-users">0</strong> người dùng</p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Thêm user mới
                </button>
            </div>

            <!-- Filters -->
            <div class="card mb-4 fade-in">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vai trò</label>
                            <select class="form-select" id="filter-role">
                                <option value="">Tất cả</option>
                                <option value="admin">Quản trị viên</option>
                                <option value="moderator">Kiểm duyệt viên</option>
                                <option value="user">Người dùng</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select" id="filter-status">
                                <option value="">Tất cả</option>
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Không hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-outline-secondary w-100" id="reset-filters">
                                <i class="fas fa-redo me-2"></i>Đặt lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card fade-in" style="animation-delay: 0.1s">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th style="width: 150px;">Vai trò</th>
                                    <th style="width: 120px;">Trạng thái</th>
                                    <th style="width: 100px;">Khảo sát</th>
                                    <th style="width: 100px;">Phản hồi</th>
                                    <th style="width: 150px;">Tham gia</th>
                                    <th style="width: 180px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/admin-mock-data.js"></script>
    <script>
        let filteredUsers = [...AdminMockData.users];

        function loadUsers() {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = filteredUsers.map(user => `
                <tr class="slide-in">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width: 40px; height: 40px; background: ${AdminHelpers.getAvatarColor(user.name)}">
                                ${user.avatar}
                            </div>
                            <div>
                                <div class="fw-bold">${user.name}</div>
                                <small class="text-muted">${user.lastActive}</small>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>
                        <span class="badge ${AdminHelpers.getRoleBadge(user.role)}">
                            ${AdminHelpers.getRoleText(user.role)}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${AdminHelpers.getStatusBadge(user.status)}">
                            ${AdminHelpers.getStatusText(user.status)}
                        </span>
                    </td>
                    <td class="text-center">${user.surveys}</td>
                    <td class="text-center"><strong class="text-primary">${user.responses}</strong></td>
                    <td>${AdminHelpers.formatDate(user.joinedAt)}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-icon btn-outline-primary" title="Xem profile">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-success" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-danger" title="Vô hiệu hóa">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            document.getElementById('total-users').textContent = filteredUsers.length;
        }

        function applyFilters() {
            const role = document.getElementById('filter-role').value;
            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('search-input').value.toLowerCase();

            filteredUsers = AdminMockData.users.filter(user => {
                if (role && user.role !== role) return false;
                if (status && user.status !== status) return false;
                if (search && !user.name.toLowerCase().includes(search) && !user.email.toLowerCase().includes(search)) return false;
                return true;
            });
            loadUsers();
        }

        document.getElementById('filter-role').addEventListener('change', applyFilters);
        document.getElementById('filter-status').addEventListener('change', applyFilters);
        document.getElementById('search-input').addEventListener('input', applyFilters);
        document.getElementById('reset-filters').addEventListener('click', () => {
            document.getElementById('filter-role').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('search-input').value = '';
            applyFilters();
        });

        document.getElementById('admin-user-menu')?.addEventListener('click', () => {
            if (confirm('Đăng xuất?')) window.location.href = '/login';
        });

        loadUsers();
    </script>
</body>
</html>