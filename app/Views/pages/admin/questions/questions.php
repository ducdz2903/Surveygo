<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Câu hỏi</h4>
            <p class="text-muted mb-0">Ngân hàng câu hỏi và quản lý liên kết khảo sát</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Tạo câu hỏi mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="search-input" class="form-control border-start-0 ps-0" placeholder="Nhập nội dung câu hỏi...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Loại</label>
                    <select class="form-select" id="filter-type">
                        <option value="">Tất cả loại</option>
                        <option value="single_choice">Một lựa chọn (Radio)</option>
                        <option value="multi_choice">Nhiều lựa chọn (Checkbox)</option>
                        <option value="text">Văn bản (Text)</option>
                        <option value="rating">Đánh giá (Rating)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Danh mục</label>
                    <select class="form-select" id="filter-survey">
                        <option value="">Tất cả danh mục</option>
                        <option value="1">Khảo sát thói quen đọc</option>
                        <option value="2">Đánh giá dịch vụ</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-light w-100 border" onclick="resetFilters()">
                        <i class="fas fa-redo"></i>
                        Đặt lại bộ lọc
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
                            <th class="ps-4" style="width: 80px;">#ID</th>
                            <th style="width: 40%;">Nội dung câu hỏi</th>
                            <th style="width: 150px;">Loại</th>
                            <th>Thuộc khảo sát</th>
                            <th style="width: 150px;">Ngày tạo</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="questions-table-body">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <span id="showing-count">0</span> / <span id="total-count">0</span> câu hỏi
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="pagination">
                        </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tạo câu hỏi mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="question-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="2" required placeholder="Nhập câu hỏi của bạn..."></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại câu hỏi</label>
                            <select class="form-select" id="modal-type-select">
                                <option value="single_choice">Một lựa chọn (Radio)</option>
                                <option value="multi_choice">Nhiều lựa chọn (Checkbox)</option>
                                <option value="text">Văn bản (Text)</option>
                                <option value="rating">Đánh giá (Star/Scale)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gán vào khảo sát</label>
                            <select class="form-select">
                                <option value="">-- Chọn khảo sát --</option>
                                <option value="1">Khảo sát thói quen đọc</option>
                                <option value="2">Đánh giá dịch vụ</option>
                            </select>
                        </div>
                    </div>
                    <div id="options-area" class="mb-3 border rounded p-3 bg-light">
                        <label class="form-label fw-bold small text-uppercase">Các lựa chọn trả lời</label>
                        <div id="options-list">
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus me-1"></i>Thêm lựa chọn
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Lưu câu hỏi</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Câu hỏi</h4>
            <p class="text-muted mb-0">Ngân hàng câu hỏi và quản lý liên kết khảo sát</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i>Tạo câu hỏi mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="search-input" class="form-control border-start-0 ps-0" placeholder="Nhập nội dung câu hỏi...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Loại</label>
                    <select class="form-select" id="filter-type">
                        <option value="">Tất cả loại</option>
                        <option value="single_choice">Một lựa chọn (Radio)</option>
                        <option value="multi_choice">Nhiều lựa chọn (Checkbox)</option>
                        <option value="text">Văn bản (Text)</option>
                        <option value="rating">Đánh giá (Rating)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Danh mục</label>
                    <select class="form-select" id="filter-survey">
                        <option value="">Tất cả danh mục</option>
                        <option value="1">Khảo sát thói quen đọc</option>
                        <option value="2">Đánh giá dịch vụ</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-light w-100 border" onclick="resetFilters()">
                        <i class="fas fa-redo"></i>
                        Đặt lại bộ lọc
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
                            <th class="ps-4" style="width: 80px;">#ID</th>
                            <th style="width: 40%;">Nội dung câu hỏi</th>
                            <th style="width: 150px;">Loại</th>
                            <th>Thuộc khảo sát</th>
                            <th style="width: 150px;">Ngày tạo</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="questions-table-body">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị <span id="showing-count">0</span> / <span id="total-count">0</span> câu hỏi
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="pagination">
                        </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tạo câu hỏi mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="question-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="2" required placeholder="Nhập câu hỏi của bạn..."></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại câu hỏi</label>
                            <select class="form-select" id="modal-type-select">
                                <option value="single_choice">Một lựa chọn (Radio)</option>
                                <option value="multi_choice">Nhiều lựa chọn (Checkbox)</option>
                                <option value="text">Văn bản (Text)</option>
                                <option value="rating">Đánh giá (Star/Scale)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gán vào khảo sát</label>
                            <select class="form-select">
                                <option value="">-- Chọn khảo sát --</option>
                                <option value="1">Khảo sát thói quen đọc</option>
                                <option value="2">Đánh giá dịch vụ</option>
                            </select>
                        </div>
                    </div>
                    <div id="options-area" class="mb-3 border rounded p-3 bg-light">
                        <label class="form-label fw-bold small text-uppercase">Các lựa chọn trả lời</label>
                        <div id="options-list">
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus me-1"></i>Thêm lựa chọn
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Lưu câu hỏi</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Config for API pagination ---
        const API_BASE = '/api/questions';
        let currentPage = 1;
        let perPage = 10;

        // --- 2. HELPERS ---
        const getTypeBadge = (type) => {
            const map = {
                'single_choice': { class: 'bg-primary', icon: 'fa-dot-circle', text: 'Radio' },
                'multi_choice': { class: 'bg-info', icon: 'fa-check-square', text: 'Checkbox' },
                'text': { class: 'bg-secondary', icon: 'fa-align-left', text: 'Text' },
                'rating': { class: 'bg-warning text-dark', icon: 'fa-star', text: 'Rating' }
            };
            const t = map[type] || { class: 'bg-secondary', icon: 'fa-question', text: type };
            return `<span class="badge ${t.class} bg-opacity-10 text-${t.class.replace('bg-', '')} border border-${t.class.replace('bg-', '')}">
                        <i class="fas ${t.icon} me-1"></i>${t.text}
                    </span>`;
        };

        // --- 3. RENDER FUNCTION ---
        function renderTable(rows, meta) {
            const tbody = document.getElementById('questions-table-body');
            const totalEl = document.getElementById('total-count');
            const showingEl = document.getElementById('showing-count');

            if (!rows || rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">Không tìm thấy câu hỏi nào phù hợp.</td></tr>`;
                totalEl.textContent = meta ? meta.total : 0;
                showingEl.textContent = 0;
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            tbody.innerHTML = rows.map(q => `
                <tr class="slide-in align-middle">
                    <td class="ps-4"><span class="text-muted small">#${q.id}</span></td>
                    <td>
                        <div class="fw-bold text-dark text-truncate" style="max-width: 350px;" title="${escapeHtml(q.noiDungCauHoi)}">${escapeHtml(q.noiDungCauHoi)}</div>
                    </td>
                    <td>${getTypeBadge(q.loaiCauHoi)}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            <i class="fas fa-poll-h me-1 text-muted"></i>${escapeHtml(q.maKhaoSat ? ('#' + q.maKhaoSat) : '')}
                        </span>
                    </td>
                    <td><small class="text-muted">${q.created_at ? q.created_at.split(' ')[0] : ''}</small></td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" onclick="editQuestion(${q.id})" title="Sửa"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger" onclick="deleteQuestion(${q.id})" title="Xóa"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');

            totalEl.textContent = meta.total;
            showingEl.textContent = rows.length;

            renderPagination(meta.page, meta.total_pages);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderPagination(page, totalPages) {
            const container = document.getElementById('pagination');
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            const pages = [];
            const start = Math.max(1, page - 2);
            const end = Math.min(totalPages, page + 2);

            if (page > 1) {
                pages.push({ label: '<', page: page - 1, disabled: false });
            } else {
                pages.push({ label: '<', page: 1, disabled: true });
            }

            for (let p = start; p <= end; p++) {
                pages.push({ label: p, page: p, active: p === page });
            }

            if (page < totalPages) {
                pages.push({ label: '>', page: page + 1, disabled: false });
            } else {
                pages.push({ label: '>', page: totalPages, disabled: true });
            }

            container.innerHTML = pages.map(item => `
                <li class="page-item ${item.disabled ? 'disabled' : ''} ${item.active ? 'active' : ''}"><a class="page-link" href="#" data-page="${item.page}">${item.label}</a></li>
            `).join('');

            // attach handlers
            Array.from(container.querySelectorAll('a[data-page]')).forEach(a => {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const p = parseInt(this.getAttribute('data-page'));
                    if (!isNaN(p) && p !== currentPage) {
                        fetchQuestions(p);
                    }
                });
            });
        }

        // --- 4. FILTER LOGIC ---
        function buildQueryParams(page = 1) {
            const params = new URLSearchParams();
            params.set('page', page);
            params.set('per_page', perPage);
            const search = document.getElementById('search-input').value.trim();
            if (search) params.set('search', search);
            const type = document.getElementById('filter-type').value;
            if (type) params.set('loaiCauHoi', type);
            const survey = document.getElementById('filter-survey').value;
            if (survey) params.set('maKhaoSat', survey);
            return params.toString();
        }

        async function fetchQuestions(page = 1) {
            currentPage = page;
            const tbody = document.getElementById('questions-table-body');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>';

            try {
                const qs = buildQueryParams(page);
                const res = await fetch(API_BASE + '?' + qs);
                const json = await res.json();
                if (json.error) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">${escapeHtml(json.message || 'Lỗi server')}</td></tr>`;
                    return;
                }

                const rows = json.data || [];
                const meta = json.meta || { total: 0, page: 1, per_page: perPage, total_pages: 1 };
                renderTable(rows, meta);
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Lỗi kết nối</td></tr>`;
                console.error(err);
            }
        }

        // --- 5. EVENT LISTENERS ---
        function debounce(fn, wait = 300) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const debouncedLoad = debounce(() => { currentPage = 1; fetchQuestions(1); }, 300);

        // Attach safely and add logs for debugging
        const si = document.getElementById('search-input');
        const ft = document.getElementById('filter-type');
        const fs = document.getElementById('filter-survey');

        if (si) {
            let composing = false;
            si.addEventListener('compositionstart', () => { composing = true; });
            si.addEventListener('compositionend', (e) => { composing = false; fetchQuestions(1); });
            si.addEventListener('input', function(e) { console.log('[questions] input', e.target.value, 'composing=', composing); if (!composing) debouncedLoad(); });
            console.log('[questions] search-input listener attached');
        } else {
            console.warn('[questions] search-input element not found');
        }

        if (ft) ft.addEventListener('change', () => { currentPage = 1; fetchQuestions(1); }); else console.warn('[questions] filter-type not found');
        if (fs) fs.addEventListener('change', () => { currentPage = 1; fetchQuestions(1); }); else console.warn('[questions] filter-survey not found');
        
        // Modal Logic (Toggle Input Area based on Type)
        document.getElementById('modal-type-select').addEventListener('change', function() {
            const type = this.value;
            const optionsArea = document.getElementById('options-area');
            if (type === 'text' || type === 'rating') {
                optionsArea.style.display = 'none';
            } else {
                optionsArea.style.display = 'block';
            }
        });

        // Global functions for buttons
        window.resetFilters = function() {
            document.getElementById('search-input').value = '';
            document.getElementById('filter-type').value = '';
            document.getElementById('filter-survey').value = '';
            fetchQuestions(1);
        };

        window.openCreateModal = function() {
            const modal = new bootstrap.Modal(document.getElementById('questionModal'));
            document.getElementById('modalTitle').textContent = 'Tạo câu hỏi mới';
            document.getElementById('question-form').reset();
            document.getElementById('options-area').style.display = 'block';
            modal.show();
        };

        window.editQuestion = function(id) {
            // Load detail and open modal (placeholder)
            fetch('/api/questions?id=' + encodeURIComponent(id))
                .then(r => r.json())
                .then(json => {
                    if (json && !json.error && json.data) {
                        const modal = new bootstrap.Modal(document.getElementById('questionModal'));
                        document.getElementById('modalTitle').textContent = 'Cập nhật câu hỏi #' + id;
                        // TODO: fill form fields from json.data
                        modal.show();
                    } else {
                        alert('Không tải được dữ liệu câu hỏi.');
                    }
                }).catch(err => { console.error(err); alert('Lỗi kết nối'); });
        };

        window.deleteQuestion = function(id) {
            if(confirm('Bạn có chắc chắn muốn xóa câu hỏi #' + id + '?')) {
                fetch('/api/questions', { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) })
                    .then(r => r.json())
                    .then(json => {
                        if (!json.error) {
                            alert('Đã xóa thành công');
                            fetchQuestions(currentPage);
                        } else {
                            alert(json.message || 'Xóa thất bại');
                        }
                    }).catch(err => { console.error(err); alert('Lỗi kết nối'); });
            }
        };

        // Init Load
        fetchQuestions(1);
    });
</script>