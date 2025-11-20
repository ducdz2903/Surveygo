<?php
/** @var string $appName */
/** @var array $urls */

$urls = $urls ?? [];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(($appName ?? 'PHP Application') . ' - Đăng ký', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- custom css -->
    <link rel="stylesheet" href="public/assets/css/auth/register.css">
    <link rel="stylesheet" href="public/assets/css/app.css">
    <link rel="stylesheet" href="public/assets/css/components/footer.css">
    <link rel="stylesheet" href="public/assets/css/components/navbar.css">

    <style>
        <?php @include __DIR__ . '/../register/style.css'; ?>
    </style>
</head>

<body class="page page--auth">
    <?php include BASE_PATH . '/app/Views/components/client/_navbar.php'; ?>

    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="auth-card">
                        <div class="text-center">
                            <div class="auth-icon mb-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h2 class="auth-title">Đăng ký tài khoản</h2>
                        </div>

                        <form id="register-form"
                            action="<?= htmlspecialchars(rtrim((string) ($baseUrl ?? ''), '/') . '/api/register', ENT_QUOTES, 'UTF-8') ?>"
                            method="post">
                            <?php if (function_exists('csrf_field')) {
                                echo csrf_field();
                            } ?>

                            <!-- Họ và tên -->
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Họ và tên
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Nhập họ và tên" required autocomplete="name">
                            </div>

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="register-email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="register-email" name="email"
                                    placeholder="Nhập email của bạn" required autocomplete="email">
                            </div>

                            <!-- Mật khẩu -->
                            <div class="form-group mb-3">
                                <label for="register-password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Mật khẩu
                                </label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control" id="register-password" name="password"
                                        placeholder="Nhập mật khẩu (ít nhất 6 ký tự)" required minlength="6"
                                        autocomplete="new-password">
                                    <button type="button" class="password-toggle"
                                        onclick="togglePassword('register-password', 'toggleIconPassword')">
                                        <i class="fas fa-eye" id="toggleIconPassword"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Xác nhận mật khẩu -->
                            <div class="form-group mb-3">
                                <label for="confirm-password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu
                                </label>
                                <div class="password-wrapper">
                                    <input type="password" class="form-control" id="confirm-password"
                                        placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                                    <button type="button" class="password-toggle"
                                        onclick="togglePassword('confirm-password', 'toggleIconConfirm')">
                                        <i class="fas fa-eye" id="toggleIconConfirm"></i>
                                    </button>
                                </div>
                                <small class="text-danger" id="passwordMatchError"></small>
                            </div>

                            <!-- Điều khoản & Điều kiện -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Tôi đồng ý với <a href="/terms-of-use" class="terms-link">Điều khoản sử dụng</a> và
                                    <a href="#" class="terms-link">Chính sách bảo mật</a>
                                </label>
                            </div>

                            <!-- Nút đăng ký -->
                            <button type="submit" class="btn btn-gradient w-100 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký
                            </button>

                            <!-- Form feedback -->
                            <div class="form-feedback mt-3" id="register-feedback"></div>
                        </form>

                        <!-- Liên kết đăng nhập -->
                        <div class="text-center mt-4">
                            <p class="mb-0 small">Đã có tài khoản? <a
                                    href="<?= htmlspecialchars($urls['login'] ?? '/login', ENT_QUOTES, 'UTF-8') ?>"
                                    class="signup-link">Đăng nhập ngay</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include BASE_PATH . '/app/Views/partials/_footer.php'; ?>

    <!-- Vanta Background -->
    <div id="vanta-bg" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.net.min.js"></script>
    <script>
        // Khởi tạo Vanta Net
        window.addEventListener('load', function () {
            if (typeof VANTA !== 'undefined') {
                VANTA.NET({
                    el: "#vanta-bg",
                    mouseControls: true,
                    touchControls: true,
                    gyroControls: false,
                    minHeight: 200.00,
                    minWidth: 200.00,
                    scale: 1.00,
                    scaleMobile: 1.00,
                    color: 0x10BCD3,
                    backgroundColor: 0x0a1428,
                    points: 15.00,
                    maxDistance: 20.00,
                    spacing: 15.00
                });
            }
        });

        // Ẩn/hiện mật khẩu
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Kiểm tra khớp mật khẩu
        document.getElementById('confirm-password').addEventListener('input', function (e) {
            const password = document.getElementById('register-password').value;
            const confirmPassword = e.target.value;
            const errorDiv = document.getElementById('passwordMatchError');

            if (confirmPassword.length > 0) {
                if (password !== confirmPassword) {
                    errorDiv.textContent = 'Mật khẩu không khớp';
                } else {
                    errorDiv.textContent = '';
                }
            } else {
                errorDiv.textContent = '';
            }
        });
    </script>
    <script><?php @include __DIR__ . '/script.js'; ?></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>