<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Phản hồi</h4>
            <p class="text-muted mb-0">Xem và quản lý ý kiến đóng góp từ người dùng</p>
        </div>
        <button class="btn btn-primary" onclick="openFeedbackModal()">
            <i class="fas fa-plus me-2"></i>Thêm phản hồi
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Tìm theo tên người dùng...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-uppercase text-muted">Đánh giá</label>
                    <select class="form-select" id="filter-rating">
                        <option value="">Tất cả sao</option>
                        <option value="5">5 Sao (Tuyệt vời)</option>
                        <option value="4">4 Sao (Tốt)</option>
                        <option value="3">3 Sao (Bình thường)</option>
                        <option value="2">2 Sao (Tệ)</option>
                        <option value="1">1 Sao (Rất tệ)</option>
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
                            <th class="ps-4" style="width: 80px;">Mã</th>
                            <th style="width: 200px;">Người gửi</th>
                            <th style="width: 150px;">Đánh giá</th>
                            <th>Nội dung bình luận</th>
                            <th style="width: 150px;">Ngày gửi</th>
                            <th class="text-end pe-4" style="width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="feedback-table-body">
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
                    Hiển thị <span id="total-feedbacks">0</span> kết quả
                </div>
                <div id="feedback-pagination"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm Feedback mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedback-form">
                    <input type="hidden" id="feedback-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên người dùng <span class="text-danger">*</span></label>
                        <input type="text" id="feedback-tenNguoiDung" class="form-control" required placeholder="Nhập tên người dùng">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Đánh giá</label>
                        <div class="d-flex align-items-center gap-2">
                            <select id="feedback-danhGia" class="form-select w-auto" required>
                                <option value="5">5 Sao</option>
                                <option value="4">4 Sao</option>
                                <option value="3">3 Sao</option>
                                <option value="2">2 Sao</option>
                                <option value="1">1 Sao</option>
                            </select>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bình luận</label>
                        <textarea id="feedback-binhLuan" class="form-control" rows="4" placeholder="Nội dung phản hồi..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btn-save-feedback" onclick="saveFeedback()">
                    <i class="fas fa-save me-2"></i>Lưu lại
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/admin-helpers.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        const itemsPerPage = 10;
        const totalEl = document.getElementById('total-feedbacks');
        const searchInput = document.getElementById('filter-search');
        const ratingFilter = document.getElementById('filter-rating');

        // --- 1. Load Data (API) ---
        async function loadFeedbacks() {
            const tbody = document.getElementById('feedback-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;

            const params = new URLSearchParams({
                page: currentPage,
                limit: itemsPerPage
            });

            if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
            if (ratingFilter.value) params.set('rating', ratingFilter.value);

            try {
                const res = await fetch(`/api/feedbacks?${params.toString()}`);
                if (!res.ok) throw new Error(`HTTP Error ${res.status}`);
                
                const json = await res.json();
                const data = Array.isArray(json.data) ? json.data : (json.data || []);
                const meta = json.meta || { total: data.length, page: currentPage, limit: itemsPerPage, totalPages: 1 };

                renderTable(data);
                renderPagination(meta.total, meta.page, meta.limit);
                if (totalEl) totalEl.textContent = meta.total || 0;

            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Không thể tải dữ liệu: ${err.message}</td></tr>`;
            }
        }

        // --- 2. Render Table ---
        function renderTable(feedbacks) {
            const tbody = document.getElementById('feedback-table-body');
            if (!feedbacks || feedbacks.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-comment-dots mb-2 display-6"></i><br>
                            Không tìm thấy phản hồi nào
                        </td>
                    </tr>`;
                return;
            }

            // Helper tạo sao (Stars)
            const renderStars = (rating) => {
                let html = '<div class="text-warning" style="font-size: 0.85rem;">';
                for (let i = 1; i <= 5; i++) {
                    if (i <= rating) html += '<i class="fas fa-star"></i>';
                    else html += '<i class="far fa-star text-muted opacity-25"></i>';
                }
                html += '</div>';
                return html;
            };
            
            // Helper ngày tháng (Fallback nếu chưa có AdminHelpers)
            const formatDate = (d) => {
                if (window.AdminHelpers && window.AdminHelpers.formatDate) return window.AdminHelpers.formatDate(d);
                return new Date(d).toLocaleDateString('vi-VN');
            }

            tbody.innerHTML = feedbacks.map(f => `
                <tr class="slide-in">
                    <td class="ps-4"><span class="font-monospace text-dark">#${f.ma || f.id}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" 
                                 style="width:32px; height:32px; font-size:0.8rem; background: ${window.AdminHelpers ? AdminHelpers.getAvatarColor(f.tenNguoiDung || f.username || '') : '#6c757d'}">
                                ${(f.tenNguoiDung || f.username || 'Ẩn danh').split(' ').map(s=>s[0]).slice(0,2).join('').toUpperCase()}
                            </div>
                            <div class="d-flex flex-column" style="line-height:1.1;">
                                <span class="fw-bold small">${f.tenNguoiDung || f.username || 'Ẩn danh'}</span>
                            </div>
                        </div>
                    </td>
                    <td>${renderStars(f.danhGia || f.rating || 0)}</td>
                    <td>
                        <div class="text-muted text-truncate" style="max-width: 350px;" title="${f.binhLuan || ''}">
                            ${f.binhLuan || '<span class="fst-italic text-light-emphasis">Không có nội dung</span>'}
                        </div>
                    </td>
                    <td><small class="text-muted">${formatDate(f.created_at || f.ngayGui)}</small></td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" onclick="editFeedback(${f.id})" title="Sửa"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger" onclick="deleteFeedback(${f.id})" title="Xóa"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // --- 3. Pagination ---
        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('feedback-pagination');
            const totalPages = Math.ceil(total / pageSize) || 1;
            
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';
            // Nút Prev
            html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page - 1})"><i class="fas fa-chevron-left"></i></button>
                     </li>`;
            
            // Số trang (Rút gọn)
            const start = Math.max(1, page - 1);
            const end = Math.min(totalPages, page + 1);
            
            if(start > 1) html += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
            if(start > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;

            for (let i = start; i <= end; i++) {
                html += `<li class="page-item ${i === page ? 'active' : ''}">
                            <button class="page-link" onclick="changePage(${i})">${i}</button>
                         </li>`;
            }

            if(end < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            if(end < totalPages) html += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;

            // Nút Next
            html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page + 1})"><i class="fas fa-chevron-right"></i></button>
                     </li>`;
            
            html += '</ul>';
            container.innerHTML = html;
        }

        // --- 4. Actions (CRUD) ---
        
        window.changePage = (p) => { currentPage = p; loadFeedbacks(); };

        window.openFeedbackModal = () => {
            document.getElementById('feedback-form').reset();
            document.getElementById('feedback-id').value = '';
            document.getElementById('modalTitle').textContent = 'Thêm Feedback mới';
            new bootstrap.Modal(document.getElementById('feedbackModal')).show();
        };

        window.editFeedback = async (id) => {
            try {
                // Gọi API lấy chi tiết (hoặc lấy từ data table hiện tại nếu đã lưu biến)
                const res = await fetch(`/api/feedbacks/${id}`);
                const json = await res.json();
                const data = json.data || json; // Tuỳ format response

                document.getElementById('feedback-id').value = data.id;
                document.getElementById('feedback-tenNguoiDung').value = data.tenNguoiDung || data.username;
                document.getElementById('feedback-danhGia').value = data.danhGia || data.rating;
                document.getElementById('feedback-binhLuan').value = data.binhLuan || data.comment;
                
                document.getElementById('modalTitle').textContent = 'Cập nhật Feedback #' + id;
                new bootstrap.Modal(document.getElementById('feedbackModal')).show();
            } catch (e) {
                alert('Lỗi lấy dữ liệu: ' + e.message);
            }
        };

        window.saveFeedback = async () => {
            const id = document.getElementById('feedback-id').value;
            const payload = {
                tenNguoiDung: document.getElementById('feedback-tenNguoiDung').value,
                danhGia: document.getElementById('feedback-danhGia').value,
                binhLuan: document.getElementById('feedback-binhLuan').value
            };

            const url = id ? `/api/feedbacks/${id}` : '/api/feedbacks';
            const method = id ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                if(res.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('feedbackModal')).hide();
                    loadFeedbacks(); // Reload table
                    alert('Lưu thành công!');
                } else {
                    alert('Có lỗi xảy ra!');
                }
            } catch (e) {
                console.error(e);
            }
        };

        window.deleteFeedback = async (id) => {
            if(confirm('Bạn có chắc muốn xóa feedback này?')) {
                try {
                    const res = await fetch(`/api/feedbacks/${id}`, { method: 'DELETE' });
                    if(res.ok) {
                        loadFeedbacks();
                    } else {
                        alert('Không thể xóa!');
                    }
                } catch(e) { console.error(e); }
            }
        };

        function debounce(fn, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn.apply(this, args), delay);
            }
        }

        searchInput.addEventListener('input', debounce(() => { currentPage = 1; loadFeedbacks(); }, 400));
        ratingFilter.addEventListener('change', () => { currentPage = 1; loadFeedbacks(); });

        window.loadFeedbacks = loadFeedbacks;
        window.resetFilters = function() {
            searchInput.value = '';
            ratingFilter.value = '';
            currentPage = 1;
            loadFeedbacks();
        };

        // Init
        loadFeedbacks();
    });
</script>