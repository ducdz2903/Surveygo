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

            <!-- Events Controls -->
            <div class="d-flex justify-content-between align-items-center my-3">
                <div>
                    <label class="mb-0">Hiển thị
                        <select id="events-page-size" class="form-select form-select-sm d-inline-block w-auto ms-2">
                            <option value="6">6</option>
                            <option value="12" selected>12</option>
                            <option value="24">24</option>
                        </select>
                    </label>
                </div>
                <nav id="events-pagination"></nav>
            </div>

            <!-- Events Grid -->
            <div class="row g-4" id="events-container"></div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/admin-mock-data.js"></script>
    <script>
        // Pagination state
        let eventsCurrentPage = 1;
        const eventsPageSizeSelect = document.getElementById('events-page-size');

        function getFilteredEvents() {
            // Placeholder for filters/search integration
            return AdminMockData.events.slice();
        }

        function getPaginated(items, page, pageSize) {
            const total = items.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            page = Math.min(Math.max(1, page), totalPages);
            const start = (page - 1) * pageSize;
            const data = items.slice(start, start + pageSize);
            return {
                data,
                meta: {
                    total,
                    page,
                    pageSize,
                    totalPages,
                    startIndex: start,
                    endIndex: start + data.length - 1
                }
            };
        }

        function renderEvents() {
            const all = getFilteredEvents();
            const pageSize = parseInt(eventsPageSizeSelect.value, 10) || 12;
            const pag = getPaginated(all, eventsCurrentPage, pageSize);
            const container = document.getElementById('events-container');

            container.innerHTML = pag.data.map((event, idx) => `
                <div class="col-md-6 fade-in" style="animation-delay: ${(pag.meta.startIndex + idx) * 0.03}s">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge badge-light">${event.code}</span>
                                    <span class="badge ${AdminHelpers.getStatusBadge(event.status)} ms-2">
                                        ${AdminHelpers.getStatusText(event.status)}
                                    </span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Chỉnh sửa</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-trash me-2"></i>Xóa</a></li>
                                    </ul>
                                </div>
                            </div>
                            <h5 class="mb-3">${event.title}</h5>
                            <div class="mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <strong>Bắt đầu:</strong> ${AdminHelpers.formatDateTime(event.startDate)}
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar-check text-success me-2"></i>
                                <strong>Kết thúc:</strong> ${AdminHelpers.formatDateTime(event.endDate)}
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <strong>Địa điểm:</strong> ${event.location}
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-user text-info me-2"></i>
                                <strong>Người tạo:</strong> ${event.creator}
                            </div>
                            <div class="d-flex gap-3 pt-3 border-top">
                                <div class="text-center flex-fill">
                                    <div class="fw-bold text-primary">${event.participants}</div>
                                    <small class="text-muted">Người tham gia</small>
                                </div>
                                <div class="text-center flex-fill">
                                    <div class="fw-bold text-success">${event.surveys}</div>
                                    <small class="text-muted">Khảo sát</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('total-events').textContent = all.length;
            renderEventsPagination(pag.meta);
        }

        function renderEventsPagination(meta) {
            const nav = document.getElementById('events-pagination');
            if (!nav) return;

            const maxPagesToShow = 5;
            let startPage = Math.max(1, meta.page - 2);
            let endPage = Math.min(meta.totalPages, meta.page + 2);

            // Expand range when near edges
            if (meta.page <= 2) {
                endPage = Math.min(meta.totalPages, maxPagesToShow);
            }
            if (meta.page >= meta.totalPages - 1) {
                startPage = Math.max(1, meta.totalPages - (maxPagesToShow - 1));
            }

            let html = '<ul class="pagination mb-0">';

            // Prev
            html += `<li class="page-item ${meta.page === 1 ? 'disabled' : ''}"><button class="page-link" onclick="changeEventsPage(${meta.page - 1})">&laquo;</button></li>`;

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="changeEventsPage(1)">1</button></li>`;
                if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }

            for (let p = startPage; p <= endPage; p++) {
                html += `<li class="page-item ${p === meta.page ? 'active' : ''}"><button class="page-link" onclick="changeEventsPage(${p})">${p}</button></li>`;
            }

            if (endPage < meta.totalPages) {
                if (endPage < meta.totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
                html += `<li class="page-item"><button class="page-link" onclick="changeEventsPage(${meta.totalPages})">${meta.totalPages}</button></li>`;
            }

            // Next
            html += `<li class="page-item ${meta.page === meta.totalPages ? 'disabled' : ''}"><button class="page-link" onclick="changeEventsPage(${meta.page + 1})">&raquo;</button></li>`;
            html += '</ul>';

            nav.innerHTML = html;
        }

        function changeEventsPage(page) {
            const all = getFilteredEvents();
            const pageSize = parseInt(eventsPageSizeSelect.value, 10) || 12;
            const totalPages = Math.max(1, Math.ceil(all.length / pageSize));
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            eventsCurrentPage = page;
            renderEvents();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Wire up page size change
        eventsPageSizeSelect.addEventListener('change', () => {
            eventsCurrentPage = 1;
            renderEvents();
        });

        // Expose change function globally for inline onclicks
        window.changeEventsPage = changeEventsPage;

        document.getElementById('admin-user-menu')?.addEventListener('click', () => {
            if (confirm('Đăng xuất?')) window.location.href = '/login';
        });

        // Initial render
        renderEvents();
    </script>
</body>

</html>