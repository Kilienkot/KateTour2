<?php
session_start();

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Проверяем блокировку
if (isset($_SESSION['block_until'][$ip]) && time() < $_SESSION['block_until'][$ip]) {
    header('Location: ../login.php?blocked=1');
    exit();
}

$login     = trim($_POST['login'] ?? '');
$password  = trim($_POST['password'] ?? '');

if (empty($login) || empty($password)) {
    header('Location: ../login.php?error=1');
    exit();
}

// Подключаем базу
require "db.php";

// Ищем пользователя
$sql = 'SELECT * FROM users WHERE login = ?';
$query = $pdo->prepare($sql);
$query->execute([$login]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    
    // Сброс блокировки при успешном входе
    unset($_SESSION['attempts'][$ip]);
    unset($_SESSION['block_until'][$ip]);

    // === Сохраняем данные пользователя ===
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['role']      = $user['role'];

    // Устанавливаем куки (на 7 дней)
    $cookie_time = time() + 3600 * 24 * 7;
    
    setcookie('login', $login, $cookie_time, "/");
    setcookie('name', $user['username'], $cookie_time, "/");
    setcookie('id', $user['id'], $cookie_time, "/");
    setcookie('role', $user['role'], $cookie_time, "/");

    // Перенаправление в зависимости от роли
    if ($user['role'] == 1) {
        header('Location: ../user-panel.php');
    } else {
        header('Location: ../admin.php');
    }
    exit();

} else {
    // Неудачная попытка входа
    $_SESSION['attempts'][$ip] = ($_SESSION['attempts'][$ip] ?? 0) + 1;

    if ($_SESSION['attempts'][$ip] >= 3) {
        $_SESSION['block_until'][$ip] = time() + 60; // блок на 1 минуту
        header('Location: ../login.php?blocked=1');
    } else {
        header('Location: ../login.php?error=1');
    }
    exit();
}