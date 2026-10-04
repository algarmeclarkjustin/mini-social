<?php
declare(strict_types=1);

final class PostController
{
    public function __construct(private PostModel $posts)
    {
    }

    public function create(): void
    {
        $user = require_auth();
        verify_csrf();
        $content = trim((string) ($_POST['content'] ?? ''));
        if ($content === '' || strlen($content) > 2000) {
            set_flash('error', 'A post needs text and must be 2,000 characters or fewer.');
            redirect(url(['page' => 'feed']));
        }
        try {
            $image = upload_image('image', 'posts');
            $this->posts->create((int) $user['id'], $content, $image);
            set_flash('success', 'Your post is live.');
        } catch (RuntimeException $exception) {
            set_flash('error', $exception->getMessage());
        }
        redirect(url(['page' => 'feed']));
    }

    public function edit(): void
    {
        $user = require_auth();
        $post = $this->ownedPost((int) ($_GET['id'] ?? 0), (int) $user['id']);
        if (!$post) {
            redirect(url(['page' => 'feed']));
        }
        render('posts/edit', ['title' => 'Edit post', 'post' => $post]);
    }

    public function update(): void
    {
        $user = require_auth();
        verify_csrf();
        $id = (int) ($_POST['id'] ?? 0);
        $post = $this->ownedPost($id, (int) $user['id']);
        if (!$post) {
            redirect(url(['page' => 'feed']));
        }
        $content = trim((string) ($_POST['content'] ?? ''));
        if ($content === '' || strlen($content) > 2000) {
            set_flash('error', 'A post needs text and must be 2,000 characters or fewer.');
            redirect(url(['page' => 'post-edit', 'id' => $id]));
        }
        try {
            $this->posts->update($id, $content, upload_image('image', 'posts'));
            set_flash('success', 'Your post has been updated.');
        } catch (RuntimeException $exception) {
            set_flash('error', $exception->getMessage());
            redirect(url(['page' => 'post-edit', 'id' => $id]));
        }
        redirect(url(['page' => 'feed']));
    }

    public function delete(): void
    {
        $user = require_auth();
        verify_csrf();
        $post = $this->ownedPost((int) ($_POST['id'] ?? 0), (int) $user['id']);
        if ($post) {
            $this->posts->delete((int) $post['id']);
            set_flash('success', 'Your post has been deleted.');
        }
        redirect(url(['page' => 'feed']));
    }

    private function ownedPost(int $id, int $userId): ?array
    {
        $post = $this->posts->find($id);
        if (!$post || (int) $post['user_id'] !== $userId) {
            set_flash('error', 'That post is unavailable.');
            return null;
        }
        return $post;
    }
}