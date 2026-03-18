<?php
declare(strict_types=1);

function fetch_remote_json(string $url): ?array
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'AI KnowledgeHub Preview/1.0',
        ]);

        $response = curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if (is_string($response) && $statusCode >= 200 && $statusCode < 300) {
            $decoded = json_decode($response, true);
            return is_array($decoded) ? $decoded : null;
        }
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 6,
            'header' => "User-Agent: AI KnowledgeHub Preview/1.0\r\n",
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if (!is_string($response)) {
        return null;
    }

    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : null;
}

function get_link_preview(string $url): array
{
    $url = trim($url);
    $preview = [
        'is_youtube' => false,
        'title' => '',
        'thumbnail' => '',
        'provider' => '',
    ];

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return $preview;
    }

    $videoId = parse_youtube_video_id($url);
    if ($videoId === null) {
        return $preview;
    }

    $preview['is_youtube'] = true;
    $preview['thumbnail'] = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
    $preview['provider'] = 'YouTube';

    $oEmbedUrl = 'https://www.youtube.com/oembed?url=' . rawurlencode($url) . '&format=json';
    $oEmbed = fetch_remote_json($oEmbedUrl);
    if (is_array($oEmbed) && isset($oEmbed['title'])) {
        $preview['title'] = (string) $oEmbed['title'];
    } else {
        $preview['title'] = 'Video do YouTube detectado';
    }

    return $preview;
}
