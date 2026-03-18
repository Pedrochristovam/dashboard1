<?php
declare(strict_types=1);

function app_asset_url(string $path): string
{
    $prefix = defined('APP_PUBLIC_PREFIX') ? (string) constant('APP_PUBLIC_PREFIX') : '';
    return $prefix . ltrim($path, '/');
}

function app_redirect(array $query = []): void
{
    $target = 'index.php';
    if ($query !== []) {
        $target .= '?' . http_build_query($query);
    }

    header('Location: ' . $target);
    exit;
}

function app_active_view(?string $requestedView): string
{
    $allowedViews = ['posts', 'innovation', 'automation'];
    $activeView = $requestedView ?? 'posts';

    return in_array($activeView, $allowedViews, true) ? $activeView : 'posts';
}
