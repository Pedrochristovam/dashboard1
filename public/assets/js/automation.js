(() => {
    const automationPanel = document.getElementById('automation-panel');
    const openAutomationBtn = document.getElementById('open-automation-btn');
    const closeAutomationButtons = document.querySelectorAll('[data-close-automation]');
    const automationTitleInput = document.getElementById('automation_title');

    function setAutomationOpen(isOpen) {
        if (!automationPanel || !openAutomationBtn) {
            return;
        }

        automationPanel.classList.toggle('is-open', isOpen);
        automationPanel.dataset.open = isOpen ? 'true' : 'false';
        openAutomationBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        openAutomationBtn.textContent = isOpen ? 'Fechar pedido' : 'Novo pedido';

        if (isOpen && automationTitleInput) {
            window.setTimeout(() => automationTitleInput.focus(), 80);
        }
    }

    if (openAutomationBtn) {
        openAutomationBtn.addEventListener('click', () => {
            setAutomationOpen(!automationPanel.classList.contains('is-open'));
        });
    }

    closeAutomationButtons.forEach((button) => {
        button.addEventListener('click', () => setAutomationOpen(false));
    });

    if (automationPanel) {
        setAutomationOpen(automationPanel.dataset.open === 'true');
    }
})();
