<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-mark">AI</div>
        <div>
            <strong>Corporate Hub</strong>
            <div class="muted">Conhecimento e inovação</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="sidebar-link <?= $activeView === 'posts' ? 'is-active' : '' ?>" href="index.php?view=posts">
            <span>Compartilhamento de IA</span>
            <small>Publicações internas</small>
        </a>
        <a class="sidebar-link <?= $activeView === 'innovation' ? 'is-active' : '' ?>" href="index.php?view=innovation">
            <span>Evolução dos setores</span>
            <small>Monitoramento executivo</small>
        </a>
        <a class="sidebar-link <?= $activeView === 'automation' ? 'is-active' : '' ?>" href="index.php?view=automation">
            <span>Pedidos de automações</span>
            <small>Solicitações internas</small>
        </a>
        <?php if (auth_is_manager()): ?>
            <a class="sidebar-link <?= $activeView === 'manager' ? 'is-active' : '' ?>" href="index.php?view=manager">
                <span>Painel do gestor</span>
                <small>Métricas e governança</small>
            </a>
        <?php endif; ?>
    </nav>

    <?php if (auth_is_manager()): ?>
        <div class="sidebar-analytics panel">
            <div class="sidebar-user-card is-empty">
                <span class="sidebar-user-label">Engajamento</span>
                <strong><?= h($analyticsTopDepartment ?? 'Sem dados') ?></strong>
                <div class="muted">Departamento mais ativo na sessão atual.</div>
            </div>
            <div class="sidebar-mini-list">
                <?php foreach ($analyticsTopDepartments as $departmentLabel => $count): ?>
                    <div class="sidebar-mini-item">
                        <span><?= h((string) $departmentLabel) ?></span>
                        <strong><?= (int) $count ?></strong>
                    </div>
                <?php endforeach; ?>
                <?php if ($analyticsTopDepartments === []): ?>
                    <div class="sidebar-mini-item">
                        <span>Aguardando uso</span>
                        <strong>0</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</aside>
