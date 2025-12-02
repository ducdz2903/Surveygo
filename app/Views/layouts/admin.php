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
    <link rel="icon" href="<?= htmlspecialchars(rtrim((string) $baseUrl, '/'), ENT_QUOTES, 'UTF-8') ?>/Asset/favIconSurveyGo_16x16.png" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/app.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/variables.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/shared/sidebar.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/shared/header.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/shared/footer.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/layout.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/components/cards.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/components/tables.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/components/badges.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/components/buttons.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/components/charts.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/components/activity.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/responsive.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/animations.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/public/assets/css/admin/pagination.css">
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