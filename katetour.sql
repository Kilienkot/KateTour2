-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Дек 09 2025 г., 04:13
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `katetour`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tours`
--

CREATE TABLE `tours` (
  `id` int NOT NULL,
  `short_title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Короткое название',
  `full_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Полное название (опционально)',
  `full_description` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Полное описание',
  `age` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Стоимость',
  `start_date` date NOT NULL COMMENT 'Дата начала',
  `end_date` date NOT NULL COMMENT 'Дата окончания',
  `instructor_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Имя инструктора (можно дублировать или оставить только ссылку)',
  `difficulty` enum('легкий','средний','сложный','эксперт') COLLATE utf8mb4_unicode_ci DEFAULT 'средний' COMMENT 'Сложность',
  `max_participants` int DEFAULT '20' COMMENT 'Максимальное количество участников',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Активный/неактивный тур',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tour_photos`
--

CREATE TABLE `tour_photos` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL COMMENT 'ID тура',
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Имя файла на диске',
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Оригинальное имя файла',
  `filepath` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Путь к файлу',
  `file_size` int DEFAULT NULL COMMENT 'Размер файла в байтах',
  `mime_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'image/jpeg' COMMENT 'MIME-тип',
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Подпись к фото',
  `is_primary` tinyint(1) DEFAULT '0' COMMENT 'Главная фотография (для превью)',
  `sort_order` int DEFAULT '0' COMMENT 'Порядок сортировки',
  `uploaded_by` int DEFAULT NULL COMMENT 'Кто загрузил (user_id)',
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tour_program`
--

CREATE TABLE `tour_program` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL COMMENT 'ID тура (связь с таблицей tours)',
  `day_number` tinyint UNSIGNED NOT NULL COMMENT 'Номер дня (1, 2, 3...)',
  `short_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Краткое описание (заголовок в свернутом виде)',
  `full_description` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Полное описание (раскрывается при клике)',
  `sort_order` smallint UNSIGNED DEFAULT '0' COMMENT 'Порядок сортировки (для перетаскивания)',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Активен ли пункт программы',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Имя пользователя',
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Логин (уникальный)',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Хэш пароля'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `login`, `password_hash`) VALUES
(1, 'Катя', 'Kate', 'b8b574e5abb14051299eab4152b1902a');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_start_date` (`start_date`),
  ADD KEY `idx_active_date` (`is_active`,`start_date`);

--
-- Индексы таблицы `tour_photos`
--
ALTER TABLE `tour_photos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_filename` (`filename`),
  ADD KEY `idx_tour_id` (`tour_id`),
  ADD KEY `idx_primary` (`tour_id`,`is_primary`),
  ADD KEY `idx_sort_order` (`tour_id`,`sort_order`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Индексы таблицы `tour_program`
--
ALTER TABLE `tour_program`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tour_day` (`tour_id`,`day_number`),
  ADD KEY `idx_tour_id` (`tour_id`),
  ADD KEY `idx_sort_order` (`tour_id`,`sort_order`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `idx_login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tour_photos`
--
ALTER TABLE `tour_photos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tour_program`
--
ALTER TABLE `tour_program`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `tour_photos`
--
ALTER TABLE `tour_photos`
  ADD CONSTRAINT `tour_photos_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_photos_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `tour_program`
--
ALTER TABLE `tour_program`
  ADD CONSTRAINT `tour_program_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
