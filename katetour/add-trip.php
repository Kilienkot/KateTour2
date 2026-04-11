<?php
// Проверка авторизации
if (!isset($_COOKIE['id'])) {
    header("Location: login.php");
    exit();
}

// Подключение к БД
include "lib/db.php";

$message = "";

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получение данных
    $short_title = trim($_POST['short_title']);
    $full_title = trim($_POST['full_title']) ?: null;
    $full_description = trim($_POST['full_description']);
    $age = trim($_POST['age']);
    $price = floatval($_POST['price']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $instructor_name = trim($_POST['instructor_name']);
    $difficulty = $_POST['difficulty'];

    // Вставка в tours
    $stmt = $pdo->prepare("INSERT INTO tours (short_title, full_title, full_description, age, price, start_date, end_date, instructor_name, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$short_title, $full_title, $full_description, $age, $price, $start_date, $end_date, $instructor_name, $difficulty]);
    $tour_id = $pdo->lastInsertId();

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

    // Обработка фото
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
                $new_size = filesize($filepath);
                $stmt = $pdo->prepare("INSERT INTO tour_photos (tour_id, filename, original_filename, filepath, file_size, mime_type, is_primary, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$tour_id, $filename, $file['name'], $filepath, $new_size, $file['type'], $i == 1 ? 1 : 0, $i]);
            }
        }
    }

    // Обработка программы
    if (isset($_POST['days'])) {
        $days = $_POST['days'];
        foreach ($days as $day) {
            $day_number = intval($day['number']);
            $short_title = trim($day['short_title']);
            $full_description = trim($day['full_description']);

            $stmt = $pdo->prepare("INSERT INTO tour_program (tour_id, day_number, short_title, full_description, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tour_id, $day_number, $short_title, $full_description, $day_number]);
        }
    }

    // Обработка включений
    if (isset($_POST['inclusions'])) {
        $inclusions = $_POST['inclusions'];
        foreach ($inclusions as $index => $inclusion) {
            $title = trim($inclusion['title']);
            $description = trim($inclusion['description']);

            if (!empty($title)) {
                $stmt = $pdo->prepare("INSERT INTO tour_inclusions (tour_id, title, description, sort_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$tour_id, $title, $description, $index + 1]);
            }
        }
    }

    $message = "Тур успешно добавлен!";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить выезд</title>
    <link rel="stylesheet" href="styles/css/admin-style.css">
</head>
<body>
    <?php include "blocks/header.php" ?>

    <main>
        <a href="admin.php" class="back-link">← Вернуться в админ</a>
        <div class="form-container">
            <h1>Добавить новый выезд</h1>
            <?php if ($message): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="short_title" title="Короткое название тура, которое будет отображаться в списках">Короткое название одним словом</label>
                    <input type="text" id="short_title" name="short_title" placeholder='например, "Камчатка"' required title="Обязательно. Короткое название тура.">
                </div>

                <div class="form-group">
                    <label for="full_title" title="Полное название тура, если отличается от короткого">Полное название (красивое длинное название)</label>
                    <input type="text" id="full_title" name="full_title" placeholder='Например: "Незабываемое путишествие по просторам Камчатки"' required title="Опционально. Полное название тура.">
                </div>

                <div class="form-group">
                    <label for="full_description" title="Подробное описание тура, которое увидят пользователи">Полное описание (подробно о туре)</label>
                    <textarea id="full_description" name="full_description" rows="5" placeholder="Приглашаем тебя с нами в захватывающее путешествие..." required title="Обязательно. Подробное описание тура."></textarea>
                </div>

                <div class="form-group">
                    <label for="age" title="Возрастная категория участников, например 'от 10 до 99'">Возраст (в формате от и до)</label>
                    <input type="text" id="age" name="age" placeholder="от 10 до 99" required title="Обязательно. Возрастная категория, например 'от 10 до 99'.">
                </div>

                <div class="form-group">
                    <label for="price" title="Стоимость тура в рублях">Стоимость (в рублях)</label>
                    <input type="number" id="price" name="price" step="0.01" placeholder="80000" required title="Обязательно. Стоимость тура в рублях.">
                </div>

                <div class="form-group">
                    <label for="start_date" title="Дата начала тура">Дата начала</label>
                    <input type="date" id="start_date" name="start_date" required title="Обязательно. Дата начала тура.">
                </div>

                <div class="form-group">
                    <label for="end_date" title="Дата окончания тура">Дата окончания</label>
                    <input type="date" id="end_date" name="end_date" required title="Обязательно. Дата окончания тура.">
                </div>

                <div class="form-group">
                    <label for="instructor_name" title="Имя инструктора, который будет вести тур">Имя инструктора (кто основной ведущий)</label>
                    <input type="text" id="instructor_name" name="instructor_name" placeholder='Например:"Екатерина"' title="Имя инструктора.">
                </div>

                <div class="form-group">
                    <label for="difficulty" title="Уровень сложности тура">Сложность (выберите уровень)</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="легкий">Лёгкий</option>
                        <option value="средний">Средний</option>
                        <option value="сложный">Сложный</option>
                        <option value="эксперт">Эксперт</option>
                    </select>
                </div>

                <div class="form-group">
                    <label title="Загрузите 5 фотографий тура. Первая будет основной.">Фотографии (5 фото для карточки)</label>
                    <div class="photos">
                        <div class="photo-input">
                            <label for="photo_1">Фото 1 (основное)</label>
                            <input type="file" id="photo_1" name="photo_1" accept="image/*" required>
                        </div>
                        <div class="photo-input">
                            <label for="photo_2">Фото 2</label>
                            <input type="file" id="photo_2" name="photo_2" accept="image/*" required>
                        </div>
                        <div class="photo-input">
                            <label for="photo_3">Фото 3</label>
                            <input type="file" id="photo_3" name="photo_3" accept="image/*" required>
                        </div>
                        <div class="photo-input">
                            <label for="photo_4">Фото 4</label>
                            <input type="file" id="photo_4" name="photo_4" accept="image/*" required>
                        </div>
                        <div class="photo-input">
                            <label for="photo_5">Фото 5</label>
                            <input type="file" id="photo_5" name="photo_5" accept="image/*" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label title="Добавьте дни программы тура">Программа тура</label>
                    <div id="program-days">
                        <div class="program-day">
                            <label>День 1</label>
                            <input type="hidden" name="days[0][number]" value="1">
                            <input type="text" name="days[0][short_title]" placeholder="Краткое описание дня">
                            <textarea name="days[0][full_description]" placeholder="Полное описание дня" rows="3"></textarea>
                            <button type="button" class="remove-day" onclick="removeDay(this)">Удалить день</button>
                        </div>
                    </div>
                    <button type="button" class="add-day" onclick="addDay()">Добавить день</button>
                </div>

                <div class="form-group">
                    <label title="Добавьте включения в стоимость тура (до 5 штук)">Что входит в стоимость</label>
                    <div id="inclusions">
                        <div class="inclusion-item">
                            <input type="text" name="inclusions[0][title]" placeholder="Название включения" required>
                            <textarea name="inclusions[0][description]" placeholder="Описание" rows="2"></textarea>
                            <button type="button" class="remove-day" onclick="removeInclusion(this)">Удалить</button>
                        </div>
                    </div>
                    <button type="button" class="add-day" onclick="addInclusion()">Добавить включение</button>
                </div>

                <button type="submit" class="submit-btn">Добавить тур</button>
            </form>
        </div>
    </main>

    <?php include "blocks/footer.php" ?>

    <script>
        let dayCount = 1;
        let inclusionCount = 1;

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

function addInclusion() {
    if (inclusionCount >= 5) {
        alert('Нельзя добавить более 5 включений');
        return;
    }
    inclusionCount++;
    const container = document.getElementById('inclusions');
    const inclusionDiv = document.createElement('div');
    inclusionDiv.className = 'inclusion-item';
    inclusionDiv.innerHTML = `
        <input type="text" name="inclusions[${inclusionCount-1}][title]" placeholder="Название включения" required>
        <textarea name="inclusions[${inclusionCount-1}][description]" placeholder="Описание" rows="2"></textarea>
        <button type="button" class="remove-day" onclick="removeInclusion(this)">Удалить</button>
    `;
    container.appendChild(inclusionDiv);
}

        function removeInclusion(button) {
            button.parentElement.remove();
            // Пересчитать номера включений
            const inclusions = document.querySelectorAll('.inclusion-item');
            inclusions.forEach((inclusion, index) => {
                inclusion.querySelector('input[type="text"]').name = `inclusions[${index}][title]`;
                inclusion.querySelector('textarea').name = `inclusions[${index}][description]`;
            });
            inclusionCount = inclusions.length;
        }
    </script>
</body>
</html>