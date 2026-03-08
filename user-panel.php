<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

// Подключение к БД
require "lib/db.php";

// Получение роли пользователя
$sql = 'SELECT role FROM users WHERE id = ?';
$query = $pdo->prepare($sql);
$query->execute([$_COOKIE['id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit();
}

// Проверка роли
if ($user['role'] == 2) {
    // Админ - перенаправляем в админ-панель
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="styles/css/admin-style.css" />
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main>
      <section class="admin-welcome">
        <h1>Добро пожаловать, <?php echo htmlspecialchars($_COOKIE['name']); ?>!</h1>
        <p>Это ваш личный кабинет.<br>Скоро, здесь вы сможете просматривать информацию о турах и управлять своим профилем.</p>
        <div class="admin-buttons">
          <a href="lib/logout.php" class="admin-btn logout-btn">Выход</a>
        </div>
      </section>
    </main>

    <?php include("blocks/footer.php") ?>

    <script src="main.js"></script>
</body>
</html>