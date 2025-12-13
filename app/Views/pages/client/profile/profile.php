<div class="user-info-section">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-4">
                <div class="profile-card">

                    <div class="text-center avatar-section">
                        <div class="avatar-wrapper">
                            <img src="https://ui-avatars.com/api/?name=T&background=ec4899&color=fff&size=150"
                                alt="Avatar" class="avatar-img">
                            <label for="avatar-upload" class="avatar-upload-btn" title="Đổi avatar">
                                <i class="fas fa-camera"></i>
                                <input type="file" id="avatar-upload" class="d-none">
                            </label>
                        </div>
                        <h5 class="user-fullname">Tên Người Dùng</h5>
                        <p class="member-since">Thành viên từ 01/01/2025</p>
                    </div>


                    <div class="user-stats">
                        <div class="stat-item">
                            <i class="fas fa-file-alt"></i>
                            <span class="stat-value">12</span>
                            <span class="stat-label">Khảo sát</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-coins"></i>
                            <span class="stat-value">150k</span>
                            <span class="stat-label">Điểm</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-check-circle"></i>
                            <span class="stat-value">8</span>
                            <span class="stat-label">Đã rút</span>
                        </div>
                    </div>


                    <nav class="profile-nav nav flex-column nav-pills">
                        <a class="nav-link active" href="#account" data-bs-toggle="tab">
                            <i class="fas fa-user-circle me-3"></i>Tài khoản
                        </a>
                        <a class="nav-link" href="#password" data-bs-toggle="tab">
                            <i class="fas fa-key me-3"></i>Đổi mật khẩu
                        </a>
                        <a class="nav-link" href="#activity" data-bs-toggle="tab">
                            <i class="fas fa-chart-line me-3"></i>Hoạt động
                        </a>
                    </nav>

                </div>
            </div>

            <div class="col-lg-8">
                <div class="tab-content">

                    <div class="tab-pane fade show active" id="account">
                        <div class="info-card">
                            <div class="card-header-custom">
                                <h5><i class="fas fa-user-edit me-2"></i>Thông tin cá nhân</h5>
                                <button id="btn-update-profile" class="btn btn-primary-gradient btn-sm">Cập
                                    nhật</button>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Họ tên</label>
                                            <input id="profile-name" type="text" class="form-control"
                                                value="Tên Người Dùng">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input id="profile-email" type="email" class="form-control"
                                                value="user@email.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Số điện thoại</label>
                                            <input id="profile-phone" type="text" class="form-control"
                                                placeholder="Chưa cập nhật">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Giới tính</label>
                                            <select id="profile-gender" class="form-select">
                                                <option value="male">Nam</option>
                                                <option value="female">Nữ</option>
                                                <option value="other">Khác</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="password">
                        <div class="info-card">
                            <div class="card-header-custom">
                                <h5><i class="fas fa-lock me-2"></i>Bảo mật & Mật khẩu</h5>
                                <button id="btn-update-password" class="btn btn-primary-gradient btn-sm">Cập
                                    nhật</button>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3 password-wrapper">
                                        <label class="form-label">Mật khẩu hiện tại</label>
                                        <input type="password" class="form-control password-input-current"
                                            id="current-password">
                                        <button type="button" class="password-toggle"
                                            onclick="togglePasswordField('current-password', 'toggle-current')"><i
                                                class="fas fa-eye" id="toggle-current"></i></button>
                                    </div>
                                    <div class="mb-3 password-wrapper">
                                        <label class="form-label">Mật khẩu mới</label>
                                        <input type="password" class="form-control password-input-new"
                                            id="new-password">
                                        <button type="button" class="password-toggle"
                                            onclick="togglePasswordField('new-password', 'toggle-new')"><i
                                                class="fas fa-eye" id="toggle-new"></i></button>
                                    </div>
                                    <div class="mb-3 password-wrapper">
                                        <label class="form-label">Xác nhận mật khẩu mới</label>
                                        <input type="password" class="form-control password-input-confirm"
                                            id="confirm-password">
                                        <button type="button" class="password-toggle"
                                            onclick="togglePasswordField('confirm-password', 'toggle-confirm')"><i
                                                class="fas fa-eye" id="toggle-confirm"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="activity">
                        <div class="info-card">
                            <div class="card-header-custom">
                                <h5><i class="fas fa-history me-2"></i>Lịch sử hoạt động</h5>
                            </div>
                            <div class="card-body">
                                <div id="activity-timeline-container" class="activity-timeline">
                                    <div class="text-center text-muted py-5">
                                        <p><i class="fas fa-spinner fa-spin me-2"></i>Đang tải...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="/public/assets/js/toast-helper.js"></script>
<script>
    const API_BASE = ''; // use relative paths; set to '' so fetch('/api/...') resolves to current origin
    
    // Function to load and update user statistics
    async function loadUserStatistics() {
        try {
            const userJson = localStorage.getItem('app.user');
            if (!userJson) {
                console.warn('No user data found in localStorage');
                return;
            }
            
            const user = JSON.parse(userJson);
            const userId = user.id;
            
            // Fetch user statistics from API
            const response = await fetch(`/api/users/profile-stats?user_id=${userId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch user statistics');
            }
            
            const result = await response.json();
            
            if (result.success && result.data) {
                const { completed_surveys, current_points, redemptions_count } = result.data;
                
                // Update the statistics in the UI
                const statItems = document.querySelectorAll('.stat-item');
                
                // Update completed surveys (first stat item)
                if (statItems[0]) {
                    const surveyValue = statItems[0].querySelector('.stat-value');
                    if (surveyValue) {
                        surveyValue.textContent = completed_surveys;
                    }
                }
                
                // Update current points (second stat item)
                if (statItems[1]) {
                    const pointsValue = statItems[1].querySelector('.stat-value');
                    if (pointsValue) {
                        // Format points with 'k' suffix if >= 1000
                        const formattedPoints = current_points >= 1000 
                            ? `${(current_points / 1000).toFixed(0)}k` 
                            : current_points.toString();
                        pointsValue.textContent = formattedPoints;
                    }
                }
                
                // Update redemptions count (third stat item)
                if (statItems[2]) {
                    const redemptionsValue = statItems[2].querySelector('.stat-value');
                    if (redemptionsValue) {
                        redemptionsValue.textContent = redemptions_count;
                    }
                }
                
                console.log('User statistics updated:', result.data);
            } else {
                console.error('Invalid response format:', result);
            }
        } catch (error) {
            console.error('Error loading user statistics:', error);
        }
    }

    // Lấy thông tin user từ localStorage khi trang load
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const userJson = localStorage.getItem('app.user');
            if (userJson) {
                const user = JSON.parse(userJson);

                // Cập nhật thông tin cá nhân
                if (user.name) {
                    document.querySelector('.user-fullname').textContent = user.name;
                    const fullNameInput = document.getElementById('profile-name');
                    if (fullNameInput) {
                        fullNameInput.value = user.name;
                    }
                }

                if (user.email) {
                    const emailInput = document.getElementById('profile-email');
                    if (emailInput) {
                        emailInput.value = user.email;
                    }
                }

                // Số điện thoại
                const phoneInput = document.getElementById('profile-phone');
                if (phoneInput) {
                    phoneInput.value = user.phone || '';
                }

                // Giới tính
                const genderSelect = document.getElementById('profile-gender');
                if (genderSelect) {
                    // set value only if provided and matches option
                    if (user.gender && Array.from(genderSelect.options).some(o => o.value === user.gender)) {
                        genderSelect.value = user.gender;
                    }

                    // update localStorage when user changes selection
                    genderSelect.addEventListener('change', function () {
                        try {
                            const raw = localStorage.getItem('app.user');
                            if (!raw) return;
                            const u = JSON.parse(raw);
                            u.gender = this.value;
                            localStorage.setItem('app.user', JSON.stringify(u));
                            console.log('Cập nhật giới tính trong localStorage:', u.gender);
                        } catch (e) {
                            console.error('Lỗi khi cập nhật giới tính:', e);
                        }
                    });
                }

                if (phoneInput) {
                    phoneInput.addEventListener('change', function () {
                        try {
                            const raw = localStorage.getItem('app.user');
                            if (!raw) return;
                            const u = JSON.parse(raw);
                            u.phone = this.value || null;
                            localStorage.setItem('app.user', JSON.stringify(u));
                            console.log('Cập nhật phone trong localStorage:', u.phone);
                        } catch (e) {
                            console.error('Lỗi khi cập nhật số điện thoại:', e);
                        }
                    });
                }

                // Cập nhật avatar với tên user
                if (user.name) {
                    const avatarImg = document.querySelector('.avatar-img');
                    if (avatarImg) {
                        const encodedName = encodeURIComponent(user.name);
                        avatarImg.src = `https://ui-avatars.com/api/?name=${encodedName}&background=ec4899&color=fff&size=150`;
                    }
                }

                // Cập nhật member since date
                if (user.created_at) {
                    const createdDate = new Date(user.created_at);
                    const monthYear = createdDate.toLocaleDateString('vi-VN', { year: 'numeric', month: '2-digit' });
                    const memberSince = document.querySelector('.member-since');
                    if (memberSince) {
                        memberSince.textContent = `Thành viên từ ${monthYear}`;
                    }
                }
                
                // Load user statistics (surveys, points, redemptions)
                loadUserStatistics();

                console.log('Thông tin user đã được cập nhật:', user);
            } else {
                console.warn('Không tìm thấy thông tin user trong localStorage');
            }
        } catch (error) {
            console.error('Lỗi khi đọc thông tin user:', error);
        }
    });

    // Function toggle password visibility
    function togglePasswordField(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    // Helper: convert file to base64 data URL
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = (err) => reject(err);
            reader.readAsDataURL(file);
        });
    }

    // Load activity logs từ API
    async function loadActivityLogs() {
        try {
            const response = await fetch(`/api/activity-logs/my?limit=10`);
            if (!response.ok) throw new Error('Failed to fetch activity logs');
            
            const result = await response.json();
            const container = document.getElementById('activity-timeline-container');
            
            if (!result.success || !result.data || result.data.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-5"><p>Chưa có hoạt động nào.</p></div>';
                return;
            }

            const actionIcons = {
                'survey_submitted': { icon: 'fas fa-check-circle', class: 'icon-success' },
                'participated_event': { icon: 'fas fa-calendar-check', class: 'icon-primary' },
                'reward_redeemed': { icon: 'fas fa-gift', class: 'icon-accent' },
                'survey_created': { icon: 'fas fa-plus-circle', class: 'icon-success' },
                'event_created': { icon: 'fas fa-calendar-plus', class: 'icon-primary' },
                'question_created': { icon: 'fas fa-lightbulb', class: 'icon-accent' },
                'profile_updated': { icon: 'fas fa-user-edit', class: 'icon-secondary-accent' },
            };

            let html = result.data.map(activity => {
                const iconData = actionIcons[activity.action] || { icon: 'fas fa-circle', class: 'icon-secondary' };
                
                const timeDate = new Date(activity.created_at);
                const now = new Date();
                const diffMs = now - timeDate;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);
                
                let timeStr = '';
                if (diffMins < 1) timeStr = 'Vừa xong';
                else if (diffMins < 60) timeStr = `${diffMins} phút trước`;
                else if (diffHours < 24) timeStr = `${diffHours} giờ trước`;
                else if (diffDays < 30) timeStr = `${diffDays} ngày trước`;
                else timeStr = timeDate.toLocaleDateString('vi-VN');

                return `
                    <div class="activity-item">
                        <div class="activity-icon ${iconData.class}">
                            <i class="${iconData.icon}"></i>
                        </div>
                        <div class="activity-content">
                            <h6>${activity.description || activity.action}</h6>
                            <p>${timeStr}</p>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = html;
        } catch (error) {
            console.error('Lỗi khi tải activity logs:', error);
            const container = document.getElementById('activity-timeline-container');
            container.innerHTML = '<div class="text-center text-danger py-5"><p>Lỗi khi tải hoạt động.</p></div>';
        }
    }

    // setup nhận sự kiện nút
    document.addEventListener('DOMContentLoaded', () => {
        // Load activity logs khi tab được click
        const activityTab = document.querySelector('a[href="#activity"]');
        if (activityTab) {
            activityTab.addEventListener('click', loadActivityLogs);
            // Nếu tab activity đã active, load ngay
            if (activityTab.classList.contains('active')) {
                loadActivityLogs();
            }
        }
        

        const avatarInput = document.getElementById('avatar-upload');
        const avatarImg = document.querySelector('.avatar-img');
        let selectedAvatarData = null; // base64 string cho ảnh đại diện

        if (avatarInput) {
            avatarInput.addEventListener('change', async (e) => {
                const file = e.target.files[0];
                if (!file) return;
                try {
                    const dataUrl = await fileToBase64(file);
                    selectedAvatarData = dataUrl;
                    if (avatarImg) avatarImg.src = dataUrl;
                } catch (err) {
                    console.error('Lỗi đọc file avatar:', err);
                    showToast('error', 'Không thể đọc file ảnh. Vui lòng thử lại.');
                }
            });
        }

        const btnUpdateProfile = document.getElementById('btn-update-profile');
        if (btnUpdateProfile) {
            btnUpdateProfile.addEventListener('click', async () => {
                try {
                    const stored = localStorage.getItem('app.user');
                    if (!stored) return showToast('warning', 'Vui lòng đăng nhập trước khi cập nhật.');
                    const currentUser = JSON.parse(stored);

                    const payload = {
                        id: currentUser.id,
                        name: document.getElementById('profile-name').value.trim(),
                        email: document.getElementById('profile-email').value.trim(),
                        phone: (document.getElementById('profile-phone') ? document.getElementById('profile-phone').value.trim() : ''),
                        gender: document.getElementById('profile-gender').value || '',
                    };

                    if (selectedAvatarData) {
                        payload.avatar = selectedAvatarData;
                    }

                    // Email validation
                    if (!payload.name) return showToast('warning', 'Vui lòng nhập họ tên.');
                    if (!payload.email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(payload.email)) return showToast('warning', 'Email không hợp lệ.');

                    const res = await fetch(`${API_BASE}/api/auth/update-profile`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                    let data;
                    try {
                        data = await res.json();
                    } catch (e) {
                        console.error('Response is not JSON:', e);
                        return showToast('error', 'Máy chủ trả về phản hồi không mong muốn. Vui lòng thử lại.');
                    }
                    if (!res.ok || data.error) {
                        return showToast('error', data.message || 'Cập nhật thất bại.');
                    }
                    showToast('success', 'Cập nhật thông tin thành công. Vui lòng đăng nhập lại.');
                    try {
                        localStorage.removeItem('app.user');
                    } catch (e) {
                        console.warn('Không thể xóa app.user từ localStorage:', e);
                    }
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 800);
                } catch (err) {
                    console.error(err);
                    showToast('error', 'Đã có lỗi xảy ra khi cập nhật.');
                }
            });
        }

        const btnUpdatePassword = document.getElementById('btn-update-password');
        if (btnUpdatePassword) {
            btnUpdatePassword.addEventListener('click', async () => {
                try {
                    const stored = localStorage.getItem('app.user');
                    if (!stored) return showToast('warning', 'Vui lòng đăng nhập trước khi đổi mật khẩu.');
                    const currentUser = JSON.parse(stored);

                    const payload = {
                        id: currentUser.id,
                        current_password: document.getElementById('current-password').value,
                        new_password: document.getElementById('new-password').value,
                        confirm_password: document.getElementById('confirm-password').value,
                    };

                    if (!payload.current_password || !payload.new_password || !payload.confirm_password) return showToast('warning', 'Vui lòng điền đầy đủ các trường mật khẩu.');
                    if (payload.new_password.length < 6) return showToast('warning', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
                    if (payload.new_password !== payload.confirm_password) return showToast('warning', 'Mật khẩu mới không khớp.');

                    const res = await fetch(`${API_BASE}/api/auth/change-password`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload),
                    });
                    let data;
                    try {
                        data = await res.json();
                    } catch (e) {
                        console.error('Response is not JSON:', e);
                        return showToast('error', 'Máy chủ trả về phản hồi không mong muốn. Vui lòng thử lại.');
                    }
                    if (!res.ok || data.error) {
                        return showToast('error', data.message || 'Đổi mật khẩu thất bại.');
                    }

                    document.getElementById('current-password').value = '';
                    document.getElementById('new-password').value = '';
                    document.getElementById('confirm-password').value = '';
                    showToast('success', 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.');
                    try {
                        localStorage.removeItem('app.user');
                    } catch (e) {
                        console.warn('Không thể xóa app.user từ localStorage:', e);
                    }
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 800);
                } catch (err) {
                    console.error(err);
                    showToast('error', 'Đã có lỗi xảy ra khi đổi mật khẩu.');
                }
            });
        }
    });
</script>