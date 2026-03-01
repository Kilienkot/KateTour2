<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles/css/login.css" />
    <title>Вход</title>
  </head>

  <body>

    <?php if (isset($_GET['error'])): ?>
      <div class="error-message">
        Такого пользователя нет, или вы ошиблись в пароле
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['blocked'])): ?>
      <script>
        alert('Вы неправильно ввели пароль 3 раза. Попробуйте снова через минуту');
      </script>
    <?php endif; ?>

    <main>
      <div class="form">
        <form method="post" action="lib/auth.php">
          <h1>Добро пожаловать!</h1>
          <div class="form-input">
            <label for="login">Логин</label>
            <input
              type="login"
              id="login"
              name="login"
              placeholder="Ваш логин"
              required
            />
          </div>
          <div class="form-input">
            <label for="password">Пароль</label>
            <input
              type="password"
              name="password"
              id="password"
              placeholder="Введите пароль"
              required
            />
          </div>
          <div class="links">
            <button type="submit">Войти</button>
          </div>
        </form>
      </div>
    </main>
    
    <script src="main.js"></script>
    <script>
      // Проверяем наличие плашки
      const errorMsg = document.querySelector('.error-message');
      if (errorMsg) {
        console.log('Error message shown');
        // Показываем с анимацией
        setTimeout(() => {
          errorMsg.classList.add('show');
        }, 100); // Небольшая задержка для применения transition
        // Скрываем плашку через 5 секунд
        setTimeout(() => {
          errorMsg.classList.remove('show');
          // После анимации скрытия убираем display
          setTimeout(() => {
            errorMsg.style.display = 'none';
          }, 500); // Время transition
        }, 2000);
      }
    </script>
  </body>
</html>
