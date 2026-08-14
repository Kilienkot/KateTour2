<?php
include "lib/db.php";

// Получаем все новости, отсортированные по дате (новые сверху)
$stmt = $pdo->prepare("
    SELECT id, title, content, image_path, created_at 
    FROM news 
    ORDER BY created_at DESC
");
$stmt->execute();
$all_news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Новости | KateTour</title>
    <link rel="stylesheet" href="styles/css/news-style.css" />
</head>
<body>
    <?php include("blocks/header.php") ?>

    <main class="news-page">
        <section class="news-header">
            <div class="container">
                <h1>Новости</h1>
                <p class="subtitle">Последние события и обновления из жизни KateTour</p>
            </div>
        </section>

        <section class="news-grid">
            <div class="container">
                <?php if (empty($all_news)): ?>
                    <p class="no-news">Пока нет опубликованных новостей.</p>
                <?php else: ?>
                    <div class="news-cards">
                        <?php foreach ($all_news as $news): ?>
                            <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="news-card">
                                <?php if (!empty($news['image_path'])): ?>
                                    <div class="news-card__image">
                                        <img src="<?php echo htmlspecialchars($news['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($news['title']); ?>">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="news-card__content">
                                    <div class="news-card__date">
                                        <?php echo date('d.m.Y', strtotime($news['created_at'])); ?>
                                    </div>
                                    <h2 class="news-card__title"><?php echo htmlspecialchars($news['title']); ?></h2>
                                    <p class="news-card__excerpt">
                                        <?php 
                                            $text = strip_tags($news['content']);
                                            echo htmlspecialchars(mb_substr($text, 0, 180)) . (mb_strlen($text) > 180 ? '...' : '');
                                        ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include "blocks/footer.php" ?>
    
    <script src="main.js"></script>
</body>
</html>