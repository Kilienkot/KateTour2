<?php
include "lib/db.php";

// Получение ближайших 4 активных туров с информацией о цене и фото
$stmt = $pdo->prepare("SELECT t.id, t.short_title, t.full_title, t.start_date, t.end_date, t.price, tp.filepath FROM tours t LEFT JOIN tour_photos tp ON t.id = tp.tour_id AND tp.is_primary = 1 WHERE t.is_active = 1 ORDER BY t.start_date LIMIT 4");
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
          <div class="button-block">
            <a href="calendar.php">Календарь Туров</a>
            <a href="#form">Записаться на Тур</a>
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
        <div class="why__item_grid">
          <div class="why__item">
            <div>
              <h3>УНИКАЛЬНЫЕ МАРШРУТЫ<br>И ЗАКРЫТЫЕ МЕСТА</h3>
              <p>С нами вы увидите то,<br>о чем другие не могут и мечтать</p>
            </div>
            <a href="https://vk.com/albums-49750096" target="_blank" rel="noopener noreferrer">Смотреть фото</a>
          </div>


          <div class="why__item">
            <div>
              <h3>ПУТЕШЕСТВИЕ БЕЗ ЗАБОТ <br>И ЛИШНЕЙ СУЕТЫ</h3>
              <p>Забудьте о стрессовых планировщиках<br>и сложных маршрутах</p>
            </div>
            <a href="#calendar" rel="noopener noreferrer">Ближайшие туры</a>
          </div>

          <div class="why__item">
            <div>
              <h3>Вдохните атмосферу<br>и почувствуйте ритм</h3>
              <p>Наши туры — это возможность прожить,<br>а не посетить</p>
            </div>
            <a href="javascript:void(0);" onclick="alert('Эта функция в разработке :-(');" rel="noopener noreferrer">Отзывы</a>
          </div>

          <div class="why__item">
            <div>
              <h3>НЕЗАБЫВАЕМЫЕ эмоции<br>и КОМФОРТ ДЛЯ ВСЕЙ СЕМЬИ</h3>
              <p>Логистика, безопасность<br>и активности — всё берём на себя</p>
            </div>
            <a href="javascript:void(0);" onclick="alert('Эта функция в разработке :-(');" rel="noopener noreferrer">О нас</a>
          </div>
        </div>
      </section>
      <hr />
      <section class="calendar" id="calendar">
        <h2>ближайшие&nbsp;<span>выезды</span></h2>
        <div class="calendar__grid">
          <?php foreach ($nearest_tours as $tour): ?>
            <a href="trip-new.php?id=<?php echo $tour['id']; ?>" class="calendar__card">
              <p class="calendar__card-dates"><?php echo formatDate($tour['start_date']); ?> - <?php echo formatDate($tour['end_date']); ?></p>
              <h3 class="calendar__card-title"><?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?></h3>
              <?php echo file_get_contents('sources/img/arrow.svg');?>
            </a>
          <?php endforeach; ?>
          <a href="calendar.php" class="calendar__more">Посмотреть <span>все</span> выезды</a>
        </div>
      </section>  
      <hr />
      <section class="tags">
        <h2>выбери свой <span>тур</span></h2>
        <div class="tags__buttons">
          <a href="calendar.php?tag=mountain-hikes" class="category-btn">
            Походы  в горы
          </a>
          <a href="calendar.php?tag=kayaking" class="category-btn">
            Сплавы на байдарках
          </a>
          <a href="calendar.php?tag=weekend-hikes" class="category-btn">
            Походы выходного дня
          </a>
          <a href="calendar.php?tag=climbing" class="category-btn">
            Восхождения
          </a>
          <a href="calendar.php?tag=ski-tours" class="category-btn">
            Лыжные сборы
          </a>
          <a href="calendar.php?tag=historical-hikes" class="category-btn">
            Исторические походы
          </a>
          <a href="calendar.php?tag=bike-tours" class="category-btn">
            Велопоходы
          </a>
          <a href="calendar.php?tag=wellness" class="category-btn">
            ЛЕЧЕБНО-ОЗДОРОВИТЕЛЬНЫЕ
          </a>
          <a href="calendar.php?tag=checkup" class="category-btn">
            Чек ап
          </a>
          <a href="calendar.php?tag=family-tours" class="category-btn">
            Семейные сборы
          </a>
          <a href="calendar.php?tag=weight-loss" class="category-btn">
            Похудение
          </a>
          <a href="calendar.php?tag=scientific-tours" class="category-btn">
            Научные походы
          </a>
          <a href="calendar.php?tag=no-tents" class="category-btn">
            Походы без палаток
          </a>
          <a href="calendar.php?tag=author-tours" class="category-btn">
            Походы авторские
          </a>
          <a href="calendar.php?tag=senior-hikes" class="category-btn">
            Походы 60+
          </a>
        </div>
      </section>
      <hr />
    
      <?php include("blocks/form.php") ?>

    </main>

    <?php include("blocks/footer.php")?>

    <script src="main.js"></script>
  </body>
</html>
