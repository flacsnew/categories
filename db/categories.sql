-- Adminer 4.8.1 MySQL 5.7.38 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `createdDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `categories` (`id`, `slug`, `name`, `description`, `createdDate`, `active`) VALUES
('1',	'first',	'Первая категория',	'Описание категории first',	'2022-07-18 10:55:40',	1),
('2',	'second',	'Мёд',	'Описание второй категории',	'2022-07-18 10:56:17',	0),
('3',	'three',	'Новая-_категория',	'Desc-_Описание',	'2022-07-20 16:26:28',	1),
('4',	'four',	'qwerty',	'is-awesome',	'2022-07-21 10:45:26',	1);

-- 2022-07-21 10:46:58
