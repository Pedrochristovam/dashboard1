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
    if (!in_array($action, ['save_sector', 'delete_document', 'create_sector', 'delete_sector'], true)) {
        return;
    }

    $period = isset($_POST['period']) ? (string) $_POST['period'] : 'mensal';
    if (!isset($periods[$period])) {
        $period = 'mensal';
    }

    $redirectView = app_active_view(isset($_GET['view']) ? (string) $_GET['view'] : 'innovation');
    $redirectBase = [
        'view' => $redirectView,
        'period' => $period,
    ];
    if (isset($_GET['tab']) && trim((string) $_GET['tab']) !== '') {
        $redirectBase['tab'] = (string) $_GET['tab'];
    }

    $sectorId = isset($_POST['sector_id']) ? trim((string) $_POST['sector_id']) : '';
    $sectorName = trim((string) ($_POST['sector_name'] ?? ''));
    $improvements = trim((string) ($_POST['improvements'] ?? ''));
    $documentId = trim((string) ($_POST['document_id'] ?? ''));
    $evidenceType = trim((string) ($_POST['evidence_type'] ?? 'documento'));
    if (!array_key_exists($evidenceType, innovation_evidence_type_options())) {
        $evidenceType = 'documento';
    }
    $currentUser = auth_current_user();
    if ($currentUser === null || !auth_is_manager()) {
        app_redirect([
            ...$redirectBase,
            'saved' => '0',
        ]);
    }

    if ($action === 'save_sector' && $improvements === '') {
        app_redirect([
            ...$redirectBase,
        ]);
    }

    $sectors = innovation_period_data($period);
    if ($action === 'create_sector' && $sectorName !== '') {
        $sectorId = innovation_sector_id_from_name($sectorName);
        $suffix = 2;
        $existingIds = array_column($sectors, 'id');
        while (in_array($sectorId, $existingIds, true)) {
            $sectorId = innovation_sector_id_from_name($sectorName) . '_' . $suffix;
            $suffix++;
        }

        $sectors[] = innovation_normalize_sector([
            'id' => $sectorId,
            'name' => strtoupper($sectorName),
            'documents' => [],
        ]);
        innovation_replace_period_data($period, $sectors);
        analytics_record_event('sector_created', 'innovation', 'sector', $sectorId, [
            'sector_label' => strtoupper($sectorName),
        ]);

        app_redirect([
            ...$redirectBase,
            'saved' => '1',
        ]);
    }

    foreach ($sectors as &$sector) {
        if ((string) ($sector['id'] ?? '') !== $sectorId) {
            continue;
        }

        if ($action === 'delete_sector') {
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
                $document['author_name'] = (string) ($currentUser['name'] ?? '');
                $document['author_department'] = (string) ($currentUser['department'] ?? 'outro');
                $document['evidence_type'] = $evidenceType;
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
                'author_name' => (string) ($currentUser['name'] ?? ''),
                'author_department' => (string) ($currentUser['department'] ?? 'outro'),
                'evidence_type' => $evidenceType,
            ];
        }

        $sector['documents'] = $documents;
        $sector['summary'] = $action === 'delete_document'
            ? ($documents !== [] ? (string) ($documents[array_key_last($documents)]['content'] ?? '') : '')
            : $improvements;
        break;
    }
    unset($sector);

    if ($action === 'delete_sector') {
        $sectors = array_values(array_filter(
            $sectors,
            static fn(array $sector): bool => (string) ($sector['id'] ?? '') !== $sectorId
        ));
        analytics_record_event('sector_deleted', 'innovation', 'sector', $sectorId);
    } else {
        analytics_record_event(
            $action === 'delete_document' ? 'innovation_document_deleted' : 'innovation_document_saved',
            'innovation',
            'sector',
            $sectorId,
            [
                'sector_label' => (string) ($sectorName !== '' ? strtoupper($sectorName) : $sectorId),
                'evidence_type' => $evidenceType,
            ]
        );
    }

    innovation_replace_period_data($period, $sectors);

    app_redirect([
        ...$redirectBase,
        'saved' => '1',
    ]);
}
