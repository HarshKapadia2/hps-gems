-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2020 at 08:46 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.6

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hps_gems`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth` (
  `token` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `auth`:
--   `user_id`
--       `user` -> `id`
--

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

DROP TABLE IF EXISTS `order_detail`;
CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `is_delivered` tinyint(1) NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `order_detail`:
--   `prod_id`
--       `product` -> `id`
--   `user_id`
--       `user` -> `id`
--

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `user_id`, `prod_id`, `qty`, `is_delivered`, `date`) VALUES
(7, 9, 5, 7, 1, '2020-11-24'),
(18, 9, 1, 3, 1, '2020-11-27'),
(19, 9, 12, 12, 1, '2020-11-27');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `price` float NOT NULL,
  `qty` int(11) NOT NULL,
  `pic_url` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `product`:
--

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`, `qty`, `pic_url`) VALUES
(1, 'Aquamarine', '11 carat \r\n11x5/10x6 \r\nLoose, rough, high quality, untreated and raw specimen', 210, 32, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Faquamarine.jpg?alt=media&token=b53597d3-5adf-4e55-9bd9-7ccdfc69381b'),
(2, 'Tourmaline', '7.10 carat \r\n15x7x6 mm \r\nSku = 4549\r\nRaw gemstone', 183, 420, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Ftourmaline.jpg?alt=media&token=7a96ebbd-ce6f-40d3-84ca-8577a8487af7'),
(5, 'Malachite', 'Natural, raw, rough and loose semiprecious gemstone', 100, 41, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Fmalachite.jpg?alt=media&token=baff9bf0-90ee-4923-b0d2-46b2d0d70dcc'),
(6, 'Yellow Sapphire', '140 carat \r\n6 to 12 mm \r\nNatural yellow \r\nRaw, rough and loose gemstone', 1260, 60, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Fyellow-sapphire.jpg?alt=media&token=25da37bc-5b80-436b-a28a-17105af43727'),
(7, 'Blue Opal', 'Natural blue \r\nLoose, raw, rough and high quality semiprecious gemstone', 570, 0, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Fblue-opal.jpg?alt=media&token=1b72f88e-97b0-4ff7-a2b3-9fa1ebd43164'),
(8, 'Labradorite', 'Multi colour \r\nPalm size \r\nNatural, raw, rough and loose semiprecious gemstone', 120, 55, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Flabradorite.jpg?alt=media&token=1f7cc792-6683-4d9c-9e1d-72714d028955'),
(9, 'Amethyst', '50/100/200g \r\n1-2cm \r\nNatural purple \r\nLoose, rough, untreated and raw crystals\r\n', 113, 41, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Famethyst.jpg?alt=media&token=6b0236e3-ac0d-45aa-be4e-b34f025a95a1'),
(10, 'Apatite', '14-16 mm \r\nNatural blue \r\nNatural, rough, raw and loose gemstone', 112, 69, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Fapatite.jpg?alt=media&token=0084135a-d2a6-4759-97f2-e45675a51471'),
(11, 'Aqua Iridized', '25, 50 or 75 count \r\n3/8\" or 12mm-14mm \r\nV 2720 \r\nMini glass nuggets, gems, pebbles and stones\r\n', 223, 22, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Faqua-iridized.jpg?alt=media&token=805b15a4-f220-4be7-b2c5-34f505337e40'),
(12, 'Citrine', 'Raw, rough cut crystal \r\nTop drilled bead', 125, 18, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Fcitrine.jpg?alt=media&token=be239097-ec80-4641-8830-4570159426e9'),
(13, 'Sphene Titanite', '2.93 carats \r\nNatural green', 560, 130, 'https://firebasestorage.googleapis.com/v0/b/hps-gems.appspot.com/o/product%2Fsphene-titanite.jpg?alt=media&token=7dc3a20c-5f7f-456d-91a3-ddddb6655407');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(500) NOT NULL,
  `phone_no` varchar(13) NOT NULL,
  `address` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `user`:
--

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `phone_no`, `address`) VALUES
(9, 'Harsh', 'Kapadia', 'harshgkapadia@gmail.com', '$2y$10$cqCer2MfptSFCoguHWxKAOAFVbRs2g4sFV4DVEGT8eZSwPWd6cxFK', '+917506412914', '105, S-1, Sunder Nagar, S.V. Road, Malad (West)');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`token`),
  ADD KEY `auth_user_id` (`user_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_user_id` (`user_id`),
  ADD KEY `order_prod_id` (`prod_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth`
--
ALTER TABLE `auth`
  ADD CONSTRAINT `auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_prod_id` FOREIGN KEY (`prod_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `order_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
