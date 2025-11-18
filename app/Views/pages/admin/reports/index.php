<?php
/** @var string $appName */
/** @var string $baseUrl */
/** @var array $urls */
/** @var string $currentPath */

$appName = $appName ?? 'Admin - Báo cáo & Thống kê';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

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
            <h5 class="mb-0">Báo cáo & Thống kê</h5>
            <div>
                <button class="btn btn-link" id="admin-logout">
                    <i class="fas fa-sign-out-alt me-1"></i>Đăng xuất
                </button>
            </div>
        </div>
    </header>

    <main class="admin-content">
        <div class="container-fluid">
            <h2 class="mb-4">Báo cáo & Thống kê</h2>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Thống kê khảo sát</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Biểu đồ thống kê khảo sát sẽ hiển thị ở đây...</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Thống kê người dùng</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Biểu đồ thống kê người dùng sẽ hiển thị ở đây...</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Báo cáo hoạt động</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Dữ liệu hoạt động sẽ hiển thị ở đây...</p>
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