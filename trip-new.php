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
    <!-- <link rel="stylesheet" href="styles/css/trip.css"> -->
    <link rel="stylesheet" href="styles/css/trip-new.css">
    <title><?php echo htmlspecialchars($tour['short_title']); ?></title>
</head>
<body>
    <?php include("blocks/header.php") ?>

    <main class="grid-container">
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
            <div class="block__info">
                <!-- Ширина 90% от родителя, размер h2 - 50rem, height - 600. Размер p - всё то же самое, но опасити 0.5 -->
                <div class="block__info-title">
                    <h2 class="name__tour"><?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?></h2>
                    <p class="date__tour">с <?php echo date('d.m.Y', strtotime($tour['start_date'])); ?> по <?php echo date('d.m.Y', strtotime($tour['end_date'])); ?></p>
                </div>
                <!-- между блоками more и instructor должна быть вертикальная линия, текст у надписи "сложность" и "инструктор" должен быть по левому краю, размером 30 рем и height 500. p должен быть в виде овальных блоков с текстом внутри. Все овальные блоки должны быть прозрачными с линией обводки. в сложности нужо понять какой в бд уровень сложности и закрасить этот блок. внутри p - 30 рем и  height 500-->
                <div class="block__info-more">
                    <div class="block__info-complexity">
                        <h5>Сложность</h5>
                        <div class="block__info-complexity-grade">
                            <p>Лёгкий</p>
                            <p>Средний</p>
                            <p>Сложный</p>
                            <p>Эксперт</p>
                        </div>
                    </div>
                    <div class="block__info-instructor">
                        <h5>Инструктор</h5>
                        <div class="block__info-instructor-name">
                            <p><?php echo htmlspecialchars($tour['instructor_name'] ?: 'Не указан'); ?></p>
                        </div>
                    </div>
                </div>
                <!-- размер текста h5 - 30 рем и height 500. p - 30 рем и  height 400 -->
                <div class="block__info-deskription">
                    <h5>Описание</h5>
                    <p>
                        <?php echo nl2br(htmlspecialchars($tour['full_description'])); ?>
                    </p>
                </div>
            </div>
            <!-- Блок с ценой должен быть чёрного цвета а текст должен быть цвета фона. Блок "записатся" должен быть в виде прямоугольника цвета фона с закруглянными углами с белым текстом внутри -->
             <div class="block__price-more">
                 <div class="block__price">
                     <p><?php echo number_format($tour['price'], 0, '.', ' '); ?>₽</p>
                     <a href="#entry">Записаться</a>
                 </div>
                 <!-- h5 - 30 рем и height 500. p в первом блоке - 20 рем и height 500. во втором p должен быть в виде овальных блоков с текстом внутри. Все овальные блоки должны быть прозрачными с линией обводки. внутри p - 30 рем и  height 500. последний блок с комментариями - 15рем, height 500., opacity 0.6-->
                 <div class="block__more">
                     <div class="block__more-included">
                         <h5>Что входит в стоимость</h5>
                         <p>
                             .- трансфер на вахтовке- безопасность на маршруте- обед, вода, горячий чай на маршруте- сопровождение гидом - проводником.- проживание в гостевом доме/хостеле.
                         </p>
                     </div>
                     <div class="block__more-take">
                         <h5>Что взять с собой?</h5>
                         <p>рюкзак</p>
                         <p>Трекинговая обувь</p>
                         <p>непромокаемая куртка/ветровка</p>
                         <p>Лекарства*</p>
                         <p>Документы</p>
                         <p>солнцезащитные очки, spf крем</p>
                         <p>легкая шапка, тонкие перчатки, кепка или панама</p>
                     </div>
                     <p class="block__more-deskription">
                         Это основа вашего комфорта и безопасности. Чтобы ничего не забыть, используйте наш детализированный чек-лист в Telegram-канале. Мы поможем вам подготовиться идеально
                     </p>
                 </div>
             </div>
        </section>
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