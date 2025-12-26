<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Người dùng</h4>
            <p class="text-muted mb-0">Danh sách tài khoản và phân quyền hệ thống</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Thêm người dùng
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


    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-admin">
                    <h5 class="modal-title text-white">Thêm người dùng mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="create-user-form">
                        <div class="mb-3">
                            <label for="create-name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="create-email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="create-email" required>
                        </div>
                        <div class="mb-3">
                            <label for="create-password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="create-password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="create-phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="create-phone">
                        </div>
                        <div class="mb-3">
                            <label for="create-gender" class="form-label">Giới tính</label>
                            <select class="form-select" id="create-gender">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other" selected>Khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="create-role" class="form-label">Vai trò</label>
                            <select class="form-select" id="create-role">
                                <option value="user" selected>Người dùng (User)</option>
                                <option value="moderator">Kiểm duyệt viên (Moderator)</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="submitCreateUser()">Tạo mới</button>
                </div>
            </div>
        </div>
    </div>
    
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Chỉnh sửa thông tin User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit-user-id">
                    
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" disabled>
                        <small class="text-muted">Email không thể thay đổi</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-phone" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="edit-phone">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-gender" class="form-label">Giới tính</label>
                        <select class="form-select" id="edit-gender">
                            <option value="other">Khác</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit-role" class="form-label">Vai trò</label>
                        <select class="form-select" id="edit-role" required>
                            <option value="user">Người dùng (User)</option>
                            <option value="moderator">Kiểm duyệt viên (Moderator)</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="edit-points" class="form-label">Điểm số</label>
                            <input type="number" class="form-control" id="edit-points" min="0">
                        </div>
                        <div class="col-6">
                            <label for="edit-spins" class="form-label">Lượt quay</label>
                            <input type="number" class="form-control" id="edit-spins" min="0">
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="admin-confirm-container">
                        <label for="admin-confirm-password" class="form-label text-danger fw-bold">Xác nhận mật khẩu Admin</label>
                        <input type="password" class="form-control border-danger" id="admin-confirm-password" placeholder="Nhập mật khẩu admin của bạn để thực hiện hành động này">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="submitEditUser()">
                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle me-2"></i>Chi tiết User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="view-avatar" src="" alt="Avatar" class="rounded-circle mb-2 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    <h5 id="view-name" class="mb-0 fw-bold"></h5>
                    <p id="view-code" class="text-muted small mb-0"></p>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Email</label>
                        <div id="view-email" class="fw-medium text-break"></div>
                    </div>
                     <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Số điện thoại</label>
                        <div id="view-phone" class="fw-medium"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Giới tính</label>
                        <div id="view-gender" class="fw-medium"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Vai trò</label>
                        <div id="view-role" class="fw-medium"></div>
                    </div>
                    <div class="col-12 border-top pt-2 mt-2">
                        <label class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Ngày tham gia</label>
                        <div id="view-created-at" class="fw-medium"></div>
                    </div>
                </div>
                
                <hr class="my-3 opacity-10">
                
                <!-- Phần Điểm số -->
                <h6 class="fw-bold mb-3 small text-uppercase text-secondary"><i class="fas fa-coins me-2 text-warning"></i>Thông tin điểm & Thưởng</h6>
                <div class="row g-3">
                    <div class="col-4">
                        <div class="p-2 border rounded bg-light text-center h-100">
                            <div class="small text-muted mb-1">Điểm hiện tại</div>
                            <div id="view-points-balance" class="fw-bold text-primary fs-5">0</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded bg-light text-center h-100">
                            <div class="small text-muted mb-1">Tổng tích lũy</div>
                            <div id="view-points-total" class="fw-bold text-success fs-5">0</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded bg-light text-center h-100">
                            <div class="small text-muted mb-1">Lượt quay</div>
                             <div id="view-spins" class="fw-bold text-info fs-5">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Đóng</button>
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
                            <button class="btn btn-sm btn-light text-primary" title="Xem" onclick="openViewModal(${user.id})"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-light text-success" title="Sửa" onclick="openEditModal(${user.id})"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger" title="Xóa" onclick="deleteUser(${user.id}, '${user.name.replace(/'/g, "\\'")}')"><i class="fas fa-trash"></i></button
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

        //Xóa user
        window.deleteUser = async function(id, name) {
            if (!confirm(`Bạn có chắc chắn muốn XÓA người dùng "${name}"?\n\nHành động này KHÔNG THỂ HOÀN TÁC!`)) {
                return;
            }
            
            try {
                const res = await fetch('/api/users', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await res.json();
                
                if (data.error) {
                    showToast('error', data.message || 'Không thể xóa người dùng');
                } else {
                    showToast('success', data.message || 'Xóa người dùng thành công');
                    // Reload lại trang hiện tại
                    loadUsers();
                }
            } catch (err) {
                console.error(err);
                showToast('error', 'Lỗi kết nối: ' + err.message);
            }
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });

        // Open create user modal
        window.openCreateModal = function() {
            document.getElementById('create-user-form').reset();
            const modal = new bootstrap.Modal(document.getElementById('createUserModal'));
            modal.show();
        };

        // Submit create user form
        window.submitCreateUser = async function() {
            const name = document.getElementById('create-name').value.trim();
            const email = document.getElementById('create-email').value.trim();
            const password = document.getElementById('create-password').value;
            const phone = document.getElementById('create-phone').value.trim();
            const gender = document.getElementById('create-gender').value;
            const role = document.getElementById('create-role').value;

            // Simple validation
            if (!name || !email || !password) {
                showToast('error', 'Vui lòng điền đầy đủ các trường bắt buộc');
                return;
            }

            if (password.length < 6) {
                showToast('error', 'Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }

            try {
                const res = await fetch('/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        password: password,
                        phone: phone,
                        gender: gender,
                        role: role
                    })
                });

                const data = await res.json();

                if (data.error) {
                    showToast('error', data.message || 'Có lỗi xảy ra khi tạo người dùng');
                } else {
                    showToast('success', 'Tạo người dùng thành công');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
                    modal.hide();
                    loadUsers(); // Reload current page
                }

            } catch (err) {
                console.error(err);
                showToast('error', 'Lỗi kết nối: ' + err.message);
            }
        };

        // Mở modal edit user
        window.openEditModal = async function(userId) {
            try {
                const res = await fetch(`/api/users/show?id=${userId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                const data = await res.json();
                
                if (data.error) {
                    showToast('error', data.message || 'Không thể tải thông tin user');
                    return;
                }
                
                const user = data.data;
                const points = user.points || { balance: 0, total_earned: 0, lucky_wheel_spins: 0 };
                
                // Điền dữ liệu vào form
                document.getElementById('edit-user-id').value = user.id;
                document.getElementById('edit-name').value = user.name;
                document.getElementById('edit-email').value = user.email;
                document.getElementById('edit-phone').value = user.phone || '';
                document.getElementById('edit-gender').value = user.gender || 'other';
                document.getElementById('edit-role').value = user.role;
                
                document.getElementById('edit-points').value = points.balance;
                document.getElementById('edit-spins').value = points.lucky_wheel_spins;

                // Reset admin password confirmation
                const adminPassInput = document.getElementById('admin-confirm-password');
                const adminPassContainer = document.getElementById('admin-confirm-container');
                adminPassInput.value = '';
                adminPassContainer.classList.add('d-none');
                
                // Store initial values
                document.getElementById('edit-role').dataset.initialRole = user.role;
                document.getElementById('edit-points').dataset.initialPoints = points.balance;
                document.getElementById('edit-spins').dataset.initialSpins = points.lucky_wheel_spins;

                // Function to check if we need admin password
                function checkAdminAuthRequirement() {
                    const roleSelect = document.getElementById('edit-role');
                    const pointsInput = document.getElementById('edit-points');
                    const spinsInput = document.getElementById('edit-spins');
                    
                    const newRole = roleSelect.value;
                    const initialRole = roleSelect.dataset.initialRole;
                    
                    const newPoints = parseInt(pointsInput.value) || 0;
                    const initialPoints = parseInt(pointsInput.dataset.initialPoints) || 0;
                    
                    const newSpins = parseInt(spinsInput.value) || 0;
                    const initialSpins = parseInt(spinsInput.dataset.initialSpins) || 0;
                    
                    const isPromotingToAdmin = (newRole === 'admin' && initialRole !== 'admin');
                    const isChangingPoints = (newPoints !== initialPoints);
                    const isChangingSpins = (newSpins !== initialSpins);
                    
                    if (isPromotingToAdmin || isChangingPoints || isChangingSpins) {
                        adminPassContainer.classList.remove('d-none');
                        // Label is generic now, no need to change text dynamically
                    } else {
                        adminPassContainer.classList.add('d-none');
                    }
                }

                // Attach listeners
                document.getElementById('edit-role').onchange = checkAdminAuthRequirement;
                document.getElementById('edit-points').oninput = checkAdminAuthRequirement;
                document.getElementById('edit-spins').oninput = checkAdminAuthRequirement;
                
                // Mở modal
                const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                modal.show();
                
            } catch (err) {
                console.error(err);
                showToast('error', 'Lỗi kết nối: ' + err.message);
            }
        };

        window.openViewModal = async function(userId) {
            try {
                const res = await fetch(`/api/users/show?id=${userId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                const data = await res.json();
                
                if (data.error) {
                    showToast('error', data.message || 'Không thể tải thông tin user');
                    return;
                }
                
                const user = data.data;
                const points = user.points || { balance: 0, total_earned: 0, lucky_wheel_spins: 0 };
                
                // Fill Data
                // Use a default avatar if none exists
                const avatarUrl = user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=random&color=fff&size=128';
                document.getElementById('view-avatar').src = avatarUrl;
                
                document.getElementById('view-name').textContent = user.name;
                document.getElementById('view-code').textContent = user.code || `ID: ${user.id}`;
                document.getElementById('view-email').textContent = user.email;
                document.getElementById('view-phone').textContent = user.phone || 'Chưa cập nhật';
                document.getElementById('view-gender').textContent = GenderLabel[user.gender] || 'Khác';
                
                // Role badge
                const roleMap = { 'admin': 'Admin', 'moderator': 'Moderator', 'user': 'User' };
                const roleBadgeClass = user.role === 'admin' ? 'bg-danger' : (user.role === 'moderator' ? 'bg-warning text-dark' : 'bg-primary');
                document.getElementById('view-role').innerHTML = `<span class="badge ${roleBadgeClass}">${roleMap[user.role] || user.role}</span>`;
                
                // Created At
                document.getElementById('view-created-at').textContent = user.created_at ? new Date(user.created_at).toLocaleString('vi-VN') : 'Không có thông tin';

                // Points
                document.getElementById('view-points-balance').textContent = new Intl.NumberFormat('en-US').format(points.balance);
                document.getElementById('view-points-total').textContent = new Intl.NumberFormat('en-US').format(points.total_earned);
                document.getElementById('view-spins').textContent = points.lucky_wheel_spins;
                
                const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
                modal.show();
                
            } catch (err) {
                console.error(err);
                showToast('error', 'Lỗi tải dữ liệu: ' + err.message);
            }
        };

        // Submit form 
        window.submitEditUser = async function() {
            const userId = parseInt(document.getElementById('edit-user-id').value);
            const name = document.getElementById('edit-name').value.trim();
            const phone = document.getElementById('edit-phone').value.trim();
            const gender = document.getElementById('edit-gender').value;
            const role = document.getElementById('edit-role').value;
            const points = document.getElementById('edit-points').value;
            const spins = document.getElementById('edit-spins').value;
            const adminPassword = document.getElementById('admin-confirm-password').value;
            
            // Validation
            if (!name) {
                showToast('error', 'Tên không được để trống');
                return;
            }
            
            try {
                const res = await fetch('/api/users', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: userId,
                        name: name,
                        phone: phone,
                        gender: gender,
                        role: role,
                        points: points ? parseInt(points) : 0,
                        spins: spins ? parseInt(spins) : 0,
                        admin_password: adminPassword
                    })
                });
                
                const data = await res.json();
                
                if (data.error) {
                    showToast('error', data.message || 'Không thể cập nhật user');
                } else {
                    showToast('success', data.message || 'Cập nhật thành công');
                    
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                    modal.hide();
                    
                    // Reload table
                    loadUsers();
                }
            } catch (err) {
                console.error(err);
                showToast('error', 'Lỗi kết nối: ' + err.message);
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