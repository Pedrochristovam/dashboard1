<div class="topbar">
    <form class="searchbar" method="get">
        <input type="hidden" name="view" value="posts">
        <span aria-hidden="true">🔎</span>
        <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar por ferramentas, autores ou temas...">
    </form>
    <button id="open-publisher-btn" class="btn-primary publisher-toggle" type="button" aria-expanded="<?= $isPublisherOpen ? 'true' : 'false' ?>">
        <?= $isPublisherOpen ? 'Fechar publicação' : 'Nova publicação' ?>
    </button>
</div>

<?php if ($created): ?>
    <div class="status success global-status">Publicação criada com sucesso.</div>
<?php endif; ?>

<?php app_render_view('posts/publisher_panel.php', get_defined_vars()); ?>

<section>
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
                <?php app_render_view('posts/post_card.php', ['post' => $post, 'categoryLabels' => $categoryLabels]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
