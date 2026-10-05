<?php
declare(strict_types=1);

final class AdminController
{
    public function __construct(private UserModel $users, private PostModel $posts)
    {
    }

    public function index(): void
    {
        require_admin();
        render('admin/index', [
            'title' => 'Admin dashboard',
            'userCount' => $this->users->countAll(),
            'postCount' => $this->posts->countAll(),
            'users' => $this->users->adminList(),
            'posts' => $this->posts->adminRecent(),
        ]);
    }

    public function deletePost(): void
    {
        require_admin();
        verify_csrf();
        $post = $this->posts->find((int) ($_POST['id'] ?? 0));
        if ($post) {
            $this->posts->delete((int) $post['id']);
            set_flash('success', 'The post has been removed.');
        }
        redirect(url(['page' => 'admin']));
    }
}