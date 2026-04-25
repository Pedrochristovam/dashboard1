<?php
$isManagerContext = $isManagerContext ?? false;
$contextView = $isManagerContext ? 'manager' : 'posts';
$contextTab = $isManagerContext ? ($managerTab ?? 'posts') : null;
?>
<div class="topbar">
    <form class="searchbar" method="get">
        <input type="hidden" name="view" value="<?= h($contextView) ?>">
        <?php if ($contextTab !== null): ?>
            <input type="hidden" name="tab" value="<?= h($contextTab) ?>">
        <?php endif; ?>
        <span aria-hidden="true">🔎</span>
        <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar por ferramentas, autores ou temas...">
    </form>
    <button id="open-publisher-btn" class="btn-primary publisher-toggle" type="button" aria-expanded="<?= $isPublisherOpen ? 'true' : 'false' ?>">
        <?= $isPublisherOpen ? 'Fechar publicação' : 'Nova publicação' ?>
    </button>
</div>

<?php if ($created): ?>
    <div class="status success global-status">Publicação criada com sucesso.</div>
<?php elseif ($updated): ?>
    <div class="status success global-status">Publicação atualizada com sucesso.</div>
<?php elseif ($managed): ?>
    <div class="status success global-status">Status editorial atualizado.</div>
<?php endif; ?>

<?php app_render_view('posts/publisher_panel.php', get_defined_vars()); ?>

<section>
    <div class="panel management-panel">
        <div class="panel-header">
            <div>
                <h2>Gestão de publicações</h2>
                <p>Filtre o acervo por autor, departamento, categoria e status. Use a lista abaixo para editar ou mudar o status editorial.</p>
            </div>
        </div>
        <form class="management-filters" method="get">
            <input type="hidden" name="view" value="<?= h($contextView) ?>">
            <?php if ($contextTab !== null): ?>
                <input type="hidden" name="tab" value="<?= h($contextTab) ?>">
            <?php endif; ?>
            <div class="field">
                <label for="post-filter-category">Categoria</label>
                <select id="post-filter-category" name="category">
                    <?php foreach ($categoryLabels as $value => $label): ?>
                        <option value="<?= h($value) ?>" <?= $categoryFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="post-filter-status">Status</label>
                <select id="post-filter-status" name="status">
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= h($value) ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="post-filter-department">Departamento</label>
                <select id="post-filter-department" name="department">
                    <option value="">Todos os departamentos</option>
                    <?php foreach ($departmentOptions as $value => $label): ?>
                        <option value="<?= h($value) ?>" <?= $departmentFilter === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="post-filter-author">Autor</label>
                <select id="post-filter-author" name="author">
                    <option value="">Todos os autores</option>
                    <?php foreach ($authorOptions as $author): ?>
                        <option value="<?= h($author) ?>" <?= $authorFilter === $author ? 'selected' : '' ?>><?= h($author) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="actions">
                <button class="btn-secondary" type="submit">Aplicar filtros</button>
                <a class="btn-tertiary" href="index.php?view=<?= h($contextView) ?><?= $contextTab !== null ? '&tab=' . h($contextTab) : '' ?>">Limpar</a>
            </div>
        </form>

        <div class="post-manage-list">
            <?php foreach ($posts as $post): ?>
                <div class="post-manage-item">
                    <div>
                        <strong><?= h((string) $post['tool_name']) ?></strong>
                        <div class="muted"><?= h((string) $post['author']) ?> • <?= h((string) ($post['department_label'] ?? '')) ?> • <?= h((string) ($statusLabels[$post['status']] ?? $post['status'])) ?></div>
                    </div>
                    <div class="post-manage-actions">
                        <form method="post" class="post-status-form">
                            <input type="hidden" name="dashboard_context" value="posts">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                            <select name="status">
                                <?php foreach ($statusLabels as $value => $label): ?>
                                    <?php if ($value === '') { continue; } ?>
                                    <option value="<?= h($value) ?>" <?= (string) $post['status'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn-tertiary" type="submit">Atualizar</button>
                        </form>
                        <a class="btn-secondary" href="index.php?view=<?= h($contextView) ?><?= $contextTab !== null ? '&tab=' . h($contextTab) : '' ?>&edit=<?= (int) $post['id'] ?>">Editar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="section-head">
        <div>
            <strong>Todas as publicações</strong>
            <div class="muted">Feed único com busca, relevância e dados do autor.</div>
        </div>
        <div class="tag-wrap">
            <span class="tag">#GenerativeAI</span>
            <span class="tag">#NoCode</span>
            <span class="tag">#ChatGPT</span>
            <span class="tag">#Analytics</span>
        </div>
    </div>

    <?php if ($posts === []): ?>
        <div class="panel empty-state">
            Nenhuma publicação encontrada para a busca aplicada.
        </div>
    <?php else: ?>
        <div class="feed">
            <?php foreach ($posts as $post): ?>
                <?php app_render_view('posts/post_card.php', ['post' => $post, 'categoryLabels' => $categoryLabels, 'statusLabels' => $statusLabels]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
