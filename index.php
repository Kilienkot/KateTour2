<?php
include "lib/db.php";

// Получение ближайших 5 активных туров
$stmt = $pdo->prepare("SELECT id, short_title, full_title, start_date, end_date FROM tours WHERE is_active = 1 AND start_date >= CURDATE() ORDER BY start_date LIMIT 5");
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
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KateTour</title>
    <link rel="stylesheet" href="styles/css/style.css" />
  </head>
  <body>
    <?php include "blocks/header.php" ?>

    <main>
      <section class="start">
        <div class="main_img">
          <div class="text-block">
            <h2>Семейные приключения по всей планете</h2>
            <h1>Катюша<span>ТУР</span></h1>
          </div>
        </div>
        <div class="collage">
          <img src="sources/img/collage1.png" alt="collage" class="block-1" />

          <div class="block-2">
            <img src="sources/img/collage2.png" alt="collage" />
            <div class="block_text">
              <h3>
                Вы&nbsp;можете&nbsp;увидеть&nbsp;это
                <span>своими&nbsp;глазами</span>
              </h3>
            </div>
          </div>

          <img src="sources/img/collage3.png" alt="collage" class="block-3" />

          <img src="sources/img/collage4.png" alt="collage" class="block-4" />

          <div class="block-5">
            <img src="sources/img/collage5.png" alt="collage" />
            <div class="block_text">
              <h3>Все фото — <span>эмоции</span> наших туристов</h3>
            </div>
          </div>

          <img src="sources/img/collage6.png" alt="collage" class="block6" />

          <div class="block-7">
            <div class="block_text">
              <h3>Самое&nbsp;время<br /><span>ехать</span>!</h3>
            </div>
            <img src="sources/img/collage7.png" alt="collage" />
          </div>

          <img src="sources/img/collage8.png" alt="collage" class="block-8" />

          <img src="sources/img/collage9.png" alt="collage" class="block-9" />
        </div>
      </section>
      <hr />
      <section class="why">
        <h2>зачем ехать в тур именно <span>с нами</span>?</h2>
        <div class="why__big-block">
          <img src="sources/img/bigblock.png" alt="Панорама" />
          <p>возможность увидеть скрытое другим</p>
        </div>
        <div class="why__mini-block">
          <img src="sources/img/miniblock1.png" alt="Семья" /><a href="#"
            >подробнее о&nbsp;<span>нашей&nbsp;команде</span></a
          >
        </div>
        <div class="why__mini-block">
          <img src="sources/img/miniblock2.png" alt="Дети" loading="lazy"><a href="https://o-len.ru" target="_blank"
            >сделано на основе <span>клуба&nbsp;о'лень</span></a
          >
        </div>
      </section>
      <hr />
      <section class="calendar" id="calendar">
        <h2>ближайшие <span>выезды</span></h2>
        <div class="calendar__main">
          <?php foreach ($nearest_tours as $tour): ?>
            <a href="trip.php?id=<?php echo $tour['id']; ?>" class="calendar__main-item">
              <p class="calendar__main-item_date"><?php echo formatDate($tour['start_date']); ?> - <?php echo formatDate($tour['end_date']); ?></p>
              <p class="calendar__main-item_description">
                <?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?>
              </p>
              <?php endforeach; ?>
            </a>
          <a href="calendar.php" class="calendar__main-more"
            >Посмотреть <span>все</span> выезды</a
          >
        </div>
      </section>
      <hr />

      <?php include("blocks/form.php") ?>

    </main>

    <?php include("blocks/footer.php")?>

    <script src="main.js"></script>
  </body>
</html>
