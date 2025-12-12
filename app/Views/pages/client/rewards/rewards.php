<section class="rewards-hero">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="rewards-title">Đổi Điểm</h1>
                    <p class="rewards-subtitle">Chuyển đổi điểm của bạn thành tiền mặt, thẻ quà tặng hoặc các phần
                        thưởng hấp dẫn khác.</p>
                    <div class="hero-stats d-flex gap-4 mt-4">
                        <div class="stat-item">
                            <div class="stat-value text-gradient" id="total-points-stat">1,250</div>
                            <div class="stat-label">Điểm Hiện Có</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="points-balance-card">
                    <div class="points-balance-content">
                        <div class="balance-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="balance-text">
                            <span class="balance-label">Tổng Điểm</span>
                            <div class="balance-amount" id="total-points">1,250</div>
                            <span class="balance-subtext">Bạn có thể đổi ngay!</span>
                        </div>
                        <div class="balance-decoration">
                            <span class="decoration-badge">Premium</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="rewards-content">
    <div class="container py-5">
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills rewards-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="cash-tab" data-bs-toggle="tab" data-bs-target="#cash-rewards"
                    type="button" role="tab" data-type="cash">
                    <i class="fas fa-wallet me-2"></i>Rút Tiền Mặt
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="giftcard-tab" data-bs-toggle="tab" data-bs-target="#giftcard-rewards" 
                    type="button" role="tab" data-type="giftcard">
                    <i class="fas fa-credit-card me-2"></i>Gift Card
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="physical-tab" data-bs-toggle="tab" data-bs-target="#physical-rewards" 
                    type="button" role="tab" data-type="physical">
                    <i class="fas fa-gift me-2"></i>Quà Tặng
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Cash Rewards Tab -->
            <div class="tab-pane fade show active" id="cash-rewards" role="tabpanel">
                <div class="row g-4" id="cash-rewards-list">
                    <div class="col-12 text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gift Card Rewards Tab -->
            <div class="tab-pane fade" id="giftcard-rewards" role="tabpanel">
                <div class="row g-4" id="giftcard-rewards-list">
                    <div class="col-12 text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physical Rewards Tab -->
            <div class="tab-pane fade" id="physical-rewards" role="tabpanel">
                <div class="row g-4" id="physical-rewards-list">
                    <div class="col-12 text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Exchange History -->
<section class="history-section">
    <div class="container py-5">
        <h2 class="section-title mb-4">Lịch sử Đổi Điểm</h2>
        <div class="table-responsive">
            <table class="table table-hover history-table">
                <thead>
                    <tr>
                        <th>Ngày Đổi</th>
                        <th>Loại</th>
                        <th>Điểm Tiêu</th>
                        <th>Giá Trị</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody id="history-table-body">
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="col-12 d-flex justify-content-center gap-2 mt-4" id="history-pagination-container">
        </div>
    </div>
</section>

<!-- modal xác nhận -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%); color: white; border: none;">
                <h5 class="modal-title" style="font-weight: 600;">Xác Nhận Đổi Điểm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="reward-info mb-3 p-3 rounded" style="background: #f8f9fa;">
                    <p class="mb-2">Bạn muốn đổi <strong id="modal-points">0</strong> điểm để nhận <strong id="modal-value">0</strong></p>
                    <p class="text-muted small mb-0">Điểm của bạn sẽ bị trừ sau khi xác nhận.</p>
                </div>

                <!-- Form nhập thông tin -->
                <form id="redemptionForm">
                    <!-- Thêm trường cho reward types khác nhau -->
                    <div id="additionalFields"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">
                    <span class="spinner-border spinner-border-sm d-none me-2" id="confirmSpinner" role="status" aria-hidden="true"></span>
                    Xác Nhận Đổi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal chi tiết Gift Card -->
<div class="modal fade" id="giftCardDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%); color: white; border: none;">
                <h5 class="modal-title" style="font-weight: 600; font-size: 1.3rem;">
                    <i class="fas fa-gift me-2"></i>Chi Tiết Thẻ Quà Tặng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <!-- Gift Card Info Card -->
                <div style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-group">
                                <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Tên Thẻ</small>
                                <p class="mt-2 mb-0" id="gc-name" style="font-size: 1.1rem; font-weight: 600; color: #333;">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Nhà Cung Cấp</small>
                                <p class="mt-2 mb-0" id="gc-provider" style="font-size: 1.1rem; font-weight: 600; color: #667eea;">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Giá Trị</small>
                                <p class="mt-2 mb-0" id="gc-value" style="font-size: 1.1rem; font-weight: 600; color: #28a745;">-</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <small class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Hạn Sử Dụng</small>
                                <p class="mt-2 mb-0" id="gc-expiry" style="font-size: 1.1rem; font-weight: 600; color: #e74c3c;">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mô Tả -->
                <div class="mb-3">
                    <small class="text-muted d-block mb-2" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Mô Tả</small>
                    <p class="mb-0" id="gc-description" style="color: #666; line-height: 1.6;">-</p>
                </div>

                <!-- Mã Thẻ Section -->
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">Mã Thẻ</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="gc-code" readonly style="border-radius: 6px 0 0 6px;">
                        <button class="btn btn-primary" type="button" id="copyCodeBtn" style="border-radius: 0 6px 6px 0; background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%); border: none;">
                            <i class="fas fa-copy me-1"></i>Copy
                        </button>
                    </div>
                </div>

                <!-- Số Serial Section -->
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; margin-bottom: 0.5rem;">Số Serial</label>
                    <input type="text" class="form-control" id="gc-serial" readonly style="border-radius: 6px; background: #f8f9fa;">
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #504242ff; padding: 1rem 2rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 6px; border: 1px solid #ddd;">Đóng</button>
                <button type="button" class="btn btn-primary" id="confirmGiftCardBtn" style="border-radius: 6px; background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%); border: none; padding: 0.6rem 1.5rem;">
                    <i class="fas fa-check me-2"></i>Xác Nhận Đã Nhận
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    const API_BASE = '/api';
    const rewardTypeIcons = {
        cash: '<i class="fas fa-coins"></i>',
        e_wallet: '<i class="fas fa-mobile"></i>',
        giftcard: '<i class="fas fa-gift"></i>',
        physical: '<i class="fas fa-box"></i>'
    };

    const rewardTypeClasses = {
        cash: 'reward-card--cash',
        e_wallet: 'reward-card--card',
        giftcard: 'reward-card--card',
        physical: 'reward-card--gift'
    };

    const rewardTypeButtons = {
        cash: 'btn-primary',
        e_wallet: 'btn-secondary-accent',
        giftcard: 'btn-secondary-accent',
        physical: 'btn-warning'
    };

    const providerLabels = {
        bank: 'Ngân Hàng',
        momo: 'MoMo',
        zalopay: 'ZaloPay',
        apple: 'Apple',
        steam: 'Steam',
        googleplay: 'Google Play'
    };

    document.addEventListener('DOMContentLoaded', function () {
        // Lấy thông tin điểm từ API
        loadUserPointsFromAPI();

        // Load rewards khi trang load
        loadRewards('cash');

        // Load redemption history
        loadRedemptionHistory();

        // Tab click handlers
        document.querySelectorAll('.rewards-tabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                const rewardType = this.dataset.type;
                loadRewards(rewardType);
            });
        });
    });

    let userBalance = 0; // Biến lưu số điểm

    function loadUserPointsFromAPI() {
        // Lấy user_id từ localStorage
        let userId = null;
        try {
            const raw = localStorage.getItem('app.user');
            if (raw) {
                const user = JSON.parse(raw);
                userId = user.id;
            }
        } catch (e) {
            console.error('Error parsing user from localStorage:', e);
        }

        if (!userId) {
            loadUserPoints();
            return;
        }

        fetch(`${API_BASE}/user-points/balance?user_id=${userId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                userBalance = data.data.balance || 0;

                // Render vào card
                document.getElementById('total-points').textContent = userBalance.toLocaleString('vi-VN');
                document.getElementById('total-points-stat').textContent = userBalance.toLocaleString('vi-VN');
            } else {
                // Fallback: lấy từ localStorage
                loadUserPoints();
            }
        })
        .catch(error => {
            console.error('Error loading user points from API:', error);
            // Fallback: lấy từ localStorage
            loadUserPoints();
        });
    }

    function loadUserPoints() {
        try {
            const raw = localStorage.getItem('app.user');
            if (raw) {
                const user = JSON.parse(raw);
                userBalance = user.points || 1250;

                document.getElementById('total-points').textContent = userBalance.toLocaleString('vi-VN');
                document.getElementById('total-points-stat').textContent = userBalance.toLocaleString('vi-VN');
            }
        } catch (e) {
            console.error('Error reading user data:', e);
        }
    }

    const ITEMS_PER_PAGE = 6;
    let allRewardsData = {}; // Cache để lưu dữ liệu

    function loadRewards(type, page = 1) {
        const containerId = `${type}-rewards-list`;
        const container = document.getElementById(containerId);

        if (!container) return;

        // Kiểm tra cache trước
        if (allRewardsData[type]) {
            renderRewardsList(type, page, allRewardsData[type]);
            return;
        }

        // Hiển thị loading
        container.innerHTML = `
            <div class="col-12 text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        `;

        // Gọi API
        fetch(`${API_BASE}/rewards?type=${type}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load rewards');
                return response.json();
            })
            .then(data => {
                if (!Array.isArray(data)) {
                    data = [];
                }
                
                // Lưu cache
                allRewardsData[type] = data;
                renderRewardsList(type, page, data);
            })
            .catch(error => {
                console.error('Error loading rewards:', error);
                container.innerHTML = `
                    <div class="col-12 text-center">
                        <p class="text-danger">Lỗi tải dữ liệu</p>
                    </div>
                `;
            });
    }

    function renderRewardsList(type, page, data) {
        const containerId = `${type}-rewards-list`;
        const container = document.getElementById(containerId);

        if (!container) return;

        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center">
                    <p class="text-muted">Không có phần thưởng nào</p>
                </div>
            `;
            return;
        }

        // Phân trang
        const totalPages = Math.ceil(data.length / ITEMS_PER_PAGE);
        const start = (page - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const paginatedData = data.slice(start, end);

        // Hiển thị dữ liệu
        let html = paginatedData.map(reward => createRewardCard(reward)).join('');
        
        if (totalPages > 1) {
            html += `
                <div class="col-12 d-flex justify-content-center gap-2 mt-4">
                    ${page > 1 ? `<button class="btn btn-sm btn-outline-primary" onclick="loadRewards('${type}', ${page - 1})">← Trước</button>` : ''}
                    <span class="btn btn-sm btn-light disabled">Trang ${page}/${totalPages}</span>
                    ${page < totalPages ? `<button class="btn btn-sm btn-outline-primary" onclick="loadRewards('${type}', ${page + 1})">Tiếp →</button>` : ''}
                </div>
            `;
        }
        
        // Set HTML once
        container.innerHTML = html;

        // Attach event listeners
        container.querySelectorAll('.reward-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const rewardId = this.dataset.rewardId;
                const reward = data.find(r => r.id == rewardId);
                if (reward) handleRewardClick(reward, this);
            });
        });
    }

    function createRewardCard(reward) {
        const icon = rewardTypeIcons[reward.type] || '<i class="fas fa-gift"></i>';
        const cardClass = rewardTypeClasses[reward.type] || 'reward-card--card';
        const buttonClass = rewardTypeButtons[reward.type] || 'btn-primary';
        
        let rewardName = reward.name;
        let rewardDesc = reward.description || 'Phần thưởng hấp dẫn';
        let displayValue = reward.value ? `${(reward.value / 1000).toLocaleString('vi-VN')} đ` : reward.name;

        return `
            <div class="col-md-6 col-lg-4">
                <div class="reward-card ${cardClass}">
                    <div class="reward-badge">${reward.provider ? providerLabels[reward.provider] || reward.provider : 'Hot'}</div>
                    <div class="reward-icon">
                        ${icon}
                    </div>
                    <h3 class="reward-title">${rewardName}</h3>
                    <p class="reward-price"><strong>${reward.point_cost.toLocaleString('vi-VN')}</strong> điểm</p>
                    <p class="reward-desc">${rewardDesc}</p>
                    ${reward.type === 'physical' && reward.stock !== null ? `
                        <small class="text-muted d-block mb-2">Kho: <strong>${reward.stock}</strong></small>`
                    : ''}
                    <button class="btn ${buttonClass} w-100 reward-btn" 
                        data-reward-id="${reward.id}"
                        data-points="${reward.point_cost}" 
                        data-amount="${reward.value || reward.name}"
                        data-type="${reward.type}">
                        <i class="fas fa-arrow-right me-2"></i>Đổi Ngay
                    </button>
                </div>
            </div>
        `;
    }

    function handleRewardClick(reward, btn) {
        const rewardId = btn.dataset.rewardId;
        const points = btn.dataset.points;
        const amount = btn.dataset.amount;
        const type = btn.dataset.type;

        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

        document.getElementById('modal-points').textContent = points;
        document.getElementById('modal-value').textContent = amount;

        // Thêm fields tùy theo loại reward
        renderAdditionalFields(type);

        // Xóa listener cũ
        const oldConfirmBtn = document.getElementById('confirmBtn');
        const newConfirmBtn = oldConfirmBtn.cloneNode(true);
        oldConfirmBtn.parentNode.replaceChild(newConfirmBtn, oldConfirmBtn);

        // Add listener mới
        newConfirmBtn.addEventListener('click', function () {
            redeemReward(rewardId, points, type);
        });

        confirmModal.show();
    }

    function renderAdditionalFields(type) {
        const container = document.getElementById('additionalFields');
        container.innerHTML = '';

        // Thêm các field tùy theo loại reward
        if (type === 'cash') {
            container.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Tên Ngân Hàng <span class="text-danger">*</span></label>
                    <select class="form-select" id="bankName" name="bank_name" required>
                        <option value="">Chọn ngân hàng</option>
                        <option value="VCB">Vietcombank (VCB)</option>
                        <option value="TCB">Techcombank (TCB)</option>
                        <option value="STB">Sacombank (STB)</option>
                        <option value="VIB">Việt Á Bank (VIB)</option>
                        <option value="SHB">SHB Bank</option>
                        <option value="MB">MB Bank</option>
                        <option value="ACB">ACB Bank</option>
                        <option value="TPB">TPBank</option>
                        <option value="BIDV">BIDV</option>
                        <option value="AGRI">Agribank</option>
                        <option value="momo">MoMo</option>
                        <option value="zalopay">ZaloPay</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số Tài Khoản / SĐT <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="accountNumber" name="account_number" 
                        placeholder="Nhập số tài khoản hoặc số điện thoại" required>
                    <small class="text-muted d-block mt-1">VD: 0123456789 (Tài khoản) hoặc 0901234567 (SĐT)</small>
                </div>
            `;
        } else if (type === 'giftcard') {
            // Không cần nhập email cho giftcard
            container.innerHTML = '';
        } else if (type === 'physical') {
            container.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Thông Tin Nhận Quà <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="receiverInfo" name="receiver_info" rows="3" 
                        placeholder="Nhập họ tên, số điện thoại, địa chỉ nhận hàng..." required></textarea>
                    <small class="text-muted d-block mt-1">Thông tin này sẽ được dùng để gửi quà cho bạn</small>
                </div>
            `;
        }
    }

    function redeemReward(rewardId, points, type) {
        // Kiểm tra form validation
        const form = document.getElementById('redemptionForm');
        if (!form.checkValidity()) {
            showToast('warning', 'Vui lòng điền đầy đủ thông tin');
            return;
        }

        // Prepare data object
        let postData = {
            reward_id: rewardId,
            receiver_info: '',
            user_id: null
        };

        // Add bank_name and account_number if available
        if (type === 'cash') {
            const bankName = document.getElementById('bankName')?.value;
            const accountNumber = document.getElementById('accountNumber')?.value;
            
            if (!bankName || !accountNumber) {
                showToast('warning', 'Vui lòng chọn ngân hàng và nhập số tài khoản');
                return;
            }
            
            if (bankName) postData.bank_name = bankName;
            if (accountNumber) postData.account_number = accountNumber;
        } else if (type === 'physical') {
            const receiverInfo = document.getElementById('receiverInfo')?.value;
            if (!receiverInfo) {
                showToast('warning', 'Vui lòng nhập thông tin nhận quà');
                return;
            }
            postData.receiver_info = receiverInfo;
        }

        // Get user_id
        try {
            const raw = localStorage.getItem('app.user');
            if (raw) {
                const user = JSON.parse(raw);
                postData.user_id = user.id;
            }
        } catch (e) {
            console.error('Error parsing user:', e);
        }

        // Show loading spinner
        const confirmBtn = document.getElementById('confirmBtn');
        const spinner = document.getElementById('confirmSpinner');
        confirmBtn.disabled = true;
        spinner.classList.remove('d-none');

        fetch(`${API_BASE}/redemptions/create`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(postData)
        })
        .then(response => response.json())
        .then(data => {
            spinner.classList.add('d-none');
            confirmBtn.disabled = false;

            if (data.success) {
                // Cập nhật số điểm
                if (data.data && data.data.user_balance !== undefined) {
                    userBalance = data.data.user_balance;
                    
                    document.getElementById('total-points').textContent = userBalance.toLocaleString('vi-VN');
                    document.getElementById('total-points-stat').textContent = userBalance.toLocaleString('vi-VN');
                    
                    // Lưu vào localStorage
                    const raw = localStorage.getItem('app.user');
                    if (raw) {
                        const user = JSON.parse(raw);
                        user.points = userBalance;
                        localStorage.setItem('app.user', JSON.stringify(user));
                    }
                }

                // Close confirm modal
                const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
                if (confirmModal) {
                    confirmModal.hide();
                }

                // Nếu là giftcard, hiển thị chi tiết gift card
                if (type === 'giftcard') {
                    setTimeout(() => {
                        showGiftCardDetails(rewardId, points, type);
                    }, 300);
                } else {
                    showToast('success', 'Đổi điểm thành công! Vui lòng chờ xác nhận.');
                    // Reload rewards list
                    const activeTab = document.querySelector('.rewards-tabs .nav-link.active');
                    if (activeTab) {
                        loadRewards(activeTab.dataset.type);
                    }
                }
            } else {
                showToast('error', data.message || 'Lỗi đổi điểm');
            }
        })
        .catch(error => {
            console.error('Error redeeming reward:', error);
            spinner.classList.add('d-none');
            confirmBtn.disabled = false;
            showToast('error', 'Lỗi: ' + error.message);
        });
    }

    // ====== Gift Card Modal Functions ======
    function showGiftCardDetails(rewardId, points, type) {
        // Hiển thị loading
        showGiftCardLoading(true);

        // Gọi API lấy chi tiết gift card
        const apiUrl = `${API_BASE}/rewards/giftcard/details?reward_id=${rewardId}`;
        
        fetch(apiUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            return response.json();
        })
        .then(data => {
            showGiftCardLoading(false);

            if (data.success && data.data) {
                const giftcard = data.data;

                // Điền dữ liệu vào modal
                document.getElementById('gc-name').textContent = giftcard.name || '-';
                document.getElementById('gc-provider').textContent = giftcard.provider ? formatProvider(giftcard.provider) : '-';
                document.getElementById('gc-value').textContent = giftcard.value ? formatCurrency(giftcard.value) : '-';
                document.getElementById('gc-code').value = giftcard.code || '-';
                document.getElementById('gc-serial').value = giftcard.serial || '-';
                document.getElementById('gc-expiry').textContent = giftcard.expiry_date ? formatDate(giftcard.expiry_date) : '-';
                document.getElementById('gc-description').textContent = giftcard.description || '-';

                // Lưu rewardId để dùng khi confirm
                document.getElementById('confirmGiftCardBtn').dataset.rewardId = rewardId;
                document.getElementById('confirmGiftCardBtn').dataset.points = points;

                // Close confirm modal, open gift card detail modal
                const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
                if (confirmModal) {
                    confirmModal.hide();
                }

                // Mở modal chi tiết sau khi modal confirm đóng
                setTimeout(() => {
                    const detailModal = new bootstrap.Modal(document.getElementById('giftCardDetailModal'));
                    detailModal.show();

                    // Xử lý copy code button
                    document.getElementById('copyCodeBtn').onclick = function() {
                        const code = document.getElementById('gc-code').value;
                        navigator.clipboard.writeText(code).then(() => {
                            showToast('success', 'Đã copy mã thẻ!');
                        }).catch(err => {
                            console.error('Error copying:', err);
                        });
                    };

                    // Xử lý confirm button
                    const confirmBtn = document.getElementById('confirmGiftCardBtn');
                    const newConfirmBtn = confirmBtn.cloneNode(true);
                    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                    newConfirmBtn.addEventListener('click', function() {
                        confirmGiftCardRedemption(rewardId, points);
                    });
                }, 200);
            } else {
                showToast('error', data.message || 'Không thể tải chi tiết thẻ');
                showGiftCardLoading(false);
            }
        })
        .catch(error => {
            console.error('Error loading gift card details:', error);
            showToast('error', 'Lỗi: ' + error.message);
            showGiftCardLoading(false);
        });
    }

    function showGiftCardLoading(show) {
        // Có thể thêm loading indicator nếu cần
    }

    function formatCurrency(value) {
        return (value).toLocaleString('vi-VN') + ' đ';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    function formatProvider(provider) {
        const providers = {
            'viettel': 'Viettel',
            'mobifone': 'Mobifone',
            'vinaphone': 'Vinaphone',
            'apple': 'Apple',
            'google': 'Google Play',
            'garena': 'Garena'
        };
        return providers[provider] || provider;
    }

    function confirmGiftCardRedemption(rewardId, points) {
        // Đóng gift card detail modal
        const detailModal = bootstrap.Modal.getInstance(document.getElementById('giftCardDetailModal'));
        if (detailModal) {
            detailModal.hide();
        }

        // Reload rewards list
        const activeTab = document.querySelector('.rewards-tabs .nav-link.active');
        if (activeTab) {
            loadRewards(activeTab.dataset.type);
        }
    }

    // ====== Load Redemption History ======
    let currentHistoryPage = 1;

    function loadRedemptionHistory(page = 1) {
        const tableBody = document.getElementById('history-table-body');
        
        // Lấy user_id từ localStorage
        let userId = null;
        try {
            const raw = localStorage.getItem('app.user');
            if (raw) {
                const user = JSON.parse(raw);
                userId = user.id;
            }
        } catch (e) {
            console.error('Error parsing user:', e);
        }

        if (!userId) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">Bạn chưa đăng nhập</td>
                </tr>
            `;
            return;
        }
        
        fetch(`${API_BASE}/redemptions/my?user_id=${userId}&page=${page}&limit=10`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                const html = data.data.map(redemption => {
                    const date = new Date(redemption.created_at);
                    const dateStr = date.toLocaleDateString('vi-VN');
                    
                    // Xác định loại reward từ type
                    const typeName = getRedemptionTypeName(redemption.type, redemption.provider);
                    const typeColor = getRedemptionTypeColor(redemption.type);
                    
                    // Xác định trạng thái
                    const statusBadge = getStatusBadge(redemption.status);
                    
                    // Lấy điểm tiêu từ point_cost
                    const points = redemption.point_cost ? redemption.point_cost.toLocaleString('vi-VN') : '-';
                    
                    // Lấy giá trị từ value - nếu là physical thì lấy tên quà tặng
                    let value = '-';
                    if (redemption.type === 'physical' && redemption.name) {
                        value = redemption.name;
                    } else if (redemption.value) {
                        value = formatValue(redemption.value);
                    }
                    
                    return `
                        <tr>
                            <td>${dateStr}</td>
                            <td><span class="badge ${typeColor}">${typeName}</span></td>
                            <td>${points}</td>
                            <td>${value}</td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                }).join('');
                
                tableBody.innerHTML = html;
                
                // Render pagination
                console.log('Pagination data:', data.pagination);
                if (data.pagination) {
                    renderHistoryPagination(data.pagination);
                }
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted">Chưa có lịch sử đổi điểm</td>
                    </tr>
                `;
                document.getElementById('history-pagination').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error loading history:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">Lỗi tải lịch sử</td>
                </tr>
            `;
        });
    }

    function renderHistoryPagination(pagination) {
        const container = document.getElementById('history-pagination-container');
        
        if (!pagination || pagination.pages <= 1) {
            container.innerHTML = '';
            return;
        }

        const currentPage = pagination.page;
        const totalPages = pagination.pages;
        
        let html = '';
        
        // Nút "Trước"
        if (currentPage > 1) {
            html += `<button class="btn btn-sm btn-outline-primary" onclick="loadRedemptionHistory(${currentPage - 1})">← Trước</button>`;
        }
        
        // Hiển thị trang hiện tại / tổng trang
        html += `<span class="btn btn-sm btn-light disabled">Trang ${currentPage}/${totalPages}</span>`;
        
        // Nút "Tiếp"
        if (currentPage < totalPages) {
            html += `<button class="btn btn-sm btn-outline-primary" onclick="loadRedemptionHistory(${currentPage + 1})">Tiếp →</button>`;
        }
        
        container.innerHTML = html;
    }

    function getRedemptionTypeName(type, provider) {
        const typeMap = {
            'cash': 'Rút Tiền',
            'e_wallet': provider ? (provider === 'momo' ? 'MoMo' : provider === 'zalopay' ? 'ZaloPay' : 'Ví Điện Tử') : 'Ví Điện Tử',
            'giftcard': 'Gift Card',
            'physical': 'Quà Tặng'
        };
        return typeMap[type] || 'Phần thưởng';
    }

    function getRedemptionTypeColor(type) {
        const colorMap = {
            'cash': 'bg-primary',
            'e_wallet': 'bg-secondary-accent',
            'giftcard': 'bg-secondary-accent',
            'physical': 'bg-warning'
        };
        return colorMap[type] || 'bg-secondary';
    }

    function getStatusBadge(status) {
        const statusMap = {
            'pending': '<span class="badge bg-warning text-dark">Chờ xử lý</span>',
            'processing': '<span class="badge bg-info">Đang xử lý</span>',
            'completed': '<span class="badge bg-success">Hoàn tất</span>',
            'rejected': '<span class="badge bg-danger">Từ chối</span>'
        };
        return statusMap[status] || '<span class="badge bg-secondary">Không xác định</span>';
    }

    function formatValue(value) {
        if (typeof value === 'number') {
            return value.toLocaleString('vi-VN') + ' đ';
        }
        return String(value);
    }
</script>