<?php
declare(strict_types=1);

final class SearchController
{
    public function __construct(private UserModel $users)
    {
    }

    public function users(): void
    {
        require_auth();
        $query = trim((string) ($_GET['q'] ?? ''));
        render('search/users', ['title' => 'Find people', 'query' => $query, 'users' => $query === '' ? [] : $this->users->search($query)]);
    }
}