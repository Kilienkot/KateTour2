<?php
include "lib/db.php";

// Получение ближайших 4 активных туров с информацией о цене
$stmt = $pdo->prepare("SELECT id, short_title, full_title, start_date, end_date, price FROM tours WHERE is_active = 1 ORDER BY start_date LIMIT 4");
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
        <div class="why__item_grid">
          <div class="why__item">
            <div>
              <h3>УНИКАЛЬНЫЕ МАРШРУТЫ<br>И ЗАКРЫТЫЕ МЕСТА</h3>
              <p>С нами вы увидите то, о чем другие<br>не могут и мечтать</p>
            </div>
            <img src="sources/img/why1.png" alt="Поле 1" />
          </div>
          <div class="why__item">
            <div>
              <h3>ПУТЕШЕСТВИЕ БЕЗ ЗАБОТ <br>И ЛИШНЕЙ СУЕТЫ</h3>
              <p>Забудьте о стрессовых планировщиках<br>и сложных маршрутах</p>
            </div>
            <img src="sources/img/why2.png" alt="Водопады 2" />
          </div>
          <div class="why__item">
            <div>
              <h3>Вдохните атмосферу<br>и почувствуйте ритм</h3>
              <p>Наши туры — это возможность прожить,<br>а не посетить</p>
            </div>
            <img src="sources/img/why3.png" alt="Люди 3" />
          </div>
          <div class="why__item">
            <div>
              <h3>НЕЗАБЫВАЕМЫЕ эмоции<br>и КОМФОРТ ДЛЯ ВСЕЙ СЕМЬИ</h3>
              <p>Логистика, безопасность и активности —<br>всё берём на себя</p>
            </div>
            <img src="sources/img/why4.png" alt="Семья 4" />
          </div>
        </div>
      </section>
      <hr />
      <section class="calendar" id="calendar">
        <h2>ближайшие <span>выезды</span></h2>
        <div class="calendar__grid">
          <?php foreach ($nearest_tours as $tour): ?>
            <a href="trip-new.php?id=<?php echo $tour['id']; ?>" class="calendar__card">
              <img src="sources/img/tour_example.jpg" alt="Тур" class="calendar__card-img">
              <div class="calendar__divider"></div>
              <h3 class="calendar__card-title"><?php echo htmlspecialchars($tour['full_title'] ?: $tour['short_title']); ?></h3>
              <p class="calendar__card-dates"><?php echo formatDate($tour['start_date']); ?> - <?php echo formatDate($tour['end_date']); ?></p>
              <p class="calendar__card-price"><?php echo number_format($tour['price'], 0, '', ' '); ?> ₽</p>
              <p class="calendar__card-more">Нажми, чтоб узнать больше</p>
            </a>
          <?php endforeach; ?>
        </div>
        <a href="/calendar.php" class="calendar__all">Смотреть&nbsp;<span> все туры <svg width="104" height="15" viewBox="0 0 104 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M103.707 8.07039C104.098 7.67986 104.098 7.0467 103.707 6.65617L97.3431 0.292213C96.9526 -0.0983109 96.3195 -0.0983109 95.9289 0.292213C95.5384 0.682738 95.5384 1.3159 95.9289 1.70643L101.586 7.36328L95.9289 13.0201C95.5384 13.4107 95.5384 14.0438 95.9289 14.4343C96.3195 14.8249 96.9526 14.8249 97.3431 14.4343L103.707 8.07039ZM0 7.36328V8.36328H103V7.36328V6.36328H0V7.36328Z" fill="#F2B705"/>
</svg>
</span></a>
      </section>  
      <hr />

      <?php include("blocks/form.php") ?>

    </main>

    <?php include("blocks/footer.php")?>

    <script src="main.js"></script>
  </body>
</html>
