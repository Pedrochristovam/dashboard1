<section
    id="publisher-panel"
    class="publisher-shell <?= $isPublisherOpen ? 'is-open' : '' ?>"
    data-open="<?= $isPublisherOpen ? 'true' : 'false' ?>"
>
    <div class="panel form-panel">
        <div class="panel-header">
            <div>
                <h2>Nova publicação</h2>
                <p>Cadastre uma publicação com vídeo do YouTube e ou link direto da ferramenta. Preencha pelo menos um dos links.</p>
            </div>
            <div class="panel-header-actions">
                <span class="badge">Colaborativo</span>
                <button class="close-panel-btn" type="button" data-close-publisher aria-label="Fechar painel">✕</button>
            </div>
        </div>

        <?php if ($errors !== []): ?>
            <div class="status error"><?= h(implode(' ', $errors)) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="dashboard_context" value="posts">
            <input type="hidden" name="action" value="create">
            <div class="form-grid">
                <div class="field">
                    <label for="tool_name">Nome da ferramenta</label>
                    <input id="tool_name" name="tool_name" type="text" placeholder="Ex: Midjourney" value="<?= h($formData['tool_name']) ?>">
                </div>

                <div class="field">
                    <label for="category">Categoria</label>
                    <select id="category" name="category">
                        <option value="automacao" <?= $formData['category'] === 'automacao' ? 'selected' : '' ?>>Automacao</option>
                        <option value="design" <?= $formData['category'] === 'design' ? 'selected' : '' ?>>Design</option>
                        <option value="texto" <?= $formData['category'] === 'texto' ? 'selected' : '' ?>>Texto</option>
                        <option value="dados" <?= $formData['category'] === 'dados' ? 'selected' : '' ?>>Dados</option>
                    </select>
                </div>

                <div class="field full">
                    <label for="video_url">Link do vídeo</label>
                    <input id="video_url" name="video_url" type="url" placeholder="https://www.youtube.com/watch?v=..." value="<?= h($formData['video_url']) ?>">
                </div>

                <div class="field full">
                    <label for="tool_url">Link da ferramenta</label>
                    <input id="tool_url" name="tool_url" type="url" placeholder="https://site-da-ferramenta.com" value="<?= h($formData['tool_url']) ?>">
                </div>

                <div class="field full">
                    <label for="description">Descrição detalhada</label>
                    <textarea id="description" name="description" placeholder="O que esta ferramenta faz?"><?= h($formData['description']) ?></textarea>
                </div>
            </div>

            <div class="preview-card" id="preview-card">
                <img class="preview-thumb" id="preview-thumb" src="" alt="Thumbnail do vídeo">
                <div>
                    <div class="preview-label">Preview detectado</div>
                    <div class="preview-title" id="preview-title">Aguardando análise do link...</div>
                    <div class="muted" id="preview-provider"></div>
                </div>
            </div>

            <div class="actions">
                <button class="btn-primary" type="submit">Publicar ferramenta</button>
            </div>
        </form>
    </div>

    <div class="side-panels">
        <div class="panel stats-panel">
            <div class="panel-header">
                <div>
                    <h2>Indicadores</h2>
                    <p>Visão geral do volume de conteúdo compartilhado no hub.</p>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat">
                    <strong><?= $featuredCount ?></strong>
                    <span class="muted">Publicações ativas</span>
                </div>
                <div class="stat">
                    <strong><?= $videoPostsCount ?></strong>
                    <span class="muted">Publicações com vídeo</span>
                </div>
                <div class="stat">
                    <strong><?= $automationContentCount ?></strong>
                    <span class="muted">Conteúdos de automação</span>
                </div>
                <div class="stat">
                    <strong><?= count($posts) ?></strong>
                    <span class="muted">Resultados exibidos</span>
                </div>
            </div>
        </div>

        <div class="panel stats-panel">
            <div class="panel-header">
                <div>
                    <h2>Boas práticas</h2>
                    <p>Mantenha o conteúdo útil, objetivo e fácil de localizar por categoria e busca.</p>
                </div>
            </div>
            <div class="tag-wrap">
                <span class="tag">Resumo objetivo</span>
                <span class="tag">Link confiável</span>
                <span class="tag">Categoria correta</span>
                <span class="tag">Relevância interna</span>
            </div>
        </div>
    </div>
</section>
