<div class="topbar">
    <form class="searchbar" method="get">
        <input type="hidden" name="view" value="automation">
        <span aria-hidden="true">⚙️</span>
        <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar por colaborador, setor, tipo ou necessidade...">
    </form>
    <button id="open-automation-btn" class="btn-primary publisher-toggle" type="button" aria-expanded="<?= $isAutomationPanelOpen ? 'true' : 'false' ?>">
        <?= $isAutomationPanelOpen ? 'Fechar pedido' : 'Novo pedido' ?>
    </button>
</div>

<?php if ($created): ?>
    <div class="status success global-status">Pedido enviado com sucesso.</div>
<?php endif; ?>

<section class="automation-dashboard">
    <section
        id="automation-panel"
        class="publisher-shell <?= $isAutomationPanelOpen ? 'is-open' : '' ?>"
        data-open="<?= $isAutomationPanelOpen ? 'true' : 'false' ?>"
    >
        <div class="panel form-panel">
            <div class="panel-header">
                <div>
                    <h2>Novo pedido de automação</h2>
                    <p>Registre uma necessidade operacional, automação ou apoio específico para acelerar a rotina da equipe.</p>
                </div>
                <div class="panel-header-actions">
                    <span class="badge">Solicitação interna</span>
                    <button class="close-panel-btn" type="button" data-close-automation aria-label="Fechar painel">✕</button>
                </div>
            </div>

            <?php if ($errors !== []): ?>
                <div class="status error"><?= h(implode(' ', $errors)) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="dashboard_context" value="automation">
                <input type="hidden" name="action" value="create">
                <div class="form-grid">
                    <div class="field">
                        <label for="requester">Colaborador</label>
                        <input id="requester" name="requester" type="text" placeholder="Nome do solicitante" value="<?= h($formData['requester']) ?>" readonly>
                        <small class="field-help">Preenchido a partir da sua sessão atual.</small>
                    </div>

                    <div class="field">
                        <label for="sector">Setor</label>
                        <select id="sector" name="sector" disabled>
                            <?php foreach ($sectorOptions as $value => $label): ?>
                                <option value="<?= h($value) ?>" <?= $formData['sector'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="sector" value="<?= h($formData['sector']) ?>">
                    </div>

                    <div class="field full">
                        <label for="title">Título do pedido</label>
                        <input id="automation_title" name="title" type="text" placeholder="Ex: Automatizar envio de relatório semanal" value="<?= h($formData['title']) ?>">
                    </div>

                    <div class="field">
                        <label for="type">Tipo de necessidade</label>
                        <select id="type" name="type">
                            <?php foreach ($typeOptions as $value => $label): ?>
                                <option value="<?= h($value) ?>" <?= $formData['type'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="priority">Prioridade</label>
                        <select id="priority" name="priority">
                            <?php foreach ($priorityOptions as $value => $label): ?>
                                <option value="<?= h($value) ?>" <?= $formData['priority'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="frequency">Frequência da atividade</label>
                        <select id="frequency" name="frequency">
                            <?php foreach ($frequencyOptions as $value => $label): ?>
                                <option value="<?= h($value) ?>" <?= $formData['frequency'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="deadline">Prazo desejado</label>
                        <input id="deadline" name="deadline" type="date" value="<?= h($formData['deadline']) ?>">
                    </div>

                    <div class="field full">
                        <label for="activity">Atividade impactada</label>
                        <textarea id="activity" name="activity" placeholder="Explique qual tarefa ou processo precisa de apoio."><?= h($formData['activity']) ?></textarea>
                    </div>

                    <div class="field full">
                        <label for="need">Problema atual</label>
                        <textarea id="need" name="need" placeholder="O que hoje é manual, demorado ou gera retrabalho?"><?= h($formData['need']) ?></textarea>
                    </div>

                    <div class="field full">
                        <label for="expected_result">Resultado esperado</label>
                        <textarea id="expected_result" name="expected_result" placeholder="Qual melhoria você espera obter com essa automação ou ajuda?"><?= h($formData['expected_result']) ?></textarea>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn-primary" type="submit">Enviar pedido</button>
                </div>
            </form>
        </div>
    </section>

    <section>
        <div class="section-head">
            <div>
                <strong>Pedidos de automações</strong>
                <div class="muted">Fila de necessidades operacionais e oportunidades de melhoria levantadas pelos colaboradores.</div>
            </div>
            <div class="tag-wrap">
                <span class="tag">Triagem</span>
                <span class="tag">Planejamento</span>
                <span class="tag">Ganho de produtividade</span>
            </div>
        </div>

        <?php if ($requests === []): ?>
            <div class="panel empty-state">
                Nenhum pedido encontrado para a busca aplicada.
            </div>
        <?php else: ?>
            <div class="request-grid">
                <?php foreach ($requests as $request): ?>
                    <?php
                    $statusKey = (string) $request['status'];
                    $priorityKey = (string) $request['priority'];
                    $statusLabel = (string) ($statusLabels[$statusKey] ?? ucfirst($statusKey));
                    $statusClass = automation_status_badge($statusKey);
                    $priorityClass = automation_priority_badge($priorityKey);
                    $sectorLabel = (string) ($sectorOptions[$request['sector']] ?? strtoupper((string) $request['sector']));
                    $typeLabel = (string) ($typeOptions[$request['type']] ?? (string) $request['type']);
                    ?>
                    <article class="panel request-card">
                        <div class="request-card-top">
                            <div class="request-person">
                                <strong><?= h((string) $request['requester']) ?></strong>
                                <span class="muted"><?= h((string) ($request['requester_department_label'] ?? '')) ?> • <?= h(date('d/m/Y H:i', strtotime((string) $request['created_at']))) ?></span>
                            </div>
                        </div>

                        <h3 class="request-title"><?= h(truncate_text((string) $request['title'], 68)) ?></h3>
                        <p class="request-summary"><?= h(truncate_text((string) $request['need'], 150)) ?></p>

                        <div class="request-chip-row">
                            <span class="request-chip status-<?= h($statusClass) ?>"><?= h($statusLabel) ?></span>
                            <span class="request-chip priority-<?= h($priorityClass) ?>"><?= h((string) ($priorityOptions[$priorityKey] ?? ucfirst($priorityKey))) ?></span>
                            <span class="request-chip neutral"><?= h($sectorLabel) ?></span>
                        </div>

                        <div class="request-details">
                            <div class="request-detail">
                                <span class="muted">Tipo</span>
                                <strong><?= h($typeLabel) ?></strong>
                            </div>
                            <div class="request-detail">
                                <span class="muted">Responsável</span>
                                <strong><?= h((string) ($request['assignee'] ?? 'Não atribuído')) ?></strong>
                            </div>
                        </div>

                        <div class="request-block">
                            <span class="muted">Resultado esperado</span>
                            <p><?= h(truncate_text((string) $request['expected_result'], 130)) ?></p>
                        </div>

                        <div class="request-footer">
                            <span class="request-deadline"><?= trim((string) $request['deadline']) !== '' ? 'Prazo desejado: ' . h(date('d/m/Y', strtotime((string) $request['deadline']))) : 'Prazo não informado' ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
