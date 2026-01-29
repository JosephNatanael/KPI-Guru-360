-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Des 2025 pada 06.00
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
-- Database: `kpi_penilaian_guru_360`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `periode_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `penilai_id` bigint(20) UNSIGNED NOT NULL,
  `role_penilai` enum('kepala_sekolah','guru','wali_murid') NOT NULL,
  `average_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `evaluations`
--

INSERT INTO `evaluations` (`id`, `periode_id`, `guru_id`, `penilai_id`, `role_penilai`, `average_score`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'kepala_sekolah', 4.85, '2025-12-17 21:15:53', '2025-12-17 21:15:54'),
(2, 1, 2, 3, 'kepala_sekolah', 4.85, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(3, 1, 2, 4, 'guru', 4.69, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(4, 1, 1, 7, 'kepala_sekolah', 3.42, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(5, 1, 2, 7, 'kepala_sekolah', 3.19, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(6, 1, 3, 7, 'kepala_sekolah', 3.96, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(7, 1, 3, 9, 'wali_murid', 3.62, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(8, 1, 1, 6, 'guru', 3.58, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(9, 1, 3, 6, 'guru', 3.62, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(10, 1, 3, 3, 'kepala_sekolah', 3.27, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(11, 1, 1, 5, 'wali_murid', 4.88, '2025-12-26 08:27:33', '2025-12-26 08:27:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluation_details`
--

CREATE TABLE `evaluation_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evaluation_id` bigint(20) UNSIGNED NOT NULL,
  `kpi_indicator_id` bigint(20) UNSIGNED NOT NULL,
  `nilai` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `evaluation_details`
--

INSERT INTO `evaluation_details` (`id`, `evaluation_id`, `kpi_indicator_id`, `nilai`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(2, 1, 2, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(3, 1, 3, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(4, 1, 4, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(5, 1, 5, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(6, 1, 6, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(7, 1, 7, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(8, 1, 8, 4, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(9, 1, 9, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(10, 1, 10, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(11, 1, 11, 4, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(12, 1, 12, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(13, 1, 13, 5, '2025-12-17 21:15:54', '2025-12-17 21:15:54'),
(14, 2, 1, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(15, 2, 2, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(16, 2, 3, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(17, 2, 4, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(18, 2, 5, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(19, 2, 6, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(20, 2, 7, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(21, 2, 8, 4, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(22, 2, 9, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(23, 2, 10, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(24, 2, 11, 4, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(25, 2, 12, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(26, 2, 13, 5, '2025-12-17 21:25:48', '2025-12-17 21:25:48'),
(27, 3, 1, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(28, 3, 2, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(29, 3, 3, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(30, 3, 4, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(31, 3, 5, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(32, 3, 6, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(33, 3, 7, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(34, 3, 8, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(35, 3, 9, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(36, 3, 10, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(37, 3, 11, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(38, 3, 12, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(39, 3, 13, 5, '2025-12-17 21:30:46', '2025-12-17 21:30:46'),
(40, 4, 1, 5, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(41, 4, 2, 5, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(42, 4, 3, 4, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(43, 4, 4, 4, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(44, 4, 5, 4, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(45, 4, 6, 5, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(46, 4, 7, 3, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(47, 4, 8, 3, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(48, 4, 9, 4, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(49, 4, 10, 3, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(50, 4, 11, 2, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(51, 4, 12, 4, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(52, 4, 13, 4, '2025-12-22 01:35:43', '2025-12-22 01:35:43'),
(53, 5, 1, 4, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(54, 5, 2, 3, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(55, 5, 3, 2, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(56, 5, 4, 4, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(57, 5, 5, 4, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(58, 5, 6, 3, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(59, 5, 7, 3, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(60, 5, 8, 4, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(61, 5, 9, 3, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(62, 5, 10, 4, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(63, 5, 11, 4, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(64, 5, 12, 4, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(65, 5, 13, 3, '2025-12-22 01:36:10', '2025-12-22 01:36:10'),
(66, 6, 1, 4, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(67, 6, 2, 5, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(68, 6, 3, 5, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(69, 6, 4, 3, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(70, 6, 5, 3, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(71, 6, 6, 4, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(72, 6, 7, 4, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(73, 6, 8, 5, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(74, 6, 9, 4, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(75, 6, 10, 4, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(76, 6, 11, 4, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(77, 6, 12, 5, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(78, 6, 13, 5, '2025-12-22 01:36:40', '2025-12-22 01:36:40'),
(79, 7, 1, 5, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(80, 7, 2, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(81, 7, 3, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(82, 7, 4, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(83, 7, 5, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(84, 7, 6, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(85, 7, 7, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(86, 7, 8, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(87, 7, 9, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(88, 7, 10, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(89, 7, 11, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(90, 7, 12, 4, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(91, 7, 13, 3, '2025-12-22 01:40:35', '2025-12-22 01:40:35'),
(92, 8, 1, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(93, 8, 2, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(94, 8, 3, 5, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(95, 8, 4, 3, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(96, 8, 5, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(97, 8, 6, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(98, 8, 7, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(99, 8, 8, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(100, 8, 9, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(101, 8, 10, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(102, 8, 11, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(103, 8, 12, 4, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(104, 8, 13, 3, '2025-12-22 01:42:30', '2025-12-22 01:42:30'),
(105, 9, 1, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(106, 9, 2, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(107, 9, 3, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(108, 9, 4, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(109, 9, 5, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(110, 9, 6, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(111, 9, 7, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(112, 9, 8, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(113, 9, 9, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(114, 9, 10, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(115, 9, 11, 5, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(116, 9, 12, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(117, 9, 13, 4, '2025-12-22 01:42:55', '2025-12-22 01:42:55'),
(118, 10, 1, 5, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(119, 10, 2, 5, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(120, 10, 3, 4, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(121, 10, 4, 5, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(122, 10, 5, 5, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(123, 10, 6, 4, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(124, 10, 7, 5, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(125, 10, 8, 4, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(126, 10, 9, 2, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(127, 10, 10, 3, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(128, 10, 11, 2, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(129, 10, 12, 2, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(130, 10, 13, 2, '2025-12-25 06:59:00', '2025-12-25 06:59:00'),
(131, 11, 1, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(132, 11, 2, 4, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(133, 11, 3, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(134, 11, 4, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(135, 11, 5, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(136, 11, 6, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(137, 11, 7, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(138, 11, 8, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(139, 11, 9, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(140, 11, 10, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(141, 11, 11, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(142, 11, 12, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33'),
(143, 11, 13, 5, '2025-12-26 08:27:33', '2025-12-26 08:27:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `evaluator_weights`
--

CREATE TABLE `evaluator_weights` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `jenis_guru` enum('wali_kelas','non_wali_kelas') NOT NULL,
  `kepala_sekolah` int(11) NOT NULL,
  `rekan_guru` int(11) NOT NULL,
  `wali_murid` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `evaluator_weights`
--

INSERT INTO `evaluator_weights` (`id`, `jenis_guru`, `kepala_sekolah`, `rekan_guru`, `wali_murid`, `created_at`, `updated_at`) VALUES
(1, 'wali_kelas', 50, 30, 20, '2025-12-17 10:09:28', '2025-12-17 10:09:28'),
(2, 'non_wali_kelas', 70, 30, NULL, '2025-12-17 10:09:28', '2025-12-17 10:09:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `final_scores`
--

CREATE TABLE `final_scores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `periode_id` bigint(20) UNSIGNED NOT NULL,
  `nilai_kepala_sekolah` decimal(5,2) DEFAULT NULL,
  `nilai_rekan_guru` decimal(5,2) DEFAULT NULL,
  `nilai_wali_murid` decimal(5,2) DEFAULT NULL,
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `rekomendasi` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `final_scores`
--

INSERT INTO `final_scores` (`id`, `guru_id`, `periode_id`, `nilai_kepala_sekolah`, `nilai_rekan_guru`, `nilai_wali_murid`, `nilai_akhir`, `rekomendasi`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 43.45, 23.52, 19.68, 86.65, 'Kesempatan pelatihan lanjutan untuk pengembangan diri.', '2025-12-17 10:18:47', '2025-12-26 08:30:16'),
(2, 2, 1, 58.03, 30.00, 0.00, 88.03, 'Kesempatan pelatihan lanjutan untuk pengembangan diri.', '2025-12-17 10:18:47', '2025-12-30 21:46:35'),
(3, 3, 1, 39.75, 24.48, 16.00, 80.23, 'Kesempatan pelatihan lanjutan untuk pengembangan diri.', '2025-12-22 01:36:46', '2025-12-30 21:46:35'),
(4, 4, 1, 0.00, 0.00, 0.00, 0.00, 'Evaluasi, pembinaan intensif dan/atau surat peringatan dari sekolah', '2025-12-26 08:30:17', '2025-12-30 21:38:59'),
(5, 7, 1, 0.00, 0.00, 0.00, 0.00, 'Evaluasi, pembinaan intensif dan/atau surat peringatan dari sekolah', '2025-12-30 21:39:00', '2025-12-30 21:39:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gurus`
--

CREATE TABLE `gurus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(191) NOT NULL,
  `nip` varchar(191) DEFAULT NULL,
  `is_wali_kelas` tinyint(1) NOT NULL DEFAULT 0,
  `kelas` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gurus`
--

INSERT INTO `gurus` (`id`, `nama`, `nip`, `is_wali_kelas`, `kelas`, `created_at`, `updated_at`) VALUES
(1, 'guru 1', '231', 1, 'TK A', '2025-12-17 10:14:13', '2025-12-17 10:14:13'),
(2, 'guru 2', '23111', 0, 'TK B', '2025-12-17 10:14:30', '2025-12-17 10:14:30'),
(3, 'guru 3', '541', 1, '1 SD', '2025-12-22 01:34:36', '2025-12-22 01:34:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_indicators`
--

CREATE TABLE `kpi_indicators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(191) NOT NULL,
  `kompetensi` enum('pedagogik','kepribadian','sosial','profesional') NOT NULL,
  `bobot` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_indicators`
--

INSERT INTO `kpi_indicators` (`id`, `nama`, `kompetensi`, `bobot`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Perencanaan Pembelajaran', 'pedagogik', 8, 1, NULL, '2025-12-21 07:46:45'),
(2, 'Pelaksanaan Pembelajaran', 'pedagogik', 8, 1, NULL, NULL),
(3, 'Penilaian Pembelajaran', 'pedagogik', 8, 1, NULL, NULL),
(4, 'Kepribadian yang Mantap', 'kepribadian', 8, 1, NULL, NULL),
(5, 'Berakhlak Mulia', 'kepribadian', 6, 1, NULL, NULL),
(6, 'Kemandirian dalam Bertugas', 'kepribadian', 8, 1, NULL, '2025-12-17 10:15:57'),
(7, 'Keteladanan', 'kepribadian', 8, 1, NULL, '2025-12-17 10:16:03'),
(8, 'Kemampuan Komunikasi Guru', 'sosial', 8, 1, NULL, NULL),
(9, 'Hubungan Sosial dengan Rekan Guru', 'sosial', 7, 1, NULL, '2025-12-26 04:38:24'),
(10, 'Kemampuan Kerjasama', 'sosial', 7, 1, NULL, '2025-12-17 10:16:24'),
(11, 'Penguasaan Materi Pembelajaran', 'profesional', 8, 1, NULL, NULL),
(12, 'Pengembangan Profesionalitas Berkelanjutan', 'profesional', 8, 1, NULL, NULL),
(13, 'Pemanfaatan Teknologi Pembelajaran', 'profesional', 8, 1, NULL, NULL),
(14, 'test 1', 'pedagogik', 10, 0, '2025-12-26 04:36:41', '2025-12-26 04:36:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kpi_questions`
--

CREATE TABLE `kpi_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kpi_indicator_id` bigint(20) UNSIGNED NOT NULL,
  `pertanyaan` varchar(191) NOT NULL,
  `urutan` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kpi_questions`
--

INSERT INTO `kpi_questions` (`id`, `kpi_indicator_id`, `pertanyaan`, `urutan`, `created_at`, `updated_at`) VALUES
(4, 1, 'Guru menyusun RPP sesuai dengan kurikulum yang berlaku', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(5, 1, 'Guru menyiapkan materi pembelajaran sebelum proses belajar mengajar', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(6, 2, 'Guru menyampaikan materi pembelajaran dengan jelas dan sistematis', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(7, 2, 'Guru menggunakan metode pembelajaran yang sesuai dengan karakteristik siswa', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(8, 3, 'Guru melakukan penilaian hasil belajar secara objektif', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(9, 3, 'Guru memberikan umpan balik terhadap hasil belajar siswa', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(10, 4, 'Guru menunjukkan sikap percaya diri dalam menjalankan tugas', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(11, 4, 'Guru bertindak konsisten sesuai dengan norma dan aturan sekolah', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(12, 5, 'Guru bersikap jujur dalam setiap tindakan dan keputusan', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(13, 5, 'Guru menunjukkan sikap santun kepada siswa dan lingkungan sekolah', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(14, 6, 'Guru mampu melaksanakan tugas tanpa ketergantungan pada pihak lain', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(15, 6, 'Guru bertanggung jawab terhadap tugas yang diberikan', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(16, 7, 'Guru menjadi teladan dalam sikap dan perilaku sehari-hari', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(17, 7, 'Guru mematuhi peraturan sekolah dengan penuh kesadaran', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(18, 8, 'Guru berkomunikasi dengan jelas kepada siswa', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(19, 8, 'Guru mampu menyampaikan pendapat secara sopan dan efektif', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(20, 9, 'Guru menjalin hubungan kerja yang harmonis dengan rekan sejawat', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(21, 9, 'Guru menghargai pendapat rekan guru', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(22, 10, 'Guru mampu bekerja sama dalam tim sekolah', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(23, 10, 'Guru berperan aktif dalam kegiatan bersama di sekolah', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(24, 11, 'Guru menguasai materi pembelajaran sesuai bidangnya', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(25, 11, 'Guru mampu menjawab pertanyaan siswa dengan tepat', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(26, 12, 'Guru mengikuti kegiatan pengembangan profesional', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(27, 12, 'Guru menerapkan hasil pelatihan dalam pembelajaran', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(28, 13, 'Guru memanfaatkan teknologi dalam proses pembelajaran', 1, '2025-12-18 04:15:19', '2025-12-18 04:15:19'),
(29, 13, 'Guru menggunakan media digital untuk meningkatkan pemahaman siswa', 2, '2025-12-18 04:15:19', '2025-12-18 04:15:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_10_121654_create_gurus_table', 1),
(5, '2025_12_10_123540_create_wali_murids_table', 1),
(6, '2025_12_10_123927_create_periods_table', 1),
(7, '2025_12_10_124005_create_kpi_indicators_table', 1),
(8, '2025_12_10_124049_create_evaluator_weights_table', 1),
(9, '2025_12_10_124126_create_evaluations_table', 1),
(10, '2025_12_10_124151_create_evaluation_details_table', 1),
(11, '2025_12_10_124210_create_final_scores_table', 1),
(12, '2025_12_11_115420_fix_periods_table', 1),
(13, '2025_12_17_000000_create_recommendations_table', 1),
(14, '2025_12_17_000001_change_rekomendasi_column_in_final_scores', 1),
(15, '2025_12_17_154707_create_kpi_questions_table', 1),
(16, '2025_12_17_162030_change_evaluation_details_to_questions', 1),
(17, '2025_12_17_162044_change_evaluation_details_to_questions', 1),
(18, '2025_12_18_000000_add_is_active_to_kpi_indicators_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `periods`
--

CREATE TABLE `periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `semester` enum('ganjil','genap') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `periods`
--

INSERT INTO `periods` (`id`, `tahun_ajaran`, `semester`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`, `updated_at`) VALUES
(1, '2024/2025', 'ganjil', '2025-12-18', '2025-12-31', 'aktif', '2025-12-17 10:18:32', '2025-12-26 04:42:53'),
(2, '2025/2026', 'genap', '2025-12-25', '2025-12-31', 'nonaktif', '2025-12-25 07:00:19', '2025-12-26 04:42:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `recommendations`
--

CREATE TABLE `recommendations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(191) NOT NULL,
  `min_score` decimal(5,2) NOT NULL,
  `max_score` decimal(5,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `recommendations`
--

INSERT INTO `recommendations` (`id`, `nama`, `min_score`, `max_score`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Penetapan penghargaan, promosi jabatan.', 90.00, 100.00, '-', '2025-12-17 21:16:58', '2025-12-17 21:16:58'),
(2, 'Kesempatan pelatihan lanjutan untuk pengembangan diri.', 80.00, 89.00, '-', '2025-12-17 21:17:17', '2025-12-17 21:17:17'),
(3, 'Pembinaan, pendampingan, pelatihan tambahan.', 51.00, 79.00, '-', '2025-12-17 21:17:32', '2025-12-17 21:17:32'),
(4, 'Evaluasi, pembinaan intensif dan/atau surat peringatan dari sekolah', 0.00, 50.00, '-', '2025-12-17 21:17:46', '2025-12-17 21:17:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` enum('admin','kepala_sekolah','guru','wali_murid') NOT NULL DEFAULT 'guru',
  `guru_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `guru_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', '2025-12-17 10:09:28', '$2y$12$0D3S578CIiN7Z2rJZeNf1ubTmZmDiunR/kfj1VvH4krpTCLYKLfia', 'guru', NULL, '6OYKPe34Ql', '2025-12-17 10:09:28', '2025-12-17 10:09:28'),
(2, 'Administrator', 'admin@example.com', NULL, '$2y$12$g8n/VqjMiUvrPGeJu..Aj.rZA3pBeFeN1g3uEc1.mvfB4sjwv.pWO', 'admin', NULL, NULL, '2025-12-17 10:09:28', '2025-12-17 10:09:28'),
(3, 'kepsek 1', 'kepsek1@gmail.com', NULL, '$2y$12$Qf0XyQxROOGWQY5l7S3RIe6xoy2IbnAeMMeDSs2MDw0f2WFPsIzwO', 'kepala_sekolah', NULL, NULL, '2025-12-17 10:12:21', '2025-12-26 00:04:32'),
(4, 'guru 1', 'guru1@gmail.com', NULL, '$2y$12$usEyFEzFWv6gWgXgUmmoR.8ohlhzdrXnfaAh0aS.JT10lc50Y7W46', 'guru', 1, NULL, '2025-12-17 10:12:40', '2025-12-17 10:14:40'),
(5, 'wali 1', 'wali1@gmail.com', NULL, '$2y$12$2K4KnT9qwApcLWsypvm1s.ZMk25fNjTAPSeKtVntvQeDTmkzbYk8.', 'wali_murid', NULL, NULL, '2025-12-17 10:12:50', '2025-12-17 10:12:50'),
(6, 'guru 2', 'guru2@gmail.com', NULL, '$2y$12$7hssA80WGClRXflGTDQXZ.QQ.HkhUJFmJxUscN0j2dx2LQJqjHIqu', 'guru', 2, NULL, '2025-12-17 10:13:05', '2025-12-17 10:14:44'),
(7, 'Kepsek2', 'kepsek2@gmail.com', NULL, '$2y$12$NDruFKSlEQzNMmsCy1RUi.VpgBDQqubRFQDB4nMcDuXOGoa30ZlNe', 'kepala_sekolah', NULL, NULL, '2025-12-22 01:33:45', '2025-12-22 01:33:45'),
(8, 'guru 3', 'guru3@gmail.com', NULL, '$2y$12$5j6aX0APsXIOMmIYEZzDnequ.k1J2OL8WlrntfEwX7F7x9wzSZ0si', 'guru', 3, NULL, '2025-12-22 01:34:08', '2025-12-22 01:43:52'),
(9, 'Wali 2', 'wali2@gmail.com', NULL, '$2y$12$chSevNcDtdLGFhC2xUImTuoqxciMjtWbCxcr/ZhASSIemOX8VosSK', 'wali_murid', NULL, NULL, '2025-12-22 01:34:49', '2025-12-22 01:34:49'),
(10, 'guru 4', 'guru4@gmail.com', NULL, '$2y$12$yCJ74VfIqF5mMZBvag.FT.fQx0ctWzhIsTO1Y76S.IwfzvSmEIPeG', 'guru', 4, NULL, '2025-12-26 04:49:46', '2025-12-26 04:49:46'),
(12, 'coba guru', 'gurucoba@gmail.com', NULL, '$2y$12$s3efburoggR4Oc3EM83dGuTHC1aMTDUWT444n8Uj8cpoK.6iDz6uy', 'guru', NULL, NULL, '2025-12-30 21:29:51', '2025-12-30 21:29:51'),
(14, 'guru 5', '1233@gmail.com', NULL, '$2y$12$R8M0b/I1CcpcGmgndrWX.eTBCE6QljnHsxK0FO0alOczEZ16qnNwO', 'guru', 7, NULL, '2025-12-30 21:38:50', '2025-12-30 21:38:50'),
(15, 'guru 6', '4341@gmail.com', NULL, '$2y$12$zS8ySDSw.jVlyOQpzvlB3OA6ksKamNBYHV0COvVthK0hQGVCejgC.', 'guru', 8, NULL, '2025-12-30 21:41:13', '2025-12-30 21:41:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wali_murids`
--

CREATE TABLE `wali_murids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(191) NOT NULL,
  `nama_anak` varchar(191) NOT NULL,
  `kelas` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `wali_murids`
--

INSERT INTO `wali_murids` (`id`, `user_id`, `nama`, `nama_anak`, `kelas`, `created_at`, `updated_at`) VALUES
(1, 5, 'wali 1', 'wali 1', 'TK A', '2025-12-17 10:15:02', '2025-12-17 10:15:02'),
(2, 9, 'Wali 2', 'test 2', '1 SD', '2025-12-22 01:39:09', '2025-12-22 01:39:09');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `evaluation_details`
--
ALTER TABLE `evaluation_details`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `evaluator_weights`
--
ALTER TABLE `evaluator_weights`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `final_scores`
--
ALTER TABLE `final_scores`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gurus`
--
ALTER TABLE `gurus`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kpi_indicators`
--
ALTER TABLE `kpi_indicators`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kpi_questions`
--
ALTER TABLE `kpi_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kpi_questions_kpi_indicator_id_foreign` (`kpi_indicator_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `wali_murids`
--
ALTER TABLE `wali_murids`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `evaluation_details`
--
ALTER TABLE `evaluation_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT untuk tabel `evaluator_weights`
--
ALTER TABLE `evaluator_weights`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `final_scores`
--
ALTER TABLE `final_scores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `gurus`
--
ALTER TABLE `gurus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kpi_indicators`
--
ALTER TABLE `kpi_indicators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `kpi_questions`
--
ALTER TABLE `kpi_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `periods`
--
ALTER TABLE `periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `wali_murids`
--
ALTER TABLE `wali_murids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kpi_questions`
--
ALTER TABLE `kpi_questions`
  ADD CONSTRAINT `kpi_questions_kpi_indicator_id_foreign` FOREIGN KEY (`kpi_indicator_id`) REFERENCES `kpi_indicators` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
