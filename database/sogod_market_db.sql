-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2024 at 03:43 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sogod_market_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(30) NOT NULL,
  `category` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `description`, `status`, `date_created`) VALUES
(10, 'Fresh Produce', '* Fruits\r\n* Vegetables\r\n* Herbs and spices', 1, '2024-05-30 10:01:04'),
(11, 'Meat and Seafood', '*Butcher shops (beef, pork, poultry)\r\n*Fishmongers (fresh and dried fish, shellfish)', 1, '2024-05-30 10:01:43'),
(12, 'Dairy and Egg', '* Milk and milk products (cheese, yogurt)\r\n* Eggs', 1, '2024-05-30 10:02:02'),
(13, 'Bakery', '* Bread\r\n* Pastries\r\n* Cakes', 1, '2024-05-30 10:02:24'),
(14, 'Dry Goods', '* Rice\r\n* Grains\r\n* Beans\r\n* Canned goods', 1, '2024-05-30 10:02:46'),
(15, 'Spices and Condiments', '* Spices\r\n* Sauces\r\n* Marinades', 1, '2024-05-30 10:03:33'),
(16, 'Clothing and Accessories', '* New and second-hand clothing\r\n* Shoes\r\n* Bags and accessories', 1, '2024-05-30 10:04:02');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(30) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` text NOT NULL,
  `address` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `generated_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `firstname`, `lastname`, `gender`, `contact`, `email`, `password`, `address`, `date_created`, `generated_code`) VALUES
(1, 'Riz', 'Michel', 'Male', '09123456789', 'jsmith@sample.com', '202cb962ac59075b964b07152d234b70', 'Sample', '2021-10-13 14:10:49', ''),
(2, 'last', 'name', 'Male', '09446612598', 'tajanlangitearl41@gmail.com', '4e1cfe9044795c3c50f41730d0071655', 'Foothill Ridge, Kalubihan, Talamban, Cebu City', '2024-08-18 17:34:33', ''),
(3, 'Earl Francis', 'Tajanlangit', 'Male', '09667713831', 'tajanlangitearl24@gmail.com', '202cb962ac59075b964b07152d234b70', 'Foothill Ridge, Kalubihan, Talamban, Cebu City', '2024-08-18 17:38:38', 'nYKHDmrysH'),
(4, 'Earl Francis', 'Tajanlangit', 'Male', '09446612598', 'tajanlangitearl114@gmail.com', '202cb962ac59075b964b07152d234b70', 'Foothill Ridge, Kalubihan, Talamban, Cebu City', '2024-08-18 18:31:15', 'vY84LgEKB8');

-- --------------------------------------------------------

--
-- Table structure for table `rent_list`
--

CREATE TABLE `rent_list` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `space_id` int(11) DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `rent_days` int(11) NOT NULL DEFAULT 0,
  `amount` float NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Pending,1=Confirmed,2=Cancelled,3=Picked -up, 4 =Returned',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rent_list`
--

INSERT INTO `rent_list` (`id`, `client_id`, `space_id`, `date_start`, `date_end`, `rent_days`, `amount`, `status`, `date_created`, `date_updated`) VALUES
(23, 5, 7, '2024-09-20', '2024-10-20', 31, 310, 3, '2024-06-18 09:28:16', '2024-08-16 22:14:54'),
(24, 10, 8, '2024-01-04', '2025-01-04', 367, 3670, 2, '2024-06-19 10:34:35', '2024-06-25 17:05:52'),
(25, 10, 8, '2024-12-02', '2025-01-02', 32, 320, 3, '2024-06-19 15:44:39', '2024-08-16 11:01:05'),
(26, 11, 7, '2025-04-20', '2025-05-30', 41, 94710, 1, '2024-06-25 14:59:39', '2024-06-25 15:00:05'),
(36, 11, 7, '2024-02-20', '2024-03-20', 30, 69300, 1, '2024-06-25 17:09:28', '2024-06-25 17:10:00'),
(37, 11, 9, '2024-04-20', '2024-05-20', 31, 310, 0, '2024-06-25 17:16:42', NULL),
(38, 11, 9, '2024-04-20', '2024-05-20', 31, 310, 0, '2024-06-25 17:18:52', NULL),
(39, 11, 9, '2024-04-20', '2024-05-20', 31, 310, 1, '2024-06-25 17:19:09', '2024-06-25 17:19:49'),
(40, 11, 9, '2024-06-06', '2024-07-06', 31, 310, 1, '2024-06-25 17:21:32', '2024-06-26 08:44:24'),
(41, 3, 9, '2024-02-02', '2024-03-02', 30, 300, 0, '2024-08-18 18:10:19', NULL),
(42, 3, 9, '2024-02-02', '2024-03-02', 30, 300, 1, '2024-08-18 18:21:02', '2024-08-18 18:26:58'),
(43, 3, 9, '2024-02-02', '2024-03-02', 30, 300, 0, '2024-08-18 18:29:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `space_list`
--

CREATE TABLE `space_list` (
  `id` int(30) NOT NULL,
  `space_type_id` int(11) DEFAULT NULL,
  `category_id` int(30) NOT NULL,
  `space_name` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `quantity` tinyint(3) NOT NULL DEFAULT 0,
  `daily_rate` float NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `space_list`
--

INSERT INTO `space_list` (`id`, `space_type_id`, `category_id`, `space_name`, `description`, `quantity`, `daily_rate`, `status`, `date_created`, `date_updated`) VALUES
(7, 10, 14, 'Lock ups', '&lt;p&gt;Sample33&lt;/p&gt;', 104, 2310, 1, '2024-05-30 13:28:06', '2024-06-25 17:10:00'),
(8, 11, 10, 'Table Space', 'Table Space for Fruits', -5, 10, 1, '2024-05-30 14:10:10', '2024-06-26 09:36:30'),
(9, 8, 11, 'Meet Stalls', '', 5, 10, 1, '2024-06-11 08:41:50', '2024-08-18 18:26:58'),
(10, 10, 13, 'sample', 'test ni&amp;nbsp;', 3, 10, 1, '2024-06-11 08:51:03', '2024-06-19 10:32:59'),
(11, 8, 16, 'spacenameyes', '', 120, 10, 1, '2024-08-18 11:09:20', '2024-08-18 11:13:58');

-- --------------------------------------------------------

--
-- Table structure for table `space_type_list`
--

CREATE TABLE `space_type_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `space_type_list`
--

INSERT INTO `space_type_list` (`id`, `name`, `status`, `date_created`) VALUES
(8, 'Stalls', 1, '2024-05-30 10:06:42'),
(9, 'Lock-Up Shops', 1, '2024-05-30 10:06:54'),
(10, 'Booths', 1, '2024-05-30 10:07:17'),
(11, 'Open-Air Spaces', 1, '2024-05-30 10:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Sogod Market Vendor&apos;s Leasing and Renewal Management System'),
(6, 'short_name', 'Sogod Market'),
(11, 'logo', 'uploads/1717039620_logo.png'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/buyingvegetables.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `generated_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`, `generated_code`) VALUES
(1, 'Adminstrator', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/1717378440_th.jpg', NULL, 1, '2021-01-20 14:02:37', '2024-06-03 09:34:09', ''),
(4, 'John', 'Smith', 'jsmith', '1254737c076cf867dc53d60a0364f38e', NULL, NULL, 0, '2021-06-19 08:36:09', '2021-06-19 10:53:12', ''),
(5, 'Claire', 'Blake', 'cblake', '4744ddea876b11dcb1d169fadf494418', NULL, NULL, 0, '2021-06-19 10:01:51', '2021-06-19 12:03:23', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rent_list`
--
ALTER TABLE `rent_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `space_list`
--
ALTER TABLE `space_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`space_type_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `space_type_list`
--
ALTER TABLE `space_type_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `rent_list`
--
ALTER TABLE `rent_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `space_list`
--
ALTER TABLE `space_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `space_type_list`
--
ALTER TABLE `space_type_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
