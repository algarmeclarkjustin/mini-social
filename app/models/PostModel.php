<?php
declare(strict_types=1);

final class PostModel
{
    public function __construct(private mysqli $db)
    {
    }

    public function feed(?string $query = null): array
    {
        $sql = 'SELECT p.*, u.username, u.full_name, u.profile_image, (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count, (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count FROM posts p JOIN users u ON u.id = p.user_id';
        $statement = $this->db->prepare($sql . ($query !== null && $query !== '' ? ' WHERE p.content LIKE ?' : '') . ' ORDER BY p.created_at DESC LIMIT 60');
        if ($query !== null && $query !== '') {
            $wildcardQuery = '%' . $query . '%';
            $statement->bind_param('s', $wildcardQuery);
        }
        $statement->execute();
        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM posts WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
        return $statement->get_result()->fetch_assoc() ?: null;
    }

    public function forUser(int $userId): array
    {
        $statement = $this->db->prepare('SELECT p.*, (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count, (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count FROM posts p WHERE p.user_id = ? ORDER BY p.created_at DESC');
        $statement->bind_param('i', $userId);
        $statement->execute();
        return $statement->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create(int $userId, string $content, ?string $image): void
    {
        $statement = $this->db->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        $statement->bind_param('iss', $userId, $content, $image);
        $statement->execute();
    }

    public function update(int $id, string $content, ?string $image = null): void
    {
        $sql = 'UPDATE posts SET content = ?';
        $statement = null;
        if ($image !== null) {
            $sql .= ', image = ?';
        }
        $sql .= ' WHERE id = ?';
        $statement = $this->db->prepare($sql);
        if ($image !== null) {
            $statement->bind_param('ssi', $content, $image, $id);
        } else {
            $statement->bind_param('si', $content, $id);
        }
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM posts WHERE id = ?');
        $statement->bind_param('i', $id);
        $statement->execute();
    }

    public function likedBy(int $postId, int $userId): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?');
        $statement->bind_param('ii', $postId, $userId);
        $statement->execute();
        return (bool) $statement->get_result()->fetch_row();
    }
}