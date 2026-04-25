<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI KnowledgeHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= h(app_asset_url('assets/css/app.css')) ?>">
</head>
<body data-active-view="<?= h($activeView) ?>">
    <?php app_render_view('shared/auth_corner.php', $authState); ?>
    <div class="shell">
        <?php app_render_view('shared/sidebar.php', ['activeView' => $activeView] + $authState + $analyticsState); ?>

        <main class="content with-sidebar">
            <?php if ($activeView === 'posts'): ?>
                <?php extract($postsState, EXTR_SKIP); ?>
                <?php app_render_view('posts/user_dashboard.php', $postsState); ?>
            <?php elseif ($activeView === 'innovation'): ?>
                <?php extract($innovationState, EXTR_SKIP); ?>
                <?php app_render_view('innovation/user_dashboard.php', $innovationState); ?>
            <?php elseif ($activeView === 'automation'): ?>
                <?php extract($automationState, EXTR_SKIP); ?>
                <?php app_render_view('automation/user_dashboard.php', $automationState); ?>
            <?php else: ?>
                <?php app_render_view('manager/dashboard.php', [
                    'authState' => $authState,
                    'analyticsState' => $analyticsState,
                    'postsState' => $postsState,
                    'innovationState' => $innovationState,
                    'automationState' => $automationState,
                ]); ?>
            <?php endif; ?>
        </main>
    </div>

    <?php $managerTab = isset($_GET['tab']) ? (string) $_GET['tab'] : ''; ?>

    <?php if ($activeView === 'posts' || ($activeView === 'manager' && $managerTab === 'posts')): ?>
        <?php app_render_view('posts/post_modal.php'); ?>
    <?php endif; ?>

    <?php if ($activeView === 'innovation' || ($activeView === 'manager' && $managerTab === 'innovation')): ?>
        <?php app_render_view('innovation/modal.php'); ?>
    <?php endif; ?>

    <?php if (!(bool) ($authState['isAuthenticated'] ?? false) || (bool) ($authState['isAuthModalOpen'] ?? false)): ?>
        <?php app_render_view('shared/auth_modal.php', $authState); ?>
    <?php endif; ?>

    <script src="<?= h(app_asset_url('assets/js/shared.js')) ?>" defer></script>
    <script src="<?= h(app_asset_url('assets/js/posts.js')) ?>" defer></script>
    <script src="<?= h(app_asset_url('assets/js/innovation.js')) ?>" defer></script>
    <script src="<?= h(app_asset_url('assets/js/automation.js')) ?>" defer></script>
</body>
</html>
