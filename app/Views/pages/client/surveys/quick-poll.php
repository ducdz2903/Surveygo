<main class="page-content pt-5 mt-5 pb-5">
    <div class="container">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-3">Quick Poll <span id="survey-count">(0)</span></h1>
                <p class="lead text-muted">Trả lời 1 câu hỏi nhanh - Nhận điểm ngay!</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-6">
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
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary" id="btn-reset-filters">
                    <i class="fas fa-redo me-2"></i>Xóa bộ lọc
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

        <!-- Pagination will be rendered inline under the list -->
    </div>
</main>
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
    });
</script>