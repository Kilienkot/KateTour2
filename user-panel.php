<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

// Подключение к БД
require_once "lib/db.php";

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
        <?php
            // Получаем заявки пользователя
            $stmt = $pdo->prepare("
                SELECT a.*, t.short_title 
                FROM applications a 
                JOIN tours t ON a.id_tour = t.id 
                WHERE a.id_user = ? 
                ORDER BY a.application_date DESC
            ");
            $stmt->execute([$_COOKIE['id']]);
            $applications = $stmt->fetchAll();
            ?>

            <section class="my-applications">
                <h2>Мои заявки</h2>
                
                <?php if (empty($applications)): ?>
                    <p>У вас пока нет заявок.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Тур</th>
                                <th>Дата подачи</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= htmlspecialchars($app['short_title']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($app['application_date'])) ?></td>
                                <td>
                                    <span class="status <?= $app['status'] ?>">
                                        <?= match($app['status']) {
                                            'new' => 'Новая',
                                            'processed' => 'В обработке',
                                            'confirmed' => 'Подтверждена',
                                            'canceled' => 'Отменена',
                                            default => $app['status']
                                        } ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        <div class="admin-buttons">
          <a href="lib/logout.php" class="admin-btn logout-btn">Выйти из аккаунта</a>
        </div>
      </section>
    </main>

    <?php include("blocks/footer.php") ?>

    <script src="main.js"></script>
</body>
</html>