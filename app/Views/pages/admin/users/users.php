<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Người dùng</h4>
            <p class="text-muted mb-0">Danh sách tài khoản và phân quyền hệ thống</p>
        </div>
        <button class="btn btn-primary" onclick="alert('Chức năng tạo mới đang phát triển')">
            <i class="fas fa-plus me-2"></i>Tạo user mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Tên hoặc email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Vai trò</label>
                    <select class="form-select" id="filter-role">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin">Quản trị viên (Admin)</option>
                        <option value="moderator">Kiểm duyệt viên</option>
                        <option value="user">Người dùng (User)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái</label>
                    <select class="form-select" id="filter-status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Vô hiệu hóa</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
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
                            <th class="ps-4" style="width: 80px;">Mã</th>
                            <th style="min-width: 200px;">Thông tin User</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th style="width: 100px;">Giới tính</th>
                            <th style="width: 140px;">Vai trò</th>
                            <th style="width: 140px;">Trạng thái</th>
                            <th class="text-center" style="width: 100px;">Khảo sát</th>
                            <th class="text-center" style="width: 100px;">Phản hồi</th>
                            <th style="width: 150px;">Ngày tham gia</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <tr>
                            <td colspan="11" class="text-center py-5">
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
                    Hiển thị <span id="total-users">0</span> kết quả
                </div>
                <div id="users-pagination"></div>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/admin-helpers.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        const itemsPerPage = 10;
        const totalUsersEl = document.getElementById('total-users');
        const searchInput = document.getElementById('filter-search');
        const roleFilter = document.getElementById('filter-role');
        const statusFilter = document.getElementById('filter-status');

        const Helpers = window.AdminHelpers || {
            getAvatarColor: () => '#6c757d',
            getRoleBadge: () => 'badge bg-secondary',
            getRoleText: (r) => r,
            getStatusBadge: () => 'badge bg-secondary',
            getStatusText: (s) => s
        };

        const GenderLabel = {
            male: 'Nam',
            female: 'Nữ',
            other: 'Khác'
        };

        function getInitials(name) {
            if (!name) return 'U';
            const parts = name.trim().split(/\s+/).filter(Boolean);
            if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        // tạo giao diện bảng
        function renderTable(users) {
            const tbody = document.getElementById('users-table-body');
            
            if (!users || users.length === 0) {
                tbody.innerHTML = `<tr><td colspan="11" class="text-center py-5 text-muted"><i class="fas fa-user-slash mb-2 display-6"></i><br>Không tìm thấy người dùng nào.</td></tr>`;
                return;
            }

            tbody.innerHTML = users.map(user => `
                <tr class="slide-in">
                    <td class="ps-4"><span class="font-monospace text-dark">#${user.code || user.id}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-avatar shadow-sm" style="width: 36px; height: 36px; background: ${Helpers.getAvatarColor(user.name)}; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; border-radius:50%; font-size: 0.85rem;">
                                ${getInitials(user.name)}
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">${user.name}</span>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>${user.phone || user.soDienThoai || user.sdt || '-'}</td>
                    <td class="text-center">${GenderLabel[user.gender] || '-'}</td>
                    <td>
                        <span>
                            ${Helpers.getRoleText(user.role)}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${Helpers.getStatusBadge(user.status || 'active')}">
                            ${Helpers.getStatusText(user.status || 'active')}
                        </span>
                    </td>
                    <td class="text-center"><span class="text-dark">${user.surveys || 0}</span></td>
                    <td class="text-center"><span class="text-dark">${user.responses || 0}</span></td>
                    <td><small class="text-muted">${user.joinedAt ? new Date(user.joinedAt).toLocaleDateString('vi-VN') : '-'}</small></td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" title="Xem" onclick="alert('Xem User ${user.id}')"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-light text-success" title="Sửa" onclick="alert('Sửa User ${user.id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger" title="Khóa" onclick="toggleStatus(${user.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // tạo giao diện phân trang
        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('users-pagination');
            if (!container) return;
            
            const totalPages = Math.ceil(total / pageSize) || 1;
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';
            
            html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page - 1})"><i class="fas fa-chevron-left"></i></button>
                     </li>`;

            const start = Math.max(1, page - 1);
            const end = Math.min(totalPages, page + 1);

            if (start > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
                if (start > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for (let i = start; i <= end; i++) {
                html += `<li class="page-item ${i === page ? 'active' : ''}">
                            <button class="page-link" onclick="changePage(${i})">${i}</button>
                         </li>`;
            }

            if (end < totalPages) {
                if (end < totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                html += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;
            }

            html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page + 1})"><i class="fas fa-chevron-right"></i></button>
                     </li>`;

            html += '</ul>';
            container.innerHTML = html;
        }

        // gọi api phân trang 
        async function loadUsers() {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = `<tr><td colspan="11" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;

            const params = new URLSearchParams();
            params.set('page', currentPage);
            params.set('limit', itemsPerPage);
            
            if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
            if (roleFilter.value) params.set('role', roleFilter.value);
            if (statusFilter.value) params.set('status', statusFilter.value);

            try {
                const res = await fetch('/api/users?' + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                const json = await res.json();
                const data = Array.isArray(json.data) ? json.data : (json.data || []);
                const meta = json.meta || { total: data.length, page: currentPage, limit: itemsPerPage, totalPages: 1 };

                renderTable(data);
                renderPagination(meta.total, meta.page, meta.limit);
                if (totalUsersEl) totalUsersEl.textContent = meta.total || 0;

            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="11" class="text-center py-4 text-danger">Không thể tải dữ liệu: ${err.message}</td></tr>`;
                document.getElementById('users-pagination').innerHTML = '';
            }
        }

        // Debounce Filter
        function debounce(fn, wait = 300) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), wait);
            }
        }

        // hàm khi thay đổi trang
        window.changePage = function(page) {
            currentPage = page;
            loadUsers();
        };

        window.toggleStatus = function(id) {
            if(confirm('Bạn có chắc muốn thay đổi trạng thái user này?')) {
                console.log('Toggle status', id);
            }
        };

        const debouncedLoad = debounce(() => {
            currentPage = 1;
            loadUsers();
        });

        searchInput.addEventListener('input', debouncedLoad);
        roleFilter.addEventListener('change', () => { currentPage = 1; loadUsers(); });
        statusFilter.addEventListener('change', () => { currentPage = 1; loadUsers(); });

        window.loadUsers = loadUsers;
        window.resetFilters = function() {
            searchInput.value = '';
            roleFilter.value = '';
            statusFilter.value = '';
            currentPage = 1;
            loadUsers();
        };

        // Initial Load
        loadUsers();
    });
</script>