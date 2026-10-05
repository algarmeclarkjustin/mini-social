CREATE DATABASE IF NOT EXISTS social_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE social_app;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(24) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(80) NOT NULL,
    bio VARCHAR(240) NOT NULL DEFAULT '',
    profile_image VARCHAR(255) NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @role_column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'role'
);
SET @role_migration = IF(
    @role_column_exists = 0,
    'ALTER TABLE users ADD COLUMN role ENUM(''user'', ''admin'') NOT NULL DEFAULT ''user''',
    'SELECT 1'
);
PREPARE ensure_user_role FROM @role_migration;
EXECUTE ensure_user_role;
DEALLOCATE PREPARE ensure_user_role;

CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_posts_created_at (created_at),
    KEY idx_posts_user_id (user_id),
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    content VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_comments_post_created (post_id, created_at),
    KEY idx_comments_user_id (user_id),
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS likes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_likes_post_user (post_id, user_id),
    KEY idx_likes_user_id (user_id),
    CONSTRAINT fk_likes_post FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
    CONSTRAINT fk_likes_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo accounts use the password: commonplace123
INSERT IGNORE INTO users (id, username, email, password, full_name, bio, role) VALUES
(1, 'maya', 'maya@example.test', '$2y$10$QZ8jb4NWQgyGymle3XFlG.GX9HuNdsglrYRSik5B7i4J1koeW146a', 'Maya Santos', 'Collecting little moments, good books, and new places.', 'user'),
(2, 'eli', 'eli@example.test', '$2y$10$QZ8jb4NWQgyGymle3XFlG.GX9HuNdsglrYRSik5B7i4J1koeW146a', 'Eli Navarro', 'Usually outside. Always up for a long conversation.', 'user'),
(3, 'samira', 'samira@example.test', '$2y$10$QZ8jb4NWQgyGymle3XFlG.GX9HuNdsglrYRSik5B7i4J1koeW146a', 'Samira Lim', 'Making things slowly and sharing them often.', 'user');

-- Admin demo account: username admin, password MiniAdmin2026!
INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@mini-social.local', '$2y$10$Djo/x/GAwkrDP4GfFvt7BuCwhdUEPQiwo/V/GjNklHcsVPRJstejK', 'Mini Social Admin', 'admin');

INSERT IGNORE INTO posts (id, user_id, content, created_at) VALUES
(1, 1, 'Took the long way home today and found a tiny bookshop tucked behind the market. Sometimes the best plans are the ones you didn’t make.', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(2, 2, 'A small reminder from this morning: you don’t have to turn every quiet moment into something productive. Coffee counts as a plan.', DATE_SUB(NOW(), INTERVAL 95 MINUTE)),
(3, 3, 'What’s a song you can listen to on repeat and never get tired of? I’m making a playlist for slow Sunday mornings.', DATE_SUB(NOW(), INTERVAL 22 MINUTE));

INSERT IGNORE INTO comments (id, post_id, user_id, content, created_at) VALUES
(1, 1, 2, 'Those are always the best finds. What did you pick up?', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 1, 1, 'A collection of short stories I’ve been meaning to read!', DATE_SUB(NOW(), INTERVAL 110 MINUTE)),
(3, 2, 3, 'This is the permission I needed today. Thank you.', DATE_SUB(NOW(), INTERVAL 70 MINUTE)),
(4, 3, 1, 'Anything by Sade belongs on a slow Sunday playlist.', DATE_SUB(NOW(), INTERVAL 10 MINUTE));

INSERT IGNORE INTO likes (post_id, user_id) VALUES
(1, 2), (1, 3), (2, 1), (2, 3), (3, 1), (3, 2);