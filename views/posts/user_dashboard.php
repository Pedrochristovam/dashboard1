<div class="topbar topbar--posts">
    <form class="searchbar searchbar--soft" method="get">
        <input type="hidden" name="view" value="posts">
        <span class="searchbar-icon" aria-hidden="true">⌕</span>
        <input type="text" name="search" value="<?= h($search) ?>" placeholder="Buscar ferramentas, autores ou temas…" autocomplete="off">
    </form>
    <button id="open-publisher-btn" class="btn-primary btn-primary--shine publisher-toggle" type="button" aria-expanded="<?= $isPublisherOpen ? 'true' : 'false' ?>">
        <?= $isPublisherOpen ? 'Fechar publicação' : 'Nova publicação' ?>
    </button>
</div>

<?php if ($created): ?>
    <div class="status success global-status">Publicação criada com sucesso.</div>
<?php elseif ($updated): ?>
    <div class="status success global-status">Publicação atualizada com sucesso.</div>
<?php endif; ?>

<?php app_render_view('posts/user_publisher_panel.php', get_defined_vars()); ?>

<section class="posts-feed-block">
    <div class="section-head section-head--editorial">
        <div class="section-head-copy">
            <span class="section-kicker">Conhecimento interno</span>
            <h1 class="section-title">Todas as publicações</h1>
            <p class="section-sub">Um feed com busca, autoria e tudo o que a equipe compartilha sobre IA e automação.</p>
        </div>
        <div class="tag-wrap tag-wrap--subtle">
            <span class="tag tag--ghost">#GenerativeAI</span>
            <span class="tag tag--ghost">#NoCode</span>
            <span class="tag tag--ghost">#ChatGPT</span>
            <span class="tag tag--ghost">#Analytics</span>
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
