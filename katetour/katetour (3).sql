-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 07 2026 г., 15:02
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
  `age` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Стоимость',
  `start_date` date NOT NULL COMMENT 'Дата начала',
  `end_date` date NOT NULL COMMENT 'Дата окончания',
  `instructor_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Имя инструктора (можно дублировать или оставить только ссылку)',
  `difficulty` enum('легкий','средний','сложный','эксперт') COLLATE utf8mb4_unicode_ci DEFAULT 'средний' COMMENT 'Сложность',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Активный/неактивный тур',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tours`
--

INSERT INTO `tours` (`id`, `short_title`, `full_title`, `full_description`, `age`, `price`, `start_date`, `end_date`, `instructor_name`, `difficulty`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Камчатка', 'Незабываемое путешествие по просторам Камчатки', 'Приглашаем тебя с нами в захватывающее путешествие на край земли - в удивительную Камчатку, землю, расположенную на самом краю России. Мы будем гулять по ледниковым фьордам и альпийским лугам, по влажным тропическим лесам и засушливым степям и пустыням, увидим огромные ледники и искрящиеся бирюзовые озера - пейзажи на этом знаменитом маршруте меняются каждый день с бешеной скоростью. Нас ждут маршруты в удивительных природных заповедниках: Торрес-Дель Пайне в Чили и Лос Гласьярес в Аргентине. Вы увидите экзотический Буэнос Айрес, знаменитый ледник Перито Морено, уютную горную деревушку Эль Чалтен и многое, многое другое. А кроме того, это еще и гастрономический тур. Практически каждый вечер, там, где будет такая возможность, мы будем посещать местные локальные заведения, где можно попробовать лучшие в мире аргентинские стейки, свежую рыбу из патагонских озер, отличное вино и многое другое.', 'от 10 до 99', '80000.00', '2025-09-12', '2025-09-21', 'Екатерина', 'легкий', 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(2, 'Новогодний тур', 'Новогодние приключения в горах', 'Отпразднуйте Новый год в уникальной атмосфере горных вершин. Мы организуем незабываемые празднования с фейерверками, снежными активностями и теплыми вечерами у камина. Идеально для семей и друзей, желающих встретить Новый год в волшебной обстановке.', 'от 5 до 70', '55000.00', '2025-12-28', '2026-01-05', 'Алексей', 'средний', 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(4, 'Экстремальный поход', 'Высокогорный треккинг в Альпах', 'Для опытных путешественников, готовых к настоящим вызовам. Профессиональные гиды, современное снаряжение, экстремальные маршруты по самым красивым местам Альп. Требуется хорошая физическая подготовка.', 'от 18 до 50', '120000.00', '2025-08-01', '2025-08-05', 'Дмитрий', 'эксперт', 1, '2025-12-09 02:00:24', '2025-12-09 02:32:00');

-- --------------------------------------------------------

--
-- Структура таблицы `tour_inclusions`
--

CREATE TABLE `tour_inclusions` (
  `id` int NOT NULL,
  `tour_id` int NOT NULL COMMENT 'ID тура (связь с таблицей tours)',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Название включения',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Описание включения',
  `sort_order` int DEFAULT '0' COMMENT 'Порядок сортировки',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Активен ли пункт',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tour_inclusions`
--

INSERT INTO `tour_inclusions` (`id`, `tour_id`, `title`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Трансфер на внедорожнике', 'Комфортабельный трансфер на специально оборудованном внедорожнике', 1, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(2, 1, 'Питание на маршруте', 'Полный пансион: завтраки, обеды, ужины, перекусы', 2, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(3, 1, 'Горячие напитки', 'Чай, кофе, какао в течение всего дня', 3, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(4, 1, 'Сопровождение гида', 'Профессиональный гид-инструктор с сертификатом', 4, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(5, 1, 'Проживание', 'Гостевые дома и хостелы с удобствами', 5, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(6, 2, 'Трансфер до места', 'Комфортабельный автобус с кондиционером', 1, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(7, 2, 'Новогодний ужин', 'Праздничный ужин с шампанским', 2, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(8, 2, 'Проживание в отеле', '4-х звездочный отель с завтраками', 3, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(9, 2, 'Снежные развлечения', 'Снегоходы, санки, лыжи', 4, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(10, 2, 'Новогодние подарки', 'Подарки от организаторов', 5, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(11, 4, 'Трансфер на альпийских маршрутах', 'Специальный трансфер для горных маршрутов', 1, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(12, 4, 'Профессиональное снаряжение', 'Всё необходимое снаряжение для альпинизма', 2, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(13, 4, 'Питание в горах', 'Специальное горное питание', 3, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(14, 4, 'Сопровождение инструкторов', 'Опытные альпинистские инструкторы', 4, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52'),
(15, 4, 'Проживание в горных домиках', 'Комфортабельные горные домики', 5, 1, '2026-03-07 10:14:52', '2026-03-07 10:14:52');

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

--
-- Дамп данных таблицы `tour_program`
--

INSERT INTO `tour_program` (`id`, `tour_id`, `day_number`, `short_title`, `full_description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Прилёт и заселение', 'Прилёт на Камчатку, организационная встреча участников, доезд до места проживания и отдых от длительного перелёта. Камчатка - это место, которым хочется насладиться, поэтому, если у вас получится приехать раньше, побалуйте себя и прогуляйтесь по набережной этого прекрасного города.', 1, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(2, 1, 2, 'Знакомство с Камчаткой', 'Экскурсия по Петропавловску-Камчатскому, посещение музеев и вулканологической станции. Начало активной программы.', 2, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(3, 1, 3, 'Восхождение на вулкан', 'Подъём на активный вулкан с профессиональными гидами. Безопасные маршруты с потрясающими видами.', 3, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(4, 1, 4, 'Поход к горячим источникам', 'Маршрут к термальным источникам в долине Гейзеров. Релакс и природные SPA-процедуры.', 4, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(5, 1, 5, 'Рыбалка и отдых', 'Рыбалка на реке с инструкторами, приготовление ухи на костре. Вечерние посиделки.', 5, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(6, 1, 6, 'Возвращение', 'Организованный трансфер в аэропорт, прощальные фотографии и отъезд домой.', 6, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(7, 2, 1, 'Прибытие и размещение', 'Встреча в аэропорту, трансфер в горный отель. Знакомство с группой и инструктаж.', 1, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(8, 2, 2, 'Снежные развлечения', 'Катание на санках, снегоходах, снежные игры и фотосессии.', 2, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(9, 2, 3, 'Новогодняя ночь', 'Празднование Нового года с фейерверками, банкетом и подарками.', 3, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(10, 2, 4, 'Горные прогулки', 'Лёгкие пешие прогулки по заснеженным тропам с гидом.', 4, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(11, 2, 5, 'Отдых и SPA', 'Релакс в отеле, сауна, массаж и подготовка к отъезду.', 5, 1, '2025-12-09 02:00:24', '2025-12-09 02:00:24'),
(19, 4, 1, '', '', 1, 1, '2026-03-02 02:16:07', '2026-03-02 02:16:07');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Имя пользователя',
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Логин (уникальный)',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Хэш пароля',
  `role` int NOT NULL COMMENT 'Уровни доступа\r\n1 - пользователь\r\n2 - админ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `login`, `password_hash`, `role`) VALUES
(1, 'Катя', 'Kate', '123', 1),
(2, 'Тим', 'tkilienko', '$2y$10$s6fJFzDQimtmu7E8Yy1mceu8MDreVBC5CvUdUhQR20QFij3pl5HoO', 2);

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
-- Индексы таблицы `tour_inclusions`
--
ALTER TABLE `tour_inclusions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tour_id` (`tour_id`),
  ADD KEY `idx_sort_order` (`tour_id`,`sort_order`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `tour_inclusions`
--
ALTER TABLE `tour_inclusions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `tour_photos`
--
ALTER TABLE `tour_photos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `tour_program`
--
ALTER TABLE `tour_program`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `tour_inclusions`
--
ALTER TABLE `tour_inclusions`
  ADD CONSTRAINT `tour_inclusions_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

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
