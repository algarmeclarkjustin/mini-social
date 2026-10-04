<?php
declare(strict_types=1);

final class UserModel
{
    public function __construct(private PDO $db)
    {
    }

    public function findByUsername(string $username): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        return $statement->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT id, username, full_name, bio, profile_image, created_at FROM users WHERE id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function create(string $username, string $email, string $passwordHash, string $fullName): int
    {
        $statement = $this->db->prepare('INSERT INTO users (username, email, password, full_name) VALUES (:username, :email, :password, :full_name)');
        $statement->execute([
            'username' => $username,
            'email' => $email,
            'password' => $passwordHash,
            'full_name' => $fullName,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $fullName, string $bio, ?string $profileImage): void
    {
        $sql = 'UPDATE users SET full_name = :full_name, bio = :bio';
        if ($profileImage !== null) {
            $sql .= ', profile_image = :profile_image';
        }
        $sql .= ' WHERE id = :id';
        $values = ['full_name' => $fullName, 'bio' => $bio, 'id' => $id];
        if ($profileImage !== null) {
            $values['profile_image'] = $profileImage;
        }
        $this->db->prepare($sql)->execute($values);
    }

    public function search(string $query): array
    {
        $statement = $this->db->prepare('SELECT id, username, full_name, bio, profile_image FROM users WHERE username LIKE :query OR full_name LIKE :query ORDER BY full_name LIMIT 30');
        $statement->execute(['query' => '%' . $query . '%']);
        return $statement->fetchAll();
    }
}