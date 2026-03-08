<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

// Подключение к БД
require "lib/db.php";

// Проверка наличия 3 уровней доступа (для отображения кнопки)
$role_count = $_COOKIE['role'];
$show_user_management = $role_count >= 3;

if (!$show_user_management) {
    header("Location: admin.php");
    exit();
}

// Обработка формы
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'update') {
        $user_id = intval($_POST['user_id']);
        $username = trim($_POST['username']);
        $login = trim($_POST['login']);
        $role = intval($_POST['role']);

        // Обновление пользователя
        $stmt = $pdo->prepare("UPDATE users SET username = ?, login = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $login, $role, $user_id]);
        $message = "Пользователь обновлен!";
    } elseif ($action == 'delete') {
        $user_id = intval($_POST['user_id']);
        // Удалить пользователя
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $message = "Пользователь удален!";
    }
}

// Получение списка пользователей
$stmt = $pdo->prepare("SELECT id, username, login, role FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="styles/css/admin-style.css">
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main>
        <a href="admin.php" class="back-link">← Вернуться в админ</a>
        <div class="form-container">
            <h1>Управление пользователями</h1>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="users-list">
                <?php foreach ($users as $user): ?>
                    <div class="user-item">
                        <form action="" method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                            <div class="form-group">
                                <label>Имя пользователя</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Логин</label>
                                <input type="text" name="login" value="<?php echo htmlspecialchars($user['login']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Роль</label>
                                <select name="role" required>
                                    <option value="1" <?php if ($user['role'] == 1) echo 'selected'; ?>>Пользователь</option>
                                    <option value="2" <?php if ($user['role'] == 2) echo 'selected'; ?>>Админ</option>
                                    <option value="3" <?php if ($user['role'] == 3) echo 'selected'; ?>>Супер-админ</option>
                                </select>
                            </div>

                            <button type="submit" class="submit-btn">Обновить</button>
                        </form>

                        <form action="" method="POST" style="display: inline; margin-left: 10px;" onsubmit="return confirm('Удалить пользователя?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="delete-btn">Удалить</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <?php include "blocks/footer.php" ?>
</body>
</html>