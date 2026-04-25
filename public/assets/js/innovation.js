(() => {
    const sectorModal = document.getElementById('sector-modal');
    const sectorModalClose = document.getElementById('sector-modal-close');
    const sectorModalTitle = document.getElementById('sector-modal-title');
    const sectorModalLevel = document.getElementById('sector-modal-level');
    const sectorModalUpdated = document.getElementById('sector-modal-updated');
    const sectorModalInitiatives = document.getElementById('sector-modal-initiatives');
    const sectorModalSummary = document.getElementById('sector-modal-summary');
    const sectorModalProgressBar = document.getElementById('sector-modal-progress-bar');
    const sectorModalProgressText = document.getElementById('sector-modal-progress-text');
    const sectorModalHighlights = document.getElementById('sector-modal-highlights');
    const innovationTopSector = document.getElementById('innovation-top-sector');
    const innovationTopSectorCopy = document.getElementById('innovation-top-sector-copy');
    const innovationAvgProgress = document.getElementById('innovation-avg-progress');
    const innovationAttentionCount = document.getElementById('innovation-attention-count');
    const innovationRankingList = document.getElementById('innovation-ranking-list');
    const innovationAttentionList = document.getElementById('innovation-attention-list');

    function openSectorModal(card) {
        if (!sectorModal) {
            return;
        }

        sectorModalTitle.textContent = card.dataset.name || '';
        sectorModalLevel.textContent = `Nível: ${card.dataset.level || ''}`;
        sectorModalUpdated.textContent = `Atualizado em: ${card.dataset.updated || ''}`;
        sectorModalInitiatives.textContent = `Iniciativas: ${card.dataset.initiatives || ''}`;
        sectorModalSummary.textContent = card.dataset.improvements || card.dataset.summary || '';
        sectorModalProgressBar.style.width = `${card.dataset.progress || 0}%`;
        sectorModalProgressText.textContent = `${card.dataset.progress || 0}% de evolução`;

        sectorModalHighlights.innerHTML = '';
        (card.dataset.highlights || '').split(' | ').filter(Boolean).forEach((item) => {
            const badge = document.createElement('span');
            badge.textContent = item;
            sectorModalHighlights.appendChild(badge);
        });

        window.AppUi.trackEvent('innovation', 'modal_open', {
            targetType: 'sector',
            targetId: card.dataset.sectorId || '',
            meta: {
                sector_label: card.dataset.name || '',
            },
        });
        window.AppUi.setModalState(sectorModal, true);
    }

    function closeSectorModal() {
        if (!sectorModal) {
            return;
        }

        window.AppUi.setModalState(sectorModal, false);
    }

    function clampProgress(value) {
        const numericValue = Number.parseInt(value, 10);
        if (Number.isNaN(numericValue)) {
            return 0;
        }

        return Math.min(100, Math.max(0, numericValue));
    }

    function innovationProgressFromDocuments(documentCount) {
        return Math.min(100, documentCount * 10);
    }

    function innovationLevelFromProgress(progress) {
        if (progress >= 70) {
            return { label: 'Avançado', badge: 'advanced' };
        }

        if (progress >= 35) {
            return { label: 'Intermediário', badge: 'intermediate' };
        }

        return { label: 'Iniciante', badge: 'starter' };
    }

    function updateSectorVisualState(card, progress) {
        const progressBar = card.querySelector('.progress-bar');
        const score = card.querySelector('.sector-score');
        const caption = card.querySelector('[data-sector-progress-caption]');
        const level = card.querySelector('[data-sector-level]');
        const levelState = innovationLevelFromProgress(progress);

        card.dataset.progress = String(progress);
        card.dataset.level = levelState.label;
        card.classList.toggle('is-empty', progress === 0);

        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }

        if (score) {
            score.textContent = `${progress}%`;
        }

        if (level) {
            level.textContent = levelState.label;
            level.classList.remove('advanced', 'intermediate', 'starter');
            level.classList.add(levelState.badge);
        }

        if (caption) {
            caption.textContent = progress === 0
                ? 'Aguardando lançamento de dados'
                : 'Progresso calculado automaticamente pelos documentos salvos';
        }

        if (sectorModal && sectorModal.classList.contains('visible') && sectorModalTitle && sectorModalTitle.textContent === (card.dataset.name || '')) {
            sectorModalLevel.textContent = `Nível: ${levelState.label}`;
            sectorModalProgressBar.style.width = `${progress}%`;
            sectorModalProgressText.textContent = `${progress}% de evolução`;
        }
    }

    function renderInnovationSidebar() {
        const sectorCards = Array.from(document.querySelectorAll('[data-sector-card]'));
        if (sectorCards.length === 0) {
            return;
        }

        const sectors = sectorCards.map((card) => ({
            id: card.dataset.sectorId || '',
            name: card.dataset.name || '',
            progress: clampProgress(card.dataset.progress || 0),
            initiatives: Number.parseInt(card.dataset.initiatives || '0', 10) || 0,
        }));

        const sortedSectors = [...sectors].sort((a, b) => {
            if (b.progress !== a.progress) {
                return b.progress - a.progress;
            }

            return b.initiatives - a.initiatives;
        });

        const avgProgress = Math.round(sectors.reduce((total, sector) => total + sector.progress, 0) / sectors.length);
        const topSector = sortedSectors[0] || null;
        const attentionSectors = sortedSectors.filter((sector) => sector.progress < 50);

        if (innovationTopSector) {
            innovationTopSector.textContent = topSector ? topSector.name : '-';
        }

        if (innovationTopSectorCopy) {
            innovationTopSectorCopy.textContent = topSector && topSector.progress > 0
                ? `Liderança atual com ${topSector.progress}% de evolução informada.`
                : 'Nenhum avanço informado ainda. Os documentos salvos por setor irão compor a evolução.';
        }

        if (innovationAvgProgress) {
            innovationAvgProgress.textContent = `${avgProgress}%`;
        }

        if (innovationAttentionCount) {
            innovationAttentionCount.textContent = String(attentionSectors.length);
        }

        if (innovationRankingList) {
            innovationRankingList.innerHTML = sortedSectors.map((sector, index) => `
                <div class="ranking-item">
                    <span class="ranking-position">#${index + 1}</span>
                    <div class="ranking-copy">
                        <strong>${sector.name}</strong>
                        <span class="muted">${sector.initiatives} iniciativas</span>
                    </div>
                    <span class="ranking-score">${sector.progress}%</span>
                </div>
            `).join('');
        }

        if (innovationAttentionList) {
            innovationAttentionList.innerHTML = attentionSectors.length > 0
                ? attentionSectors.map((sector) => `
                    <div class="attention-item">
                        <strong>${sector.name}</strong>
                        <span class="muted">${sector.progress}% de evolução</span>
                    </div>
                `).join('')
                : '<div class="attention-item"><strong>Nenhum setor em atenção</strong><span class="muted">Todos os percentuais estão acima do limite de atenção.</span></div>';
        }
    }

    function updateSectorDocumentHint(card) {
        const documentIdInput = card.querySelector('[data-document-id-input]');
        const actionInput = card.querySelector('[data-sector-action-input]');
        const hint = card.querySelector('[data-progress-hint]');
        const editorMode = card.querySelector('[data-editor-mode]');
        const deleteButton = card.querySelector('[data-delete-document]');
        const documentCount = card.querySelectorAll('[data-document-chip]').length;
        const currentProgress = innovationProgressFromDocuments(documentCount);
        const nextProgress = innovationProgressFromDocuments(documentCount + 1);
        const isEditing = documentIdInput && documentIdInput.value !== '';

        if (actionInput) {
            actionInput.value = 'save_sector';
        }

        updateSectorVisualState(card, currentProgress);
        card.dataset.initiatives = String(documentCount);

        if (hint) {
            hint.textContent = isEditing
                ? `Você está editando um documento existente. O progresso atual permanece em ${currentProgress}%.`
                : `Ao salvar um novo documento, o progresso deste setor irá para ${nextProgress}%.`;
        }

        if (editorMode) {
            editorMode.textContent = isEditing ? 'Editando documento existente' : 'Pronto para novo documento';
        }

        if (deleteButton) {
            deleteButton.hidden = !isEditing;
        }

        renderInnovationSidebar();
    }

    function setActiveDocumentChip(card, activeChip) {
        card.querySelectorAll('[data-document-chip]').forEach((chip) => {
            chip.classList.toggle('is-active', chip === activeChip);
        });
    }

    function resetSectorDocumentEditor(card) {
        const documentIdInput = card.querySelector('[data-document-id-input]');
        const textarea = card.querySelector('[data-improvements-input]');

        if (documentIdInput) {
            documentIdInput.value = '';
        }

        if (textarea) {
            textarea.value = '';
            textarea.focus();
        }

        setActiveDocumentChip(card, null);
        updateSectorDocumentHint(card);
    }

    function loadSectorDocumentIntoEditor(card, chip) {
        const documentIdInput = card.querySelector('[data-document-id-input]');
        const textarea = card.querySelector('[data-improvements-input]');

        if (documentIdInput) {
            documentIdInput.value = chip.dataset.documentId || '';
        }

        if (textarea) {
            textarea.value = chip.dataset.documentContent || '';
            textarea.focus();
        }

        setActiveDocumentChip(card, chip);
        updateSectorDocumentHint(card);
    }

    function updateSectorImprovements(card, value) {
        const improvements = value.trim();
        card.dataset.improvements = improvements;

        if (sectorModal && sectorModal.classList.contains('visible') && sectorModalTitle && sectorModalTitle.textContent === (card.dataset.name || '')) {
            sectorModalSummary.textContent = improvements || card.dataset.summary || '';
        }
    }

    document.querySelectorAll('[data-sector-card]').forEach((card) => {
        card.addEventListener('click', (event) => {
            if (event.target.closest('input, textarea, select, button, label, form')) {
                return;
            }

            openSectorModal(card);
        });

        const improvementsInput = card.querySelector('[data-improvements-input]');
        if (improvementsInput) {
            improvementsInput.addEventListener('input', (event) => {
                updateSectorImprovements(card, event.target.value);
            });
        }

        const newDocumentButton = card.querySelector('.document-new-trigger');
        if (newDocumentButton) {
            newDocumentButton.addEventListener('click', () => resetSectorDocumentEditor(card));
        }

        card.querySelectorAll('[data-document-chip]').forEach((chip) => {
            chip.addEventListener('click', () => loadSectorDocumentIntoEditor(card, chip));
        });

        const form = card.querySelector('.sector-form');
        if (form) {
            form.addEventListener('submit', (event) => {
                const actionInput = card.querySelector('[data-sector-action-input]');
                const textarea = card.querySelector('[data-improvements-input]');

                if (actionInput && actionInput.value === 'delete_document') {
                    if (!window.confirm('Deseja excluir este documento?')) {
                        event.preventDefault();
                    }
                    return;
                }

                if (textarea && textarea.value.trim() === '') {
                    event.preventDefault();
                    textarea.focus();
                }
            });
        }

        const deleteButton = card.querySelector('[data-delete-document]');
        if (deleteButton) {
            deleteButton.addEventListener('click', () => {
                const actionInput = card.querySelector('[data-sector-action-input]');
                if (actionInput) {
                    actionInput.value = 'delete_document';
                }
            });
        }

        const deleteSectorButton = card.querySelector('[data-delete-sector]');
        if (deleteSectorButton) {
            deleteSectorButton.addEventListener('click', () => {
                if (!window.confirm('Deseja excluir este setor e todos os documentos associados?')) {
                    return;
                }

                const actionInput = card.querySelector('[data-sector-action-input]');
                const form = card.querySelector('.sector-form');
                if (actionInput && form) {
                    actionInput.value = 'delete_sector';
                    form.submit();
                }
            });
        }

        const saveButton = card.querySelector('.sector-save-btn');
        if (saveButton) {
            saveButton.addEventListener('click', () => {
                const actionInput = card.querySelector('[data-sector-action-input]');
                if (actionInput) {
                    actionInput.value = 'save_sector';
                }
            });
        }

        updateSectorDocumentHint(card);
    });

    document.querySelectorAll('.sector-detail-trigger').forEach((button) => {
        button.addEventListener('click', (event) => {
            const card = event.currentTarget.closest('[data-sector-card]');
            if (card) {
                openSectorModal(card);
            }
        });
    });

    if (sectorModalClose) {
        sectorModalClose.addEventListener('click', closeSectorModal);
    }

    window.AppUi.bindOverlayClose(sectorModal, closeSectorModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sectorModal && sectorModal.classList.contains('visible')) {
            closeSectorModal();
        }
    });
})();
