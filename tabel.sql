
--
-- Table structure for table `t_games`
--

CREATE TABLE `t_games` (
  `nomor` int(11) NOT NULL,
  `id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `titleid` varchar(16) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `fileSize` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `md5Checksum` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `folder` varchar(32) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_games`
--

INSERT INTO `t_games` (`nomor`, `id`, `title`, `titleid`, `fileSize`, `md5Checksum`, `folder`) VALUES
(1, '1mqUb2nXyLeGJVe7Ob8kXCiF7RS2i6rki', 'YouTube[01003A400C3DA000][US][v0].nsz', '01003A400C3DA000', '53830921', '6a831a77849b069d66767f115f52b62f', 'nsz'),
(2, '1QUjapxmgWvIWBBHGCYDt0oExGBkx1zXK', 'YouTube[01003A400C3DA800][US][v131072].nsz', '01003A400C3DA800', '25473613', '1606c9ffddb50b2bbf39dddb3d279cc3', 'updates'),
(3, '13dCzkukasg7i1Tcpl4drDHF1i0DO9j_I', 'YouTubers Life OMG! [Europe] [En,De,Fr,Es,It,Pt,Ru] [0100A7700CAA4000].xci', '0100A7700CAA4000', '2223010304', '9de62f646b72528aadeb7a1c074ff083', 'xci');

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
  MODIFY `nomor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;