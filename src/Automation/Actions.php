<?php
declare(strict_types=1);

function automation_default_form_data(): array
{
    return [
        'requester' => 'Pedro',
        'sector' => 'gerat',
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

    if ($action === 'delete') {
        $requestId = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        $requests = array_values(array_filter(
            automation_all(),
            static fn(array $request): bool => (int) $request['id'] !== $requestId
        ));
        automation_replace_all($requests);

        $redirectQuery = ['view' => 'automation'];
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $redirectQuery['search'] = (string) $_GET['search'];
        }

        app_redirect($redirectQuery);
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
        'sector' => $formData['sector'],
        'type' => $formData['type'],
        'priority' => $formData['priority'],
        'frequency' => $formData['frequency'],
        'activity' => $formData['activity'],
        'need' => $formData['need'],
        'expected_result' => $formData['expected_result'],
        'deadline' => $formData['deadline'],
        'status' => 'novo',
        'created_at' => date('Y-m-d H:i'),
    ]);

    automation_replace_all($requests);

    app_redirect([
        'view' => 'automation',
        'created' => '1',
    ]);
}
