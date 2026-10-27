-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2026 at 09:39 AM
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
-- Database: `garage-erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `SKU` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `low_stock` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `hsn_code` varchar(255) DEFAULT NULL,
  `gst_option` varchar(255) DEFAULT NULL,
  `product_gst` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `availablility` enum('in_stock','out_stock') DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `branch_id`, `created_by`, `vendor_id`, `category_id`, `brand_id`, `name`, `SKU`, `barcode`, `description`, `price`, `quantity`, `low_stock`, `unit_id`, `hsn_code`, `gst_option`, `product_gst`, `images`, `availablility`, `status`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, 1, 1, 'disha', '96325874', 'xfghfgjg', NULL, 500.00, 51, NULL, 1, '30049099', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-08-03 10:59:18', '2026-08-03 06:20:50'),
(2, 1, NULL, 1, 1, 1, 'Admin', 'SKU-331843', '654lkh', NULL, 800.00, 20, NULL, 1, NULL, 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-08-03 11:25:07', '2026-08-03 11:25:07'),
(3, 1, NULL, 1, 2, 1, 'test-lead12', 'SKU-569595', 'Ut praesentium rerumvcv  cvvbcvhfg', 'nbm', 1500.00, 21, NULL, 1, NULL, 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-08-03 11:37:19', '2026-08-03 06:45:11'),
(4, 1, NULL, 1, 2, 1, 'sdgsdg', 'SKU-386914', 'hzchzfhfhfghgsgh', NULL, 569.00, 53, NULL, 1, NULL, 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-08-03 11:40:17', '2026-08-03 06:20:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_barcode_unique` (`barcode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
