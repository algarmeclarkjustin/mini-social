<?php
declare(strict_types=1);

final class LikeController
{
    public function __construct(private LikeModel $likes, private PostModel $posts)
    {
    }

    public function toggle(): void
    {
        $user = require_auth();
        verify_csrf();
        $postId = (int) ($_POST['post_id'] ?? 0);
        if ($this->posts->find($postId)) {
            $this->likes->toggle($postId, (int) $user['id']);
        }
        redirect(url(['page' => 'feed']));
    }
}