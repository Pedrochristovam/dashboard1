<?php
declare(strict_types=1);

function automation_filtered_requests(string $search): array
{
    return array_values(array_filter(
        automation_all(),
        static function (array $request) use ($search): bool {
            if ($search === '') {
                return true;
            }

            $haystack = text_lower(implode(' ', [
                (string) ($request['title'] ?? ''),
                (string) ($request['requester'] ?? ''),
                (string) ($request['activity'] ?? ''),
                (string) ($request['need'] ?? ''),
                (string) ($request['expected_result'] ?? ''),
                (string) ($request['sector'] ?? ''),
                (string) ($request['type'] ?? ''),
                (string) ($request['priority'] ?? ''),
                (string) ($request['status'] ?? ''),
            ]));

            return str_contains($haystack, text_lower($search));
        }
    ));
}

function automation_build_state(): array
{
    automation_ensure_session();
    [$errors, $formData] = automation_handle_actions();

    $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
    $statusFilter = isset($_GET['status']) ? trim((string) $_GET['status']) : '';
    $sectorFilter = isset($_GET['sector']) ? trim((string) $_GET['sector']) : '';
    $priorityFilter = isset($_GET['priority']) ? trim((string) $_GET['priority']) : '';

    $requests = array_values(array_filter(
        automation_filtered_requests($search),
        static function (array $request) use ($statusFilter, $sectorFilter, $priorityFilter): bool {
            if ($statusFilter !== '' && (string) ($request['status'] ?? '') !== $statusFilter) {
                return false;
            }

            if ($sectorFilter !== '' && (string) ($request['sector'] ?? '') !== $sectorFilter) {
                return false;
            }

            if ($priorityFilter !== '' && (string) ($request['priority'] ?? '') !== $priorityFilter) {
                return false;
            }

            return true;
        }
    ));

    usort($requests, static fn(array $a, array $b): int => strtotime((string) $b['created_at']) <=> strtotime((string) $a['created_at']));

    $allRequests = automation_all();
    $priorityCounts = array_count_values(array_column($allRequests, 'priority'));
    $statusCounts = array_count_values(array_column($allRequests, 'status'));
    $quickWins = count(array_filter(
        $allRequests,
        static fn(array $request): bool => in_array((string) $request['type'], ['automacao_processo', 'painel_relatorio'], true)
    ));
    $departmentCounts = array_count_values(array_map(
        static fn(array $request): string => (string) ($request['requester_department_label'] ?? 'Nao identificado'),
        $allRequests
    ));
    arsort($departmentCounts);

    return [
        'errors' => $errors,
        'formData' => $formData,
        'search' => $search,
        'statusFilter' => $statusFilter,
        'sectorFilter' => $sectorFilter,
        'priorityFilter' => $priorityFilter,
        'requests' => $requests,
        'created' => isset($_GET['created']),
        'updated' => isset($_GET['updated']),
        'isAutomationPanelOpen' => $errors !== [],
        'sectorOptions' => automation_sector_options(),
        'typeOptions' => automation_type_options(),
        'priorityOptions' => automation_priority_options(),
        'frequencyOptions' => automation_frequency_options(),
        'statusLabels' => automation_status_labels(),
        'totalRequests' => count($allRequests),
        'highPriorityCount' => (int) (($priorityCounts['alta'] ?? 0) + ($priorityCounts['critica'] ?? 0)),
        'triageCount' => (int) (($statusCounts['novo'] ?? 0) + ($statusCounts['triagem'] ?? 0)),
        'quickWins' => $quickWins,
        'completedCount' => (int) ($statusCounts['concluido'] ?? 0),
        'topRequesterDepartment' => $departmentCounts !== [] ? (string) array_key_first($departmentCounts) : 'Sem dados',
    ];
}
