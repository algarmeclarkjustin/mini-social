<?php
declare(strict_types=1);

final class CommentModel
{
    public function __construct(private mysqli $db)
    {
    }

    public function forPost(int $postId): array
    {
        $statement = $this->db->prepare('SELECT c.*, u.username, u.full_name, u.profile_image FROM comments c JOIN users u ON u.id = c.user_id WHERE c.post_id = ? ORDER BY c.created_at ASC');
        $statement->bind_param('i', $postId);
        $statement->execute();
        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create(int $postId, int $userId, string $content): void
    {
        $statement = $this->db->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
        $statement->bind_param('iis', $postId, $userId, $content);
        $statement->execute();
    }

    public function update(int $id, string $content): void
    {
        $statement = $this->db->prepare('UPDATE comments SET content = ? WHERE id = ?');
        $statement->bind_param('si', $content, $id);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM comments WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM comments WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
        return $statement->get_result()->fetch_assoc() ?: null;
    }
}