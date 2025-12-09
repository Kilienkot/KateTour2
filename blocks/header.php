<header>
  <nav aria-label="Главное меню">
    <ul>
      <?php
        // Получаем полный путь к текущему скрипту
        $current_script = $_SERVER['SCRIPT_NAME'];

        // Сравниваем разными способами
        if ($current_script === '/index.php' || 
            $current_script === '/public/index.php' || 
            $current_script === '/') {
            echo '<li><a href="#calendar">Календарь</a></li>';
        } else {
            echo '<li><a href="index.php">Главная</a></li>';
        }
      ?>
      <li><a href="#footer">Контакты</a></li>
      <li class="header_accent">
        <a href="#entry">Записаться<br />в&nbsp;поездку</a>
      </li>
    </ul>
  </nav>
</header>
