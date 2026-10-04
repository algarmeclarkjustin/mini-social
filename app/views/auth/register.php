<section class="auth-layout">
    <div class="auth-story">
        <div class="story-note"><span class="eyebrow">Your space, your pace</span><span class="story-spark"><i class="bi bi-sun"></i></span></div>
        <h1>Start with<br>a <em>hello.</em></h1>
        <p>Bring your whole self. Find your people. Keep the good conversations going.</p>
        <div class="story-foot"><span class="story-line"></span><span>Small circles. Real connection.</span></div>
    </div>
    <div class="auth-panel">
        <div class="auth-heading"><span class="eyebrow">Join the conversation</span><h2>Make yourself at home.</h2><p>It only takes a moment to get started.</p></div>
        <form class="form-stack" action="<?= e(url(['page' => 'register'])) ?>" method="post">
            <?= csrf_field() ?>
            <label>Your name<input name="full_name" type="text" maxlength="80" autocomplete="name" required></label>
            <label>Username<input name="username" type="text" minlength="3" maxlength="24" pattern="[A-Za-z0-9_]+" autocomplete="username" required><small>Letters, numbers, and underscores.</small></label>
            <label>Email<input name="email" type="email" autocomplete="email" required></label>
            <label>Password<input name="password" type="password" minlength="8" autocomplete="new-password" required><small>At least 8 characters.</small></label>
            <button class="button button-primary button-wide" type="submit">Create account <i class="bi bi-arrow-right"></i></button>
        </form>
        <p class="auth-switch">Already a member? <a href="<?= e(url(['page' => 'login'])) ?>">Sign in</a></p>
    </div>
</section>