<?php
include "lib/db.php";

$tour_id = isset($_GET['id']) ? intval($_GET['id']) : 1; // По умолчанию первый тур

// Получение данных тура
$stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
$stmt->execute([$tour_id]);
$tour = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tour) {
    die("Тур не найден");
}

// Получение фото
$stmt = $pdo->prepare("SELECT * FROM tour_photos WHERE tour_id = ? ORDER BY sort_order");
$stmt->execute([$tour_id]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение программы
$stmt = $pdo->prepare("SELECT * FROM tour_program WHERE tour_id = ? ORDER BY day_number");
$stmt->execute([$tour_id]);
$program = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/css/trip.css">
    <title><?php echo htmlspecialchars($tour['short_title']); ?></title>
</head>
<body>
    <?php include("blocks/header.php") ?>

    <main>
        <section class="title">
            <h2 class="name__tour"><?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?></h2>
            <p class="date__tour">с <?php echo date('d.m.Y', strtotime($tour['start_date'])); ?> по <?php echo date('d.m.Y', strtotime($tour['end_date'])); ?></p>
        </section>
        <section class="photo-gallery">
            <?php if (count($photos) >= 1): ?>
                <div class="photo-large">
                    <img src="<?php echo htmlspecialchars($photos[0]['filepath']); ?>" alt="Основное фото" class="img-cover">
                    <div class="photo-caption">Главное фото</div>
                </div>
            <?php else: ?>
                <div class="photo-large">
                    <img src="sources/img/placeholder.jpg" alt="Фото отсутствует" class="img-cover">
                    <div class="photo-caption">Фото отсутствует</div>
                </div>
            <?php endif; ?>

            <?php for ($i = 1; $i < 5; $i++): ?>
                <?php if (isset($photos[$i])): ?>
                    <div class="photo-small">
                        <img src="<?php echo htmlspecialchars($photos[$i]['filepath']); ?>" alt="Фото <?php echo $i + 1; ?>" class="img-cover">
                    </div>
                <?php else: ?>
                    <div class="photo-small">
                        <img src="sources/img/placeholder.jpg" alt="Фото отсутствует" class="img-cover">
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </section>
        <section class="blocks">
            <div class="blocks__item">
                <p>Возраст</p>
                <p><?php echo htmlspecialchars($tour['age']); ?></p>
            </div>
            <div class="blocks__item">
                <p>Сложность</p>
                <p><?php echo htmlspecialchars(ucfirst($tour['difficulty'])); ?></p>
            </div>
            <div class="blocks__item">
                <p>Инструктор</p>
                <p><?php echo htmlspecialchars($tour['instructor_name'] ?: 'Не указан'); ?></p>
            </div>
            <div class="blocks__item">
                <p>Стоимость</p>
                <p><?php echo number_format($tour['price'], 0, '.', ' '); ?>₽</p>
            </div>
        </section>
        <p class="deskription">
            <?php echo nl2br(htmlspecialchars($tour['full_description'])); ?>
        </p>
        <hr>
        <section class="program">
            <h3>Программа</h3>
            <?php foreach ($program as $day): ?>
                <div class="program__item">
                    <button class="program__item-more">День <?php echo $day['day_number']; ?>. <?php echo htmlspecialchars($day['short_title']); ?></button>
                    <div class="program__item-more_panel">
                        <p><?php echo nl2br(htmlspecialchars($day['full_description'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
        <hr>
        <?php include("blocks/form.php") ?>
    </main>

    <?php include("blocks/footer.php") ?>

    <script src="main.js"></script>
</body>
</html>
