<?php
    // ====================== ПОДКЛЮЧЕНИЕ К БД ======================
    try {
        $pdo = new PDO(
            'mysql:host=127.0.0.1:3306;dbname=katetour;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ]
        );
    } catch (PDOException $e) {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }

    // ====================== AUDIT LOG ======================
    // Защита от повторного объявления функции
    if (!function_exists('setAuditUser')) {
        function setAuditUser($pdo) {
            
            // Приоритет 1: Сессия
            if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
                $user_id  = (int)$_SESSION['user_id'];
                $username = $_SESSION['username'];
            }
            // Приоритет 2: Куки
            elseif (isset($_COOKIE['id']) && isset($_COOKIE['name'])) {
                $user_id  = (int)$_COOKIE['id'];
                $username = $_COOKIE['name'];
            }
            else {
                $user_id  = null;
                $username = 'anonymous';
            }

            try {
                $pdo->exec("SET @current_user_id = " . $user_id);
                $pdo->exec("SET @current_username = " . $pdo->quote($username));
            } catch (Exception $e) {
                error_log("Ошибка setAuditUser: " . $e->getMessage());
            }
        }
    }

    // ====================== УДОБНАЯ ФУНКЦИЯ ======================
    if (!function_exists('getDBConnection')) {
        function getDBConnection() {
            global $pdo;
            setAuditUser($pdo);
            return $pdo;
        }
    }
?>