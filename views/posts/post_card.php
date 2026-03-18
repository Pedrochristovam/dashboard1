<?php
$hasVideoUrl = trim((string) ($post['video_url'] ?? '')) !== '';
$hasToolUrl = trim((string) ($post['tool_url'] ?? '')) !== '';
$youtubeVideoId = $hasVideoUrl ? parse_youtube_video_id((string) $post['video_url']) : null;
$previewText = (string) ($post['preview_title'] ?? '') !== ''
    ? (string) $post['preview_title']
    : ($hasVideoUrl && $hasToolUrl
        ? 'Vídeo e ferramenta compartilhados'
        : ($hasVideoUrl ? 'Vídeo compartilhado' : 'Ferramenta compartilhada'));
$cardTitle = truncate_text((string) $post['tool_name'], 54);
$cardSummary = truncate_text((string) $post['summary'], 112);
$cardPreview = truncate_text($previewText, 78);
$categoryLabel = (string) ($categoryLabels[$post['category']] ?? ucfirst((string) $post['category']));
?>
<article
    class="panel card"
    tabindex="0"
    data-card
    data-title="<?= h((string) $post['tool_name']) ?>"
    data-author="<?= h((string) $post['author']) ?>"
    data-date="<?= h(date('d/m/Y H:i', strtotime((string) $post['published_at']))) ?>"
    data-category="<?= h($categoryLabel) ?>"
    data-thumbnail="<?= h((string) $post['thumbnail']) ?>"
    data-preview="<?= h($previewText) ?>"
    data-description="<?= h((string) $post['description']) ?>"
    data-video-url="<?= h((string) ($post['video_url'] ?? '')) ?>"
    data-tool-url="<?= h((string) ($post['tool_url'] ?? '')) ?>"
    data-youtube-id="<?= h((string) ($youtubeVideoId ?? '')) ?>"
>
    <div class="card-media">
        <form class="card-delete-overlay" method="post" onsubmit="return confirm('Deseja excluir esta publicação?');">
            <input type="hidden" name="dashboard_context" value="posts">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
            <button class="delete-btn" type="submit" aria-label="Excluir publicação" title="Excluir publicação">🗑</button>
        </form>
        <img src="<?= h((string) $post['thumbnail']) ?>" alt="<?= h((string) $post['tool_name']) ?>">
        <span class="card-tag"><?= h($categoryLabel) ?></span>
    </div>
    <div class="card-body">
        <div class="card-topline">
            <span class="card-author"><?= h((string) $post['author']) ?></span>
            <span class="card-date"><?= h(date('d/m/Y H:i', strtotime((string) $post['published_at']))) ?></span>
        </div>

        <h3><?= h($cardTitle) ?></h3>
        <p><?= h($cardSummary) ?></p>

        <div class="preview-inline <?= (string) ($post['preview_title'] ?? '') === '' ? 'is-empty' : '' ?>">
            <?= h($cardPreview) ?>
        </div>

        <div class="card-divider"></div>

        <div class="card-footer">
            <div class="card-links">
                <button class="btn-tertiary card-open-trigger" type="button">Ver mais</button>
                <?php if ($hasVideoUrl): ?>
                    <a class="btn-secondary" href="<?= h((string) $post['video_url']) ?>" target="_blank" rel="noopener noreferrer">Acessar vídeo</a>
                <?php endif; ?>
                <?php if ($hasToolUrl): ?>
                    <a class="btn-secondary" href="<?= h((string) $post['tool_url']) ?>" target="_blank" rel="noopener noreferrer">Acessar ferramenta</a>
                <?php endif; ?>
            </div>
            <div class="card-actions">
                <span class="muted"><?= $hasVideoUrl ? 'Conteúdo com vídeo' : 'Conteúdo com link externo' ?></span>
            </div>
        </div>
    </div>
</article>
