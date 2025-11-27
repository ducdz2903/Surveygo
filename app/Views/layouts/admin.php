<?php
/**
 * Admin Layout
 * Layout cho admin panel với sidebar và header riêng
 */
$title = $title ?? ($appName ?? 'Admin Panel');
$content = $content ?? '';
$baseUrl = $baseUrl ?? '';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/app.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin.css">
</head>

<body class="admin-layout">
    <!-- Admin Header -->
    <?php include BASE_PATH . '/app/Views/components/admin/_header.php'; ?>

    <div class="admin-container">
        <!-- Admin Sidebar -->
        <?php include BASE_PATH . '/app/Views/components/admin/_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="admin-content">
            <?= $content ?>
        </main>
    </div>

    <!-- Admin Footer -->
    <?php include BASE_PATH . '/app/Views/components/admin/_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/js/admin.js"></script>
</body>

</html>