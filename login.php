<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles/css/login.css" />
    <title>Авторизация</title>
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
    
    <?php include("blocks/header.php") ?>
    
    <main>
      <div class="form">
        <form method="post" action="lib/auth.php">
          <h1>Добро пожаловать!</h1>
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
          <div class="links">
            <button type="submit">Войти</button>
          </div>
          <div class="form-footer">
            <p>Ещё не знакомы? <a href="register.php">Создать аккаунт</a></p>
          </div>
        </form>
      </div>
    </main>

    <?php include("blocks/footer.php") ?>

    <script src="main.js"></script>
</body>
</html>