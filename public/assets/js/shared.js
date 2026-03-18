window.AppUi = (() => {
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

    return {
        setModalState,
        bindOverlayClose,
    };
})();
