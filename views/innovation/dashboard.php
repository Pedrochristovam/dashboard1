<div class="topbar">
    <form class="searchbar" method="get">
        <input type="hidden" name="view" value="innovation">
        <span aria-hidden="true">📊</span>
        <input type="text" value="Dashboard executivo de inovação por setor" readonly>
    </form>
</div>

<?php if ($saved): ?>
    <div class="status success global-status">Documento salvo com sucesso.</div>
<?php endif; ?>

<section class="innovation-dashboard">
    <div class="innovation-toolbar panel">
        <div>
            <strong>Monitoramento de inovação por setor</strong>
            <div class="muted">Acompanhe a evolução, compare áreas e identifique onde acelerar iniciativas.</div>
        </div>
        <form class="period-filter" method="get">
            <input type="hidden" name="view" value="innovation">
            <label for="period" class="muted">Período</label>
            <select id="period" name="period" onchange="this.form.submit()">
                <?php foreach ($periods as $value => $label): ?>
                    <option value="<?= h($value) ?>" <?= $selectedPeriod === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="innovation-overview">
        <div class="panel overview-card emphasis">
            <span class="muted">Setor líder</span>
            <strong id="innovation-top-sector"><?= h((string) ($topSector['name'] ?? '-')) ?></strong>
            <p id="innovation-top-sector-copy">Maior percentual informado atualmente na visão de acompanhamento.</p>
        </div>
        <div class="panel overview-card">
            <span class="muted">Média de evolução</span>
            <strong id="innovation-avg-progress"><?= $avgProgress ?>%</strong>
            <p>Visão consolidada do avanço tecnológico entre os setores monitorados.</p>
        </div>
        <div class="panel overview-card">
            <span class="muted">Setores em atenção</span>
            <strong id="innovation-attention-count"><?= count($attentionSectors) ?></strong>
            <p>Áreas que demandam reforço em governança, adoção ou aceleração de iniciativas.</p>
        </div>
    </div>

    <div class="innovation-grid">
        <div class="innovation-main">
            <div class="panel sectors-panel">
                <div class="panel-header">
                    <div>
                        <h2>Setores da empresa</h2>
                        <p>Visão executiva com nível de inovação, progresso e volume de iniciativas implementadas.</p>
                    </div>
                </div>

                <div class="sector-list">
                    <?php foreach ($sectors as $sector): ?>
                        <?php $levelClass = innovation_level_badge((string) $sector['level']); ?>
                        <?php $updatedLabel = trim((string) $sector['updated_at']) !== '' ? date('d/m/Y', strtotime((string) $sector['updated_at'])) : 'Sem atualização'; ?>
                        <?php $improvementsText = trim((string) ($sector['summary'] ?? '')); ?>
                        <?php $documents = isset($sector['documents']) && is_array($sector['documents']) ? $sector['documents'] : []; ?>
                        <article
                            class="sector-card <?= (int) $sector['progress'] === 0 ? 'is-empty' : '' ?>"
                            data-sector-card
                            data-sector-id="<?= h((string) $sector['id']) ?>"
                            data-name="<?= h((string) $sector['name']) ?>"
                            data-level="<?= h((string) $sector['level']) ?>"
                            data-progress="<?= (int) $sector['progress'] ?>"
                            data-initiatives="<?= (int) $sector['initiatives'] ?>"
                            data-updated="<?= h($updatedLabel) ?>"
                            data-summary="<?= h((string) $sector['summary']) ?>"
                            data-improvements="<?= h($improvementsText) ?>"
                            data-highlights="<?= h(implode(' | ', $sector['highlights'])) ?>"
                            data-trend="<?= h(implode(',', array_map('strval', $sector['trend']))) ?>"
                        >
                            <form method="post" class="sector-form">
                                <input type="hidden" name="dashboard_context" value="innovation">
                                <input type="hidden" name="action" value="save_sector" data-sector-action-input>
                                <input type="hidden" name="period" value="<?= h($selectedPeriod) ?>">
                                <input type="hidden" name="sector_id" value="<?= h((string) $sector['id']) ?>">
                                <input type="hidden" name="document_id" value="" data-document-id-input>

                                <div class="sector-card-top">
                                    <div>
                                        <h3><?= h((string) $sector['name']) ?></h3>
                                        <span class="sector-level <?= h($levelClass) ?>" data-sector-level><?= h((string) $sector['level']) ?></span>
                                    </div>
                                    <div class="sector-score"><?= (int) $sector['progress'] ?>%</div>
                                </div>

                                <div class="progress-track">
                                    <div class="progress-bar" style="width: <?= (int) $sector['progress'] ?>%"></div>
                                </div>
                                <div class="sector-progress-caption" data-sector-progress-caption>
                                    <?= (int) $sector['progress'] === 0 ? 'Aguardando lançamento de dados' : 'Progresso consolidado do período' ?>
                                </div>

                                <div class="sector-documents-editor">
                                    <div class="sector-documents-top">
                                        <label>Documentos salvos</label>
                                        <button class="btn-tertiary document-new-trigger" type="button">Novo documento</button>
                                    </div>
                                    <div class="document-chip-list">
                                        <?php if ($documents === []): ?>
                                            <span class="document-empty">Nenhum documento salvo ainda</span>
                                        <?php else: ?>
                                            <?php foreach ($documents as $document): ?>
                                                <button
                                                    class="document-chip"
                                                    type="button"
                                                    data-document-chip
                                                    data-document-id="<?= h((string) $document['id']) ?>"
                                                    data-document-label="<?= h((string) $document['label']) ?>"
                                                    data-document-content="<?= h((string) $document['content']) ?>"
                                                >
                                                    <?= h((string) $document['label']) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sector-progress-hint" data-progress-hint>
                                        Cada novo documento salvo adiciona <?= innovation_document_progress_step() ?>% ao progresso do setor.
                                    </div>
                                </div>

                                <div class="sector-improvement-editor">
                                    <div class="sector-editor-header">
                                        <label for="improvements-<?= h((string) $sector['id']) ?>">Conteúdo do documento</label>
                                        <span class="editor-mode-badge" data-editor-mode>Pronto para novo documento</span>
                                    </div>
                                    <textarea
                                        id="improvements-<?= h((string) $sector['id']) ?>"
                                        name="improvements"
                                        rows="4"
                                        placeholder="Descreva aqui as melhorias, ações previstas ou evoluções desse setor..."
                                        data-improvements-input
                                    ></textarea>
                                </div>

                                <div class="sector-meta-grid">
                                    <div class="sector-meta">
                                        <span class="muted">Documentos</span>
                                        <strong><?= (int) $sector['initiatives'] ?></strong>
                                    </div>
                                    <div class="sector-meta">
                                        <span class="muted">Última atualização</span>
                                        <strong><?= h($updatedLabel) ?></strong>
                                    </div>
                                </div>

                                <div class="sector-card-actions">
                                    <button class="btn-primary sector-save-btn" type="submit">Salvar documento</button>
                                    <button class="btn-tertiary sector-delete-btn" type="submit" data-delete-document hidden>Excluir documento</button>
                                    <button class="btn-tertiary sector-detail-trigger" type="button">Ver detalhes</button>
                                </div>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="innovation-side">
            <div class="panel ranking-panel">
                <div class="panel-header">
                    <div>
                        <h2>Ranking de inovação</h2>
                        <p>Setores com melhor desempenho no período selecionado.</p>
                    </div>
                </div>
                <div class="ranking-list" id="innovation-ranking-list">
                    <?php foreach ($ranking as $index => $sector): ?>
                        <div class="ranking-item">
                            <span class="ranking-position">#<?= $index + 1 ?></span>
                            <div class="ranking-copy">
                                <strong><?= h((string) $sector['name']) ?></strong>
                                <span class="muted"><?= (int) $sector['initiatives'] ?> iniciativas</span>
                            </div>
                            <span class="ranking-score"><?= (int) $sector['progress'] ?>%</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="panel attention-panel">
                <div class="panel-header">
                    <div>
                        <h2>Áreas que pedem atenção</h2>
                        <p>Destaque para setores com menor tração ou menor padronização de inovação.</p>
                    </div>
                </div>
                <div class="attention-list" id="innovation-attention-list">
                    <?php foreach ($attentionSectors as $sector): ?>
                        <div class="attention-item">
                            <strong><?= h((string) $sector['name']) ?></strong>
                            <span class="muted"><?= (int) $sector['progress'] ?>% de evolução</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
