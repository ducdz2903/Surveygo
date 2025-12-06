<div id="vanta-bg" style="position: fixed; inset: 0; z-index: -1; pointer-events: none;"></div>
<section class="auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <div class="auth-icon mb-3">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2 class="auth-title">Đăng nhập</h2>
                    </div>

                    <form id="login-form"
                        action="<?= htmlspecialchars(rtrim((string) ($baseUrl ?? ''), '/') . '/api/login', ENT_QUOTES, 'UTF-8') ?>"
                        method="post">
                        <?php if (function_exists('csrf_field')) {
                            echo csrf_field();
                        } ?>

                        <!-- Tài khoản -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Tài khoản
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Nhập email hoặc số điện thoại của bạn" required autocomplete="email">
                        </div>
                        <!-- Mật khẩu -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mật khẩu
                            </label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Nhập mật khẩu" required autocomplete="current-password">
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Ghi nhớ & Quên mật khẩu -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>
                            <a href="#" class="forgot-link">Quên mật khẩu?</a>
                        </div>

                        <!-- Nút đăng nhập -->
                        <button type="submit" class="btn btn-gradient w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </button>

                        <!-- Form feedback -->
                        <div class="form-feedback mt-3" id="login-feedback"></div>
                    </form>

                    <!-- Liên kết đăng ký -->
                    <div class="text-center mt-4">
                        <p class="mb-0 small">Chưa có tài khoản? <a
                                href="<?= htmlspecialchars($urls['register'] ?? '/register', ENT_QUOTES, 'UTF-8') ?>"
                                class="signup-link">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
<script src="https://unpkg.com/vanta@latest/dist/vanta.net.min.js"></script>
<script src="/public/assets/js/toast-helper.js"></script>
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
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

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
</script>