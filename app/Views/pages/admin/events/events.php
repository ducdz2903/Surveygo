<?php
/** @var string $appName */
$appName = $appName ?? 'Admin - Quản lý Events';
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
            <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Quản lý Events</h5>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm sự kiện..." id="search-input">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
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
                    <h2 class="mb-1">Quản lý Events</h2>
                    <p class="text-muted mb-0">Tổng số: <strong id="total-events">0</strong> sự kiện</p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo event mới
                </button>
            </div>

            <!-- Filters -->
            <div class="card mb-4 fade-in">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tìm theo tiêu đề</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="filter-search" class="form-control"
                                    placeholder="Tìm theo tiêu đề...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Loại</label>
                            <select class="form-select" id="filter-type">
                                <option value="">Tất cả</option>
                                <option value="upcoming">Sắp diễn ra</option>
                                <option value="ongoing">Đang diễn ra</option>
                                <option value="completed">Đã kết thúc</option>
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

            <!-- Events Table -->
            <div class="card fade-in" style="animation-delay: 0.1s">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Mã</th>
                                    <th style="width: 300px;">Tiêu đề</th>
                                    <th style="width: 160px;">Thời gian</th>
                                    <th style="width: 180px;">Người tạo</th>
                                    <th style="width: 120px;">Khảo sát</th>
                                    <th style="width: 140px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="events-table-body">
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3" id="events-pagination"></div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/admin-helpers.js"></script>
    <script>
        // events admin: server-driven pagination + search via /api/events
        let eventsCurrentPage = 1;
        const itemsPerPage = 10;

        function renderEventsTable(events) {
            const tbody = document.getElementById('events-table-body');
            if (!events || events.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Không tìm thấy sự kiện nào</td></tr>';
                return;
            }

            tbody.innerHTML = events.map(ev => `
                <tr class="slide-in">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark">${ev.code}</span>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-truncate" style="max-width:230px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${ev.title}</div>
                        <small class="text-muted d-block">${ev.location}</small>
                        <div class="mt-1">
                            <span class="badge ${AdminHelpers.getStatusBadge(ev.status)}">${AdminHelpers.getStatusText(ev.status)}</span>
                            <small class="ms-2 text-primary fw-bold">${ev.participants} tham gia</small>
                        </div>
                    </td>
                    <td>${AdminHelpers.formatDateTime(ev.startDate)}<br/><small class="text-muted">→ ${AdminHelpers.formatDateTime(ev.endDate)}</small></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width:36px;height:36px;border-radius:6px;background:${AdminHelpers.getAvatarColor(ev.creator)};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;">${getInitials(ev.creator)}</div>
                            <div>
                                <div class="fw-bold">${ev.creator}</div>
                                <small class="text-muted">${AdminHelpers.getStatusText(ev.status)}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center"><span class="text-success fw-bold">${ev.surveys}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-icon btn-outline-primary" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-icon btn-outline-success" title="Chỉnh sửa"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-icon btn-outline-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('events-pagination');
            if (!container) return;
            const totalPages = Math.ceil(total / pageSize) || 1;
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination justify-content-center">';
            if (page > 1) html += `<li class="page-item"><button class="page-link" onclick="changePage(${page - 1})">← Trước</button></li>`;

            const startPage = Math.max(1, page - 2);
            const endPage = Math.min(totalPages, page + 2);

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
                if (startPage > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === page) html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                else html += `<li class="page-item"><button class="page-link" onclick="changePage(${i})">${i}</button></li>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                html += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;
            }

            if (page < totalPages) html += `<li class="page-item"><button class="page-link" onclick="changePage(${page + 1})">Tiếp →</button></li>`;
            html += '</ul>';
            container.innerHTML = html;
        }

        function changePage(page) {
            eventsCurrentPage = page;
            loadEvents();
            const tableTop = document.querySelector('.table-responsive');
            if (tableTop) tableTop.scrollIntoView({ behavior: 'smooth' });
        }

        window.changePage = changePage;

        function applyFilters() {
            // For server-side filtering we just reset to page 1 and call loadEvents()
            eventsCurrentPage = 1;
            loadEvents();
        }

        // debounce helper
        function debounce(fn, wait = 300) {
            let timer = null;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const debouncedApplyFilters = debounce(() => applyFilters(), 300);

        document.getElementById('filter-type').addEventListener('change', applyFilters);
        document.getElementById('filter-search')?.addEventListener('input', debouncedApplyFilters);
        document.getElementById('search-input').addEventListener('input', debouncedApplyFilters);
        document.getElementById('reset-filters').addEventListener('click', () => {
            document.getElementById('filter-type').value = '';
            if (document.getElementById('filter-search')) document.getElementById('filter-search').value = '';
            document.getElementById('search-input').value = '';
            applyFilters();
        });

        document.getElementById('admin-user-menu')?.addEventListener('click', () => {
            if (confirm('Đăng xuất?')) window.location.href = '/login';
        });

        // Returns two-letter initials for a full name (e.g. "Nguyễn Văn A" -> "NA")
        function getInitials(name) {
            if (!name) return '';
            const parts = name.trim().split(/\s+/).filter(Boolean);
            if (parts.length === 1) {
                return parts[0].slice(0, 2).toUpperCase();
            }
            const first = parts[0][0] || '';
            const last = parts[parts.length - 1][0] || '';
            return (first + last).toUpperCase();
        }

        async function loadEvents() {
            const tbody = document.getElementById('events-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></td></tr>`;

            const params = new URLSearchParams();
            params.set('page', eventsCurrentPage);
            params.set('limit', itemsPerPage);

            const searchVal = (document.getElementById('filter-search')?.value || document.getElementById('search-input')?.value || '').trim();
            if (searchVal) params.set('search', searchVal);

            const type = document.getElementById('filter-type')?.value || '';
            if (type) {
                // send both possible param names in case backend expects one of them
                params.set('trangThai', type);
                params.set('status', type);
            }

            try {
                const res = await fetch('/api/events?' + params.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();

                const data = Array.isArray(json.data) ? json.data : (json.data || []);
                const meta = json.meta || { total: data.length, page: eventsCurrentPage, limit: itemsPerPage, totalPages: 1 };

                renderEventsTable(data);
                renderPagination(meta.total || 0, meta.page || eventsCurrentPage, meta.limit || itemsPerPage);
                document.getElementById('total-events').textContent = meta.total || 0;
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Lỗi khi tải dữ liệu: ${err.message}</td></tr>`;
                document.getElementById('events-pagination').innerHTML = '';
                document.getElementById('total-events').textContent = '0';
                // eslint-disable-next-line no-console
                console.error('loadEvents error', err);
            }
        }

        // initial
        loadEvents();
    </script>
</body>

</html>