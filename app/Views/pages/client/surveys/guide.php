<main class="guide-container" style="margin-top: 8rem;">
    <div id="survey-guide">
        <div class="loading">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            <p>Đang tải thông tin khảo sát...</p>
        </div>
    </div>
</main>


<script>
    // --- TÍCH HỢP TOAST HELPER ---
    (function (global) {
        function ensureContainer() {
            let container = document.getElementById('global-toast-container');
            if (container) return container;
            container = document.createElement('div');
            container.id = 'global-toast-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = 1080;
            document.body.appendChild(container);
            return container;
        }

        function iconFor(status) {
            switch ((status || '').toLowerCase()) {
                case 'success': return '<i class="fas fa-check-circle me-2"></i>';
                case 'warning': return '<i class="fas fa-exclamation-triangle me-2"></i>';
                case 'error':
                case 'danger': return '<i class="fas fa-times-circle me-2"></i>';
                case 'info': return '<i class="fas fa-info-circle me-2"></i>';
                default: return '<i class="fas fa-bell me-2"></i>';
            }
        }

        function bgClassFor(status) {
            switch ((status || '').toLowerCase()) {
                case 'success': return 'bg-success text-white';
                case 'warning': return 'bg-warning text-dark';
                case 'error':
                case 'danger': return 'bg-danger text-white';
                case 'info': return 'bg-info text-dark';
                default: return 'bg-secondary text-white';
            }
        }

        function showToast(status, text, opts = {}) {
            try {
                const container = ensureContainer();
                const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = `
                        <div id="${toastId}" class="toast align-items-center ${bgClassFor(status)} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                          <div class="d-flex">
                            <div class="toast-body d-flex align-items-center">${iconFor(status)}<div class="toast-text">${escapeHtml(text)}</div></div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                          </div>
                        </div>
                    `;
                const toastEl = wrapper.firstElementChild;
                container.appendChild(toastEl);
                const delay = typeof opts.delay === 'number' ? opts.delay : 3000;
                const bsToast = new bootstrap.Toast(toastEl, { delay });
                toastEl.addEventListener('hidden.bs.toast', function () {
                    try { toastEl.remove(); } catch (e) { /* ignore */ }
                });
                bsToast.show();
                return bsToast;
            } catch (e) {
                console.error('Toast error', e);
            }
        }

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }
        global.ToastHelper = { show: showToast };
    })(window);

    // --- LOGIC TRANG HƯỚNG DẪN ---

    document.addEventListener('DOMContentLoaded', async function () {
        const params = new URLSearchParams(window.location.search);
        const surveyId = params.get('id');

        if (!surveyId) {
            document.getElementById('survey-guide').innerHTML = `
                    <div class="error-message">
                        <strong>Lỗi:</strong> Không tìm thấy khảo sát. <a href="/surveys">Quay lại</a>
                    </div>
                `;
            return;
        }

        try {
            const response = await fetch(`/api/surveys/show?id=${surveyId}`);
            const result = await response.json();

            if (result.error) {
                document.getElementById('survey-guide').innerHTML = `
                        <div class="error-message">
                            <strong>Lỗi:</strong> ${result.message}
                        </div>
                    `;
                return;
            }

            const survey = result.data;
            const estimatedTime = survey.thoiLuongDuTinh || 5;

            const guideHTML = `
                    <div class="guide-header">
                        <div class="guide-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h2 class="guide-title">${escapeHtml(survey.tieuDe || 'Khảo sát')}</h2>
                            <p class="text-muted mb-0">${escapeHtml(survey.moTa || 'Khảo sát')}</p>
                        </div>
                    </div>

                    <div class="guide-info">
                        <div class="info-item">
                            <div class="info-label">Độ dài</div>
                            <div class="info-value">${estimatedTime}</div>
                            <div class="text-muted">phút</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Số câu hỏi</div>
                            <div class="info-value">${survey.questionCount || 0}</div>
                            <div class="text-muted">câu</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Điểm thưởng</div>
                            <div class="info-value">${survey.diemThuong || 0}</div>
                            <div class="text-muted">điểm</div>
                        </div>
                    </div>

                    <div class="guide-notes">
                        <h5><i class="fas fa-info-circle"></i> Hướng dẫn</h5>
                        <p><strong>Điểm sẽ thường được cộng ngay sau khi bạn hoàn thành khảo sát.</strong></p>
                        <p>Chúng tôi mong bạn thông cảm rằng, việc tham gia các cuộc khảo sát này có thể xuất hiện những khó khăn...</p>
                        <ul style="margin: 0.5rem 0; padding-left: 1.5rem;">
                            <li>Nếu bạn đóng trang khảo sát giữa chừng, bạn có thể sẽ không thể tiếp tục trả lời khảo sát.</li>
                            <li>Nếu bạn không đọc kỹ câu hỏi hoặc trả lời không nhất quán, cuộc khảo sát có thể bị dừng lại.</li>
                        </ul>
                    </div>

                    <div class="guide-actions">
                        <button class="btn btn-outline-secondary" onclick="history.back()">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </button>
                        <button class="btn btn-primary" onclick="startSurvey(${surveyId})">
                            <i class="fas fa-play me-2"></i>Bắt đầu khảo sát
                        </button>
                    </div>
                `;

            document.getElementById('survey-guide').innerHTML = guideHTML;

        } catch (error) {
            console.error('Lỗi:', error);
            document.getElementById('survey-guide').innerHTML = `
                    <div class="error-message">
                        <strong>Lỗi:</strong> Không thể tải khảo sát. Vui lòng thử lại.
                    </div>
                `;
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    async function startSurvey(surveyId) {
        // Get user from localStorage
        const userRaw = localStorage.getItem('app.user');
        const user = userRaw ? JSON.parse(userRaw) : null;

        if (!user || !user.id) {
            // SỬ DỤNG TOAST HELPER
            ToastHelper.show('warning', 'Vui lòng đăng nhập để bắt đầu khảo sát');
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
            return;
        }

        try {
            // Check if user already submitted this survey
            const response = await fetch(`/api/surveys/${surveyId}/check-submission?userId=${user.id}`);
            const result = await response.json();

            if (result.data && result.data.hasSubmitted) {
                // SỬ DỤNG TOAST HELPER
                ToastHelper.show('warning', 'Bạn đã thực hiện khảo sát này rồi. Mỗi người chỉ được thực hiện một lần.');
                return;
            }

            // If no submission found, proceed to survey directly
            window.location.href = `/surveys/${surveyId}/questions`;

        } catch (error) {
            console.error('Lỗi:', error);
            // SỬ DỤNG TOAST HELPER
            ToastHelper.show('danger', 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }
</script>