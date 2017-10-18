-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Окт 18 2017 г., 10:37
-- Версия сервера: 10.1.21-MariaDB
-- Версия PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `guestbook`
--

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `author` varchar(255) NOT NULL,
  `authorIP` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `likes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `date`, `author`, `authorIP`, `content`, `likes`) VALUES
(1, '2017-10-13', 'admin', '137.172.15.35,::1,127.0.0.1', 'Первый отзыв', 3),
(2, '2017-10-13', 'admin', '137.172.15.35,127.0.0.1', 'Второй отзыв', 2),
(3, '2017-10-14', 'admin', '137.172.15.35,127.0.0.1', 'Третий отзыв', 1),
(4, '2017-10-14', 'Mike', ',::1,::1,127.0.0.1', 'Very, very good guestbook!', 3),
(5, '2017-10-14', 'Фёдор', '198.172.11.54,127.0.0.1', 'Не..., ну это нечто...', 1),
(6, '2017-10-15', 'Фёдор', '198.172.11.54', 'Не..., ну это нечто...seolink', 0),
(7, '2017-10-17', 'Валерий', '::1', 'Еще один отзыв', 0),
(8, '2017-10-17', 'Валерий', '::1,127.0.0.1', 'Еще один отзыв', 1),
(9, '2017-10-17', 'Mike', '127.0.0.1', 'Уже писал, еще напишу...', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
