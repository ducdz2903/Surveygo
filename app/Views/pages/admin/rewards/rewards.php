<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Quản lý Phần Thưởng</h4>
            <p class="text-muted mb-0">Thêm, sửa, xóa và quản lý các phần thưởng trong hệ thống</p>
        </div>
        <button class="btn btn-primary" onclick="showRewardForm()">
            <i class="fas fa-plus me-2"></i>Thêm Phần Thưởng
        </button>
    </div>

    <div class="card mb-4 fade-in">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0" placeholder="Tên phần thưởng...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Loại</label>
                    <select class="form-select" id="filter-type">
                        <option value="">Tất cả loại</option>
                        <option value="cash">Rút Tiền</option>
                        <option value="e_wallet">Ví Điện Tử</option>
                        <option value="giftcard">Gift Card</option>
                        <option value="physical">Quà Tặng</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-light w-100 border" id="reset-filters" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>Đặt lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card fade-in" style="animation-delay: 0.1s">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">ID</th>
                            <th style="min-width: 180px;">Tên Phần Thưởng</th>
                            <th style="width: 120px;">Loại</th>
                            <th style="width: 100px;">Điểm</th>
                            <th style="width: 130px;">Giá Trị</th>
                            <th style="width: 100px;">Kho</th>
                            <th style="width: 120px;">Nhà Cung Cấp</th>
                            <th class="text-end pe-4" style="width: 200px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="rewards-table-body">
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
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
                    Hiển thị <span id="total-rewards">0</span> kết quả
                </div>
                <div id="rewards-pagination"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rewardFormModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title"><i class="fas fa-gift me-2"></i><span id="form-title">Thêm Phần Thưởng</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rewardForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên Phần Thưởng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reward-name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mã Phần Thưởng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reward-code" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại <span class="text-danger">*</span></label>
                            <select class="form-select" id="reward-type" required onchange="onRewardTypeChange()">
                                <option value="">Chọn loại</option>
                                <option value="cash">Rút Tiền</option>
                                <option value="giftcard">Gift Card</option>
                                <option value="physical">Quà Tặng</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Điểm Cần (Point Cost) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="reward-point-cost" min="1" required>
                        </div>
                        <div class="col-md-6" id="stock-field" style="display: none;">
                            <label class="form-label">Số Lượng Kho</label>
                            <input type="number" class="form-control" id="reward-stock" min="0">
                            <small class="text-muted">Để trống = không giới hạn</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Provider/Nhà Cung Cấp</label>
                            <input type="text" class="form-control" id="reward-provider" placeholder="VD: momo, zalopay, apple...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô Tả</label>
                            <textarea class="form-control" id="reward-description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="reward-active" checked>
                                <label class="form-check-label" for="reward-active">
                                    Hoạt động
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="save-reward-btn" onclick="saveReward()">
                    <i class="fas fa-save me-1"></i>Lưu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác Nhận Xóa -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn chắc chắn muốn xóa phần thưởng này? Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                    <i class="fas fa-trash me-1"></i>Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/public/assets/js/admin-helpers.js"></script>

<script>    const API_BASE = '/api';
    let currentPage = 1;
    let selectedRewardId = null;

    const TypeLabels = {
        'cash': 'Rút Tiền',
        'e_wallet': 'Ví Điện Tử',
        'giftcard': 'Gift Card',
        'physical': 'Quà Tặng'
    };

    const TypeBadges = {
        'cash': 'bg-primary',
        'e_wallet': 'bg-secondary-accent',
        'giftcard': 'bg-secondary-accent',
        'physical': 'bg-warning'
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadRewards(1);

        document.getElementById('filter-search').addEventListener('input', () => {
            currentPage = 1;
            loadRewards(1);
        });

        document.getElementById('filter-type').addEventListener('change', () => {
            currentPage = 1;
            loadRewards(1);
        });

        document.getElementById('filter-status').addEventListener('change', () => {
            currentPage = 1;
            loadRewards(1);
        });

        document.getElementById('reset-filters').addEventListener('click', () => {
            document.getElementById('filter-search').value = '';
            document.getElementById('filter-type').value = '';
            document.getElementById('filter-status').value = '';
            currentPage = 1;
            loadRewards(1);
        });

        // Event listener cho nút xóa trong modal
        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            const rewardId = window.pendingDeleteId;
            if (!rewardId) return;

            fetch(`${API_BASE}/admin/rewards/${rewardId}/delete`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Xóa phần thưởng thành công!');
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                    modal.hide();
                    
                    loadRewards(currentPage);
                } else {
                    showToast('error', data.message || 'Lỗi xóa phần thưởng');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Lỗi: ' + error.message);
            });
        });
    });

    function loadRewards(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('rewards-table-body');

        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </td>
            </tr>
        `;

        // Lấy giá trị filter
        const search = document.getElementById('filter-search').value;
        const type = document.getElementById('filter-type').value;

        const params = new URLSearchParams({
            page: page,
            limit: 10,
            search: search,
            type: type
        });

        fetch(`${API_BASE}/admin/rewards?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(json => {
            const data = json.data || [];
            const pagination = json.pagination || { current_page: 1, pages: 1, total: 0 };

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-muted">Không có phần thưởng nào.</td></tr>`;
            } else {
                tbody.innerHTML = data.map(r => renderRewardRow(r)).join('');
            }

            renderPagination(pagination);
            document.getElementById('total-rewards').textContent = pagination.total || 0;
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-danger">Lỗi tải dữ liệu</td></tr>`;
        });
    }

    function renderPagination(pagination) {
        const container = document.getElementById('rewards-pagination');
        
        if (!pagination || pagination.pages <= 1) {
            container.innerHTML = '';
            return;
        }

        const currentPage = pagination.current_page;
        const totalPages = pagination.pages;
        
        let html = '<ul class="pagination pagination-sm mb-0">';

        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <button class="page-link" onclick="loadRewards(${currentPage - 1})">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                 </li>`;

        const start = Math.max(1, currentPage - 1);
        const end = Math.min(totalPages, currentPage + 1);

        if (start > 1) {
            html += `<li class="page-item">
                        <button class="page-link" onclick="loadRewards(1)">1</button>
                     </li>`;
        }
        if (start > 2) {
            html += `<li class="page-item disabled">
                        <span class="page-link">...</span>
                     </li>`;
        }

        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                        <button class="page-link" onclick="loadRewards(${i})">${i}</button>
                     </li>`;
        }

        if (end < totalPages - 1) {
            html += `<li class="page-item disabled">
                        <span class="page-link">...</span>
                     </li>`;
        }
        if (end < totalPages) {
            html += `<li class="page-item">
                        <button class="page-link" onclick="loadRewards(${totalPages})">${totalPages}</button>
                     </li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <button class="page-link" onclick="loadRewards(${currentPage + 1})">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                 </li>`;

        html += '</ul>';
        container.innerHTML = html;
    }

    function renderRewardRow(reward) {
        const typeLabel = TypeLabels[reward.type] || reward.type;
        const typeBadge = TypeBadges[reward.type] || 'bg-light text-dark';
        const stockDisplay = reward.stock === null ? 'Không giới hạn' : reward.stock;
        const providerLabel = reward.provider ? reward.provider : '-';

        return `
            <tr class="slide-in">
                <td class="ps-4"><span class="font-monospace text-dark">#${reward.id}</span></td>
                <td><strong>${reward.name}</strong></td>
                <td><span class="badge ${typeBadge}">${typeLabel}</span></td>
                <td><strong>${reward.point_cost.toLocaleString('vi-VN')}</strong></td>
                <td>${reward.type === 'physical' ? reward.name : (reward.value ? reward.value.toLocaleString('vi-VN') + ' đ' : '-')}</td>
                <td>${stockDisplay}</td>
                <td><small>${providerLabel}</small></td>
                <td class="text-end pe-4">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-light text-primary" onclick="editReward(${reward.id})" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger" onclick="deleteReward(${reward.id})" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function showRewardForm() {
        selectedRewardId = null;
        document.getElementById('form-title').textContent = 'Thêm Phần Thưởng';
        document.getElementById('rewardForm').reset();
        document.getElementById('reward-active').checked = true;
        document.getElementById('stock-field').style.display = 'none';
        const modal = new bootstrap.Modal(document.getElementById('rewardFormModal'));
        modal.show();
    }

    function onRewardTypeChange() {
        const type = document.getElementById('reward-type').value;
        const stockField = document.getElementById('stock-field');
        
        // Chỉ hiển thị stock field cho physical rewards
        if (type === 'physical') {
            stockField.style.display = 'block';
        } else {
            stockField.style.display = 'none';
        }
    }

    function editReward(rewardId) {
        fetch(`${API_BASE}/admin/rewards?page=1&limit=10`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(json => {
            const rewards = json.data || [];
            const reward = rewards.find(r => r.id == rewardId);

            if (!reward) {
                showToast('error', 'Phần thưởng không tìm thấy');
                return;
            }

            selectedRewardId = rewardId;
            document.getElementById('form-title').textContent = 'Sửa Phần Thưởng';
            
            // Gán giá trị an toàn
            const setFieldValue = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = value || '';
            };
            
            setFieldValue('reward-name', reward.name);
            setFieldValue('reward-code', reward.code);
            setFieldValue('reward-type', reward.type);
            setFieldValue('reward-provider', reward.provider);
            setFieldValue('reward-point-cost', reward.point_cost);
            setFieldValue('reward-value', reward.value);
            setFieldValue('reward-stock', reward.stock);
            setFieldValue('reward-description', reward.description);

            onRewardTypeChange();

            const modal = new bootstrap.Modal(document.getElementById('rewardFormModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Lỗi tải dữ liệu phần thưởng');
        });
    }

    function saveReward() {
        const getFieldValue = (id) => {
            const el = document.getElementById(id);
            return el ? el.value : '';
        };

        const name = getFieldValue('reward-name').trim();
        const code = getFieldValue('reward-code').trim();
        const type = getFieldValue('reward-type');
        const provider = getFieldValue('reward-provider').trim();
        const pointCost = getFieldValue('reward-point-cost');
        let value = getFieldValue('reward-value');
        const stock = getFieldValue('reward-stock');
        const description = getFieldValue('reward-description').trim();

        // Validation
        if (!name) {
            showToast('warning', 'Vui lòng nhập tên phần thưởng');
            return;
        }
        if (!code) {
            showToast('warning', 'Vui lòng nhập mã phần thưởng');
            return;
        }
        if (!type) {
            showToast('warning', 'Vui lòng chọn loại phần thưởng');
            return;
        }
        if (!pointCost || pointCost <= 0) {
            showToast('warning', 'Vui lòng nhập số điểm hợp lệ');
            return;
        }
        if(type === 'physical' && stock && stock < 0) {
            showToast('warning', 'Số lượng kho không được âm');
            return;
        }
        if(type === 'cash' ) {
            value = pointCost ;
        }else if(type === 'giftcard') {
            value = pointCost;
        } else if (type === 'physical') {
            value = name;
        }

        const rewardData = {
            name: name,
            code: code,
            type: type,
            provider: provider || null,
            point_cost: parseInt(pointCost),
            value: value ? parseInt(value) : 0,
            stock: stock ? parseInt(stock) : null,
            description: description || null
        };

        const isUpdate = selectedRewardId !== null;
        const url = isUpdate 
            ? `${API_BASE}/admin/rewards/${selectedRewardId}`
            : `${API_BASE}/admin/rewards`;
        const method = isUpdate ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(rewardData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.id) {
                const successMsg = isUpdate ? 'Cập nhật phần thưởng thành công' : 'Tạo phần thưởng thành công';
                showToast('success', successMsg);
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('rewardFormModal'));
                modal.hide();
                
                loadRewards(1);
                resetRewardForm();
                selectedRewardId = null;
            } else {
                showToast('error', data.message || 'Lỗi lưu dữ liệu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Lỗi: ' + error.message);
        });
    }

    function resetRewardForm() {
        document.getElementById('rewardForm').reset();
        document.getElementById('form-title').textContent = 'Thêm Phần Thưởng';
        selectedRewardId = null;
        document.getElementById('stock-field').style.display = 'none';
    }

    function deleteReward(rewardId) {
        // Lưu rewardId để dùng khi confirm
        window.pendingDeleteId = rewardId;
        
        // Hiển thị modal xác nhận
        const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        modal.show();
    }

    function resetFilters() {
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-type').value = '';
        document.getElementById('filter-status').value = '';
        currentPage = 1;
        loadRewards();
    }
</script>
