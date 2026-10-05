<?php
declare(strict_types=1);

final class LikeModel
{
    public function __construct(private mysqli $db)
    {
    }

    public function toggle(int $postId, int $userId): void
    {
        $statement = $this->db->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
        $statement->bind_param('ii', $postId, $userId);
        $statement->execute();
        if ($statement->affected_rows === 0) {
            $statement = $this->db->prepare('INSERT INTO likes (post_id, user_id) VALUES (?, ?)');
            $statement->bind_param('ii', $postId, $userId);
            $statement->execute();
        }
    }
}