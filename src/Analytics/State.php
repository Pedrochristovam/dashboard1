<?php
declare(strict_types=1);

function analytics_build_state(): array
{
    $events = analytics_all_events();
    $moduleCounts = analytics_group_count($events, static fn(array $event): string => (string) ($event['module'] ?? ''));
    $departmentCounts = analytics_group_count($events, static fn(array $event): string => (string) ($event['department_label'] ?? ''));

    $postOpenCounts = analytics_group_count(
        array_values(array_filter($events, static fn(array $event): bool => (string) ($event['type'] ?? '') === 'modal_open' && (string) ($event['module'] ?? '') === 'posts')),
        static fn(array $event): string => (string) (($event['meta']['title'] ?? '') ?: ($event['target_id'] ?? ''))
    );

    $sectorCounts = analytics_group_count(
        array_values(array_filter($events, static fn(array $event): bool => in_array((string) ($event['module'] ?? ''), ['innovation', 'automation'], true))),
        static fn(array $event): string => (string) ($event['meta']['sector_label'] ?? '')
    );

    $moduleAverages = [];
    foreach (['posts', 'innovation', 'automation'] as $module) {
        $moduleAverages[$module] = analytics_average_duration_for_module($events, $module);
    }

    return [
        'analyticsTotalEvents' => count($events),
        'analyticsTopModule' => $moduleCounts !== [] ? array_key_first($moduleCounts) : null,
        'analyticsTopModuleCount' => $moduleCounts !== [] ? (int) reset($moduleCounts) : 0,
        'analyticsTopDepartment' => $departmentCounts !== [] ? array_key_first($departmentCounts) : null,
        'analyticsTopDepartmentCount' => $departmentCounts !== [] ? (int) reset($departmentCounts) : 0,
        'analyticsModuleAverages' => $moduleAverages,
        'analyticsTopPosts' => array_slice($postOpenCounts, 0, 3, true),
        'analyticsTopDepartments' => array_slice($departmentCounts, 0, 5, true),
        'analyticsTopSectors' => array_slice($sectorCounts, 0, 5, true),
    ];
}
