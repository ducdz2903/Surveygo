<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Contact Messages</h4>
            <p class="text-muted mb-0">Xem và quản lý các liên hệ/khách hàng gửi về thông qua form liên hệ</p>
        </div>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0"
                            placeholder="Tìm theo tên, email hoặc chủ đề...">
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-light w-100 border" onclick="resetFilters()">
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
                            <th style="width: 220px;">Người gửi</th>
                            <th style="width: 200px;">Email</th>
                            <th>Chủ đề</th>
                            <th style="width: 180px;">Ngày gửi</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="contact-table-body">
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
                    Hiển thị <span id="total-contacts">0</span> kết quả
                </div>
                <div id="contact-pagination"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Chi tiết Contact Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="contact-form">
                    <input type="hidden" id="contact-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Họ tên</label>
                            <input type="text" id="contact-hoTen" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" id="contact-email" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" id="contact-soDienThoai" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mã</label>
                            <input type="text" id="contact-ma" class="form-control" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Chủ đề</label>
                            <input type="text" id="contact-chuDe" class="form-control" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Nội dung tin nhắn</label>
                            <textarea id="contact-tinNhan" class="form-control" rows="4" readonly></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Phản hồi</label>
                            <textarea id="contact-phanHoi" class="form-control" rows="4"
                                placeholder="Ghi phản hồi cho người gửi..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-danger" onclick="deleteContactFromModal()">
                    <i class="fas fa-trash me-2"></i>Xóa
                </button>
                <button type="button" class="btn btn-primary" onclick="saveContact()">
                    <i class="fas fa-save me-2"></i>Lưu phản hồi
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/admin-helpers.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentPage = 1;
        const itemsPerPage = 10;
        const totalEl = document.getElementById('total-contacts');
        const searchInput = document.getElementById('filter-search');


        const formatDate = (d) => {
            if (window.AdminHelpers && window.AdminHelpers.formatDate) 
                return window.AdminHelpers.formatDate(d);
            return new Date(d).toLocaleDateString('vi-VN');
        };

        const getAvatarColor = (name) => {
            if (window.AdminHelpers && window.AdminHelpers.getAvatarColor) 
                return window.AdminHelpers.getAvatarColor(name);
            return '#6c757d';
        };

        const getInitials = (name) => {
            if (!name) return 'NA';
            return name.split(' ').map(s => s[0]).slice(0, 2).join('').toUpperCase();
        };

        // gọi api phân trang 
        async function loadContacts() {
            const tbody = document.getElementById('contact-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;

            const params = new URLSearchParams({
                page: currentPage,
                limit: itemsPerPage
            });

            if (searchInput.value.trim()) params.set('search', searchInput.value.trim());

            try {
                const res = await fetch(`/api/contact-messages?${params.toString()}`);
                if (!res.ok) throw new Error(`HTTP Error ${res.status}`);

                const json = await res.json();
                const data = Array.isArray(json.data) ? json.data : (json.data || []);
                const meta = json.meta || { 
                    total: data.length, 
                    page: currentPage, 
                    limit: itemsPerPage, 
                    totalPages: 1 
                };

                renderTable(data);
                renderPagination(meta.total, meta.page, meta.limit);
                if (totalEl) totalEl.textContent = meta.total || 0;

            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Không thể tải dữ liệu: ${err.message}</td></tr>`;
            }
        }

        // hàm tạo giao diện bảng
        function renderTable(items) {
            const tbody = document.getElementById('contact-table-body');
            
            if (!items || items.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-envelope-open mb-2 display-6"></i><br>
                            Không tìm thấy liên hệ nào
                        </td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = items.map(it => `
                <tr class="slide-in">
                    <td class="ps-4">
                        <span class="font-monospace text-dark">#${it.ma || it.id}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" 
                                 style="width:32px; height:32px; font-size:0.8rem; background: ${getAvatarColor(it.hoTen || '')}">
                                ${getInitials(it.hoTen || 'Ẩn danh')}
                            </div>
                            <div class="d-flex flex-column" style="line-height:1.1;">
                                <span class="fw-bold small">${it.hoTen || 'Ẩn danh'}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width:180px;" title="${it.email || ''}">
                            ${it.email || '<span class="text-muted fst-italic">Chưa có</span>'}
                        </div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width:200px;" title="${it.chuDe || ''}">
                            ${it.chuDe || '<span class="text-muted fst-italic">Không có chủ đề</span>'}
                        </div>
                    </td>
                    <td>
                        <small class="text-muted">${formatDate(it.created_at || it.updated_at)}</small>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" 
                                    onclick="openContact(${it.id})" 
                                    title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-danger" 
                                    onclick="deleteContact(${it.id})" 
                                    title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // hàm tạo giao diện phân trang
        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('contact-pagination');
            const totalPages = Math.ceil(total / pageSize) || 1;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';

            html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page - 1})">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                     </li>`;

            const start = Math.max(1, page - 1);
            const end = Math.min(totalPages, page + 1);

            if (start > 1) {
                html += `<li class="page-item">
                            <button class="page-link" onclick="changePage(1)">1</button>
                         </li>`;
            }
            if (start > 2) {
                html += `<li class="page-item disabled">
                            <span class="page-link">...</span>
                         </li>`;
            }

            for (let i = start; i <= end; i++) {
                html += `<li class="page-item ${i === page ? 'active' : ''}">
                            <button class="page-link" onclick="changePage(${i})">${i}</button>
                         </li>`;
            }

            if (end < totalPages - 1) {
                html += `<li class="page-item disabled">
                            <span class="page-link">...</span>
                         </li>`;
            }
            if (end < totalPages) {
                html += `<li class="page-item">
                            <button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button>
                         </li>`;
            }

            html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page + 1})">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                     </li>`;

            html += '</ul>';
            container.innerHTML = html;
        }

        // hàm thay đổi trang
        window.changePage = (p) => {
            currentPage = p;
            loadContacts();
        };

        // hàm tạo modal xem chi tiết
        window.openContact = async (id) => {
            try {
                const res = await fetch(`/api/contact-messages/${id}`);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                const json = await res.json();
                const d = json.data || json;

                document.getElementById('contact-id').value = d.id || '';
                document.getElementById('contact-hoTen').value = d.hoTen || '';
                document.getElementById('contact-email').value = d.email || '';
                document.getElementById('contact-soDienThoai').value = d.soDienThoai || '';
                document.getElementById('contact-ma').value = d.ma || '';
                document.getElementById('contact-chuDe').value = d.chuDe || '';
                document.getElementById('contact-tinNhan').value = d.tinNhan || '';
                document.getElementById('contact-phanHoi').value = d.phanHoi || '';

                new bootstrap.Modal(document.getElementById('contactModal')).show();
            } catch (err) {
                console.error(err);
                alert('Không thể tải chi tiết: ' + err.message);
            }
        };

        // hàm lưu
        window.saveContact = async () => {
            const id = document.getElementById('contact-id').value;
            const phanHoi = document.getElementById('contact-phanHoi').value.trim();
            
            if (!id) return alert('ID không xác định');

            try {
                const res = await fetch(`/api/contact-messages/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ phanHoi })
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                const json = await res.json();
                if (json.error) throw new Error(json.message || 'Lỗi server');

                bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
                loadContacts();
                alert('Lưu phản hồi thành công!');
            } catch (err) {
                console.error(err);
                alert('Không thể lưu: ' + err.message);
            }
        };

        // hàm xóa
        window.deleteContact = async (id) => {
            if (!confirm('Bạn có chắc muốn xóa liên hệ này?')) return;

            try {
                const res = await fetch(`/api/contact-messages/${id}`, { 
                    method: 'DELETE' 
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                const json = await res.json();
                if (json.error) throw new Error(json.message || 'Lỗi server');

                loadContacts();
                alert('Xóa thành công!');
            } catch (err) {
                console.error(err);
                alert('Không thể xóa: ' + err.message);
            }
        };

        // tạo modal xóa
        window.deleteContactFromModal = async () => {
            const id = document.getElementById('contact-id').value;
            if (!id) return alert('ID không xác định');

            if (!confirm('Bạn có chắc muốn xóa liên hệ này?')) return;

            try {
                const res = await fetch(`/api/contact-messages/${id}`, { 
                    method: 'DELETE' 
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                
                const json = await res.json();
                if (json.error) throw new Error(json.message || 'Lỗi server');

                bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
                loadContacts();
                alert('Xóa thành công!');
            } catch (err) {
                console.error(err);
                alert('Không thể xóa: ' + err.message);
            }
        };

        function debounce(fn, delay) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn.apply(this, args), delay);
            }
        }

        searchInput.addEventListener('input', debounce(() => {
            currentPage = 1;
            loadContacts();
        }, 300));

        window.loadContacts = loadContacts;
        window.resetFilters = function () {
            searchInput.value = '';
            currentPage = 1;
            loadContacts();
        };

        // Init
        loadContacts();
    });
</script>