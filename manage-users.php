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
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // Проверка, что такой логин не занят другим пользователем
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ? AND id != ?");
        $stmt->execute([$login, $user_id]);
        if ($stmt->rowCount() > 0) {
            $message = "Ошибка: Логин уже занят другим пользователем!";
        } else {
            // Базовое обновление (без пароля)
            $stmt = $pdo->prepare("UPDATE users SET username = ?, login = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $login, $role, $user_id]);
            
            // Если пароль указан - обновляем его
            if (!empty($new_password)) {
                // Проверяем, что пароль не короче 6 символов
                if (mb_strlen($new_password) < 6) {
                    $message = "Пароль должен содержать минимум 6 символов!";
                } elseif ($new_password !== $confirm_password) {
                    $message = "Пароли не совпадают!";
                } else {
                    // Хешируем и обновляем пароль
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$password_hash, $user_id]);
                    $message = "Пользователь обновлен! Пароль изменен.";
                }
            } else {
                $message = "Пользователь обновлен!";
            }
        }
    } elseif ($action == 'delete') {
        $user_id = intval($_POST['user_id']);
        
        // Проверяем, не пытается ли админ удалить самого себя
        if ($user_id == $_COOKIE['id']) {
            $message = "Нельзя удалить самого себя!";
        } else {
            // Удалить пользователя
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = "Пользователь удален!";
        }
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
    <link rel="icon" type="image/png" href="sources/img/icon.png">
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main>
        <a href="admin.php" class="back-link">← Вернуться в админ</a>
        <div class="form-container">
            <h1>Управление пользователями</h1>
            <?php if ($message): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <div class="users-list">
                <?php foreach ($users as $user): ?>
                    <div class="user-item">
                        <form action="" method="POST" onsubmit="return validatePassword(this)">
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

                            <!-- ========== НОВЫЙ БЛОК С ПАРОЛЕМ ========== -->
                            <div class="form-group password-section">
                                <label>Пароль</label>
                                <div class="password-fields">
                                    <div class="password-field">
                                        <input type="password" id="new_password_<?php echo $user['id']; ?>" name="new_password" placeholder="Новый пароль (оставьте пустым, если не менять)" autocomplete="new-password">
                                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new_password_<?php echo $user['id']; ?>', this)">
                                            👁️
                                        </button>
                                    </div>
                                    <div class="password-field">
                                        <input type="password" id="confirm_password_<?php echo $user['id']; ?>" name="confirm_password" placeholder="Подтвердите новый пароль" autocomplete="new-password">
                                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirm_password_<?php echo $user['id']; ?>', this)">
                                            👁️
                                        </button>
                                    </div>
                                    <div id="password_error_<?php echo $user['id']; ?>" class="password-error" style="color: red; display: none; margin-top: 5px;"></div>
                                </div>
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
    <script src="main.js"></script>

    <script>
        // Функция для переключения видимости пароля
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    button.textContent = '🙈';
                } else {
                    input.type = 'password';
                    button.textContent = '👁️';
                }
            }
        }

        // Функция валидации перед отправкой формы
        function validatePassword(form) {
            const newPassword = form.querySelector('input[name="new_password"]');
            const confirmPassword = form.querySelector('input[name="confirm_password"]');
            const errorDiv = form.querySelector('.password-error');
            
            // Если оба поля пустые - пропускаем (пароль не меняем)
            if (!newPassword.value && !confirmPassword.value) {
                return true;
            }

            // Проверяем, что оба поля заполнены
            if (!newPassword.value || !confirmPassword.value) {
                errorDiv.textContent = 'Заполните оба поля для смены пароля!';
                errorDiv.style.display = 'block';
                return false;
            }

            // Проверяем длину пароля
            if (newPassword.value.length < 6) {
                errorDiv.textContent = 'Пароль должен содержать минимум 6 символов!';
                errorDiv.style.display = 'block';
                return false;
            }

            // Проверяем совпадение паролей
            if (newPassword.value !== confirmPassword.value) {
                errorDiv.textContent = 'Пароли не совпадают!';
                errorDiv.style.display = 'block';
                return false;
            }

            // Всё хорошо
            errorDiv.style.display = 'none';
            return true;
        }

        // Скрываем ошибку при изменении полей
        document.querySelectorAll('input[name="new_password"], input[name="confirm_password"]').forEach(input => {
            input.addEventListener('input', function() {
                const form = this.closest('form');
                const errorDiv = form.querySelector('.password-error');
                errorDiv.style.display = 'none';
            });
        });
    </script>
</body>
</html>