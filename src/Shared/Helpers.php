<?php
declare(strict_types=1);

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function text_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }

    return strlen($value);
}

function text_slice(string $value, int $start, int $length): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, $start, $length, 'UTF-8');
    }

    return substr($value, $start, $length);
}

function text_lower(string $value): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    return strtolower($value);
}

function truncate_text(string $text, int $limit = 150): string
{
    $clean = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

    if (text_length($clean) <= $limit) {
        return $clean;
    }

    $truncated = text_slice($clean, 0, $limit - 3);
    $lastSpace = strrpos($truncated, ' ');

    if ($lastSpace !== false && $lastSpace > (int) (($limit - 3) * 0.55)) {
        $truncated = substr($truncated, 0, $lastSpace);
    }

    return rtrim($truncated, " \t\n\r\0\x0B.,;:-") . '...';
}

function parse_youtube_video_id(string $url): ?string
{
    $patterns = [
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/i',
        '/youtu\.be\/([a-zA-Z0-9_-]{11})/i',
        '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/i',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/i',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches) === 1) {
            return $matches[1];
        }
    }

    $parts = parse_url($url);
    if (!isset($parts['host'], $parts['query'])) {
        return null;
    }

    parse_str($parts['query'], $queryParams);

    return isset($queryParams['v']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', (string) $queryParams['v']) === 1
        ? (string) $queryParams['v']
        : null;
}
