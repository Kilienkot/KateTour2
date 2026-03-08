<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

// Проверка роли пользователя
if (!isset($_COOKIE['role']) || $_COOKIE['role'] < 2) {
    header("Location: user-panel.php");
    exit();
}

// Подключение к БД
require "lib/db.php";

// Проверка наличия 3 уровней доступа
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT role) as role_count FROM users");
$stmt->execute();
$role_count = $_COOKIE['role'];
$show_user_management = $role_count >= 3;
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель</title>
    <link rel="stylesheet" href="styles/css/admin-style.css" />
  </head>
  <body>
    <?php include "blocks/header.php" ?>

    <main>
      <section class="admin-welcome">
        <h1>Здравствуйте, <?php echo htmlspecialchars($_COOKIE['name']); ?>. Что вы хотите сделать?</h1>
        <div class="admin-buttons">
          <a href="add-trip.php" class="admin-btn">Добавить выезд</a>
          <a href="edit-trips.php" class="admin-btn">Редактировать выезды</a>
          <?php if ($show_user_management): ?>
            <a href="manage-users.php" class="admin-btn">Управление пользователями</a>
          <?php endif; ?>
          <a href="lib/logout.php" class="admin-btn logout-btn">Выход</a>
        </div>
      </section>
    </main>

    <?php include("blocks/footer.php") ?>

    <script src="main.js"></script>
  </body>
</html>