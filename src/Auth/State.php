<?php
declare(strict_types=1);

function auth_build_state(): array
{
    [$errors, $formData] = auth_handle_actions();
    $currentUser = auth_current_user();

    return [
        'currentUser' => $currentUser,
        'isAuthenticated' => auth_is_authenticated(),
        'isManager' => auth_is_manager(),
        'authErrors' => $errors,
        'authFormData' => $formData,
        'departmentOptions' => auth_department_options(),
        'roleOptions' => auth_role_options(),
        'loginSuccess' => isset($_GET['login']),
        'logoutSuccess' => isset($_GET['logged_out']),
        'isAuthModalOpen' => $errors !== [],
    ];
}
