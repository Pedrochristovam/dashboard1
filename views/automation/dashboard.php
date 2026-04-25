<?php
$isManagerContext = $isManagerContext ?? false;
$contextView = $isManagerContext ? 'manager' : 'automation';
$contextTab = $isManagerContext ? ($managerTab ?? 'automation') : null;
?>
<div class="topbar">
    <form class="searchbar" method="get">
        <input type="hidden" name="view" value="<?= h($contextView) ?>">
        <?php if ($contextTab !== null): ?>
            <input type="hidden" name="tab" value="<?= h($contextTab) ?>">
        <?php endif; ?>
        <span aria-hidden="true">⚙️</span>
        <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar por colaborador, setor, tipo ou necessidade...">
    </form>
    <button id="open-automation-btn" class="btn-primary publisher-toggle" type="button" aria-expanded="<?= $isAutomationPanelOpen ? 'true' : 'false' ?>">
        <?= $isAutomationPanelOpen ? 'Fechar pedido' : 'Novo pedido' ?>
    </button>
</div>

<?php if ($created): ?>
    <div class="status success global-status">Pedido enviado com sucesso.</div>
<?php elseif ($updated): ?>
    <div class="status success global-status">Acompanhamento do ticket atualizado com sucesso.</div>
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

        <div class="side-panels">
            <div class="panel stats-panel">
                <div class="panel-header">
                    <div>
                        <h2>Resumo operacional</h2>
                        <p>Visão rápida do volume e da urgência das solicitações recebidas.</p>
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat">
                        <strong><?= $totalRequests ?></strong>
                        <span class="muted">Pedidos cadastrados</span>
                    </div>
                    <div class="stat">
                        <strong><?= $highPriorityCount ?></strong>
                        <span class="muted">Alta prioridade</span>
                    </div>
                    <div class="stat">
                        <strong><?= $triageCount ?></strong>
                        <span class="muted">Em entrada ou triagem</span>
                    </div>
                    <div class="stat">
                        <strong><?= $quickWins ?></strong>
                        <span class="muted">Ganhos rápidos possíveis</span>
                    </div>
                    <div class="stat">
                        <strong><?= $completedCount ?></strong>
                        <span class="muted">Concluídos</span>
                    </div>
                    <div class="stat">
                        <strong><?= h($topRequesterDepartment) ?></strong>
                        <span class="muted">Área mais demandante</span>
                    </div>
                </div>
            </div>

            <div class="panel stats-panel">
                <div class="panel-header">
                    <div>
                        <h2>Sugestões de pedido</h2>
                        <p>Tipos de demanda que costumam gerar mais resultado com pouco esforço inicial.</p>
                    </div>
                </div>
                <div class="tag-wrap">
                    <span class="tag">Fluxo repetitivo</span>
                    <span class="tag">Relatório recorrente</span>
                    <span class="tag">Integração de sistemas</span>
                    <span class="tag">IA para atendimento</span>
                    <span class="tag">Apoio com planilhas</span>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="panel management-panel">
            <div class="panel-header">
                <div>
                    <h2>Acompanhamento de tickets</h2>
                    <p>Filtre solicitações e acompanhe status, responsável, comentários e histórico de mudanças.</p>
                </div>
            </div>
            <form class="management-filters" method="get">
                <input type="hidden" name="view" value="<?= h($contextView) ?>">
                <?php if ($contextTab !== null): ?>
                    <input type="hidden" name="tab" value="<?= h($contextTab) ?>">
                <?php endif; ?>
                <div class="field">
                    <label for="ticket-status-filter">Status</label>
                    <select id="ticket-status-filter" name="status">
                        <option value="">Todos os status</option>
                        <?php foreach ($statusLabels as $value => $label): ?>
                            <option value="<?= h($value) ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="ticket-sector-filter">Setor</label>
                    <select id="ticket-sector-filter" name="sector">
                        <option value="">Todos os setores</option>
                        <?php foreach ($sectorOptions as $value => $label): ?>
                            <option value="<?= h($value) ?>" <?= $sectorFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="ticket-priority-filter">Prioridade</label>
                    <select id="ticket-priority-filter" name="priority">
                        <option value="">Todas as prioridades</option>
                        <?php foreach ($priorityOptions as $value => $label): ?>
                            <option value="<?= h($value) ?>" <?= $priorityFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="actions">
                    <button class="btn-secondary" type="submit">Aplicar</button>
                    <a class="btn-tertiary" href="index.php?view=<?= h($contextView) ?><?= $contextTab !== null ? '&tab=' . h($contextTab) : '' ?>">Limpar</a>
                </div>
            </form>
        </div>

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
                    $frequencyLabel = (string) ($frequencyOptions[$request['frequency']] ?? (string) $request['frequency']);
                    ?>
                    <article class="panel request-card">
                        <div class="request-card-top">
                            <div class="request-person">
                                <strong><?= h((string) $request['requester']) ?></strong>
                                <span class="muted"><?= h((string) ($request['requester_department_label'] ?? '')) ?> • <?= h(date('d/m/Y H:i', strtotime((string) $request['created_at']))) ?></span>
                            </div>
                            <form method="post" onsubmit="return confirm('Deseja excluir este pedido?');">
                                <input type="hidden" name="dashboard_context" value="automation">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                <button class="delete-btn" type="submit" aria-label="Excluir pedido" title="Excluir pedido">🗑</button>
                            </form>
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
                                <span class="muted">Frequência</span>
                                <strong><?= h($frequencyLabel) ?></strong>
                            </div>
                        </div>

                        <div class="request-block">
                            <span class="muted">Atividade impactada</span>
                            <p><?= h(truncate_text((string) $request['activity'], 120)) ?></p>
                        </div>

                        <div class="request-block">
                            <span class="muted">Resultado esperado</span>
                            <p><?= h(truncate_text((string) $request['expected_result'], 130)) ?></p>
                        </div>

                        <div class="request-block">
                            <span class="muted">Responsável atual</span>
                            <p><?= h((string) ($request['assignee'] ?? 'Não atribuído')) ?></p>
                        </div>

                        <div class="request-manage-grid">
                            <form method="post" class="request-manage-form">
                                <input type="hidden" name="dashboard_context" value="automation">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                <div class="field">
                                    <label>Status</label>
                                    <select name="status">
                                        <?php foreach ($statusLabels as $value => $label): ?>
                                            <option value="<?= h($value) ?>" <?= $statusKey === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>Responsável</label>
                                    <input name="assignee" type="text" value="<?= h((string) ($request['assignee'] ?? '')) ?>" placeholder="Nome do responsável">
                                </div>
                                <button class="btn-tertiary" type="submit">Salvar acompanhamento</button>
                            </form>

                            <form method="post" class="request-manage-form">
                                <input type="hidden" name="dashboard_context" value="automation">
                                <input type="hidden" name="action" value="add_comment">
                                <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                                <div class="field">
                                    <label>Comentário</label>
                                    <textarea name="comment" placeholder="Registre alinhamentos, bloqueios ou devolutivas."></textarea>
                                </div>
                                <button class="btn-secondary" type="submit">Adicionar comentário</button>
                            </form>
                        </div>

                        <div class="request-timeline">
                            <strong>Histórico do ticket</strong>
                            <?php foreach (array_slice(array_reverse((array) ($request['timeline'] ?? [])), 0, 4) as $timelineItem): ?>
                                <div class="request-timeline-item">
                                    <span><?= h((string) ($timelineItem['label'] ?? 'Atualização')) ?></span>
                                    <small><?= h((string) ($timelineItem['author'] ?? 'Equipe')) ?> • <?= h(date('d/m/Y H:i', strtotime((string) ($timelineItem['created_at'] ?? 'now')))) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ((array) ($request['comments'] ?? []) !== []): ?>
                            <div class="request-comments">
                                <strong>Últimos comentários</strong>
                                <?php foreach (array_slice(array_reverse((array) ($request['comments'] ?? [])), 0, 3) as $comment): ?>
                                    <div class="request-comment">
                                        <span><?= h((string) ($comment['message'] ?? '')) ?></span>
                                        <small><?= h((string) ($comment['author'] ?? 'Equipe')) ?> • <?= h(date('d/m/Y H:i', strtotime((string) ($comment['created_at'] ?? 'now')))) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="request-footer">
                            <span class="request-deadline"><?= trim((string) $request['deadline']) !== '' ? 'Prazo desejado: ' . h(date('d/m/Y', strtotime((string) $request['deadline']))) : 'Prazo não informado' ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
