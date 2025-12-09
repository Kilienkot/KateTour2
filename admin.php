<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}
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
        </div>
      </section>
    </main>

    <?php include("blocks/footer.php") ?>

    <script src="main.js"></script>
  </body>
</html>
