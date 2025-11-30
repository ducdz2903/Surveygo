<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Cài đặt hệ thống</h4>
            <p class="text-muted mb-0">Quản lý các cấu hình cốt lõi của ứng dụng</p>
        </div>
        <button class="btn btn-outline-secondary" onclick="clearCache()">
            <i class="fas fa-sync-alt me-2"></i>Xóa Cache hệ thống
        </button>
    </div>

    <div class="row g-4 fade-in">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-2">
                    <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-3" id="tab-general-btn" data-bs-toggle="pill"
                            data-bs-target="#tab-general" type="button" role="tab">
                            <i class="fas fa-sliders-h me-2 text-primary" style="width: 20px;"></i> Chung
                        </button>
                        <button class="nav-link text-start py-3" id="tab-email-btn" data-bs-toggle="pill" data-bs-target="#tab-email"
                            type="button" role="tab">
                            <i class="fas fa-envelope me-2 text-success" style="width: 20px;"></i> Email & SMTP
                        </button>
                        <button class="nav-link text-start py-3" id="tab-security-btn" data-bs-toggle="pill"
                            data-bs-target="#tab-security" type="button" role="tab">
                            <i class="fas fa-shield-alt me-2 text-danger" style="width: 20px;"></i> Bảo mật
                        </button>
                        <button class="nav-link text-start py-3" id="tab-api-btn" data-bs-toggle="pill" data-bs-target="#tab-api"
                            type="button" role="tab">
                            <i class="fas fa-code me-2 text-info" style="width: 20px;"></i> API & Tích hợp
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card h-100">
                <div class="card-body p-4 tab-content">
                    
                    <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                        <h5 class="mb-4 pb-2 border-bottom">Cài đặt chung</h5>
                        <form onsubmit="saveSettings(event, 'chung')">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tên ứng dụng</label>
                                    <input type="text" class="form-control" value="Surveygo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email liên hệ</label>
                                    <input type="email" class="form-control" value="contact@surveygo.com">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả hệ thống</label>
                                <textarea class="form-control" rows="3">Nền tảng khảo sát trực tuyến hàng đầu.</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ngôn ngữ mặc định</label>
                                <select class="form-select">
                                    <option value="vi" selected>Tiếng Việt</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tab-email" role="tabpanel">
                        <h5 class="mb-4 pb-2 border-bottom">Cấu hình Email (SMTP)</h5>
                        <form onsubmit="saveSettings(event, 'email')">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">SMTP Host</label>
                                    <input type="text" class="form-control" value="smtp.gmail.com" placeholder="e.g. smtp.example.com">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Port</label>
                                    <input type="text" class="form-control" value="587">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tài khoản (Username)</label>
                                    <input type="text" class="form-control" value="noreply@surveygo.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Mật khẩu ứng dụng</label>
                                    <input type="password" class="form-control" value="*************">
                                </div>
                            </div>
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email-ssl" checked>
                                <label class="form-check-label" for="email-ssl">Sử dụng kết nối an toàn (SSL/TLS)</label>
                            </div>
                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="testEmail()">Kiểm tra kết nối</button>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu cấu hình</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tab-security" role="tabpanel">
                        <h5 class="mb-4 pb-2 border-bottom">Bảo mật & Phiên đăng nhập</h5>
                        <form onsubmit="saveSettings(event, 'security')">
                            <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-4">
                                <i class="fas fa-info-circle me-2"></i> Các thay đổi bảo mật sẽ áp dụng cho tất cả người dùng trong lần đăng nhập tới.
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="enable-2fa">
                                    <label class="form-check-label fw-bold" for="enable-2fa">Bắt buộc xác thực hai yếu tố (2FA)</label>
                                    <div class="form-text">Yêu cầu tất cả quản trị viên sử dụng Google Authenticator.</div>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="force-https" checked>
                                    <label class="form-check-label fw-bold" for="force-https">Bắt buộc HTTPS</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian hết hạn phiên (phút)</label>
                                <input type="number" class="form-control" value="60" min="5" max="1440">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Số lần đăng nhập sai tối đa</label>
                                <input type="number" class="form-control" value="5">
                            </div>
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu bảo mật</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="tab-api" role="tabpanel">
                        <h5 class="mb-4 pb-2 border-bottom">Kết nối API</h5>
                        <form onsubmit="saveSettings(event, 'api')">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Public API Key</label>
                                <div class="input-group">
                                    <input type="text" class="form-control font-monospace" value="pk_live_51M3m9..." readonly id="public-key">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('public-key')"><i class="fas fa-copy"></i></button>
                                </div>
                                <div class="form-text">Dùng cho các tích hợp phía Client-side.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Secret API Key</label>
                                <div class="input-group">
                                    <input type="password" class="form-control font-monospace" value="sk_live_28H4x..." readonly id="secret-key">
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSecret()"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-outline-secondary" type="button" onclick="regenerateKey()"><i class="fas fa-sync"></i> Tạo mới</button>
                                </div>
                                <div class="form-text text-danger">Giữ bí mật khóa này. Không chia sẻ trong mã client-side.</div>
                            </div>
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu cấu hình API</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function saveSettings(event, section) {
        event.preventDefault();
        const btn = event.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            showToast('success', `Đã lưu cài đặt phần: ${section.toUpperCase()} thành công!`);
        }, 800);
    }

    function clearCache() {
        if(confirm('Bạn có chắc muốn xóa toàn bộ Cache hệ thống? Hành động này có thể làm chậm ứng dụng trong giây lát.')) {
            showToast('success', 'Đã xóa cache thành công!');
        }
    }

    function testEmail() {
        const email = prompt("Nhập email nhận thử nghiệm:");
        if(email) showToast('success', `Đã gửi email test đến ${email}. Vui lòng kiểm tra hộp thư.`);
    }

    function copyToClipboard(id) {
        const copyText = document.getElementById(id);
        copyText.select();
        navigator.clipboard.writeText(copyText.value);
            showToast('success', "Đã sao chép vào bộ nhớ tạm!");
    }

    function toggleSecret() {
        const input = document.getElementById('secret-key');
        if (input.type === "password") input.type = "text";
        else input.type = "password";
    }

    function regenerateKey() {
        if(confirm("Tạo khóa mới sẽ làm khóa cũ vô hiệu lực ngay lập tức. Tiếp tục?")) {
                document.getElementById('secret-key').value = "sk_live_" + Math.random().toString(36).substr(2, 16);
                showToast('success', "Khóa mới đã được tạo.");
            }
    }
</script>