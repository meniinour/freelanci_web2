<?php
class Auth
{
    private static bool $sessionStarted = false;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            self::$sessionStarted = true;
        }
    }

    public static function isLoggedIn(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    }

    public static function isFreelance(): bool
    {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'freelance';
    }

    public static function isClient(): bool
    {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'client';
    }

    public static function login(array $user): void
    {
        self::startSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['type_utilisateur'];
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
    }

    public static function getCurrentUser(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id'     => $_SESSION['user_id'],
            'nom'    => $_SESSION['user_nom'] ?? '',
            'prenom' => $_SESSION['user_prenom'] ?? '',
            'email'  => $_SESSION['user_email'] ?? '',
            'type'   => $_SESSION['user_type'] ?? ''
        ];
    }

    public static function getUserId(): ?int
    {
        return self::isLoggedIn() ? (int)$_SESSION['user_id'] : null;
    }

    public static function requireLogin(string $redirect = 'login.php'): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function requireGuest(string $redirect = 'index.php'): void
    {
        if (self::isLoggedIn()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function requireAdmin(string $redirect = 'index.php'): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ' . $redirect);
            exit;
        }
    }
}
