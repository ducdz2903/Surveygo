<main class="page-content pt-5 mt-5 pb-5">
    <div class="container">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-12">
                <h1 class="display-6 fw-bold mb-3">Quick Poll <span id="survey-count">(0)</span></h1>
                <p class="lead text-muted">Trả lời 1 câu hỏi nhanh - Nhận điểm ngay!</p>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-body p-4">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 bg-light" id="search-input" placeholder="Tìm kiếm quick poll..." style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select bg-light" id="status-filter" style="border-radius: 8px;">
                            <option value="">Tất cả</option>
                            <option value="hoạtĐộng">Hot 🔥</option>
                            <option value="pending">Mới ⭐</option>
                            <option value="chờDuyệt">Chờ duyệt ⏳</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="btn-reset-filters" style="border-radius: 8px;">
                            <i class="fas fa-redo me-2"></i>Xóa bộ lọc
                        </button>
                        <button class="btn btn-primary-gradient btn-lg d-flex align-items-center justify-content-center gap-2 px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createQuickPollModal" style="border-radius: 10px;">
                            <i class="fas fa-plus-circle fs-5"></i>
                            <span>Tạo Quick-poll</span>
                        </button>
                    </div>
                </div>

                <!-- Quick Polls List -->
                <div class="row g-4" id="polls-container">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination will be rendered inline under the list -->
    </div>
</main>

<!-- Create Quick Poll Modal -->
<div class="modal fade" id="createQuickPollModal" tabindex="-1" aria-labelledby="createQuickPollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="margin-top: 75px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #10BCD3 0%, #0B99B8 100%); color: white; border-bottom: none;">
                <h5 class="modal-title fw-bold" id="createQuickPollModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Tạo Quick Poll
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body glass-card" style="border-radius: 0; border: none; box-shadow: none; max-height: 70vh; overflow-y: auto;">
                <form id="createQuickPollForm">
                    <div class="mb-3">
                        <label for="pollTitle" class="form-label fw-semibold">Tên chủ đề</label>
                        <input type="text" class="form-control" id="pollTitle" required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label for="pollDescription" class="form-label fw-semibold">Mô tả</label>
                        <textarea class="form-control" id="pollDescription" rows="3" required style="border-radius: 10px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pollPoints" class="form-label fw-semibold">Số điểm thưởng (tối đa 100)</label>
                        <input type="number" class="form-control" id="pollPoints" min="1" max="100" required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label for="questionType" class="form-label fw-semibold">Loại câu hỏi</label>
                        <select class="form-select" id="questionType" required style="border-radius: 10px;">
                            <option value="single" selected>Chọn 1</option>
                            <option value="multiple">Chọn nhiều</option>
                            <option value="rating">Đánh giá sao</option>
                            <option value="yesno">Có/Không</option>
                            <option value="text">Nhập chữ</option>
                        </select>
                    </div>
                    <div class="mb-3" id="answerOptions" style="display: block;">
                        <label class="form-label fw-semibold">Các lựa chọn trả lời</label>
                        <div id="optionsContainer">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Lựa chọn 1" required style="border-radius: 10px 0 0 10px;">
                                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)" style="border-radius: 0 10px 10px 0;">Xóa</button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Lựa chọn 2" required style="border-radius: 10px 0 0 10px;">
                                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)" style="border-radius: 0 10px 10px 0;">Xóa</button>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Lựa chọn 3" required style="border-radius: 10px 0 0 10px;">
                                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)" style="border-radius: 0 10px 10px 0;">Xóa</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption()" style="border-radius: 20px;">Thêm lựa chọn</button>
                    </div>
                    <div class="mb-3" id="answerPreview" style="display: none;">
                        <label class="form-label fw-semibold">Xem trước câu trả lời</label>
                        <div id="previewContainer" class="border rounded p-3 bg-light" style="border-radius: 15px;">
                            <!-- Preview will be rendered here -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-outline-secondary-accent" data-bs-dismiss="modal" style="border-radius: 25px;">Hủy</button>
                <button type="button" class="btn btn-primary-gradient" onclick="saveQuickPoll()" style="border-radius: 25px;">Lưu Quick Poll</button>
            </div>
        </div>
    </div>
</div>
<script>
    const BASE = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';
    const pageSize = 6;
    let currentPage = 1;

    // Load quick polls
    async function loadQuickPolls(page = 1, filters = {}) {
        const params = new URLSearchParams({
            page: page,
            limit: pageSize,
            isQuickPoll: true,
        });

        if (filters.search) {
            params.append('search', filters.search);
        }

        if (filters.status) {
            params.append('trangThai', filters.status);
        }

        try {
            const response = await fetch(`${BASE}/api/surveys?${params.toString()}`);
            const result = await response.json();

            if (result.error) {
                console.error('Error:', result.error);
                document.getElementById('polls-container').innerHTML =
                    '<div class="col-12"><div class="alert alert-danger">Lỗi tải quick poll.</div></div>';
                return;
            }

            currentPage = result.meta.page;
            renderQuickPolls(result.data, result.meta);
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('polls-container').innerHTML =
                '<div class="col-12"><div class="alert alert-danger">Lỗi kết nối tới máy chủ.</div></div>';
        }
    }

    // Render quick polls grid (same style as surveys)
    function renderQuickPolls(surveys, meta) {
        const container = document.getElementById('polls-container');

        if (!surveys || surveys.length === 0) {
            container.innerHTML =
                '<div class="col-12 text-center py-5"><p class="text-muted fs-5">Không tìm thấy quick poll nào.</p></div>';
            return;
        }

        const badgeMap = {
            'hoạtĐộng': { class: 'badge-hot', icon: 'fas fa-fire', text: 'Hot' },
            'pending': { class: '', icon: 'fas fa-star', text: 'Mới' },
            'chờDuyệt': { class: '', icon: 'fas fa-star', text: 'Mới' },
        };

        container.innerHTML = surveys.map(survey => {
            const badge = badgeMap[survey.trangThai] || { class: '', icon: 'fas fa-star', text: 'Mới' };
            const timeEstimate = survey.thoiLuongDuTinh || 1;

            return `
                    <div class="col-lg-4 col-md-6">
                        <div class="survey-card">
                            <div class="survey-badge ${badge.class}">
                                <i class="${badge.icon} me-1"></i>${badge.text}
                            </div>
                            <div class="survey-header">
                                <h5 class="survey-title">${survey.tieuDe}</h5>
                                <div class="survey-meta">
                                    <span class="text-primary fw-bold"><i class="fas fa-coins me-1"></i>+${survey.diemThuong || 5} điểm</span>
                                    <span><i class="fas fa-clock me-1"></i>~${timeEstimate} phút</span>
                                </div>
                            </div>
                            <p class="survey-desc">${survey.moTa || 'Trả lời nhanh 1 câu hỏi để kiếm điểm!'}</p>
                            <button class="btn btn-gradient mt-auto w-100" onclick="startPoll(${survey.id})">
                                <i class="fas fa-play me-1"></i>Bắt đầu
                            </button>
                        </div>
                    </div>
                `;
        }).join('');

        // Update total count
        const countEl = document.getElementById('survey-count');
        if (countEl && meta) countEl.textContent = `(${meta.total})`;

        // Add simple prev/next pagination like home
        if (meta && meta.totalPages > 1) {
            let pagHtml = `
                <div class="col-12 d-flex justify-content-center gap-2 mt-4">
                    ${meta.page > 1 ? `<button class="btn btn-sm btn-outline-primary" onclick="loadQuickPolls(${meta.page - 1}, getFilters())">← Trước</button>` : ''}
                    <span class="btn btn-sm btn-light disabled">Trang ${meta.page}/${meta.totalPages}</span>
                    ${meta.page < meta.totalPages ? `<button class="btn btn-sm btn-outline-primary" onclick="loadQuickPolls(${meta.page + 1}, getFilters())">Tiếp →</button>` : ''}
                </div>
            `;
            container.innerHTML += pagHtml;
        }
    }

    // Render pagination
    function renderPagination(meta) {
        const container = document.getElementById('pagination-container');

        if (meta.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination justify-content-center">';

        if (meta.page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(${meta.page - 1}, getFilters()); return false;">← Trước</a></li>`;
        }

        const startPage = Math.max(1, meta.page - 2);
        const endPage = Math.min(meta.totalPages, meta.page + 2);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(1, getFilters()); return false;">1</a></li>`;
            if (startPage > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === meta.page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(${i}, getFilters()); return false;">${i}</a></li>`;
            }
        }

        if (endPage < meta.totalPages) {
            if (endPage < meta.totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(${meta.totalPages}, getFilters()); return false;">${meta.totalPages}</a></li>`;
        }

        if (meta.page < meta.totalPages) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(${meta.page + 1}, getFilters()); return false;">Tiếp →</a></li>`;
        }

        html += '</ul>';
        container.innerHTML = html;
    }

    // Get current filters
    function getFilters() {
        return {
            search: document.getElementById('search-input').value,
            status: document.getElementById('status-filter').value,
        };
    }

    // Start a poll - redirect to survey guide page
    function startPoll(pollId) {
        window.location.href = `/surveys/guide?id=${pollId}`;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        loadQuickPolls();

        document.getElementById('search-input').addEventListener('keyup', function () {
            loadQuickPolls(1, getFilters());
        });

        document.getElementById('status-filter').addEventListener('change', function () {
            loadQuickPolls(1, getFilters());
        });

        document.getElementById('btn-reset-filters').addEventListener('click', function () {
            document.getElementById('search-input').value = '';
            document.getElementById('status-filter').value = '';
            loadQuickPolls(1, {});
        });

        // Modal functionality
        document.getElementById('questionType').addEventListener('change', function () {
            toggleAnswerOptions();
        });

        // Initialize modal on show
        document.getElementById('createQuickPollModal').addEventListener('show.bs.modal', function () {
            resetModal();
        });
    });

    // Toggle answer options visibility based on question type
    function toggleAnswerOptions() {
        const questionType = document.getElementById('questionType').value;
        const answerOptions = document.getElementById('answerOptions');
        const answerPreview = document.getElementById('answerPreview');
        if (questionType === 'single' || questionType === 'multiple') {
            answerOptions.style.display = 'block';
            answerPreview.style.display = 'none';
        } else {
            answerOptions.style.display = 'none';
            answerPreview.style.display = 'block';
            renderPreview(questionType);
        }
    }

    // Render preview based on question type
    function renderPreview(questionType) {
        const container = document.getElementById('previewContainer');
        let html = '';

        switch (questionType) {
            case 'rating':
                html = `
                    <div class="text-center preview-rating">
                        <p class="mb-3 fw-semibold">Đánh giá:</p>
                        <div class="rating-stars d-flex justify-content-center gap-1" style="pointer-events: none;">
                            <i class="fas fa-star text-warning fs-4"></i>
                            <i class="fas fa-star text-warning fs-4"></i>
                            <i class="fas fa-star text-warning fs-4"></i>
                            <i class="fas fa-star text-warning fs-4"></i>
                            <i class="fas fa-star text-muted fs-4"></i>
                        </div>
                        <small class="text-muted mt-2 d-block">1-5 sao (không thể chỉnh sửa trong xem trước)</small>
                    </div>
                `;
                break;
            case 'yesno':
                html = `
                    <div class="text-center preview-yesno">
                        <p class="mb-3 fw-semibold">Chọn câu trả lời:</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-success btn-lg" disabled style="cursor: not-allowed;">Có</button>
                            <button class="btn btn-outline-danger btn-lg" disabled style="cursor: not-allowed;">Không</button>
                        </div>
                        <small class="text-muted mt-2 d-block">Các nút này chỉ để xem trước, không thể nhấn</small>
                    </div>
                `;
                break;
            case 'text':
                html = `
                    <div class="preview-text">
                        <p class="mb-3 fw-semibold">Nhập câu trả lời:</p>
                        <textarea class="form-control bg-light" rows="3" placeholder="Nhập câu trả lời của bạn..." readonly style="resize: none; border: 2px dashed #dee2e6;"></textarea>
                        <small class="text-muted mt-2 d-block">Ô văn bản này chỉ để xem trước, không thể nhập liệu</small>
                    </div>
                `;
                break;
            default:
                html = '<p class="text-muted text-center">Chọn loại câu hỏi để xem trước.</p>';
        }

        container.innerHTML = html;
    }

    // Add a new answer option
    function addOption() {
        const container = document.getElementById('optionsContainer');
        const optionCount = container.children.length + 1;
        const newOption = document.createElement('div');
        newOption.className = 'input-group mb-2';
        newOption.innerHTML = `
            <input type="text" class="form-control" placeholder="Lựa chọn ${optionCount}" required>
            <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">Xóa</button>
        `;
        container.appendChild(newOption);
    }

    // Remove an answer option
    function removeOption(button) {
        const container = document.getElementById('optionsContainer');
        if (container.children.length > 1) {
            button.parentElement.remove();
        } else {
            alert('Phải có ít nhất một lựa chọn.');
        }
    }

    // Reset modal form
    function resetModal() {
        document.getElementById('createQuickPollForm').reset();
        document.getElementById('answerOptions').style.display = 'block';
        document.getElementById('answerPreview').style.display = 'none';
        const container = document.getElementById('optionsContainer');
        container.innerHTML = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" placeholder="Lựa chọn 1" required>
                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">Xóa</button>
            </div>
            <div class="input-group mb-2">
                <input type="text" class="form-control" placeholder="Lựa chọn 2" required>
                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">Xóa</button>
            </div>
            <div class="input-group mb-2">
                <input type="text" class="form-control" placeholder="Lựa chọn 3" required>
                <button class="btn btn-outline-danger" type="button" onclick="removeOption(this)">Xóa</button>
            </div>
        `;
    }

    // Save quick poll (placeholder - needs backend integration)
    function saveQuickPoll() {
        const form = document.getElementById('createQuickPollForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const pollData = {
            title: document.getElementById('pollTitle').value,
            description: document.getElementById('pollDescription').value,
            points: document.getElementById('pollPoints').value,
            questionType: document.getElementById('questionType').value,
            options: []
        };

        if (pollData.questionType === 'single' || pollData.questionType === 'multiple') {
            const options = document.querySelectorAll('#optionsContainer input');
            options.forEach(option => {
                if (option.value.trim()) {
                    pollData.options.push(option.value.trim());
                }
            });
        }

        // Placeholder for saving - replace with actual API call
        console.log('Saving quick poll:', pollData);
        alert('Quick poll đã được lưu thành công! (Chức năng này cần tích hợp backend)');

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('createQuickPollModal'));
        modal.hide();
    }
</script>