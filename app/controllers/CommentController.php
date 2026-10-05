<?php
declare(strict_types=1);

final class CommentController
{
    public function __construct(private CommentModel $comments)
    {
    }

    public function create(): void
    {
        $user = require_auth();
        verify_csrf();
        $postId = (int) ($_POST['post_id'] ?? 0);
        $content = trim((string) ($_POST['content'] ?? ''));
        if ($content === '' || strlen($content) > 500) {
            set_flash('error', 'Comments must contain 1-500 characters.');
        } else {
            $this->comments->create($postId, (int) $user['id'], $content);
        }
        redirect(url(['page' => 'feed']) . ($postId > 0 ? '#comment-form-' . $postId : ''));
    }

    public function update(): void
    {
        $user = require_auth();
        verify_csrf();
        $id = (int) ($_POST['id'] ?? 0);
        $comment = $this->ownedComment($id, (int) $user['id']);
        $postId = (int) ($comment['post_id'] ?? 0);
        $content = trim((string) ($_POST['content'] ?? ''));
        if ($comment && $content !== '' && strlen($content) <= 500) {
            $this->comments->update($id, $content);
        }
        redirect(url(['page' => 'feed']) . ($postId > 0 ? '#post-' . $postId : ''));
    }

    public function delete(): void
    {
        $user = require_auth();
        verify_csrf();
        $comment = $this->comments->find((int) ($_POST['id'] ?? 0));
        $postId = (int) ($comment['post_id'] ?? 0);
        $isAdmin = ($user['role'] ?? 'user') === 'admin';
        if ($comment && ($isAdmin || (int) $comment['user_id'] === (int) $user['id'])) {
            $this->comments->delete((int) $comment['id']);
        } else {
            set_flash('error', 'You can only delete your own comments.');
        }
        redirect(url(['page' => 'feed']) . ($postId > 0 ? '#post-' . $postId : ''));
    }

    private function ownedComment(int $id, int $userId): ?array
    {
        $comment = $this->comments->find($id);
        if (!$comment || (int) $comment['user_id'] !== $userId) {
            set_flash('error', 'That comment is unavailable.');
            return null;
        }
        return $comment;
    }
}