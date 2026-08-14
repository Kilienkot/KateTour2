<?php
session_start();

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // Проверяем блокировку
    if (isset($_SESSION['block_until'][$id]) && time() < $_SESSION['block_until'][$id]) {
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
    // Сброс счетчика при успешном логине
    unset($_SESSION['attempts'][$id]);
    unset($_SESSION['block_until'][$id]);

    // === Сохраняем данные пользователя ===
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['role']      = $user['role'];

// Устанавливаем куки (логин, имя и роль раздельно)
setcookie('login', $login, time() + 3600 * 24 * 7, "/");
setcookie('name', $name, time() + 3600 * 24 * 7, "/");
setcookie('id', $id, time() + 3600 * 24 * 7, "/");
setcookie('role', $user['role'], time() + 3600 * 24 * 7, "/");

header('Location: ../admin.php');
    exit(); // Обязательно завершаем выполнение скрипта
} else {
    // Увеличиваем счетчик попыток
    $_SESSION['attempts'][$id] = ($_SESSION['attempts'][$id] ?? 0) + 1;

    if ($_SESSION['attempts'][$id] >= 3) {
        // Блокируем на 1 минуту
        $_SESSION['block_until'][$id] = time() + 1;
        header('Location: ../login.php?blocked=1');
    } else {
        header('Location: ../login.php?error=1');
    }
    exit();
}
    $ip = $_SERVER['REMOTE_ADDR'];

    if ($query->rowCount() == 0) {
        // Увеличиваем счетчик попыток
        $_SESSION['attempts'][$ip] = ($_SESSION['attempts'][$ip] ?? 0) + 1;

        if ($_SESSION['attempts'][$ip] >= 3) {
            // Блокируем на 1 минуту
            $_SESSION['block_until'][$ip] = time() + 1;
            header('Location: ../login.php?blocked=1');
        } else {
            header('Location: ../login.php?error=1');
        }
        exit();
    } else {
        // Сброс счетчика при успешном логине
        unset($_SESSION['attempts'][$ip]);
        unset($_SESSION['block_until'][$ip]);
        
        // Получаем данные пользователя
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        // Проверяем наличие имени
        if (!isset($user['username'])) {
            $user['username'] = ''; // Устанавливаем пустое имя, если его нет
        }

        // Создаем переменную name с именем пользователя
        $name = $user['username'];

// Создаём переменную с id
$id = $user['id'];

// Устанавливаем куки (логин и имя раздельно)
setcookie('login', $login, time() + 3600 * 24 * 7, "/");
setcookie('name', $name, time() + 3600 * 24 * 7, "/");
setcookie('id', $id, time() + 3600 * 24 * 7, "/");

// Проверяем роль пользователя
if ($user['role'] == 1) {
    header('Location: ../user-panel.php');
} else {
    header('Location: ../admin.php');
}
        exit(); // Обязательно завершаем выполнение скрипта
    }

    function getCurrentUserId() {
        // Проверяем наличие куки user_id
        if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
        // Перенаправляем на страницу авторизации
            header('Location: ../login.php');
            exit(); // Обязательно завершаем выполнение скрипта
        }
        
        return (int)$_COOKIE['id']; // Приводим к целому числу для безопасности
    }
?>