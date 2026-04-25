<?php
declare(strict_types=1);

function analytics_handle_event_request(): void
{
    if (!isset($_GET['analytics'])) {
        return;
    }

    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Metodo nao permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $payload = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Payload invalido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $type = trim((string) ($payload['type'] ?? ''));
    $module = trim((string) ($payload['module'] ?? ''));
    if ($type === '' || $module === '') {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Evento incompleto.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $targetType = isset($payload['targetType']) ? trim((string) $payload['targetType']) : null;
    $targetId = isset($payload['targetId']) ? trim((string) $payload['targetId']) : null;
    $durationSeconds = isset($payload['durationSeconds']) ? (int) $payload['durationSeconds'] : null;
    $meta = isset($payload['meta']) && is_array($payload['meta']) ? $payload['meta'] : [];

    analytics_record_event($type, $module, $targetType, $targetId, $meta, $durationSeconds);

    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

function analytics_record_page_view(string $module): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        return;
    }

    analytics_record_event('view_open', $module);
}
