<?php
declare(strict_types=1);

final class PostModel
{
    public function __construct(private PDO $db)
    {
    }

    public function feed(?string $query = null): array
    {
        $sql = 'SELECT p.*, u.username, u.full_name, u.profile_image, (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count, (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count FROM posts p JOIN users u ON u.id = p.user_id';
        $values = [];
        if ($query !== null && $query !== '') {
            $sql .= ' WHERE p.content LIKE :query';
            $values['query'] = '%' . $query . '%';
        }
        $sql .= ' ORDER BY p.created_at DESC LIMIT 60';
        $statement = $this->db->prepare($sql);
        $statement->execute($values);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM posts WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function forUser(int $userId): array
    {
        $statement = $this->db->prepare('SELECT p.*, (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count, (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count FROM posts p WHERE p.user_id = :user_id ORDER BY p.created_at DESC');
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll();
    }

    public function create(int $userId, string $content, ?string $image): void
    {
        $this->db->prepare('INSERT INTO posts (user_id, content, image) VALUES (:user_id, :content, :image)')->execute([
            'user_id' => $userId,
            'content' => $content,
            'image' => $image,
        ]);
    }

    public function update(int $id, string $content, ?string $image = null): void
    {
        $sql = 'UPDATE posts SET content = :content';
        $values = ['content' => $content, 'id' => $id];
        if ($image !== null) {
            $sql .= ', image = :image';
            $values['image'] = $image;
        }
        $sql .= ' WHERE id = :id';
        $this->db->prepare($sql)->execute($values);
    }

    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM posts WHERE id = :id')->execute(['id' => $id]);
    }

    public function likedBy(int $postId, int $userId): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM likes WHERE post_id = :post_id AND user_id = :user_id');
        $statement->execute(['post_id' => $postId, 'user_id' => $userId]);
        return (bool) $statement->fetchColumn();
    }
}