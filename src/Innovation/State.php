<?php
declare(strict_types=1);

function innovation_build_state(): array
{
    innovation_ensure_session();
    $periods = innovation_period_labels();
    innovation_handle_actions($periods);

    $selectedPeriod = isset($_GET['period']) ? (string) $_GET['period'] : 'mensal';
    if (!isset($periods[$selectedPeriod])) {
        $selectedPeriod = 'mensal';
    }

    $data = innovation_period_data($selectedPeriod);
    usort($data, static fn(array $a, array $b): int => $b['progress'] <=> $a['progress']);

    $attentionSectors = array_values(array_filter($data, static fn(array $sector): bool => (bool) $sector['attention']));
    $avgProgress = $data === [] ? 0 : (int) round(array_sum(array_column($data, 'progress')) / count($data));
    $departmentContributions = [];
    foreach ($data as $sector) {
        foreach ((array) ($sector['documents'] ?? []) as $document) {
            $label = (string) ($document['author_department_label'] ?? 'Nao identificado');
            $departmentContributions[$label] = ($departmentContributions[$label] ?? 0) + 1;
        }
    }
    arsort($departmentContributions);

    return [
        'periods' => $periods,
        'selectedPeriod' => $selectedPeriod,
        'sectors' => $data,
        'ranking' => $data,
        'attentionSectors' => $attentionSectors,
        'topSector' => $data[0] ?? null,
        'avgProgress' => $avgProgress,
        'saved' => isset($_GET['saved']),
        'evidenceTypeOptions' => innovation_evidence_type_options(),
        'contributorDepartments' => array_slice($departmentContributions, 0, 5, true),
    ];
}
