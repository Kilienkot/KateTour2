<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

// Подключение к БД
include "lib/db.php";

$message = "";

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'edit') {
        // Получение данных
        $tour_id = intval($_POST['tour_id']);
        $short_title = trim($_POST['short_title']);
        $full_title = trim($_POST['full_title']) ?: null;
        $full_description = trim($_POST['full_description']);
        $age = trim($_POST['age']);
        $price = floatval($_POST['price']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $instructor_name = trim($_POST['instructor_name']);
        $difficulty = $_POST['difficulty'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Обновление тура
        $stmt = $pdo->prepare("UPDATE tours SET short_title = ?, full_title = ?, full_description = ?, age = ?, price = ?, start_date = ?, end_date = ?, instructor_name = ?, difficulty = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$short_title, $full_title, $full_description, $age, $price, $start_date, $end_date, $instructor_name, $difficulty, $is_active, $tour_id]);

    // Функция для сжатия изображения
    function resizeImage($source, $destination, $maxWidth = 800, $maxHeight = 600, $quality = 80) {
        $imageInfo = getimagesize($source);
        if (!$imageInfo) return false;

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // Рассчитать новые размеры
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        if ($ratio > 1) $ratio = 1; // Не увеличивать
        $newWidth = $width * $ratio;
        $newHeight = $height * $ratio;

        // Создать изображение
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

        // Сохранить
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($dst, $destination, $quality);
                break;
            case 'image/png':
                imagepng($dst, $destination, 9); // Максимальное сжатие для PNG
                break;
            case 'image/gif':
                imagegif($dst, $destination);
                break;
        }

        imagedestroy($src);
        imagedestroy($dst);
        return true;
    }

    // Обработка фото (если загружены новые)
    $upload_dir = 'sources/img/tours/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    for ($i = 1; $i <= 5; $i++) {
        if (isset($_FILES["photo_$i"]) && $_FILES["photo_$i"]['error'] == 0) {
            $file = $_FILES["photo_$i"];
            $filename = uniqid() . '_' . basename($file['name']);
            $filepath = $upload_dir . $filename;

            // Сжать и сохранить
            if (resizeImage($file['tmp_name'], $filepath)) {
                // Удалить старое фото
                $stmt = $pdo->prepare("SELECT filepath FROM tour_photos WHERE tour_id = ? AND sort_order = ?");
                $stmt->execute([$tour_id, $i]);
                $old_photo = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($old_photo && file_exists($old_photo['filepath'])) {
                    unlink($old_photo['filepath']);
                }

                // Обновить фото
                $new_size = filesize($filepath);
                $stmt = $pdo->prepare("UPDATE tour_photos SET filename = ?, original_filename = ?, filepath = ?, file_size = ?, mime_type = ? WHERE tour_id = ? AND sort_order = ?");
                $stmt->execute([$filename, $file['name'], $filepath, $new_size, $file['type'], $tour_id, $i]);
            }
        }
    }

        // Обработка программы
        if (isset($_POST['days'])) {
            // Удалить старую программу
            $stmt = $pdo->prepare("DELETE FROM tour_program WHERE tour_id = ?");
            $stmt->execute([$tour_id]);

            // Вставить новую
            $days = $_POST['days'];
            foreach ($days as $day) {
                $day_number = intval($day['number']);
                $short_title = trim($day['short_title']);
                $full_description = trim($day['full_description']);

                $stmt = $pdo->prepare("INSERT INTO tour_program (tour_id, day_number, short_title, full_description, sort_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$tour_id, $day_number, $short_title, $full_description, $day_number]);
            }
        }

        $message = "Тур обновлен!";

        // Отладка загрузки фото
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_FILES["photo_$i"])) {
                $error = $_FILES["photo_$i"]['error'];
                if ($error == 0) {
                    $message .= " Фото $i загружено.";
                } elseif ($error == 4) {
                    $message .= " Фото $i не выбрано.";
                } else {
                    $message .= " Ошибка загрузки фото $i: $error.";
                }
            } else {
                $message .= " Фото $i не установлено.";
            }
        }
    } elseif ($action == 'toggle') {
        $tour_id = intval($_POST['tour_id']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $stmt = $pdo->prepare("UPDATE tours SET is_active = ? WHERE id = ?");
        $stmt->execute([$is_active, $tour_id]);
        $message = "Статус тура изменен!";
    } elseif ($action == 'delete') {
        $tour_id = intval($_POST['tour_id']);
        // Удалить фото
        $stmt = $pdo->prepare("SELECT filepath FROM tour_photos WHERE tour_id = ?");
        $stmt->execute([$tour_id]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($photos as $photo) {
            if (file_exists($photo['filepath'])) {
                unlink($photo['filepath']);
            }
        }
        // Удалить из БД (каскадное удаление)
        $stmt = $pdo->prepare("DELETE FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $message = "Тур удален!";
    }
}

// Получение списка туров
$stmt = $pdo->prepare("SELECT id, short_title, is_active, start_date, end_date FROM tours ORDER BY id DESC");
$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение тура для редактирования
$edit_tour = null;
if (isset($_GET['edit'])) {
    $tour_id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$tour_id]);
    $edit_tour = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($edit_tour) {
        // Фото
        $stmt = $pdo->prepare("SELECT * FROM tour_photos WHERE tour_id = ? ORDER BY sort_order");
        $stmt->execute([$tour_id]);
        $edit_tour['photos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Программа
        $stmt = $pdo->prepare("SELECT * FROM tour_program WHERE tour_id = ? ORDER BY day_number");
        $stmt->execute([$tour_id]);
        $edit_tour['program'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать выезды</title>
    <link rel="stylesheet" href="styles/css/admin-style.css">
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main>
        <a href="admin.php" class="back-link">← Вернуться в админ</a>
        <div class="form-container">
            <h1>Редактировать выезды</h1>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>

            <?php if ($edit_tour): ?>
                <!-- Форма редактирования -->
                <h2>Редактировать тур: <?php echo htmlspecialchars($edit_tour['short_title']); ?></h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="tour_id" value="<?php echo $edit_tour['id']; ?>">

                    <div class="form-group">
                        <label for="short_title">Короткое название (например, "Камчатка") *</label>
                        <input type="text" id="short_title" name="short_title" value="<?php echo htmlspecialchars($edit_tour['short_title']); ?>" placeholder="Незабываемое путешествие по просторам Камчатки" required>
                    </div>

                    <div class="form-group">
                        <label for="full_title">Полное название (опционально)</label>
                        <input type="text" id="full_title" name="full_title" value="<?php echo htmlspecialchars($edit_tour['full_title'] ?: ''); ?>" placeholder="Полное название тура, если отличается от короткого">
                    </div>

                    <div class="form-group">
                        <label for="full_description">Полное описание (подробно о туре) *</label>
                        <textarea id="full_description" name="full_description" rows="5" placeholder="Приглашаем тебя с нами в захватывающее путешествие..." required><?php echo htmlspecialchars($edit_tour['full_description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="age">Возраст (от 1 до 99) *</label>
                        <input type="text" id="age" name="age" value="<?php echo htmlspecialchars($edit_tour['age']); ?>" placeholder="от 10 до 99" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Стоимость (в рублях) *</label>
                        <input type="number" id="price" name="price" value="<?php echo $edit_tour['price']; ?>" step="0.01" placeholder="80000" required>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Дата начала *</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo $edit_tour['start_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">Дата окончания *</label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo $edit_tour['end_date']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="instructor_name">Имя инструктора (опционально)</label>
                        <input type="text" id="instructor_name" name="instructor_name" value="<?php echo htmlspecialchars($edit_tour['instructor_name'] ?: ''); ?>" placeholder="Екатерина">
                    </div>

                    <div class="form-group">
                        <label for="difficulty">Сложность (выберите уровень) *</label>
                        <select id="difficulty" name="difficulty" required>
                            <option value="легкий" <?php if ($edit_tour['difficulty'] == 'легкий') echo 'selected'; ?>>Лёгкий</option>
                            <option value="средний" <?php if ($edit_tour['difficulty'] == 'средний') echo 'selected'; ?>>Средний</option>
                            <option value="сложный" <?php if ($edit_tour['difficulty'] == 'сложный') echo 'selected'; ?>>Сложный</option>
                            <option value="эксперт" <?php if ($edit_tour['difficulty'] == 'эксперт') echo 'selected'; ?>>Эксперт</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="is_active">Активный тур</label>
                        <input type="checkbox" id="is_active" name="is_active" <?php if ($edit_tour['is_active']) echo 'checked'; ?>>
                    </div>

                    <div class="form-group">
                        <label>Фотографии (загрузите новые, если нужно заменить)</label>
                        <div class="photos">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="photo-input">
                                    <label for="photo_<?php echo $i; ?>">Фото <?php echo $i; ?> <?php if ($i == 1) echo '(основное)'; ?></label>
                                    <input type="file" id="photo_<?php echo $i; ?>" name="photo_<?php echo $i; ?>" accept="image/*">
                                    <?php if (isset($edit_tour['photos'][$i-1])): ?>
                                        <small>Текущее: <?php echo htmlspecialchars($edit_tour['photos'][$i-1]['original_filename']); ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Программа тура</label>
                        <div id="program-days">
                            <?php if (count($edit_tour['program']) > 0): ?>
                                <?php foreach ($edit_tour['program'] as $index => $day): ?>
                                    <div class="program-day">
                                        <label>День <?php echo $day['day_number']; ?></label>
                                        <input type="hidden" name="days[<?php echo $index; ?>][number]" value="<?php echo $day['day_number']; ?>">
                                        <input type="text" name="days[<?php echo $index; ?>][short_title]" value="<?php echo htmlspecialchars($day['short_title']); ?>" placeholder="Краткое описание дня">
                                        <textarea name="days[<?php echo $index; ?>][full_description]" placeholder="Полное описание дня" rows="3"><?php echo htmlspecialchars($day['full_description']); ?></textarea>
                                        <button type="button" class="remove-day" onclick="removeDay(this)">Удалить день</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="program-day">
                                    <label>День 1</label>
                                    <input type="hidden" name="days[0][number]" value="1">
                                    <input type="text" name="days[0][short_title]" placeholder="Краткое описание дня">
                                    <textarea name="days[0][full_description]" placeholder="Полное описание дня" rows="3"></textarea>
                                    <button type="button" class="remove-day" onclick="removeDay(this)">Удалить день</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="add-day" onclick="addDay()">Добавить день</button>
                    </div>

                    <button type="submit" class="submit-btn">Обновить тур</button>
                    <a href="edit-trips.php" class="back-link">Отмена</a>
                </form>
            <?php else: ?>
                <!-- Список туров -->
                <h2>Список выездов</h2>
                <?php if (count($tours) > 0): ?>
                    <div class="tours-list">
                        <?php foreach ($tours as $tour): ?>
                            <div class="tour-item <?php if (!$tour['is_active']) echo 'inactive'; ?>">
                                <h3><?php echo htmlspecialchars($tour['short_title']); ?></h3>
                                <p><?php echo date('d.m.Y', strtotime($tour['start_date'])); ?> - <?php echo date('d.m.Y', strtotime($tour['end_date'])); ?></p>
                                <p>Статус: <?php echo $tour['is_active'] ? 'Активный' : 'Неактивный'; ?></p>
                                <div class="tour-actions">
                                    <form action="" method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                                        <input type="checkbox" name="is_active" <?php if ($tour['is_active']) echo 'checked'; ?> onchange="this.form.submit()">
                                        <label>Активный</label>
                                    </form>
                                    <a href="?edit=<?php echo $tour['id']; ?>" class="edit-btn">Редактировать</a>
                                    <form action="" method="POST" style="display: inline; margin-left: auto;" onsubmit="return confirm('Удалить тур?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                                        <button type="submit" class="delete-btn">Удалить</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Нет выездов для редактирования.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include "blocks/footer.php" ?>

    <script>
        let dayCount = <?php echo count($edit_tour['program'] ?? []); ?>;

        function addDay() {
            dayCount++;
            const container = document.getElementById('program-days');
            const dayDiv = document.createElement('div');
            dayDiv.className = 'program-day';
            dayDiv.innerHTML = `
                <label>День ${dayCount}</label>
                <input type="hidden" name="days[${dayCount-1}][number]" value="${dayCount}">
                <input type="text" name="days[${dayCount-1}][short_title]" placeholder="Краткое описание дня">
                <textarea name="days[${dayCount-1}][full_description]" placeholder="Полное описание дня" rows="3"></textarea>
                <button type="button" class="remove-day" onclick="removeDay(this)">Удалить день</button>
            `;
            container.appendChild(dayDiv);
        }

        function removeDay(button) {
            button.parentElement.remove();
            // Пересчитать номера дней
            const days = document.querySelectorAll('.program-day');
            days.forEach((day, index) => {
                day.querySelector('label').textContent = `День ${index + 1}`;
                day.querySelector('input[type="hidden"]').value = index + 1;
                day.querySelector('input[type="text"]').name = `days[${index}][short_title]`;
                day.querySelector('textarea').name = `days[${index}][full_description]`;
            });
            dayCount = days.length;
        }
    </script>
</body>
</html>
