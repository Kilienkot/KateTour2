<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles/css/login.css" />
    <title>Регистрация</title>
</head>

<body>

    <?php include("blocks/header.php") ?>

    <?php if (isset($_GET['error'])): ?>
      <div class="error-message">
        <?php
        $errors = [
            '1' => 'Пользователь с таким логином уже существует',
            '2' => 'Пароли не совпадают',
            '3' => 'Заполните все поля',
            '4' => 'Ошибка при регистрации'
        ];
        echo isset($errors[$_GET['error']]) ? $errors[$_GET['error']] : 'Ошибка регистрации';
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
      <div class="success-message">
        Регистрация прошла успешно! Теперь вы можете войти в систему.
      </div>
    <?php endif; ?>

    <main>
      <div class="form">
        <form method="post" action="lib/register.php">
          <h1>Создать аккаунт</h1>
          <div class="form-input">
            <label for="username">Имя</label>
            <input
              type="text"
              id="username"
              name="username"
              placeholder="Ваше имя"
              required
            />
          </div>
          <div class="form-input">
            <label for="login">Логин</label>
            <input
              type="text"
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
          <div class="form-input">
            <label for="confirm_password">Подтвердите пароль</label>
            <input
              type="password"
              name="confirm_password"
              id="confirm_password"
              placeholder="Повторите пароль"
              required
            />
          </div>
          <div class="links">
            <button type="submit">Зарегистрироваться</button>
          </div>
          <div class="form-footer">
            <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
          </div>
        </form>
      </div>
    </main>

    <?php include("blocks/footer.php") ?>
      
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

      // Проверяем наличие успешного сообщения
      const successMsg = document.querySelector('.success-message');
      if (successMsg) {
        console.log('Success message shown');
        // Показываем с анимацией
        setTimeout(() => {
          successMsg.classList.add('show');
        }, 100); // Небольшая задержка для применения transition
        // Скрываем плашку через 5 секунд
        setTimeout(() => {
          successMsg.classList.remove('show');
          // После анимации скрытия убираем display
          setTimeout(() => {
            successMsg.style.display = 'none';
          }, 500); // Время transition
        }, 3000);
      }
    </script>
</body>
</html>