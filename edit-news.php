<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

include "lib/db.php";

$message = "";

// Функция для сжатия изображения (скопирована из add-news.php для единообразия)
function resizeImageNews($source, $destination, $maxWidth = 1200, $maxHeight = 800, $quality = 80) {
    $imageInfo = getimagesize($source);
    if (!$imageInfo) return false;

    $width = $imageInfo[0];
    $height = $imageInfo[1];
    $mime = $imageInfo['mime'];

    $ratio = min($maxWidth / $width, $maxHeight / $height);
    if ($ratio > 1) $ratio = 1;
    $newWidth = $width * $ratio;
    $newHeight = $height * $ratio;

    switch ($mime) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $src = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $src = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($dst, $destination, $quality);
            break;
        case 'image/png':
            imagepng($dst, $destination, 9);
            break;
        case 'image/gif':
            imagegif($dst, $destination);
            break;
    }

    imagedestroy($src);
    imagedestroy($dst);
    return true;
}

// Обработка форм
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'edit') {
        // Редактирование новости
        $news_id = intval($_POST['news_id']);
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $image_path = null;

        // Проверяем, загружено ли новое изображение
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'sources/img/news/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $filepath = $upload_dir . $filename;

            // Сжимаем и сохраняем
            if (resizeImageNews($_FILES['image']['tmp_name'], $filepath)) {
                $image_path = $filepath;
                
                // Удаляем старое изображение
                $stmt = $pdo->prepare("SELECT image_path FROM news WHERE id = ?");
                $stmt->execute([$news_id]);
                $old_image = $stmt->fetchColumn();
                if ($old_image && file_exists($old_image)) {
                    unlink($old_image);
                }
            }
        }

        // Обновляем новость
        if ($image_path !== null) {
            // Если загружено новое изображение
            $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$title, $content, $image_path, $news_id]);
        } else {
            // Если изображение не меняется
            $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$title, $content, $news_id]);
        }

        $message = "Новость обновлена!";
        
    } elseif ($action == 'delete') {
        // Удаление новости
        $news_id = intval($_POST['news_id']);
        
        // Удаляем изображение
        $stmt = $pdo->prepare("SELECT image_path FROM news WHERE id = ?");
        $stmt->execute([$news_id]);
        $image_path = $stmt->fetchColumn();
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }
        
        // Удаляем запись
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$news_id]);
        
        $message = "Новость удалена!";
    }
}

// Получение списка новостей
$stmt = $pdo->prepare("SELECT id, title, image_path, created_at FROM news ORDER BY created_at DESC");
$stmt->execute();
$news_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение новости для редактирования
$edit_news = null;
if (isset($_GET['edit'])) {
    $news_id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$news_id]);
    $edit_news = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать новости</title>
    <link rel="stylesheet" href="styles/css/admin-style.css">
    <link rel="icon" type="image/png" href="sources/img/icon.png">
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main>
        <a href="admin.php" class="back-link">← Вернуться в админ-панель</a>
        
        <div class="form-container">
            <h1>Редактировать новости</h1>
            
            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <?php if ($edit_news): ?>
                <!-- Форма редактирования новости -->
                <h2>Редактировать новость: <?= htmlspecialchars($edit_news['title']) ?></h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="news_id" value="<?= $edit_news['id'] ?>">

                    <div class="form-group">
                        <label for="title">Заголовок новости *</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($edit_news['title']) ?>" required>
                    </div>

                    <div class="form-group image-upload" id="imageUploadGroup">
                        <label for="image">Главное изображение новости</label>
                        
                        <?php if ($edit_news['image_path'] && file_exists($edit_news['image_path'])): ?>
                            <div class="current-image">
                                <p>Текущее изображение:</p>
                                <img src="<?= $edit_news['image_path'] ?>" alt="Текущее изображение" style="max-width: 300px; max-height: 200px; border-radius: 8px; margin: 10px 0;">
                                <p><small>Загрузите новое изображение, чтобы заменить текущее</small></p>
                            </div>
                        <?php endif; ?>

                        <div class="upload-area">
                            <div class="upload-content">
                                <div class="upload-icon">📸</div>
                                <p>Нажмите для выбора нового фото<br>или перетащите файл сюда</p>
                                <span class="recommended">Рекомендуемый размер: 1200 × 800 px</span>
                            </div>
                        </div>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="content">Текст новости *</label>
                        <textarea id="content" name="content" rows="15" required><?= htmlspecialchars($edit_news['content']) ?></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Обновить новость</button>
                    <a href="edit-news.php" class="back-link" style="margin-left: 15px;">Отмена</a>
                </form>

            <?php else: ?>
                <!-- Список новостей -->
                <h2>Список новостей</h2>
                
                <?php if (count($news_list) > 0): ?>
                    <div class="news-list">
                        <?php foreach ($news_list as $news): ?>
                            <div class="news-item">
                                <div class="news-info">
                                    <?php if ($news['image_path'] && file_exists($news['image_path'])): ?>
                                        <img src="<?= $news['image_path'] ?>" alt="<?= htmlspecialchars($news['title']) ?>" class="news-thumbnail">
                                    <?php endif; ?>
                                    <div class="news-details">
                                        <h3><?= htmlspecialchars($news['title']) ?></h3>
                                        <p class="news-date"><?= date('d.m.Y H:i', strtotime($news['created_at'])) ?></p>
                                    </div>
                                </div>
                                
                                <div class="news-actions">
                                    <a href="?edit=<?= $news['id'] ?>" class="edit-btn">Редактировать</a>
                                    
                                    <form action="" method="POST" style="display: inline;" onsubmit="return confirm('Удалить новость «<?= htmlspecialchars($news['title']) ?>»?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                                        <button type="submit" class="delete-btn">Удалить</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Нет новостей для редактирования. <a href="add-news.php">Добавить первую новость</a></p>
                <?php endif; ?>
                
                <div style="margin-top: 20px; text-align: center;">
                    <a href="add-news.php" class="submit-btn" style="display: inline-block; text-decoration: none; text-align: center; width: 90%;">➕ Добавить новую новость</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include "blocks/footer.php" ?>

    <script>
        // Простой скрипт для предпросмотра изображения при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('image');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Показываем превью загруженного изображения
                            const currentImage = document.querySelector('.current-image img');
                            if (currentImage) {
                                currentImage.src = e.target.result;
                            } else {
                                // Если нет блока с текущим изображением - создаем
                                const uploadArea = document.querySelector('.upload-area');
                                const preview = document.createElement('div');
                                preview.className = 'current-image';
                                preview.innerHTML = `
                                    <p>Новое изображение:</p>
                                    <img src="${e.target.result}" alt="Новое изображение" style="max-width: 300px; max-height: 200px; border-radius: 8px; margin: 10px 0;">
                                `;
                                uploadArea.parentNode.insertBefore(preview, uploadArea);
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>