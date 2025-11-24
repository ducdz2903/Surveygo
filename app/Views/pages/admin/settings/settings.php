<?php
/** @var string $appName */
/** @var string $baseUrl */
/** @var array $urls */
/** @var string $currentPath */

$appName = $appName ?? 'Admin - Cài đặt hệ thống';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link href="/public/assets/css/admin.css" rel="stylesheet">

    <style>
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            overflow-y: auto;
            z-index: 1000;
        }

        .admin-header {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background: white;
            border-bottom: 1px solid #ddd;
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 2rem;
        }

        .admin-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 2rem;
            min-height: calc(100vh - 60px);
        }
    </style>
</head>

<body>
    <?php include BASE_PATH . '/app/Views/components/admin/_sidebar.php'; ?>

    <header class="admin-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0">Cài đặt hệ thống</h5>
            <div>
                <button class="btn btn-link" id="admin-logout">
                    <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                </button>
            </div>
        </div>
    </header>

    <main class="admin-content">
        <div class="container-fluid">
            <h2 class="mb-4">Cài đặt hệ thống</h2>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist"
                        aria-orientation="vertical">
                        <button class="nav-link active" id="tab-general-btn" data-bs-toggle="pill"
                            data-bs-target="#tab-general" type="button" role="tab" aria-controls="tab-general"
                            aria-selected="true">Chung</button>
                        <button class="nav-link" id="tab-email-btn" data-bs-toggle="pill" data-bs-target="#tab-email"
                            type="button" role="tab" aria-controls="tab-email" aria-selected="false">Email</button>
                        <button class="nav-link" id="tab-security-btn" data-bs-toggle="pill"
                            data-bs-target="#tab-security" type="button" role="tab" aria-controls="tab-security"
                            aria-selected="false">Bảo mật</button>
                        <button class="nav-link" id="tab-api-btn" data-bs-toggle="pill" data-bs-target="#tab-api"
                            type="button" role="tab" aria-controls="tab-api" aria-selected="false">API</button>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body tab-content">
                            <div class="tab-pane fade show active" id="tab-general" role="tabpanel"
                                aria-labelledby="tab-general-btn">
                                <div class="card-header">
                                    <h5 class="mb-0">Cài đặt chung</h5>
                                </div>
                                <div class="card-body pt-3">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Tên ứng dụng</label>
                                            <input type="text" class="form-control" value="Surveygo">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email liên hệ</label>
                                            <input type="email" class="form-control" value="contact@surveygo.com">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Mô tả</label>
                                            <textarea class="form-control"
                                                rows="3">Nền tảng khảo sát trực tuyến</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                    </form>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-email" role="tabpanel" aria-labelledby="tab-email-btn">
                                <div class="card-header">
                                    <h5 class="mb-0">Cài đặt Email</h5>
                                </div>
                                <div class="card-body pt-3">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" value="smtp.example.com">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Port</label>
                                            <input type="text" class="form-control" value="587">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tài khoản</label>
                                            <input type="text" class="form-control" value="user@example.com">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Lưu</button>
                                    </form>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-security" role="tabpanel"
                                aria-labelledby="tab-security-btn">
                                <div class="card-header">
                                    <h5 class="mb-0">Cài đặt Bảo mật</h5>
                                </div>
                                <div class="card-body pt-3">
                                    <form>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="enable-2fa">
                                            <label class="form-check-label" for="enable-2fa">Bật xác thực hai yếu tố
                                                (2FA)</label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Thời gian kết thúc phiên (phút)</label>
                                            <input type="number" class="form-control" value="60">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Lưu</button>
                                    </form>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab-api" role="tabpanel" aria-labelledby="tab-api-btn">
                                <div class="card-header">
                                    <h5 class="mb-0">Cài đặt API</h5>
                                </div>
                                <div class="card-body pt-3">
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">API Key</label>
                                            <input type="text" class="form-control" value="">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Lưu</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('admin-logout')?.addEventListener('click', function () {
            localStorage.removeItem('app.user');
            window.location.href = '/login';
        });
    </script>
</body>

</html>