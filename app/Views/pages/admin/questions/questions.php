<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Câu hỏi</h4>
            <p class="text-muted mb-0">Ngân hàng câu hỏi và quản lý liên kết khảo sát</p>
        </div>
        <button class="btn btn-primary" onclick="openQuestionModal('create')">
            <i class="fas fa-plus me-2"></i>Tạo câu hỏi mới
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0"
                            placeholder="Nhập nội dung câu hỏi...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Loại</label>
                    <select class="form-select" id="filter-type">
                        <option value="">Tất cả loại</option>
                        <option value="single_choice">Một lựa chọn (Radio)</option>
                        <option value="multiple_choice">Nhiều lựa chọn (Checkbox)</option>
                        <option value="text">Văn bản (Text)</option>
                        <option value="rating">Đánh giá (Rating)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Danh mục</label>
                    <select class="form-select" id="filter-survey">
                        <option value="">Tất cả danh mục</option>
                        <option value="-1">Không liên kết khảo sát</option>
                        <?php if (!empty($surveys)): ?>
                            <?php foreach ($surveys as $survey): ?>
                                <option value="<?= $survey->getId() ?>"><?= htmlspecialchars($survey->getTieuDe()) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
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
                            <th style="width: 50%;">Nội dung câu hỏi</th>
                            <th style="width: 150px;">Loại</th>
                            <th style="width: 150px;">Ngày tạo</th>
                            <th class="text-end pe-4" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="questions-table-body">
                        <tr>
                            <td colspan="5" class="text-center py-5">
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
                    Hiển thị <span id="total-questions">0</span> kết quả
                </div>
                <div id="questions-pagination"></div>
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
                    <input type="hidden" id="question-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea id="question-content" class="form-control" rows="2" required
                            placeholder="Nhập câu hỏi của bạn..."></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại câu hỏi</label>
                            <select class="form-select" id="modal-type-select">
                                <option value="single_choice">Một lựa chọn (Radio)</option>
                                <option value="multiple_choice">Nhiều lựa chọn (Checkbox)</option>
                                <option value="text">Văn bản (Text)</option>
                                <option value="rating">Đánh giá (Star/Scale)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gán vào khảo sát</label>
                            <select class="form-select" id="modal-survey-select">
                                <option value="">Không liên kết khảo sát</option>
                                <?php if (!empty($surveys)): ?>
                                    <?php foreach ($surveys as $survey): ?>
                                        <option value="<?= $survey->getId() ?>"><?= htmlspecialchars($survey->getTieuDe()) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="modal-is-quickpoll">
                        <label class="form-check-label" for="modal-is-quickpoll">Quick Poll</label>
                    </div>
                    <div id="options-area" class="mb-3 border rounded p-3 bg-light">
                        <label class="form-label fw-bold small text-uppercase">Các lựa chọn trả lời</label>
                        <div id="options-list">
                            <div class="input-group mb-2">
                                <span class="input-group-text">A</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">B</span>
                                <input type="text" class="form-control" placeholder="Nhập lựa chọn...">
                                <button type="button" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn-add-option">
                            <i class="fas fa-plus me-1"></i>Thêm lựa chọn
                        </button>
                    </div>
                    <div id="preview-area" class="mb-3 border rounded p-3 bg-white d-none">
                        <label class="form-label fw-bold small text-uppercase text-primary">Xem trước</label>
                        <div id="preview-content" class="p-2"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btn-save-question">
                    <i class="fas fa-save me-2"></i>Lưu câu hỏi
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/modal-helper.js"></script>

<script>
    // Toast Helper
    const showToast = (type, message) => {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1050';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now();
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';

        const html = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${icon} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const template = document.createElement('template');
        template.innerHTML = html.trim();
        const toastEl = template.content.firstChild;
        container.appendChild(toastEl);

        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        let currentPage = 1;
        const itemsPerPage = 10;
        const totalEl = document.getElementById('total-questions');
        const searchInput = document.getElementById('filter-search');
        const typeFilter = document.getElementById('filter-type');
        const surveyFilter = document.getElementById('filter-survey');

        const getTypeBadge = (type) => {
            const map = {
                'single_choice': { class: 'bg-primary', icon: 'fa-dot-circle', text: 'Radio' },
                'multiple_choice': { class: 'bg-info', icon: 'fa-check-square', text: 'Checkbox' },
                'text': { class: 'bg-secondary', icon: 'fa-align-left', text: 'Text' },
                'rating': { class: 'bg-warning text-dark', icon: 'fa-star', text: 'Rating' }
            };
            const t = map[type] || { class: 'bg-secondary', icon: 'fa-question', text: type };
            return `<span class="badge ${t.class} bg-opacity-10 text-${t.class.replace('bg-', '')} border border-${t.class.replace('bg-', '')}">
                        <i class="fas ${t.icon} me-1"></i>${t.text}
                    </span>`;
        };

        const escapeHtml = (str) => {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        // gọi api phân trang
        async function loadQuestions() {
            const tbody = document.getElementById('questions-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;

            const params = new URLSearchParams({
                page: currentPage,
                limit: itemsPerPage
            });

            if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
            if (typeFilter.value) params.set('loaiCauHoi', typeFilter.value);
            if (surveyFilter.value) params.set('maKhaoSat', surveyFilter.value);

            try {
                const res = await fetch(`/api/questions?${params.toString()}`);
                if (!res.ok) throw new Error(`HTTP Error ${res.status}`);

                const json = await res.json();
                const data = Array.isArray(json.data) ? json.data : (json.data || []);
                const metaRaw = json.meta || {};

                const total = metaRaw.total ?? metaRaw.total_items ?? data.length;
                const page = metaRaw.page ?? metaRaw.current_page ?? currentPage;
                const pageSize = metaRaw.limit ?? metaRaw.per_page ?? itemsPerPage;

                renderTable(data);
                renderPagination(total, page, pageSize);
                if (totalEl) totalEl.textContent = total || 0;

            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Không thể tải dữ liệu: ${err.message}</td></tr>`;
            }
        }

        // hàm tạo giao diện bảng
        function renderTable(questions) {
            const tbody = document.getElementById('questions-table-body');
            if (!questions || questions.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-question-circle mb-2 display-6"></i><br>
                            Không tìm thấy câu hỏi nào
                        </td>
                    </tr>`;
                return;
            }

            tbody.innerHTML = questions.map(q => `
                <tr class="slide-in">
                    <td class="ps-4"><span class="font-monospace text-dark">#${q.maCauHoi || q.id}</span></td>
                    <td>
                <div class="fw-bold text-dark text-truncate" style="max-width: 350px;" 
                             title="${escapeHtml(q.noiDungCauHoi)}">${escapeHtml(q.noiDungCauHoi)}${(q.isQuickPoll || q.quick_poll) ? ' <span class="badge bg-warning text-dark ms-2">Quick Poll</span>' : ''}</div>
                    </td>
                    <td>${getTypeBadge(q.loaiCauHoi)}</td>
                    <td><small class="text-muted">${q.created_at ? q.created_at.split(' ')[0] : ''}</small></td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-light text-primary" onclick="openQuestionModal('edit', ${q.id})" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-info" onclick="openQuestionModal('view', ${q.id})" title="Xem">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-light text-danger" onclick="deleteQuestion(${q.id})" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // hàm tạo giao diện phân trang
        function renderPagination(total, page, pageSize) {
            const container = document.getElementById('questions-pagination');
            const totalPages = Math.ceil(total / pageSize) || 1;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination pagination-sm mb-0">';

            // Nút Prev
            html += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page - 1})">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                     </li>`;

            const start = Math.max(1, page - 1);
            const end = Math.min(totalPages, page + 1);

            if (start > 1) html += `<li class="page-item"><button class="page-link" onclick="changePage(1)">1</button></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;

            for (let i = start; i <= end; i++) {
                html += `<li class="page-item ${i === page ? 'active' : ''}">
                            <button class="page-link" onclick="changePage(${i})">${i}</button>
                         </li>`;
            }

            if (end < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            if (end < totalPages) html += `<li class="page-item"><button class="page-link" onclick="changePage(${totalPages})">${totalPages}</button></li>`;

            // Nút Next
            html += `<li class="page-item ${page === totalPages ? 'disabled' : ''}">
                        <button class="page-link" onclick="changePage(${page + 1})">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                     </li>`;

            html += '</ul>';
            container.innerHTML = html;
        }

        // Helper to set form readonly state
        const setFormReadOnly = (isReadOnly) => {
            const form = document.getElementById('question-form');
            const elements = form.querySelectorAll('input, select, textarea, button:not([data-bs-dismiss])');
            
            // Disable/Enable all form elements
            elements.forEach(el => {
                el.disabled = isReadOnly;
            });
            
            const btnSave = document.getElementById('btn-save-question');
            
            // Toggle Save Button
            if (isReadOnly) {
                btnSave.style.display = 'none';
            } else {
                btnSave.style.display = 'block';
            }
        };

        const updateLivePreview = () => {
            const type = document.getElementById('modal-type-select').value;
            // Gather answers from inputs
            const answerInputs = document.querySelectorAll('#options-list input');
            const answers = Array.from(answerInputs).map(input => ({ noiDungCauTraLoi: input.value }));
            
            renderPreview(type, answers);
        };

        const addOption = (value = '') => {
            const list = document.getElementById('options-list');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 option-item';
            div.innerHTML = `
                <span class="input-group-text"><i class="fas fa-circle small"></i></span>
                <input type="text" class="form-control" placeholder="Nhập lựa chọn..." value="${escapeHtml(value)}">
                <button type="button" class="btn btn-outline-danger btn-remove-option">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(div);
            
            // Bind input event for live preview
            div.querySelector('input').addEventListener('input', updateLivePreview);
            div.querySelector('.btn-remove-option').addEventListener('click', function() {
                div.remove();
                updateLivePreview();
            });
        };

        const renderPreview = (type, answers) => {
            const container = document.getElementById('preview-content');
            container.innerHTML = '';

            if (type === 'single_choice') {
                if (!answers || answers.length === 0) {
                    container.innerHTML = '<span class="text-muted fst-italic">Chưa có lựa chọn nào</span>';
                    return;
                }
                answers.forEach(ans => {
                    const div = document.createElement('div');
                    div.className = 'form-check mb-2';
                    div.innerHTML = `
                        <input class="form-check-input" type="radio" disabled>
                        <label class="form-check-label opacity-100 text-dark">${escapeHtml(ans.noiDungCauTraLoi)}</label>
                    `;
                    container.appendChild(div);
                });
            } else if (type === 'multiple_choice') {
                 if (!answers || answers.length === 0) {
                    container.innerHTML = '<span class="text-muted fst-italic">Chưa có lựa chọn nào</span>';
                    return;
                }
                answers.forEach(ans => {
                    const div = document.createElement('div');
                    div.className = 'form-check mb-2';
                    div.innerHTML = `
                        <input class="form-check-input" type="checkbox" disabled>
                        <label class="form-check-label opacity-100 text-dark">${escapeHtml(ans.noiDungCauTraLoi)}</label>
                    `;
                    container.appendChild(div);
                });
            } else if (type === 'text') {
                container.innerHTML = `
                    <textarea class="form-control" rows="3" disabled placeholder="Người dùng sẽ nhập câu trả lời tại đây..."></textarea>
                `;
            } else if (type === 'rating') {
                container.innerHTML = `
                    <div class="d-flex gap-2">
                        <i class="far fa-star fa-2x text-warning"></i>
                        <i class="far fa-star fa-2x text-warning"></i>
                        <i class="far fa-star fa-2x text-warning"></i>
                        <i class="far fa-star fa-2x text-warning"></i>
                        <i class="far fa-star fa-2x text-warning"></i>
                    </div>
                `;
            }
        };



        // hàm đổi trang
        window.changePage = (p) => { currentPage = p; loadQuestions(); };

        // Unified Modal Function
        window.openQuestionModal = (mode, id = null) => {
            const form = document.getElementById('question-form');
            form.reset();
            document.getElementById('question-id').value = '';
            document.getElementById('options-list').innerHTML = '';
            
            const modalTitle = document.getElementById('modalTitle');
            const optionsArea = document.getElementById('options-area');

            // Reset UI State
            setFormReadOnly(false);
            optionsArea.style.display = 'block';

            if (mode === 'create') {
                modalTitle.textContent = 'Tạo câu hỏi mới';
                addOption('');
                addOption('');
                setFormReadOnly(false);
                updateLivePreview();
                new bootstrap.Modal(document.getElementById('questionModal')).show();
            } 
            else if (mode === 'edit' || mode === 'view') {
                if (mode === 'edit') {
                    modalTitle.textContent = 'Cập nhật câu hỏi #' + id;
                    setFormReadOnly(false);
                } else {
                    modalTitle.textContent = 'Chi tiết câu hỏi';
                    setFormReadOnly(true);
                }
                fetchQuestion(id);
            }
        };

        async function fetchQuestion(id) {
            try {
                const res = await fetch(`/api/questions/show?id=${id}`);
                const json = await res.json();
                if (json.error || !res.ok) {
                    showToast('error', json.message || 'Không thể tải câu hỏi');
                    return;
                }
                const data = json.data || json;
                fillQuestionForm(data);
                new bootstrap.Modal(document.getElementById('questionModal')).show();
            } catch (e) {
                console.error(e);
                showToast('error', 'Lỗi lấy dữ liệu: ' + e.message);
            }
        }

        function fillQuestionForm(data) {
             const optionsArea = document.getElementById('options-area');

            // Populate Fields
            document.getElementById('question-id').value = data.id || '';
            document.getElementById('question-content').value = data.noiDungCauHoi;
            document.getElementById('modal-type-select').value = data.loaiCauHoi;
            document.getElementById('modal-survey-select').value = data.maKhaoSat || '';
            try { document.getElementById('modal-is-quickpoll').checked = Boolean(data.isQuickPoll || data.quick_poll); } catch(e) {}

            // Toggle Options Area based on Type
            optionsArea.style.display = (data.loaiCauHoi === 'text' || data.loaiCauHoi === 'rating') ? 'none' : 'block';

            // Populate Options
            if (data.answers && data.answers.length > 0) {
                data.answers.forEach(ans => addOption(ans.noiDungCauTraLoi));
            } else {
                addOption('');
                addOption('');
            }

            updateLivePreview(); // Force Preview Update
        }

        // hàm lưu
        // Logic xử lý khi nhấn nút Lưu câu hỏi
        document.getElementById('btn-save-question').addEventListener('click', async function() {
            const form = document.getElementById('question-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const modalTitle = document.getElementById('modalTitle').textContent;
            const id = document.getElementById('question-id').value;
            
            // Handle maKhaoSat explicitly
            let maKhaoSat = document.getElementById('modal-survey-select').value;
            if (!maKhaoSat || maKhaoSat === '-1' || maKhaoSat === '') {
                maKhaoSat = null;
            }

            // Collect data - base payload
            const payload = {
                noiDungCauHoi: document.getElementById('question-content').value,
                loaiCauHoi: document.getElementById('modal-type-select').value,
                isQuickPoll: (document.getElementById('modal-is-quickpoll') ? (document.getElementById('modal-is-quickpoll').checked ? 1 : 0) : 0),
                answers: [] // Prepare answers array
            };

            // Only add maKhaoSat if it has a valid value
            if (maKhaoSat !== null) {
                payload.maKhaoSat = parseInt(maKhaoSat);
            }

            // Add id only for update operations
            if (id) {
                payload.id = id;
            }

            // Collect Answers
            const type = document.getElementById('modal-type-select').value;
            if (type === 'single_choice' || type === 'multiple_choice') {
                const answerInputs = document.querySelectorAll('#options-list input');
                answerInputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        payload.answers.push({ noiDungCauTraLoi: input.value.trim() });
                    }
                });
            }

            // Logic phân nhánh dựa trên tiêu đề form hoặc ID
            if (modalTitle === 'Tạo câu hỏi mới' || !id) {
                // Tạo câu hỏi mới
                console.log('Creating question with payload:', JSON.stringify(payload, null, 2));
                try {
                    const res = await fetch('/api/questions', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });


                    if (res.ok) {
                        const modalEl = document.getElementById('questionModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        
                        // Reset form when modal is hidden
                        modalEl.addEventListener('hidden.bs.modal', function resetForm() {
                            const form = document.getElementById('question-form');
                            form.reset();
                            document.getElementById('question-id').value = '';
                            document.getElementById('options-list').innerHTML = '';
                            // Remove this listener after it fires once
                            modalEl.removeEventListener('hidden.bs.modal', resetForm);
                        }, { once: true });
                        
                        modalInstance.hide();
                        loadQuestions();
                        showToast('success', 'Tạo câu hỏi mới thành công!');
                    } else {
                        console.error('HTTP Status:', res.status);
                        console.error('Payload sent:', payload);
                        const err = await res.json().catch(() => ({ message: 'Không thể parse response' }));
                        console.error('Error Response:', err);
                        let msg = err.message || 'Không thể tạo câu hỏi';
                        if (err.errors) {
                            msg += ': ' + Object.values(err.errors).join(', ');
                        }
                        showToast('error', 'Lỗi ' + res.status + ': ' + msg);
                    }
                } catch (e) {
                    console.error(e);
                    showToast('error', 'Lỗi kết nối khi tạo mới');
                }
            } else if (modalTitle.startsWith('Cập nhật câu hỏi #') || (id && id !== 'undefined')) {
                // Cập nhật câu hỏi
                if (!id || id === 'undefined') {
                    showToast('error', 'Lỗi: ID câu hỏi không hợp lệ');
                    return;
                }
                try {
                    // Update to match backend route: PUT /api/questions?id={id}
                    const res = await fetch(`/api/questions?id=${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (res.ok) {
                        const modalEl = document.getElementById('questionModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        
                        // Reset form when modal is hidden
                        modalEl.addEventListener('hidden.bs.modal', function resetForm() {
                            const form = document.getElementById('question-form');
                            form.reset();
                            document.getElementById('question-id').value = '';
                            document.getElementById('options-list').innerHTML = '';
                            // Remove this listener after it fires once
                            modalEl.removeEventListener('hidden.bs.modal', resetForm);
                        }, { once: true });
                        
                        modalInstance.hide();
                        loadQuestions();
                        showToast('success', 'Cập nhật thành công!');
                    } else {
                        const err = await res.json();
                        showToast('error', 'Lỗi: ' + (err.message || 'Không thể cập nhật'));
                    }
                } catch (e) {
                    console.error(e);
                    showToast('error', 'Lỗi kết nối khi cập nhật');
                }
            }
        });


        function debounce(fn, delay) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn.apply(this, args), delay);
            }
        }

        searchInput.addEventListener('input', debounce(() => {
            currentPage = 1;
            loadQuestions();
        }, 300));

        typeFilter.addEventListener('change', () => {
            currentPage = 1;
            loadQuestions();
        });

        surveyFilter.addEventListener('change', () => {
            currentPage = 1;
            loadQuestions();
        });

        document.getElementById('modal-type-select').addEventListener('change', function () {
            const type = this.value;
            const optionsArea = document.getElementById('options-area');
            optionsArea.style.display = (type === 'text' || type === 'rating') ? 'none' : 'block';
            updateLivePreview();
        });

        document.getElementById('question-content').addEventListener('input', updateLivePreview);
        
        document.getElementById('btn-add-option').addEventListener('click', () => {
            addOption();
            updateLivePreview();
        });

        window.loadQuestions = loadQuestions;
        window.resetFilters = function () {
            searchInput.value = '';
            typeFilter.value = '';
            surveyFilter.value = '';
            currentPage = 1;
            loadQuestions();
        };

        // Init
        loadQuestions();
    });
        // Ghi đè hàm xóa để dùng modal-helper.js
        window.deleteQuestion = (id) => {
            showConfirm({
                title: 'Xác nhận xóa',
                message: 'Bạn có chắc muốn xóa câu hỏi này?',
                type: 'danger',
                confirmText: 'Xóa',
                cancelText: 'Hủy',
                isDangerous: true,
                onConfirm: async () => {
                    try {
                        const res = await fetch(`/api/questions/${id}`, { method: 'DELETE' });
                        if (res.ok) {
                            loadQuestions();
                            showToast('success', 'Xóa thành công!');
                        } else {
                            showToast('error', 'Không thể xóa!');
                        }
                    } catch (e) {
                        console.error(e);
                        showToast('error', 'Lỗi kết nối khi xóa!');
                    }
                }
            });
        };
</script>
