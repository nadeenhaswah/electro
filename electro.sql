-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 28, 2025 at 08:05 PM
-- Server version: 9.1.0
-- PHP Version: 8.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `electro`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `status` enum('active','converted','abandoned') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `session_id`, `status`, `created_at`) VALUES
(1, NULL, '071a1d6e86affebe37f7689a71c37dda', 'converted', '2025-12-27 13:25:36'),
(2, 12, NULL, 'active', '2025-12-27 13:58:16'),
(3, NULL, '071a1d6e86affebe37f7689a71c37dda', 'converted', '2025-12-27 14:16:20'),
(5, NULL, '071a1d6e86affebe37f7689a71c37dda', 'converted', '2025-12-27 14:17:41'),
(6, 14, NULL, 'active', '2025-12-27 14:18:13'),
(7, NULL, '071a1d6e86affebe37f7689a71c37dda', 'converted', '2025-12-27 14:24:43'),
(8, 15, NULL, 'active', '2025-12-27 18:19:07');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `price_at_time` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_id` (`cart_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `item_id`, `quantity`, `price_at_time`) VALUES
(1, 8, 10, 1, 240.00),
(2, 6, 15, 2, 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `discount_start` datetime DEFAULT NULL,
  `discount_end` datetime DEFAULT NULL,
  `visibility` tinyint(1) DEFAULT '1',
  `allow_comments` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `discount_value`, `discount_start`, `discount_end`, `visibility`, `allow_comments`) VALUES
(1, 'Mobiles', 'Smartphones and mobile devices', 15.00, '2025-12-20 00:00:00', '2025-12-30 00:00:00', 1, 1),
(3, 'Laptops', 'Laptops and notebooks', 0.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 0),
(5, 'Chargers', 'for all devices', 20.00, '2025-12-27 00:00:00', '2025-12-30 00:00:00', 1, 1),
(6, 'Cameras', 'all types of cameras', 40.00, '2025-12-20 00:00:00', '2025-12-30 00:00:00', 1, 1),
(8, 'Headphones', 'all types of cameras', 70.00, '2025-12-27 00:00:00', '2026-01-03 00:00:00', 0, 0),
(9, 'ipad', 'ipad', 35.00, '2025-12-31 13:03:00', '2025-12-30 16:03:00', 1, 1),
(10, 'Accessories', 'Accessories', 0.00, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `comment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `item_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `item_id` (`item_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `comment`, `status`, `comment_date`, `item_id`, `user_id`) VALUES
(4, 'very good', 1, '2025-12-28 17:20:19', 10, 12),
(6, 'Excellent laptop! The performance is amazing and the display is stunning. Highly recommended for professionals.', 1, '2025-12-28 17:30:05', 11, 14),
(7, 'Excellent laptop! The performance is amazing and the display is stunning. Highly recommended for professionals.', 0, '2025-12-28 17:30:29', 10, 10),
(8, 'nice', 1, '2025-12-28 23:00:49', 10, 14),
(9, 'nice', 1, '2025-12-28 23:01:08', 10, 14);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `discount_start` datetime DEFAULT NULL,
  `discount_end` datetime DEFAULT NULL,
  `add_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `country_made` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `category_id` int NOT NULL,
  `quantity` int DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `name`, `description`, `price`, `discount_value`, `discount_start`, `discount_end`, `add_date`, `country_made`, `status`, `category_id`, `quantity`) VALUES
(10, 'Hp laptop', 'Hp laptop core i5', 300.00, 20.00, '2025-12-27 00:00:00', '2025-12-27 00:00:00', '2025-12-27 23:45:14', 'Jordan', 'Low Stock', 3, 4),
(11, 'Samsung S23', '128 GB', 500.00, 10.00, '2025-12-12 00:00:00', '2025-12-03 00:00:00', '2025-12-28 13:17:19', 'UK', 'In Stock', 1, 40),
(15, 'Canera canon', '', 300.00, NULL, NULL, NULL, '2025-12-28 22:51:36', 'china', 'available', 6, 100),
(16, 'MacBook Air M1', 'Apple laptop', 600.00, 20.00, '2026-01-10 00:00:00', '2026-01-10 00:00:00', '2025-12-28 22:54:11', 'USA', 'available', 3, 20),
(17, 'Smart watch', 'Apple Watch', 800.00, NULL, NULL, NULL, '2025-12-28 22:59:52', 'USA', 'available', 10, 40),
(18, 'Gaming Mouse', 'RGB gaming mouse', 10.00, NULL, NULL, NULL, '2025-12-28 23:03:20', 'Japan', 'available', 10, 50);

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

DROP TABLE IF EXISTS `item_images`;
CREATE TABLE IF NOT EXISTS `item_images` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`image_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`image_id`, `item_id`, `image_path`, `is_main`) VALUES
(2, 10, 'item_6950455a4cc97.png', 1),
(3, 10, 'item_6950455a4d8b6.png', 0),
(4, 10, 'item_6950455a4dea1.png', 0),
(5, 10, 'item_6950fd844c90b.png', 0),
(6, 11, 'item_695103afb7129.png', 1),
(7, 11, 'item_695187a6c30f5.png', 0),
(9, 15, 'item_69518a480e51f.jpeg', 1),
(10, 16, 'item_69518ae305808.jpeg', 1),
(11, 17, 'item_69518c38be52e.jpeg', 1),
(12, 18, 'item_69518d0804b69.jpeg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `item_ratings`
--

DROP TABLE IF EXISTS `item_ratings`;
CREATE TABLE IF NOT EXISTS `item_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_item_user` (`item_id`,`user_id`),
  KEY `fk_item_ratings_user` (`user_id`)
) ;

--
-- Dumping data for table `item_ratings`
--

INSERT INTO `item_ratings` (`id`, `item_id`, `user_id`, `rating`, `created_at`) VALUES
(1, 10, 14, 5, '2025-12-28 23:00:49');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 20.00, 'processing', '2025-12-25 17:30:53'),
(2, 2, 66.00, 'completed', '2025-12-26 01:38:21'),
(3, 1, 450.00, 'completed', '2025-12-26 02:03:05'),
(4, 14, 450.00, 'completed', '2025-12-28 12:55:10');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `price`) VALUES
(4, 4, 10, 8, 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `cardholder_name` varchar(100) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `expiry_date` varchar(7) NOT NULL,
  `cvv` varchar(4) NOT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer') DEFAULT 'credit_card',
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `cardholder_name`, `card_number`, `expiry_date`, `cvv`, `billing_address`, `amount`, `payment_method`, `payment_status`, `payment_date`) VALUES
(1, 3, 'nadeen', '666', '12/1/20', '77', 'amman', 77.00, 'paypal', 'completed', '2025-12-26 14:27:17'),
(2, 2, 'rama', '666', '12/1/20', '77', 'amman', 77.00, 'credit_card', 'pending', '2025-12-26 14:41:34'),
(3, 1, 'mmm', '666', '12/1/20', '77', 'zarqaa', 77.00, 'paypal', 'completed', '2025-12-26 16:32:42'),
(4, 4, 'randa', '809', '12/1/20', '7799', 'amman', 450.00, 'paypal', 'completed', '2025-12-28 13:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `first_name`, `last_name`, `mobile`, `password`, `role`) VALUES
(1, 'nadeen@gmail.com', 'nadeen', 'haswah', '0799342463', 'nADEEN#56', 'admin'),
(2, 'rana@gmail.com', 'rana', 'rami', '0799342463', '1111', 'user'),
(10, 'masa@gmail.com', 'masa', 'omar', '0799342498', '$2y$12$.0vDz6UzmslXhDo3NqQfO.tMeVA0mY0A9jFZJ9fu8kbdk5X/RfE.S', 'user'),
(12, 'toleen@gmail.com', 'toleen', 'mustafa', '0799342463', '$2y$12$AV1WzEMlzIiWihWac/9CBumTDDsSdyyymuoidkrEj8V9wPqN0WvGy', 'user'),
(14, 'randa@gmail.com', 'randa', 'almansi', '0799342463', '$2y$12$sIUXKcNPTdG05rHY7gIx9eYNnSjvy80ko/Blal0.KRqFW1uH5ftDq', 'admin'),
(15, 'raghad@gmail.com', 'raghad', 'himour', '0799342463', '$2y$12$ANLRy/.amUvIdyG8gda3UOlsnWqRZ7k7jExTuJqPHz9F5UYSyN9Ua', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE IF NOT EXISTS `wishlists` (
  `wishlist_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`wishlist_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `created_at`) VALUES
(1, 12, '2025-12-27 13:58:25'),
(3, 14, '2025-12-27 14:18:13'),
(4, 15, '2025-12-27 18:19:07');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_items`
--

DROP TABLE IF EXISTS `wishlist_items`;
CREATE TABLE IF NOT EXISTS `wishlist_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `wishlist_id` int NOT NULL,
  `item_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `wishlist_id` (`wishlist_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlist_items`
--

INSERT INTO `wishlist_items` (`id`, `wishlist_id`, `item_id`) VALUES
(1, 4, 11),
(2, 3, 11);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_images`
--
ALTER TABLE `item_images`
  ADD CONSTRAINT `item_images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `item_ratings`
--
ALTER TABLE `item_ratings`
  ADD CONSTRAINT `fk_item_ratings_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD CONSTRAINT `wishlist_items_ibfk_1` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlists` (`wishlist_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
