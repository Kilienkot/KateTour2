<?php
    // Удаляем все куки пользователя
    setcookie('name', '', time() - 3600 * 24 * 7, "/");
    setcookie('login', '', time() - 3600 * 24 * 7, "/");
    setcookie('role', $role, time() - 3600 * 24 * 7, "/");
    setcookie('id', $id, time() - 3600 * 24 * 7, "/");

    // Перенаправляем на главную страницу
    header('Location: /');
    exit();
?>