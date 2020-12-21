-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 21, 2020 at 10:00 AM
-- Server version: 8.0.13
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `nintendo`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_games`
--

CREATE TABLE `t_games` (
  `nomor` int(11) NOT NULL,
  `url` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `filename` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `titleid` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `fileSize` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '0',
  `md5Checksum` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `folder` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `root` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `owner` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `shared` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 shared'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_games`
--

INSERT INTO `t_games` (`nomor`, `url`, `filename`, `title`, `titleid`, `fileSize`, `md5Checksum`, `folder`, `root`, `owner`, `shared`) VALUES
(1, '1Ywmnyna6yeZE8pyp9S27v9ygO0A2Uvz5', 'Youtubers Life OMG Edition[01002C9005F36000][US][v0].nsz', 'Youtubers Life OMG Edition', '01002C9005F36000', '665796945', '60710e3ecd4683fcfa7742e50785283b', 'nsz', ' ', ' ', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_games`
--
ALTER TABLE `t_games`
  ADD PRIMARY KEY (`nomor`),
  ADD UNIQUE KEY `id` (`url`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_games`
--
ALTER TABLE `t_games`
  MODIFY `nomor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;
