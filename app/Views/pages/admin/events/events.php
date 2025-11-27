<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Sự kiện</h4>
            <p class="text-muted mb-0">Danh sách các sự kiện đang diễn ra và sắp tới</p>
        </div>
        <button class="btn btn-primary" onclick="window.location.href='/admin/events/create'">
            <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Nhập tên sự kiện...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái</label>
                    <select class="form-select" id="filter-type">
                        <option value="">Tất cả trạng thái</option>
                        <option value="upcoming">Sắp diễn ra</option>
                        <option value="ongoing">Đang diễn ra</option>
                        <option value="completed">Đã kết thúc</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-light w-100 border" id="reset-filters" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>Đặt lại bộ lọc
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card fade-in" style="animation-delay: 0.1s">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 100px;">Mã</th>
                            <th style="min-width: 250px;">Thông tin sự kiện</th>
                            <th style="width: 180px;">Thời gian</th>
                            <th style="width: 200px;">Người tạo</th>
                            <th class="text-center" style="width: 120px;">Khảo sát</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
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
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <span id="total-events">0</span> kết quả
                </div>
                <div id="events-pagination"></div>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/admin-helpers.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let eventsCurrentPage = 1;
        const itemsPerPage = 10;
        const totalEventsEl = document.getElementById('total-events');
        
        function getInitials(name) {
            if (!name) return 'EV';
            const parts = name.trim().split(/\s+/).filter(Boolean);
            if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        // hàm tạo giao diện bảng
        function renderEventsTable(events) {
            const tbody = document.getElementById('events-table-body');
            
            if (!events || events.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times mb-2 display-6"></i><br>
                            Không tìm thấy sự kiện nào
                        </td>
                    </tr>`;
                return;
            }

            const Helpers = window.AdminHelpers || {
                getStatusBadge: (s) => 'badge bg-secondary',
                getStatusText: (s) => s,
                formatDateTime: (d) => d,
                getAvatarColor: () => '#6c757d'
            };

            tbody.innerHTML = events.map(ev => `
                <tr class="slide-in">
                    <td class="ps-4">
                        <span class="font-monospace text-dark">#${ev.code || ev.id || 'N/A'}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-primary mb-1 text-truncate" style="max-width: 300px;">
                            ${ev.title}
                        </div>
                        <div class="small text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>${ev.location || 'Online'}</div>
                        <div>
                            <span class="badge ${Helpers.getStatusBadge(ev.status)}">${Helpers.getStatusText(ev.status)}</span>
                            <span class="ms-2 small text-muted"><i class="fas fa-users me-1"></i>${ev.participants || 0} tham gia</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <div class="small text-muted"><i class="fas fa-clock me-1"></i>${Helpers.formatDateTime(ev.startDate)}</div>
                            <div class="small text-muted ms-3">↓</div>
                            <div class="small text-muted"><i class="fas fa-flag-checkered me-1"></i>${Helpers.formatDateTime(ev.endDate)}</div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" 
                                 style="width:32px; height:32px; font-size:0.8rem; background:${Helpers.getAvatarColor(ev.creator)}">
                                ${getInitials(ev.creator)}
                            </div>
                            <div class="d-flex flex-column" style="line-height: 1.2;">
                                <span class="fw-bold small">${ev.creator}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="text-dark">${ev.surveys || 0}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" title="Xem chi tiết" onclick="alert('Xem chi tiết ID: ${ev.id}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-success" title="Chỉnh sửa" onclick="alert('Sửa ID: ${ev.id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-danger" title="Xóa" onclick="deleteEvent(${ev.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // hàm tạo giao diện phan trang
        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('events-pagination');
            if (!container) return;
            
            const totalPages = Math.ceil(total / pageSize) || 1;
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';
            
            // Prev
            html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page - 1})"><i class="fas fa-chevron-left"></i></button>
                     </li>`;

            const startPage = Math.max(1, page - 1);
            const endPage = Math.min(totalPages, page + 1);

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
                if (startPage > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === page ? 'active' : ''}">
                            <button class="page-link" onclick="changePage(${i})">${i}</button>
                         </li>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                html += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;
            }

            // Next
            html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page + 1})"><i class="fas fa-chevron-right"></i></button>
                     </li>`;

            html += '</ul>';
            container.innerHTML = html;
        }

        // hàm gọ api
        async function loadEvents() {
            const tbody = document.getElementById('events-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>`;

            const params = new URLSearchParams();
            params.set('page', eventsCurrentPage);
            params.set('limit', itemsPerPage);

            const searchVal = document.getElementById('filter-search')?.value.trim();
            if (searchVal) params.set('search', searchVal);

            const type = document.getElementById('filter-type')?.value;
            if (type) {
                params.set('trangThai', type);
            }
            try {
                const res = await fetch('/api/events?' + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!res.ok) throw new Error(`Lỗi server: ${res.status}`);
                
                const json = await res.json();
                
                const data = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
                const meta = json.meta || { total: data.length, page: eventsCurrentPage, limit: itemsPerPage, totalPages: 1 };

                renderEventsTable(data);
                renderPagination(meta.total || 0, meta.page || eventsCurrentPage, meta.limit || itemsPerPage);
                
                if (totalEventsEl) totalEventsEl.textContent = meta.total || 0;

            } catch (err) {
                console.error('API Error:', err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Không thể tải dữ liệu: ${err.message}</td></tr>`;
                document.getElementById('events-pagination').innerHTML = '';
            }
        }

        // hàm thay đổi trang
        window.changePage = function(page) {
            eventsCurrentPage = page;
            loadEvents();
        }
        
        // hàm xóa
        window.deleteEvent = function(id) {
            if(confirm('Bạn có chắc chắn muốn xóa sự kiện này?')) {
                console.log('Delete event', id);
            }
        }

        function debounce(fn, wait = 300) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), wait);
            }
        }

        const debouncedLoad = debounce(() => {
            eventsCurrentPage = 1;
            loadEvents();
        });

        document.getElementById('filter-type').addEventListener('change', () => {
            eventsCurrentPage = 1;
            loadEvents();
        });
        
        document.getElementById('filter-search')?.addEventListener('input', debouncedLoad);
        
        window.loadEvents = loadEvents;
        window.debouncedLoad = debouncedLoad;
        window.resetFilters = function() {
            const ft = document.getElementById('filter-type');
            const fs = document.getElementById('filter-search');
            if (ft) ft.value = '';
            if (fs) fs.value = '';
            eventsCurrentPage = 1;
            loadEvents();
        };

        // Initial Load
        loadEvents();
    });
</script>