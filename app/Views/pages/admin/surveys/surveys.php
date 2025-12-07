<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý khảo sát</h4>
            <p class="text-muted mb-0">Danh sách và quản lý các bài khảo sát hệ thống</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSurveyModal">
            <i class="fas fa-plus me-2"></i>Tạo khảo sát mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Nhập tiêu đề...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái</label>
                    <select class="form-select" id="filter-status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="approved">Đã duyệt</option>
                        <option value="pending">Chờ duyệt</option>
                        <option value="draft">Nháp</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Danh mục</label>
                    <select class="form-select" id="filter-category">
                        <option value="">Tất cả danh mục</option>
                        <option value="Thói quen">Thói quen</option>
                        <option value="Sức khỏe">Sức khỏe</option>
                        <option value="Công nghệ">Công nghệ</option>
                        <option value="Giáo dục">Giáo dục</option>
                        <option value="Dịch vụ">Dịch vụ</option>
                        <option value="QuickPoll">QuickPoll</option>
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
                            <th class="ps-4" style="width: 100px;">Mã</th>
                            <th>Tiêu đề & Tác giả</th>
                            <th style="width: 120px;">Loại</th>
                            <th style="width: 120px;">Trạng thái</th>
                            <th class="text-center" style="width: 100px;">Câu hỏi</th>
                            <th class="text-center" style="width: 100px;">Phản hồi</th>
                            <th style="width: 150px;">Ngày tạo</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="surveys-table-body">
                        <tr>
                            <td colspan="8" class="text-center py-5">
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
                    Hiển thị <span id="total-surveys">0</span> kết quả
                </div>
                <nav aria-label="Pagination" id="pagination-container"></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createSurveyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tạo khảo sát mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="create-survey-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tiêu đề khảo sát <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Ví dụ: Khảo sát thói quen đọc sách 2024..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn</label>
                        <textarea class="form-control" rows="3" placeholder="Mô tả mục đích của khảo sát này..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Loại khảo sát</label>
                            <select class="form-select" required>
                                <option value="regular">Khảo sát thường</option>
                                <option value="quickpoll">Quick Poll</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="Thói quen">Thói quen</option>
                                <option value="Sức khỏe">Sức khỏe</option>
                                <option value="Công nghệ">Công nghệ</option>
                                <option value="Giáo dục">Giáo dục</option>
                                <option value="Dịch vụ">Dịch vụ</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Điểm thưởng (Points)</label>
                            <input type="number" class="form-control" value="10" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thời lượng dự tính (phút)</label>
                            <input type="number" class="form-control" value="15" min="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select w-100" id="survey-status" required style="width:100%;">
                                <option value="draft">Nháp</option>
                                <option value="pending">Chờ duyệt</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sự kiện</label>
                            <select class="form-select w-100" id="survey-event-select" style="width:100%;" disabled>
                                <option value="">Đang tải sự kiện...</option>
                            </select>
                        </div>
                    </div>                        
                    </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="createSurvey()">
                    <i class="fas fa-save me-2"></i>Tạo ngay
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/toast-helper.js"></script>
<script src="/public/assets/js/modal-helper.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPage = 1;
        const itemsPerPage = 10;
        const totalSurveysEl = document.getElementById('total-surveys');
        
        const AdminHelpers = window.AdminHelpers || {
            getStatusBadge: (status) => {
                const map = { 'approved': 'success', 'pending': 'warning', 'draft': 'secondary', 'rejected': 'danger' };
                return 'badge bg-' + (map[status] || 'secondary');
            },
            getStatusText: (status) => {
                const map = { 'approved': 'Đã duyệt', 'pending': 'Chờ duyệt', 'draft': 'Nháp', 'rejected': 'Từ chối' };
                return map[status] || status;
            },
            formatDate: (dateString) => {
                if(!dateString) return '-';
                return new Date(dateString).toLocaleDateString('vi-VN');
            }
        };
        const toast = typeof window.showToast === 'function'
            ? window.showToast
            : function(_, text) { try { alert(text); } catch(e) { console.log(text); } };

        // gọi api phân trang
        async function loadSurveys(page = 1) {
            currentPage = page; 
            
            const status = document.getElementById('filter-status').value;
            const category = document.getElementById('filter-category').value;
            const searchInput = document.getElementById('filter-search');
            const search = (searchInput ? searchInput.value : '').trim();

            const params = new URLSearchParams({
                page: page,
                limit: itemsPerPage,
                ...(search && { search }),
                ...(status && { trangThai: status }),
                ...(category && { danhMuc: category })
            });

            const tbody = document.getElementById('surveys-table-body');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>';

            try {
                const res = await fetch(`/api/surveys?${params.toString()}`);
                const json = await res.json();
                
                // Hỗ trợ cả cấu trúc {data: [...]} hoặc [...]
                const data = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
                const meta = json.meta || { total: data.length, page: page, totalPages: 1 };

                if (data.length > 0) {
                    renderSurveysTable(data);
                    renderPagination(meta);
                    if (totalSurveysEl) totalSurveysEl.textContent = meta.total;
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-folder-open mb-2 display-6"></i><br>Không tìm thấy dữ liệu.</td></tr>';
                    document.getElementById('pagination-container').innerHTML = '';
                    if (totalSurveysEl) totalSurveysEl.textContent = '0';
                }
            } catch (err) {
                console.error('Lỗi:', err);
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-danger">Lỗi khi tải dữ liệu: ' + err.message + '</td></tr>';
            }
        }

        // tạo giao diện table
        function renderSurveysTable(surveys) {
            const tbody = document.getElementById('surveys-table-body');
            tbody.innerHTML = surveys.map(s => `
                <tr class="slide-in align-middle">
                    <td class="ps-4">
                        <span class="font-monospace text-dark" style="font-size: 0.9rem;">
                            #${s.maKhaoSat || s.ma_khao_sat || s.id}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-primary">${s.tieuDe || s.tieu_de || 'Không tiêu đề'}</div>
                        <div class="small text-muted"><i class="fas fa-user-circle me-1"></i> ${s.maNguoiTao || 'Ẩn danh'}</div>
                    </td>
                    <td>
                        ${s.isQuickPoll || s.loaiKhaoSat === 'quickpoll' 
                            ? '<span class="badge bg-info bg-opacity-10 text-info border border-info">Quick Poll</span>' 
                            : '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Thường</span>'}
                    </td>
                    <td>
                        <span class="${AdminHelpers.getStatusBadge(s.trangThai || 'draft')}">
                            ${AdminHelpers.getStatusText(s.trangThai || 'draft')}
                        </span>
                    </td>
                    <td class="text-center"><span class="text-dark">0</span></td>
                    <td class="text-center"><span class="text-dark">0</span></td>
                    <td><small class="text-muted">${AdminHelpers.formatDate(s.created_at || s.createdAt)}</small></td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" title="Xem" onclick="window.location.href='/admin/surveys/view?id=${s.id}'"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-light text-danger" title="Xóa" onclick="deleteSurvey(${s.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // tạo giao diện phân trang
        function renderPagination(meta) {
            const container = document.getElementById('pagination-container');
            if (!meta || meta.totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';
            html += `<li class="page-item ${meta.page === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="loadSurveys(${meta.page - 1})"><i class="fas fa-chevron-left"></i></button>
                     </li>`;

            const startPage = Math.max(1, meta.page - 1);
            const endPage = Math.min(meta.totalPages, meta.page + 1);

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="loadSurveys(1)">1</button></li>`;
                if (startPage > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === meta.page ? 'active' : ''}">
                            <button class="page-link" onclick="loadSurveys(${i})">${i}</button>
                         </li>`;
            }

            if (endPage < meta.totalPages) {
                if (endPage < meta.totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                html += `<li class="page-item"><button class="page-link" onclick="loadSurveys(${meta.totalPages})">${meta.totalPages}</button></li>`;
            }

            html += `<li class="page-item ${meta.page === meta.totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="loadSurveys(${meta.page + 1})"><i class="fas fa-chevron-right"></i></button>
                     </li>`;
            
            html += '</ul>';
            container.innerHTML = html;
        }

        function debounce(fn, wait = 500) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), wait);
            }
        }

        const debouncedLoad = debounce(() => loadSurveys(1));

        // event listeners cho filters
        document.getElementById('filter-status').addEventListener('change', () => loadSurveys(1));
        document.getElementById('filter-category').addEventListener('change', () => loadSurveys(1));
        document.getElementById('filter-search')?.addEventListener('input', debouncedLoad);
        const eventSearchInput = document.getElementById('survey-event-search');
        const debouncedEventSearch = debounce((term) => loadEventsToDropdown(term), 400);
        eventSearchInput?.addEventListener('input', (e) => debouncedEventSearch(e.target.value));

        const createModalEl = document.getElementById('createSurveyModal');
        if (createModalEl) {
            createModalEl.addEventListener('show.bs.modal', () => {
                const term = eventSearchInput?.value?.trim() || '';
                loadEventsToDropdown(term);
            });
        }

        window.loadSurveys = loadSurveys;
        window.debouncedLoad = debouncedLoad;
        window.loadEventsToDropdown = loadEventsToDropdown;

        // hàm đặt lại bộ lọc
        window.resetFilters = function() {
            const fs = document.getElementById('filter-status');
            const fc = document.getElementById('filter-category');
            const fsrch = document.getElementById('filter-search');
            if (fs) fs.value = '';
            if (fc) fc.value = '';
            if (fsrch) fsrch.value = '';
            loadSurveys(1);
        };
        // Load events into dropdown for create form
        let eventsLoadToken = 0;
        async function loadEventsToDropdown(searchTerm = '') {
            const sel = document.getElementById('survey-event-select');
            const searchValue = (searchTerm || '').trim();
            if (!sel) return;
            const token = ++eventsLoadToken;
            sel.disabled = true;
            sel.innerHTML = '<option value="">Đang tải sự kiện...</option>';
            try {
                const params = new URLSearchParams({ page: 1, limit: 20 });
                if (searchValue) params.set('search', searchValue);
                const res = await fetch('/api/events?' + params.toString(), { headers: { 'Accept': 'application/json' }});
                if (token !== eventsLoadToken) return;
                if (!res.ok) throw new Error('Không thể tải danh sách sự kiện');
                const json = await res.json();
                const items = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
                if (!items.length) {
                    sel.innerHTML = '<option value="">Không tìm thấy sự kiện phù hợp</option>';
                    sel.disabled = false;
                    return;
                }
                const optionsHtml = ['<option value="">Chọn sự kiện</option>'].concat(items.map(ev => {
                    const code = ev.code || ev.maSuKien || ev.id;
                    const title = ev.title || ev.tenSuKien || ev.name || 'Không tên';
                    const id = ev.id ?? ev.maSuKien;
                    return `<option value="${id}" data-code="${code || ''}">${code ? '#' + code + ' - ' : ''}${title}</option>`;
                })).join('');
                sel.innerHTML = optionsHtml;
            } catch (err) {
                if (token !== eventsLoadToken) return;
                sel.innerHTML = '<option value="">Lỗi khi tải sự kiện</option>';
                console.error('Load events failed', err);
            } finally {
                if (token === eventsLoadToken) sel.disabled = false;
            }
        }
    
        window.createSurvey = async function() {
            console.log('Creating survey...');
            //(tieuDe, moTa, loaiKhaoSat, thoiLuongDuTinh, isQuickPoll,
            // maNguoiTao, trangThai, diemThuong, danhMuc, maSuKien, created_at, updated_at)
            const form = document.getElementById('create-survey-form');
            const tieuDe = form.querySelector('input[type="text"]').value.trim();
            const moTa = form.querySelector('textarea').value.trim();
            const loaiKhaoSat = document.getElementById('survey-type') ? document.getElementById('survey-type').value : form.querySelector('select').value;
            const thoiluong = parseInt(form.querySelector('input[name="thoiluong"]')?.value ?? form.querySelectorAll('input[type="number"]')[1].value) || 0;
            const maNguoiTao = (function() {
                const raw = localStorage.getItem('app.user');
                if (!raw) return 1;
                try {
                    const user = JSON.parse(raw);
                    return Number(user.id ?? user.maNguoiTao ?? 1) || 1;
                } catch (e) {
                    return 1;
                }
            })();
            const category = document.getElementById('survey-category') ? document.getElementById('survey-category').value : form.querySelectorAll('select')[1].value;
            const trangThai = document.getElementById('survey-status') ? document.getElementById('survey-status').value : form.querySelectorAll('select')[2].value;
            const points = parseInt(form.querySelectorAll('input[type="number"]')[0].value) || 0;
            const created_at = new Date().toISOString();
            const eventSelect = document.getElementById('survey-event-select');
            const selectedEventId = eventSelect?.value;
            const maSuKien = selectedEventId ? (Number(selectedEventId) || null) : null;
    
            const payload = {
                tieuDe,
                moTa,
                loaiKhaoSat,
                thoiLuongDuTinh: thoiluong,
                isQuickPoll : loaiKhaoSat === 'quickpoll' ? 1 : 0,
                maNguoiTao,
                trangThai,
                diemThuong: points,
                danhMuc: category,
                maSuKien,
                created_at,
            };
            if (!tieuDe) {
                toast('error', 'Vui lòng nhập tiêu đề khảo sát.');
                return;
            }
            // Gọi API tạo khảo sát mới
            const createRes = await fetch('/api/surveys', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            if (!createRes.ok) {
                const errJson = await createRes.json().catch(() => ({}));
                toast('error', 'Tạo khảo sát thất bại: ' + (errJson.message || createRes.statusText));
                return;
            }
            toast('success', 'Tạo khảo sát thành công!');
            form.reset();
            const modalEl = document.getElementById('createSurveyModal');
            if (modalEl && window.bootstrap) {
                const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                inst.hide();
            }
            // refresh list
            if (window.loadSurveys) loadSurveys(1);
        };
        window.deleteSurvey = function(id) {
            ModalHelper.confirm({
                title: 'Xóa khảo sát',
                message: 'Bạn có chắc chắn muốn xóa khảo sát #' + id + '?',
                type: 'danger',
                confirmText: 'Xóa',
                cancelText: 'Hủy',
                isDangerous: true,
                onConfirm: async function () {
                    try {
                        const res = await fetch(`/api/surveys?id=${id}`, { method: 'DELETE' });
                        const json = await res.json().catch(() => ({}));
                        if (!res.ok || json.error) {
                            throw new Error(json.message || res.statusText);
                        }
                        toast('success', 'Đã xóa khảo sát #' + id);
                        if (window.loadSurveys) loadSurveys(currentPage);
                    } catch (err) {
                        toast('error', 'Xóa thất bại: ' + err.message);
                    }
                }
            });
        };

        // Init load
        loadSurveys(1);
        loadEventsToDropdown();
    });
</script>
