(() => {
    const publisherPanel = document.getElementById('publisher-panel');
    const openPublisherBtn = document.getElementById('open-publisher-btn');
    const closePublisherButtons = document.querySelectorAll('[data-close-publisher]');
    const toolNameInput = document.getElementById('tool_name');
    const videoUrlInput = document.getElementById('video_url');
    const previewCard = document.getElementById('preview-card');
    const previewThumb = document.getElementById('preview-thumb');
    const previewTitle = document.getElementById('preview-title');
    const previewProvider = document.getElementById('preview-provider');

    const postModal = document.getElementById('post-modal');
    const postModalCategory = document.getElementById('post-modal-category');
    const postModalTitle = document.getElementById('post-modal-title');
    const postModalAuthor = document.getElementById('post-modal-author');
    const postModalDepartment = document.getElementById('post-modal-department');
    const postModalStatus = document.getElementById('post-modal-status');
    const postModalDate = document.getElementById('post-modal-date');
    const postModalPreview = document.getElementById('post-modal-preview');
    const postModalDescription = document.getElementById('post-modal-description');
    const postModalLearning = document.getElementById('post-modal-learning');
    const postModalPrompts = document.getElementById('post-modal-prompts');
    const postLearningCard = document.getElementById('post-learning-card');
    const postPromptCard = document.getElementById('post-prompt-card');
    const postModalVideo = document.getElementById('post-modal-video');
    const postModalTool = document.getElementById('post-modal-tool');
    const postModalArticle = document.getElementById('post-modal-article');
    const postModalPlayer = document.getElementById('post-modal-player');
    const postModalIframe = document.getElementById('post-modal-iframe');
    const postDocument = document.getElementById('post-document');
    const postDocumentName = document.getElementById('post-document-name');
    const postDocumentLink = document.getElementById('post-document-link');
    const postGallery = document.getElementById('post-gallery');
    const postGalleryImage = document.getElementById('post-gallery-image');
    const postGalleryDots = document.getElementById('post-gallery-dots');
    const postGalleryPrev = document.getElementById('post-gallery-prev');
    const postGalleryNext = document.getElementById('post-gallery-next');
    const postModalClose = document.getElementById('post-modal-close');

    let previewTimer = null;
    let galleryPhotos = [];
    let galleryIndex = 0;

    function setPublisherOpen(isOpen) {
        if (!publisherPanel || !openPublisherBtn) {
            return;
        }

        publisherPanel.classList.toggle('is-open', isOpen);
        publisherPanel.dataset.open = isOpen ? 'true' : 'false';
        openPublisherBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        openPublisherBtn.textContent = isOpen ? 'Fechar publicação' : 'Nova publicação';

        if (isOpen && toolNameInput) {
            window.setTimeout(() => toolNameInput.focus(), 80);
        }
    }

    function hidePreview() {
        if (!previewCard || !previewThumb || !previewTitle || !previewProvider) {
            return;
        }

        previewCard.classList.remove('visible');
        previewThumb.src = '';
        previewTitle.textContent = 'Aguardando análise do link...';
        previewProvider.textContent = '';
    }

    async function loadPreview(url) {
        try {
            const response = await fetch(`${window.location.pathname}?preview=1&url=${encodeURIComponent(url)}`);
            if (!response.ok) {
                hidePreview();
                return;
            }

            const data = await response.json();
            if (!data.is_youtube) {
                hidePreview();
                return;
            }

            previewThumb.src = data.thumbnail || '';
            previewTitle.textContent = data.title || 'Vídeo do YouTube detectado';
            previewProvider.textContent = data.provider || 'YouTube';
            previewCard.classList.add('visible');
        } catch (_error) {
            hidePreview();
        }
    }

    function buildYouTubeEmbedUrl(videoId) {
        const params = new URLSearchParams({
            rel: '0',
            modestbranding: '1',
            playsinline: '1',
        });

        if (window.location.protocol === 'http:' || window.location.protocol === 'https:') {
            params.set('origin', window.location.origin);
        }

        return `https://www.youtube-nocookie.com/embed/${videoId}?${params.toString()}`;
    }

    function parseCardPhotos(card) {
        try {
            const photos = JSON.parse(card.dataset.photos || '[]');
            if (!Array.isArray(photos)) {
                return [];
            }

            return photos.filter((photo) => photo && typeof photo.url === 'string' && photo.url.trim() !== '');
        } catch (_error) {
            return [];
        }
    }

    function renderGalleryDots(title) {
        if (!postGalleryDots) {
            return;
        }

        postGalleryDots.replaceChildren();

        galleryPhotos.forEach((photo, index) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'post-gallery-dot';
            dot.setAttribute('aria-label', `Abrir foto ${index + 1} de ${galleryPhotos.length}`);
            dot.setAttribute('aria-pressed', index === galleryIndex ? 'true' : 'false');
            if (index === galleryIndex) {
                dot.classList.add('is-active');
            }

            dot.addEventListener('click', () => {
                galleryIndex = index;
                renderGallery(title);
            });

            postGalleryDots.appendChild(dot);
        });
    }

    function renderGallery(title = '') {
        if (!postGallery || !postGalleryImage || !postGalleryPrev || !postGalleryNext) {
            return;
        }

        if (galleryPhotos.length === 0) {
            postGallery.classList.remove('visible');
            postGalleryImage.src = '';
            postGalleryImage.alt = 'Imagem da publicacao';
            if (postGalleryDots) {
                postGalleryDots.replaceChildren();
            }
            return;
        }

        const currentPhoto = galleryPhotos[galleryIndex];
        postGallery.classList.add('visible');
        postGalleryImage.src = currentPhoto.url;
        postGalleryImage.alt = title ? `${title} - foto ${galleryIndex + 1}` : `Foto ${galleryIndex + 1}`;
        postGalleryPrev.disabled = galleryPhotos.length < 2;
        postGalleryNext.disabled = galleryPhotos.length < 2;
        renderGalleryDots(title);
    }

    function setGalleryPhotos(photos, title = '') {
        galleryPhotos = photos;
        galleryIndex = 0;
        renderGallery(title);
    }

    function moveGallery(step, title = '') {
        if (galleryPhotos.length < 2) {
            return;
        }

        galleryIndex = (galleryIndex + step + galleryPhotos.length) % galleryPhotos.length;
        renderGallery(title);
    }

    function openPostModal(card) {
        if (!postModal) {
            return;
        }

        postModalCategory.textContent = card.dataset.category || '';
        postModalTitle.textContent = card.dataset.title || '';
        postModalAuthor.textContent = card.dataset.author || '';
        postModalDepartment.textContent = card.dataset.department || '';
        postModalStatus.textContent = card.dataset.status || '';
        postModalDate.textContent = card.dataset.date || '';
        postModalPreview.textContent = card.dataset.preview || '';
        postModalDescription.textContent = card.dataset.description || '';
        setGalleryPhotos(parseCardPhotos(card), card.dataset.title || '');
        if (postModalLearning && postLearningCard) {
            postModalLearning.textContent = card.dataset.learningTips || '';
            postLearningCard.style.display = card.dataset.learningTips ? 'block' : 'none';
        }
        if (postModalPrompts && postPromptCard) {
            postModalPrompts.textContent = card.dataset.promptsUsed || '';
            postPromptCard.style.display = card.dataset.promptsUsed ? 'block' : 'none';
        }

        if (card.dataset.youtubeId) {
            postModalIframe.src = '';
            postModalIframe.src = buildYouTubeEmbedUrl(card.dataset.youtubeId);
            postModalPlayer.classList.add('visible');
        } else {
            postModalIframe.src = '';
            postModalPlayer.classList.remove('visible');
        }

        if (card.dataset.videoUrl) {
            postModalVideo.href = card.dataset.videoUrl;
            postModalVideo.style.display = 'inline-flex';
            window.AppUi.trackEvent('posts', 'video_link_visible', {
                targetType: 'post',
                targetId: card.dataset.title || '',
            });
        } else {
            postModalVideo.style.display = 'none';
        }

        if (card.dataset.toolUrl) {
            postModalTool.href = card.dataset.toolUrl;
            postModalTool.style.display = 'inline-flex';
        } else {
            postModalTool.style.display = 'none';
        }

        if (postModalArticle) {
            if (card.dataset.articleUrl) {
                postModalArticle.href = card.dataset.articleUrl;
                postModalArticle.style.display = 'inline-flex';
            } else {
                postModalArticle.style.display = 'none';
            }
        }

        if (postDocument && postDocumentName && postDocumentLink) {
            if (card.dataset.documentUrl) {
                postDocument.classList.add('visible');
                postDocumentName.textContent = card.dataset.documentName || 'Documento complementar';
                postDocumentLink.href = card.dataset.documentUrl;
            } else {
                postDocument.classList.remove('visible');
                postDocumentName.textContent = '';
                postDocumentLink.removeAttribute('href');
            }
        }

        window.AppUi.trackEvent('posts', 'modal_open', {
            targetType: 'post',
            targetId: card.dataset.title || '',
            meta: {
                title: card.dataset.title || '',
                category: card.dataset.category || '',
                department: card.dataset.department || '',
            },
        });
        window.AppUi.setModalState(postModal, true);
    }

    function closePostModal() {
        if (!postModal) {
            return;
        }

        window.AppUi.setModalState(postModal, false);
        postModalIframe.src = '';
        postModalPlayer.classList.remove('visible');
        galleryPhotos = [];
        galleryIndex = 0;
        renderGallery();
        if (postDocument) {
            postDocument.classList.remove('visible');
        }
    }

    if (openPublisherBtn) {
        openPublisherBtn.addEventListener('click', () => {
            setPublisherOpen(!publisherPanel.classList.contains('is-open'));
        });
    }

    closePublisherButtons.forEach((button) => {
        button.addEventListener('click', () => setPublisherOpen(false));
    });

    if (publisherPanel) {
        setPublisherOpen(publisherPanel.dataset.open === 'true');
    }

    if (videoUrlInput) {
        videoUrlInput.addEventListener('input', (event) => {
            const value = event.target.value.trim();
            window.clearTimeout(previewTimer);

            if (value === '') {
                hidePreview();
                return;
            }

            previewTimer = window.setTimeout(() => {
                loadPreview(value);
            }, 350);
        });

        if (videoUrlInput.value.trim() !== '') {
            loadPreview(videoUrlInput.value.trim());
        }
    }

    document.querySelectorAll('[data-card]').forEach((card) => {
        card.addEventListener('click', (event) => {
            if (event.target.closest('a, button, form, input, textarea, select')) {
                return;
            }

            openPostModal(card);
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openPostModal(card);
            }
        });
    });

    document.querySelectorAll('.card-open-trigger').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const card = event.currentTarget.closest('[data-card]');
            if (card) {
                openPostModal(card);
            }
        });
    });

    if (postModalClose) {
        postModalClose.addEventListener('click', closePostModal);
    }

    [
        [postModalVideo, 'video_click'],
        [postModalTool, 'tool_click'],
        [postModalArticle, 'article_click'],
        [postDocumentLink, 'document_open'],
    ].forEach(([element, eventType]) => {
        if (!element) {
            return;
        }

        element.addEventListener('click', () => {
            window.AppUi.trackEvent('posts', eventType, {
                targetType: 'post',
                targetId: postModalTitle ? postModalTitle.textContent : '',
                meta: {
                    title: postModalTitle ? postModalTitle.textContent : '',
                },
            });
        });
    });

    if (postGalleryPrev) {
        postGalleryPrev.addEventListener('click', () => moveGallery(-1, postModalTitle ? postModalTitle.textContent : ''));
    }

    if (postGalleryNext) {
        postGalleryNext.addEventListener('click', () => moveGallery(1, postModalTitle ? postModalTitle.textContent : ''));
    }

    window.AppUi.bindOverlayClose(postModal, closePostModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && postModal && postModal.classList.contains('visible')) {
            closePostModal();
            return;
        }

        if (postModal && postModal.classList.contains('visible')) {
            if (event.key === 'ArrowLeft') {
                moveGallery(-1, postModalTitle ? postModalTitle.textContent : '');
            }

            if (event.key === 'ArrowRight') {
                moveGallery(1, postModalTitle ? postModalTitle.textContent : '');
            }
        }
    });
})();
