<?php
// manage_applications.php

if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_COOKIE['role']) || $_COOKIE['role'] < 2) {
    header("Location: user-panel.php");
    exit();
}

require_once "lib/db.php";

// === Обработка изменения статуса ===
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $app_id     = (int)$_POST['app_id'];
    $new_status = $_POST['status'];
    
    $allowed = ['Новая', 'В обработке', 'Подтверждена', 'Отклонена'];
    
    if (in_array($new_status, $allowed)) {
        $stmt = $pdo->prepare("UPDATE applications SET status = ?, updated_at = NOW() WHERE id_application = ?");
        $stmt->execute([$new_status, $app_id]);
        $message = "✅ Статус заявки успешно обновлён!";
    } else {
        $message = "❌ Ошибка: недопустимый статус.";
    }
}

// === Получение всех заявок ===
$stmt = $pdo->query("
    SELECT 
        a.*,
        t.short_title AS tour_title,
        COALESCE(u.username, 'Гость') AS username
    FROM applications a
    LEFT JOIN tours t ON a.id_tour = t.id
    LEFT JOIN users u ON a.id_user = u.id
    ORDER BY a.application_date DESC
");
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заявками</title>
    <link rel="stylesheet" href="styles/css/admin-style.css">
    
    <style>
        .table-container {
            overflow-x: auto;
            max-width: 100%;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            min-width: 1200px; /* чтобы не сжималось слишком сильно */
            border-collapse: collapse;
        }
        
        th, td {
            padding: 10px 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }
        
        th {
            background-color: #f4f4f4;
            position: sticky;
            top: 0;
        }

        .form-container {
            max-width: 90dvw;
        }
        
        .status-Новая       { color: orange; font-weight: bold; }
        .status-В обработке { color: blue; font-weight: bold; }
        .status-Подтверждена{ color: green; font-weight: bold; }
        .status-Отклонена   { color: red; font-weight: bold; }
        
        .back-link {
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include "blocks/header.php"; ?>

    <main class="form-container" style="padding-top: 120rem;">
        <a href="admin.php" class="back-link">← Вернуться в админ-панель</a>
        
        <h1>Управление заявками</h1>
        
        <?php if ($message): ?>
            <p style="padding: 10px; background: #d4edda; color: #155724; border-radius: 5px; font-size: 30rem;">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <?php if (empty($applications)): ?>
            <p>Пока нет заявок.</p>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Дата заявки</th>
                            <th>Тур</th>
                            <th>Клиент</th>
                            <th>Имя</th>
                            <th>Телефон</th>
                            <th>Дата рождения</th>
                            <th>Комментарий</th>
                            <th>Статус</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= $app['id_application'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($app['application_date'])) ?></td>
                            <td><?= htmlspecialchars($app['tour_title'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($app['username']) ?></td>
                            <td><?= htmlspecialchars($app['name']) ?></td>
                            <td><?= htmlspecialchars($app['phone_number']) ?></td>
                            <td><?= $app['date_of_birth'] ? date('d.m.Y', strtotime($app['date_of_birth'])) : '—' ?></td>
                            <td><?= htmlspecialchars($app['comment'] ?? '') ?></td>
                            <td>
                                <span class="status-<?= $app['status'] ?>">
                                    <?= htmlspecialchars($app['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form action="" method="POST" style="margin:0;">
                                    <input type="hidden" name="app_id" value="<?= $app['id_application'] ?>">
                                    
                                    <select name="status" style="margin-bottom: 5px;">
                                        <option value="Новая"        <?= $app['status'] == 'Новая' ? 'selected' : '' ?>>Новая</option>
                                        <option value="В обработке"  <?= $app['status'] == 'В обработке' ? 'selected' : '' ?>>В обработке</option>
                                        <option value="Подтверждена" <?= $app['status'] == 'Подтверждена' ? 'selected' : '' ?>>Подтверждена</option>
                                        <option value="Отклонена"    <?= $app['status'] == 'Отклонена' ? 'selected' : '' ?>>Отклонена</option>
                                    </select>
                                    <br>
                                    <button type="submit" name="change_status" class="submit-btn">
                                        Изменить
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <?php include "blocks/footer.php"; ?>
</body>
</html>