<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

include "lib/db.php";

$message = "";

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = trim($_POST['title']);
    $content     = trim($_POST['content']);
    $image_path  = null;

    // Обработка загрузки изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'sources/img/news/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $filepath = $upload_dir . $filename;

        // Перемещаем файл
        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $image_path = $filepath;
        }
    }

    // Вставка в базу
    $stmt = $pdo->prepare("
        INSERT INTO news (title, content, image_path, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$title, $content, $image_path])) {
        $message = "Новость успешно добавлена!";
    } else {
        $message = "Ошибка при добавлении новости.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить новость</title>
    <link rel="stylesheet" href="styles/css/admin-style.css">
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main>
        <a href="admin.php" class="back-link">← Вернуться в админ-панель</a>
        
        <div class="form-container">
            <h1>Добавить новую новость</h1>
            
            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="title">Заголовок новости</label>
                    <input type="text" id="title" name="title" placeholder="Например: Открытие нового маршрута в Архызе" required>
                </div>

                <div class="form-group">
                    <label for="image">Главное изображение (рекомендуется 1200×800)</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="content">Текст новости</label>
                    <textarea id="content" name="content" rows="15" placeholder="Полный текст новости..." required></textarea>
                </div>

                <button type="submit" class="submit-btn">Опубликовать новость</button>
            </form>
        </div>
    </main>

    <?php include "blocks/footer.php" ?>
</body>
</html>