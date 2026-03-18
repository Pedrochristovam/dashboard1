<?php
declare(strict_types=1);

function innovation_handle_actions(array $periods): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $dashboardContext = isset($_POST['dashboard_context']) ? (string) $_POST['dashboard_context'] : '';
    if ($dashboardContext !== 'innovation') {
        return;
    }

    $action = isset($_POST['action']) ? (string) $_POST['action'] : '';
    if (!in_array($action, ['save_sector', 'delete_document'], true)) {
        return;
    }

    $period = isset($_POST['period']) ? (string) $_POST['period'] : 'mensal';
    if (!isset($periods[$period])) {
        $period = 'mensal';
    }

    $sectorId = isset($_POST['sector_id']) ? trim((string) $_POST['sector_id']) : '';
    $improvements = trim((string) ($_POST['improvements'] ?? ''));
    $documentId = trim((string) ($_POST['document_id'] ?? ''));

    if ($action === 'save_sector' && $improvements === '') {
        app_redirect([
            'view' => 'innovation',
            'period' => $period,
        ]);
    }

    $sectors = innovation_period_data($period);
    foreach ($sectors as &$sector) {
        if ((string) ($sector['id'] ?? '') !== $sectorId) {
            continue;
        }

        $documents = isset($sector['documents']) && is_array($sector['documents']) ? $sector['documents'] : [];
        $timestamp = date('Y-m-d H:i');

        if ($action === 'delete_document' && $documentId !== '') {
            $documents = array_values(array_filter(
                $documents,
                static fn(array $document): bool => (string) ($document['id'] ?? '') !== $documentId
            ));
        } elseif ($documentId !== '') {
            foreach ($documents as &$document) {
                if ((string) ($document['id'] ?? '') !== $documentId) {
                    continue;
                }

                $document['content'] = $improvements;
                $document['updated_at'] = $timestamp;
                break;
            }
            unset($document);
        } else {
            $nextNumber = count($documents) + 1;
            $documents[] = [
                'id' => (string) $nextNumber,
                'label' => 'Documento ' . $nextNumber,
                'content' => $improvements,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        $sector['documents'] = $documents;
        $sector['summary'] = $action === 'delete_document'
            ? ($documents !== [] ? (string) ($documents[array_key_last($documents)]['content'] ?? '') : '')
            : $improvements;
        break;
    }
    unset($sector);

    innovation_replace_period_data($period, $sectors);

    app_redirect([
        'view' => 'innovation',
        'period' => $period,
        'saved' => '1',
    ]);
}
