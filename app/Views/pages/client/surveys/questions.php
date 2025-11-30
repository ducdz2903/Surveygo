<main class="questions-container" style="margin-top: 5rem;">
    <div class="survey-content">
        <div id="survey-header" class="survey-header mb-4"></div>

        <form id="survey-form">
            <div id="questions-container"></div>

            <div class="survey-buttons">
                <button type="button" class="btn btn-outline-secondary" id="btn-prev" disabled>
                    <i class="fas fa-arrow-left me-2"></i>Câu trước
                </button>
                <button type="button" class="btn btn-primary" id="btn-next">
                    Câu tiếp theo<i class="fas fa-arrow-right ms-2"></i>
                </button>
                <button type="submit" class="btn btn-success d-none" id="btn-submit">
                    <i class="fas fa-check me-2"></i>Nộp bài
                </button>
            </div>
        </form>

        <div class="survey-progress">
            <div class="progress-bar-container">
                <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
            </div>
            <div class="progress-text">
                <span id="progress-current">1</span> / <span id="progress-total">0</span>
            </div>
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


    // --- LOGIC TRẢ LỜI CÂU HỎI ---

    let surveyData = null;
    let currentQuestion = 0;
    let answers = {};

    document.addEventListener('DOMContentLoaded', async function () {
        const pathParts = window.location.pathname.split('/');
        const surveyId = pathParts[2];

        if (!surveyId || !surveyId.match(/^\d+$/)) {
            window.location.href = '/surveys';
            return;
        }

        await loadSurvey(surveyId);
    });

    async function loadSurvey(surveyId) {
        try {
            const response = await fetch(`/api/surveys/show?id=${surveyId}`);
            const result = await response.json();

            if (result.error) {
                showError('Không thể tải khảo sát');
                return;
            }

            surveyData = result.data;
            initializeSurvey();
        } catch (error) {
            console.error('Lỗi:', error);
            showError('Có lỗi xảy ra khi tải khảo sát');
        }
    }

    function initializeSurvey() {
        if (!surveyData || !surveyData.questions || surveyData.questions.length === 0) {
            showError('Khảo sát không có câu hỏi');
            return;
        }
        surveyData.questions.forEach(q => {
            answers[q.id] = null;
        });
        document.getElementById('progress-total').textContent = surveyData.questions.length;
        renderHeader();
        renderQuestion(0);
        setupEventListeners();
    }

    function renderHeader() {
        const header = document.getElementById('survey-header');
        header.innerHTML = `
                <div class="survey-title">
                    <h2>${escapeHtml(surveyData.tieuDe)}</h2>
                    <p class="text-muted">${escapeHtml(surveyData.moTa)}</p>
                </div>
            `;
    }

    async function renderQuestion(index) {
        currentQuestion = index;
        const question = surveyData.questions[index];
        if (!question) return;

        const container = document.getElementById('questions-container');
        const optionsHTML = await renderOptions(question);

        container.innerHTML = `
                <div class="question-card">
                    <div class="question-number">Câu hỏi ${index + 1}</div>
                    <div class="question-text">${escapeHtml(question.noiDungCauHoi)}</div>
                    <div id="options-container" class="options-container mt-4">
                        ${optionsHTML}
                    </div>
                </div>
            `;

        document.getElementById('progress-current').textContent = index + 1;
        document.getElementById('progress-bar').style.width = ((index + 1) / surveyData.questions.length * 100) + '%';
        updateButtonStates();
    }

    async function renderOptions(question) {
        const loaiCauHoi = question.loaiCauHoi;
        const questionId = question.id;

        if (loaiCauHoi === 'text') {
            const value = answers[questionId] || '';
            return `
                    <textarea 
                        id="question_${questionId}" 
                        name="question_${questionId}"
                        class="form-control question-textarea"
                        rows="4"
                        placeholder="Nhập câu trả lời của bạn..."
                    >${escapeHtml(value)}</textarea>
                `;
        }

        const inputType = loaiCauHoi === 'single_choice' ? 'radio' : 'checkbox';
        let questionAnswers = [];
        if (question.answers && question.answers.length > 0) {
            questionAnswers = question.answers;
        } else {
            try {
                const answersResponse = await fetch(`/api/questions/${question.id}/answers`);
                const answersResult = await answersResponse.json();
                if (!answersResult.error && answersResult.data) {
                    questionAnswers = answersResult.data;
                }
            } catch (error) {
                console.error('Lỗi khi lấy đáp án:', error);
            }
        }

        let html = '';
        if (questionAnswers && questionAnswers.length > 0) {
            questionAnswers.forEach((answer) => {
                const answerId = answer.id;
                const answerText = answer.noiDungCauTraLoi || answer.noiDungDapAn;
                const isChecked = loaiCauHoi === 'single_choice'
                    ? answers[questionId] === answerId
                    : (Array.isArray(answers[questionId]) && answers[questionId].includes(answerId));

                html += `
                        <div class="option-item">
                            <input 
                                type="${inputType}" 
                                id="answer_${answerId}" 
                                name="question_${questionId}" 
                                value="${answerId}"
                                class="form-check-input"
                                ${isChecked ? 'checked' : ''}
                            >
                            <label for="answer_${answerId}" class="form-check-label">
                                ${escapeHtml(answerText)}
                            </label>
                        </div>
                    `;
            });
        }
        return html;
    }

    function setupEventListeners() {
        document.getElementById('btn-next').addEventListener('click', function (e) {
            e.preventDefault();
            saveCurrentAnswer();
            if (currentQuestion < surveyData.questions.length - 1) {
                renderQuestion(currentQuestion + 1);
            }
        });

        document.getElementById('btn-prev').addEventListener('click', function (e) {
            e.preventDefault();
            saveCurrentAnswer();
            if (currentQuestion > 0) {
                renderQuestion(currentQuestion - 1);
            }
        });

        document.getElementById('survey-form').addEventListener('submit', function (e) {
            e.preventDefault();
            submitSurvey();
        });
    }

    function saveCurrentAnswer() {
        const question = surveyData.questions[currentQuestion];
        const loaiCauHoi = question.loaiCauHoi;
        const inputName = `question_${question.id}`;

        if (loaiCauHoi === 'text') {
            const textarea = document.querySelector(`textarea[name="${inputName}"]`);
            answers[question.id] = textarea ? textarea.value : null;
        } else if (loaiCauHoi === 'single_choice') {
            const checked = document.querySelector(`input[name="${inputName}"]:checked`);
            answers[question.id] = checked ? checked.value : null;
        } else if (loaiCauHoi === 'multiple_choice') {
            const checked = document.querySelectorAll(`input[name="${inputName}"]:checked`);
            answers[question.id] = Array.from(checked).map(c => c.value);
        }
    }

    function updateButtonStates() {
        const isFirst = currentQuestion === 0;
        const isLast = currentQuestion === surveyData.questions.length - 1;
        document.getElementById('btn-prev').disabled = isFirst;
        document.getElementById('btn-next').classList.toggle('d-none', isLast);
        document.getElementById('btn-submit').classList.toggle('d-none', !isLast);
    }

    async function submitSurvey() {
        try {
            saveCurrentAnswer();
            const pathParts = window.location.pathname.split('/');
            const surveyId = pathParts[2];
            const userRaw = localStorage.getItem('app.user');
            const user = userRaw ? JSON.parse(userRaw) : null;

            if (!user || !user.id) {
                // SỬ DỤNG TOAST HELPER
                ToastHelper.show('warning', 'Vui lòng đăng nhập để nộp bài');
                setTimeout(() => { window.location.href = '/login'; }, 1500);
                return;
            }

            const formattedAnswers = {};
            for (const [questionId, value] of Object.entries(answers)) {
                formattedAnswers[questionId] = typeof value === 'string'
                    ? value
                    : JSON.stringify(value);
            }

            const response = await fetch(`/api/surveys/${surveyId}/submit`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    userId: user.id,
                    answers: formattedAnswers
                })
            });

            const result = await response.json();

            if (result.error) {
                showError(result.message || 'Lỗi khi nộp bài');
                return;
            }

            // SỬ DỤNG TOAST HELPER
            ToastHelper.show('success', 'Nộp bài thành công!');
            setTimeout(() => {
                window.location.href = '/surveys';
            }, 1000);
        } catch (error) {
            console.error('Lỗi:', error);
            // SỬ DỤNG TOAST HELPER
            ToastHelper.show('danger', 'Có lỗi xảy ra khi nộp bài');
        }
    }

    function showError(message) {
        const container = document.getElementById('questions-container');
        container.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <strong>Lỗi:</strong> ${escapeHtml(message)}
                    <br><br>
                    <a href="/surveys" class="btn btn-primary mt-3">Quay lại danh sách</a>
                </div>
            `;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
</script>