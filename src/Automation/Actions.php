<?php
declare(strict_types=1);

function automation_default_form_data(): array
{
    $currentUser = auth_current_user();

    return [
        'requester' => (string) ($currentUser['name'] ?? ''),
        'sector' => (string) ($currentUser['department'] ?? 'gerat'),
        'type' => 'automacao_processo',
        'priority' => 'media',
        'frequency' => 'sob_demanda',
        'title' => '',
        'activity' => '',
        'need' => '',
        'expected_result' => '',
        'deadline' => '',
    ];
}

function automation_redirect_query_from_request(array $extra = []): array
{
    $requestedView = app_active_view(isset($_GET['view']) ? (string) $_GET['view'] : 'automation');
    $query = ['view' => $requestedView];
    foreach (['search', 'status', 'sector', 'priority', 'tab'] as $key) {
        if (isset($_GET[$key]) && trim((string) $_GET[$key]) !== '') {
            $query[$key] = (string) $_GET[$key];
        }
    }

    return array_merge($query, $extra);
}

function automation_handle_actions(): array
{
    $errors = [];
    $formData = automation_default_form_data();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [$errors, $formData];
    }

    $dashboardContext = isset($_POST['dashboard_context']) ? (string) $_POST['dashboard_context'] : '';
    if ($dashboardContext !== 'automation') {
        return [$errors, $formData];
    }

    $action = isset($_POST['action']) ? (string) $_POST['action'] : 'create';
    $currentUser = auth_current_user();

    if (in_array($action, ['delete', 'update_status', 'add_comment', 'create'], true) && $currentUser === null) {
        return [['Entre com nome, departamento e perfil para gerenciar tickets.'], $formData];
    }

    if (in_array($action, ['delete', 'update_status', 'add_comment'], true) && !auth_is_manager()) {
        return [['O acompanhamento completo dos tickets é exclusivo para gestores.'], $formData];
    }

    if ($action === 'delete') {
        $requestId = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        $requests = array_values(array_filter(
            automation_all(),
            static fn(array $request): bool => (int) $request['id'] !== $requestId
        ));
        automation_replace_all($requests);
        analytics_record_event('ticket_deleted', 'automation', 'ticket', (string) $requestId);

        app_redirect(automation_redirect_query_from_request());
    }

    if (in_array($action, ['update_status', 'add_comment'], true)) {
        $requestId = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        $requests = automation_all();
        $timestamp = date('Y-m-d H:i');

        foreach ($requests as &$request) {
            if ((int) ($request['id'] ?? 0) !== $requestId) {
                continue;
            }

            if ($action === 'update_status') {
                $status = trim((string) ($_POST['status'] ?? 'novo'));
                $assignee = trim((string) ($_POST['assignee'] ?? ''));
                if (array_key_exists($status, automation_status_labels())) {
                    $request['status'] = $status;
                    $request['assignee'] = $assignee !== '' ? $assignee : (string) ($request['assignee'] ?? '');
                    $request['timeline'][] = [
                        'id' => (string) (count((array) $request['timeline']) + 1),
                        'type' => 'status',
                        'label' => 'Status atualizado para ' . (string) (automation_status_labels()[$status] ?? $status),
                        'author' => (string) ($currentUser['name'] ?? 'Equipe'),
                        'created_at' => $timestamp,
                    ];
                    analytics_record_event('ticket_status_changed', 'automation', 'ticket', (string) $requestId, [
                        'status' => $status,
                        'sector_label' => (string) (automation_sector_options()[(string) ($request['sector'] ?? 'outro')] ?? ''),
                    ]);
                }
            }

            if ($action === 'add_comment') {
                $comment = trim((string) ($_POST['comment'] ?? ''));
                if ($comment !== '') {
                    $request['comments'][] = [
                        'id' => (string) (count((array) $request['comments']) + 1),
                        'author' => (string) ($currentUser['name'] ?? 'Equipe'),
                        'message' => $comment,
                        'created_at' => $timestamp,
                    ];
                    $request['timeline'][] = [
                        'id' => (string) (count((array) $request['timeline']) + 1),
                        'type' => 'comment',
                        'label' => 'Comentário adicionado ao acompanhamento',
                        'author' => (string) ($currentUser['name'] ?? 'Equipe'),
                        'created_at' => $timestamp,
                    ];
                    analytics_record_event('ticket_comment_added', 'automation', 'ticket', (string) $requestId, [
                        'sector_label' => (string) (automation_sector_options()[(string) ($request['sector'] ?? 'outro')] ?? ''),
                    ]);
                }
            }
            break;
        }
        unset($request);

        automation_replace_all($requests);
        app_redirect(automation_redirect_query_from_request(['updated' => '1']));
    }

    $formData = [
        'requester' => trim((string) ($_POST['requester'] ?? '')),
        'sector' => trim((string) ($_POST['sector'] ?? '')),
        'type' => trim((string) ($_POST['type'] ?? '')),
        'priority' => trim((string) ($_POST['priority'] ?? '')),
        'frequency' => trim((string) ($_POST['frequency'] ?? '')),
        'title' => trim((string) ($_POST['title'] ?? '')),
        'activity' => trim((string) ($_POST['activity'] ?? '')),
        'need' => trim((string) ($_POST['need'] ?? '')),
        'expected_result' => trim((string) ($_POST['expected_result'] ?? '')),
        'deadline' => trim((string) ($_POST['deadline'] ?? '')),
    ];

    if ($currentUser !== null) {
        $formData['requester'] = (string) $currentUser['name'];
        $formData['sector'] = (string) $currentUser['department'];
    }

    if ($formData['requester'] === '') {
        $errors[] = 'Informe o nome do colaborador solicitante.';
    }

    if ($formData['title'] === '') {
        $errors[] = 'Informe um título para o pedido.';
    }

    if ($formData['activity'] === '') {
        $errors[] = 'Descreva a atividade impactada.';
    }

    if ($formData['need'] === '') {
        $errors[] = 'Explique a necessidade atual.';
    }

    if ($formData['expected_result'] === '') {
        $errors[] = 'Descreva o resultado esperado.';
    }

    if (!array_key_exists($formData['sector'], automation_sector_options())) {
        $errors[] = 'Selecione um setor válido.';
    }

    if (!array_key_exists($formData['type'], automation_type_options())) {
        $errors[] = 'Selecione um tipo de pedido válido.';
    }

    if (!array_key_exists($formData['priority'], automation_priority_options())) {
        $errors[] = 'Selecione uma prioridade válida.';
    }

    if (!array_key_exists($formData['frequency'], automation_frequency_options())) {
        $errors[] = 'Selecione uma frequência válida.';
    }

    if ($formData['deadline'] !== '' && strtotime($formData['deadline']) === false) {
        $errors[] = 'Informe uma data válida para necessidade.';
    }

    if ($errors !== []) {
        return [$errors, $formData];
    }

    $requests = automation_all();
    $nextId = $requests !== [] ? max(array_column($requests, 'id')) + 1 : 1;

    array_unshift($requests, [
        'id' => $nextId,
        'title' => $formData['title'],
        'requester' => $formData['requester'],
        'requester_department' => $formData['sector'],
        'sector' => $formData['sector'],
        'type' => $formData['type'],
        'priority' => $formData['priority'],
        'frequency' => $formData['frequency'],
        'activity' => $formData['activity'],
        'need' => $formData['need'],
        'expected_result' => $formData['expected_result'],
        'deadline' => $formData['deadline'],
        'status' => 'novo',
        'assignee' => 'Não atribuído',
        'comments' => [],
        'timeline' => [
            [
                'id' => '1',
                'type' => 'status',
                'label' => 'Solicitação criada',
                'author' => $formData['requester'],
                'created_at' => date('Y-m-d H:i'),
            ],
        ],
        'created_at' => date('Y-m-d H:i'),
    ]);

    automation_replace_all($requests);
    analytics_record_event('ticket_created', 'automation', 'ticket', (string) $nextId, [
        'sector_label' => (string) (automation_sector_options()[$formData['sector']] ?? ''),
    ]);

    app_redirect(automation_redirect_query_from_request(['created' => '1']));
}
