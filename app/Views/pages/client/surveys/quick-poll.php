<?php
/** @var string $appName */
/** @var array $urls */
/** @var string $baseUrl */

$appName = $appName ?? 'Surveygo';
$urls = $urls ?? [];
$baseUrl = $baseUrl ?? '';

$__base = rtrim((string) $baseUrl, '/');
$__mk = static function (string $base, string $path): string {
    $p = '/' . ltrim($path, '/');
    return $base === '' ? $p : ($base . $p);
};
$urls['home'] = $urls['home'] ?? $__mk($__base, '/');
$urls['login'] = $urls['login'] ?? $__mk($__base, '/login');
$urls['register'] = $urls['register'] ?? $__mk($__base, '/register');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quick Poll - <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="public/assets/css/app.css">
    <link rel="stylesheet" href="public/assets/css/components/navbar.css">
    <link rel="stylesheet" href="public/assets/css/client/home.css">
    <link rel="stylesheet" href="public/assets/css/components/footer.css">
</head>

<body class="page page--quick-polls">
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

    <main class="page-content pt-5 mt-5 pb-5">
        <div class="container">
            <!-- Header -->
            <div class="row mb-5">
                <div class="col-lg-8">
                    <h1 class="display-6 fw-bold mb-3">Quick Poll</h1>
                    <p class="lead text-muted">Trả lời 1 câu hỏi nhanh - Nhận điểm ngay!</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search-input" placeholder="Tìm kiếm quick poll...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="status-filter">
                        <option value="">Tất cả</option>
                        <option value="hoạtĐộng">Hot 🔥</option>
                        <option value="pending">Mới ⭐</option>
                        <option value="chờDuyệt">Chờ duyệt ⏳</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-outline-primary flex-fill" id="btn-reset-filters">
                        <i class="fas fa-redo me-2"></i>Xóa bộ lọc
                    </button>
                    <button class="btn btn-primary-gradient flex-fill" id="btn-add-quickpoll">
                        <i class="fas fa-plus me-2"></i>Thêm Quick-poll
                    </button>
                </div>
            </div>

            <!-- Quick Polls List -->
            <div class="row g-4 mb-4" id="polls-container">
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" id="pagination-container"></nav>
        </div>
    </main>

    <!-- Add Quick Poll Modal -->
    <div class="modal fade" id="addQuickPollModal" tabindex="-1" aria-labelledby="addQuickPollModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title text-gradient" id="addQuickPollModalLabel"><i class="fas fa-plus-circle me-2"></i>Thêm Quick Poll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addQuickPollForm">
                        <div class="mb-4">
                            <label for="pollTopic" class="form-label fw-bold"><i class="fas fa-tag text-primary me-2"></i>Chủ đề</label>
                            <input type="text" class="form-control form-control-lg" id="pollTopic" placeholder="Nhập chủ đề của quick poll" required style="border-radius: 12px; border: 2px solid var(--border-color);">
                        </div>
                        <div class="mb-4">
                            <label for="pollQuestion" class="form-label fw-bold"><i class="fas fa-question-circle text-primary me-2"></i>Nội dung câu hỏi</label>
                            <textarea class="form-control form-control-lg" id="pollQuestion" rows="3" placeholder="Nhập nội dung câu hỏi" required style="border-radius: 12px; border: 2px solid var(--border-color); resize: vertical;"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="pollReward" class="form-label fw-bold"><i class="fas fa-coins text-warning me-2"></i>Số điểm thưởng</label>
                            <input type="number" class="form-control form-control-lg" id="pollReward" placeholder="Nhập số điểm thưởng" min="1" required style="border-radius: 12px; border: 2px solid var(--border-color);">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-list-check text-success me-2"></i>Loại lựa chọn</label>
                            <select class="form-select form-select-lg" id="pollType" style="border-radius: 12px; border: 2px solid var(--border-color);">
                                <option value="single">Chọn 1 câu duy nhất</option>
                                <option value="multiple">Chọn nhiều câu</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-reply-all text-info me-2"></i>Câu trả lời</label>
                            <div id="answersContainer">
                                <div class="input-group mb-3 answer-item" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <input type="text" class="form-control form-control-lg border-0" placeholder="Câu trả lời 1" required style="background: var(--background-light);">
                                    <button type="button" class="btn btn-outline-danger remove-answer border-0" disabled style="background: var(--background-light);"><i class="fas fa-trash"></i></button>
                                </div>
                                <div class="input-group mb-3 answer-item" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <input type="text" class="form-control form-control-lg border-0" placeholder="Câu trả lời 2" required style="background: var(--background-light);">
                                    <button type="button" class="btn btn-outline-danger remove-answer border-0" disabled style="background: var(--background-light);"><i class="fas fa-trash"></i></button>
                                </div>
                                <div class="input-group mb-3 answer-item" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <input type="text" class="form-control form-control-lg border-0" placeholder="Câu trả lời 3" required style="background: var(--background-light);">
                                    <button type="button" class="btn btn-outline-danger remove-answer border-0" disabled style="background: var(--background-light);"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="addAnswerBtn" style="border-radius: 25px; padding: 0.5rem 1rem;">
                                <i class="fas fa-plus me-1"></i>Thêm câu trả lời
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 25px; padding: 0.5rem 1.5rem;">Hủy</button>
                    <button type="button" class="btn btn-primary-gradient" id="saveQuickPollBtn" style="border-radius: 25px; padding: 0.5rem 1.5rem;">Lưu Quick Poll</button>
                </div>
            </div>
        </div>
    </div>

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const baseUrl = window.location.origin;
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
                const response = await fetch(`${baseUrl}/api/surveys?${params.toString()}`);
                const result = await response.json();

                if (result.error) {
                    console.error('Error:', result.error);
                    document.getElementById('polls-container').innerHTML =
                        '<div class="col-12"><div class="alert alert-danger">Lỗi tải quick poll.</div></div>';
                    return;
                }

                currentPage = result.meta.page;
                renderQuickPolls(result.data);
                renderPagination(result.meta);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('polls-container').innerHTML =
                    '<div class="col-12"><div class="alert alert-danger">Lỗi kết nối tới máy chủ.</div></div>';
            }
        }

        // Render quick polls grid (same style as surveys)
        function renderQuickPolls(surveys) {
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

            // Add Quick Poll Modal functionality
            const addQuickPollBtn = document.getElementById('btn-add-quickpoll');
            const addQuickPollModal = new bootstrap.Modal(document.getElementById('addQuickPollModal'));
            const addAnswerBtn = document.getElementById('addAnswerBtn');
            const answersContainer = document.getElementById('answersContainer');
            const saveQuickPollBtn = document.getElementById('saveQuickPollBtn');

            addQuickPollBtn.addEventListener('click', function () {
                addQuickPollModal.show();
            });

            addAnswerBtn.addEventListener('click', function () {
                const answerCount = answersContainer.querySelectorAll('.answer-item').length;
                const newAnswerItem = document.createElement('div');
                newAnswerItem.className = 'input-group mb-3 answer-item';
                newAnswerItem.style.cssText = 'border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
                newAnswerItem.innerHTML = `
                    <input type="text" class="form-control form-control-lg border-0" placeholder="Câu trả lời ${answerCount + 1}" required style="background: var(--background-light);">
                    <button type="button" class="btn btn-outline-danger remove-answer border-0" style="background: var(--background-light);"><i class="fas fa-trash"></i></button>
                `;
                answersContainer.appendChild(newAnswerItem);
                updateRemoveButtons();
            });

            answersContainer.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-answer') || e.target.closest('.remove-answer')) {
                    const answerItems = answersContainer.querySelectorAll('.answer-item');
                    if (answerItems.length > 3) {
                        e.target.closest('.answer-item').remove();
                        updateRemoveButtons();
                    }
                }
            });

            function updateRemoveButtons() {
                const answerItems = answersContainer.querySelectorAll('.answer-item');
                answerItems.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-answer');
                    removeBtn.disabled = answerItems.length <= 3;
                });
            }

            saveQuickPollBtn.addEventListener('click', function () {
                const form = document.getElementById('addQuickPollForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const pollData = {
                    topic: document.getElementById('pollTopic').value,
                    question: document.getElementById('pollQuestion').value,
                    reward: parseInt(document.getElementById('pollReward').value),
                    type: document.getElementById('pollType').value,
                    answers: Array.from(answersContainer.querySelectorAll('.answer-item input')).map(input => input.value)
                };

                // TODO: Send data to backend API
                console.log('Quick Poll Data:', pollData);

                // For now, just show success message and close modal
                alert('Quick Poll đã được tạo thành công!');
                addQuickPollModal.hide();
                form.reset();
                // Reset answers to default 3
                answersContainer.innerHTML = `
                    <div class="input-group mb-3 answer-item" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <input type="text" class="form-control form-control-lg border-0" placeholder="Câu trả lời 1" required style="background: var(--background-light);">
                        <button type="button" class="btn btn-outline-danger remove-answer border-0" disabled style="background: var(--background-light);"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="input-group mb-3 answer-item" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <input type="text" class="form-control form-control-lg border-0" placeholder="Câu trả lời 2" required style="background: var(--background-light);">
                        <button type="button" class="btn btn-outline-danger remove-answer border-0" disabled style="background: var(--background-light);"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="input-group mb-3 answer-item" style="border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <input type="text" class="form-control form-control-lg border-0" placeholder="Câu trả lời 3" required style="background: var(--background-light);">
                        <button type="button" class="btn btn-outline-danger remove-answer border-0" disabled style="background: var(--background-light);"><i class="fas fa-trash"></i></button>
                    </div>
                `;
            });
        });
    </script>
</body>

</html>