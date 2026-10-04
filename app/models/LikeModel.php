<?php
declare(strict_types=1);

final class LikeModel
{
    public function __construct(private PDO $db)
    {
    }

    public function toggle(int $postId, int $userId): void
    {
        $statement = $this->db->prepare('DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id');
        $statement->execute(['post_id' => $postId, 'user_id' => $userId]);
        if ($statement->rowCount() === 0) {
            $this->db->prepare('INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)')->execute([
                'post_id' => $postId,
                'user_id' => $userId,
            ]);
        }
    }
}