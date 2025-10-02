-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2025 at 12:13 PM
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
-- Database: `online_ordering`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(3, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(34, 40, 63, 't-shirt 123', 550, 1, 'wallpaper.png');

-- --------------------------------------------------------

--
-- Table structure for table `loginlogs`
--

CREATE TABLE `loginlogs` (
  `id` int(11) DEFAULT NULL,
  `IpAddress` varchar(16) NOT NULL,
  `Trytime` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loginlogs`
--

INSERT INTO `loginlogs` (`id`, `IpAddress`, `Trytime`) VALUES
(NULL, '::1', '1669560843'),
(NULL, '::1', '1669560846'),
(NULL, '::1', '1669560849');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(7, 40, 'Lucky Keith Padua', 'luckypadua4@gmail.com', '09930512859', 'meow\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(100) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `design_file` varchar(255) DEFAULT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` text DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `gcash_ref` varchar(50) DEFAULT NULL,
  `placed_on` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `expected_delivery_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `status_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `type`, `quantity`, `size`, `price`, `design_file`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `gcash_ref`, `placed_on`, `payment_status`, `expected_delivery_date`, `status`, `status_updated_at`) VALUES
(40, 40, NULL, NULL, NULL, NULL, NULL, NULL, 'Lucky Keith Padua', '09930512859', 'luckypadua4@gmail.com', 'Cash On Delivery', 'Centro Sur', 't-shirt (₱600 x 1)', 600.00, NULL, '2025-10-02 16:50:58', 'pending', '2025-10-23', 'To Received', '2025-10-02 08:51:20'),
(41, 41, NULL, NULL, NULL, NULL, NULL, NULL, 'Glenard U Pagurayan', '09557997409', 'glenard2308@gmail.com', 'Cash On Delivery', 'Bishan St. 23', 't-shirt (₱600 x 1)', 600.00, NULL, '2025-10-02 17:03:38', 'pending', '2025-10-14', 'received', '2025-10-02 09:07:08'),
(42, 41, NULL, NULL, NULL, NULL, NULL, NULL, 'Glenard U Pagurayan', '09557997409', 'glenard2308@gmail.com', 'Cash On Delivery', 'Bishan St. 23', 't-shirt (₱600 x 1)', 600.00, NULL, '2025-10-02 17:46:48', 'pending', '2025-10-02', 'received', '2025-10-02 09:48:51'),
(43, 41, NULL, NULL, NULL, NULL, NULL, NULL, 'Glenard U Pagurayan', '09557997409', 'glenard2308@gmail.com', 'Cash On Delivery', 'Bishan St. 23', 't-shirt (₱600 x 1)', 600.00, NULL, '2025-10-02 18:08:12', 'pending', '2025-10-08', 'delivered', '2025-10-02 10:08:31'),
(44, 41, NULL, NULL, NULL, NULL, NULL, NULL, 'Glenard U Pagurayan', '09557997409', 'glenard2308@gmail.com', 'Cash On Delivery', 'Bishan St. 23', 't-shirt (₱600 x 1)', 600.00, NULL, '2025-10-02 18:08:39', 'pending', NULL, 'pending', '2025-10-02 10:08:39');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 40, 64, 1, 600.00),
(2, 41, 64, 1, 600.00),
(3, 42, 64, 1, 600.00),
(4, 43, 64, 1, 600.00),
(5, 44, 64, 1, 600.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_ratings`
--

CREATE TABLE `order_ratings` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_ratings`
--

INSERT INTO `order_ratings` (`id`, `order_id`, `user_id`, `product_id`, `rating`, `review`, `created_at`) VALUES
(2, 41, 41, 64, 1, 'nagbayag', '2025-10-02 17:07:04'),
(3, 42, 41, 64, 5, 'awdawd', '2025-10-02 17:48:48'),
(4, 43, 41, 64, 5, 'Solid', '2025-10-02 18:08:47');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `image`) VALUES
(56, 'tarpaulin', 'Tarpaulin', 123, '03.png'),
(57, 'Souvenir', 'Souvenirs', 100, '04.png'),
(58, 'Sticker', 'Customize stickers', 300, '02.png'),
(61, 'Chinese Collar', 'Full sublimation', 500, 'CA ML mockup copy.jpg'),
(62, 'Collar', 'Full sublimation', 500, 'Educ ColSC mockup copy.jpg'),
(63, 't-shirt 123', 'Full sublimation', 550, 'wallpaper.png'),
(64, 't-shirt', 'Full sublimation', 600, 'a3d40bc0-2088-4ed3-bba1-76b49f751703.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` varchar(500) NOT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `security_question_1` varchar(255) DEFAULT NULL,
  `security_answer_1` varchar(255) DEFAULT NULL,
  `security_question_2` varchar(255) DEFAULT NULL,
  `security_answer_2` varchar(255) DEFAULT NULL,
  `security_question_3` varchar(255) DEFAULT NULL,
  `security_answer_3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `number`, `password`, `address`, `valid_id`, `security_question_1`, `security_answer_1`, `security_question_2`, `security_answer_2`, `security_question_3`, `security_answer_3`) VALUES
(40, 'Lucky Keith Padua', 'luckypadua4@gmail.com', '09930512859', '$2y$10$N2Q1Bhjv7f6WFWc.q16puOqkaBlNQ0EYpNkoQ1AE8FwenTtIJbAO2', 'Centro Sur', 'uploaded_ids/68dccee3a0f52_ATTY.png', 'What is your favorite movie?', '123', 'What is your dream job?', '123', 'What is your pet&#39;s name?', '123'),
(41, 'Glenard U Pagurayan', 'glenard2308@gmail.com', '09557997409', '$2y$10$Uwb7vk/v0.pXMVOhiNMv6.SOOkY3SudMtg38IJ9Wf.NQ7IsNmphnK', 'Bishan St. 23', 'uploaded_ids/68de3fc96bb6c_33156cec-27ae-4f0d-85ce-40934523e639.jpg', 'What is your favorite movie?', '123', 'What city were you born in?', '123', 'Who is your childhood hero?', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_ratings`
--
ALTER TABLE `order_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_ratings_product_fk` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_ratings`
--
ALTER TABLE `order_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_ratings`
--
ALTER TABLE `order_ratings`
  ADD CONSTRAINT `order_ratings_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `order_ratings_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
