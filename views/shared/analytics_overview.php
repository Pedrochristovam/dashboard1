<section class="analytics-overview">
    <div class="panel analytics-card">
        <span class="muted">Interações registradas</span>
        <strong><?= (int) $analyticsTotalEvents ?></strong>
        <p>Métricas prototípicas da sessão atual para medir uso e engajamento do hub.</p>
    </div>
    <div class="panel analytics-card">
        <span class="muted">Módulo mais acessado</span>
        <strong><?= h($analyticsTopModule !== null ? ucfirst($analyticsTopModule) : 'Sem dados') ?></strong>
        <p><?= $analyticsTopModuleCount > 0 ? $analyticsTopModuleCount . ' eventos registrados' : 'Aguardando navegação nas telas' ?></p>
    </div>
    <div class="panel analytics-card">
        <span class="muted">Departamento mais envolvido</span>
        <strong><?= h($analyticsTopDepartment ?? 'Sem dados') ?></strong>
        <p><?= $analyticsTopDepartmentCount > 0 ? $analyticsTopDepartmentCount . ' interações na sessão' : 'Faça login e use o hub para gerar dados' ?></p>
    </div>
    <div class="panel analytics-card">
        <span class="muted">Tempo médio por tela</span>
        <strong>
            Posts <?= (int) ($analyticsModuleAverages['posts'] ?? 0) ?>s
            • Inovação <?= (int) ($analyticsModuleAverages['innovation'] ?? 0) ?>s
            • Tickets <?= (int) ($analyticsModuleAverages['automation'] ?? 0) ?>s
        </strong>
        <p>O cálculo ignora abas inativas e eventos sem duração válida.</p>
    </div>
</section>
