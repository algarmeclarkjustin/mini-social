</main>
<?php if (current_user()): ?>
<nav class="mobile-nav" aria-label="Mobile navigation">
    <a href="<?= e(url(['page' => 'feed'])) ?>" aria-label="Home"><i class="bi bi-house-door"></i></a>
    <a href="<?= e(url(['page' => 'search-users'])) ?>" aria-label="Find people"><i class="bi bi-search"></i></a>
    <a href="<?= e(url(['page' => 'profile', 'username' => current_user()['username']])) ?>" aria-label="Profile"><i class="bi bi-person"></i></a>
</nav>
<?php endif; ?>
</body>
</html>