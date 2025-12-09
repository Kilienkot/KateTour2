<?php
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // password
    $salt = 'adminpassword';
    $password = md5($salt.$password);

    //DB
        require "db.php";

    //Auth
    $sql = 'SELECT * FROM users WHERE login = ? AND password_hash = ?';
    $query = $pdo->prepare($sql);
    $query->execute([$login, $password]);

    if ($query->rowCount() == 0) {
        header('Location: /login.php?error=1');
        exit();
    } else {
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
