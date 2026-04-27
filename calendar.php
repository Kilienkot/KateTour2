<?php
include "lib/db.php";

// Получаем выбранный тег из GET-параметра
$selected_tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';

// Получаем ID тега, если он выбран
$tag_id = null;
$tag_name = null;

if (!empty($selected_tag)) {
    $stmt = $pdo->prepare("SELECT id, name FROM tags WHERE slug = ?");
    $stmt->execute([$selected_tag]);
    $tag = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tag) {
        $tag_id = $tag['id'];
        $tag_name = $tag['name'];
    }
}

// Формируем запрос с учётом фильтрации по тегу
if ($tag_id) {
    // Запрос с фильтром по тегу
    $stmt = $pdo->prepare("
        SELECT DISTINCT t.id, t.short_title, t.full_title, t.start_date, t.end_date, t.price, tp.filepath 
        FROM tours t 
        LEFT JOIN tour_photos tp ON t.id = tp.tour_id AND tp.is_primary = 1 
        INNER JOIN tour_tags tt ON t.id = tt.tour_id 
        WHERE t.is_active = 1 AND tt.tag_id = ? 
        ORDER BY t.start_date
    ");
    $stmt->execute([$tag_id]);
    $nearest_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Запрос без фильтра - все туры
    $stmt = $pdo->prepare("
        SELECT t.id, t.short_title, t.full_title, t.start_date, t.end_date, t.price, tp.filepath 
        FROM tours t 
        LEFT JOIN tour_photos tp ON t.id = tp.tour_id AND tp.is_primary = 1 
        WHERE t.is_active = 1 
        ORDER BY t.start_date
    ");
    $stmt->execute();
    $nearest_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для форматирования даты
function formatDate($date) {
    $months = [
        1 => 'янв', 2 => 'фев', 3 => 'мар', 4 => 'апр', 5 => 'май', 6 => 'июн',
        7 => 'июл', 8 => 'авг', 9 => 'сен', 10 => 'окт', 11 => 'ноя', 12 => 'дек'
    ];
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    return $day . ' ' . $month;
}

// Функция для определения класса
function getTripClass($start_date, $end_date) {
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    $days = ($end - $start) / (60 * 60 * 24) + 1;
    return $days > 7 ? 'long-trip' : 'small-trip';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/css/calendar-style.css" />
    <title>Календарь <?php echo $tag_name ? '- ' . htmlspecialchars($tag_name) : ''; ?></title>
</head>
<body>
    <?php include("blocks/header.php") ?>

    <main>
        <section class="startlife">
            <img src="sources/img/startlife.png" alt="Начни жить по полной!">
            <div class="startlife__text">
                <h2>Самое время начать <span>жить</span> по полной</h2>
            </div>
        </section>
        <section class="calendar">
            <!-- Блок с активным фильтром -->
            <?php if ($tag_id): ?>
                <div class="active-filter">
                    <span>Категория:<br><?php echo htmlspecialchars($tag_name); ?></span>
                    <a href="calendar.php" class="clear-filter">✕ Сбросить фильтр</a>
                </div>
            <?php endif; ?>

            <div class="btns">
                <div id="savelong">
                    <h3>длинные <span>выезды</span></h3>
                </div>
                <div id="savesmall">
                    <h3>короткие <span>поездки</span></h3>
                </div>
            </div>
            
            <!-- Сообщение, если туры не найдены -->
            <?php if (empty($nearest_tours)): ?>
                <div class="no-tours-message">
                    <p>В этой категории ещё нет туров<br>возвращайтесь позже!</p>
                    <a href="calendar.php" class="reset-all">Посмотреть все туры</a>
                </div>
            <?php else: ?>
                <div class="calendar__grid">
                <?php foreach ($nearest_tours as $tour): ?>
                    <a href="trip-new.php?id=<?php echo $tour['id']; ?>" class="calendar__card <?php echo htmlspecialchars(getTripClass($tour['start_date'], $tour['end_date'])); ?>">
                        <p class="calendar__card-dates"><?php echo formatDate($tour['start_date']); ?> - <?php echo formatDate($tour['end_date']); ?></p>
                        <h3 class="calendar__card-title"><?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?></h3>
                        <?php echo file_get_contents('sources/img/arrow.svg');?>
                    </a>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <hr>
        <?php include("blocks/form.php") ?>
    </main>

    <?php include("blocks/footer.php") ?>

    <script src="main.js"></script>
</body>
</html>
