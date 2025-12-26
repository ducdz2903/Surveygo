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
                        <span class="input-group-text bg-light border-end-0"><i
                                class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0"
                            placeholder="Tên hoặc email...">
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
                        <label class="form-label fw-bold small text-uppercase text-muted mb-2">Cập nhật trạng
                            thái</label>
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

    document.addEventListener('DOMContentLoaded', function () {
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
                    // Xác định transfer status label
                    let transferStatusLabel = '<span class="text-warning"><i>Chưa hoàn thành</i></span>';
                    if (redemptionData.transfer_status === 'completed') {
                        transferStatusLabel = '<span class="text-success"><strong><i class="fas fa-check-circle me-1"></i>Đã hoàn thành</strong></span>';
                    } else if (redemptionData.transfer_status === 'failed') {
                        transferStatusLabel = '<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Thất bại</span>';
                    }

                    // Xác định có hiển thị nút chuyển khoản không
                    const isTransferCompleted = redemptionData.transfer_status === 'completed';
                    const transferBtnClass = isTransferCompleted ? 'btn-secondary' : 'btn-success';
                    const transferBtnDisabled = isTransferCompleted ? 'disabled' : '';
                    const transferBtnText = isTransferCompleted ? '<i class="fas fa-check me-1"></i>Đã thực hiện' : '<i class="fas fa-paper-plane me-1"></i>Thực hiện CK';

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
                        <input type="hidden" id="account-name-input" value="${redemptionData.account_name || ''}">
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
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Ngân hàng</small>
                                    <strong>${redemptionData.bank_name || '-'}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Số tài khoản</small>
                                    <strong>${redemptionData.account_number || '-'}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">
                                        Chủ tài khoản 
                                        <i class="fas fa-search text-info ms-1" role="button" id="verify-account-icon" 
                                           onclick="verifyBankAccount('${redemptionData.bank_name || ''}', '${redemptionData.account_number}', '${redemptionData.account_name || ''}')" 
                                           title="Xác minh tài khoản" style="cursor: pointer;"></i>
                                    </small>
                                    <div id="verify-account-result">
                                        ${redemptionData.account_name ? `<strong class="text-success">${redemptionData.account_name}</strong>` : '<span class="text-muted">-</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ` : ''}

                    <div class="col-12">
                        <h6 class="text-muted small text-uppercase mb-3">Trạng thái</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Hiện tại</small>
                                <span class="badge ${StatusBadges[redemptionData.status] || 'bg-secondary'}">${StatusLabels[redemptionData.status] || redemptionData.status}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Ngày yêu cầu</small>
                                <strong>${new Date(redemptionData.created_at).toLocaleDateString('vi-VN')}</strong>
                            </div>
                            ${redemptionData.type === 'cash' && redemptionData.account_number ? `
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Chuyển khoản</small>
                                <div id="transfer-status">
                                    ${transferStatusLabel}
                                </div>
                                <button class="btn btn-sm ${transferBtnClass} mt-2" id="submit-transfer-btn" ${transferBtnDisabled}
                                    onclick="submitBankTransfer('${redemptionData.bank_name || ''}', '${redemptionData.account_number}', ${redemptionData.id}, '${redemptionData.account_name || ''}')">
                                    ${transferBtnText}
                                </button>
                            </div>
                            ` : ''}
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

        const accountNameValue = (document.getElementById('account-name-input') && document.getElementById('account-name-input').value) || '';

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

    // Hàm xác minh tài khoản ngân hàng
    async function verifyBankAccount(bankName, accountNumber, accountName) {
        const resultDiv = document.getElementById('verify-account-result');
        const verifyIcon = document.getElementById('verify-account-icon');

        // Nếu đã có accountName từ dữ liệu, hiển thị trước
        if (accountName && accountName.trim() !== '') {
            resultDiv.innerHTML = `<strong class="text-success">${accountName}</strong>`;
        }

        // Hiển thị loading
        verifyIcon.className = 'fas fa-spinner fa-spin text-info ms-1';
        verifyIcon.style.cursor = 'default';
        verifyIcon.onclick = null;
        resultDiv.innerHTML = '<span class="text-muted"><i>Đang xác minh...</i></span>';

        // Chuẩn hóa label_text từ bank_name
        const labelText = bankName ? bankName.toLowerCase() : 'vcb';

        try {
            const response = await fetch('/api/bank/verify-account', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    url_id: null,
                    label_text: labelText,
                    number: accountNumber,
                    headless: true,
                    timeout_ms: 30000
                })
            });

            const data = await response.json();

            // Kiểm tra nếu có accName thì thành công
            if (data.accName && !data.error) {
                resultDiv.innerHTML = `<strong class="text-success">${data.accName}</strong>`;
                // populate hidden input
                const accInput = document.getElementById('account-name-input');
                if (accInput) accInput.value = data.accName;
                verifyIcon.className = 'fas fa-check-circle text-success ms-1';

                // Tự động lưu account_name vào database
                await saveAccountNameToDb(selectedRedemptionId, data.accName);
            }
            // Kiểm tra nếu có error hoặc message (bao gồm cả http_code 400, 500, etc.)
            else if (data.error === true || data.message) {
                const errorMsg = data.message || 'Lỗi không xác định';
                resultDiv.innerHTML = `<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>${errorMsg}</small>`;
                verifyIcon.className = 'fas fa-times-circle text-danger ms-1';
            }
            // Trường hợp không có accName và không có error
            else {
                resultDiv.innerHTML = `<small class="text-warning"><i class="fas fa-question-circle me-1"></i>Không tìm thấy</small>`;
                verifyIcon.className = 'fas fa-question-circle text-warning ms-1';
            }
        } catch (error) {
            console.error('Error verifying account:', error);
            resultDiv.innerHTML = `<small class="text-danger"><i class="fas fa-times-circle me-1"></i>Lỗi: ${error.message}</small>`;
            verifyIcon.className = 'fas fa-times-circle text-danger ms-1';
        } finally {
            verifyIcon.style.cursor = 'pointer';
            verifyIcon.onclick = function () { verifyBankAccount(bankName, accountNumber, accountName); };
        }
    }

    // Hàm thực hiện chuyển khoản tự động
    async function submitBankTransfer(bankName, accountNumber, redemptionId, accountName) {
        const transferStatusDiv = document.getElementById('transfer-status');
        const submitBtn = document.getElementById('submit-transfer-btn');

        if (!confirm('Bạn có chắc chắn muốn thực hiện chuyển khoản tự động?')) {
            return;
        }

        // Hiển thị loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang xử lý...';
        transferStatusDiv.innerHTML = '<span class="text-info"><i>Đang thực hiện...</i></span>';

        // Chuẩn hóa label_text từ bank_name
        const labelText = bankName ? bankName.toLowerCase() : '';

        try {
            const response = await fetch('/api/bank/submit-transfer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    url_id: null,
                    label_text: labelText,
                    number: accountNumber,
                    headless: true,
                    timeout_ms: 30000
                })
            });

            const data = await response.json();

            // Kiểm tra nếu thành công
            if (data.success === true) {
                transferStatusDiv.innerHTML = '<span class="text-success"><strong><i class="fas fa-check-circle me-1"></i>Đã hoàn thành</strong></span>';
                submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Đã thực hiện';
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-success');
                submitBtn.classList.add('btn-secondary');

                // Tự động chuyển status dropdown về 'completed'
                const statusSelect = document.getElementById('status-select');
                if (statusSelect) {
                    statusSelect.value = 'completed';
                }

                // Tự động lưu transfer_status vào database
                await saveTransferStatusToDb(redemptionId, 'completed');

                showToast('success', 'Chuyển khoản thành công! Trạng thái đã được lưu.');
            }
            // Kiểm tra nếu có error
            else if (data.error === true || data.message) {
                const errorMsg = data.message || 'Lỗi không xác định';
                transferStatusDiv.innerHTML = `<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>${errorMsg}</small>`;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Thực hiện CK';
                showToast('error', errorMsg);
            }
            // Trường hợp khác
            else {
                transferStatusDiv.innerHTML = '<small class="text-warning"><i class="fas fa-question-circle me-1"></i>Không thành công</small>';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Thực hiện CK';
                showToast('warning', 'Không thể thực hiện chuyển khoản');
            }
        } catch (error) {
            console.error('Error submitting transfer:', error);
            transferStatusDiv.innerHTML = `<small class="text-danger"><i class="fas fa-times-circle me-1"></i>Lỗi: ${error.message}</small>`;
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Thực hiện CK';
            showToast('error', 'Lỗi: ' + error.message);

            // Lưu transfer_status failed vào database
            await saveTransferStatusToDb(redemptionId, 'failed');
        }
    }

    // Hàm lưu account_name vào database (tự động gọi khi verify thành công)
    async function saveAccountNameToDb(redemptionId, accountName) {
        try {
            const response = await fetch(`${API_BASE}/admin/redemptions/save-account-name`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id: redemptionId,
                    account_name: accountName
                })
            });
            const data = await response.json();
            if (data.success) {
                console.log('Account name saved successfully');
            } else {
                console.error('Failed to save account name:', data.message);
            }
        } catch (error) {
            console.error('Error saving account name:', error);
        }
    }

    // Hàm lưu transfer_status vào database (tự động gọi khi transfer thành công/thất bại)
    async function saveTransferStatusToDb(redemptionId, transferStatus) {
        try {
            const response = await fetch(`${API_BASE}/admin/redemptions/save-transfer-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id: redemptionId,
                    transfer_status: transferStatus
                })
            });
            const data = await response.json();
            if (data.success) {
                console.log('Transfer status saved successfully:', transferStatus);
            } else {
                console.error('Failed to save transfer status:', data.message);
            }
        } catch (error) {
            console.error('Error saving transfer status:', error);
        }
    }
</script>