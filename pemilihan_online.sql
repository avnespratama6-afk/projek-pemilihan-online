-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 02:26 PM
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
-- Database: `pemilihan_online`
--

-- --------------------------------------------------------

--
-- Table structure for table `hasil_pemilihan`
--

CREATE TABLE `hasil_pemilihan` (
  `id_hasil` int(11) NOT NULL,
  `id_kandidat` int(11) NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `perolehan_suara_kandidat` int(11) NOT NULL,
  `total_suara` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hasil_pemilihan`
--

INSERT INTO `hasil_pemilihan` (`id_hasil`, `id_kandidat`, `tanggal_mulai`, `tanggal_selesai`, `perolehan_suara_kandidat`, `total_suara`) VALUES
(19, 67, '2025-12-30 17:08:00', '2025-12-31 17:08:00', 1, 1),
(20, 68, '2025-12-30 17:08:00', '2025-12-31 17:08:00', 0, 1),
(21, 69, '2025-12-30 17:08:00', '2025-12-31 17:08:00', 0, 1),
(22, 67, '2026-01-18 10:34:00', '2026-01-24 10:34:00', 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `kandidat`
--

CREATE TABLE `kandidat` (
  `id` int(11) NOT NULL,
  `no_urut` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `misi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `suara` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kandidat`
--

INSERT INTO `kandidat` (`id`, `no_urut`, `nama`, `visi`, `misi`, `foto`, `suara`) VALUES
(67, 2, 'Kandidat 2', 'Visi Kandidat 2', 'Misi Kandidat 2', '694fdfedca24c.jpg', 1),
(68, 1, 'Kandidat 1', '-Visi Kandidat 1\r\n-Visi ANSAN\r\n-IVIS\r\n', 'Misi Kandidat 1', '694fdfcfea588.jpg', 0),
(69, 3, 'Kandidat 3', 'Visi Kandidat 3', 'Misi Kandidat 3', '694fe00757434.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `periode_pemilu`
--

CREATE TABLE `periode_pemilu` (
  `id` int(11) NOT NULL,
  `nama_periode` varchar(100) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'nonaktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `periode_pemilu`
--

INSERT INTO `periode_pemilu` (`id`, `nama_periode`, `start_datetime`, `end_datetime`, `status`) VALUES
(12, '11', '2026-01-15 19:20:00', '2026-01-16 19:20:00', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `sudah_memilih` tinyint(1) NOT NULL DEFAULT 0,
  `kode_unik` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nik`, `nama`, `email`, `password`, `role`, `sudah_memilih`, `kode_unik`) VALUES
(35, '1111111111', 'Wang Lin', 'wanglin@gmail.com', '$2y$10$9gToWqIYg0YZBYrq/yKBDeOTH0u1z.lsEVSoGW28NmexU..B3wEJa', 'admin', 0, NULL),
(41, '3333333333', 'Avnes Pratama', 'avnespratama6@gmail.com', '$2y$10$klcv3.twwVwJYXEtO06r2ek7kcJAagxp2YHUHorP2MM5HovkkI5Ju', 'pemilih', 0, 'VKLNPQ'),
(42, '4444444444', 'Chen nan', 'Chen@gmail.com', '$2y$10$HC6VRmmN/meuESKGaKHky.ssTVhkHByfXOj4BIKrC1f9wIehrj3Uy', 'pemilih', 0, 'TDWLWK'),
(43, '2222222222', 'Xiao Yan', 'xiaoyan@gmail.com', '$2y$10$wTYr2K9yfJLrc9m5KbYBPu7zUwQp03UppzoqJx0sU39LBe69ZMxJ.', 'pemilih', 0, '1761AX'),
(44, '666666666', 'Tamaa', 'xiaoyan2@gmail.com', '$2y$10$5esXV4ZvvWuV1Dz5h2hpuOBOiKkM0a9XeBZbcA7Y7gRMIHpsu28e6', 'pemilih', 0, 'N6N9GA'),
(46, '88888888888', 'Tama Saja', 'tamasaja@gmail.com', 'tamatest123', 'pemilih', 0, 'DNLA92'),
(51, '999999999999', 'Jamal', 'jamal1@gmail.com', 'jamaltest2', 'admin', 0, 'AGX4RG'),
(52, '99999999999', 'Adam Sidiq', 'adam@gmail.com', '$2y$10$hQdNvfhAB9TXKA6cJddxLuUVTqLd61yVA8Pbbd82EMBBu/EGjuvm6', 'pemilih', 0, 'XSQ0YV'),
(53, '100000000', 'Ahmad Syah', 'ahmad1@gmail.com', '$2y$10$ROieH0kXvi74cbfw/wkOl.SHUO0cUAo2kADwZ305P0i.mFlhKlonu', 'pemilih', 0, 'SG567A'),
(54, '101010101010', 'Aji Kelas', 'aji@gmail.com', '$2y$10$FUL3LtkQ937wWCcTRzghce.M7FxnzjZy/hZLPvLUTRYSbYv6xQylm', 'pemilih', 1, 'V2I5J0');

-- --------------------------------------------------------

--
-- Table structure for table `voting`
--

CREATE TABLE `voting` (
  `id` int(11) NOT NULL,
  `id_pemilih` int(11) NOT NULL,
  `id_kandidat` int(11) NOT NULL,
  `waktu_vote` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voting`
--

INSERT INTO `voting` (`id`, `id_pemilih`, `id_kandidat`, `waktu_vote`) VALUES
(56, 54, 67, '2026-01-15 19:20:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hasil_pemilihan`
--
ALTER TABLE `hasil_pemilihan`
  ADD PRIMARY KEY (`id_hasil`),
  ADD KEY `id_kandidat` (`id_kandidat`);

--
-- Indexes for table `kandidat`
--
ALTER TABLE `kandidat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `periode_pemilu`
--
ALTER TABLE `periode_pemilu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `kode_unik` (`kode_unik`);

--
-- Indexes for table `voting`
--
ALTER TABLE `voting`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hasil_pemilihan`
--
ALTER TABLE `hasil_pemilihan`
  MODIFY `id_hasil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `kandidat`
--
ALTER TABLE `kandidat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `periode_pemilu`
--
ALTER TABLE `periode_pemilu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `voting`
--
ALTER TABLE `voting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hasil_pemilihan`
--
ALTER TABLE `hasil_pemilihan`
  ADD CONSTRAINT `hasil_pemilihan_ibfk_1` FOREIGN KEY (`id_kandidat`) REFERENCES `kandidat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
