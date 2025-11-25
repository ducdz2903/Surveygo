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
    <title>Khảo sát - <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="public/assets/css/app.css">
    <link rel="stylesheet" href="public/assets/css/components/navbar.css">
    <link rel="stylesheet" href="public/assets/css/client/home.css">
    <link rel="stylesheet" href="public/assets/css/components/footer.css">
</head>

<body class="page page--surveys">
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

    <main class="page-content pt-5 mt-5 pb-5">
        <div class="container">
            <!-- Header -->
            <div class="row mb-5">
                <div class="col-lg-8">
                    <h1 class="display-6 fw-bold mb-3">Danh sách Khảo sát</h1>
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

            <!-- Pagination -->
            <nav aria-label="Page navigation" id="pagination-container"></nav>
        </div>
    </main>

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                    isQuickPoll : false ,
                    ...filters,
                });

                const response = await fetch(`/api/surveys?${queryParams}`);
                const result = await response.json();

                if (!result.error && result.data && result.meta) {
                    currentPage = result.meta.page;
                    currentFilters = filters;
                    renderSurveys(result.data, result.meta);
                    renderPagination(result.meta);
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
        }

        // Render pagination
        function renderPagination(meta) {
            const container = document.getElementById('pagination-container');

            if (meta.totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination justify-content-center">';

            // Previous button
            if (meta.page > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="loadSurveys(${meta.page - 1}, ${JSON.stringify(currentFilters).replace(/"/g, '&quot;')})">← Trước</button></li>`;
            }

            // Page numbers
            const startPage = Math.max(1, meta.page - 2);
            const endPage = Math.min(meta.totalPages, meta.page + 2);

            if (startPage > 1) {
                html += `<li class="page-item"><button class="page-link" onclick="loadSurveys(1, ${JSON.stringify(currentFilters).replace(/"/g, '&quot;')})">1</button></li>`;
                if (startPage > 2) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for (let i = startPage; i <= endPage; i++) {
                if (i === meta.page) {
                    html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    html += `<li class="page-item"><button class="page-link" onclick="loadSurveys(${i}, ${JSON.stringify(currentFilters).replace(/"/g, '&quot;')})">${i}</button></li>`;
                }
            }

            if (endPage < meta.totalPages) {
                if (endPage < meta.totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                html += `<li class="page-item"><button class="page-link" onclick="loadSurveys(${meta.totalPages}, ${JSON.stringify(currentFilters).replace(/"/g, '&quot;')})">${meta.totalPages}</button></li>`;
            }

            // Next button
            if (meta.page < meta.totalPages) {
                html += `<li class="page-item"><button class="page-link" onclick="loadSurveys(${meta.page + 1}, ${JSON.stringify(currentFilters).replace(/"/g, '&quot;')})">Tiếp →</button></li>`;
            }

            html += '</ul>';
            container.innerHTML = html;
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
</body>

</html>