<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['admin_user']);
session_destroy();
header('Location: login.php');
exit;
