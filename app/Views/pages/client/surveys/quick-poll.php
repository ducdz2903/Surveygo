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
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="search-input" placeholder="Tìm kiếm quick poll...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="status-filter">
                        <option value="">Tất c</option>
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

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const baseUrl = window.location.origin;
        const pageSize = 12;
        let currentPage = 1;

        // Load quick polls
        async function loadQuickPolls(page = 1, filters = {}) {
            const params = new URLSearchParams({
                page: page,
                limit: pageSize,
                quickPoll: true,
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

        // Render quick polls grid
        function renderQuickPolls(surveys) {
            const container = document.getElementById('polls-container');

            if (!surveys || surveys.length === 0) {
                container.innerHTML =
                    '<div class="col-12"><div class="alert alert-info text-center">Không tìm thấy quick poll nào.</div></div>';
                return;
            }

            container.innerHTML = surveys.map(survey => {
                const badge = survey.trangThai === 'hoạtĐộng' ? '🔥 Hot' : '⭐ Mới';
                const timeEstimate = survey.thoiGianUocTinh ? `~${survey.thoiGianUocTinh} phút` : '~1 phút';

                return `
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm hover-shadow survey-card">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-success">${badge}</span>
                                    <small class="text-muted"><i class="far fa-clock"></i> ${timeEstimate}</small>
                                </div>
                                <h5 class="card-title fw-bold mb-2 flex-grow-1">${survey.tieuDe}</h5>
                                <p class="card-text text-muted small mb-3 flex-grow-1">${survey.moTa || 'Không có mô tả'}</p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-warning text-dark">${survey.diemThuong || 0} điểm</span>
                                    ${survey.danhMuc ? `<small class="text-muted">${survey.danhMuc}</small>` : ''}
                                </div>
                                <button class="btn btn-primary w-100" onclick="startPoll(${survey.id})">
                                    Trả lời ngay <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
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
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(${meta.page - 1}, getFilters()); return false;">Trước</a></li>`;
            } else {
                html += '<li class="page-item disabled"><span class="page-link">Trước</span></li>';
            }

            for (let i = 1; i <= meta.totalPages; i++) {
                if (i === meta.page) {
                    html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(${i}, getFilters()); return false;">${i}</a></li>`;
                }
            }

            if (meta.page < meta.totalPages) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadQuickPolls(${meta.page + 1}, getFilters()); return false;">Tiếp</a></li>`;
            } else {
                html += '<li class="page-item disabled"><span class="page-link">Tiếp</span></li>';
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

        // Start a poll (placeholder)
        function startPoll(pollId) {
            alert(`Bạn đã chọn quick poll #${pollId}`);
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
</body>

</html>