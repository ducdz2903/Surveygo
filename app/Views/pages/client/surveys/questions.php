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

    <!-- Modal Phản hồi -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content feedback-modal">
                <div class="modal-header feedback-header border-0">
                    <div>
                        <h5 class="modal-title fw-bold">Đánh giá khảo sát</h5>
                        <p class="small text-muted mb-0">Giúp chúng tôi cải thiện dịch vụ</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <form id="feedback-form">
                        <!-- Rating Section -->
                        <div class="mb-4">
                            <label class="d-block mb-3 fw-600">
                                Bạn cảm thấy như thế nào? <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-2 justify-content-center" id="rating-stars">
                                <button type="button" class="rating-btn" data-rating="1" title="Rất tệ">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button type="button" class="rating-btn" data-rating="2" title="Tệ">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button type="button" class="rating-btn" data-rating="3" title="Bình thường">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button type="button" class="rating-btn" data-rating="4" title="Tốt">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button type="button" class="rating-btn" data-rating="5" title="Rất tốt">
                                    <i class="fas fa-star"></i>
                                </button>
                            </div>
                            <p class="text-center text-muted small mt-3 mb-0" id="rating-text">Chọn một đánh giá</p>
                            <input type="hidden" id="rating-value" name="danhGia" value="0">
                        </div>

                        <!-- Comment Section -->
                        <div class="mb-3">
                            <label for="feedback-text" class="form-label fw-600">
                                Góp ý thêm <span class="text-muted fw-normal">(tuỳ chọn)</span>
                            </label>
                            <textarea id="feedback-text" name="binhLuan" class="form-control feedback-input" rows="3"
                                placeholder="Chia sẻ ý kiến của bạn..."></textarea>
                            <small class="text-muted d-block mt-2">Tối đa 500 ký tự</small>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" id="btn-skip-feedback">
                        Bỏ qua
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-submit-feedback">
                        <i class="fas fa-check me-1"></i>Gửi phản hồi
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/public/assets/js/toast-helper.js"></script>
<script>
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

        // Handle rating type questions with 5 stars
        if (loaiCauHoi === 'rating') {
            const currentRating = answers[questionId] || 0;
            return `
                    <div class="rating-container text-center" data-question-id="${questionId}">
                        <div class="rating-stars d-flex justify-content-center gap-2 mb-3" style="font-size: 2.5rem;">
                            ${[1, 2, 3, 4, 5].map(star => `
                                <i class="fas fa-star rating-star ${star <= currentRating ? 'text-warning' : 'text-muted'}" 
                                   data-rating="${star}" 
                                   style="cursor: pointer; transition: color 0.2s;"
                                   onclick="setRating(${questionId}, ${star})"></i>
                            `).join('')}
                        </div>
                        <input type="hidden" id="rating_${questionId}" name="question_${questionId}" value="${currentRating}">
                        <p class="text-muted" id="rating-text-${questionId}">
                            ${currentRating > 0 ? `Đánh giá: ${currentRating} sao` : 'Chọn số sao để đánh giá'}
                        </p>
                    </div>
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

    // Function to handle star rating clicks
    function setRating(questionId, rating) {
        // Update the answers object
        answers[questionId] = rating;
        
        // Update the hidden input
        const hiddenInput = document.getElementById(`rating_${questionId}`);
        if (hiddenInput) {
            hiddenInput.value = rating;
        }
        
        // Update all stars visual state
        const container = document.querySelector(`[data-question-id="${questionId}"]`);
        if (container) {
            const stars = container.querySelectorAll('.rating-star');
            stars.forEach((star, index) => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                if (starRating <= rating) {
                    star.classList.remove('text-muted');
                    star.classList.add('text-warning');
                } else {
                    star.classList.remove('text-warning');
                    star.classList.add('text-muted');
                }
            });
            
            // Update the rating text
            const ratingText = document.getElementById(`rating-text-${questionId}`);
            if (ratingText) {
                ratingText.textContent = `Đánh giá: ${rating} sao`;
            }
        }
    }

    function saveCurrentAnswer() {
        const question = surveyData.questions[currentQuestion];
        const loaiCauHoi = question.loaiCauHoi;
        const inputName = `question_${question.id}`;

        if (loaiCauHoi === 'text') {
            const textarea = document.querySelector(`textarea[name="${inputName}"]`);
            answers[question.id] = textarea ? textarea.value : null;
        } else if (loaiCauHoi === 'rating') {
            const ratingInput = document.getElementById(`rating_${question.id}`);
            answers[question.id] = ratingInput ? parseInt(ratingInput.value) || null : null;
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
                window.ToastHelper?.show('warning', 'Vui lòng đăng nhập để nộp bài');
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

            window.ToastHelper?.show('success', 'Nộp bài thành công!');

            // Lưu surveyId và userId để sử dụng trong feedback modal
            window.currentSurveyId = surveyId;
            window.currentUserId = user.id;
            window.currentUserName = user.name || 'Người dùng';

            // Hiển thị modal phản hồi thay vì redirect ngay
            setTimeout(() => {
                const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                feedbackModal.show();
            }, 500);
        } catch (error) {
            console.error('Lỗi:', error);
            window.ToastHelper?.show('error', 'Có lỗi xảy ra khi nộp bài');
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

    // Setup feedback modal handlers
    document.addEventListener('DOMContentLoaded', function () {
        // Setup rating stars
        const ratingStars = document.querySelectorAll('.rating-btn');
        const ratingValue = document.getElementById('rating-value');
        const ratingText = document.getElementById('rating-text');

        ratingStars.forEach(star => {
            star.addEventListener('click', function (e) {
                e.preventDefault();
                const rating = this.dataset.rating;
                ratingValue.value = rating;

                // Update UI - remove active from all, add to clicked
                ratingStars.forEach(s => s.classList.remove('active'));
                for (let i = 0; i < rating; i++) {
                    ratingStars[i].classList.add('active');
                }

                // Update text
                const ratingTexts = {
                    1: '⭐ Rất tệ',
                    2: '⭐⭐ Tệ',
                    3: '⭐⭐⭐ Bình thường',
                    4: '⭐⭐⭐⭐ Tốt',
                    5: '⭐⭐⭐⭐⭐ Rất tốt'
                };
                ratingText.textContent = ratingTexts[rating];
            });
        });

        // Nút bỏ qua phản hồi
        document.getElementById('btn-skip-feedback')?.addEventListener('click', function () {
            const modal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
            modal?.hide();
            setTimeout(() => {
                window.location.href = '/surveys';
            }, 300);
        });

        // Nút gửi phản hồi
        document.getElementById('btn-submit-feedback')?.addEventListener('click', async function () {
            const rating = parseInt(document.getElementById('rating-value').value) || 0;
            const feedbackText = document.getElementById('feedback-text')?.value?.trim() || '';

            if (rating === 0) {
                window.ToastHelper?.show('warning', 'Vui lòng chọn đánh giá');
                return;
            }

            if (feedbackText.length > 500) {
                window.ToastHelper?.show('warning', 'Phản hồi không được vượt quá 500 ký tự');
                return;
            }

            const btn = this;
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang gửi...';

            try {
                const response = await fetch('/api/feedbacks/submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        idKhaoSat: window.currentSurveyId,
                        idNguoiDung: window.currentUserId,
                        tenNguoiDung: window.currentUserName,
                        danhGia: rating,
                        binhLuan: feedbackText
                    })
                });

                const result = await response.json();

                if (result.error) {
                    window.ToastHelper?.show('error', result.message || 'Lỗi khi gửi phản hồi');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    return;
                }

                window.ToastHelper?.show('success', 'Cảm ơn phản hồi của bạn!');

                const modal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
                modal?.hide();

                setTimeout(() => {
                    window.location.href = '/surveys';
                }, 500);
            } catch (error) {
                console.error('Lỗi:', error);
                window.ToastHelper?.show('error', 'Có lỗi xảy ra khi gửi phản hồi');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    });
</script>