<?php
include_once "../lib/db.php";

// Получение 6 случайных новостей из БД
$newsStmt = $pdo->prepare("SELECT id, title, content, image_path, created_at FROM news ORDER BY RAND() LIMIT 6");
$newsStmt->execute();
$news_items = $newsStmt->fetchAll(PDO::FETCH_ASSOC);

function truncateText($text, $limit = 100) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}
?>

<section class="news-section">
  <h2>Новости</h2>
  <div class="news__slider-container">
    <div class="swiper news-slider">
      <div class="swiper-wrapper news__grid">
        <?php foreach ($news_items as $news): ?>
        <div class="swiper-slide news-card">
          <?php if (!empty($news['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($news['image_path']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" class="news-image" />
          <?php else: ?>
            <div class="news-image" style="background-color: #eee; display: flex; justify-content: center; align-items: center;">Нет изображения</div>
          <?php endif; ?>
          <div class="news-content">
            <h3 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h3>
            <p class="news-excerpt"><?php echo htmlspecialchars(truncateText(strip_tags($news['content']), 120)); ?></p>
            <p class="news-date"><?php echo date('d.m.Y', strtotime($news['created_at'])); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <!-- Navigation buttons -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <!-- Pagination -->
      <div class="swiper-pagination"></div>
    </div>
  </div>
  <a href="javascript:void(0);" class="view-all-news">Посмотреть все новости</a>
</section>