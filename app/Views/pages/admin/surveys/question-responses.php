<?php
/**
 * Question Responses Page (admin)
 * Display all user responses for a specific question
 */

$baseUrl = $baseUrl ?? '';
$surveyId = (int) ($surveyId ?? 0);
$questionId = (int) ($questionId ?? 0);

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
                <p class="text-uppercase small text-muted mb-1">Câu trả lời của câu hỏi</p>
                <h4 class="mb-0" id="question-title">Đang tải...</h4>
                <div class="text-muted small" id="question-meta">
                    #<?= htmlspecialchars((string) $questionId, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border" id="response-count">
                <i class="fas fa-users me-1"></i>0 câu trả lời
            </span>
        </div>
    </div>

    <div class="row g-3">
        <!-- Left: User List -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Danh sách người trả lời</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="user-list">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Response Detail -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-comment-dots me-2 text-success"></i>Chi tiết câu trả lời</h6>
                    <span class="badge bg-secondary-subtle text-secondary border" id="selected-user-badge">Chưa
                        chọn</span>
                </div>
                <div class="card-body" id="response-detail">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-hand-pointer fa-3x mb-3 text-secondary"></i>
                        <p class="mb-0">Chọn một người dùng từ danh sách bên trái để xem chi tiết câu trả lời</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= $__mk($__base, 'public/assets/js/toast-helper.js') ?>"></script>
<script>
    const surveyId = <?= json_encode($surveyId) ?>;
    const questionId = <?= json_encode($questionId) ?>;
    const baseUrl = <?= json_encode($__base) ?>;
    const apiUrl = (pathWithLeadingSlash) => {
        const cleanBase = baseUrl ? baseUrl.replace(/\/+$/, '') : '';
        const cleanPath = pathWithLeadingSlash.startsWith('/') ? pathWithLeadingSlash : '/' + pathWithLeadingSlash;
        return cleanBase + cleanPath;
    };

    let responseData = null;
    let selectedUserId = null;

    window.addEventListener('load', () => {
        window.showToast = typeof showToast === 'function' ? showToast : function (_, text) { alert(text); };

        document.getElementById('btn-back')?.addEventListener('click', () => {
            if (surveyId) {
                window.location.href = apiUrl(`/admin/surveys/view?id=${surveyId}`);
            } else {
                window.history.back();
            }
        });

        if (!questionId) {
            showToast('error', 'Không xác định được câu hỏi.');
            return;
        }

        loadData();
    });

    async function loadData() {
        try {
            const params = new URLSearchParams({ questionId });
            if (surveyId) params.set('surveyId', surveyId);

            const res = await fetch(apiUrl(`/api/surveys/question-responses?${params.toString()}`));
            const json = await res.json();

            if (json.error) {
                showToast('error', json.message || 'Không thể tải dữ liệu');
                return;
            }

            responseData = json.data;
            renderQuestionInfo(responseData.question);
            renderUserList(responseData.responses);
            document.getElementById('response-count').innerHTML =
                `<i class="fas fa-users me-1"></i>${responseData.totalResponses} câu trả lời`;

        } catch (e) {
            console.error(e);
            showToast('error', 'Có lỗi xảy ra khi tải dữ liệu');
        }
    }

    function renderQuestionInfo(question) {
        document.getElementById('question-title').textContent = question.noiDungCauHoi || 'Câu hỏi';
        document.getElementById('question-meta').textContent = `#${question.id} • ${typeLabelVi(question.loaiCauHoi)}`;
    }

    function renderUserList(responses) {
        const container = document.getElementById('user-list');

        if (!responses || responses.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">Chưa có ai trả lời câu hỏi này</p>
                </div>
            `;
            return;
        }

        container.innerHTML = responses.map((r, idx) => `
            <button type="button" 
                    class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 ${idx === 0 ? 'active' : ''}"
                    data-user-id="${r.maNguoiDung}"
                    onclick="selectUser(${r.maNguoiDung})">
                <div class="avatar-circle bg-primary-subtle text-primary">
                    ${r.userAvatar
                ? `<img src="${escapeHtml(r.userAvatar)}" alt="avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">`
                : `<span>${(r.userName || 'U').charAt(0).toUpperCase()}</span>`
            }
                </div>
                <div class="flex-grow-1 text-start">
                    <div class="fw-semibold">${escapeHtml(r.userName || 'Ẩn danh')}</div>
                    <div class="small text-muted">${escapeHtml(r.userEmail || '')}</div>
                </div>
                <div class="small text-muted">${formatDateTime(r.created_at)}</div>
            </button>
        `).join('');

        // Auto-select first user
        if (responses.length > 0) {
            selectUser(responses[0].maNguoiDung);
        }
    }

    function selectUser(userId) {
        selectedUserId = userId;

        // Update active state in list
        document.querySelectorAll('#user-list .list-group-item').forEach(el => {
            el.classList.toggle('active', parseInt(el.dataset.userId) === userId);
        });

        // Find user response
        const response = responseData.responses.find(r => r.maNguoiDung === userId);
        if (!response) return;

        // Update selected user badge
        document.getElementById('selected-user-badge').textContent = response.userName || 'Ẩn danh';

        // Render response detail
        renderResponseDetail(response);
    }

    function renderResponseDetail(response) {
        const container = document.getElementById('response-detail');
        const question = responseData.question;
        const answers = responseData.answers || [];
        const questionType = question.loaiCauHoi;

        let contentHtml = '';

        // Handle based on question type
        switch (questionType) {
            case 'single_choice':
            case 'multiple_choice':
                if (answers.length > 0) {
                    const selectedIds = parseSelectedAnswerIds(response.noiDungTraLoi);
                    const typeLabel = questionType === 'single_choice' ? 'Một lựa chọn' : 'Nhiều lựa chọn';

                    contentHtml = `
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">${typeLabel.toUpperCase()}</label>
                            <div class="d-flex flex-column gap-2">
                                ${answers.map(a => {
                        const isSelected = selectedIds.includes(a.id);
                        const icon = questionType === 'single_choice'
                            ? (isSelected ? 'fa-circle-dot' : 'fa-circle')
                            : (isSelected ? 'fa-square-check' : 'fa-square');
                        return `
                                        <div class="d-flex align-items-center gap-2 p-2 rounded ${isSelected ? 'bg-success-subtle border border-success' : 'bg-light'}">
                                            <i class="fas ${icon} ${isSelected ? 'text-success' : 'text-muted'}"></i>
                                            <span class="${isSelected ? 'fw-semibold' : ''}">${escapeHtml(a.noiDungCauTraLoi)}</span>
                                        </div>
                                    `;
                    }).join('')}
                            </div>
                        </div>
                    `;
                } else {
                    // Fallback: display raw response if no predefined answers
                    contentHtml = renderTextResponse(response.noiDungTraLoi, 'CÂU TRẢ LỜI');
                }
                break;

            case 'rating':
                const ratingValue = parseInt(response.noiDungTraLoi) || 0;
                const maxRating = 5;
                let starsHtml = '';
                for (let i = 1; i <= maxRating; i++) {
                    starsHtml += `<i class="fas fa-star ${i <= ratingValue ? 'text-warning' : 'text-muted'}" style="font-size: 1.5rem;"></i>`;
                }
                contentHtml = `
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small">ĐÁNH GIÁ</label>
                        <div class="bg-light rounded p-3 d-flex align-items-center gap-2">
                            ${starsHtml}
                            <span class="ms-2 fw-semibold fs-5">${ratingValue}/${maxRating}</span>
                        </div>
                    </div>
                `;
                break;

            case 'yes_no':
                const isYes = ['yes', 'có', '1', 'true'].includes((response.noiDungTraLoi || '').toLowerCase());
                contentHtml = `
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small">CÂU TRẢ LỜI</label>
                        <div class="d-flex gap-3">
                            <div class="p-3 rounded ${isYes ? 'bg-success-subtle border border-success' : 'bg-light'}">
                                <i class="fas fa-check-circle ${isYes ? 'text-success' : 'text-muted'} me-2"></i>
                                <span class="${isYes ? 'fw-semibold' : ''}">Có</span>
                            </div>
                            <div class="p-3 rounded ${!isYes ? 'bg-danger-subtle border border-danger' : 'bg-light'}">
                                <i class="fas fa-times-circle ${!isYes ? 'text-danger' : 'text-muted'} me-2"></i>
                                <span class="${!isYes ? 'fw-semibold' : ''}">Không</span>
                            </div>
                        </div>
                    </div>
                `;
                break;

            case 'text':
            default:
                contentHtml = renderTextResponse(response.noiDungTraLoi, 'NỘI DUNG TRẢ LỜI');
                break;
        }

        // Metadata
        contentHtml += `
            <hr>
            <div class="row g-3 small">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">NGƯỜI TRẢ LỜI</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-circle-sm bg-primary-subtle text-primary">
                            ${response.userName ? response.userName.charAt(0).toUpperCase() : 'U'}
                        </div>
                        <div>
                            <div class="fw-semibold">${escapeHtml(response.userName || 'Ẩn danh')}</div>
                            <div class="text-muted">${escapeHtml(response.userEmail || '')}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">THỜI GIAN TRẢ LỜI</label>
                    <div>${formatDateTimeFull(response.created_at)}</div>
                </div>
            </div>
        `;

        container.innerHTML = contentHtml;
    }

    function renderTextResponse(text, label) {
        return `
            <div class="mb-4">
                <label class="form-label fw-bold text-muted small">${label}</label>
                <div class="bg-light rounded p-3">
                    <p class="mb-0" style="white-space: pre-wrap;">${escapeHtml(text || 'Không có nội dung')}</p>
                </div>
            </div>
        `;
    }


    function parseSelectedAnswerIds(noiDungTraLoi) {
        if (!noiDungTraLoi) return [];
        // Try to parse as JSON array or comma-separated ids
        try {
            const parsed = JSON.parse(noiDungTraLoi);
            if (Array.isArray(parsed)) return parsed.map(Number);
            return [Number(parsed)];
        } catch (e) {
            // Maybe it's a comma-separated list
            return noiDungTraLoi.split(',').map(s => Number(s.trim())).filter(n => !isNaN(n) && n > 0);
        }
    }

    function typeLabelVi(type) {
        const map = {
            'single_choice': 'Một lựa chọn',
            'multiple_choice': 'Nhiều lựa chọn',
            'text': 'Văn bản',
            'rating': 'Đánh giá',
            'yes_no': 'Có/Không',
        };
        return map[type] || type || 'Không xác định';
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

    function formatDateTime(value) {
        if (!value) return '-';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return value;
        return d.toLocaleDateString('vi-VN');
    }

    function formatDateTimeFull(value) {
        if (!value) return '-';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return value;
        return d.toLocaleString('vi-VN');
    }
</script>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
    }

    .avatar-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }

    #user-list {
        max-height: 70vh;
        overflow-y: auto;
    }

    #user-list .list-group-item.active {
        background-color: #e7f1ff;
        border-color: #86b7fe;
        color: inherit;
    }

    #user-list .list-group-item.active .text-muted {
        color: #6c757d !important;
    }
</style>