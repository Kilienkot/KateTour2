<?php
include "lib/db.php";

// Получение ближайших 4 активных туров с информацией о цене и фото
$stmt = $pdo->prepare("SELECT t.id, t.short_title, t.full_title, t.start_date, t.end_date, t.price, tp.filepath FROM tours t LEFT JOIN tour_photos tp ON t.id = tp.tour_id AND tp.is_primary = 1 WHERE t.is_active = 1 ORDER BY start_date");
$stmt->execute();
$nearest_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $days = ($end - $start) / (60 * 60 * 24) + 1; // +1 потому что включая последний день
    return $days > 7 ? 'long-trip' : 'small-trip';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/css/calendar-style.css" />
    <title>Календарь</title>
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
            <div class="btns">
                <div id="savelong">
                    <h3>Оставить <span>длинные выезды</span></h3>
                </div>
                <div id="savesmall">
                    <h3>Оставить <span>короткие поездки</span></h3>
                </div>
            </div>
            <div class="calendar__grid">
                <?php foreach ($nearest_tours as $tour): ?>
                    <a href="trip-new.php?id=<?php echo $tour['id']; ?>" class="calendar__card <?php echo htmlspecialchars(getTripClass(formatDate($tour['start_date']), formatDate($tour['end_date']))); ?>">
<img src="<?php echo htmlspecialchars($tour['filepath'] ?: 'sources/img/tour_example.jpg'); ?>" alt="Тур" class="calendar__card-img">
                    <div class="calendar__divider"></div>
                    <h3 class="calendar__card-title"><?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?></h3>
                    <p class="calendar__card-dates"><?php echo formatDate($tour['start_date']); ?> - <?php echo formatDate($tour['end_date']); ?></p>
                    <p class="calendar__card-price"><?php echo number_format($tour['price'], 0, '', ' '); ?> ₽</p>
                    <p class="calendar__card-more">Нажми, чтоб узнать больше</p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <hr>
        <?php include("blocks/form.php") ?>
    </main>

    <?php include("blocks/footer.php") ?>

    <script>
        let activeFilter = null;

        function showAllTrips() {
            const trips = document.querySelectorAll('.calendar__card');
            trips.forEach(trip => {
                trip.style.display = 'flex';
            });
        }

        function hideFilterButtons() {
            document.getElementById('savelong').classList.remove('active');
            document.getElementById('savesmall').classList.remove('active');
        }

        document.getElementById('savelong').addEventListener('click', function() {
            if (activeFilter === 'long') {
                showAllTrips();
                hideFilterButtons();
                activeFilter = null;
                return;
            }
            hideFilterButtons();
            this.classList.add('active');
            activeFilter = 'long';
            const trips = document.querySelectorAll('.calendar__card');
            trips.forEach(trip => {
                if (!trip.classList.contains('long-trip')) {
                    trip.style.display = 'none';
                } else {
                    trip.style.display = 'flex';
                }
            });
        });

        document.getElementById('savesmall').addEventListener('click', function() {
            if (activeFilter === 'small') {
                showAllTrips();
                hideFilterButtons();
                activeFilter = null;
                return;
            }
            hideFilterButtons();
            this.classList.add('active');
            activeFilter = 'small';
            const trips = document.querySelectorAll('.calendar__card');
            trips.forEach(trip => {
                if (!trip.classList.contains('small-trip')) {
                    trip.style.display = 'none';
                } else {
                    trip.style.display = 'flex';
                }
            });
        });
    </script>
</body>
</html>
