<div class="app-auth-corner">
    <?php if ($loginSuccess): ?>
        <div class="app-auth-feedback status success">Sessão iniciada com sucesso.</div>
    <?php elseif ($logoutSuccess): ?>
        <div class="app-auth-feedback status success">Sessão encerrada com sucesso.</div>
    <?php endif; ?>

    <?php if ($isAuthenticated && $currentUser !== null): ?>
        <div class="app-auth-user">
            <div class="app-auth-user-copy">
                <strong><?= h((string) $currentUser['name']) ?></strong>
                <span><?= h((string) $currentUser['department_label']) ?> • <?= h((string) $currentUser['role_label']) ?></span>
            </div>
            <?php if ($isManager): ?>
                <a class="btn-secondary app-auth-manager-link" href="index.php?view=manager">Painel do gestor</a>
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="dashboard_context" value="auth">
                <input type="hidden" name="action" value="logout">
                <button class="btn-tertiary" type="submit">Sair</button>
            </form>
        </div>
    <?php else: ?>
        <button class="btn-primary app-auth-login-trigger" id="open-auth-modal" type="button">Login</button>
    <?php endif; ?>
</div>
