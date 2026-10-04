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
        redirect(url(['page' => 'feed']));
    }

    public function update(): void
    {
        $user = require_auth();
        verify_csrf();
        $id = (int) ($_POST['id'] ?? 0);
        $comment = $this->ownedComment($id, (int) $user['id']);
        $content = trim((string) ($_POST['content'] ?? ''));
        if ($comment && $content !== '' && strlen($content) <= 500) {
            $this->comments->update($id, $content);
        }
        redirect(url(['page' => 'feed']));
    }

    public function delete(): void
    {
        $user = require_auth();
        verify_csrf();
        $comment = $this->ownedComment((int) ($_POST['id'] ?? 0), (int) $user['id']);
        if ($comment) {
            $this->comments->delete((int) $comment['id']);
        }
        redirect(url(['page' => 'feed']));
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