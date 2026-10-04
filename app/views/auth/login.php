<section class="auth-layout">
    <div class="auth-story">
        <div class="story-note"><span class="eyebrow">A little more human</span><span class="story-spark"><i class="bi bi-stars"></i></span></div>
        <h1>Good things<br>happen <em>in company.</em></h1>
        <p>A thoughtful corner of the internet for the people, ideas, and small moments worth keeping.</p>
        <div class="story-foot"><span class="story-line"></span><span>Make room for each other.</span></div>
    </div>
    <div class="auth-panel">
        <div class="auth-heading"><span class="eyebrow">Welcome back</span><h2>Come on in.</h2><p>Your people have been here.</p></div>
        <form class="form-stack" action="<?= e(url(['page' => 'login'])) ?>" method="post">
            <?= csrf_field() ?>
            <label>Username<input name="username" type="text" autocomplete="username" required autofocus></label>
            <label>Password<input name="password" type="password" autocomplete="current-password" required></label>
            <button class="button button-primary button-wide" type="submit">Sign in <i class="bi bi-arrow-right"></i></button>
        </form>
        <p class="auth-switch">New around here? <a href="<?= e(url(['page' => 'register'])) ?>">Make an account</a></p>
    </div>
</section>