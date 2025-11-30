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
                                <div class="activity-timeline">
                                    <div class="activity-item">
                                        <div class="activity-icon icon-success"> <i class="fas fa-poll"></i>
                                        </div>
                                        <div class="activity-content">
                                            <h6>Hoàn thành khảo sát "Thói quen tiêu dùng"</h6>
                                            <p>+ 50 điểm - 1 giờ trước</p>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon icon-secondary-accent"> <i
                                                class="fas fa-user-check"></i>
                                        </div>
                                        <div class="activity-content">
                                            <h6>Cập nhật thông tin cá nhân</h6>
                                            <p>2 ngày trước</p>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon icon-warning"> <i class="fas fa-gift"></i>
                                        </div>
                                        <div class="activity-content">
                                            <h6>Đổi 1000 điểm lấy thẻ cào 100k</h6>
                                            <p>5 ngày trước</p>
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
</div>
<script src="/public/assets/js/toast-helper.js"></script>
<script>
    const API_BASE = ''; // use relative paths; set to '' so fetch('/api/...') resolves to current origin
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

    // setup nhận sự kiện nút
    document.addEventListener('DOMContentLoaded', () => {
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