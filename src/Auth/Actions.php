<?php
declare(strict_types=1);

function auth_default_form_data(): array
{
    $currentUser = auth_current_user();

    return [
        'name' => (string) ($currentUser['name'] ?? ''),
        'department' => (string) ($currentUser['department'] ?? 'gerat'),
        'role' => (string) ($currentUser['role'] ?? 'colaborador'),
    ];
}

function auth_handle_actions(): array
{
    auth_ensure_session();

    $errors = [];
    $formData = auth_default_form_data();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [$errors, $formData];
    }

    $dashboardContext = isset($_POST['dashboard_context']) ? (string) $_POST['dashboard_context'] : '';
    if ($dashboardContext !== 'auth') {
        return [$errors, $formData];
    }

    $action = isset($_POST['action']) ? (string) $_POST['action'] : 'login';

    if ($action === 'logout') {
        $redirectQuery = [
            'view' => app_active_view(isset($_GET['view']) ? (string) $_GET['view'] : null),
            'logged_out' => '1',
        ];
        if (isset($_GET['tab']) && trim((string) $_GET['tab']) !== '') {
            $redirectQuery['tab'] = (string) $_GET['tab'];
        }

        auth_clear_user();
        app_redirect($redirectQuery);
    }

    $formData = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'department' => trim((string) ($_POST['department'] ?? 'gerat')),
        'role' => trim((string) ($_POST['role'] ?? 'colaborador')),
    ];

    if ($formData['name'] === '') {
        $errors[] = 'Informe o nome do colaborador para entrar.';
    }

    if (!array_key_exists($formData['department'], auth_department_options())) {
        $errors[] = 'Selecione um departamento válido.';
    }

    if (!array_key_exists($formData['role'], auth_role_options())) {
        $errors[] = 'Selecione um perfil válido.';
    }

    if ($errors !== []) {
        return [$errors, $formData];
    }

    auth_set_user([
        'name' => $formData['name'],
        'department' => $formData['department'],
        'role' => $formData['role'],
        'logged_in_at' => date('Y-m-d H:i:s'),
    ]);

    $redirectQuery = [
        'view' => app_active_view(isset($_GET['view']) ? (string) $_GET['view'] : null),
        'login' => '1',
    ];
    if (isset($_GET['tab']) && trim((string) $_GET['tab']) !== '') {
        $redirectQuery['tab'] = (string) $_GET['tab'];
    }

    app_redirect($redirectQuery);
}
