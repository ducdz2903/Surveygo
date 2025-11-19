<?php
/** @var string $appName */
/** @var array $urls */

$appName = $appName ?? 'Surveygo';
$urls = $urls ?? []; // Giả định $urls được truyền vào
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông tin tài khoản - <?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="public/assets/css/app.css">
    <link rel="stylesheet" href="public/assets/css/components/navbar.css">
    <link rel="stylesheet" href="public/assets/css/components/footer.css">
    <link rel="stylesheet" href="public/assets/css/client/profile.css">
</head>

<body class="page page--profile">

    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

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
                                    <button class="btn btn-primary-gradient btn-sm">Cập nhật</button>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Họ tên</label>
                                                <input type="text" class="form-control" value="Tên Người Dùng">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="user@email.com">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Số điện thoại</label>
                                                <input type="text" class="form-control" placeholder="Chưa cập nhật">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Giới tính</label>
                                                <select class="form-select">
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
                                    <button class="btn btn-primary-gradient btn-sm">Cập nhật</button>
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

    <?php include BASE_PATH . '/app/Views/components/client/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Lấy thông tin user từ localStorage khi trang load
        document.addEventListener('DOMContentLoaded', function () {
            try {
                const userJson = localStorage.getItem('app.user');
                if (userJson) {
                    const user = JSON.parse(userJson);

                    // Cập nhật thông tin cá nhân
                    if (user.name) {
                        document.querySelector('.user-fullname').textContent = user.name;
                        const fullNameInput = document.querySelector('input[value="Tên Người Dùng"]');
                        if (fullNameInput) {
                            fullNameInput.value = user.name;
                        }
                    }

                    if (user.email) {
                        const emailInput = document.querySelector('input[type="email"]');
                        if (emailInput) {
                            emailInput.value = user.email;
                        }
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
    </script>
</body>

</html>