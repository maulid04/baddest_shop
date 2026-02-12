-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 05:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `baddest_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `quantity`, `last_updated`) VALUES
(1, 1, 48, '2026-02-12 11:01:12'),
(2, 2, 96, '2026-02-12 11:03:15'),
(3, 3, 71, '2026-02-12 11:03:00'),
(4, 4, 25, '2026-02-11 17:49:54'),
(5, 5, 58, '2026-02-12 11:01:35'),
(6, 6, 44, '2026-02-12 10:57:26'),
(7, 7, 73, '2026-02-12 10:57:26'),
(8, 8, 81, '2026-02-12 11:02:42');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `shipping_address`, `created_at`, `updated_at`) VALUES
(1, 2, 1029.98, 'delivered', '456 Customer Ave', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(2, 4, 1999.98, 'pending', 'dar es salaam', '2026-02-12 11:01:12', '2026-02-12 11:01:12'),
(3, 4, 399.98, 'pending', 'dar es salaam', '2026-02-12 11:01:35', '2026-02-12 11:01:35'),
(4, 5, 4093.76, 'pending', 'bibi titi', '2026-02-12 11:02:42', '2026-02-12 11:02:42'),
(5, 5, 319.96, 'pending', 'bibi titi', '2026-02-12 11:03:00', '2026-02-12 11:03:00'),
(6, 5, 119.96, 'pending', 'bibi titi', '2026-02-12 11:03:15', '2026-02-12 11:03:15');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 999.99),
(2, 1, 2, 1, 29.99),
(3, 2, 1, 2, 999.99),
(4, 3, 5, 2, 199.99),
(5, 4, 8, 4, 1023.44),
(6, 5, 3, 4, 79.99),
(7, 6, 2, 4, 29.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_path`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Laptop', 'High-performance laptop for work and gaming', 999.99, 'assets/images/laptop.png', 'Electronics', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(2, 'Mouse', 'Wireless ergonomic mouse', 29.99, 'assets/images/mouse.png', 'Accessories', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(3, 'Keyboard', 'Mechanical gaming keyboard', 79.99, 'assets/images/keyboard.png', 'Accessories', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(4, 'Monitor', '27-inch 4K monitor', 349.99, 'assets/images/monitor.png', 'Electronics', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(5, 'Headphones', 'Noise-cancelling wireless headphones', 199.99, 'assets/images/headphones.jpg', 'Audio', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(6, 'digital camera', 'hd camera', 203.98, 'assets/images/698db181bae51_digital camera.jpg', 'electronics', '2026-02-12 10:54:57', '2026-02-12 10:54:57'),
(7, 'television', 'fully_screen tv', 548.09, 'assets/images/698db1c9dc6b5_television.jpg', 'electronics', '2026-02-12 10:56:09', '2026-02-12 10:56:09'),
(8, 'system case', 'high cpu', 1023.44, 'assets/images/698db212c1c62_system case.jpg', 'electronics', '2026-02-12 10:57:22', '2026-02-12 10:57:22');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(2, 'admin'),
(1, 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `sales_reports`
--

CREATE TABLE `sales_reports` (
  `id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `total_sales` decimal(10,2) DEFAULT 0.00,
  `total_orders` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_reports`
--

INSERT INTO `sales_reports` (`id`, `report_date`, `total_sales`, `total_orders`, `created_at`) VALUES
(1, '2023-01-01', 1029.98, 1, '2026-02-11 17:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role_id`, `first_name`, `last_name`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@baddestshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Admin', 'User', '123-456-7890', '123 Admin St', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(2, 'customer1', 'customer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'John', 'Doe', '987-654-3210', '456 Customer Ave', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(3, 'customer2', 'customer2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Jane', 'Smith', '555-123-4567', '789 Shop Blvd', '2026-02-11 17:49:54', '2026-02-11 17:49:54'),
(4, 'maulid', 'Abdallahmaulid789@gmail.com', '$2y$10$RLSpA6WzdtbBKRAhP9tTY.JzvGfAoyVR71uFUlv/kmw1UWkQqgAjO', 1, 'maulid', 'abdallah', '0618278200', 'dar es salaam', '2026-02-12 11:00:24', '2026-02-12 11:00:24'),
(5, 'cbe', 'cbecollege123@gmail.com', '$2y$10$knZEL7KYAXMpwpLKz9PhK.civOzPEdq/epi8mJ9w6xsX6TspWpGou', 1, 'cbe', 'college', '0682292287', 'bibi titi', '2026-02-12 11:02:10', '2026-02-12 11:02:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sales_reports`
--
ALTER TABLE `sales_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_date` (`report_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales_reports`
--
ALTER TABLE `sales_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
