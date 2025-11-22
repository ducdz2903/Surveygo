<?php
/** @var string $appName */
/** @var string $baseUrl */
/** @var array $urls */

$appName = $appName ?? 'Admin - Quản lý Khảo sát';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/public/assets/css/admin.css" rel="stylesheet">
</head>

<body>
    <?php include BASE_PATH . '/app/Views/components/admin/_sidebar.php'; ?>

    <header class="admin-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0"><i class="fas fa-poll me-2"></i>Quản lý Khảo sát</h5>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm..." id="search-input">
                </div>
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">5</span>
                </div>
                <div class="user-menu" id="admin-user-menu">
                    <div class="user-avatar">AD</div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">Admin</div>
                        <div style="font-size: 0.75rem; color: #999;">Quản trị viên</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-content">
        <div class="container-fluid">
            <!-- Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Quản lý Khảo sát</h2>
                    <p class="text-muted mb-0">Tổng số: <strong id="total-surveys">0</strong> khảo sát</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSurveyModal">
                    <i class="fas fa-plus me-2"></i>Tạo khảo sát mới
                </button>
            </div>

            <!-- Filters -->
            <div class="card mb-4 fade-in">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select class="form-select" id="filter-status">
                                <option value="">Tất cả</option>
                                <option value="approved">Đã duyệt</option>
                                <option value="pending">Chờ duyệt</option>
                                <option value="draft">Nháp</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Loại</label>
                            <select class="form-select" id="filter-type">
                                <option value="">Tất cả</option>
                                <option value="regular">Khảo sát thường</option>
                                <option value="quickpoll">Quick Poll</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select class="form-select" id="filter-category">
                                <option value="">Tất cả</option>
                                <option value="Thói quen">Thói quen</option>
                                <option value="Sức khỏe">Sức khỏe</option>
                                <option value="Công nghệ">Công nghệ</option>
                                <option value="Giáo dục">Giáo dục</option>
                                <option value="Dịch vụ">Dịch vụ</option>
                                <option value="QuickPoll">QuickPoll</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-outline-secondary w-100" id="reset-filters">
                                <i class="fas fa-redo me-2"></i>Đặt lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Surveys Table -->
            <div class="card fade-in" style="animation-delay: 0.1s">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Mã</th>
                                    <th>Tiêu đề</th>
                                    <th style="width: 120px;">Loại</th>
                                    <th style="width: 120px;">Trạng thái</th>
                                    <th style="width: 100px;">Câu hỏi</th>
                                    <th style="width: 100px;">Phản hồi</th>
                                    <th style="width: 120px;">Ngày tạo</th>
                                    <th style="width: 180px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="surveys-table-body">
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Pagination" id="pagination-container" class="mt-4"></nav>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Survey Modal -->
    <div class="modal fade" id="createSurveyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tạo khảo sát mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="create-survey-form">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề khảo sát</label>
                            <input type="text" class="form-control" placeholder="Nhập tiêu đề..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea class="form-control" rows="3" placeholder="Nhập mô tả..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại khảo sát</label>
                                <select class="form-select" required>
                                    <option value="regular">Khảo sát thường</option>
                                    <option value="quickpoll">Quick Poll</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select class="form-select" required>
                                    <option value="Thói quen">Thói quen</option>
                                    <option value="Sức khỏe">Sức khỏe</option>
                                    <option value="Công nghệ">Công nghệ</option>
                                    <option value="Giáo dục">Giáo dục</option>
                                    <option value="Dịch vụ">Dịch vụ</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Điểm thưởng</label>
                                <input type="number" class="form-control" value="10" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời lượng dự tính (phút)</label>
                                <input type="number" class="form-control" value="15" min="1">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="createSurvey()">
                        <i class="fas fa-save me-2"></i>Tạo khảo sát
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/admin-mock-data.js"></script>
    <script>
        let currentPage = 1;
        const itemsPerPage = 10;
        let filteredSurveys = [...AdminMockData.surveys];

        // Load surveys
        function loadSurveys() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedSurveys = filteredSurveys.slice(start, end);

            const tbody = document.getElementById('surveys-table-body');
            
            if (paginatedSurveys.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">Không tìm thấy khảo sát nào</td></tr>';
                return;
            }

            tbody.innerHTML = paginatedSurveys.map(survey => `
                <tr class="slide-in">
                    <td><span class="badge badge-light">${survey.code}</span></td>
                    <td>
                        <div class="fw-bold">${survey.title}</div>
                        <small class="text-muted">Người tạo: ${survey.creator}</small>
                    </td>
                    <td>
                        <span class="badge ${survey.type === 'quickpoll' ? 'badge-success' : 'badge-primary'}">
                            ${survey.type === 'quickpoll' ? 'Quick Poll' : 'Thường'}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${AdminHelpers.getStatusBadge(survey.status)}">
                            ${AdminHelpers.getStatusText(survey.status)}
                        </span>
                    </td>
                    <td class="text-center">${survey.questions}</td>
                    <td class="text-center">
                        <strong class="text-primary">${survey.responses}</strong>
                    </td>
                    <td>${AdminHelpers.formatDate(survey.createdAt)}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-icon btn-outline-primary" title="Xem chi tiết" onclick="viewSurvey(${survey.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-success" title="Chỉnh sửa" onclick="editSurvey(${survey.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-info" title="Thống kê" onclick="viewStats(${survey.id})">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-danger" title="Xóa" onclick="deleteSurvey(${survey.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            updatePagination();
            document.getElementById('total-surveys').textContent = filteredSurveys.length;
        }

        // Pagination
        function updatePagination() {
            const totalPages = Math.ceil(filteredSurveys.length / itemsPerPage);
            const container = document.getElementById('pagination-container');

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<ul class="pagination">';
            
            if (currentPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Trước</a></li>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                } else {
                    html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a></li>`;
                }
            }

            if (currentPage < totalPages) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Tiếp</a></li>`;
            }

            html += '</ul>';
            container.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            loadSurveys();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Filters
        function applyFilters() {
            const status = document.getElementById('filter-status').value;
            const type = document.getElementById('filter-type').value;
            const category = document.getElementById('filter-category').value;
            const search = document.getElementById('search-input').value.toLowerCase();

            filteredSurveys = AdminMockData.surveys.filter(survey => {
                if (status && survey.status !== status) return false;
                if (type && survey.type !== type) return false;
                if (category && survey.category !== category) return false;
                if (search && !survey.title.toLowerCase().includes(search)) return false;
                return true;
            });

            currentPage = 1;
            loadSurveys();
        }

        document.getElementById('filter-status').addEventListener('change', applyFilters);
        document.getElementById('filter-type').addEventListener('change', applyFilters);
        document.getElementById('filter-category').addEventListener('change', applyFilters);
        document.getElementById('search-input').addEventListener('input', applyFilters);

        document.getElementById('reset-filters').addEventListener('click', () => {
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-type').value = '';
            document.getElementById('filter-category').value = '';
            document.getElementById('search-input').value = '';
            applyFilters();
        });

        // Actions
        function viewSurvey(id) {
            alert(`Xem chi tiết khảo sát #${id}`);
        }

        function editSurvey(id) {
            alert(`Chỉnh sửa khảo sát #${id}`);
        }

        function viewStats(id) {
            alert(`Xem thống kê khảo sát #${id}`);
        }

        function deleteSurvey(id) {
            if (confirm('Bạn có chắc muốn xóa khảo sát này?')) {
                alert(`Đã xóa khảo sát #${id}`);
            }
        }

        function createSurvey() {
            alert('Tạo khảo sát mới (demo)');
            bootstrap.Modal.getInstance(document.getElementById('createSurveyModal')).hide();
        }

        // User menu
        document.getElementById('admin-user-menu')?.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn đăng xuất?')) {
                localStorage.removeItem('app.user');
                window.location.href = '/login';
            }
        });

        // Initialize
        loadSurveys();
    </script>
</body>

</html>