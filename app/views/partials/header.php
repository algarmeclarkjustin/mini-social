<?php $flash = take_flash(); $viewer = current_user(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f7f3">
    <title><?= e($title ?? 'Commonplace') ?> · Commonplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,500;6..72,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/app.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(url(['page' => $viewer ? 'feed' : 'login'])) ?>" aria-label="Commonplace home">
        <span class="brand-mark"><i class="bi bi-asterisk"></i></span><span>commonplace</span>
    </a>
    <?php if ($viewer): ?>
        <form class="top-search" action="index.php" method="get">
            <input type="hidden" name="page" value="search-users">
            <i class="bi bi-search" aria-hidden="true"></i>
            <input type="search" name="q" placeholder="Find your people" aria-label="Search people">
            <kbd>/</kbd>
        </form>
        <nav class="top-actions" aria-label="Account navigation">
            <a class="icon-link" href="<?= e(url(['page' => 'feed'])) ?>" aria-label="Home" title="Home"><i class="bi bi-house-door"></i></a>
            <a class="viewer-link" href="<?= e(url(['page' => 'profile', 'username' => $viewer['username']])) ?>">
                <?php if (!empty($viewer['profile_image'])): ?><img class="avatar avatar-small" src="uploads/<?= e($viewer['profile_image']) ?>" alt="">
                <?php else: ?><span class="avatar avatar-small avatar-initial"><?= e(strtoupper(substr($viewer['full_name'] ?? $viewer['username'], 0, 1))) ?></span><?php endif; ?>
                <span><?= e($viewer['username']) ?></span>
            </a>
            <form action="<?= e(url(['page' => 'logout'])) ?>" method="post">
                <?= csrf_field() ?><button class="icon-link" type="submit" aria-label="Sign out" title="Sign out"><i class="bi bi-box-arrow-right"></i></button>
            </form>
        </nav>
    <?php endif; ?>
</header>
<?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status"><i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-circle' : 'bi-check-circle' ?>"></i><?= e($flash['message']) ?></div>
<?php endif; ?>
<main class="page-shell">