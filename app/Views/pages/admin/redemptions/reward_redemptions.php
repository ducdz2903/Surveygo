<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Đổi Quà</h4>
            <p class="text-muted mb-0">Theo dõi và quản lý các yêu cầu đổi điểm của người dùng</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Tên hoặc email...">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Trạng thái</label>
                    <select class="form-select form-select-sm" id="filter-status">
                        <option value="">Tất cả</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="completed">Hoàn tất</option>
                        <option value="rejected">Từ chối</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Loại</label>
                    <select class="form-select form-select-sm" id="filter-type">
                        <option value="">Tất cả</option>
                        <option value="cash">Rút Tiền</option>
                        <option value="e_wallet">Ví Điện Tử</option>
                        <option value="giftcard">Gift Card</option>
                        <option value="physical">Quà Tặng</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex align-items-end gap-2">
                    <button class="btn btn-sm btn-light border flex-grow-1" id="reset-filters" onclick="resetFilters()">
                        <i class="fas fa-redo me-1"></i>Đặt lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card fade-in" style="animation-delay: 0.1s">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">ID</th>
                            <th style="min-width: 140px;">Người dùng</th>
                            <th style="min-width: 120px;">Phần thưởng</th>
                            <th style="width: 80px;">Điểm</th>
                            <th style="width: 100px;">Giá trị</th>
                            <th style="width: 90px;">Loại</th>
                            <th style="width: 120px;">Trạng thái</th>
                            <th style="width: 100px;">Ngày</th>
                            <th class="text-end pe-4" style="width: 50px;">Xem</th>
                        </tr>
                    </thead>
                    <tbody id="redemptions-table-body">
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-primary spinner-border-sm" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Tổng: <strong id="total-redemptions">0</strong> yêu cầu
                </div>
                <div id="redemptions-pagination"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal chi tiết Redemption -->
<div class="modal fade" id="redemptionDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Chi tiết Yêu cầu Đổi Quà</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="detail-content">
                    <!-- Loaded by JS -->
                </div>
                <div class="row g-3 mt-3 pt-3 border-top">
                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-muted mb-2">Cập nhật trạng thái</label>
                        <select class="form-select" id="status-select">
                            <option value="">-- Chọn trạng thái --</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="completed">Hoàn tất</option>
                            <option value="rejected">Từ chối</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="update-status-btn" onclick="updateRedemptionStatus()">
                    <i class="fas fa-save me-1"></i>Cập nhật
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/admin-helpers.js"></script>

<script>
    const API_BASE = '/api';
    let currentPage = 1;
    let selectedRedemptionId = null;

    const StatusLabels = {
        'pending': 'Chờ xử lý',
        'processing': 'Đang xử lý',
        'completed': 'Hoàn tất',
        'rejected': 'Từ chối'
    };

    const StatusBadges = {
        'pending': 'bg-warning text-dark',
        'processing': 'bg-info',
        'completed': 'bg-success',
        'rejected': 'bg-danger'
    };

    const TypeBadges = {
        'cash': 'bg-primary',
        'e_wallet': 'bg-secondary-accent',
        'giftcard': 'bg-secondary-accent',
        'physical': 'bg-warning'
    };

    const TypeLabels = {
        'cash': 'Rút Tiền',
        'e_wallet': 'Ví Điện Tử',
        'giftcard': 'Gift Card',
        'physical': 'Quà Tặng'
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadRedemptions(1);

        document.getElementById('filter-status').addEventListener('change', () => {
            currentPage = 1;
            loadRedemptions(1);
        });

        document.getElementById('filter-type').addEventListener('change', () => {
            currentPage = 1;
            loadRedemptions(1);
        });

        document.getElementById('filter-search').addEventListener('input', () => {
            currentPage = 1;
            loadRedemptions(1);
        });

        document.getElementById('reset-filters').addEventListener('click', () => {
            document.getElementById('filter-search').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-type').value = '';
            currentPage = 1;
            loadRedemptions(1);
        });
    });

    function resetFilters() {
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-type').value = '';
        currentPage = 1;
        loadRedemptions(1);
    }

    function loadRedemptions(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('redemptions-table-body');

        const status = document.getElementById('filter-status').value;
        const type = document.getElementById('filter-type').value;
        const search = document.getElementById('filter-search').value;

        const params = new URLSearchParams({
            page: page,
            limit: 10,
            status: status,
            type: type,
            search: search
        });

        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </td>
            </tr>
        `;

        fetch(`${API_BASE}/admin/redemptions?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(json => {
            const data = json.data || [];
            const pagination = json.pagination || { current_page: 1, pages: 1, total: 0 };

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-muted">Không tìm thấy yêu cầu nào.</td></tr>`;
            } else {
                tbody.innerHTML = data.map(r => renderRedemptionRow(r)).join('');
            }

            renderPagination(pagination);
            document.getElementById('total-redemptions').textContent = pagination.total || 0;
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-danger">Lỗi tải dữ liệu</td></tr>`;
        });
    }

    function renderRedemptionRow(redemption) {
        const date = new Date(redemption.created_at);
        const dateStr = date.toLocaleDateString('vi-VN', { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit'
        });

        const statusBadge = StatusBadges[redemption.status] || 'bg-secondary';
        const statusLabel = StatusLabels[redemption.status] || redemption.status;
        const typeLabel = TypeLabels[redemption.type] || redemption.type;
        const typeBadge = TypeBadges[redemption.type] || 'bg-light text-dark';

        return `
            <tr class="slide-in">
                <td class="ps-4"><span class="font-monospace text-dark">#${redemption.id}</span></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; border-radius:50%; font-size: 0.75rem;">
                            ${getInitials(redemption.user_name || 'User')}
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${redemption.user_name || 'Unknown'}</div>
                            <small class="text-muted">${redemption.user_email || '-'}</small>
                        </div>
                    </div>
                </td>
                <td>${redemption.reward_name || '-'}</td>
                <td><strong>${redemption.point_cost ? redemption.point_cost.toLocaleString('vi-VN') : '-'}</strong></td>
                <td>
                    ${redemption.type === 'physical' ? 
                        (redemption.reward_name || '-') : 
                        (redemption.value ? redemption.value.toLocaleString('vi-VN') + ' đ' : '-')
                    }
                </td>
                <td><span class="badge ${typeBadge}">${typeLabel}</span></td>
                <td><span class="badge ${statusBadge}">${statusLabel}</span></td>
                <td>${dateStr}</td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-light text-primary" onclick="showRedemptionDetail(${redemption.id})" title="Chi tiết">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function renderPagination(pagination) {
        const container = document.getElementById('redemptions-pagination');
        
        if (!pagination || pagination.pages <= 1) {
            container.innerHTML = '';
            return;
        }

        const currentPage = pagination.current_page;
        const totalPages = pagination.pages;
        
        let html = '<ul class="pagination pagination-sm mb-0">';

        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <button class="page-link" onclick="loadRedemptions(${currentPage - 1})">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                 </li>`;

        const start = Math.max(1, currentPage - 1);
        const end = Math.min(totalPages, currentPage + 1);

        if (start > 1) {
            html += `<li class="page-item">
                        <button class="page-link" onclick="loadRedemptions(1)">1</button>
                     </li>`;
        }
        if (start > 2) {
            html += `<li class="page-item disabled">
                        <span class="page-link">...</span>
                     </li>`;
        }

        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                        <button class="page-link" onclick="loadRedemptions(${i})">${i}</button>
                     </li>`;
        }

        if (end < totalPages - 1) {
            html += `<li class="page-item disabled">
                        <span class="page-link">...</span>
                     </li>`;
        }
        if (end < totalPages) {
            html += `<li class="page-item">
                        <button class="page-link" onclick="loadRedemptions(${totalPages})">${totalPages}</button>
                     </li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <button class="page-link" onclick="loadRedemptions(${currentPage + 1})">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                 </li>`;

        html += '</ul>';
        container.innerHTML = html;
    }

    function showRedemptionDetail(redemptionId) {
        selectedRedemptionId = redemptionId;
        
        // Find redemption in current page data
        const tbody = document.getElementById('redemptions-table-body');
        const rows = tbody.querySelectorAll('tr');
        let redemptionData = null;

        // Get data from API
        fetch(`${API_BASE}/admin/redemptions?page=${currentPage}&limit=10`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(json => {
            const allRedemptions = json.data || [];
            redemptionData = allRedemptions.find(r => r.id == redemptionId);

            if (redemptionData) {
                const detailHtml = `
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                            <div class="user-avatar" style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:600; border-radius:50%; font-size: 1rem;">
                                ${getInitials(redemptionData.user_name || 'User')}
                            </div>
                            <div>
                                <h6 class="mb-0">${redemptionData.user_name || 'Unknown'}</h6>
                                <small class="text-muted">${redemptionData.user_email || '-'}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase mb-3">Phần thưởng</h6>
                        <div class="p-3 bg-light rounded mb-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">Tên</small>
                                    <strong>${redemptionData.reward_name || '-'}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">Loại</small>
                                    <span class="badge ${TypeBadges[redemptionData.type] || 'bg-light text-dark'}">${TypeLabels[redemptionData.type] || redemptionData.type}</span>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">Điểm tiêu</small>
                                    <strong>${redemptionData.point_cost ? redemptionData.point_cost.toLocaleString('vi-VN') : '-'}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">Giá trị</small>
                                    <strong>
                                        ${redemptionData.type === 'physical' ? 
                                            (redemptionData.reward_name || '-') : 
                                            (redemptionData.value ? redemptionData.value.toLocaleString('vi-VN') + ' đ' : '-')
                                        }
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${redemptionData.type === 'physical' && redemptionData.receiver_info ? `
                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase mb-3">Địa chỉ nhận hàng</h6>
                        <div class="p-3 bg-light rounded mb-3">
                            <p class="mb-0">${redemptionData.receiver_info}</p>
                        </div>
                    </div>
                    ` : ''}

                    ${redemptionData.type === 'cash' && (redemptionData.bank_name || redemptionData.account_number) ? `
                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase mb-3">Thông tin ngân hàng</h6>
                        <div class="p-3 bg-light rounded mb-3">
                            <div class="row g-2">
                                ${redemptionData.bank_name ? `
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Ngân hàng</small>
                                    <strong>${redemptionData.bank_name}</strong>
                                </div>
                                ` : ''}
                                ${redemptionData.account_number ? `
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Số tài khoản</small>
                                    <strong>${redemptionData.account_number}</strong>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    ` : ''}

                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase mb-3">Trạng thái</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Hiện tại</small>
                                <span class="badge ${StatusBadges[redemptionData.status] || 'bg-secondary'}">${StatusLabels[redemptionData.status] || redemptionData.status}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Ngày yêu cầu</small>
                                <strong>${new Date(redemptionData.created_at).toLocaleDateString('vi-VN')}</strong>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('detail-content').innerHTML = detailHtml;
                document.getElementById('status-select').value = redemptionData.status || '';

                const modal = new bootstrap.Modal(document.getElementById('redemptionDetailModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function updateRedemptionStatus() {
        const newStatus = document.getElementById('status-select').value;

        if (!newStatus) {
            showToast('warning', 'Vui lòng chọn trạng thái mới');
            return;
        }

        fetch(`${API_BASE}/admin/redemptions/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: selectedRedemptionId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Cập nhật trạng thái thành công');
                const modal = bootstrap.Modal.getInstance(document.getElementById('redemptionDetailModal'));
                modal.hide();
                loadRedemptions(currentPage);
            } else {
                showToast('error', data.message || 'Lỗi cập nhật trạng thái');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Lỗi: ' + error.message);
        });
    }

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
    }
</script>

