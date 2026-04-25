<?php
$hasVideoUrl = trim((string) ($post['video_url'] ?? '')) !== '';
$hasToolUrl = trim((string) ($post['tool_url'] ?? '')) !== '';
$hasArticleUrl = trim((string) ($post['article_url'] ?? '')) !== '';
$document = is_array($post['document'] ?? null) ? $post['document'] : null;
$hasDocument = $document !== null && trim((string) ($document['url'] ?? '')) !== '';
$photos = is_array($post['photos'] ?? null) ? array_values($post['photos']) : [];
$hasPhotos = $photos !== [];
$statusLabel = (string) ($statusLabels[(string) ($post['status'] ?? 'published')] ?? ucfirst((string) ($post['status'] ?? 'published')));
$youtubeVideoId = $hasVideoUrl ? parse_youtube_video_id((string) $post['video_url']) : null;
$previewText = (string) ($post['preview_title'] ?? '') !== ''
    ? (string) $post['preview_title']
    : ($hasPhotos
        ? sprintf('Galeria com %d foto%s anexada%s', count($photos), count($photos) === 1 ? '' : 's', count($photos) === 1 ? '' : 's')
        : ($hasDocument
            ? 'Documento complementar anexado'
            : ($hasVideoUrl && $hasToolUrl
                ? 'Vídeo e ferramenta compartilhados'
                : ($hasVideoUrl ? 'Vídeo compartilhado' : 'Ferramenta compartilhada'))));
$cardTitle = truncate_text((string) $post['tool_name'], 54);
$cardSummary = truncate_text((string) $post['summary'], 112);
$cardPreview = truncate_text($previewText, 78);
$categoryLabel = (string) ($categoryLabels[$post['category']] ?? ucfirst((string) $post['category']));
$photosPayload = array_map(
    static fn(array $photo): array => ['url' => (string) ($photo['url'] ?? '')],
    $photos
);
$photosJson = json_encode($photosPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if (!is_string($photosJson)) {
    $photosJson = '[]';
}
$canDeletePost = function_exists('auth_is_manager') && auth_is_manager();
$cardDetailParts = [];
if ($hasVideoUrl) {
    $cardDetailParts[] = 'vídeo';
}
if ($hasToolUrl) {
    $cardDetailParts[] = 'link';
}
if ($hasDocument) {
    $cardDetailParts[] = 'documento';
}
if ($hasPhotos) {
    $cardDetailParts[] = count($photos) . ' foto' . (count($photos) === 1 ? '' : 's');
}
if ($hasArticleUrl) {
    $cardDetailParts[] = 'artigo';
}
?>
<article
    class="panel card post-card"
    tabindex="0"
    data-card
    data-title="<?= h((string) $post['tool_name']) ?>"
    data-author="<?= h((string) $post['author']) ?>"
    data-department="<?= h((string) ($post['department_label'] ?? '')) ?>"
    data-status="<?= h($statusLabel) ?>"
    data-date="<?= h(app_format_datetime((string) ($post['published_at'] ?? ''))) ?>"
    data-category="<?= h($categoryLabel) ?>"
    data-thumbnail="<?= h((string) $post['thumbnail']) ?>"
    data-preview="<?= h($previewText) ?>"
    data-description="<?= h((string) $post['description']) ?>"
    data-learning-tips="<?= h((string) ($post['learning_tips'] ?? '')) ?>"
    data-prompts-used="<?= h((string) ($post['prompts_used'] ?? '')) ?>"
    data-video-url="<?= h((string) ($post['video_url'] ?? '')) ?>"
    data-tool-url="<?= h((string) ($post['tool_url'] ?? '')) ?>"
    data-article-url="<?= h((string) ($post['article_url'] ?? '')) ?>"
    data-youtube-id="<?= h((string) ($youtubeVideoId ?? '')) ?>"
    data-document-url="<?= h((string) ($document['url'] ?? '')) ?>"
    data-document-name="<?= h((string) ($document['name'] ?? 'Documento anexo')) ?>"
    data-photos="<?= h($photosJson) ?>"
>
    <div class="card-media">
        <?php if ($canDeletePost): ?>
            <form class="card-delete-overlay" method="post" onsubmit="return confirm('Deseja excluir esta publicação?');">
                <input type="hidden" name="dashboard_context" value="posts">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                <button class="delete-btn" type="submit" aria-label="Excluir publicação" title="Excluir publicação">
                    <span aria-hidden="true">✕</span>
                </button>
            </form>
        <?php endif; ?>
        <img src="<?= h((string) $post['thumbnail']) ?>" alt="<?= h((string) $post['tool_name']) ?>">
        <span class="card-tag"><?= h($categoryLabel) ?></span>
        <?php if ($hasPhotos): ?>
            <span class="card-media-badge"><?= count($photos) ?> foto<?= count($photos) === 1 ? '' : 's' ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="card-topline">
            <span class="card-author"><?= h((string) $post['author']) ?></span>
            <span class="card-date"><?= h((string) ($post['department_label'] ?? '')) ?> • <?= h(app_format_datetime((string) ($post['published_at'] ?? ''))) ?></span>
        </div>

        <h3><?= h($cardTitle) ?></h3>
        <p><?= h($cardSummary) ?></p>

        <p class="card-preview-hint <?= (string) ($post['preview_title'] ?? '') === '' ? 'is-empty' : '' ?>">
            <?= h($cardPreview) ?>
        </p>

        <div class="card-footer">
            <button class="card-open-trigger card-primary-cta" type="button">Ver detalhes</button>
            <?php if ($hasVideoUrl || $hasToolUrl || $hasDocument || $hasArticleUrl): ?>
                <div class="card-extras" aria-label="Acessos rápidos">
                    <?php if ($hasVideoUrl): ?>
                        <a class="card-extra-link" href="<?= h((string) $post['video_url']) ?>" target="_blank" rel="noopener noreferrer">Vídeo</a>
                    <?php endif; ?>
                    <?php if ($hasToolUrl): ?>
                        <a class="card-extra-link" href="<?= h((string) $post['tool_url']) ?>" target="_blank" rel="noopener noreferrer">Ferramenta</a>
                    <?php endif; ?>
                    <?php if ($hasDocument): ?>
                        <a class="card-extra-link" href="<?= h((string) $document['url']) ?>" target="_blank" rel="noopener noreferrer">Documento</a>
                    <?php endif; ?>
                    <?php if ($hasArticleUrl): ?>
                        <a class="card-extra-link" href="<?= h((string) $post['article_url']) ?>" target="_blank" rel="noopener noreferrer">Matéria</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="card-foot-meta">
                <span class="card-foot-status"><?= h($statusLabel) ?></span>
                <?php if ($cardDetailParts !== []): ?>
                    <span class="card-foot-types" title="<?= h(implode(', ', $cardDetailParts)) ?>"><?= h(implode(' · ', $cardDetailParts)) ?></span>
                <?php else: ?>
                    <span class="card-foot-types">Conteúdo</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
