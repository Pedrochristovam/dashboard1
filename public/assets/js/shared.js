window.AppUi = (() => {
    const activeView = document.body.dataset.activeView || 'posts';
    const viewStartedAt = Date.now();
    let sentViewDuration = false;
    const authModal = document.getElementById('auth-modal');
    const authModalClose = document.getElementById('auth-modal-close');
    const authModalOpen = document.getElementById('open-auth-modal');
    const authNameInput = document.getElementById('auth_name');

    if (authModal && authModal.classList.contains('visible')) {
        document.body.classList.add('modal-open');
    }

    function setModalState(modal, isOpen) {
        if (!modal) {
            return;
        }

        modal.classList.toggle('visible', isOpen);
        modal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        document.body.classList.toggle('modal-open', isOpen);
    }

    function bindOverlayClose(modal, closeHandler) {
        if (!modal) {
            return;
        }

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeHandler();
            }
        });
    }

    function sendAnalyticsEvent(payload) {
        try {
            const url = `${window.location.pathname}?analytics=1`;
            const body = JSON.stringify(payload);

            if (navigator.sendBeacon) {
                const blob = new Blob([body], { type: 'application/json' });
                navigator.sendBeacon(url, blob);
                return;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body,
                keepalive: true,
            }).catch(() => {});
        } catch (_error) {
            // Ignore analytics transport errors in prototype mode.
        }
    }

    function trackEvent(module, type, payload = {}) {
        sendAnalyticsEvent({
            module,
            type,
            targetType: payload.targetType || null,
            targetId: payload.targetId || null,
            durationSeconds: payload.durationSeconds || null,
            meta: payload.meta || {},
        });
    }

    function flushViewDuration() {
        if (sentViewDuration) {
            return;
        }

        const durationSeconds = Math.max(1, Math.round((Date.now() - viewStartedAt) / 1000));
        sentViewDuration = true;
        trackEvent(activeView, 'view_duration', {
            durationSeconds,
        });
    }

    window.addEventListener('pagehide', flushViewDuration);
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            flushViewDuration();
        }
    });

    if (authModalOpen) {
        authModalOpen.addEventListener('click', () => {
            setModalState(authModal, true);
            if (authNameInput) {
                window.setTimeout(() => authNameInput.focus(), 80);
            }
        });
    }

    if (authModalClose) {
        authModalClose.addEventListener('click', () => {
            setModalState(authModal, false);
        });
    }

    bindOverlayClose(authModal, () => setModalState(authModal, false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && authModal && authModal.classList.contains('visible')) {
            setModalState(authModal, false);
        }
    });

    return {
        setModalState,
        bindOverlayClose,
        trackEvent,
    };
})();
