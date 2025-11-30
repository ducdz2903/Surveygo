<?php
/**
 * Survey detail page (admin)
 * Manage survey questions (view/add from library/edit/delete).
 */

$baseUrl = $baseUrl ?? '';
$surveyId = (int) ($surveyId ?? 0);

$__base = rtrim((string) $baseUrl, '/');
$__mk = static function (string $base, string $path): string {
    $p = '/' . ltrim($path, '/');
    return $base === '' ? $p : ($base . $p);
};
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary btn-sm" id="btn-back">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </button>
            <div>
                <p class="text-uppercase small text-muted mb-1">Chi tiết khảo sát</p>
                <h4 class="mb-0" id="survey-title">Đang tải...</h4>
                <div class="text-muted small" id="survey-meta">#<?= htmlspecialchars((string) $surveyId, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" id="btn-cancel-page">
                <i class="fas fa-times me-1"></i>Hủy
            </button>
            <button class="btn btn-success" type="button" id="btn-save-page">
                <i class="fas fa-save me-1"></i>Lưu thay đổi
            </button>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div id="survey-status" class="badge bg-secondary-subtle text-secondary border mb-2">Chưa rõ</div>
                            <p class="text-muted small mb-1">Mã khảo sát</p>
                            <div class="fw-semibold" id="survey-code">#<?= htmlspecialchars((string) $surveyId, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small">Cập nhật</div>
                            <div class="fw-semibold" id="survey-updated">-</div>
                        </div>
                    </div>
                    <p class="text-muted" id="survey-desc">Đang tải thông tin khảo sát...</p>
                    <div class="d-flex flex-wrap gap-3 small">
                        <span><i class="fas fa-list-ol me-2 text-primary"></i><span id="survey-question-count">0</span> câu hỏi</span>
                        <span><i class="fas fa-layer-group me-2 text-secondary"></i><span id="survey-category">-</span></span>
                        <span><i class="fas fa-clock me-2 text-secondary"></i><span id="survey-created">-</span></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Danh sách câu hỏi</h5>
                            <p class="text-muted small mb-0">Quản lý câu hỏi thuộc khảo sát này</p>
                        </div>
                        <button class="btn btn-primary" type="button" id="btn-open-library-2">
                            <i class="fas fa-plus me-1"></i>Thêm câu hỏi
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3" style="width: 90px;">Mã</th>
                                    <th>Câu hỏi</th>
                                    <th style="width: 150px;">Loại</th>
                                    <th style="width: 110px;" class="text-center">Thứ tự</th>
                                    <th class="text-end pe-3" style="width: 150px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="questions-body">
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit question modal -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="question-modal-title">Thêm câu hỏi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="question-form">
                    <input type="hidden" id="question-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="question-content" rows="2" placeholder="Nhập nội dung câu hỏi..." required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Loại câu hỏi</label>
                            <select class="form-select" id="question-type">
                                <option value="single_choice">Một lựa chọn (Radio)</option>
                                <option value="multiple_choice">Nhiều lựa chọn (Checkbox)</option>
                                <option value="text">Văn bản (Text)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" id="question-order" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="question-required">
                        <label class="form-check-label" for="question-required">Bắt buộc trả lời</label>
                    </div>
                    <p class="text-muted small mb-0">Popup theo mẫu trang quản lý câu hỏi. Dữ liệu lưu khi bấm "Lưu".</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btn-save-question">
                    <i class="fas fa-save me-2"></i>Lưu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Library modal: choose existing questions -->
<div class="modal fade" id="libraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn câu hỏi có sẵn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="library-search" placeholder="Tìm kiếm câu hỏi...">
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-secondary" id="btn-library-reload">
                            <i class="fas fa-rotate me-1"></i>Tải lại
                        </button>
                    </div>
                </div>
                <div class="table-responsive border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 90px;">Mã</th>
                                <th>Nội dung</th>
                                <th style="width: 120px;">Loại</th>
                                <th style="width: 120px;" class="text-end pe-3">Chọn</th>
                            </tr>
                        </thead>
                        <tbody id="library-body">
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small mt-2 mb-0">Chọn câu hỏi đã có để thêm nhanh vào khảo sát này (nội dung sẽ được sao chép).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= $__mk($__base, 'public/assets/js/toast-helper.js') ?>"></script>
<script>
    const surveyId = <?= json_encode($surveyId) ?>;
    const baseUrl = <?= json_encode($__base) ?>;
    const apiUrl = (pathWithLeadingSlash) => {
        const cleanBase = baseUrl ? baseUrl.replace(/\/+$/, '') : '';
        const cleanPath = pathWithLeadingSlash.startsWith('/') ? pathWithLeadingSlash : '/' + pathWithLeadingSlash;
        return cleanBase + cleanPath;
    };

    let questionsCache = [];
    let questionModal = null;
    let libraryModal = null;

    const modalEl = document.getElementById('questionModal');
    const libraryEl = document.getElementById('libraryModal');

    const questionIdInput = document.getElementById('question-id');
    const questionContentInput = document.getElementById('question-content');
    const questionTypeSelect = document.getElementById('question-type');
    const questionOrderInput = document.getElementById('question-order');
    const questionRequiredInput = document.getElementById('question-required');

    const libraryBody = document.getElementById('library-body');
    const librarySearch = document.getElementById('library-search');

    window.addEventListener('load', () => {
        window.showToast = typeof showToast === 'function' ? showToast : function (_, text) { alert(text); };

        if (typeof bootstrap !== 'undefined') {
            questionModal = new bootstrap.Modal(modalEl);
            libraryModal = new bootstrap.Modal(libraryEl);
        }

        document.getElementById('btn-back')?.addEventListener('click', () => window.location.href = apiUrl('/admin/surveys'));
        document.getElementById('btn-cancel-page')?.addEventListener('click', () => window.location.href = apiUrl('/admin/surveys'));
        document.getElementById('btn-save-page')?.addEventListener('click', () => showToast('success', 'Đã lưu thay đổi.'));

        document.getElementById('btn-open-library')?.addEventListener('click', () => openLibrary());
        document.getElementById('btn-open-library-2')?.addEventListener('click', () => openLibrary());
        document.getElementById('btn-save-question')?.addEventListener('click', saveQuestion);
        document.getElementById('btn-library-reload')?.addEventListener('click', () => loadLibrary());
        librarySearch?.addEventListener('input', debounce(() => loadLibrary(), 300));

        if (!surveyId) {
            showToast('error', 'Không xác định được khảo sát.');
            return;
        }

        loadSurvey();
    });

    async function loadSurvey() {
        setLoadingState();
        try {
            const res = await fetch(apiUrl(`/api/surveys/show?id=${surveyId}`));
            const json = await res.json();
            if (json.error) {
                showToast('error', json.message || 'Không thể tải khảo sát');
                return;
            }
            renderSurveyInfo(json.data);
            renderQuestions(json.data.questions || []);
        } catch (e) {
            console.error(e);
            showToast('error', 'Có lỗi xảy ra khi tải khảo sát');
        }
    }

    async function loadLibrary() {
        libraryBody.innerHTML = `<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>`;
        const search = (librarySearch?.value || '').trim();
        const params = new URLSearchParams({ per_page: 50 });
        if (search) params.set('search', search);
        try {
            const res = await fetch(apiUrl(`/api/questions?${params.toString()}`));
            const json = await res.json();
            const data = Array.isArray(json.data) ? json.data : [];
            if (!data.length) {
                libraryBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Không có câu hỏi phù hợp</td></tr>`;
                return;
            }
            libraryBody.innerHTML = data.map(q => `
                <tr>
                    <td><span class="font-monospace">#${escapeHtml(q.maCauHoi || q.id)}</span></td>
                    <td>${escapeHtml(q.noiDungCauHoi)}</td>
                    <td>${typeBadge(q.loaiCauHoi)}</td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-primary" type="button" onclick="addQuestionFromLibrary(${q.id})">
                            <i class="fas fa-plus me-1"></i>Thêm
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (e) {
            console.error(e);
            libraryBody.innerHTML = `<tr><td colspan="4" class="text-danger text-center py-4">Lỗi tải thư viện câu hỏi</td></tr>`;
        }
    }

    function openLibrary() {
        if (libraryModal) libraryModal.show();
        loadLibrary();
    }

    async function addQuestionFromLibrary(id) {
        try {
            const payload = {
                maKhaoSat: surveyId,
                maCauHoi: id,
                thuTu: questionsCache.length + 1,
            };
            const res = await fetch(apiUrl('/api/surveys/attach-question'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const json = await res.json();
            if (!res.ok || json.error) {
                showToast('error', json.message || 'Không thể gắn câu hỏi');
                return;
            }
            showToast('success', 'Đã thêm câu hỏi vào khảo sát');
            loadQuestions();
        } catch (e) {
            console.error(e);
            showToast('error', 'Lỗi khi thêm câu hỏi');
        }
    }

    function renderSurveyInfo(data) {
        document.getElementById('survey-title').textContent = data.tieuDe || 'Khảo sát';
        document.getElementById('survey-desc').textContent = data.moTa || 'Không có mô tả.';
        document.getElementById('survey-meta').textContent = '#' + (data.id || surveyId);
        document.getElementById('survey-code').textContent = data.maKhaoSat || data.ma_khao_sat || ('#' + surveyId);
        document.getElementById('survey-created').textContent = formatDate(data.created_at || data.createdAt);
        document.getElementById('survey-updated').textContent = formatDate(data.updated_at || data.updatedAt || data.created_at || data.createdAt);
        document.getElementById('survey-category').textContent = data.danhMuc || 'Chưa rõ';
        document.getElementById('survey-question-count').textContent = (data.questionCount ?? (data.questions ? data.questions.length : 0)) || 0;

        const statusEl = document.getElementById('survey-status');
        const status = (data.trangThai || 'draft').toLowerCase();
        const badge = statusBadge(status);
        statusEl.className = `badge ${badge.class}`;
        statusEl.textContent = badge.text;
    }

    function renderQuestions(list) {
        questionsCache = list;
        const tbody = document.getElementById('questions-body');
        if (!list || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-inbox me-2"></i>Chưa có câu hỏi nào trong khảo sát này.</td></tr>`;
            document.getElementById('survey-question-count').textContent = 0;
            return;
        }

        document.getElementById('survey-question-count').textContent = list.length;

        tbody.innerHTML = list.map(q => `
            <tr>
                <td class="ps-3"><span class="font-monospace">#${escapeHtml(q.maCauHoi || q.id)}</span></td>
                <td>
                    <div class="fw-semibold text-dark">${escapeHtml(q.noiDungCauHoi)}</div>
                    <div class="text-muted small">${q.batBuocTraLoi ? 'Bắt buộc trả lời' : 'Không bắt buộc'}</div>
                </td>
                <td>${typeBadge(q.loaiCauHoi)}</td>
                <td class="text-center">${q.thuTu ?? '-'}</td>
                <td class="text-end pe-3">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-light text-primary" type="button" onclick="openQuestionModal(${q.id})" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-light text-danger" type="button" onclick="deleteQuestion(${q.id})" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function setLoadingState() {
        const tbody = document.getElementById('questions-body');
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>`;
    }

    function openQuestionModal(id = null) {
        document.getElementById('question-form').reset();
        questionIdInput.value = id ? id : '';
        if (questionModal) {
            questionModal.show();
        }

        if (id) {
            document.getElementById('question-modal-title').textContent = 'Cập nhật câu hỏi';
            const existing = questionsCache.find(q => Number(q.id) === Number(id));
            if (existing) {
                fillQuestionForm(existing);
                return;
            }
            fetchQuestion(id);
        } else {
            document.getElementById('question-modal-title').textContent = 'Thêm câu hỏi';
            questionTypeSelect.value = 'single_choice';
            questionOrderInput.value = Math.max(0, questionsCache.length + 1);
            questionRequiredInput.checked = false;
        }
    }

    async function fetchQuestion(id) {
        try {
            const res = await fetch(apiUrl(`/api/questions/show?id=${id}`));
            const json = await res.json();
            if (json.error) {
                showToast('error', json.message || 'Không thể tải câu hỏi');
                return;
            }
            fillQuestionForm(json.data || json);
        } catch (e) {
            console.error(e);
            showToast('error', 'Lỗi khi tải câu hỏi');
        }
    }

    function fillQuestionForm(question) {
        questionContentInput.value = question.noiDungCauHoi || '';
        questionTypeSelect.value = question.loaiCauHoi || 'single_choice';
        questionOrderInput.value = question.thuTu ?? 0;
        questionRequiredInput.checked = Boolean(question.batBuocTraLoi);
    }

    async function saveQuestion() {
        const content = (questionContentInput.value || '').trim();
        if (!content) {
            showToast('warning', 'Vui lòng nhập nội dung câu hỏi');
            return;
        }

        const payload = {
            maKhaoSat: surveyId,
            noiDungCauHoi: content,
            loaiCauHoi: questionTypeSelect.value,
            batBuocTraLoi: questionRequiredInput.checked ? 1 : 0,
            thuTu: Number(questionOrderInput.value || 0),
        };

        const id = questionIdInput.value;
        const url = id ? `/api/questions?id=${id}` : '/api/questions';
        const method = id ? 'PUT' : 'POST';

        try {
            const res = await fetch(apiUrl(url), {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const json = await res.json();

            if (!res.ok || json.error) {
                showToast('error', json.message || 'Không thể lưu câu hỏi');
                return;
            }

            if (questionModal) {
                questionModal.hide();
            }
            showToast('success', id ? 'Đã cập nhật câu hỏi' : 'Đã thêm câu hỏi');
            loadQuestions();
        } catch (e) {
            console.error(e);
            showToast('error', 'Lỗi khi lưu câu hỏi');
        }
    }

    async function deleteQuestion(id) {
        if (!confirm('Bạn chắc muốn gỡ câu hỏi này khỏi khảo sát?')) return;
        try {
            const res = await fetch(apiUrl('/api/surveys/detach-question'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ maKhaoSat: surveyId, maCauHoi: id }),
            });
            const json = await res.json();
            if (!res.ok || json.error) {
                showToast('error', json.message || 'Không thể gỡ câu hỏi');
                return;
            }
            showToast('success', 'Đã gỡ câu hỏi khỏi khảo sát');
            loadQuestions();
        } catch (e) {
            console.error(e);
            showToast('error', 'Lỗi khi gỡ câu hỏi');
        }
    }

    function statusBadge(status) {
        const map = {
            approved: { class: 'bg-success text-white', text: 'Đã duyệt' },
            pending: { class: 'bg-warning text-dark', text: 'Chờ duyệt' },
            draft: { class: 'bg-secondary text-white', text: 'Nháp' },
            rejected: { class: 'bg-danger text-white', text: 'Từ chối' },
            published: { class: 'bg-primary text-white', text: 'Công bố' },
        };
        return map[status] || { class: 'bg-secondary text-white', text: status };
    }

    function typeBadge(type) {
        const map = {
            single_choice: { class: 'badge bg-primary-subtle text-primary border', text: 'Radio' },
            multiple_choice: { class: 'badge bg-info-subtle text-info border', text: 'Checkbox' },
            text: { class: 'badge bg-secondary-subtle text-secondary border', text: 'Text' },
        };
        const cfg = map[type] || { class: 'badge bg-secondary', text: type || 'N/A' };
        return `<span class="${cfg.class}">${cfg.text}</span>`;
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatDate(value) {
        if (!value) return '-';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return value;
        return d.toLocaleDateString('vi-VN');
    }

    function debounce(fn, delay = 300) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }
</script>
