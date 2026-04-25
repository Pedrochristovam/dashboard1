<?php
$currentUser = $authState['currentUser'] ?? null;
$isManager = auth_is_manager();
$managerTab = isset($_GET['tab']) ? trim((string) $_GET['tab']) : 'overview';
$allowedTabs = ['overview', 'posts', 'innovation', 'automation'];
if (!in_array($managerTab, $allowedTabs, true)) {
    $managerTab = 'overview';
}
?>

<?php if (!$isManager): ?>
    <section class="panel empty-state">
        Esta área é reservada para gestores e administradores.
    </section>
<?php else: ?>
    <div class="topbar">
        <div class="searchbar">
            <span aria-hidden="true">🛠</span>
            <input type="text" value="Painel do gestor para métricas, governança e acompanhamento" readonly>
        </div>
    </div>

    <section class="panel management-panel">
        <div class="panel-header">
            <div>
                <h2>Painel do gestor</h2>
                <p>Área exclusiva para métricas, governança editorial, evolução dinâmica dos setores e acompanhamento completo dos tickets.</p>
            </div>
        </div>
        <div class="tag-wrap">
            <a class="tag <?= $managerTab === 'overview' ? 'is-active' : '' ?>" href="index.php?view=manager&tab=overview">Visão geral</a>
            <a class="tag <?= $managerTab === 'posts' ? 'is-active' : '' ?>" href="index.php?view=manager&tab=posts">Publicações</a>
            <a class="tag <?= $managerTab === 'innovation' ? 'is-active' : '' ?>" href="index.php?view=manager&tab=innovation">Setores</a>
            <a class="tag <?= $managerTab === 'automation' ? 'is-active' : '' ?>" href="index.php?view=manager&tab=automation">Tickets</a>
        </div>
        <?php if ($currentUser !== null): ?>
            <div class="muted">Sessão do gestor: <?= h((string) $currentUser['name']) ?> • <?= h((string) $currentUser['department_label']) ?> • <?= h((string) $currentUser['role_label']) ?></div>
        <?php endif; ?>
    </section>

    <?php if ($managerTab === 'overview'): ?>
        <?php app_render_view('shared/analytics_overview.php', $analyticsState); ?>

        <section class="manager-grid">
            <div class="panel analytics-card">
                <span class="muted">Publicações mais abertas</span>
                <?php foreach (($analyticsState['analyticsTopPosts'] ?? []) as $title => $count): ?>
                    <div class="sidebar-mini-item">
                        <span><?= h((string) $title) ?></span>
                        <strong><?= (int) $count ?></strong>
                    </div>
                <?php endforeach; ?>
                <?php if (($analyticsState['analyticsTopPosts'] ?? []) === []): ?>
                    <p>Sem dados suficientes nesta sessão.</p>
                <?php endif; ?>
            </div>
            <div class="panel analytics-card">
                <span class="muted">Setores com mais interação</span>
                <?php foreach (($analyticsState['analyticsTopSectors'] ?? []) as $sectorLabel => $count): ?>
                    <div class="sidebar-mini-item">
                        <span><?= h((string) $sectorLabel) ?></span>
                        <strong><?= (int) $count ?></strong>
                    </div>
                <?php endforeach; ?>
                <?php if (($analyticsState['analyticsTopSectors'] ?? []) === []): ?>
                    <p>Os dados aparecerão conforme o time usar o hub.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php elseif ($managerTab === 'posts'): ?>
        <?php extract($postsState, EXTR_SKIP); ?>
        <?php app_render_view('posts/dashboard.php', $postsState + ['isManagerContext' => true, 'managerTab' => 'posts']); ?>
    <?php elseif ($managerTab === 'innovation'): ?>
        <?php extract($innovationState, EXTR_SKIP); ?>
        <?php app_render_view('innovation/dashboard.php', $innovationState + ['isManagerContext' => true, 'managerTab' => 'innovation']); ?>
    <?php else: ?>
        <?php extract($automationState, EXTR_SKIP); ?>
        <?php app_render_view('automation/dashboard.php', $automationState + ['isManagerContext' => true, 'managerTab' => 'automation']); ?>
    <?php endif; ?>
<?php endif; ?>
