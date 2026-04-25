<div class="post-modal" id="post-modal" aria-hidden="true">
    <div class="post-modal-card panel" role="dialog" aria-modal="true" aria-labelledby="post-modal-title">
        <div class="post-modal-body">
            <div class="post-modal-top">
                <h2 id="post-modal-title"></h2>
                <button class="post-modal-close" id="post-modal-close" type="button" aria-label="Fechar">✕</button>
            </div>
            <div class="post-modal-meta">
                <span class="badge" id="post-modal-category"></span>
                <span id="post-modal-author"></span>
                <span id="post-modal-department"></span>
                <span id="post-modal-status"></span>
                <span id="post-modal-date"></span>
            </div>
            <div class="post-modal-player" id="post-modal-player">
                <iframe
                    id="post-modal-iframe"
                    src=""
                    title="Vídeo do YouTube"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
                    allowfullscreen
                ></iframe>
            </div>
            <div class="post-gallery" id="post-gallery">
                <div class="post-gallery-stage">
                    <button class="post-gallery-nav" id="post-gallery-prev" type="button" aria-label="Foto anterior">‹</button>
                    <img id="post-gallery-image" src="" alt="Imagem da publicação">
                    <button class="post-gallery-nav" id="post-gallery-next" type="button" aria-label="Proxima foto">›</button>
                </div>
                <div class="post-gallery-dots" id="post-gallery-dots" aria-label="Selecionar foto do carrossel"></div>
            </div>
            <div class="post-modal-preview" id="post-modal-preview"></div>
            <p class="post-modal-description" id="post-modal-description"></p>
            <div class="post-insight-grid">
                <div class="post-insight-card" id="post-learning-card">
                    <strong>Dicas úteis e aprendizado</strong>
                    <p id="post-modal-learning"></p>
                </div>
                <div class="post-insight-card" id="post-prompt-card">
                    <strong>Prompts utilizados</strong>
                    <p id="post-modal-prompts"></p>
                </div>
            </div>
            <div class="post-document" id="post-document">
                <div>
                    <strong id="post-document-title">Documento complementar</strong>
                    <div class="muted" id="post-document-name"></div>
                </div>
                <a class="btn-tertiary" id="post-document-link" href="#" target="_blank" rel="noopener noreferrer">Abrir documento</a>
            </div>
            <div class="post-modal-actions">
                <a class="btn-secondary" id="post-modal-video" href="#" target="_blank" rel="noopener noreferrer">Acessar vídeo</a>
                <a class="btn-secondary" id="post-modal-tool" href="#" target="_blank" rel="noopener noreferrer">Acessar ferramenta</a>
                <a class="btn-secondary" id="post-modal-article" href="#" target="_blank" rel="noopener noreferrer">Acessar matéria</a>
            </div>
        </div>
    </div>
</div>
