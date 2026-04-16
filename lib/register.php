<?php
session_start();

// Проверяем блокировку
if (isset($_SESSION['block_until'][$ip]) && time() < $_SESSION['block_until'][$ip]) {
    header('Location: /register.php?blocked=1');
    exit();
}

$username = trim($_POST['username']);
$login = trim($_POST['login']);
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);

// Проверка на пустые поля
if (empty($username) || empty($login) || empty($password) || empty($confirm_password)) {
    header('Location: /register.php?error=3');
    exit();
}

// Проверка совпадения паролей
if ($password !== $confirm_password) {
    header('Location: /register.php?error=2');
    exit();
}

// Подключение к БД
require "db.php";

// Проверка существования пользователя
$sql = 'SELECT * FROM users WHERE login = ?';
$query = $pdo->prepare($sql);
$query->execute([$login]);

if ($query->rowCount() > 0) {
    header('Location: /register.php?error=1');
    exit();
}

// Создание нового пользователя
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Установка роли по умолчанию (1 - пользователь)
$sql = 'INSERT INTO users (username, login, password_hash, role) VALUES (?, ?, ?, 1)';
$query = $pdo->prepare($sql);
$result = $query->execute([$username, $login, $password_hash]);

// Проверка наличия 3 уровней доступа
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT role) as role_count FROM users");
$stmt->execute();
$role_count = $stmt->fetch(PDO::FETCH_ASSOC)['role_count'];

if ($role_count >= 3) {
    // Если уже есть 3 уровня, создаем пользователя с ролью 2 (админ)
    $sql = 'UPDATE users SET role = 2 WHERE login = ?';
    $query = $pdo->prepare($sql);
    $query->execute([$login]);
}

if ($result) {
    header('Location: /register.php?success=1');
} else {
    header('Location: /register.php?error=4');
}
exit();
?>