<?php
// process_application.php
session_start();
require_once "lib/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Получаем данные из формы
$name       = trim($_POST['name'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$birthdate  = $_POST['birthdate'] ?? null;
$tour_title = trim($_POST['tour'] ?? '');

// Убираем comment из публичной формы (как ты просил)
$comment = null;   // только админ сможет заполнять

// Валидация
if (empty($name) || empty($phone) || empty($birthdate) || empty($tour_title)) {
    $_SESSION['error'] = "Пожалуйста, заполните все обязательные поля!";
    header("Location: index.php#entry");
    exit();
}

// Находим id_tour
$stmt = $pdo->prepare("SELECT id FROM tours WHERE short_title = ? LIMIT 1");
$stmt->execute([$tour_title]);
$tour = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tour) {
    $_SESSION['error'] = "Выбранный тур не найден";
    header("Location: index.php#entry");
    exit();
}

// Определяем id_user
$id_user = null;
if (isset($_SESSION['user_id']) || isset($_COOKIE['id'])) {
    $id_user = $_SESSION['user_id'] ?? (int)$_COOKIE['id'];
}

$sql = "INSERT INTO applications 
        (id_tour, id_user, name, phone_number, date_of_birth, status, comment) 
        VALUES 
        (:id_tour, :id_user, :name, :phone_number, :date_of_birth, 'Новая', NULL)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id_tour'       => $tour['id'],
    ':id_user'       => $id_user,
    ':name'          => $name,
    ':phone_number'  => $phone,
    ':date_of_birth' => $birthdate
]);

$_SESSION['success'] = "Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.";
header("Location: index.php#entry");
exit();