
CREATE TABLE `t_games` (
  `nomor` int(11) NOT NULL,
  `id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `fileSize` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `md5Checksum` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `folder` varchar(32) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_games`
--
ALTER TABLE `t_games`
  ADD PRIMARY KEY (`nomor`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_games`
--
ALTER TABLE `t_games`
  MODIFY `nomor` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
