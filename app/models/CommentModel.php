<?php
declare(strict_types=1);

final class CommentModel
{
    public function __construct(private PDO $db)
    {
    }

    public function forPost(int $postId): array
    {
        $statement = $this->db->prepare('SELECT c.*, u.username, u.full_name, u.profile_image FROM comments c JOIN users u ON u.id = c.user_id WHERE c.post_id = :post_id ORDER BY c.created_at ASC');
        $statement->execute(['post_id' => $postId]);
        return $statement->fetchAll();
    }

    public function create(int $postId, int $userId, string $content): void
    {
        $this->db->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)')->execute([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
        ]);
    }

    public function update(int $id, string $content): void
    {
        $this->db->prepare('UPDATE comments SET content = :content WHERE id = :id')->execute(['content' => $content, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM comments WHERE id = :id')->execute(['id' => $id]);
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM comments WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }
}