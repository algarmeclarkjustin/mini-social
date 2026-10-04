<?php
declare(strict_types=1);

final class AuthController
{
    public function __construct(private UserModel $users)
    {
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $username = trim((string) ($_POST['username'] ?? ''));
            $user = $this->users->findByUsername($username);
            if ($user && password_verify((string) ($_POST['password'] ?? ''), $user['password'])) {
                session_regenerate_id(true);
                unset($user['password'], $user['email']);
                $_SESSION['user'] = $user;
                redirect(url(['page' => 'feed']));
            }
            set_flash('error', 'That username and password combination was not recognized.');
        }
        render('auth/login', ['title' => 'Sign in']);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $username = trim((string) ($_POST['username'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $fullName = trim((string) ($_POST['full_name'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            if (!preg_match('/^[a-zA-Z0-9_]{3,24}$/', $username) || !filter_var($email, FILTER_VALIDATE_EMAIL) || $fullName === '' || strlen($fullName) > 80 || strlen($password) < 8) {
                set_flash('error', 'Check your details. Usernames need 3-24 letters, numbers, or underscores, and passwords need 8 characters.');
            } elseif ($this->users->findByUsername($username)) {
                set_flash('error', 'That username is already taken.');
            } else {
                try {
                    $id = $this->users->create($username, $email, $password, $fullName);
                    $_SESSION['user'] = $this->users->findById($id);
                    session_regenerate_id(true);
                    redirect(url(['page' => 'feed']));
                } catch (PDOException $exception) {
                    set_flash('error', 'That email address is already registered.');
                }
            }
        }
        render('auth/register', ['title' => 'Create account']);
    }

    public function logout(): void
    {
        verify_csrf();
        $_SESSION = [];
        session_destroy();
        redirect(url(['page' => 'login']));
    }
}