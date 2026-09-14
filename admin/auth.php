<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getLoggedInUser(): ?array
{
    return $_SESSION['admin_user'] ?? null;
}

function requireAdminAuth(): array
{
    $user = getLoggedInUser();
    if (!$user) {
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'json')) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized. Please log in.']);
            exit;
        }
        header('Location: login.php');
        exit;
    }
    return $user;
}

function hasRole(string $role): bool
{
    $user = getLoggedInUser();
    return $user && ($user['role'] === $role || $user['role'] === 'Super Admin');
}
