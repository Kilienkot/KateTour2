<?php
include "lib/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: news.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT id, title, content, image_path, created_at 
    FROM news 
    WHERE id = ?
");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    header("Location: news.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($news['title']); ?> | KateTour</title>
    <link rel="stylesheet" href="styles/css/news-style.css" />
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main class="news-detail">
        <article class="news-article">
            <div class="container">
                
                <!-- Дата и назад -->
                <div class="news-detail__meta">
                    <a href="news.php" class="news-detail__back">← Все новости</a>
                    <span class="news-detail__date">
                        <?php echo date('d.m.Y', strtotime($news['created_at'])); ?>
                    </span>
                </div>

                <!-- Заголовок -->
                <h1 class="news-detail__title"><?php echo htmlspecialchars($news['title']); ?></h1>

                <!-- Изображение -->
                <?php if (!empty($news['image_path'])): ?>
                    <div class="news-detail__image">
                        <img src="<?php echo htmlspecialchars($news['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($news['title']); ?>">
                    </div>
                <?php endif; ?>

                <!-- Основной контент -->
                <div class="news-detail__content">
                    <?php 
                        // Выводим контент с сохранением переносов строк и параграфов
                        $content = nl2br(htmlspecialchars($news['content']));
                        echo $content; 
                    ?>
                </div>

            </div>
        </article>
    </main>

    <?php include "blocks/footer.php" ?>
    
    <script src="main.js"></script>
</body>
</html>