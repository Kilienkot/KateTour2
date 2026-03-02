<?php
    session_start();

    // Проверяем блокировку
    if (isset($_SESSION['block_until'][$ip]) && time() < $_SESSION['block_until'][$ip]) {
        header('Location: /login.php?blocked=1');
        exit();
    }

    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // password
    // $salt = '8';
    // $password = md5($salt.$password);

    //DB
        require "db.php";

    //Auth
    $sql = 'SELECT * FROM users WHERE login = ? AND password_hash = ?';
    $query = $pdo->prepare($sql);
    $query->execute([$login, $password]);

    $ip = $_SERVER['REMOTE_ADDR'];

    if ($query->rowCount() == 0) {
        // Увеличиваем счетчик попыток
        $_SESSION['attempts'][$ip] = ($_SESSION['attempts'][$ip] ?? 0) + 1;

        if ($_SESSION['attempts'][$ip] >= 3) {
            // Блокируем на 1 минуту
            $_SESSION['block_until'][$ip] = time() + 1;
            header('Location: /login.php?blocked=1');
        } else {
            header('Location: /login.php?error=1');
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
        
        header('Location: /admin.php');
        exit(); // Обязательно завершаем выполнение скрипта
    }

    function getCurrentUserId() {
        // Проверяем наличие куки user_id
        if (!isset($_COOKIE['id']) || empty($_COOKIE['id'])) {
        // Перенаправляем на страницу авторизации
            header('Location: /login.php');
            exit(); // Обязательно завершаем выполнение скрипта
        }
        
        return (int)$_COOKIE['id']; // Приводим к целому числу для безопасности
    }
?>
