<?php
session_start();
require_once "lib/db.php";

// Проверка доступа (только админ)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 3) {
    header('Location: login.php');
    exit();
}

$pdo = getDBConnection();

// Фильтры
$table_filter   = $_GET['table'] ?? '';
$action_filter  = $_GET['action'] ?? '';
$user_filter    = $_GET['user'] ?? '';
$date_from      = $_GET['date_from'] ?? '';
$date_to        = $_GET['date_to'] ?? '';

// Основной запрос
$sql = "SELECT * FROM audit_log WHERE 1=1";
$params = [];

if ($table_filter) {
    $sql .= " AND table_name = ?";
    $params[] = $table_filter;
}
if ($action_filter) {
    $sql .= " AND action = ?";
    $params[] = $action_filter;
}
if ($user_filter) {
    $sql .= " AND username LIKE ?";
    $params[] = "%$user_filter%";
}
if ($date_from) {
    $sql .= " AND DATE(created_at) >= ?";
    $params[] = $date_from;
}
if ($date_to) {
    $sql .= " AND DATE(created_at) <= ?";
    $params[] = $date_to;
}

$sql .= " ORDER BY created_at DESC LIMIT 500";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Логи изменений — Админ панель</title>
    <link rel="stylesheet" href="styles/css/admin-style.css">
</head>
<body>

<?php include "blocks/header.php" ?>

<main>
     <a href="admin.php" class="back-link">← Вернуться в админ</a>

    <h1 class="logs__title">Логи изменений в базе данных</h1>
    
    <!-- Фильтры -->
    <div class="filter-form">
        <form method="GET">
            <select name="table">
                <option value="">Все таблицы</option>
                <option value="tours" <?= $table_filter=='tours'?'selected':'' ?>>tours</option>
                <option value="tour_inclusions" <?= $table_filter=='tour_inclusions'?'selected':'' ?>>tour_inclusions</option>
                <option value="tour_program" <?= $table_filter=='tour_program'?'selected':'' ?>>tour_program</option>
                <option value="tour_photos" <?= $table_filter=='tour_photos'?'selected':'' ?>>tour_photos</option>
            </select>
    
            <select name="action">
                <option value="">Все действия</option>
                <option value="INSERT" <?= $action_filter=='INSERT'?'selected':'' ?>>INSERT</option>
                <option value="UPDATE" <?= $action_filter=='UPDATE'?'selected':'' ?>>UPDATE</option>
                <option value="DELETE" <?= $action_filter=='DELETE'?'selected':'' ?>>DELETE</option>
            </select>
    
            <input type="text" name="user" placeholder="Пользователь" value="<?= htmlspecialchars($user_filter) ?>">
            <input type="date" name="date_from" value="<?= $date_from ?>">
            <input type="date" name="date_to" value="<?= $date_to ?>">
            
            <button type="submit">Фильтровать</button>
            <a href="audit_logs.php">Сбросить</a>
        </form>
    </div>
    
    <table>
        <tr>
            <th>Дата</th>
            <th>Таблица</th>
            <th>ID записи</th>
            <th>Действие</th>
            <th>Пользователь</th>
            <th>Изменения</th>
        </tr>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log['created_at'] ?></td>
            <td><strong><?= htmlspecialchars($log['table_name']) ?></strong></td>
            <td><?= $log['record_id'] ?></td>
            <td>
                <span style="color: 
                    <?= $log['action']=='INSERT' ? 'green' : ($log['action']=='DELETE' ? 'red' : 'orange') ?>">
                    <?= $log['action'] ?>
                </span>
            </td>
            <td><?= htmlspecialchars($log['username'] ?? '—') ?></td>
            <td>
                <?php if ($log['old_values']): ?>
                    <div class="old-values"><strong>Было:</strong> <?= htmlspecialchars(json_encode($log['old_values'], JSON_UNESCAPED_UNICODE)) ?></div>
                <?php endif; ?>
                <?php if ($log['new_values']): ?>
                    <div class="new-values"><strong>Стало:</strong> <?= htmlspecialchars(json_encode($log['new_values'], JSON_UNESCAPED_UNICODE)) ?></div>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <?php if (empty($logs)): ?>
        <p>Логов пока нет.</p>
    <?php endif; ?>
</main>


</body>
</html>