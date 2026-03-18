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
    </nav>
</aside>
