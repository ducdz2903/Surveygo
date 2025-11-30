e<main class="page-content pt-5 mt-5 pb-5">
    <div class="container">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-12">
                <h1 class="display-6 fw-bold mb-3">Danh sách Khảo sát <span id="survey-count">(0)</span></h1>
                <p class="lead text-muted">Tham gia các khảo sát để kiếm điểm và đổi thưởng</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="search-input" placeholder="Tìm kiếm khảo sát...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="status-filter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="hoạtĐộng">Hot 🔥</option>
                    <option value="chờDuyệt">Mới ⭐</option>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                    <button class="btn btn-primary-gradient flex-fill" id="add-survey-btn">
                        <i class="fas fa-plus-circle me-2"></i>Thêm khảo sát
                    </button>
                <button class="btn btn-outline-primary flex-fill" id="btn-reset-filters">
                    <i class="fas fa-redo me-2"></i>Xóa bộ lọc
                </button>
            </div>
        </div>

        <!-- Surveys List -->
        <div class="row g-4 mb-4" id="surveys-container">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        </div>

        <!-- Pagination (rendered inline below the list) -->
    </div>
</main>


    <!-- Add Survey Modal -->
    <div class="modal fade" id="addSurveyModal" tabindex="-1" aria-labelledby="addSurveyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content glass-card">
                <div class="modal-header bg-body-light">
                    <h5 class="modal-title text-gradient fw-bold" id="addSurveyModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Tạo khảo sát mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-survey-form">
                        <!-- Survey Details Section -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Thông tin khảo sát
                            </h6>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="survey-title" class="form-label fw-semibold">Tiêu đề khảo sát <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg" id="survey-title" placeholder="Nhập tiêu đề khảo sát" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="survey-status" class="form-label fw-semibold">Trạng thái</label>
                                    <select class="form-select form-select-lg" id="survey-status">
                                        <option value="chờDuyệt">Chờ duyệt</option>
                                        <option value="hoạtĐộng">Hoạt động</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="survey-description" class="form-label fw-semibold">Mô tả khảo sát</label>
                                <textarea class="form-control" id="survey-description" rows="3" placeholder="Nhập mô tả cho khảo sát"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="survey-time" class="form-label fw-semibold">
                                        <i class="fas fa-clock me-1"></i>Thời lượng dự tính (phút)
                                    </label>
                                    <input type="number" class="form-control" id="survey-time" placeholder="10" min="1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="survey-points" class="form-label fw-semibold">
                                        <i class="fas fa-coins me-1"></i>Điểm thưởng
                                    </label>
                                    <input type="number" class="form-control" id="survey-points" placeholder="50" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <hr class="my-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-primary mb-0">
                                <i class="fas fa-question-circle me-2"></i>Câu hỏi khảo sát
                            </h6>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-question-btn">
                                <i class="fas fa-plus me-1"></i>Tạo câu hỏi tiếp theo
                            </button>
                        </div>
                        <div id="questions-container">
                            <div class="question-item glass-card mb-4">
                                <div class="question-header d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-secondary mb-0">
                                        <i class="fas fa-edit me-2"></i>Câu hỏi 1
                                    </h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-question" style="display: none;">
                                        <i class="fas fa-trash me-1"></i>Xóa
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-semibold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control question-content" placeholder="Nhập nội dung câu hỏi" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-semibold">Loại câu hỏi</label>
                                        <select class="form-select question-type">
                                            <option value="text">Text</option>
                                            <option value="multiple-choice">Multiple Choice</option>
                                            <option value="yes-no">Yes/No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 answers-container">
                                    <!-- Dynamic answers will be added here based on type -->
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Bắt buộc trả lời?</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input question-required" type="radio" name="required-0" value="yes">
                                            <label class="form-check-label">Có</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input question-required" type="radio" name="required-0" value="no" checked>
                                            <label class="form-check-label">Không</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-body-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Hủy
                    </button>
                    <button type="button" class="btn btn-primary-gradient" id="save-survey-btn">
                        <i class="fas fa-save me-1"></i>Lưu khảo sát
                    </button>
                </div>
            </div>
        </div>
    </div>

<script>
    let currentPage = 1;
    const pageSize = 6;
    let currentFilters = {};

    // Load surveys
    async function loadSurveys(page = 1, filters = {}) {
        try {
            const queryParams = new URLSearchParams({
                page: page,
                limit: pageSize,
                isQuickPoll: false,
                ...filters,
            });

            const response = await fetch(`/api/surveys?${queryParams}`);
            const result = await response.json();

            if (!result.error && result.data && result.meta) {
                currentPage = result.meta.page;
                currentFilters = filters;
                renderSurveys(result.data, result.meta);
            } else {
                document.getElementById('surveys-container').innerHTML =
                    '<div class="col-12 text-center"><p class="text-muted">Không có khảo sát nào.</p></div>';
            }
        } catch (error) {
            console.error('Lỗi khi tải khảo sát:', error);
            document.getElementById('surveys-container').innerHTML =
                '<div class="col-12 text-center"><p class="text-danger">Lỗi khi tải khảo sát.</p></div>';
        }
    }

    // Render surveys
    function renderSurveys(surveys, meta) {
        const container = document.getElementById('surveys-container');

        if (surveys.length === 0) {
            container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted fs-5">Không có khảo sát nào phù hợp.</p></div>';
            return;
        }

        const badgeMap = {
            'hoạtĐộng': { class: 'badge-hot', icon: 'fas fa-fire', text: 'Hot' },
            'chờDuyệt': { class: '', icon: 'fas fa-star', text: 'Mới' },
        };

        container.innerHTML = surveys.map((survey) => {
            const badge = badgeMap[survey.trangThai] || { class: '', icon: 'fas fa-star', text: 'Mới' };

            return `
                    <div class="col-lg-4 col-md-6">
                        <div class="survey-card">
                            <div class="survey-badge ${badge.class}">
                                <i class="${badge.icon} me-1"></i>${badge.text}
                            </div>
                            <div class="survey-header">
                                <h5 class="survey-title">${survey.tieuDe}</h5>
                                <div class="survey-meta">
                                    <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+${survey.diemThuong || 50} điểm</span>
                                    <span><i class="fas fa-clock me-1"></i>~${survey.thoiLuongDuTinh || 10} phút</span>
                                </div>
                            </div>
                            <p class="survey-desc">${survey.moTa || 'Tham gia khảo sát này để kiếm điểm.'}</p>
                            <a href="/surveys/guide?id=${survey.id}" class="btn btn-gradient mt-auto w-100">
                                <i class="fas fa-play me-1"></i>Bắt đầu
                            </a>
                        </div>
                    </div>
                `;
        }).join('');

        // Update total count display (like home)
        const countEl = document.getElementById('survey-count');
        if (countEl) countEl.textContent = `(${meta.total})`;

        // Add simple prev/next pagination like home
        if (meta.totalPages > 1) {
            let pagHtml = `
                <div class="col-12 d-flex justify-content-center gap-2 mt-4">
                    ${meta.page > 1 ? `<button class="btn btn-sm btn-outline-primary" onclick="loadSurveys(${meta.page - 1})">← Trước</button>` : ''}
                    <span class="btn btn-sm btn-light disabled">Trang ${meta.page}/${meta.totalPages}</span>
                    ${meta.page < meta.totalPages ? `<button class="btn btn-sm btn-outline-primary" onclick="loadSurveys(${meta.page + 1})">Tiếp →</button>` : ''}
                </div>
            `;
            container.innerHTML += pagHtml;
        }
    }


    // Filter handlers
    document.getElementById('search-input').addEventListener('input', function (e) {
        const filters = { ...currentFilters };
        if (e.target.value.trim()) {
            filters.search = e.target.value.trim();
        } else {
            delete filters.search;
        }
        loadSurveys(1, filters);
    });

    document.getElementById('status-filter').addEventListener('change', function (e) {
        const filters = { ...currentFilters };
        if (e.target.value) {
            filters.trangThai = e.target.value;
        } else {
            delete filters.trangThai;
        }
        loadSurveys(1, filters);
    });

    document.getElementById('btn-reset-filters').addEventListener('click', function () {
        document.getElementById('search-input').value = '';
        document.getElementById('status-filter').value = '';
        loadSurveys(1, {});
    });

    // Load initial data
    document.addEventListener('DOMContentLoaded', function () {
        loadSurveys(1, {});
    });

        // Add survey button handler
        document.getElementById('add-survey-btn').addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('addSurveyModal'));
            modal.show();
        });

        // Question management functions
        let questionIndex = 1;

        function updateAnswers(questionItem) {
            const typeSelect = questionItem.querySelector('.question-type');
            const answersContainer = questionItem.querySelector('.answers-container');
            const type = typeSelect.value;

            answersContainer.innerHTML = '';

            if (type === 'multiple-choice') {
                answersContainer.innerHTML = `
                    <label class="form-label">Câu trả lời (nhập mỗi lựa chọn trên một dòng)</label>
                    <textarea class="form-control" rows="4" placeholder="Lựa chọn 1&#10;Lựa chọn 2&#10;Lựa chọn 3"></textarea>
                `;
            } else if (type === 'text') {
                answersContainer.innerHTML = `
                    <label class="form-label">Câu trả lời (text area)</label>
                    <textarea class="form-control" rows="3" placeholder="Người dùng sẽ nhập câu trả lời ở đây" readonly></textarea>
                `;
            }
            // For yes-no, no answers needed
        }

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const questionItem = document.createElement('div');
            questionItem.className = 'question-item glass-card mb-4';
            questionItem.innerHTML = `
                <div class="question-header d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-secondary mb-0">
                        <i class="fas fa-edit me-2"></i>Câu hỏi ${questionIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-question">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-semibold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control question-content" placeholder="Nhập nội dung câu hỏi" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Loại câu hỏi</label>
                        <select class="form-select question-type">
                            <option value="text">Text</option>
                            <option value="multiple-choice">Multiple Choice</option>
                            <option value="yes-no">Yes/No</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 answers-container">
                    <!-- Dynamic answers will be added here based on type -->
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Bắt buộc trả lời?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input question-required" type="radio" name="required-${questionIndex}" value="yes">
                            <label class="form-check-label">Có</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input question-required" type="radio" name="required-${questionIndex}" value="no" checked>
                            <label class="form-check-label">Không</label>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(questionItem);
            updateAnswers(questionItem);

            // Add event listeners
            const typeSelect = questionItem.querySelector('.question-type');
            typeSelect.addEventListener('change', () => updateAnswers(questionItem));

            const removeBtn = questionItem.querySelector('.remove-question');
            removeBtn.addEventListener('click', () => removeQuestion(questionItem));

            questionIndex++;
            updateQuestionNumbers();
        }

        function removeQuestion(questionItem) {
            questionItem.remove();
            updateQuestionNumbers();
        }

        function updateQuestionNumbers() {
            const questionItems = document.querySelectorAll('.question-item');
            questionItems.forEach((item, index) => {
                const header = item.querySelector('.question-header h6');
                if (header) {
                    header.innerHTML = `<i class="fas fa-edit me-2"></i>Câu hỏi ${index + 1}`;
                }
            });
        }

        // Add question button handler
        document.getElementById('add-question-btn').addEventListener('click', addQuestion);

        // Initialize first question
        document.addEventListener('DOMContentLoaded', function () {
            loadSurveys(1, {});
            const firstQuestion = document.querySelector('.question-item');
            if (firstQuestion) {
                updateAnswers(firstQuestion);
                const typeSelect = firstQuestion.querySelector('.question-type');
                typeSelect.addEventListener('change', () => updateAnswers(firstQuestion));
            }
        });
</script>