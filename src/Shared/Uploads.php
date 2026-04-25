<?php
declare(strict_types=1);

function app_public_path(string $relativePath = ''): string
{
    $basePath = APP_ROOT . DIRECTORY_SEPARATOR . 'public';
    if ($relativePath === '') {
        return $basePath;
    }

    $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relativePath, '/\\'));
    return $basePath . DIRECTORY_SEPARATOR . $normalized;
}

function app_public_url(string $relativePath): string
{
    return app_asset_url(ltrim(str_replace('\\', '/', $relativePath), '/'));
}

function app_store_uploaded_file(array $uploadedFile, string $relativeDirectory, string $extension): string
{
    $tmpName = (string) ($uploadedFile['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('Nao foi possivel validar o arquivo enviado.');
    }

    $relativeDirectory = trim(str_replace('\\', '/', $relativeDirectory), '/');
    $targetDirectory = app_public_path($relativeDirectory);

    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0777, true) && !is_dir($targetDirectory)) {
        throw new RuntimeException('Nao foi possivel preparar a pasta de uploads.');
    }

    $safeExtension = strtolower(ltrim($extension, '.'));
    $filename = bin2hex(random_bytes(16)) . ($safeExtension !== '' ? '.' . $safeExtension : '');
    $relativePath = ($relativeDirectory !== '' ? $relativeDirectory . '/' : '') . $filename;
    $targetPath = app_public_path($relativePath);

    if (!move_uploaded_file($tmpName, $targetPath)) {
        throw new RuntimeException('Nao foi possivel salvar o arquivo enviado.');
    }

    return str_replace('\\', '/', $relativePath);
}

function app_delete_public_file(?string $relativePath): void
{
    if ($relativePath === null || trim($relativePath) === '') {
        return;
    }

    $targetPath = app_public_path($relativePath);
    if (!is_file($targetPath)) {
        return;
    }

    $realTargetPath = realpath($targetPath);
    $realPublicPath = realpath(app_public_path());
    if ($realTargetPath === false || $realPublicPath === false) {
        return;
    }

    if (str_starts_with($realTargetPath, $realPublicPath . DIRECTORY_SEPARATOR)) {
        @unlink($realTargetPath);
    }
}
