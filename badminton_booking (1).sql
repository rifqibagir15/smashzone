-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Bulan Mei 2026 pada 16.48
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `badminton_booking`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `court_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'durasi dalam jam',
  `end_time` time NOT NULL,
  `total_price` int(11) NOT NULL,
  `booking_name` varchar(100) NOT NULL,
  `whatsapp_number` varchar(20) NOT NULL,
  `status` enum('pending','waiting_verification','confirmed','cancelled','expired') DEFAULT 'pending',
  `payment_deadline` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expired_at` datetime DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_uploaded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `court_id`, `booking_date`, `start_time`, `duration`, `end_time`, `total_price`, `booking_name`, `whatsapp_number`, `status`, `payment_deadline`, `created_at`, `updated_at`, `expired_at`, `payment_proof`, `payment_uploaded_at`) VALUES
(1, 2, 1, '2026-05-08', '13:00:00', 2, '15:00:00', 100000, 'ujang', '0812345678', 'confirmed', NULL, '2026-05-07 05:00:27', '2026-05-07 07:01:05', '2026-05-07 07:10:27', 'PAYMENT_1778135802_2932.jpg', NULL),
(2, 3, 1, '2026-05-08', '11:00:00', 2, '13:00:00', 100000, 'Jinja', '123456789', 'cancelled', NULL, '2026-05-07 07:47:02', '2026-05-10 14:39:42', '2026-05-07 09:57:02', 'PAYMENT_1778140046_5913.jpg', NULL),
(6, 2, 2, '2026-05-08', '08:00:00', 2, '10:00:00', 100000, 'zxcvbhjm', '23456789', 'confirmed', '2026-05-07 13:41:29', '2026-05-07 08:41:29', '2026-05-07 08:42:59', NULL, 'PAYMENT_1778143363_6336.png', NULL),
(7, 2, 3, '2026-05-11', '11:00:00', 1, '12:00:00', 50000, 'asdfgh', '123456789', 'confirmed', '2026-05-07 13:52:33', '2026-05-07 08:52:33', '2026-05-10 16:59:52', NULL, 'PAYMENT_1778143977_9196.png', NULL),
(8, 2, 3, '2026-05-11', '08:00:00', 3, '11:00:00', 150000, 'qwerty', '1298722231', 'confirmed', '2026-05-10 19:30:32', '2026-05-10 14:30:32', '2026-05-10 16:59:26', NULL, 'PAYMENT_1778423522_7533.jpg', NULL),
(9, 3, 3, '2026-05-11', '12:00:00', 1, '13:00:00', 50000, 'nuha', '176289323', 'confirmed', '2026-05-10 19:33:18', '2026-05-10 14:33:18', '2026-05-10 16:41:25', NULL, 'PAYMENT_1778423606_3991.jpg', NULL),
(10, 2, 2, '2026-05-12', '08:00:00', 2, '10:00:00', 100000, 'kima', '123456789', 'confirmed', '2026-05-11 21:48:40', '2026-05-11 16:48:40', '2026-05-11 17:34:10', NULL, 'PAYMENT_1778520185_9831.jpg', NULL),
(11, 2, 3, '2026-05-12', '08:00:00', 1, '09:00:00', 50000, 'qwerty', '123456789', 'confirmed', '2026-05-11 22:38:57', '2026-05-11 17:38:57', '2026-05-11 17:39:31', NULL, 'PAYMENT_1778521155_5778.jpg', NULL),
(12, 4, 2, '2026-05-12', '14:00:00', 3, '17:00:00', 150000, 'bagir', '087729359927', 'confirmed', '2026-05-12 10:51:35', '2026-05-12 05:51:35', '2026-05-12 06:01:55', NULL, 'PAYMENT_1778565125_8397.png', NULL),
(13, 4, 4, '2026-05-12', '10:00:00', 2, '12:00:00', 100000, 'rommy', '081234567890', 'confirmed', '2026-05-12 11:53:24', '2026-05-12 06:53:24', '2026-05-12 06:54:30', NULL, 'PAYMENT_1778568829_5729.png', NULL),
(14, 4, 4, '2026-05-12', '15:00:00', 2, '17:00:00', 100000, 'bagir', '08123456789', 'confirmed', '2026-05-12 12:09:12', '2026-05-12 07:09:12', '2026-05-12 07:10:14', NULL, 'PAYMENT_1778569766_4129.png', NULL),
(15, 4, 1, '2026-05-12', '11:00:00', 2, '13:00:00', 100000, 'ataya anti nangis', '12345678', 'confirmed', '2026-05-12 13:41:56', '2026-05-12 08:41:56', '2026-05-12 08:42:49', NULL, 'PAYMENT_1778575352_7174.png', NULL),
(16, 4, 1, '2026-05-12', '18:00:00', 3, '21:00:00', 150000, 'maulida', '087729359927', 'confirmed', '2026-05-12 19:12:49', '2026-05-12 14:12:49', '2026-05-12 14:15:16', NULL, 'PAYMENT_1778595201_1945.png', NULL),
(17, 4, 1, '2026-05-12', '08:00:00', 2, '10:00:00', 100000, 'bagir', '087729359927', 'confirmed', '2026-05-12 19:41:52', '2026-05-12 14:41:52', '2026-05-12 14:42:15', NULL, 'PAYMENT_1778596923_2473.png', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `courts`
--

CREATE TABLE `courts` (
  `id` int(11) NOT NULL,
  `court_name` varchar(100) NOT NULL,
  `price_per_hour` int(11) NOT NULL DEFAULT 50000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `courts`
--

INSERT INTO `courts` (`id`, `court_name`, `price_per_hour`, `created_at`) VALUES
(1, 'Lapangan 1', 50000, '2026-05-07 01:07:50'),
(2, 'Lapangan 2', 50000, '2026-05-07 01:07:50'),
(3, 'Lapangan 3', 50000, '2026-05-07 01:07:50'),
(4, 'Lapangan 4', 50000, '2026-05-07 01:07:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_status` enum('unpaid','waiting','approved','rejected') DEFAULT 'unpaid',
  `paid_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `payment_proof`, `payment_status`, `paid_at`, `verified_at`, `created_at`) VALUES
(1, 1, NULL, 'unpaid', NULL, NULL, '2026-05-07 05:00:27'),
(2, 2, NULL, 'unpaid', NULL, NULL, '2026-05-07 07:47:02'),
(3, 6, NULL, 'unpaid', NULL, NULL, '2026-05-07 08:41:29'),
(4, 7, NULL, 'unpaid', NULL, NULL, '2026-05-07 08:52:33'),
(5, 8, NULL, 'unpaid', NULL, NULL, '2026-05-10 14:30:32'),
(6, 9, NULL, 'unpaid', NULL, NULL, '2026-05-10 14:33:18'),
(7, 10, NULL, 'unpaid', NULL, NULL, '2026-05-11 16:48:40'),
(8, 11, NULL, 'unpaid', NULL, NULL, '2026-05-11 17:38:57'),
(9, 12, NULL, 'unpaid', NULL, NULL, '2026-05-12 05:51:35'),
(10, 13, NULL, 'unpaid', NULL, NULL, '2026-05-12 06:53:24'),
(11, 14, NULL, 'unpaid', NULL, NULL, '2026-05-12 07:09:12'),
(12, 15, NULL, 'unpaid', NULL, NULL, '2026-05-12 08:41:56'),
(13, 16, NULL, 'unpaid', NULL, NULL, '2026-05-12 14:12:49'),
(14, 17, NULL, 'unpaid', NULL, NULL, '2026-05-12 14:41:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(100) DEFAULT 'Booking Badminton',
  `whatsapp_admin` varchar(20) DEFAULT NULL,
  `qris_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `whatsapp_admin`, `qris_image`, `created_at`) VALUES
(1, 'Booking Badminton', '628123456789', 'qris.png', '2026-05-07 01:07:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$wH6KzJxWw8M4nA8QvKz1Gu2M4nN7QJ6L0JmTt0D6U9D2jv9lKz0rW', NULL, 'admin', '2026-05-07 01:07:50'),
(2, 'darel', 'darrelrabbani2@gmail.com', '$2y$10$fozlojXBDD1ad8bVjuI68en26qeRk.91yig4x8.3U1aMELcjI2Tl.', '1234567890', 'user', '2026-05-07 02:04:29'),
(3, 'Jinja', 'darreljikusa@gmail.com', '$2y$10$3di2lcVmiDiT3Q9w1atuku/X.x3PlAhvEgfhmqW5dgv/AHrc7Nb1.', '123456789', 'user', '2026-05-07 07:44:35'),
(4, 'rifqi bagir', 'rifqibagir15@gmail.com', '$2y$10$84Srw9.b3U1pYcEVuaVwqOfr6QzvDpWYCrPGu.PFltsh/ZBr6ybDq', '087729359927', 'user', '2026-05-12 05:49:57');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking` (`court_id`,`booking_date`,`start_time`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_booking_status` (`status`);

--
-- Indeks untuk tabel `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `courts`
--
ALTER TABLE `courts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
