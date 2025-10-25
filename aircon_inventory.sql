-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 10:10 AM
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
-- Database: `aircon_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `archived_products`
--

CREATE TABLE `archived_products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `archived_by` varchar(50) DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archived_products`
--

INSERT INTO `archived_products` (`id`, `product_id`, `product_name`, `category`, `brand`, `description`, `stock`, `price`, `archived_by`, `archived_at`) VALUES
(8, 5, 'Panasonic Aero Series 1.0 HP Split Type', 'Split Type', 'Panasonic', 'Energy-saving inverter with nanoe-G purification system.', 5, 27499.00, 'admin', '2025-10-24 07:30:40');

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `action` enum('Login','Logout','Failed Login') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `category`, `brand`, `description`, `stock`, `price`, `created_by`, `created_at`) VALUES
(1, 'LG Dual Inverter Window Type 1.0 HP', 'Window Type', 'LG', 'Energy-efficient aircon with fast cooling and quiet operation.', 10, 18995.00, NULL, '2025-10-24 06:14:21'),
(2, 'Carrier Compact Window Type 1.5 HP', 'Window Type', 'Carrier', 'Compact design, powerful cooling, and low noise level.', 8, 21999.00, NULL, '2025-10-24 06:14:21'),
(3, 'Samsung WindFree Window Type 1.0 HP', 'Window Type', 'Samsung', 'No direct airflow technology for comfortable cooling.', 12, 20495.00, NULL, '2025-10-24 06:14:21'),
(4, 'Daikin Split Type Inverter 1.5 HP', 'Split Type', 'Daikin', 'Smart inverter technology and antibacterial filter for clean air.', 6, 28995.00, NULL, '2025-10-24 06:14:21'),
(6, 'Midea Blanc Series Split Type 2.0 HP', 'Split Type', 'Midea', 'Fast cooling with self-cleaning mode and eco-friendly refrigerant.', 7, 30995.00, NULL, '2025-10-24 06:14:21'),
(13, 'LG Cassette Type Inverter 2.5 HP', 'Cassette Type', 'LG', 'Smart inverter cassette AC with elegant ceiling design.', 3, 48499.00, NULL, '2025-10-24 06:14:21'),
(14, 'Mitsubishi Ceiling Mounted 4.0 HP', 'Ceiling Mounted', 'Mitsubishi', 'Powerful ceiling-mounted aircon for commercial applications.', 2, 59995.00, NULL, '2025-10-24 06:14:21'),
(15, 'Carrier Ceiling Mounted 3.0 HP', 'Ceiling Mounted', 'Carrier', 'Durable commercial-grade ceiling mount system.', 3, 55999.00, NULL, '2025-10-24 06:14:21');

-- --------------------------------------------------------

--
-- Table structure for table `product_activity_log`
--

CREATE TABLE `product_activity_log` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `action_type` enum('Add','Edit','Archive','Restore','Delete','Login','Logout','Failed Login','Register') NOT NULL,
  `performed_by` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `performed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_activity_log`
--

INSERT INTO `product_activity_log` (`id`, `product_id`, `product_name`, `action_type`, `performed_by`, `details`, `performed_at`) VALUES
(1, NULL, NULL, 'Register', 'admin', 'New user (admin) registered successfully as admin', '2025-10-24 06:12:29'),
(2, NULL, NULL, 'Login', 'admin', 'Successful login', '2025-10-24 06:12:32'),
(9, NULL, NULL, 'Login', 'admin', 'Successful login', '2025-10-24 07:22:43'),
(10, NULL, NULL, 'Login', 'admin', 'Successful login', '2025-10-24 07:28:04'),
(11, NULL, 'Panasonic Aero Series 1.0 HP Split Type', 'Archive', 'admin', 'Product archived successfully.', '2025-10-24 07:30:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$NCw3x.g3xcCiw6OlE8ztTuwjxQ9Sg5B3.C9nQx7d7rznPGerKPhYS', 'admin', '2025-10-24 06:12:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archived_products`
--
ALTER TABLE `archived_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `archived_by` (`archived_by`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `product_activity_log`
--
ALTER TABLE `product_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archived_products`
--
ALTER TABLE `archived_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_activity_log`
--
ALTER TABLE `product_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_activity_log`
--
ALTER TABLE `product_activity_log`
  ADD CONSTRAINT `product_activity_log_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
