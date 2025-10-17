-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 11:21 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cities_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Cebu', 'Known as the Queen City of the South', '2025-10-17 09:00:59'),
(2, 'London', 'London is the capital city of England. It is the most populous city in the United Kingdom, with a metropolitan area of over 13 million inhabitants. Standing on the River Thames, London has been a major settlement for two millennia, its history going back to its founding by the Romans, who named it Londinium.', '2025-10-17 08:06:57'),
(3, 'Paris', 'Paris is the capital and largest city of France. It is located on the river Seine in northern France. Paris is one of the most beautiful cities in the world and is known as the City of Light.', '2025-10-17 08:06:57'),
(4, 'Tokyo', 'Tokyo is the capital and most populous city of Japan. It is the seat of the Emperor of Japan and the Japanese government. Tokyo is a major international finance center and is considered a global city.', '2025-10-17 08:06:57'),
(5, 'Bohol', 'Known for its Chocolate Hills and Tarsiers', '2025-10-17 08:09:11'),
(6, 'Cagayan de Oro ', 'The city of golden friendship', '2025-10-17 08:55:52'),
(7, 'Tagbilaran', 'Capital city of Bohol with many bustling buildings', '2025-10-17 08:57:26'),
(8, 'Davao ', 'Biggest City in Mindanao\r\n', '2025-10-17 08:59:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
