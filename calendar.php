<?php
include "lib/db.php";

// Получение активных туров
$stmt = $pdo->prepare("SELECT id, short_title, full_title, start_date, end_date FROM tours WHERE is_active = 1 ORDER BY start_date");
$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <div class="trips">
                <?php foreach ($tours as $tour): ?>
                    <a href="trip.php?id=<?php echo $tour['id']; ?>" class="trip__item <?php echo getTripClass($tour['start_date'], $tour['end_date']); ?>">
                        <p class="trip__item-date"><?php echo formatDate($tour['start_date']); ?> - <?php echo formatDate($tour['end_date']); ?></p>
                        <p class="trip__item-description">
                            <?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?>
                        </p>
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
            const trips = document.querySelectorAll('.trip__item');
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
            const trips = document.querySelectorAll('.trip__item');
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
            const trips = document.querySelectorAll('.trip__item');
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
