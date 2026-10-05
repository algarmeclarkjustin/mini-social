<?php
declare(strict_types=1);

session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../app/helpers.php';
foreach (glob(__DIR__ . '/../app/models/*.php') as $modelFile) {
    require_once $modelFile;
}
foreach (glob(__DIR__ . '/../app/controllers/*.php') as $controllerFile) {
    require_once $controllerFile;
}

try {
    $db = database();
    $users = new UserModel($db);
    $posts = new PostModel($db);
    $comments = new CommentModel($db);
    $likes = new LikeModel($db);
    $page = (string) ($_GET['page'] ?? (current_user() ? 'feed' : 'login'));

    switch ($page) {
        case 'login':
            (new AuthController($users))->login();
            break;
        case 'register':
            (new AuthController($users))->register();
            break;
        case 'logout':
            (new AuthController($users))->logout();
            break;
        case 'feed':
            (new FeedController($posts, $comments, $users))->index();
            break;
        case 'profile':
            (new ProfileController($users, $posts))->show();
            break;
        case 'profile-edit':
            (new ProfileController($users, $posts))->edit();
            break;
        case 'profile-update':
            (new ProfileController($users, $posts))->update();
            break;
        case 'post-create':
            (new PostController($posts))->create();
            break;
        case 'post-edit':
            (new PostController($posts))->edit();
            break;
        case 'post-update':
            (new PostController($posts))->update();
            break;
        case 'post-delete':
            (new PostController($posts))->delete();
            break;
        case 'comment-create':
            (new CommentController($comments))->create();
            break;
        case 'comment-update':
            (new CommentController($comments))->update();
            break;
        case 'comment-delete':
            (new CommentController($comments))->delete();
            break;
        case 'like':
            (new LikeController($likes, $posts))->toggle();
            break;
        case 'search-users':
            (new SearchController($users))->users();
            break;
        case 'admin':
            (new AdminController($users, $posts))->index();
            break;
        case 'admin-post-delete':
            (new AdminController($users, $posts))->deletePost();
            break;
        default:
            http_response_code(404);
            render('errors/not_found', ['title' => 'Page not found']);
    }
} catch (mysqli_sql_exception $exception) {
    http_response_code(500);
    render('errors/database', ['title' => 'Database connection needed']);
}