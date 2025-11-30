<main class="page-content pt-5 mt-5 pb-5">
    <div class="container">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-3">Danh sách Khảo sát <span id="survey-count">(0)</span></h1>
                <p class="lead text-muted">Tham gia các khảo sát để kiếm điểm và đổi thưởng</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="search-input" placeholder="Tìm kiếm khảo sát...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="status-filter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="hoạtĐộng">Hot 🔥</option>
                    <option value="chờDuyệt">Mới ⭐</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary" id="btn-reset-filters">
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
</script>