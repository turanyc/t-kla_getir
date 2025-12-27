-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 07 Ara 2025, 23:58:47
-- Sunucu sürümü: 10.6.20-MariaDB
-- PHP Sürümü: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `u2495148_kuryecim`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `district` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `title`, `address`, `district`, `city`, `postal_code`, `latitude`, `longitude`, `phone`, `instructions`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 6, 'ev', 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-12-05 22:26:24', '2025-12-05 22:26:24');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_type_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 10.00,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `commission_rate` decimal(5,2) DEFAULT 15.00,
  `is_open` tinyint(1) DEFAULT 1,
  `is_approved` tinyint(1) DEFAULT 0,
  `opening_time` time DEFAULT '09:00:00',
  `closing_time` time DEFAULT '23:00:00',
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_payment_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `businesses`
--

INSERT INTO `businesses` (`id`, `user_id`, `vendor_type_id`, `name`, `slug`, `description`, `address`, `phone`, `email`, `logo`, `cover_image`, `latitude`, `longitude`, `delivery_fee`, `min_order`, `commission_rate`, `is_open`, `is_approved`, `opening_time`, `closing_time`, `rating`, `total_reviews`, `created_at`, `updated_at`, `last_payment_date`) VALUES
(1, 2, 1, 'Lezzet Ustası', 'lezzet-ustasi', 'Gerçek ev yemekleri, taze ve hızlı!', 'Atatürk Cd. No:42 Merkez/İstanbul', '0212 999 88 77', 'lezzet@ustasi.com', NULL, NULL, 40.99280000, 29.02600000, 15.00, 50.00, 15.00, 1, 1, '09:00:00', '22:00:00', 0.00, 0, '2025-12-04 00:06:35', '2025-12-07 14:54:56', NULL),
(2, 3, 3, 'Taze Manav', 'taze-manav', 'Günlük taze sebze & meyve, köy yollardan', 'İnönü Cd. No:18/A Bahçelievler/İstanbul', '0212 555 66 44', 'manav@taze.com', NULL, NULL, NULL, NULL, 12.00, 30.00, 15.00, 1, 1, '08:00:00', '20:00:00', 0.00, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51', NULL),
(3, 4, 4, 'Kuruyemiş Dünyası', 'kuruyemis-dunyasi', 'Taze kavrulmuş kuruyemişler, kuru meyveler', 'Bağdat Cd. No:120 Maltepe/İstanbul', '0216 777 88 99', 'kuruyemis@dunya.com', NULL, NULL, NULL, NULL, 10.00, 40.00, 15.00, 1, 1, '09:00:00', '21:00:00', 0.00, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51', NULL),
(4, 5, 2, 'Hızlı Market', 'hizli-market', 'Hızlı ve ekonomik market alışverişi', 'Menderes Cd. No:5 Ümraniye/İstanbul', '0216 333 44 55', 'market@hizli.com', NULL, NULL, NULL, NULL, 9.90, 75.00, 15.00, 1, 1, '08:00:00', '23:00:00', 0.00, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `business_fcm_tokens`
--

CREATE TABLE `business_fcm_tokens` (
  `user_id` int(11) NOT NULL,
  `fcm_token` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `business_payments`
--

CREATE TABLE `business_payments` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `vendor_type_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `business_id`, `vendor_type_id`, `name`, `slug`, `description`, `icon`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 1, 1, 'Ana Yemekler', 'ana-yemekler', NULL, NULL, 1, 1, '2025-12-04 00:06:35'),
(2, 1, 1, 'Çorbalar', 'corbalar', NULL, NULL, 2, 1, '2025-12-04 00:06:35'),
(3, 1, 1, 'İçecekler', 'icecekler', NULL, NULL, 3, 1, '2025-12-04 00:06:35'),
(4, 2, 3, 'Sebzeler', 'sebzeler', NULL, NULL, 1, 1, '2025-12-04 00:09:51'),
(5, 2, 3, 'Meyveler', 'meyveler', NULL, NULL, 2, 1, '2025-12-04 00:09:51'),
(6, 2, 3, 'Yeşillikler', 'yesillikler', NULL, NULL, 3, 1, '2025-12-04 00:09:51'),
(7, 3, 4, 'Kuruyemişler', 'kuruyemisler', NULL, NULL, 1, 1, '2025-12-04 00:09:51'),
(8, 3, 4, 'Kuru Meyveler', 'kuru-meyveler', NULL, NULL, 2, 1, '2025-12-04 00:09:51'),
(9, 3, 4, 'Çerezler', 'cerezler', NULL, NULL, 3, 1, '2025-12-04 00:09:51'),
(10, 4, 2, 'İçecekler', 'm-icecekler', NULL, NULL, 1, 1, '2025-12-04 00:09:51'),
(11, 4, 2, 'Atıştırmalık', 'atistirmalik', NULL, NULL, 2, 1, '2025-12-04 00:09:51'),
(12, 4, 2, 'Temel Gıda', 'temel-gida', NULL, NULL, 3, 1, '2025-12-04 00:09:51');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `plate_code` varchar(3) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `couriers`
--

CREATE TABLE `couriers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_type` enum('motosiklet','bisiklet','araba','scooter') DEFAULT 'motosiklet',
  `vehicle_plate` varchar(20) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `status` enum('active','passive') NOT NULL DEFAULT 'passive',
  `is_available` tinyint(1) DEFAULT 1,
  `current_order_id` int(11) DEFAULT NULL,
  `commission_rate` decimal(5,2) DEFAULT 10.00,
  `total_deliveries` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `balance` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `advance_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `location_updated_at` timestamp NULL DEFAULT NULL,
  `fcm_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `couriers`
--

INSERT INTO `couriers` (`id`, `user_id`, `vehicle_type`, `vehicle_plate`, `license_number`, `is_active`, `status`, `is_available`, `current_order_id`, `commission_rate`, `total_deliveries`, `rating`, `balance`, `created_at`, `updated_at`, `advance_balance`, `latitude`, `longitude`, `location_updated_at`, `fcm_token`) VALUES
(1, 7, 'motosiklet', '34ABC123', 'A123456', 1, 'active', 0, 23, 10.00, 0, 0.00, 0.00, '2025-12-04 21:10:04', '2025-12-07 14:28:10', 0.00, 37.00490240, 35.30752000, '2025-12-07 14:28:10', NULL),
(2, 8, 'motosiklet', '34DEF456', 'B654321', 1, 'passive', 1, NULL, 10.00, 0, 0.00, 0.00, '2025-12-04 21:10:04', '2025-12-06 19:21:54', 0.00, 37.59472640, 36.89021440, '2025-12-06 19:01:31', NULL),
(3, 9, 'bisiklet', NULL, NULL, 1, 'active', 1, 15, 10.00, 0, 0.00, 0.00, '2025-12-04 21:10:04', '2025-12-06 19:12:16', 0.00, 37.59472640, 36.89021440, '2025-12-06 19:12:16', NULL),
(4, 10, 'motosiklet', '34GHI789', 'C789012', 1, 'passive', 1, 16, 10.00, 0, 0.00, 0.00, '2025-12-04 21:10:04', '2025-12-06 19:12:52', 0.00, 37.59472640, 36.89021440, '2025-12-06 19:12:28', NULL),
(5, 11, 'scooter', NULL, NULL, 1, 'passive', 1, NULL, 10.00, 0, 0.00, 0.00, '2025-12-04 21:10:04', '2025-12-06 19:21:47', 0.00, 37.59472640, 36.89021440, '2025-12-06 19:07:08', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `courier_fcm_tokens`
--

CREATE TABLE `courier_fcm_tokens` (
  `user_id` int(11) NOT NULL,
  `fcm_token` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `courier_finances`
--

CREATE TABLE `courier_finances` (
  `id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `transaction_type` enum('earning','withdrawal','penalty','bonus','adjustment') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance_before` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'completed',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `courier_location`
--

CREATE TABLE `courier_location` (
  `id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `accuracy` decimal(10,2) DEFAULT NULL,
  `speed` decimal(10,2) DEFAULT NULL,
  `heading` decimal(5,2) DEFAULT NULL,
  `recorded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `courier_location`
--

INSERT INTO `courier_location` (`id`, `courier_id`, `latitude`, `longitude`, `accuracy`, `speed`, `heading`, `recorded_at`) VALUES
(1, 1, 41.00820000, 28.97840000, 10.00, 25.50, 180.00, '2025-12-04 21:10:22'),
(2, 2, 41.01500000, 28.98000000, 8.00, 30.00, 90.00, '2025-12-04 21:10:22'),
(3, 3, 41.02000000, 28.97000000, 12.00, 15.00, 270.00, '2025-12-04 21:10:22'),
(4, 4, 41.02500000, 28.98500000, 9.00, 20.00, 45.00, '2025-12-04 21:10:22'),
(5, 5, 41.03000000, 28.99000000, 11.00, 35.00, 315.00, '2025-12-04 21:10:22');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `courier_payment_confirm`
--

CREATE TABLE `courier_payment_confirm` (
  `id` int(11) NOT NULL,
  `courier_id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `status` enum('waiting','confirmed','rejected') DEFAULT 'waiting',
  `notes` text DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `confirmed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `stock_quantity` int(11) DEFAULT NULL,
  `unit` varchar(20) DEFAULT 'adet',
  `calories` int(11) DEFAULT NULL,
  `preparation_time` int(11) DEFAULT 15,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `menu_items`
--

INSERT INTO `menu_items` (`id`, `business_id`, `category_id`, `name`, `description`, `price`, `image`, `is_available`, `is_featured`, `discount_percent`, `stock_quantity`, `unit`, `calories`, `preparation_time`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Tavuk Sote', 'Izgara tavuk, sebzelerle sotelenmiş', 68.00, NULL, 1, 1, 0.00, NULL, 'porsiyon', NULL, 15, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(2, 1, 1, 'Köfte & Patates', 'Izgara köfte, kızarmış patates', 75.00, NULL, 1, 0, 0.00, NULL, 'porsiyon', NULL, 20, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(3, 1, 1, 'Etli Nohut', 'Dana etli, ev usulü nohut', 82.00, NULL, 1, 0, 0.00, NULL, 'porsiyon', NULL, 25, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(4, 1, 1, 'Sebzeli Pirinç Pilavı', 'Arpa şehriye, havuç, bezelye', 30.00, NULL, 1, 0, 0.00, NULL, 'porsiyon', NULL, 10, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(5, 1, 2, 'Mercimek Çorbası', 'Kırmızı mercimek, baharat', 28.00, NULL, 1, 1, 0.00, NULL, 'kase', NULL, 8, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(6, 1, 2, 'Tavuk Suyu Çorba', 'Tavuk suyu, şehriye, havuç', 32.00, NULL, 1, 0, 0.00, NULL, 'kase', NULL, 8, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(7, 1, 2, 'Ezogelin', 'Bulgur, mercimek, nane', 28.00, NULL, 1, 0, 0.00, NULL, 'kase', NULL, 8, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(8, 1, 3, 'Kola 33 cl', 'Soğuk kola', 18.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(9, 1, 3, 'Ayran 30 cl', 'Geleneksel yoğurt ayranı', 15.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(10, 1, 3, 'Su 0.5 L', 'Damacana su', 6.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(11, 1, 3, 'Şeftali Aromalı İce Tea', 'Soğuk ice-tea', 18.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(12, 1, 3, 'Soda 20 cl', 'Madensuyu', 12.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:06:35', '2025-12-04 00:06:35'),
(13, 2, 4, 'Domates', 'Yerli, kırmızı domates', 18.00, NULL, 1, 1, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(14, 2, 4, 'Salatalık', 'Dış mevsim salatalığı', 22.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(15, 2, 4, 'Patates', 'Yerli nişastalı', 15.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(16, 2, 5, 'Muz', 'Ekvador ithal', 35.00, NULL, 1, 1, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(17, 2, 5, 'Elma Gala', 'Gala elması', 28.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(18, 2, 5, 'Portakal', 'Valencia', 24.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(19, 2, 6, 'Maydanoz', 'Dal maydanoz', 12.00, NULL, 1, 0, 0.00, NULL, 'demet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(20, 2, 6, 'Roka', 'Taze roka', 10.00, NULL, 1, 0, 0.00, NULL, 'demet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(21, 3, 7, 'Tuzlu Yer Fıstığı', 'Kavrulmuş yer fıstığı', 65.00, NULL, 1, 1, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(22, 3, 7, 'Antep Fıstığı', 'Kavrulmuş Antep fıstığı', 180.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(23, 3, 7, 'Badem İç', 'Badem iç', 140.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(24, 3, 8, 'Kuru Kayısı', 'Türkiye kayısısı', 90.00, NULL, 1, 1, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(25, 3, 8, 'Kuru Üzüm', 'Sultani çekirdeksiz', 55.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(26, 3, 8, 'Kuru İncir', 'Aydın inciri', 110.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(27, 3, 9, 'Mısır Gevreği', 'Patlamış mısır', 35.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(28, 3, 9, 'Kabak Çekirdeği', 'Tuzlu kabak', 70.00, NULL, 1, 0, 0.00, NULL, 'kg', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(29, 4, 10, 'Coca-Cola 1 L', 'Pet şişe', 22.00, NULL, 1, 1, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(30, 4, 10, 'Su 5 L', 'Damacana', 7.50, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(31, 4, 10, 'Fuse Tea 33 cl', 'Şişe', 18.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(32, 4, 11, 'Lays 90 g', 'Patates cipsi', 32.00, NULL, 1, 1, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(33, 4, 11, 'Doritos 85 g', 'Mısır cipsi', 30.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(34, 4, 11, 'Çikolatalı Gofret', '35 g', 6.50, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(35, 4, 12, 'Süt 1 L', 'Pastörize', 18.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51'),
(36, 4, 12, 'Yumurta 10\'lu', 'Köy yumurtası', 32.00, NULL, 1, 0, 0.00, NULL, 'adet', NULL, 0, 0, '2025-12-04 00:09:51', '2025-12-04 00:09:51');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','danger','order','promo') DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `sender_id`, `title`, `message`, `type`, `link`, `is_read`, `created_at`) VALUES
(1, 0, NULL, 'YENİ SİPARİŞ!', 'Sipariş #1 - 50.00₺', 'order', NULL, 0, '2025-12-05 22:26:54'),
(2, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #1', 'order', NULL, 0, '2025-12-05 22:26:54'),
(3, 0, NULL, 'YENİ SİPARİŞ!', 'Sipariş #2 - 50.00₺', 'order', NULL, 0, '2025-12-05 22:47:01'),
(4, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #2', 'order', NULL, 0, '2025-12-05 22:47:01'),
(5, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #3 - 50.00₺', 'order', NULL, 0, '2025-12-05 22:53:28'),
(6, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #3', 'order', NULL, 0, '2025-12-05 22:53:28'),
(7, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #4 - 38.00₺', 'order', NULL, 0, '2025-12-05 23:06:32'),
(8, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #4', 'order', NULL, 0, '2025-12-05 23:06:32'),
(9, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #5 - 135.00₺', 'order', NULL, 0, '2025-12-06 00:15:51'),
(10, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #5', 'order', NULL, 0, '2025-12-06 00:15:51'),
(11, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #6 - 38.00₺', 'order', NULL, 0, '2025-12-06 01:50:18'),
(12, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #6', 'order', NULL, 0, '2025-12-06 01:50:18'),
(13, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 02:26:00'),
(14, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 02:26:04'),
(15, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 02:54:56'),
(16, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 02:54:58'),
(17, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 02:55:00'),
(18, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 02:55:02'),
(19, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 02:55:04'),
(20, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 02:55:05'),
(21, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #7 - 195.00₺', 'order', NULL, 0, '2025-12-06 13:22:58'),
(22, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #7', 'order', NULL, 0, '2025-12-06 13:22:58'),
(23, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 13:24:21'),
(24, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 13:24:35'),
(25, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #8 - 92.00₺', 'order', NULL, 0, '2025-12-06 13:30:07'),
(26, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #8', 'order', NULL, 0, '2025-12-06 13:30:07'),
(27, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #9 - 92.00₺', 'order', NULL, 0, '2025-12-06 13:33:42'),
(28, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #9', 'order', NULL, 0, '2025-12-06 13:33:42'),
(29, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 13:33:48'),
(30, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 13:33:49'),
(31, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 13:33:51'),
(32, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 13:33:53'),
(33, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #10 - 120.00₺', 'order', NULL, 0, '2025-12-06 13:59:50'),
(34, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #10', 'order', NULL, 0, '2025-12-06 13:59:50'),
(35, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 14:00:00'),
(36, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 14:00:02'),
(37, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #11 - 38.00₺', 'order', NULL, 0, '2025-12-06 14:54:48'),
(38, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #11', 'order', NULL, 0, '2025-12-06 14:54:48'),
(39, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 14:54:54'),
(40, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 14:54:59'),
(41, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #12 - 38.00₺', 'order', NULL, 0, '2025-12-06 15:15:12'),
(42, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #12', 'order', NULL, 0, '2025-12-06 15:15:12'),
(43, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 15:15:21'),
(44, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 15:15:26'),
(45, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #13 - 120.00₺', 'order', NULL, 0, '2025-12-06 18:34:53'),
(46, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #13', 'order', NULL, 0, '2025-12-06 18:34:53'),
(47, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 18:35:03'),
(48, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 18:35:05'),
(49, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #14 - 153.00₺', 'order', NULL, 0, '2025-12-06 19:01:00'),
(50, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #14', 'order', NULL, 0, '2025-12-06 19:01:00'),
(51, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 19:01:13'),
(52, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 19:01:17'),
(53, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #15 - 28.00₺', 'order', NULL, 0, '2025-12-06 19:04:39'),
(54, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #15', 'order', NULL, 0, '2025-12-06 19:04:39'),
(55, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 19:06:49'),
(56, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 19:06:54'),
(57, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:09:20'),
(58, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #16 - 182.00₺', 'order', NULL, 0, '2025-12-06 19:11:58'),
(59, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #16', 'order', NULL, 0, '2025-12-06 19:11:58'),
(60, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 19:12:07'),
(61, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 19:12:09'),
(62, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:12:30'),
(63, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:09'),
(64, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:10'),
(65, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:12'),
(66, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:14'),
(67, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:15'),
(68, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:17'),
(69, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:19'),
(70, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:21'),
(71, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:23'),
(72, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:25'),
(73, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:26'),
(74, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:23:29'),
(75, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #17 - 56.00₺', 'order', NULL, 0, '2025-12-06 19:40:15'),
(76, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #17', 'order', NULL, 0, '2025-12-06 19:40:15'),
(77, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 19:40:24'),
(78, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 19:40:26'),
(79, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 19:41:13'),
(80, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #18 - 43.00₺', 'order', NULL, 0, '2025-12-06 19:45:36'),
(81, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #18', 'order', NULL, 0, '2025-12-06 19:45:36'),
(82, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 19:45:47'),
(83, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 19:45:51'),
(84, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #19 - 107.00₺', 'order', NULL, 0, '2025-12-06 19:59:23'),
(85, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #19', 'order', NULL, 0, '2025-12-06 19:59:23'),
(86, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #20 - 118.00₺', 'order', NULL, 0, '2025-12-06 20:03:38'),
(87, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #20', 'order', NULL, 0, '2025-12-06 20:03:38'),
(88, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 20:04:47'),
(89, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 20:04:49'),
(90, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 20:04:52'),
(91, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 20:04:54'),
(92, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #21 - 103.00₺', 'order', NULL, 0, '2025-12-06 20:06:49'),
(93, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #21', 'order', NULL, 0, '2025-12-06 20:06:49'),
(94, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-06 20:06:57'),
(95, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-06 20:06:59'),
(96, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 20:08:20'),
(97, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 20:08:22'),
(98, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 20:08:25'),
(99, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-06 20:08:27'),
(100, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #22 - 107.00₺', 'order', NULL, 0, '2025-12-07 13:21:38'),
(101, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #22', 'order', NULL, 0, '2025-12-07 13:21:38'),
(102, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-07 13:21:47'),
(103, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-07 13:21:49'),
(104, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-07 13:22:58'),
(105, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #23 - 120.00₺', 'order', NULL, 0, '2025-12-07 14:12:56'),
(106, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #23', 'order', NULL, 0, '2025-12-07 14:12:56'),
(107, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-07 14:13:11'),
(108, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-07 14:18:54'),
(109, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #24 - 107.00₺', 'order', NULL, 0, '2025-12-07 14:19:14'),
(110, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #24', 'order', NULL, 0, '2025-12-07 14:19:14'),
(111, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-07 14:19:27'),
(112, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #25 - 107.00₺', 'order', NULL, 0, '2025-12-07 14:24:51'),
(113, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #25', 'order', NULL, 0, '2025-12-07 14:24:51'),
(114, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-07 14:26:02'),
(115, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-07 14:26:17'),
(116, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-07 14:26:20'),
(117, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-07 14:28:18'),
(118, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-07 14:28:20'),
(119, 6, NULL, 'Sipariş Güncellemesi', '✅ Sipariş teslim edildi', 'order', NULL, 0, '2025-12-07 14:28:22'),
(120, 2, NULL, 'YENİ SİPARİŞ!', 'Sipariş #26 - 210.00₺', 'order', NULL, 0, '2025-12-07 14:56:58'),
(121, 1, NULL, 'YENİ MÜŞTERİ SİPARİŞİ', 'Sipariş oluşturuldu #26', 'order', NULL, 0, '2025-12-07 14:56:58'),
(122, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz hazırlanıyor', 'order', NULL, 0, '2025-12-07 15:05:28'),
(123, 6, NULL, 'Sipariş Güncellemesi', '???? Siparişiniz yola çıktı', 'order', NULL, 0, '2025-12-07 15:05:34');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `courier_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `address` text NOT NULL,
  `delivery_address` text DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `status` enum('yeni','onaylandi','hazirlaniyor','yolda','teslim','iptal') DEFAULT 'yeni',
  `payment_method` enum('online','kapida_nakit','kapida_pos') DEFAULT 'kapida_nakit',
  `payment_status` enum('beklemede','odendi','iptal') DEFAULT 'beklemede',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL,
  `courier_commission` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `estimated_delivery_time` int(11) DEFAULT 30,
  `actual_delivery_time` int(11) DEFAULT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL,
  `prepared_at` timestamp NULL DEFAULT NULL,
  `picked_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `commission_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `business_id`, `restaurant_id`, `courier_id`, `address_id`, `address`, `delivery_address`, `customer_phone`, `customer_name`, `order_number`, `status`, `payment_method`, `payment_status`, `subtotal`, `delivery_fee`, `discount`, `total_price`, `promo_code`, `courier_commission`, `notes`, `cancel_reason`, `estimated_delivery_time`, `actual_delivery_time`, `rating`, `review`, `created_at`, `updated_at`, `accepted_at`, `prepared_at`, `picked_at`, `delivered_at`, `commission_amount`) VALUES
(1, 6, NULL, 1, NULL, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'yeni', 'kapida_nakit', 'beklemede', 40.00, 10.00, 0.00, 50.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-05 22:26:54', '2025-12-05 22:26:54', NULL, NULL, NULL, NULL, 0.00),
(2, 6, NULL, 1, NULL, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'yeni', 'kapida_nakit', 'beklemede', 40.00, 10.00, 0.00, 50.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-05 22:47:01', '2025-12-05 22:47:01', NULL, NULL, NULL, NULL, 0.00),
(3, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 40.00, 10.00, 0.00, 50.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-05 22:53:28', '2025-12-06 19:23:29', NULL, NULL, NULL, NULL, 0.00),
(4, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 28.00, 10.00, 0.00, 38.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-05 23:06:32', '2025-12-06 19:23:26', NULL, NULL, NULL, NULL, 0.00),
(5, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 125.00, 10.00, 0.00, 135.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 00:15:51', '2025-12-06 19:23:25', NULL, NULL, NULL, NULL, 0.00),
(6, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 28.00, 10.00, 0.00, 38.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 01:50:18', '2025-12-06 19:23:23', NULL, NULL, NULL, NULL, 0.00),
(7, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 185.00, 10.00, 0.00, 195.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 13:22:58', '2025-12-06 19:23:21', NULL, NULL, NULL, NULL, 0.00),
(8, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 82.00, 10.00, 0.00, 92.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 13:30:07', '2025-12-06 19:23:19', NULL, NULL, NULL, NULL, 0.00),
(9, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 82.00, 10.00, 0.00, 92.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 13:33:42', '2025-12-06 19:23:17', NULL, NULL, NULL, NULL, 0.00),
(10, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 110.00, 10.00, 0.00, 120.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 13:59:50', '2025-12-06 19:23:15', NULL, NULL, NULL, NULL, 0.00),
(11, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 28.00, 10.00, 0.00, 38.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 14:54:48', '2025-12-06 19:23:14', NULL, NULL, NULL, NULL, 0.00),
(12, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 28.00, 10.00, 0.00, 38.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 15:15:12', '2025-12-06 19:23:12', NULL, NULL, NULL, NULL, 0.00),
(13, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 110.00, 10.00, 0.00, 120.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 18:34:53', '2025-12-06 19:23:10', NULL, NULL, NULL, NULL, 0.00),
(14, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 143.00, 10.00, 0.00, 153.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 19:01:00', '2025-12-06 19:23:09', NULL, NULL, NULL, NULL, 0.00),
(15, 6, 1, NULL, 3, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 18.00, 10.00, 0.00, 28.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 19:04:39', '2025-12-06 19:09:20', NULL, NULL, NULL, NULL, 0.00),
(16, 6, 1, NULL, 4, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 172.00, 10.00, 0.00, 182.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 19:11:58', '2025-12-06 19:12:30', NULL, NULL, NULL, NULL, 0.00),
(17, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 46.00, 10.00, 0.00, 56.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 19:40:15', '2025-12-06 19:41:13', NULL, NULL, NULL, NULL, 0.00),
(18, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 33.00, 10.00, 0.00, 43.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 19:45:36', '2025-12-06 20:08:20', NULL, NULL, NULL, NULL, 0.00),
(19, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 97.00, 10.00, 0.00, 107.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 19:59:23', '2025-12-06 20:08:22', NULL, NULL, NULL, NULL, 0.00),
(20, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 108.00, 10.00, 0.00, 118.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 20:03:38', '2025-12-06 20:08:27', NULL, NULL, NULL, NULL, 0.00),
(21, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 93.00, 10.00, 0.00, 103.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-06 20:06:49', '2025-12-06 20:08:25', NULL, NULL, NULL, NULL, 0.00),
(22, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 97.00, 10.00, 0.00, 107.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-07 13:21:38', '2025-12-07 13:22:58', NULL, NULL, NULL, NULL, 0.00),
(23, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 110.00, 10.00, 0.00, 120.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-07 14:12:56', '2025-12-07 14:28:22', NULL, NULL, NULL, NULL, 0.00),
(24, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 97.00, 10.00, 0.00, 107.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-07 14:19:14', '2025-12-07 14:28:20', NULL, NULL, NULL, NULL, 0.00),
(25, 6, 1, NULL, 1, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'teslim', 'kapida_nakit', 'beklemede', 97.00, 10.00, 0.00, 107.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-07 14:24:51', '2025-12-07 14:28:18', NULL, NULL, NULL, NULL, 0.00),
(26, 6, 1, NULL, NULL, NULL, 'tavşentepe kozan adana', NULL, NULL, NULL, NULL, 'yolda', 'kapida_nakit', 'beklemede', 200.00, 10.00, 0.00, 210.00, NULL, 0.00, '', NULL, 30, NULL, NULL, NULL, '2025-12-07 14:56:58', '2025-12-07 15:05:34', NULL, NULL, NULL, NULL, 0.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `product_name`, `quantity`, `unit_price`, `total_price`, `notes`, `created_at`) VALUES
(1, 1, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-05 22:26:54'),
(2, 1, 12, 'Soda 20 cl', 1, 12.00, 12.00, NULL, '2025-12-05 22:26:54'),
(3, 2, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-05 22:47:01'),
(4, 2, 12, 'Soda 20 cl', 1, 12.00, 12.00, NULL, '2025-12-05 22:47:01'),
(5, 3, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-05 22:53:28'),
(6, 3, 12, 'Soda 20 cl', 1, 12.00, 12.00, NULL, '2025-12-05 22:53:28'),
(7, 4, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-05 23:06:32'),
(8, 5, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 00:15:51'),
(9, 5, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-06 00:15:51'),
(10, 5, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 00:15:51'),
(11, 6, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 01:50:18'),
(12, 7, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 13:22:58'),
(13, 7, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 13:22:58'),
(14, 7, 2, 'Köfte & Patates', 1, 75.00, 75.00, NULL, '2025-12-06 13:22:58'),
(15, 8, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 13:30:07'),
(16, 9, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 13:33:42'),
(17, 10, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 13:59:50'),
(18, 10, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 13:59:50'),
(19, 11, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 14:54:48'),
(20, 12, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 15:15:12'),
(21, 13, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 18:34:53'),
(22, 13, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 18:34:53'),
(23, 14, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-06 19:01:00'),
(24, 14, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 19:01:00'),
(25, 14, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-06 19:01:00'),
(26, 14, 8, 'Kola 33 cl', 1, 18.00, 18.00, NULL, '2025-12-06 19:01:00'),
(27, 15, 8, 'Kola 33 cl', 1, 18.00, 18.00, NULL, '2025-12-06 19:04:39'),
(28, 16, 2, 'Köfte & Patates', 1, 75.00, 75.00, NULL, '2025-12-06 19:11:58'),
(29, 16, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 19:11:58'),
(30, 16, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-06 19:11:58'),
(31, 17, 5, 'Mercimek Çorbası', 1, 28.00, 28.00, NULL, '2025-12-06 19:40:15'),
(32, 17, 8, 'Kola 33 cl', 1, 18.00, 18.00, NULL, '2025-12-06 19:40:15'),
(33, 18, 8, 'Kola 33 cl', 1, 18.00, 18.00, NULL, '2025-12-06 19:45:36'),
(34, 18, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-06 19:45:36'),
(35, 19, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-06 19:59:23'),
(36, 19, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-06 19:59:23'),
(37, 20, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-06 20:03:38'),
(38, 20, 2, 'Köfte & Patates', 1, 75.00, 75.00, NULL, '2025-12-06 20:03:38'),
(39, 20, 8, 'Kola 33 cl', 1, 18.00, 18.00, NULL, '2025-12-06 20:03:38'),
(40, 21, 2, 'Köfte & Patates', 1, 75.00, 75.00, NULL, '2025-12-06 20:06:49'),
(41, 21, 8, 'Kola 33 cl', 1, 18.00, 18.00, NULL, '2025-12-06 20:06:49'),
(42, 22, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-07 13:21:38'),
(43, 22, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-07 13:21:38'),
(44, 23, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-07 14:12:56'),
(45, 23, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-07 14:12:56'),
(46, 24, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-07 14:19:14'),
(47, 24, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-07 14:19:14'),
(48, 25, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-07 14:24:51'),
(49, 25, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-07 14:24:51'),
(50, 26, 3, 'Etli Nohut', 1, 82.00, 82.00, NULL, '2025-12-07 14:56:58'),
(51, 26, 7, 'Ezogelin', 1, 28.00, 28.00, NULL, '2025-12-07 14:56:58'),
(52, 26, 9, 'Ayran 30 cl', 1, 15.00, 15.00, NULL, '2025-12-07 14:56:58'),
(53, 26, 2, 'Köfte & Patates', 1, 75.00, 75.00, NULL, '2025-12-07 14:56:58');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percent','fixed') DEFAULT 'percent',
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `free_delivery` tinyint(1) DEFAULT 0,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_count` int(11) DEFAULT 0,
  `per_user_limit` int(11) DEFAULT 1,
  `valid_from` timestamp NULL DEFAULT current_timestamp(),
  `valid_until` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `applicable_to` enum('all','business','vendor_type') DEFAULT 'all',
  `business_id` int(11) DEFAULT NULL,
  `vendor_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `promotions`
--

INSERT INTO `promotions` (`id`, `code`, `description`, `discount_type`, `discount_percent`, `discount_amount`, `min_order_amount`, `max_discount`, `free_delivery`, `usage_limit`, `usage_count`, `per_user_limit`, `valid_from`, `valid_until`, `is_active`, `applicable_to`, `business_id`, `vendor_type_id`, `created_at`, `updated_at`) VALUES
(1, '2026TIKLAGELİR', NULL, 'percent', 0.00, 0.00, 0.00, NULL, 1, NULL, 0, 1, '2025-12-06 19:26:20', '2026-01-04 21:00:00', 1, 'all', NULL, NULL, '2025-12-06 19:26:20', '2025-12-06 19:26:20'),
(2, '2026TIKLAGELİR1', NULL, 'percent', 5.00, 0.00, 0.00, NULL, 1, NULL, 0, 1, '2025-12-06 19:28:45', '2026-01-04 21:00:00', 1, 'all', NULL, NULL, '2025-12-06 19:28:45', '2025-12-06 19:28:45'),
(3, '2026TIKLAGELİR2', NULL, 'percent', 15.00, 0.00, 0.00, NULL, 1, NULL, 0, 1, '2025-12-06 19:29:01', '2026-01-04 21:00:00', 1, 'all', NULL, NULL, '2025-12-06 19:29:01', '2025-12-06 19:29:01');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `guest_count` int(11) NOT NULL DEFAULT 2,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `table_number` varchar(20) DEFAULT NULL,
  `confirmation_code` varchar(50) DEFAULT NULL,
  `cancelled_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 10.00,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `commission_rate` decimal(5,2) DEFAULT 15.00,
  `is_open` tinyint(1) DEFAULT 1,
  `is_approved` tinyint(1) DEFAULT 0,
  `opening_time` time DEFAULT '09:00:00',
  `closing_time` time DEFAULT '23:00:00',
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `restaurant_payments`
--

CREATE TABLE `restaurant_payments` (
  `id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `total_orders` int(11) NOT NULL DEFAULT 0,
  `total_revenue` decimal(10,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `courier_id` int(11) DEFAULT NULL,
  `rating` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `is_public` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `category`, `is_public`, `updated_at`) VALUES
(1, 'site_name', 'Tıkla Gelir', 'text', 'Site adı', 'general', 0, '2025-12-03 23:08:51'),
(2, 'site_email', 'destek@tiklagelir.com.tr', 'text', 'Destek e-posta adresi', 'general', 0, '2025-12-03 23:08:51'),
(3, 'site_phone', '05441392254', 'text', 'Destek telefon', 'general', 0, '2025-12-03 23:08:51'),
(4, 'default_delivery_fee', '10.00', 'number', 'Varsayılan teslimat ücreti', 'delivery', 0, '2025-12-03 23:08:51'),
(5, 'default_commission_rate', '15.00', 'number', 'Varsayılan komisyon oranı (%)', 'payment', 0, '2025-12-03 23:08:51'),
(6, 'min_order_amount', '50.00', 'number', 'Minimum sipariş tutarı', 'order', 0, '2025-12-03 23:08:51'),
(7, 'currency', 'TRY', 'text', 'Para birimi', 'general', 0, '2025-12-03 23:08:51'),
(8, 'timezone', 'Europe/Istanbul', 'text', 'Saat dilimi', 'general', 0, '2025-12-03 23:08:51');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','business','admin','courier','restaurant') DEFAULT 'customer',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `city` varchar(50) DEFAULT NULL,
  `phone_verified_at` datetime DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `verification_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `is_active`, `created_at`, `updated_at`, `city`, `phone_verified_at`, `email_verified_at`, `verification_code`, `verification_expires_at`) VALUES
(1, 'Admin', 'admin@tiklagelir.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2025-12-03 23:08:51', '2025-12-03 23:08:51', NULL, NULL, NULL, NULL, NULL),
(2, 'Lezzet Ustası', 'lezzet@ustasi.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business', 1, '2025-12-04 00:06:35', '2025-12-04 00:06:35', NULL, NULL, NULL, NULL, NULL),
(3, 'Taze Manav', 'manav@taze.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business', 1, '2025-12-04 00:09:51', '2025-12-04 00:09:51', NULL, NULL, NULL, NULL, NULL),
(4, 'Kuruyemiş Dünyası', 'kuruyemis@dunya.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business', 1, '2025-12-04 00:09:51', '2025-12-04 00:09:51', NULL, NULL, NULL, NULL, NULL),
(5, 'Hızlı Market', 'market@hizli.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business', 1, '2025-12-04 00:09:51', '2025-12-04 00:09:51', NULL, NULL, NULL, NULL, NULL),
(6, 'Ali veli', 'ali@test.com', NULL, '$2y$10$m2yzspgjcd0GyNak01pml.R5M2xUvONjmWwyxDpVRW2U1kNz2EEqG', 'customer', 1, '2025-12-04 08:49:55', '2025-12-04 08:49:55', NULL, NULL, NULL, NULL, NULL),
(7, 'Ahmet Kurye', 'ahmet.kurye@site.com', '05441111111', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'courier', 1, '2025-12-04 22:03:38', '2025-12-04 22:03:38', NULL, NULL, NULL, NULL, NULL),
(8, 'Mehmet Kurye', 'mehmet.kurye@site.com', '05442222222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'courier', 1, '2025-12-04 22:03:38', '2025-12-04 22:03:38', NULL, NULL, NULL, NULL, NULL),
(9, 'Ayşe Kurye', 'ayse.kurye@site.com', '05443333333', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'courier', 1, '2025-12-04 22:03:38', '2025-12-04 22:03:38', NULL, NULL, NULL, NULL, NULL),
(10, 'Fatma Kurye', 'fatma.kurye@site.com', '05444444444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'courier', 1, '2025-12-04 22:03:38', '2025-12-04 22:03:38', NULL, NULL, NULL, NULL, NULL),
(11, 'Ali Kurye', 'ali.kurye@site.com', '05445555555', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'courier', 1, '2025-12-04 22:03:38', '2025-12-04 22:03:38', NULL, NULL, NULL, NULL, NULL),
(12, 'emre tırpan', 'tirpanemre906@gmail.com', NULL, '$2y$10$CRYP2hS9cKYyYVokIT6zZOtFbxubV6EAsvC3E1ice1nIwrWpJU3HK', 'customer', 1, '2025-12-04 23:24:08', '2025-12-04 23:24:08', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `vendor_types`
--

CREATE TABLE `vendor_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `vendor_types`
--

INSERT INTO `vendor_types` (`id`, `name`, `slug`, `description`, `icon`, `is_approved`, `created_at`) VALUES
(1, 'Restoran', 'restaurant', 'Yemek siparişi', 'fa-utensils', 1, '2025-12-03 23:08:51'),
(2, 'Market', 'market', 'Market alışverişi', 'fa-shopping-basket', 1, '2025-12-03 23:08:51'),
(3, 'Manav', 'grocery', 'Sebze meyve', 'fa-carrot', 1, '2025-12-03 23:08:51'),
(4, 'Kuruyemiş', 'dried-goods', 'Kuruyemiş ürünleri', 'fa-seedling', 1, '2025-12-03 23:08:51');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_default` (`is_default`);

--
-- Tablo için indeksler `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Tablo için indeksler `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Tablo için indeksler `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_vendor_type` (`vendor_type_id`),
  ADD KEY `idx_is_approved` (`is_approved`),
  ADD KEY `idx_is_open` (`is_open`),
  ADD KEY `idx_lat_lng` (`latitude`,`longitude`);

--
-- Tablo için indeksler `business_fcm_tokens`
--
ALTER TABLE `business_fcm_tokens`
  ADD PRIMARY KEY (`user_id`);

--
-- Tablo için indeksler `business_payments`
--
ALTER TABLE `business_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_paid_at` (`paid_at`);

--
-- Tablo için indeksler `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`menu_item_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `fk_cart_menu_item` (`menu_item_id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_vendor_type_id` (`vendor_type_id`);

--
-- Tablo için indeksler `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Tablo için indeksler `couriers`
--
ALTER TABLE `couriers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_id` (`user_id`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_is_available` (`is_available`),
  ADD KEY `idx_status_lat_lng` (`status`,`latitude`,`longitude`);

--
-- Tablo için indeksler `courier_fcm_tokens`
--
ALTER TABLE `courier_fcm_tokens`
  ADD PRIMARY KEY (`user_id`);

--
-- Tablo için indeksler `courier_finances`
--
ALTER TABLE `courier_finances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_courier_id` (`courier_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_transaction_type` (`transaction_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Tablo için indeksler `courier_location`
--
ALTER TABLE `courier_location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_courier_id` (`courier_id`),
  ADD KEY `idx_timestamp` (`recorded_at`),
  ADD KEY `idx_courier_latest` (`courier_id`,`recorded_at`);

--
-- Tablo için indeksler `courier_payment_confirm`
--
ALTER TABLE `courier_payment_confirm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_courier_id` (`courier_id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_restaurant_id` (`restaurant_id`),
  ADD KEY `idx_status` (`status`);

--
-- Tablo için indeksler `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_city_id` (`city_id`),
  ADD KEY `idx_name` (`name`);

--
-- Tablo için indeksler `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_is_available` (`is_available`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_restaurant_id` (`restaurant_id`),
  ADD KEY `idx_courier_id` (`courier_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Tablo için indeksler `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_menu_item_id` (`menu_item_id`);

--
-- Tablo için indeksler `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `unique_code` (`code`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_valid_until` (`valid_until`);

--
-- Tablo için indeksler `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_restaurant_id` (`restaurant_id`),
  ADD KEY `idx_reservation_date` (`reservation_date`),
  ADD KEY `idx_status` (`status`);

--
-- Tablo için indeksler `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_approved` (`is_approved`),
  ADD KEY `idx_is_open` (`is_open`);

--
-- Tablo için indeksler `restaurant_payments`
--
ALTER TABLE `restaurant_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_business_id` (`business_id`),
  ADD KEY `idx_restaurant_id` (`restaurant_id`),
  ADD KEY `idx_status` (`status`);

--
-- Tablo için indeksler `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD UNIQUE KEY `unique_setting_key` (`setting_key`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Tablo için indeksler `vendor_types`
--
ALTER TABLE `vendor_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `business_payments`
--
ALTER TABLE `business_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Tablo için AUTO_INCREMENT değeri `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `couriers`
--
ALTER TABLE `couriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `courier_finances`
--
ALTER TABLE `courier_finances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `courier_location`
--
ALTER TABLE `courier_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `courier_payment_confirm`
--
ALTER TABLE `courier_payment_confirm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Tablo için AUTO_INCREMENT değeri `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Tablo için AUTO_INCREMENT değeri `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `restaurant_payments`
--
ALTER TABLE `restaurant_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
