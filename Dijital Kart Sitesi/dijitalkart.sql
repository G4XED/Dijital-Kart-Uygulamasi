-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 06 Haz 2025, 21:20:11
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `dijitalkart`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `digital_cards`
--

CREATE TABLE `digital_cards` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `social_media` text DEFAULT NULL,
  `projects` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `digital_cards`
--

INSERT INTO `digital_cards` (`id`, `name`, `bio`, `avatar`, `email`, `phone`, `social_media`, `projects`, `skills`, `created_at`) VALUES
(20, 'afafa', 'fafadfadf', '3', 'arda1234@gmail.com', '21341431', '{\"instagram\":\"saf\",\"twitter\":\"saf\",\"linkedin\":\"saf\",\"github\":\"asf\"}', 'https://www.youtube.com/@gaxed', 'safasf', '2025-06-05 17:00:41'),
(21, 'afafa', 'fafadfadf', '3', 'arda1234@gmail.com', '21341431', '{\"instagram\":\"saf\",\"twitter\":\"saf\",\"linkedin\":\"saf\",\"github\":\"asfadgfagd\"}', 'https://www.youtube.com/@gaxed', 'safasf', '2025-06-05 17:02:35');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'GAXED', 'ardadmrspl@gmail.com', '$2y$10$WeqZ3zQl75t9X1hxISc9EO..5UFv/qQImiCCoMYbPBrfxoBbWrv6i');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `digital_cards`
--
ALTER TABLE `digital_cards`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `digital_cards`
--
ALTER TABLE `digital_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
