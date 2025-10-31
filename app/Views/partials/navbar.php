<?php
$currentPath = $currentPath ?? ($_SERVER['REQUEST_URI'] ?? '/');
$baseUrl = $baseUrl ?? '';
$urls = $urls ?? [];
$basePath = (string)(parse_url($baseUrl, PHP_URL_PATH) ?? '');
$basePath = $basePath === '/' ? '' : rtrim($basePath, '/');

$normalize = static function (string $path) use ($basePath): string {
    if ($basePath !== '' && str_starts_with($path, $basePath)) {
        $trimmed = substr($path, strlen($basePath));
        return $trimmed === '' ? '/' : $trimmed;
    }

    return $path === '' ? '/' : $path;
};

$current = $normalize($currentPath);

$url = static function (array $urls, string $key, string $fallbackPath = '/') use ($baseUrl) {
    $given = $urls[$key] ?? null;
    if (is_string($given) && $given !== '') {
        return htmlspecialchars($given, ENT_QUOTES, 'UTF-8');
    }

    $normalizedBase = rtrim((string)$baseUrl, '/');
    $normalizedPath = '/' . ltrim((string)$fallbackPath, '/');
    $computed = $normalizedBase === '' ? $normalizedPath : ($normalizedBase . $normalizedPath);
    return htmlspecialchars($computed, ENT_QUOTES, 'UTF-8');
};
?>
<nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top bg-white bg-opacity-90">
    <div class="container py-2">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= $url($urls, 'home', '/') ?>">
            <span class="badge bg-primary text-white rounded-circle p-3">*</span>
            <span><?= htmlspecialchars($appName ?? 'PHP App', ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
            <ul class="navbar-nav gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/' ? 'active' : '' ?>" href="<?= $url($urls, 'home', '/') ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/features' ? 'active' : '' ?>" href="<?= $url($urls, 'features', '/features') ?>">Tính năng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/login' ? 'active' : '' ?>" href="<?= $url($urls, 'login', '/login') ?>">Đăng nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current === '/register' ? 'active' : '' ?>" href="<?= $url($urls, 'register', '/register') ?>">Đăng ký</a>
                </li>
            </ul>
            
        </div>
    </div>
</nav>
