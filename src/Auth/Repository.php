<?php
declare(strict_types=1);

function auth_session_key(): string
{
    return 'auth_user';
}

function auth_role_options(): array
{
    return [
        'colaborador' => 'Colaborador',
        'gestor' => 'Gestor',
        'administrador' => 'Administrador',
    ];
}

function auth_department_options(): array
{
    return [
        'gerat' => 'GERAT',
        'gelic' => 'GELIC',
        'gecov' => 'GECOV',
        'gecre' => 'GECRE',
        'juridco' => 'JURIDCO',
        'sutec' => 'SUTEC',
        'diretoria' => 'DIRETORIA',
        'rh' => 'RH',
        'outro' => 'Outro setor',
    ];
}

function auth_normalize_user(array $user): array
{
    $department = trim((string) ($user['department'] ?? ''));
    if (!array_key_exists($department, auth_department_options())) {
        $department = 'outro';
    }

    $role = trim((string) ($user['role'] ?? ''));
    if (!array_key_exists($role, auth_role_options())) {
        $role = 'colaborador';
    }

    return [
        'name' => trim((string) ($user['name'] ?? '')),
        'department' => $department,
        'department_label' => (string) auth_department_options()[$department],
        'role' => $role,
        'role_label' => (string) auth_role_options()[$role],
        'logged_in_at' => trim((string) ($user['logged_in_at'] ?? '')),
    ];
}

function auth_ensure_session(): void
{
    app_ensure_session();

    if (!isset($_SESSION[auth_session_key()])) {
        $_SESSION[auth_session_key()] = null;
    }

    if (is_array($_SESSION[auth_session_key()])) {
        $_SESSION[auth_session_key()] = auth_normalize_user($_SESSION[auth_session_key()]);
    } else {
        $_SESSION[auth_session_key()] = null;
    }
}

function auth_current_user(): ?array
{
    auth_ensure_session();
    return is_array($_SESSION[auth_session_key()]) ? $_SESSION[auth_session_key()] : null;
}

function auth_is_authenticated(): bool
{
    $user = auth_current_user();
    return $user !== null && trim((string) ($user['name'] ?? '')) !== '';
}

function auth_is_manager(): bool
{
    $user = auth_current_user();
    if ($user === null) {
        return false;
    }

    return in_array((string) ($user['role'] ?? ''), ['gestor', 'administrador'], true);
}

function auth_set_user(array $user): void
{
    auth_ensure_session();
    $_SESSION[auth_session_key()] = auth_normalize_user($user);
}

function auth_clear_user(): void
{
    auth_ensure_session();
    $_SESSION[auth_session_key()] = null;
}
