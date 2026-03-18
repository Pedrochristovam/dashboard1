<?php
declare(strict_types=1);

function app_view_path(string $relativePath): string
{
    return APP_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
}

function app_render_view(string $relativePath, array $variables = []): void
{
    extract($variables, EXTR_SKIP);
    require app_view_path($relativePath);
}
