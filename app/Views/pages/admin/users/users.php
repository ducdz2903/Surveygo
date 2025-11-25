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
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tìm theo tên / email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="filter-search" class="form-control"
                                    placeholder="Tìm theo tên hoặc email...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Vai trò</label>
                            <select class="form-select" id="filter-role">
                                <option value="">Tất cả</option>
                                <option value="admin">Quản trị viên</option>
                                <option value="moderator">Kiểm duyệt viên</option>
                                <option value="user">Người dùng</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select" id="filter-status">
                                <option value="">Tất cả</option>
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Không hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
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
                    <div class="d-flex justify-content-center mt-3" id="users-pagination"></div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/admin-helpers.js"></script>
    <script>
        // Server-driven users list via /api/users
        let currentPage = 1;
        const itemsPerPage = 10;

        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('users-pagination');
            if (!container) return;
            const totalPages = Math.ceil(total / pageSize) || 1;
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination justify-content-center">';

            // Previous
            if (page > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="changePage(${page - 1})">← Trước</button></li>`;
            }

            const startPage = Math.max(1, page - 2);
            const endPage = Math.min(totalPages, page + 2);

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
                if (startPage > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === page) {
                    html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    html += `<li class="page-item"><button class="page-link" onclick="changePage(${i})">${i}</button></li>`;
                }
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                html += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;
            }

            // Next
            if (page < totalPages) {
                html += `<li class="page-item"><button class="page-link" onclick="changePage(${page + 1})">Tiếp →</button></li>`;
            }

            html += '</ul>';
            container.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            loadUsers();
            // scroll to table top
            const table = document.querySelector('.table-responsive');
            if (table) table.scrollIntoView({ behavior: 'smooth' });
        }

        // expose changePage globally for inline onclick handlers
        window.changePage = changePage;

        async function loadUsers() {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></td></tr>`;

            const params = new URLSearchParams();
            params.set('page', currentPage);
            params.set('limit', itemsPerPage);

            const searchVal = (document.getElementById('filter-search')?.value || document.getElementById('search-input')?.value || '').trim();
            if (searchVal) params.set('search', searchVal);

            const role = document.getElementById('filter-role')?.value || '';
            if (role) params.set('role', role);

            try {
                const res = await fetch('/api/users?' + params.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();

                const data = Array.isArray(json.data) ? json.data : (json.data || []);
                const meta = json.meta || { total: data.length, page: currentPage, limit: itemsPerPage, totalPages: 1 };

                tbody.innerHTML = data.map(user => `
                    <tr class="slide-in">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar" style="width: 40px; height: 40px; background: ${AdminHelpers.getAvatarColor(user.name)};display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:600;border-radius:6px;">
                                    ${(user.name || '').split(/\s+/).map(p => p[0] || '').slice(0, 2).join('').toUpperCase()}
                                </div>
                                <div>
                                    <div class="fw-bold">${user.name}</div>
                                    <small class="text-muted">${user.joinedAt ? new Date(user.joinedAt).toLocaleDateString() : ''}</small>
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
                            <span class="badge ${AdminHelpers.getStatusBadge(user.status || 'active')}">
                                ${AdminHelpers.getStatusText(user.status || 'active')}
                            </span>
                        </td>
                        <td class="text-center">${user.surveys}</td>
                        <td class="text-center"><strong class="text-primary">${user.responses}</strong></td>
                        <td>${user.joinedAt ? new Date(user.joinedAt).toLocaleString() : ''}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-icon btn-outline-primary" title="Xem profile"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-icon btn-outline-success" title="Chỉnh sửa"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-icon btn-outline-danger" title="Vô hiệu hóa"><i class="fas fa-ban"></i></button>
                            </div>
                        </td>
                    </tr>
                `).join('');

                document.getElementById('total-users').textContent = meta.total || 0;
                renderPagination(meta.total || 0, meta.page || currentPage, meta.limit || itemsPerPage);
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Lỗi khi tải dữ liệu: ${err.message}</td></tr>`;
                document.getElementById('users-pagination').innerHTML = '';
                document.getElementById('total-users').textContent = '0';
                console.error('loadUsers error', err);
            }
        }

        function applyFilters() {
            // Server-side filters: reset to first page and re-fetch.
            // Note: only `search` and `role` are sent to the API currently; `status` UI is kept for future support.
            currentPage = 1; // reset to first page when filters change
            loadUsers();
        }

        // debounce helper for search inputs
        function debounce(fn, wait = 300) {
            let timer = null;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const debouncedApplyFilters = debounce(() => applyFilters(), 300);

        document.getElementById('filter-role').addEventListener('change', applyFilters);
        document.getElementById('filter-status').addEventListener('change', applyFilters);
        document.getElementById('filter-search')?.addEventListener('input', debouncedApplyFilters);
        document.getElementById('search-input').addEventListener('input', debouncedApplyFilters);
        document.getElementById('reset-filters').addEventListener('click', () => {
            document.getElementById('filter-role').value = '';
            document.getElementById('filter-status').value = '';
            if (document.getElementById('filter-search')) document.getElementById('filter-search').value = '';
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