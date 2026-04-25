<section
    id="publisher-panel"
    class="publisher-shell <?= $isPublisherOpen ? 'is-open' : '' ?>"
    data-open="<?= $isPublisherOpen ? 'true' : 'false' ?>"
>
    <div class="panel form-panel form-panel--composer">
        <div class="panel-header panel-header--composer">
            <div>
                <p class="panel-kicker">Novo conteúdo</p>
                <h2>Nova publicação</h2>
                <p class="panel-lead">Compartilhe uma ferramenta, material, vídeo ou o prompt que deu certo — tudo em um fluxo leve e opcional passo a passo.</p>
            </div>
            <div class="panel-header-actions">
                <span class="badge badge--soft">Colaborativo</span>
                <button class="close-panel-btn" type="button" data-close-publisher aria-label="Fechar painel">✕</button>
            </div>
        </div>

        <?php if ($errors !== []): ?>
            <div class="status error"><?= h(implode(' ', $errors)) ?></div>
        <?php endif; ?>

        <form class="composer-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="dashboard_context" value="posts">
            <input type="hidden" name="action" value="<?= $editingPost !== null ? 'update' : 'create' ?>">
            <input type="hidden" name="post_id" value="<?= h((string) $formData['post_id']) ?>">
            <input type="hidden" name="status" value="published">
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

                <div class="field full field-prompt-optional">
                    <label for="prompts_used">
                        Prompt usado para tais resultados
                        <span class="field-optional-badge">opcional</span>
                    </label>
                    <textarea
                        id="prompts_used"
                        name="prompts_used"
                        rows="4"
                        class="input-prompt"
                        placeholder="Se quiser, compartilhe o prompt (ou atalhos) que geraram bons resultados — ajuda o time a reproduzir a mesma qualidade."
                    ><?= h($formData['prompts_used']) ?></textarea>
                    <small class="field-help">Deixe em branco se preferir manter o processo privado. O conteúdo aparece no detalhe da publicação.</small>
                </div>

                <div class="field">
                    <label for="support_document">Documento complementar</label>
                    <input id="support_document" name="support_document" type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt">
                    <small class="field-help">Opcional. Material de apoio com até 10 MB.</small>
                </div>

                <div class="field">
                    <label for="gallery_photos">Fotos da publicação</label>
                    <input id="gallery_photos" name="gallery_photos[]" type="file" accept="image/*" multiple>
                    <small class="field-help">Opcional. Envie imagens para mostrar exemplos práticos.</small>
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
                <button class="btn-primary" type="submit"><?= $editingPost !== null ? 'Salvar publicação' : 'Publicar ferramenta' ?></button>
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
    </div>
</section>
