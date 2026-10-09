<section class="admin-page">
    <div class="section-heading">
        <div><span class="eyebrow">MVXB ChatSpace</span>
            <h1>Admin dashboard</h1>
        </div><span class="admin-badge"><i class="bi bi-shield-check"></i> Administrator</span>
    </div>
    <div class="admin-stats">
        <article class="admin-stat"><span>Registered members</span><strong><?= (int) $userCount ?></strong><i class="bi bi-people"></i></article>
        <article class="admin-stat"><span>Community posts</span><strong><?= (int) $postCount ?></strong><i class="bi bi-chat-square-text"></i></article>
    </div>
    <section class="admin-section">
        <div class="admin-section-heading">
            <div><span class="eyebrow">Community</span>
                <h2>Members</h2>
            </div><span><?= count($users) ?> shown</span>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $member): ?><tr>
                            <td><?= e($member['full_name']) ?></td>
                            <td>@<?= e($member['username']) ?></td>
                            <td><?= e($member['email']) ?></td>
                            <td><span class="role-pill <?= $member['role'] === 'admin' ? 'role-admin' : '' ?>"><?= e(ucfirst($member['role'])) ?></span></td>
                        </tr><?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="admin-section">
        <div class="admin-section-heading">
            <div><span class="eyebrow">Moderation</span>
                <h2>Recent posts</h2>
            </div><span>Latest 20</span>
        </div>
        <div class="admin-post-list">
            <?php foreach ($posts as $post): ?><article class="admin-post">
                    <div class="admin-post-copy"><strong><?= e($post['full_name']) ?> <span>@<?= e($post['username']) ?></span></strong>
                        <p><?= nl2br(e($post['content'])) ?></p><small><?= e(date('M j, Y · g:i a', strtotime($post['created_at']))) ?></small>
                    </div>
                    <form action="<?= e(url(['page' => 'admin-post-delete'])) ?>" method="post" onsubmit="return confirm('Remove this post?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $post['id'] ?>"><button class="admin-delete" type="submit" aria-label="Remove post"><i class="bi bi-trash3"></i> Remove</button></form>
                </article><?php endforeach; ?>
            <?php if (!$posts): ?><p class="admin-empty">No community posts yet.</p><?php endif; ?>
        </div>
    </section>
</section>