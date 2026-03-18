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
    const postModalDate = document.getElementById('post-modal-date');
    const postModalPreview = document.getElementById('post-modal-preview');
    const postModalDescription = document.getElementById('post-modal-description');
    const postModalVideo = document.getElementById('post-modal-video');
    const postModalTool = document.getElementById('post-modal-tool');
    const postModalPlayer = document.getElementById('post-modal-player');
    const postModalIframe = document.getElementById('post-modal-iframe');
    const postModalClose = document.getElementById('post-modal-close');

    let previewTimer = null;

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

    function openPostModal(card) {
        if (!postModal) {
            return;
        }

        postModalCategory.textContent = card.dataset.category || '';
        postModalTitle.textContent = card.dataset.title || '';
        postModalAuthor.textContent = card.dataset.author || '';
        postModalDate.textContent = card.dataset.date || '';
        postModalPreview.textContent = card.dataset.preview || '';
        postModalDescription.textContent = card.dataset.description || '';

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
        } else {
            postModalVideo.style.display = 'none';
        }

        if (card.dataset.toolUrl) {
            postModalTool.href = card.dataset.toolUrl;
            postModalTool.style.display = 'inline-flex';
        } else {
            postModalTool.style.display = 'none';
        }

        window.AppUi.setModalState(postModal, true);
    }

    function closePostModal() {
        if (!postModal) {
            return;
        }

        window.AppUi.setModalState(postModal, false);
        postModalIframe.src = '';
        postModalPlayer.classList.remove('visible');
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

    window.AppUi.bindOverlayClose(postModal, closePostModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && postModal && postModal.classList.contains('visible')) {
            closePostModal();
        }
    });
})();
