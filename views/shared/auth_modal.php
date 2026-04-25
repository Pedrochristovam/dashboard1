<div class="post-modal auth-modal <?= $isAuthModalOpen ? 'visible' : '' ?>" id="auth-modal" aria-hidden="<?= $isAuthModalOpen ? 'false' : 'true' ?>">
    <div class="post-modal-card panel auth-modal-card" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
        <div class="post-modal-body">
            <div class="post-modal-top">
                <h2 id="auth-modal-title">Entrar no hub</h2>
                <button class="post-modal-close" id="auth-modal-close" type="button" aria-label="Fechar">✕</button>
            </div>

            <?php if ($authErrors !== []): ?>
                <div class="status error"><?= h(implode(' ', $authErrors)) ?></div>
            <?php endif; ?>

            <p class="muted auth-modal-copy">Use seu nome, departamento e perfil para personalizar a navegação e liberar as áreas do gestor quando necessário.</p>

            <form method="post" class="auth-modal-form">
                <input type="hidden" name="dashboard_context" value="auth">
                <input type="hidden" name="action" value="login">
                <div class="form-grid">
                    <div class="field full">
                        <label for="auth_name">Nome</label>
                        <input id="auth_name" name="name" type="text" placeholder="Seu nome" value="<?= h((string) $authFormData['name']) ?>">
                    </div>
                    <div class="field">
                        <label for="auth_department">Departamento</label>
                        <select id="auth_department" name="department">
                            <?php foreach ($departmentOptions as $value => $label): ?>
                                <option value="<?= h($value) ?>" <?= (string) $authFormData['department'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="auth_role">Perfil</label>
                        <select id="auth_role" name="role">
                            <?php foreach ($roleOptions as $value => $label): ?>
                                <option value="<?= h($value) ?>" <?= (string) $authFormData['role'] === $value ? 'selected' : '' ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn-primary" type="submit">Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
