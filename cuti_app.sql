-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 10:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cuti_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `cuti`
--

CREATE TABLE `cuti` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nip` int(20) NOT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `divisi` varchar(50) DEFAULT NULL,
  `no_hp` int(20) DEFAULT NULL,
  `pengganti` varchar(100) DEFAULT NULL,
  `jenis_cuti` varchar(50) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_akhir` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `dokumen` varchar(255) DEFAULT NULL,
  `csv` varchar(255) DEFAULT NULL,
  `status_pengajuan` enum('Menunggu','Disetujui','Ditolak','Selesai') DEFAULT 'Menunggu',
  `notified` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal_disetujui` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_selesai` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuti`
--

INSERT INTO `cuti` (`id`, `username`, `nip`, `jabatan`, `divisi`, `no_hp`, `pengganti`, `jenis_cuti`, `tanggal_mulai`, `tanggal_akhir`, `catatan`, `dokumen`, `csv`, `status_pengajuan`, `notified`, `tanggal_disetujui`, `tanggal_selesai`, `created_at`) VALUES
(52, 'ZEN.AZURA', 1807070003, 'Staff', 'Engginer', 2147483647, 'Akbar', 'Sick Leave', '2025-07-02', '2025-07-03', 'sakit', 'tampilannew.jpeg', NULL, 'Disetujui', 1, '2025-07-02 04:19:58', '2025-07-02 04:15:27', '2025-07-02 04:15:27'),
(53, 'ZEN.AZURA', 1807070003, 'Staff', 'Engginer', 2147483647, 'Akbar', 'Annual Leave', '2025-07-03', '2025-07-04', 'libur', 'tangan.png', NULL, 'Ditolak', 1, '2025-07-02 04:22:33', '2025-07-02 04:22:07', '2025-07-02 04:22:07');

-- --------------------------------------------------------

--
-- Table structure for table `cuti_limit`
--

CREATE TABLE `cuti_limit` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `jenis_cuti` varchar(100) NOT NULL,
  `jatah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuti_limit`
--

INSERT INTO `cuti_limit` (`id`, `username`, `jenis_cuti`, `jatah`) VALUES
(1, 'ZEN.AZURA', 'Leave', 15),
(3, 'MUHAMMAD.HERMAWAN', 'Leave', 15);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penerima_role` varchar(50) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('baru','dibaca') DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `judul`, `penerima_role`, `pesan`, `status`, `created_at`) VALUES
(14, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-25 sampai 2025-06-26.', 'dibaca', '2025-06-24 03:45:48'),
(15, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-28 sampai 2025-06-29.', 'dibaca', '2025-06-24 03:49:12'),
(16, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-04 sampai 2025-07-05.', 'dibaca', '2025-06-24 04:54:13'),
(17, 'Pengajuan Cuti Baru', 'admin', 'Karyawan MUHAMMAD.HERMAWAN mengajukan cuti dari 2025-06-26 sampai 2025-06-28.', 'dibaca', '2025-06-24 04:56:26'),
(18, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-24 sampai 2025-06-25.', 'dibaca', '2025-06-24 10:12:47'),
(19, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-25 sampai 2025-06-25.', 'dibaca', '2025-06-25 02:43:02'),
(20, 'Pengajuan Cuti Baru', 'admin', 'Karyawan MUHAMMAD.HERMAWAN mengajukan cuti dari 2025-06-26 sampai 2025-06-27.', 'dibaca', '2025-06-25 02:50:58'),
(21, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-27 sampai 2025-06-28.', 'dibaca', '2025-06-25 03:49:53'),
(22, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-26 sampai 2025-06-27.', 'dibaca', '2025-06-25 03:52:15'),
(23, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-25 03:53:18'),
(24, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-26 sampai 2025-06-27.', 'dibaca', '2025-06-25 04:24:49'),
(25, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-26 sampai 2025-06-27.', 'dibaca', '2025-06-25 04:44:22'),
(26, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-26 sampai 2025-06-27.', 'dibaca', '2025-06-25 07:21:20'),
(27, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-25 07:22:54'),
(28, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-26 sampai 2025-06-27.', 'dibaca', '2025-06-26 07:04:24'),
(29, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-26 09:58:23'),
(30, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-30 sampai 2025-07-01.', 'dibaca', '2025-06-30 06:36:26'),
(31, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 06:37:15'),
(32, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-03 sampai 2025-07-04.', 'dibaca', '2025-06-30 06:43:42'),
(33, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 06:44:09'),
(34, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-01 sampai 2025-07-02.', 'dibaca', '2025-06-30 06:51:46'),
(35, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 06:52:14'),
(36, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-03 sampai 2025-07-04.', 'dibaca', '2025-06-30 06:56:29'),
(37, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 06:57:02'),
(38, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-05 sampai 2025-07-17.', 'dibaca', '2025-06-30 06:58:42'),
(39, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 06:58:59'),
(40, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-30 sampai 2025-07-01.', 'dibaca', '2025-06-30 07:11:59'),
(41, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 07:12:28'),
(42, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-02 sampai 2025-07-14.', 'dibaca', '2025-06-30 07:13:06'),
(43, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 07:13:46'),
(44, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-30 sampai 2025-07-01.', 'dibaca', '2025-06-30 07:36:07'),
(45, '', 'user', 'Pengajuan cuti Anda telah ditolak oleh atasan.', 'dibaca', '2025-06-30 07:37:11'),
(46, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-06-30 sampai 2025-07-01.', 'dibaca', '2025-06-30 07:37:50'),
(47, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-06-30 07:38:11'),
(48, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-02 sampai 2025-07-03.', 'dibaca', '2025-07-01 05:38:13'),
(49, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-02 sampai 2025-07-03.', 'dibaca', '2025-07-01 06:45:22'),
(50, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-07-01 06:54:06'),
(51, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-02 sampai 2025-07-03.', 'dibaca', '2025-07-02 04:11:32'),
(52, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-02 sampai 2025-07-03.', 'dibaca', '2025-07-02 04:12:42'),
(53, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-02 sampai 2025-07-03.', 'dibaca', '2025-07-02 04:15:27'),
(54, '', 'user', 'Pengajuan cuti Anda telah disetujui oleh atasan.', 'dibaca', '2025-07-02 04:21:23'),
(55, 'Pengajuan Cuti Baru', 'admin', 'Karyawan ZEN.AZURA mengajukan cuti dari 2025-07-03 sampai 2025-07-04.', 'dibaca', '2025-07-02 04:22:07'),
(56, '', 'user', 'Pengajuan cuti Anda telah ditolak oleh atasan.', 'dibaca', '2025-07-02 04:22:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuti`
--
ALTER TABLE `cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cuti_limit`
--
ALTER TABLE `cuti_limit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuti`
--
ALTER TABLE `cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `cuti_limit`
--
ALTER TABLE `cuti_limit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
