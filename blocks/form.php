<?php
include "lib/db.php";

// Получение активных туров для селекта
$stmt = $pdo->prepare("SELECT id, short_title FROM tours WHERE is_active = 1 ORDER BY short_title");
$stmt->execute();
$active_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="form" id="entry">
    <div class="form__text">
        <h2>записаться на&nbsp;<span>тур</span></h2>
        <p>
        Выберите поездку и оставьте свои контакты, а мы свяжемся с вами и
        все расскажем
        </p>
    </div>
    <form action="process_application.php" method="POST">
        <div class="form-group">
            <label for="user-name">Как к вам обращаться?</label> 
            <input
            type="text"
            id="user-name"
            name="name"
            placeholder="Введите ваше имя"
            pattern="[А-Яа-яЁёA-Za-z\s\-]+"
            title="Допускаются только буквы, пробелы и дефисы"
            required
            />
        </div>
        <div class="form-group">
            <label for="birth-date">Ваша дата рождения</label>
            <input type="date" id="birth-date" name="birthdate" required />
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                placeholder="Контактный телефон" 
                maxlength="18" 
                required>
        </div>
        <div class="form-group">
            <label for="tour-select">Какой тур понравился?</label>
            <select id="tour-select" name="tour" required>
                <?php foreach ($active_tours as $tour): ?>
                    <option value="<?php echo htmlspecialchars($tour['short_title']); ?>"><?php echo htmlspecialchars($tour['short_title']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Записаться</button>
    </form>
</section>
