<?php
declare(strict_types=1);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (!defined('APP_PUBLIC_PREFIX')) {
    define('APP_PUBLIC_PREFIX', '');
}

require_once APP_ROOT . '/src/bootstrap.php';

analytics_handle_event_request();

$activeView = app_active_view(isset($_GET['view']) ? (string) $_GET['view'] : null);
$authState = auth_build_state();
analytics_record_page_view($activeView);
$analyticsState = analytics_build_state();
$postsState = posts_build_state();
$innovationState = innovation_build_state();
$automationState = automation_build_state();

app_render_view('layout.php', [
    'activeView' => $activeView,
    'authState' => $authState,
    'analyticsState' => $analyticsState,
    'postsState' => $postsState,
    'innovationState' => $innovationState,
    'automationState' => $automationState,
]);
