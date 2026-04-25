<?php
declare(strict_types=1);

function analytics_session_key(): string
{
    return 'analytics_events';
}

function analytics_ensure_session(): void
{
    app_ensure_session();

    if (!isset($_SESSION[analytics_session_key()]) || !is_array($_SESSION[analytics_session_key()])) {
        $_SESSION[analytics_session_key()] = [];
    }
}

function analytics_all_events(): array
{
    analytics_ensure_session();
    return $_SESSION[analytics_session_key()];
}

function analytics_add_event(array $event): void
{
    analytics_ensure_session();
    $_SESSION[analytics_session_key()][] = $event;
}

function analytics_build_event(
    string $type,
    string $module,
    ?string $targetType = null,
    ?string $targetId = null,
    array $meta = [],
    ?int $durationSeconds = null
): array {
    $currentUser = auth_current_user();

    return [
        'id' => bin2hex(random_bytes(12)),
        'type' => $type,
        'module' => $module,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'meta' => $meta,
        'duration_seconds' => $durationSeconds,
        'user_name' => (string) ($currentUser['name'] ?? ''),
        'department' => (string) ($currentUser['department'] ?? 'nao_identificado'),
        'department_label' => (string) ($currentUser['department_label'] ?? 'Nao identificado'),
        'occurred_at' => date('Y-m-d H:i:s'),
    ];
}

function analytics_record_event(
    string $type,
    string $module,
    ?string $targetType = null,
    ?string $targetId = null,
    array $meta = [],
    ?int $durationSeconds = null
): void {
    analytics_add_event(analytics_build_event($type, $module, $targetType, $targetId, $meta, $durationSeconds));
}

function analytics_group_count(array $events, callable $keyResolver): array
{
    $counts = [];
    foreach ($events as $event) {
        $key = (string) $keyResolver($event);
        if ($key === '') {
            continue;
        }

        $counts[$key] = ($counts[$key] ?? 0) + 1;
    }

    arsort($counts);
    return $counts;
}

function analytics_average_duration_for_module(array $events, string $module): int
{
    $durations = [];
    foreach ($events as $event) {
        if ((string) ($event['module'] ?? '') !== $module) {
            continue;
        }

        $duration = $event['duration_seconds'] ?? null;
        if (!is_int($duration) || $duration <= 0) {
            continue;
        }

        $durations[] = $duration;
    }

    if ($durations === []) {
        return 0;
    }

    return (int) round(array_sum($durations) / count($durations));
}
