<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI KnowledgeHub</title>
    <link rel="stylesheet" href="<?= h(app_asset_url('assets/css/app.css')) ?>">
</head>
<body>
    <div class="shell">
        <?php app_render_view('shared/sidebar.php', ['activeView' => $activeView]); ?>

        <main class="content with-sidebar">
            <?php if ($activeView === 'posts'): ?>
                <?php extract($postsState, EXTR_SKIP); ?>
                <?php app_render_view('posts/dashboard.php', $postsState); ?>
            <?php elseif ($activeView === 'innovation'): ?>
                <?php extract($innovationState, EXTR_SKIP); ?>
                <?php app_render_view('innovation/dashboard.php', $innovationState); ?>
            <?php else: ?>
                <?php extract($automationState, EXTR_SKIP); ?>
                <?php app_render_view('automation/dashboard.php', $automationState); ?>
            <?php endif; ?>
        </main>
    </div>

    <?php if ($activeView === 'posts'): ?>
        <?php app_render_view('posts/post_modal.php'); ?>
    <?php endif; ?>

    <?php if ($activeView === 'innovation'): ?>
        <?php app_render_view('innovation/modal.php'); ?>
    <?php endif; ?>

    <script src="<?= h(app_asset_url('assets/js/shared.js')) ?>" defer></script>
    <script src="<?= h(app_asset_url('assets/js/posts.js')) ?>" defer></script>
    <script src="<?= h(app_asset_url('assets/js/innovation.js')) ?>" defer></script>
    <script src="<?= h(app_asset_url('assets/js/automation.js')) ?>" defer></script>
</body>
</html>
